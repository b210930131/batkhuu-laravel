<?php

namespace App\Filament\Customer\Resources\Products\Pages;

use App\Filament\Customer\Resources\Products\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;
}
