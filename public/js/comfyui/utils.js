// public/js/comfyui/utils.js - Utils class
// Guard against multiple loads
if (typeof Utils === 'undefined') {
    const Utils = {
    // Compress image
    async compressImage(base64, maxWidth = 1024, maxHeight = 1024) {
        return new Promise((resolve) => {
            const img = new Image();
            img.onload = () => {
                let width = img.width;
                let height = img.height;
                
                if (width > maxWidth || height > maxHeight) {
                    if (width > height) {
                        height = (height * maxWidth) / width;
                        width = maxWidth;
                    } else {
                        width = (width * maxHeight) / height;
                        height = maxHeight;
                    }
                }
                
                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);
                
                const compressedBase64 = canvas.toDataURL('image/jpeg', 0.85);
                resolve(compressedBase64.split(',')[1]);
            };
            img.src = `data:image/png;base64,${base64}`;
        });
    },
    
    // Update status message
    updateStatus(message, type = "info") {
        const statusDiv = document.getElementById("status");
        if (statusDiv) {
            const colors = {
                success: "#d4edda",
                error: "#f8d7da",
                warning: "#fff3cd",
                info: "#e7f3ff"
            };
            statusDiv.style.backgroundColor = colors[type] || colors.info;
            statusDiv.innerHTML = message;
            statusDiv.style.color = "#000";
            
            // Auto clear after 5 seconds for success/error
            if (type === "success" || type === "error") {
                setTimeout(() => {
                    if (statusDiv.innerHTML === message) {
                        statusDiv.style.backgroundColor = "#f0f0f0";
                        statusDiv.innerHTML = "✅ Ready";
                    }
                }, 5000);
            }
        }
        console.log(`[${type.toUpperCase()}] ${message}`);
    },
    
    // Format file size
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    },
    
    // Generate random ID
    generateId() {
        return Math.random().toString(36).substr(2, 9);
    }
    };
    // Assign to window if not already assigned
    window.Utils = Utils;
}

// Export for CommonJS if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { Utils };
}