<?php

namespace App\Filament\Platform\Resources\Subscriptions;

use App\Filament\Platform\Resources\Subscriptions\Pages\CreateSubscription;
use App\Filament\Platform\Resources\Subscriptions\Pages\ListSubscriptions;
use App\Models\Admin;
use App\Models\SaasPlanPrice;
use App\Models\SaasSubscription;
use App\Models\Tenant;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
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
        return auth('platform')->user() instanceof Admin;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Activación manual')->description(
                'La activación manual no registra un pago. Selecciona una tarifa activa e indica el motivo administrativo.'
            )->schema([
                Select::make('tenant_id')
                    ->label('Tienda')
                    ->options(fn (): array => Tenant::query()->with('domains')->orderBy('id')->get()->mapWithKeys(
                        function (Tenant $tenant): array {
                            $id = (string) $tenant->getTenantKey();
                            $data = $tenant->getAttribute('data');
                            $name = data_get($data, 'name') ?: data_get($data, 'slug') ?: $id;
                            $domain = $tenant->domains->first()?->domain;

                            return [
                                $id => $name.' — '.$id.($domain ? ' ('.$domain.')' : ''),
                            ];
                        },
                    )->all())
                    ->searchable()
                    ->required(),
                Select::make('plan_price_id')
                    ->label('Plan y tarifa')
                    ->options(fn (): array => SaasPlanPrice::query()
                        ->with(['plan', 'currency'])
                        ->where('is_active', true)
                        ->whereHas('plan', fn ($query) => $query->where('is_active', true))
                        ->whereHas('currency', fn ($query) => $query->where('is_active', true))
                        ->orderBy('plan_id')
                        ->get()
                        ->mapWithKeys(fn (SaasPlanPrice $price): array => [
                            $price->getKey() => $price->plan->name.' — '.$price->currency->code.' '
                                .number_format((float) $price->amount, 2).' / '
                                .($price->billing_interval === 'yearly' ? 'anual' : 'mensual'),
                        ])->all())
                    ->searchable()
                    ->required(),
                TextInput::make('duration_days')
                    ->label('Duración personalizada (días)')
                    ->helperText('Opcional. Déjalo vacío para usar la periodicidad de la tarifa. Útil para períodos de prueba.')
                    ->numeric()
                    ->integer()
                    ->minValue(1)
                    ->maxValue(3660),
                Textarea::make('creation_reason')
                    ->label('Motivo de la activación')
                    ->placeholder('Ej.: período de prueba acordado con la tienda')
                    ->required()
                    ->minLength(5)
                    ->maxLength(2000)
                    ->columnSpanFull(),
            ])->columns(2)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->modifyQueryUsing(fn ($query) => $query->with([
            'plan', 'planPrice.currency', 'latestPayment', 'createdByAdmin',
        ]))->columns([
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
            TextColumn::make('latestPayment.id')->label('Pago asociado')->placeholder('Sin pago'),
            TextColumn::make('createdByAdmin.name')->label('Activada por')->placeholder('Pago aprobado'),
            TextColumn::make('creation_reason')->label('Motivo administrativo')->placeholder('—')->wrap()->toggleable(),
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
        return [
            'index' => ListSubscriptions::route('/'),
            'create' => CreateSubscription::route('/create'),
        ];
    }
}
