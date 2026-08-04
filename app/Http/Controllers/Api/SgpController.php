<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Exception;

class SgpController extends Controller
{
    public function registrarPreCadastro(Request $request)
    {
        // 1. Validação básica de segurança para evitar envios maliciosos
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'cpfcnpj' => 'required|string|max:14',
            'celular' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'datanasc' => 'required|date',
            'logradouro' => 'required|string|max:255',
            'numero' => 'required|integer',
            'bairro' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
            'cep' => 'required|string|max:10',
            'uf' => 'required|string|max:2',
            'plano_id' => 'required|integer',
        ]);

        try {
            // 2. Monta o pacote de dados EXATAMENTE como a tabela do SGP pede
            $payload = [
                'app' => env('SGP_APP_ID'),       // Definido no seu arquivo .env
                'token' => env('SGP_APP_TOKEN'),  // Definido no seu arquivo .env
                'nome' => $request->nome,
                'cpfcnpj' => $request->cpfcnpj,
                'rg' => $request->rg ?? '',
                'email' => $request->email,
                'celular' => $request->celular,
                'datanasc' => $request->datanasc,
                'logradouro' => $request->logradouro,
                'numero' => $request->numero,
                'complemento' => $request->complemento ?? '',
                'bairro' => $request->bairro,
                'cidade' => $request->cidade,
                'cep' => $request->cep,
                'uf' => $request->uf,
                'pais' => 'Brasil',
                'plano_id' => $request->plano_id,
                'modoaquisicao' => 1, // 1 = Comodato (Roteador da Empresa)
                'precadastro_ativar' => 0 // 0 = Apenas Pré-cadastro. Espera a aprovação humana.
            ];

            // 3. Dispara a requisição para a API do SGP
            // (Substitua a URL abaixo pela URL real da API do seu SGP)
            $sgpUrl = env('SGP_API_URL') . '/ws/precadastro_pf';
            
            $response = Http::asForm()->post($sgpUrl, $payload);

            if ($response->successful()) {
                return response()->json(['message' => 'Pré-cadastro integrado ao SGP com sucesso!']);
            } else {
                return response()->json(['message' => 'O SGP recusou os dados.', 'details' => $response->json()], 422);
            }

        } catch (Exception $e) {
            return response()->json(['message' => 'Erro interno na comunicação com o SGP.'], 500);
        }
    }
}