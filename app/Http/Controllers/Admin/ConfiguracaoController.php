<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Configuracao;
use App\Models\Download;
use Illuminate\Support\Facades\Cache;

class ConfiguracaoController extends Controller
{
    public function index()
    {
        // Pega a primeira configuração ou cria uma em branco se não existir
        $config = Configuracao::first() ?? new Configuracao();

        // Busca todos os downloads/PDFs ativos cadastrados no sistema
        $downloads = Download::where('ativo', true)->orderBy('titulo', 'asc')->get();

        return view('admin.configuracoes.index', compact('config', 'downloads'));
    }

    public function store(Request $request)
    {
        $config = Configuracao::first();
        if (!$config) {
            $config = new Configuracao();
        }

        $dados = $request->all();

        // Garantia de integridade: Se o select do contrato for enviado em branco (""),
        // converte para null para não violar a FK (Foreign Key) do banco de dados.
        if (array_key_exists('contrato_download_id', $dados) && empty($dados['contrato_download_id'])) {
            $dados['contrato_download_id'] = null;
        }

        // Salva tudo que veio do formulário
        $config->fill($dados);
        $config->save();

        // Apaga o cache antigo para o site atualizar na mesma hora
        Cache::forget('config_site_array_v1');

        return redirect()->back()->with('success', 'Configurações globais salvas com sucesso!');
    }
}