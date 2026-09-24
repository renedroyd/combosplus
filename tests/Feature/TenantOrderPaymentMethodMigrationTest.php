<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TenantOrderPaymentMethodMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_order_payment_method_is_backfilled_by_code_or_name(): void
    {
        $tenant = Tenant::create(['id' => 'order-payment-method']);

        try {
            $tenant->run(function (): void {
                $paymentMethod = PaymentMethod::create([
                    'name' => 'Transferencia',
                    'code' => 'transfer',
                    'is_active' => true,
                ]);

                $order = Order::create([
                    'payment_method' => 'transfer',
                    'subtotal' => 10,
                    'total' => 10,
                ]);

                DB::table('orders')->where('id', $order->id)->update([
                    'payment_method_id' => null,
                ]);

                Artisan::call('migrate', [
                    '--database' => 'tenant',
                    '--path' => 'database/migrations/tenant/2026_09_24_000004_backfill_order_payment_method_id.php',
                    '--realpath' => false,
                    '--force' => true,
                ]);

                $this->assertSame(
                    $paymentMethod->id,
                    DB::table('orders')->where('id', $order->id)->value('payment_method_id')
                );
            });
        } finally {
            $tenant->delete();
        }
    }

    public function test_unmatched_legacy_payment_method_is_left_untouched_for_compatibility(): void
    {
        $tenant = Tenant::create(['id' => 'order-payment-legacy']);

        try {
            $tenant->run(function (): void {
                $order = Order::create([
                    'payment_method' => 'legacy-method',
                    'subtotal' => 10,
                    'total' => 10,
                ]);

                Artisan::call('migrate', [
                    '--database' => 'tenant',
                    '--path' => 'database/migrations/tenant/2026_09_24_000004_backfill_order_payment_method_id.php',
                    '--realpath' => false,
                    '--force' => true,
                ]);

                $this->assertSame(
                    'legacy-method',
                    DB::table('orders')->where('id', $order->id)->value('payment_method')
                );
                $this->assertNull(
                    DB::table('orders')->where('id', $order->id)->value('payment_method_id')
                );
            });
        } finally {
            $tenant->delete();
        }
    }
}
