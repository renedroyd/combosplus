<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saas_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('plan_id')->constrained('saas_plans')->restrictOnDelete();
            $table->foreignId('plan_price_id')->constrained('saas_plan_prices')->restrictOnDelete();
            $table->foreignId('latest_payment_id')->nullable()->constrained('saas_subscription_payments')->nullOnDelete();
            $table->string('status', 20)->default('active')->index();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->index();
            $table->timestamp('next_billing_at')->nullable()->index();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->index(['tenant_id', 'status', 'starts_at'], 'saas_subscription_tenant_status_idx');
            $table->index(['status', 'ends_at'], 'saas_subscription_expiry_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saas_subscriptions');
    }
};
