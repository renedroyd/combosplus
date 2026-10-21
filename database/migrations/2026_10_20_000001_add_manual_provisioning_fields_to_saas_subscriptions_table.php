<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('saas_subscriptions', function (Blueprint $table) {
            $table->foreignId('created_by_admin_id')
                ->nullable()
                ->after('latest_payment_id')
                ->constrained('admins')
                ->nullOnDelete();
            $table->text('creation_reason')->nullable()->after('canceled_at');
        });
    }

    public function down(): void
    {
        Schema::table('saas_subscriptions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by_admin_id');
            $table->dropColumn('creation_reason');
        });
    }
};
