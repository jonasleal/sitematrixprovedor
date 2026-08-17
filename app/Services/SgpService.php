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
        $this->url = rtrim(config('services.sgp.url', env('SGP_API_URL')), '/');
        $this->app = config('services.sgp.app', env('SGP_APP'));
        $this->token = config('services.sgp.token', env('SGP_APP_TOKEN'));
    }

    /**
     * MOTOR HTTP CENTRALIZADO (ZERO GAMBIARRAS)
     * Injeta automaticamente o Basic Auth (Login e Senha do SGP) e as configurações de segurança
     * em TODAS as requisições que saírem deste Service.
     */
    private function httpClient($timeout = 15)
    {
        // Força a leitura como string para evitar erros de tipagem
        $user = (string) env('SGP_API_USER', '');
        $pass = (string) env('SGP_API_PASSWORD', '');

        return Http::withBasicAuth($user, $pass)
                   ->acceptJson()
                   ->timeout($timeout);
    }

    public function getPlanos()
    {
        try {
            // Usa o novo motor HTTP com 10 segundos de timeout
            $http = $this->httpClient(10);

            // 1. Puxa o JSON da URA
            $responseUra = $http->get($this->url . '/api/ura/consultaplano/', [
                'app' => $this->app,
                'token' => $this->token,
            ]);

            // Se o SGP retornar erro 400, 401, 403, 404, 500, força a cair direto no CATCH
            $responseUra->throw();

            // Acessa a chave "planos"
            $planosUra = $responseUra->json()['planos'] ?? [];

            // 2. AUXILIAR (Pré-cadastro - POST)
            $responsePre = $http->asForm()->post($this->url . '/api/precadastro/plano/list/', [
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
                $id = $u['id'] ?? null;
                if (!$id) continue;

                $dadosPre = $preIndexados[$id] ?? [];

                $planosMesclados[] = [
                    'id'         => $id,
                    'nome'       => $u['descricao'] ?? 'Plano Matrix',
                    'valor'      => $u['preco'] ?? 0,
                    'download'   => $u['download'] ?? null,
                    'upload'     => $u['upload'] ?? null,
                    'tipo'       => strtolower($dadosPre['tipo'] ?? 'internet'),
                    'observacao' => $dadosPre['observacao'] ?? '',
                    'assinantes' => $u['qtd_servicos'] ?? 0,
                ];
            }

            return $planosMesclados;

        } catch (RequestException $e) {
            // Captura erros específicos (Timeout ou 403 Forbidden)
            Log::error("SgpService Falha de Conexão (RequestException): " . $e->getMessage());
            return null; // Retorna NULL para ativar o sistema de Cache local do Laravel
        } catch (\Exception $e) {
            // Captura erros na montagem do JSON
            Log::critical("SgpService Falha Grave no Motor Duplo: " . $e->getMessage());
            return null; 
        }
    }

    /**
     * Busca OLTs, PONs e CTOs no SGP.
     * Retorna estrutura detalhada para Agrupamento no Frontend (OLT -> Slot -> PON).
     */
    public function getDadosMapaNoc()
    {
        try {
            // Usa o novo motor HTTP com 15 segundos para aguentar carga pesada
            $http = $this->httpClient(15);
            $ponDict = [];
            
            // 1. Busca todas as OLTs
            $oltsResp = $http->get($this->url . '/api/fttx/olt/list/');
            
            if ($oltsResp->successful() && is_array($oltsResp->json())) {
                foreach ($oltsResp->json() as $olt) {
                    if (!isset($olt['id'])) continue;
                    
                    // 2. Busca as PONs
                    $ponsResp = $http->get($this->url . '/api/fttx/olt/' . $olt['id'] . '/pon/list/');
                    
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
            $ctosResp = $http->get($this->url . '/api/fttx/splitter/all/');
            
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
            throw new \Exception("Erro ao buscar dados do SGP no Mapa NOC: " . $th->getMessage());
        }
    }
}