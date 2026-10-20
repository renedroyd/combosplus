<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\SaasPlanPrice;
use App\Models\SaasSubscription;
use App\Models\SaasSubscriptionPayment;
use App\Models\Tenant;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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

        $endsAt = $this->endDateFor($price->billing_interval, $startsAt);

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

    /**
     * Provision a subscription from the platform panel without fabricating a payment.
     * The reason and platform administrator are stored for auditability.
     */
    public function createManually(
        string $tenantId,
        int $planPriceId,
        int|Admin $administrator,
        string $reason,
        ?int $durationDays = null,
    ): SaasSubscription {
        $reason = trim($reason);

        if ($reason === '') {
            throw ValidationException::withMessages([
                'creation_reason' => 'Indica el motivo administrativo de la activación.',
            ]);
        }

        if ($durationDays !== null && ($durationDays < 1 || $durationDays > 3660)) {
            throw ValidationException::withMessages([
                'duration_days' => 'La duración personalizada debe estar entre 1 y 3660 días.',
            ]);
        }

        $administratorId = $administrator instanceof Admin
            ? $administrator->getKey()
            : $administrator;

        return DB::connection((new SaasSubscription())->getConnectionName())
            ->transaction(function () use ($tenantId, $planPriceId, $administratorId, $reason): SaasSubscription {
                Tenant::query()->whereKey($tenantId)->lockForUpdate()->firstOrFail();

                $price = SaasPlanPrice::query()
                    ->with(['plan', 'currency'])
                    ->whereKey($planPriceId)
                    ->where('is_active', true)
                    ->lockForUpdate()
                    ->first();

                if (! $price || ! $price->plan?->is_active || ! $price->currency?->is_active) {
                    throw ValidationException::withMessages([
                        'plan_price_id' => 'Selecciona una tarifa activa de un plan y una moneda activos.',
                    ]);
                }

                $now = now();
                $latest = SaasSubscription::query()
                    ->where('tenant_id', $tenantId)
                    ->where('status', SaasSubscription::STATUS_ACTIVE)
                    ->where('ends_at', '>', $now)
                    ->orderByDesc('ends_at')
                    ->lockForUpdate()
                    ->first();

                $startsAt = $latest && $latest->ends_at->gt($now)
                    ? $latest->ends_at->copy()
                    : $now->copy();

                $endsAt = $durationDays === null
                    ? $this->endDateFor($price->billing_interval, $startsAt)
                    : $startsAt->copy()->addDays($durationDays);

                return SaasSubscription::query()->create([
                    'tenant_id' => $tenantId,
                    'plan_id' => $price->plan_id,
                    'plan_price_id' => $price->getKey(),
                    'latest_payment_id' => null,
                    'created_by_admin_id' => $administratorId,
                    'creation_reason' => $reason,
                    'status' => SaasSubscription::STATUS_ACTIVE,
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                    'next_billing_at' => $endsAt,
                ]);
            });
    }

    private function endDateFor(string $billingInterval, Carbon $startsAt): Carbon
    {
        return match ($billingInterval) {
            'monthly' => $startsAt->copy()->addMonthNoOverflow(),
            'yearly' => $startsAt->copy()->addYearNoOverflow(),
            default => throw ValidationException::withMessages([
                'billing_interval' => 'La periodicidad de la tarifa no es válida.',
            ]),
        };
    }
}
