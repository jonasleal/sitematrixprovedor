<!-- Cabeçalho Fixo com Efeito Glass Original -->
<header x-data="{ mobileMenuOpen: false }" class="glass fixed w-full top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-20">
        
        <!-- Logo Matrix Original -->
        <div class="flex-shrink-0">
            <a href="/">
                <img src="/assets/logo-matrix.png" alt="Logo Matrix" class="h-14">
            </a>
        </div>
        
        <!-- Menu Desktop Original (Centralizado) -->
        <nav class="hidden md:flex space-x-8 text-sm font-semibold tracking-wide items-center">
            <a href="/" class="hover:text-green-400 transition {{ request()->is('/') ? 'text-white text-glow' : 'text-gray-300' }}">INÍCIO</a>
            
            <!-- Novo Dropdown Institucional com estética original -->
            <div x-data="{ dropdownOpen: false }" @mouseenter="dropdownOpen = true" @mouseleave="dropdownOpen = false" class="relative py-4">
                <button class="flex items-center hover:text-green-400 transition uppercase focus:outline-none {{ request()->is('p/*') ? 'text-white text-glow' : 'text-gray-300' }}">
                    INSTITUCIONAL
                    <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                
                <!-- Painel Suspenso -->
                <div x-show="dropdownOpen" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-2"
                     class="absolute left-0 mt-0 w-56 rounded-xl shadow-2xl bg-[#0a0f1d]/95 backdrop-blur-xl border border-white/10 overflow-hidden z-50"
                     style="display: none;">
                    <div class="py-2">
                        <a href="/p/sobre" class="block px-4 py-3 text-sm text-gray-300 hover:text-green-400 hover:bg-white/5 transition-colors uppercase tracking-wide">NOSSA HISTÓRIA</a>
                        <a href="/p/downloads" class="block px-4 py-3 text-sm text-gray-300 hover:text-green-400 hover:bg-white/5 transition-colors uppercase tracking-wide">DOWNLOADS</a>
                    </div>
                </div>
            </div>

            <a href="/#planos" class="text-gray-300 hover:text-green-400 transition">PLANOS</a>
            <a href="/#cobertura-noticias" class="text-gray-300 hover:text-green-400 transition">COBERTURA</a>
            <a href="/planos-disponiveis" class="hover:text-green-400 transition {{ request()->is('planos-disponiveis') ? 'text-white text-glow' : 'text-gray-300' }}">PRÉ-CADASTRO</a>
            <a href="/noticias" class="hover:text-green-400 transition {{ request()->is('noticias') ? 'text-white text-glow' : 'text-gray-300' }}">NOTÍCIAS</a>
        </nav>
        
        <!-- Bloco Direita: Botão Original (Desktop) e Hamburger (Mobile) -->
        <div class="flex items-center">
            <!-- Variavel para Central do Assinante do SGP (Escondido no Mobile) -->
            <a href="{{ $configGlobal->link_central_assinante ?? '#' }}" target="_blank" class="hidden md:inline-block border border-green-400 text-green-400 hover:bg-green-400 hover:text-gray-900 px-5 py-2 rounded-full font-bold transition duration-300 text-sm tracking-wide">
                CENTRAL DO CLIENTE
            </a>

            <!-- Botão Hamburger para telemóveis -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-green-400 hover:text-white focus:outline-none ml-4 p-2">
                <svg x-show="!mobileMenuOpen" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="mobileMenuOpen" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Menu Mobile Retrátil (Com a estética Glass) -->
    <div x-show="mobileMenuOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-4"
         class="md:hidden absolute top-20 left-0 w-full glass border-b border-white/10 shadow-2xl"
         style="display: none;">
        
        <div class="px-4 pt-2 pb-6 space-y-2 flex flex-col">
            <a href="/" class="block px-4 py-3 rounded-lg text-sm font-semibold tracking-wide text-gray-300 hover:text-green-400 hover:bg-white/5 uppercase">INÍCIO</a>
            
            <!-- Dropdown Mobile Institucional -->
            <div x-data="{ mobileDropdownOpen: false }" class="space-y-1">
                <button @click="mobileDropdownOpen = !mobileDropdownOpen" class="w-full flex justify-between items-center px-4 py-3 rounded-lg text-sm font-semibold tracking-wide text-gray-300 hover:text-green-400 hover:bg-white/5 uppercase">
                    INSTITUCIONAL
                    <svg :class="{'rotate-180': mobileDropdownOpen}" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="mobileDropdownOpen" class="pl-8 pr-4 py-2 space-y-2 border-l-2 border-green-400/50 ml-4">
                    <a href="/p/sobre" class="block py-2 text-sm font-semibold tracking-wide text-gray-400 hover:text-green-400 uppercase">NOSSA HISTÓRIA</a>
                    <a href="/p/downloads" class="block py-2 text-sm font-semibold tracking-wide text-gray-400 hover:text-green-400 uppercase">DOWNLOADS</a>
                </div>
            </div>

            <a href="/#planos" class="block px-4 py-3 rounded-lg text-sm font-semibold tracking-wide text-gray-300 hover:text-green-400 hover:bg-white/5 uppercase">PLANOS</a>
            <a href="/#cobertura-noticias" class="block px-4 py-3 rounded-lg text-sm font-semibold tracking-wide text-gray-300 hover:text-green-400 hover:bg-white/5 uppercase">COBERTURA</a>
            <a href="/planos-disponiveis" class="block px-4 py-3 rounded-lg text-sm font-semibold tracking-wide text-gray-300 hover:text-green-400 hover:bg-white/5 uppercase">PRÉ-CADASTRO</a>
            <a href="/noticias" class="block px-4 py-3 rounded-lg text-sm font-semibold tracking-wide text-gray-300 hover:text-green-400 hover:bg-white/5 uppercase">NOTÍCIAS</a>
            
            <div class="pt-4 pb-2 px-4">
                <a href="{{ $configGlobal->link_central_assinante ?? '#' }}" target="_blank" class="block w-full text-center border border-green-400 text-green-400 hover:bg-green-400 hover:text-gray-900 px-5 py-3 rounded-full font-bold transition duration-300 text-sm tracking-wide">
                    CENTRAL DO CLIENTE
                </a>
            </div>
        </div>
    </div>
</header>