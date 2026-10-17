<?php

namespace Tests\Feature;

use App\Models\SaasCurrency;
use App\Models\SaasPlan;
use App\Models\SaasPlanPrice;
use Tests\TestCase;

class SaasPlanPricingFoundationTest extends TestCase
{
    public function test_saas_billing_models_use_central_connection_and_expected_tables(): void
    {
        $this->assertSame('saas_currencies', (new SaasCurrency())->getTable());
        $this->assertSame('saas_plans', (new SaasPlan())->getTable());
        $this->assertSame('saas_plan_prices', (new SaasPlanPrice())->getTable());

        $centralConnection = config('tenancy.database.central_connection');

        $this->assertSame($centralConnection, (new SaasCurrency())->getConnectionName());
        $this->assertSame($centralConnection, (new SaasPlan())->getConnectionName());
        $this->assertSame($centralConnection, (new SaasPlanPrice())->getConnectionName());
    }

    public function test_plans_and_currencies_expose_price_relationships(): void
    {
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, (new SaasPlan())->prices());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, (new SaasCurrency())->prices());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, (new SaasPlanPrice())->plan());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, (new SaasPlanPrice())->currency());
    }
}
