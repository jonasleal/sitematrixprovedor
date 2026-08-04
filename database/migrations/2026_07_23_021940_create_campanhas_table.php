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
    Schema::create('campanhas', function (Blueprint $table) {
        $table->id();
        $table->string('tag')->default('Novidade'); // Ex: "Promoção", "Sorteio"
        $table->string('titulo');
        $table->text('descricao');
        $table->string('texto_botao')->default('Saiba Mais');
        $table->string('link'); // Para onde o botão leva (ex: /noticias/regulamento)
        
        $table->string('imagem_desktop')->nullable(); // Imagem formato deitado (PC)
        $table->string('imagem_mobile')->nullable();  // Imagem formato quadrado (Celular)
        
        $table->dateTime('data_inicio');
        $table->dateTime('data_fim');
        $table->boolean('ativo')->default(true); // Botão de pânico para desligar a campanha
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campanhas');
    }
};
