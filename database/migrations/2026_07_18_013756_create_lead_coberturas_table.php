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
        // Alterado de 'leads_cobertura' para 'lead_coberturas'
        Schema::create('lead_coberturas', function (Blueprint $table) {
            $table->id();
            $table->string('pronome', 10);
            $table->string('nome');
            $table->string('whatsapp', 20);
            $table->string('endereco_pesquisado')->nullable();
            $table->enum('status', ['Pendente', 'Contatado', 'Sem Viabilidade'])->default('Pendente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_coberturas');
    }
};
