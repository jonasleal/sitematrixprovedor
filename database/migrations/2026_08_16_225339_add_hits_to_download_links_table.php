<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('download_links', function (Blueprint $table) {
            // Adiciona a coluna hits logo após a coluna 'link'
            $table->unsignedBigInteger('hits')->default(0)->after('link');
        });
    }

    public function down(): void
    {
        Schema::table('download_links', function (Blueprint $table) {
            $table->dropColumn('hits');
        });
    }
};