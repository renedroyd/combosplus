<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['carts', 'addresses', 'orders', 'remesas'] as $tableName) {
            if (! Schema::hasColumn($tableName, 'user_id')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table): void {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }

        if (Schema::hasColumn('orders', 'payment_method')) {
            Schema::table('orders', function (Blueprint $table): void {
                $table->dropColumn('payment_method');
            });
        }
    }

    public function down(): void
    {
        foreach (['carts', 'addresses', 'orders', 'remesas'] as $tableName) {
            if (Schema::hasColumn($tableName, 'user_id')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table): void {
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('orders', 'payment_method')) {
            Schema::table('orders', function (Blueprint $table): void {
                $table->string('payment_method')->nullable();
            });
        }
    }
};
