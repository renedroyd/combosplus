<?php

namespace Tests\Feature;

use App\Models\PlatformUser;
use Illuminate\Support\Str;
use App\Http\Middleware\EnsureTenantMembership;
use App\Services\Tenancy\TenantAccessService;
use Illuminate\Http\Request;
use Tests\TestCase;

class FilamentSellerAuthenticationTest extends TestCase
{
    public function test_admin_guard_uses_platform_users(): void
    {
        $this->assertSame(
            PlatformUser::class,
            config('auth.guards.admin.provider') === 'admins'
                ? config('auth.providers.admins.model')
                : config('auth.providers.users.model'),
        );
    }

    public function test_tenant_membership_prefers_the_admin_guard_for_filament(): void
    {
        $adminUser = PlatformUser::query()->create([
            'name' => 'Admin Seller',
            'email' => 'admin-' . Str::uuid() . '@example.com',
            'password' => 'password',
        ]);
        $webUser = PlatformUser::query()->create([
            'name' => 'Web Seller',
            'email' => 'web-' . Str::uuid() . '@example.com',
            'password' => 'password',
        ]);

        $this->actingAs($webUser, 'web');
        $this->actingAs($adminUser, 'admin');

        $service = $this->mock(TenantAccessService::class);
        $service->shouldReceive('canAccessCurrentTenant')
            ->once()
            ->with($adminUser)
            ->andReturnTrue();

        $request = Request::create('/admin', 'GET');
        $request->setUserResolver(fn ($guard = null) => auth()->guard($guard ?? 'web')->user());

        $response = (new EnsureTenantMembership($service))->handle(
            $request,
            fn () => response('ok'),
        );

        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_platform_user_implements_filament_access_contract(): void
    {
        $this->assertInstanceOf(
            \Filament\Models\Contracts\FilamentUser::class,
            new PlatformUser(),
        );
    }
}
