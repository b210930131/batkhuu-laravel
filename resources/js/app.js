// public/js/comfyui/app.js - Fixed version
let wsManager, gallery, uiManager;

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', async () => {
    console.log('Initializing ComfyUI Studio...');
    
    try {
        // Check if all classes are loaded
        if (typeof GalleryManager === 'undefined') {
            console.error('❌ GalleryManager class not found!');
            Utils.updateStatus("❌ GalleryManager not loaded", "error");
            return;
        }
        
        if (typeof WebSocketManager === 'undefined') {
            console.error('❌ WebSocketManager class not found!');
            Utils.updateStatus("❌ WebSocketManager not loaded", "error");
            return;
        }
        
        if (typeof UIManager === 'undefined') {
            console.error('❌ UIManager class not found!');
            Utils.updateStatus("❌ UIManager not loaded", "error");
            return;
        }
        
        console.log('✅ All classes loaded');
        
        // Initialize modules
        console.log('Initializing GalleryManager...');
        gallery = new GalleryManager();
        
        // Set up gallery callback
        gallery.onImageAdded = (image) => {
            console.log('New image added to gallery:', image);
        };
        
        console.log('Initializing WebSocketManager...');
        wsManager = new WebSocketManager();
        
        console.log('Initializing UIManager...');
        uiManager = new UIManager(wsManager, gallery);
        
        // Setup WebSocket callbacks
        wsManager.onExecutionComplete = (promptId) => {
            console.log('Execution complete for prompt:', promptId);
            if (uiManager) uiManager.isGenerating = false;
            
            const btn = document.getElementById("generateBtn");
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = "🚀 Generate with ControlNet";
            }
            
            // Stop polling and refresh gallery
            if (gallery) {
                gallery.stopPolling();
                setTimeout(() => {
                    gallery.loadAllImages();
                }, 1000);
            }
        };
        
        wsManager.onImageReceived = (img) => {
            console.log('Image received via WebSocket:', img);
            if (gallery && typeof gallery.addImage === 'function') {
                gallery.addImage(img);
            }
        };
        
        wsManager.onProgress = (progressData) => {
            console.log('Generation progress:', progressData);
            if (uiManager) {
                uiManager.updateProgress(progressData);
            }
        };
        
        // Connect WebSocket
        console.log('Connecting WebSocket...');
        try {
            await wsManager.connect();
            console.log('✅ WebSocket connected');
        } catch (error) {
            console.warn('WebSocket connection failed:', error);
            Utils.updateStatus("⚠️ WebSocket connection failed - real-time updates disabled", "warning");
        }
        
        // Initialize UI
        console.log('Initializing UI...');
        uiManager.init();
        
        // Load existing images
        console.log('Loading images...');
        await gallery.loadAllImages();
        
        // Make available globally
        window.wsManager = wsManager;
        window.gallery = gallery;
        window.uiManager = uiManager;
        
        console.log('✅ ComfyUI Studio initialized successfully');
        
        // Update status
        const statusDiv = document.getElementById('status');
        if (statusDiv) {
            statusDiv.innerHTML = '✅ Ready • All systems operational';
            statusDiv.style.backgroundColor = '#d4edda';
        }
        
        // Check ComfyUI connection
        await API.checkComfyUI();
        
    } catch (error) {
        console.error('Initialization error:', error);
        const statusDiv = document.getElementById('status');
        if (statusDiv) {
            statusDiv.innerHTML = `❌ Initialization failed: ${error.message}`;
            statusDiv.style.backgroundColor = '#f8d7da';
        }
        Utils.updateStatus(`❌ Initialization failed: ${error.message}`, "error");
    }
});

// Handle page unload
window.addEventListener('beforeunload', () => {
    if (wsManager && wsManager.disconnect) {
        wsManager.disconnect();
    }
    if (gallery && gallery.stopPolling) {
        gallery.stopPolling();
    }
});