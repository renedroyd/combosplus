<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('orders', 'payment_method_id')
            || ! Schema::hasColumn('orders', 'payment_method')) {
            return;
        }

        DB::table('orders')
            ->whereNull('payment_method_id')
            ->whereNotNull('payment_method')
            ->orderBy('id')
            ->eachById(function (object $order): void {
                $legacy = trim((string) $order->payment_method);

                if ($legacy === '') {
                    return;
                }

                $paymentMethodId = DB::table('payment_methods')
                    ->where('code', $legacy)
                    ->orWhere('name', $legacy)
                    ->value('id');

                if ($paymentMethodId !== null) {
                    DB::table('orders')
                        ->where('id', $order->id)
                        ->update(['payment_method_id' => $paymentMethodId]);
                }
            });
    }

    public function down(): void
    {
        // The legacy payment_method column remains available during the
        // compatibility window, so there is no destructive rollback work.
    }
};
