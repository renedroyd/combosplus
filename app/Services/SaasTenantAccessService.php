<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Product;
use App\Models\SaasSubscription;
use App\Models\Tenant;
use App\Models\TenantMembership;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaasTenantAccessService
{
    public const STATE_ACTIVE = 'active';
    public const STATE_GRACE = 'grace';
    public const STATE_RESTRICTED = 'restricted';

    public function __construct(
        private readonly SaasPlanEntitlementService $entitlements,
    ) {
    }

    public function state(string $tenantId): string
    {
        if ($this->entitlements->currentSubscription($tenantId)) {
            return self::STATE_ACTIVE;
        }

        $graceDays = max(0, (int) config('saas.grace_days', 7));

        if ($graceDays > 0) {
            $inGrace = SaasSubscription::query()
                ->where('tenant_id', $tenantId)
                ->where('status', SaasSubscription::STATUS_ACTIVE)
                ->where('starts_at', '<=', now())
                ->where('ends_at', '<=', now())
                ->where('ends_at', '>', now()->subDays($graceDays))
                ->exists();

            if ($inGrace) {
                return self::STATE_GRACE;
            }
        }

        return self::STATE_RESTRICTED;
    }

    public function canUseCommercialFeatures(string $tenantId): bool
    {
        return in_array($this->state($tenantId), [
            self::STATE_ACTIVE,
            self::STATE_GRACE,
        ], true);
    }

    public function assertCanUseCommercialFeatures(string $tenantId): void
    {
        if ($this->canUseCommercialFeatures($tenantId)) {
            return;
        }

        abort(402, 'La suscripción de esta tienda ha vencido. Puedes consultar el historial y renovar el plan para recuperar las operaciones comerciales.');
    }

    public function canCreateProduct(string $tenantId): bool
    {
        if (! $this->canUseCommercialFeatures($tenantId)) {
            return false;
        }

        $limit = $this->entitlements->limit($tenantId, 'max_products', 0);

        return $limit === null || Product::query()->count() < $limit;
    }

    public function assertCanCreateProduct(string $tenantId): void
    {
        $this->assertCanUseCommercialFeatures($tenantId);
        $limit = $this->entitlements->limit($tenantId, 'max_products', 0);

        if ($limit !== null && Product::query()->count() >= $limit) {
            throw ValidationException::withMessages([
                'name' => "Tu plan permite un máximo de {$limit} productos. Actualiza el plan o elimina productos para continuar.",
            ]);
        }
    }

    public function canCreateBranch(string $tenantId): bool
    {
        if (! $this->canUseCommercialFeatures($tenantId)) {
            return false;
        }

        $limit = $this->entitlements->limit($tenantId, 'max_branches', 0);

        return $limit === null || Branch::query()->where('is_active', true)->count() < $limit;
    }

    public function assertCanCreateBranch(string $tenantId): void
    {
        $this->assertCanUseCommercialFeatures($tenantId);
        $limit = $this->entitlements->limit($tenantId, 'max_branches', 0);

        if ($limit !== null && Branch::query()->where('is_active', true)->count() >= $limit) {
            throw ValidationException::withMessages([
                'name' => "Tu plan permite un máximo de {$limit} sucursales activas. Actualiza el plan o desactiva una sucursal para continuar.",
            ]);
        }
    }

    public function assertCanAddTeamMember(string $tenantId): void
    {
        $this->assertCanUseCommercialFeatures($tenantId);
        $limit = $this->entitlements->limit($tenantId, 'max_users', 0);

        $count = TenantMembership::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->where(function ($query): void {
                $query->where('is_owner', true)->orWhere('role', '!=', 'customer');
            })
            ->count();

        if ($limit !== null && $count >= $limit) {
            throw ValidationException::withMessages([
                'user_id' => "Tu plan permite un máximo de {$limit} usuarios, incluido el propietario.",
            ]);
        }
    }

    /**
     * Serialize quota checks for a tenant. The central tenant row is the shared
     * lock target while the new resource is written to that tenant's database.
     */
    public function withTenantLock(string $tenantId, callable $callback): mixed
    {
        $connection = (string) config('tenancy.database.central_connection');

        return DB::connection($connection)->transaction(function () use ($tenantId, $callback): mixed {
            Tenant::query()->whereKey($tenantId)->lockForUpdate()->firstOrFail();

            return $callback();
        });
    }
}
