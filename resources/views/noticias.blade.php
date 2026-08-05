@extends('layouts.site')

@section('content')
<main class="flex-grow pt-32 pb-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
    <div class="text-center mb-16">
        <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4 text-glow">Matrix Informa</h1>
        <p class="text-xl text-gray-300">Fique por dentro das novidades, expansões e comunicados oficiais.</p>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($noticias as $noticia)
            {{-- CARD 100% CLICÁVEL --}}
            <a href="{{ url('/noticia/' . $noticia->slug) }}" class="glass rounded-3xl overflow-hidden border border-white/10 hover:-translate-y-2 transition duration-300 group flex flex-col h-full cursor-pointer">
                
                @if($noticia->imagem_destaque)
                    <div class="h-48 bg-gray-800 relative overflow-hidden">
                        <img src="{{ asset('storage/' . $noticia->imagem_destaque) }}" alt="{{ $noticia->titulo }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        <div class="absolute inset-0 bg-black/20 group-hover:bg-transparent transition duration-300"></div>
                    </div>
                @else
                    <div class="h-48 bg-gradient-to-r from-green-500 to-cyan-500 relative overflow-hidden">
                        <div class="absolute inset-0 bg-black/20 group-hover:bg-transparent transition duration-300"></div>
                    </div>
                @endif

                <div class="p-8 flex flex-col flex-grow">
                    <div class="flex justify-between items-center mb-4">
                        <span class="bg-green-400/20 text-green-400 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider border border-green-400/30">
                            {{ $noticia->tag ?? 'EXPANSÃO' }}
                        </span>
                        <span class="text-gray-400 text-sm">{{ $noticia->data_curta }}</span>
                    </div>
                    
                    <h2 class="text-2xl font-bold text-white mb-4 group-hover:text-green-400 transition">{{ $noticia->titulo }}</h2>
                    <p class="text-gray-300 mb-6 flex-grow line-clamp-3">{{ $noticia->resumo }}</p>
                    
                    <span class="text-cyan-400 font-bold group-hover:text-white transition inline-flex items-center mt-auto">
                        Ler Completa 
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </span>
                </div>
            </a>
        @empty
            <div class="col-span-full text-center py-12 glass rounded-2xl">
                <p class="text-gray-400 text-lg">Nenhuma notícia publicada no momento. Volte em breve!</p>
            </div>
        @endforelse
    </div>

    <div class="mt-12">
        {{ $noticias->links() }}
    </div>
</main>
@endsection