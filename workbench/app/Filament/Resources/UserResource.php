<?php

namespace Workbench\App\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Workbench\App\Models\User;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \Workbench\App\Filament\Resources\UserResource\Pages\ListUsers::route('/'),
        ];
    }
}
