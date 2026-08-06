@extends('layouts.site')

@section('title', 'Central de Downloads - Matrix Provedor')

@section('content')
<main class="hero-bg min-h-[85vh] flex-grow flex flex-col justify-center pt-28 pb-16 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto w-full">
        
        <div class="text-center mb-12">
            <h1 class="text-4xl sm:text-5xl font-extrabold text-white mb-4 text-glow">Central de Downloads</h1>
            <p class="text-gray-300 text-lg max-w-2xl mx-auto">Aceda e baixe aqui os nossos aplicativos oficiais, contratos de prestação de serviços, manuais técnicos e guias rápidos.</p>
            <div class="w-24 h-1 bg-gradient-to-r from-green-400 to-cyan-500 mx-auto rounded-full mt-6"></div>
        </div>

        @if($downloads->isEmpty())
            <div class="glass p-8 text-center rounded-2xl max-w-lg mx-auto">
                <p class="text-gray-300">Nenhum ficheiro disponível para download de momento.</p>
            </div>
        @else
            <div class="space-y-12">
                @foreach($downloads as $categoria => $itens)
                    <div>
                        <h2 class="text-2xl font-bold text-cyan-400 uppercase tracking-wider mb-6 flex items-center border-b border-white/10 pb-2">
                            @if($categoria === 'aplicativo')
                                <svg class="w-6 h-6 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                Aplicativos Oficiais
                            @elseif($categoria === 'contrato')
                                <svg class="w-6 h-6 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Contratos e Termos
                            @elseif($categoria === 'manual')
                                <svg class="w-6 h-6 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                Manuais e Guias Técnicos
                            @else
                                <svg class="w-6 h-6 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Outros Documentos
                            @endif
                        </h2>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            @foreach($itens as $item)
                                <div class="glass p-6 rounded-2xl border border-white/10 hover:border-green-400/50 transition-all duration-300 flex flex-col sm:flex-row gap-6">
                                    
                                    @if($item->imagem_path)
                                        <div class="shrink-0 mx-auto sm:mx-0">
                                            <img src="{{ asset('storage/' . $item->imagem_path) }}" alt="{{ $item->titulo }}" class="w-24 h-24 sm:w-32 sm:h-32 object-cover rounded-2xl shadow-[0_0_15px_rgba(34,197,94,0.2)] border border-white/10 bg-white/5">
                                        </div>
                                    @endif

                                    <div class="flex flex-col flex-grow">
                                        <div class="mb-4 text-center sm:text-left">
                                            <div class="flex flex-col sm:flex-row justify-between items-center sm:items-start mb-2 gap-2">
                                                <h3 class="text-xl font-bold text-white leading-tight">{{ $item->titulo }}</h3>
                                                @if($item->versao)
                                                    <span class="px-2.5 py-1 text-xs font-semibold bg-cyan-500/20 text-cyan-300 rounded-full border border-cyan-500/30 whitespace-nowrap">
                                                        {{ $item->versao }}
                                                    </span>
                                                @endif
                                            </div>
                                            @if($item->descricao)
                                                <p class="text-gray-300 text-sm leading-relaxed">{{ $item->descricao }}</p>
                                            @endif
                                        </div>

                                        <div class="pt-4 mt-auto border-t border-white/5">
                                            @php
                                                $url = $item->tipo_link === 'upload' ? asset('storage/' . $item->arquivo_path) : $item->arquivo_path;
                                            @endphp
                                            <a href="{{ $url }}" target="_blank" class="inline-flex items-center justify-center w-full px-5 py-3 text-sm font-bold text-white bg-gradient-to-r from-green-500 to-cyan-600 rounded-xl hover:shadow-[0_0_20px_rgba(34,197,94,0.4)] transition-all duration-300 hover:-translate-y-1">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                Baixar Ficheiro
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</main>
@endsection