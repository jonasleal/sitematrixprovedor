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
        Schema::create('poligonos_cobertura', function (Blueprint $table) {
            $table->id();
            $table->string('nome'); // Ex: "Cobertura Magano CTO-01 a 10"
            $table->string('cor', 20)->default('#81c700'); // Cor da mancha no mapa
            $table->json('coordenadas'); // Vai guardar o array de latitudes e longitudes do desenho
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poligono_coberturas');
    }
};
