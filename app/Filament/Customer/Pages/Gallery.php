<?php

namespace App\Filament\Customer\Pages;

use Filament\Pages\Page;
use App\Models\GeneratedImage;

class Gallery extends Page
{
    // protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationLabel = 'Миний зургууд';
    protected static ?string $title = 'Миний зургууд';
    protected string $view = 'filament.customer.pages.gallery';  // <-- static
    
    public $images;
    
    public function mount()
    {
        $this->images = GeneratedImage::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
    }
}