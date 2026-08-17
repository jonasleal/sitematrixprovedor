<?php

namespace App\Http\Controllers;

use App\Models\Download;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // Importante para registro de erros
use App\Services\ImageUploadService;

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
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'descricao' => 'nullable|string',
            'versao' => 'nullable|string|max:50',
            'ordem' => 'nullable|integer',
            // Validação dos Links Dinâmicos
            'links.*.plataforma' => 'required|string',
            'links.*.tipo_link' => 'required|in:upload,externo',
            'links.*.arquivo' => 'nullable|file|mimes:pdf,apk,zip,rar,doc,docx|max:51200',
            'links.*.link_externo' => 'nullable|url',
        ]);

        $imagemPath = null;

        try {
            // 1. Upload da Miniatura (Capa do App)
            if ($request->hasFile('imagem')) {
                $imagemPath = ImageUploadService::uploadAndOptimize($request->file('imagem'), 'downloads/miniaturas', 85);
            }

            // 2. Salva o App "Pai"
            $download = Download::create([
                'titulo' => $request->titulo,
                'descricao' => $request->descricao,
                'categoria' => $request->categoria,
                'imagem_path' => $imagemPath, 
                'versao' => $request->versao,
                'ordem' => $request->ordem ?? 0,
                'ativo' => $request->has('ativo'),
            ]);

            // 3. Salva os Links (Filhos)
            if ($request->has('links')) {
                foreach ($request->links as $index => $linkData) {
                    $linkPath = null;

                    // Se for Upload de Arquivo
                    if ($linkData['tipo_link'] === 'upload' && $request->hasFile("links.{$index}.arquivo")) {
                        $linkPath = $request->file("links.{$index}.arquivo")->store('downloads/arquivos', 'public');
                    } 
                    // Se for Link Externo
                    elseif ($linkData['tipo_link'] === 'externo' && !empty($linkData['link_externo'])) {
                        $linkPath = $linkData['link_externo'];
                    }

                    // Só salva no banco se houver um caminho válido
                    if ($linkPath) {
                        $download->links()->create([
                            'plataforma' => $linkData['plataforma'],
                            'link' => $linkPath,
                        ]);
                    }
                }
            }

            return redirect()->route('admin.downloads.index')->with('success', 'Download adicionado com sucesso!');

        } catch (\Exception $e) {
            // Em caso de erro grave, limpa a miniatura
            if ($imagemPath && Storage::disk('public')->exists($imagemPath)) {
                Storage::disk('public')->delete($imagemPath);
            }
            Log::error('Erro ao criar Download Multi-plataforma: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Erro interno ao salvar. Verifique se os arquivos não ultrapassam o limite.');
        }
    }

    // Form Editar
    public function edit($id)
    {
        try {
            // Carrega o App e já traz os links anexados a ele
            $download = Download::with('links')->findOrFail($id);
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
            'descricao' => 'nullable|string',
            'versao' => 'nullable|string|max:50',
            'ordem' => 'nullable|integer',
            // Validação dos Links
            'links.*.id' => 'nullable|integer|exists:download_links,id',
            'links.*.plataforma' => 'required|string',
            'links.*.tipo_link' => 'required|in:upload,externo',
            'links.*.arquivo' => 'nullable|file|mimes:pdf,apk,zip,rar,doc,docx|max:51200',
            'links.*.link_externo' => 'nullable|url',
        ]);

        $novaImagemPath = null;
        $antigaImagemPath = $download->imagem_path;

        try {
            // 1. Atualiza Miniatura
            if ($request->hasFile('imagem')) {
                $novaImagemPath = ImageUploadService::uploadAndOptimize($request->file('imagem'), 'downloads/miniaturas', 85);
            }

            // 2. Atualiza o App Pai
            $download->update([
                'titulo' => $request->titulo,
                'descricao' => $request->descricao,
                'categoria' => $request->categoria,
                'imagem_path' => $novaImagemPath ?? $antigaImagemPath,
                'versao' => $request->versao,
                'ordem' => $request->ordem ?? 0,
                'ativo' => $request->has('ativo'),
            ]);

            // Se subiu foto nova, apaga a velha
            if ($novaImagemPath && $antigaImagemPath && Storage::disk('public')->exists($antigaImagemPath)) {
                Storage::disk('public')->delete($antigaImagemPath);
            }

            // 3. Processamento Complexo: Sincronização de Links Filhos
            $linksEnviadosIds = [];

            if ($request->has('links')) {
                foreach ($request->links as $index => $linkData) {
                    $linkModel = null;
                    $linkPath = null;

                    // Se o link já existia, vamos atualizá-lo
                    if (!empty($linkData['id'])) {
                        $linkModel = $download->links()->find($linkData['id']);
                        $linksEnviadosIds[] = $linkModel->id;
                        $linkPath = $linkModel->link; // Mantém o antigo por padrão
                    }

                    // Se subiu um arquivo novo
                    if ($linkData['tipo_link'] === 'upload' && $request->hasFile("links.{$index}.arquivo")) {
                        $novoArquivo = $request->file("links.{$index}.arquivo")->store('downloads/arquivos', 'public');
                        // Apaga o antigo se existia e era upload
                        if ($linkModel && !filter_var($linkModel->link, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($linkModel->link)) {
                            Storage::disk('public')->delete($linkModel->link);
                        }
                        $linkPath = $novoArquivo;
                    } 
                    // Se mudou para link externo
                    elseif ($linkData['tipo_link'] === 'externo' && !empty($linkData['link_externo'])) {
                        // Apaga o antigo se existia e era upload
                        if ($linkModel && !filter_var($linkModel->link, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($linkModel->link)) {
                            Storage::disk('public')->delete($linkModel->link);
                        }
                        $linkPath = $linkData['link_externo'];
                    }

                    // Salva ou Cria o Link
                    if ($linkPath) {
                        if ($linkModel) {
                            $linkModel->update([
                                'plataforma' => $linkData['plataforma'],
                                'link' => $linkPath,
                            ]);
                        } else {
                            $novoLink = $download->links()->create([
                                'plataforma' => $linkData['plataforma'],
                                'link' => $linkPath,
                            ]);
                            $linksEnviadosIds[] = $novoLink->id;
                        }
                    }
                }
            }

            // 4. Excluir links removidos pelo usuário na tela
            $linksParaApagar = $download->links()->whereNotIn('id', $linksEnviadosIds)->get();
            foreach ($linksParaApagar as $l) {
                if (!filter_var($l->link, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($l->link)) {
                    Storage::disk('public')->delete($l->link);
                }
                $l->delete();
            }

            return redirect()->route('admin.downloads.index')->with('success', 'Download atualizado com sucesso!');

        } catch (\Exception $e) {
            if ($novaImagemPath && Storage::disk('public')->exists($novaImagemPath)) {
                Storage::disk('public')->delete($novaImagemPath);
            }
            Log::error('Erro ao atualizar Download ID ' . $id . ': ' . $e->getMessage());
            return back()->withInput()->with('error', 'Ocorreu um erro interno. Verifique os arquivos.');
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
            // Eager Loading: with('links') traz os botões anexados de uma só vez
            $downloads = Download::with('links')
                ->where('ativo', true)
                ->orderBy('ordem', 'asc')
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy('categoria');

            return view('downloads', compact('downloads'));
        } catch (\Exception $e) {
            Log::error('Erro ao exibir Downloads Públicos: ' . $e->getMessage());
            // Retornamos um collect() vazio em vez de um Array []
            return view('downloads', ['downloads' => collect([])]);
        }
    }
    /**
     * Intercepta o clique, contabiliza o Hit e redireciona para a URL real.
     */
    public function registrarClique($id)
    {
        $link = \App\Models\DownloadLink::findOrFail($id);
        
        // Soma +1 no banco de dados automaticamente
        $link->increment('hits');
        
        // Verifica se é uma URL externa (http...) ou um arquivo interno do storage
        $urlDestino = filter_var($link->link, FILTER_VALIDATE_URL) 
                        ? $link->link 
                        : asset('storage/' . $link->link);
        
        // Redireciona o usuário (usamos away() para garantir que links externos não quebrem)
        return redirect()->away($urlDestino);
    }
}