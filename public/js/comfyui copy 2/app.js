// public/js/comfyui/app.js
// Main entry point for ComfyUI Studio

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', async () => {
    console.log('ComfyUI Studio starting...');
    
    // Check if we're already initialized via main.js
    if (window.uiManager && window.gallery && window.wsManager) {
        console.log('Already initialized via main.js');
        return;
    }
    
    try {
        // Check if required classes are available
        if (typeof GalleryManager === 'undefined') {
            console.error('GalleryManager not found');
            return;
        }
        
        if (typeof WebSocketManager === 'undefined') {
            console.error('WebSocketManager not found');
            return;
        }
        
        if (typeof UIManager === 'undefined') {
            console.error('UIManager not found');
            return;
        }
        
        // Initialize modules
        console.log('Initializing GalleryManager...');
        const gallery = new GalleryManager();
        
        console.log('Initializing WebSocketManager...');
        const wsManager = new WebSocketManager();
        
        console.log('Initializing UIManager...');
        const uiManager = new UIManager(wsManager, gallery);
        
        // Setup WebSocket callbacks
        wsManager.onExecutionComplete = () => {
            console.log('Execution complete');
            uiManager.isGenerating = false;
            
            const btn = document.getElementById("generateBtn");
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = "🚀 Generate with ControlNet";
            }
            
            setTimeout(() => {
                if (gallery && typeof gallery.loadAllImages === 'function') {
                    gallery.loadAllImages();
                }
            }, 1000);
        };
        
        wsManager.onImageReceived = (img) => {
            console.log('Image received:', img);
            if (gallery && typeof gallery.addImage === 'function') {
                gallery.addImage(img);
            }
        };
        
        wsManager.onStatusUpdate = (status) => {
            console.log('Status:', status);
        };
        
        // Connect WebSocket
        await wsManager.connect();
        
        // Initialize UI
        uiManager.init();
        
        // Load existing images
        await gallery.loadAllImages();
        
        // Make available globally
        window.gallery = gallery;
        window.wsManager = wsManager;
        window.uiManager = uiManager;
        
        console.log('✅ ComfyUI Studio ready');
        
    } catch (error) {
        console.error('Initialization error:', error);
    }
});