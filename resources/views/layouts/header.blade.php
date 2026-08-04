<header class="glass fixed w-full top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-20">
        <div class="flex-shrink-0">
            <a href="/">
                <img src="/assets/logo-matrix.png" alt="Logo Matrix" class="h-14">
            </a>
        </div>
        
        <nav class="hidden md:flex space-x-8 text-sm font-semibold tracking-wide">
            <!-- Usa a função do Laravel para acender o menu de acordo com a página atual -->
            <a href="/" class="hover:text-green-400 transition {{ request()->is('/') ? 'text-white text-glow' : 'text-gray-300' }}">INÍCIO</a>
            <a href="/sobre" class="hover:text-green-400 transition {{ request()->is('sobre') ? 'text-white text-glow' : 'text-gray-300' }}">SOBRE NÓS</a>
            <a href="/#planos" class="text-gray-300 hover:text-green-400 transition">PLANOS</a>
            <a href="/#cobertura-noticias" class="text-gray-300 hover:text-green-400 transition">COBERTURA</a>
            <a href="/planos-disponiveis" class="hover:text-green-400 transition {{ request()->is('planos-disponiveis') ? 'text-white text-glow' : 'text-gray-300' }}">PRÉ-CADASTRO</a>
			<a href="/noticias" class="hover:text-green-400 transition {{ request()->is('planos-disponiveis') ? 'text-white text-glow' : 'text-gray-300' }}">NOTÍCIAS</a>
        </nav>
        
        <div>
            <!-- Coloque aqui a URL real da Central do Assinante do SGP -->
            <a href="{{ $configGlobal->link_central_assinante ?? '#' }}" target="_blank" class="border border-green-400 text-green-400 hover:bg-green-400 hover:text-gray-900 px-5 py-2 rounded-full font-bold transition duration-300">
                CENTRAL DO CLIENTE
            </a>
        </div>
    </div>
</header>