<?php

namespace App\Services;

use App\Models\SaasSubscription;
use App\Models\SaasSubscriptionPayment;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class SaasSubscriptionLifecycleService
{
    public function activateFromApprovedPayment(SaasSubscriptionPayment $payment): SaasSubscription
    {
        if ($payment->status !== SaasSubscriptionPayment::STATUS_APPROVED) {
            throw ValidationException::withMessages([
                'status' => 'Solo se puede activar una suscripción desde un pago aprobado.',
            ]);
        }

        $price = $payment->planPrice()->with('plan')->firstOrFail();

        if (! $price->plan) {
            throw ValidationException::withMessages([
                'plan_price_id' => 'La tarifa no tiene un plan válido asociado.',
            ]);
        }

        $now = now();
        $latest = SaasSubscription::query()
            ->where('tenant_id', $payment->tenant_id)
            ->where('status', SaasSubscription::STATUS_ACTIVE)
            ->where('ends_at', '>', $now)
            ->orderByDesc('ends_at')
            ->lockForUpdate()
            ->first();

        $startsAt = $latest && $latest->ends_at->gt($now)
            ? $latest->ends_at->copy()
            : $now->copy();

        $endsAt = match ($price->billing_interval) {
            'monthly' => $startsAt->copy()->addMonthNoOverflow(),
            'yearly' => $startsAt->copy()->addYearNoOverflow(),
            default => throw ValidationException::withMessages([
                'billing_interval' => 'La periodicidad de la tarifa no es válida.',
            ]),
        };

        return SaasSubscription::query()->create([
            'tenant_id' => $payment->tenant_id,
            'plan_id' => $price->plan_id,
            'plan_price_id' => $price->getKey(),
            'latest_payment_id' => $payment->getKey(),
            'status' => SaasSubscription::STATUS_ACTIVE,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'next_billing_at' => $endsAt,
        ]);
    }
}
