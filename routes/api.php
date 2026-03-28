// routes/api.php
<?php
use App\Http\Controllers\ComfyUIController;

Route::prefix('comfyui')->group(function () {
    Route::get('/object_info', [ComfyUIController::class, 'getObjectInfo']);
    Route::get('/health', [ComfyUIController::class, 'healthCheck']);
    Route::get('/system_stats', [ComfyUIController::class, 'getSystemStats']);
    // Add other proxy routes here
});

Route::post('/generate', [ComfyUIController::class, 'generate']);
Route::post('/api/generate', [ComfyUIController::class, 'generate']);