<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 20px;
        color: #1f2937;
    }
    
    .container {
        max-width: 1600px; /* Widened for dashboard layout */
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1.2fr 0.8fr; /* Balanced for controls vs gallery/health */
        gap: 25px;
    }
    
    .card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        overflow: hidden;
        margin-bottom: 20px;
        border: 1px solid rgba(255,255,255,0.1);
    }
    
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px 20px;
        font-weight: 600;
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    /* Form Elements */
    .control-group {
        padding: 15px 20px;
        border-bottom: 1px solid #f1f5f9;
    }
    
    .control-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        font-size: 13px;
        color: #4b5563;
    }
    
    .control-group input, 
    .control-group select, 
    .control-group textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 14px;
        background: #f8fafc;
        transition: all 0.2s;
    }
    
    .control-group input:focus, 
    .control-group select:focus, 
    .control-group textarea:focus {
        outline: none;
        border-color: #667eea;
        background: white;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    /* Health & System Dashboard Specifics */
    .btn-group {
        display: flex;
        gap: 8px;
        margin-bottom: 15px;
    }

    .vram-container {
        background: #e2e8f0;
        border-radius: 10px;
        height: 12px;
        width: 100%;
        margin: 8px 0;
        overflow: hidden;
        border: 1px solid #cbd5e1;
    }

    .vram-bar {
        background: linear-gradient(90deg, #10b981 0%, #f59e0b 70%, #ef4444 100%);
        height: 100%;
        width: 0%;
        transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .debug-log {
        background: #0f172a;
        color: #94a3b8;
        padding: 15px;
        border-radius: 8px;
        font-family: 'Fira Code', 'Courier New', monospace;
        font-size: 11px;
        max-height: 250px;
        overflow-y: auto;
        line-height: 1.5;
        border: 1px solid #1e293b;
    }

    .status-badge {
        padding: 4px 10px;
        border-radius: 9999px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Buttons */
    button {
        padding: 10px 18px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    button:hover {
        filter: brightness(1.05);
        transform: translateY(-1px);
    }

    button:active {
        transform: translateY(0);
    }

    /* Gallery Grid */
    #images {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 15px;
        padding: 20px;
    }

    .image-card {
        border-radius: 12px;
        overflow: hidden;
        background: #f1f5f9;
        aspect-ratio: 1 / 1;
        border: 1px solid #e2e8f0;
    }

    .image-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Animations */
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .card {
        animation: slideIn 0.4s ease-out forwards;
    }

    /* Mobile Responsiveness */
    @media (max-width: 1024px) {
        .container {
            grid-template-columns: 1fr;
        }
        
        body {
            padding: 10px;
        }
    }
</style>