<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;

class CustomPage extends Page
{
    // Navigation (static)
    protected static ?string $navigationLabel = 'Ai зураг үүсгэх';
    
    // Page properties (static)
    protected static ?string $title = 'Миний хуудас';

    // Non-static
    protected string $view = 'filament.admin.pages.custom-page';
    protected ?string $heading = 'Миний хуудас';

    
}