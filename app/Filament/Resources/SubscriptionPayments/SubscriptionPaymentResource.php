<?php

namespace App\\Filament\\Resources\\SubscriptionPayments;

use App\\Filament\\Resources\\SubscriptionPayments\\Pages\\CreateSubscriptionPayment;
use App\\Filament\\Resources\\SubscriptionPayments\\Pages\\ListSubscriptionPayments;
use App\\Models\\PlatformUser;
use App\\Models\\SaasPlanPrice;
use App\\Models\\SaasSubscriptionPayment;
use BackedEnum;
use Filament\\Forms\\Components\\FileUpload;
use Filament\\Forms\\Components\\Select;
use Filament\\Forms\\Components\\TextInput;
use Filament\\Resources\\Resource;
use Filament\\Schemas\\Components\\Section;
use Filament\\Schemas\\Schema;
use Filament\\Support\\Icons\\Heroicon;
use Filament\\Tables\\Columns\\TextColumn;
use Filament\\Tables\\Filters\\SelectFilter;
use Filament\\Tables\\Table;
use Illuminate\\Database\\Eloquent\\Builder;
use UnitEnum;

class SubscriptionPaymentResource extends Resource
{
    protected static ?string $model = SaasSubscriptionPayment::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;
    protected static ?string $modelLabel = 'Pago de suscripción';
    protected static ?string $pluralLabel = 'Mis pagos de suscripción';
    protected static string|UnitEnum|null $navigationGroup = 'Mi tienda';
    protected static ?int $navigationSort = 80;

    public static function canViewAny(): bool
    {
        return static::canManageSubscriptionPayments();
    }

    public static function canCreate(): bool
    {
        return static::canManageSubscriptionPayments();
    }

    private static function canManageSubscriptionPayments(): bool
    {
        $user = auth('admin')->user();
        $tenantId = tenant()?->getTenantKey();

        if (! $user instanceof PlatformUser || ! is_string($tenantId)) {
            return false;
        }

        return $user->activeMembershipForTenant($tenantId)?->canManageTeam() ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('tenant_id', tenant()->getTenantKey());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Solicitar o registrar pago')->schema([
                Select::make('plan_price_id')->label('Plan y tarifa')
                    ->options(fn () => SaasPlanPrice::query()->with(['plan', 'currency'])
                        ->where('is_active', true)->whereHas('plan', fn (Builder $query) => $query->where('is_active', true))
                        ->whereHas('currency', fn (Builder $query) => $query->where('is_active', true))
                        ->get()->mapWithKeys(fn (SaasPlanPrice $price) => [
                            $price->id => $price->plan->name.' — '.$price->currency->code.' '.number_format((float) $price->amount, 2).' / '.($price->billing_interval === 'yearly' ? 'anual' : 'mensual'),
                        ]))
                    ->required(),
                TextInput::make('payment_method')->label('Método de pago')->required()->maxLength(80),
                TextInput::make('payment_reference')->label('Referencia de pago')->maxLength(120),
                FileUpload::make('receipt_path')->label('Comprobante de pago')
                    ->disk('local')->directory('saas-payment-receipts')->visibility('private')
                    ->required()->maxSize(10240)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf']),
            ])->columns(2)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('plan_name')->label('Plan')->searchable(),
            TextColumn::make('currency_code')->label('Moneda'),
            TextColumn::make('amount')->label('Importe')->numeric(decimalPlaces: 2)->sortable(),
            TextColumn::make('billing_interval')->label('Periodicidad')->formatStateUsing(fn (string $state): string => $state === 'yearly' ? 'Anual' : 'Mensual'),
            TextColumn::make('payment_method')->label('Método'),
            TextColumn::make('payment_reference')->label('Referencia')->placeholder('—'),
            TextColumn::make('status')->label('Estado')->badge()->color(fn (string $state): string => match ($state) {
                'approved' => 'success',
                'rejected' => 'danger',
                default => 'warning',
            }),
            TextColumn::make('rejection_reason')->label('Motivo del rechazo')->wrap()->toggleable(),
            TextColumn::make('created_at')->label('Fecha')->dateTime()->sortable(),
        ])->filters([
            SelectFilter::make('status')->options(['pending' => 'Pendiente', 'approved' => 'Aprobado', 'rejected' => 'Rechazado']),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubscriptionPayments::route('/'),
            'create' => CreateSubscriptionPayment::route('/create'),
        ];
    }
}
