<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Client\RequestException;

class PreCadastroController extends Controller
{
    public function store(Request $request)
    {
        // 1. MENSAGENS DE VALIDAÇÃO
        $mensagens = [
            'required' => 'Este campo é obrigatório.',
            'email'    => 'Digite um endereço de e-mail válido.',
            'max'      => 'O valor digitado é muito longo.',
        ];

        // 2. VALIDAÇÃO DO FORMULÁRIO
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
                'details' => $validator->errors()
            ], 422);
        }

        try {
            // Puxa as credenciais centralizadas do config/services.php (Zero env() direto)
            $baseUrl = rtrim(config('services.sgp.url'), '/');
            $app     = config('services.sgp.app');
            $token   = config('services.sgp.token');

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

            // 3. ENVIO DIRETO PARA A API DO SGP (TIMEOUT DE 15s)
            $response = Http::timeout(15)->asForm()->post($baseUrl . '/api/precadastro/F/', $payload);

            if ($response->successful()) {
                return response()->json(['message' => 'Pré-cadastro realizado com sucesso!'], 200);
            } else {
                $respostaSgp = $response->json();
                Log::error("Erro SGP Pré-cadastro: ", is_array($respostaSgp) ? $respostaSgp : [$response->body()]);

                $mensagemAmigavel = $respostaSgp['error'] ?? 'Erro desconhecido no SGP.';
                $campoComErro = 'geral';
                $isCpfDuplicado = false;

                // Mapeamento de CPF / Erros de regra de negócio do SGP
                if (stripos($mensagemAmigavel, 'CPF') !== false || stripos($mensagemAmigavel, 'inválida') !== false) {
                    $campoComErro = 'cpfcnpj';
                    if (stripos($mensagemAmigavel, 'inválida') !== false) {
                        $mensagemAmigavel = 'O CPF informado é inválido.';
                    }
                    if (stripos($mensagemAmigavel, 'Já existe') !== false || stripos($mensagemAmigavel, 'duplicado') !== false) {
                        $isCpfDuplicado = true;
                    }
                }

                return response()->json([
                    'message' => 'O SGP recusou os dados.',
                    'details' => [$campoComErro => [$mensagemAmigavel]],
                    'is_cpf_duplicado' => $isCpfDuplicado
                ], 422); 
            }

        } catch (RequestException $e) {
            // SGP FORA DO AR OU TIMEOUT: Dispara Fallback para o WhatsApp no Frontend
            Log::error("SGP Fora do ar ou Lento ao receber pré-cadastro: " . $e->getMessage());

            return response()->json([
                'message' => 'Sistema de integração indisponível no momento.',
                'whatsapp_fallback' => true
            ], 503);

        } catch (\Exception $e) {
            // ERRO INESPERADO DO CONTROLADOR
            Log::critical("Erro Fatal no Controller de Pré-cadastro: " . $e->getMessage());

            return response()->json([
                'message' => 'Identificamos uma instabilidade temporária.', 
                'whatsapp_fallback' => true
            ], 500);
        }
    }
}