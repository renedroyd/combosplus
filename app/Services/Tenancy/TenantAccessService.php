<?php

namespace App\Services\Tenancy;

use App\Models\PlatformUser;
use App\Models\TenantMembership;

class TenantAccessService
{
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
}
