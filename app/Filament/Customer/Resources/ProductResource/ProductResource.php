<?php

namespace App\Filament\Customer\Resources;

use App\Filament\Customer\Resources\ProfileResource\Pages;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class ProfileResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationLabel = 'Миний профайл';

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('id', auth()->id());
    }

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label('Нэр'),
                TextInput::make('email')
                    ->required()
                    ->email()
                    ->label('Имэйл'),
                TextInput::make('password')
                    ->password()
                    ->label('Шинэ нууц үг')
                    ->placeholder('Хэрэв өөрчлөхгүй бол хоосон үлдээх')
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Нэр'),
                Tables\Columns\TextColumn::make('email')
                    ->label('Имэйл'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Бүртгүүлсэн'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Засах'),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProfiles::route('/'),
            'edit' => Pages\EditProfile::route('/{record}/edit'),
        ];
    }
    
    public static function canEdit($record): bool
    {
        return $record->id === auth()->id();
    }
    
    public static function canCreate(): bool
    {
        return false;
    }
    
    public static function canDelete($record): bool
    {
        return false;
    }
}