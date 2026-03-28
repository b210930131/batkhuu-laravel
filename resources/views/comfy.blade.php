

    <!-- LEFT: Generation params -->
      @include('partials.styles2')
    @include('partials.generation-form')

    <!-- RIGHT: ControlNet preprocessor selector + image input -->
    @include('partials.controlnet-panel')


<script>async function startGeneration() {
    const btn = document.getElementById('generateBtn');
    btn.disabled = true;
    btn.innerText = "⏳ Processing...";

    // 1. Get Base Parameters (from generation-form)
    const baseData = {
        model: document.getElementById('model').value,
        positive_prompt: document.getElementById('positive_prompt').value,
        negative_prompt: document.getElementById('negative_prompt').value,
        steps: parseInt(document.getElementById('steps').value),
        cfg: parseFloat(document.getElementById('cfg').value),
        width: parseInt(document.getElementById('width').value),
        height: parseInt(document.getElementById('height').value),
        sampler: document.getElementById('sampler').value,
    };

    // 2. Get ControlNet Parameters (from controlnet-panel)
    const fileInput = document.getElementById('controlImageInput');
    const controlnet = {
        enabled: false,
        preprocessor: selectedPreprocessor, // From your panel script
        strength: parseFloat(document.getElementById('cnStrength').value),
        start_percent: parseFloat(document.getElementById('cnStart').value || 0),
        end_percent: parseFloat(document.getElementById('cnEnd').value || 1),
        image_base64: null
    };

    // 3. Handle Image Conversion
    if (fileInput.files.length > 0) {
        controlnet.enabled = true;
        const file = fileInput.files[0];
        controlnet.image_base64 = await new Promise((resolve) => {
            const reader = new FileReader();
            reader.onload = (e) => resolve(e.target.result);
            reader.readAsDataURL(file);
        });
    }

    // 4. Send to Laravel Backend
    try {
        const response = await fetch('/api/generate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ ...baseData, controlnet })
        });

        const result = await response.json();
        if (result.success) {
            console.log("Generation started! Prompt ID:", result.prompt_id);
            // Trigger your status poller here
        } else {
            alert("Error: " + result.error);
        }
    } catch (e) {
        console.error(e);
    } finally {
        btn.disabled = false;
        btn.innerText = "🚀 Generate Image";
    }
}

// Attach to your button
document.getElementById('generateBtn').addEventListener('click', startGeneration);  </script>