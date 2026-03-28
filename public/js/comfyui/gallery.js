// public/js/comfyui/gallery.js - Fixed GalleryManager class
// Guard against multiple loads
if (typeof GalleryManager === 'undefined') {
    class GalleryManager {
    constructor() {
        this.images = [];
        this.pollingInterval = null;
        this.currentPromptId = null;
        this.onImageAdded = null;
        this.maxImages = 100; // Maximum number of images to store
    }
    
    startPolling(promptId) {
        console.log('Starting polling for prompt:', promptId);
        this.currentPromptId = promptId;
        
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
        }
        
        let attempts = 0;
        const maxAttempts = 60; // 2 minutes (60 * 2 seconds)
        
        this.pollingInterval = setInterval(async () => {
            attempts++;
            
            try {
                const history = await API.fetchHistory();
                
                if (history[promptId]) {
                    const promptData = history[promptId];
                    
                    if (promptData.outputs) {
                        let imagesFound = false;
                        
                        for (const nodeId in promptData.outputs) {
                            const output = promptData.outputs[nodeId];
                            if (output.images && output.images.length > 0) {
                                imagesFound = true;
                                output.images.forEach(image => {
                                    this.addImage(image);
                                });
                            }
                        }
                        
                        if (imagesFound) {
                            console.log('Images found, stopping polling');
                            this.stopPolling();
                            Utils.updateStatus("✅ Generation complete!", "success");
                            
                            // Trigger callback if exists
                            if (this.onImageAdded) {
                                this.onImageAdded({ complete: true });
                            }
                        }
                    }
                }
                
                if (attempts >= maxAttempts) {
                    console.log('Polling timeout');
                    this.stopPolling();
                    Utils.updateStatus("⚠️ Generation timeout, but images may still appear", "warning");
                }
            } catch (error) {
                console.error('Error checking for new images:', error);
                if (attempts >= maxAttempts) {
                    this.stopPolling();
                }
            }
        }, 2000);
        
        Utils.updateStatus("🎨 Generation started, waiting for images...", "info");
    }
    
    addImage(image) {
        if (!image || !image.filename) {
            console.warn('Invalid image data:', image);
            return;
        }
        
        const imageUrl = Config.getImageUrl(image.filename, image.subfolder || '', image.type || 'output');
        
        // Check if image already exists
        const exists = this.images.some(img => img.filename === image.filename);
        if (!exists) {
            const newImage = {
                filename: image.filename,
                url: imageUrl,
                subfolder: image.subfolder || '',
                type: image.type || 'output',
                timestamp: Date.now()
            };
            
            this.images.unshift(newImage);
            
            // Limit number of images
            if (this.images.length > this.maxImages) {
                this.images = this.images.slice(0, this.maxImages);
            }
            
            this.renderGallery();
            
            // Trigger callback
            if (this.onImageAdded) {
                this.onImageAdded(newImage);
            }
            
            console.log(`Image added: ${image.filename}, Total: ${this.images.length}`);
        }
    }
    
    renderGallery() {
        const container = document.getElementById('images');
        if (!container) {
            console.warn('Gallery container not found');
            return;
        }
        
        if (this.images.length === 0) {
            container.innerHTML = `
                <div style="text-align: center; padding: 60px 20px; color: #6c757d;">
                    🎨 No images yet<br>
                    <small>Generated images will appear here</small>
                </div>
            `;
            return;
        }
        
        // let html = '<div class="gallery-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">';
        
        // this.images.forEach(image => {
        //     const date = new Date(image.timestamp);
        //     const timeStr = date.toLocaleTimeString();
        //     const dateStr = date.toLocaleDateString();
            
        //     // Escape filename for safe HTML
        //     const escapedFilename = this.escapeHtml(image.filename);
        //     const escapedUrl = this.escapeHtml(image.url);
            
        //     html += `
        //         <div class="gallery-item" style="position: relative; border-radius: 12px; overflow: hidden; background: #f0f0f0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: transform 0.2s;">
        //             <img src="${escapedUrl}" alt="${escapedFilename}" 
        //                  style="width: 100%; height: auto; cursor: pointer; display: block;"
        //                  onclick="window.open('${escapedUrl}', '_blank')"
        //                  onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'200\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23999\' stroke-width=\'2\'%3E%3Crect x=\'2\' y=\'2\' width=\'20\' height=\'20\'/%3E%3C/svg%3E'">
        //             <div style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.7); color: white; padding: 5px 8px; font-size: 11px; display: flex; justify-content: space-between; align-items: center;">
        //                 <span>${dateStr} ${timeStr}</span>
        //                 <button onclick="window.gallery.downloadImage('${escapedFilename}', '${image.subfolder}', '${image.type}')" 
        //                         style="background: none; border: none; color: white; cursor: pointer; font-size: 14px; padding: 2px 5px;"
        //                         onmouseover="this.style.backgroundColor='rgba(255,255,255,0.2)'"
        //                         onmouseout="this.style.backgroundColor='transparent'">⬇️</button>
        //             </div>
        //         </div>
        //     `;
        // });
        
        // html += '</div>';
        // container.innerHTML = html;
        // 1. Start the string with a full-width container
            let html = `
                <div class="gallery-grid" style="
                    display: grid; 
                    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); 
                    gap: 20px; 
                    width: 100%; 
                    padding: 10px;
                    box-sizing: border-box;
                ">`;
                    
            this.images.forEach(image => {
                const date = new Date(image.timestamp);
                const timeStr = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                const dateStr = date.toLocaleDateString();
                
                const escapedFilename = this.escapeHtml(image.filename);
                const escapedUrl = this.escapeHtml(image.url);
                
                html += `
                    <div class="gallery-item" style="
                        position: relative; 
                        border-radius: 12px; 
                        overflow: hidden; 
                        background: #1a1a1a; 
                        box-shadow: 0 4px 12px rgba(0,0,0,0.15); 
                        aspect-ratio: 1 / 1;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    ">
                        <img src="${escapedUrl}" alt="${escapedFilename}" 
                            style="width: 100%; height: 100%; object-fit: cover; cursor: pointer; display: block;"
                            onclick="window.open('${escapedUrl}', '_blank')"
                            onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'200\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23999\' stroke-width=\'2\'%3E%3Crect x=\'2\' y=\'2\' width=\'20\' height=\'20\'/%3E%3C/svg%3E'">
                        
                        <div style="
                            position: absolute; 
                            bottom: 0; left: 0; right: 0; 
                            background: linear-gradient(transparent, rgba(0,0,0,0.8)); 
                            color: white; 
                            padding: 12px 10px; 
                            font-size: 11px; 
                            display: flex; 
                            justify-content: space-between; 
                            align-items: center;
                            backdrop-filter: blur(2px);
                        ">
                            <span style="font-family: monospace;">${dateStr} ${timeStr}</span>
                            <button onclick="window.gallery.downloadImage('${escapedFilename}', '${image.subfolder}', '${image.type}')" 
                                    style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 4px; color: white; cursor: pointer; font-size: 14px; padding: 4px 8px; transition: 0.2s;"
                                    onmouseover="this.style.background='rgba(255,255,255,0.3)'"
                                    onmouseout="this.style.background='rgba(255,255,255,0.1)'">⬇️</button>
                        </div>
                    </div>
                `;
            });

            html += '</div>';

            // 2. CRITICAL: Clear the container's own layout styles if they conflict
            container.style.display = 'block'; 
            container.innerHTML = html;
    }
    
    async downloadImage(filename, subfolder = '', type = 'output') {
        try {
            const url = Config.getImageUrl(filename, subfolder, type);
            console.log('Downloading:', url);
            
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            
            const blob = await response.blob();
            const blobUrl = URL.createObjectURL(blob);
            
            const a = document.createElement('a');
            a.href = blobUrl;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(blobUrl);
            
            Utils.updateStatus(`✅ Downloaded: ${filename}`, "success");
        } catch (error) {
            console.error('Download error:', error);
            Utils.updateStatus(`❌ Failed to download: ${filename}`, "error");
        }
    }
    
    async loadAllImages() {
        try {
            Utils.updateStatus("🔄 Loading images from ComfyUI...", "info");
            
            const history = await API.fetchHistory();
            this.images = [];
            
            for (const promptId in history) {
                const promptData = history[promptId];
                if (promptData.outputs) {
                    for (const nodeId in promptData.outputs) {
                        const output = promptData.outputs[nodeId];
                        if (output.images && output.images.length > 0) {
                            output.images.forEach(image => {
                                this.images.push({
                                    filename: image.filename,
                                    url: Config.getImageUrl(image.filename, image.subfolder || '', image.type || 'output'),
                                    subfolder: image.subfolder || '',
                                    type: image.type || 'output',
                                    timestamp: promptData.timestamp || Date.now()
                                });
                            });
                        }
                    }
                }
            }
            
            // Sort by timestamp (newest first)
            this.images.sort((a, b) => b.timestamp - a.timestamp);
            
            // Limit number of images
            if (this.images.length > this.maxImages) {
                this.images = this.images.slice(0, this.maxImages);
            }
            
            this.renderGallery();
            Utils.updateStatus(`✅ Loaded ${this.images.length} images`, "success");
            
        } catch (error) {
            console.error('Error loading images:', error);
            Utils.updateStatus(`❌ Failed to load images: ${error.message}`, "error");
        }
    }
    
    stopPolling() {
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
            this.pollingInterval = null;
            this.currentPromptId = null;
            console.log('Polling stopped');
        }
    }
    
    clearGallery() {
        if (confirm('Are you sure you want to clear all images from the gallery?')) {
            this.images = [];
            this.renderGallery();
            Utils.updateStatus("🗑️ Gallery cleared", "info");
        }
    }
    
    async refresh() {
        await this.loadAllImages();
    }
    
    getImageCount() {
        return this.images.length;
    }
    
    getLatestImage() {
        return this.images[0] || null;
    }
    
    getImagesByTimeRange(startTime, endTime) {
        return this.images.filter(img => img.timestamp >= startTime && img.timestamp <= endTime);
    }
    
    deleteImage(filename) {
        const index = this.images.findIndex(img => img.filename === filename);
        if (index !== -1) {
            this.images.splice(index, 1);
            this.renderGallery();
            Utils.updateStatus(`🗑️ Deleted: ${filename}`, "info");
            return true;
        }
        return false;
    }
    
    // Helper method to escape HTML
    escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
    }
    // Make available globally
    window.GalleryManager = GalleryManager;

    // Export for use in other files (if using modules)
    if (typeof module !== 'undefined' && module.exports) {
        module.exports = { GalleryManager };
    }
}