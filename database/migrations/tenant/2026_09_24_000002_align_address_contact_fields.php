<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('addresses', 'alias')) {
            Schema::table('addresses', function (Blueprint $table): void {
                $table->string('alias')->nullable()->after('user_id');
            });
        }

        if (! Schema::hasColumn('addresses', 'phone')) {
            Schema::table('addresses', function (Blueprint $table): void {
                $table->string('phone', 20)->nullable()->after('country');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('addresses', 'phone')) {
            Schema::table('addresses', function (Blueprint $table): void {
                $table->dropColumn('phone');
            });
        }

        if (Schema::hasColumn('addresses', 'alias')) {
            Schema::table('addresses', function (Blueprint $table): void {
                $table->dropColumn('alias');
            });
        }
    }
};
