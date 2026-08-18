<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadCobertura extends Model
{
    use HasFactory;

    protected $table = 'lead_coberturas';

    // Todos os campos reais e úteis que ficaram na tabela após a faxina
    protected $fillable = [
        'pronome',
        'nome',
        'whatsapp',
        'bairro',
        'cidade',
        'estado',
        'lat',
        'lng',
        'endereco_pesquisado',
        'status',
        'observacoes'
    ];

    /**
     * ========================================================
     * ESCOPOS DE BUSCA (A Mágica do Motor de Filtros do CRM)
     * ========================================================
     */
    
    // Puxa leads que estão puramente em demanda reprimida
    public function scopeReprimidos($query)
    {
        return $query->where('status', 'Demanda Reprimida');
    }

    // Puxa leads que a engenharia já está olhando
    public function scopeEmEstudo($query)
    {
        return $query->whereIn('status', ['Em Estudo', 'Projeto Aprovado']);
    }

    // Puxa leads que viraram cliente e rede foi ativada
    public function scopeConvertidos($query)
    {
        return $query->where('status', 'Rede Liberada');
    }
}