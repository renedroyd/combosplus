<?php

namespace App\Filament\Resources\Branches\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BranchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Información de la sucursal')->schema([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(150)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug((string) $state))),
                TextInput::make('slug')
                    ->label('Identificador URL')
                    ->required()
                    ->maxLength(180)
                    ->unique(ignoreRecord: true),
                Textarea::make('address')
                    ->label('Dirección')
                    ->maxLength(1000)
                    ->columnSpanFull(),
                TextInput::make('phone')
                    ->label('Teléfono')
                    ->tel()
                    ->maxLength(40),
                Toggle::make('is_active')
                    ->label('Sucursal activa')
                    ->default(true),
            ])->columns(2)->columnSpanFull(),
        ]);
    }
}
