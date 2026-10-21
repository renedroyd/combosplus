<?php

namespace App\Policies;

use App\Enums\TenantRole;
use App\Models\Category;
use App\Models\PlatformUser;
use App\Services\Tenancy\TenantAccessService;

class CategoryPolicy
{
    public function __construct(private readonly TenantAccessService $access) {}

    public function viewAny(PlatformUser $user): bool
    {
        return $this->canManageCatalog($user);
    }

    public function view(PlatformUser $user, Category $category): bool
    {
        return $this->canManageCatalog($user);
    }

    public function create(PlatformUser $user): bool
    {
        return $this->canManageCatalog($user);
    }

    public function update(PlatformUser $user, Category $category): bool
    {
        return $this->canManageCatalog($user);
    }

    public function delete(PlatformUser $user, Category $category): bool
    {
        return $this->canManageCatalog($user);
    }

    public function deleteAny(PlatformUser $user): bool
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
