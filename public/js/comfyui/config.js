// public/js/comfyui/api.js
if (typeof API === 'undefined') {
    const API = {
        async generate(payload) {
            try {
                console.log('Sending generate request...');
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                
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
                const response = await fetch('/api/comfyui/queue');
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                return await response.json();
            } catch (error) {
                console.error('Failed to fetch queue:', error);
                return null;
            }
        },
        
        async checkComfyUI() {
            try {
                const response = await fetch('/api/comfyui/health');
                if (!response.ok) throw new Error(`Server responded with ${response.status}`);
                const data = await response.json();
                if (data.status === 'online') {
                    Utils.updateStatus("✅ Connected to ComfyUI", "success");
                    return true;
                }
                return false;
            } catch (e) {
                console.error("Health Check Error:", e);
                Utils.updateStatus("❌ ComfyUI unreachable via Laravel Proxy", "error");
                return false;
            }
        },
        
        async getSystemStats() {
            try {
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
                
                if (data && data.checkpoints) {
                    console.log("✅ Models found:", data.checkpoints.length);
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
                const response = await fetch('/api/comfyui/queue', {
                    method: 'DELETE'
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
    
    window.API = API;
}

// Export for CommonJS if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { API };
}