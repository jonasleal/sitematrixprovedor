<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Tag;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    private $mensagensErro = [
        'required' => 'O campo :attribute é obrigatório.',
        'image' => 'O arquivo enviado deve ser uma imagem válida.',
        'max' => 'A imagem selecionada é muito pesada. O limite máximo é 1MB (1024KB).',
    ];

    public function index()
    {
        $banners = Banner::orderBy('ordem', 'asc')->get();
        $tags = Tag::orderBy('nome', 'asc')->get();
        return view('admin.banners.index', compact('banners', 'tags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'caminho_imagem' => 'required|image|max:1024',
            'caminho_imagem_mobile' => 'nullable|image|max:1024',
            'titulo'         => 'required|string',
            'tag_id'         => 'nullable|exists:tags,id',
            'tema_cor'       => 'required|string',
        ], $this->mensagensErro);

        $pathPC = $request->file('caminho_imagem')->store('banners', 'public');
        $pathMobile = $request->hasFile('caminho_imagem_mobile') 
            ? $request->file('caminho_imagem_mobile')->store('banners', 'public') 
            : null;

        Banner::create([
            'titulo'        => $request->titulo,
            'tag_id'        => $request->tag_id,
            'tema_cor'      => $request->tema_cor,
            'descricao'     => $request->descricao,
            'texto_botao'   => $request->texto_botao ?: 'Saiba Mais',
            'link_destino'  => $request->link_destino,
            'caminho_imagem'=> $pathPC,
            'caminho_imagem_mobile' => $pathMobile,
            'proporcao_imagem'=> $request->proporcao_imagem ?? '50',
            'posicao_x'     => $request->posicao_x ?? 50,
            'posicao_y'     => $request->posicao_y ?? 50,
            'zoom'          => $request->zoom ?? 100,
            'ativo'         => $request->has('ativo'),
            'data_inicio'   => $request->data_inicio,
            'data_fim'      => $request->data_fim,
            'ordem'         => Banner::max('ordem') + 1,
        ]);

        return redirect()->back()->with('success', 'Banner criado com sucesso!');
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $request->validate([
            'caminho_imagem' => 'nullable|image|max:1024',
            'caminho_imagem_mobile' => 'nullable|image|max:1024',
            'titulo'         => 'required|string',
            'tag_id'         => 'nullable|exists:tags,id',
            'tema_cor'       => 'required|string',
        ], $this->mensagensErro);

        if ($request->hasFile('caminho_imagem')) {
            if ($banner->caminho_imagem) Storage::disk('public')->delete($banner->caminho_imagem);
            $banner->caminho_imagem = $request->file('caminho_imagem')->store('banners', 'public');
        }

        if ($request->hasFile('caminho_imagem_mobile')) {
            if ($banner->caminho_imagem_mobile) Storage::disk('public')->delete($banner->caminho_imagem_mobile);
            $banner->caminho_imagem_mobile = $request->file('caminho_imagem_mobile')->store('banners', 'public');
        }

        $banner->update([
            'titulo'        => $request->titulo,
            'tag_id'        => $request->tag_id,
            'tema_cor'      => $request->tema_cor,
            'descricao'     => $request->descricao,
            'texto_botao'   => $request->texto_botao,
            'link_destino'  => $request->link_destino,
            'proporcao_imagem'=> $request->proporcao_imagem ?? '50',
            'posicao_x'     => $request->posicao_x ?? 50,
            'posicao_y'     => $request->posicao_y ?? 50,
            'zoom'          => $request->zoom ?? 100,
            'ativo'         => $request->has('ativo'),
            'data_inicio'   => $request->data_inicio,
            'data_fim'      => $request->data_fim,
        ]);

        return redirect()->back()->with('success', 'Banner atualizado com sucesso!');
    }

    public function toggleStatus($id) { 
        $banner = Banner::findOrFail($id); 
        $banner->ativo = !$banner->ativo; 
        $banner->save(); 
        return redirect()->back()->with('success', 'Status do banner alterado!'); 
    }
    
    public function reordenar(Request $request) { 
        $ordem = $request->input('ordem', []); 
        foreach ($ordem as $item) { 
            Banner::where('id', $item['id'])->update(['ordem' => $item['ordem']]); 
        } 
        return response()->json(['status' => 'success']); 
    }
    
    public function destroy($id) { 
        $banner = Banner::findOrFail($id); 
        if ($banner->caminho_imagem) Storage::disk('public')->delete($banner->caminho_imagem); 
        if ($banner->caminho_imagem_mobile) Storage::disk('public')->delete($banner->caminho_imagem_mobile); 
        $banner->delete(); 
        return redirect()->back()->with('success', 'Banner excluído!'); 
    }
}