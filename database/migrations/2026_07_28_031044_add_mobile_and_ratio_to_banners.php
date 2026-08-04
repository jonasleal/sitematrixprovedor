<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->integer('proporcao_imagem')->default(50)->after('posicao_y');
            $table->string('caminho_imagem_mobile')->nullable()->after('caminho_imagem');
        });
    }

    public function down()
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['proporcao_imagem', 'caminho_imagem_mobile']);
        });
    }
};