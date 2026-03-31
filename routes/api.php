// routes/api.php
<?php

use App\Http\Controllers\ComfyUIController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/comfyui/health', [ComfyUIController::class, 'health']);
Route::get('/comfyui/object-info', [ComfyUIController::class, 'proxyObjectInfo']);


Route::get('/checkpoints', [ComfyUIController::class, 'getCheckpoints']);

// Protected routes (auth required)
Route::middleware('auth:sanctum')->group(function () {
    // ComfyUI generation
    Route::post('/generate', [ComfyUIController::class, 'generate']);
    
    // ComfyUI proxy endpoints
    Route::prefix('comfyui')->group(function () {
        Route::get('/history', [ComfyUIController::class, 'proxyHistory']);
        Route::get('/view', [ComfyUIController::class, 'proxyView']);
        Route::post('/interrupt', [ComfyUIController::class, 'proxyInterrupt']);
        Route::get('/queue', [ComfyUIController::class, 'proxyQueue']);
        Route::get('/system-stats', [ComfyUIController::class, 'proxySystemStats']);
    });
});