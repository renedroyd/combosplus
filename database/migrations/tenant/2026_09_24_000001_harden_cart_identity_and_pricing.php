<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('cart_items', 'price')) {
            Schema::table('cart_items', function (Blueprint $table): void {
                $table->decimal('price', 10, 2)->after('quantity');
            });
        }

        $indexes = collect(Schema::getIndexes('carts'));

        if (! $indexes->contains(fn (array $index): bool =>
            ($index['unique'] ?? false) === true
            && ($index['columns'] ?? []) === ['user_id']
        )) {
            Schema::table('carts', function (Blueprint $table): void {
                $table->unique('user_id');
            });
        }
    }

    public function down(): void
    {
        $indexes = collect(Schema::getIndexes('carts'));

        if ($indexes->contains(fn (array $index): bool =>
            ($index['unique'] ?? false) === true
            && ($index['columns'] ?? []) === ['user_id']
        )) {
            Schema::table('carts', function (Blueprint $table): void {
                $table->dropUnique(['user_id']);
            });
        }

        if (Schema::hasColumn('cart_items', 'price')) {
            Schema::table('cart_items', function (Blueprint $table): void {
                $table->dropColumn('price');
            });
        }
    }
};
