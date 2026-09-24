<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Order;
use App\Models\PlatformUser;
use App\Models\Tenant;
use App\Services\Tenancy\TenantCustomerProvisioner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantOrderAddressRelationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_addresses_resolve_against_the_tenant_foreign_keys(): void
    {
        $tenant = Tenant::create(['id' => 'order-addresses']);
        $platformUser = PlatformUser::create([
            'name' => 'Address Customer',
            'email' => 'order-addresses@example.test',
            'password' => 'password',
        ]);

        try {
            $tenant->run(function () use ($platformUser): void {
                app(TenantCustomerProvisioner::class)->ensure($platformUser);

                $shipping = Address::create([
                    'platform_user_id' => $platformUser->id,
                    'type' => 'shipping',
                    'name' => 'Shipping Address',
                    'address_line1' => '123 Main Street',
                    'city' => 'Havana',
                    'state' => 'La Habana',
                    'postal_code' => '10100',
                    'country' => 'Cuba',
                ]);

                $billing = Address::create([
                    'platform_user_id' => $platformUser->id,
                    'type' => 'billing',
                    'name' => 'Billing Address',
                    'address_line1' => '456 Second Street',
                    'city' => 'Havana',
                    'state' => 'La Habana',
                    'postal_code' => '10101',
                    'country' => 'Cuba',
                ]);

                $order = Order::create([
                    'platform_user_id' => $platformUser->id,
                    'shipping_address_id' => $shipping->id,
                    'billing_address_id' => $billing->id,
                    'subtotal' => 20,
                    'total' => 20,
                ]);

                $this->assertSame($shipping->id, $order->shippingAddress->id);
                $this->assertSame($billing->id, $order->billingAddress->id);
                $this->assertSame($shipping->id, $order->address->id);
            });
        } finally {
            $tenant->delete();
            $platformUser->delete();
        }
    }
}
