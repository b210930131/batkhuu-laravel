<?php

use App\Http\Controllers\ComfyUIController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController; // Add this

// Main routes
Route::get('/', function () {
    return view('partials.generation-form');
})->name('home');

Route::get('/comfy', function () {
    return view('partials.generation-form');
})->name('comfy');

// Dashboard (optional - you can remove if not needed)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// API routes - ALL ComfyUI endpoints
Route::prefix('api')->group(function () {
    // Generation endpoint
    Route::post('/generate', [ComfyUIController::class, 'generate']);
    
    // Test endpoint (for debugging)
    Route::get('/test', [ComfyUIController::class, 'test']);
    
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

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');