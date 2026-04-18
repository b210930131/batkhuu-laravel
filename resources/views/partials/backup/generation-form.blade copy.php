<!-- resources/views/partials/generation-form.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ComfyUI ControlNet Studio</title>
    @include('partials.styles2')
    
    <style>
        /* Additional specific styles if needed */
        .preprocessor-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 12px;
            padding: 20px;
        }
        
        .control-strength {
            padding: 15px 20px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .control-strength label {
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
        }
        
        .image-preview img {
            max-width: 100%;
            border-radius: 8px;
            margin-top: 10px;
        }
        
        hr {
            margin: 15px 0;
            border: none;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- LEFT PANEL: Generation Controls -->
        <div>
            <div class="card">
                <div class="card-header">⚙️ Model & Prompt Configuration</div>
                
                <div class="control-group">
                    <label>🎭 Model Selection</label>
                    <select id="model">
                        <option value="dreamshaper_8.safetensors" selected>✨ Dreamshaper 8 (SD1.5, artistic, ControlNet ready)</option>
                        <option value="v1-5-pruned-emaonly-fp16.safetensors">📦 v1-5-pruned-emaonly-fp16 (SD1.5, FP16, low VRAM)</option>
                        <option value="v1-5-pruned.safetensors">📦 v1-5-pruned (SD1.5, standard)</option>
                        <option value="realisticVisionV60B1_v51HyperVAE.safetensors">🎭 Realistic Vision V6.0 (SD1.5, photorealistic)</option>
                        <option value="sd_xl_base_1.0.safetensors">🎨 SDXL Base 1.0 (high quality, 1024x1024)</option>
                        <option value="sd_xl_refiner_1.0.safetensors">🔧 SDXL Refiner 1.0 (detail enhancement)</option>
                        <option value="flux1-dev-fp8.safetensors">⚡ Flux Dev FP8 (experimental, high VRAM)</option>
                        <option value="sd3.5_large_fp8_scaled.safetensors">🌀 SD3.5 Large FP8 (requires >8GB VRAM)</option>
                        <option value="qwen_image_2512_fp8_e4m3fn.safetensors">🖼️ Qwen Image 2.5 (experimental)</option>
                    </select>
                    <div id="vram-warning" class="vram-warning" style="display:none;">
                        ⚠️ Warning: Selected model may exceed 8GB VRAM when using ControlNet. Consider using an SD1.5 model.
                    </div>
                </div>
                
                <div class="control-group">
                    <label>✨ Positive Prompt</label>
                    <textarea id="positive_prompt" rows="3">masterpiece, architectural photography, modern building exterior, (golden hour lighting:1.1), luxury facade, glass and concrete materials, realistic landscape, high-quality rendering, V-Ray style, Unreal Engine 5 render, sharp focus, volumetric lighting, hyper-detailed environment, realistic sky.</textarea>
                </div>
                
                <div class="control-group">
                    <label>❌ Negative Prompt</label>
                    <textarea id="negative_prompt" rows="2">(worst quality, low quality:1.4), distorted architecture, unrealistic perspective, blurry, foggy, messy garden, bad reflections, overexposed, cartoonish, 3d render look, low-poly, floating objects.</textarea>
                </div>
                
                <div class="param-row">
                    <div class="param-item">
                        <label>Steps</label>
                        <input type="number" id="steps" value="20" min="1" max="100">
                    </div>
                    <div class="param-item">
                        <label>CFG Scale</label>
                        <input type="number" id="cfg" value="7.0" step="0.5" min="1" max="20">
                    </div>
                    <div class="param-item">
                        <label>Width</label>
                        <input type="number" id="width" value="768" min="512" max="1536" step="64">
                    </div>
                    <div class="param-item">
                        <label>Height</label>
                        <input type="number" id="height" value="768" min="512" max="1536" step="64">
                    </div>
                    <div class="param-item">
                        <label>Sampler</label>
                        <select id="sampler">
                            <option value="euler">Euler</option>
                            <option value="euler_ancestral">Euler Ancestral</option>
                            <option value="dpmpp_2m" selected>DPM++ 2M</option>
                            <option value="dpmpp_2m_karras">DPM++ 2M Karras</option>
                            <option value="ddim">DDIM</option>
                            <option value="dpmpp_sde">DPM++ SDE</option>
                            <option value="lcm">LCM</option>
                        </select>
                    </div>
                </div>
                
                <div class="control-strength">
                    <label>💪 ControlNet Strength</label>
                    <input type="range" id="cnStrength" min="0" max="2" value="0.85" step="0.01">
                    <span id="strengthVal">0.85</span>
                    <small>Higher = stronger adherence to control image</small>
                </div>
                
                <div class="param-row">
                    <div class="param-item">
                        <label>▶️ Start Percent</label>
                        <input type="range" id="cnStart" min="0" max="1" value="0" step="0.01">
                        <span id="startVal">0</span>
                        <small>When ControlNet starts applying</small>
                    </div>
                    <div class="param-item">
                        <label>⏹️ End Percent</label>
                        <input type="range" id="cnEnd" min="0" max="1" value="1" step="0.01">
                        <span id="endVal">1</span>
                        <small>When ControlNet stops applying</small>
                    </div>
                </div>
                
                <div class="control-group">
                    <label>🖼️ Control Image (for ControlNet)</label>
                    <input type="file" id="controlImageInput" accept="image/*">
                    <div id="controlPreview" class="image-preview"></div>
                    <small>Upload an image to guide the generation (JPG, PNG, etc.)</small>
                </div>
                
                <div style="display: flex; gap: 12px; padding: 20px;">
                    <button id="generateBtn" class="btn-primary">🚀 Generate Image</button>
                    <button id="refreshGalleryBtn" style="background: #6c757d; color: white;">🔄 Refresh Gallery</button>
                    <button id="interruptBtn" style="background: #dc3545; color: white;">⏹️ Interrupt</button>
                </div>
                
                <div id="status" style="margin: 0 20px 20px 20px; padding: 12px; border-radius: 8px;">
                    ✅ Ready • ControlNet inactive
                </div>
            </div>
            
            <!-- Preprocessor Selection Panel -->
            <div class="card">
                <div class="card-header">🎨 ControlNet Preprocessors</div>
                <div id="preprocessorList" class="preprocessor-grid">
                    <!-- Preprocessors will be added dynamically -->
                </div>
                <div style="padding: 0 20px 20px 20px;">
                    <small>💡 Preprocessors extract different features from your control image</small>
                </div>
            </div>
            
            <!-- ControlNet Advanced Settings -->
            <div class="card">
                <div class="card-header">🎛️ Preprocessor Settings</div>
                <div style="padding: 20px;">
                    <div id="preprocessorSettings">
                        <!-- Canny Settings -->
                        <div id="cannySettings" class="preprocessor-settings">
                            <div class="control-group">
                                <label>🔲 Canny Low Threshold</label>
                                <input type="range" id="cannyLowThreshold" min="0" max="255" value="50" step="1">
                                <span id="cannyLowVal">50</span>
                                <small>Lower values detect more edges</small>
                            </div>
                            <div class="control-group">
                                <label>🔲 Canny High Threshold</label>
                                <input type="range" id="cannyHighThreshold" min="0" max="255" value="150" step="1">
                                <span id="cannyHighVal">150</span>
                                <small>Higher values detect only strong edges</small>
                            </div>
                        </div>
                        
                        <!-- Depth Settings -->
                        <div id="depthSettings" class="preprocessor-settings" style="display: none;">
                            <div class="control-group">
                                <label>🗺️ Depth Resolution</label>
                                <select id="depthResolution">
                                    <option value="256">256x256 (Fast)</option>
                                    <option value="512" selected>512x512 (Balanced)</option>
                                    <option value="768">768x768 (Quality)</option>
                                    <option value="1024">1024x1024 (Best quality)</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- OpenPose Settings -->
                        <div id="openposeSettings" class="preprocessor-settings" style="display: none;">
                            <div class="control-group">
                                <label>🧍 Detection Features</label>
                                <div style="display: flex; gap: 10px; margin-top: 8px;">
                                    <label style="display: flex; align-items: center; gap: 5px;">
                                        <input type="checkbox" id="openposeHands" checked> Hands
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 5px;">
                                        <input type="checkbox" id="openposeBody" checked> Body
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 5px;">
                                        <input type="checkbox" id="openposeFace" checked> Face
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- MLSD Settings -->
                        <div id="mlsdSettings" class="preprocessor-settings" style="display: none;">
                            <div class="control-group">
                                <label>📐 Score Threshold</label>
                                <input type="range" id="mlsdScoreThr" min="0" max="1" value="0.1" step="0.01">
                                <span id="mlsdScoreVal">0.10</span>
                                <small>Higher = fewer lines (only confident lines)</small>
                            </div>
                            <div class="control-group">
                                <label>📏 Distance Threshold</label>
                                <input type="range" id="mlsdDistThr" min="0" max="0.5" value="0.1" step="0.01">
                                <span id="mlsdDistVal">0.10</span>
                                <small>Controls line grouping</small>
                            </div>
                            <div class="control-group">
                                <label>Resolution</label>
                                <select id="mlsdResolution">
                                    <option value="256">256x256</option>
                                    <option value="512" selected>512x512</option>
                                    <option value="768">768x768</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- HED Settings -->
                        <div id="hedSettings" class="preprocessor-settings" style="display: none;">
                            <div class="control-group">
                                <label>Resolution</label>
                                <select id="hedResolution">
                                    <option value="256">256x256</option>
                                    <option value="512" selected>512x512</option>
                                    <option value="768">768x768</option>
                                    <option value="1024">1024x1024</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Scribble Settings -->
                        <div id="scribbleSettings" class="preprocessor-settings" style="display: none;">
                            <div class="control-group">
                                <label>Mode</label>
                                <select id="scribbleMode">
                                    <option value="simple">Simple</option>
                                    <option value="edge" selected>Edge</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Segmentation Settings -->
                        <div id="segSettings" class="preprocessor-settings" style="display: none;">
                            <div class="control-group">
                                <label>Resolution</label>
                                <select id="segResolution">
                                    <option value="256">256x256</option>
                                    <option value="512" selected>512x512</option>
                                    <option value="768">768x768</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Normal Map Settings -->
                        <div id="normalSettings" class="preprocessor-settings" style="display: none;">
                            <div class="control-group">
                                <label>Resolution</label>
                                <select id="normalResolution">
                                    <option value="256">256x256</option>
                                    <option value="512" selected>512x512</option>
                                    <option value="768">768x768</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ControlNet Status Panel -->
            <div class="card" style="background: linear-gradient(135deg, #667eea10 0%, #764ba210 100%);">
                <div class="card-header">🎮 ControlNet Status</div>
                <div id="controlnetStatus" style="padding: 20px;">
                    <div id="controlnetIndicator" style="display: flex; align-items: center; gap: 12px;">
                        <div id="controlnetDot" style="width: 12px; height: 12px; border-radius: 50%; background-color: #ef4444;"></div>
                        <span id="controlnetText" style="font-weight: 500;">No control image uploaded</span>
                    </div>
                    <div id="controlnetDetails" style="font-size: 12px; margin-top: 12px; color: #6b7280;">
                        Upload an image and select a preprocessor to enable ControlNet
                    </div>
                </div>
            </div>
        </div>
        
        <!-- RIGHT PANEL: Gallery and Debug -->
        <div>
            <div class="card">
                <div class="card-header">🖼️ Generated Images Gallery</div>
                <div id="images">
                    <div style="text-align: center; padding: 60px 20px; color: #9ca3af;">
                        🎨 Loading images from ComfyUI...<br>
                        <small style="margin-top: 8px; display: block;">Images will appear here automatically</small>
                    </div>
                </div>
            </div>
        <div id="vramStatus" style="margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 4px; display: none;">
            <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 5px;">
                <span>Memory (VRAM):</span>
                <span id="vramText">0 / 8 GB</span>
                 </div>
                <div style="width: 100%; height: 8px; background: #e9ecef; border-radius: 4px; overflow: hidden;">
            <div id="vramBar" style="width: 0%; height: 100%; background: #28a745; transition: width 0.5s ease;"></div>
         </div>
    <small id="gpuName" style="font-size: 10px; color: #6c757d; display: block; mt-1"></small>
</div>
            <!-- Debug Panel -->
            <div class="card">
    <div class="card-header">🔍 Debug & System Info</div>
    <div id="debugInfo" style="padding: 20px;">
        <div id="vramStatus" style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 8px; display: none; border: 1px solid #ddd;">
            <div style="display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 4px;">
                <span id="gpuName" style="font-weight: bold; color: #444;">GPU</span>
                <span id="vramText" style="color: #666;">0/0 GB</span>
            </div>
            <div style="width: 100%; height: 6px; background: #eee; border-radius: 3px; overflow: hidden;">
                <div id="vramBar" style="width: 0%; height: 100%; background: #28a745; transition: width 0.4s ease;"></div>
            </div>
        </div>
        <button id="checkModelsBtn" class="btn-sm">🔍 Check Models</button>
        <button id="checkHealthBtn" class="btn-sm">❤️ Check Health</button>
        <div id="modelStatus" style="margin-top: 15px; font-family: monospace; font-size: 11px;"></div>
    </div>
</div>

    <!-- Include JavaScript -->
    <!-- <script src="{{ asset('js/comfyui.js') }}"></script> -->
    
    <script>
        // Additional initialization if needed
        document.addEventListener('DOMContentLoaded', function() {
            console.log('ComfyUI ControlNet Studio initialized');
            
            // Add health check button handler
            const healthBtn = document.getElementById('checkHealthBtn');
            if (healthBtn) {
                healthBtn.onclick = async () => {
                    const statusDiv = document.getElementById('modelStatus');
                    statusDiv.innerHTML = 'Checking ComfyUI connection...';
                    
                    try {
                        const response = await fetch(`http://${window.location.hostname}:8188/`);
                        if (response.ok) {
                            statusDiv.innerHTML = '✅ ComfyUI is running and accessible';
                            statusDiv.style.color = '#155724';
                        } else {
                            statusDiv.innerHTML = '⚠️ ComfyUI responded but with status: ' + response.status;
                            statusDiv.style.color = '#856404';
                        }
                    } catch (error) {
                        statusDiv.innerHTML = `❌ Cannot connect to ComfyUI: ${error.message}<br>
                        Make sure ComfyUI is running on port 8188`;
                        statusDiv.style.color = '#721c24';
                    }
                };
            }
        });
    </script>
    
</body>
@include('partials.footer')
</html>