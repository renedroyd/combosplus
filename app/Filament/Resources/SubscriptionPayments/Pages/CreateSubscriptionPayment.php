<?php

namespace App\\Filament\\Resources\\SubscriptionPayments\\Pages;

use App\\Filament\\Resources\\SubscriptionPayments\\SubscriptionPaymentResource;
use App\\Models\\SaasPlanPrice;
use App\\Models\\SaasSubscriptionPayment;
use Filament\\Resources\\Pages\\CreateRecord;
use Illuminate\\Validation\\ValidationException;

class CreateSubscriptionPayment extends CreateRecord
{
    protected static string $resource = SubscriptionPaymentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $tenantId = tenant()?->getTenantKey();
        $user = auth('admin')->user();

        if (! is_string($tenantId) || ! $user instanceof \\App\\Models\\PlatformUser) {
            throw ValidationException::withMessages(['tenant_id' => 'No se pudo verificar la tienda activa.']);
        }

        $membership = $user->activeMembershipForTenant($tenantId);

        if (! $membership?->canManageTeam()) {
            throw ValidationException::withMessages(['tenant_id' => 'No tienes permisos para gestionar los pagos de esta tienda.']);
        }

        $price = SaasPlanPrice::query()->with(['plan', 'currency'])
            ->where('is_active', true)
            ->whereHas('plan', fn ($query) => $query->where('is_active', true))
            ->whereHas('currency', fn ($query) => $query->where('is_active', true))
            ->findOrFail($data['plan_price_id']);

        return [
            'tenant_id' => $tenantId,
            'submitted_by_user_id' => $user->getKey(),
            'plan_price_id' => $price->getKey(),
            'plan_name' => $price->plan->name,
            'currency_code' => $price->currency->code,
            'billing_interval' => $price->billing_interval,
            'amount' => $price->amount,
            'payment_method' => $data['payment_method'],
            'payment_reference' => $data['payment_reference'] ?? null,
            'receipt_path' => $data['receipt_path'],
            'status' => SaasSubscriptionPayment::STATUS_PENDING,
        ];
    }
}
