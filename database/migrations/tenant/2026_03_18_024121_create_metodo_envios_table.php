<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('metodo_envios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('descripcion');
            $table->decimal('costo_base', 8, 2);
            $table->integer('tiempo_estimado_horas');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        DB::table('metodo_envios')->insert([
            [
                'nombre' => 'Transferencia bancaria',
                'descripcion' => 'Depósito en cuenta bancaria cubana',
                'costo_base' => 8.00,
                'tiempo_estimado_horas' => 24
            ],
            [
                'nombre' => 'Efectivo a domicilio',
                'descripcion' => 'Entrega en efectivo en la puerta de su casa',
                'costo_base' => 10.00,
                'tiempo_estimado_horas' => 48
            ],
            [
                'nombre' => 'Tarjeta prepago',
                'descripcion' => 'Recarga a tarjeta magnética',
                'costo_base' => 3.00,
                'tiempo_estimado_horas' => 1
            ],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('metodo_envios');
    }
};