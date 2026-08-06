<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PreCadastroController extends Controller
{
    public function store(Request $request)
    {
        // 1. MENSAGENS TRADUZIDAS PARA O LARAVEL
        $mensagens = [
            'required' => 'Este campo é obrigatório.',
            'email'    => 'Digite um endereço de e-mail válido.',
            'max'      => 'O valor digitado é muito longo.',
        ];

        // 2. VALIDAÇÃO DO SITE
        $validator = Validator::make($request->all(), [
            'nome'       => 'required|string|max:255',
            'cpfcnpj'    => 'required|string',
            'celular'    => 'required|string',
            'email'      => 'required|email',
            'logradouro' => 'required|string',
            'numero'     => 'required',
            'bairro'     => 'required|string',
            'cidade'     => 'required|string',
            'cep'        => 'required|string',
            'uf'         => 'required|string|max:2',
            'plano_id'   => 'required',
        ], $mensagens);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Faltam dados ou há erros no formulário.',
                'details' => $validator->errors() // Manda os erros exatos de cada campo
            ], 422);
        }

        try {
            $baseUrl = rtrim(env('SGP_API_URL', 'https://matrix.sgp.tsmx.app'), '/');
            $app = env('SGP_APP_ID', 'SiteMatrix');
            $token = env('SGP_APP_TOKEN', '5f510256-6c73-44fe-8af3-889536242230');

            $payload = [
                'token'       => $token,
                'app'         => $app,
                'nome'        => $request->nome,
                'cpfcnpj'     => preg_replace('/\D/', '', $request->cpfcnpj),
                'celular'     => preg_replace('/\D/', '', $request->celular),
                'email'       => $request->email,
                'datanasc'    => $request->datanasc,
                'rg'          => $request->rg ?? '',
                'logradouro'  => $request->logradouro,
                'numero'      => $request->numero,
                'complemento' => $request->complemento ?? '',
                'bairro'      => $request->bairro,
                'cidade'      => $request->cidade,
                'cep'         => preg_replace('/\D/', '', $request->cep),
                'uf'          => strtoupper($request->uf),
                'plano_id'    => (int) $request->plano_id,
            ];

            // 3. ENVIO PARA O SGP
            $response = Http::asForm()->post($baseUrl . '/api/precadastro/F/', $payload);

            if ($response->successful()) {
                return response()->json(['message' => 'Pré-cadastro realizado com sucesso!'], 200);
            } else {
                $respostaSgp = $response->json();
                Log::error("Erro SGP Pré-cadastro: ", is_array($respostaSgp) ? $respostaSgp : [$response->body()]);

                $mensagemAmigavel = $respostaSgp['error'] ?? 'Erro desconhecido no SGP.';
                $campoComErro = 'geral';
                $isCpfDuplicado = false; // NOVA FLAG DE INTELIGÊNCIA

                // Mapeia erros para o campo correto
                if (stripos($mensagemAmigavel, 'CPF') !== false || stripos($mensagemAmigavel, 'inválida') !== false) {
                    $campoComErro = 'cpfcnpj';
                    if (stripos($mensagemAmigavel, 'inválida') !== false) {
                        $mensagemAmigavel = 'O CPF informado é inválido.';
                    }
                    // SE O SGP AVISAR QUE JÁ EXISTE, ATIVAMOS A FLAG
                    if (stripos($mensagemAmigavel, 'Já existe') !== false || stripos($mensagemAmigavel, 'duplicado') !== false) {
                        $isCpfDuplicado = true;
                    }
                }

                return response()->json([
                    'message' => 'O SGP recusou os dados.',
                    'details' => [$campoComErro => [$mensagemAmigavel]],
                    'is_cpf_duplicado' => $isCpfDuplicado // Envia o aviso pro frontend
                ], 422); 
            }

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro fatal de comunicação.', 'details' => []], 500);
        }
    }

}