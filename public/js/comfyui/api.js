
// Guard against multiple loads
if (typeof API === 'undefined') {
    const API = {
    async generate(payload) {
        try {
            // Log the payload for debugging (remove sensitive data)
            const logPayload = {
                ...payload,
                controlnet: payload.controlnet ? {
                    ...payload.controlnet,
                    image_base64: payload.controlnet.image_base64 ? '[BASE64_IMAGE]' : null
                } : null
            };
            console.log('Sending generate request:', logPayload);
            
            // Get CSRF token safely
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            if (!csrfToken) {
                console.warn('CSRF token not found');
            }
            
            // FIXED: Use the correct endpoint that exists in your Laravel routes
            const response = await fetch('/api/generate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(payload)
            });
            
            if (!response.ok) {
                let errorMessage = `HTTP ${response.status}`;
                try {
                    const errorData = await response.json();
                    console.error('API Error Response:', errorData);
                    errorMessage = errorData.error || errorData.message || JSON.stringify(errorData);
                } catch (e) {
                    const text = await response.text();
                    errorMessage = `${errorMessage}: ${text.substring(0, 200)}`;
                }
                throw new Error(errorMessage);
            }
            
            const data = await response.json();
            console.log('Generate response:', data);
            return data;
            
        } catch (error) {
            console.error('Generate API error:', error);
            throw error;
        }
    },
    
    async fetchHistory() {
        try {
            console.log('Fetching history from Laravel proxy...');
            const response = await fetch('/api/comfyui/history');
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            
            const data = await response.json();
            console.log('History fetched, prompts:', Object.keys(data).length);
            return data;
        } catch (error) {
            console.error('Failed to fetch history:', error);
            return {};
        }
    },
    
    async fetchQueue() {
        try {
            console.log('Fetching queue status from Laravel proxy...');
            const response = await fetch('/api/comfyui/queue');
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Failed to fetch queue:', error);
            return null;
        }
    },
    
    async fetchProgress(promptId) {
        try {
            const response = await fetch(`${Config.getProgressUrl()}?prompt_id=${promptId}`);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            
            return await response.json();
        } catch (error) {
            console.error('Failed to fetch progress:', error);
            return null;
        }
    },
async checkComfyUI() {
    try {
        // ALWAYS use the Laravel relative path
        const response = await fetch('/api/comfyui/health'); 
        
        if (!response.ok) {
            throw new Error(`Server responded with ${response.status}`);
        }

        const data = await response.json();
        if (data.status === 'online') {
            Utils.updateStatus("✅ Connected to ComfyUI", "success");
            return true;
        }
        return false;
    } catch (e) {
        console.error("Health Check Error:", e);
        // This is the error message you are seeing:
        Utils.updateStatus("❌ ComfyUI unreachable via Laravel Proxy", "error");
        return false;
    }
},
    
   async getSystemStats() {
    try {
        // Update URL to use Laravel Proxy
        const response = await fetch('/api/comfyui/system_stats');
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        return await response.json();
    } catch (error) {
        console.error('Failed to fetch system stats:', error);
        return null;
    }
},
    
    async getObjectInfo() {
        try {
            const response = await fetch('/api/comfyui/object_info');
            
            if (!response.ok) throw new Error(`Server responded with ${response.status}`);
            
            const data = await response.json();
            
            // Fix: Check if checkpoints exist directly, 
            // since some Laravel responses might not have a .success wrapper
            if (data && (data.checkpoints || data.success)) {
                console.log("✅ Models found:", data.checkpoints?.length || 0);
                return data; 
            }
            
            console.warn("⚠️ API returned success but no checkpoints found.");
            return null;
        } catch (error) {
            console.error('❌ Proxy fetch failed:', error);
            return null;
        }
    },
    
    async interrupt() {
        try {
            const response = await fetch('/api/comfyui/interrupt', {
                method: 'POST'
            });
            
            if (response.ok) {
                Utils.updateStatus("✅ Generation interrupted", "success");
                return true;
            }
            return false;
        } catch (error) {
            console.error('Failed to interrupt:', error);
            return false;
        }
    },
    
    async clearQueue() {
    try {
        // Update URL to use Laravel Proxy
        const response = await fetch('/api/comfyui/queue', {
            method: 'DELETE' // Ensure your Laravel Controller has a method for this!
        });
        if (response.ok) {
            Utils.updateStatus("✅ Queue cleared", "success");
            return true;
        }
        return false;
    } catch (error) {
        console.error('Failed to clear queue:', error);
        return false;
    }
}
    };
    // Assign to window if not already assigned
    window.API = API;
}

// Export for CommonJS if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { API };
}

// Add fetch interceptor for debugging
const originalFetch = window.fetch;
window.fetch = async function(...args) {
    const url = args[0];
    const options = args[1];
    
    // Log all API calls
    if (typeof url === 'string' && (url.includes('/api/') || url.includes(Config.COMFYUI_HOST))) {
        console.log(`🌐 Fetch: ${options?.method || 'GET'} ${url}`);
        
        if (options?.body && url.includes('/api/generate')) {
            try {
                const body = JSON.parse(options.body);
                const logBody = {
                    ...body,
                    controlnet: body.controlnet ? {
                        ...body.controlnet,
                        image_base64: body.controlnet.image_base64 ? '[BASE64_IMAGE]' : null
                    } : null
                };
                console.log('Request body:', logBody);
            } catch (e) {
                // Ignore parsing errors
            }
        }
    }
    
    try {
        const response = await originalFetch.apply(this, args);
        
        if (!response.ok && url.includes('/api/')) {
            console.error(`❌ Fetch failed: ${url} - ${response.status}`);
        }
        
        return response;
    } catch (error) {
        console.error(`❌ Fetch error: ${url} - ${error.message}`);
        throw error;
    }
};
document.addEventListener('DOMContentLoaded', async () => {
    console.log('Initializing ComfyUI Studio...');
    
    try {
        // Check if GalleryManager is loaded
        if (typeof GalleryManager === 'undefined') {
            console.error('GalleryManager class not found!');
            Utils.updateStatus("❌ GalleryManager not loaded", "error");
            return;
        }
        
        console.log('✅ GalleryManager found');
        
        // Initialize modules
        gallery = new GalleryManager();  // <-- GalleryManager ашиглах
        wsManager = new WebSocketManager();
        uiManager = new UIManager(wsManager, gallery);
        
        // ... остальной код ...
        
        // Make available globally
        window.gallery = gallery;
        window.wsManager = wsManager;
        window.uiManager = uiManager;
        
    } catch (error) {
        console.error('Initialization error:', error);
        Utils.updateStatus(`❌ Initialization failed: ${error.message}`, "error");
    }
});