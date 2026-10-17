<?php

namespace App\Filament\Platform\Resources\Currencies;

use App\Models\SaasCurrency;
use App\Filament\Platform\Resources\Currencies\Pages\CreateCurrency;
use App\Filament\Platform\Resources\Currencies\Pages\EditCurrency;
use App\Filament\Platform\Resources\Currencies\Pages\ListCurrencies;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class CurrencyResource extends Resource
{
    protected static ?string $model = SaasCurrency::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static ?string $modelLabel = 'Moneda';

    protected static ?string $pluralLabel = 'Monedas';

    protected static string|UnitEnum|null $navigationGroup = 'Planes y precios';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Información de la moneda')
                ->schema([
                    TextInput::make('code')->label('Código ISO')->required()->length(3)->alpha()->unique(ignoreRecord: true)->dehydrateStateUsing(fn ($state) => strtoupper($state)),
                    TextInput::make('name')->label('Nombre')->required()->maxLength(120),
                    TextInput::make('symbol')->label('Símbolo')->required()->maxLength(8),
                    TextInput::make('decimal_places')->label('Decimales')->numeric()->required()->minValue(0)->maxValue(4)->default(2),
                    Toggle::make('is_active')->label('Activa')->default(true),
                ])->columns(2)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('Código')->searchable()->sortable(),
                TextColumn::make('name')->label('Nombre')->searchable()->sortable(),
                TextColumn::make('symbol')->label('Símbolo'),
                TextColumn::make('decimal_places')->label('Decimales')->sortable(),
                IconColumn::make('is_active')->label('Activa')->boolean(),
                TextColumn::make('updated_at')->label('Modificada')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([\Filament\Actions\EditAction::make()])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCurrencies::route('/'),
            'create' => CreateCurrency::route('/create'),
            'edit' => EditCurrency::route('/{record}/edit'),
        ];
    }
}
