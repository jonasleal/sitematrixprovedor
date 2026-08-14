<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Tag;
use App\Services\ImageUploadService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // Importante para gravar os erros

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
            'caminho_imagem' => 'required|image|max:5120', // Aumentei o limite inicial, pois o WebP vai esmagar o arquivo depois
            'caminho_imagem_mobile' => 'nullable|image|max:5120',
            'titulo'         => 'required|string',
            'tag_id'         => 'nullable|exists:tags,id',
            'tema_cor'       => 'required|string',
        ], $this->mensagensErro);

        $pathPC = null;
        $pathMobile = null;

        try {
            // 1. Tenta subir os arquivos usando o NOVO MOTOR DE OTIMIZAÇÃO (WebP 85%)
            if ($request->hasFile('caminho_imagem')) {
                $pathPC = ImageUploadService::uploadAndOptimize($request->file('caminho_imagem'), 'banners', 85);
            }
            
            if ($request->hasFile('caminho_imagem_mobile')) {
                $pathMobile = ImageUploadService::uploadAndOptimize($request->file('caminho_imagem_mobile'), 'banners', 85);
            }

            // 2. Tenta salvar no Banco de Dados
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
                'ordem'         => (Banner::max('ordem') ?? 0) + 1,
            ]);

            return redirect()->back()->with('success', 'Banner criado e otimizado com sucesso!');

        } catch (\Exception $e) {
            // 3. SE ALGO DEU ERRADO: Limpeza do lixo (arquivos órfãos)
            if ($pathPC) Storage::disk('public')->delete($pathPC);
            if ($pathMobile) Storage::disk('public')->delete($pathMobile);

            Log::error('Erro ao salvar Banner: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Ocorreu um erro interno ao criar o banner. Tente novamente.');
        }
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $request->validate([
            'caminho_imagem' => 'nullable|image|max:5120',
            'caminho_imagem_mobile' => 'nullable|image|max:5120',
            'titulo'         => 'required|string',
            'tag_id'         => 'nullable|exists:tags,id',
            'tema_cor'       => 'required|string',
        ], $this->mensagensErro);

        $novoPathPC = null;
        $novoPathMobile = null;
        $antigoPathPC = $banner->caminho_imagem;
        $antigoPathMobile = $banner->caminho_imagem_mobile;

        try {
            // 1. Upload e conversão WebP (mantendo as imagens velhas a salvo por enquanto)
            if ($request->hasFile('caminho_imagem')) {
                $novoPathPC = ImageUploadService::uploadAndOptimize($request->file('caminho_imagem'), 'banners', 85);
            }

            if ($request->hasFile('caminho_imagem_mobile')) {
                $novoPathMobile = ImageUploadService::uploadAndOptimize($request->file('caminho_imagem_mobile'), 'banners', 85);
            }

            // 2. Atualiza o Banco de Dados
            $banner->update([
                'titulo'        => $request->titulo,
                'tag_id'        => $request->tag_id,
                'tema_cor'      => $request->tema_cor,
                'descricao'     => $request->descricao,
                'texto_botao'   => $request->texto_botao,
                'link_destino'  => $request->link_destino,
                'caminho_imagem'=> $novoPathPC ?? $antigoPathPC,
                'caminho_imagem_mobile' => $novoPathMobile ?? $antigoPathMobile,
                'proporcao_imagem'=> $request->proporcao_imagem ?? '50',
                'posicao_x'     => $request->posicao_x ?? 50,
                'posicao_y'     => $request->posicao_y ?? 50,
                'zoom'          => $request->zoom ?? 100,
                'ativo'         => $request->has('ativo'),
                'data_inicio'   => $request->data_inicio,
                'data_fim'      => $request->data_fim,
            ]);

            // 3. Sucesso no BD? Agora apagamos as imagens velhas (sejam JPG, PNG ou WebP)
            if ($novoPathPC && $antigoPathPC) Storage::disk('public')->delete($antigoPathPC);
            if ($novoPathMobile && $antigoPathMobile) Storage::disk('public')->delete($antigoPathMobile);

            return redirect()->back()->with('success', 'Banner atualizado com sucesso!');

        } catch (\Exception $e) {
            // 4. Falhou no BD? Apaga os WebP novos que acabaram de subir
            if ($novoPathPC) Storage::disk('public')->delete($novoPathPC);
            if ($novoPathMobile) Storage::disk('public')->delete($novoPathMobile);

            Log::error('Erro ao atualizar Banner ID ' . $id . ': ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Ocorreu um erro interno ao atualizar o banner.');
        }
    }

    public function toggleStatus($id) 
    { 
        try {
            $banner = Banner::findOrFail($id); 
            $banner->ativo = !$banner->ativo; 
            $banner->save(); 
            return redirect()->back()->with('success', 'Status do banner alterado!'); 
        } catch (\Exception $e) {
            Log::error('Erro ao alterar status do Banner: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Não foi possível alterar o status.'); 
        }
    }
    
    public function reordenar(Request $request) 
    { 
        try {
            $ordem = $request->input('ordem', []); 
            foreach ($ordem as $item) { 
                Banner::where('id', $item['id'])->update(['ordem' => $item['ordem']]); 
            } 
            return response()->json(['status' => 'success']); 
        } catch (\Exception $e) {
            Log::error('Erro ao reordenar Banners: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Falha ao reordenar'], 500); 
        }
    }
    
    public function destroy($id) 
    { 
        try {
            $banner = Banner::findOrFail($id); 
            $pathPC = $banner->caminho_imagem;
            $pathMobile = $banner->caminho_imagem_mobile;

            // Apaga do BD primeiro
            $banner->delete(); 

            // Se apagou do BD com sucesso, remove os arquivos físicos
            if ($pathPC) Storage::disk('public')->delete($pathPC); 
            if ($pathMobile) Storage::disk('public')->delete($pathMobile); 

            return redirect()->back()->with('success', 'Banner excluído!'); 
        } catch (\Exception $e) {
            Log::error('Erro ao excluir Banner: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Não foi possível excluir o banner.'); 
        }
    }
}