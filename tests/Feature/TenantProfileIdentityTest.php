<?php

namespace Tests\Feature;

use App\Enums\TenantRole;
use App\Models\PlatformUser;
use App\Models\Tenant;
use App\Models\TenantMembership;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantProfileIdentityTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_update_syncs_the_central_identity_to_the_tenant_projection(): void
    {
        $tenant = Tenant::create(['id' => 'profile-identity']);
        $domain = $tenant->domains()->create(['domain' => 'profile-identity.test']);
        $user = PlatformUser::create([
            'name' => 'Original Name',
            'email' => 'profile-identity@example.test',
            'password' => 'password',
        ]);

        TenantMembership::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'role' => TenantRole::Customer,
            'status' => 'active',
            'is_owner' => false,
        ]);

        try {
            $tenant->run(function () use ($user): void {
                app(\App\Services\Tenancy\TenantCustomerProvisioner::class)->ensure($user);
            });

            $response = $this->actingAs($user)->patch(
                'http://' . $domain->domain . '/perfil',
                [
                    'name' => 'Updated Name',
                    'email' => 'profile-identity@example.test',
                ]
            );

            $response->assertRedirect();

            $this->assertSame('Updated Name', $user->fresh()->name);
            $this->assertSame(
                'Updated Name',
                $tenant->run(fn () => \App\Models\User::query()->find($user->id)?->name)
            );
        } finally {
            tenancy()->end();
            $tenant->delete();
            $user->delete();
        }
    }

    public function test_profile_email_must_be_unique_in_the_central_identity_store(): void
    {
        $tenant = Tenant::create(['id' => 'profile-email']);
        $domain = $tenant->domains()->create(['domain' => 'profile-email.test']);
        $user = PlatformUser::create([
            'name' => 'Current User',
            'email' => 'current-profile@example.test',
            'password' => 'password',
        ]);
        $other = PlatformUser::create([
            'name' => 'Other User',
            'email' => 'existing-profile@example.test',
            'password' => 'password',
        ]);

        TenantMembership::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'role' => TenantRole::Customer,
            'status' => 'active',
            'is_owner' => false,
        ]);

        try {
            $tenant->run(function () use ($user): void {
                app(\App\Services\Tenancy\TenantCustomerProvisioner::class)->ensure($user);
            });

            $response = $this->actingAs($user)->patch(
                'http://' . $domain->domain . '/perfil',
                [
                    'name' => 'Current User',
                    'email' => $other->email,
                ]
            );

            $response->assertSessionHasErrors('email');
            $this->assertSame('current-profile@example.test', $user->fresh()->email);
        } finally {
            tenancy()->end();
            $tenant->delete();
            $user->delete();
            $other->delete();
        }
    }
}
