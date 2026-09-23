<?php

namespace App\Policies;

use App\Enums\TenantRole;
use App\Models\PlatformUser;
use App\Models\Product;
use App\Services\Tenancy\TenantAccessService;

class ProductPolicy
{
    public function __construct(private readonly TenantAccessService $access)
    {
    }

    public function viewAny(PlatformUser $user): bool
    {
        return $this->access->canAccessCurrentTenant($user);
    }

    public function view(PlatformUser $user, Product $product): bool
    {
        return $this->access->canAccessCurrentTenant($user);
    }

    public function create(PlatformUser $user): bool
    {
        return $this->canManageCatalog($user);
    }

    public function update(PlatformUser $user, Product $product): bool
    {
        return $this->canManageCatalog($user);
    }

    public function delete(PlatformUser $user, Product $product): bool
    {
        return $this->canManageCatalog($user);
    }

    private function canManageCatalog(PlatformUser $user): bool
    {
        $membership = $this->access->membershipForCurrentTenant($user);

        return $membership !== null && in_array($membership->role, [
            TenantRole::Owner,
            TenantRole::Admin,
            TenantRole::Manager,
        ], true);
    }
}
