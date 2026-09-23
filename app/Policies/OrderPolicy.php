<?php

namespace App\Policies;

use App\Enums\TenantRole;
use App\Models\Order;
use App\Models\PlatformUser;
use App\Services\Tenancy\TenantAccessService;

class OrderPolicy
{
    public function __construct(private readonly TenantAccessService $access)
    {
    }

    public function viewAny(PlatformUser $user): bool
    {
        return $this->access->canAccessCurrentTenant($user);
    }

    public function view(PlatformUser $user, Order $order): bool
    {
        return $this->canOperateOrders($user);
    }

    public function update(PlatformUser $user, Order $order): bool
    {
        return $this->canOperateOrders($user);
    }

    public function delete(PlatformUser $user, Order $order): bool
    {
        return $this->canManageOrders($user);
    }

    private function canOperateOrders(PlatformUser $user): bool
    {
        $membership = $this->access->membershipForCurrentTenant($user);

        return $membership !== null && in_array($membership->role, [
            TenantRole::Owner,
            TenantRole::Admin,
            TenantRole::Manager,
            TenantRole::Staff,
        ], true);
    }

    private function canManageOrders(PlatformUser $user): bool
    {
        $membership = $this->access->membershipForCurrentTenant($user);

        return $membership !== null && in_array($membership->role, [
            TenantRole::Owner,
            TenantRole::Admin,
            TenantRole::Manager,
        ], true);
    }
}
