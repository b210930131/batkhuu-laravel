// public/js/comfyui/config.js
if (typeof Config === 'undefined') {
    const Config = {
        // SETTINGS
        USE_DIRECT: false, // Set to false to use Laravel Proxy and avoid CORS
        COMFYUI_HOST: window.location.hostname,
        COMFYUI_PORT: 8188,
        API_BASE: '/api/comfyui', // Your Laravel Proxy Prefix
        
        MAX_IMAGE_SIZE: 10 * 1024 * 1024,
        POLL_INTERVAL: 2000,
        RECONNECT_ATTEMPTS: 5,
        RECONNECT_DELAY: 2000,
        // --- DYNAMIC PROXY METHODS ---

        // Gets images via Laravel Proxy
        getImageUrl(filename, subfolder = '', type = 'output') {
            if (!filename) return '';
            let url = `${this.API_BASE}/view?filename=${encodeURIComponent(filename)}&type=${type}`;
            if (subfolder) url += `&subfolder=${encodeURIComponent(subfolder)}`;
            return url;
        },

        // Gets WebSocket URL (WebSockets are exempt from CORS)
        getWsUrl(clientId) {
        const id = clientId || `client-${Date.now()}`;
        // Use wss:// for HTTPS, ws:// for HTTP
        const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
        return `${protocol}//${this.COMFYUI_HOST}:${this.COMFYUI_PORT}/ws?clientId=${id}`;
        },
        getInterruptUrl() {
        return `${this.API_BASE}/interrupt`;
        },
        getProgressUrl() {
        return `${this.API_BASE}/progress`;
      },

        // Gets Object Info (Models/Nodes) via Proxy
        getObjectInfoUrl() {
            return `${this.API_BASE}/object_info`;
        },
        

        // Gets History via Proxy
        getHistoryUrl() {
            return `${this.API_BASE}/history`;
        },

        // --- UTILS ---
        validateResolution(width, height) {
            const multiple = 64;
            return {
                width: Math.round(width / multiple) * multiple,
                height: Math.round(height / multiple) * multiple
            };
        }
        
    };

    // Preprocessor Definitions
    window.Preprocessors = [
        { id: "canny", label: "Canny Edge", icon: "🔲", desc: "Sharp outlines" },
        { id: "depth", label: "Depth Map", icon: "🗺️", desc: "3D structure" },
        { id: "openpose", label: "OpenPose", icon: "🧍", desc: "Human skeleton" },
        { id: "scribble", label: "Scribble", icon: "✏️", desc: "Doodle to Art" }
    ];

    window.Config = Config;
}