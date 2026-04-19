<?php

namespace App\Http\Helpers;

use Illuminate\Support\Facades\Log;

class ComfyUIHelper
{
    public static function saveImageToComfyUI($base64Image, $filename)
    {
        // First, try the user's ComfyUI input directory
        $comfyInputDir = env('COMFYUI_INPUT_DIR', '/home/batkhuu/ComfyUI/input');
        
        // Fallback paths
        $possiblePaths = [
            $comfyInputDir,
            storage_path('app/comfyui/input'),
            public_path('comfyui_input'),
            sys_get_temp_dir() . '/comfyui_input',
            '/tmp/comfyui_input'
        ];
        
        $successPath = null;
        
        foreach ($possiblePaths as $path) {
            try {
                // Create directory if it doesn't exist
                if (!file_exists($path)) {
                    @mkdir($path, 0777, true);
                }
                
                // Check if directory is writable
                if (!is_writable($path)) {
                    continue;
                }
                
                $fullPath = $path . '/' . $filename;
                
                // Clean base64 data
                $cleanBase64 = $base64Image;
                if (strpos($cleanBase64, 'base64,') !== false) {
                    $cleanBase64 = explode('base64,', $cleanBase64)[1];
                }
                
                $cleanBase64 = preg_replace('/\s+/', '', $cleanBase64);
                $imageData = base64_decode($cleanBase64);
                
                if ($imageData === false) {
                    throw new \Exception('Failed to decode base64 image');
                }
                
                // Save the file
                if (file_put_contents($fullPath, $imageData) !== false) {
                    $successPath = $fullPath;
                    Log::info("Image saved successfully", ['path' => $fullPath]);
                    break;
                }
                
            } catch (\Exception $e) {
                Log::warning("Failed to save to {$path}: " . $e->getMessage());
                continue;
            }
        }
        
        if (!$successPath) {
            throw new \Exception('Failed to save image to any location');
        }
        
        return $filename;
    }
}