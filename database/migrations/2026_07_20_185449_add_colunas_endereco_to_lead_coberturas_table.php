<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lead_coberturas', function (Blueprint $table) {
            $table->string('rua')->nullable()->after('whatsapp');
            $table->string('bairro')->nullable()->after('rua');
            $table->string('cidade')->nullable()->after('bairro');
            $table->string('estado')->nullable()->after('cidade');
            $table->string('cep')->nullable()->after('estado');
            $table->decimal('lat', 10, 7)->nullable()->after('cep');
            $table->decimal('lng', 10, 7)->nullable()->after('lat');
        });
    }

    public function down(): void
    {
        Schema::table('lead_coberturas', function (Blueprint $table) {
            $table->dropColumn(['rua', 'bairro', 'cidade', 'estado', 'cep', 'lat', 'lng']);
        });
    }
};