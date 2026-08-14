<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descricao',
        'categoria',
        'imagem_path',
        'tipo_link',
        'arquivo_path',
        'versao',
        'ordem',
        'ativo',
    ];

    /**
     * Retorna todos os links (plataformas) associados a este aplicativo.
     */
    public function links()
    {
        return $this->hasMany(DownloadLink::class);
    }
}