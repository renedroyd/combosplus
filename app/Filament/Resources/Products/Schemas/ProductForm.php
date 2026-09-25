<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del producto')
                    ->description('Define los datos que verán tus clientes y controla su disponibilidad.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(150)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, ?string $state): void {
                                if (blank($state)) {
                                    return;
                                }

                                $set('slug', Str::slug($state));
                            })
                            ->columnSpan(7),

                        TextInput::make('sku')
                            ->label('SKU')
                            ->required()
                            ->maxLength(80)
                            ->helperText('Código interno único para identificar el producto.')
                            ->columnSpan(5),

                        TextInput::make('slug')
                            ->label('URL amigable')
                            ->required()
                            ->maxLength(180)
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
                            ->helperText('Se genera automáticamente a partir del nombre; puedes ajustarlo.')
                            ->columnSpan(7),

                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->label('Categoría')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpan(5),

                        Textarea::make('description')
                            ->label('Descripción')
                            ->required()
                            ->rows(4)
                            ->maxLength(2000)
                            ->columnSpanFull(),
                    ])
                    ->columns(12)
                    ->columnSpanFull(),

                Section::make('Precio e inventario')
                    ->schema([
                        TextInput::make('price')
                            ->label('Precio')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->prefix('$')
                            ->columnSpan(3),

                        TextInput::make('compare_price')
                            ->label('Precio anterior')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('$')
                            ->helperText('Opcional. Úsalo para mostrar el precio anterior.')
                            ->columnSpan(3),

                        TextInput::make('offer_price')
                            ->label('Precio de oferta')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('$')
                            ->helperText('Opcional. Activa "En oferta" para publicarlo como promoción.')
                            ->columnSpan(3),

                        TextInput::make('quantity')
                            ->label('Cantidad disponible')
                            ->required()
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->default(0)
                            ->columnSpan(3),
                    ])
                    ->columns(12)
                    ->columnSpanFull(),

                Section::make('Publicación')
                    ->description('Controla qué productos aparecen en el catálogo público.')
                    ->schema([
                        Toggle::make('is_visible')
                            ->label('Publicado')
                            ->helperText('Los productos publicados pueden aparecer en la tienda.')
                            ->default(true)
                            ->columnSpan(4),

                        Toggle::make('featured')
                            ->label('Destacado')
                            ->helperText('Permite promocionar el producto en espacios destacados.')
                            ->default(false)
                            ->columnSpan(4),

                        Toggle::make('on_sale')
                            ->label('En oferta')
                            ->helperText('Marca el producto como oferta cuando tenga un precio promocional.')
                            ->default(false)
                            ->columnSpan(4),
                    ])
                    ->columns(12)
                    ->columnSpanFull(),

                Section::make('Imagen')
                    ->schema([
                        FileUpload::make('image')
                            ->label('Imagen principal')
                            ->image()
                            ->directory('productos')
                            ->imageEditor()
                            ->automaticallyResizeImagesMode('cover')
                            ->automaticallyCropImagesToAspectRatio('16:9')
                            ->automaticallyResizeImagesToWidth(300)
                            ->automaticallyResizeImagesToHeight(200)
                            ->automaticallyUpscaleImagesWhenResizing(false)
                            ->helperText('Recomendado: formato horizontal 16:9. Máximo 300×200 px.')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
