<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PoligonoCobertura;
use Exception;

class PoligonoController extends Controller
{
    // Exibe a tela do mapa
    public function index()
    {
        return view('admin.mapa');
    }

    // Busca os dados do banco para desenhar no mapa
    public function indexData()
    {
        try {
            // Busca todos os polígonos ativos
            $poligonos = PoligonoCobertura::where('ativo', true)->get();
            return response()->json($poligonos);
            
        } catch (Exception $e) {
            // Se algo der errado, devolve o erro real do banco de dados!
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Recebe o AJAX do mapa e salva no banco
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nome' => 'required|string|max:255',
                'cor' => 'required|string|max:20',
                'coordenadas' => 'required|array',
            ]);

            PoligonoCobertura::create([
                'nome' => $request->nome,
                'cor' => $request->cor,
                'coordenadas' => $request->coordenadas,
                'ativo' => true
            ]);

            return response()->json(['message' => 'Polígono salvo com sucesso!']);
            
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
	// Atualiza APENAS as coordenadas de um polígono existente
    public function update(Request $request, $id)
    {
        try {
            $poligono = PoligonoCobertura::findOrFail($id);
            $poligono->coordenadas = $request->coordenadas;
            $poligono->save();

            return response()->json(['message' => 'Área atualizada com sucesso!']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Deleta um polígono
    public function destroy($id)
    {
        try {
            PoligonoCobertura::destroy($id);
            return response()->json(['message' => 'Área removida com sucesso!']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}