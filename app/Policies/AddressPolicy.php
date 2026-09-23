<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\PlatformUser;
use App\Services\Tenancy\TenantAccessService;

class AddressPolicy
{
    public function __construct(private readonly TenantAccessService $access) {}

    public function viewAny(PlatformUser $user): bool
    {
        return $this->access->canAccessCurrentTenant($user);
    }

    public function view(PlatformUser $user, Address $address): bool
    {
        return $this->ownsAddress($user, $address);
    }

    public function create(PlatformUser $user): bool
    {
        return $this->access->canAccessCurrentTenant($user);
    }

    public function update(PlatformUser $user, Address $address): bool
    {
        return $this->ownsAddress($user, $address);
    }

    public function delete(PlatformUser $user, Address $address): bool
    {
        return $this->ownsAddress($user, $address);
    }

    private function ownsAddress(PlatformUser $user, Address $address): bool
    {
        return $this->access->canAccessCurrentTenant($user)
            && (string) $address->user_id === (string) $user->getAuthIdentifier();
    }
}
