<?php

namespace App\Filament\Platform\Resources\Admins\Schemas;

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
                Section::make('Información del administrador')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre completo')
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
                            ->label('Contraseña')
                            ->password()
                            ->required()
                            ->columnSpan(6),
                        TextInput::make('verify_password')
                            ->label('Confirmar contraseña')
                            ->same('password')
                            ->dehydrated(false)
                            ->columnSpan(6),
                    ])
                    ->columns(12)
                    ->columnSpanFull(),
            ]);
    }
}
