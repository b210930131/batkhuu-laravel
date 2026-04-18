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
    Route::get('/images', [ComfyUIController::class, 'getUserImages']); // ADD THIS
    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/api/images/complete', [ComfyUIController::class, 'finalizeImage']);
    Route::get('/api/images', function() {
            return App\Models\GeneratedImage::where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->get();
        });

    // Зураг устгах route
    Route::delete('/admin/gallery/delete/{id}', function ($id) {
        $image = \App\Models\GeneratedImage::findOrFail($id);
        
        // Файлыг устгах
        if ($image->file_name && file_exists(public_path('outputs/' . $image->file_name))) {
            unlink(public_path('outputs/' . $image->file_name));
        }
        
        $image->delete();
        
        return response()->json(['success' => true]);
    })->name('admin.gallery.delete');
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
            Route::get('/api/comfyui/view', [ComfyUIController::class, 'proxyView'])->name('proxy.view');
            Route::get('/object-info', [ComfyUIController::class, 'proxyObjectInfo']); // <--- ADD THIS LINE
            Route::get('/api/models', [ComfyUIController::class, 'getModels']);
            Route::get('/api/refiners', [ComfyUIController::class, 'getRefinerModels']);
            Route::get('/api/images', [ComfyUIController::class, 'getUserImages']);
            
            // Add other proxies here if needed
        });
    });
});
/*
|--------------------------------------------------------------------------
| Guest Routes (Authentication)
|--------------------------------------------------------------------------
*/
// Registration Routes
// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

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



