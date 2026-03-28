<div class="container">
    <!-- LEFT: Generation params -->
    <div>
        <div class="card">
            <div class="card-header">⚙️ Model & Prompt</div>
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
                <div class="param-item"><label>Steps</label><input type="number" id="steps" value="20" min="1" max="100"></div>
                <div class="param-item"><label>CFG</label><input type="number" id="cfg" value="7.0" step="0.5" min="1" max="20"></div>
                <div class="param-item"><label>Width</label><input type="number" id="width" value="768" min="512" max="1536" step="64"></div>
                <div class="param-item"><label>Height</label><input type="number" id="height" value="768" min="512" max="1536" step="64"></div>
                <div class="param-item"><label>Sampler</label>
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
            
            <!-- ControlNet Strength and Range Controls -->
            <div class="control-group" style="margin-top: 15px;">
                <label>💪 ControlNet Strength</label>
                <input type="range" id="cnStrength" min="0" max="2" value="0.85" step="0.01">
                <span id="strengthVal">0.85</span>
                <small>Higher = stronger effect</small>
            </div>
            
            <div class="param-row">
                <div class="param-item">
                    <label>▶️ Start Percent</label>
                    <input type="range" id="cnStart" min="0" max="1" value="0" step="0.01">
                    <span id="startVal">0</span>
                    <small>When ControlNet starts (0-100%)</small>
                </div>
                <div class="param-item">
                    <label>⏹️ End Percent</label>
                    <input type="range" id="cnEnd" min="0" max="1" value="1" step="0.01">
                    <span id="endVal">1</span>
                    <small>When ControlNet ends (0-100%)</small>
                </div>
            </div>
            
            <!-- Control Image Upload -->
            <div class="control-group">
                <label>🖼️ Control Image (for ControlNet)</label>
                <input type="file" id="controlImageInput" accept="image/*">
                <div id="controlPreview" style="margin-top: 10px; min-height: 100px;"></div>
                <small>Upload an image to guide the generation</small>
            </div>
            
            <div style="display: flex; gap: 10px; margin: 20px;">
                <button id="generateBtn" class="btn-primary">🚀 Generate with ControlNet</button>
                <button id="refreshGalleryBtn" style="background: #6c757d; color: white;">🔄 Refresh Gallery</button>
                <button id="interruptBtn" style="background: #dc3545; color: white;">⏹️ Interrupt</button>
            </div>
            <div id="status" style="margin: 0 20px 20px 20px; padding: 8px; border-radius: 8px; background: #f0f0f0;">✅ Ready • ControlNet inactive</div>
        </div>

        <!-- Preprocessor Selection Panel -->
        <div class="card" style="margin-top: 20px;">
            <div class="card-header">🎨 Preprocessor Selection</div>
            <div id="preprocessorList" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px; padding: 20px;">
                <!-- Preprocessor buttons will be added here by JavaScript -->
            </div>
        </div>

        <!-- ControlNet Advanced Settings Card -->
        <div class="card" style="margin-top: 20px;">
            <div class="card-header">🎛️ ControlNet Advanced Settings</div>
            <div style="padding: 20px;">
                <div id="preprocessorSettings">
                    <!-- Canny Settings -->
                    <div id="cannySettings" class="preprocessor-settings" style="display: block;">
                        <div class="control-group">
                            <label>🔲 Canny Low Threshold (0-255)</label>
                            <input type="range" id="cannyLowThreshold" min="0" max="255" value="50" step="1">
                            <span id="cannyLowVal">50</span>
                            <small>Lower values detect more edges</small>
                        </div>
                        <div class="control-group">
                            <label>🔲 Canny High Threshold (0-255)</label>
                            <input type="range" id="cannyHighThreshold" min="0" max="255" value="150" step="1">
                            <span id="cannyHighVal">150</span>
                            <small>Higher values detect stronger edges only</small>
                        </div>
                    </div>
                    
                    <!-- Depth Settings -->
                    <div id="depthSettings" class="preprocessor-settings" style="display: none;">
                        <div class="control-group">
                            <label>🗺️ Depth Resolution</label>
                            <select id="depthResolution">
                                <option value="256">256x256</option>
                                <option value="384">384x384</option>
                                <option value="512" selected>512x512</option>
                                <option value="768">768x768</option>
                                <option value="1024">1024x1024</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- OpenPose Settings -->
                    <div id="openposeSettings" class="preprocessor-settings" style="display: none;">
                        <div class="control-group">
                            <label>🧍 Detect Hands</label>
                            <select id="openposeHands">
                                <option value="enable" selected>Enable</option>
                                <option value="disable">Disable</option>
                            </select>
                        </div>
                        <div class="control-group">
                            <label>🧍 Detect Body</label>
                            <select id="openposeBody">
                                <option value="enable" selected>Enable</option>
                                <option value="disable">Disable</option>
                            </select>
                        </div>
                        <div class="control-group">
                            <label>🧍 Detect Face</label>
                            <select id="openposeFace">
                                <option value="enable" selected>Enable</option>
                                <option value="disable">Disable</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- MLSD Settings -->
                    <div id="mlsdSettings" class="preprocessor-settings" style="display: none;">
                        <div class="control-group">
                            <label>📐 MLSD Score Threshold</label>
                            <input type="range" id="mlsdScoreThr" min="0" max="1" value="0.1" step="0.01">
                            <span id="mlsdScoreVal">0.10</span>
                            <small>Higher = fewer lines (only confident lines)</small>
                        </div>
                        <div class="control-group">
                            <label>📐 MLSD Distance Threshold</label>
                            <input type="range" id="mlsdDistThr" min="0" max="0.5" value="0.1" step="0.01">
                            <span id="mlsdDistVal">0.10</span>
                            <small>Controls line grouping</small>
                        </div>
                        <div class="control-group">
                            <label>📐 MLSD Resolution</label>
                            <select id="mlsdResolution">
                                <option value="256">256x256</option>
                                <option value="384">384x384</option>
                                <option value="512" selected>512x512</option>
                                <option value="768">768x768</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- HED Settings -->
                    <div id="hedSettings" class="preprocessor-settings" style="display: none;">
                        <div class="control-group">
                            <label>🎨 HED Resolution</label>
                            <select id="hedResolution">
                                <option value="256">256x256</option>
                                <option value="384">384x384</option>
                                <option value="512" selected>512x512</option>
                                <option value="768">768x768</option>
                                <option value="1024">1024x1024</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Scribble Settings -->
                    <div id="scribbleSettings" class="preprocessor-settings" style="display: none;">
                        <div class="control-group">
                            <label>✏️ Scribble Mode</label>
                            <select id="scribbleMode">
                                <option value="simple">Simple</option>
                                <option value="edge" selected>Edge</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Segmentation Settings -->
                    <div id="segSettings" class="preprocessor-settings" style="display: none;">
                        <div class="control-group">
                            <label>🏞️ Segmentation Resolution</label>
                            <select id="segResolution">
                                <option value="256">256x256</option>
                                <option value="384">384x384</option>
                                <option value="512" selected>512x512</option>
                                <option value="768">768x768</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Normal Map Settings -->
                    <div id="normalSettings" class="preprocessor-settings" style="display: none;">
                        <div class="control-group">
                            <label>⚡ Normal Map Resolution</label>
                            <select id="normalResolution">
                                <option value="256">256x256</option>
                                <option value="384">384x384</option>
                                <option value="512" selected>512x512</option>
                                <option value="768">768x768</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ControlNet Status Panel -->
        <div class="card" style="margin-top: 20px; background: #f0f7ff;">
            <div class="card-header">🎮 ControlNet Status</div>
            <div id="controlnetStatus" style="padding: 20px;">
                <div id="controlnetIndicator" style="display: flex; align-items: center; gap: 10px;">
                    <div id="controlnetDot" style="width: 12px; height: 12px; border-radius: 50%; background-color: #ef4444;"></div>
                    <span id="controlnetText">No control image uploaded</span>
                </div>
                <div id="controlnetDetails" style="font-size: 12px; margin-top: 10px; color: #666;">
                    Upload an image to enable ControlNet
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: Output gallery -->
    <div>
        <div class="card">
            <div class="card-header">🖼️ Generated Images Gallery</div>
            <div id="images" style="min-height: 400px;">
                <div style="text-align: center; padding: 60px 20px; color: #6c757d;">
                    🎨 Loading images from ComfyUI...<br>
                    <small>Images will appear here automatically</small>
                </div>
            </div>
        </div>
        
        <!-- Debug Panel -->
        <div class="card" style="margin-top: 20px; background: #f8f9fa;">
            <div class="card-header">🔍 Debug Info</div>
            <div id="debugInfo" style="font-family: monospace; font-size: 12px; padding: 20px;">
                <button id="checkModelsBtn" style="background: #6c757d; color: white; font-size: 12px; padding: 5px 10px;">Check Models</button>
                <div id="modelStatus" style="margin-top: 10px;"></div>
            </div>
        </div>
    </div>
</div>

<script>
// Update display for Canny thresholds
const lowSlider = document.getElementById('cannyLowThreshold');
const highSlider = document.getElementById('cannyHighThreshold');
const lowSpan = document.getElementById('cannyLowVal');
const highSpan = document.getElementById('cannyHighVal');

if (lowSlider && lowSpan) {
    lowSlider.addEventListener('input', () => {
        lowSpan.innerText = lowSlider.value;
    });
}

if (highSlider && highSpan) {
    highSlider.addEventListener('input', () => {
        highSpan.innerText = highSlider.value;
    });
}

// Update strength display
const strengthSlider = document.getElementById('cnStrength');
const strengthVal = document.getElementById('strengthVal');
if (strengthSlider && strengthVal) {
    strengthSlider.addEventListener('input', () => {
        strengthVal.innerText = strengthSlider.value;
    });
}

// Update start/end displays
const startSlider = document.getElementById('cnStart');
const endSlider = document.getElementById('cnEnd');
const startVal = document.getElementById('startVal');
const endVal = document.getElementById('endVal');

if (startSlider && startVal) {
    startSlider.addEventListener('input', () => {
        startVal.innerText = startSlider.value;
    });
}

if (endSlider && endVal) {
    endSlider.addEventListener('input', () => {
        endVal.innerText = endSlider.value;
    });
}

// MLSD sliders
const mlsdScoreSlider = document.getElementById('mlsdScoreThr');
const mlsdScoreVal = document.getElementById('mlsdScoreVal');
if (mlsdScoreSlider && mlsdScoreVal) {
    mlsdScoreSlider.addEventListener('input', () => {
        mlsdScoreVal.innerText = mlsdScoreSlider.value;
    });
}

const mlsdDistSlider = document.getElementById('mlsdDistThr');
const mlsdDistVal = document.getElementById('mlsdDistVal');
if (mlsdDistSlider && mlsdDistVal) {
    mlsdDistSlider.addEventListener('input', () => {
        mlsdDistVal.innerText = mlsdDistSlider.value;
    });
}

// Function to update preprocessor settings visibility
function updatePreprocessorSettings(preprocessorId) {
    const settingsDivs = document.querySelectorAll('.preprocessor-settings');
    settingsDivs.forEach(div => {
        div.style.display = 'none';
    });
    
    const targetSettings = document.getElementById(`${preprocessorId}Settings`);
    if (targetSettings) {
        targetSettings.style.display = 'block';
    }
}

// Update ControlNet status
function updateControlNetStatus(hasImage, preprocessor = null) {
    const dot = document.getElementById('controlnetDot');
    const text = document.getElementById('controlnetText');
    const details = document.getElementById('controlnetDetails');
    
    if (hasImage) {
        dot.style.backgroundColor = '#10b981';
        text.innerHTML = `✅ ControlNet active with ${preprocessor || 'selected'} preprocessor`;
        details.innerHTML = 'ControlNet will guide the generation based on your uploaded image';
    } else {
        dot.style.backgroundColor = '#ef4444';
        text.innerHTML = 'No control image uploaded';
        details.innerHTML = 'Upload an image to enable ControlNet';
    }
}

// Check models button
const checkModelsBtn = document.getElementById('checkModelsBtn');
if (checkModelsBtn) {
    checkModelsBtn.onclick = async () => {
        const statusDiv = document.getElementById('modelStatus');
        statusDiv.innerHTML = 'Checking models...';
        
        try {
            const response = await fetch(`http://${comfyHost || window.location.hostname}:8188/object_info`);
            const data = await response.json();
            
            const models = Object.keys(data).filter(key => key.includes('model') || key.includes('checkpoint'));
            statusDiv.innerHTML = `
                <strong>✅ Available Models:</strong><br>
                ${models.slice(0, 10).join('<br>')}
                ${models.length > 10 ? `<br>... and ${models.length - 10} more` : ''}
            `;
        } catch (error) {
            statusDiv.innerHTML = `❌ Failed to fetch models: ${error.message}`;
        }
    };
}

// Make functions globally available
window.updatePreprocessorSettings = updatePreprocessorSettings;
window.updateControlNetStatus = updateControlNetStatus;
</script>
