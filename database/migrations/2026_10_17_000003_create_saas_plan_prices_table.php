<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saas_plan_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('saas_plans')->cascadeOnDelete();
            $table->foreignId('currency_id')->constrained('saas_currencies')->restrictOnDelete();
            $table->string('billing_interval', 16);
            $table->decimal('amount', 12, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['plan_id', 'currency_id', 'billing_interval'], 'saas_plan_price_period_unique');
            $table->index(['currency_id', 'billing_interval', 'is_active'], 'saas_plan_price_lookup_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saas_plan_prices');
    }
};
