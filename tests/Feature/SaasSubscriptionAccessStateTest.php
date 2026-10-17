<?php

namespace Tests\Feature;

use App\Models\SaasCurrency;
use App\Models\SaasPlan;
use App\Models\SaasPlanEntitlement;
use App\Models\SaasPlanPrice;
use App\Models\SaasSubscription;
use App\Models\Tenant;
use App\Services\SaasPlanEntitlementService;
use App\Services\SaasTenantAccessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class SaasSubscriptionAccessStateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-10-17 12:00:00'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_unexpired_subscription_allows_commercial_access(): void
    {
        [$tenant] = $this->createSubscription('saas-active-state', now()->addDay());

        try {
            $access = app(SaasTenantAccessService::class);

            $this->assertSame(SaasTenantAccessService::STATE_ACTIVE, $access->state($tenant->id));
            $this->assertTrue($access->canUseCommercialFeatures($tenant->id));
        } finally {
            $tenant->delete();
        }
    }

    public function test_grace_period_preserves_the_expired_plan_entitlements(): void
    {
        [$tenant, $plan] = $this->createSubscription('saas-grace-state', now()->subDays(3));

        try {
            SaasPlanEntitlement::create([
                'plan_id' => $plan->id,
                'key' => 'max_products',
                'value' => '100',
                'description' => 'Maximum product count.',
            ]);

            $access = app(SaasTenantAccessService::class);
            $entitlements = app(SaasPlanEntitlementService::class);

            $this->assertSame(SaasTenantAccessService::STATE_GRACE, $access->state($tenant->id));
            $this->assertTrue($access->canUseCommercialFeatures($tenant->id));
            $this->assertSame(100, $entitlements->limit($tenant->id, 'max_products', 0));
        } finally {
            $tenant->delete();
        }
    }

    public function test_subscription_is_restricted_after_the_seven_day_grace_period(): void
    {
        [$tenant] = $this->createSubscription('saas-restricted-state', now()->subDays(7)->subSecond());

        try {
            $access = app(SaasTenantAccessService::class);

            $this->assertSame(SaasTenantAccessService::STATE_RESTRICTED, $access->state($tenant->id));
            $this->assertFalse($access->canUseCommercialFeatures($tenant->id));
        } finally {
            $tenant->delete();
        }
    }

    /**
     * @return array{Tenant, SaasPlan, SaasSubscription}
     */
    private function createSubscription(string $tenantId, Carbon $endsAt): array
    {
        $tenant = Tenant::create(['id' => $tenantId]);
        $plan = SaasPlan::create([
            'name' => 'Inicial',
            'slug' => 'inicial',
            'is_active' => true,
            'sort_order' => 0,
        ]);
        $currency = SaasCurrency::create([
            'code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
            'decimal_places' => 2,
            'is_active' => true,
        ]);
        $price = SaasPlanPrice::create([
            'plan_id' => $plan->id,
            'currency_id' => $currency->id,
            'billing_interval' => 'monthly',
            'amount' => '10.00',
            'is_active' => true,
        ]);
        $subscription = SaasSubscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'plan_price_id' => $price->id,
            'status' => SaasSubscription::STATUS_ACTIVE,
            'starts_at' => now()->subMonth(),
            'ends_at' => $endsAt,
        ]);

        return [$tenant, $plan, $subscription];
    }
}
