<?php

namespace App\Policies;

use App\Enums\TenantRole;
use App\Models\Branch;
use App\Models\PlatformUser;
use App\Services\Tenancy\TenantAccessService;

class BranchPolicy
{
    public function __construct(private readonly TenantAccessService $access) {}

    public function viewAny(PlatformUser $user): bool
    {
        return $this->canManageBranches($user);
    }

    public function view(PlatformUser $user, Branch $branch): bool
    {
        return $this->canManageBranches($user);
    }

    public function create(PlatformUser $user): bool
    {
        return $this->canManageBranches($user);
    }

    public function update(PlatformUser $user, Branch $branch): bool
    {
        return $this->canManageBranches($user);
    }

    public function delete(PlatformUser $user, Branch $branch): bool
    {
        return $this->canManageBranches($user);
    }

    public function deleteAny(PlatformUser $user): bool
    {
        return $this->canManageBranches($user);
    }

    private function canManageBranches(PlatformUser $user): bool
    {
        $membership = $this->access->membershipForCurrentTenant($user);

        return $membership !== null && in_array($membership->role, [
            TenantRole::Owner,
            TenantRole::Admin,
            TenantRole::Manager,
        ], true);
    }
}
