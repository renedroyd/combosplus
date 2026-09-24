<?php

namespace Tests\Feature;

use App\Models\Tenant;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TenantCartSchemaTest extends TestCase
{
    public function test_tenant_cart_schema_supports_price_snapshots_and_one_cart_per_customer(): void
    {
        $tenant = Tenant::create(['id' => 'cart-schema']);

        try {
            $tenant->run(function (): void {
                $this->assertTrue(Schema::hasColumn('cart_items', 'price'));

                $indexes = collect(Schema::getIndexes('carts'));

                $this->assertTrue(
                    $indexes->contains(fn (array $index): bool =>
                        ($index['unique'] ?? false) === true
                        && $index['columns'] === ['platform_user_id']
                    )
                );
            });
        } finally {
            $tenant->delete();
        }
    }
}
