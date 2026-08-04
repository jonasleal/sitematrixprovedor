<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Services\SgpService;
use App\Models\PlanoDetalhe; 

class PlanoDetalheController extends Controller
{
    protected $sgpService;

    public function __construct(SgpService $sgpService)
    {
        $this->sgpService = $sgpService;
    }

    public function index()
    {
        // 1. Busca do Service de forma direta!
        $planosSgp = $this->sgpService->getPlanos();

        // 2. Busca personalizações salvas
        $detalhesLocais = PlanoDetalhe::get()->keyBy('sgp_plano_id');

        // 3. Monta a lista combinada
        $planosCompletos = [];
        foreach ($planosSgp as $p) {
            $sgpId = $p['id'] ?? $p['idplano'] ?? null;
            if ($sgpId) {
                $planosCompletos[] = [
                    'sgp_id'      => $sgpId,
                    'nome_sgp'    => $p['nome'] ?? $p['descricao'] ?? $p['nomeplano'] ?? 'Sem nome',
                    'preco_sgp'   => $p['valor'] ?? $p['preco'] ?? $p['valormensalidade'] ?? 0,
                    'personalizacao' => $detalhesLocais->get($sgpId)
                ];
            }
        }

        return view('admin.planos.index', compact('planosCompletos'));
    }

    public function store(Request $request)
    {
        $beneficiosArray = [];
        if ($request->filled('beneficios_texto')) {
            $linhas = explode("\n", str_replace("\r", "", $request->beneficios_texto));
            foreach ($linhas as $linha) {
                if (!empty(trim($linha))) $beneficiosArray[] = trim($linha);
            }
        }

        if ($request->has('destaque') && $request->destaque) {
            PlanoDetalhe::where('sgp_plano_id', '!=', $request->sgp_plano_id)->update(['destaque' => false]);
        }

        PlanoDetalhe::updateOrCreate(
            ['sgp_plano_id' => $request->sgp_plano_id],
            [
                'nome_personalizado'  => $request->nome_personalizado,
                'preco_personalizado' => $request->preco_personalizado,
                'velocidade_down'     => $request->velocidade_down, // Novo
                'velocidade_up'       => $request->velocidade_up,   // Novo
                'desconto'            => $request->desconto ?? 0,   // Novo
                'ordem'               => $request->ordem ?? 999,    // Novo
                'topicos_beneficios'  => !empty($beneficiosArray) ? json_encode($beneficiosArray) : null,
                'destaque'            => $request->has('destaque'),
                'ativo'               => $request->has('ativo'),
                'ocultar_apos_vencimento' => $request->has('ocultar_apos_vencimento'), // Novo
                'data_inicio'         => $request->data_inicio,
                'data_fim'            => $request->data_fim,
            ]
        );

        Cache::forget('planos_site_matrix');
        return redirect()->back()->with('success', 'Plano atualizado!');
    }
}