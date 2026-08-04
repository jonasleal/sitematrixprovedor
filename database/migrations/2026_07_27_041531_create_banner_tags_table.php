<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('banner_tags', function (Blueprint $table) {$table->id();
            $table->string('nome')->unique();$table->timestamps();
        });

        // Insere tags iniciais padrão
        DB::table('banner_tags')->insert([
            ['nome' => 'PROMOÇÃO ESPECIAL', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'NOVIDADE LOCAL', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'AVISO DE REDE', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'DESTAQUE MATRIX', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('banner_tags');
    }
};