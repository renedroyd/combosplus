<?php

namespace App\Policies;

use App\Enums\TenantRole;
use App\Models\Order;
use App\Models\PlatformUser;
use App\Services\Tenancy\TenantAccessService;

class OrderPolicy
{
    public function viewAny(PlatformUser $user, TenantAccessService $access): bool
    {
        return $access->canAccessCurrentTenant($user);
    }

    public function view(PlatformUser $user, Order $order, TenantAccessService $access): bool
    {
        return $this->canOperateOrders($user, $access);
    }

    public function update(PlatformUser $user, Order $order, TenantAccessService $access): bool
    {
        return $this->canOperateOrders($user, $access);
    }

    public function delete(PlatformUser $user, Order $order, TenantAccessService $access): bool
    {
        return $this->canManageOrders($user, $access);
    }

    private function canOperateOrders(PlatformUser $user, TenantAccessService $access): bool
    {
        $membership = $access->membershipForCurrentTenant($user);

        return $membership !== null && in_array($membership->role, [
            TenantRole::Owner,
            TenantRole::Admin,
            TenantRole::Manager,
            TenantRole::Staff,
        ], true);
    }

    private function canManageOrders(PlatformUser $user, TenantAccessService $access): bool
    {
        $membership = $access->membershipForCurrentTenant($user);

        return $membership !== null && in_array($membership->role, [
            TenantRole::Owner,
            TenantRole::Admin,
            TenantRole::Manager,
        ], true);
    }
}
