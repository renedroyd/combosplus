<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_reviews', function (Blueprint $table): void {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedTinyInteger('rating');
            $table->string('title')->nullable();
            $table->text('comment')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->boolean('verified_purchase')->default(false);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'rating']);
            $table->unique(['tenant_id', 'user_id']);
        });

        Schema::create('product_reviews', function (Blueprint $table): void {
            $table->id();
            $table->string('tenant_id');
            $table->unsignedBigInteger('product_id');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedTinyInteger('rating');
            $table->string('title')->nullable();
            $table->text('comment')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->boolean('verified_purchase')->default(false);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->index(['tenant_id', 'product_id', 'status']);
            $table->index(['tenant_id', 'rating']);
            $table->unique(['tenant_id', 'product_id', 'user_id']);
        });

        Schema::create('platform_reviews', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->string('title')->nullable();
            $table->text('comment')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_reviews');
        Schema::dropIfExists('product_reviews');
        Schema::dropIfExists('store_reviews');
    }
};