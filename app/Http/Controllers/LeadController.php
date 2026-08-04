<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeadCobertura;

class LeadController extends Controller
{
    public function store(Request $request)
    {
        // Validação básica de segurança
        $request->validate([
            'pronome' => 'required|string|max:10',
            'nome' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:20',
            'endereco' => 'nullable|string|max:255',
        ]);

        // Salva no banco de dados
        LeadCobertura::create([
            'pronome' => $request->pronome,
            'nome' => $request->nome,
            'whatsapp' => $request->whatsapp,
            'endereco_pesquisado' => $request->endereco_completo,
            'rua' => $request->rua,
            'bairro' => $request->bairro,
            'cidade' => $request->cidade,
            'estado' => $request->estado,
            'cep' => $request->cep,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'status' => 'Pendente',
        ]);

        return response()->json(['message' => 'Salvo com sucesso!']);
    }
}