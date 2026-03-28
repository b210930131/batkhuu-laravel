<div class="card" id="controlnet-main-container">
    <div class="card-header" style="display: flex; align-items: center; justify-content: space-between;">
        <span>🎮 ControlNet Preprocessors</span>
        <div id="controlnetDot" style="width: 10px; height: 10px; border-radius: 50%; background: #ef4444; transition: background 0.3s;"></div>
    </div>
    
    <div style="padding: 20px;">
        <div class="preprocessor-grid" id="preprocessorList" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(90px, 1fr)); gap: 8px; margin-bottom: 20px;">
            </div>

        <!-- <div class="control-group" style="background: #f8fafc; border: 2px dashed #e2e8f0; border-radius: 12px; padding: 15px; text-align: center; margin-bottom: 20px;">
            <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 10px; color: #475569;">📤 Upload Control Image</label>
            <input type="file" id="controlImageInput" accept="image/*" style="font-size: 11px; width: 100%;">
            <div id="controlPreview" style="margin-top: 10px; border-radius: 8px; overflow: hidden; background: white; min-height: 40px; display: flex; align-items: center; justify-content: center; border: 1px solid #f1f5f9;">
                <span style="color: #cbd5e1; font-size: 10px;">No preview</span>
            </div>
        </div> -->

        <div class="control-group" style="border: none; padding: 0;">
            <div style="margin-bottom: 15px;">
                <div style="display: flex; justify-content: space-between;">
                    <label style="font-size: 0.8rem; font-weight: bold;">💪 Strength</label>
                    <span id="strengthVal" style="font-weight: bold; color: #6366f1;">0.85</span>
                </div>
                <input type="range" id="cnStrength" min="0" max="2" step="0.01" value="0.85" style="width: 100%;">
            </div>
            
            <div style="display: flex; gap: 12px;">
                <div style="flex: 1;">
                    <div style="display: flex; justify-content: space-between;">
                        <label style="font-size: 0.7rem; color: #64748b;">▶️ Start %</label>
                        <span id="startVal" style="font-size: 0.7rem; font-weight: bold;">0.00</span>
                    </div>
                    <input type="range" id="cnStart" min="0" max="1" value="0.00" step="0.01" style="width: 100%;">
                </div>
                
                <div style="flex: 1;">
                    <div style="display: flex; justify-content: space-between;">
                        <label style="font-size: 0.7rem; color: #64748b;">⏹️ End %</label>
                        <span id="endVal" style="font-size: 0.7rem; font-weight: bold;">1.00</span>
                    </div>
                    <input type="range" id="cnEnd" min="0" max="1" value="1.00" step="0.01" style="width: 100%;">
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Define preprocessors
    const preprocessors = [
        { id: 'canny', name: 'Canny', icon: '🔲' },
        { id: 'depth', name: 'Depth', icon: '🗺️' },
        { id: 'openpose', name: 'Pose', icon: '🧍' },
        { id: 'scribble', name: 'Sketch', icon: '✏️' },
        { id: 'mlsd', name: 'MLSD', icon: '📐' },
        { id: 'hed', name: 'SoftEdge', icon: '🎨' },
        { id: 'seg', name: 'Seg', icon: '🏞️' },
        { id: 'normal', name: 'Normal', icon: '⚡' }
    ];

    window.selectedPreprocessor = 'canny';

    // // 2. Build Grid
    // const grid = document.getElementById('preprocessorList');
    // preprocessors.forEach(p => {
    //     const btn = document.createElement('button');
    //     btn.className = `btn-secondary preprocessor-btn ${p.id === window.selectedPreprocessor ? 'active' : ''}`;
    //     btn.style.padding = '8px 4px';
    //     btn.style.fontSize = '11px';
    //     btn.innerHTML = `<div style="font-size: 16px;">${p.icon}</div><div>${p.name}</div>`;
    //     btn.onclick = () => {
    //         document.querySelectorAll('.preprocessor-btn').forEach(b => b.classList.remove('active'));
    //         btn.classList.add('active');
    //         window.selectedPreprocessor = p.id;
    //         console.log("Selected:", p.id);
    //         if(window.updatePreprocessorSettings) window.updatePreprocessorSettings(p.id);
    //     };
    //     grid.appendChild(btn);
    // });

    // 3. UI Event Listeners for Sliders
    document.getElementById('cnStrength').addEventListener('input', (e) => {
        document.getElementById('strengthVal').textContent = e.target.value;
    });

    document.getElementById('cnStart').addEventListener('input', (e) => {
        document.getElementById('startVal').textContent = parseFloat(e.target.value).toFixed(2);
    });

    document.getElementById('cnEnd').addEventListener('input', (e) => {
        document.getElementById('endVal').textContent = parseFloat(e.target.value).toFixed(2);
    });

    // 4. Handle Image Upload & Dot Status
    document.getElementById('controlImageInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (ev) => {
                document.getElementById('controlPreview').innerHTML = `<img src="${ev.target.result}" style="width:100%; border-radius:4px;">`;
                document.getElementById('controlnetDot').style.background = '#10b981'; // Green for active
            };
            reader.readAsDataURL(file);
        } else {
            document.getElementById('controlnetDot').style.background = '#ef4444'; // Red for empty
        }
    });
</script>

<style>
    .preprocessor-btn {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .preprocessor-btn.active {
        background: #6366f1 !important;
        color: white !important;
        border-color: #4f46e5 !important;
        box-shadow: 0 2px 4px rgba(99, 102, 241, 0.3);
    }
    .preprocessor-btn:hover:not(.active) {
        background: #f8fafc;
        border-color: #cbd5e1;
    }
</style>