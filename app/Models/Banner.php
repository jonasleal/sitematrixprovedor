<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'data_inicio' => 'datetime',
        'data_fim'    => 'datetime',
        'ativo'       => 'boolean',
        'inverter_posicao' => 'boolean',
    ];

    // Converte [destaque]Palavra[/destaque] no gradiente correto de acordo com o tema selecionado
    // MOTOR DE SHORTCODES (Transforma marcações em HTML seguro)
    private function parseShortcodes($texto, $isTitulo = false)
    {
        if (empty($texto)) return '';

        // Escapa scripts maliciosos, mas preserva a quebra de linha
        $texto = nl2br(e($texto));

        // 1. Destaque Neon (Gradiente)
        $gradients = [
            'green-cyan'    => 'from-green-400 to-cyan-400',
            'pink-purple'   => 'from-pink-400 to-purple-400',
            'orange-yellow' => 'from-amber-300 to-orange-400',
        ];
        $grad = $gradients[$this->tema_cor] ?? $gradients['green-cyan'];
        
        $pesoFonte = $isTitulo ? 'font-extrabold' : 'font-bold';
        
        $texto = preg_replace(
            '/\[destaque\](.*?)\[\/destaque\]/is', 
            '<span class="text-transparent bg-clip-text bg-gradient-to-r ' . $grad . ' ' . $pesoFonte . '">$1</span>', 
            $texto
        );

        // 2. Alinhamentos
        $texto = preg_replace('/\[centro\](.*?)\[\/centro\]/is', '<div class="text-center w-full block">$1</div>', $texto);
        $texto = preg_replace('/\[direita\](.*?)\[\/direita\]/is', '<div class="text-right w-full block">$1</div>', $texto);

        // 3. Formatação de Texto Básica
        $texto = preg_replace('/\[negrito\](.*?)\[\/negrito\]/is', '<strong class="font-black">$1</strong>', $texto);
        $texto = preg_replace('/\*\*(.*?)\*\*/is', '<strong class="font-black">$1</strong>', $texto); // Suporta Markdown **negrito** também
        $texto = preg_replace('/\[riscado\](.*?)\[\/riscado\]/is', '<del class="opacity-70">$1</del>', $texto);
        $texto = preg_replace('/\[sublinhado\](.*?)\[\/sublinhado\]/is', '<u class="underline decoration-2 underline-offset-4">$1</u>', $texto);

        return $texto;
    }

    public function getTituloFormatadoAttribute()
    {
        return $this->parseShortcodes($this->titulo, true);
    }

    public function getDescricaoFormatadaAttribute()
    {
        return $this->parseShortcodes($this->descricao, false);
    }
    public function tag()
    {
        return $this->belongsTo(\App\Models\Tag::class, 'tag_id');
    }
}