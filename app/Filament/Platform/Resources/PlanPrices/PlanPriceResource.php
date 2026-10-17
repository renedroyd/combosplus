<?php

namespace App\Filament\Platform\Resources\PlanPrices;

use App\Filament\Platform\Resources\PlanPrices\Pages\CreatePlanPrice;
use App\Filament\Platform\Resources\PlanPrices\Pages\EditPlanPrice;
use App\Filament\Platform\Resources\PlanPrices\Pages\ListPlanPrices;
use App\Models\SaasPlanPrice;
use BackedEnum;
use Filament\Forms\Components\Select;
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

class PlanPriceResource extends Resource
{
    protected static ?string $model = SaasPlanPrice::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;
    protected static ?string $modelLabel = 'Tarifa';
    protected static ?string $pluralLabel = 'Tarifas de planes';
    protected static string|UnitEnum|null $navigationGroup = 'Planes y precios';
    protected static ?int $navigationSort = 22;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Información de la tarifa')->schema([
                Select::make('plan_id')->label('Plan')->relationship('plan', 'name')->searchable()->preload()->required(),
                Select::make('currency_id')->label('Moneda')->relationship('currency', 'name')->searchable()->preload()->required(),
                Select::make('billing_interval')->label('Periodicidad')->options(['monthly' => 'Mensual', 'yearly' => 'Anual'])->required(),
                TextInput::make('amount')->label('Precio')->numeric()->required()->minValue(0)->step('0.01'),
                Toggle::make('is_active')->label('Activa')->default(true),
            ])->columns(2)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('plan.name')->label('Plan')->searchable()->sortable(),
            TextColumn::make('currency.code')->label('Moneda')->sortable(),
            TextColumn::make('billing_interval')->label('Periodicidad')->formatStateUsing(fn (string $state): string => $state === 'yearly' ? 'Anual' : 'Mensual')->sortable(),
            TextColumn::make('amount')->label('Importe')->numeric(decimalPlaces: 2)->sortable(),
            IconColumn::make('is_active')->label('Activa')->boolean(),
            TextColumn::make('updated_at')->label('Modificada')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->recordActions([
            \Filament\Actions\EditAction::make(),
        ])->toolbarActions([
            \Filament\Actions\BulkActionGroup::make([
                \Filament\Actions\DeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlanPrices::route('/'),
            'create' => CreatePlanPrice::route('/create'),
            'edit' => EditPlanPrice::route('/{record}/edit'),
        ];
    }
}
