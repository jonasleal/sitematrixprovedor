<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\LeadCobertura; // Certifique-se de importar seu model de Lead
use Illuminate\Http\Client\RequestException;

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
            // INICIA TRANSAÇÃO NO BANCO DE DADOS
            DB::beginTransaction();

            $baseUrl = rtrim(env('SGP_API_URL'), '/');
            $app = env('SGP_APP_ID');
            $token = env('SGP_APP_TOKEN');

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

            // 3. SALVA O LEAD NO BANCO DE DADOS LOCAL (MATRIZ DE SEGURANÇA)
            // Mesmo se o SGP falhar, você não perde o contato do cliente!
            $lead = LeadCobertura::create([
                'nome' => $payload['nome'],
                'cpf' => $payload['cpfcnpj'],
                'celular' => $payload['celular'],
                'email' => $payload['email'],
                'logradouro' => $payload['logradouro'],
                'numero' => $payload['numero'],
                'bairro' => $payload['bairro'],
                'cidade' => $payload['cidade'],
                'estado' => $payload['uf'],
                'cep' => $payload['cep'],
                'plano_id' => $payload['plano_id'],
                'status_integracao' => 'pendente' // Indica que ainda vai tentar enviar
            ]);

            // 4. ENVIO PARA O SGP COM TIMEOUT
            // Timeout de 15 segundos. Mais que isso, a UX degrada e cortamos a requisição.
            $response = Http::timeout(15)->asForm()->post($baseUrl . '/api/precadastro/F/', $payload);

            if ($response->successful()) {
                // Atualiza o lead local informando que integrou com sucesso
                $lead->update(['status_integracao' => 'sucesso']);
                DB::commit(); // Confirma a gravação no banco de dados

                return response()->json(['message' => 'Pré-cadastro realizado com sucesso!'], 200);
            } else {
                // Se a API retornou um erro (ex: 400 Bad Request, CPF Duplicado)
                DB::rollBack(); // Desfaz a criação do lead local para não poluir sua base com CPFs inválidos

                $respostaSgp = $response->json();
                Log::error("Erro SGP Pré-cadastro: ", is_array($respostaSgp) ? $respostaSgp : [$response->body()]);

                $mensagemAmigavel = $respostaSgp['error'] ?? 'Erro desconhecido no SGP.';
                $campoComErro = 'geral';
                $isCpfDuplicado = false;

                // Inteligência de mapeamento de erros
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
            // FALHA DE TIMEOUT OU SGP FORA DO AR (API CAIU)
            // Aqui está a MÁGICA: Commitamos o lead local para você ligar depois, e avisamos o cliente que deu tudo certo!
            $lead->update(['status_integracao' => 'falha_api']);
            DB::commit();

            Log::error("SGP Fora do ar ou Lento ao receber pré-cadastro. Lead ID {$lead->id} salvo localmente.");

            // Retorna 200 para a UI. O cliente final não tem culpa que o SGP caiu. Ele recebe a festa de confete.
            return response()->json(['message' => 'Pré-cadastro recebido! Nossa equipe entrará em contato.'], 200);

        } catch (\Exception $e) {
            // FALHA GRAVE NO LARAVEL / BANCO DE DADOS (Erro 500 Interno)
            DB::rollBack();
            Log::critical("Erro Fatal no Controller de Pré-cadastro: " . $e->getMessage());

            // A UI exibe um toast de erro sem quebrar a tela branca
            return response()->json([
                'message' => 'Identificamos uma instabilidade. Por favor, tente novamente em instantes.', 
                'details' => []
            ], 500);
        }
    }
}