<?php

namespace App\Http\Controllers;

use App\Models\Download;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // Importante para registro de erros

class DownloadController extends Controller
{
    // Listagem Admin
    public function index()
    {
        try {
            $downloads = Download::orderBy('ordem', 'asc')->orderBy('created_at', 'desc')->get();
            return view('admin.downloads.index', compact('downloads'));
        } catch (\Exception $e) {
            Log::error('Erro ao listar downloads no Admin: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Erro interno ao carregar downloads.');
        }
    }

    // Form Criar
    public function create()
    {
        return view('admin.downloads.create');
    }

    // Guardar Ficheiro
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'categoria' => 'required|string',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048', // Validação da Miniatura
            'tipo_link' => 'required|in:upload,externo',
            'arquivo' => 'nullable|file|mimes:pdf,apk,zip,rar,doc,docx|max:51200', // Limite de 50MB
            'link_externo' => 'nullable|url',
            'versao' => 'nullable|string|max:50',
            'ordem' => 'nullable|integer',
        ]);

        $path = null;
        $imagemPath = null;

        try {
            // 1. Upload dos Arquivos PRIMEIRO
            if ($request->tipo_link === 'upload' && $request->hasFile('arquivo')) {
                $path = $request->file('arquivo')->store('downloads/arquivos', 'public');
            } elseif ($request->tipo_link === 'externo') {
                $path = $request->link_externo;
            }

            // Upload da Miniatura
            if ($request->hasFile('imagem')) {
                $imagemPath = $request->file('imagem')->store('downloads/miniaturas', 'public');
            }

            // 2. Tenta Salvar no Banco de Dados
            Download::create([
                'titulo' => $request->titulo,
                'descricao' => $request->descricao,
                'categoria' => $request->categoria,
                'imagem_path' => $imagemPath, 
                'tipo_link' => $request->tipo_link,
                'arquivo_path' => $path,
                'versao' => $request->versao,
                'ordem' => $request->ordem ?? 0,
                'ativo' => $request->has('ativo'),
            ]);

            return redirect()->route('admin.downloads.index')->with('success', 'Download adicionado com sucesso!');

        } catch (\Exception $e) {
            // 3. SE FALHAR O BANCO: Apaga os arquivos que acabaram de subir para não virar lixo
            if ($path && $request->tipo_link === 'upload' && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            if ($imagemPath && Storage::disk('public')->exists($imagemPath)) {
                Storage::disk('public')->delete($imagemPath);
            }

            Log::error('Erro ao criar Download: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Erro interno ao salvar o download. Verifique se o arquivo não ultrapassa 50MB.');
        }
    }

    // Form Editar
    public function edit($id)
    {
        try {
            $download = Download::findOrFail($id);
            return view('admin.downloads.edit', compact('download'));
        } catch (\Exception $e) {
            Log::error("Erro ao tentar editar o Download ID {$id}: " . $e->getMessage());
            return redirect()->route('admin.downloads.index')->with('error', 'Download não encontrado.');
        }
    }

    // Atualizar
    public function update(Request $request, $id)
    {
        $download = Download::findOrFail($id);

        $request->validate([
            'titulo' => 'required|string|max:255',
            'categoria' => 'required|string',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'tipo_link' => 'required|in:upload,externo',
            'arquivo' => 'nullable|file|mimes:pdf,apk,zip,rar,doc,docx|max:51200',
            'link_externo' => 'nullable|url',
            'versao' => 'nullable|string|max:50',
            'ordem' => 'nullable|integer',
        ]);

        $novoPath = null;
        $novaImagemPath = null;
        
        $antigoPath = $download->arquivo_path;
        $antigaImagemPath = $download->imagem_path;

        try {
            // 1. Faz o upload dos NOVOS arquivos (se houverem), sem apagar os velhos ainda
            if ($request->tipo_link === 'upload' && $request->hasFile('arquivo')) {
                $novoPath = $request->file('arquivo')->store('downloads/arquivos', 'public');
            } elseif ($request->tipo_link === 'externo' && $request->filled('link_externo')) {
                $novoPath = $request->link_externo;
            }

            if ($request->hasFile('imagem')) {
                $novaImagemPath = $request->file('imagem')->store('downloads/miniaturas', 'public');
            }

            // 2. Atualiza o Banco de Dados
            $download->update([
                'titulo' => $request->titulo,
                'descricao' => $request->descricao,
                'categoria' => $request->categoria,
                'imagem_path' => $novaImagemPath ?? $antigaImagemPath, // Mantém a antiga se não enviou nova
                'tipo_link' => $request->tipo_link,
                'arquivo_path' => $novoPath ?? $antigoPath,
                'versao' => $request->versao,
                'ordem' => $request->ordem ?? 0,
                'ativo' => $request->has('ativo'),
            ]);

            // 3. Sucesso no BD? Agora sim apagamos os arquivos físicos ANTIGOS
            if ($novoPath && $download->getOriginal('tipo_link') === 'upload' && $antigoPath && Storage::disk('public')->exists($antigoPath)) {
                Storage::disk('public')->delete($antigoPath);
            }
            if ($novaImagemPath && $antigaImagemPath && Storage::disk('public')->exists($antigaImagemPath)) {
                Storage::disk('public')->delete($antigaImagemPath);
            }

            return redirect()->route('admin.downloads.index')->with('success', 'Download atualizado com sucesso!');

        } catch (\Exception $e) {
            // 4. Falha no BD? Apaga os arquivos NOVOS que acabaram de subir para não virar lixo
            if ($novoPath && $request->tipo_link === 'upload' && Storage::disk('public')->exists($novoPath)) {
                Storage::disk('public')->delete($novoPath);
            }
            if ($novaImagemPath && Storage::disk('public')->exists($novaImagemPath)) {
                Storage::disk('public')->delete($novaImagemPath);
            }

            Log::error('Erro ao atualizar Download ID ' . $id . ': ' . $e->getMessage());
            return back()->withInput()->with('error', 'Ocorreu um erro interno ao atualizar. Verifique os limites de tamanho de arquivo.');
        }
    }

    // Apagar
    public function destroy($id)
    {
        try {
            $download = Download::findOrFail($id);
            
            $pathParaApagar = $download->arquivo_path;
            $imagemParaApagar = $download->imagem_path;
            $eraUpload = ($download->tipo_link === 'upload');

            // 1. Apaga do BD primeiro
            $download->delete();

            // 2. Se apagou do BD com sucesso, remove os ficheiros físicos
            if ($eraUpload && $pathParaApagar && Storage::disk('public')->exists($pathParaApagar)) {
                Storage::disk('public')->delete($pathParaApagar);
            }
            if ($imagemParaApagar && Storage::disk('public')->exists($imagemParaApagar)) {
                Storage::disk('public')->delete($imagemParaApagar);
            }

            return redirect()->route('admin.downloads.index')->with('success', 'Download removido com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao excluir Download ID ' . $id . ': ' . $e->getMessage());
            return redirect()->route('admin.downloads.index')->with('error', 'Não foi possível remover o download. Ele pode estar em uso.');
        }
    }

    // Exibição Pública (/p/downloads)
    public function showPublic()
    {
        try {
            $downloads = Download::where('ativo', true)
                ->orderBy('ordem', 'asc')
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy('categoria');

            return view('downloads', compact('downloads'));
        } catch (\Exception $e) {
            Log::error('Erro ao exibir Downloads Públicos: ' . $e->getMessage());
            // Mostramos um array vazio para não quebrar o layout, mas não revelamos o erro 500
            return view('downloads', ['downloads' => []]);
        }
    }
}