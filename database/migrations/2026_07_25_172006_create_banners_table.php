<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
	public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('titulo')->nullable(); // Apenas para controle interno e acessibilidade (alt)
            $table->string('caminho_imagem'); // Onde a imagem será salva no servidor
            $table->string('link_destino')->nullable(); // Se o cliente clicar, vai para onde?
            $table->boolean('ativo')->default(true); // Botão de liga/desliga o banner
            $table->integer('ordem')->default(0); // Para você escolher qual aparece primeiro
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
