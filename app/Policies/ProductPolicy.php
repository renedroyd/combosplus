<?php

namespace App\Policies;

use App\Enums\TenantRole;
use App\Models\PlatformUser;
use App\Models\Product;
use App\Services\Tenancy\TenantAccessService;

class ProductPolicy
{
    public function viewAny(PlatformUser $user, TenantAccessService $access): bool
    {
        return $access->canAccessCurrentTenant($user);
    }

    public function view(PlatformUser $user, Product $product, TenantAccessService $access): bool
    {
        return $access->canAccessCurrentTenant($user);
    }

    public function create(PlatformUser $user, TenantAccessService $access): bool
    {
        return $this->canManageCatalog($user, $access);
    }

    public function update(PlatformUser $user, Product $product, TenantAccessService $access): bool
    {
        return $this->canManageCatalog($user, $access);
    }

    public function delete(PlatformUser $user, Product $product, TenantAccessService $access): bool
    {
        return $this->canManageCatalog($user, $access);
    }

    private function canManageCatalog(PlatformUser $user, TenantAccessService $access): bool
    {
        $membership = $access->membershipForCurrentTenant($user);

        return $membership !== null && in_array($membership->role, [
            TenantRole::Owner,
            TenantRole::Admin,
            TenantRole::Manager,
        ], true);
    }
}
