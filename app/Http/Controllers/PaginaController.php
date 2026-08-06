<?php

namespace App\Http\Controllers;

use App\Models\Pagina;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaginaController extends Controller
{
    public function index()
    {
        $paginas = Pagina::orderBy('created_at', 'desc')->get();
        return view('admin.paginas.index', compact('paginas'));
    }

    public function create()
    {
        return view('admin.paginas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:paginas,slug',
            'template' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['slug'] = $request->slug ? Str::slug($request->slug) : Str::slug($request->titulo);
        $data['ativo'] = $request->has('ativo');

        Pagina::create($data);

        return redirect()->route('admin.paginas.index')->with('success', 'Página criada com sucesso!');
    }

    public function edit($id)
    {
        $pagina = Pagina::findOrFail($id);
        return view('admin.paginas.edit', compact('pagina'));
    }

    public function update(Request $request, $id)
    {
        $pagina = Pagina::findOrFail($id);

        $request->validate([
            'titulo' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:paginas,slug,' . $pagina->id,
            'template' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['slug'] = $request->slug ? Str::slug($request->slug) : Str::slug($request->titulo);
        $data['ativo'] = $request->has('ativo');

        $pagina->update($data);

        return redirect()->route('admin.paginas.index')->with('success', 'Página atualizada com sucesso!');
    }

    public function destroy($id)
    {
        $pagina = Pagina::findOrFail($id);
        $pagina->delete();

        return redirect()->route('admin.paginas.index')->with('success', 'Página apagada com sucesso!');
    }
        // Exibe a página no frontend do site
    public function showPublic($slug)
    {
        $pagina = Pagina::where('slug', $slug)->where('ativo', true)->firstOrFail();
        return view('pagina', compact('pagina'));
    }
}