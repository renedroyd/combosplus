<?php

namespace Tests\Feature;

use App\Enums\TenantRole;
use App\Models\PlatformUser;
use App\Models\Tenant;
use App\Models\TenantMembership;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreOnboardingWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_onboarding_widget_can_calculate_progress_from_tenant_catalog(): void
    {
        $tenant = Tenant::create(['id' => 'onboarding-widget']);
        $tenant->domains()->create(['domain' => 'onboarding-widget.localhost']);

        $user = PlatformUser::create([
            'name' => 'Owner',
            'email' => 'onboarding-owner@example.test',
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
            $this->actingAs($user, 'admin');

            $widget = new \App\Filament\Widgets\StoreOnboarding;
            $data = $widget->getViewData();

            $this->assertSame(25, $data['progress']);
            $this->assertCount(4, $data['steps']);
            $this->assertTrue($data['steps'][0]['done']);
            $this->assertFalse($data['steps'][1]['done']);
        } finally {
            $tenant->delete();
            $user->delete();
        }
    }
}
