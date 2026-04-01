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
        Log::info('ComfyUI Controller initialized', ['url' => $this->comfyUrl]);
    }

    /**
     * Main Generation Entry Point
     */
    public function generate(Request $request)
{
    Log::info('Generate request received', $request->all());
    
    try {
        $validated = $request->validate([
            'client_id' => 'nullable|string',
            'model' => 'required|string',
            'refiner_model' => 'nullable|string',
            'positive_prompt' => 'required|string',
            'negative_prompt' => 'nullable|string',
            'steps' => 'integer|min:1|max:100',
            'refiner_steps' => 'nullable|integer|min:1|max:100',
            'cfg' => 'numeric|min:1|max:20',
            'width' => 'integer|min:256|max:1536',
            'height' => 'integer|min:256|max:1536',
            'sampler' => 'string',
            'controlnet' => 'nullable|array',
            'controlnet.enabled' => 'boolean',
            'controlnet.preprocessor' => 'nullable|string',
            'controlnet.image_base64' => 'nullable|string',
            'controlnet.strength' => 'nullable|numeric',
        ]);

        $clientId = $validated['client_id'] ?? 'laravel_' . uniqid();
        
        // Workflow Selection Logic
        $isSDXL = str_contains(strtolower($validated['model']), 'sdxl') || str_contains(strtolower($validated['model']), 'sd_xl');
        $hasRefiner = !empty($validated['refiner_model']);

        if ($isSDXL || $hasRefiner) {
            $workflow = $this->buildSDXLWorkflow($validated);
        } elseif (!empty($validated['controlnet']['enabled'])) {
            $workflow = $this->buildControlNetWorkflow($validated);
        } else {
            $workflow = $this->buildBaseWorkflow($validated);
        }

        // Send to ComfyUI
        $response = Http::timeout(300)->post($this->comfyUrl . '/prompt', [
            'prompt' => $workflow,
            'client_id' => $clientId,
        ]);

        if ($response->failed()) {
            throw new \Exception('ComfyUI Connection Failed: ' . $response->body());
        }

        $responseData = $response->json();
        
        // Ensure prompt_id exists in response
        if (!isset($responseData['prompt_id'])) {
            throw new \Exception('ComfyUI did not return a prompt_id');
        }

        $promptId = $responseData['prompt_id'];

        // Create the record
        \App\Models\GeneratedImage::create([
            'user_id' => auth()->id(),
            'prompt_id' => $promptId, 
            'file_name' => null,     
            'positive_prompt' => $validated['positive_prompt'],
            'model_used' => $validated['model'],
            'width' => $validated['width'],
            'height' => $validated['height'],
        ]);

        return response()->json([
            'success' => true, 
            'prompt_id' => $promptId,
            'client_id' => $clientId
        ]);

    } catch (\Exception $e) {
        Log::error('Generation Failed: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}
    /**
     * SDXL Workflow with Base + Refiner
     */
    protected function buildSDXLWorkflow(array $p)
{
    $baseSteps = $p['steps'] ?? 25; 
    $refinerSteps = $p['refiner_steps'] ?? 10;
    $totalSteps = $baseSteps + $refinerSteps; // Important: Samplers must know total count

    return [
        "4" => ["class_type" => "CheckpointLoaderSimple", "inputs" => ["ckpt_name" => $p['model']]],
        "14" => ["class_type" => "CheckpointLoaderSimple", "inputs" => ["ckpt_name" => $p['refiner_model'] ?? 'sd_xl_refiner_1.0.safetensors']],
        
        // Base Encoders
        "6" => ["class_type" => "CLIPTextEncode", "inputs" => ["text" => $p['positive_prompt'], "clip" => ["4", 1]]],
        "7" => ["class_type" => "CLIPTextEncode", "inputs" => ["text" => $p['negative_prompt'] ?? "", "clip" => ["4", 1]]],
        
        // Refiner Encoders (using same prompts but refiner CLIP)
        "15" => ["class_type" => "CLIPTextEncode", "inputs" => ["text" => $p['positive_prompt'], "clip" => ["14", 1]]],
        "16" => ["class_type" => "CLIPTextEncode", "inputs" => ["text" => $p['negative_prompt'] ?? "", "clip" => ["14", 1]]],
        
        "5" => ["class_type" => "EmptyLatentImage", "inputs" => ["width" => $p['width'], "height" => $p['height'], "batch_size" => 1]],

        // BASE SAMPLER: Run from 0 to 25
        "3" => [
            "class_type" => "KSampler",
            "inputs" => [
                "seed" => rand(1, 999999999),
                // "steps" => $totalSteps,
                "steps" => 30, // Total steps
                "end_at_step" => 20, // Base stops at 20
                "cfg" => $p['cfg'] ?? 7,
                "sampler_name" => $p['sampler'] ?? 'dpmpp_2m',
                "scheduler" => "karras",
                "denoise" => 1.0,
                "model" => ["4", 0],
                "positive" => ["6", 0],
                "negative" => ["7", 0],
                "latent_image" => ["5", 0],
                "start_at_step" => 0,
                "end_at_step" => $baseSteps, 
                "return_with_leftover_noise" => "enable" // Keep noise for refiner
            ]
        ],

        // REFINER SAMPLER: Start at 25, finish at end
        "17" => [
            "class_type" => "KSampler",
            "inputs" => [
                "seed" => ["3", 0], // Same seed
                "steps" => $totalSteps,
                "cfg" => $p['cfg'] ?? 7,
                "sampler_name" => $p['sampler'] ?? 'dpmpp_2m',
                "scheduler" => "karras",
                "denoise" => 1.0, 
                "model" => ["14", 0],
                "positive" => ["15", 0],
                "negative" => ["16", 0],
                "latent_image" => ["3", 0], // LINK: Input is output of Base
                // "start_at_step" => $baseSteps,
                "start_at_step" => 20,
                "end_at_step" => 10000,
                "return_with_leftover_noise" => "disable"
            ]
        ],

        "8" => ["class_type" => "VAEDecode", "inputs" => ["samples" => ["17", 0], "vae" => ["14", 2]]],
        "9" => ["class_type" => "SaveImage", "inputs" => ["filename_prefix" => "SDXL_Chained", "images" => ["8", 0]]]
    ];
}

    /**
     * Base SD1.5 Workflow
     */
    protected function buildBaseWorkflow(array $p)
    {
        return [
            "3" => [
                "class_type" => "KSampler",
                "inputs" => [
                    "seed" => rand(1, 999999999),
                    "steps" => $p['steps'] ?? 20,
                    "cfg" => $p['cfg'] ?? 7,
                    "sampler_name" => $p['sampler'] ?? 'euler',
                    "scheduler" => "normal",
                    "denoise" => 1,
                    "model" => ["4", 0],
                    "positive" => ["6", 0],
                    "negative" => ["7", 0],
                    "latent_image" => ["5", 0]
                ]
            ],
            "4" => [
                "class_type" => "CheckpointLoaderSimple",
                "inputs" => ["ckpt_name" => $p['model']]
            ],
            "5" => [
                "class_type" => "EmptyLatentImage",
                "inputs" => [
                    "width" => $p['width'],
                    "height" => $p['height'],
                    "batch_size" => 1
                ]
            ],
            "6" => [
                "class_type" => "CLIPTextEncode",
                "inputs" => [
                    "text" => $p['positive_prompt'],
                    "clip" => ["4", 1]
                ]
            ],
            "7" => [
                "class_type" => "CLIPTextEncode",
                "inputs" => [
                    "text" => $p['negative_prompt'] ?? "",
                    "clip" => ["4", 1]
                ]
            ],
            "8" => [
                "class_type" => "VAEDecode",
                "inputs" => [
                    "samples" => ["3", 0],
                    "vae" => ["4", 2]
                ]
            ],
            "9" => [
                "class_type" => "SaveImage",
                "inputs" => [
                    "filename_prefix" => "ComfyUI_",
                    "images" => ["8", 0]
                ]
            ]
        ];
    }

    /**
     * ControlNet Workflow
     */
    protected function buildControlNetWorkflow(array $p)
    {
        $filename = $this->saveBase64Image($p['controlnet']['image_base64']);
        
        return [
            "3" => [
                "class_type" => "KSampler",
                "inputs" => [
                    "seed" => rand(1, 999999999),
                    "steps" => $p['steps'] ?? 20,
                    "cfg" => $p['cfg'] ?? 7,
                    "sampler_name" => $p['sampler'] ?? 'euler',
                    "scheduler" => "normal",
                    "denoise" => 1,
                    "model" => ["4", 0],
                    "positive" => ["13", 0],
                    "negative" => ["7", 0],
                    "latent_image" => ["5", 0]
                ]
            ],
            "4" => [
                "class_type" => "CheckpointLoaderSimple",
                "inputs" => ["ckpt_name" => $p['model']]
            ],
            "5" => [
                "class_type" => "EmptyLatentImage",
                "inputs" => [
                    "width" => $p['width'],
                    "height" => $p['height'],
                    "batch_size" => 1
                ]
            ],
            "6" => [
                "class_type" => "CLIPTextEncode",
                "inputs" => [
                    "text" => $p['positive_prompt'],
                    "clip" => ["4", 1]
                ]
            ],
            "7" => [
                "class_type" => "CLIPTextEncode",
                "inputs" => [
                    "text" => $p['negative_prompt'] ?? "",
                    "clip" => ["4", 1]
                ]
            ],
            "8" => [
                "class_type" => "VAEDecode",
                "inputs" => [
                    "samples" => ["3", 0],
                    "vae" => ["4", 2]
                ]
            ],
            "9" => [
                "class_type" => "SaveImage",
                "inputs" => [
                    "filename_prefix" => "ControlNet_",
                    "images" => ["8", 0]
                ]
            ],
            "10" => [
                "class_type" => "LoadImage",
                "inputs" => ["image" => $filename]
            ],
            "11" => $this->getPreprocessorNode($p['controlnet']['preprocessor'] ?? 'canny', "10"),
            "12" => [
                "class_type" => "ControlNetLoader",
                "inputs" => [
                    "control_net_name" => $this->getControlNetModel($p['controlnet']['preprocessor'] ?? 'canny')
                ]
            ],
            "13" => [
                "class_type" => "ControlNetApply",
                "inputs" => [
                    "strength" => (float)($p['controlnet']['strength'] ?? 0.85),
                    "conditioning" => ["6", 0],
                    "control_net" => ["12", 0],
                    "image" => ["11", 0]
                ]
            ]
        ];
    }

    public function getCheckpoints()
{
    try {
        $response = Http::timeout(30)->get($this->comfyUrl . '/object_info');
        if ($response->successful()) {
            $data = $response->json();
            $checkpoints = $data['CheckpointLoaderSimple']['input']['required']['ckpt_name'][0] ?? [];
            return response()->json([
                'success' => true,
                'checkpoints' => $checkpoints
            ]);
        }
        return response()->json(['success' => false, 'error' => 'Failed to fetch checkpoints'], 500);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}
    // Proxy methods
    public function proxyHistory()
{
    try {
        // Clear any previous output
        if (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();
        
        $response = Http::timeout(30)->get($this->comfyUrl . '/history');
        
        if ($response->successful()) {
            // Clear output buffer before returning
            ob_end_clean();
            return response()->json($response->json())
                ->header('Content-Type', 'application/json');
        }
        
        ob_end_clean();
        return response()->json(['error' => 'Failed to fetch history'], $response->status());
        
    } catch (\Exception $e) {
        ob_end_clean();
        Log::error('History proxy error: ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
    public function proxyView(Request $request)
    {
        $filename = $request->query('filename');
        $subfolder = $request->query('subfolder', '');
        $type = $request->query('type', 'output');
        
        if (!$filename) {
            return response()->json(['error' => 'Filename required'], 400);
        }
        
        try {
            $url = $this->comfyUrl . "/view";
            $params = [
                'filename' => $filename,
                'type' => $type,
                '_' => time()
            ];
            
            if ($subfolder) {
                $params['subfolder'] = $subfolder;
            }
            
            $response = Http::timeout(30)->get($url, $params);
            
            if ($response->failed()) {
                return response()->json(['error' => 'Image not found'], 404);
            }
            
            $contentType = $response->header('Content-Type') ?: 'image/png';
            
            return response($response->body(), 200)
                ->header('Content-Type', $contentType)
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
                
        } catch (\Exception $e) {
            Log::error('View proxy error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function proxyQueue()
    {
        try {
            $response = Http::timeout(30)->get($this->comfyUrl . '/queue');
            if ($response->successful()) {
                return response()->json($response->json());
            }
            return response()->json(['error' => 'Failed to fetch queue'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function proxyQueueDelete()
    {
        try {
            $response = Http::timeout(30)->post($this->comfyUrl . '/queue', ['clear' => true]);
            return response()->json(['success' => $response->successful()]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function proxySystemStats()
    {
        try {
            $response = Http::timeout(30)->get($this->comfyUrl . '/system_stats');
            if ($response->successful()) {
                return response()->json($response->json());
            }
            return response()->json(['error' => 'Failed to fetch system stats'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function proxyInterrupt()
    {
        try {
            $response = Http::timeout(30)->post($this->comfyUrl . '/interrupt');
            return response()->json(['success' => $response->successful()]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function proxyObjectInfo()
    {
        try {
            $response = Http::timeout(30)->get($this->comfyUrl . '/object_info');
            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'checkpoints' => $data['CheckpointLoaderSimple']['input']['required']['ckpt_name'][0] ?? [],
                    'controlnets' => $data['ControlNetLoader']['input']['required']['control_net_name'][0] ?? [],
                    'samplers' => $data['KSampler']['input']['required']['sampler_name'][0] ?? [],
                ]);
            }
            return response()->json(['success' => false, 'error' => 'Failed to fetch object info'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
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

    public function debugModels()
    {
        try {
            $response = Http::get($this->comfyUrl . '/object_info');
            $data = $response->json();
            
            return response()->json([
                'success' => true,
                'comfyui_url' => $this->comfyUrl,
                'checkpoints' => $data['CheckpointLoaderSimple']['input']['required']['ckpt_name'][0] ?? [],
                'controlnets' => $data['ControlNetLoader']['input']['required']['control_net_name'][0] ?? [],
                'samplers' => $data['KSampler']['input']['required']['sampler_name'][0] ?? [],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'comfyui_url' => $this->comfyUrl
            ], 500);
        }
    }

    protected function saveBase64Image($base64String)
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $type)) {
            $base64String = substr($base64String, strpos($base64String, ',') + 1);
        }
        
        $data = base64_decode($base64String);
        $filename = 'upload_' . time() . '_' . rand(1000, 9999) . '.png';
        
        $inputPath = env('COMFYUI_INPUT_DIR', '/home/batkhuu/ComfyUI/input');
        
        if (!File::exists($inputPath)) {
            File::makeDirectory($inputPath, 0777, true);
        }
        
        $fullPath = $inputPath . '/' . $filename;
        File::put($fullPath, $data);
        
        Log::info('Image saved', ['path' => $fullPath]);
        
        return $filename;
    }

    protected function getPreprocessorNode($type, $imgNode)
{
    $nodes = [
        'canny' => [
            "class_type" => "Canny", 
            "inputs" => [
                "image" => [$imgNode, 0], 
                "low_threshold" => 0.4,  // Default 0.4 (was 50/255)
                "high_threshold" => 0.8   // Default 0.8 (was 150/255)
            ]
        ],
        'depth' => [
            "class_type" => "DepthAnythingPreprocessor", 
            "inputs" => [
                "image" => [$imgNode, 0], 
                "resolution" => 512
            ]
        ],
        'openpose' => [
            "class_type" => "OpenposePreprocessor", 
            "inputs" => [
                "image" => [$imgNode, 0], 
                "detect_hand" => "enable", 
                "detect_body" => "enable", 
                "detect_face" => "disable"
            ]
        ],
        'scribble' => [
            "class_type" => "ScribblePreprocessor", 
            "inputs" => ["image" => [$imgNode, 0]]
        ],
        'mlsd' => [
            "class_type" => "M-LSDPreprocessor", 
            "inputs" => [
                "image" => [$imgNode, 0],
                "score_threshold" => 0.1,
                "dist_threshold" => 0.1  // Fixed: was "distance_threshold", now "dist_threshold"
            ]
        ],
        'hed' => [
            "class_type" => "HEDPreprocessor", 
            "inputs" => [
                "image" => [$imgNode, 0], 
                "resolution" => 512
            ]
        ],
        'seg' => [
            "class_type" => "SegPreprocessor", 
            "inputs" => [
                "image" => [$imgNode, 0], 
                "resolution" => 512
            ]
        ],
        'normal' => [
            "class_type" => "NormalMapPreprocessor", 
            "inputs" => [
                "image" => [$imgNode, 0], 
                "resolution" => 512
            ]
        ],
    ];
    
    return $nodes[$type] ?? $nodes['canny'];
}

    protected function getControlNetModel($type)
{
    $models = [
        'canny' => 'control_sd15_canny.pth',
        'depth' => 'control_sd15_depth.pth',
        'openpose' => 'control_sd15_openpose.pth',
        'scribble' => 'control_sd15_scribble.pth',
        'mlsd' => 'control_sd15_mlsd.pth',
        'hed' => 'control_sd15_hed.pth',
        'seg' => 'control_sd15_seg.pth',
        'normal' => 'control_sd15_normal.pth',
    ];
    
    // For SDXL models, use SDXL ControlNets if available
    // This will be handled by the frontend passing the correct model
    
    return $models[$type] ?? 'control_sd15_canny.pth';
}
public function myGallery()
{
    // Get images for current user, showing most recent first
    $images = \App\Models\GeneratedImage::where('user_id', auth()->id())
        ->orderBy('created_at', 'desc')
        ->paginate(12);

    return view('gallery.index', compact('images'));
}

// Add this to ComfyUIController.php

public function finalizeImage(Request $request)
{
    Log::info('Finalizing image record', $request->all());

    try {
        $validated = $request->validate([
            'prompt_id' => 'required|string',
            'file_name' => 'required|string'
        ]);

        // Find the record created during the generate() call for this specific user
        $image = \App\Models\GeneratedImage::where('prompt_id', $validated['prompt_id'])
                    ->where('user_id', auth()->id())
                    ->first();

        if ($image) {
            $image->update([
                'file_name' => $validated['file_name']
            ]);
            
            Log::info('Image record updated successfully', ['id' => $image->id]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Record not found'], 404);

    } catch (\Exception $e) {
        Log::error('Finalize Failed: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}
}