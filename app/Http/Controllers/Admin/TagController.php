<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tag;

class TagController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100|unique:tags,nome'
        ]);

        Tag::create([
            'nome' => mb_strtoupper(trim($request->nome))
        ]);

        return redirect()->back()->with('success', 'Nova tag adicionada!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required|string|max:100|unique:tags,nome,' . $id
        ]);

        $tag = Tag::findOrFail($id);
        $tag->update([
            'nome' => mb_strtoupper(trim($request->nome))
        ]);

        return redirect()->back()->with('success', 'Tag atualizada com sucesso!');
    }

    public function destroy($id)
    {
        Tag::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Tag removida!');
    }
}