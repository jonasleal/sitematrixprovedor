<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DownloadLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'download_id',
        'plataforma',
        'link',
    ];

    // Relacionamento reverso: Um link pertence a um Download (App)
    public function download()
    {
        return $this->belongsTo(Download::class);
    }
}