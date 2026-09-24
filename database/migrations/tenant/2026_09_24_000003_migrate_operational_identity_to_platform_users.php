<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['carts', 'addresses', 'orders'] as $table) {
            if (! Schema::hasColumn($table, 'platform_user_id')) {
                Schema::table($table, function (Blueprint $blueprint): void {
                    $blueprint->unsignedBigInteger('platform_user_id')->nullable()->index();
                });
            }

            if (Schema::hasColumn($table, 'user_id')) {
                DB::table($table)
                    ->whereNull('platform_user_id')
                    ->whereNotNull('user_id')
                    ->update(['platform_user_id' => DB::raw('user_id')]);
            }
        }

        if (Schema::hasColumn('carts', 'platform_user_id')) {
            $indexes = collect(Schema::getIndexes('carts'));
            if (! $indexes->contains(fn (array $index): bool =>
                ($index['unique'] ?? false) === true
                && ($index['columns'] ?? []) === ['platform_user_id']
            )) {
                Schema::table('carts', function (Blueprint $table): void {
                    $table->unique('platform_user_id');
                });
            }
        }
    }

    public function down(): void
    {
        $indexes = collect(Schema::getIndexes('carts'));
        if ($indexes->contains(fn (array $index): bool =>
            ($index['unique'] ?? false) === true
            && ($index['columns'] ?? []) === ['platform_user_id']
        )) {
            Schema::table('carts', function (Blueprint $table): void {
                $table->dropUnique(['platform_user_id']);
            });
        }

        foreach (['orders', 'addresses', 'carts'] as $table) {
            if (Schema::hasColumn($table, 'platform_user_id')) {
                Schema::table($table, function (Blueprint $blueprint): void {
                    $blueprint->dropColumn('platform_user_id');
                });
            }
        }
    }
};
