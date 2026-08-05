<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('noticias', function (Blueprint $table) {
            $table->dateTime('data_publicacao')->nullable()->after('imagem_destaque');
            $table->string('tag')->nullable()->default('INFORMATIVO')->after('data_publicacao');
        });
    }

    public function down(): void
    {
        Schema::table('noticias', function (Blueprint $table) {
            $table->dropColumn(['data_publicacao', 'tag']);
        });
    }
};