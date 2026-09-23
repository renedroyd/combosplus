<?php

namespace Tests\Feature;

use App\Enums\TenantRole;
use App\Models\PlatformUser;
use App\Models\Tenant;
use App\Models\TenantMembership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class TenantAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_platform_user_is_provisioned_when_logging_into_a_tenant(): void
    {
        $tenant = Tenant::create(['id' => 'auth-login-tenant']);
        $user = PlatformUser::create(['name' => 'Tenant Customer', 'email' => 'tenant-login@example.test', 'password' => 'password']);
        TenantMembership::create(['tenant_id' => $tenant->id, 'user_id' => $user->id, 'role' => TenantRole::Customer, 'status' => 'active', 'is_owner' => false]);

        try {
            tenancy()->initialize($tenant);
            $response = $this->post('/login', ['email' => $user->email, 'password' => 'password']);
            $response->assertRedirect('/');
            $this->assertTrue(Auth::check());
            $this->assertInstanceOf(PlatformUser::class, Auth::user());
            $this->assertSame($user->email, User::query()->find($user->id)?->email);
        } finally {
            tenancy()->end();
            $tenant->delete();
            $user->delete();
        }
    }

    public function test_tenant_login_requires_an_active_membership(): void
    {
        $tenant = Tenant::create(['id' => 'auth-membership-tenant']);
        $user = PlatformUser::create(['name' => 'Unauthorized', 'email' => 'tenant-denied@example.test', 'password' => 'password']);

        try {
            tenancy()->initialize($tenant);
            $response = $this->post('/login', ['email' => $user->email, 'password' => 'password']);
            $response->assertForbidden();
            $this->assertFalse(Auth::check());
        } finally {
            tenancy()->end();
            $tenant->delete();
            $user->delete();
        }
    }

    public function test_registration_creates_platform_identity_membership_and_customer_projection_in_tenant(): void
    {
        $tenant = Tenant::create(['id' => 'auth-register-tenant']);

        try {
            tenancy()->initialize($tenant);
            $response = $this->post('/register', [
                'name' => 'New Customer',
                'email' => 'tenant-register@example.test',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response->assertRedirect('/');
            $platformUser = PlatformUser::query()->where('email', 'tenant-register@example.test')->first();

            $this->assertNotNull($platformUser);
            $this->assertTrue(TenantMembership::query()
                ->where('tenant_id', $tenant->id)
                ->where('user_id', $platformUser->id)
                ->where('role', TenantRole::Customer->value)
                ->where('status', 'active')
                ->exists());
            $this->assertNotNull(User::query()->find($platformUser->id));
        } finally {
            tenancy()->end();
            $tenant->delete();
        }
    }
}
