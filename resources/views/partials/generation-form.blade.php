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
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
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
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        flex: 1;
        border: none;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.2s;
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
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
    }
    
    .preprocessor-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 12px;
        padding: 15px;
    }
    
    .preprocessor-btn {
        background: white;
        border: 1px solid #e2e8f0;
        padding: 10px;
        border-radius: 10px;
        text-align: center;
        font-size: 12px;
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
    
    .preprocessor-settings {
        display: none;
        padding: 15px;
        background: #f8fafc;
        border-radius: 12px;
        margin-top: 10px;
    }
    
    .preprocessor-settings.active {
        display: block;
    }
    
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
        position: relative;
    }
    
    .image-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        cursor: pointer;
    }
    
    .image-actions {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0,0,0,0.7);
        padding: 8px;
        text-align: center;
        opacity: 0;
        transition: opacity 0.2s;
    }
    
    .image-card:hover .image-actions {
        opacity: 1;
    }
    
    .download-btn {
        background: #667eea;
        color: white;
        border: none;
        padding: 4px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 12px;
    }
    
    .vram-warning {
        background: #fff3cd;
        border: 1px solid #ffc107;
        padding: 8px 12px;
        border-radius: 6px;
        margin-top: 8px;
        font-size: 12px;
        display: none;
    }
    
    .btn-group {
        display: flex;
        gap: 12px;
        padding: 20px;
    }
    
    #status {
        margin: 0 20px 20px 20px;
        padding: 12px;
        border-radius: 8px;
        background: #f0fdf4;
        color: #166534;
    }
    
    .control-strength {
        padding: 15px 20px;
    }
    
    .control-strength label {
        font-weight: 600;
        margin-bottom: 8px;
        display: block;
    }
    
    input[type=range] {
        width: 100%;
        accent-color: #8b5cf6;
    }
    
    .image-preview {
        margin-top: 10px;
        text-align: center;
    }
    
    .image-preview img {
        max-width: 100%;
        max-height: 200px;
        border-radius: 8px;
        border: 2px solid #e2e8f0;
    }
    
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @media (max-width: 1024px) {
        .container {
            grid-template-columns: 1fr;
        }
        body {
            padding: 10px;
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
                    <div id="vram-warning" class="vram-warning">⚠️ Warning: Selected model may exceed VRAM</div>
                </div>
                
                <div class="control-group" id="refinerGroup" style="display: none;">
                    <label>🔧 SDXL Refiner Model</label>
                    <select id="refiner_model">
                        <option value="sd_xl_refiner_1.0.safetensors">SDXL Refiner 1.0</option>
                    </select>
                    <div style="margin-top: 8px;">
                        <label>✨ Refiner Steps</label>
                        <input type="number" id="refiner_steps" value="15" min="1" max="100">
                        <small>Refiner adds details after base generation</small>
                    </div>
                    <div id="resolutionHint" style="margin-top: 8px; padding: 8px; background: #e0e7ff; border-radius: 6px; font-size: 12px; display: none;"></div>
                </div>
                
                <div class="control-group">
                    <label>✨ Positive Prompt</label>
                    <textarea id="positive_prompt" rows="3">masterpiece, architectural photography, modern building exterior, golden hour lighting, luxury facade, high-quality rendering, sharp focus, volumetric lighting</textarea>
                </div>
                
                <div class="control-group">
                    <label>❌ Negative Prompt</label>
                    <textarea id="negative_prompt" rows="2">worst quality, low quality, blurry, distorted, cartoonish, ugly</textarea>
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
                        <input type="number" id="width" value="512" min="256" max="1536" step="64">
                    </div>
                    <div class="param-item">
                        <label>Height</label>
                        <input type="number" id="height" value="512" min="256" max="1536" step="64">
                    </div>
                    <div class="param-item">
                        <label>Sampler</label>
                        <select id="sampler">
                            <option value="euler">Euler</option>
                            <option value="euler_ancestral">Euler Ancestral</option>
                            <option value="dpmpp_2m" selected>DPM++ 2M</option>
                            <option value="ddim">DDIM</option>
                        </select>
                    </div>
                </div>
                
                <div class="control-group">
                    <label>🖼️ Control Image (for ControlNet)</label>
                    <input type="file" id="controlImageInput" accept="image/*">
                    <div id="controlPreview" class="image-preview"></div>
                    <small>Upload an image to guide the generation</small>
                </div>
                
                <div class="btn-group">
                    <button id="generateBtn" class="btn-primary">🚀 Generate Image</button>
                    <button id="refreshGalleryBtn" class="btn-secondary">🔄 Refresh Gallery</button>
                    <button id="interruptBtn" class="btn-secondary" style="background: #dc3545; color: white;">⏹️ Interrupt</button>
                </div>
                
                <div id="status">✅ Ready • ControlNet inactive</div>
            </div>
            
            <!-- ControlNet Preprocessor Selection -->
            <div class="card">
                <div class="card-header">🎨 ControlNet Preprocessors</div>
                <div id="preprocessorList" class="preprocessor-grid"></div>
            </div>
            
            <!-- Preprocessor Settings -->
            <div class="card">
                <div class="card-header">🎛️ Preprocessor Settings</div>
                <div id="preprocessorSettings">
                    <div id="cannySettings" class="preprocessor-settings">
                        <div class="control-group">
                            <label>🔲 Canny Low Threshold: <span id="cannyLowVal">50</span> (0-255)</label>
                            <input type="range" id="cannyLowThreshold" min="0" max="255" value="50" step="1">
                        </div>
                        <div class="control-group">
                            <label>🔲 Canny High Threshold: <span id="cannyHighVal">150</span> (0-255)</label>
                            <input type="range" id="cannyHighThreshold" min="0" max="255" value="150" step="1">
                        </div>
                    </div>
                    <div id="depthSettings" class="preprocessor-settings">
                        <div class="control-group">
                            <label>Depth Resolution</label>
                            <select id="depthResolution">
                                <option value="512">512</option>
                                <option value="1024">1024</option>
                            </select>
                        </div>
                    </div>
                    <div id="openposeSettings" class="preprocessor-settings">
                        <div class="control-group">
                            <label>Detection Features</label>
                            <div style="display: flex; gap: 10px; margin-top: 8px;">
                                <label><input type="checkbox" id="openposeHands" checked> Hands</label>
                                <label><input type="checkbox" id="openposeBody" checked> Body</label>
                                <label><input type="checkbox" id="openposeFace"> Face</label>
                            </div>
                        </div>
                    </div>
                    <div id="mlsdSettings" class="preprocessor-settings">
                        <div class="control-group">
                            <label>Score Threshold: <span id="mlsdScoreVal">0.10</span></label>
                            <input type="range" id="mlsdScoreThr" min="0" max="1" value="0.1" step="0.01">
                        </div>
                        <div class="control-group">
                            <label>Distance Threshold: <span id="mlsdDistVal">0.10</span></label>
                            <input type="range" id="mlsdDistThr" min="0" max="0.5" value="0.1" step="0.01">
                        </div>
                    </div>
                    <div id="scribbleSettings" class="preprocessor-settings">
                        <div class="control-group">
                            <label>Scribble Mode</label>
                            <select id="scribbleMode">
                                <option value="hed">HED</option>
                                <option value="pidi">PIDI</option>
                            </select>
                        </div>
                    </div>
                    <div id="hedSettings" class="preprocessor-settings">
                        <div class="control-group">
                            <label>HED Resolution</label>
                            <select id="hedResolution">
                                <option value="512">512</option>
                                <option value="1024">1024</option>
                            </select>
                        </div>
                    </div>
                    <div id="segSettings" class="preprocessor-settings">
                        <div class="control-group">
                            <label>SEG Resolution</label>
                            <select id="segResolution">
                                <option value="512">512</option>
                                <option value="1024">1024</option>
                            </select>
                        </div>
                    </div>
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
            
            <!-- ControlNet Strength Controls -->
            <div class="card">
                <div class="card-header">🎮 ControlNet Strength</div>
                <div class="control-strength">
                    <div style="margin-bottom: 15px;">
                        <div style="display: flex; justify-content: space-between;">
                            <label>💪 Strength</label>
                            <span id="strengthVal">0.85</span>
                        </div>
                        <input type="range" id="cnStrength" min="0" max="2" step="0.01" value="0.85">
                    </div>
                    <div style="display: flex; gap: 12px;">
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between;">
                                <label>▶️ Start %</label>
                                <span id="startVal">0.00</span>
                            </div>
                            <input type="range" id="cnStart" min="0" max="1" value="0" step="0.01">
                        </div>
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between;">
                                <label>⏹️ End %</label>
                                <span id="endVal">1.00</span>
                            </div>
                            <input type="range" id="cnEnd" min="0" max="1" value="1" step="0.01">
                        </div>
                    </div>
                </div>
                <div id="controlnetStatus" style="padding: 0 20px 20px 20px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
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
                        🎨 No images yet<br>
                        <small>Your generated images will appear here</small>
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

// ========== PREPROCESSORS ==========
const preprocessors = [
    { id: 'canny', name: '🔲 Canny', icon: '🔲' },
    { id: 'depth', name: '🗺️ Depth', icon: '🗺️' },
    { id: 'openpose', name: '🧍 OpenPose', icon: '🧍' },
    { id: 'mlsd', name: '📐 MLSD', icon: '📐' },
    { id: 'scribble', name: '✏️ Scribble', icon: '✏️' },
    { id: 'hed', name: '🎨 HED', icon: '🎨' },
    { id: 'seg', name: '🏷️ SEG', icon: '🏷️' },
    { id: 'normal', name: '📐 Normal', icon: '📐' }
];

// ========== HELPER FUNCTIONS ==========
function getPreprocessorName(id) {
    const names = {
        'canny': 'Canny Edge', 'depth': 'Depth Map', 'openpose': 'OpenPose',
        'mlsd': 'MLSD', 'scribble': 'Scribble', 'hed': 'HED',
        'seg': 'Segmentation', 'normal': 'Normal Map'
    };
    return names[id] || id;
}

function updateControlNetStatus() {
    const dot = document.getElementById('controlnetDot');
    const text = document.getElementById('controlnetText');
    const details = document.getElementById('controlnetDetails');
    const hasImage = currentControlImage !== null;
    
    if (dot && text && details) {
        if (hasImage) {
            dot.style.backgroundColor = '#10b981';
            text.textContent = `✅ ControlNet Active: ${getPreprocessorName(currentPreprocessor)}`;
            details.innerHTML = `Using ${getPreprocessorName(currentPreprocessor)} to guide generation. Strength: ${document.getElementById('cnStrength')?.value || 0.85}`;
        } else {
            dot.style.backgroundColor = '#ef4444';
            text.textContent = '⏸️ ControlNet Inactive';
            details.innerHTML = 'Upload an image and select a preprocessor to enable ControlNet.';
        }
    }
}

function buildPreprocessorUI() {
    const container = document.getElementById('preprocessorList');
    if (!container) return;
    
    container.innerHTML = '';
    preprocessors.forEach(pp => {
        const btn = document.createElement('button');
        btn.className = `preprocessor-btn ${pp.id === currentPreprocessor ? 'active' : ''}`;
        btn.setAttribute('data-id', pp.id);
        btn.innerHTML = `<div style="font-size: 20px;">${pp.icon}</div><div style="font-size: 11px; margin-top: 4px;">${pp.name}</div>`;
        btn.onclick = () => selectPreprocessor(pp.id);
        container.appendChild(btn);
    });
}

function selectPreprocessor(id) {
    currentPreprocessor = id;
    
    document.querySelectorAll('.preprocessor-btn').forEach(btn => {
        if (btn.getAttribute('data-id') === id) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
    
    document.querySelectorAll('.preprocessor-settings').forEach(panel => {
        panel.classList.remove('active');
        panel.style.display = 'none';
    });
    
    const selectedPanel = document.getElementById(`${id}Settings`);
    if (selectedPanel) {
        selectedPanel.classList.add('active');
        selectedPanel.style.display = 'block';
    }
    
    updateControlNetStatus();
}

function setupImageUpload() {
    const input = document.getElementById('controlImageInput');
    const preview = document.getElementById('controlPreview');
    
    if (input) {
        input.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 10 * 1024 * 1024) {
                    alert('Image size should be less than 10MB');
                    input.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = (event) => {
                    currentControlImage = event.target.result;
                    if (preview) {
                        preview.innerHTML = `<img src="${currentControlImage}" style="max-width: 100%; max-height: 200px; border-radius: 8px; border: 2px solid #667eea;">`;
                    }
                    updateControlNetStatus();
                };
                reader.readAsDataURL(file);
            } else {
                currentControlImage = null;
                if (preview) preview.innerHTML = '';
                updateControlNetStatus();
            }
        });
    }
}

function setupSliders() {
    const strengthSlider = document.getElementById('cnStrength');
    if (strengthSlider) {
        strengthSlider.addEventListener('input', () => {
            document.getElementById('strengthVal').textContent = strengthSlider.value;
            updateControlNetStatus();
        });
    }
    
    const startSlider = document.getElementById('cnStart');
    if (startSlider) {
        startSlider.addEventListener('input', () => {
            document.getElementById('startVal').textContent = parseFloat(startSlider.value).toFixed(2);
        });
    }
    
    const endSlider = document.getElementById('cnEnd');
    if (endSlider) {
        endSlider.addEventListener('input', () => {
            document.getElementById('endVal').textContent = parseFloat(endSlider.value).toFixed(2);
        });
    }
    
    const lowSlider = document.getElementById('cannyLowThreshold');
    if (lowSlider) {
        lowSlider.addEventListener('input', () => {
            document.getElementById('cannyLowVal').textContent = lowSlider.value;
        });
    }
    
    const highSlider = document.getElementById('cannyHighThreshold');
    if (highSlider) {
        highSlider.addEventListener('input', () => {
            document.getElementById('cannyHighVal').textContent = highSlider.value;
        });
    }
    
    const mlsdScore = document.getElementById('mlsdScoreThr');
    if (mlsdScore) {
        mlsdScore.addEventListener('input', () => {
            document.getElementById('mlsdScoreVal').textContent = parseFloat(mlsdScore.value).toFixed(2);
        });
    }
    
    const mlsdDist = document.getElementById('mlsdDistThr');
    if (mlsdDist) {
        mlsdDist.addEventListener('input', () => {
            document.getElementById('mlsdDistVal').textContent = parseFloat(mlsdDist.value).toFixed(2);
        });
    }
}

function setupSDXLSupport() {
    const modelSelect = document.getElementById('model');
    const refinerGroup = document.getElementById('refinerGroup');
    
    modelSelect.addEventListener('change', () => {
        const val = modelSelect.value.toLowerCase();
        // Fixed: Added check for 'sd_xl' to match your filenames
        const isSDXL = val.includes('sdxl') || val.includes('sd_xl');

        if (isSDXL) {
            refinerGroup.style.display = 'block';
            // SDXL performs best at 1024x1024
            document.getElementById('width').value = 1024;
            document.getElementById('height').value = 1024;
        } else {
            refinerGroup.style.display = 'none';
        }
    });
}
async function refreshGallery() {
    const galleryDiv = document.getElementById('images');
    if (!galleryDiv) return;
    
    try {
        // Change the URL to your internal Laravel API
        const response = await fetch('/api/images'); 
        const images = await response.json();
        
        if (!images || images.length === 0) {
            galleryDiv.innerHTML = '<div style="text-align: center; padding: 60px 20px; color: #9ca3af;">🎨 No images yet</div>';
            return;
        }
        
        galleryDiv.innerHTML = images.map(img => {
            // If file_name is null, show the "Painting" placeholder
            if (!img.file_name) {
                return `<div class="image-card" style="display:flex; align-items:center; justify-content:center; background:#eee;">
                            <div style="text-align:center">🎨 Painting...<br><small>${img.prompt_id.substring(0,8)}</small></div>
                        </div>`;
            }

            // Otherwise show the real image from your symlinked outputs folder
            return `
                <div class="image-card">
                    <img src="/outputs/${img.file_name}" onclick="window.open(this.src, '_blank')">
                    <div class="image-actions">
                        <button class="download-btn" onclick="downloadImage('${img.file_name}')">📥 Download</button>
                    </div>
                </div>`;
        }).join('');
    } catch (error) {
        console.error('Gallery error:', error);
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

async function pollForResults(promptId) {
    let attempts = 0;
    const maxAttempts = 60; // 2 minutes (60 * 2 seconds)
    
    const interval = setInterval(async () => {
        attempts++;
        
        try {
            // 1. Check ComfyUI history to see if the job is done
            const response = await fetch('/api/comfyui/history');
            const history = await response.json();
            
            if (history[promptId]) {
                clearInterval(interval);
                
                // 2. Extract the filename from the ComfyUI output
                const outputs = history[promptId].outputs;
                let fileName = null;
                
                // ComfyUI outputs are nested; we look for the first image filename found
                for (const nodeId in outputs) {
                    if (outputs[nodeId].images && outputs[nodeId].images.length > 0) {
                        fileName = outputs[nodeId].images[0].filename;
                        break;
                    }
                }

                if (fileName) {
                    // 3. IMPORTANT: Tell Laravel to update the database row from NULL to the actual filename
                    await fetch('/api/images/complete', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            prompt_id: promptId,
                            file_name: fileName
                        })
                    });
                }

                // 4. UI Updates
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
            
            // Handle Timeout
            if (attempts >= maxAttempts) {
                clearInterval(interval);
                console.error('Polling timed out');
                isGenerating = false;
                const btn = document.getElementById('generateBtn');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '🚀 Generate Image';
                }
                if (statusDiv) statusDiv.innerHTML = '❌ Timeout: Image taking too long.';
            }
        } catch (error) {
            console.error('Polling error:', error);
        }
    }, 2000);
}
// ========== LOAD MODELS DYNAMICALLY ==========
async function loadModels() {
    const modelSelect = document.getElementById('model');
    if (!modelSelect) return;
    
    modelSelect.innerHTML = '<option value="">Loading models...</option>';
    
    try {
        const response = await fetch('/api/comfyui/object-info');
        const data = await response.json();
        
        if (data && data.checkpoints && data.checkpoints.length > 0) {
            const checkpoints = data.checkpoints;
            
            // Organize models by type
            const sd15Models = [];
            const sdxlModels = [];
            const fluxModels = [];
            const otherModels = [];
            
            checkpoints.forEach(model => {
                const lowerModel = model.toLowerCase();
                if (lowerModel.includes('sdxl') || lowerModel.includes('xl')) {
                    sdxlModels.push(model);
                } else if (lowerModel.includes('flux')) {
                    fluxModels.push(model);
                } else if (lowerModel.includes('sd15') || lowerModel.includes('v1-5') || 
                           lowerModel.includes('dreamshaper') || lowerModel.includes('realistic')) {
                    sd15Models.push(model);
                } else if (lowerModel.includes('sd3.5') || lowerModel.includes('qwen')) {
                    otherModels.push(model);
                } else {
                    otherModels.push(model);
                }
            });
            
            // Build the options HTML
            let optionsHtml = '';
            
            if (sd15Models.length > 0) {
                optionsHtml += '<optgroup label="📦 SD1.5 Models">';
                sd15Models.forEach(model => {
                    const selected = model === 'dreamshaper_8.safetensors' ? 'selected' : '';
                    optionsHtml += `<option value="${model}" ${selected}>✨ ${model}</option>`;
                });
                optionsHtml += '</optgroup>';
            }
            
            if (sdxlModels.length > 0) {
                optionsHtml += '<optgroup label="🎨 SDXL Models (1024x1024)">';
                sdxlModels.forEach(model => {
                    optionsHtml += `<option value="${model}">🎨 ${model}</option>`;
                });
                optionsHtml += '</optgroup>';
            }
            
            if (fluxModels.length > 0) {
                optionsHtml += '<optgroup label="⚡ Flux Models (High VRAM)">';
                fluxModels.forEach(model => {
                    optionsHtml += `<option value="${model}">⚡ ${model}</option>`;
                });
                optionsHtml += '</optgroup>';
            }
            
            if (otherModels.length > 0) {
                optionsHtml += '<optgroup label="🌀 Other Models">';
                otherModels.forEach(model => {
                    optionsHtml += `<option value="${model}">🌀 ${model}</option>`;
                });
                optionsHtml += '</optgroup>';
            }
            
            modelSelect.innerHTML = optionsHtml;
            
            // Trigger change event to update SDXL settings
            modelSelect.dispatchEvent(new Event('change'));
            
            console.log(`✅ Loaded ${checkpoints.length} models`);
        } else {
            // Fallback to default models if API fails
            modelSelect.innerHTML = `
                <option value="dreamshaper_8.safetensors">✨ Dreamshaper 8 (SD1.5)</option>
                <option value="v1-5-pruned-emaonly-fp16.safetensors">📦 v1-5-pruned-emaonly-fp16 (SD1.5)</option>
                <option value="realisticVisionV60B1_v51HyperVAE.safetensors">🎭 Realistic Vision V6.0 (SD1.5)</option>
                <option value="sd_xl_base_1.0.safetensors">🎨 SDXL Base 1.0 (1024x1024)</option>
                <option value="flux1-dev-fp8.safetensors">⚡ Flux Dev FP8</option>
                <option value="sd3.5_large_fp8_scaled.safetensors">🌀 SD3.5 Large FP8</option>
                <option value="qwen_image_2512_fp8_e4m3fn.safetensors">🖼️ Qwen Image 2.5</option>
            `;
            console.warn('Using fallback models');
        }
    } catch (error) {
        console.error('Failed to load models:', error);
        // Fallback options
        modelSelect.innerHTML = `
            <option value="dreamshaper_8.safetensors" selected>✨ Dreamshaper 8 (SD1.5, artistic, ControlNet ready)</option>
                        <option value="v1-5-pruned-emaonly-fp16.safetensors">📦 v1-5-pruned-emaonly-fp16 (SD1.5, FP16, low VRAM)</option>
                        <option value="v1-5-pruned.safetensors">📦 v1-5-pruned (SD1.5, standard)</option>
                        <option value="realisticVisionV60B1_v51HyperVAE.safetensors">🎭 Realistic Vision V6.0 (SD1.5, photorealistic)</option>
                        <option value="sd_xl_base_1.0.safetensors">🎨 SDXL Base 1.0 (high quality, 1024x1024)</option>
                        <option value="sd_xl_refiner_1.0.safetensors">🔧 SDXL Refiner 1.0 (detail enhancement)</option>
                        <option value="flux1-dev-fp8.safetensors">⚡ Flux Dev FP8 (experimental, high VRAM)</option>
                        <option value="sd3.5_large_fp8_scaled.safetensors">🌀 SD3.5 Large FP8 (requires >8GB VRAM)</option>
                        <option value="qwen_image_2512_fp8_e4m3fn.safetensors">🖼️ Qwen Image 2.5 (experimental)</option>
        `;
    }
}

// Also load refiner models dynamically
async function loadRefinerModels() {
    const refinerSelect = document.getElementById('refiner_model');
    if (!refinerSelect) return;
    
    try {
        const response = await fetch('/api/comfyui/object-info');
        const data = await response.json();
        
        if (data && data.checkpoints && data.checkpoints.length > 0) {
            const refiners = data.checkpoints.filter(model => 
                model.toLowerCase().includes('refiner') || 
                model.toLowerCase().includes('sdxl') && model.toLowerCase().includes('refiner')
            );
            
            if (refiners.length > 0) {
                refinerSelect.innerHTML = refiners.map(model => 
                    `<option value="${model}">🔧 ${model}</option>`
                ).join('');
            } else {
                // Default refiner options
                refinerSelect.innerHTML = `
                    <option value="sd_xl_refiner_1.0.safetensors">SDXL Refiner 1.0</option>
                `;
            }
        }
    } catch (error) {
        console.error('Failed to load refiner models:', error);
    }
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
    const hasControlImage = currentControlImage !== null;
    
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
        controlnet: hasControlImage ? {
            enabled: true,
            preprocessor: currentPreprocessor,
            image_base64: currentControlImage,
            strength: parseFloat(document.getElementById('cnStrength')?.value || 0.85),
            start_percent: parseFloat(document.getElementById('cnStart')?.value || 0),
            end_percent: parseFloat(document.getElementById('cnEnd')?.value || 1)
        } : null
    };
    
    // Add preprocessor-specific settings
    if (payload.controlnet) {
        switch(currentPreprocessor) {
            case 'canny':
                payload.controlnet.canny_low = parseInt(document.getElementById('cannyLowThreshold')?.value || 50) / 255;
                payload.controlnet.canny_high = parseInt(document.getElementById('cannyHighThreshold')?.value || 150) / 255;
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


// ========== INITIALIZATION ==========
document.addEventListener('DOMContentLoaded', () => {
    console.log('ComfyUI Studio initializing...');

    // Load models first
    loadModels();
    loadRefinerModels();

    buildPreprocessorUI();
    setupImageUpload();
    setupSliders();
    setupSDXLSupport();
    selectPreprocessor('canny');
    refreshGallery();
    
    const generateBtn = document.getElementById('generateBtn');
    if (generateBtn) generateBtn.onclick = generate;
    
    const refreshBtn = document.getElementById('refreshGalleryBtn');
    if (refreshBtn) refreshBtn.onclick = refreshGallery;
    
    const interruptBtn = document.getElementById('interruptBtn');
    if (interruptBtn) interruptBtn.onclick = interruptGeneration;
    
    setInterval(refreshGallery, 10000);
    
    console.log('ComfyUI Studio ready!');
});

// Make functions global
window.downloadImage = downloadImage;
</script>
</body>
</html>