<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ej: "Efectivo", "Tarjeta de crédito", etc.
            $table->string('code')->unique(); // identificador para lógica interna
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable(); // configuraciones adicionales (API keys, etc.)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
