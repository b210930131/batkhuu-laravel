<!-- resources/views/partials/generation-form.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ComfyUI ControlNet Studio</title>
    
    <style>
        /* Additional specific styles if needed */
        .preprocessor-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 12px;
            padding: 15px;
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
        
        /* Preprocessor Button Styling */
        .preprocessor-btn {
            background: white;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
            cursor: pointer;
            padding: 12px;
            border-radius: 10px;
            text-align: center;
            font-size: 14px;
            font-weight: 500;
        }
        
        .preprocessor-btn:hover {
            border-color: #667eea;
            background: #f0f4ff;
            transform: translateY(-2px);
        }
        
        /* Active button style */
        .preprocessor-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        /* Settings panels */
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
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Control Image Preview */
        #controlPreview img {
            max-width: 100%;
            height: auto;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            margin-top: 10px;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .card-header {
            padding: 15px 20px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
            font-size: 16px;
        }
        
        .control-group {
            padding: 15px 20px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .control-group label {
            font-weight: 600;
            margin-bottom: 8px;
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
        
        select, input[type="text"], input[type="number"], textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            flex: 1;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            padding: 20px;
            max-width: 1600px;
            margin: 0 auto;
        }
        
        @media (max-width: 1024px) {
            .container {
                grid-template-columns: 1fr;
            }
        }
        
        .vram-warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 8px 12px;
            border-radius: 6px;
            margin-top: 8px;
            font-size: 12px;
        }
        
        .image-preview {
            margin-top: 10px;
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
            <div class="card">
                <div class="card-header">🎨 ControlNet Preprocessors</div>
                <div id="preprocessorList" class="preprocessor-grid">
                    <!-- Preprocessors will be added dynamically via JavaScript -->
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
    document.addEventListener('DOMContentLoaded', function() {
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
        let currentPreprocessor = 'canny';
        
        // Create preprocessor buttons
        preprocessors.forEach(pp => {
            const btn = document.createElement('button');
            btn.className = 'preprocessor-btn' + (pp.id === currentPreprocessor ? ' active' : '');
            btn.setAttribute('data-preprocessor', pp.id);
            btn.innerHTML = `${pp.icon} ${pp.name}`;
            btn.onclick = () => switchPreprocessor(pp.id);
            preprocessorList.appendChild(btn);
        });
        
        // Function to switch preprocessor
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
            } else {
                console.log(`No settings panel found for ${preprocessorId}`);
            }
            
            // Update status
            updateControlNetStatus();
        }
        
        // ========== Canny Slider Updates ==========
        const lowSlider = document.getElementById('cannyLowThreshold');
        const highSlider = document.getElementById('cannyHighThreshold');
        const lowVal = document.getElementById('cannyLowVal');
        const highVal = document.getElementById('cannyHighVal');
        
        if (lowSlider) {
            lowSlider.addEventListener('input', function() {
                lowVal.textContent = this.value;
                console.log(`Canny Low Threshold: ${this.value}`);
            });
        }
        
        if (highSlider) {
            highSlider.addEventListener('input', function() {
                highVal.textContent = this.value;
                console.log(`Canny High Threshold: ${this.value}`);
            });
        }
        
        // ========== MLSD Slider Updates ==========
        const mlsdScore = document.getElementById('mlsdScoreThr');
        const mlsdDist = document.getElementById('mlsdDistThr');
        const mlsdScoreVal = document.getElementById('mlsdScoreVal');
        const mlsdDistVal = document.getElementById('mlsdDistVal');
        
        if (mlsdScore) {
            mlsdScore.addEventListener('input', function() {
                mlsdScoreVal.textContent = this.value;
            });
        }
        
        if (mlsdDist) {
            mlsdDist.addEventListener('input', function() {
                mlsdDistVal.textContent = this.value;
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
                        controlPreview.innerHTML = `<img src="${e.target.result}" alt="Control Image" style="max-width: 100%; border-radius: 8px;">`;
                        updateControlNetStatus(true);
                    };
                    reader.readAsDataURL(file);
                } else {
                    controlPreview.innerHTML = '';
                    updateControlNetStatus(false);
                }
            });
        }
        
        // ========== ControlNet Status Update ==========
        function updateControlNetStatus(hasImage = null) {
            const hasControlImage = controlImageInput && controlImageInput.files && controlImageInput.files.length > 0;
            const dot = document.getElementById('controlnetDot');
            const text = document.getElementById('controlnetText');
            const details = document.getElementById('controlnetDetails');
            
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
        
        // ========== Get Current Preprocessor Settings ==========
        window.getPreprocessorSettings = function() {
            const settings = {
                preprocessor: currentPreprocessor,
                params: {}
            };
            
            switch(currentPreprocessor) {
                case 'canny':
                    settings.params = {
                        low_threshold: parseInt(lowSlider?.value || 50),
                        high_threshold: parseInt(highSlider?.value || 150)
                    };
                    break;
                case 'depth':
                    settings.params = {
                        resolution: document.getElementById('depthResolution')?.value || 512
                    };
                    break;
                case 'openpose':
                    settings.params = {
                        hands: document.getElementById('openposeHands')?.checked || false,
                        body: document.getElementById('openposeBody')?.checked || true,
                        face: document.getElementById('openposeFace')?.checked || false
                    };
                    break;
                case 'mlsd':
                    settings.params = {
                        score_threshold: parseFloat(mlsdScore?.value || 0.1),
                        dist_threshold: parseFloat(mlsdDist?.value || 0.1),
                        resolution: document.getElementById('mlsdResolution')?.value || 512
                    };
                    break;
                case 'scribble':
                    settings.params = {
                        mode: document.getElementById('scribbleMode')?.value || 'hed'
                    };
                    break;
                case 'hed':
                    settings.params = {
                        resolution: document.getElementById('hedResolution')?.value || 512
                    };
                    break;
                case 'seg':
                    settings.params = {
                        resolution: document.getElementById('segResolution')?.value || 512
                    };
                    break;
                case 'normal':
                    settings.params = {
                        resolution: document.getElementById('normalResolution')?.value || 512
                    };
                    break;
            }
            
            return settings;
        };
        
        // ========== Generate Button Handler ==========
        const generateBtn = document.getElementById('generateBtn');
        if (generateBtn) {
            generateBtn.onclick = async () => {
                const settings = window.getPreprocessorSettings();
                console.log('Generating with settings:', settings);
                
                const statusDiv = document.getElementById('status');
                statusDiv.innerHTML = '🎨 Generating image... Please wait.';
                statusDiv.style.background = '#fef3c7';
                statusDiv.style.color = '#92400e';
                
                // Here you would call your actual generation API
                // Example:
                // const response = await fetch('/api/generate', {
                //     method: 'POST',
                //     headers: {
                //         'Content-Type': 'application/json',
                //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                //     },
                //     body: JSON.stringify({
                //         model: document.getElementById('model').value,
                //         prompt: document.getElementById('positive_prompt').value,
                //         negative_prompt: document.getElementById('negative_prompt').value,
                //         steps: document.getElementById('steps').value,
                //         cfg: document.getElementById('cfg').value,
                //         width: document.getElementById('width').value,
                //         height: document.getElementById('height').value,
                //         sampler: document.getElementById('sampler').value,
                //         preprocessor: settings
                //     })
                // });
                
                // Simulate generation
                setTimeout(() => {
                    statusDiv.innerHTML = '✅ Generation complete! Check the gallery.';
                    statusDiv.style.background = '#f0fdf4';
                    statusDiv.style.color = '#166534';
                }, 2000);
            };
        }
        
        // Initialize - show default settings (canny)
        switchPreprocessor('canny');
        
        console.log('Preprocessor system initialized with', preprocessors.length, 'preprocessors');
    });
    </script>
</body>
</html>