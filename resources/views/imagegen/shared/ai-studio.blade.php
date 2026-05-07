@extends($layout)

@section('title', $title)
@section('page_title', $pageTitle)
@section('page_subtitle', $pageSubtitle)

@section('content')
<div class="space-y-8">
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 px-8 py-8 text-white shadow-2xl">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(99,102,241,0.25),transparent_30%),radial-gradient(circle_at_bottom_left,rgba(168,85,247,0.18),transparent_28%)]"></div>

        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="inline-flex items-center rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs font-semibold tracking-wide text-slate-100">
                    AI STUDIO
                </div>
                <h1 class="mt-4 text-3xl font-bold tracking-tight md:text-4xl">
                    {{ $heroTitle }}
                </h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300">
                    {{ $heroSubtitle }}
                </p>
            </div>

            <div class="grid grid-cols-1 gap-4">
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                    <div class="text-xs uppercase tracking-wider text-slate-300">Access</div>
                    <div class="mt-2 text-xl font-bold">{{ $accessLabel }}</div>
                </div>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1.35fr)_minmax(360px,0.95fr)]">
        <!-- LEFT SIDE -->
        <div class="space-y-6">
            <!-- Main Configuration -->
            <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-gradient-to-r from-slate-950 via-slate-900 to-indigo-950 px-6 py-5 text-white">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-semibold">Model & Prompt Configuration</h2>
                            <p class="mt-1 text-sm text-slate-300">{{ $workspaceHint }}</p>
                        </div>
                        <span class="rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs font-medium text-slate-100">
                            {{ $studioBadge }}
                        </span>
                    </div>
                </div>

                <div class="space-y-6 p-6">
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
                            </div>

                            <div id="resolutionHint" class="hidden rounded-xl bg-white px-4 py-3 text-xs text-slate-600"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5">

                        <!-- POSITIVE PROMPT -->
                        <div class="space-y-2">
                            <label for="positive_prompt" class="block text-sm font-medium text-slate-700">
                                Positive Prompt
                            </label>

                            <textarea id="positive_prompt" rows="4"
                                class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-800 outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">modern cozy living room interior, warm and inviting atmosphere, elegant contemporary design, soft ambient lighting, comfortable fabric sofa, stylish coffee table, textured rug, wooden floor, neutral color palette, beige and cream tones, large window, natural light, sheer curtains, indoor plants, minimal decor, clean layout, realistic interior, detailed furniture, soft shadows, cozy ceiling light, high detail, photorealistic, beautiful composition, interior design photography
                                </textarea>
                            </div>
                            
                            

                            <!-- NEGATIVE PROMPT -->
                            <div class="space-y-2">
                                <label for="negative_prompt" class="block text-sm font-medium text-slate-700">
                                    Negative Prompt
                                </label>

                                <textarea id="negative_prompt" rows="3"
                                    class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-800 outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">low quality, worst quality, blurry, distorted furniture, deformed room, bad perspective, extra objects, cluttered, messy, oversaturated, cartoon, anime, low detail, duplicate furniture, floating objects, unrealistic lighting, warped walls, bad anatomy, text, watermark, logo
                                </textarea>
                            </div>

                        </div>

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
                            <label for="denoise" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Denoise</label>
                            <input type="number" id="denoise" value="1.0" min="0" max="1" step="0.05"
                                class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
                            <p class="text-[11px] leading-4 text-slate-500">0.0 = keep input, 1.0 = full redraw</p>
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
                                <option value="uni_pc">Uni PC</option>
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

	                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50/70 p-5">
	                        <div class="space-y-3">
	                            <div class="flex items-center justify-between gap-3">
	                                <label class="block text-sm font-medium text-slate-700">Control Image</label>
	                                <button id="chooseInputImageBtn" type="button"
	                                    class="inline-flex items-center justify-center rounded-xl border border-transparent bg-indigo-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700">
	                                    Input images
	                                </button>
	                            </div>
	                            <p class="text-xs text-slate-500">Choose or upload an input image to guide generation.</p>
	                            <div id="controlPreview" class="overflow-hidden rounded-2xl"></div>
	                        </div>
	                    </div>

                    <div class="flex flex-col gap-3 md:flex-row">
                        <button id="generateBtn"
                            class="inline-flex flex-1 items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 px-5 py-3.5 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:from-indigo-700 hover:to-violet-700 disabled:cursor-not-allowed disabled:opacity-70">
                            🚀 Generate
                        </button>

                        <button id="refreshGalleryBtn"
                            class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-5 py-3.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            🔄 Refresh
                        </button>

                        <button id="interruptBtn"
                            class="inline-flex items-center justify-center rounded-2xl bg-rose-500 px-5 py-3.5 text-sm font-semibold text-white transition hover:bg-rose-600">
                            ⏹️ Interrupt
                        </button>
                    </div>

                    <div id="status"
                        class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                        ✅ Ready • ControlNet inactive
                    </div>
                </div>
            </section>

            <!-- Preprocessors -->
            <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-gradient-to-r from-slate-900 to-slate-800 px-6 py-5 text-white">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold">ControlNet Preprocessors</h2>
                        <div id="controlnetDot" class="h-3 w-3 rounded-full bg-rose-500"></div>
                    </div>
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
                            <div id="controlnetStatusDot" class="h-3 w-3 rounded-full bg-rose-500"></div>
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
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold">Generated Images</h2>
                        <span id="adminCount" class="rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs font-medium text-slate-100">
                            0 images
                        </span>
                    </div>
                </div>

                <div class="space-y-4 p-5">
                    <div id="galleryStatus" class="hidden rounded-2xl border px-4 py-3 text-sm font-medium"></div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div class="mb-3">
                            <div class="inline-flex w-fit items-center rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">
                                User Folder
                            </div>
                            <h3 class="mt-3 text-lg font-bold tracking-tight text-slate-900">Library Control</h3>
                        </div>

                        <form id="folderForm" class="rounded-2xl bg-white p-3">
                            <label for="folderName" class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">New folder</label>
                            <div class="flex items-center gap-2">
                                @if($canChooseFolderUser)
                                    <input id="folderUserId" name="user_id" type="number" min="1" placeholder="User"
                                        class="w-20 rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @endif
                                <input id="folderName" name="folder_name" type="text" maxlength="80" placeholder="Name"
                                    class="min-w-0 flex-1 rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <button type="submit"
                                    class="shrink-0 rounded-xl bg-indigo-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">
                                    Add
                                </button>
                            </div>
                        </form>

                        <div class="mt-4 grid grid-cols-2 gap-2">
                            <button type="button" data-folder-filter="all"
                                class="folder-filter flex w-full items-center justify-between rounded-xl bg-white px-3 py-2.5 text-left text-sm font-semibold transition hover:bg-slate-100">
                                <span>All images</span>
                                <span id="allCount" class="text-xs text-slate-500">0</span>
                            </button>
                            <button type="button" data-folder-filter="unfiled"
                                class="folder-filter flex w-full items-center justify-between rounded-xl bg-white px-3 py-2.5 text-left text-sm font-semibold transition hover:bg-slate-100">
                                <span>Unfiled</span>
                                <span id="unfiledCount" class="text-xs text-slate-500">0</span>
                            </button>
                        </div>

                        <div class="mt-4 border-t border-slate-200 pt-3">
                            <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">All folders</div>
                            <div id="folderList" class="space-y-1.5"></div>
                        </div>
                    </div>

                    <div id="images" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-16 text-center text-slate-400">
                            No images yet
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<div id="inputImagePickerModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm">
    <div class="flex flex-col overflow-hidden rounded-2xl bg-white shadow-2xl"
        style="width: 75vw; max-width: 1180px; min-width: min(94vw, 720px); height: 75vh; max-height: 75vh;">
        <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-5 py-4">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Choose Input Image</h3>
                <p class="mt-1 text-sm text-slate-500">Select an existing input image for ControlNet.</p>
            </div>
            <button type="button" onclick="closeInputImagePicker()"
                class="rounded-xl border border-slate-200 px-3 py-1.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                Close
            </button>
        </div>

        <div id="inputImagePickerStatus" class="hidden border-b border-slate-200 px-5 py-2 text-sm font-medium"></div>

        <div class="border-b border-slate-200 bg-slate-50 px-5 py-3">
            <div class="grid items-center gap-3 rounded-xl border border-dashed border-slate-300 bg-white p-3 md:grid-cols-[180px_minmax(0,1fr)]">
                <label for="controlImageInput" class="text-sm font-semibold text-slate-800">Upload new input image</label>
                <input type="file" id="controlImageInput" accept="image/*"
                    class="block w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-600 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-white hover:file:bg-indigo-700">
            </div>
        </div>

        <div id="inputImagePickerGrid" class="grid min-h-0 flex-1 auto-rows-max grid-cols-1 gap-4 overflow-y-auto p-5 lg:grid-cols-2"
            style="overflow-y: auto; overscroll-behavior: contain;">
            <div class="col-span-full flex min-h-[260px] items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-5 text-center text-slate-400">
                Loading input images...
            </div>
        </div>
    </div>
</div>

<div id="imageModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm">
    <div class="overflow-hidden rounded-2xl bg-white shadow-2xl"
        style="display:grid;grid-template-columns:minmax(680px,820px) minmax(360px,1fr);width:min(1540px,calc(100vw - 32px));height:min(92vh,880px);">
        <div class="flex min-w-0 items-center justify-center bg-slate-950 p-4">
            <img id="modalImage" src="" alt="Generated image" class="rounded-xl object-contain" style="max-width:780px;width:100%;max-height:820px;">
        </div>

        <aside class="min-w-0 overflow-y-auto border-l border-slate-200 bg-white p-5">
            <div class="mb-4 flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h3 id="modalTitle" class="truncate text-lg font-bold text-slate-900">Image details</h3>
                    <p id="modalMeta" class="mt-1 text-xs text-slate-500"></p>
                </div>
                <button type="button" onclick="closeImageModal()"
                    class="rounded-xl border border-slate-200 px-3 py-1.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    Close
                </button>
            </div>

            <div id="modalDetails" class="text-sm"></div>
        </aside>
    </div>
</div>

@push('scripts')
<script>
const studioRoutes = @json($studioRoutes);
let currentPreprocessor = 'canny';
let currentControlImage = null;
let isGenerating = false;
let galleryImages = [];
let galleryFolders = [];
let activeFolder = 'all';
let inputImageChoices = [];

const preprocessors = [
    { id: 'canny', name: 'Canny', icon: '🔲' },
    { id: 'depth', name: 'Depth', icon: '🗺️' },
    { id: 'sd35_canny', name: 'ControlNet 3.5 Canny', icon: '🔲' },
    { id: 'sd35_depth', name: 'ControlNet 3.5 Depth', icon: '🗺️' },
    { id: 'sd35_blur', name: 'ControlNet 3.5 Blur', icon: '🌫️' },
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
        sd35_canny: 'ControlNet 3.5 Canny',
        sd35_depth: 'ControlNet 3.5 Depth',
        sd35_blur: 'ControlNet 3.5 Blur',
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

function hasActiveControlImage() {
    return typeof currentControlImage === 'string' && currentControlImage.trim() !== '';
}

function readyStatusMessage() {
    return hasActiveControlImage() ? 'Ready • ControlNet ready' : 'Ready • ControlNet inactive';
}

function updateControlNetStatus() {
    const dot = document.getElementById('controlnetStatusDot');
    const text = document.getElementById('controlnetText');
    const details = document.getElementById('controlnetDetails');
    const topDot = document.getElementById('controlnetDot');
    const hasImage = hasActiveControlImage();

    if (!dot || !text || !details || !topDot) return;

    if (hasImage) {
        dot.className = 'h-3 w-3 rounded-full bg-emerald-500';
        topDot.className = 'h-3 w-3 rounded-full bg-emerald-500';
        text.textContent = `ControlNet Active: ${getPreprocessorName(currentPreprocessor)}`;
        details.textContent = `Using ${getPreprocessorName(currentPreprocessor)} to guide generation. Strength: ${document.getElementById('cnStrength')?.value || 0.85}`;
    } else {
        dot.className = 'h-3 w-3 rounded-full bg-rose-500';
        topDot.className = 'h-3 w-3 rounded-full bg-rose-500';
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

    if (id.startsWith('sd35_')) {
        const modelSelect = document.getElementById('model');
        const sd35Model = Array.from(modelSelect?.options || [])
            .find(option => option.value.toLowerCase().includes('sd3.5'));

        if (sd35Model && modelSelect.value !== sd35Model.value) {
            modelSelect.value = sd35Model.value;
            modelSelect.dispatchEvent(new Event('change'));
        }

        applySD35ControlNetDefaults();
    }

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

    const settingsPanelId = {
        sd35_canny: 'canny',
        sd35_depth: 'depth'
    }[id] || id;
    const selectedPanel = document.getElementById(`${settingsPanelId}Settings`);
    if (selectedPanel) selectedPanel.classList.remove('hidden');

    updateControlNetStatus();
}

function setFieldValue(id, value) {
    const field = document.getElementById(id);
    if (!field) return;
    field.value = value;
    field.dispatchEvent(new Event('input'));
    field.dispatchEvent(new Event('change'));
}

function applySD35ControlNetDefaults() {
    setFieldValue('steps', 35);
    setFieldValue('cfg', 5);
    setFieldValue('width', 768);
    setFieldValue('height', 320);
    setFieldValue('sampler', 'dpmpp_2m');
    setFieldValue('scheduler', 'normal');
    setFieldValue('cnStrength', 0.55);
    setFieldValue('cnStart', 0);
    setFieldValue('cnEnd', 1);
    updateControlNetStatus();
}

function setupImageUpload() {
    const input = document.getElementById('controlImageInput');
    const preview = document.getElementById('controlPreview');

    if (!input) return;

    input.addEventListener('change', async (e) => {
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

	        try {
	            currentControlImage = await prepareControlImage(file);
	            if (preview) {
	                preview.innerHTML = `
	                    <img src="${currentControlImage}" class="max-h-64 w-full rounded-2xl border border-slate-200 object-contain bg-white p-2">
	                    <div class="mt-2 rounded-xl bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700">
	                        Uploaded: ${escapeHtml(file.name)}
	                    </div>
	                `;
	            }
	            updateControlNetStatus();
	            closeInputImagePicker();
	            setStatus('Control image uploaded.', 'success');
	        } catch (error) {
            console.error('Image upload error:', error);
            alert(error.message || 'Failed to load image');
            input.value = '';
            currentControlImage = null;
            if (preview) preview.innerHTML = '';
            updateControlNetStatus();
        }
    });
}

function readFileAsDataUrl(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = (event) => resolve(event.target.result);
        reader.onerror = () => reject(new Error('Could not read image file'));
        reader.readAsDataURL(file);
    });
}

function loadImageElement(src) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.onload = () => resolve(img);
        img.onerror = () => reject(new Error('Could not load image preview'));
        img.src = src;
    });
}

async function prepareControlImage(file) {
    const originalDataUrl = await readFileAsDataUrl(file);
    const image = await loadImageElement(originalDataUrl);
    const maxSide = 1280;
    const scale = Math.min(1, maxSide / Math.max(image.width, image.height));
    const width = Math.max(1, Math.round(image.width * scale));
    const height = Math.max(1, Math.round(image.height * scale));

    const canvas = document.createElement('canvas');
    canvas.width = width;
    canvas.height = height;

    const ctx = canvas.getContext('2d');
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, width, height);
    ctx.drawImage(image, 0, 0, width, height);

    const compressedDataUrl = canvas.toDataURL('image/jpeg', 0.88);
    if (originalDataUrl.length > 4 * 1024 * 1024) {
        return compressedDataUrl;
    }

    return compressedDataUrl.length < originalDataUrl.length ? compressedDataUrl : originalDataUrl;
}

function setInputImagePickerStatus(message, type = 'info') {
    const el = document.getElementById('inputImagePickerStatus');
    if (!el) return;

    const styles = {
        info: 'border-indigo-200 bg-indigo-50 text-indigo-700',
        success: 'border-emerald-200 bg-emerald-50 text-emerald-700',
        error: 'border-rose-200 bg-rose-50 text-rose-700',
    };

    el.className = `border-b px-5 py-3 text-sm font-medium ${styles[type] || styles.info}`;
    el.textContent = message;
    el.classList.remove('hidden');
}

function inputImageOwnerLabel(image) {
    return image.user?.name || `User #${image.user_id}`;
}

function renderInputImageChoices() {
    const grid = document.getElementById('inputImagePickerGrid');
    if (!grid) return;

    if (!inputImageChoices.length) {
        grid.innerHTML = `
            <div class="col-span-full flex min-h-[260px] items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-5 text-center text-slate-400">
                No input images found.
            </div>
        `;
        return;
    }

    grid.innerHTML = inputImageChoices.map(img => {
        const imageUrl = `/${img.path}`;

        return `
            <button type="button" onclick="selectInputImage(${img.id})"
                class="group rounded-2xl border border-slate-200 bg-white p-2 text-left shadow-sm transition hover:border-indigo-300 hover:shadow-lg">

                <div class="relative mx-auto aspect-[5/9] w-full max-w-[220px] overflow-hidden rounded-xl bg-slate-100">
                    <img src="${imageUrl}" alt="${escapeHtml(img.file_name)}"
                        class="h-full w-full object-contain p-2">
                </div>

                <div class="mt-2 space-y-1 border-t border-slate-100 p-3">
                    <p class="truncate text-sm font-semibold text-slate-800">${escapeHtml(inputImageOwnerLabel(img))}</p>
                    <p class="truncate text-xs text-slate-500">${escapeHtml(img.file_name)}</p>
                    <p class="text-xs text-slate-500">${escapeHtml(img.preprocessor || img.source_type || 'input')}</p>
                </div>
            </button>
        `;
    }).join('');
}

async function loadInputImageChoices() {
    const response = await fetch(studioRoutes.inputImages, { headers: { 'Accept': 'application/json' } });
    const images = await response.json();
    if (!response.ok) throw new Error(images.message || 'Failed to load input images');
    inputImageChoices = (images || []).filter(img => img.path);
}

async function openInputImagePicker() {
    const modal = document.getElementById('inputImagePickerModal');
    const grid = document.getElementById('inputImagePickerGrid');
    if (!modal || !grid) return;

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    grid.innerHTML = '<div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-5 py-14 text-center text-slate-400">Loading input images...</div>';
    setInputImagePickerStatus('Loading input images...', 'info');

    try {
        await loadInputImageChoices();
        renderInputImageChoices();
        setInputImagePickerStatus(`Loaded ${inputImageChoices.length} input image(s).`, 'success');
    } catch (error) {
        grid.innerHTML = `<div class="col-span-full rounded-2xl border border-rose-200 bg-rose-50 px-5 py-14 text-center text-rose-600">Failed to load input images: ${escapeHtml(error.message)}</div>`;
        setInputImagePickerStatus(`Input image error: ${error.message}`, 'error');
    }
}

function closeInputImagePicker() {
    const modal = document.getElementById('inputImagePickerModal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function imageUrlToDataUrl(url) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.crossOrigin = 'anonymous';
        img.onload = () => {
            const canvas = document.createElement('canvas');
            canvas.width = img.naturalWidth || img.width;
            canvas.height = img.naturalHeight || img.height;
            const ctx = canvas.getContext('2d');
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 0, 0);
            resolve(canvas.toDataURL('image/jpeg', 0.88));
        };
        img.onerror = () => reject(new Error('Could not load selected input image'));
        img.src = url;
    });
}

async function selectInputImage(id) {
    const selected = inputImageChoices.find(img => Number(img.id) === Number(id));
    if (!selected) return;

    const imageUrl = `/${selected.path}`;
    setInputImagePickerStatus('Selecting image...', 'info');

    try {
        currentControlImage = await imageUrlToDataUrl(imageUrl);
        document.getElementById('controlImageInput').value = '';
        document.getElementById('controlPreview').innerHTML = `
            <img src="${imageUrl}" class="max-h-64 w-full rounded-2xl border border-slate-200 bg-white object-contain p-2">
            <div class="mt-2 rounded-xl bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700">
                Selected: ${escapeHtml(selected.file_name)}
            </div>
        `;
        updateControlNetStatus();
        closeInputImagePicker();
        setStatus('✅ Control image selected from input images.', 'success');
    } catch (error) {
        setInputImagePickerStatus(`Select error: ${error.message}`, 'error');
    }
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

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
}

function setGalleryStatus(message, type = 'info') {
    const el = document.getElementById('galleryStatus');
    if (!el) return;

    const styles = {
        info: 'border-indigo-200 bg-indigo-50 text-indigo-700',
        success: 'border-emerald-200 bg-emerald-50 text-emerald-700',
        error: 'border-rose-200 bg-rose-50 text-rose-700',
    };

    el.className = `rounded-2xl border px-4 py-3 text-sm font-medium ${styles[type] || styles.info}`;
    el.textContent = message;
    el.classList.remove('hidden');
}

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function detailCard(label, value, tone = 'slate', span = '') {
    const text = value ? escapeHtml(value) : 'Not provided.';
    const tones = {
        green: 'bg-emerald-50 text-emerald-700',
        red: 'bg-rose-50 text-rose-700',
        blue: 'bg-indigo-50 text-indigo-700',
        slate: 'bg-slate-50 text-slate-500',
    };

    return `
        <div class="${span} rounded-2xl ${tones[tone] || tones.slate} p-4">
            <div class="text-xs font-bold uppercase tracking-wide">${escapeHtml(label)}</div>
            <p class="mt-3 text-sm text-slate-700" style="white-space:normal;overflow-wrap:break-word;word-break:normal;line-height:1.75;max-width:620px;">${text}</p>
        </div>
    `;
}

function openImageModal(id, imageUrl) {
    const img = galleryImages.find(item => Number(item.id) === Number(id));
    if (!img) return;

    const folder = galleryFolders.find(item => Number(item.id) === Number(img.gallery_folder_id));
    const modal = document.getElementById('imageModal');
    document.getElementById('modalImage').src = imageUrl;
    document.getElementById('modalTitle').textContent = img.file_name || 'Generated image';
    document.getElementById('modalMeta').textContent = [
        `ID ${img.id}`,
        folder ? folder.name : 'Unfiled',
        img.user?.name || null,
    ].filter(Boolean).join(' · ');

    document.getElementById('modalDetails').innerHTML = `
        <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
            ${detailCard('Original', img.original_prompt, 'slate', 'xl:col-span-2')}
            ${detailCard('Canonical', img.canonical_prompt || img.positive_prompt, 'blue', 'xl:col-span-2')}
            ${detailCard('Positive prompt', img.positive_prompt, 'green')}
            ${detailCard('Negative prompt', img.negative_prompt, 'red')}
            ${detailCard('Model', img.model_used, 'slate')}
            ${detailCard('Size', img.width && img.height ? `${img.width} x ${img.height}` : '', 'slate')}
            ${detailCard('Type', img.type || 'output', 'slate', 'xl:col-span-2')}
        </div>
    `;

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.getElementById('modalImage').src = '';
}

function ownerLabel(image) {
    return image.user?.name || `User #${image.user_id}`;
}

function compatibleFolders(image) {
    return galleryFolders.filter(folder => Number(folder.user_id) === Number(image.user_id));
}

function visibleImages() {
    const completed = galleryImages.filter(img => img.file_name);
    if (activeFolder === 'all') return completed;
    if (activeFolder === 'unfiled') return completed.filter(img => !img.gallery_folder_id);
    return completed.filter(img => Number(img.gallery_folder_id) === Number(activeFolder));
}

function renderFolders() {
    const allCount = galleryImages.filter(img => img.file_name).length;
    const unfiledCount = galleryImages.filter(img => img.file_name && !img.gallery_folder_id).length;
    document.getElementById('allCount').textContent = allCount;
    document.getElementById('unfiledCount').textContent = unfiledCount;

    document.querySelectorAll('.folder-filter').forEach(button => {
        const value = button.dataset.folderFilter;
        const active = String(activeFolder) === String(value);
        button.className = `folder-filter flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-left text-sm font-semibold transition ${active ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-100'}`;
    });

    const folderList = document.getElementById('folderList');
    if (!galleryFolders.length) {
        folderList.innerHTML = '<div class="rounded-xl border border-dashed border-slate-300 px-3 py-4 text-sm text-slate-500">No folders yet.</div>';
        return;
    }

    folderList.innerHTML = galleryFolders.map(folder => {
        const count = galleryImages.filter(img => Number(img.gallery_folder_id) === Number(folder.id)).length;
        const active = String(activeFolder) === String(folder.id);
        return `
            <div class="flex items-center gap-2">
                <button type="button" data-folder-filter="${folder.id}"
                    class="folder-filter min-w-0 flex-1 rounded-xl px-3 py-2.5 text-left text-sm font-semibold transition ${active ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-100'}">
                    <span class="block truncate">${escapeHtml(folder.name)}</span>
                    <span class="block text-xs ${active ? 'text-indigo-100' : 'text-slate-500'}">${escapeHtml(folder.user?.name || (folder.user_id ? `User #${folder.user_id}` : 'Mine'))} - ${count}</span>
                </button>
                <button type="button" onclick="deleteFolder(${folder.id}, '${escapeHtml(folder.name)}')"
                    class="rounded-xl border border-rose-200 px-2.5 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-50">
                    Delete
                </button>
            </div>
        `;
    }).join('');

    document.querySelectorAll('[data-folder-filter]').forEach(button => {
        button.addEventListener('click', () => {
            activeFolder = button.dataset.folderFilter;
            renderFolders();
            renderImages();
        });
    });
}

function folderOptions(image) {
    const options = ['<option value="">Unfiled</option>'].concat(
        compatibleFolders(image).map(folder => `<option value="${folder.id}" ${Number(image.gallery_folder_id) === Number(folder.id) ? 'selected' : ''}>${escapeHtml(folder.name)}</option>`)
    );
    return options.join('');
}

function renderImages() {
    const gallery = document.getElementById('images');
    const count = document.getElementById('adminCount');
    const images = visibleImages();

    if (count) count.textContent = `${galleryImages.filter(img => img.file_name).length} images`;

    if (!images.length) {
        gallery.innerHTML = '<div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-16 text-center text-slate-400">No images found.</div>';
        return;
    }

    gallery.innerHTML = images.map(img => {
        const imageUrl = `/api/comfyui/view?filename=${encodeURIComponent(img.file_name)}&subfolder=${encodeURIComponent(img.subfolder || '')}&type=${encodeURIComponent(img.type || 'output')}`;
        const folder = galleryFolders.find(item => Number(item.id) === Number(img.gallery_folder_id));

        return `
            <article class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                <div class="relative aspect-square overflow-hidden bg-slate-100">
                    <img src="${imageUrl}" alt="Generated image" class="h-full w-full cursor-pointer object-cover transition duration-300 group-hover:scale-[1.03]" onclick="openImageModal(${img.id}, this.src)">
                </div>

                <div class="space-y-3 p-3">
                    <div>
                        <p class="truncate text-sm font-semibold text-slate-800">${escapeHtml(ownerLabel(img))}</p>
                        <p class="mt-1 text-xs text-slate-500">${escapeHtml(folder ? folder.name : 'Unfiled')} - ID ${escapeHtml(img.id)}</p>
                    </div>
                    <p class="line-clamp-2 text-xs leading-5 text-slate-500">${escapeHtml(img.positive_prompt || 'No prompt')}</p>

                    <label class="block text-xs font-semibold text-slate-600">
                        Move to folder
                        <select name="gallery_folder_id" onchange="moveImage(${img.id}, this.value)"
                            class="mt-1 w-full rounded-xl border-slate-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            ${folderOptions(img)}
                        </select>
                    </label>

                    <button type="button" onclick="deleteImage(${img.id})" class="w-full rounded-xl bg-rose-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-rose-700">
                        Delete image
                    </button>
                </div>
            </article>
        `;
    }).join('');
}

async function loadFolders() {
    const response = await fetch(studioRoutes.folders, { headers: { 'Accept': 'application/json' } });
    const folders = await response.json();
    if (!response.ok) throw new Error(folders.message || 'Failed to load folders');
    galleryFolders = folders || [];
}

async function loadImages() {
    const response = await fetch(studioRoutes.images, { headers: { 'Accept': 'application/json' } });
    const images = await response.json();
    if (!response.ok) throw new Error(images.message || 'Failed to load images');
    galleryImages = images || [];
}

async function refreshGallery() {
    const gallery = document.getElementById('images');
    if (!gallery) return;

    try {
        await Promise.all([loadFolders(), loadImages()]);
        renderFolders();
        renderImages();
    } catch (error) {
        console.error(error);
        gallery.innerHTML = `<div class="col-span-full rounded-2xl border border-rose-200 bg-rose-50 px-6 py-16 text-center text-rose-600">Failed to load gallery: ${error.message}</div>`;
        setGalleryStatus(`Gallery error: ${error.message}`, 'error');
    }
}

async function createFolder(event) {
    event.preventDefault();
    const userInput = document.getElementById('folderUserId');
    const userId = userInput ? userInput.value : null;
    const nameInput = document.getElementById('folderName');
    const name = nameInput.value.trim();
    if (!name || (studioRoutes.requiresFolderUser && !userId)) return;

    try {
        const response = await fetch(studioRoutes.folders, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify(userId ? { user_id: Number(userId), name } : { name })
        });
        const result = await response.json();
        if (!response.ok) throw new Error(result.message || 'Folder create failed');
        nameInput.value = '';
        activeFolder = String(result.id);
        await refreshGallery();
        setGalleryStatus('Folder created.', 'success');
    } catch (error) {
        setGalleryStatus(`Folder error: ${error.message}`, 'error');
    }
}

async function deleteFolder(id, name) {
    if (!confirm(`Delete folder "${name}"? Images will move to Unfiled.`)) return;

    try {
        const response = await fetch(`${studioRoutes.folders}/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' }
        });
        const result = await response.json();
        if (!response.ok) throw new Error(result.message || 'Folder delete failed');
        activeFolder = 'all';
        await refreshGallery();
        setGalleryStatus('Folder deleted.', 'success');
    } catch (error) {
        setGalleryStatus(`Folder delete error: ${error.message}`, 'error');
    }
}

async function moveImage(id, folderId) {
    try {
        const response = await fetch(`${studioRoutes.imageFolderBase}/${id}/folder`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ folder_id: folderId ? Number(folderId) : null })
        });
        const result = await response.json();
        if (!response.ok) throw new Error(result.message || 'Move failed');
        await refreshGallery();
        setGalleryStatus('Image moved.', 'success');
    } catch (error) {
        setGalleryStatus(`Move error: ${error.message}`, 'error');
        refreshGallery();
    }
}

async function deleteImage(id) {
    if (!confirm('Устгахдаа итгэлтэй байна уу?')) return;

    try {
        const response = await fetch(`${studioRoutes.imageDeleteBase}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        if (data.success) {
            await refreshGallery();
            setGalleryStatus('Image deleted.', 'success');
        }
    } catch (error) {
        console.error('Delete error:', error);
        setGalleryStatus(`Delete error: ${error.message}`, 'error');
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
                setTimeout(() => setStatus(`✅ ${readyStatusMessage()}`, 'success'), 2500);
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
    const hasControlImage = hasActiveControlImage();

    const payload = {
        client_id: 'client_' + Date.now(),
        model: selectedModel,
        positive_prompt: document.getElementById('positive_prompt').value,
        negative_prompt: document.getElementById('negative_prompt').value,
        steps: parseInt(document.getElementById('steps').value),
        cfg: parseFloat(document.getElementById('cfg').value),
        denoise: parseFloat(document.getElementById('denoise').value),
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
            case 'sd35_canny':
                payload.controlnet.canny_low = parseInt(document.getElementById('cannyLowThreshold')?.value || 50) / 255;
                payload.controlnet.canny_high = parseInt(document.getElementById('cannyHighThreshold')?.value || 150) / 255;
                break;
            case 'depth':
            case 'sd35_depth':
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
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(payload)
        });

        const responseText = await response.text();
        let data;
        try {
            data = JSON.parse(responseText);
        } catch (error) {
            const plainError = responseText.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
            throw new Error(plainError || `Server returned ${response.status}`);
        }

        if (!response.ok || !data.success) throw new Error(data.error || data.message || 'Generation failed');

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

                    document.getElementById('folderForm')?.addEventListener('submit', createFolder);
                    document.getElementById('chooseInputImageBtn')?.addEventListener('click', openInputImagePicker);
                    document.getElementById('inputImagePickerModal')?.addEventListener('click', event => {
                        if (event.target === event.currentTarget) closeInputImagePicker();
                    });
                    document.getElementById('imageModal')?.addEventListener('click', event => {
                        if (event.target === event.currentTarget) closeImageModal();
                    });
                    document.addEventListener('keydown', event => {
                        if (event.key === 'Escape') {
                            closeInputImagePicker();
                            closeImageModal();
                        }
                    });

                    const interruptBtn = document.getElementById('interruptBtn');
                    if (interruptBtn) interruptBtn.onclick = interruptGeneration;

                    setInterval(refreshGallery, 10000);
                });
</script>
@endpush
@endsection
