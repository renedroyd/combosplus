<?php

namespace App\Policies;

use App\Enums\TenantRole;
use App\Models\PlatformUser;
use App\Models\User;
use App\Services\Tenancy\TenantAccessService;

class UserPolicy
{
    public function __construct(private readonly TenantAccessService $access) {}

    public function viewAny(PlatformUser $user): bool
    {
        return $this->canManageCustomers($user);
    }

    public function view(PlatformUser $user, User $customer): bool
    {
        return $this->canManageCustomers($user);
    }

    public function create(PlatformUser $user): bool
    {
        return $this->canManageCustomers($user);
    }

    public function update(PlatformUser $user, User $customer): bool
    {
        return $this->canManageCustomers($user);
    }

    public function delete(PlatformUser $user, User $customer): bool
    {
        $membership = $this->access->membershipForCurrentTenant($user);

        return $membership !== null && in_array($membership->role, [
            TenantRole::Owner,
            TenantRole::Admin,
            TenantRole::Manager,
        ], true);
    }

    public function deleteAny(PlatformUser $user): bool
    {
        $membership = $this->access->membershipForCurrentTenant($user);

        return $membership !== null && in_array($membership->role, [
            TenantRole::Owner,
            TenantRole::Admin,
            TenantRole::Manager,
        ], true);
    }

    private function canManageCustomers(PlatformUser $user): bool
    {
        $membership = $this->access->membershipForCurrentTenant($user);

        return $membership !== null && in_array($membership->role, [
            TenantRole::Owner,
            TenantRole::Admin,
            TenantRole::Manager,
            TenantRole::Staff,
        ], true);
    }
}
