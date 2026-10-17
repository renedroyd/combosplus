<?php

namespace App\Services;

use App\Models\SaasPlanEntitlement;
use App\Models\SaasSubscription;

class SaasPlanEntitlementService
{
    public function currentSubscription(string $tenantId): ?SaasSubscription
    {
        return SaasSubscription::query()
            ->with(['plan.entitlements', 'planPrice.currency'])
            ->where('tenant_id', $tenantId)
            ->current()
            ->orderByDesc('starts_at')
            ->first();
    }

    public function value(string $tenantId, string $key, mixed $default = null): mixed
    {
        $entitlement = $this->currentSubscription($tenantId)?->plan?->entitlements
            ->firstWhere('key', $key);

        return $entitlement?->value ?? $default;
    }

    public function limit(string $tenantId, string $key, ?int $default = null): ?int
    {
        $entitlement = $this->currentSubscription($tenantId)?->plan?->entitlements
            ->firstWhere('key', $key);

        if (! $entitlement instanceof SaasPlanEntitlement) {
            return $default;
        }

        return $entitlement->numericLimit();
    }

    public function allows(string $tenantId, string $key, bool $default = false): bool
    {
        $entitlement = $this->currentSubscription($tenantId)?->plan?->entitlements
            ->firstWhere('key', $key);

        if (! $entitlement instanceof SaasPlanEntitlement) {
            return $default;
        }

        return $entitlement->booleanValue() ?? $default;
    }
}
