<?php

namespace App\Filament\Customer\Resources; // Namespace-ийг Customer болгох

use App\Models\Product;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationLabel = 'Зөвлөмж материал';
    protected static ?string $pluralLabel = 'Зөвлөмж материалууд';

    // Customer-т зориулж Form-ийг хоосон эсвэл disabled үлдээж болно
    public static function form(Schema $form): Schema
    {
        return $form->components([]); 
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->circular()->label('Зураг'),
                Tables\Columns\TextColumn::make('name')->searchable()->label('Нэр'),
                Tables\Columns\TextColumn::make('price')->money('MNT')->label('Үнэ'),
                Tables\Columns\TextColumn::make('description')->label('Тайлбар'),
            ])
            ->actions([
                // Зөвхөн харах товч үлдээнэ
                Tables\Actions\ViewAction::make()->label('Үзэх'),
            ]);
    }

    // Нэмэх, Устгах, Засах эрхийг Customer-т хаах
    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Customer\Resources\ProductResource\Pages\ListProducts::route('/'),
        ];
    }
}