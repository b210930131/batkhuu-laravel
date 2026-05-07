<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use App\Services\PromptDictionaryService;

class ComfyUIController extends Controller
{
    protected $comfyUrl;

    public function __construct()
    {
        $this->comfyUrl = env('COMFYUI_URL', 'http://127.0.0.1:8188');
        Log::info('ComfyUI Controller initialized', ['url' => $this->comfyUrl]);
    }
     public function twenty(Request $request)
    {
        return view('partials.twenty');
    }

    /**
     * Main Generation Entry Point
     */
    public function generate(Request $request, PromptDictionaryService $promptService)
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
            'denoise' => 'nullable|numeric|min:0|max:1',
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
        ]);

        $clientId = $validated['client_id'] ?? 'laravel_' . uniqid();

        $originalPrompt = trim($validated['positive_prompt']);

        $processed = $promptService->buildCanonicalPrompt($originalPrompt);
        $canonicalPrompt = trim($processed['prompt'] ?? '');

        if ($canonicalPrompt === '') {
            $canonicalPrompt = $originalPrompt;
        }

        $validated['positive_prompt'] = $canonicalPrompt;

        if (empty($validated['negative_prompt'])) {
            $validated['negative_prompt'] = $processed['negative_prompt'];
        }

        Log::info('Prompt processed', [
            'original' => $originalPrompt,
            'canonical' => $validated['positive_prompt'],
        ]);

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

        $response = Http::timeout(300)->post($this->comfyUrl . '/prompt', [
            'prompt' => $workflow,
            'client_id' => $clientId,
        ]);

        if ($response->failed()) {
            throw new \Exception('ComfyUI Connection Failed: ' . $response->body());
        }

        $responseData = $response->json();
        if (!isset($responseData['prompt_id'])) {
            throw new \Exception('ComfyUI did not return a prompt_id');
        }

        $promptId = $responseData['prompt_id'];

        // 🔥 4. DB-д ORIGINAL + CANONICAL хадгал
        \App\Models\GeneratedImage::create([
            'user_id' => auth()->id(),
            'prompt_id' => $promptId,
            'file_name' => null,

            // 🔥 ШИНЭ 2 COLUMN
            'original_prompt' => $originalPrompt,
            'canonical_prompt' => $validated['positive_prompt'],

            // хуучин (compatibility)
            'positive_prompt' => $validated['positive_prompt'],

            'model_used' => $validated['model'],
            'width' => $validated['width'],
            'height' => $validated['height'],
        ]);

        return response()->json([
            'success' => true,
            'prompt_id' => $promptId,
            'client_id' => $clientId,
            'canonical_prompt' => $validated['positive_prompt'] // debug-д гоё
        ]);

    } catch (\Exception $e) {
        Log::error('Generation Failed: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}
    /**
     * SDXL Workflow with Base + Refiner
     */
    /**
     * SDXL Workflow with Base + Refiner
     * Fixed: Added $sharedSeed variable to prevent type mismatch
     */
    protected function buildSDXLWorkflow(array $p)
    {
        $baseSteps = $p['steps'] ?? 25; 
        $refinerSteps = $p['refiner_steps'] ?? 10;
        $totalSteps = $baseSteps + $refinerSteps;
        $sharedSeed = rand(1, 999999999); // Use one integer for both samplers

        return [
            "4" => ["class_type" => "CheckpointLoaderSimple", "inputs" => ["ckpt_name" => $p['model']]],
            "14" => ["class_type" => "CheckpointLoaderSimple", "inputs" => ["ckpt_name" => $p['refiner_model'] ?? 'sd_xl_refiner_1.0.safetensors']],
            
            "6" => ["class_type" => "CLIPTextEncode", "inputs" => ["text" => $p['positive_prompt'], "clip" => ["4", 1]]],
            "7" => ["class_type" => "CLIPTextEncode", "inputs" => ["text" => $p['negative_prompt'] ?? "", "clip" => ["4", 1]]],
            
            "15" => ["class_type" => "CLIPTextEncode", "inputs" => ["text" => $p['positive_prompt'], "clip" => ["14", 1]]],
            "16" => ["class_type" => "CLIPTextEncode", "inputs" => ["text" => $p['negative_prompt'] ?? "", "clip" => ["14", 1]]],
            
            "5" => ["class_type" => "EmptyLatentImage", "inputs" => ["width" => $p['width'], "height" => $p['height'], "batch_size" => 1]],

            "3" => [
                "class_type" => "KSampler",
                "inputs" => [
                    "seed" => $sharedSeed,
                    "steps" => $totalSteps,
                    "cfg" => $p['cfg'] ?? 7,
                    "sampler_name" => $p['sampler'] ?? 'dpmpp_2m',
                    "scheduler" => "karras",
                    "denoise" => (float)($p['denoise'] ?? 1.0),
                    "model" => ["4", 0],
                    "positive" => ["6", 0],
                    "negative" => ["7", 0],
                    "latent_image" => ["5", 0],
                    "start_at_step" => 0,
                    "end_at_step" => $baseSteps, 
                    "return_with_leftover_noise" => "enable" 
                ]
            ],

            "17" => [
                "class_type" => "KSampler",
                "inputs" => [
                    "seed" => $sharedSeed, // Fixed: Pass integer variable, not a latent link
                    "steps" => $totalSteps,
                    "cfg" => $p['cfg'] ?? 7,
                    "sampler_name" => $p['sampler'] ?? 'dpmpp_2m',
                    "scheduler" => "karras",
                    "denoise" => (float)($p['denoise'] ?? 1.0), 
                    "model" => ["14", 0],
                    "positive" => ["15", 0],
                    "negative" => ["16", 0],
                    "latent_image" => ["3", 0], 
                    "start_at_step" => $baseSteps,
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
                    "denoise" => (float)($p['denoise'] ?? 1),
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
        $controlnetType = $p['controlnet']['preprocessor'] ?? 'canny';
        $isSD35ControlNet = str_starts_with($controlnetType, 'sd35_');

        if ($isSD35ControlNet && !str_contains(strtolower($p['model']), 'sd3.5')) {
            $p['model'] = 'sd3.5_large_fp8_scaled.safetensors';
        }

        if ($isSD35ControlNet) {
            $p['steps'] = max((int)($p['steps'] ?? 35), 35);
            $p['cfg'] = (float)($p['cfg'] ?? 5);
            $p['scheduler'] = $p['scheduler'] ?? 'normal';
            $p['controlnet']['strength'] = (float)($p['controlnet']['strength'] ?? 0.55);
        }

        $filename = $this->saveBase64Image(
            $p['controlnet']['image_base64'],
            $controlnetType
        );
        
        return [
            "3" => [
                "class_type" => "KSampler",
                "inputs" => [
                    "seed" => rand(1, 999999999),
                    "steps" => $p['steps'] ?? 20,
                    "cfg" => $p['cfg'] ?? 7,
                    // "sampler_name" => $p['sampler'] ?? 'euler',
                    "sampler_name" => $p['sampler'] ?? 'dpmpp_2m',  // Өөрчлөх
                    // "scheduler" => "normal",
                    "scheduler" => $p['scheduler'] ?? 'normal', // Scheduler тусад нь
                    "denoise" => (float)($p['denoise'] ?? 1),
                    "model" => ["4", 0],
                    "positive" => ["13", 0],
                    "negative" => $isSD35ControlNet ? ["13", 1] : ["7", 0],
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
            "11" => $this->getPreprocessorNode($controlnetType, "10"),
            "12" => [
                "class_type" => "ControlNetLoader",
                "inputs" => [
                    "control_net_name" => $this->getControlNetModel($controlnetType)
                ]
            ],
            "13" => $isSD35ControlNet ? [
                "class_type" => "ControlNetApplySD3",
                "inputs" => [
                    "positive" => ["6", 0],
                    "negative" => ["7", 0],
                    "control_net" => ["12", 0],
                    "vae" => ["4", 2],
                    "image" => ["11", 0],
                    "strength" => (float)($p['controlnet']['strength'] ?? 0.85),
                    "start_percent" => (float)($p['controlnet']['start_percent'] ?? 0.0),
                    "end_percent" => (float)($p['controlnet']['end_percent'] ?? 1.0)
                ]
            ] : [
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
            
            // We return the full data so JS can pick what it needs
            return response()->json([
                'success' => true,
                'checkpoints' => $data['CheckpointLoaderSimple']['input']['required']['ckpt_name'][0] ?? [],
                'samplers' => $data['KSampler']['input']['required']['sampler_name'][0] ?? [],
            ]);
        }
        return response()->json(['error' => 'ComfyUI not responding'], 500);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
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

    protected function saveBase64Image($base64String, ?string $preprocessor = null)
    {
        $mimeType = 'image/png';
        if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $type)) {
            $mimeType = 'image/' . strtolower($type[1]);
            $base64String = substr($base64String, strpos($base64String, ',') + 1);
        }
        
        $data = base64_decode($base64String);
        if ($data === false) {
            throw new \Exception('Invalid input image data');
        }

        $extension = match ($mimeType) {
            'image/jpeg', 'image/jpg' => 'jpg',
            'image/webp' => 'webp',
            default => 'png',
        };
        $filename = 'upload_' . time() . '_' . uniqid() . '.' . $extension;
        
        $inputPath = env('COMFYUI_INPUT_DIR', '/home/batkhuu/ComfyUI/input');
        
        if (!File::exists($inputPath)) {
            File::makeDirectory($inputPath, 0777, true);
        }
        
        $fullPath = $inputPath . '/' . $filename;
        File::put($fullPath, $data);

        $userId = auth()->id();
        if ($userId) {
            try {
                $publicDir = public_path('input-images/' . $userId);
                if (!File::exists($publicDir)) {
                    File::makeDirectory($publicDir, 0777, true);
                }

                $publicPath = 'input-images/' . $userId . '/' . $filename;
                File::put(public_path($publicPath), $data);

                \App\Models\InputImage::create([
                    'user_id' => $userId,
                    'file_name' => $filename,
                    'path' => $publicPath,
                    'mime_type' => $mimeType,
                    'source_type' => 'controlnet',
                    'preprocessor' => $preprocessor,
                ]);
            } catch (\Throwable $e) {
                Log::warning('Input image gallery copy failed', [
                    'user_id' => $userId,
                    'file_name' => $filename,
                    'error' => $e->getMessage(),
                ]);
            }
        }
        
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
                "low_threshold" => 0.1,  // Default 0.4 (was 50/255)
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
        'sd35_canny' => [
            "class_type" => "Canny",
            "inputs" => [
                "image" => [$imgNode, 0],
                "low_threshold" => 0.1,
                "high_threshold" => 0.8
            ]
        ],
        'sd35_depth' => [
            "class_type" => "DepthAnythingPreprocessor",
            "inputs" => [
                "image" => [$imgNode, 0],
                "resolution" => 512
            ]
        ],
        'sd35_blur' => [
            "class_type" => "ImageBlur",
            "inputs" => [
                "image" => [$imgNode, 0],
                "blur_radius" => 5,
                "sigma" => 1.0
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
        'sd35_canny' => 'sd3.5_large_controlnet_canny-fp8.safetensors',
        'sd35_depth' => 'sd3.5_large_controlnet_depth-fp8.safetensors',
        'sd35_blur' => 'sd3.5_large_controlnet_blur-fp8.safetensors',
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

        //  Find the record created during the generate() call for this specific user
        $image = \App\Models\GeneratedImage::where('prompt_id', $validated['prompt_id'])
                    ->where('user_id', auth()->id())
                    ->first();

        if ($image) {
            $image->update([
            'file_name' => $validated['file_name'],
            'subfolder' => $request->input('subfolder', ''),
            'type' => $request->input('type', 'output'),
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

public function getModels()
{
    // This tells the frontend which checkpoints are available in your ComfyUI folder
    $models = [
        'sd_xl_base_1.0.safetensors',
        'v1-5-pruned-emaonly.safetensors',
        // Add your actual filenames here
    ];
    return response()->json($models);
}

public function getRefinerModels()
{
    $refiners = [
        'sd_xl_refiner_1.0.safetensors',
    ];
    return response()->json($refiners);
}
public function getUserImages()
{
    $images = \App\Models\GeneratedImage::with('galleryFolder')
                ->where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->get();

    return response()->json($images);
}

public function getUserInputImages()
{
    $images = \App\Models\InputImage::where('user_id', auth()->id())
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json($images);
}

public function deleteCustomerInputImage($id)
{
    $image = \App\Models\InputImage::where('user_id', auth()->id())->findOrFail($id);
    $this->deleteInputImageFile($image);
    $image->delete();

    return response()->json(['success' => true]);
}

public function getUserFolders()
{
    $folders = \App\Models\GalleryFolder::where('user_id', auth()->id())
        ->withCount('images')
        ->orderBy('name')
        ->get();

    return response()->json($folders);
}

public function storeUserFolder(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:80',
    ]);

    $name = trim($validated['name']);
    if ($name === '') {
        return response()->json(['message' => 'Folder name is required.'], 422);
    }

    $folder = \App\Models\GalleryFolder::firstOrCreate([
        'user_id' => auth()->id(),
        'name' => $name,
    ]);

    return response()->json($folder->loadCount('images'), 201);
}

public function deleteUserFolder($id)
{
    $folder = \App\Models\GalleryFolder::where('user_id', auth()->id())->findOrFail($id);

    \App\Models\GeneratedImage::where('user_id', auth()->id())
        ->where('gallery_folder_id', $folder->id)
        ->update(['gallery_folder_id' => null]);

    $folder->delete();

    return response()->json(['success' => true]);
}

public function moveCustomerImage(Request $request, $id)
{
    $validated = $request->validate([
        'folder_id' => 'nullable|integer|exists:gallery_folders,id',
    ]);

    $image = \App\Models\GeneratedImage::where('user_id', auth()->id())->findOrFail($id);
    $folderId = $validated['folder_id'] ?? null;

    if ($folderId) {
        \App\Models\GalleryFolder::where('user_id', auth()->id())->findOrFail($folderId);
    }

    $image->update(['gallery_folder_id' => $folderId]);

    return response()->json(['success' => true, 'image' => $image->fresh('galleryFolder')]);
}

public function getAdminFolders()
{
    $folders = \App\Models\GalleryFolder::with('user')
        ->withCount('images')
        ->orderBy('name')
        ->get();

    return response()->json($folders);
}

public function storeAdminFolder(Request $request)
{
    $validated = $request->validate([
        'user_id' => 'required|integer|exists:users,id',
        'name' => 'required|string|max:80',
    ]);

    $name = trim($validated['name']);
    if ($name === '') {
        return response()->json(['message' => 'Folder name is required.'], 422);
    }

    $folder = \App\Models\GalleryFolder::firstOrCreate([
        'user_id' => $validated['user_id'],
        'name' => $name,
    ]);

    return response()->json($folder->load(['user'])->loadCount('images'), 201);
}

public function deleteAdminFolder($id)
{
    $folder = \App\Models\GalleryFolder::findOrFail($id);

    \App\Models\GeneratedImage::where('gallery_folder_id', $folder->id)
        ->update(['gallery_folder_id' => null]);

    $folder->delete();

    return response()->json(['success' => true]);
}

public function moveAdminImage(Request $request, $id)
{
    $validated = $request->validate([
        'folder_id' => 'nullable|integer|exists:gallery_folders,id',
    ]);

    $image = \App\Models\GeneratedImage::findOrFail($id);
    $folderId = $validated['folder_id'] ?? null;

    if ($folderId) {
        $folder = \App\Models\GalleryFolder::findOrFail($folderId);
        if ((int) $folder->user_id !== (int) $image->user_id) {
            return response()->json(['message' => 'Folder owner must match image owner.'], 422);
        }
    }

    $image->update(['gallery_folder_id' => $folderId]);

    return response()->json(['success' => true, 'image' => $image->fresh(['user', 'galleryFolder'])]);
}
// public function adminAiStudio()
// {
//     $images = \App\Models\GeneratedImage::with('user')
//         ->orderBy('created_at', 'desc')
//         ->get();

//     return view('imagegen.admin.pages.admin', compact('images'));
// }

// public function customerAiStudio()
// {
//     return view('imagegen.customer.pages.customer');
// }
// app/Http/Controllers/ComfyUIController.php
public function getAllImages()
{
    $images = \App\Models\GeneratedImage::with(['user', 'galleryFolder'])
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json($images);
}

public function getAllInputImages()
{
    $images = \App\Models\InputImage::with('user')
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json($images);
}

public function deleteAdminInputImage($id)
{
    $image = \App\Models\InputImage::findOrFail($id);
    $this->deleteInputImageFile($image);
    $image->delete();

    return response()->json(['success' => true]);
}

protected function deleteInputImageFile(\App\Models\InputImage $image): void
{
    $publicFile = public_path($image->path);
    if ($image->path && file_exists($publicFile)) {
        unlink($publicFile);
    }

    $comfyInputDir = env('COMFYUI_INPUT_DIR', '/home/batkhuu/ComfyUI/input');
    $comfyFile = $comfyInputDir . '/' . $image->file_name;
    if ($image->file_name && file_exists($comfyFile)) {
        unlink($comfyFile);
    }
}
public function deleteCustomerImage($id)
{
    $image = \App\Models\GeneratedImage::where('user_id', auth()->id())->findOrFail($id);

    $relativePath = trim(($image->subfolder ? $image->subfolder . '/' : '') . $image->file_name, '/');

    $publicFile = public_path('outputs/' . $relativePath);
    if ($image->file_name && file_exists($publicFile)) {
        unlink($publicFile);
    }

    $comfyOutputDir = env('COMFYUI_OUTPUT_DIR', '/home/batkhuu/ComfyUI/output');
    $comfyFile = $comfyOutputDir . '/' . $relativePath;

    if ($image->file_name && file_exists($comfyFile)) {
        unlink($comfyFile);
    }

    $image->delete();

    return response()->json([
        'success' => true,
        'message' => 'Image deleted successfully'
    ]);
}
public function deleteImage($id)
{
    $image = \App\Models\GeneratedImage::findOrFail($id);

    $relativePath = trim(($image->subfolder ? $image->subfolder . '/' : '') . $image->file_name, '/');

    $publicFile = public_path('outputs/' . $relativePath);
    if ($image->file_name && file_exists($publicFile)) {
        unlink($publicFile);
    }

    $comfyOutputDir = env('COMFYUI_OUTPUT_DIR', '/home/batkhuu/ComfyUI/output');
    $comfyFile = $comfyOutputDir . '/' . $relativePath;

    if ($image->file_name && file_exists($comfyFile)) {
        unlink($comfyFile);
    }

    $image->delete();

    return response()->json(['success' => true]);
}
public function customerGallery()
{
    $images = \App\Models\GeneratedImage::with('galleryFolder')
        ->where('user_id', auth()->id())
        ->whereNotNull('file_name')
        ->orderBy('created_at', 'desc')
        ->get();

    $folders = \App\Models\GalleryFolder::where('user_id', auth()->id())
        ->withCount('images')
        ->orderBy('name')
        ->get();

    return view('imagegen.customer.pages.gallery', [
        'initialImages' => $images,
        'initialFolders' => $folders,
    ]);
}

public function adminGallery()
{
    return view('imagegen.admin.pages.admin-gallery');
}

public function customerInputImages()
{
    return view('imagegen.customer.pages.input-images');
}

public function adminInputImages()
{
    return view('imagegen.admin.pages.input-images');
}
public function customerAiStudio()
{
    return $this->aiStudioView('customer');
}

public function adminAiStudio()
{
    return $this->aiStudioView('admin');
}

protected function aiStudioView(string $panel)
{
    $isAdmin = $panel === 'admin';

    return view('imagegen.shared.ai-studio', [
        'layout' => $isAdmin ? 'imagegen.admin.layouts.app' : 'imagegen.customer.layouts.app',
        'title' => 'AI Studio',
        'pageTitle' => 'EiT',
        'pageSubtitle' => $isAdmin
            ? 'Generate images, use ControlNet, and manage all outputs'
            : 'Create stylish AI-generated images with modern controls',
        'panelLabel' => $isAdmin ? 'Admin' : 'Customer',
        'heroTitle' => $isAdmin ? 'Image Generation Control Center' : 'Creative Workspace',
        'heroSubtitle' => $isAdmin
            ? 'Full model selection, ControlNet preprocessing, and gallery management from one page.'
            : 'Model selection, ControlNet preprocessing, and your gallery management from one page.',
        'accessLabel' => $isAdmin ? 'Admin' : 'Customer',
        'featureLabel' => $isAdmin ? 'Full Studio' : 'Studio',
        'workspaceHint' => $isAdmin ? 'Full admin generation workspace' : 'Customer generation workspace',
        'studioBadge' => $isAdmin ? 'Admin Tools' : 'Creator Tools',
        'canChooseFolderUser' => $isAdmin,
        'studioRoutes' => [
            'requiresFolderUser' => $isAdmin,
            'inputImages' => $isAdmin ? url('/admin/api/input-images') : url('/customer/api/input-images'),
            'folders' => $isAdmin ? url('/admin/api/folders') : url('/customer/api/folders'),
            'images' => $isAdmin ? url('/admin/api/images') : url('/customer/api/images'),
            'imageFolderBase' => $isAdmin ? url('/admin/gallery') : url('/customer/api/images'),
            'imageDeleteBase' => $isAdmin ? url('/admin/gallery/delete') : url('/customer/api/images'),
        ],
    ]);
}
}
