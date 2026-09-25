<?php

namespace Tests\Feature;

use App\Models\PlatformUser;
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

    public function test_platform_user_implements_filament_access_contract(): void
    {
        $this->assertInstanceOf(
            \Filament\Models\Contracts\FilamentUser::class,
            new PlatformUser(),
        );
    }
}
