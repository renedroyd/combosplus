<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\SaasCurrency;
use App\Models\SaasPlan;
use App\Models\SaasPlanPrice;
use App\Models\Tenant;
use App\Services\SaasSubscriptionLifecycleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ManualSubscriptionProvisioningTest extends TestCase
{
    // Suppress tenant lifecycle events so tests do not create tenant databases
    // or execute DDL inside RefreshDatabase's transaction.
    use RefreshDatabase;

    public function test_platform_admin_can_manually_provision_subscription_with_audit_reason(): void
    {
        $this->travelTo(now()->startOfSecond());

        $tenant = Tenant::withoutEvents(fn () => Tenant::create(['id' => 'manual-subscription-tenant']));
        $admin = Admin::create([
            'name' => 'Platform Admin',
            'email' => 'manual-subscription-admin@example.test',
            'password' => 'password',
        ]);
        $plan = SaasPlan::create([
            'name' => 'Inicial',
            'slug' => 'inicial-manual-test',
            'is_active' => true,
            'sort_order' => 1,
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
            'amount' => 10,
            'is_active' => true,
        ]);

        $subscription = app(SaasSubscriptionLifecycleService::class)->createManually(
            $tenant->id,
            $price->id,
            $admin,
            'Prueba administrativa de 14 días',
            14,
        );

        $this->assertSame($tenant->id, $subscription->tenant_id);
        $this->assertSame($plan->id, $subscription->plan_id);
        $this->assertSame($admin->id, $subscription->created_by_admin_id);
        $this->assertSame('Prueba administrativa de 14 días', $subscription->creation_reason);
        $this->assertSame('active', $subscription->status);
        $this->assertTrue($subscription->starts_at->equalTo(now()));
        $this->assertTrue($subscription->ends_at->equalTo(now()->copy()->addDays(14)));
        $this->assertNull($subscription->latest_payment_id);
    }

    public function test_manual_provisioning_rejects_inactive_price(): void
    {
        $tenant = Tenant::withoutEvents(fn () => Tenant::create(['id' => 'manual-subscription-inactive']));
        $admin = Admin::create([
            'name' => 'Platform Admin',
            'email' => 'manual-subscription-inactive@example.test',
            'password' => 'password',
        ]);
        $plan = SaasPlan::create([
            'name' => 'Inicial',
            'slug' => 'inicial-manual-inactive',
            'is_active' => true,
            'sort_order' => 1,
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
            'amount' => 10,
            'is_active' => false,
        ]);

        try {
            app(SaasSubscriptionLifecycleService::class)->createManually(
                $tenant->id,
                $price->id,
                $admin,
                'Prueba de validación',
            );
            $this->fail('Una tarifa inactiva no debe ser asignable.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('plan_price_id', $exception->errors());
        }
    }
}
