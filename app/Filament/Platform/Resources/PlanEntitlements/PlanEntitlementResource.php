<?php

namespace App\Filament\Platform\Resources\PlanEntitlements;

use App\Filament\Platform\Resources\PlanEntitlements\Pages\CreatePlanEntitlement;
use App\Filament\Platform\Resources\PlanEntitlements\Pages\EditPlanEntitlement;
use App\Filament\Platform\Resources\PlanEntitlements\Pages\ListPlanEntitlements;
use App\Models\SaasPlanEntitlement;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class PlanEntitlementResource extends Resource
{
    protected static ?string $model = SaasPlanEntitlement::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;
    protected static ?string $modelLabel = 'Límite o función';
    protected static ?string $pluralLabel = 'Límites y funciones';
    protected static string|UnitEnum|null $navigationGroup = 'Planes y precios';
    protected static ?int $navigationSort = 23;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Regla del plan')->schema([
                Select::make('plan_id')->label('Plan')->relationship('plan', 'name')->searchable()->preload()->required(),
                TextInput::make('key')->label('Clave')->required()->maxLength(100)->alphaDash(),
                TextInput::make('value')->label('Valor')->required()->maxLength(255)
                    ->helperText('Para límites numéricos usa un entero; también se admiten unlimited, true o false.'),
                Textarea::make('description')->label('Descripción')->rows(2)->columnSpanFull(),
            ])->columns(2)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('plan.name')->label('Plan')->searchable()->sortable(),
            TextColumn::make('key')->label('Clave')->searchable()->sortable(),
            TextColumn::make('value')->label('Valor')->badge(),
            TextColumn::make('description')->label('Descripción')->wrap()->toggleable(),
            TextColumn::make('updated_at')->label('Modificado')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
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
            'index' => ListPlanEntitlements::route('/'),
            'create' => CreatePlanEntitlement::route('/create'),
            'edit' => EditPlanEntitlement::route('/{record}/edit'),
        ];
    }
}
