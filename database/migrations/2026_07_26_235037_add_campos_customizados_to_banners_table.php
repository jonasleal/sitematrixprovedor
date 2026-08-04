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
        Schema::table('banners', function (Blueprint $table) {
            $table->string('categoria_tag')->default('PROMOÇÃO ESPECIAL')->after('titulo');
            $table->string('tema_cor')->default('green-cyan')->after('categoria_tag'); // green-cyan, pink-purple, orange-yellow
            $table->text('descricao')->nullable()->after('titulo');
            $table->string('texto_botao')->default('Saiba Mais')->after('link_destino');
            $table->string('posicao_imagem')->default('15% 50%')->after('caminho_imagem');
            $table->boolean('inverter_posicao')->default(false)->after('posicao_imagem');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['categoria_tag', 'tema_cor', 'descricao', 'texto_botao', 'posicao_imagem', 'inverter_posicao']);
        });
    }
};
