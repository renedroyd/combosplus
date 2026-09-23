<?php

namespace Tests\Feature;

use App\Enums\TenantRole;
use App\Models\Tenant;
use App\Models\TenantMembership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantMembershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_memberships_are_stored_in_the_central_database(): void
    {
        $tenant = Tenant::create(['id' => 'membership-test']);
        $user = User::create([
            'name' => 'Platform User',
            'email' => 'platform@example.test',
            'password' => 'password',
        ]);

        try {
            tenancy()->initialize($tenant);

            $membership = TenantMembership::create([
                'tenant_id' => $tenant->id,
                'user_id' => $user->id,
                'role' => TenantRole::Owner,
                'status' => 'active',
                'is_owner' => true,
            ]);

            $this->assertSame(
                config('tenancy.database.central_connection'),
                $membership->getConnectionName()
            );
            $this->assertSame($tenant->id, $membership->tenant_id);
            $this->assertTrue($membership->active());
            $this->assertTrue($membership->canManageTeam());
            $this->assertSame(TenantRole::Owner, $membership->role);
        } finally {
            tenancy()->end();
            $tenant->delete();
            $user->delete();
        }
    }

    public function test_membership_role_permissions_are_explicit(): void
    {
        $membership = new TenantMembership(['role' => TenantRole::Manager]);

        $this->assertFalse($membership->canManageTeam());

        $membership->role = TenantRole::Admin;
        $this->assertTrue($membership->canManageTeam());
    }
}
