<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ComfyUIController;
use App\Http\Controllers\BlenderController;
use App\Http\Middleware\IsAdmin;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\DashboardPostController;
use App\Http\Controllers\DashboardController;

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
        return redirect()->route(
            auth()->user()->role === 'admin' ? 'admin.dashboard' : 'customer.dashboard'
        );
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
        Route::get('/dashboard', [DashboardController::class, 'customer'])->name('dashboard');
        Route::get('/ai-studio', [ComfyUIController::class, 'customerAiStudio'])->name('ai-studio');
        Route::get('/blender-studio', [BlenderController::class, 'customerStudio'])->name('blender-studio');
        Route::get('/gallery', [ComfyUIController::class, 'customerGallery'])->name('gallery');
        Route::get('/input-images', [ComfyUIController::class, 'customerInputImages'])->name('input-images');

        Route::get('/api/images', [ComfyUIController::class, 'getUserImages'])->name('api.images.index');
        Route::get('/api/input-images', [ComfyUIController::class, 'getUserInputImages'])->name('api.input-images.index');
        Route::delete('/api/input-images/{id}', [ComfyUIController::class, 'deleteCustomerInputImage'])->name('api.input-images.destroy');
        Route::delete('/api/images/{id}', [ComfyUIController::class, 'deleteCustomerImage'])
            ->name('api.images.destroy');
        Route::patch('/api/images/{id}/folder', [ComfyUIController::class, 'moveCustomerImage'])
            ->name('api.images.folder');
        Route::get('/api/folders', [ComfyUIController::class, 'getUserFolders'])->name('api.folders.index');
        Route::post('/api/folders', [ComfyUIController::class, 'storeUserFolder'])->name('api.folders.store');
        Route::delete('/api/folders/{id}', [ComfyUIController::class, 'deleteUserFolder'])->name('api.folders.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Panel
    |--------------------------------------------------------------------------
    */
 Route::middleware([IsAdmin::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
    Route::get('/users', [AdminManagementController::class, 'users'])->name('users.index');
    Route::post('/users', [AdminManagementController::class, 'storeUser'])->name('users.store');
    Route::patch('/users/{user}/toggle', [AdminManagementController::class, 'toggleUser'])->name('users.toggle');
    Route::get('/statistics', [AdminManagementController::class, 'statistics'])->name('statistics');
    Route::get('/management', [AdminManagementController::class, 'management'])->name('management');
    Route::resource('/posts', DashboardPostController::class)->except(['show']);
    Route::get('/ai-studio', [ComfyUIController::class, 'adminAiStudio'])->name('ai-studio');
    Route::get('/blender-studio', [BlenderController::class, 'adminStudio'])->name('blender-studio');
    Route::get('/gallery', [ComfyUIController::class, 'adminGallery'])->name('gallery');
    Route::get('/input-images', [ComfyUIController::class, 'adminInputImages'])->name('input-images');
    Route::get('/api/images', [ComfyUIController::class, 'getAllImages'])->name('api.images');
    Route::get('/api/input-images', [ComfyUIController::class, 'getAllInputImages'])->name('api.input-images');
    Route::delete('/api/input-images/{id}', [ComfyUIController::class, 'deleteAdminInputImage'])->name('api.input-images.destroy');
    Route::get('/api/folders', [ComfyUIController::class, 'getAdminFolders'])->name('api.folders.index');
    Route::post('/api/folders', [ComfyUIController::class, 'storeAdminFolder'])->name('api.folders.store');
    Route::delete('/api/folders/{id}', [ComfyUIController::class, 'deleteAdminFolder'])->name('api.folders.destroy');

    Route::delete('/gallery/delete/{id}', [ComfyUIController::class, 'deleteImage'])
        ->name('gallery.delete');
    Route::patch('/gallery/{id}/folder', [ComfyUIController::class, 'moveAdminImage'])
        ->name('gallery.folder');
});

    /*
    |--------------------------------------------------------------------------
    | User / Generation API
    |--------------------------------------------------------------------------
    */
    Route::prefix('api')->group(function () {
        Route::post('/generate', [ComfyUIController::class, 'generate'])->name('api.generate');
        Route::post('/blender/render', [BlenderController::class, 'render'])->name('api.blender.render');
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
