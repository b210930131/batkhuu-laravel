<style>
    * {
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
        background: #f1f4f9;
        margin: 0;
        padding: 24px;
        color: #1e293b;
    }

    .app-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    h1 {
        font-size: 1.9rem;
        font-weight: 600;
        background: linear-gradient(135deg, #1e293b, #2d3a4b);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        margin-bottom: 0.25rem;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .subhead {
        color: #5b6e8c;
        margin-bottom: 24px;
        border-left: 3px solid #8b5cf6;
        padding-left: 16px;
        font-weight: 500;
    }

    .main-grid {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 24px;
    }

    .card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.03), 0 2px 4px rgba(0,0,0,0.05);
        padding: 20px 24px;
        margin-bottom: 24px;
        transition: all 0.2s;
        border: 1px solid #e9edf2;
    }

    .card-header {
        font-weight: 600;
        font-size: 1.2rem;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        border-bottom: 2px solid #eff3f8;
        padding-bottom: 8px;
    }

    .control-group {
        margin-bottom: 20px;
    }

    label {
        font-weight: 600;
        display: block;
        margin-bottom: 8px;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        color: #4a5b7a;
    }

    select, textarea, input {
        width: 100%;
        padding: 12px 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 16px;
        font-size: 0.9rem;
        background: #ffffff;
        transition: 0.2s;
        font-family: inherit;
    }

    select:focus, textarea:focus, input:focus {
        outline: none;
        border-color: #8b5cf6;
        box-shadow: 0 0 0 3px rgba(139,92,246,0.1);
    }

    textarea {
        font-family: 'SF Mono', monospace;
        resize: vertical;
    }

    .param-row {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        align-items: center;
    }

    .param-item {
        flex: 1;
        min-width: 100px;
    }

    button {
        background: #1e293b;
        color: white;
        border: none;
        padding: 12px 28px;
        border-radius: 40px;
        font-weight: 600;
        cursor: pointer;
        font-size: 1rem;
        transition: 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }

    button:hover {
        background: #0f172a;
        transform: translateY(-1px);
    }

    button:disabled {
        background: #b9c2d4;
        cursor: not-allowed;
        transform: none;
    }

    .btn-primary {
        background: linear-gradient(105deg, #8b5cf6, #6d28d9);
        box-shadow: 0 4px 12px rgba(139,92,246,0.25);
    }

    .btn-primary:hover {
        background: linear-gradient(105deg, #7c3aed, #5b21b6);
    }

    #status {
        background: #f1f5f9;
        padding: 12px 16px;
        border-radius: 40px;
        font-size: 0.85rem;
        font-weight: 500;
        margin: 16px 0 0;
    }

    .error {
        background: #fee2e2;
        color: #b91c1c;
    }

    .success {
        background: #e0f2fe;
        color: #0c4a6e;
    }

    .controlnet-card {
        background: white;
        border-radius: 24px;
        padding: 20px;
        border: 1px solid #e9edf2;
        /* position: sticky; */
        top: 20px;
    }

    .preprocessor-grid {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin: 16px 0;
    }

    .preprocessor-btn {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 60px;
        padding: 10px 16px;
        text-align: left;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.2s;
        cursor: pointer;
    }

    .preprocessor-btn.active {
        background: #ede9fe;
        border-color: #8b5cf6;
        color: #5b21b6;
        box-shadow: 0 2px 8px rgba(139,92,246,0.2);
    }

    .preprocessor-badge {
        font-size: 1.4rem;
    }

    .preprocessor-title {
        font-weight: 700;
    }

    .preprocessor-desc {
        font-size: 0.7rem;
        opacity: 0.7;
    }

    .image-upload-area {
        margin: 20px 0;
        border: 2px dashed #cbd5e1;
        border-radius: 20px;
        padding: 20px;
        text-align: center;
        background: #fefefe;
        transition: 0.2s;
    }

    .image-preview {
        margin-top: 12px;
        display: flex;
        justify-content: center;
    }

    .image-preview img {
        max-width: 100%;
        max-height: 180px;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border: 2px solid white;
    }

    .control-strength {
        margin: 16px 0;
    }

    #images {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 20px;
        margin-top: 32px;
    }

    .image-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 8px 18px rgba(0,0,0,0.05);
        transition: 0.2s;
    }

    .image-card img {
        width: 100%;
        height: auto;
        display: block;
    }

    .image-actions {
        padding: 12px;
        text-align: center;
        background: #ffffff;
    }

    .download-btn {
        background: #f1f5f9;
        color: #1e293b;
        padding: 6px 16px;
        border-radius: 40px;
        font-size: 0.8rem;
        box-shadow: none;
        display: inline-block;
        cursor: pointer;
    }

    hr {
        margin: 18px 0;
        border-color: #ecf3f9;
    }

    .model-badge {
        display: inline-block;
        background: #eef2ff;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.7rem;
        margin-left: 8px;
        color: #4f46e5;
    }

    .vram-warning {
        background: #fffbeb;
        border-left: 4px solid #f59e0b;
        padding: 10px 12px;
        margin-top: 12px;
        font-size: 0.75rem;
        border-radius: 12px;
        color: #b45309;
    }

    .preprocessor-settings {
        background: #f8fafc;
        padding: 16px;
        border-radius: 16px;
        margin-top: 16px;
    }

    @media (max-width: 880px) {
        .main-grid {
            grid-template-columns: 1fr;
        }
    }
</style>