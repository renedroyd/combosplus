<?php

namespace Tests\Feature;

use App\Enums\TenantRole;
use App\Filament\Pages\CatalogPublication;
use App\Models\Category;
use App\Models\PlatformUser;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\TenantMembership;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CatalogPublicationPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_publish_a_complete_catalog(): void
    {
        [$tenant, $user] = $this->createStore();

        $tenant->run(function (): void {
            $category = Category::query()->create(['name' => 'Combos', 'slug' => 'combos']);

            Product::query()->create([
                'category_id' => $category->id,
                'name' => 'Combo inicial',
                'slug' => 'combo-inicial',
                'description' => 'Producto de prueba',
                'price' => 10,
                'is_visible' => true,
            ]);
        });

        $this->actingAs($user, 'admin');
        tenancy()->initialize($tenant);

        app(CatalogPublication::class)->publish();

        $this->assertSame(
            'published',
            DB::connection(config('tenancy.database.central_connection', config('database.default')))
                ->table('tenants')
                ->where('id', $tenant->getTenantKey())
                ->value('catalog_status'),
        );
    }

    public function test_catalog_cannot_be_published_without_visible_products(): void
    {
        [$tenant, $user] = $this->createStore();

        $tenant->run(function (): void {
            $category = Category::query()->create(['name' => 'Combos']);

            Product::query()->create([
                'category_id' => $category->id,
                'name' => 'Borrador',
                'slug' => 'borrador',
                'description' => 'Producto de prueba',
                'price' => 10,
                'is_visible' => false,
            ]);
        });

        $this->actingAs($user, 'admin');
        tenancy()->initialize($tenant);

        $this->expectException(ValidationException::class);

        app(CatalogPublication::class)->publish();
    }

    public function test_owner_can_unpublish_catalog(): void
    {
        [$tenant, $user] = $this->createStore('published');

        $this->actingAs($user, 'admin');
        tenancy()->initialize($tenant);

        app(CatalogPublication::class)->unpublish();

        $this->assertSame(
            'unpublished',
            DB::connection(config('tenancy.database.central_connection', config('database.default')))
                ->table('tenants')
                ->where('id', $tenant->getTenantKey())
                ->value('catalog_status'),
        );
    }

    private function createStore(string $catalogStatus = 'draft'): array
    {
        $tenant = Tenant::create(['id' => 'publication-'.uniqid()]);

        DB::table('tenants')->where('id', $tenant->getTenantKey())->update([
            'name' => 'Tienda de prueba',
            'slug' => 'tienda-'.uniqid(),
            'catalog_status' => $catalogStatus,
        ]);

        $user = PlatformUser::query()->create([
            'name' => 'Seller',
            'email' => 'seller-'.uniqid().'@example.com',
            'password' => 'password',
        ]);

        TenantMembership::query()->create([
            'tenant_id' => $tenant->getTenantKey(),
            'user_id' => $user->getAuthIdentifier(),
            'role' => TenantRole::Owner,
            'status' => 'active',
            'is_owner' => true,
        ]);

        return [$tenant, $user];
    }
}
