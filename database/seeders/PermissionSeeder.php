<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Limpa o cache de permissões antes de rodar
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Matriz Granular de Módulos e Ações
        $modulos = [
            'banners'       => ['ver', 'criar', 'editar', 'excluir'],
            'noticias'      => ['ver', 'criar', 'editar', 'excluir'],
            'planos'        => ['ver', 'criar', 'editar', 'excluir'],
            'leads'         => ['ver', 'editar', 'excluir'], // Leads não são criados pelo admin
            'downloads'     => ['ver', 'criar', 'editar', 'excluir'],
            'paginas'       => ['ver', 'criar', 'editar', 'excluir'],
            'cobertura'     => ['ver', 'criar', 'editar', 'excluir'],
            'configuracoes' => ['ver', 'editar'], // Configurações são fixas, não se exclui
            'equipa'        => ['ver', 'criar', 'editar', 'excluir'], // Gestão de utilizadores
        ];

        // 2. Injeta as permissões cirurgicamente no banco
        foreach ($modulos as $modulo => $acoes) {
            foreach ($acoes as $acao) {
                Permission::firstOrCreate(['name' => "{$acao} {$modulo}", 'guard_name' => 'web']);
            }
        }

        // 3. Cria os "Templates de Cargo" para facilitar o seu clique na UI futuramente
        $marketing = Role::firstOrCreate(['name' => 'Marketing', 'guard_name' => 'web']);
        $marketing->givePermissionTo([
            'ver banners', 'criar banners', 'editar banners', 'excluir banners',
            'ver noticias', 'criar noticias', 'editar noticias', 'excluir noticias',
            'ver paginas', 'criar paginas', 'editar paginas', 'excluir paginas',
        ]);

        $atendimento = Role::firstOrCreate(['name' => 'Atendimento', 'guard_name' => 'web']);
        $atendimento->givePermissionTo([
            'ver leads', 'editar leads',
            'ver planos',
            'ver downloads',
            'ver cobertura'
        ]);
    }
}