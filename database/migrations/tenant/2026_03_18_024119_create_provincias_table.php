<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('provincias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Insertar provincias de Cuba
        DB::table('provincias')->insert([
            ['nombre' => 'Pinar del Río'],
            ['nombre' => 'Artemisa'],
            ['nombre' => 'La Habana'],
            ['nombre' => 'Mayabeque'],
            ['nombre' => 'Matanzas'],
            ['nombre' => 'Cienfuegos'],
            ['nombre' => 'Villa Clara'],
            ['nombre' => 'Sancti Spíritus'],
            ['nombre' => 'Ciego de Ávila'],
            ['nombre' => 'Camagüey'],
            ['nombre' => 'Las Tunas'],
            ['nombre' => 'Holguín'],
            ['nombre' => 'Granma'],
            ['nombre' => 'Santiago de Cuba'],
            ['nombre' => 'Guantánamo'],
            ['nombre' => 'Isla de la Juventud'],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('provincias');
    }
};