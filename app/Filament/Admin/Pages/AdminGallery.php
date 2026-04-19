<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use App\Models\GeneratedImage;

class AdminGallery extends Page
{
    // protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationLabel = 'Бүх зургууд';
    // protected static ?string $navigationGroup = 'Зураг';  // <-- static
    protected static ?string $title = 'Бүх хэрэглэгчдийн зургууд';
    protected string $view = 'filament.admin.pages.admin-gallery';
    protected static ?string $slug = 'ai-studio';
    public $images;
    
    public function mount()
    {
        $this->images = GeneratedImage::with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}