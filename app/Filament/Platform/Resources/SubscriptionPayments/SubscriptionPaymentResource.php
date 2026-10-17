<?php

namespace App\Filament\Platform\Resources\SubscriptionPayments;

use App\Filament\Platform\Resources\SubscriptionPayments\Pages\CreateSubscriptionPayment;
use App\Filament\Platform\Resources\SubscriptionPayments\Pages\EditSubscriptionPayment;
use App\Filament\Platform\Resources\SubscriptionPayments\Pages\ListSubscriptionPayments;
use App\Models\SaasPlanPrice;
use App\Models\SaasSubscriptionPayment;
use App\Services\SaasSubscriptionPaymentReviewService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
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

class SubscriptionPaymentResource extends Resource
{
    protected static ?string $model = SaasSubscriptionPayment::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;
    protected static ?string $modelLabel = 'Pago de suscripción';
    protected static ?string $pluralLabel = 'Pagos de suscripciones';
    protected static string|UnitEnum|null $navigationGroup = 'Facturación SaaS';
    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Solicitud de pago')->schema([
                TextInput::make('tenant_id')->label('ID de la tienda (tenant)')->required()->maxLength(255),
                Select::make('plan_price_id')->label('Tarifa del plan')
                    ->options(fn () => SaasPlanPrice::query()->with(['plan', 'currency'])->where('is_active', true)->get()
                        ->mapWithKeys(fn (SaasPlanPrice $price) => [
                            $price->id => $price->plan->name.' — '.$price->currency->code.' '.number_format((float) $price->amount, 2).' / '.$price->billing_interval,
                        ]))
                    ->live()->required()->afterStateUpdated(function ($state, callable $set): void {
                        $price = $state ? SaasPlanPrice::query()->with(['plan', 'currency'])->find($state) : null;
                        if ($price) {
                            $set('plan_name', $price->plan->name);
                            $set('currency_code', $price->currency->code);
                            $set('billing_interval', $price->billing_interval);
                            $set('amount', $price->amount);
                        }
                    }),
                TextInput::make('plan_name')->label('Nombre del plan (instantánea)')->required()->maxLength(120),
                TextInput::make('currency_code')->label('Moneda')->required()->length(3),
                Select::make('billing_interval')->label('Periodicidad')->options(['monthly' => 'Mensual', 'yearly' => 'Anual'])->required(),
                TextInput::make('amount')->label('Importe recibido')->numeric()->required()->minValue(0)->step('0.01'),
                TextInput::make('payment_method')->label('Método de pago')->required()->maxLength(80),
                TextInput::make('payment_reference')->label('Referencia de pago')->maxLength(120),
                FileUpload::make('receipt_path')->label('Comprobante')->disk('local')->directory('saas-payment-receipts')->visibility('private')->maxSize(10240),
                Textarea::make('rejection_reason')->label('Motivo del rechazo')->rows(3)->columnSpanFull()->visible(fn (?Model $record): bool => $record?->status === SaasSubscriptionPayment::STATUS_REJECTED),
            ])->columns(2)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('tenant_id')->label('Tienda')->searchable()->sortable(),
            TextColumn::make('plan_name')->label('Plan')->searchable(),
            TextColumn::make('currency_code')->label('Moneda'),
            TextColumn::make('amount')->label('Importe')->numeric(decimalPlaces: 2)->sortable(),
            TextColumn::make('billing_interval')->label('Periodicidad')->formatStateUsing(fn (string $state): string => $state === 'yearly' ? 'Anual' : 'Mensual'),
            TextColumn::make('payment_method')->label('Método'),
            TextColumn::make('status')->label('Estado')->badge()->color(fn (string $state): string => match ($state) {
                'approved' => 'success',
                'rejected' => 'danger',
                default => 'warning',
            }),
            TextColumn::make('created_at')->label('Enviado')->dateTime()->sortable(),
        ])->filters([
            SelectFilter::make('status')->options(['pending' => 'Pendiente', 'approved' => 'Aprobado', 'rejected' => 'Rechazado']),
        ])->recordActions([
            Action::make('approve')
                ->label('Aprobar')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success')
                ->visible(fn (SaasSubscriptionPayment $record): bool => $record->isPending())
                ->requiresConfirmation()
                ->action(fn (SaasSubscriptionPayment $record) => app(SaasSubscriptionPaymentReviewService::class)->approve($record, auth('platform')->id())),
            Action::make('reject')
                ->label('Rechazar')
                ->icon(Heroicon::OutlinedXCircle)
                ->color('danger')
                ->visible(fn (SaasSubscriptionPayment $record): bool => $record->isPending())
                ->form([Textarea::make('reason')->label('Motivo del rechazo')->required()->minLength(3)->maxLength(2000)])
                ->action(fn (SaasSubscriptionPayment $record, array $data) => app(SaasSubscriptionPaymentReviewService::class)->reject($record, auth('platform')->id(), $data['reason'])),
            EditAction::make()->visible(fn (SaasSubscriptionPayment $record): bool => $record->isPending()),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubscriptionPayments::route('/'),
            'create' => CreateSubscriptionPayment::route('/create'),
            'edit' => EditSubscriptionPayment::route('/{record}/edit'),
        ];
    }
}
