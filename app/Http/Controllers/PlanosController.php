<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PlanosController extends Controller
{
    public function index()
    {
        $planosParaSite = Cache::remember('planos_site_matrix', 3600, function () {
            $url = 'https://matrix.sgp.tsmx.app/api/ura/consultaplano/';
            try {
                $response = Http::get($url, [
                    'app' => 'SiteMatrix',
                    'token' => '5f510256-6c73-44fe-8af3-889536242230'
                ]);

                if ($response->successful()) {
                    $dados = $response->json();
                    $todosPlanos = $dados['planos'] ?? [];

                    $planosFiltrados = array_filter($todosPlanos, function ($plano) {
                        return $plano['preco'] > 0;
                    });

                    return array_values($planosFiltrados); // Reindexa o array
                }
                return [];
            } catch (\Exception $e) {
                return [];
            }
        });

        // 1. Simulação da variável que virá do seu painel de Admin
        // Coloque um ID válido aqui (ex: 5) para testar a flag, ou deixe null para forçar o cálculo automático
        $planoDestaqueId = null; 
        $indexDestaque = 0; // Por padrão, o primeiro

        if (count($planosParaSite) > 0) {
            $achouDestaque = false;
            
            // 2. Procura se o plano definido no Admin existe na lista retornada do SGP
            foreach ($planosParaSite as $index => $plano) {
                if ($plano['id'] == $planoDestaqueId) {
                    $indexDestaque = $index;
                    $achouDestaque = true;
                    break;
                }
            }

            // 3. Se não tem flag no admin, ou o SGP não retornou o plano da flag, pega matematicamente o plano do meio
            if (!$achouDestaque) {
                $indexDestaque = floor(count($planosParaSite) / 2);
            }
        }

        return view('welcome', [
            'planos' => $planosParaSite,
            'indexDestaque' => $indexDestaque
        ]);
    }
}