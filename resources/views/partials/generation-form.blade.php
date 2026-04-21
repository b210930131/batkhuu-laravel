<div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1.35fr)_minmax(320px,0.9fr)]">
    <!-- LEFT SIDE -->
    <div class="space-y-6">
        <!-- Main Configuration -->
        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 bg-gradient-to-r from-slate-950 via-slate-900 to-indigo-950 px-6 py-5 text-white">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold">Model & Prompt Configuration</h2>
                        <p class="mt-1 text-sm text-slate-300">Stylish AI image generation workspace</p>
                    </div>
                    <span class="rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs font-medium text-slate-100">
                        Studio
                    </span>
                </div>
            </div>

            <div class="space-y-6 p-6">
                <!-- Model -->
                <div class="space-y-2">
                    <label for="model" class="block text-sm font-medium text-slate-700">Model Selection</label>
                    <select id="model"
                        class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
                        <option value="dreamshaper_8.safetensors" selected>✨ Dreamshaper 8 (SD1.5, artistic, ControlNet ready)</option>
                        <option value="v1-5-pruned-emaonly-fp16.safetensors">📦 v1-5-pruned-emaonly-fp16 (SD1.5, FP16, low VRAM)</option>
                        <option value="v1-5-pruned.safetensors">📦 v1-5-pruned (SD1.5, standard)</option>
                        <option value="realisticVisionV60B1_v51HyperVAE.safetensors">🎭 Realistic Vision V6.0 (SD1.5, photorealistic)</option>
                        <option value="sd_xl_base_1.0.safetensors">🎨 SDXL Base 1.0 (high quality, 1024x1024)</option>
                        <option value="sd_xl_refiner_1.0.safetensors">🔧 SDXL Refiner 1.0 (detail enhancement)</option>
                        <option value="flux1-dev-fp8.safetensors">⚡ Flux Dev FP8 (experimental, high VRAM)</option>
                        <option value="sd3.5_large_fp8_scaled.safetensors">🌀 SD3.5 Large FP8 (requires >8GB VRAM)</option>
                        <option value="qwen_image_2512_fp8_e4m3fn.safetensors">🖼️ Qwen Image 2.5 (experimental)</option>
                    </select>

                    <div id="vram-warning"
                        class="hidden rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                        ⚠️ Warning: Selected model may exceed VRAM
                    </div>
                </div>

                <!-- Refiner -->
                <div id="refinerGroup" class="hidden rounded-2xl border border-indigo-100 bg-indigo-50/70 p-4">
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label for="refiner_model" class="block text-sm font-medium text-slate-700">SDXL Refiner Model</label>
                            <select id="refiner_model"
                                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                                <option value="sd_xl_refiner_1.0.safetensors">SDXL Refiner 1.0</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label for="refiner_steps" class="block text-sm font-medium text-slate-700">Refiner Steps</label>
                            <input type="number" id="refiner_steps" value="15" min="1" max="100"
                                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                            <p class="text-xs text-slate-500">Refiner adds details after base generation.</p>
                        </div>

                        <div id="resolutionHint" class="hidden rounded-xl bg-white px-4 py-3 text-xs text-slate-600"></div>
                    </div>
                </div>

                <!-- Prompts -->
                <div class="grid grid-cols-1 gap-5">
                    <div class="space-y-2">
                        <label for="positive_prompt" class="block text-sm font-medium text-slate-700">Positive Prompt</label>
                        <textarea id="positive_prompt" rows="4"
                            class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">masterpiece, architectural photography, modern building exterior, golden hour lighting, luxury facade, high-quality rendering, sharp focus, volumetric lighting</textarea>
                    </div>

                    <div class="space-y-2">
                        <label for="negative_prompt" class="block text-sm font-medium text-slate-700">Negative Prompt</label>
                        <textarea id="negative_prompt" rows="3"
                            class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">worst quality, low quality, blurry, distorted, cartoonish, ugly</textarea>
                    </div>
                </div>

                <!-- Parameters -->
                <div class="grid grid-cols-2 gap-4 lg:grid-cols-3">
                    <div class="space-y-2">
                        <label for="steps" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Steps</label>
                        <input type="number" id="steps" value="20" min="1" max="100"
                            class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
                    </div>

                    <div class="space-y-2">
                        <label for="cfg" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">CFG Scale</label>
                        <input type="number" id="cfg" value="7.0" step="0.5" min="1" max="20"
                            class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
                    </div>

                    <div class="space-y-2">
                        <label for="width" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Width</label>
                        <input type="number" id="width" value="512" min="256" max="1536" step="64"
                            class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
                    </div>

                    <div class="space-y-2">
                        <label for="height" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Height</label>
                        <input type="number" id="height" value="512" min="256" max="1536" step="64"
                            class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
                    </div>

                    <div class="space-y-2">
                        <label for="sampler" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Sampler</label>
                        <select id="sampler"
                            class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
                            <option value="euler">Euler</option>
                            <option value="euler_ancestral">Euler Ancestral</option>
                            <option value="dpmpp_2m" selected>DPM++ 2M</option>
                            <option value="dpmpp_2m_sde">DPM++ 2M SDE</option>
                            <option value="ddim">DDIM</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="scheduler" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Scheduler</label>
                        <select id="scheduler"
                            class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
                            <option value="normal">Normal</option>
                            <option value="karras" selected>Karras</option>
                            <option value="exponential">Exponential</option>
                            <option value="sgm_uniform">SGM Uniform</option>
                            <option value="simple">Simple</option>
                        </select>
                    </div>
                </div>

                <!-- Upload -->
                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50/70 p-5">
                    <div class="space-y-3">
                        <label for="controlImageInput" class="block text-sm font-medium text-slate-700">Control Image</label>
                        <input type="file" id="controlImageInput" accept="image/*"
                            class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-600 file:mr-4 file:rounded-xl file:border-0 file:bg-indigo-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-indigo-700">
                        <p class="text-xs text-slate-500">Upload an image to guide the generation.</p>
                        <div id="controlPreview" class="overflow-hidden rounded-2xl"></div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex flex-col gap-3 md:flex-row">
                    <button id="generateBtn"
                        class="inline-flex flex-1 items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 px-5 py-3.5 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:from-indigo-700 hover:to-violet-700 disabled:cursor-not-allowed disabled:opacity-70">
                        🚀 Generate Image
                    </button>

                    <button id="refreshGalleryBtn"
                        class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-5 py-3.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        🔄 Refresh Gallery
                    </button>

                    <button id="interruptBtn"
                        class="inline-flex items-center justify-center rounded-2xl bg-rose-500 px-5 py-3.5 text-sm font-semibold text-white transition hover:bg-rose-600">
                        ⏹️ Interrupt
                    </button>
                </div>

                <!-- Status -->
                <div id="status"
                    class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    ✅ Ready • ControlNet inactive
                </div>
            </div>
        </section>

        <!-- Preprocessors -->
        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 bg-gradient-to-r from-slate-900 to-slate-800 px-6 py-5 text-white">
                <h2 class="text-lg font-semibold">ControlNet Preprocessors</h2>
            </div>
            <div class="p-6">
                <div id="preprocessorList" class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4"></div>
            </div>
        </section>

        <!-- Settings -->
        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 bg-gradient-to-r from-slate-900 to-slate-800 px-6 py-5 text-white">
                <h2 class="text-lg font-semibold">Preprocessor Settings</h2>
            </div>

            <div id="preprocessorSettings" class="p-6">
                <div id="cannySettings" class="preprocessor-settings hidden space-y-5 rounded-2xl bg-slate-50 p-5">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-medium text-slate-700">Canny Low Threshold</label>
                            <span id="cannyLowVal" class="text-sm font-semibold text-indigo-600">50</span>
                        </div>
                        <input type="range" id="cannyLowThreshold" min="0" max="255" value="50" step="1" class="w-full accent-indigo-600">
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-medium text-slate-700">Canny High Threshold</label>
                            <span id="cannyHighVal" class="text-sm font-semibold text-indigo-600">150</span>
                        </div>
                        <input type="range" id="cannyHighThreshold" min="0" max="255" value="150" step="1" class="w-full accent-indigo-600">
                    </div>
                </div>

                <div id="depthSettings" class="preprocessor-settings hidden rounded-2xl bg-slate-50 p-5">
                    <div class="space-y-2">
                        <label for="depthResolution" class="block text-sm font-medium text-slate-700">Depth Resolution</label>
                        <select id="depthResolution" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                            <option value="512">512</option>
                            <option value="1024">1024</option>
                        </select>
                    </div>
                </div>

                <div id="openposeSettings" class="preprocessor-settings hidden rounded-2xl bg-slate-50 p-5">
                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-slate-700">Detection Features</label>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                            <label class="flex items-center gap-2 rounded-xl bg-white px-4 py-3 text-sm text-slate-700 shadow-sm">
                                <input type="checkbox" id="openposeHands" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                Hands
                            </label>
                            <label class="flex items-center gap-2 rounded-xl bg-white px-4 py-3 text-sm text-slate-700 shadow-sm">
                                <input type="checkbox" id="openposeBody" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                Body
                            </label>
                            <label class="flex items-center gap-2 rounded-xl bg-white px-4 py-3 text-sm text-slate-700 shadow-sm">
                                <input type="checkbox" id="openposeFace" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                Face
                            </label>
                        </div>
                    </div>
                </div>

                <div id="mlsdSettings" class="preprocessor-settings hidden space-y-5 rounded-2xl bg-slate-50 p-5">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-medium text-slate-700">Score Threshold</label>
                            <span id="mlsdScoreVal" class="text-sm font-semibold text-indigo-600">0.10</span>
                        </div>
                        <input type="range" id="mlsdScoreThr" min="0" max="1" value="0.1" step="0.01" class="w-full accent-indigo-600">
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-medium text-slate-700">Distance Threshold</label>
                            <span id="mlsdDistVal" class="text-sm font-semibold text-indigo-600">0.10</span>
                        </div>
                        <input type="range" id="mlsdDistThr" min="0" max="0.5" value="0.1" step="0.01" class="w-full accent-indigo-600">
                    </div>
                </div>

                <div id="scribbleSettings" class="preprocessor-settings hidden rounded-2xl bg-slate-50 p-5">
                    <div class="space-y-2">
                        <label for="scribbleMode" class="block text-sm font-medium text-slate-700">Scribble Mode</label>
                        <select id="scribbleMode" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                            <option value="hed">HED</option>
                            <option value="pidi">PIDI</option>
                        </select>
                    </div>
                </div>

                <div id="hedSettings" class="preprocessor-settings hidden rounded-2xl bg-slate-50 p-5">
                    <div class="space-y-2">
                        <label for="hedResolution" class="block text-sm font-medium text-slate-700">HED Resolution</label>
                        <select id="hedResolution" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                            <option value="512">512</option>
                            <option value="1024">1024</option>
                        </select>
                    </div>
                </div>

                <div id="segSettings" class="preprocessor-settings hidden rounded-2xl bg-slate-50 p-5">
                    <div class="space-y-2">
                        <label for="segResolution" class="block text-sm font-medium text-slate-700">SEG Resolution</label>
                        <select id="segResolution" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                            <option value="512">512</option>
                            <option value="1024">1024</option>
                        </select>
                    </div>
                </div>

                <div id="normalSettings" class="preprocessor-settings hidden rounded-2xl bg-slate-50 p-5">
                    <div class="space-y-2">
                        <label for="normalResolution" class="block text-sm font-medium text-slate-700">Normal Resolution</label>
                        <select id="normalResolution" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                            <option value="512">512</option>
                            <option value="1024">1024</option>
                        </select>
                    </div>
                </div>
            </div>
        </section>

        <!-- Strength -->
        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 bg-gradient-to-r from-slate-900 to-slate-800 px-6 py-5 text-white">
                <h2 class="text-lg font-semibold">ControlNet Strength</h2>
            </div>

            <div class="space-y-5 p-6">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-medium text-slate-700">Strength</label>
                        <span id="strengthVal" class="text-sm font-semibold text-indigo-600">0.85</span>
                    </div>
                    <input type="range" id="cnStrength" min="0" max="2" step="0.01" value="0.85" class="w-full accent-indigo-600">
                </div>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-medium text-slate-700">Start %</label>
                            <span id="startVal" class="text-sm font-semibold text-indigo-600">0.00</span>
                        </div>
                        <input type="range" id="cnStart" min="0" max="1" value="0" step="0.01" class="w-full accent-indigo-600">
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-medium text-slate-700">End %</label>
                            <span id="endVal" class="text-sm font-semibold text-indigo-600">1.00</span>
                        </div>
                        <input type="range" id="cnEnd" min="0" max="1" value="1" step="0.01" class="w-full accent-indigo-600">
                    </div>
                </div>

                <div id="controlnetStatus" class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="flex items-center gap-3">
                        <div id="controlnetDot" class="h-3 w-3 rounded-full bg-rose-500"></div>
                        <span id="controlnetText" class="text-sm font-medium text-slate-800">No control image uploaded</span>
                    </div>
                    <p id="controlnetDetails" class="mt-3 text-xs leading-5 text-slate-500">
                        Upload an image and select a preprocessor to enable ControlNet.
                    </p>
                </div>
            </div>
        </section>
    </div>

    <!-- RIGHT SIDE -->
    <div class="space-y-6">
        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 bg-gradient-to-r from-slate-950 via-slate-900 to-indigo-950 px-6 py-5 text-white">
                <h2 class="text-lg font-semibold">Generated Images Gallery</h2>
            </div>

            <div id="images" class="grid grid-cols-1 gap-4 p-6 sm:grid-cols-2">
                <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-16 text-center text-slate-400">
                    🎨 No images yet<br>
                    <span class="text-xs">Your generated images will appear here</span>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
let currentPreprocessor = 'canny';
let currentControlImage = null;
let isGenerating = false;

const preprocessors = [
    { id: 'canny', name: 'Canny', icon: '🔲' },
    { id: 'depth', name: 'Depth', icon: '🗺️' },
    { id: 'openpose', name: 'OpenPose', icon: '🧍' },
    { id: 'mlsd', name: 'MLSD', icon: '📐' },
    { id: 'scribble', name: 'Scribble', icon: '✏️' },
    { id: 'hed', name: 'HED', icon: '🎨' },
    { id: 'seg', name: 'SEG', icon: '🏷️' },
    { id: 'normal', name: 'Normal', icon: '📐' }
];

function getPreprocessorName(id) {
    const names = {
        canny: 'Canny Edge',
        depth: 'Depth Map',
        openpose: 'OpenPose',
        mlsd: 'MLSD',
        scribble: 'Scribble',
        hed: 'HED',
        seg: 'Segmentation',
        normal: 'Normal Map'
    };
    return names[id] || id;
}

function setStatus(message, type = 'success') {
    const statusDiv = document.getElementById('status');
    if (!statusDiv) return;

    const styles = {
        success: 'border-emerald-200 bg-emerald-50 text-emerald-700',
        warning: 'border-amber-200 bg-amber-50 text-amber-700',
        error: 'border-rose-200 bg-rose-50 text-rose-700',
        info: 'border-indigo-200 bg-indigo-50 text-indigo-700'
    };

    statusDiv.className = `rounded-2xl border px-4 py-3 text-sm font-medium ${styles[type] || styles.success}`;
    statusDiv.textContent = message;
}

function setButtonLoading(btn, loading, loadingText = 'Processing...') {
    if (!btn) return;

    if (loading) {
        btn.dataset.originalText = btn.innerHTML;
        btn.innerHTML = loadingText;
        btn.disabled = true;
        btn.classList.add('opacity-70', 'cursor-not-allowed');
    } else {
        btn.innerHTML = btn.dataset.originalText || 'Submit';
        btn.disabled = false;
        btn.classList.remove('opacity-70', 'cursor-not-allowed');
    }
}

function updateControlNetStatus() {
    const dot = document.getElementById('controlnetDot');
    const text = document.getElementById('controlnetText');
    const details = document.getElementById('controlnetDetails');
    const hasImage = currentControlImage !== null;

    if (!dot || !text || !details) return;

    if (hasImage) {
        dot.className = 'h-3 w-3 rounded-full bg-emerald-500';
        text.textContent = `ControlNet Active: ${getPreprocessorName(currentPreprocessor)}`;
        details.textContent = `Using ${getPreprocessorName(currentPreprocessor)} to guide generation. Strength: ${document.getElementById('cnStrength')?.value || 0.85}`;
    } else {
        dot.className = 'h-3 w-3 rounded-full bg-rose-500';
        text.textContent = 'ControlNet Inactive';
        details.textContent = 'Upload an image and select a preprocessor to enable ControlNet.';
    }
}

function buildPreprocessorUI() {
    const container = document.getElementById('preprocessorList');
    if (!container) return;

    container.innerHTML = '';
    preprocessors.forEach(pp => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.setAttribute('data-id', pp.id);
        btn.className = `preprocessor-btn rounded-2xl border px-4 py-4 text-left transition ${
            pp.id === currentPreprocessor
                ? 'border-indigo-500 bg-gradient-to-r from-indigo-600 to-violet-600 text-white shadow-md'
                : 'border-slate-200 bg-slate-50 text-slate-700 hover:border-indigo-300 hover:bg-indigo-50'
        }`;
        btn.innerHTML = `
            <div class="text-xl">${pp.icon}</div>
            <div class="mt-2 text-sm font-semibold">${pp.name}</div>
        `;
        btn.onclick = () => selectPreprocessor(pp.id);
        container.appendChild(btn);
    });
}

function selectPreprocessor(id) {
    currentPreprocessor = id;

    document.querySelectorAll('.preprocessor-btn').forEach(btn => {
        if (btn.getAttribute('data-id') === id) {
            btn.className = 'preprocessor-btn rounded-2xl border border-indigo-500 bg-gradient-to-r from-indigo-600 to-violet-600 px-4 py-4 text-left text-white shadow-md transition';
        } else {
            btn.className = 'preprocessor-btn rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-left text-slate-700 transition hover:border-indigo-300 hover:bg-indigo-50';
        }
    });

    document.querySelectorAll('.preprocessor-settings').forEach(panel => {
        panel.classList.add('hidden');
    });

    const selectedPanel = document.getElementById(`${id}Settings`);
    if (selectedPanel) selectedPanel.classList.remove('hidden');

    updateControlNetStatus();
}

function setupImageUpload() {
    const input = document.getElementById('controlImageInput');
    const preview = document.getElementById('controlPreview');

    if (!input) return;

    input.addEventListener('change', (e) => {
        const file = e.target.files[0];

        if (!file) {
            currentControlImage = null;
            if (preview) preview.innerHTML = '';
            updateControlNetStatus();
            return;
        }

        if (file.size > 10 * 1024 * 1024) {
            alert('Image size should be less than 10MB');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = (event) => {
            currentControlImage = event.target.result;
            if (preview) {
                preview.innerHTML = `
                    <img src="${currentControlImage}" class="max-h-64 w-full rounded-2xl border border-slate-200 object-contain bg-white p-2">
                `;
            }
            updateControlNetStatus();
        };
        reader.readAsDataURL(file);
    });
}

function setupSliders() {
    const bindValue = (id, outId, format = (v) => v) => {
        const el = document.getElementById(id);
        const out = document.getElementById(outId);
        if (!el || !out) return;
        el.addEventListener('input', () => {
            out.textContent = format(el.value);
            if (id === 'cnStrength') updateControlNetStatus();
        });
    };

    bindValue('cnStrength', 'strengthVal');
    bindValue('cnStart', 'startVal', (v) => parseFloat(v).toFixed(2));
    bindValue('cnEnd', 'endVal', (v) => parseFloat(v).toFixed(2));
    bindValue('cannyLowThreshold', 'cannyLowVal');
    bindValue('cannyHighThreshold', 'cannyHighVal');
    bindValue('mlsdScoreThr', 'mlsdScoreVal', (v) => parseFloat(v).toFixed(2));
    bindValue('mlsdDistThr', 'mlsdDistVal', (v) => parseFloat(v).toFixed(2));
}

function setupSDXLSupport() {
    const modelSelect = document.getElementById('model');
    const refinerGroup = document.getElementById('refinerGroup');
    if (!modelSelect || !refinerGroup) return;

    modelSelect.addEventListener('change', () => {
        const val = modelSelect.value.toLowerCase();
        const isSDXL = val.includes('sdxl') || val.includes('sd_xl');

        if (isSDXL) {
            refinerGroup.classList.remove('hidden');
            document.getElementById('width').value = 1024;
            document.getElementById('height').value = 1024;
        } else {
            refinerGroup.classList.add('hidden');
        }
    });
}

async function refreshGallery() {
    const galleryDiv = document.getElementById('images');
    if (!galleryDiv) return;

    try {
        const response = await fetch('/api/images');
        const images = await response.json();

        if (!images || images.length === 0) {
            galleryDiv.innerHTML = `
                <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-16 text-center text-slate-400">
                    🎨 No images yet<br>
                    <span class="text-xs">Your generated images will appear here</span>
                </div>
            `;
            return;
        }

        galleryDiv.innerHTML = images.map(img => {
            if (!img.file_name) {
                return `
                    <div class="flex aspect-square items-center justify-center rounded-2xl border border-slate-200 bg-slate-100 p-4 text-center text-slate-500">
                        <div>
                            🎨 Painting...<br>
                            <small>${img.prompt_id.substring(0, 8)}</small>
                        </div>
                    </div>
                `;
            }

            return `
                <div class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <img src="/outputs/${img.file_name}" onclick="window.open(this.src, '_blank')" class="aspect-square w-full cursor-pointer object-cover">
                    <div class="flex items-center justify-between gap-2 border-t border-slate-100 p-3">
                        <span class="truncate text-xs text-slate-500">${img.file_name}</span>
                        <button class="rounded-xl bg-indigo-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-indigo-700" onclick="downloadImage('${img.file_name}', '${img.subfolder || ''}', '${img.type || 'output'}')">Download</button>
                    </div>
                </div>
            `;
        }).join('');
    } catch (error) {
        console.error('Gallery error:', error);
    }
}

async function downloadImage(filename, subfolder = '', type = 'output') {
    try {
        const url = `/api/comfyui/view?filename=${encodeURIComponent(filename)}&subfolder=${encodeURIComponent(subfolder)}&type=${encodeURIComponent(type)}`;
        const response = await fetch(url);
        const blob = await response.blob();
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(a.href);
    } catch (error) {
        console.error('Download error:', error);
        alert('Failed to download image');
    }
}

async function pollForResults(promptId) {
    let attempts = 0;
    const maxAttempts = 60;

    const interval = setInterval(async () => {
        attempts++;

        try {
            const response = await fetch('/api/comfyui/history');
            const history = await response.json();

            if (history[promptId]) {
                clearInterval(interval);

                const outputs = history[promptId].outputs;
                let fileName = null;
                let subfolder = '';
                let type = 'output';

                for (const nodeId in outputs) {
                    if (outputs[nodeId].images && outputs[nodeId].images.length > 0) {
                        fileName = outputs[nodeId].images[0].filename;
                        subfolder = outputs[nodeId].images[0].subfolder || '';
                        type = outputs[nodeId].images[0].type || 'output';
                        break;
                    }
                }

                if (fileName) {
                    await fetch('/api/images/complete', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            prompt_id: promptId,
                            file_name: fileName,
                            subfolder: subfolder,
                            type: type
                        })
                    });
                }

                await refreshGallery();
                setStatus('✅ Generation complete! Image added to gallery.', 'success');

                isGenerating = false;
                setButtonLoading(document.getElementById('generateBtn'), false);
                setTimeout(() => setStatus('✅ Ready • ControlNet ready', 'success'), 2500);
            }

            if (attempts >= maxAttempts) {
                clearInterval(interval);
                isGenerating = false;
                setButtonLoading(document.getElementById('generateBtn'), false);
                setStatus('❌ Timeout: Image taking too long.', 'error');
            }
        } catch (error) {
            console.error('Polling error:', error);
        }
    }, 2000);
}

async function loadModels() {
    const modelSelect = document.getElementById('model');
    if (!modelSelect) return;

    modelSelect.innerHTML = '<option value="">Loading models...</option>';

    try {
        const response = await fetch('/api/comfyui/object-info');
        const data = await response.json();

        if (data && data.checkpoints && data.checkpoints.length > 0) {
            const checkpoints = data.checkpoints;
            const sd15Models = [];
            const sdxlModels = [];
            const fluxModels = [];
            const otherModels = [];

            checkpoints.forEach(model => {
                const lowerModel = model.toLowerCase();
                if (lowerModel.includes('sdxl') || lowerModel.includes('xl')) sdxlModels.push(model);
                else if (lowerModel.includes('flux')) fluxModels.push(model);
                else if (lowerModel.includes('sd15') || lowerModel.includes('v1-5') || lowerModel.includes('dreamshaper') || lowerModel.includes('realistic')) sd15Models.push(model);
                else otherModels.push(model);
            });

            let optionsHtml = '';
            if (sd15Models.length) {
                optionsHtml += '<optgroup label="SD1.5 Models">';
                sd15Models.forEach(model => {
                    const selected = model === 'dreamshaper_8.safetensors' ? 'selected' : '';
                    optionsHtml += `<option value="${model}" ${selected}>${model}</option>`;
                });
                optionsHtml += '</optgroup>';
            }
            if (sdxlModels.length) {
                optionsHtml += '<optgroup label="SDXL Models">';
                sdxlModels.forEach(model => optionsHtml += `<option value="${model}">${model}</option>`);
                optionsHtml += '</optgroup>';
            }
            if (fluxModels.length) {
                optionsHtml += '<optgroup label="Flux Models">';
                fluxModels.forEach(model => optionsHtml += `<option value="${model}">${model}</option>`);
                optionsHtml += '</optgroup>';
            }
            if (otherModels.length) {
                optionsHtml += '<optgroup label="Other Models">';
                otherModels.forEach(model => optionsHtml += `<option value="${model}">${model}</option>`);
                optionsHtml += '</optgroup>';
            }

            modelSelect.innerHTML = optionsHtml;
            modelSelect.dispatchEvent(new Event('change'));
        }
    } catch (error) {
        console.error('Failed to load models:', error);
    }
}

async function loadRefinerModels() {
    const refinerSelect = document.getElementById('refiner_model');
    if (!refinerSelect) return;

    try {
        const response = await fetch('/api/comfyui/object-info');
        const data = await response.json();

        if (data && data.checkpoints && data.checkpoints.length > 0) {
            const refiners = data.checkpoints.filter(model =>
                model.toLowerCase().includes('refiner') ||
                (model.toLowerCase().includes('sdxl') && model.toLowerCase().includes('refiner'))
            );

            if (refiners.length > 0) {
                refinerSelect.innerHTML = refiners.map(model => `<option value="${model}">${model}</option>`).join('');
            }
        }
    } catch (error) {
        console.error('Failed to load refiner models:', error);
    }
}

async function generate() {
    if (isGenerating) {
        alert('Generation already in progress. Please wait.');
        return;
    }

    isGenerating = true;
    const btn = document.getElementById('generateBtn');
    setButtonLoading(btn, true, '🎨 Processing...');
    setStatus('⏳ Initializing generation...', 'warning');

    const selectedModel = document.getElementById('model').value;
    const isSDXL = selectedModel.toLowerCase().includes('sdxl');
    const hasControlImage = currentControlImage !== null;

    const payload = {
        client_id: 'client_' + Date.now(),
        model: selectedModel,
        positive_prompt: document.getElementById('positive_prompt').value,
        negative_prompt: document.getElementById('negative_prompt').value,
        steps: parseInt(document.getElementById('steps').value),
        cfg: parseFloat(document.getElementById('cfg').value),
        width: parseInt(document.getElementById('width').value),
        height: parseInt(document.getElementById('height').value),
        sampler: document.getElementById('sampler').value,
        scheduler: document.getElementById('scheduler').value,
        refiner_model: isSDXL ? document.getElementById('refiner_model')?.value : null,
        refiner_steps: isSDXL ? parseInt(document.getElementById('refiner_steps')?.value || 15) : null,
        controlnet: hasControlImage ? {
            enabled: true,
            preprocessor: currentPreprocessor,
            image_base64: currentControlImage,
            strength: parseFloat(document.getElementById('cnStrength')?.value || 0.85),
            start_percent: parseFloat(document.getElementById('cnStart')?.value || 0),
            end_percent: parseFloat(document.getElementById('cnEnd')?.value || 1)
        } : null
    };

    if (payload.controlnet) {
        switch (currentPreprocessor) {
            case 'canny':
                payload.controlnet.canny_low = parseInt(document.getElementById('cannyLowThreshold')?.value || 50) / 255;
                payload.controlnet.canny_high = parseInt(document.getElementById('cannyHighThreshold')?.value || 150) / 255;
                break;
            case 'depth':
                payload.controlnet.depth_resolution = parseInt(document.getElementById('depthResolution')?.value || 512);
                break;
            case 'openpose':
                payload.controlnet.openpose_hands = document.getElementById('openposeHands')?.checked ? 'enable' : 'disable';
                payload.controlnet.openpose_body = document.getElementById('openposeBody')?.checked ? 'enable' : 'disable';
                payload.controlnet.openpose_face = document.getElementById('openposeFace')?.checked ? 'enable' : 'disable';
                break;
            case 'mlsd':
                payload.controlnet.mlsd_score_thr = parseFloat(document.getElementById('mlsdScoreThr')?.value || 0.1);
                payload.controlnet.mlsd_dist_thr = parseFloat(document.getElementById('mlsdDistThr')?.value || 0.1);
                break;
            case 'scribble':
                payload.controlnet.scribble_mode = document.getElementById('scribbleMode')?.value || 'hed';
                break;
            case 'hed':
                payload.controlnet.hed_resolution = parseInt(document.getElementById('hedResolution')?.value || 512);
                break;
            case 'seg':
                payload.controlnet.seg_resolution = parseInt(document.getElementById('segResolution')?.value || 512);
                break;
            case 'normal':
                payload.controlnet.normal_resolution = parseInt(document.getElementById('normalResolution')?.value || 512);
                break;
        }
    }

    try {
        const response = await fetch('/api/generate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(payload)
        });

        const data = await response.json();

        if (!data.success) throw new Error(data.error || 'Generation failed');

        setStatus('✅ Generation started! Processing...', 'info');
        pollForResults(data.prompt_id);
    } catch (error) {
        console.error('Generation error:', error);
        isGenerating = false;
        setButtonLoading(btn, false);
        setStatus(`❌ Error: ${error.message}`, 'error');
    }
}

async function interruptGeneration() {
    try {
        await fetch('/api/comfyui/interrupt', { method: 'POST' });
        isGenerating = false;
        setButtonLoading(document.getElementById('generateBtn'), false);
        setStatus('⏹️ Generation interrupted', 'warning');
    } catch (error) {
        console.error('Interrupt error:', error);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    loadModels();
    loadRefinerModels();
    buildPreprocessorUI();
    setupImageUpload();
    setupSliders();
    setupSDXLSupport();
    selectPreprocessor('canny');
    refreshGallery();

    const generateBtn = document.getElementById('generateBtn');
    if (generateBtn) generateBtn.onclick = generate;

    const refreshBtn = document.getElementById('refreshGalleryBtn');
    if (refreshBtn) refreshBtn.onclick = refreshGallery;

    const interruptBtn = document.getElementById('interruptBtn');
    if (interruptBtn) interruptBtn.onclick = interruptGeneration;

    setInterval(refreshGallery, 10000);
});
</script>