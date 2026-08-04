<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('banners', function (Blueprint $table) {
            // Eixos de Posicionamento e Zoom
            $table->integer('posicao_x')->default(50)->after('posicao_imagem');
            $table->integer('posicao_y')->default(50)->after('posicao_x');
            $table->integer('zoom')->default(100)->after('posicao_y');
            
            // Agendamento por datas
            $table->dateTime('data_inicio')->nullable()->after('ativo');
            $table->dateTime('data_fim')->nullable()->after('data_inicio');
        });
    }

    public function down()
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['posicao_x', 'posicao_y', 'zoom', 'data_inicio', 'data_fim']);
        });
    }
};