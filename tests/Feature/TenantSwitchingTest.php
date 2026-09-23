<?php

namespace Tests\Feature;

use App\Enums\TenantRole;
use App\Models\PlatformUser;
use App\Models\Tenant;
use App\Models\TenantMembership;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantSwitchingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_only_active_tenant_memberships(): void
    {
        $tenant = Tenant::create(['id' => 'switch-active']);
        $inactive = Tenant::create(['id' => 'switch-inactive']);
        $user = PlatformUser::create([
            'name' => 'Switcher',
            'email' => 'switcher@example.test',
            'password' => 'password',
        ]);

        TenantMembership::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'role' => TenantRole::Owner,
            'status' => 'active',
            'is_owner' => true,
        ]);
        TenantMembership::create([
            'tenant_id' => $inactive->id,
            'user_id' => $user->id,
            'role' => TenantRole::Staff,
            'status' => 'suspended',
            'is_owner' => false,
        ]);

        try {
            $response = $this->actingAs($user)->get('/tenant/switch');

            $response->assertOk()
                ->assertJsonPath('data.0.tenant_id', 'switch-active')
                ->assertJsonCount(1, 'data');
        } finally {
            $tenant->delete();
            $inactive->delete();
            $user->delete();
        }
    }

    public function test_switch_rejects_tenant_without_active_membership(): void
    {
        $tenant = Tenant::create(['id' => 'switch-denied']);
        $user = PlatformUser::create([
            'name' => 'Denied',
            'email' => 'denied@example.test',
            'password' => 'password',
        ]);

        try {
            $response = $this->actingAs($user)
                ->get('/tenant/switch/' . $tenant->id);

            $response->assertForbidden();
        } finally {
            $tenant->delete();
            $user->delete();
        }
    }
}
