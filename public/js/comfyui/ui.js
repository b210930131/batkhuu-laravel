// public/js/comfyui/ui.js - UIManager class for ComfyUI Studio
if (typeof UIManager === 'undefined') {
    class UIManager {
        constructor(wsManager, gallery) {
            this.ws = wsManager;
            this.gallery = gallery;
            this.selectedPreprocessor = "canny";
            this.currentControlImageBase64 = null;
            this.isGenerating = false;
        }

        async init() {
            // Check if essential Utils exist
            if (typeof Utils === 'undefined') {
                console.error("Utils not found. Make sure utils.js is loaded before ui.js");
                return;
            }

            this.bindEvents();
            this.updateControlNetStatus();
            
            // Load initial data through the Proxy
            await this.loadModels(); 
        }

        async loadModels() {
            try {
                Utils.updateStatus("🔄 Fetching models via Proxy...", "");
                
                const response = await fetch('/api/comfyui/object_info');
                const data = await response.json();
                
                // Note: Adjusted to match the Laravel Controller output provided earlier
                if (data && data.checkpoints) {
                    this.populateDropdown("model", data.checkpoints, "dreamshaper_8.safetensors");
                    console.log(`✅ Checkpoints loaded: ${data.checkpoints.length}`);
                }

                if (data && data.controlnets) {
                    this.populateDropdown("controlnet_model", data.controlnets);
                    console.log(`✅ ControlNets loaded: ${data.controlnets.length}`);
                }
                
                Utils.updateStatus("✅ Models synchronized", "success");
            } catch (err) {
                console.error("Failed to load models:", err);
                Utils.updateStatus("❌ Could not connect to ComfyUI backend", "error");
            }
        }

        populateDropdown(elementId, items, defaultValue) {
            const select = document.getElementById(elementId);
            if (!select) {
                console.warn(`Dropdown element #${elementId} not found in DOM`);
                return;
            }

            select.innerHTML = items.map(name => 
                `<option value="${name}" ${name === defaultValue ? 'selected' : ''}>${name}</option>`
            ).join('');
        }

        bindEvents() {
            const generateBtn = document.getElementById("generateBtn");
            const refreshBtn = document.getElementById("refreshGalleryBtn");
            const controlImageInput = document.getElementById("controlImageInput");
            const checkModelsBtn = document.getElementById("checkModelsBtn");
            
            if (generateBtn) generateBtn.onclick = () => this.generate();
            
            if (refreshBtn) {
                refreshBtn.onclick = async () => {
                    Utils.updateStatus("🔄 Refreshing gallery...", "");
                    await this.gallery.loadAllImages();
                };
            }
            
            if (controlImageInput) {
                controlImageInput.addEventListener("change", (e) => this.handleImageUpload(e));
            }
            
            if (checkModelsBtn) {
                checkModelsBtn.onclick = () => this.loadModels();
            }
            
            // Slider Listeners
            ['cnStrength', 'cnStart', 'cnEnd'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.addEventListener('input', () => {
                    const valId = id === 'cnStrength' ? 'strengthVal' : (id === 'cnStart' ? 'startVal' : 'endVal');
                    const valSpan = document.getElementById(valId);
                    if (valSpan) valSpan.innerText = el.value;
                    this.updateControlNetStatus();
                });
            });
        }

        updateControlNetStatus() {
            // Logic to update UI badges or text based on current slider values
            const statusIcon = document.getElementById('cnStatusIcon');
            if (statusIcon) {
                statusIcon.style.color = this.currentControlImageBase64 ? '#10b981' : '#6b7280';
            }
        }

        async generate() {
    if (this.isGenerating) return;
    
    this.isGenerating = true;
    const btn = document.getElementById("generateBtn");
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = "🎨 Processing...";
    }
    
    try {
        const payload = {
            client_id: 'laravel-client-' + Date.now(),
            model: document.getElementById('model')?.value,
            positive_prompt: document.getElementById('positive_prompt')?.value,
            negative_prompt: document.getElementById('negative_prompt')?.value,
            steps: parseInt(document.getElementById('steps')?.value || 20),
            cfg: parseFloat(document.getElementById('cfg')?.value || 7.0),
            width: parseInt(document.getElementById('width')?.value || 512),
            height: parseInt(document.getElementById('height')?.value || 512),
            sampler: document.getElementById('sampler')?.value,
            controlnet: this.currentControlImageBase64 ? {
                enabled: true,
                preprocessor: this.selectedPreprocessor,
                image_base64: this.currentControlImageBase64,
                strength: parseFloat(document.getElementById('cnStrength')?.value || 0.85),
                start_percent: parseFloat(document.getElementById('cnStart')?.value || 0),
                end_percent: parseFloat(document.getElementById('cnEnd')?.value || 1),
                ...this.getPreprocessorParams()
            } : null
        };

        const response = await fetch('/api/generate', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: JSON.stringify(payload)
        });
        
        const result = await response.json();
        
        if (result.success) {
            Utils.updateStatus("🚀 Generation started!", "success");
            if (this.gallery && this.gallery.startPolling) {
                this.gallery.startPolling(result.prompt_id);
            }
        } else {
            throw new Error(result.error || "Generation failed");
        }
    } catch (err) {
        Utils.updateStatus(`❌ Error: ${err.message}`, "error");
        console.error('Generation error:', err);
    } finally {
        this.isGenerating = false;
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = "🚀 Generate Image";
        }
    }
}
        handleImageUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                this.currentControlImageBase64 = e.target.result.split(',')[1];
                Utils.updateStatus("📸 Control image uploaded", "success");
                this.updateControlNetStatus();
            };
            reader.readAsDataURL(file);
        }
    setPreprocessor(preprocessorId) {
    this.selectedPreprocessor = preprocessorId;
    console.log('Preprocessor set to:', preprocessorId);
}

getPreprocessorParams() {
    const params = {};
    
    switch(this.selectedPreprocessor) {
        case 'canny':
            params.canny_low = parseInt(document.getElementById('cannyLowThreshold')?.value || 50);
            params.canny_high = parseInt(document.getElementById('cannyHighThreshold')?.value || 150);
            break;
        case 'depth':
            params.depth_resolution = parseInt(document.getElementById('depthResolution')?.value || 512);
            break;
        case 'openpose':
            params.openpose_hands = document.getElementById('openposeHands')?.checked ? 'enable' : 'disable';
            params.openpose_body = document.getElementById('openposeBody')?.checked ? 'enable' : 'disable';
            params.openpose_face = document.getElementById('openposeFace')?.checked ? 'enable' : 'disable';
            break;
        case 'mlsd':
            params.mlsd_score_thr = parseFloat(document.getElementById('mlsdScoreThr')?.value || 0.1);
            params.mlsd_dist_thr = parseFloat(document.getElementById('mlsdDistThr')?.value || 0.1);
            params.mlsd_resolution = parseInt(document.getElementById('mlsdResolution')?.value || 512);
            break;
        case 'scribble':
            params.scribble_mode = document.getElementById('scribbleMode')?.value || 'hed';
            break;
        case 'hed':
            params.hed_resolution = parseInt(document.getElementById('hedResolution')?.value || 512);
            break;
        case 'seg':
            params.seg_resolution = parseInt(document.getElementById('segResolution')?.value || 512);
            break;
        case 'normal':
            params.normal_resolution = parseInt(document.getElementById('normalResolution')?.value || 512);
            break;
    }
    
    return params;
}
handleImageUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (e) => {
        // Store as data URL with MIME type
        this.currentControlImageBase64 = e.target.result;
        Utils.updateStatus("📸 Control image uploaded", "success");
        this.updateControlNetStatus();
    };
    reader.readAsDataURL(file);
}

updateControlNetStatus() {
    const dot = document.getElementById('controlnetDot');
    const text = document.getElementById('controlnetText');
    const details = document.getElementById('controlnetDetails');
    
    const preprocessorNames = {
        'canny': 'Canny Edge', 'depth': 'Depth Map', 'openpose': 'OpenPose',
        'mlsd': 'MLSD', 'scribble': 'Scribble', 'hed': 'HED',
        'seg': 'Segmentation', 'normal': 'Normal Map'
    };
    
    if (this.currentControlImageBase64) {
        dot.style.backgroundColor = '#10b981';
        text.textContent = `✅ ControlNet Active • ${preprocessorNames[this.selectedPreprocessor] || this.selectedPreprocessor}`;
        details.innerHTML = `Using ${preprocessorNames[this.selectedPreprocessor] || this.selectedPreprocessor} to guide generation.`;
    } else {
        dot.style.backgroundColor = '#ef4444';
        text.textContent = '⏸️ ControlNet Inactive';
        details.innerHTML = 'Upload an image to enable ControlNet.';
    }
}

    }

    // Attach to window so other scripts can see it
    window.UIManager = UIManager;
}