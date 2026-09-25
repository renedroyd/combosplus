<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MarketplaceCatalogOnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_owner_can_build_and_publish_a_catalog_visible_on_the_marketplace(): void
    {
        $tenant = Tenant::create([
            'id' => 'catalog-'.bin2hex(random_bytes(8)),
        ]);

        try {
            DB::table('tenants')
                ->where('id', $tenant->getTenantKey())
                ->update([
                    'name' => 'Catálogo Demo',
                    'slug' => 'catalogo-demo',
                    'status' => 'active',
                ]);
            $tenant->refresh();

            $tenant->domains()->create([
                'domain' => 'catalogo-demo.localhost',
            ]);

            $tenant->run(function () use (&$category, &$product): void {
                $category = Category::create([
                    'name' => 'Tecnología',
                    'slug' => 'tecnologia',
                    'description' => 'Productos tecnológicos.',
                    'is_visible' => true,
                    'sort_order' => 0,
                ]);

                $product = Product::create([
                    'category_id' => $category->getKey(),
                    'name' => 'Laptop Demo',
                    'slug' => 'laptop-demo',
                    'description' => 'Producto publicado de prueba.',
                    'price' => 999.00,
                    'quantity' => 10,
                    'sku' => 'LAP-DEMO-001',
                    'is_visible' => true,
                    'featured' => true,
                ]);
            });

            $this->get(route('marketplace.store', ['tenant' => $tenant->slug]))
                ->assertOk()
                ->assertSee('Catálogo Demo')
                ->assertSee('Laptop Demo');

            $this->get(route('marketplace.product', [
                'tenant' => $tenant->slug,
                'product' => $product->getKey(),
            ]))
                ->assertOk()
                ->assertSee('Laptop Demo')
                ->assertSee('Producto publicado de prueba.');
        } finally {
            $tenant->delete();
        }
    }

    public function test_catalog_data_remains_isolated_between_tenants(): void
    {
        if (config('database.default') !== 'mysql') {
            $this->markTestSkipped('Tenant isolation requires the MySQL CI service.');
        }

        $tenantA = Tenant::create([
            'id' => 'catalog-a-'.bin2hex(random_bytes(8)),
        ]);
        $tenantB = Tenant::create([
            'id' => 'catalog-b-'.bin2hex(random_bytes(8)),
        ]);

        try {
            foreach ([[$tenantA, 'Producto A', 'SKU-A'], [$tenantB, 'Producto B', 'SKU-B']] as [$tenant, $name, $sku]) {
                $tenant->run(function () use ($name, $sku): void {
                    Product::create([
                        'name' => $name,
                        'slug' => strtolower(str_replace(' ', '-', $name)),
                        'description' => $name,
                        'price' => 10,
                        'quantity' => 1,
                        'sku' => $sku,
                        'is_visible' => true,
                    ]);
                });
            }

            $tenantA->run(function (): void {
                $this->assertTrue(Product::where('sku', 'SKU-A')->exists());
                $this->assertFalse(Product::where('sku', 'SKU-B')->exists());
            });

            $tenantB->run(function (): void {
                $this->assertTrue(Product::where('sku', 'SKU-B')->exists());
                $this->assertFalse(Product::where('sku', 'SKU-A')->exists());
            });
        } finally {
            $tenantA->delete();
            $tenantB->delete();
        }
    }
}
