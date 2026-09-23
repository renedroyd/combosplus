<?php

namespace App\Services\Tenancy;

use App\Models\PlatformUser;
use App\Models\Tenant;
use App\Models\TenantMembership;
use Illuminate\Support\Collection;

class TenantAccessService
{
    public function membershipsForUser(?PlatformUser $user): Collection
    {
        if (! $user) {
            return collect();
        }

        return $user->memberships()
            ->where('status', 'active')
            ->with('tenant')
            ->get();
    }

    public function membershipForCurrentTenant(?PlatformUser $user): ?TenantMembership
    {
        $tenant = tenant();

        if (! $user || ! $tenant) {
            return null;
        }

        return $user->activeMembershipForTenant((string) $tenant->getTenantKey());
    }

    public function canAccessCurrentTenant(?PlatformUser $user): bool
    {
        return $this->membershipForCurrentTenant($user) !== null;
    }

    public function tenantForUser(?PlatformUser $user, string $tenantId): ?Tenant
    {
        if (! $user) {
            return null;
        }

        return TenantMembership::query()
            ->where('user_id', $user->getAuthIdentifier())
            ->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->with('tenant')
            ->first()
            ?->tenant;
    }
}
