// Configuration
const apiBase = '/api';  // Use Laravel API endpoints
let ws = null;
let currentPromptId = null;

// Initialize preprocessors
const preprocessors = [
    { id: 'canny', name: '🔍 Canny (Edges)', default: true, icon: '🔍' },
    { id: 'depth', name: '🗺️ Depth Map', icon: '🗺️' },
    { id: 'openpose', name: '🧍 OpenPose', icon: '🧍' },
    { id: 'scribble', name: '✏️ Scribble', icon: '✏️' },
    { id: 'mlsd', name: '📏 MLSD (Lines)', icon: '📏' },
    { id: 'hed', name: '🎨 HED (Soft Edges)', icon: '🎨' },
    { id: 'seg', name: '🏞️ Segmentation', icon: '🏞️' },
    { id: 'normal', name: '⚡ Normal Map', icon: '⚡' }
];

// Populate preprocessor buttons with better styling
const preprocessorList = document.getElementById('preprocessorList');
if (preprocessorList) {
    preprocessorList.innerHTML = '';
    preprocessors.forEach(pp => {
        const btn = document.createElement('button');
        btn.className = `preprocessor-btn ${pp.default ? 'active' : ''}`;
        btn.dataset.preprocessor = pp.id;
        btn.innerHTML = `
            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="font-size: 24px;">${pp.icon}</span>
                <div>
                    <div class="preprocessor-title">${pp.name}</div>
                    <div class="preprocessor-desc">${getPreprocessorDesc(pp.id)}</div>
                </div>
            </div>
        `;
        btn.onclick = () => selectPreprocessor(pp.id);
        preprocessorList.appendChild(btn);
    });
}

function getPreprocessorDesc(id) {
    const descs = {
        canny: 'Edge detection for outlines',
        depth: '3D depth information',
        openpose: 'Human pose and skeleton',
        scribble: 'Sketch to detailed image',
        mlsd: 'Straight line detection',
        hed: 'Soft painterly edges',
        seg: 'Semantic segmentation',
        normal: 'Surface normal maps'
    };
    return descs[id] || 'ControlNet preprocessor';
}

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

if (imageInput) {
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

// Handle VRAM warning
const modelSelect = document.getElementById('model');
const vramWarning = document.getElementById('vram-warning');

if (modelSelect && vramWarning) {
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
}

// Generate function
async function generate() {
    const generateBtn = document.getElementById('generateBtn');
    const statusDiv = document.getElementById('status');
    
    if (!generateBtn || !statusDiv) return;
    
    try {
        generateBtn.disabled = true;
        statusDiv.innerHTML = '⏳ Generating image... This may take a moment';
        statusDiv.className = 'warning';
        
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
                    formData.controlnet.openpose_hands = document.getElementById('openposeHands').checked ? 'enable' : 'disable';
                    formData.controlnet.openpose_body = document.getElementById('openposeBody').checked ? 'enable' : 'disable';
                    formData.controlnet.openpose_face = document.getElementById('openposeFace').checked ? 'enable' : 'disable';
                    break;
                case 'mlsd':
                    formData.controlnet.mlsd_score_thr = parseFloat(document.getElementById('mlsdScoreThr').value);
                    formData.controlnet.mlsd_dist_thr = parseFloat(document.getElementById('mlsdDistThr').value);
                    formData.controlnet.mlsd_resolution = parseInt(document.getElementById('mlsdResolution').value);
                    break;
                case 'hed':
                    formData.controlnet.hed_resolution = parseInt(document.getElementById('hedResolution').value);
                    break;
                case 'scribble':
                    formData.controlnet.scribble_mode = document.getElementById('scribbleMode').value;
                    break;
                case 'seg':
                    formData.controlnet.seg_resolution = parseInt(document.getElementById('segResolution').value);
                    break;
                case 'normal':
                    formData.controlnet.normal_resolution = parseInt(document.getElementById('normalResolution').value);
                    break;
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
        statusDiv.className = 'success';
        
        // Poll for results instead of WebSocket (simpler)
        pollForResults(currentPromptId);
        
    } catch (error) {
        console.error('Generation error:', error);
        statusDiv.innerHTML = `❌ Error: ${error.message}`;
        statusDiv.className = 'error';
    } finally {
        generateBtn.disabled = false;
    }
}

// Poll for results
async function pollForResults(promptId) {
    let attempts = 0;
    const maxAttempts = 60; // 1 minute timeout
    
    const checkInterval = setInterval(async () => {
        attempts++;
        
        try {
            const response = await fetch('/api/comfyui/history');
            const history = await response.json();
            
            if (history[promptId]) {
                clearInterval(checkInterval);
                refreshGallery();
                const statusDiv = document.getElementById('status');
                if (statusDiv) {
                    statusDiv.innerHTML = '✅ Generation complete!';
                    statusDiv.className = 'success';
                    setTimeout(() => {
                        statusDiv.innerHTML = '✅ Ready • ControlNet ' + 
                            (currentControlImage ? 'active' : 'inactive');
                        statusDiv.className = '';
                    }, 3000);
                }
            } else if (attempts >= maxAttempts) {
                clearInterval(checkInterval);
                const statusDiv = document.getElementById('status');
                if (statusDiv) {
                    statusDiv.innerHTML = '⏰ Generation timed out';
                    statusDiv.className = 'error';
                }
            }
        } catch (error) {
            console.error('Polling error:', error);
        }
    }, 1000);
}

// Refresh gallery using proxy
async function refreshGallery() {
    const galleryDiv = document.getElementById('images');
    if (!galleryDiv) return;
    
    galleryDiv.innerHTML = '<div style="text-align: center; padding: 60px 20px;">🔄 Loading images...</div>';
    
    try {
        const response = await fetch('/api/comfyui/history');
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
                                type: image.type,
                                timestamp: prompt.timestamp
                            });
                        });
                    }
                });
            }
        });
        
        // Sort by timestamp, most recent first
        images.sort((a, b) => (b.timestamp || 0) - (a.timestamp || 0));
        
        if (images.length === 0) {
            galleryDiv.innerHTML = '<div style="text-align: center; padding: 60px 20px;">No images yet. Generate your first image!</div>';
            return;
        }
        
        galleryDiv.innerHTML = images.map(img => `
            <div class="image-card">
                <img src="/api/comfyui/view?filename=${encodeURIComponent(img.filename)}&subfolder=${encodeURIComponent(img.subfolder)}&type=${img.type}" 
                     alt="Generated image"
                     onclick="window.open(this.src, '_blank')">
                <div class="image-actions">
                    <button class="download-btn" onclick="downloadImage('${img.filename}', '${img.subfolder}', '${img.type}')">
                        📥 Download
                    </button>
                </div>
            </div>
        `).join('');
        
    } catch (error) {
        console.error('Failed to refresh gallery:', error);
        galleryDiv.innerHTML = `<div style="text-align: center; padding: 60px 20px; color: #dc3545;">❌ Failed to load images: ${error.message}</div>`;
    }
}

// Download image
async function downloadImage(filename, subfolder, type) {
    try {
        const response = await fetch(`/api/comfyui/view?filename=${encodeURIComponent(filename)}&subfolder=${encodeURIComponent(subfolder)}&type=${type}`);
        const blob = await response.blob();
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    } catch (error) {
        console.error('Download error:', error);
    }
}

// Interrupt generation
async function interruptGeneration() {
    try {
        await fetch('/api/comfyui/interrupt', { method: 'POST' });
        const statusDiv = document.getElementById('status');
        if (statusDiv) {
            statusDiv.innerHTML = '⏹️ Generation interrupted';
            statusDiv.className = 'warning';
            setTimeout(() => {
                statusDiv.innerHTML = '✅ Ready • ControlNet ' + 
                    (currentControlImage ? 'active' : 'inactive');
                statusDiv.className = '';
            }, 3000);
        }
    } catch (error) {
        console.error('Failed to interrupt:', error);
    }
}

// Check models
async function checkModels() {
    const statusDiv = document.getElementById('modelStatus');
    if (!statusDiv) return;
    
    statusDiv.innerHTML = 'Checking models...';
    
    try {
        const response = await fetch('/api/comfyui/object_info');
        const data = await response.json();
        
        const models = Object.keys(data).filter(key => 
            key.includes('model') || key.includes('checkpoint') || key.includes('Checkpoint')
        );
        
        statusDiv.innerHTML = `
            <strong>✅ Available Models (${models.length}):</strong><br>
            ${models.slice(0, 20).map(m => `• ${m}`).join('<br>')}
            ${models.length > 20 ? `<br>... and ${models.length - 20} more` : ''}
        `;
        statusDiv.style.color = '#155724';
    } catch (error) {
        statusDiv.innerHTML = `❌ Failed to fetch models: ${error.message}`;
        statusDiv.style.color = '#721c24';
    }
}

// Update preprocessor settings visibility
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

// Event listeners
document.addEventListener('DOMContentLoaded', () => {
    const generateBtn = document.getElementById('generateBtn');
    const refreshBtn = document.getElementById('refreshGalleryBtn');
    const interruptBtn = document.getElementById('interruptBtn');
    const checkModelsBtn = document.getElementById('checkModelsBtn');
    
    if (generateBtn) generateBtn.addEventListener('click', generate);
    if (refreshBtn) refreshBtn.addEventListener('click', refreshGallery);
    if (interruptBtn) interruptBtn.addEventListener('click', interruptGeneration);
    if (checkModelsBtn) checkModelsBtn.addEventListener('click', checkModels);
    
    // Initial gallery load
    refreshGallery();
    
    // Auto-refresh every 10 seconds
    setInterval(refreshGallery, 10000);
    
    // Update slider displays
    const lowSlider = document.getElementById('cannyLowThreshold');
    const highSlider = document.getElementById('cannyHighThreshold');
    const lowSpan = document.getElementById('cannyLowVal');
    const highSpan = document.getElementById('cannyHighVal');
    
    if (lowSlider && lowSpan) {
        lowSlider.addEventListener('input', () => lowSpan.innerText = lowSlider.value);
    }
    if (highSlider && highSpan) {
        highSlider.addEventListener('input', () => highSpan.innerText = highSlider.value);
    }
    
    const strengthSlider = document.getElementById('cnStrength');
    const strengthVal = document.getElementById('strengthVal');
    if (strengthSlider && strengthVal) {
        strengthSlider.addEventListener('input', () => strengthVal.innerText = strengthSlider.value);
    }
    
    const startSlider = document.getElementById('cnStart');
    const endSlider = document.getElementById('cnEnd');
    const startVal = document.getElementById('startVal');
    const endVal = document.getElementById('endVal');
    
    if (startSlider && startVal) {
        startSlider.addEventListener('input', () => startVal.innerText = startSlider.value);
    }
    if (endSlider && endVal) {
        endSlider.addEventListener('input', () => endVal.innerText = endSlider.value);
    }
    
    const mlsdScoreSlider = document.getElementById('mlsdScoreThr');
    const mlsdScoreVal = document.getElementById('mlsdScoreVal');
    if (mlsdScoreSlider && mlsdScoreVal) {
        mlsdScoreSlider.addEventListener('input', () => mlsdScoreVal.innerText = mlsdScoreSlider.value);
    }
    
    const mlsdDistSlider = document.getElementById('mlsdDistThr');
    const mlsdDistVal = document.getElementById('mlsdDistVal');
    if (mlsdDistSlider && mlsdDistVal) {
        mlsdDistSlider.addEventListener('input', () => mlsdDistVal.innerText = mlsdDistSlider.value);
    }
});

// Make functions globally available
window.updatePreprocessorSettings = updatePreprocessorSettings;
window.updateControlNetStatus = updateControlNetStatus;
window.downloadImage = downloadImage;
