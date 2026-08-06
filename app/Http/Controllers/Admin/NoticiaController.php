<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


use App\Models\Noticia;
use App\Models\BannerTag;


class NoticiaController extends Controller
{
    public function index()
    {
        $noticias = Noticia::orderBy('created_at', 'desc')->paginate(10);
        
        // PUXANDO AS TAGS PARA ENVIAR PRO FORMULÁRIO
        $tags = \App\Models\Tag::orderBy('nome', 'asc')->get();
        
        return view('admin.noticias.index', compact('noticias', 'tags'));
    }

    public function store(Request $request)
    {
        // 1. Validação (garante que o título é único para não duplicar o link/slug)
        $request->validate([
            'titulo' => 'required|string|max:255|unique:noticias,titulo',
            'resumo' => 'required|string|max:500',
            'conteudo' => 'required|string',
            'tag_id' => 'nullable|exists:tags,id',
            'imagem_destaque' => 'nullable|image|max:2048',
        ]);

        // 2. Upload da imagem (se houver)
        $path = null;
        if ($request->hasFile('imagem_destaque')) {
            $path = $request->file('imagem_destaque')->store('noticias', 'public');
        }

        // 3. Gravação na base de dados
        Noticia::create([
            'titulo' => $request->titulo,
            'slug' => Str::slug($request->titulo), // Ex: "Nova Fibra" vira "nova-fibra"
            'resumo' => $request->resumo,
            'conteudo' => $request->conteudo,
            'imagem_destaque' => $path,
            'ativo' => $request->has('ativo'),
        ]);

        return redirect()->back()->with('success', 'Notícia publicada com sucesso!');
    }

    public function destroy($id)
    {
        $noticia = Noticia::findOrFail($id);
        
        // Elimina a imagem física do servidor para não acumular lixo
        if ($noticia->imagem_destaque) {
            Storage::disk('public')->delete($noticia->imagem_destaque);
        }
        
        $noticia->delete();
        
        return redirect()->back()->with('success', 'Notícia eliminada com sucesso!');
    }

    public function uploadImagem(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $path = $request->file('file')->store('noticias/conteudo', 'public');

        return response()->json([
            'location' => asset('storage/' . $path)
        ]);
    }

    public function edit($id)
    {
        $noticia = Noticia::findOrFail($id);
        $tags = \App\Models\Tag::orderBy('nome', 'asc')->get();
        return view('admin.noticias.edit', compact('noticia', 'tags'));
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
        ]);

        $data['ativo'] = $request->has('ativo');
        
        // Atualiza o slug caso o título tenha mudado
        if ($noticia->titulo !== $request->titulo) {
            $data['slug'] = Str::slug($request->titulo) . '-' . uniqid();
        }

        // Verifica se enviou uma imagem nova
        if ($request->hasFile('imagem_destaque')) {
            $request->validate(['imagem_destaque' => 'image|max:2048']);
            // Apaga a antiga
            if ($noticia->imagem_destaque) {
                Storage::disk('public')->delete($noticia->imagem_destaque);
            }
            $data['imagem_destaque'] = $request->file('imagem_destaque')->store('noticias', 'public');
        }

        $noticia->update($data);

        return redirect()->route('admin.noticias.index')->with('success', 'Notícia atualizada com sucesso!');
    }
}