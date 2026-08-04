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
        Schema::create('configuracoes', function (Blueprint $table) {
            $table->id();
            
            // Contatos e Suporte
            $table->string('whatsapp')->nullable();
            $table->string('telefone_principal')->nullable();
            $table->string('telefone_0800')->nullable();
            $table->string('email_contato')->nullable();

            // Plataformas e Apps
            $table->string('link_site_principal')->nullable();
            $table->string('link_central_assinante')->nullable();
            $table->string('link_app_android')->nullable();
            $table->string('link_app_ios')->nullable();

            // Redes Sociais Completas
            $table->string('link_instagram')->nullable();
            $table->string('link_facebook')->nullable();
            $table->string('link_linkedin')->nullable();
            $table->string('link_twitter_x')->nullable();
            $table->string('link_youtube')->nullable();
            $table->string('link_tiktok')->nullable();

            // Dados Institucionais
            $table->text('texto_sobre_nos')->nullable();
            $table->string('endereco_fisico')->nullable();
            $table->string('horario_atendimento')->nullable();
            $table->string('cnpj')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('configuracoes'); // Corrigido aqui!
    }
};
