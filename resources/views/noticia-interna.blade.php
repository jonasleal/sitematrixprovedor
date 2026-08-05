@extends('layouts.site')

@section('content')
<main class="flex-grow pt-32 pb-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
    
    <a href="/noticias" class="text-gray-400 hover:text-green-400 transition inline-flex items-center mb-8">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Voltar para notícias
    </a>

    <article class="glass p-8 md:p-12 rounded-3xl border border-white/10 relative">
        <div class="flex items-center gap-3 mb-4">
            <span class="bg-green-400/20 text-green-400 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider border border-green-400/30">
                {{ $noticia->tag ?? 'INFORMATIVO' }}
            </span>
            <span class="text-sm text-gray-400 font-medium">
                {{ $noticia->data_formatada }}
            </span>
        </div>
        
        <h1 class="text-3xl md:text-5xl font-extrabold text-white mb-6 leading-tight text-glow">
            {{ $noticia->titulo }}
        </h1>

        @if($noticia->imagem_destaque)
            <div class="w-full h-64 md:h-96 rounded-2xl overflow-hidden mb-8 border border-white/10">
                <img src="{{ asset('storage/' . $noticia->imagem_destaque) }}" alt="{{ $noticia->titulo }}" class="w-full h-full object-cover">
            </div>
        @endif

        {{-- O CONTEÚDO RICO (O Escudo de Formatação) --}}
        <div class="conteudo-rico">
            {!! $noticia->conteudo !!}
        </div>
    </article>

    {{-- CSS Dedicado para renderizar o HTML do TinyMCE de forma bonita e legível no Dark Mode --}}
    <style>
        .conteudo-rico {
            color: #d1d5db; /* gray-300 */
            font-size: 1.125rem; /* text-lg */
            line-height: 1.75;
        }
        .conteudo-rico h2 { font-size: 2rem; color: #fff; font-weight: bold; margin-top: 2rem; margin-bottom: 1rem; }
        .conteudo-rico h3 { font-size: 1.5rem; color: #fff; font-weight: bold; margin-top: 1.5rem; margin-bottom: 0.75rem; }
        .conteudo-rico p { margin-bottom: 1.25rem; }
        .conteudo-rico a { color: #22c55e; text-decoration: underline; font-weight: 600; }
        .conteudo-rico a:hover { color: #4ade80; }
        .conteudo-rico ul { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1.25rem; }
        .conteudo-rico ol { list-style-type: decimal; padding-left: 1.5rem; margin-bottom: 1.25rem; }
        .conteudo-rico li { margin-bottom: 0.5rem; }
        .conteudo-rico strong, .conteudo-rico b { color: #fff; font-weight: bold; }
        .conteudo-rico blockquote { border-left: 4px solid #22c55e; padding-left: 1rem; font-style: italic; color: #9ca3af; }
        .conteudo-rico img { max-width: 100%; height: auto; border-radius: 0.75rem; margin: 2rem 0; }
    </style>
</main>
@endsection