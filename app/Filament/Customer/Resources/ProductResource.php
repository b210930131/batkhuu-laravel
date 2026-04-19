<?php

namespace App\Filament\Customer\Resources;

use App\Models\Product;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
// Хэрэв дээрх namespace-үүд алдаа заавал, доорхыг турш:
use Filament\Actions\ViewAction; 
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationLabel = 'Зөвлөмж материал';

    // Customer хуудас тул form-ийг зөвхөн харах горимд эсвэл хоосон үлдээж болно
    public static function form(Schema $form): Schema
    {
        return $form
        ->components([
            \Filament\Forms\Components\FileUpload::make('image')
                ->label('Зураг'),
            \Filament\Forms\Components\TextInput::make('name')
                ->label('Нэр'),
            \Filament\Forms\Components\TextInput::make('price')
                ->label('Үнэ'),
            \Filament\Forms\Components\Textarea::make('description')
                ->label('Тайлбар'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->circular()->label('Зураг'),
                Tables\Columns\TextColumn::make('name')->searchable()->label('Нэр'),
                Tables\Columns\TextColumn::make('price')->money('MNT')->label('Үнэ'),
            ])
            ->actions([
                // Энд ViewAction-ийг шууд бүтэн замаар нь дуудвал алдаа гарахгүй
                \Filament\Actions\ViewAction::make()->label('Харах'),
            ])
            ->bulkActions([]); // Customer устгах эрхгүй тул хоосон үлдээнэ
    }
    // Буруу: public static function infolist(Infolist $infolist): Infolist
// Зөв (v4):
    public static function infolist(Schema $infolist): Schema
    {
        return $infolist
            ->components([ // schema() биш components()
                ImageEntry::make('image')
                    ->label('Зураг')
                    ->width(500)  // Энд хэмжээг нь томруулж өгөөрэй
                    ->height(500)
                    ->extraImgAttributes([
                        'style' => 'max-width: 100%; height: auto; object-fit: contain;',
                    ]),
                TextEntry::make('name')
                    ->label('Нэр')
                    ->weight('bold')
                    ->size('lg'),
                TextEntry::make('price')
                    ->label('Үнэ')
                    ->money('MNT'),
                TextEntry::make('description')
                    ->label('Тайлбар'),
            ]);
    }
    // Эрхүүдийг хаах
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