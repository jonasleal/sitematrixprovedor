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
        Schema::create('download_links', function (Blueprint $table) {
            $table->id();
            // Chave estrangeira ligando ao aplicativo pai (se o app for apagado, os links somem junto)
            $table->foreignId('download_id')->constrained('downloads')->onDelete('cascade');
            
            // Ex: 'android', 'ios', 'windows', 'tizen', 'webos'
            $table->string('plataforma'); 
            
            // O link real da loja ou arquivo
            $table->string('link', 500); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('download_links');
    }
};
