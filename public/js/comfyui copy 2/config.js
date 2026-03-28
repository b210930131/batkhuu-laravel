// public/js/comfyui/config.js - Enhanced version
// Guard against multiple loads
if (typeof Config === 'undefined') {
    const Config = {
    USE_DIRECT: true,
    COMFYUI_PORT: 8188,
    COMFYUI_HOST: window.location.hostname || '127.0.0.1',
    MAX_IMAGE_SIZE: 10 * 1024 * 1024,
    MAX_IMAGE_DIMENSION: 1024,
    RECONNECT_ATTEMPTS: 5,
    POLL_INTERVAL: 2000,
    GENERATION_TIMEOUT: 180000,
    
    // API endpoints
    API_ENDPOINTS: {
        GENERATE: '/api/generate',
        HEALTH: '/api/health',
        RECENT_IMAGES: '/api/recent-images',
        DEBUG_MODELS: '/api/debug-models',
        DEBUG_PERMISSIONS: '/api/debug-permissions',
        AVAILABLE_PREPROCESSORS: '/api/available-preprocessors',
        TEST_WORKFLOW: '/api/test-workflow'
    },
    
    // Get image URL from ComfyUI
    getImageUrl(filename, subfolder = '', type = 'output') {
        if (!filename) {
            console.error('No filename provided for image URL');
            return '';
        }
        
        let url = `http://${this.COMFYUI_HOST}:${this.COMFYUI_PORT}/view?filename=${encodeURIComponent(filename)}&type=${type}`;
        if (subfolder) {
            url += `&subfolder=${encodeURIComponent(subfolder)}`;
        }
        // Add cache buster only when needed
        if (type === 'output') {
            url += `&_=${Date.now()}`;
        }
        return url;
    },
    
    // Get thumbnail URL
    getThumbnailUrl(filename, subfolder = '', type = 'output') {
        let url = this.getImageUrl(filename, subfolder, type);
        url += `&thumbnail=true`;
        return url;
    },
    
    // Get history URL
    getHistoryUrl() {
        return `http://${this.COMFYUI_HOST}:${this.COMFYUI_PORT}/history`;
    },
    
    // Get queue URL
    getQueueUrl() {
        return `http://${this.COMFYUI_HOST}:${this.COMFYUI_PORT}/queue`;
    },
    
    // Get progress URL
    getProgressUrl(promptId = null) {
        let url = `http://${this.COMFYUI_HOST}:${this.COMFYUI_PORT}/progress`;
        if (promptId) {
            url += `?prompt_id=${promptId}`;
        }
        return url;
    },
    
    // Get WebSocket URL
    getWsUrl(clientId) {
        if (!clientId) {
            clientId = this.generateClientId();
        }
        return `ws://${this.COMFYUI_HOST}:${this.COMFYUI_PORT}/ws?clientId=${clientId}`;
    },
    
    // Generate unique client ID for WebSocket
    generateClientId() {
        return 'client-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
    },
    
    // Get system stats URL
    getSystemStatsUrl() {
        return `http://${this.COMFYUI_HOST}:${this.COMFYUI_PORT}/system_stats`;
    },
    
    // Get object info URL
    getObjectInfoUrl() {
        return `http://${this.COMFYUI_HOST}:${this.COMFYUI_PORT}/object_info`;
    },
    
    // Get interrupt URL
    getInterruptUrl() {
        return `http://${this.COMFYUI_HOST}:${this.COMFYUI_PORT}/interrupt`;
    },
    
    // Get clear queue URL
    getClearQueueUrl() {
        return `http://${this.COMFYUI_HOST}:${this.COMFYUI_PORT}/queue`;
    },
    
    // Get prompt URL (for submitting workflows)
    getPromptUrl() {
        return `http://${this.COMFYUI_HOST}:${this.COMFYUI_PORT}/prompt`;
    },
    
    // Check if ComfyUI is reachable
    async checkConnection() {
        try {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 5000);
            
            const response = await fetch(`http://${this.COMFYUI_HOST}:${this.COMFYUI_PORT}/`, {
                method: 'HEAD',
                signal: controller.signal,
                mode: 'cors'
            });
            
            clearTimeout(timeoutId);
            return response.ok;
        } catch (error) {
            console.error('ComfyUI connection check failed:', error.message);
            return false;
        }
    },
    
    // Get recommended model based on ControlNet usage
    getRecommendedModel(useControlNet = false, currentModel = null) {
        if (useControlNet && currentModel) {
            // Check if current model is SD1.5
            const isSd15 = Sd15Models.some(m => m.name === currentModel);
            if (!isSd15) {
                return Sd15Models[0]?.name || 'dreamshaper_8.safetensors';
            }
        }
        return currentModel;
    },
    
    // Validate resolution (must be multiple of 64)
    validateResolution(width, height) {
        const minSize = 512;
        const maxSize = 1536;
        const multiple = 64;
        
        let validWidth = Math.min(Math.max(width, minSize), maxSize);
        let validHeight = Math.min(Math.max(height, minSize), maxSize);
        
        validWidth = Math.round(validWidth / multiple) * multiple;
        validHeight = Math.round(validHeight / multiple) * multiple;
        
        return { width: validWidth, height: validHeight };
    },
    
    // Get default generation parameters
    getDefaultParams() {
        return {
            model: 'dreamshaper_8.safetensors',
            positive_prompt: 'masterpiece, best quality, high resolution',
            negative_prompt: 'worst quality, low quality, blurry',
            steps: 20,
            cfg: 7.0,
            width: 768,
            height: 768,
            sampler: 'dpmpp_2m',
            controlnet_strength: 0.85,
            controlnet_start: 0,
            controlnet_end: 1
        };
    },
    
    // Get default Canny thresholds
    getDefaultCannyThresholds() {
        return {
            low: 50,
            high: 150
        };
    },
    
    // Normalize Canny thresholds (0-255 to 0-1)
    normalizeCannyThreshold(low, high) {
        return {
            low: Math.max(0, Math.min(0.99, low / 255)),
            high: Math.max(0, Math.min(0.99, high / 255))
        };
    },
    
    // Denormalize Canny thresholds (0-1 to 0-255)
    denormalizeCannyThreshold(low, high) {
        return {
            low: Math.round(Math.max(0, Math.min(255, low * 255))),
            high: Math.round(Math.max(0, Math.min(255, high * 255)))
        };
    },
    
    // Check if model is heavy (needs more VRAM)
    isHeavyModel(modelName) {
        return HeavyModels.some(m => m.name === modelName);
    },
    
    // Check if model is SD1.5
    isSd15Model(modelName) {
        return Sd15Models.some(m => m.name === modelName);
    },
    
    // Get model info
    getModelInfo(modelName) {
        const heavyModel = HeavyModels.find(m => m.name === modelName);
        if (heavyModel) return heavyModel;
        
        const sd15Model = Sd15Models.find(m => m.name === modelName);
        if (sd15Model) return sd15Model;
        
        return null;
    },
    
    // Get recommended resolution for model
    getRecommendedResolution(modelName) {
        if (this.isHeavyModel(modelName)) {
            return { width: 1024, height: 1024 };
        }
        return { width: 768, height: 768 };
    },
    
    // Get all available samplers
    getAvailableSamplers() {
        return [
            'euler', 'euler_ancestral', 'heun', 'dpm_2', 'dpm_2_ancestral',
            'lms', 'dpm_fast', 'dpm_adaptive', 'dpmpp_2s_ancestral', 'dpmpp_2m',
            'ddim', 'uni_pc', 'uni_pc_bh2'
        ];
    },
    
    // Get all available schedulers
    getAvailableSchedulers() {
        return ['normal', 'karras', 'exponential', 'sgm_uniform', 'simple', 'ddim_uniform'];
    },
    
    // Format file size
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    },
    
    // Check if image dimensions are valid
    validateImageDimensions(width, height) {
        const minDim = 64;
        const maxDim = 2048;
        
        return width >= minDim && width <= maxDim && 
               height >= minDim && height <= maxDim &&
               width % 64 === 0 && height % 64 === 0;
    },
    
    // Get optimal steps based on sampler
    getOptimalSteps(sampler) {
        const optimalSteps = {
            'euler': 20,
            'euler_ancestral': 30,
            'heun': 20,
            'dpm_2': 20,
            'dpm_2_ancestral': 30,
            'lms': 20,
            'dpm_fast': 20,
            'dpm_adaptive': 20,
            'dpmpp_2s_ancestral': 20,
            'dpmpp_2m': 20,
            'ddim': 20,
            'uni_pc': 20,
            'uni_pc_bh2': 20
        };
        return optimalSteps[sampler] || 20;
    },
    
    // Get model category
    getModelCategory(modelName) {
        if (this.isHeavyModel(modelName)) return 'heavy';
        if (this.isSd15Model(modelName)) return 'sd15';
        return 'other';
    }
    };
    // Assign to window if not already assigned
    window.Config = Config;
    
    // Preprocessors configuration
    const Preprocessors = [
        { 
            id: "canny", 
            label: "Canny Edge", 
            icon: "🔲", 
            desc: "Sharp edges & outlines",
            settings: ["low_threshold", "high_threshold"],
            defaultSettings: { low_threshold: 50, high_threshold: 150 },
            category: "edge"
        },
        { 
            id: "depth", 
            label: "Depth Map", 
            icon: "🗺️", 
            desc: "3D heatmap, spatial structure",
            settings: ["resolution"],
            defaultSettings: { resolution: 512 },
            category: "3d"
        },
        { 
            id: "openpose", 
            label: "OpenPose", 
            icon: "🧍", 
            desc: "Human skeleton pose",
            settings: ["hands", "body", "face"],
            defaultSettings: { hands: "enable", body: "enable", face: "disable" },
            category: "pose"
        },
        { 
            id: "scribble", 
            label: "Scribble", 
            icon: "✏️", 
            desc: "Turn rough doodles into art",
            settings: ["mode"],
            defaultSettings: { mode: "edge" },
            category: "sketch"
        },
        { 
            id: "mlsd", 
            label: "MLSD", 
            icon: "📐", 
            desc: "Straight line detection",
            settings: ["score_threshold", "distance_threshold", "resolution"],
            defaultSettings: { score_threshold: 0.1, distance_threshold: 0.1, resolution: 512 },
            category: "geometry"
        },
        { 
            id: "hed", 
            label: "HED Soft Edge", 
            icon: "🎨", 
            desc: "Organic edges, painterly style",
            settings: ["resolution"],
            defaultSettings: { resolution: 512 },
            category: "edge"
        },
        { 
            id: "seg", 
            label: "Segmentation", 
            icon: "🏞️", 
            desc: "Scene composition",
            settings: ["resolution"],
            defaultSettings: { resolution: 512 },
            category: "semantic"
        },
        { 
            id: "normal", 
            label: "Normal Map", 
            icon: "⚡", 
            desc: "Surface geometry",
            settings: ["resolution"],
            defaultSettings: { resolution: 512 },
            category: "3d"
        }
    ];
    
    // Heavy models configuration
    const HeavyModels = [
        { name: "sd_xl_base_1.0.safetensors", vram: "8GB+", recommendedRes: "1024x1024", description: "SDXL Base" },
        { name: "sd_xl_refiner_1.0.safetensors", vram: "8GB+", recommendedRes: "1024x1024", description: "SDXL Refiner" },
        { name: "flux1-dev-fp8.safetensors", vram: "12GB+", recommendedRes: "1024x1024", description: "Flux Dev" },
        { name: "sd3.5_large_fp8_scaled.safetensors", vram: "12GB+", recommendedRes: "1024x1024", description: "SD3.5 Large" }
    ];
    
    // SD1.5 models configuration
    const Sd15Models = [
        { name: "dreamshaper_8.safetensors", description: "Artistic, anime, vibrant colors", vram: "4GB+", recommendedRes: "768x768" },
        { name: "v1-5-pruned-emaonly-fp16.safetensors", description: "Lightweight, fast, low VRAM", vram: "2GB+", recommendedRes: "768x768" },
        { name: "v1-5-pruned.safetensors", description: "Standard SD1.5, balanced", vram: "4GB+", recommendedRes: "768x768" },
        { name: "realisticVisionV60B1_v51HyperVAE.safetensors", description: "Photorealistic, portraits", vram: "4GB+", recommendedRes: "768x768" },
        { name: "interior_design_sd15.safetensors", description: "Interior design specialized", vram: "4GB+", recommendedRes: "768x768" },
        { name: "architecture_interior_v1.safetensors", description: "Architecture & interior design", vram: "4GB+", recommendedRes: "768x768" }
    ];
    
    // Assign all to window
    window.Preprocessors = Preprocessors;
    window.HeavyModels = HeavyModels;
    window.Sd15Models = Sd15Models;
}

// Export for CommonJS if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { Config, Preprocessors, HeavyModels, Sd15Models };
}