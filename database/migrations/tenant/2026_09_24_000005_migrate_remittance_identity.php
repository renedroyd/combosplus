<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('remesas', 'platform_user_id')) {
            Schema::table('remesas', function (Blueprint $table): void {
                $table->unsignedBigInteger('platform_user_id')->nullable()->index();
            });
        }

        if (Schema::hasColumn('remesas', 'user_id')) {
            DB::table('remesas')
                ->whereNull('platform_user_id')
                ->whereNotNull('user_id')
                ->update(['platform_user_id' => DB::raw('user_id')]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('remesas', 'platform_user_id')) {
            Schema::table('remesas', function (Blueprint $table): void {
                $table->dropColumn('platform_user_id');
            });
        }
    }
};
