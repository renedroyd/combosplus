<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(100)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, ?string $state): void {
                        if (blank($state)) {
                            return;
                        }

                        $set('slug', Str::slug($state));
                    }),

                TextInput::make('slug')
                    ->label('URL amigable')
                    ->required()
                    ->maxLength(120)
                    ->prefix('/')
                    ->suffixAction(
                        Action::make('generateSlug')
                            ->label('Generar')
                            ->icon('heroicon-m-sparkles')
                            ->action(fn (Get $get, Set $set): mixed => $set(
                                'slug',
                                Str::slug((string) $get('name')),
                            )),
                    )
                    ->helperText('Se genera automáticamente a partir del nombre; puedes ajustarlo.'),

                Textarea::make('description')
                    ->label('Descripción')
                    ->rows(3)
                    ->maxLength(500)
                    ->columnSpanFull(),

                TextInput::make('parent_id')
                    ->label('Categoría padre')
                    ->numeric()
                    ->minValue(1)
                    ->helperText('Opcional. Puedes dejarlo vacío para una categoría principal.'),

                Toggle::make('is_visible')
                    ->label('Visible')
                    ->default(true)
                    ->helperText('Las categorías visibles se muestran en el catálogo.'),

                TextInput::make('sort_order')
                    ->label('Orden')
                    ->required()
                    ->numeric()
                    ->integer()
                    ->minValue(0)
                    ->default(0),

                FileUpload::make('image')
                    ->label('Imagen')
                    ->image()
                    ->directory('categorias')
                    ->imageEditor()
                    ->helperText('Imagen opcional para identificar la categoría.'),
            ]);
    }
}
