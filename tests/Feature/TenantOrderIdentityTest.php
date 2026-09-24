<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PlatformUser;
use App\Models\Tenant;
use App\Services\Tenancy\TenantCustomerProvisioner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantOrderIdentityTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_number_is_generated_without_legacy_uniqid(): void
    {
        $tenant = Tenant::create(['id' => 'order-number']);
        $platformUser = PlatformUser::create([
            'name' => 'Order Customer',
            'email' => 'order-number@example.test',
            'password' => 'password',
        ]);

        try {
            $tenant->run(function () use ($platformUser): void {
                app(TenantCustomerProvisioner::class)->ensure($platformUser);

                $order = Order::create([
                    'platform_user_id' => $platformUser->id,
                    'subtotal' => 10,
                    'total' => 10,
                ]);

                $this->assertMatchesRegularExpression('/^ORD-[A-Z0-9]{12}$/', $order->order_number);
                $this->assertSame($platformUser->id, $order->platform_user_id);
                $this->assertSame($platformUser->id, $order->user->id);
            });
        } finally {
            $tenant->delete();
            $platformUser->delete();
        }
    }

    public function test_orders_keep_customer_ownership_inside_the_current_tenant(): void
    {
        $tenantA = Tenant::create(['id' => 'order-owner-a']);
        $tenantB = Tenant::create(['id' => 'order-owner-b']);
        $platformUser = PlatformUser::create([
            'name' => 'Shared Identity',
            'email' => 'order-owner@example.test',
            'password' => 'password',
        ]);

        try {
            $tenantA->run(fn () => app(TenantCustomerProvisioner::class)->ensure($platformUser));
            $tenantB->run(fn () => app(TenantCustomerProvisioner::class)->ensure($platformUser));

            $orderA = $tenantA->run(fn () => Order::create([
                'user_id' => $platformUser->id,
                'subtotal' => 20,
                'total' => 20,
            ]));

            $this->assertNull($tenantB->run(
                fn () => Order::query()->find($orderA->id)
            ));

            $this->assertNotNull($tenantA->run(
                fn () => Order::query()->find($orderA->id)
            ));
        } finally {
            $tenantA->delete();
            $tenantB->delete();
            $platformUser->delete();
        }
    }
}
