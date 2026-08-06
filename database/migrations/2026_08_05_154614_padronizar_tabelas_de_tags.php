<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Limpeza e padronização na tabela NOTICIAS
        Schema::table('noticias', function (Blueprint $table) {
            // Remove as colunas antigas e inúteis
            $table->dropColumn(['tag', 'data_publicacao']);
            
            // Remove a foreign key antiga e renomeia a coluna para o padrão
            $table->dropForeign('noticias_banner_tag_id_foreign');
            $table->renameColumn('banner_tag_id', 'tag_id');
            
            // Recria a foreign key correta
            $table->foreign('tag_id')->references('id')->on('tags')->nullOnDelete();
        });

        // 2. Padronização na tabela BANNERS
        Schema::table('banners', function (Blueprint $table) {
            // Remove a coluna de texto antiga
            $table->dropColumn('categoria_tag');
            
            // Cria o relacionamento real com a tabela de tags
            $table->foreignId('tag_id')->nullable()->after('descricao')->constrained('tags')->nullOnDelete();
        });
    }

    public function down(): void
    {
        // Reversão omitida por segurança em ambiente de refatoração estrutural
    }
};