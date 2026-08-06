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
        Schema::create('paginas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('slug')->unique(); // URL amigável (ex: /sobre-nos)
            $table->longText('conteudo')->nullable(); // Onde vai o HTML do TinyMCE
            $table->string('template')->default('padrao'); // Para páginas dinâmicas no futuro
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paginas');
    }
};
