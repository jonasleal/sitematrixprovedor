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
    Schema::table('noticias', function (Blueprint $table) {
        $table->foreignId('banner_tag_id')->nullable()->constrained('banner_tags')->nullOnDelete();
        $table->date('publicado_em')->nullable();
    });
}

public function down(): void
{
    Schema::table('noticias', function (Blueprint $table) {
        $table->dropForeign(['banner_tag_id']);
        $table->dropColumn(['banner_tag_id', 'publicado_em']);
    });
}
};
