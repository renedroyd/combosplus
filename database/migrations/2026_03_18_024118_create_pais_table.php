<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pais', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Insertar provincias de Cuba
        DB::table('pais')->insert([
            ['nombre' => 'Cuba'],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('pais');
    }
};