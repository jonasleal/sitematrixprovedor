<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeadCobertura;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    /**
     * Lista os leads com filtros e métricas resumidas
     */
    public function index(Request $request)
    {
        $query = LeadCobertura::query();

        // 1. Filtro de Busca Textual Geral (Nome, WhatsApp, Endereço)
        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                  ->orWhere('whatsapp', 'like', "%{$busca}%")
                  ->orWhere('endereco_pesquisado', 'like', "%{$busca}%");
            });
        }

        // 2. Filtros por Coluna (Cidade, Bairro, Status)
        if ($request->filled('cidade')) {
            $query->where('cidade', $request->cidade);
        }

        if ($request->filled('bairro')) {
            $query->where('bairro', $request->bairro);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Resultado da consulta filtrada
        $leads = $query->orderBy('created_at', 'desc')->get();

        // Metrics resumidas do Topo (KPIs)
        $metricas = [
            'total'     => LeadCobertura::count(),
            'reprimido' => LeadCobertura::where('status', 'Demanda Reprimida')->count(),
            'estudo'    => LeadCobertura::where('status', 'Em Estudo')->count(),
            'aprovado'  => LeadCobertura::where('status', 'Projeto Aprovado')->count(),
            'liberado'  => LeadCobertura::where('status', 'Rede Liberada')->count(),
        ];

        // Listas distintas para popular os selects dos filtros
        $cidades = LeadCobertura::whereNotNull('cidade')->where('cidade', '!=', '')->distinct()->pluck('cidade');
        $bairros = LeadCobertura::whereNotNull('bairro')->where('bairro', '!=', '')->distinct()->pluck('bairro');

        return view('dashboard', compact('leads', 'metricas', 'cidades', 'bairros'));
    }

    /**
     * Atualiza o Status e Observações do Lead
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
            'observacoes' => 'nullable|string'
        ]);

        $lead = LeadCobertura::findOrFail($id);
        $lead->update([
            'status' => $request->status,
            'observacoes' => $request->observacoes
        ]);

        return back()->with('success', "Lead #{$lead->id} ({$lead->nome}) atualizado com sucesso!");
    }

    /**
     * Remove o Lead do Banco de Dados
     */
    public function destroy($id)
    {
        // Validação de segurança de permissão
        if (!auth()->user()->can('excluir cobertura') && auth()->id() !== 1) {
            return back()->with('error', 'Ação não autorizada. Você não tem permissão para excluir leads.');
        }

        $lead = LeadCobertura::findOrFail($id);
        $nomeLead = $lead->nome;
        $lead->delete();

        return back()->with('success', "O registro de '{$nomeLead}' foi excluído com sucesso.");
    }
}