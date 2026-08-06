<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pagina extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'slug',
        'conteudo',
        'template',
        'ativo'
    ];

    // Gera o slug automaticamente antes de salvar
    protected static function boot()
    {
        parent::boot();
        static::saving(function ($pagina) {
            if (empty($pagina->slug)) {
                $pagina->slug = Str::slug($pagina->titulo);
            }
        });
    }
}