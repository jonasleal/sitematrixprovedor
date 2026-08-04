<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadCobertura extends Model
{
    use HasFactory;

    // Atualizado para bater com a migração
    protected $table = 'lead_coberturas';

    protected $fillable = [
        'pronome',
        'nome',
        'whatsapp',
        'endereco_pesquisado',
        'status'
    ];
}