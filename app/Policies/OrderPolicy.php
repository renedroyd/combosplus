<?php

namespace App\Policies;

use App\Enums\TenantRole;
use App\Models\Order;
use App\Models\PlatformUser;
use App\Services\Tenancy\TenantAccessService;

class OrderPolicy
{
    public function __construct(private readonly TenantAccessService $access) {}

    public function viewAny(PlatformUser $user): bool
    {
        return $this->access->canAccessCurrentTenant($user);
    }

    public function view(PlatformUser $user, Order $order): bool
    {
        if (! $this->access->canAccessCurrentTenant($user)) return false;
        $role = $this->access->membershipForCurrentTenant($user)?->role;

        if ($role === TenantRole::Customer) {
            return (string) $order->user_id === (string) $user->getAuthIdentifier();
        }

        return in_array($role, [TenantRole::Owner, TenantRole::Admin, TenantRole::Manager, TenantRole::Staff], true);
    }

    public function update(PlatformUser $user, Order $order): bool
    {
        if (! $this->access->canAccessCurrentTenant($user)) return false;
        $role = $this->access->membershipForCurrentTenant($user)?->role;

        if ($role === TenantRole::Customer) {
            return (string) $order->user_id === (string) $user->getAuthIdentifier()
                && $order->status === 'pending';
        }

        return in_array($role, [TenantRole::Owner, TenantRole::Admin, TenantRole::Manager, TenantRole::Staff], true);
    }

    public function delete(PlatformUser $user, Order $order): bool
    {
        $role = $this->access->membershipForCurrentTenant($user)?->role;

        return in_array($role, [TenantRole::Owner, TenantRole::Admin, TenantRole::Manager], true);
    }
}
