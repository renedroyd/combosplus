<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketplaceSellerOnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_marketplace_registration_creates_platform_account_store_and_owner_membership(): void
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

        $this->assertDatabaseHas('users', ['email' => 'owner-demo@example.test']);
        $this->assertDatabaseHas('tenants', [
            'slug' => 'tienda-demo',
            'name' => 'Tienda Demo',
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('domains', [
            'tenant_id' => \App\Models\Tenant::where('slug', 'tienda-demo')->value('id'),
            'domain' => 'tienda-demo.localhost',
        ]);
        $this->assertDatabaseHas('tenant_memberships', [
            'role' => 'owner',
            'is_owner' => true,
        ]);
    }
}
