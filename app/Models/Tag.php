<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'tags';
    protected $fillable = ['nome', 'cor_fundo', 'cor_texto', 'tipo'];


    public function noticias()
    {
        return $this->hasMany(Noticia::class, 'tag_id');
    }
}