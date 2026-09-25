<?php

namespace Tests\Feature;

use App\Filament\Pages\StoreSettings;
use App\Models\PlatformUser;
use App\Models\Tenant;
use App\Models\TenantMembership;
use App\Enums\TenantRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StoreSettingsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_settings_updates_current_tenant_profile(): void
    {
        $tenant = Tenant::create(['id' => 'settings-tenant']);

        DB::table('tenants')->where('id', $tenant->getTenantKey())->update([
            'name' => 'Tienda inicial',
            'slug' => 'tienda-inicial',
        ]);

        $user = PlatformUser::query()->create([
            'name' => 'Seller',
            'email' => 'seller-settings@example.com',
            'password' => 'password',
        ]);

        TenantMembership::query()->create([
            'tenant_id' => $tenant->getTenantKey(),
            'user_id' => $user->getAuthIdentifier(),
            'role' => TenantRole::Owner,
            'status' => 'active',
            'is_owner' => true,
        ]);

        $this->actingAs($user, 'admin');

        tenancy()->initialize($tenant);

        $page = app(StoreSettings::class);
        $page->data = [
            'name' => 'Tienda actualizada',
            'slug' => 'tienda-actualizada',
            'description' => 'Una descripción pública.',
            'logo' => null,
        ];

        $page->save();

        $this->assertDatabaseHas(config('tenancy.database.central_connection', config('database.default')) . '.tenants', [
            'id' => $tenant->getTenantKey(),
            'name' => 'Tienda actualizada',
            'slug' => 'tienda-actualizada',
            'description' => 'Una descripción pública.',
        ]);
    }
}
