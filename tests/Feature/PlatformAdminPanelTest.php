<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\PlatformUser;
use App\Providers\Filament\PlatformPanelProvider;
use Filament\Panel;
use Tests\TestCase;

class PlatformAdminPanelTest extends TestCase
{
    public function test_platform_panel_uses_a_separate_guard_and_admin_provider(): void
    {
        $this->assertSame('platform_admins', config('auth.guards.platform.provider'));
        $this->assertSame(Admin::class, config('auth.providers.platform_admins.model'));
        $this->assertSame(PlatformUser::class, config('auth.providers.admins.model'));
    }

    public function test_platform_admin_can_access_only_the_platform_panel(): void
    {
        $admin = new Admin();
        $platformPanel = $this->mock(Panel::class);
        $platformPanel->shouldReceive('getId')->once()->andReturn('platform');

        $sellerPanel = $this->mock(Panel::class);
        $sellerPanel->shouldReceive('getId')->once()->andReturn('admin');

        $this->assertTrue($admin->canAccessPanel($platformPanel));
        $this->assertFalse($admin->canAccessPanel($sellerPanel));
    }

    public function test_platform_panel_provider_is_registered(): void
    {
        $this->assertContains(
            PlatformPanelProvider::class,
            require base_path('bootstrap/providers.php'),
        );
    }
}
