<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lead_coberturas', function (Blueprint $table) {
            // 1. Adiciona a coluna de observações (se não existir)
            if (!Schema::hasColumn('lead_coberturas', 'observacoes')) {
                $table->text('observacoes')->nullable()->after('status');
            }

            // 2. Remove o lixo do pré-cadastro antigo com segurança
            $colunasLixo = [
                'cpf', 'celular', 'email', 'rua', 'cep', 
                'plano_id', 'logradouro', 'numero', 'status_integracao'
            ];

            foreach ($colunasLixo as $coluna) {
                if (Schema::hasColumn('lead_coberturas', $coluna)) {
                    $table->dropColumn($coluna);
                }
            }
        });

        // 3. Liberta o campo "status" das correntes do ENUM (Transforma em VARCHAR)
        DB::statement("ALTER TABLE lead_coberturas MODIFY status VARCHAR(255) DEFAULT 'Demanda Reprimida'");
        
        // 4. Migração de Dados (Preservando o Histórico)
        // Converte os pendentes antigos para a nova nomenclatura de CRM
        DB::table('lead_coberturas')->where('status', 'Pendente')->update(['status' => 'Demanda Reprimida']);
    }

    public function down(): void
    {
        Schema::table('lead_coberturas', function (Blueprint $table) {
            if (Schema::hasColumn('lead_coberturas', 'observacoes')) {
                $table->dropColumn('observacoes');
            }
            // Não vamos recriar o lixo no método down para manter o banco limpo
        });
    }
};