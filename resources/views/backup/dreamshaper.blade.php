<!DOCTYPE html>
<html>
<head>
    <title>ComfyUI Laravel - Dreamshaper8</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        h1 { color: #333; }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .model-selector, .prompt-input, .settings {
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        select, textarea, input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        textarea {
            font-family: monospace;
        }
        .settings input {
            width: auto;
            margin-right: 10px;
        }
        button {
            background: #4CAF50;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 0;
        }
        button:hover { background: #45a049; }
        button:disabled { background: #ccc; cursor: not-allowed; }
        #status {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            background: #e3f2fd;
            color: #1976d2;
            font-weight: bold;
        }
        .error { background: #ffebee; color: #c62828; }
        .success { background: #e8f5e9; color: #2e7d32; }
        #images {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .image-container {
            background: white;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        img {
            width: 100%;
            height: auto;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .download-btn {
            background: #2196F3;
            padding: 8px 16px;
            font-size: 14px;
        }
        .download-btn:hover { background: #1976D2; }
    </style>
</head>
<body>

<h1>🎨 AI Image Generator with Dreamshaper8</h1>

<div class="container">
    <div class="model-selector">
        <label>🎨 Model сонгох:</label>
        <select id="model">
            <option value="dreamshaper_8.safetensors">Dreamshaper 8 (Сайн чанар, хурдан)</option>
            <option value="realisticVisionV60B1_v51HyperVAE.safetensors">Realistic Vision (Фото реал)</option>
            <option value="sd3.5_large_fp8_scaled.safetensors">SD 3.5 Large (Шинэ)</option>
            <option value="flux1-dev-fp8.safetensors">Flux Dev (Өндөр чанар)</option>
        </select>
    </div>

    <div class="prompt-input">
        <label>✨ Positive Prompt:</label>
        <textarea id="positive_prompt" rows="3">
masterpiece, best quality, beautiful woman, detailed face, fantasy art, magical forest, glowing flowers, ethereal lighting, 8k, highly detailed, vibrant colors
        </textarea>
    </div>

    <div class="prompt-input">
        <label>❌ Negative Prompt:</label>
        <textarea id="negative_prompt" rows="2">
worst quality, low quality, blurry, distorted, ugly, bad anatomy, watermark, signature, extra limbs, fused fingers, mutated hands
        </textarea>
    </div>

    <div class="settings">
        <label>⚙️ Settings:</label>
        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
            <div>
                <span>Steps: </span>
                <input type="number" id="steps" value="25" min="1" max="100" style="width: 80px;">
            </div>
            <div>
                <span>CFG: </span>
                <input type="number" id="cfg" value="7.0" step="0.5" min="1" max="20" style="width: 80px;">
            </div>
            <div>
                <span>Width: </span>
                <input type="number" id="width" value="512" min="256" max="1024" step="64" style="width: 80px;">
            </div>
            <div>
                <span>Height: </span>
                <input type="number" id="height" value="512" min="256" max="1024" step="64" style="width: 80px;">
            </div>
            <div>
                <span>Sampler: </span>
                <select id="sampler" style="width: 150px;">
                    <option value="euler">Euler</option>
                    <option value="euler_ancestral">Euler Ancestral</option>
                    <option value="dpmpp_2m">DPM++ 2M</option>
                    <option value="dpmpp_2m_karras">DPM++ 2M Karras</option>
                    <option value="ddim">DDIM</option>
                    <option value="lcm">LCM</option>
                </select>
            </div>
        </div>
    </div>

    <button onclick="generate()" id="generateBtn">🚀 Generate Image</button>
    <div id="status">✅ Бэлэн</div>
</div>

<div id="images"></div>

<script>
let clientId = Math.random().toString(36).substring(7);
let ws = null;
let isGenerating = false;

// Нэгдмэл хаяг ашиглах - localhost эсвэл 127.0.0.1
// Хоёулаа ажиллахын тулд window.location.hostname ашиглах
const baseUrl = window.location.hostname; // 'localhost' эсвэл '127.0.0.1'
const comfyuiUrl = baseUrl; // Эсвэл '127.0.0.1' гэж тогтоох

console.log('Using base URL:', baseUrl);
console.log('ComfyUI URL:', comfyuiUrl);

// WebSocket холболт
function connectWebSocket() {
    const wsUrl = `ws://${comfyuiUrl}:8188/ws?clientId=${clientId}`;
    console.log('Connecting to:', wsUrl);
    
    if (ws && ws.readyState !== WebSocket.CLOSED) {
        ws.close();
    }
    
    ws = new WebSocket(wsUrl);
    
    ws.onopen = () => {
        console.log("✅ WebSocket холбогдлоо");
        document.getElementById("status").innerHTML = "✅ WebSocket холбогдсон - Бэлэн";
        document.getElementById("status").className = "success";
    };
    
    ws.onmessage = (event) => {
        try {
            let data = JSON.parse(event.data);
            console.log("📨 Мессеж:", data);
            
            if (data.type === 'executing') {
                if (data.data && data.data.node) {
                    document.getElementById("status").innerHTML = `🎨 Генерац хийж байна... (${data.data.node})`;
                } else {
                    document.getElementById("status").innerHTML = `✅ Генерац дууслаа!`;
                    document.getElementById("status").className = "success";
                    isGenerating = false;
                    document.getElementById("generateBtn").disabled = false;
                }
            }
            
            if (data.type === "executed" && data.data?.output?.images) {
                let images = data.data.output.images;
                console.log("🖼 Зураг ирлээ:", images.length);
                
                images.forEach(img => {
                    const imageContainer = document.createElement("div");
                    imageContainer.className = "image-container";
                    
                    const image = document.createElement("img");
                    image.src = `http://${comfyuiUrl}:8188/view?filename=${img.filename}&timestamp=${Date.now()}`;
                    
                    const downloadBtn = document.createElement("button");
                    downloadBtn.textContent = "💾 Download";
                    downloadBtn.className = "download-btn";
                    downloadBtn.onclick = () => {
                        const link = document.createElement("a");
                        link.href = image.src;
                        link.download = `dreamshaper_${Date.now()}.png`;
                        link.click();
                    };
                    
                    imageContainer.appendChild(image);
                    imageContainer.appendChild(downloadBtn);
                    document.getElementById("images").prepend(imageContainer);
                });
            }
            
        } catch (error) {
            console.error("WebSocket parse алдаа:", error);
        }
    };
    
    ws.onerror = (error) => {
        console.error("❌ WebSocket алдаа:", error);
        document.getElementById("status").innerHTML = "❌ WebSocket алдаа гарлаа";
        document.getElementById("status").className = "error";
    };
    
    ws.onclose = (event) => {
        console.log("❌ WebSocket хаагдлаа. Code:", event.code);
        document.getElementById("status").innerHTML = "⚠️ WebSocket холболт тасарсан. Дахин холбогдож байна...";
        document.getElementById("status").className = "error";
        setTimeout(connectWebSocket, 5000);
    };
}

// Generate функц
async function generate() {
    if (isGenerating) {
        alert("Зураг үүсгэж байна. Түр хүлээнэ үү.");
        return;
    }
    
    isGenerating = true;
    const btn = document.getElementById("generateBtn");
    btn.disabled = true;
    
    const data = {
        client_id: clientId,
        model: document.getElementById('model').value,
        positive_prompt: document.getElementById('positive_prompt').value,
        negative_prompt: document.getElementById('negative_prompt').value,
        steps: parseInt(document.getElementById('steps').value),
        cfg: parseFloat(document.getElementById('cfg').value),
        width: parseInt(document.getElementById('width').value),
        height: parseInt(document.getElementById('height').value),
        sampler: document.getElementById('sampler').value
    };
    
    document.getElementById("status").innerHTML = "🚀 Prompt илгээж байна...";
    document.getElementById("status").className = "";
    
    try {
        const response = await fetch('/generate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            document.getElementById("status").innerHTML = "✅ Prompt амжилттай илгээгдлээ! Зураг үүсгэж байна...";
            document.getElementById("status").className = "success";
        } else {
            throw new Error(result.error || 'Алдаа гарлаа');
        }
        
    } catch (error) {
        console.error("❌ Алдаа:", error);
        document.getElementById("status").innerHTML = `❌ Алдаа: ${error.message}`;
        document.getElementById("status").className = "error";
        isGenerating = false;
        btn.disabled = false;
    }
}

// ComfyUI-г шалгах
async function checkComfyUI() {
    const urls = [
        `http://${window.location.hostname}:8188/`,
        'http://localhost:8188/',
        'http://127.0.0.1:8188/'
    ];
    
    for (const url of urls) {
        try {
            const response = await fetch(url, { 
                method: 'HEAD',
                cache: 'no-cache',
                mode: 'cors'
            });
            if (response.ok) {
                console.log(`✅ ComfyUI found at: ${url}`);
                return url.split('//')[1].split(':')[0];
            }
        } catch(e) {
            console.log(`❌ Not available: ${url}`);
        }
    }
    return null;
}

// Хуудас ачаалахад
document.addEventListener('DOMContentLoaded', async () => {
    const ip = await checkComfyUI();
    if (ip) {
        // Global variable өөрчлөх
        window.comfyuiIp = ip;
        connectWebSocket();
    } else {
        document.getElementById("status").innerHTML = "❌ ComfyUI сервер олдсонгүй. Сервер ажиллаж байгаа эсэхийг шалгаарай.";
        document.getElementById("status").className = "error";
    }
});
</script>

</body>
</html>