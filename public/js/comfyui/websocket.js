// public/js/comfyui/websocket.js - WebSocketManager class
// Guard against multiple loads
if (typeof WebSocketManager === 'undefined') {
    class WebSocketManager {
        constructor() {
            this.ws = null;
            this.clientId = null;
            this.reconnectAttempts = 0;
            // Use Config values with fallbacks
            this.maxReconnectAttempts = Config.RECONNECT_ATTEMPTS || 5;
            this.reconnectDelay = Config.RECONNECT_DELAY || 2000; // Use Config value
            this.isConnecting = false;
            
            // Callbacks
            this.onExecutionComplete = null;
            this.onImageReceived = null;
            this.onStatusUpdate = null;
            this.onError = null;
        }
        
        getClientId() {
            if (!this.clientId) {
                this.clientId = Utils.generateId();
            }
            return this.clientId;
        }
        
        isConnected() {
            return this.ws && this.ws.readyState === WebSocket.OPEN;
        }
        
        async connect() {
            if (this.isConnecting) {
                console.log('Already connecting...');
                return;
            }
            
            if (this.isConnected()) {
                console.log('Already connected');
                return;
            }
            
            this.isConnecting = true;
            
            try {
                // Use Config.getWsUrl() which now exists
                const wsUrl = Config.getWsUrl(this.getClientId());
                console.log('Connecting to WebSocket:', wsUrl);
                
                this.ws = new WebSocket(wsUrl);
                
                this.ws.onopen = () => {
                    console.log('WebSocket connected');
                    this.reconnectAttempts = 0;
                    this.isConnecting = false;
                    if (this.onStatusUpdate) {
                        this.onStatusUpdate('WebSocket connected');
                    }
                };
                
                this.ws.onmessage = (event) => {
                    try {
                        const data = JSON.parse(event.data);
                        this.handleMessage(data);
                    } catch (e) {
                        console.error('Failed to parse WebSocket message:', e);
                    }
                };
                
                this.ws.onerror = (error) => {
                    console.error('WebSocket error:', error);
                    if (this.onError) {
                        this.onError(error);
                    }
                };
                
                this.ws.onclose = (event) => {
                    console.log('WebSocket disconnected, code:', event.code, 'reason:', event.reason);
                    this.isConnecting = false;
                    
                    // Only attempt reconnect if not a normal closure (code 1000)
                    if (event.code !== 1000 && this.reconnectAttempts < this.maxReconnectAttempts) {
                        this.reconnectAttempts++;
                        console.log(`Reconnecting in ${this.reconnectDelay}ms... (${this.reconnectAttempts}/${this.maxReconnectAttempts})`);
                        setTimeout(() => this.connect(), this.reconnectDelay);
                    } else if (event.code !== 1000) {
                        console.error('Max reconnect attempts reached');
                        if (this.onError) {
                            this.onError(new Error('WebSocket connection lost after ' + this.maxReconnectAttempts + ' attempts'));
                        }
                    }
                };
                
            } catch (error) {
                console.error('Failed to connect WebSocket:', error);
                this.isConnecting = false;
                if (this.onError) {
                    this.onError(error);
                }
            }
        }
        
        handleMessage(data) {
            console.log('WebSocket message:', data);
            
            // Handle different message types based on ComfyUI protocol
            if (data.type === 'execution_start') {
                console.log('Execution started for prompt:', data.data?.prompt_id);
                if (this.onStatusUpdate) {
                    this.onStatusUpdate('🎨 Generation started...');
                }
            }
            
            if (data.type === 'execution_success') {
                console.log('Execution success for prompt:', data.data?.prompt_id);
                if (this.onExecutionComplete) {
                    this.onExecutionComplete(data.data?.prompt_id);
                }
                if (this.onStatusUpdate) {
                    this.onStatusUpdate('✅ Generation complete!');
                }
            }
            
            if (data.type === 'execution_error') {
                console.error('Execution error:', data);
                if (this.onError) {
                    this.onError(new Error(data.message || 'Execution error'));
                }
                if (this.onStatusUpdate) {
                    this.onStatusUpdate('❌ Generation failed: ' + (data.message || 'Unknown error'));
                }
            }
            
            if (data.type === 'progress') {
                // Validate progress data to prevent NaN
                const value = parseInt(data.data?.value) || 0;
                const max = parseInt(data.data?.max) || 1;
                const percent = Math.min(100, Math.max(0, (value / max) * 100));
                
                if (this.onProgress) {
                    this.onProgress({ value, max, percent });
                }
                
                if (this.onStatusUpdate) {
                    // Create a nice progress bar visualization
                    const barLength = 20;
                    const filled = Math.round((percent / 100) * barLength);
                    const empty = barLength - filled;
                    const bar = '█'.repeat(filled) + '░'.repeat(empty);
                    this.onStatusUpdate(`⏳ Generating... [${bar}] ${percent.toFixed(0)}%`);
                }
            }
            
            if (data.type === 'executed') {
                // Check for images in output
                if (data.data?.output?.images) {
                    data.data.output.images.forEach(img => {
                        console.log('Image received:', img.filename);
                        if (this.onImageReceived) {
                            this.onImageReceived(img);
                        }
                    });
                }
            }
        }
        
        disconnect() {
            if (this.ws) {
                // Use code 1000 for normal closure
                this.ws.close(1000, 'Normal closure');
                this.ws = null;
                console.log('WebSocket manually disconnected');
            }
            this.reconnectAttempts = this.maxReconnectAttempts; // Prevent auto-reconnect
        }
        
        async interrupt() {
            try {
                const response = await fetch(Config.getInterruptUrl(), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                });
                
                if (response.ok) {
                    console.log('Interrupt sent successfully');
                    return true;
                } else {
                    console.error('Interrupt failed:', response.status);
                    return false;
                }
            } catch (error) {
                console.error('Failed to interrupt:', error);
                return false;
            }
        }
        
        send(data) {
            if (this.isConnected()) {
                this.ws.send(JSON.stringify(data));
                console.log('Message sent:', data);
            } else {
                console.error('WebSocket not connected, cannot send message');
            }
        }
    }
    
    // Assign to window if not already assigned
    window.WebSocketManager = WebSocketManager;
}

// Export for CommonJS if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { WebSocketManager };
}