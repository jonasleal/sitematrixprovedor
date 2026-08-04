<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Configuracao;
use Illuminate\Support\Facades\Cache;

class ConfiguracaoController extends Controller
{
    public function index()
    {
        // Pega a primeira configuração ou cria uma em branco se não existir
        $config = Configuracao::first() ?? new Configuracao();
        return view('admin.configuracoes.index', compact('config'));
    }

    public function store(Request $request)
    {
        $config = Configuracao::first();
        if (!$config) {
            $config = new Configuracao();
        }

        // Salva tudo que vier do formulário
        $config->fill($request->all());
        $config->save();

        // Apaga o cache antigo para o site atualizar na mesma hora
        Cache::forget('configuracoes_globais');

        return redirect()->back()->with('success', 'Configurações globais salvas com sucesso!');
    }
}