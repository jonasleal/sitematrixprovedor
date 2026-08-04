<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Services\SgpService;
use App\Models\Campanha;
use App\Models\PlanoDetalhe;
use App\Models\Noticia;
use App\Models\Banner;

class HomeController extends Controller
{
    protected $sgpService;

    public function __construct(SgpService $sgpService)
    {
        $this->sgpService = $sgpService;
    }

    public function index()
    {
        $planosSgp = Cache::remember('planos_site_matrix', 3600, function () {
            return $this->sgpService->getPlanos();
        });

        $planosFinais = [];
        $indexDestaque = 0;

        if (!empty($planosSgp)) {
            // CORREÇÃO: Agora buscamos TODOS, ativos ou não!
            $detalhesLocais = PlanoDetalhe::all()->keyBy('sgp_plano_id');

            foreach ($planosSgp as $p_sgp) {
                // Filtra para exibir apenas planos de internet
                if (($p_sgp['tipo'] ?? 'internet') !== 'internet') continue;

                $id = $p_sgp['id'] ?? null;
                if (!$id) continue;

                $local = $detalhesLocais->get($id);

                // REGRA 1: Se o Admin desmarcou "Exibir no Site", oculta imediatamente!
                if ($local && !$local->ativo) {
                    continue; 
                }

                // REGRA 2: Verificação de Vencimento
                if ($local && $local->data_fim && now() > $local->data_fim) {
                    if ($local->ocultar_apos_vencimento) {
                        continue; // Oculta o plano pois passou da data
                    }
                }

                // Matemática de Preço e Desconto
                $valorOriginal = (float) ($p_sgp['valor'] ?? 0);
                $desconto = $local ? (float) $local->desconto : 0;
                $valorFinal = ($local && $local->preco_personalizado) ? $local->preco_personalizado : ($valorOriginal - $desconto);

                // Trata os benefícios
                $beneficios = [];
                if ($local && !empty($local->topicos_beneficios)) {
                    $beneficios = json_decode($local->topicos_beneficios, true) ?? [];
                } else {
                    $obs = trim($p_sgp['observacao'] ?? '');
                    $beneficios = !empty($obs) ? explode("\n", str_replace("\r", "", $obs)) : ['Consulte condições com nossos atendentes'];
                }

                $planosFinais[] = [
                    'id'              => $id,
                    'nome'            => ($local && $local->nome_personalizado) ? $local->nome_personalizado : $p_sgp['nome'],
                    'valor_original'  => $valorOriginal,
                    'valor_final'     => $valorFinal,
                    'tem_desconto'    => $desconto > 0 || ($local && $local->preco_personalizado && $local->preco_personalizado < $valorOriginal),
                    'velocidade_down' => $local->velocidade_down ?? $p_sgp['download'] ?? null,
                    'velocidade_up'   => $local->velocidade_up ?? $p_sgp['upload'] ?? null,
                    'beneficios'      => $beneficios,
                    'destaque'        => $local->destaque ?? false,
                    'ordem'           => $local->ordem ?? 999,
                    'data_fim'        => $local->data_fim ?? null // Passa a data para a tela
                ];
            }
        }

        // Ordena pela coluna "ordem"
        usort($planosFinais, function ($a, $b) {
            return $a['ordem'] <=> $b['ordem'];
        });

        foreach ($planosFinais as $index => $plano) {
            if ($plano['destaque']) {
                $indexDestaque = $index; break;
            }
        }

        if ($indexDestaque == 0 && count($planosFinais) > 0) {
            $indexDestaque = floor(count($planosFinais) / 2);
        }
		
        $campanhas = Campanha::where('ativo', true)->where('data_inicio', '<=', now())->where('data_fim', '>=', now())->get();

        // ==========================================
        // NOVAS BUSCAS (BANNERS E NOTÍCIAS)
        // ==========================================
        
        // Filtra banners ativos e que estão dentro do período de agendamento (se houver data cadastrada)
        $banners = Banner::where('ativo', true)
            ->where(function($q) {
                $q->whereNull('data_inicio')->orWhere('data_inicio', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('data_fim')->orWhere('data_fim', '>=', now());
            })
            ->orderBy('ordem', 'asc')
            ->get();

        // Traz apenas as 3 últimas notícias ativas (para não lotar a página inicial)
        $noticias = Noticia::where('ativo', true)->orderBy('created_at', 'desc')->take(3)->get();

        // Envia tudo para o HTML (welcome.blade.php)
        return view('welcome', [
            'planos' => $planosFinais, 
            'indexDestaque' => $indexDestaque, 
            'campanhas' => $campanhas,
            'banners' => $banners,    // <-- Enviando os Banners
            'noticias' => $noticias   // <-- Enviando as Notícias
        ]);
    }
    
	public function precadastro()
    {
        // TRUQUE DE MESTRE: Rodamos a mesma lógica da Home e pegamos os planos já processados
        // Isso garante que os Descontos, Nomes Personalizados e Ordenações sejam IDÊNTICOS!
        $dadosDaHome = $this->index()->getData();
        $planos = $dadosDaHome['planos'];

        return view('precadastro', compact('planos'));
    }
}