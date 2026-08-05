<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Noticia extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'slug',
        'resumo',
        'conteudo',
        'imagem_destaque',
        'data_publicacao',
        'tag',
        'ativo'
    ];

    protected $casts = [
        'data_publicacao' => 'datetime',
        'ativo' => 'boolean',
    ];

    // Data por extenso em Português (ex: 05 de Agosto, 2026)
    public function getDataFormatadaAttribute()
    {
        $data = $this->data_publicacao ?? $this->created_at;
        return Carbon::parse($data)->locale('pt_BR')->translatedFormat('d \d\e F, Y');
    }

    // Data curta (ex: 05/08/2026)
    public function getDataCurtaAttribute()
    {
        $data = $this->data_publicacao ?? $this->created_at;
        return Carbon::parse($data)->format('d/m/Y');
    }
}