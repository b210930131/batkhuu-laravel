<?php

namespace App\Filament\Customer\Pages;

use Filament\Pages\Page;

class ComfyUI extends Page
{
    // protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationLabel = 'AI Зураг үүсгэх';
    protected static ?string $title = 'ComfyUI Studio';
    protected string $view = 'filament.customer.pages.comfy-ui';
    
}

// <?php

// namespace App\Filament\Admin\Pages;

// use Filament\Pages\Page;

// class CustomPage extends Page
// {
//     // Navigation (static)
//     protected static ?string $navigationLabel = 'Хуудасны нэр';
    
//     // Page properties (static)
//     protected static ?string $title = 'Миний хуудас';

//     // Non-static
//     protected string $view = 'filament.admin.pages.custom-page';
//     protected ?string $heading = 'Миний хуудас';
// }