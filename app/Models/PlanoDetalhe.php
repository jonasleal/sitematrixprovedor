<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanoDetalhe extends Model
{
    use HasFactory;

    protected $table = 'plano_detalhes';

    protected $fillable = [
        'sgp_plano_id',
		'nome_personalizado',
		'preco_personalizado',
		'topicos_beneficios',
        'velocidade_down',
		'velocidade_up',
		'desconto',
		'ordem',
        'destaque',
		'ativo',
		'data_inicio',
		'data_fim',
		'ocultar_apos_vencimento'
    ];

    protected $casts = [
        'destaque' => 'boolean',
        'ativo' => 'boolean',
        'data_inicio' => 'datetime',
        'data_fim' => 'datetime',
    ];
}