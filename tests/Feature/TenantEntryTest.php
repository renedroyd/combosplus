<?php

namespace Tests\Feature;

use App\Enums\TenantRole;
use App\Models\PlatformUser;
use App\Models\Tenant;
use App\Models\TenantMembership;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantEntryTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_member_can_enter_its_tenant(): void
    {
        $tenant = Tenant::create(['id' => 'entry-allowed']);
        $user = PlatformUser::create([
            'name' => 'Tenant Owner',
            'email' => 'owner@example.test',
            'password' => 'password',
        ]);

        TenantMembership::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'role' => TenantRole::Owner,
            'status' => 'active',
            'is_owner' => true,
        ]);

        try {
            $response = $this->actingAs($user)
                ->get($this->tenantUrl($tenant, '/tenant/secure'));

            $response->assertOk()->assertJson([
                'status' => 'ok',
                'tenant_id' => $tenant->id,
            ]);
        } finally {
            tenancy()->end();
            $tenant->delete();
            $user->delete();
        }
    }

    public function test_non_member_is_denied(): void
    {
        $tenant = Tenant::create(['id' => 'entry-denied']);
        $user = PlatformUser::create([
            'name' => 'Other User',
            'email' => 'other@example.test',
            'password' => 'password',
        ]);

        try {
            $response = $this->actingAs($user)
                ->get($this->tenantUrl($tenant, '/tenant/secure'));

            $response->assertForbidden();
        } finally {
            tenancy()->end();
            $tenant->delete();
            $user->delete();
        }
    }

    public function test_inactive_membership_is_denied(): void
    {
        $tenant = Tenant::create(['id' => 'entry-inactive']);
        $user = PlatformUser::create([
            'name' => 'Inactive User',
            'email' => 'inactive@example.test',
            'password' => 'password',
        ]);

        TenantMembership::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'role' => TenantRole::Staff,
            'status' => 'suspended',
            'is_owner' => false,
        ]);

        try {
            $response = $this->actingAs($user)
                ->get($this->tenantUrl($tenant, '/tenant/secure'));

            $response->assertForbidden();
        } finally {
            tenancy()->end();
            $tenant->delete();
            $user->delete();
        }
    }

    public function test_membership_for_another_tenant_does_not_grant_access(): void
    {
        $tenantA = Tenant::create(['id' => 'entry-a']);
        $tenantB = Tenant::create(['id' => 'entry-b']);
        $user = PlatformUser::create([
            'name' => 'Single Tenant User',
            'email' => 'single@example.test',
            'password' => 'password',
        ]);

        TenantMembership::create([
            'tenant_id' => $tenantA->id,
            'user_id' => $user->id,
            'role' => TenantRole::Owner,
            'status' => 'active',
            'is_owner' => true,
        ]);

        try {
            $response = $this->actingAs($user)
                ->get($this->tenantUrl($tenantB, '/tenant/secure'));

            $response->assertForbidden();
        } finally {
            tenancy()->end();
            $tenantA->delete();
            $tenantB->delete();
            $user->delete();
        }
    }

    private function tenantUrl(Tenant $tenant, string $path): string
    {
        $domain = $tenant->domains()->first();

        if (! $domain) {
            $domain = $tenant->domains()->create([
                'domain' => $tenant->id . '.localhost',
            ]);
        }

        return 'http://' . $domain->domain . $path;
    }
}
