<style>
    /* Main container */
    .comfy-container {
        padding: 20px;
        background: #f5f5f5;
        border-radius: 12px;
    }
    
    .comfy-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .card-header {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e5e7eb;
        color: #374151;
    }
    
    .form-group {
        margin-bottom: 15px;
    }
    
    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 5px;
        color: #374151;
    }
    
    .form-input, .form-select, .form-textarea {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        background: white;
    }
    
    .form-input:focus, .form-select:focus, .form-textarea:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 2px rgba(99,102,241,0.2);
    }
    
    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    
    .btn-primary {
        background: #6366f1;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
    }
    
    .btn-primary:hover {
        background: #4f46e5;
    }
    
    .btn-secondary {
        background: #e5e7eb;
        color: #374151;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
    }
    
    .btn-secondary:hover {
        background: #d1d5db;
    }
    
    .button-group {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }
    
    .status {
        margin-top: 15px;
        padding: 10px;
        border-radius: 8px;
        font-size: 14px;
    }
    
    .status-ready {
        background: #f3f4f6;
        color: #374151;
    }
    
    .status-generating {
        background: #fef3c7;
        color: #92400e;
    }
    
    .status-success {
        background: #d1fae5;
        color: #065f46;
    }
    
    .status-error {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .preprocessor-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        margin-bottom: 20px;
    }
    
    .preprocessor-btn {
        background: white;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 10px;
        text-align: center;
        cursor: pointer;
        font-size: 12px;
        transition: all 0.2s;
    }
    
    .preprocessor-btn:hover {
        border-color: #6366f1;
        background: #eef2ff;
    }
    
    .preprocessor-btn-active {
        background: #6366f1;
        color: white;
        border-color: #6366f1;
    }
    
    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
    
    .gallery-item {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        position: relative;
    }
    
    .gallery-img {
        width: 100%;
        height: 130px;
        object-fit: cover;
        cursor: pointer;
    }
    
    .gallery-placeholder {
        width: 100%;
        height: 130px;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
    }
    
    .gallery-prompt {
        padding: 8px;
        font-size: 11px;
        color: #6b7280;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .delete-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: #ef4444;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 4px 8px;
        cursor: pointer;
        font-size: 11px;
    }
    
    .delete-btn:hover {
        background: #dc2626;
    }
    
    .upload-area {
        background: #f8fafc;
        border: 2px dashed #e2e8f0;
        border-radius: 12px;
        padding: 15px;
        text-align: center;
        margin-bottom: 20px;
    }
    
    .image-preview {
        margin-top: 10px;
        border-radius: 8px;
        overflow: hidden;
        background: white;
        min-height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .image-preview img {
        max-width: 100%;
        max-height: 100px;
        object-fit: cover;
    }
    
    .range-group {
        margin-bottom: 15px;
    }
    
    .range-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
    }
    
    .range-label {
        font-size: 0.8rem;
        font-weight: bold;
    }
    
    .range-value {
        font-weight: bold;
        color: #6366f1;
    }
    
    input[type="range"] {
        width: 100%;
        accent-color: #6366f1;
    }
    
    .flex-row {
        display: flex;
        gap: 12px;
    }
    
    .flex-1 {
        flex: 1;
    }
    
    .dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #ef4444;
        transition: background 0.3s;
    }
    
    @media (max-width: 768px) {
        .grid-2, .gallery-grid {
            grid-template-columns: 1fr;
        }
        .preprocessor-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<x-filament-panels::page>
    <div>
        <div class="comfy-container">
            <div class="grid-2">
                <!-- LEFT PANEL -->
                <div>
                    <div class="comfy-card">
                        <div class="card-header">⚙️ Model & Prompt Configuration</div>
                        
                        <div class="form-group">
                            <label class="form-label">🎭 Model</label>
                            <select id="model" class="form-select">
                                
                                <option value="sd_xl_base_1.0.safetensors">SDXL Base 1.0</option>
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
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">✨ Positive Prompt</label>
                            <textarea id="positive_prompt" rows="3" class="form-textarea">masterpiece, high quality, detailed</textarea>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">❌ Negative Prompt</label>
                            <textarea id="negative_prompt" rows="2" class="form-textarea">worst quality, low quality, blurry</textarea>
                        </div>
                        
                        <div class="grid-2" style="gap: 15px;">
                            <div class="form-group">
                                <label class="form-label">Steps</label>
                                <input type="number" id="steps" value="20" class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label">CFG</label>
                                <input type="number" id="cfg" value="7" step="0.5" class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Width</label>
                                <input type="number" id="width" value="512" class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Height</label>
                                <input type="number" id="height" value="512" class="form-input">
                            </div>
                        </div>
                        <div class="grid-2" style="gap: 15px;">
                        <div class="form-group">
                            <label class="form-label">Steps</label>
                            <input type="number" id="steps" value="20" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">CFG</label>
                            <input type="number" id="cfg" value="7" step="0.5" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Width</label>
                            <input type="number" id="width" value="512" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Height</label>
                            <input type="number" id="height" value="512" class="form-input">
                        </div>
                        <!-- SAMPLER НЭМЭХ -->
                        <div class="form-group">
                            <label class="form-label">🎛️ Sampler</label>
                            <select id="sampler" class="form-select">
                                <option value="euler">Euler</option>
                                <option value="euler_ancestral">Euler Ancestral</option>
                                <option value="dpmpp_2m" selected>DPM++ 2M</option>
                                <option value="dpmpp_2m_sde">DPM++ 2M SDE</option>
                                <option value="ddim">DDIM</option>
                                <option value="uni_pc">Uni PC</option>
                            </select>
                        </div>
                        <!-- SCHEDULER НЭМЭХ -->
                        <div class="form-group">
                            <label class="form-label">⚙️ Scheduler</label>
                            <select id="scheduler" class="form-select">
                                <option value="normal">Normal</option>
                                <option value="karras" selected>Karras</option>
                                <option value="exponential">Exponential</option>
                                <option value="sgm_uniform">SGM Uniform</option>
                            </select>
                        </div>
                    </div>
                        <div class="button-group">
                            <button id="generateBtn" class="btn-primary">🚀 Generate</button>
                            <button id="refreshGalleryBtn" class="btn-secondary">🔄 Refresh</button>
                        </div>
                        
                        <div id="status" class="status status-ready">✅ Ready</div>
                    </div>
                    
                    <div class="comfy-card">
                        <div class="card-header" style="display: flex; align-items: center; justify-content: space-between;">
                            <span>🎮 ControlNet Preprocessors</span>
                            <div id="controlnetDot" class="dot"></div>
                        </div>
                        
                        <div id="preprocessorList" class="preprocessor-grid"></div>
                        
                        <div class="upload-area">
                            <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 10px;">📤 Upload Control Image</label>
                            <input type="file" id="controlImageInput" accept="image/*" style="width: 100%; font-size: 11px;">
                            <div id="controlPreview" class="image-preview">
                                <span style="color: #cbd5e1; font-size: 10px;">No preview</span>
                            </div>
                        </div>
                        
                        <div class="range-group">
                            <div class="range-header">
                                <label class="range-label">💪 Strength</label>
                                <span id="strengthVal" class="range-value">0.85</span>
                            </div>
                            <input type="range" id="cnStrength" min="0" max="2" step="0.01" value="0.85">
                        </div>
                        
                        <div class="flex-row">
                            <div class="flex-1">
                                <div class="range-header">
                                    <label class="range-label">▶️ Start %</label>
                                    <span id="startVal" class="range-value">0.00</span>
                                </div>
                                <input type="range" id="cnStart" min="0" max="1" value="0" step="0.01">
                            </div>
                            <div class="flex-1">
                                <div class="range-header">
                                    <label class="range-label">⏹️ End %</label>
                                    <span id="endVal" class="range-value">1.00</span>
                                </div>
                                <input type="range" id="cnEnd" min="0" max="1" value="1" step="0.01">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- RIGHT PANEL -->
                <div>
                    <div class="comfy-card">
                        <div class="card-header">🖼️ Generated Images</div>
                        <div id="images" class="gallery-grid">
                            <div style="text-align: center; grid-column: span 2; color: #9ca3af;">No images yet</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>

@push('scripts')
<script>
    const preprocessors = [
        { id: 'canny', name: 'Canny', icon: '🔲' },
        { id: 'depth', name: 'Depth', icon: '🗺️' },
        { id: 'openpose', name: 'Pose', icon: '🧍' },
        { id: 'scribble', name: 'Sketch', icon: '✏️' },
        { id: 'mlsd', name: 'MLSD', icon: '📐' },
        { id: 'hed', name: 'HED', icon: '🎨' },
        { id: 'seg', name: 'Seg', icon: '🏷️' },
        { id: 'normal', name: 'Normal', icon: '📐' }
    ];
    
    let currentPreprocessor = 'canny';
    let currentControlImage = null;
    let isGenerating = false;
    
    function buildPreprocessors() {
        const container = document.getElementById('preprocessorList');
        if (!container) return;
        
        container.innerHTML = '';
        preprocessors.forEach(pp => {
            const btn = document.createElement('button');
            btn.className = `preprocessor-btn ${pp.id === currentPreprocessor ? 'preprocessor-btn-active' : ''}`;
            btn.innerHTML = `${pp.icon} ${pp.name}`;
            btn.onclick = () => {
                document.querySelectorAll('.preprocessor-btn').forEach(b => {
                    b.classList.remove('preprocessor-btn-active');
                });
                btn.classList.add('preprocessor-btn-active');
                currentPreprocessor = pp.id;
            };
            container.appendChild(btn);
        });
    }
    
    function setupImageUpload() {
        const input = document.getElementById('controlImageInput');
        const preview = document.getElementById('controlPreview');
        const dot = document.getElementById('controlnetDot');
        
        input?.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    currentControlImage = ev.target.result;
                    preview.innerHTML = `<img src="${currentControlImage}">`;
                    if (dot) dot.style.background = '#10b981';
                };
                reader.readAsDataURL(file);
            } else {
                currentControlImage = null;
                preview.innerHTML = '<span style="color: #cbd5e1; font-size: 10px;">No preview</span>';
                if (dot) dot.style.background = '#ef4444';
            }
        });
    }
    
    async function refreshGallery() {
        const galleryDiv = document.getElementById('images');
        try {
            const response = await fetch('/api/images');
            const images = await response.json();
            
            if (!images || images.length === 0) {
                galleryDiv.innerHTML = '<div style="text-align: center; grid-column: span 2; color: #9ca3af;">No images yet</div>';
                return;
            }
            
            galleryDiv.innerHTML = images.map(img => `
                <div class="gallery-item">
                    ${img.file_name ? 
                        `<img src="/outputs/${img.file_name}" class="gallery-img" onclick="window.open(this.src)">` :
                        `<div class="gallery-placeholder">🎨</div>`
                    }
                    <div class="gallery-prompt">${img.positive_prompt?.substring(0, 50) || 'No prompt'}</div>
                    <button class="delete-btn" onclick="deleteImage(${img.id})">🗑️</button>
                </div>
            `).join('');
        } catch (error) {
            console.error('Gallery error:', error);
        }
    }
    
    async function deleteImage(id) {
        if (!confirm('Устгахдаа итгэлтэй байна уу?')) return;
        
        try {
            const response = await fetch(`/gallery/delete/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                }
            });
            if (response.ok) {
                refreshGallery();
            }
        } catch (error) {
            console.error('Delete error:', error);
        }
    }
    
    async function pollForResults(promptId) {
        let attempts = 0;
        const maxAttempts = 30;
        
        const interval = setInterval(async () => {
            attempts++;
            console.log(`Polling attempt ${attempts}`);
            
            try {
                const response = await fetch('/api/comfyui/history');
                const history = await response.json();
                
                if (history[promptId]) {
                    clearInterval(interval);
                    
                    const outputs = history[promptId].outputs;
                    let fileName = null;
                    
                    for (const nodeId in outputs) {
                        if (outputs[nodeId].images && outputs[nodeId].images.length > 0) {
                            fileName = outputs[nodeId].images[0].filename;
                            break;
                        }
                    }
                    
                    if (fileName) {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                        await fetch('/api/images/complete', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                prompt_id: promptId,
                                file_name: fileName
                            })
                        });
                        refreshGallery();
                    }
                }
                
                if (attempts >= maxAttempts) {
                    clearInterval(interval);
                }
            } catch (error) {
                console.error('Polling error:', error);
            }
        }, 2000);
    }
    
    async function generate() {
        if (isGenerating) return;
        isGenerating = true;
        
        const btn = document.getElementById('generateBtn');
        const statusDiv = document.getElementById('status');
        
        btn.disabled = true;
        btn.textContent = '🎨 Processing...';
        statusDiv.innerHTML = '⏳ Generating...';
        statusDiv.className = 'status status-generating';
        
        const payload = {
            model: document.getElementById('model').value,
            positive_prompt: document.getElementById('positive_prompt').value,
            negative_prompt: document.getElementById('negative_prompt').value,
            steps: parseInt(document.getElementById('steps').value),
            cfg: parseFloat(document.getElementById('cfg').value),
            width: parseInt(document.getElementById('width').value),
            height: parseInt(document.getElementById('height').value),
            sampler: document.getElementById('sampler')?.value || 'euler',        // НЭМЭХ
            scheduler: document.getElementById('scheduler')?.value || 'normal',  // НЭМЭХ
            controlnet: currentControlImage ? {
                enabled: true,
                preprocessor: currentPreprocessor,
                image_base64: currentControlImage,
                strength: parseFloat(document.getElementById('cnStrength')?.value || 0.85)
            } : null
        };
        
        try {
            const response = await fetch('/api/generate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: JSON.stringify(payload)
            });
            
            const data = await response.json();
            
            if (data.success) {
                statusDiv.innerHTML = '✅ Generation started!';
                statusDiv.className = 'status status-success';
                pollForResults(data.prompt_id);
                setTimeout(refreshGallery, 5000);
            } else {
                throw new Error(data.error);
            }
        } catch (error) {
            statusDiv.innerHTML = `❌ Error: ${error.message}`;
            statusDiv.className = 'status status-error';
        }
        
        isGenerating = false;
        btn.disabled = false;
        btn.textContent = '🚀 Generate';
    }
    
    function setupSliders() {
        const strength = document.getElementById('cnStrength');
        if (strength) {
            strength.addEventListener('input', () => {
                document.getElementById('strengthVal').textContent = strength.value;
            });
        }
        
        const start = document.getElementById('cnStart');
        if (start) {
            start.addEventListener('input', () => {
                document.getElementById('startVal').textContent = parseFloat(start.value).toFixed(2);
            });
        }
        
        const end = document.getElementById('cnEnd');
        if (end) {
            end.addEventListener('input', () => {
                document.getElementById('endVal').textContent = parseFloat(end.value).toFixed(2);
            });
        }
    }
    
    document.addEventListener('DOMContentLoaded', () => {
        buildPreprocessors();
        setupImageUpload();
        setupSliders();
        refreshGallery();
        
        document.getElementById('generateBtn')?.addEventListener('click', generate);
        document.getElementById('refreshGalleryBtn')?.addEventListener('click', refreshGallery);
        
        setInterval(refreshGallery, 10000);
    });
</script>
@endpush