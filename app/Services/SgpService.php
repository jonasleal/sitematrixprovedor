<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SgpService
{
    protected $url;
    protected $app;
    protected $token;

    public function __construct()
    {
        $this->url = rtrim(env('SGP_API_URL'), '/');
        $this->app = env('SGP_APP_NAME');
        $this->token =env('SGP_TOKEN');
    }

    public function getPlanos()
    {
        try {
            // 1. CHEFE (URA - GET): Puxa o JSON exato que você me enviou
            $responseUra = Http::get($this->url . '/api/ura/consultaplano/', [
                'app' => $this->app,
                'token' => $this->token,
            ]);
            // Acessa a chave "planos" conforme seu JSON da URA
            $planosUra = $responseUra->successful() ? ($responseUra->json()['planos'] ?? []) : [];

            // 2. AUXILIAR (Pré-cadastro - POST): Puxa o array direto
            $responsePre = Http::asForm()->post($this->url . '/api/precadastro/plano/list/', [
                'app' => $this->app,
                'token' => $this->token,
            ]);
            
            // O seu JSON provou que o retorno já é o array direto, sem envolver em outras chaves
            $planosPre = $responsePre->successful() ? $responsePre->json() : [];

            if (!$responsePre->successful()) {
                Log::warning("SgpService: Pré-cadastro falhou com status " . $responsePre->status());
            }

            // 3. Indexa o Auxiliar pelo "id" para cruzamento rápido
            $preIndexados = [];
            if (is_array($planosPre)) {
                foreach ($planosPre as $p) {
                    if (isset($p['id'])) {
                        $preIndexados[$p['id']] = $p;
                    }
                }
            }

            // 4. Mapeamento Estrito Baseado no seu JSON
            $planosMesclados = [];
            foreach ($planosUra as $u) {
                $id = $u['id'] ?? null; // Chave exata: "id"
                if (!$id) continue;

                $dadosPre = $preIndexados[$id] ?? [];

                $planosMesclados[] = [
                    'id'         => $id,
                    'nome'       => $u['descricao'] ?? 'Plano Matrix',       // Chave exata da URA: "descricao" (Ex: 350Mbs_Promocao)
                    'valor'      => $u['preco'] ?? 0,                        // Chave exata da URA: "preco"
                    'download'   => $u['download'] ?? null,                  // Chave exata da URA: "download"
                    'upload'     => $u['upload'] ?? null,                    // Chave exata da URA: "upload"
                    'tipo'       => strtolower($dadosPre['tipo'] ?? 'internet'), // Chave exata do Pre: "tipo"
                    'observacao' => $dadosPre['observacao'] ?? '',           // Chave exata do Pre: "observacao"
                    'assinantes' => $u['qtd_servicos'] ?? 0,                 // Chave exata da URA: "qtd_servicos"
                ];
            }

            return $planosMesclados;

        } catch (\Exception $e) {
            Log::error("SgpService Falha no Motor Duplo: " . $e->getMessage());
            return [];
        }
    }
}