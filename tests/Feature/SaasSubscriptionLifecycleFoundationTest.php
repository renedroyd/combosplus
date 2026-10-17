<?php

namespace Tests\Feature;

use App\Models\SaasPlanEntitlement;
use App\Models\SaasSubscription;
use App\Services\SaasPlanEntitlementService;
use Tests\TestCase;

class SaasSubscriptionLifecycleFoundationTest extends TestCase
{
    public function test_subscription_and_entitlement_models_use_central_connection(): void
    {
        $centralConnection = config('tenancy.database.central_connection');

        $this->assertSame('saas_subscriptions', (new SaasSubscription())->getTable());
        $this->assertSame('saas_plan_entitlements', (new SaasPlanEntitlement())->getTable());
        $this->assertSame($centralConnection, (new SaasSubscription())->getConnectionName());
        $this->assertSame($centralConnection, (new SaasPlanEntitlement())->getConnectionName());
    }

    public function test_entitlements_interpret_numeric_unlimited_and_boolean_values(): void
    {
        $limit = new SaasPlanEntitlement(['key' => 'max_products', 'value' => '250']);
        $unlimited = new SaasPlanEntitlement(['key' => 'max_products', 'value' => 'unlimited']);
        $feature = new SaasPlanEntitlement(['key' => 'custom_domain', 'value' => 'true']);
        $disabled = new SaasPlanEntitlement(['key' => 'advanced_reports', 'value' => 'false']);

        $this->assertSame(250, $limit->numericLimit());
        $this->assertNull($unlimited->numericLimit());
        $this->assertTrue($feature->booleanValue());
        $this->assertFalse($disabled->booleanValue());
    }

    public function test_subscription_current_scope_and_relationships_are_available(): void
    {
        $subscription = new SaasSubscription();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $subscription->plan());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $subscription->planPrice());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $subscription->latestPayment());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $subscription->newQuery()->current());
        $this->assertInstanceOf(SaasPlanEntitlementService::class, app(SaasPlanEntitlementService::class));
    }
}
