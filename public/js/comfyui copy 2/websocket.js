// public/js/comfyui/websocket.js - WebSocketManager class
// Guard against multiple loads
if (typeof WebSocketManager === 'undefined') {
    class WebSocketManager {
    constructor() {
        this.ws = null;
        this.clientId = null;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = Config.RECONNECT_ATTEMPTS || 5;
        this.reconnectDelay = 2000;
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
            
            this.ws.onclose = () => {
                console.log('WebSocket disconnected');
                this.isConnecting = false;
                
                if (this.reconnectAttempts < this.maxReconnectAttempts) {
                    this.reconnectAttempts++;
                    console.log(`Reconnecting in ${this.reconnectDelay}ms... (${this.reconnectAttempts}/${this.maxReconnectAttempts})`);
                    setTimeout(() => this.connect(), this.reconnectDelay);
                } else {
                    console.error('Max reconnect attempts reached');
                    if (this.onError) {
                        this.onError(new Error('WebSocket connection lost'));
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
        
        if (data.type === 'execution_start') {
            console.log('Execution started');
            if (this.onStatusUpdate) {
                this.onStatusUpdate('Generation started...');
            }
        }
        
        if (data.type === 'execution_success') {
            console.log('Execution success');
            if (this.onExecutionComplete) {
                this.onExecutionComplete();
            }
        }
        
        if (data.type === 'execution_error') {
            console.error('Execution error:', data);
            if (this.onError) {
                this.onError(new Error(data.message || 'Execution error'));
            }
        }
        
        if (data.type === 'progress') {
            // Validate progress data to prevent NaN
            const value = parseInt(data.value) || 0;
            const max = parseInt(data.max) || 1;
            const percent = Math.min(100, Math.max(0, (value / max) * 100));
            
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
            if (data.output && data.output.images) {
                data.output.images.forEach(img => {
                    if (this.onImageReceived) {
                        this.onImageReceived(img);
                    }
                });
            }
        }
    }
    
    disconnect() {
        if (this.ws) {
            this.ws.close();
            this.ws = null;
        }
    }
    
    async interrupt() {
        try {
            const response = await fetch(Config.getInterruptUrl(), {
                method: 'POST'
            });
            return response.ok;
        } catch (error) {
            console.error('Failed to interrupt:', error);
            return false;
        }
    }
    
    send(data) {
        if (this.isConnected()) {
            this.ws.send(JSON.stringify(data));
        } else {
            console.error('WebSocket not connected');
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