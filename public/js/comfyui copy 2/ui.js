// public/js/comfyui/ui.js - UIManager class for ComfyUI Studio
// Guard against multiple loads
if (typeof UIManager === 'undefined') {
    class UIManager {
    constructor(wsManager, gallery) {
        this.ws = wsManager;
        this.gallery = gallery;
        this.selectedPreprocessor = "canny";
        this.currentControlImageBase64 = null;
        this.isGenerating = false;
    }
    
    init() {
        this.buildPreprocessorUI();
        this.bindEvents();
        this.setupModelWarning();
        this.setupStrengthSlider();
        this.setupPreprocessorSettings();
        this.updateControlNetStatus();
        this.setupStartEndSliders(); // Add start/end sliders
    }
    
    buildPreprocessorUI() {
        const container = document.getElementById("preprocessorList");
        if (!container) return;
        
        container.innerHTML = "";
        Preprocessors.forEach(pre => {
            const btn = document.createElement("div");
            btn.className = `preprocessor-btn ${this.selectedPreprocessor === pre.id ? 'active' : ''}`;
            btn.innerHTML = `
                <div class="preprocessor-badge">${pre.icon}</div>
                <div style="flex:1">
                    <div class="preprocessor-title">${pre.label}</div>
                    <div class="preprocessor-desc">${pre.desc}</div>
                </div>
            `;
            btn.onclick = () => {
                document.querySelectorAll('.preprocessor-btn').forEach(el => el.classList.remove('active'));
                btn.classList.add('active');
                this.selectedPreprocessor = pre.id;
                Utils.updateStatus(`✅ Preprocessor set to: ${pre.label}`, "success");
                this.updateSettingsVisibility();
                this.updateControlNetStatus();
            };
            container.appendChild(btn);
        });
    }
    
    setupPreprocessorSettings() {
        // Add event listeners for sliders to show values
        const addSliderDisplay = (sliderId, displayId, isNormalized = false) => {
            const slider = document.getElementById(sliderId);
            const display = document.getElementById(displayId);
            if (slider && display) {
                const updateDisplay = () => {
                    if (isNormalized) {
                        // For normalized values (0-1), show as is
                        display.innerText = parseFloat(slider.value).toFixed(2);
                    } else {
                        // For 0-255 values, show both raw and normalized
                        const rawValue = parseInt(slider.value);
                        const normalizedValue = (rawValue / 255).toFixed(2);
                        display.innerText = `${rawValue} (${normalizedValue})`;
                    }
                };
                slider.addEventListener('input', updateDisplay);
                updateDisplay(); // Initial display
            }
        };
        
        // Canny sliders (0-255 range)
        addSliderDisplay('cannyLowThreshold', 'cannyLowVal', false);
        addSliderDisplay('cannyHighThreshold', 'cannyHighVal', false);
        
        // MLSD sliders (0-1 range)
        addSliderDisplay('mlsdScoreThr', 'mlsdScoreVal', true);
        addSliderDisplay('mlsdDistThr', 'mlsdDistVal', true);
        
        // Start/End percent sliders
        this.setupStartEndSliders();
        
        this.updateSettingsVisibility();
    }
    
    setupStartEndSliders() {
        const startSlider = document.getElementById('cnStart');
        const endSlider = document.getElementById('cnEnd');
        const startVal = document.getElementById('startVal');
        const endVal = document.getElementById('endVal');
        
        if (startSlider && startVal) {
            startSlider.addEventListener('input', () => {
                startVal.innerText = parseFloat(startSlider.value).toFixed(2);
            });
            startVal.innerText = startSlider.value;
        }
        
        if (endSlider && endVal) {
            endSlider.addEventListener('input', () => {
                endVal.innerText = parseFloat(endSlider.value).toFixed(2);
            });
            endVal.innerText = endSlider.value;
        }
    }
    
    updateSettingsVisibility() {
        // Hide all settings
        const allSettings = document.querySelectorAll('.preprocessor-settings');
        allSettings.forEach(el => {
            el.style.display = 'none';
        });
        
        // Show settings for current preprocessor
        const settingMap = {
            'canny': 'cannySettings',
            'depth': 'depthSettings',
            'openpose': 'openposeSettings',
            'scribble': 'scribbleSettings',
            'mlsd': 'mlsdSettings',
            'hed': 'hedSettings',
            'seg': 'segSettings',
            'normal': 'normalSettings'
        };
        
        const settingsId = settingMap[this.selectedPreprocessor];
        if (settingsId) {
            const activeSettings = document.getElementById(settingsId);
            if (activeSettings) {
                activeSettings.style.display = 'block';
            }
        }
    }
    
    updateControlNetStatus() {
        const dot = document.getElementById('controlnetDot');
        const text = document.getElementById('controlnetText');
        const detailsDiv = document.getElementById('controlnetDetails');
        
        if (!dot) return;
        
        const isActive = !!this.currentControlImageBase64;
        const strength = parseFloat(document.getElementById('cnStrength')?.value || 0.85);
        const startPercent = parseFloat(document.getElementById('cnStart')?.value || 0);
        const endPercent = parseFloat(document.getElementById('cnEnd')?.value || 1);
        const preprocessor = this.selectedPreprocessor;
        
        if (isActive) {
            dot.style.backgroundColor = '#10b981';
            text.innerHTML = '✓ ControlNet Active';
            if (detailsDiv) {
                detailsDiv.innerHTML = `
                    <strong>Preprocessor:</strong> ${preprocessor}<br>
                    <strong>Strength:</strong> ${strength}<br>
                    <strong>Range:</strong> ${startPercent*100}% - ${endPercent*100}%<br>
                    <strong>Status:</strong> Ready to generate
                `;
            }
        } else {
            dot.style.backgroundColor = '#ef4444';
            text.innerHTML = '✗ ControlNet Not Active';
            if (detailsDiv) {
                detailsDiv.innerHTML = 'Upload a control image to enable ControlNet';
            }
        }
    }
    
    bindEvents() {
        const generateBtn = document.getElementById("generateBtn");
        const refreshBtn = document.getElementById("refreshGalleryBtn");
        const controlImageInput = document.getElementById("controlImageInput");
        const checkModelsBtn = document.getElementById("checkModelsBtn");
        
        if (generateBtn) generateBtn.onclick = () => this.generate();
        
        if (refreshBtn) {
            refreshBtn.onclick = async () => {
                Utils.updateStatus("🔄 Loading all images...", "");
                await this.gallery.loadAllImages();
            };
        }
        
        if (controlImageInput) {
            controlImageInput.addEventListener("change", (e) => this.handleImageUpload(e));
        }
        
        if (checkModelsBtn) {
            checkModelsBtn.onclick = async () => {
                const statusDiv = document.getElementById("modelStatus");
                if (statusDiv) {
                    statusDiv.innerHTML = "Checking models...";
                }
                
                try {
                    const response = await fetch('/api/debug-models');
                    const data = await response.json();
                    
                    let html = '<strong>📁 Checkpoints:</strong><br>';
                    if (data.checkpoints && data.checkpoints.files && data.checkpoints.files.length > 0) {
                        html += '<ul style="margin-top: 5px;">';
                        data.checkpoints.files.forEach(file => {
                            const currentModel = document.getElementById('model')?.value;
                            const isSelected = file === currentModel;
                            html += `<li ${isSelected ? 'style="color: green; font-weight: bold;"' : ''}>${file} ${isSelected ? '✓ SELECTED' : ''}</li>`;
                        });
                        html += '</ul>';
                    } else {
                        html += '<p>❌ No checkpoints found! Check your ComfyUI models/checkpoints directory.</p>';
                    }
                    
                    html += '<strong>🎮 ControlNet Models:</strong><br>';
                    if (data.controlnet_models && data.controlnet_models.files && data.controlnet_models.files.length > 0) {
                        html += '<ul style="margin-top: 5px;">';
                        data.controlnet_models.files.forEach(file => {
                            html += `<li>${file}</li>`;
                        });
                        html += '</ul>';
                    } else {
                        html += '<p>❌ No ControlNet models found! Check your ComfyUI/models/controlnet directory.</p>';
                    }
                    
                    html += '<strong>🔧 Available Nodes:</strong><br>';
                    if (data.comfyui_nodes) {
                        html += '<ul style="margin-top: 5px;">';
                        if (data.comfyui_nodes.controlnet_nodes && data.comfyui_nodes.controlnet_nodes.length > 0) {
                            html += `<li>ControlNet Nodes: ${data.comfyui_nodes.controlnet_nodes.join(', ')}</li>`;
                        } else {
                            html += '<li>❌ No ControlNet nodes found! Make sure ControlNet extension is installed.</li>';
                        }
                        if (data.comfyui_nodes.preprocessor_nodes && data.comfyui_nodes.preprocessor_nodes.length > 0) {
                            html += `<li>Preprocessor Nodes: ${data.comfyui_nodes.preprocessor_nodes.slice(0, 5).join(', ')}${data.comfyui_nodes.preprocessor_nodes.length > 5 ? '...' : ''}</li>`;
                        }
                        html += '</ul>';
                    }
                    
                    if (statusDiv) statusDiv.innerHTML = html;
                    Utils.updateStatus("✅ Model check completed", "success");
                } catch (err) {
                    console.error("Debug error:", err);
                    if (statusDiv) statusDiv.innerHTML = `<span style="color: red;">❌ Error: ${err.message}</span>`;
                    Utils.updateStatus(`❌ Failed to check models: ${err.message}`, "error");
                }
            };
        }
        
        // Add strength slider listener for status update
        const strengthSlider = document.getElementById("cnStrength");
        if (strengthSlider) {
            strengthSlider.addEventListener('input', () => {
                const span = document.getElementById("strengthVal");
                if (span) span.innerText = strengthSlider.value;
                this.updateControlNetStatus();
            });
        }
        
        // Add start/end slider listeners
        const startSlider = document.getElementById("cnStart");
        const endSlider = document.getElementById("cnEnd");
        if (startSlider) {
            startSlider.addEventListener('input', () => this.updateControlNetStatus());
        }
        if (endSlider) {
            endSlider.addEventListener('input', () => this.updateControlNetStatus());
        }
        
        // Add resolution listeners for validation
        const widthInput = document.getElementById("width");
        const heightInput = document.getElementById("height");
        if (widthInput && heightInput) {
            const validateResolution = () => {
                let width = parseInt(widthInput.value);
                let height = parseInt(heightInput.value);
                
                // Ensure width and height are multiples of 64
                if (width % 64 !== 0) {
                    width = Math.round(width / 64) * 64;
                    widthInput.value = width;
                }
                if (height % 64 !== 0) {
                    height = Math.round(height / 64) * 64;
                    heightInput.value = height;
                }
            };
            widthInput.addEventListener('change', validateResolution);
            heightInput.addEventListener('change', validateResolution);
        }
    }
    
    async handleImageUpload(event) {
        const file = event.target.files[0];
        if (!file) return;
        
        if (file.size > Config.MAX_IMAGE_SIZE) {
            Utils.updateStatus("❌ Image too large! Maximum 10MB", "error");
            return;
        }
        
        Utils.updateStatus("📤 Processing image...", "");
        
        const reader = new FileReader();
        reader.onload = async (ev) => {
            const imgData = ev.target.result;
            const base64 = imgData.split(',')[1];
            this.currentControlImageBase64 = await Utils.compressImage(base64);
            
            const previewDiv = document.getElementById("controlPreview");
            if (previewDiv) {
                previewDiv.innerHTML = `<img src="${ev.target.result}" style="max-width:100%; max-height:160px; border-radius:16px; object-fit:contain;">`;
            }
            
            this.updateControlNetStatus();
            Utils.updateStatus("✅ Control image loaded - ControlNet is now ACTIVE!", "success");
            
            // Show ControlNet settings card if hidden
            const controlNetCard = document.querySelector('.card:has(.card-header:contains("ControlNet Advanced Settings"))');
            if (controlNetCard) {
                controlNetCard.style.display = 'block';
            }
        };
        reader.readAsDataURL(file);
    }
    
    setupModelWarning() {
        const modelSelect = document.getElementById("model");
        const vramWarning = document.getElementById("vram-warning");
        
        if (modelSelect && vramWarning) {
            modelSelect.addEventListener("change", () => {
                const selectedModel = modelSelect.value;
                const heavyModels = ['sd_xl_base_1.0.safetensors', 'sd_xl_refiner_1.0.safetensors', 'flux1-dev-fp8.safetensors', 'sd3.5_large_fp8_scaled.safetensors', 'qwen_image_2512_fp8_e4m3fn.safetensors'];
                const sd15Models = ['dreamshaper_8.safetensors', 'v1-5-pruned-emaonly-fp16.safetensors', 'v1-5-pruned.safetensors', 'realisticVisionV60B1_v51HyperVAE.safetensors'];
                
                if (heavyModels.includes(selectedModel) && this.currentControlImageBase64) {
                    vramWarning.style.display = "block";
                    vramWarning.innerHTML = "⚠️ Warning: This model is very heavy for 8GB VRAM. Using ControlNet may cause out-of-memory errors. Consider switching to an SD1.5 model.";
                } else if (heavyModels.includes(selectedModel)) {
                    vramWarning.style.display = "block";
                    vramWarning.innerHTML = "⚠️ Warning: This model may exceed 8GB VRAM. If you encounter issues, reduce resolution or use an SD1.5 model.";
                } else {
                    vramWarning.style.display = "none";
                }
            });
        }
    }
    
    setupStrengthSlider() {
        const slider = document.getElementById("cnStrength");
        const span = document.getElementById("strengthVal");
        if (slider && span) {
            slider.addEventListener("input", () => {
                span.innerText = slider.value;
                this.updateControlNetStatus();
            });
        }
    }
    
    async generate() {
        if (this.isGenerating) {
            Utils.updateStatus("⚠️ Generation already in progress, please wait...", "error");
            return;
        }
        
        if (!this.ws.isConnected()) {
            Utils.updateStatus("WebSocket not connected, connecting...", "error");
            this.ws.connect();
            // Wait for connection
            await new Promise(resolve => setTimeout(resolve, 2000));
            if (!this.ws.isConnected()) {
                Utils.updateStatus("❌ Failed to connect to ComfyUI", "error");
                return;
            }
        }
        
        this.isGenerating = true;
        const btn = document.getElementById("generateBtn");
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = "🎨 Generating...";
        }
        
        try {
            let model = document.getElementById('model').value;
            const positive = document.getElementById('positive_prompt').value;
            const negative = document.getElementById('negative_prompt').value;
            const steps = parseInt(document.getElementById('steps').value);
            const cfg = parseFloat(document.getElementById('cfg').value);
            let width = parseInt(document.getElementById('width').value);
            let height = parseInt(document.getElementById('height').value);
            const sampler = document.getElementById('sampler').value;
            const cnStrengthVal = parseFloat(document.getElementById('cnStrength').value);
            const cnStart = parseFloat(document.getElementById('cnStart').value);
            const cnEnd = parseFloat(document.getElementById('cnEnd').value);
            
            // Validate prompts
            if (!positive.trim()) {
                throw new Error("Positive prompt cannot be empty");
            }
            
            // Update status to show ControlNet state
            if (this.currentControlImageBase64) {
                Utils.updateStatus(`🎮 ControlNet ACTIVE | Preprocessor: ${this.selectedPreprocessor} | Strength: ${cnStrengthVal} | Range: ${cnStart*100}%-${cnEnd*100}%`, "success");
            } else {
                Utils.updateStatus("🚀 Generating without ControlNet...", "");
            }
            
            // Collect preprocessor-specific settings
            const preprocessorSettings = {
                canny_low_threshold: parseInt(document.getElementById('cannyLowThreshold')?.value || 50),
                canny_high_threshold: parseInt(document.getElementById('cannyHighThreshold')?.value || 150),

                depth_resolution: parseInt(document.getElementById('depthResolution')?.value || 512),
                openpose_hands: document.getElementById('openposeHands')?.value || 'enable',
                openpose_body: document.getElementById('openposeBody')?.value || 'enable',
                openpose_face: document.getElementById('openposeFace')?.value || 'disable',
                mlsd_score_threshold: parseFloat(document.getElementById('mlsdScoreThr')?.value || 0.1),
                mlsd_distance_threshold: parseFloat(document.getElementById('mlsdDistThr')?.value || 0.1),
                mlsd_resolution: parseInt(document.getElementById('mlsdResolution')?.value || 512),
                hed_resolution: parseInt(document.getElementById('hedResolution')?.value || 512),
                scribble_mode: document.getElementById('scribbleMode')?.value || 'edge',
                seg_resolution: parseInt(document.getElementById('segResolution')?.value || 512),
                normal_resolution: parseInt(document.getElementById('normalResolution')?.value || 512)
            };
            
            // Auto-switch to SD1.5 if using ControlNet
            const sd15Models = ['dreamshaper_8.safetensors', 'v1-5-pruned-emaonly-fp16.safetensors', 'v1-5-pruned.safetensors', 'realisticVisionV60B1_v51HyperVAE.safetensors'];
            if (this.currentControlImageBase64 && !sd15Models.includes(model)) {
                const originalModel = model;
                model = "dreamshaper_8.safetensors";
                document.getElementById('model').value = model;
                Utils.updateStatus(`⚠️ ControlNet requires SD1.5 model. Switching from ${originalModel} to ${model}.`, "error");
            }
            
            // Reduce resolution if using heavy models
            const heavyModels = ['sd_xl_base_1.0.safetensors', 'sd_xl_refiner_1.0.safetensors', 'flux1-dev-fp8.safetensors'];
            if (heavyModels.includes(model) && (width > 768 || height > 768)) {
                const oldWidth = width, oldHeight = height;
                width = Math.min(width, 768);
                height = Math.min(height, 768);
                Utils.updateStatus(`⚠️ To avoid VRAM overflow, resolution reduced from ${oldWidth}x${oldHeight} to ${width}x${height}.`, "error");
                document.getElementById('width').value = width;
                document.getElementById('height').value = height;
            }
            
            const payload = {
                client_id: this.ws.getClientId(),
        model: model,
        positive_prompt: positive,
        negative_prompt: negative,
        steps: steps,
        cfg: cfg,
        width: width,
        height: height,
        sampler: sampler,
        controlnet: {
            enabled: !!this.currentControlImageBase64,
            preprocessor: this.selectedPreprocessor,
            image_base64: this.currentControlImageBase64 || null,
            strength: cnStrengthVal,
            start_percent: cnStart,
            end_percent: cnEnd,
            // Include all preprocessor settings
            ...preprocessorSettings
        }
    };
            
            console.log("Sending payload:", payload);
            console.log('Raw Canny thresholds (integers):', {
        low: preprocessorSettings.canny_low_threshold,
        high: preprocessorSettings.canny_high_threshold
    });
            const result = await API.generate(payload);
            
            if (result.success) {
                Utils.updateStatus("✅ Prompt sent, waiting for image...", "success");
                // Start polling for images
                this.gallery.startPolling(result.prompt_id);
            } else {
                throw new Error(result.error || "Unknown error");
            }
        } catch (err) {
            console.error("Generation error:", err);
            Utils.updateStatus(`❌ Generation failed: ${err.message}`, "error");
        } finally {
            this.isGenerating = false;
            const btn = document.getElementById("generateBtn");
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = "🚀 Generate with ControlNet";
            }
        }
    }
    }
    // Assign to window if not already assigned
    window.UIManager = UIManager;
}

// Export for CommonJS if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { UIManager };
}