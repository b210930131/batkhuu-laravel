<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
     protected static ?string $model = User::class;
    // protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Хэрэглэгчид';
    // protected static ?string $navigationGroup = 'Хэрэглэгч';  // <-- static
    protected static ?string $pluralLabel = 'Хэрэглэгчид';

    // form() методыг БҮРЭН ХАССАН

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Нэр'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->label('Имэйл'),
                Tables\Columns\TextColumn::make('role')
                    ->label('Эрх'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Бүртгүүлсэн'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'admin' => 'Админ',
                        'customer' => 'Хэрэглэгч',
                    ]),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->label('Устгах')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        // Хэрэглэгчийн зургуудыг устгах
                        if (method_exists($record, 'generatedImages')) {
                            $record->generatedImages()->delete();
                        }
                        $record->delete();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                if (method_exists($record, 'generatedImages')) {
                                    $record->generatedImages()->delete();
                                }
                                $record->delete();
                            }
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
        ];
    }
    
    // Create боломжгүй
    public static function canCreate(): bool
    {
        return false;
    }
    
    // Edit боломжгүй
    public static function canEdit($record): bool
    {
        return false;
    }
}