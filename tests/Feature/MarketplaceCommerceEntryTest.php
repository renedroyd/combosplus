<?php

namespace Tests\Feature;

use App\Http\Controllers\MarketplaceController;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MarketplaceCommerceEntryTest extends TestCase
{
    use RefreshDatabase;

    public function test_marketplace_product_can_enter_the_tenant_store_checkout_flow(): void
    {
        $tenant = Tenant::create([
            'id' => 'commerce-'.bin2hex(random_bytes(8)),
        ]);

        try {
            DB::table('tenants')
                ->where('id', $tenant->getTenantKey())
                ->update([
                    'name' => 'Tienda Commerce',
                    'slug' => 'tienda-commerce',
                    'status' => 'active',
                    'catalog_status' => 'published',
                ]);

            $tenant->refresh();
            $tenant->domains()->create(['domain' => 'tienda-commerce.localhost']);

            $product = $tenant->run(
                fn () => Product::create([
                    'name' => 'Producto Commerce',
                    'slug' => 'producto-commerce',
                    'description' => 'Producto publicado.',
                    'price' => 25,
                    'quantity' => 5,
                    'sku' => 'COM-001',
                    'is_visible' => true,
                ])
            );

            $request = Request::create('/tiendas/tienda-commerce/productos/'.$product->getKey().'/comprar', 'GET');

            $response = app(MarketplaceController::class)->buy($request, $tenant, (string) $product->getKey());

            $this->assertSame(
                'http://tienda-commerce.localhost/productos/'.$product->getKey(),
                $response->getTargetUrl(),
            );
        } finally {
            $tenant->delete();
        }
    }

    public function test_marketplace_cannot_start_commerce_for_unpublished_catalog(): void
    {
        $tenant = Tenant::create([
            'id' => 'commerce-private-'.bin2hex(random_bytes(8)),
        ]);

        try {
            DB::table('tenants')
                ->where('id', $tenant->getTenantKey())
                ->update([
                    'name' => 'Tienda privada',
                    'slug' => 'tienda-privada',
                    'status' => 'active',
                    'catalog_status' => 'unpublished',
                ]);

            $tenant->refresh();
            $tenant->domains()->create(['domain' => 'tienda-privada.localhost']);

            $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

            $request = Request::create('/tiendas/tienda-privada/productos/1/comprar', 'GET');
            app(MarketplaceController::class)->buy($request, $tenant, '1');
        } finally {
            $tenant->delete();
        }
    }
}
