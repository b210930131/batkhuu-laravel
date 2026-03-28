<script>
// ---------- UI State ----------
let selectedPreprocessor = "canny";
let currentControlImageBase64 = null;
let clientId = Math.random().toString(36).substring(7);
let ws = null;
let isGenerating = false;
let currentPromptId = null;
const comfyuiPort = 8188;
let comfyHost = window.location.hostname || "127.0.0.1";
let reconnectAttempts = 0;
const maxReconnectAttempts = 5;
let imagePollingInterval = null;

// Preprocessors definition with full configurations
const preprocessors = [
    { id: "canny", label: "Canny", icon: "🔲", desc: "Sharp edges & outlines, architectural precision", settings: ["low_threshold", "high_threshold"] },
    { id: "depth", label: "Depth", icon: "🗺️", desc: "3D heatmap, spatial structure", settings: ["resolution"] },
    { id: "openpose", label: "OpenPose", icon: "🧍", desc: "Human skeleton pose replication", settings: ["hands", "body", "face"] },
    { id: "scribble", label: "Scribble", icon: "✏️", desc: "Turn rough doodles into art", settings: ["mode"] },
    { id: "mlsd", label: "MLSD", icon: "📐", desc: "Straight line detection (interior/architecture)", settings: ["score_threshold", "distance_threshold"] },
    { id: "hed", label: "HED (SoftEdge)", icon: "🎨", desc: "Organic edges, painterly style", settings: ["resolution"] },
    { id: "seg", label: "Segmentation", icon: "🏞️", desc: "Scene composition: sky, road, objects", settings: ["resolution"] },
    { id: "normal", label: "Normal Map", icon: "⚡", desc: "Surface geometry, detailed 3D texture", settings: ["resolution"] }
];

// Model lists
const heavyModels = ["sd_xl_base_1.0.safetensors", "sd_xl_refiner_1.0.safetensors", "flux1-dev-fp8.safetensors", "sd3.5_large_fp8_scaled.safetensors", "qwen_image_2512_fp8_e4m3fn.safetensors"];
const sd15Models = ["dreamshaper_8.safetensors", "v1-5-pruned-emaonly-fp16.safetensors", "v1-5-pruned.safetensors", "realisticVisionV60B1_v51HyperVAE.safetensors", "interior_design_sd15.safetensors", "architecture_interior_v1.safetensors"];

// ---------- UI Building Functions ----------
function buildPreprocessorUI() {
    const container = document.getElementById("preprocessorList");
    if (!container) return;
    container.innerHTML = "";
    
    preprocessors.forEach(pre => {
        const btn = document.createElement("div");
        btn.className = `preprocessor-btn ${selectedPreprocessor === pre.id ? 'active' : ''}`;
        btn.setAttribute('data-id', pre.id);
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
            selectedPreprocessor = pre.id;
            console.log("[ControlNet] preprocessor set to:", selectedPreprocessor);
            updateStatus(`✅ Preprocessor set to: ${pre.label}`, "success");
            
            // Show advanced settings if available
            showPreprocessorSettings(pre);
        };
        container.appendChild(btn);
    });
}

function showPreprocessorSettings(preprocessor) {
    const settingsPanel = document.getElementById("preprocessorSettings");
    if (!settingsPanel || !preprocessor.settings) return;
    
    let html = '<div style="margin-top: 10px; padding: 10px; background: #f5f5f5; border-radius: 8px;"><h4>⚙️ Advanced Settings</h4>';
    
    preprocessor.settings.forEach(setting => {
        switch(setting) {
            case 'low_threshold':
                html += `
                    <div style="margin: 8px 0;">
                        <label>Low Threshold: <span id="lowThresholdVal">50</span></label>
                        <input type="range" id="lowThreshold" min="0" max="255" value="50" oninput="document.getElementById('lowThresholdVal').innerText = this.value">
                    </div>
                `;
                break;
            case 'high_threshold':
                html += `
                    <div style="margin: 8px 0;">
                        <label>High Threshold: <span id="highThresholdVal">150</span></label>
                        <input type="range" id="highThreshold" min="0" max="255" value="150" oninput="document.getElementById('highThresholdVal').innerText = this.value">
                    </div>
                `;
                break;
            case 'resolution':
                html += `
                    <div style="margin: 8px 0;">
                        <label>Resolution: <span id="resolutionVal">512</span></label>
                        <input type="range" id="resolution" min="256" max="1024" step="64" value="512" oninput="document.getElementById('resolutionVal').innerText = this.value">
                    </div>
                `;
                break;
        }
    });
    
    html += '</div>';
    settingsPanel.innerHTML = html;
}

// ---------- Image Handling ----------
async function compressImage(base64, maxWidth = 1024, quality = 0.8) {
    return new Promise((resolve) => {
        const img = new Image();
        img.onload = () => {
            const canvas = document.createElement('canvas');
            let width = img.width;
            let height = img.height;
            
            if (width > maxWidth) {
                height = (height * maxWidth) / width;
                width = maxWidth;
            }
            
            canvas.width = width;
            canvas.height = height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, width, height);
            const compressed = canvas.toDataURL('image/jpeg', quality);
            resolve(compressed.split(',')[1]); // return just the base64 data
        };
        img.src = 'data:image/jpeg;base64,' + base64;
    });
}

async function handleImageUpload(file) {
    if (!file) return;
    
    if (file.size > 10 * 1024 * 1024) {
        updateStatus("❌ Image too large! Maximum 10MB", "error");
        return;
    }
    
    const reader = new FileReader();
    reader.onload = async (ev) => {
        const imgData = ev.target.result;
        let base64Data = imgData.split(',')[1];
        
        // Compress if needed
        if (base64Data.length > 5 * 1024 * 1024) { // >5MB
            updateStatus("🔄 Compressing large image...", "info");
            base64Data = await compressImage(base64Data);
            updateStatus("✅ Image compressed", "success");
        }
        
        currentControlImageBase64 = base64Data;
        const previewDiv = document.getElementById("controlPreview");
        if (previewDiv) {
            previewDiv.innerHTML = `<img src="${ev.target.result}" alt="control preview" style="max-width:100%; max-height:160px; border-radius:16px;">`;
        }
        updateStatus("✅ Control image loaded", "success");
        
        // Re-check VRAM warning
        const modelSelect = document.getElementById("model");
        if (modelSelect) modelSelect.dispatchEvent(new Event('change'));
    };
    reader.onerror = () => {
        updateStatus("❌ Failed to read image", "error");
    };
    reader.readAsDataURL(file);
}

// ---------- WebSocket Management ----------
function connectWebSocket() {
    const wsUrl = `ws://${comfyHost}:${comfyuiPort}/ws?clientId=${clientId}`;
    console.log("Connecting to WebSocket:", wsUrl);
    
    if (ws && ws.readyState !== WebSocket.CLOSED) {
        ws.close();
    }
    
    ws = new WebSocket(wsUrl);
    
    ws.onopen = () => { 
        console.log("✅ WS Connected"); 
        updateStatus("WebSocket ready", "success");
        reconnectAttempts = 0;
    };
    
    ws.onmessage = (event) => {
        try {
            const msg = JSON.parse(event.data);
            console.log("WebSocket message:", msg.type, msg);
            
            switch(msg.type) {
                case 'executing':
                    if (msg.data && msg.data.node) {
                        updateStatus(`🎨 Generating... (${msg.data.node})`, "info");
                    } else if (msg.data && msg.data.node === null) {
                        updateStatus("✅ Generation finished!", "success");
                        isGenerating = false;
                        const btn = document.getElementById("generateBtn");
                        if (btn) btn.disabled = false;
                        
                        setTimeout(() => {
                            fetchRecentImages();
                        }, 1000);
                    }
                    break;
                    
                case 'progress':
                    const percent = Math.round((msg.data.value / msg.data.max) * 100);
                    updateStatus(`🎨 Generating... ${percent}%`, "info");
                    break;
                    
                case 'executed':
                    if (msg.data && msg.data.output && msg.data.output.images) {
                        msg.data.output.images.forEach(img => {
                            addImageToGallery(img.filename, img.subfolder, img.type);
                        });
                    }
                    break;
                    
                case 'execution_success':
                    if (msg.data && msg.data.images) {
                        msg.data.images.forEach(img => {
                            addImageToGallery(img.filename);
                        });
                    }
                    break;
                    
                case 'execution_error':
                    updateStatus(`❌ Generation error: ${msg.data.error}`, "error");
                    isGenerating = false;
                    const btn = document.getElementById("generateBtn");
                    if (btn) btn.disabled = false;
                    break;
            }
        } catch(e) { 
            console.error("Error parsing WebSocket message:", e);
        }
    };
    
    ws.onerror = (error) => { 
        console.error("WebSocket error:", error);
        updateStatus("WebSocket error - check ComfyUI connection", "error");
    };
    
    ws.onclose = (event) => { 
        console.log("WebSocket closed:", event.code, event.reason);
        
        if (reconnectAttempts < maxReconnectAttempts && !isGenerating) {
            reconnectAttempts++;
            const delay = Math.min(1000 * Math.pow(2, reconnectAttempts), 30000);
            updateStatus(`WS disconnected, reconnecting in ${delay/1000}s... (${reconnectAttempts}/${maxReconnectAttempts})`, "warning");
            setTimeout(connectWebSocket, delay);
        } else if (reconnectAttempts >= maxReconnectAttempts) {
            updateStatus("Failed to connect to ComfyUI WebSocket. Please check if ComfyUI is running.", "error");
        }
    };
}

// ---------- Gallery Functions ----------
function addImageToGallery(filename, subfolder = "", type = "output") {
    const gallery = document.getElementById("images");
    if (!gallery) {
        console.error("Gallery element not found");
        return;
    }
    
    let imageUrl = `http://${comfyHost}:${comfyuiPort}/view?filename=${encodeURIComponent(filename)}&type=${type}&_=${Date.now()}`;
    if (subfolder) {
        imageUrl += `&subfolder=${encodeURIComponent(subfolder)}`;
    }
    
    console.log("Adding image to gallery:", imageUrl);
    
    // Check if image already exists in gallery
    const existingImages = gallery.querySelectorAll('img');
    for (let img of existingImages) {
        if (img.src.includes(filename)) {
            console.log("Image already in gallery:", filename);
            return;
        }
    }
    
    const card = document.createElement("div");
    card.className = "image-card";
    card.innerHTML = `
        <img src="${imageUrl}" loading="lazy" 
             onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'100%25\' height=\'200\'%3E%3Crect width=\'100%25\' height=\'100%25\' fill=\'%23f0f0f0\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\'%3EImage not found%3C/text%3E%3C/svg%3E';"
             onclick="window.open('${imageUrl}', '_blank')">
        <div class="image-actions">
            <button class="download-btn" onclick="event.stopPropagation(); downloadImage('${imageUrl}', '${filename}')">💾 Download</button>
        </div>
    `;
    
    gallery.prepend(card);
    card.scrollIntoView({ behavior: 'smooth', block: 'start' });
    
    // Limit gallery to 100 images
    while (gallery.children.length > 100) {
        gallery.removeChild(gallery.lastChild);
    }
}

function downloadImage(url, filename = null) {
    const a = document.createElement('a');
    a.href = url;
    a.download = filename || `comfyui_${Date.now()}.png`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    updateStatus(`✅ Downloaded: ${filename || 'image'}`, "success");
}

async function fetchRecentImages() {
    try {
        updateStatus("🔄 Loading images from history...", "info");
        const response = await fetch(`http://${comfyHost}:${comfyuiPort}/history`);
        const history = await response.json();
        
        console.log("Checking history for images...");
        
        let imagesFound = 0;
        const prompts = Object.values(history);
        
        // Process in reverse order to get newest first
        for (let i = prompts.length - 1; i >= 0; i--) {
            const prompt = prompts[i];
            if (prompt.outputs) {
                for (const nodeId in prompt.outputs) {
                    const output = prompt.outputs[nodeId];
                    if (output.images && output.images.length > 0) {
                        imagesFound += output.images.length;
                        for (const img of output.images) {
                            addImageToGallery(img.filename, img.subfolder, img.type);
                        }
                    }
                }
            }
        }
        
        if (imagesFound > 0) {
            updateStatus(`✅ Loaded ${imagesFound} images from history`, "success");
        } else {
            updateStatus("No images found in history", "info");
        }
        
        if (isGenerating) {
            isGenerating = false;
            const btn = document.getElementById("generateBtn");
            if (btn) btn.disabled = false;
        }
        
    } catch (e) {
        console.error("Failed to fetch history:", e);
        updateStatus("Failed to fetch images from history", "error");
    }
}

function clearGallery() {
    const gallery = document.getElementById("images");
    if (gallery && confirm('Are you sure you want to clear all images?')) {
        gallery.innerHTML = '';
        updateStatus("🗑️ Gallery cleared", "info");
    }
}

// ---------- Generation Functions ----------
async function generateWithControlNet() {
    if (isGenerating) { 
        alert("Please wait, generation in progress"); 
        return; 
    }
    
    if (!ws || ws.readyState !== WebSocket.OPEN) { 
        updateStatus("WebSocket not connected, connecting...", "warning"); 
        connectWebSocket(); 
        setTimeout(() => generateWithControlNet(), 2000); 
        return; 
    }

    isGenerating = true;
    const btn = document.getElementById("generateBtn");
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = "⏳ Generating...";
    }
    updateStatus("🚀 Preparing generation...", "info");

    let model = document.getElementById('model').value;
    const positive = document.getElementById('positive_prompt').value;
    const negative = document.getElementById('negative_prompt').value;
    const steps = parseInt(document.getElementById('steps').value);
    const cfg = parseFloat(document.getElementById('cfg').value);
    let width = parseInt(document.getElementById('width').value);
    let height = parseInt(document.getElementById('height').value);
    const sampler = document.getElementById('sampler').value;
    const cnStrengthVal = document.getElementById("cnStrength") ? parseFloat(document.getElementById("cnStrength").value) : 0.85;
    const cnStart = parseFloat(document.getElementById('cnStart').value);
    const cnEnd = parseFloat(document.getElementById('cnEnd').value);
    
    let controlImageB64 = currentControlImageBase64;
    
    // Validate and auto-adjust for ControlNet
    if (controlImageB64 && !sd15Models.includes(model)) {
        const originalModel = model;
        model = "dreamshaper_8.safetensors"; // fallback to a reliable SD1.5 model
        updateStatus(`⚠️ ControlNet requires an SD1.5 model. Switching from ${originalModel} to ${model}.`, "warning");
        const modelSelect = document.getElementById('model');
        if (modelSelect) modelSelect.value = model;
    }
    
    // Validate resolution for heavy models
    if (heavyModels.includes(model)) {
        const recommendedWidth = 1024;
        const recommendedHeight = 1024;
        if (width > recommendedWidth || height > recommendedHeight) {
            width = recommendedWidth;
            height = recommendedHeight;
            updateStatus(`⚠️ Resolution reduced to ${width}x${height} for heavy model`, "warning");
            document.getElementById('width').value = width;
            document.getElementById('height').value = height;
        }
    }
    
    // Ensure dimensions are multiples of 64
    width = Math.round(width / 64) * 64;
    height = Math.round(height / 64) * 64;
    
    const payload = {
        client_id: clientId,
        model: model,
        positive_prompt: positive,
        negative_prompt: negative,
        steps: steps,
        cfg: cfg,
        width: width,
        height: height,
        sampler: sampler,
        controlnet: {
            enabled: !!controlImageB64,
            preprocessor: selectedPreprocessor,
            image_base64: controlImageB64 || null,
            strength: cnStrengthVal,
            start_percent: cnStart,
            end_percent: cnEnd
        }
    };

    console.log("Sending payload:", {
        ...payload,
        controlnet: {
            ...payload.controlnet,
            image_base64: payload.controlnet.image_base64 ? '[BASE64_IMAGE]' : null
        }
    });

    try {
        const response = await fetch('/generate-controlnet', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify(payload)
        });
        
        const result = await response.json();
        console.log("Response:", result);
        
        if (result.success) {
            updateStatus("✅ Prompt sent, waiting for image...", "success");
            currentPromptId = result.prompt_id;
            
            // Set timeout to check for images if generation takes too long
            const expectedTime = (steps * 2) + 10;
            setTimeout(() => {
                if (isGenerating) {
                    updateStatus("⚠️ Generation taking longer than expected, checking for images...", "warning");
                    fetchRecentImages();
                }
            }, expectedTime * 1000);
            
        } else {
            let errorMsg = "Unknown error";
            if (typeof result.error === 'string') {
                errorMsg = result.error;
            } else if (result.error && result.error.message) {
                errorMsg = result.error.message;
            } else if (result.error && typeof result.error === 'object') {
                errorMsg = JSON.stringify(result.error, null, 2);
            }
            throw new Error(errorMsg);
        }
    } catch (err) {
        console.error("Generation error:", err);
        updateStatus(`❌ Generation failed: ${err.message}`, "error");
        isGenerating = false;
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = "🚀 Generate with ControlNet";
        }
    }
}

async function interruptGeneration() {
    if (!isGenerating) {
        updateStatus("No generation in progress", "info");
        return;
    }
    
    try {
        const response = await fetch(`http://${comfyHost}:${comfyuiPort}/interrupt`, {
            method: 'POST'
        });
        
        if (response.ok) {
            updateStatus("⏹️ Generation interrupted", "success");
            isGenerating = false;
            const btn = document.getElementById("generateBtn");
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = "🚀 Generate with ControlNet";
            }
        }
    } catch (error) {
        console.error("Failed to interrupt:", error);
        updateStatus("Failed to interrupt generation", "error");
    }
}

// ---------- Connection Check ----------
async function checkComfyUI() {
    const possibleHosts = [window.location.hostname, 'localhost', '127.0.0.1'];
    
    for (let host of possibleHosts) {
        try {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 3000);
            
            const res = await fetch(`http://${host}:${comfyuiPort}/`, { 
                method: 'HEAD', 
                mode: 'cors', 
                cache: 'no-cache',
                signal: controller.signal,
                headers: { 'Cache-Control': 'no-cache' }
            });
            
            clearTimeout(timeoutId);
            
            if (res.ok) {
                comfyHost = host;
                console.log(`✅ ComfyUI detected at ${comfyHost}:${comfyuiPort}`);
                updateStatus(`✅ Connected to ComfyUI at ${comfyHost}:${comfyuiPort}`, "success");
                return true;
            }
        } catch(e) {
            console.log(`Failed to connect to ${host}:`, e.message);
        }
    }
    
    updateStatus("⚠️ ComfyUI not reachable. Make sure server runs on port 8188", "error");
    return false;
}

// ---------- UI Helpers ----------
function updateStatus(msg, type = "info") {
    const statusDiv = document.getElementById("status");
    if (!statusDiv) return;
    
    statusDiv.innerText = msg;
    statusDiv.className = type;
    
    // Auto-clear after 5 seconds for success/error messages
    if (type === "success" || type === "error") {
        setTimeout(() => {
            if (statusDiv.innerText === msg) {
                statusDiv.className = "";
            }
        }, 5000);
    }
}

function updateUIState() {
    // Update strength display
    const strengthSlider = document.getElementById("cnStrength");
    const strengthSpan = document.getElementById("strengthVal");
    if (strengthSlider && strengthSpan) {
        strengthSpan.innerText = strengthSlider.value;
    }
    
    // Update VRAM warning
    const modelSelect = document.getElementById("model");
    const vramWarning = document.getElementById("vram-warning");
    if (modelSelect && vramWarning) {
        const selectedModel = modelSelect.value;
        if (heavyModels.includes(selectedModel) && currentControlImageBase64) {
            vramWarning.style.display = "block";
            vramWarning.innerHTML = "⚠️ Warning: This model is very heavy for 8GB VRAM. Using ControlNet may cause out-of-memory errors. Consider switching to an SD1.5 model.";
        } else if (heavyModels.includes(selectedModel)) {
            vramWarning.style.display = "block";
            vramWarning.innerHTML = "⚠️ Warning: This model may exceed 8GB VRAM. If you encounter issues, reduce resolution or use an SD1.5 model.";
        } else {
            vramWarning.style.display = "none";
        }
    }
}

// ---------- Event Listeners Setup ----------
function setupEventListeners() {
    // Model selection
    const modelSelect = document.getElementById("model");
    if (modelSelect) {
        modelSelect.addEventListener("change", updateUIState);
    }
    
    // Control image upload
    const controlImageInput = document.getElementById("controlImageInput");
    if (controlImageInput) {
        controlImageInput.addEventListener("change", (e) => {
            handleImageUpload(e.target.files[0]);
        });
    }
    
    // Strength slider
    const strengthSlider = document.getElementById("cnStrength");
    if (strengthSlider) {
        strengthSlider.addEventListener("input", updateUIState);
    }
    
    // Generate button
    const generateBtn = document.getElementById("generateBtn");
    if (generateBtn) {
        generateBtn.onclick = generateWithControlNet;
    }
    
    // Refresh gallery button
    const refreshBtn = document.getElementById("refreshGalleryBtn");
    if (refreshBtn) {
        refreshBtn.onclick = fetchRecentImages;
    }
    
    // Clear gallery button
    const clearBtn = document.getElementById("clearGalleryBtn");
    if (clearBtn) {
        clearBtn.onclick = () => clearGallery();
    }
    
    // Interrupt button
    const interruptBtn = document.getElementById("interruptBtn");
    if (interruptBtn) {
        interruptBtn.onclick = interruptGeneration;
    }
    
    // Resolution auto-adjust based on model
    const widthInput = document.getElementById('width');
    const heightInput = document.getElementById('height');
    
    if (modelSelect && widthInput && heightInput) {
        modelSelect.addEventListener('change', () => {
            const selectedModel = modelSelect.value;
            if (selectedModel.includes('sd_xl') || selectedModel.includes('sd3.5')) {
                widthInput.value = 1024;
                heightInput.value = 1024;
                updateStatus("💡 SDXL/SD3.5 model selected - recommended resolution 1024x1024", "success");
            } else if (selectedModel.includes('flux')) {
                widthInput.value = 1024;
                heightInput.value = 1024;
                updateStatus("⚡ Flux model selected - recommended resolution 1024x1024", "success");
            } else {
                widthInput.value = 768;
                heightInput.value = 768;
            }
        });
    }
}

// ---------- Initialization ----------
window.onload = async () => {
    console.log("Initializing ComfyUI ControlNet Studio...");
    
    // Build UI
    buildPreprocessorUI();
    setupEventListeners();
    
    // Check connection and initialize
    const connected = await checkComfyUI();
    if (connected) {
        connectWebSocket();
    }
    
    // Initialize UI state
    updateUIState();
    
    // Load existing images
    setTimeout(() => {
        fetchRecentImages();
    }, 2000);
    
    console.log("Initialization complete");
};
</script>