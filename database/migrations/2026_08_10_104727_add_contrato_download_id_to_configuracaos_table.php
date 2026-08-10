<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Alterado de 'configuracaos' para 'configuracoes'
        Schema::table('configuracoes', function (Blueprint $table) {
            $table->foreignId('contrato_download_id')
                  ->nullable()
                  ->constrained('downloads')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        // Alterado de 'configuracaos' para 'configuracoes'
        Schema::table('configuracoes', function (Blueprint $table) {
            $table->dropForeign(['contrato_download_id']);
            $table->dropColumn('contrato_download_id');
        });
    }
};