<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// Шинэ хувилбарт Action-ууд энд байрлах магадлалтай:
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationLabel = 'Зөвлөмж материал';

    public static function form(Schema $form): Schema
    {
        return $form
            ->components([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Нэр'),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('₮')
                    ->label('Үнэ'),
                Forms\Components\Textarea::make('description')
                    ->label('Тайлбар')
                    ->rows(3),
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->directory('products')
                    ->label('Зураг'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Идэвхтэй')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->circular()
                    ->label('Зураг'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Нэр'),
                Tables\Columns\TextColumn::make('price')
                    ->money('MNT')
                    ->label('Үнэ'),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->label('Тайлбар'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Идэвхтэй'),
            ])
            ->filters([])
            ->actions([
                // Хэрэв дээр use хийсэн бол шууд ингэж дуудна
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                // BulkActionGroup олдохгүй бол шууд жагсаалт хэлбэрээр бичиж үзээрэй
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}