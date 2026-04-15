<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Complete la siguiente informacion del producto')
                    ->schema([
                        TextInput::make('sku')
                            ->label('SKU')
                            ->required()
                            ->columnSpan(2),
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->label('Categoria')
                            ->required()
                            ->columnSpan(3),
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->columnSpan(4),
                        TextInput::make('slug')
                            ->required()
                            ->columnSpan(3),
                        Textarea::make('description')
                            ->label('Descripcion')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('price')
                            ->label('Precio')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->columnSpan(3),
                        TextInput::make('compare_price')
                            ->label('Precio anterior')
                            ->numeric()
                            ->prefix('$')
                            ->columnSpan(3),
                        TextInput::make('offer_price')
                            ->label('Precio de oferta')
                            ->numeric()
                            ->prefix('$')
                            ->columnSpan(3),
                        TextInput::make('quantity')
                            ->label('Cantidad')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->columnSpan(3),
                        Toggle::make('is_visible')
                            ->label('Visible')
                            ->required()
                            ->columnSpan(4),
                        Toggle::make('featured')
                            ->label('Destacado')
                            ->required()
                            ->columnSpan(4),
                        Toggle::make('on_sale')
                            ->label('En oferta')
                            ->required()
                            ->columnSpan(4),
                        FileUpload::make('image')
                            ->label('Imagen')
                            ->image()
                            ->directory('productos')
                            ->automaticallyResizeImagesMode('cover') // 'cover', 'contain' o 'force'
                            ->automaticallyCropImagesToAspectRatio('16:9') // opcional: fuerza una relación de aspecto
                            ->automaticallyResizeImagesToWidth(300)  // ancho deseado
                            ->automaticallyResizeImagesToHeight(200) // alto deseado
                            ->automaticallyUpscaleImagesWhenResizing(false) 
                            ->columnSpanFull(),
                    ])->columns(12)->columnSpanFull(),
            ]);
    }
}
