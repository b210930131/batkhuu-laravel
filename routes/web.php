<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ComfyUIController;
use App\Http\Middleware\IsAdmin;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware('verified')->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/twenty', [ComfyUIController::class, 'twenty'])->name('twenty');

    /*
    |--------------------------------------------------------------------------
    | Customer Panel
    |--------------------------------------------------------------------------
    */
    Route::prefix('customer')->name('customer.')->group(function () {
        Route::view('/dashboard', 'dashboard', [
            'dashboardLayout' => 'imagegen.customer.layouts.app',
            'dashboardTitle' => 'Customer Dashboard',
            'dashboardHeading' => 'Customer Dashboard',
            'dashboardSubtitle' => 'AI platform overview',
        ])->name('dashboard');
        Route::view('/ai-studio', 'imagegen.customer.pages.customer')->name('ai-studio');
        Route::view('/gallery', 'imagegen.customer.pages.gallery')->name('gallery');

        Route::delete('/api/images/{id}', [ComfyUIController::class, 'deleteCustomerImage'])
            ->name('api.images.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Panel
    |--------------------------------------------------------------------------
    */
 Route::middleware([IsAdmin::class])->prefix('admin')->name('admin.')->group(function () {
    Route::view('/dashboard', 'dashboard', [
        'dashboardLayout' => 'imagegen.admin.layouts.app',
        'dashboardTitle' => 'Admin Dashboard',
        'dashboardHeading' => 'Admin Dashboard',
        'dashboardSubtitle' => 'AI platform overview',
    ])->name('dashboard');
    Route::get('/ai-studio', [ComfyUIController::class, 'adminAiStudio'])->name('ai-studio');
    Route::get('/gallery', [ComfyUIController::class, 'adminGallery'])->name('gallery');
    Route::get('/api/images', [ComfyUIController::class, 'getAllImages'])->name('api.images');

    Route::delete('/gallery/delete/{id}', [ComfyUIController::class, 'deleteImage'])
        ->name('gallery.delete');
});

    /*
    |--------------------------------------------------------------------------
    | User / Generation API
    |--------------------------------------------------------------------------
    */
    Route::prefix('api')->group(function () {
        Route::post('/generate', [ComfyUIController::class, 'generate'])->name('api.generate');
        Route::post('/images/complete', [ComfyUIController::class, 'finalizeImage'])->name('api.images.complete');
        Route::get('/images', [ComfyUIController::class, 'getUserImages'])->name('api.images');

        Route::prefix('comfyui')->group(function () {
            Route::get('/history', [ComfyUIController::class, 'proxyHistory'])->name('comfyui.history');
            Route::get('/view', [ComfyUIController::class, 'proxyView'])->name('comfyui.view');
            Route::post('/interrupt', [ComfyUIController::class, 'proxyInterrupt'])->name('comfyui.interrupt');
            Route::get('/queue', [ComfyUIController::class, 'proxyQueue'])->name('comfyui.queue');
            Route::get('/health', [ComfyUIController::class, 'health'])->name('comfyui.health');
            Route::get('/object-info', [ComfyUIController::class, 'proxyObjectInfo'])->name('comfyui.object-info');
            Route::get('/models', [ComfyUIController::class, 'getModels'])->name('comfyui.models');
            Route::get('/refiners', [ComfyUIController::class, 'getRefinerModels'])->name('comfyui.refiners');
            Route::get('/proxy/view', [ComfyUIController::class, 'proxyView'])->name('proxy.view');
        });
    });
});