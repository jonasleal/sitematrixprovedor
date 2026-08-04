<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Noticia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NoticiaController extends Controller
{
    public function index()
    {
        // Traz as notícias mais recentes primeiro
        $noticias = Noticia::orderBy('created_at', 'desc')->get();
        return view('admin.noticias.index', compact('noticias'));
    }

    public function store(Request $request)
    {
        // 1. Validação (garante que o título é único para não duplicar o link/slug)
        $request->validate([
            'titulo' => 'required|string|max:255|unique:noticias,titulo',
            'resumo' => 'required|string|max:500',
            'conteudo' => 'required|string',
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
}