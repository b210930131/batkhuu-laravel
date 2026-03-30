// routes/web.php
<?php

use App\Http\Controllers\ComfyUIController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Main routes
Route::get('/', function () {
    return view('partials.generation-form');
})->name('home');

Route::get('/comfy', function () {
    return view('partials.generation-form');
})->name('comfy');

// Dashboard (auth optional - you can keep or remove)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Auth routes (optional - you can comment out if not needed)
// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

// API routes - ALL ComfyUI endpoints
Route::prefix('api')->group(function () {
    // Generation endpoint
    Route::post('/generate', [ComfyUIController::class, 'generate']);
    
    // Test endpoint (for debugging)
    if (env('APP_DEBUG', false)) {
        Route::get('/test', [ComfyUIController::class, 'test']);
    }
    
    // ComfyUI proxy endpoints
    Route::prefix('comfyui')->group(function () {
        Route::get('/history', [ComfyUIController::class, 'proxyHistory']);
        Route::get('/view', [ComfyUIController::class, 'proxyView']);
        Route::post('/interrupt', [ComfyUIController::class, 'proxyInterrupt']);
        Route::delete('/queue', [ComfyUIController::class, 'proxyQueueDelete']);
        Route::get('/queue', [ComfyUIController::class, 'proxyQueue']);
        Route::get('/system_stats', [ComfyUIController::class, 'proxySystemStats']);
        Route::get('/object_info', [ComfyUIController::class, 'proxyObjectInfo']);
        Route::get('/health', [ComfyUIController::class, 'health']);
        Route::get('/debug-models', [ComfyUIController::class, 'debugModels']);
    });
});

// Include auth routes if needed
// require __DIR__.'/auth.php';