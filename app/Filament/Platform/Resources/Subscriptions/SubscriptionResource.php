<?php

namespace App\Filament\Platform\Resources\Subscriptions;

use App\Filament\Platform\Resources\Subscriptions\Pages\ListSubscriptions;
use App\Models\SaasSubscription;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class SubscriptionResource extends Resource
{
    protected static ?string $model = SaasSubscription::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;
    protected static ?string $modelLabel = 'Suscripción';
    protected static ?string $pluralLabel = 'Suscripciones';
    protected static string|UnitEnum|null $navigationGroup = 'Facturación SaaS';
    protected static ?int $navigationSort = 31;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->modifyQueryUsing(fn ($query) => $query->with(['plan', 'planPrice.currency', 'latestPayment']))
            ->columns([
                TextColumn::make('tenant_id')->label('Tienda')->searchable()->sortable(),
                TextColumn::make('plan.name')->label('Plan')->searchable(),
                TextColumn::make('planPrice.currency.code')->label('Moneda'),
                TextColumn::make('planPrice.amount')->label('Precio')->numeric(decimalPlaces: 2),
                TextColumn::make('status')->label('Estado')->badge()->color(fn (string $state): string => match ($state) {
                    'active' => 'success',
                    'past_due' => 'danger',
                    'canceled', 'expired' => 'gray',
                    default => 'warning',
                }),
                TextColumn::make('starts_at')->label('Inicio')->dateTime()->sortable(),
                TextColumn::make('ends_at')->label('Vence')->dateTime()->sortable(),
                TextColumn::make('next_billing_at')->label('Próximo cobro')->dateTime()->placeholder('—'),
                TextColumn::make('latestPayment.id')->label('Último pago')->placeholder('—'),
            ])->filters([
                SelectFilter::make('status')->options([
                    'active' => 'Activa',
                    'past_due' => 'Vencida por pago',
                    'canceled' => 'Cancelada',
                    'expired' => 'Expirada',
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListSubscriptions::route('/')];
    }
}
