<?php

namespace App\Services;

class ComfyUIService
{
    protected $host;
    protected $port;
    protected $inputPath;
    protected $outputPath;
    
    public function __construct()
    {
        $this->host = config('comfyui.host', '127.0.0.1');
        $this->port = config('comfyui.port', 8188);
        $this->inputPath = config('comfyui.input_path', '/home/batkhuu/ComfyUI/input');
        $this->outputPath = config('comfyui.output_path', '/home/batkhuu/ComfyUI/output');
    }
    
    public function checkConnection()
    {
        try {
            $response = Http::timeout(5)->get("http://{$this->host}:{$this->port}/");
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
    
    public function queuePrompt($workflow, $clientId)
    {
        $response = Http::timeout(180)->post("http://{$this->host}:{$this->port}/prompt", [
            'prompt' => $workflow,
            'client_id' => $clientId
        ]);
        
        if ($response->successful()) {
            return $response->json();
        }
        
        throw new \Exception('Failed to queue prompt: ' . $response->body());
    }