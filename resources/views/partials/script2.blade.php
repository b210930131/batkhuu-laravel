// Configuration
<script>
const comfyHost = window.location.hostname;
const comfyPort = 8188;
const comfyUrl = `http://${comfyHost}:${comfyPort}`;
let ws = null;
let currentPromptId = null;

// Initialize preprocessors
const preprocessors = [
    { id: 'canny', name: '🔍 Canny (Edges)', default: true },
    { id: 'depth', name: '🗺️ Depth Map' },
    { id: 'openpose', name: '🧍 OpenPose' },
    { id: 'scribble', name: '✏️ Scribble' },
    { id: 'mlsd', name: '📏 MLSD (Lines)' },
    { id: 'hed', name: '🎨 HED (Soft Edges)' },
    { id: 'seg', name: '🏞️ Segmentation' },
    { id: 'normal', name: '⚡ Normal Map' }
];

// Populate preprocessor buttons
const preprocessorList = document.getElementById('preprocessorList');
preprocessors.forEach(pp => {
    const btn = document.createElement('button');
    btn.textContent = pp.name;
    btn.className = `preprocessor-btn ${pp.default ? 'active' : ''}`;
    btn.dataset.preprocessor = pp.id;
    btn.onclick = () => selectPreprocessor(pp.id);
    preprocessorList.appendChild(btn);
});

let currentPreprocessor = 'canny';
let currentControlImage = null;

function selectPreprocessor(preprocessorId) {
    currentPreprocessor = preprocessorId;
    
    // Update button styles
    document.querySelectorAll('.preprocessor-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.dataset.preprocessor === preprocessorId) {
            btn.classList.add('active');
        }
    });
    
    // Update settings visibility
    updatePreprocessorSettings(preprocessorId);
    
    // Update status if image is uploaded
    if (currentControlImage) {
        updateControlNetStatus(true, preprocessorId);
    }
}

// Handle image upload
const imageInput = document.getElementById('controlImageInput');
const controlPreview = document.getElementById('controlPreview');

imageInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (event) => {
            currentControlImage = event.target.result;
            controlPreview.innerHTML = `<img src="${currentControlImage}" style="max-width: 100%; max-height: 200px; border-radius: 8px;">`;
            updateControlNetStatus(true, currentPreprocessor);
        };
        reader.readAsDataURL(file);
    } else {
        currentControlImage = null;
        controlPreview.innerHTML = '';
        updateControlNetStatus(false);
    }
});

// Handle VRAM warning
const modelSelect = document.getElementById('model');
const vramWarning = document.getElementById('vram-warning');

modelSelect.addEventListener('change', () => {
    const selectedModel = modelSelect.value;
    const isHighVRAM = selectedModel.includes('sdxl') || 
                       selectedModel.includes('flux') || 
                       selectedModel.includes('sd3.5') ||
                       selectedModel.includes('qwen');
    
    if (isHighVRAM && currentControlImage) {
        vramWarning.style.display = 'block';
    } else {
        vramWarning.style.display = 'none';
    }
});

// Generate function
async function generate() {
    const generateBtn = document.getElementById('generateBtn');
    const statusDiv = document.getElementById('status');
    
    try {
        generateBtn.disabled = true;
        statusDiv.innerHTML = '⏳ Generating image... This may take a moment';
        statusDiv.style.background = '#fff3cd';
        
        // Collect form data
        const formData = {
            client_id: 'laravel-client-' + Date.now(),
            model: modelSelect.value,
            positive_prompt: document.getElementById('positive_prompt').value,
            negative_prompt: document.getElementById('negative_prompt').value,
            steps: parseInt(document.getElementById('steps').value),
            cfg: parseFloat(document.getElementById('cfg').value),
            width: parseInt(document.getElementById('width').value),
            height: parseInt(document.getElementById('height').value),
            sampler: document.getElementById('sampler').value,
            controlnet: currentControlImage ? {
                enabled: true,
                preprocessor: currentPreprocessor,
                image_base64: currentControlImage,
                strength: parseFloat(document.getElementById('cnStrength').value),
                start_percent: parseFloat(document.getElementById('cnStart').value),
                end_percent: parseFloat(document.getElementById('cnEnd').value)
            } : null
        };
        
        // Add preprocessor-specific settings
        if (currentControlImage) {
            switch(currentPreprocessor) {
                case 'canny':
                    formData.controlnet.canny_low = parseInt(document.getElementById('cannyLowThreshold').value);
                    formData.controlnet.canny_high = parseInt(document.getElementById('cannyHighThreshold').value);
                    break;
                case 'depth':
                    formData.controlnet.depth_resolution = parseInt(document.getElementById('depthResolution').value);
                    break;
                case 'openpose':
                    formData.controlnet.openpose_hands = document.getElementById('openposeHands').value;
                    formData.controlnet.openpose_body = document.getElementById('openposeBody').value;
                    formData.controlnet.openpose_face = document.getElementById('openposeFace').value;
                    break;
                case 'mlsd':
                    formData.controlnet.mlsd_score_thr = parseFloat(document.getElementById('mlsdScoreThr').value);
                    formData.controlnet.mlsd_dist_thr = parseFloat(document.getElementById('mlsdDistThr').value);
                    formData.controlnet.mlsd_resolution = parseInt(document.getElementById('mlsdResolution').value);
                    break;
                // Add other preprocessors as needed
            }
        }
        
        // Send to backend
        const response = await fetch('/api/generate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.error || 'Generation failed');
        }
        
        currentPromptId = data.prompt_id;
        statusDiv.innerHTML = '✅ Generation started! Waiting for results...';
        statusDiv.style.background = '#d4edda';
        
        // Connect to WebSocket for real-time updates
        connectWebSocket(currentPromptId);
        
    } catch (error) {
        console.error('Generation error:', error);
        statusDiv.innerHTML = `❌ Error: ${error.message}`;
        statusDiv.style.background = '#f8d7da';
    } finally {
        generateBtn.disabled = false;
    }
}

// WebSocket connection for real-time results
function connectWebSocket(promptId) {
    if (ws && ws.readyState === WebSocket.OPEN) {
        ws.close();
    }
    
    ws = new WebSocket(`ws://${comfyHost}:${comfyPort}/ws?clientId=laravel-client-${Date.now()}`);
    
    ws.onopen = () => {
        console.log('WebSocket connected');
    };
    
    ws.onmessage = (event) => {
        const data = JSON.parse(event.data);
        console.log('WebSocket message:', data);
        
        if (data.type === 'executing' && data.data.node === null && data.data.prompt_id === promptId) {
            // Generation complete
            refreshGallery();
            document.getElementById('status').innerHTML = '✅ Generation complete!';
            document.getElementById('status').style.background = '#d4edda';
            setTimeout(() => {
                document.getElementById('status').innerHTML = '✅ Ready • ControlNet ' + 
                    (currentControlImage ? 'active' : 'inactive');
                document.getElementById('status').style.background = '#f0f0f0';
            }, 3000);
        }
    };
    
    ws.onerror = (error) => {
        console.error('WebSocket error:', error);
    };
}

// Refresh gallery
async function refreshGallery() {
    const galleryDiv = document.getElementById('images');
    galleryDiv.innerHTML = '<div style="text-align: center; padding: 60px 20px;">🔄 Loading images...</div>';
    
    try {
        const response = await fetch(`${comfyUrl}/history`);
        const data = await response.json();
        
        const images = [];
        Object.values(data).forEach(prompt => {
            if (prompt.outputs) {
                Object.values(prompt.outputs).forEach(output => {
                    if (output.images) {
                        output.images.forEach(image => {
                            images.push({
                                filename: image.filename,
                                subfolder: image.subfolder,
                                type: image.type
                            });
                        });
                    }
                });
            }
        });
        
        // Display most recent first
        images.reverse();
        
        if (images.length === 0) {
            galleryDiv.innerHTML = '<div style="text-align: center; padding: 60px 20px;">No images yet. Generate your first image!</div>';
            return;
        }
        
        galleryDiv.innerHTML = `
            <div style="display: grid; gap: 20px;">
                ${images.map(img => `
                    <div style="border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
                        <img src="${comfyUrl}/view?filename=${encodeURIComponent(img.filename)}&subfolder=${encodeURIComponent(img.subfolder)}&type=${img.type}" 
                             style="width: 100%; height: auto;" 
                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\' viewBox=\'0 0 100 100\'%3E%3Crect width=\'100\' height=\'100\' fill=\'%23ddd\'/%3E%3Ctext x=\'50\' y=\'50\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\'%3EImage not found%3C/text%3E%3C/svg%3E'">
                    </div>
                `).join('')}
            </div>
        `;
        
    } catch (error) {
        console.error('Failed to refresh gallery:', error);
        galleryDiv.innerHTML = `<div style="text-align: center; padding: 60px 20px; color: #dc3545;">❌ Failed to load images: ${error.message}</div>`;
    }
}

// Interrupt generation
async function interruptGeneration() {
    try {
        await fetch(`${comfyUrl}/interrupt`, { method: 'POST' });
        document.getElementById('status').innerHTML = '⏹️ Generation interrupted';
        document.getElementById('status').style.background = '#f8d7da';
        setTimeout(() => {
            document.getElementById('status').innerHTML = '✅ Ready • ControlNet ' + 
                (currentControlImage ? 'active' : 'inactive');
            document.getElementById('status').style.background = '#f0f0f0';
        }, 3000);
    } catch (error) {
        console.error('Failed to interrupt:', error);
    }
}

// Event listeners
document.getElementById('generateBtn').addEventListener('click', generate);
document.getElementById('refreshGalleryBtn').addEventListener('click', refreshGallery);
document.getElementById('interruptBtn').addEventListener('click', interruptGeneration);

// Initial gallery load
refreshGallery();

// Auto-refresh gallery every 10 seconds
setInterval(refreshGallery, 10000);
</script>