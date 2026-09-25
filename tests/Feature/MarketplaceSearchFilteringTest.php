<?php

namespace Tests\Feature;

use App\Http\Controllers\MarketplaceController;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MarketplaceSearchFilteringTest extends TestCase
{
    use RefreshDatabase;

    public function test_marketplace_store_search_matches_description_and_only_returns_published_stores(): void
    {
        $matching = $this->createPublishedTenant('Café Central', 'cafe-central', 'Café artesanal y repostería local.');
        $other = $this->createPublishedTenant('Tienda Norte', 'tienda-norte', 'Artículos para el hogar.');
        $draft = $this->createTenant('Tienda Borrador', 'tienda-borrador', 'Café artesanal', 'draft');

        try {
            $view = app(MarketplaceController::class)->stores(
                Request::create('/tiendas', 'GET', ['q' => 'artesanal'])
            );

            $stores = $view->getData()['stores'];

            $this->assertTrue($stores->contains(fn (Tenant $tenant) => $tenant->getKey() === $matching->getKey()));
            $this->assertFalse($stores->contains(fn (Tenant $tenant) => $tenant->getKey() === $other->getKey()));
            $this->assertFalse($stores->contains(fn (Tenant $tenant) => $tenant->getKey() === $draft->getKey()));
        } finally {
            $matching->delete();
            $other->delete();
            $draft->delete();
        }
    }

    public function test_marketplace_store_category_filter_matches_visible_products_only(): void
    {
        $matching = $this->createPublishedTenant('Tecnología Plus', 'tecnologia-plus', 'Tecnología para tu negocio.');
        $other = $this->createPublishedTenant('Moda Plus', 'moda-plus', 'Moda y accesorios.');

        try {
            $matching->run(function (): void {
                $category = Category::create([
                    'name' => 'Electrónica',
                    'slug' => 'electronica',
                    'is_visible' => true,
                    'sort_order' => 0,
                ]);

                Product::create([
                    'category_id' => $category->getKey(),
                    'name' => 'Laptop',
                    'slug' => 'laptop',
                    'price' => 100,
                    'quantity' => 1,
                    'sku' => 'LAP-001',
                    'is_visible' => true,
                ]);
            });

            $other->run(function (): void {
                $category = Category::create([
                    'name' => 'Electrónica',
                    'slug' => 'electronica',
                    'is_visible' => true,
                    'sort_order' => 0,
                ]);

                Product::create([
                    'category_id' => $category->getKey(),
                    'name' => 'Oculto',
                    'slug' => 'oculto',
                    'price' => 100,
                    'quantity' => 1,
                    'sku' => 'OCU-001',
                    'is_visible' => false,
                ]);
            });

            $view = app(MarketplaceController::class)->stores(
                Request::create('/tiendas', 'GET', ['category' => 'electrónica'])
            );

            $stores = $view->getData()['stores'];

            $this->assertTrue($stores->contains(fn (Tenant $tenant) => $tenant->getKey() === $matching->getKey()));
            $this->assertFalse($stores->contains(fn (Tenant $tenant) => $tenant->getKey() === $other->getKey()));
        } finally {
            $matching->delete();
            $other->delete();
        }
    }

    public function test_marketplace_store_sorting_supports_newest_and_name(): void
    {
        $this->createPublishedTenant('Zeta Store', 'zeta-store', 'Zeta.');
        $this->createPublishedTenant('Alpha Store', 'alpha-store', 'Alpha.');

        $nameView = app(MarketplaceController::class)->stores(
            Request::create('/tiendas', 'GET', ['sort' => 'name'])
        );

        $this->assertSame('Alpha Store', $nameView->getData()['stores']->first()->name);

        $newestView = app(MarketplaceController::class)->stores(
            Request::create('/tiendas', 'GET', ['sort' => 'newest'])
        );

        $this->assertNotEmpty($newestView->getData()['stores']);
    }

    private function createPublishedTenant(string $name, string $slug, string $description): Tenant
    {
        return $this->createTenant($name, $slug, $description, 'published');
    }

    private function createTenant(string $name, string $slug, string $description, string $catalogStatus): Tenant
    {
        $tenant = Tenant::create(['id' => bin2hex(random_bytes(8))]);

        DB::table('tenants')
            ->where('id', $tenant->getTenantKey())
            ->update([
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
                'status' => 'active',
                'catalog_status' => $catalogStatus,
            ]);

        $tenant->refresh();

        return $tenant;
    }
}
