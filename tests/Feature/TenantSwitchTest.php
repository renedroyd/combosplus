<?php

namespace Tests\Feature;

use App\Models\PlatformUser;
use App\Models\Tenant;
use App\Models\TenantMembership;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tests\TestCase;

class TenantSwitchTest extends TestCase
{
    use RefreshDatabase;

    public function test_switch_bridges_web_authentication_to_filament_admin_guard(): void
    {
        $user = PlatformUser::query()->create([
            'name' => 'Owner',
            'email' => 'owner-'.Str::uuid().'@example.test',
            'password' => 'password',
        ]);

        $tenant = Tenant::create([
            'id' => (string) Str::uuid(),
        ]);

        $tenant->domains()->create([
            'domain' => 'store.localhost',
        ]);

        TenantMembership::create([
            'tenant_id' => $tenant->getTenantKey(),
            'user_id' => $user->getAuthIdentifier(),
            'role' => 'owner',
            'status' => 'active',
            'is_owner' => true,
        ]);

        $this->actingAs($user, 'web');

        $response = $this->get(route('tenant.switch', [
            'tenantId' => $tenant->getTenantKey(),
        ]));

        $response->assertRedirect(config('app.url').replaceFirst(parse_url(config('app.url'), PHP_URL_HOST), 'store.localhost').'/tenant/secure');
        $this->assertTrue(Auth::guard('web')->check());
        $this->assertTrue(Auth::guard('admin')->check());
        $this->assertSame($user->getAuthIdentifier(), Auth::guard('admin')->id());
    }
}
