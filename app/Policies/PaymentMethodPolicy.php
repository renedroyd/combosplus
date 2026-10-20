<?php

namespace App\Policies;

use App\Enums\TenantRole;
use App\Models\PaymentMethod;
use App\Models\PlatformUser;
use App\Services\Tenancy\TenantAccessService;

class PaymentMethodPolicy
{
    public function __construct(private readonly TenantAccessService $access) {}

    public function viewAny(PlatformUser $user): bool
    {
        return $this->canManagePayments($user);
    }

    public function view(PlatformUser $user, PaymentMethod $paymentMethod): bool
    {
        return $this->canManagePayments($user);
    }

    public function create(PlatformUser $user): bool
    {
        return $this->canManagePayments($user);
    }

    public function update(PlatformUser $user, PaymentMethod $paymentMethod): bool
    {
        return $this->canManagePayments($user);
    }

    public function delete(PlatformUser $user, PaymentMethod $paymentMethod): bool
    {
        return $this->canManagePayments($user);
    }

    public function deleteAny(PlatformUser $user): bool
    {
        return $this->canManagePayments($user);
    }

    private function canManagePayments(PlatformUser $user): bool
    {
        $membership = $this->access->membershipForCurrentTenant($user);

        return $membership !== null && in_array($membership->role, [
            TenantRole::Owner,
            TenantRole::Admin,
            TenantRole::Manager,
        ], true);
    }
}
