<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ComfyUIProxyController extends Controller
{
    protected $comfyUrl;

    public function __construct()
    {
        $this->comfyUrl = env('COMFYUI_URL', 'http://127.0.0.1:8188');
    }

    public function history(Request $request)
    {
        try {
            $response = Http::timeout(30)->get($this->comfyUrl . '/history');
            
            if ($response->successful()) {
                return response()->json($response->json());
            }
            
            return response()->json(['error' => 'Failed to fetch history'], $response->status());
            
        } catch (\Exception $e) {
            Log::error('History proxy error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function view(Request $request)
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
            
            $contentType = $response->header('Content-Type');
            if (!$contentType) {
                $contentType = 'image/png';
            }
            
            return response($response->body(), 200)
                ->header('Content-Type', $contentType)
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
                
        } catch (\Exception $e) {
            Log::error('View proxy error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function upload(Request $request)
    {
        if (!$request->hasFile('image')) {
            return response()->json(['error' => 'No image uploaded'], 400);
        }
        
        try {
            $file = $request->file('image');
            $response = Http::attach(
                'image',
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            )->post($this->comfyUrl . '/upload');
            
            if ($response->successful()) {
                return response()->json($response->json());
            }
            
            return response()->json(['error' => 'Upload failed'], $response->status());
            
        } catch (\Exception $e) {
            Log::error('Upload proxy error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function prompt(Request $request)
    {
        try {
            $response = Http::timeout(30)->post($this->comfyUrl . '/prompt', $request->all());
            
            if ($response->successful()) {
                return response()->json($response->json());
            }
            
            return response()->json(['error' => 'Prompt failed'], $response->status());
            
        } catch (\Exception $e) {
            Log::error('Prompt proxy error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function check(Request $request)
    {
        try {
            $response = Http::timeout(5)->get($this->comfyUrl . '/');
            
            return response()->json([
                'status' => $response->successful() ? 'online' : 'offline',
                'code' => $response->status(),
                'url' => $this->comfyUrl
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'offline',
                'error' => $e->getMessage()
            ], 503);
        }
    }
}