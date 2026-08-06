<?php

namespace App\Http\Controllers;

use App\Models\Download;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    // Listagem Admin
    public function index()
    {
        $downloads = Download::orderBy('ordem', 'asc')->orderBy('created_at', 'desc')->get();
        return view('admin.downloads.index', compact('downloads'));
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
            'arquivo' => 'nullable|file|mimes:pdf,apk,zip,rar,doc,docx|max:51200',
            'link_externo' => 'nullable|url',
            'versao' => 'nullable|string|max:50',
            'ordem' => 'nullable|integer',
        ]);

        $path = null;
        if ($request->tipo_link === 'upload' && $request->hasFile('arquivo')) {
            $path = $request->file('arquivo')->store('downloads/arquivos', 'public');
        } elseif ($request->tipo_link === 'externo') {
            $path = $request->link_externo;
        }

        // Upload da Miniatura
        $imagemPath = null;
        if ($request->hasFile('imagem')) {
            $imagemPath = $request->file('imagem')->store('downloads/miniaturas', 'public');
        }

        Download::create([
            'titulo' => $request->titulo,
            'descricao' => $request->descricao,
            'categoria' => $request->categoria,
            'imagem_path' => $imagemPath, // Grava a miniatura
            'tipo_link' => $request->tipo_link,
            'arquivo_path' => $path,
            'versao' => $request->versao,
            'ordem' => $request->ordem ?? 0,
            'ativo' => $request->has('ativo'),
        ]);

        return redirect()->route('admin.downloads.index')->with('success', 'Download adicionado com sucesso!');
    }

    // Form Editar
    public function edit($id)
    {
        $download = Download::findOrFail($id);
        return view('admin.downloads.edit', compact('download'));
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

        $path = $download->arquivo_path;

        if ($request->tipo_link === 'upload' && $request->hasFile('arquivo')) {
            if ($download->tipo_link === 'upload' && $download->arquivo_path) {
                Storage::disk('public')->delete($download->arquivo_path);
            }
            $path = $request->file('arquivo')->store('downloads/arquivos', 'public');
        } elseif ($request->tipo_link === 'externo' && $request->filled('link_externo')) {
            $path = $request->link_externo;
        }

        // Atualizar Miniatura
        $imagemPath = $download->imagem_path;
        if ($request->hasFile('imagem')) {
            if ($download->imagem_path) {
                Storage::disk('public')->delete($download->imagem_path);
            }
            $imagemPath = $request->file('imagem')->store('downloads/miniaturas', 'public');
        }

        $download->update([
            'titulo' => $request->titulo,
            'descricao' => $request->descricao,
            'categoria' => $request->categoria,
            'imagem_path' => $imagemPath, // Atualiza a miniatura
            'tipo_link' => $request->tipo_link,
            'arquivo_path' => $path,
            'versao' => $request->versao,
            'ordem' => $request->ordem ?? 0,
            'ativo' => $request->has('ativo'),
        ]);

        return redirect()->route('admin.downloads.index')->with('success', 'Download atualizado com sucesso!');
    }

    // Apagar
    public function destroy($id)
    {
        $download = Download::findOrFail($id);
        
        // Apaga o ficheiro se for local
        if ($download->tipo_link === 'upload' && $download->arquivo_path) {
            Storage::disk('public')->delete($download->arquivo_path);
        }
        
        // Apaga a miniatura
        if ($download->imagem_path) {
            Storage::disk('public')->delete($download->imagem_path);
        }
        
        $download->delete();

        return redirect()->route('admin.downloads.index')->with('success', 'Download removido com sucesso!');
    }

    // Exibição Pública (/p/downloads)
    public function showPublic()
    {
        $downloads = Download::where('ativo', true)
            ->orderBy('ordem', 'asc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('categoria');

        return view('downloads', compact('downloads'));
    }
}