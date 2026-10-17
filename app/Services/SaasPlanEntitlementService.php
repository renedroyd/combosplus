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

    /**
     * During the grace period, keep using the plan that just expired.
     */
    public function subscriptionForEntitlements(string $tenantId): ?SaasSubscription
    {
        $current = $this->currentSubscription($tenantId);

        if ($current) {
            return $current;
        }

        $graceDays = max(0, (int) config('saas.grace_days', 7));
        if ($graceDays === 0) {
            return null;
        }

        return SaasSubscription::query()
            ->with(['plan.entitlements', 'planPrice.currency'])
            ->where('tenant_id', $tenantId)
            ->where('status', SaasSubscription::STATUS_ACTIVE)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '<=', now())
            ->where('ends_at', '>', now()->subDays($graceDays))
            ->orderByDesc('ends_at')
            ->first();
    }

    public function value(string $tenantId, string $key, mixed $default = null): mixed
    {
        $subscription = $this->subscriptionForEntitlements($tenantId);
        $entitlement = $subscription?->plan?->entitlements?->firstWhere('key', $key);

        if ($entitlement instanceof SaasPlanEntitlement) {
            return $entitlement->value;
        }

        $fallback = self::defaultLimitForPlanSlug(
            (string) ($subscription?->plan?->slug ?? ''),
            $key,
        );

        return $fallback === null ? $default : (string) $fallback;
    }

    public function limit(string $tenantId, string $key, ?int $default = null): ?int
    {
        $subscription = $this->subscriptionForEntitlements($tenantId);
        $entitlement = $subscription?->plan?->entitlements?->firstWhere('key', $key);

        if ($entitlement instanceof SaasPlanEntitlement) {
            $value = strtolower(trim((string) $entitlement->value));

            if ($value === 'unlimited') {
                return null;
            }

            return is_numeric($value) ? max(0, (int) $value) : $default;
        }

        $fallback = self::defaultLimitForPlanSlug(
            (string) ($subscription?->plan?->slug ?? ''),
            $key,
        );

        if ($fallback === 'unlimited') {
            return null;
        }

        return is_numeric($fallback) ? (int) $fallback : $default;
    }

    public function allows(string $tenantId, string $key, bool $default = false): bool
    {
        $entitlement = $this->subscriptionForEntitlements($tenantId)?->plan?->entitlements
            ->firstWhere('key', $key);

        if (! $entitlement instanceof SaasPlanEntitlement) {
            return $default;
        }

        return $entitlement->booleanValue() ?? $default;
    }

    /**
     * Safe defaults keep the agreed caps active for existing plans even when
     * their entitlement rows have not yet been created in the platform panel.
     *
     * The string "unlimited" is distinct from null, which means no default was
     * found and the caller must apply its fail-closed default.
     */
    public static function defaultLimitForPlanSlug(string $slug, string $key): int|string|null
    {
        $slug = strtolower(trim($slug));

        $plan = match ($slug) {
            'inicial', 'initial', 'starter', 'basic', 'basico' => 'initial',
            'profesional', 'professional', 'pro' => 'professional',
            'empresarial', 'enterprise', 'business', 'empresa' => 'enterprise',
            default => null,
        };

        $limits = [
            'initial' => ['max_products' => 100, 'max_users' => 1, 'max_branches' => 1],
            'professional' => ['max_products' => 1000, 'max_users' => 5, 'max_branches' => 2],
            'enterprise' => ['max_products' => 'unlimited', 'max_users' => 15, 'max_branches' => 5],
        ];

        return $plan === null ? null : ($limits[$plan][$key] ?? null);
    }
}
