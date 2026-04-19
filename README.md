# ComfyUI Laravel Studio

A Laravel-based web interface for ComfyUI with ControlNet support, SDXL integration, and real-time image generation.

## Features

- 🎨 **Image Generation** with SD1.5 and SDXL models
- 🎮 **ControlNet Support** with 8 different preprocessors:
  - Canny Edge Detection
  - Depth Map
  - OpenPose
  - MLSD Line Detection
  - Scribble
  - HED Edge Detection
  - Segmentation
  - Normal Map
- 🔄 **Real-time WebSocket** updates
- 🖼️ **Gallery** with generated images
- ⚡ **SDXL Base + Refiner** workflow
- 📁 **Image Download** functionality
- 🚀 **Easy Setup** with Composer

## Requirements

- PHP 8.3+ fpm
- Composer
- MySQL or SQLite
- ComfyUI with ControlNet models installed
- Node.js & NPM (for frontend assets)

## Installation

### 1. Clone the repository
```bash
https://github.com/b210930131/batkhuu-laravel.git
cd comfyui-laravel-studio