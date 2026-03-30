<!-- resources/views/partials/generation-form.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ComfyUI ControlNet Studio</title>

    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 20px;
        color: #1f2937;
    }
    
    .container {
        max-width: 1600px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1.2fr 0.8fr;
        gap: 25px;
        padding: 0;
    }
    
    .card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        overflow: hidden;
        margin-bottom: 20px;
        border: 1px solid rgba(255,255,255,0.1);
        animation: slideIn 0.4s ease-out forwards;
    }
    
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px 20px;
        font-weight: 600;
        font-size: 16px;
        display: grid;
        align-items: center;
        gap: 10px;
    }
    
    /* Form Elements */
    .control-group {
        padding: 15px 20px;
        border-bottom: 1px solid #f1f5f9;
    }
    
    .control-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        font-size: 13px;
        color: #4b5563;
    }
    .controlnet-box {
    max-width: 600px;
    max=height: 400px;
    margin: 0 auto;
}
    .control-group input, 
    .control-group select, 
    .control-group textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 14px;
        background: #f8fafc;
        transition: all 0.2s;
    }
    
    .control-group input:focus, 
    .control-group select:focus, 
    .control-group textarea:focus {
        outline: none;
        border-color: #667eea;
        background: white;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    /* Buttons */
    button {
        padding: 10px 18px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        flex: 1;
    }

    .btn-primary:hover {
        filter: brightness(1.05);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    button:hover {
        filter: brightness(1.05);
        transform: translateY(-1px);
    }

    button:active {
        transform: translateY(0);
    }

    /* Preprocessor Grid & Buttons */
    .preprocessor-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, auto));
        gap: 12px;
        padding: 15px;
    }

    .preprocessor-btn {
        background: white;
        border: 1px solid #e2e8f0;
        padding: 12px;
        border-radius: 10px;
        text-align: center;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }

    .preprocessor-btn:hover {
        border-color: #667eea;
        background: #f0f4ff;
        transform: translateY(-2px);
    }

    .preprocessor-btn.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    /* Preprocessor Settings */
    .preprocessor-settings {
        display: none;
        animation: fadeIn 0.3s ease-in-out;
    }

    .preprocessor-settings.active {
        display: block;
    }

    /* Slider styling */
    input[type=range] {
        accent-color: #8b5cf6;
        width: 100%;
    }

    /* Control Strength & Image Preview */
    .control-strength {
        padding: 15px 20px;
        border-bottom: 1px solid #e5e7eb;
    }

    .control-strength label {
        font-weight: 600;
        margin-bottom: 8px;
        display: block;
    }

    .image-preview {
        margin-top: 10px;
    }

    .image-preview img,
    #controlPreview img {
        max-width: 100%;
        height: auto;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        margin-top: 10px;
    }

    /* Parameters Row */
    .param-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 15px;
        padding: 15px 20px;
        border-bottom: 1px solid #e5e7eb;
    }

    .param-item label {
        font-size: 12px;
        font-weight: 600;
        display: block;
        margin-bottom: 5px;
    }

    /* Gallery Grid */
    #images {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 15px;
        padding: 20px;
    }

    .image-card {
        border-radius: 12px;
        overflow: hidden;
        background: #f1f5f9;
        aspect-ratio: 1 / 1;
        border: 1px solid #e2e8f0;
    }

    .image-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* VRAM & Status */
    .vram-warning {
        background: #fff3cd;
        border: 1px solid #ffc107;
        padding: 8px 12px;
        border-radius: 6px;
        margin-top: 8px;
        font-size: 12px;
    }

    .vram-container {
        background: #e2e8f0;
        border-radius: 10px;
        height: 12px;
        width: 100%;
        margin: 8px 0;
        overflow: hidden;
        border: 1px solid #cbd5e1;
    }

    .vram-bar {
        background: linear-gradient(90deg, #10b981 0%, #f59e0b 70%, #ef4444 100%);
        height: 100%;
        width: 0%;
        transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .status-badge {
        padding: 4px 10px;
        border-radius: 9999px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Utility Classes */
    .btn-group {
        display: flex;
        gap: 8px;
        margin-bottom: 15px;
    }

    hr {
        margin: 15px 0;
        border: none;
        border-top: 1px solid #e5e7eb;
    }

    /* Animations */
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Debug Log */
    .debug-log {
        background: #0f172a;
        color: #94a3b8;
        padding: 15px;
        border-radius: 8px;
        font-family: 'Fira Code', 'Courier New', monospace;
        font-size: 11px;
        max-height: 250px;
        overflow-y: auto;
        line-height: 1.5;
        border: 1px solid #1e293b;
    }

    /* Mobile Responsiveness */
    @media (max-width: 1024px) {
        .container {
            grid-template-columns: 1fr;
            padding: 0;
        }
        
        body {
            padding: 10px;
        }

        .preprocessor-grid {
            grid-template-columns: repeat(3, 1fr);
        }
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
    <option value="dreamshaper_8.safetensors">✨ Dreamshaper 8 (SD1.5)</option>
    <option value="v1-5-pruned-emaonly-fp16.safetensors">📦 v1-5-pruned-emaonly-fp16 (SD1.5)</option>
    <option value="realisticVisionV60B1_v51HyperVAE.safetensors">🎭 Realistic Vision V6.0 (SD1.5)</option>
    <option value="sd_xl_base_1.0.safetensors">🎨 SDXL Base 1.0 (1024x1024 recommended)</option>
    <option value="sd_xl_base_1.0_fp16.safetensors">🎨 SDXL Base 1.0 FP16 (lower VRAM)</option>
</select>
                    <div id="vram-warning" class="vram-warning" style="display:none;">
                        ⚠️ Warning: Selected model may exceed 8GB VRAM when using ControlNet. Consider using an SD1.5 model.
                    </div>
                </div>
                <div class="control-group" id="refinerGroup" style="display: none;">
    <label>🔧 SDXL Refiner Model</label>
    <select id="refiner_model">
        <option value="sd_xl_refiner_1.0.safetensors">SDXL Refiner 1.0</option>
        <option value="sd_xl_refiner_1.0_fp16.safetensors">SDXL Refiner 1.0 (FP16)</option>
    </select>
    <div style="margin-top: 8px;">
        <label>✨ Refiner Steps</label>
        <input type="number" id="refiner_steps" value="15" min="1" max="100">
        <small style="display: block; margin-top: 4px; color: #6b7280;">Refiner adds details after base generation (typically 10-20 steps)</small>
    </div>
    <div id="resolutionHint" style="margin-top: 8px; padding: 8px; background: #e0e7ff; border-radius: 6px; font-size: 12px; display: none;"></div>
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
                
                <div class="control-group">
                    <label>🖼️ Control Image (for ControlNet)</label>
                    <input type="file" id="controlImageInput" accept="image/*">
                    <div id="controlPreview" class="image-preview"></div>
                    <small>Upload an image to guide the generation (JPG, PNG, etc.)</small>
                </div>
                
                <div style="display: flex; gap: 12px; padding: 20px;">
                    <button id="generateBtn" class="btn-primary">🚀 Generate Image</button>
                    <button id="refreshGalleryBtn" style="background: #6c757d; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer;">🔄 Refresh Gallery</button>
                    <button id="interruptBtn" style="background: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer;">⏹️ Interrupt</button>
                </div>
                
                <div id="status" style="margin: 0 20px 20px 20px; padding: 12px; border-radius: 8px; background: #f0fdf4; color: #166534;">
                    ✅ Ready • ControlNet inactive
                </div>
                
            </div>
            
            <!-- Preprocessor Selection Panel -->
             <div class="controlnet-box">
            <div class="card">
                <div class="card-header">🎨 ControlNet Preprocessors</div>
                <div style="padding: 20px;">
                    <div id="preprocessorList" class="preprocessor-grid">
                        <!-- Preprocessors will be added dynamically via JavaScript -->
                    </div>
                </div>
            </div>
            
            <!-- Preprocessor Settings Panel -->
            <div class="card">
                <div class="card-header">🎛️ Preprocessor Settings</div>
                <div style="padding: 20px;">
                    <div id="preprocessorSettings">
                        <!-- Canny Settings -->
                        <div id="cannySettings" class="preprocessor-settings">
                            <div class="control-group">
                                <label>🔲 Canny Low Threshold: <span id="cannyLowVal">50</span></label>
                                <input type="range" id="cannyLowThreshold" min="0" max="255" value="50">
                            </div>
                            <div class="control-group">
                                <label>🔲 Canny High Threshold: <span id="cannyHighVal">150</span></label>
                                <input type="range" id="cannyHighThreshold" min="0" max="255" value="150">
                            </div>
                        </div>

                        <!-- Depth Settings -->
                        <div id="depthSettings" class="preprocessor-settings">
                            <div class="control-group">
                                <label>🗺️ Depth Resolution</label>
                                <select id="depthResolution">
                                    <option value="512" selected>512</option>
                                    <option value="1024">1024</option>
                                </select>
                            </div>
                        </div>

                        <!-- OpenPose Settings -->
                        <div id="openposeSettings" class="preprocessor-settings">
                            <div class="control-group">
                                <label>🧍 Detection Features</label>
                                <div style="display: flex; gap: 10px; margin-top: 8px;">
                                    <label><input type="checkbox" id="openposeHands" checked> Hands</label>
                                    <label><input type="checkbox" id="openposeBody" checked> Body</label>
                                    <label><input type="checkbox" id="openposeFace" checked> Face</label>
                                </div>
                            </div>
                        </div>

                        <!-- MLSD Settings -->
                        <div id="mlsdSettings" class="preprocessor-settings">
                            <div class="control-group">
                                <label>📐 Score Threshold: <span id="mlsdScoreVal">0.10</span></label>
                                <input type="range" id="mlsdScoreThr" min="0" max="1" value="0.1" step="0.01">
                            </div>
                            <div class="control-group">
                                <label>📏 Dist Threshold: <span id="mlsdDistVal">0.10</span></label>
                                <input type="range" id="mlsdDistThr" min="0" max="0.5" value="0.1" step="0.01">
                            </div>
                            <div class="control-group">
                                <label>Resolution</label>
                                <select id="mlsdResolution">
                                    <option value="512">512</option>
                                    <option value="1024">1024</option>
                                </select>
                            </div>
                        </div>

                        <!-- Scribble Settings -->
                        <div id="scribbleSettings" class="preprocessor-settings">
                            <div class="control-group">
                                <label>Scribble Mode</label>
                                <select id="scribbleMode">
                                    <option value="hed">HED</option>
                                    <option value="pidi">PIDI</option>
                                </select>
                            </div>
                        </div>

                        <!-- HED Settings -->
                        <div id="hedSettings" class="preprocessor-settings">
                            <div class="control-group">
                                <label>HED Resolution</label>
                                <select id="hedResolution">
                                    <option value="512">512</option>
                                    <option value="1024">1024</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- SEG Settings -->
                        <div id="segSettings" class="preprocessor-settings">
                            <div class="control-group">
                                <label>SEG Resolution</label>
                                <select id="segResolution">
                                    <option value="512">512</option>
                                    <option value="1024">1024</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Normal Settings -->
                        <div id="normalSettings" class="preprocessor-settings">
                            <div class="control-group">
                                <label>Normal Resolution</label>
                                <select id="normalResolution">
                                    <option value="512">512</option>
                                    <option value="1024">1024</option>
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
        
        <!-- RIGHT PANEL: Gallery -->
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
        </div>
    </div>

    <script>
// ========== GLOBAL VARIABLES ==========
let currentPreprocessor = 'canny';
let currentControlImage = null;
let isGenerating = false;

// ========== HELPER FUNCTIONS (Defined First) ==========

function getPreprocessorName(id) {
    const names = {
        'canny': 'Canny Edge Detection',
        'depth': 'Depth Map',
        'openpose': 'OpenPose',
        'mlsd': 'MLSD Line Detection',
        'scribble': 'Scribble',
        'hed': 'HED Edge Detection',
        'seg': 'Segmentation',
        'normal': 'Normal Map'
    };
    return names[id] || id;
}

function updateControlNetStatus() {
    const dot = document.getElementById('controlnetDot');
    const text = document.getElementById('controlnetText');
    const details = document.getElementById('controlnetDetails');
    const controlImageInput = document.getElementById('controlImageInput');
    
    const hasControlImage = controlImageInput && controlImageInput.files && controlImageInput.files.length > 0;
    
    if (dot && text && details) {
        if (hasControlImage) {
            dot.style.backgroundColor = '#10b981';
            text.textContent = `✅ ControlNet Active • Preprocessor: ${getPreprocessorName(currentPreprocessor)}`;
            details.innerHTML = `Using ${getPreprocessorName(currentPreprocessor)} preprocessor with custom settings. ControlNet will guide the generation.`;
        } else {
            dot.style.backgroundColor = '#ef4444';
            text.textContent = '⏸️ ControlNet Inactive';
            details.innerHTML = 'Upload a control image to enable ControlNet guidance.';
        }
    }
}

function updateResolutionHint(width, height) {
    const widthInput = document.getElementById('width');
    const heightInput = document.getElementById('height');
    const hintDiv = document.getElementById('resolutionHint');
    
    if (widthInput && heightInput) {
        widthInput.value = width;
        heightInput.value = height;
    }
    
    if (hintDiv) {
        hintDiv.innerHTML = `💡 Recommended resolution: ${width}x${height}`;
        hintDiv.style.display = 'block';
        setTimeout(() => {
            hintDiv.style.display = 'none';
        }, 3000);
    }
}

function setupSDXLSupport() {
    const modelSelect = document.getElementById('model');
    const refinerSelect = document.getElementById('refiner_model');
    const refinerStepsInput = document.getElementById('refiner_steps');
    const refinerGroup = document.getElementById('refinerGroup');
    
    if (modelSelect && refinerGroup) {
        modelSelect.addEventListener('change', () => {
            const selectedModel = modelSelect.value.toLowerCase();
            const isSDXL = selectedModel.includes('sdxl');
            
            if (isSDXL) {
                refinerGroup.style.display = 'block';
                
                // Auto-select refiner if available
                if (refinerSelect && refinerSelect.options.length > 0) {
                    let hasRefiner = false;
                    for (let i = 0; i < refinerSelect.options.length; i++) {
                        if (refinerSelect.options[i].value.toLowerCase().includes('refiner')) {
                            refinerSelect.value = refinerSelect.options[i].value;
                            hasRefiner = true;
                            break;
                        }
                    }
                    
                    if (!hasRefiner) {
                        const option = document.createElement('option');
                        option.value = 'sd_xl_refiner_1.0.safetensors';
                        option.text = '🔧 SDXL Refiner 1.0';
                        refinerSelect.appendChild(option);
                        refinerSelect.value = 'sd_xl_refiner_1.0.safetensors';
                    }
                }
                
                // Set default steps for SDXL
                const stepsInput = document.getElementById('steps');
                if (stepsInput && stepsInput.value === '20') {
                    stepsInput.value = '30';
                }
                
                // Set default refiner steps
                if (refinerStepsInput && refinerStepsInput.value === '') {
                    refinerStepsInput.value = '15';
                }
                
                // Show SDXL resolution hint
                updateResolutionHint(1024, 1024);
            } else {
                refinerGroup.style.display = 'none';
                updateResolutionHint(512, 512);
            }
        });
        
        // Trigger initial check
        modelSelect.dispatchEvent(new Event('change'));
    }
}

async function refreshGallery() {
    const galleryDiv = document.getElementById('images');
    if (!galleryDiv) return;
    
    try {
        galleryDiv.innerHTML = '<div style="text-align: center; padding: 60px 20px;">🔄 Loading images...</div>';
        
        const response = await fetch('/api/comfyui/history');
        const data = await response.json();
        const images = [];
        
        Object.values(data).forEach(prompt => {
            if (prompt.outputs) {
                Object.values(prompt.outputs).forEach(output => {
                    if (output.images) {
                        output.images.forEach(img => {
                            images.push(img);
                        });
                    }
                });
            }
        });
        
        images.reverse();
        
        if (images.length === 0) {
            galleryDiv.innerHTML = '<div style="text-align: center; padding: 60px 20px; color: #9ca3af;">🎨 No images yet<br><small>Your generated images will appear here</small></div>';
            return;
        }
        
        galleryDiv.innerHTML = images.map(img => `
            <div class="image-card" style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 16px;">
                <img src="/api/comfyui/view?filename=${encodeURIComponent(img.filename)}&subfolder=${encodeURIComponent(img.subfolder || '')}&type=${img.type}" 
                     style="width: 100%; height: auto; cursor: pointer;"
                     onclick="window.open(this.src, '_blank')"
                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'200\'%3E%3Crect width=\'200\' height=\'200\' fill=\'%23ccc\'/%3E%3Ctext x=\'100\' y=\'100\' text-anchor=\'middle\' fill=\'%23999\'%3EError%3C/text%3E%3C/svg%3E'">
                <div class="image-actions" style="padding: 12px; text-align: center;">
                    <button class="download-btn" onclick="event.stopPropagation(); downloadImage('${img.filename}', '${img.subfolder || ''}', '${img.type}')" 
                            style="background: #667eea; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer;">
                        📥 Download
                    </button>
                </div>
            </div>
        `).join('');
    } catch (error) {
        console.error('Gallery error:', error);
        galleryDiv.innerHTML = `<div style="text-align: center; padding: 60px 20px; color: #ef4444;">❌ Error loading gallery: ${error.message}</div>`;
    }
}

async function downloadImage(filename, subfolder, type) {
    try {
        const url = `/api/comfyui/view?filename=${encodeURIComponent(filename)}&subfolder=${encodeURIComponent(subfolder)}&type=${type}`;
        const response = await fetch(url);
        const blob = await response.blob();
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(a.href);
    } catch (error) {
        console.error('Download error:', error);
        alert('Failed to download image');
    }
}

async function interruptGeneration() {
    try {
        await fetch('/api/comfyui/interrupt', { method: 'POST' });
        const statusDiv = document.getElementById('status');
        if (statusDiv) {
            statusDiv.innerHTML = '⏹️ Generation interrupted';
            statusDiv.style.background = '#fff3cd';
            statusDiv.style.color = '#856404';
            setTimeout(() => {
                statusDiv.innerHTML = '✅ Ready • ControlNet ready';
                statusDiv.style.background = '#f0fdf4';
                statusDiv.style.color = '#166534';
            }, 2000);
        }
        isGenerating = false;
        const btn = document.getElementById('generateBtn');
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '🚀 Generate Image';
        }
    } catch (error) {
        console.error('Interrupt error:', error);
    }
}

async function pollForResults(promptId) {
    let attempts = 0;
    const maxAttempts = 60;
    
    const interval = setInterval(async () => {
        attempts++;
        
        try {
            const response = await fetch('/api/comfyui/history');
            const history = await response.json();
            
            if (history[promptId]) {
                clearInterval(interval);
                await refreshGallery();
                const statusDiv = document.getElementById('status');
                if (statusDiv) {
                    statusDiv.innerHTML = '✅ Generation complete! Image added to gallery.';
                    statusDiv.style.background = '#d4edda';
                    statusDiv.style.color = '#155724';
                    
                    setTimeout(() => {
                        statusDiv.innerHTML = '✅ Ready • ControlNet ready';
                        statusDiv.style.background = '#f0fdf4';
                        statusDiv.style.color = '#166534';
                    }, 3000);
                }
                isGenerating = false;
                const btn = document.getElementById('generateBtn');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '🚀 Generate Image';
                }
            }
            
            if (attempts >= maxAttempts) {
                clearInterval(interval);
                const statusDiv = document.getElementById('status');
                if (statusDiv) {
                    statusDiv.innerHTML = '⏱️ Generation timeout. Please check gallery manually.';
                    statusDiv.style.background = '#fff3cd';
                    statusDiv.style.color = '#856404';
                }
                isGenerating = false;
                const btn = document.getElementById('generateBtn');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '🚀 Generate Image';
                }
            }
        } catch (error) {
            console.error('Polling error:', error);
        }
    }, 2000);
}

async function generate() {
    if (isGenerating) {
        alert('Generation already in progress. Please wait.');
        return;
    }
    
    isGenerating = true;
    const btn = document.getElementById('generateBtn');
    const statusDiv = document.getElementById('status');
    
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '🎨 Processing...';
    }
    
    if (statusDiv) {
        statusDiv.innerHTML = '⏳ Initializing generation...';
        statusDiv.style.background = '#fff3cd';
        statusDiv.style.color = '#856404';
    }
    
    const selectedModel = document.getElementById('model').value;
    const isSDXL = selectedModel.toLowerCase().includes('sdxl');
    const controlImageInput = document.getElementById('controlImageInput');
    const hasControlImage = controlImageInput && controlImageInput.files && controlImageInput.files.length > 0;
    
    // Get control image base64 if exists
    let controlImageBase64 = null;
    if (hasControlImage) {
        const file = controlImageInput.files[0];
        controlImageBase64 = await new Promise((resolve) => {
            const reader = new FileReader();
            reader.onload = (e) => resolve(e.target.result);
            reader.readAsDataURL(file);
        });
    }
    
    const payload = {
        client_id: 'client_' + Date.now(),
        model: selectedModel,
        positive_prompt: document.getElementById('positive_prompt').value,
        negative_prompt: document.getElementById('negative_prompt').value,
        steps: parseInt(document.getElementById('steps').value),
        cfg: parseFloat(document.getElementById('cfg').value),
        width: parseInt(document.getElementById('width').value),
        height: parseInt(document.getElementById('height').value),
        sampler: document.getElementById('sampler').value,
        refiner_model: isSDXL ? document.getElementById('refiner_model')?.value : null,
        refiner_steps: isSDXL ? parseInt(document.getElementById('refiner_steps')?.value || 15) : null,
        controlnet: hasControlImage && controlImageBase64 ? {
            enabled: true,
            preprocessor: currentPreprocessor,
            image_base64: controlImageBase64,
            strength: parseFloat(document.getElementById('cnStrength')?.value || 0.85),
            start_percent: parseFloat(document.getElementById('cnStart')?.value || 0),
            end_percent: parseFloat(document.getElementById('cnEnd')?.value || 1)
        } : null
    };
    
    // Add preprocessor-specific settings
    if (payload.controlnet) {
        switch(currentPreprocessor) {
            case 'canny':
                payload.controlnet.canny_low = parseInt(document.getElementById('cannyLowThreshold')?.value || 50);
                payload.controlnet.canny_high = parseInt(document.getElementById('cannyHighThreshold')?.value || 150);
                break;
            case 'depth':
                payload.controlnet.depth_resolution = parseInt(document.getElementById('depthResolution')?.value || 512);
                break;
            case 'openpose':
                payload.controlnet.openpose_hands = document.getElementById('openposeHands')?.checked ? 'enable' : 'disable';
                payload.controlnet.openpose_body = document.getElementById('openposeBody')?.checked ? 'enable' : 'disable';
                payload.controlnet.openpose_face = document.getElementById('openposeFace')?.checked ? 'enable' : 'disable';
                break;
            case 'mlsd':
                payload.controlnet.mlsd_score_thr = parseFloat(document.getElementById('mlsdScoreThr')?.value || 0.1);
                payload.controlnet.mlsd_dist_thr = parseFloat(document.getElementById('mlsdDistThr')?.value || 0.1);
                payload.controlnet.mlsd_resolution = parseInt(document.getElementById('mlsdResolution')?.value || 512);
                break;
            case 'scribble':
                payload.controlnet.scribble_mode = document.getElementById('scribbleMode')?.value || 'hed';
                break;
            case 'hed':
                payload.controlnet.hed_resolution = parseInt(document.getElementById('hedResolution')?.value || 512);
                break;
            case 'seg':
                payload.controlnet.seg_resolution = parseInt(document.getElementById('segResolution')?.value || 512);
                break;
            case 'normal':
                payload.controlnet.normal_resolution = parseInt(document.getElementById('normalResolution')?.value || 512);
                break;
        }
    }
    
    try {
        const response = await fetch('/api/generate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(payload)
        });
        
        const data = await response.json();
        
        if (data.success) {
            if (statusDiv) {
                statusDiv.innerHTML = '✅ Generation started! Processing...';
                statusDiv.style.background = '#d4edda';
                statusDiv.style.color = '#155724';
            }
            pollForResults(data.prompt_id);
        } else {
            throw new Error(data.error || 'Generation failed');
        }
    } catch (error) {
        console.error('Generation error:', error);
        if (statusDiv) {
            statusDiv.innerHTML = `❌ Error: ${error.message}`;
            statusDiv.style.background = '#f8d7da';
            statusDiv.style.color = '#721c24';
        }
        isGenerating = false;
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '🚀 Generate Image';
        }
    }
}

function switchPreprocessor(preprocessorId) {
    console.log('Switching to preprocessor:', preprocessorId);
    currentPreprocessor = preprocessorId;
    
    // Update button active states
    document.querySelectorAll('.preprocessor-btn').forEach(btn => {
        if (btn.getAttribute('data-preprocessor') === preprocessorId) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
    
    // Hide all settings panels
    document.querySelectorAll('.preprocessor-settings').forEach(settings => {
        settings.classList.remove('active');
        settings.style.display = 'none';
    });
    
    // Show selected settings panel
    const selectedSettings = document.getElementById(`${preprocessorId}Settings`);
    if (selectedSettings) {
        selectedSettings.classList.add('active');
        selectedSettings.style.display = 'block';
        console.log(`Showing ${preprocessorId} settings`);
    }
    
    // Update status
    updateControlNetStatus();
}

// ========== MAIN INITIALIZATION ==========
document.addEventListener('DOMContentLoaded', () => {
    console.log('ComfyUI ControlNet Studio initialized');
    
    // ========== Preprocessor List ==========
    const preprocessors = [
        { id: 'canny', name: '🔲 Canny Edge', icon: '🔲' },
        { id: 'depth', name: '🗺️ Depth Map', icon: '🗺️' },
        { id: 'openpose', name: '🧍 OpenPose', icon: '🧍' },
        { id: 'mlsd', name: '📐 MLSD', icon: '📐' },
        { id: 'scribble', name: '✏️ Scribble', icon: '✏️' },
        { id: 'hed', name: '🎨 HED', icon: '🎨' },
        { id: 'seg', name: '🏷️ SEG', icon: '🏷️' },
        { id: 'normal', name: '📐 Normal Map', icon: '📐' }
    ];
    
    const preprocessorList = document.getElementById('preprocessorList');
    if (preprocessorList) {
        preprocessors.forEach(pp => {
            const btn = document.createElement('button');
            btn.className = 'preprocessor-btn' + (pp.id === currentPreprocessor ? ' active' : '');
            btn.setAttribute('data-preprocessor', pp.id);
            btn.innerHTML = `${pp.icon} ${pp.name}`;
            btn.onclick = () => switchPreprocessor(pp.id);
            preprocessorList.appendChild(btn);
        });
    }
    
    // ========== Setup SDXL Support ==========
    setupSDXLSupport();
    
    // ========== Setup Sliders ==========
    // Canny sliders
    const lowSlider = document.getElementById('cannyLowThreshold');
    const highSlider = document.getElementById('cannyHighThreshold');
    const lowVal = document.getElementById('cannyLowVal');
    const highVal = document.getElementById('cannyHighVal');
    
    if (lowSlider && lowVal) {
        lowSlider.addEventListener('input', function() {
            lowVal.textContent = this.value;
        });
    }
    
    if (highSlider && highVal) {
        highSlider.addEventListener('input', function() {
            highVal.textContent = this.value;
        });
    }
    
    // MLSD sliders
    const mlsdScore = document.getElementById('mlsdScoreThr');
    const mlsdDist = document.getElementById('mlsdDistThr');
    const mlsdScoreVal = document.getElementById('mlsdScoreVal');
    const mlsdDistVal = document.getElementById('mlsdDistVal');
    
    if (mlsdScore && mlsdScoreVal) {
        mlsdScore.addEventListener('input', function() {
            mlsdScoreVal.textContent = this.value;
        });
    }
    
    if (mlsdDist && mlsdDistVal) {
        mlsdDist.addEventListener('input', function() {
            mlsdDistVal.textContent = this.value;
        });
    }
    
    // ControlNet strength sliders
    const strengthSlider = document.getElementById('cnStrength');
    const strengthVal = document.getElementById('strengthVal');
    if (strengthSlider && strengthVal) {
        strengthSlider.addEventListener('input', function() {
            strengthVal.textContent = this.value;
        });
    }
    
    const startSlider = document.getElementById('cnStart');
    const startVal = document.getElementById('startVal');
    if (startSlider && startVal) {
        startSlider.addEventListener('input', function() {
            startVal.textContent = parseFloat(this.value).toFixed(2);
        });
    }
    
    const endSlider = document.getElementById('cnEnd');
    const endVal = document.getElementById('endVal');
    if (endSlider && endVal) {
        endSlider.addEventListener('input', function() {
            endVal.textContent = parseFloat(this.value).toFixed(2);
        });
    }
    
    // ========== Control Image Preview ==========
    const controlImageInput = document.getElementById('controlImageInput');
    const controlPreview = document.getElementById('controlPreview');
    
    if (controlImageInput) {
        controlImageInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (controlPreview) {
                        controlPreview.innerHTML = `<img src="${e.target.result}" alt="Control Image" style="max-width: 100%; border-radius: 8px;">`;
                    }
                    updateControlNetStatus();
                };
                reader.readAsDataURL(file);
            } else {
                if (controlPreview) controlPreview.innerHTML = '';
                updateControlNetStatus();
            }
        });
    }
    
    // ========== Generate Button ==========
    const generateBtn = document.getElementById('generateBtn');
    if (generateBtn) {
        generateBtn.onclick = generate;
    }
    
    // ========== Refresh Gallery Button ==========
    const refreshBtn = document.getElementById('refreshGalleryBtn');
    if (refreshBtn) {
        refreshBtn.onclick = refreshGallery;
    }
    
    // ========== Interrupt Button ==========
    const interruptBtn = document.getElementById('interruptBtn');
    if (interruptBtn) {
        interruptBtn.onclick = interruptGeneration;
    }
    
    // ========== Initialize ==========
    switchPreprocessor('canny');
    refreshGallery();
    
    // Auto-refresh gallery every 10 seconds
    setInterval(refreshGallery, 10000);
    
    console.log('Preprocessor system initialized with', preprocessors.length, 'preprocessors');
});

// Make functions global for HTML onclick
window.downloadImage = downloadImage;
window.switchPreprocessor = switchPreprocessor;
window.generate = generate;
window.interruptGeneration = interruptGeneration;
window.refreshGallery = refreshGallery;
</script>
    <script src="{{ asset('js/comfyui/config.js') }}"></script>
<script src="{{ asset('js/comfyui/utils.js') }}"></script>
<script src="{{ asset('js/comfyui/api.js') }}"></script>
<script src="{{ asset('js/comfyui/gallery.js') }}"></script>
<script src="{{ asset('js/comfyui/websocket.js') }}"></script>
<script src="{{ asset('js/comfyui/ui.js') }}"></script>
<script src="{{ asset('js/comfyui/main.js') }}"></script>

</body>
</html>