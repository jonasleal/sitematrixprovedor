<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;

class SgpService
{
    protected $url;
    protected $app;
    protected $token;

    public function __construct()
    {
        $this->url = rtrim(config('services.sgp.url'), '/');
        $this->app = config('services.sgp.app');
        $this->token = config('services.sgp.token');
    }

    public function getPlanos()
    {
        try {
            // 1. Puxa o JSON com Timeout de 10 segundos para não travar o site
            $responseUra = Http::timeout(10)->get($this->url . '/api/ura/consultaplano/', [
                'app' => $this->app,
                'token' => $this->token,
            ]);

            // Se o SGP retornar erro 400, 401, 404, 500, força a cair direto no CATCH
            $responseUra->throw();

            // Acessa a chave "planos"
            $planosUra = $responseUra->json()['planos'] ?? [];

            // 2. AUXILIAR (Pré-cadastro - POST): Puxa o array direto com Timeout
            $responsePre = Http::timeout(10)->asForm()->post($this->url . '/api/precadastro/plano/list/', [
                'app' => $this->app,
                'token' => $this->token,
            ]);
            
            $planosPre = [];
            if ($responsePre->successful()) {
                $planosPre = $responsePre->json();
            } else {
                // Falha silenciosa aceitável: Se o auxiliar falhar, avisamos no log, mas o site não cai.
                Log::warning("SgpService: API de Pré-cadastro falhou com status " . $responsePre->status());
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
                    'nome'       => $u['descricao'] ?? 'Plano Matrix',       // Chave exata da URA: "descricao" 
                    'valor'      => $u['preco'] ?? 0,                        // Chave exata da URA: "preco"
                    'download'   => $u['download'] ?? null,                  // Chave exata da URA: "download"
                    'upload'     => $u['upload'] ?? null,                    // Chave exata da URA: "upload"
                    'tipo'       => strtolower($dadosPre['tipo'] ?? 'internet'), // Chave exata do Pre: "tipo"
                    'observacao' => $dadosPre['observacao'] ?? '',           // Chave exata do Pre: "observacao"
                    'assinantes' => $u['qtd_servicos'] ?? 0,                 // Chave exata da URA: "qtd_servicos"
                ];
            }

            return $planosMesclados;

        } catch (RequestException $e) {
            // Captura erros específicos de lentidão (Timeout) ou Servidor Fora do Ar
            Log::error("SgpService Falha de Conexão (RequestException): " . $e->getMessage());
            return null; // Retorna NULL para ativar o sistema de Cache local do Laravel
        } catch (\Exception $e) {
            // Captura erros na montagem do JSON ou outras quebras internas
            Log::critical("SgpService Falha Grave no Motor Duplo: " . $e->getMessage());
            return null; // Retorna NULL para ativar o sistema de Cache local do Laravel
        }
    }

    /**
     * Busca OLTs, PONs e CTOs no SGP.
     * Monta o dicionário F/S/P e cruza os dados, devolvendo mastigado para o Mapa NOC.
     */
    /**
     * Busca OLTs, PONs e CTOs no SGP.
     * Retorna estrutura detalhada para Agrupamento no Frontend (OLT -> Slot -> PON).
     */
    public function getDadosMapaNoc()
    {
        try {
            $baseUrl = rtrim(env('SGP_API_URL'), '/');
            $user = (string) env('SGP_API_USER', '');
            $pass = (string) env('SGP_API_PASSWORD', '');

            $http = \Illuminate\Support\Facades\Http::withBasicAuth($user, $pass)
                        ->acceptJson()
                        ->timeout(15);

            $ponDict = [];
            
            // 1. Busca todas as OLTs
            $oltsResp = $http->get($baseUrl . '/api/fttx/olt/list/');
            
            if ($oltsResp->successful() && is_array($oltsResp->json())) {
                foreach ($oltsResp->json() as $olt) {
                    if (!isset($olt['id'])) continue;
                    
                    // 2. Busca as PONs
                    $ponsResp = $http->get($baseUrl . '/api/fttx/olt/' . $olt['id'] . '/pon/list/');
                    
                    if ($ponsResp->successful() && is_array($ponsResp->json())) {
                        foreach ($ponsResp->json() as $pon) {
                            $ponDict[$pon['id']] = [
                                'olt_name' => $pon['olt_name'] ?? 'OLT Padrão',
                                'slot' => $pon['slot'] ?? 0,
                                'pon' => $pon['pon'] ?? 0,
                                'description' => $pon['description'] ?? null
                            ];
                        }
                    }
                }
            }

            // 3. Busca TODAS as CTOs
            $ctosResp = $http->get($baseUrl . '/api/fttx/splitter/all/');
            
            $ctosFormatadas = [];
            $ponsAtivas = [];

            if ($ctosResp->successful() && is_array($ctosResp->json())) {
                foreach ($ctosResp->json() as $cto) {
                    if (!empty($cto['map_ll'])) {
                        $coords = array_map('trim', explode(',', $cto['map_ll']));
                        
                        if (count($coords) == 2 && is_numeric($coords[0]) && is_numeric($coords[1])) {
                            $ponId = $cto['pon_id'] ?? 0;
                            
                            $ctosFormatadas[] = [
                                'id' => $cto['id'] ?? 0,
                                'nome' => $cto['ident'] ?? 'CTO S/N',
                                'lat' => (float) $coords[0],
                                'lng' => (float) $coords[1],
                                'pon_id' => $ponId
                            ];

                            if (!isset($ponsAtivas[$ponId])) {
                                $ponsAtivas[$ponId] = [
                                    'id' => $ponId,
                                    'olt_name' => $ponDict[$ponId]['olt_name'] ?? 'S/N',
                                    'slot' => $ponDict[$ponId]['slot'] ?? 0,
                                    'pon' => $ponDict[$ponId]['pon'] ?? 0,
                                    'description' => $ponDict[$ponId]['description'] ?? null
                                ];
                            }
                        }
                    }
                }
            }

            return [
                'ctos' => $ctosFormatadas,
                'pons' => array_values($ponsAtivas)
            ];

        } catch (\Throwable $th) {
            throw new \Exception("Erro ao buscar dados do SGP: " . $th->getMessage());
        }
    }
}