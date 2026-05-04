<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class BlenderController extends Controller
{
    public function adminStudio()
    {
        return view('imagegen.shared.blender-studio', [
            'layout' => 'imagegen.admin.layouts.app',
            'panel' => 'admin',
        ]);
    }

    public function customerStudio()
    {
        return view('imagegen.shared.blender-studio', [
            'layout' => 'imagegen.customer.layouts.app',
            'panel' => 'customer',
        ]);
    }

    public function render(Request $request)
    {
        $validated = $request->validate([
            'room.width' => 'required|numeric|min:2|max:20',
            'room.length' => 'required|numeric|min:2|max:30',
            'room.height' => 'required|numeric|min:2|max:6',
            'openings' => 'array',
            'openings.*.type' => 'required|string|in:door,window,balcony',
            'openings.*.wall' => 'required|string|in:north,south,east,west',
            'openings.*.position' => 'required|numeric|min:0',
            'openings.*.width' => 'required|numeric|min:0.2|max:6',
            'openings.*.height' => 'required|numeric|min:0.2|max:4',
            'openings.*.sill' => 'nullable|numeric|min:0|max:3',
            'columns' => 'array',
            'columns.*.x' => 'required|numeric|min:0',
            'columns.*.y' => 'required|numeric|min:0',
            'columns.*.width' => 'required|numeric|min:0.1|max:2',
            'columns.*.depth' => 'required|numeric|min:0.1|max:2',
            'beams' => 'array',
            'beams.*.direction' => 'required|string|in:x,y',
            'beams.*.position' => 'required|numeric|min:0',
            'beams.*.width' => 'required|numeric|min:0.1|max:2',
            'beams.*.depth' => 'required|numeric|min:0.1|max:2',
            'electrical' => 'array',
            'electrical.*.type' => 'required|string|in:switch,socket',
            'electrical.*.wall' => 'required|string|in:north,south,east,west',
            'electrical.*.position' => 'required|numeric|min:0',
            'electrical.*.height' => 'required|numeric|min:0.1|max:2.5',
            'camera.preset' => 'required|string',
            'camera.x' => 'required|numeric',
            'camera.y' => 'required|numeric',
            'camera.target_x' => 'required|numeric',
            'camera.target_y' => 'required|numeric',
            'camera.height' => 'required|numeric|min:0.5|max:4',
            'camera.fov' => 'required|numeric|min:25|max:90',
        ]);

        $blender = $this->resolveBlenderBinary();
        $script = env('BLENDER_ROOM_SCRIPT', 'C:/ai_blender_pipeline/scripts/create_room.py');
        $pipelineInputDir = '/mnt/c/ai_blender_pipeline/inputs';
        $pipelineOutputDir = '/mnt/c/ai_blender_pipeline/outputs/renders';

        if (!$blender) {
            return response()->json(['success' => false, 'error' => 'Windows Blender was not found.'], 500);
        }

        if (!File::exists('/mnt/c/ai_blender_pipeline/scripts/create_room.py')) {
            return response()->json(['success' => false, 'error' => 'C:/ai_blender_pipeline/scripts/create_room.py is missing.'], 500);
        }

        $userId = auth()->id();
        $outputDir = public_path('input-images/' . $userId);
        if (!File::exists($outputDir)) {
            File::makeDirectory($outputDir, 0777, true);
        }
        if (!File::exists($pipelineInputDir)) {
            File::makeDirectory($pipelineInputDir, 0777, true);
        }
        if (!File::exists($pipelineOutputDir)) {
            File::makeDirectory($pipelineOutputDir, 0777, true);
        }

        $fileName = 'blender_room_' . time() . '_' . uniqid() . '.png';
        $pipelineInputName = 'room_' . $userId . '_' . time() . '.json';
        $pipelineInputPath = $pipelineInputDir . '/' . $pipelineInputName;
        $pipelineOutputPath = $pipelineOutputDir . '/' . $fileName;
        $windowsInputPath = 'C:/ai_blender_pipeline/inputs/' . $pipelineInputName;
        $windowsOutputPath = 'C:/ai_blender_pipeline/outputs/renders/' . $fileName;
        $appOutputPath = $outputDir . '/' . $fileName;

        File::put($pipelineInputPath, json_encode(
            $this->buildPipelinePayload($validated, $windowsOutputPath),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        ));

        $process = new Process([
            $blender,
            '--background',
            '--python',
            $script,
            '--',
            $windowsInputPath,
        ]);
        $process->setTimeout(300);

        try {
            $process->run();
        } catch (\Throwable $e) {
            Log::error('Blender process failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Blender could not start. Check Windows Blender path.',
            ], 500);
        }

        if (!$process->isSuccessful() || !File::exists($pipelineOutputPath)) {
            Log::error('Blender render failed', [
                'exit_code' => $process->getExitCode(),
                'output' => $process->getOutput(),
                'error' => $process->getErrorOutput(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Blender render failed. Check Blender installation and script output.',
            ], 500);
        }

        File::copy($pipelineOutputPath, $appOutputPath);

        $publicPath = 'input-images/' . $userId . '/' . $fileName;
        \App\Models\InputImage::create([
            'user_id' => $userId,
            'file_name' => $fileName,
            'path' => $publicPath,
            'mime_type' => 'image/png',
            'source_type' => 'blender',
            'preprocessor' => 'room-render',
        ]);

        return response()->json([
            'success' => true,
            'image' => [
                'file_name' => $fileName,
                'path' => $publicPath,
                'url' => '/' . $publicPath,
            ],
        ]);
    }

    private function resolveBlenderBinary(): ?string
    {
        $configured = env('BLENDER_BIN');
        if ($configured) {
            return $configured;
        }

        $candidates = [
            '/mnt/c/Program Files/Blender Foundation/Blender 5.0/blender.exe',
            '/mnt/c/Program Files/Blender Foundation/Blender 4.3/blender.exe',
            '/mnt/c/Program Files/Blender Foundation/Blender 4.2/blender.exe',
            '/mnt/c/Program Files/Blender Foundation/Blender 4.1/blender.exe',
            '/mnt/c/Program Files/Blender Foundation/Blender 4.0/blender.exe',
        ];

        foreach ($candidates as $candidate) {
            if (File::exists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function buildPipelinePayload(array $data, string $outputPath): array
    {
        $room = $data['room'];
        $length = (float) $room['length'];
        $rightWallItems = collect($data['openings'] ?? [])->where('wall', 'east')->values();
        $glassDoor = $rightWallItems->firstWhere('type', 'balcony')
            ?: $rightWallItems->firstWhere('type', 'door')
            ?: ['position' => 0.7, 'width' => 0.9, 'height' => 2.2];
        $window = $rightWallItems->firstWhere('type', 'window')
            ?: ['position' => 2.4, 'width' => 1.6, 'height' => 1.2, 'sill' => 0.9];
        $camera = $data['camera'];

        return [
            'room' => [
                'width' => (float) $room['width'],
                'left_length' => $length,
                'right_length' => $length,
                'height' => (float) $room['height'],
                'wall_thickness' => 0.12,
            ],
            'right_wall' => [
                'glass_door' => [
                    'enabled' => (bool) $rightWallItems->whereIn('type', ['balcony', 'door'])->count(),
                    'center_y' => $this->wallPositionToCenteredY((float) $glassDoor['position'], (float) $glassDoor['width'], $length),
                    'width' => (float) $glassDoor['width'],
                    'height' => (float) $glassDoor['height'],
                ],
                'window' => [
                    'enabled' => (bool) $rightWallItems->where('type', 'window')->count(),
                    'center_y' => $this->wallPositionToCenteredY((float) $window['position'], (float) $window['width'], $length),
                    'width' => (float) $window['width'],
                    'height' => (float) $window['height'],
                    'sill_height' => (float) ($window['sill'] ?? 0.9),
                ],
            ],
            'openings' => $data['openings'] ?? [],
            'visibility' => [
                'show_front_wall' => false,
            ],
            'lighting' => [
                'type' => 'AREA',
                'location' => [0, -1.2, max(2.4, (float) $room['height'] - 0.25)],
                'energy' => 850,
                'size' => 4.5,
            ],
            'columns' => $data['columns'] ?? [],
            'beams' => $data['beams'] ?? [],
            'electrical' => $data['electrical'] ?? [],
            'camera' => [
                'location' => [
                    $this->centerX((float) $camera['x'], (float) $room['width']),
                    $this->centerY((float) $camera['y'], $length),
                    (float) $camera['height'],
                ],
                'target' => [
                    $this->centerX((float) $camera['target_x'], (float) $room['width']),
                    $this->centerY((float) $camera['target_y'], $length),
                    min((float) $room['height'] - 0.2, 1.45),
                ],
                'lens' => 18,
                'angle_degrees' => (float) $camera['fov'],
            ],
            'render' => [
                'resolution' => [1280, 720],
                'samples' => 64,
                'output' => $outputPath,
            ],
        ];
    }

    private function wallPositionToCenteredY(float $position, float $width, float $length): float
    {
        return $position + ($width / 2) - ($length / 2);
    }

    private function centerX(float $x, float $width): float
    {
        return $x - ($width / 2);
    }

    private function centerY(float $y, float $length): float
    {
        return $y - ($length / 2);
    }
}
