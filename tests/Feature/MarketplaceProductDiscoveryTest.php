<?php

namespace Tests\Feature;

use App\Http\Controllers\MarketplaceController;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Vite;
use Tests\TestCase;

class MarketplaceProductDiscoveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_marketplace_product_search_only_returns_visible_products_from_published_tenants(): void
    {
        $matching = $this->createPublishedTenant('Tecnología Plus', 'tecnologia-plus', 'Tecnología.');
        $draft = $this->createTenant('Borrador', 'borrador', 'Tecnología.', 'draft');

        try {
            $matching->run(function (): void {
                $category = Category::create([
                    'name' => 'Tecnología',
                    'slug' => 'tecnologia',
                    'is_visible' => true,
                    'sort_order' => 0,
                ]);

                Product::create([
                    'category_id' => $category->getKey(),
                    'name' => 'Laptop Pro',
                    'slug' => 'laptop-pro',
                    'price' => 900,
                    'quantity' => 2,
                    'sku' => 'LAP-001',
                    'description' => 'Laptop profesional.',
                    'is_visible' => true,
                ]);

                Product::create([
                    'category_id' => $category->getKey(),
                    'name' => 'Laptop oculta',
                    'slug' => 'laptop-oculta',
                    'price' => 500,
                    'quantity' => 1,
                    'sku' => 'LAP-002',
                    'description' => 'No debe aparecer.',
                    'is_visible' => false,
                ]);
            });

            $draft->run(function (): void {
                Product::create([
                    'name' => 'Laptop borrador',
                    'slug' => 'laptop-borrador',
                    'price' => 400,
                    'quantity' => 1,
                    'sku' => 'LAP-003',
                    'description' => 'No debe aparecer.',
                    'is_visible' => true,
                ]);
            });

            $view = app(MarketplaceController::class)->products(
                Request::create('/productos', 'GET', ['q' => 'laptop'])
            );

            $products = $view->getData()['products'];

            $this->assertCount(1, $products);
            $this->assertSame('Laptop Pro', $products->first()->name);
            $this->assertSame($matching->getKey(), $products->first()->marketplace_tenant_id);
            $this->assertSame('tecnologia-plus', $products->first()->marketplace_tenant_slug);
        } finally {
            $matching->delete();
            $draft->delete();
        }
    }

    public function test_marketplace_product_category_filter_and_price_sorting_work(): void
    {
        $tenant = $this->createPublishedTenant('Moda Plus', 'moda-plus', 'Moda.');

        try {
            $tenant->run(function (): void {
                $category = Category::create([
                    'name' => 'Calzado',
                    'slug' => 'calzado',
                    'is_visible' => true,
                    'sort_order' => 0,
                ]);

                Product::create([
                    'category_id' => $category->getKey(),
                    'name' => 'Zapato premium',
                    'slug' => 'zapato-premium',
                    'price' => 120,
                    'quantity' => 1,
                    'sku' => 'ZAP-001',
                    'description' => 'Calzado premium.',
                    'is_visible' => true,
                ]);

                Product::create([
                    'category_id' => $category->getKey(),
                    'name' => 'Zapato básico',
                    'slug' => 'zapato-basico',
                    'price' => 40,
                    'quantity' => 1,
                    'sku' => 'ZAP-002',
                    'description' => 'Calzado básico.',
                    'is_visible' => true,
                ]);
            });

            $view = app(MarketplaceController::class)->products(
                Request::create('/productos', 'GET', ['category' => 'calzado', 'sort' => 'price_low'])
            );

            $products = $view->getData()['products'];

            $this->assertCount(2, $products);
            $this->assertSame('Zapato básico', $products->first()->name);
        } finally {
            $tenant->delete();
        }
    }

    public function test_homepage_exposes_registered_store_public_slug(): void
    {
        $tenant = $this->createTenant('Nexo Test Store', 'nexo-test-store', 'Tienda de prueba.', 'draft');

        try {
            $view = app(MarketplaceController::class)->home();
            $data = $view->getData();

            $this->assertTrue(
                $data['stores']->contains(fn (Tenant $store): bool => $store->slug === 'nexo-test-store')
            );
            $this->assertCount(0, $data['featuredProducts']);
            $this->assertCount(0, $data['categories']);
        } finally {
            $tenant->delete();
        }
    }

    public function test_homepage_renders_prominent_discovery_and_seller_sections(): void
    {
        Vite::shouldReceive('__invoke')->andReturn('');

        $view = app(MarketplaceController::class)->home();
        $html = $view->render();

        $this->assertStringContainsString('Descubre tu próximo', $html);
        $this->assertStringContainsString('Productos que te pueden encantar', $html);
        $this->assertStringContainsString('Haz que más personas descubran tu negocio', $html);
        $this->assertStringContainsString('Vender en Nexo', $html);
        $this->assertStringContainsString('images.unsplash.com', $html);
        $this->assertStringContainsString('alt="Tecnología y dispositivos electrónicos"', $html);
        $this->assertStringContainsString('loading="lazy"', $html);
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
