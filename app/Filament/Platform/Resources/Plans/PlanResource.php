<?php

namespace App\Filament\Platform\Resources\Plans;

use App\Filament\Platform\Resources\Plans\Pages\CreatePlan;
use App\Filament\Platform\Resources\Plans\Pages\EditPlan;
use App\Filament\Platform\Resources\Plans\Pages\ListPlans;
use App\Models\SaasPlan;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class PlanResource extends Resource
{
    protected static ?string $model = SaasPlan::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $modelLabel = 'Plan';
    protected static ?string $pluralLabel = 'Planes';
    protected static string|UnitEnum|null $navigationGroup = 'Planes y precios';
    protected static ?int $navigationSort = 21;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Información del plan')->schema([
                TextInput::make('name')->label('Nombre')->required()->maxLength(120),
                TextInput::make('slug')->label('Identificador')->required()->alphaDash()->unique(ignoreRecord: true)->maxLength(120),
                Textarea::make('description')->label('Descripción')->rows(3)->columnSpanFull(),
                TextInput::make('sort_order')->label('Orden')->numeric()->required()->minValue(0)->default(0),
                Toggle::make('is_active')->label('Activo')->default(true),
            ])->columns(2)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Nombre')->searchable()->sortable(),
            TextColumn::make('slug')->label('Identificador')->searchable(),
            TextColumn::make('prices_count')->counts('prices')->label('Tarifas'),
            TextColumn::make('sort_order')->label('Orden')->sortable(),
            IconColumn::make('is_active')->label('Activo')->boolean(),
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
            'index' => ListPlans::route('/'),
            'create' => CreatePlan::route('/create'),
            'edit' => EditPlan::route('/{record}/edit'),
        ];
    }
}
