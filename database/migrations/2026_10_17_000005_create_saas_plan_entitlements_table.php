<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saas_plan_entitlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('saas_plans')->cascadeOnDelete();
            $table->string('key', 100);
            $table->string('value', 255);
            $table->string('description')->nullable();
            $table->timestamps();

            $table->unique(['plan_id', 'key'], 'saas_plan_entitlement_unique');
            $table->index('key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saas_plan_entitlements');
    }
};
