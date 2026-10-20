<?php

namespace Tests\Feature;

use App\Enums\TenantRole;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Products\ProductResource;
use App\Models\Category;
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
        $tenant = Tenant::create([
            'id' => 'onboarding-widget',
            'name' => 'Onboarding Store',
            'slug' => 'onboarding-widget',
        ]);
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
            $data = $tenant->run(fn () => $widget->getViewData());

            $this->assertSame(25, $data['progress']);
            $this->assertCount(4, $data['steps']);
            $this->assertTrue($data['steps'][0]['done']);
            $this->assertFalse($data['steps'][1]['done']);
            $this->assertSame(CategoryResource::getUrl('create'), $data['steps'][1]['url']);
            $this->assertSame(ProductResource::getUrl('create'), $data['steps'][2]['url']);
            $this->assertSame(ProductResource::getUrl('index'), $data['steps'][3]['url']);
        } finally {
            $tenant->delete();
            $user->delete();
        }
    }

    public function test_onboarding_widget_uses_current_tenant_when_owner_has_multiple_stores(): void
    {
        $tenantA = Tenant::create([
            'id' => 'onboarding-multi-a',
            'name' => 'Store A',
            'slug' => 'onboarding-multi-a',
        ]);
        $tenantB = Tenant::create([
            'id' => 'onboarding-multi-b',
            'name' => 'Store B',
            'slug' => 'onboarding-multi-b',
        ]);

        $user = PlatformUser::create([
            'name' => 'Multi-store Owner',
            'email' => 'onboarding-multi-owner@example.test',
            'password' => 'password',
        ]);

        try {
            TenantMembership::create([
                'tenant_id' => $tenantB->id,
                'user_id' => $user->id,
                'role' => TenantRole::Owner,
                'status' => 'active',
                'is_owner' => true,
            ]);

            // Make Store A the most recently created membership to catch regressions
            // that accidentally select the user's latest membership instead of the active tenant.
            TenantMembership::create([
                'tenant_id' => $tenantA->id,
                'user_id' => $user->id,
                'role' => TenantRole::Owner,
                'status' => 'active',
                'is_owner' => true,
            ]);

            $tenantA->run(fn () => Category::create([
                'name' => 'Store A category',
                'slug' => 'store-a-category',
            ]));

            $this->actingAs($user, 'admin');
            $widget = new \App\Filament\Widgets\StoreOnboarding;
            $data = $tenantB->run(fn () => $widget->getViewData());

            $this->assertSame(25, $data['progress']);
            $this->assertFalse($data['steps'][1]['done']);
        } finally {
            $tenantA->delete();
            $tenantB->delete();
            $user->delete();
        }
    }
}
