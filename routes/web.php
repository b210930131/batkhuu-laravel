<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComfyUIController;
use App\Http\Controllers\ComfyUIProxyController;

// Main route
Route::get('/', [ComfyUIController::class, 'index']);


// Web authentication routes (хэрэв Blade view хэрэгтэй бол)
Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');




// Generation endpoint
Route::post('/api/generate', [ComfyUIController::class, 'generate']);

// Proxy endpoints for ComfyUI (no CORS issues)
Route::prefix('api/comfyui')->group(function () {
    Route::get('/history', [ComfyUIController::class, 'proxyHistory']);
    Route::get('/view', [ComfyUIController::class, 'proxyView']);
    Route::post('/interrupt', [ComfyUIController::class, 'proxyInterrupt']);
    Route::get('/object_info', [ComfyUIController::class, 'proxyObjectInfo']);
    Route::get('/queue', [ComfyUIController::class, 'proxyQueue']);
});

// Info endpoints
Route::prefix('api')->group(function () {
    // Route::get('/health', [ComfyUIController::class, 'health']);
    // Route::get('/debug-models', [ComfyUIController::class, 'debugModels']);
    Route::get('/health', [ComfyUIController::class, 'health']);
    Route::post('/generate', [ComfyUIController::class, 'generate']);
});

Route::prefix('comfyui')->group(function () {
    Route::get('/object_info', [ComfyUIController::class, 'proxyObjectInfo']);
    Route::get('/history', [ComfyUIController::class, 'proxyHistory']);
    Route::get('/system_stats', [ComfyUIController::class, 'proxySystemStats']); // Add this!
    Route::get('/queue', [ComfyUIController::class, 'proxyQueue']);
});



