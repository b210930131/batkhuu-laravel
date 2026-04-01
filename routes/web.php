<?php

use App\Http\Controllers\ComfyUIController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Protected)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    
    // Main Studio Page
    Route::get('/', function () {
        return view('partials.generation-form');
    })->name('home');

    // User Gallery Page (NEW)
    Route::get('/my-gallery', [ComfyUIController::class, 'myGallery'])->name('gallery');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/api/images/complete', [ComfyUIController::class, 'finalizeImage']);
    /*
    |--------------------------------------------------------------------------
    | API / ComfyUI Proxy Routes
    |--------------------------------------------------------------------------
    | All these now require authentication so we can track user_id
    */
    Route::prefix('api')->group(function () {
        
        // The generation endpoint that saves to DB
        Route::post('/generate', [ComfyUIController::class, 'generate']);
        
        // Endpoint to update DB once polling confirms image is ready (NEW)
        Route::post('/images/complete', [ComfyUIController::class, 'finalizeImage']);

        // ComfyUI proxy endpoints
        Route::prefix('comfyui')->group(function () {
            Route::get('/history', [ComfyUIController::class, 'proxyHistory']);
            Route::get('/view', [ComfyUIController::class, 'proxyView']);
            Route::post('/interrupt', [ComfyUIController::class, 'proxyInterrupt']);
            Route::get('/queue', [ComfyUIController::class, 'proxyQueue']);
            Route::get('/health', [ComfyUIController::class, 'health']);
            // Add other proxies here if needed
        });
    });
});

/*
|--------------------------------------------------------------------------
| OLD / COMMENTED OUT ROUTES
|--------------------------------------------------------------------------
| 
// Route::get('/comfy', function () {
//     return view('partials.generation-form');
// })->name('comfy');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');
*/