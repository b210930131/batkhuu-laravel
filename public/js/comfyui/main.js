// public/js/comfyui/main.js - Main entry point for ComfyUI Studio
let wsManager, gallery, uiManager;
let initAttempts = 0;
const MAX_INIT_ATTEMPTS = 3;

// Guard to prevent multiple initializations
let isInitialized = false;

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', async () => {
    if (isInitialized) {
        console.log('Main.js already initialized');
        return;
    }
    isInitialized = true;
    console.log('Initializing ComfyUI Studio...');
    
    try {
        // Check if all required global objects exist
        const requiredGlobals = {
            'GalleryManager': 'gallery.js',
            'WebSocketManager': 'websocket.js',
            'UIManager': 'ui.js',
            'Config': 'config.js',
            'API': 'api.js',
            'Utils': 'utils.js'
        };
        
        const missingGlobals = [];
        for (const [global, file] of Object.entries(requiredGlobals)) {
            if (typeof window[global] === 'undefined') {
                missingGlobals.push(`${global} (from ${file})`);
            }
        }
        
        if (missingGlobals.length > 0) {
            console.error('Missing required classes:', missingGlobals);
            Utils.updateStatus(`❌ Missing: ${missingGlobals.join(', ')}`, "error");
            
            // Attempt to reload after showing error
            setTimeout(() => {
                if (confirm('Missing required scripts. Reload page?')) {
                    window.location.reload();
                }
            }, 3000);
            return;
        }
        
        // ✅ ADD THIS: Initialize selectedPreprocessor if not already set
        if (typeof window.selectedPreprocessor === 'undefined') {
            window.selectedPreprocessor = 'canny';
            console.log('Initialized selectedPreprocessor to:', window.selectedPreprocessor);
        }
        
        // Check ComfyUI connection (don't block if fails)
        const isConnected = await checkComfyUIConnection();
        if (isConnected) {
            console.log('✅ ComfyUI is reachable');
        } else {
            console.warn('⚠️ ComfyUI is not reachable, some features may not work');
            Utils.updateStatus("⚠️ ComfyUI not reachable, check if ComfyUI is running on port 8188", "warning");
        }
        
        // Initialize modules with error boundaries
        console.log('Initializing GalleryManager...');
        try {
            gallery = new GalleryManager();
            // Set max images limit
            if (gallery.setMaxImages) {
                gallery.setMaxImages(100);
            }
        } catch (error) {
            console.error('Failed to initialize GalleryManager:', error);
            throw new Error(`GalleryManager initialization failed: ${error.message}`);
        }
        
        console.log('Initializing WebSocketManager...');
        try {
            wsManager = new WebSocketManager();
        } catch (error) {
            console.error('Failed to initialize WebSocketManager:', error);
            throw new Error(`WebSocketManager initialization failed: ${error.message}`);
        }
        
        console.log('Initializing UIManager...');
        try {
            uiManager = new UIManager(wsManager, gallery);
        } catch (error) {
            console.error('Failed to initialize UIManager:', error);
            throw new Error(`UIManager initialization failed: ${error.message}`);
        }
        
        // Setup WebSocket callbacks
        setupWebSocketCallbacks();
        
        // Connect WebSocket with timeout
        console.log('Connecting WebSocket...');
        try {
            await Promise.race([
                wsManager.connect(),
                new Promise((_, reject) => 
                    setTimeout(() => reject(new Error('WebSocket connection timeout')), 10000)
                )
            ]);
            console.log('✅ WebSocket connected');
        } catch (error) {
            console.warn('WebSocket connection failed:', error);
            Utils.updateStatus("⚠️ WebSocket connection failed - real-time updates disabled", "warning");
            // Continue without WebSocket - will use polling
        }
        
        // Initialize UI
        console.log('Initializing UI...');
        await uiManager.init();
        
        // Load all existing images from history
        console.log('Loading existing images...');
        try {
            await gallery.loadAllImages();
            console.log(`✅ Loaded ${gallery.getImageCount()} images`);
        } catch (error) {
            console.error('Failed to load images:', error);
            Utils.updateStatus("⚠️ Failed to load existing images", "warning");
        }
        
        // Set up periodic connection check
        setupConnectionHealthCheck();
        
        // Make globally available for debugging
        window.wsManager = wsManager;
        window.gallery = gallery;
        window.uiManager = uiManager;
        
        // Initialize keyboard shortcuts
        setupKeyboardShortcuts();
        
        // Update status with system info
        await updateSystemInfo();
        
        console.log('✅ ComfyUI Studio initialized successfully');
        Utils.updateStatus("✅ Ready - All systems operational", "success");
        
        // Dispatch initialization complete event
        window.dispatchEvent(new CustomEvent('comfyui:initialized', {
            detail: { wsManager, gallery, uiManager }
        }));
        
    } catch (error) {
        console.error('Initialization error:', error);
        Utils.updateStatus(`❌ Initialization failed: ${error.message}`, "error");
        
        // Attempt recovery
        if (initAttempts < MAX_INIT_ATTEMPTS) {
            initAttempts++;
            console.log(`Attempting recovery (${initAttempts}/${MAX_INIT_ATTEMPTS})...`);
            setTimeout(() => {
                window.location.reload();
            }, 3000);
        } else {
            console.error('Max initialization attempts reached');
            showRecoveryOptions();
        }
    }
});

function setupWebSocketCallbacks() {
    if (!wsManager) return;
    
    wsManager.onExecutionComplete = (promptId) => {
        console.log('🎨 Execution complete for prompt:', promptId);
        if (uiManager) {
            uiManager.isGenerating = false;
        }
        
        const btn = document.getElementById("generateBtn");
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = "🚀 Generate";
        }
        
        // Reload all images to include the new one
        Utils.updateStatus("✅ Generation complete!", "success");
        
        // Add a small delay to ensure image is saved
        setTimeout(async () => {
            if (gallery && typeof gallery.loadAllImages === 'function') {
                await gallery.loadAllImages();
                
                // Scroll to newest image
                const imagesContainer = document.getElementById('images');
                if (imagesContainer && imagesContainer.firstChild) {
                    imagesContainer.firstChild.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            } else {
                console.error('Gallery.loadAllImages not available');
            }
        }, 1500);
    };
    
    wsManager.onImageReceived = (img) => {
        console.log('📸 Image received via WebSocket:', img);
        
        // Add the new image immediately
        if (gallery && typeof gallery.addImage === 'function') {
            if (img.filename) {
                gallery.addImage(img);
                Utils.updateStatus("✨ New image generated!", "success");
                
                // Play notification sound (optional)
                playNotificationSound();
            } else {
                console.warn('Invalid image data received:', img);
            }
        } else {
            console.error('Gallery.addImage not available');
        }
    };
    
    wsManager.onStatusUpdate = (status) => {
        console.log('Status update:', status);
        Utils.updateStatus(status, "info");
    };
    
    wsManager.onError = (error) => {
        console.error('WebSocket error:', error);
        Utils.updateStatus(`⚠️ WebSocket error: ${error.message || 'Unknown error'}`, "error");
    };
    
    wsManager.onProgress = (progress) => {
        if (uiManager && uiManager.updateProgress) {
            uiManager.updateProgress(progress);
        }
        Utils.updateStatus(`🎨 Generating... ${progress.percentage || 0}%`, "info");
    };
}

async function checkComfyUIConnection() {
    try {
        // Use the API object we already defined in api.js
        return await API.checkComfyUI();
    } catch (error) {
        return false;
    }
}

function setupConnectionHealthCheck() {
    // Check connection every 30 seconds
    setInterval(async () => {
        if (wsManager && !wsManager.isConnected()) {
            console.log('WebSocket disconnected, attempting reconnect...');
            try {
                await wsManager.connect();
                Utils.updateStatus("✅ WebSocket reconnected", "success");
            } catch (error) {
                console.warn('WebSocket reconnection failed:', error);
            }
        }
    }, 30000);
}

function setupKeyboardShortcuts() {
    document.addEventListener('keydown', (e) => {
        // Don't trigger if typing in input/textarea
        const target = e.target;
        const isTyping = target.tagName === 'INPUT' || target.tagName === 'TEXTAREA' || target.isContentEditable;
        
        // Ctrl+Enter to generate
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter' && !isTyping) {
            e.preventDefault();
            if (uiManager && !uiManager.isGenerating) {
                uiManager.generate();
            }
        }
        
        // Ctrl+R to refresh gallery
        if ((e.ctrlKey || e.metaKey) && e.key === 'r' && !isTyping) {
            e.preventDefault();
            if (gallery && gallery.loadAllImages) {
                gallery.loadAllImages();
                Utils.updateStatus("🔄 Gallery refreshed", "success");
            }
        }
        
        // Esc to stop generation
        if (e.key === 'Escape' && uiManager && uiManager.isGenerating) {
            e.preventDefault();
            if (wsManager && wsManager.interrupt) {
                wsManager.interrupt();
                Utils.updateStatus("⏹️ Generation interrupted", "warning");
            }
        }
        
        // Ctrl+Shift+C to clear gallery
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'C') {
            e.preventDefault();
            if (gallery && gallery.clearGallery) {
                gallery.clearGallery();
                Utils.updateStatus("🗑️ Gallery cleared", "info");
            }
        }
    });
}

async function updateSystemInfo() {
    try {
        const stats = await API.getSystemStats();
        if (stats) {
            console.log('System stats:', stats);
            // Could display VRAM usage, etc. in UI
        }
        
        const objectInfo = await API.getObjectInfo();
        if (objectInfo) {
            const modelCount = Object.keys(objectInfo).filter(key => 
                key.includes('model') || key.includes('checkpoint')
            ).length;
            console.log(`Found ${modelCount} models available`);
        }
    } catch (error) {
        console.log('Failed to fetch system info:', error);
    }
}

function playNotificationSound() {
    // Optional: Play a subtle notification sound
    // You can add a simple beep or use Web Audio API
    try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.value = 880;
        gainNode.gain.value = 0.1;
        
        oscillator.start();
        gainNode.gain.exponentialRampToValueAtTime(0.00001, audioContext.currentTime + 0.5);
        oscillator.stop(audioContext.currentTime + 0.5);
        
        // Close context after sound
        setTimeout(() => audioContext.close(), 1000);
    } catch (error) {
        // Silently fail if audio not supported
        console.log('Notification sound not supported');
    }
}

function showRecoveryOptions() {
    const recoveryDiv = document.createElement('div');
    recoveryDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        z-index: 10000;
        max-width: 300px;
    `;
    recoveryDiv.innerHTML = `
        <h3>⚠️ Initialization Failed</h3>
        <p>Failed to initialize after ${MAX_INIT_ATTEMPTS} attempts.</p>
        <button onclick="window.location.reload()" style="margin: 5px; padding: 8px 16px;">Reload Page</button>
        <button onclick="window.debug?.testConnection()" style="margin: 5px; padding: 8px 16px;">Test Connection</button>
        <button onclick="this.parentElement.remove()" style="margin: 5px; padding: 8px 16px;">Dismiss</button>
    `;
    document.body.appendChild(recoveryDiv);
}

// Handle page unload
window.addEventListener('beforeunload', () => {
    if (wsManager && wsManager.disconnect) {
        console.log('Disconnecting WebSocket...');
        wsManager.disconnect();
    }
    
    // Clean up intervals and timeouts
    if (window._connectionCheckInterval) {
        clearInterval(window._connectionCheckInterval);
    }
});

// Handle offline/online events
window.addEventListener('online', async () => {
    console.log('Network online, reconnecting...');
    Utils.updateStatus("🌐 Network restored, reconnecting...", "info");
    
    if (wsManager && wsManager.connect) {
        try {
            await wsManager.connect();
            Utils.updateStatus("✅ Reconnected to server", "success");
        } catch (error) {
            console.error('Reconnection failed:', error);
        }
    }
    
    if (gallery && gallery.loadAllImages) {
        await gallery.loadAllImages();
    }
});

window.addEventListener('offline', () => {
    console.log('Network offline');
    Utils.updateStatus("⚠️ Network offline, some features may not work", "warning");
});

// Helper function for debugging
window.debug = {
    getState: () => ({
        wsConnected: wsManager?.isConnected ? wsManager.isConnected() : false,
        isGenerating: uiManager?.isGenerating || false,
        imageCount: gallery?.getImageCount ? gallery.getImageCount() : 0,
        selectedPreprocessor: uiManager?.selectedPreprocessor,
        hasControlImage: !!(uiManager?.currentControlImageBase64),
        comfyUIHost: Config?.COMFYUI_HOST,
        comfyUIPort: Config?.COMFYUI_PORT
    }),
    
    reloadGallery: async () => {
        if (gallery && gallery.loadAllImages) {
            await gallery.loadAllImages();
            console.log('Gallery reloaded');
            return true;
        }
        return false;
    },
    
    clearGallery: () => {
        if (gallery && gallery.clearGallery) {
            gallery.clearGallery();
            console.log('Gallery cleared');
            return true;
        }
        return false;
    },
    
    testConnection: async () => {
        console.log('Testing ComfyUI connection...');
        const connected = await API.checkComfyUI();
        console.log(`ComfyUI connection: ${connected ? 'OK' : 'FAILED'}`);
        return connected;
    },
    
    getLogs: () => {
        return {
            initAttempts,
            timestamp: new Date().toISOString(),
            modules: {
                wsManager: !!wsManager,
                gallery: !!gallery,
                uiManager: !!uiManager,
                config: !!Config,
                api: !!API,
                utils: !!Utils
            }
        };
    },
    
    forceReconnect: async () => {
        if (wsManager && wsManager.disconnect && wsManager.connect) {
            wsManager.disconnect();
            await wsManager.connect();
            console.log('Forced WebSocket reconnection');
            return true;
        }
        return false;
    }
};

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { wsManager, gallery, uiManager };
}