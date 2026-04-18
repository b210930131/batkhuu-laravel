<?php

namespace App\Filament\Customer\Resources;

use App\Models\Order;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationLabel = 'Миний захиалга';

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Захиалгын дугаар')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('MNT')
                    ->label('Нийт дүн'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Төлөв'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Огноо'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [];
    }
    
    public static function canCreate(): bool
    {
        return false;
    }
}
