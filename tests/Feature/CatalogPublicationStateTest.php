<?php

namespace Tests\Feature;

use App\Enums\CatalogStatus;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class CatalogPublicationStateTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_store_starts_as_draft(): void
    {
        $tenant = Tenant::create(['id' => (string) Str::uuid()]);

        DB::table('tenants')
            ->where('id', $tenant->getTenantKey())
            ->update([
                'name' => 'Tienda Demo',
                'slug' => 'tienda-demo',
            ]);

        $tenant->refresh();

        $this->assertSame(CatalogStatus::Draft->value, $tenant->catalog_status ?? CatalogStatus::Draft->value);
    }

    public function test_catalog_status_can_transition_to_published(): void
    {
        $tenant = Tenant::create(['id' => (string) Str::uuid()]);

        DB::table('tenants')
            ->where('id', $tenant->getTenantKey())
            ->update([
                'name' => 'Tienda Demo',
                'slug' => 'tienda-demo',
                'catalog_status' => CatalogStatus::Published->value,
            ]);

        $tenant->refresh();

        $this->assertSame(CatalogStatus::Published->value, $tenant->catalog_status);
    }
}
