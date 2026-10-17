<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saas_subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('submitted_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('plan_price_id')->constrained('saas_plan_prices')->restrictOnDelete();
            $table->string('plan_name');
            $table->string('currency_code', 3);
            $table->string('billing_interval', 16);
            $table->decimal('amount', 12, 2);
            $table->string('payment_method', 80);
            $table->string('payment_reference', 120)->nullable();
            $table->string('receipt_path')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->string('idempotency_key', 64)->unique();
            $table->foreignId('reviewed_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->index(['tenant_id', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saas_subscription_payments');
    }
};
