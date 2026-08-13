<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('configuracoes', function (Blueprint $table) {
            $table->string('google_analytics_id')->nullable()->after('whatsapp_sucesso');
            $table->string('meta_pixel_id')->nullable()->after('google_analytics_id');
        });
    }

    public function down()
    {
        Schema::table('configuracoes', function (Blueprint $table) {
            $table->dropColumn(['google_analytics_id', 'meta_pixel_id']);
        });
    }
};