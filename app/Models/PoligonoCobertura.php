<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoligonoCobertura extends Model
{
    use HasFactory;

    protected $table = 'poligonos_cobertura';

    protected $fillable = [
        'nome',
        'cor',
        'coordenadas',
        'ativo'
    ];

    // Diz ao Laravel que a coluna 'coordenadas' é um JSON/Array
    protected $casts = [
        'coordenadas' => 'array',
    ];
}