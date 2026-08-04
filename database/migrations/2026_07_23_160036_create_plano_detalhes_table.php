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
        Schema::create('plano_detalhes', function (Blueprint $table) {
            $table->id();
            $table->integer('sgp_plano_id')->unique();
            $table->string('nome_personalizado')->nullable();
            $table->decimal('preco_personalizado', 8, 2)->nullable();
            
            // Novos campos:
            $table->string('velocidade_down')->nullable();
            $table->string('velocidade_up')->nullable();
            $table->decimal('desconto', 8, 2)->default(0);
            $table->integer('ordem')->default(999);
            $table->boolean('ocultar_apos_vencimento')->default(true); // Oculta o plano se passar da data
            
            $table->json('topicos_beneficios')->nullable();
            $table->boolean('destaque')->default(false);
            $table->boolean('ativo')->default(true);
            $table->dateTime('data_inicio')->nullable();
            $table->dateTime('data_fim')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plano_detalhes');
    }
};
