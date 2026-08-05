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
        $tags = BannerTag::orderBy('nome', 'asc')->get(); 
        
        return view('admin.noticias.index', compact('noticias', 'tags'));
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
}