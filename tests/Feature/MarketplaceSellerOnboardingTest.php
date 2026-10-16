<?php

namespace Tests\Feature;

use App\Models\PlatformUser;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketplaceSellerOnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_marketplace_registration_creates_platform_account_store_owner_membership_and_tenant_user(): void
    {
        $this->post(route('marketplace.register.store'), [
            'store_name' => 'Tienda Demo',
            'slug' => 'tienda-demo',
            'description' => 'Una tienda de prueba.',
            'name' => 'Propietario Demo',
            'email' => 'owner-demo@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect();

        $platformUser = PlatformUser::query()->where('email', 'owner-demo@example.test')->firstOrFail();
        $tenant = Tenant::query()->where('slug', 'tienda-demo')->firstOrFail();

        $this->assertDatabaseHas('users', ['email' => 'owner-demo@example.test']);
        $this->assertDatabaseHas('tenants', [
            'slug' => 'tienda-demo',
            'name' => 'Tienda Demo',
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('domains', [
            'tenant_id' => $tenant->getTenantKey(),
            'domain' => 'tienda-demo.localhost',
        ]);
        $this->assertDatabaseHas('tenant_memberships', [
            'tenant_id' => $tenant->getTenantKey(),
            'user_id' => $platformUser->getAuthIdentifier(),
            'role' => 'owner',
            'is_owner' => true,
        ]);

        $tenantUserExists = $tenant->run(
            fn () => User::query()
                ->whereKey($platformUser->getAuthIdentifier())
                ->where('email', 'owner-demo@example.test')
                ->exists()
        );

        $this->assertTrue($tenantUserExists, 'The initial store owner must exist in the tenant users table.');
    }
}
