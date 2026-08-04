<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracao extends Model
{
    use HasFactory;
    protected $table = 'configuracoes';
    protected $guarded = []; // Libera a edição de todos os campos
}