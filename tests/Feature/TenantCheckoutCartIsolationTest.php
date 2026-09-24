<?php

namespace Tests\Feature;

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\PlatformUser;
use App\Models\Product;
use App\Models\Tenant;
use App\Services\Tenancy\TenantCustomerProvisioner;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TenantCheckoutCartIsolationTest extends TestCase
{
    private array $tenants = [];
    private array $platformUsers = [];

    protected function tearDown(): void
    {
        Auth::guard('web')->logout();

        foreach ($this->tenants as $tenant) {
            $tenant->delete();
        }

        foreach ($this->platformUsers as $user) {
            $user->delete();
        }

        parent::tearDown();
    }

    public function test_cart_mutations_reject_another_users_cart_item(): void
    {
        $tenant = $this->tenant('cart-user-isolation');
        [$owner, $other] = $this->platformUsers('cart-owner', 'cart-other');

        $tenant->run(function () use ($owner, $other): void {
            app(TenantCustomerProvisioner::class)->ensure($owner);
            app(TenantCustomerProvisioner::class)->ensure($other);

            $product = Product::create([
                'name' => 'Isolation Product',
                'slug' => 'isolation-product',
                'description' => 'Cart isolation test',
                'price' => 10,
                'quantity' => 10,
                'sku' => 'CART-ISO-001',
            ]);

            $otherCart = Cart::create(['platform_user_id' => $other->id]);
            $item = $otherCart->items()->create([
                'product_id' => $product->id,
                'quantity' => 2,
                'price' => $product->price,
            ]);

            Auth::guard('web')->setUser($owner);

            $request = Request::create('/cart/item/'.$item->id, 'PATCH', ['quantity' => 9]);

            try {
                app(CartController::class)->update($request, $item);
                $this->fail('A cart mutation for another user must be rejected.');
            } catch (HttpException $exception) {
                $this->assertSame(403, $exception->getStatusCode());
            }

            $this->assertSame(2, $item->refresh()->quantity);
        });
    }

    public function test_cart_data_isolated_between_tenants_even_when_customer_identity_is_shared(): void
    {
        $tenantA = $this->tenant('cart-tenant-a');
        $tenantB = $this->tenant('cart-tenant-b');
        [$customer] = $this->platformUsers('shared-cart-customer');

        $tenantA->run(function () use ($customer): void {
            app(TenantCustomerProvisioner::class)->ensure($customer);
            Cart::create(['platform_user_id' => $customer->id]);
        });

        $tenantB->run(function () use ($customer): void {
            app(TenantCustomerProvisioner::class)->ensure($customer);
            $this->assertNull(Cart::where('platform_user_id', $customer->id)->first());
        });
    }

    public function test_checkout_rejects_address_owned_by_another_user(): void
    {
        $tenant = $this->tenant('checkout-address-isolation');
        [$owner, $other] = $this->platformUsers('checkout-owner', 'checkout-other');

        $tenant->run(function () use ($owner, $other): void {
            app(TenantCustomerProvisioner::class)->ensure($owner);
            app(TenantCustomerProvisioner::class)->ensure($other);

            $product = Product::create([
                'name' => 'Checkout Product',
                'slug' => 'checkout-product',
                'description' => 'Checkout isolation test',
                'price' => 25,
                'quantity' => 10,
                'sku' => 'CHECKOUT-ISO-001',
            ]);

            Cart::create(['platform_user_id' => $owner->id])
                ->items()
                ->create([
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'price' => $product->price,
                ]);

            $foreignAddress = Address::create([
                'platform_user_id' => $other->id,
                'type' => 'shipping',
                'name' => 'Other Customer',
                'address_line1' => 'Foreign street 1',
                'city' => 'Santiago',
                'state' => 'RM',
                'postal_code' => '00000',
                'country' => 'Chile',
            ]);

            $paymentMethod = PaymentMethod::create([
                'name' => 'Efectivo',
                'code' => 'cash-isolation-test',
                'is_active' => true,
                'sort_order' => 1,
            ]);

            Auth::guard('web')->setUser($owner);

            $request = Request::create('/checkout', 'POST', [
                'delivery_type' => 'delivery',
                'address_id' => $foreignAddress->id,
                'payment_method_id' => $paymentMethod->id,
            ]);

            $response = app(CheckoutController::class)->process($request);

            $this->assertSame(302, $response->getStatusCode());
            $this->assertSame(0, Order::count());
            $this->assertSame(1, Cart::where('platform_user_id', $owner->id)->first()->items()->count());
        });
    }

    public function test_checkout_rejects_inactive_payment_method_without_creating_order(): void
    {
        $tenant = $this->tenant('checkout-payment-isolation');
        [$owner] = $this->platformUsers('checkout-payment-owner');

        $tenant->run(function () use ($owner): void {
            app(TenantCustomerProvisioner::class)->ensure($owner);

            $product = Product::create([
                'name' => 'Payment Product',
                'slug' => 'payment-product',
                'description' => 'Payment isolation test',
                'price' => 30,
                'quantity' => 10,
                'sku' => 'PAYMENT-ISO-001',
            ]);

            Cart::create(['platform_user_id' => $owner->id])
                ->items()
                ->create([
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'price' => $product->price,
                ]);

            $paymentMethod = PaymentMethod::create([
                'name' => 'Inactive',
                'code' => 'inactive-isolation-test',
                'is_active' => false,
                'sort_order' => 1,
            ]);

            Auth::guard('web')->setUser($owner);

            $request = Request::create('/checkout', 'POST', [
                'delivery_type' => 'pickup',
                'payment_method_id' => $paymentMethod->id,
            ]);

            $response = app(CheckoutController::class)->process($request);

            $this->assertSame(302, $response->getStatusCode());
            $this->assertSame(0, Order::count());
            $this->assertSame(1, Cart::where('platform_user_id', $owner->id)->first()->items()->count());
        });
    }

    public function test_tenant_schema_keeps_cart_and_payment_method_constraints_required_by_checkout(): void
    {
        $tenant = $this->tenant('checkout-schema');

        $tenant->run(function (): void {
            $this->assertTrue(Schema::hasColumn('cart_items', 'price'));
            $this->assertTrue(Schema::hasColumn('carts', 'platform_user_id'));
            $this->assertTrue(Schema::hasColumn('addresses', 'platform_user_id'));
            $this->assertTrue(Schema::hasColumn('orders', 'platform_user_id'));
            $this->assertTrue(Schema::hasColumn('payment_methods', 'is_active'));
            $this->assertTrue(Schema::hasColumn('payment_methods', 'sort_order'));

            $indexes = collect(Schema::getIndexes('carts'));

            $this->assertTrue(
                $indexes->contains(fn (array $index): bool =>
                    ($index['unique'] ?? false) === true
                    && ($index['columns'] ?? []) === ['platform_user_id']
                )
            );
        });
    }

    private function tenant(string $id): Tenant
    {
        $tenant = Tenant::create(['id' => $id.'-'.bin2hex(random_bytes(4))]);
        $this->tenants[] = $tenant;

        return $tenant;
    }

    private function platformUsers(string ...$prefixes): array
    {
        return array_map(function (string $prefix): PlatformUser {
            $user = PlatformUser::create([
                'name' => str_replace('-', ' ', $prefix),
                'email' => $prefix.'-'.bin2hex(random_bytes(4)).'@example.test',
                'password' => 'password',
            ]);

            $this->platformUsers[] = $user;

            return $user;
        }, $prefixes);
    }
}
