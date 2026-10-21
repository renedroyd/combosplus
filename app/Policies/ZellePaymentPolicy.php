<?php

namespace App\Policies;

use App\Enums\TenantRole;
use App\Models\PlatformUser;
use App\Models\ZellePayment;
use App\Services\Tenancy\TenantAccessService;

class ZellePaymentPolicy
{
    public function __construct(private readonly TenantAccessService $access) {}

    public function viewAny(PlatformUser $user): bool
    {
        return $this->canReviewPayments($user);
    }

    public function view(PlatformUser $user, ZellePayment $payment): bool
    {
        return $this->canReviewPayments($user);
    }

    public function create(PlatformUser $user): bool
    {
        return false;
    }

    public function update(PlatformUser $user, ZellePayment $payment): bool
    {
        return $this->canReviewPayments($user);
    }

    public function delete(PlatformUser $user, ZellePayment $payment): bool
    {
        return $this->canReviewPayments($user);
    }

    public function deleteAny(PlatformUser $user): bool
    {
        return $this->canReviewPayments($user);
    }

    private function canReviewPayments(PlatformUser $user): bool
    {
        $membership = $this->access->membershipForCurrentTenant($user);

        return $membership !== null && in_array($membership->role, [
            TenantRole::Owner,
            TenantRole::Admin,
            TenantRole::Manager,
        ], true);
    }
}
