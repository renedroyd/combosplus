<?php

namespace App\Filament\Resources\Admins\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AdminForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make("Informacion del usuario")
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombres y Apellidos')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('email')
                            ->label('Correo')
                            ->email()
                            ->required()
                            ->columnSpan(6),
                        DateTimePicker::make('email_verified_at')
                            ->label('Correo verificado')
                            ->columnSpan(6),
                        TextInput::make('password')
                            ->label('Contrasena')
                            ->password()
                            ->required()
                            ->columnSpan(6),
                        TextInput::make('verify_password')
                            ->label('Verificar contrasena')
                            ->same('password')
                            ->dehydrated(false)
                            ->columnSpan(6),
                    ])
                        ->columns(12)
                        ->columnSpanFull()
            ]);
    }
}
