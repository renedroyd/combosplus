<?php

namespace Tests\Feature;

use App\Enums\TenantRole;
use App\Models\PlatformUser;
use App\Models\Tenant;
use App\Models\TenantMembership;
use App\Services\Tenancy\TenantAccessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_management_roles_are_explicit(): void
    {
        $tenant = Tenant::create(['id' => 'policy-roles']);
        $manager = PlatformUser::create(['name' => 'Manager', 'email' => 'manager-policy@example.test', 'password' => 'password']);
        $staff = PlatformUser::create(['name' => 'Staff', 'email' => 'staff-policy@example.test', 'password' => 'password']);

        TenantMembership::create(['tenant_id' => $tenant->id, 'user_id' => $manager->id, 'role' => TenantRole::Manager, 'status' => 'active', 'is_owner' => false]);
        TenantMembership::create(['tenant_id' => $tenant->id, 'user_id' => $staff->id, 'role' => TenantRole::Staff, 'status' => 'active', 'is_owner' => false]);

        try {
            $this->assertTrue($tenant->run(fn () => app(TenantAccessService::class)->canAccessCurrentTenant($manager)));
            $this->assertTrue($tenant->run(fn () => app(TenantAccessService::class)->canAccessCurrentTenant($staff)));
        } finally {
            $tenant->delete();
            $manager->delete();
            $staff->delete();
        }
    }

    public function test_membership_in_one_tenant_does_not_authorize_another(): void
    {
        $allowed = Tenant::create(['id' => 'policy-allowed']);
        $other = Tenant::create(['id' => 'policy-other']);
        $user = PlatformUser::create(['name' => 'Owner', 'email' => 'owner-policy@example.test', 'password' => 'password']);

        TenantMembership::create(['tenant_id' => $allowed->id, 'user_id' => $user->id, 'role' => TenantRole::Owner, 'status' => 'active', 'is_owner' => true]);

        try {
            $this->assertTrue($allowed->run(fn () => app(TenantAccessService::class)->canAccessCurrentTenant($user)));
            $this->assertFalse($other->run(fn () => app(TenantAccessService::class)->canAccessCurrentTenant($user)));
        } finally {
            $allowed->delete();
            $other->delete();
            $user->delete();
        }
    }
}
