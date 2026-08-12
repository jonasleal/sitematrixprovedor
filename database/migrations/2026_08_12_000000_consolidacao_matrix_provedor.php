<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tags
        if (!Schema::hasTable('tags')) {
            Schema::create('tags', function (Blueprint $table) {
                $table->id();
                $table->string('nome');
                $table->string('slug')->unique();
                $table->timestamps();
            });
        }

        // 2. Banners
        if (!Schema::hasTable('banners')) {
            Schema::create('banners', function (Blueprint $table) {
                $table->id();
                $table->string('titulo')->nullable();
                $table->text('descricao')->nullable();
                $table->unsignedBigInteger('tag_id')->nullable();
                $table->string('tema_cor')->default('green-cyan');
                $table->string('caminho_imagem');
                $table->string('caminho_imagem_mobile')->nullable();
                $table->string('posicao_imagem')->default('15% 50%');
                $table->integer('posicao_x')->default(50);
                $table->integer('posicao_y')->default(50);
                $table->integer('proporcao_imagem')->default(50);
                $table->boolean('ativo')->default(true);
                $table->integer('ordem')->default(0);
                $table->timestamps();
            });
        }

        // 3. Banner Tags
        if (!Schema::hasTable('banner_tags')) {
            Schema::create('banner_tags', function (Blueprint $table) {
                $table->id();
                $table->string('nome');
                $table->string('slug')->unique();
                $table->timestamps();
            });
        }

        // 4. Campanhas
        if (!Schema::hasTable('campanhas')) {
            Schema::create('campanhas', function (Blueprint $table) {
                $table->id();
                $table->string('nome');
                $table->string('descricao')->nullable();
                $table->decimal('percentual_desconto', 5, 2)->nullable();
                $table->decimal('valor_desconto', 8, 2)->nullable();
                $table->dateTime('data_inicio');
                $table->dateTime('data_fim');
                $table->boolean('ativo')->default(true);
                $table->timestamps();
            });
        }

        // 5. Configurações Globais
        if (!Schema::hasTable('configuracaos')) {
            Schema::create('configuracaos', function (Blueprint $table) {
                $table->id();
                $table->string('chave')->unique();
                $table->text('valor')->nullable();
                $table->string('tipo')->default('string');
                $table->string('descricao')->nullable();
                $table->timestamps();
            });
        }

        // 6. Downloads
        if (!Schema::hasTable('downloads')) {
            Schema::create('downloads', function (Blueprint $table) {
                $table->id();
                $table->string('titulo');
                $table->text('descricao')->nullable();
                $table->string('arquivo_path');
                $table->string('imagem_path')->nullable();
                $table->string('tipo')->nullable();
                $table->unsignedBigInteger('tamanho')->nullable();
                $table->boolean('ativo')->default(true);
                $table->timestamps();
            });
        }

        // 7. Lead Coberturas (Corrigido)
        if (!Schema::hasTable('lead_coberturas')) {
            Schema::create('lead_coberturas', function (Blueprint $table) {
                $table->id();
                $table->string('pronome')->nullable();
                $table->string('nome');
                $table->string('whatsapp')->nullable();
                $table->string('celular')->nullable();
                $table->string('cpf')->nullable();
                $table->string('email')->nullable();
                $table->string('cep')->nullable();
                $table->string('logradouro')->nullable();
                $table->string('numero')->nullable();
                $table->string('bairro')->nullable();
                $table->string('cidade')->nullable();
                $table->string('estado')->nullable();
                $table->unsignedBigInteger('plano_id')->nullable();
                $table->string('status_integracao')->default('pendente');
                $table->text('mensagem_erro')->nullable();
                $table->timestamps();
            });
        }

        // 8. Noticias
        if (!Schema::hasTable('noticias')) {
            Schema::create('noticias', function (Blueprint $table) {
                $table->id();
                $table->string('titulo');
                $table->string('slug')->unique();
                $table->text('resumo')->nullable();
                $table->longText('conteudo');
                $table->string('imagem_destaque')->nullable();
                $table->dateTime('data_publicacao')->nullable();
                $table->unsignedBigInteger('tag_id')->nullable();
                $table->boolean('ativo')->default(true);
                $table->timestamps();
            });
        }

        // 9. Páginas
        if (!Schema::hasTable('paginas')) {
            Schema::create('paginas', function (Blueprint $table) {
                $table->id();
                $table->string('titulo');
                $table->string('slug')->unique();
                $table->longText('conteudo')->nullable();
                $table->boolean('ativa')->default(true);
                $table->timestamps();
            });
        }

        // 10. Plano Detalhes
        if (!Schema::hasTable('plano_detalhes')) {
            Schema::create('plano_detalhes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('plano_id')->unique();
                $table->boolean('destaque')->default(false);
                $table->boolean('visivel')->default(true);
                $table->integer('ordem')->default(0);
                $table->string('tag_texto')->nullable();
                $table->string('tag_cor')->nullable();
                $table->text('beneficios_extras')->nullable();
                $table->timestamps();
            });
        }

        // 11. Polígono Coberturas
        if (!Schema::hasTable('poligono_coberturas')) {
            Schema::create('poligono_coberturas', function (Blueprint $table) {
                $table->id();
                $table->string('nome');
                $table->json('coordenadas');
                $table->string('cor_preenchimento')->default('#22c55e');
                $table->boolean('ativo')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('poligono_coberturas');
        Schema::dropIfExists('plano_detalhes');
        Schema::dropIfExists('paginas');
        Schema::dropIfExists('noticias');
        Schema::dropIfExists('lead_coberturas');
        Schema::dropIfExists('downloads');
        Schema::dropIfExists('configuracaos');
        Schema::dropIfExists('campanhas');
        Schema::dropIfExists('banner_tags');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('tags');
    }
};
