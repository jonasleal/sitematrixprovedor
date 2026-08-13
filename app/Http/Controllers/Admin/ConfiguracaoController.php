<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Configuracao;
use App\Models\Download;


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
    
    /**
     * Força a execução manual do backup e envio para o Google Drive
     */
    /**
     * Força a execução manual do backup e envio para o Google Drive
     */
    public function runBackup()
    {
        try {
            set_time_limit(300);
            $exitCode = Artisan::call('backup:run');
            $output = Artisan::output();

            // Se falhou, envia mensagem simples e guarda o log para o Console do JS
            if ($exitCode !== 0) {
                return redirect()->back()
                    ->with('error', 'Falha ao gerar o backup. Verifique o console do navegador (F12) para detalhes técnicos.')
                    ->with('console_log_error', $output);
            }

            // MÁGICA: Extrair o tamanho do arquivo do texto bruto do terminal usando Regex
            $tamanho = 'Tamanho desconhecido';
            if (preg_match('/Size is ([\d\.]+\s*[A-Z]+)/', $output, $matches)) {
                $tamanho = $matches[1];
            }

            // Formata a mensagem limpa e agradável (usando um separador | para não quebrar a formatação de toasts padrão)
            $mensagemLimpa = "✔ Backup gerado ({$tamanho}) | ✔ Enviado para Google Drive | ✔ Concluído!";

            return redirect()->back()->with('success', $mensagemLimpa);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro crítico ao processar o backup. Verifique o console (F12).')
                ->with('console_log_error', $e->getMessage());
        }
    }
}