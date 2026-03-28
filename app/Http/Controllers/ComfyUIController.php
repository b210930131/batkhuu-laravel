<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class ComfyUIController extends Controller
{
    protected $comfyUrl;

    public function __construct()
    {
        $this->comfyUrl = env('COMFYUI_URL', 'http://127.0.0.1:8188');
    }

    public function index()
    {
        return view('comfy');
    }

    public function generate(Request $request)
    {
        try {
            $validated = $request->validate([
                'client_id' => 'nullable|string',
                'model' => 'required|string',
                'positive_prompt' => 'required|string',
                'negative_prompt' => 'nullable|string',
                'steps' => 'integer|min:1|max:100',
                'cfg' => 'numeric|min:1|max:20',
                'width' => 'integer|min:256|max:1536',
                'height' => 'integer|min:256|max:1536',
                'sampler' => 'string',
                'controlnet' => 'nullable|array',
                'controlnet.enabled' => 'boolean',
                'controlnet.preprocessor' => 'nullable|string',
                'controlnet.image_base64' => 'nullable|string',
                'controlnet.strength' => 'nullable|numeric',
                'controlnet.start_percent' => 'nullable|numeric',
                'controlnet.end_percent' => 'nullable|numeric',
                // Preprocessor specific parameters
                'controlnet.canny_low' => 'nullable|integer',
                'controlnet.canny_high' => 'nullable|integer',
                'controlnet.depth_resolution' => 'nullable|integer',
                'controlnet.openpose_hands' => 'nullable|string',
                'controlnet.openpose_body' => 'nullable|string',
                'controlnet.openpose_face' => 'nullable|string',
                'controlnet.mlsd_score_thr' => 'nullable|numeric',
                'controlnet.mlsd_dist_thr' => 'nullable|numeric',
                'controlnet.mlsd_resolution' => 'nullable|integer',
                'controlnet.scribble_mode' => 'nullable|string',
                'controlnet.hed_resolution' => 'nullable|integer',
                'controlnet.seg_resolution' => 'nullable|integer',
                'controlnet.normal_resolution' => 'nullable|integer',
            ]);

            $clientId = $validated['client_id'] ?? 'laravel_' . uniqid();

            if (!empty($validated['controlnet']['enabled']) && !empty($validated['controlnet']['image_base64'])) {
                $workflow = $this->buildControlNetWorkflow($validated);
            } else {
                $workflow = $this->buildBaseWorkflow($validated);
            }

            $response = Http::timeout(180)->post($this->comfyUrl . '/prompt', [
                'prompt' => $workflow,
                'client_id' => $clientId,
            ]);

            if ($response->failed()) {
                return response()->json(['success' => false, 'error' => 'ComfyUI Error: ' . $response->body()], 500);
            }

            return response()->json([
                'success' => true, 
                'prompt_id' => $response->json()['prompt_id']
            ]);

        } catch (\Exception $e) {
            Log::error('Generation Failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    protected function buildBaseWorkflow(array $p)
    {
        return [
            "4" => ["class_type" => "CheckpointLoaderSimple", "inputs" => ["ckpt_name" => $p['model']]],
            "5" => ["class_type" => "EmptyLatentImage", "inputs" => ["width" => $p['width'], "height" => $p['height'], "batch_size" => 1]],
            "6" => ["class_type" => "CLIPTextEncode", "inputs" => ["text" => $p['positive_prompt'], "clip" => ["4", 1]]],
            "7" => ["class_type" => "CLIPTextEncode", "inputs" => ["text" => $p['negative_prompt'] ?? "", "clip" => ["4", 1]]],
            "3" => [
                "class_type" => "KSampler",
                "inputs" => [
                    "seed" => rand(1, 999999999), 
                    "steps" => $p['steps'], 
                    "cfg" => $p['cfg'],
                    "sampler_name" => $p['sampler'], 
                    "scheduler" => "karras", 
                    "denoise" => 1,
                    "model" => ["4", 0], 
                    "positive" => ["6", 0], 
                    "negative" => ["7", 0], 
                    "latent_image" => ["5", 0]
                ]
            ],
            "8" => ["class_type" => "VAEDecode", "inputs" => ["samples" => ["3", 0], "vae" => ["4", 2]]],
            "9" => ["class_type" => "SaveImage", "inputs" => ["filename_prefix" => "Latent_", "images" => ["8", 0]]]
        ];
    }

    protected function buildControlNetWorkflow(array $p)
    {
        $cn = $p['controlnet'];
        $filename = $this->saveBase64Image($cn['image_base64']);
        $preprocessor = $cn['preprocessor'] ?? 'canny';
        
        // Get preprocessor node with parameters
        $preNode = $this->getPreprocessorNode($preprocessor, "10", $cn);
        
        $workflow = [
            "4"  => ["class_type" => "CheckpointLoaderSimple", "inputs" => ["ckpt_name" => $p['model']]],
            "10" => ["class_type" => "LoadImage", "inputs" => ["image" => $filename]],
            "11" => $preNode,
            "12" => ["class_type" => "ControlNetLoader", "inputs" => ["control_net_name" => $this->getControlNetModel($preprocessor)]],
            "5"  => ["class_type" => "EmptyLatentImage", "inputs" => ["width" => $p['width'], "height" => $p['height'], "batch_size" => 1]],
            "6"  => ["class_type" => "CLIPTextEncode", "inputs" => ["text" => $p['positive_prompt'], "clip" => ["4", 1]]],
            "7"  => ["class_type" => "CLIPTextEncode", "inputs" => ["text" => $p['negative_prompt'] ?? "", "clip" => ["4", 1]]],
            "13" => [
                "class_type" => "ControlNetApply", 
                "inputs" => [
                    "strength" => (float)($cn['strength'] ?? 0.85), 
                    "start_percent" => (float)($cn['start_percent'] ?? 0.0), 
                    "end_percent" => (float)($cn['end_percent'] ?? 1.0),
                    "conditioning" => ["6", 0], 
                    "control_net" => ["12", 0], 
                    "image" => ["11", 0]
                ]
            ],
            "3" => [
                "class_type" => "KSampler",
                "inputs" => [
                    "seed" => rand(1, 999999999), 
                    "steps" => $p['steps'], 
                    "cfg" => $p['cfg'],
                    "sampler_name" => $p['sampler'], 
                    "scheduler" => "karras", 
                    "denoise" => 1,
                    "model" => ["4", 0], 
                    "positive" => ["13", 0], 
                    "negative" => ["7", 0], 
                    "latent_image" => ["5", 0]
                ]
            ],
            "8" => ["class_type" => "VAEDecode", "inputs" => ["samples" => ["3", 0], "vae" => ["4", 2]]],
            "9" => ["class_type" => "SaveImage", "inputs" => ["filename_prefix" => "ControlNet_", "images" => ["8", 0]]]
        ];
        
        return $workflow;
    }

    /**
     * Get preprocessor node with dynamic parameters from frontend
     */
    protected function getPreprocessorNode($type, $imgNode, $params = [])
    {
        $nodes = [
            'canny' => [
                "class_type" => "Canny",
                "inputs" => [
                    "image" => [$imgNode, 0],
                    "low_threshold" => (float)($params['canny_low'] ?? 50) / 255,
                    "high_threshold" => (float)($params['canny_high'] ?? 150) / 255
                ]
            ],
            'depth' => [
                "class_type" => "DepthAnythingPreprocessor",
                "inputs" => [
                    "image" => [$imgNode, 0],
                    "resolution" => (int)($params['depth_resolution'] ?? 512)
                ]
            ],
            'openpose' => [
                "class_type" => "OpenposePreprocessor",
                "inputs" => [
                    "image" => [$imgNode, 0],
                    "detect_hand" => ($params['openpose_hands'] ?? 'enable') === 'enable' ? 'enable' : 'disable',
                    "detect_body" => ($params['openpose_body'] ?? 'enable') === 'enable' ? 'enable' : 'disable',
                    "detect_face" => ($params['openpose_face'] ?? 'disable') === 'enable' ? 'enable' : 'disable'
                ]
            ],
            'scribble' => [
                "class_type" => "ScribblePreprocessor",
                "inputs" => [
                    "image" => [$imgNode, 0],
                    "mode" => $params['scribble_mode'] ?? 'hed'
                ]
            ],
            'hed' => [
                "class_type" => "HEDPreprocessor",
                "inputs" => [
                    "image" => [$imgNode, 0],
                    "resolution" => (int)($params['hed_resolution'] ?? 512)
                ]
            ],
            'seg' => [
                "class_type" => "SemanticSegmentationPreprocessor",
                "inputs" => [
                    "image" => [$imgNode, 0],
                    "resolution" => (int)($params['seg_resolution'] ?? 512)
                ]
            ],
            'mlsd' => [
                "class_type" => "MLSDPreprocessor",
                "inputs" => [
                    "image" => [$imgNode, 0],
                    "score_threshold" => (float)($params['mlsd_score_thr'] ?? 0.1),
                    "dist_threshold" => (float)($params['mlsd_dist_thr'] ?? 0.1),
                    "resolution" => (int)($params['mlsd_resolution'] ?? 512)
                ]
            ],
            'normal' => [
                "class_type" => "NormalLineArtPreprocessor",
                "inputs" => [
                    "image" => [$imgNode, 0],
                    "resolution" => (int)($params['normal_resolution'] ?? 512)
                ]
            ],
        ];
        
        return $nodes[$type] ?? $nodes['canny'];
    }

    protected function getControlNetModel($type)
    {
        $models = [
            'canny'    => 'control_sd15_canny.pth',
            'depth'    => 'control_sd15_depth.pth',
            'openpose' => 'control_sd15_openpose.pth',
            'scribble' => 'control_sd15_scribble.pth',
            'hed'      => 'control_sd15_hed.pth',
            'seg'      => 'control_sd15_seg.pth',
            'mlsd'     => 'control_sd15_mlsd.pth',
            'normal'   => 'control_sd15_normal.pth',
        ];
        
        return $models[$type] ?? 'control_sd15_canny.pth';
    }

    protected function saveBase64Image($base64String)
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $type)) {
            $base64String = substr($base64String, strpos($base64String, ',') + 1);
        }
        $data = base64_decode($base64String);
        $filename = 'upload_' . time() . '_' . uniqid() . '.png';
        
        $inputPath = env('COMFYUI_INPUT_DIR', '/home/batkhuu/ComfyUI/input');
        if (!File::exists($inputPath)) {
            File::makeDirectory($inputPath, 0777, true);
        }
        
        File::put($inputPath . '/' . $filename, $data);
        return $filename;
    }

    // Proxy methods
    public function proxyObjectInfo()
    {
        $data = Http::get($this->comfyUrl . '/object_info')->json();
        return response()->json([
            'success'     => true,
            'checkpoints' => $data['CheckpointLoaderSimple']['input']['required']['ckpt_name'][0] ?? [],
            'controlnets' => $data['ControlNetLoader']['input']['required']['control_net_name'][0] ?? [],
            'samplers'    => $data['KSampler']['input']['required']['sampler_name'][0] ?? [],
            'schedulers'  => $data['KSampler']['input']['required']['scheduler_name'][0] ?? []
        ]);
    }

    public function proxyView(Request $request)
    {
        $res = Http::get($this->comfyUrl . '/view', $request->all());
        return response($res->body())->header('Content-Type', $res->header('Content-Type'));
    }

    public function proxyHistory() 
    { 
        return Http::get($this->comfyUrl . '/history')->json(); 
    }
    
    public function proxyQueue() 
    { 
        return Http::get($this->comfyUrl . '/queue')->json(); 
    }
    
    public function proxySystemStats() 
    { 
        return Http::get($this->comfyUrl . '/system_stats')->json(); 
    }
    
    public function proxyInterrupt() 
    { 
        return Http::post($this->comfyUrl . '/interrupt')->json(); 
    }
    
    public function proxyQueueDelete() 
    {
        return response()->json(['success' => Http::post($this->comfyUrl . '/queue', ['clear' => true])->successful()]);
    }

    public function health() 
    {
        try {
            $res = Http::timeout(2)->get($this->comfyUrl . "/system_stats");
            return response()->json(['status' => $res->successful() ? 'online' : 'offline']);
        } catch (\Exception $e) { 
            return response()->json(['status' => 'offline']); 
        }
    }
    
    public function getObjectInfo() 
    {
        try {
            $response = Http::get($this->comfyUrl . '/object_info');
            
            if (!$response->successful()) {
                return response()->json(['success' => false, 'error' => 'ComfyUI Offline'], 503);
            }

            $data = $response->json();
            $checkpoints = $data['CheckpointLoaderSimple']['input']['required']['ckpt_name'][0] ?? [];
            $controlnets = $data['ControlNetLoader']['input']['required']['control_net_name'][0] ?? [];

            return response()->json([
                'success' => true,
                'checkpoints' => $checkpoints,
                'controlnets' => $controlnets
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}