@include('partials.styles2')
<style>
    .preprocessor-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 12px;
        padding: 20px;
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

    /* VRAM Progress Bar Styles */
    .vram-container {
        background: #eee;
        border-radius: 10px;
        height: 10px;
        width: 100%;
        margin-top: 10px;
        overflow: hidden;
    }
    .vram-bar {
        background: linear-gradient(to right, #10b981, #f59e0b);
        height: 100%;
        width: 0%;
        transition: width 0.5s ease;
    }
</style>

<div class="container">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        
        <div>
            <div class="card">
                <div class="card-header">⚙️ Model & Prompt Configuration</div>
                
                <div class="control-group">
                    <label>🎭 Model Selection</label>
                    <select id="model">
                        <option value="dreamshaper_8.safetensors" selected>✨ Dreamshaper 8 (SD1.5)</option>
                        <option value="v1-5-pruned-emaonly-fp16.safetensors">📦 v1-5 (FP16)</option>
                        <option value="realisticVisionV60B1_v51HyperVAE.safetensors">🎭 Realistic Vision V6.0</option>
                        <option value="sd_xl_base_1.0.safetensors">🎨 SDXL Base 1.0</option>
                    </select>
                </div>
                
                <div class="control-group">
                    <label>✨ Positive Prompt</label>
                    <textarea id="positive_prompt" rows="3">masterpiece, architectural photography, modern building exterior, luxury facade, high-quality rendering.</textarea>
                </div>
                
                <div class="control-group">
                    <label>❌ Negative Prompt</label>
                    <textarea id="negative_prompt" rows="2">worst quality, low quality, distorted, blurry.</textarea>
                </div>
                
                <div class="param-row">
                    <div class="param-item"><label>Steps</label><input type="number" id="steps" value="20"></div>
                    <div class="param-item"><label>CFG</label><input type="number" id="cfg" value="7.0" step="0.5"></div>
                    <div class="param-item"><label>Width</label><input type="number" id="width" value="768"></div>
                    <div class="param-item"><label>Height</label><input type="number" id="height" value="768"></div>
                </div>
                
                <div class="control-strength">
                    <label>💪 ControlNet Strength: <span id="strengthVal">0.85</span></label>
                    <input type="range" id="cnStrength" min="0" max="2" value="0.85" step="0.01">
                </div>

                <div class="control-group" style="padding: 20px;">
                    <label>🖼️ Control Image</label>
                    <input type="file" id="controlImageInput" accept="image/*">
                    <div id="controlPreview" class="image-preview"></div>
                </div>
                
                <div style="display: flex; gap: 12px; padding: 20px;">
                    <button id="generateBtn" class="btn-primary">🚀 Generate</button>
                    <button id="interruptBtn" style="background: #dc3545; color: white;">⏹️ Interrupt</button>
                </div>
                
                <div id="status" style="margin: 0 20px 20px 20px; padding: 12px; border-radius: 8px; background: #f3f4f6;">
                    ✅ Ready
                </div>
            </div>

            <div class="card">
                <div class="card-header">🎨 ControlNet Preprocessors</div>
                <div id="preprocessorList" class="preprocessor-grid">
                    <button onclick="updatePreprocessorSettings('canny')" class="btn-secondary">Canny</button>
                    <button onclick="updatePreprocessorSettings('depth')" class="btn-secondary">Depth</button>
                </div>
            </div>
        </div>
        
        <div>
            <div class="card" style="background: #f8fafc; border: 1px solid #cbd5e1;">
                <div class="card-header">🖥️ Server Health & GPU Status</div>
                <div style="padding: 20px;">
                    <button id="checkHealthBtn" style="background: #4f46e5; color: white; padding: 8px 15px; border-radius: 5px; cursor: pointer; border: none;">
                        🔍 Check System Health
                    </button>
                    
                    <div id="modelStatus" style="margin-top: 15px; font-family: monospace; font-size: 13px;">
                        Status: Unknown (Click to check)
                    </div>

                    <div id="vramStatus" style="display: none; margin-top: 15px; padding-top: 15px; border-top: 1px solid #e2e8f0;">
                        <div style="display: flex; justify-content: space-between; font-size: 12px;">
                            <strong id="gpuName">GPU Loading...</strong>
                            <span id="vramText">0/0 GB Used</span>
                        </div>
                        <div class="vram-container">
                            <div id="vramBar" class="vram-bar"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">🖼️ Gallery</div>
                <div id="images" style="min-height: 300px; padding: 20px;">
                    <div style="text-align: center; color: #9ca3af;">Generated images will appear here.</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Slider Value Syncing
    const setupSlider = (id, valId) => {
        const slider = document.getElementById(id);
        const display = document.getElementById(valId);
        if (slider && display) {
            slider.oninput = () => display.innerText = slider.value;
        }
    };
    setupSlider('cnStrength', 'strengthVal');

    // 2. Preprocessor Visibility
    window.updatePreprocessorSettings = function(id) {
        console.log("Selected Preprocessor:", id);
        // Add logic to show specific settings divs if you have them
    };

    // 3. Health Check Logic (The Request)
    const healthBtn = document.getElementById('checkHealthBtn');
    const statusDiv = document.getElementById('modelStatus');
    const vramPanel = document.getElementById('vramStatus');

    if (healthBtn) {
        healthBtn.onclick = async () => {
            statusDiv.innerHTML = '🔄 Contacting Laravel Proxy...';
            statusDiv.style.color = '#4b5563';
            
            try {
                // Pointing to your Laravel Route
                const response = await fetch('/api/comfyui/health');
                const data = await response.json();
                
                if (data && data.success) {
                    statusDiv.innerHTML = `✅ <strong>ComfyUI Online</strong><br>Proxy route verified.`;
                    statusDiv.style.color = '#059669';
                    
                    if (data.vram_total) {
                        vramPanel.style.display = 'block';
                        const totalGB = (data.vram_total / (1024 ** 3)).toFixed(1);
                        const freeGB = (data.vram_free / (1024 ** 3)).toFixed(1);
                        const usedGB = (totalGB - freeGB).toFixed(1);
                        const percent = Math.round((usedGB / totalGB) * 100);
                        
                        document.getElementById('vramText').innerText = `${usedGB} / ${totalGB} GB Used`;
                        document.getElementById('vramBar').style.width = `${percent}%`;
                        document.getElementById('gpuName').innerText = data.gpu_name || 'NVIDIA GPU';
                        
                        // Change bar color if VRAM is high
                        const bar = document.getElementById('vramBar');
                        bar.style.background = percent > 85 ? '#ef4444' : (percent > 60 ? '#f59e0b' : '#10b981');
                    }
                } else {
                    throw new Error(data.error || 'The Proxy received an invalid response.');
                }
            } catch (error) {
                statusDiv.innerHTML = `❌ <strong>Connection Failed</strong><br>${error.message}`;
                statusDiv.style.color = '#dc3545';
                vramPanel.style.display = 'none';
            }
        };
    }
});
</script>

@include('partials.footer')