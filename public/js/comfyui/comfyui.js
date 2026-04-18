// --- Configuration & State ---
const apiBase = '/api';
let currentPromptId = null;
let currentPreprocessor = 'canny';
let currentControlImage = null;

const preprocessors = [
    { id: 'canny', name: 'Canny', icon: '🔲', desc: 'Edge detection' },
    { id: 'depth', name: 'Depth', icon: '🗺️', desc: '3D depth map' },
    { id: 'openpose', name: 'OpenPose', icon: '🧍', desc: 'Human pose skeleton' },
    { id: 'scribble', name: 'Scribble', icon: '✏️', desc: 'Hand-drawn sketch' },
    { id: 'mlsd', name: 'MLSD', icon: '📐', desc: 'Straight line detection' },
    { id: 'hed', name: 'HED', icon: '🎨', desc: 'Soft edges' },
    { id: 'seg', name: 'Segmentation', icon: '🏷️', desc: 'Semantic segmentation' },
    { id: 'normal', name: 'Normal Map', icon: '⚡', desc: 'Surface normal maps' }
];

// --- Initialization ---
document.addEventListener('DOMContentLoaded', () => {
    console.log('Initializing ComfyUI Studio...');
    
    initPreprocessorUI();
    setupSliderListeners();
    setupImageUpload();
    setupVramWarning();
    setupModelChangeListener();
    
    // Set default preprocessor (Canny)
    selectPreprocessor('canny');
    
    // Initial gallery load and auto-refresh
    refreshGallery();
    setInterval(refreshGallery, 10000);

    // Global Button Assignments
    const genBtn = document.getElementById('generateBtn');
    if (genBtn) genBtn.onclick = generate;

    const refreshBtn = document.getElementById('refreshGalleryBtn');
    if (refreshBtn) refreshBtn.onclick = refreshGallery;

    const interruptBtn = document.getElementById('interruptBtn');
    if (interruptBtn) interruptBtn.onclick = interruptGeneration;
    
    console.log('Initialization complete');
});

// --- UI Functions ---

function initPreprocessorUI() {
    const list = document.getElementById('preprocessorList');
    if (!list) {
        console.error('preprocessorList element not found');
        return;
    }

    list.innerHTML = '';
    preprocessors.forEach(pp => {
        const btn = document.createElement('button');
        btn.className = `preprocessor-btn ${pp.id === 'canny' ? 'active' : ''}`;
        btn.dataset.id = pp.id;
        btn.innerHTML = `
            <div style="display: flex; align-items: center; gap: 12px; text-align: left;">
                <span style="font-size: 24px;">${pp.icon}</span>
                <div>
                    <div style="font-weight: bold;">${pp.name}</div>
                    <div style="font-size: 11px; opacity: 0.8;">${pp.desc}</div>
                </div>
            </div>`;
        btn.onclick = () => selectPreprocessor(pp.id);
        list.appendChild(btn);
    });
    
    console.log('Preprocessor buttons created:', preprocessors.length);
}

function selectPreprocessor(id) {
    console.log(`Switching preprocessor to: ${id}`);
    
    // 1. Update global state
    currentPreprocessor = id;
    
    // 2. Update button UI - active state
    document.querySelectorAll('.preprocessor-btn').forEach(btn => {
        if (btn.dataset.id === id) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
    
    // 3. Hide ALL preprocessor settings panels
    const allSettings = document.querySelectorAll('.preprocessor-settings');
    allSettings.forEach(panel => {
        panel.style.display = 'none';
        panel.classList.remove('active');
    });
    
    // 4. Show the selected preprocessor settings panel
    const targetPanel = document.getElementById(`${id}Settings`);
    if (targetPanel) {
        targetPanel.style.display = 'block';
        targetPanel.classList.add('active');
        console.log(`Showing settings for: ${id}Settings`);
    } else {
        console.warn(`Settings panel not found for: ${id}Settings`);
    }
    
    // 5. Update ControlNet status if image exists
    if (currentControlImage) {
        updateControlNetStatus(true, getPreprocessorDisplayName(id));
    }
}

function getPreprocessorDisplayName(id) {
    const names = {
        'canny': 'Canny Edge',
        'depth': 'Depth Map',
        'openpose': 'OpenPose',
        'scribble': 'Scribble',
        'mlsd': 'MLSD',
        'hed': 'HED Edge',
        'seg': 'Segmentation',
        'normal': 'Normal Map'
    };
    return names[id] || id;
}

function setupSliderListeners() {
    // Canny sliders
    const lowSlider = document.getElementById('cannyLowThreshold');
    const lowVal = document.getElementById('cannyLowVal');
    if (lowSlider && lowVal) {
        lowSlider.addEventListener('input', () => {
            lowVal.textContent = lowSlider.value;
        });
    }
    
    const highSlider = document.getElementById('cannyHighThreshold');
    const highVal = document.getElementById('cannyHighVal');
    if (highSlider && highVal) {
        highSlider.addEventListener('input', () => {
            highVal.textContent = highSlider.value;
        });
    }
    
    // MLSD sliders
    const mlsdScore = document.getElementById('mlsdScoreThr');
    const mlsdScoreVal = document.getElementById('mlsdScoreVal');
    if (mlsdScore && mlsdScoreVal) {
        mlsdScore.addEventListener('input', () => {
            mlsdScoreVal.textContent = parseFloat(mlsdScore.value).toFixed(2);
        });
    }
    
    const mlsdDist = document.getElementById('mlsdDistThr');
    const mlsdDistVal = document.getElementById('mlsdDistVal');
    if (mlsdDist && mlsdDistVal) {
        mlsdDist.addEventListener('input', () => {
            mlsdDistVal.textContent = parseFloat(mlsdDist.value).toFixed(2);
        });
    }
    
    // ControlNet strength slider
    const cnStrength = document.getElementById('cnStrength');
    const strengthVal = document.getElementById('strengthVal');
    if (cnStrength && strengthVal) {
        cnStrength.addEventListener('input', () => {
            strengthVal.textContent = cnStrength.value;
        });
    }
    
    // ControlNet start/end sliders
    const cnStart = document.getElementById('cnStart');
    const startVal = document.getElementById('startVal');
    if (cnStart && startVal) {
        cnStart.addEventListener('input', () => {
            startVal.textContent = cnStart.value;
        });
    }
    
    const cnEnd = document.getElementById('cnEnd');
    const endVal = document.getElementById('endVal');
    if (cnEnd && endVal) {
        cnEnd.addEventListener('input', () => {
            endVal.textContent = cnEnd.value;
        });
    }
}

// --- Image Handling ---

function setupImageUpload() {
    const imageInput = document.getElementById('controlImageInput');
    const controlPreview = document.getElementById('controlPreview');

    if (imageInput) {
        imageInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                // Check file size (max 10MB)
                if (file.size > 10 * 1024 * 1024) {
                    alert('Image size should be less than 10MB');
                    imageInput.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = (event) => {
                    currentControlImage = event.target.result;
                    controlPreview.innerHTML = `<img src="${currentControlImage}" style="max-width: 100%; max-height: 200px; border-radius: 8px; border: 2px solid #667eea;">`;
                    updateControlNetStatus(true, getPreprocessorDisplayName(currentPreprocessor));
                };
                reader.readAsDataURL(file);
            } else {
                currentControlImage = null;
                controlPreview.innerHTML = '';
                updateControlNetStatus(false);
            }
        });
    }
}

function updateControlNetStatus(hasImage, preprocessorName = null) {
    const dot = document.getElementById('controlnetDot');
    const text = document.getElementById('controlnetText');
    const details = document.getElementById('controlnetDetails');
    
    if (dot && text && details) {
        if (hasImage && preprocessorName) {
            dot.style.backgroundColor = '#10b981';
            text.innerHTML = `✅ ControlNet Active: ${preprocessorName}`;
            details.innerHTML = `Using ${preprocessorName} preprocessor to guide generation.`;
        } else if (hasImage) {
            dot.style.backgroundColor = '#f59e0b';
            text.innerHTML = `⚠️ ControlNet: Image uploaded, no preprocessor selected`;
            details.innerHTML = 'Select a preprocessor to enable ControlNet guidance.';
        } else {
            dot.style.backgroundColor = '#ef4444';
            text.innerHTML = '⏸️ ControlNet Inactive';
            details.innerHTML = 'Upload an image and select a preprocessor to enable ControlNet.';
        }
    }
}

function setupVramWarning() {
    const modelSelect = document.getElementById('model');
    const vramWarning = document.getElementById('vram-warning');

    if (modelSelect && vramWarning) {
        modelSelect.addEventListener('change', () => {
            const val = modelSelect.value.toLowerCase();
            const isHighVRAM = ['sdxl', 'flux', 'sd3.5', 'qwen'].some(m => val.includes(m));
            vramWarning.style.display = isHighVRAM ? 'block' : 'none';
        });
    }
}

function setupModelChangeListener() {
    const modelSelect = document.getElementById('model');
    if (modelSelect) {
        modelSelect.addEventListener('change', () => {
            const val = modelSelect.value;
            console.log('Model changed to:', val);
            
            // Update recommended dimensions for SDXL
            if (val.includes('sdxl')) {
                const widthInput = document.getElementById('width');
                const heightInput = document.getElementById('height');
                if (widthInput && heightInput) {
                    if (widthInput.value === '768' && heightInput.value === '768') {
                        widthInput.value = '1024';
                        heightInput.value = '1024';
                    }
                }
            }
        });
    }
}

// --- Core API Logic ---

async function generate() {
    const statusDiv = document.getElementById('status');
    const genBtn = document.getElementById('generateBtn');
    
    if (!statusDiv) return;
    
    // Validation
    const prompt = document.getElementById('positive_prompt').value.trim();
    if (!prompt) {
        statusDiv.innerHTML = '❌ Please enter a positive prompt';
        statusDiv.className = 'error';
        return;
    }
    
    if (genBtn) genBtn.disabled = true;
    statusDiv.innerHTML = '⏳ Initializing generation...';
    statusDiv.className = 'warning';

    const formData = {
        client_id: 'laravel-client-' + Date.now(),
        model: document.getElementById('model').value,
        positive_prompt: prompt,
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
            strength: parseFloat(document.getElementById('cnStrength')?.value || 0.85),
            start_percent: parseFloat(document.getElementById('cnStart')?.value || 0),
            end_percent: parseFloat(document.getElementById('cnEnd')?.value || 1),
            ...getPreprocessorParams() 
        } : null
    };
    
    console.log('Generation request:', {
        model: formData.model,
        prompt_length: formData.positive_prompt.length,
        controlnet_enabled: !!formData.controlnet,
        preprocessor: formData.controlnet?.preprocessor
    });

    try {
        const response = await fetch('/api/generate', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        if (!response.ok) throw new Error(data.error || 'Generation failed');

        statusDiv.innerHTML = '✅ Generation started! Processing...';
        statusDiv.className = 'success';
        if (data.prompt_id) {
            pollForResults(data.prompt_id);
        }

    } catch (e) {
        console.error('Generation error:', e);
        statusDiv.innerHTML = "❌ Error: " + e.message;
        statusDiv.className = 'error';
    } finally {
        if (genBtn) genBtn.disabled = false;
    }
}

function getPreprocessorParams() {
    const params = {};
    
    switch(currentPreprocessor) {
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
            
        default:
            // Generic resolution parameter for any preprocessor
            const resEl = document.getElementById(`${currentPreprocessor}Resolution`);
            if (resEl) {
                params[`${currentPreprocessor}_resolution`] = parseInt(resEl.value);
            }
    }
    
    console.log('Preprocessor params:', params);
    return params;
}

// --- Polling & Gallery ---

async function pollForResults(promptId) {
    let attempts = 0;
    const maxAttempts = 60; // 2 minutes max (2 sec intervals)
    
    const checkInterval = setInterval(async () => {
        attempts++;
        
        if (attempts > maxAttempts) {
            clearInterval(checkInterval);
            const statusDiv = document.getElementById('status');
            if (statusDiv) {
                statusDiv.innerHTML = '⏱️ Generation timeout. Please try again.';
                statusDiv.className = 'error';
            }
            return;
        }
        
        try {
            const res = await fetch('/api/comfyui/history');
            const history = await res.json();
            
            if (history[promptId]) {
                clearInterval(checkInterval);
                refreshGallery();
                const statusDiv = document.getElementById('status');
                if (statusDiv) {
                    statusDiv.innerHTML = '✅ Generation complete! Image added to gallery.';
                    statusDiv.className = 'success';
                    
                    // Reset after 3 seconds
                    setTimeout(() => {
                        if (statusDiv) {
                            statusDiv.innerHTML = '✅ Ready • ControlNet ready';
                            statusDiv.className = '';
                        }
                    }, 3000);
                }
            }
        } catch (e) {
            console.error("Polling error", e);
        }
    }, 2000);
}

async function refreshGallery() {
    const galleryDiv = document.getElementById('images');
    if (!galleryDiv) return;

    try {
        const response = await fetch('/api/comfyui/history');
        const data = await response.json();
        const images = [];

        Object.values(data).forEach(prompt => {
            if (prompt.outputs) {
                Object.values(prompt.outputs).forEach(out => {
                    if (out.images) {
                        out.images.forEach(img => {
                            images.push({
                                ...img, 
                                timestamp: prompt.timestamp || Date.now(),
                                prompt_id: prompt.prompt_id
                            });
                        });
                    }
                });
            }
        });

        images.sort((a, b) => (b.timestamp || 0) - (a.timestamp || 0));

        if (images.length === 0) {
            galleryDiv.innerHTML = `
                <div style="text-align: center; padding: 60px 20px; color: #9ca3af;">
                    🎨 No images generated yet<br>
                    <small style="margin-top: 8px; display: block;">Your generated images will appear here</small>
                </div>`;
            return;
        }

        galleryDiv.innerHTML = images.map(img => `
            <div class="image-card" style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 16px;">
                <img src="/api/comfyui/view?filename=${encodeURIComponent(img.filename)}&subfolder=${encodeURIComponent(img.subfolder)}&type=${img.type}" 
                     style="width: 100%; height: auto; cursor: pointer;"
                     onclick="window.open(this.src, '_blank')">
                <div class="image-actions" style="padding: 12px; text-align: center;">
                    <button class="download-btn" onclick="downloadImage('${img.filename}', '${img.subfolder}', '${img.type}')" 
                            style="background: #667eea; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer;">
                        📥 Download
                    </button>
                </div>
            </div>`).join('');
            
    } catch (e) {
        console.error("Gallery Refresh Error", e);
        galleryDiv.innerHTML = `<div style="text-align: center; padding: 60px 20px; color: #ef4444;">
            ❌ Error loading gallery: ${e.message}
        </div>`;
    }
}

async function downloadImage(filename, subfolder, type) {
    try {
        const url = `/api/comfyui/view?filename=${encodeURIComponent(filename)}&subfolder=${encodeURIComponent(subfolder)}&type=${type}`;
        const res = await fetch(url);
        if (!res.ok) throw new Error('Download failed');
        
        const blob = await res.blob();
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(a.href);
    } catch (e) {
        console.error('Download error:', e);
        alert('Failed to download image');
    }
}

async function interruptGeneration() {
    try {
        await fetch('/api/comfyui/interrupt', { method: 'POST' });
        const statusDiv = document.getElementById('status');
        if (statusDiv) {
            statusDiv.innerHTML = '⏹️ Generation interrupted';
            statusDiv.className = 'warning';
            setTimeout(() => {
                statusDiv.innerHTML = '✅ Ready • ControlNet ready';
                statusDiv.className = '';
            }, 2000);
        }
    } catch (e) {
        console.error('Interrupt error:', e);
    }
}

// Global exposure
window.downloadImage = downloadImage;
window.selectPreprocessor = selectPreprocessor;
window.getPreprocessorParams = getPreprocessorParams;