@extends('layouts.site')

@section('title')
    @yield('code') - @yield('title_page') | Matrix Provedor
@endsection

@section('content')
<main class="hero-bg min-h-[85vh] flex-grow flex flex-col items-center justify-center pt-28 pb-16 px-4 sm:px-6 lg:px-8">
    
    <div class="bg-[#0a0f1d]/80 backdrop-blur-xl border border-white/10 shadow-[0_0_50px_rgba(0,0,0,0.5)] text-center max-w-lg w-full relative z-10 p-8 sm:p-12 rounded-3xl">
        
        <div class="mb-6">
            <span class="text-7xl sm:text-8xl font-black text-transparent bg-clip-text bg-gradient-to-r from-green-400 via-cyan-400 to-cyan-200 tracking-wider drop-shadow-lg">
                @yield('code')
            </span>
        </div>

        <h1 class="text-2xl sm:text-3xl font-extrabold text-white mb-4 leading-tight">
            @yield('headline')
        </h1>

        <p class="text-gray-300 text-base mb-10 leading-relaxed">
            @yield('message')
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <a href="/" class="w-full sm:w-auto px-6 py-3 rounded-full font-bold text-white bg-gradient-to-r from-green-500 to-cyan-600 hover:shadow-[0_0_20px_rgba(34,197,94,0.4)] hover:-translate-y-1 transition-all duration-300 flex justify-center items-center">
                Voltar para a Home
            </a>
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $configGlobal->whatsapp ?? '87999644914') }}" target="_blank" class="w-full sm:w-auto px-6 py-3 rounded-full font-bold text-gray-300 border border-white/20 hover:border-cyan-400 hover:text-cyan-400 hover:-translate-y-1 transition-all duration-300 flex justify-center items-center">
                Falar no Suporte
            </a>
        </div>

    </div>
</main>
@endsection