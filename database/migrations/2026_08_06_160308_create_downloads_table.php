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
        Schema::create('downloads', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->string('categoria'); // 'aplicativo', 'contrato', 'manual', 'outros'
            $table->string('tipo_link')->default('upload'); // 'upload' ou 'externo' (ex: link Play Store)
            $table->string('arquivo_path')->nullable(); // Caminho do ficheiro no Storage ou URL externa
            $table->string('versao')->nullable(); // Ex: v1.2.0
            $table->integer('ordem')->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('downloads');
    }
};
