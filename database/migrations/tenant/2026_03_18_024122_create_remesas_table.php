<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('remesas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('remitente_nombre');
            $table->string('remitente_email');
            $table->string('remitente_telefono');
            $table->string('destinatario_nombre');
            $table->string('destinatario_ci', 11);
            $table->string('destinatario_telefono', 20);
            $table->text('destinatario_direccion');
            $table->foreignId('municipio_id')->constrained();
            $table->decimal('monto', 10, 2);
            $table->decimal('monto_recibir', 10, 2);
            $table->foreignId('metodo_envio_id')->constrained();
            $table->enum('moneda_origen', ['USD', 'EUR'])->default('USD');
            $table->enum('estado', ['pendiente', 'pagado', 'procesando', 'enviado', 'entregado', 'cancelado'])->default('pendiente');
            $table->foreignId('user_id')->nullable()->constrained();
            $table->string('comprobante_pago')->nullable();
            $table->timestamp('pagado_en')->nullable();
            $table->timestamp('entregado_en')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('remesas');
    }
};