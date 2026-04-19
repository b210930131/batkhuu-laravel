<div style="display: flex; flex-direction: column; gap: 20px;">
    
    <div class="card" style="border: 1px solid #e2e8f0; padding: 15px; border-radius: 10px;">
        <div class="control-group">
            <label>🎭 Model</label>
            <select id="model_select" style="width: 100%; padding: 8px;">
                <option value="dreamshaper_8.safetensors">Dreamshaper 8</option>
                <option value="realisticVisionV60B1.safetensors">Realistic Vision</option>
            </select>
        </div>
        <div class="control-group" style="margin-top:10px;">
            <label>✨ Prompt</label>
            <textarea id="pos_prompt" style="width: 100%;" rows="3">masterpiece, ultra high res</textarea>
        </div>
    </div>

    <div class="card" style="border: 1px solid #e2e8f0; padding: 15px; border-radius: 10px;">
        <label>🎨 Preprocessor</label>
        <div id="prep_grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px;">
            <button type="button" class="prep-btn active" data-id="canny">🔲 Canny</button>
            <button type="button" class="prep-btn" data-id="depth">🗺️ Depth</button>
            <button type="button" class="prep-btn" data-id="openpose">🧍 Pose</button>
            <button type="button" class="prep-btn" data-id="mlsd">📐 MLSD</button>
        </div>
    </div>

    <div class="card" style="border: 1px solid #e2e8f0; padding: 15px; border-radius: 10px;">
        <label>🖼️ Control Image</label>
        <input type="file" id="image_input" style="margin-top: 10px;">
        <div id="preview_box" style="margin-top:10px; border:1px dashed #ccc; height:100px; display:flex; align-items:center; justify-content:center;">Preview</div>
    </div>

    <button id="main_gen_btn" style="background: #4f46e5; color: white; padding: 15px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">
        🚀 GENERATE IMAGE
    </button>
    
    <div id="status_log" style="background: #1e293b; color: #38bdf8; padding: 10px; font-family: monospace; font-size: 12px; height: 60px; overflow-y: auto; margin-top: 10px;">
        > System Online
    </div>
</div>

<style>
    .prep-btn { padding: 10px; border: 1px solid #cbd5e1; background: white; border-radius: 6px; cursor: pointer; }
    .prep-btn.active { background: #4f46e5; color: white; border-color: #4f46e5; }
</style>

<script>
// We use a self-invoking function to avoid global variable conflicts
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        console.log("Generator Ready");
        
        let currentPrep = 'canny';
        const log = (m) => { document.getElementById('status_log').innerHTML += `<div>> ${m}</div>`; };

        // 1. Preprocessor Toggle
        document.querySelectorAll('.prep-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.prep-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentPrep = this.dataset.id;
                log("Mode: " + currentPrep);
            });
        });

        // 2. Image Preview
        const imgInput = document.getElementById('image_input');
        imgInput.onchange = function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    document.getElementById('preview_box').innerHTML = `<img src="${ev.target.result}" style="height:100%; border-radius:4px;">`;
                };
                reader.readAsDataURL(file);
            }
        };

        // 3. The Actual Fetch
        const genBtn = document.getElementById('main_gen_btn');
        genBtn.onclick = async function() {
            log("Generating...");
            this.disabled = true;
            this.innerText = "⏳ Wait...";

            const formData = new FormData();
            formData.append('model', document.getElementById('model_select').value);
            formData.append('prompt', document.getElementById('pos_prompt').value);
            formData.append('preprocessor', currentPrep);
            
            const file = document.getElementById('image_input').files[0];
            if (file) formData.append('control_image', file);

            // Fetch CSRF Token from meta tag
            const token = document.querySelector('meta[name="csrf-token"]')?.content;

            try {
                const response = await fetch('/api/generate', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': token }
                });

                const data = await response.json();
                if (data.success) {
                    log("Success!");
                    // Update your image gallery here
                } else {
                    log("Server Error: " + (data.message || "Unknown"));
                }
            } catch (err) {
                log("Connection Failed: Check Console");
                console.error(err);
            } finally {
                this.disabled = false;
                this.innerText = "🚀 GENERATE IMAGE";
            }
        };
    });
})();
</script>