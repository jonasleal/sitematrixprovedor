<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log; 
use App\Models\Noticia;
use App\Models\BannerTag;
use App\Services\ImageUploadService;

class NoticiaController extends Controller
{
    public function index()
    {
        try {
            $noticias = Noticia::orderBy('created_at', 'desc')->paginate(10);
            
            // PUXANDO AS TAGS PARA ENVIAR PRO FORMULÁRIO
            $tags = \App\Models\Tag::orderBy('nome', 'asc')->get();
            
            return view('admin.noticias.index', compact('noticias', 'tags'));
        } catch (\Exception $e) {
            Log::error('Erro ao carregar Index de Notícias: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Erro interno ao carregar notícias.');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255|unique:noticias,titulo',
            'resumo' => 'required|string|max:500',
            'conteudo' => 'required|string',
            'tag_id' => 'nullable|exists:tags,id',
            'imagem_destaque' => 'nullable|image|max:5120',
        ]);

        $path = null;

        try {
            // Converte a capa da notícia para WebP
            if ($request->hasFile('imagem_destaque')) {
                $path = ImageUploadService::uploadAndOptimize($request->file('imagem_destaque'), 'noticias', 85);
            }

            Noticia::create([
                'titulo' => $request->titulo,
                'slug' => Str::slug($request->titulo),
                'resumo' => $request->resumo,
                'conteudo' => $request->conteudo,
                'imagem_destaque' => $path,
                'ativo' => $request->has('ativo'),
            ]);

            return redirect()->back()->with('success', 'Notícia publicada com sucesso!');

        } catch (\Exception $e) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            
            Log::error('Erro ao criar Notícia: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Ocorreu um erro ao salvar a notícia. Tente novamente.');
        }
    }

    public function destroy($id)
    {
        try {
            $noticia = Noticia::findOrFail($id);
            $imagemParaApagar = $noticia->imagem_destaque;
            
            // Apaga do BD primeiro
            $noticia->delete();
            
            // Se apagou do BD com sucesso, remove a imagem física do servidor
            if ($imagemParaApagar && Storage::disk('public')->exists($imagemParaApagar)) {
                Storage::disk('public')->delete($imagemParaApagar);
            }
            
            return redirect()->back()->with('success', 'Notícia eliminada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao excluir Notícia ID ' . $id . ': ' . $e->getMessage());
            return redirect()->back()->with('error', 'Não foi possível excluir a notícia.');
        }
    }

    public function uploadImagem(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            ]);

            // MÁGICA: Até as imagens arrastadas para dentro do texto da notícia agora viram WebP
            $path = ImageUploadService::uploadAndOptimize($request->file('file'), 'noticias/conteudo', 80);

            return response()->json([
                'location' => asset('storage/' . $path)
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'A imagem selecionada é muito pesada ou formato inválido.'
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro grave de Upload TinyMCE: ' . $e->getMessage());
            return response()->json([
                'error' => 'Falha interna no servidor ao processar a imagem.'
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $noticia = Noticia::findOrFail($id);
            $tags = \App\Models\Tag::orderBy('nome', 'asc')->get();
            return view('admin.noticias.edit', compact('noticia', 'tags'));
        } catch (\Exception $e) {
            Log::error('Erro ao abrir tela de edição de Notícia ID ' . $id . ': ' . $e->getMessage());
            return redirect()->route('admin.noticias.index')->with('error', 'Notícia não encontrada.');
        }
    }

    public function update(Request $request, $id)
    {
        $noticia = Noticia::findOrFail($id);

        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'resumo' => 'required|string',
            'conteudo' => 'required|string',
            'tag_id' => 'nullable|exists:tags,id',
            'publicado_em' => 'nullable|date',
            'imagem_destaque' => 'nullable|image|max:5120',
        ]);

        $data['ativo'] = $request->has('ativo');
        
        if ($noticia->titulo !== $request->titulo) {
            $data['slug'] = Str::slug($request->titulo) . '-' . uniqid();
        }

        $novoPath = null;
        $pathAntigo = $noticia->imagem_destaque;

        try {
            // Conversão da nova imagem
            if ($request->hasFile('imagem_destaque')) {
                $novoPath = ImageUploadService::uploadAndOptimize($request->file('imagem_destaque'), 'noticias', 85);
                $data['imagem_destaque'] = $novoPath;
            }

            $noticia->update($data);

            if ($novoPath && $pathAntigo && Storage::disk('public')->exists($pathAntigo)) {
                Storage::disk('public')->delete($pathAntigo);
            }

            return redirect()->route('admin.noticias.index')->with('success', 'Notícia atualizada com sucesso!');

        } catch (\Exception $e) {
            if ($novoPath && Storage::disk('public')->exists($novoPath)) {
                Storage::disk('public')->delete($novoPath);
            }
            
            Log::error('Erro ao atualizar Notícia ID ' . $id . ': ' . $e->getMessage());
            return back()->withInput()->with('error', 'Falha ao atualizar a notícia. Verifique os dados inseridos.');
        }
    }
}