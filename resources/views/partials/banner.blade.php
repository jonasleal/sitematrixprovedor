<style> [x-cloak] { display: none !important; } </style>

@if(isset($banners) && $banners->count() > 0)
<section class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div x-data="{ 
            slide: 0, 
            total: {{ $banners->count() }}, 
            paused: false,
            timer: null,
            init() { 
                this.timer = setInterval(() => { 
                    if(!this.paused) this.next(); 
                }, 6000); 
            },
            next() { this.slide = (this.slide + 1) % this.total; },
            prev() { this.slide = (this.slide - 1 + this.total) % this.total; }
         }"
         @mouseenter="paused = true" 
         @mouseleave="paused = false"
         class="relative group">

        <div class="glass rounded-3xl overflow-hidden relative border border-green-400/30 h-[700px] md:h-[450px] shadow-2xl bg-gray-900">
            
            @foreach($banners as $banner)
                @php
                    $inverter = ($loop->index % 2 === 1);
                    $direcaoFlex = $inverter ? 'md:flex-row-reverse' : 'md:flex-row';

                    $temas = [
                        'green-cyan' => [
                            'badge' => 'from-green-400 to-cyan-500 text-gray-900 shadow-[0_0_10px_rgba(0,199,169,0.5)]',
                            'hover_btn' => 'hover:bg-green-400 hover:text-gray-900',
                        ],
                        'pink-purple' => [
                            'badge' => 'from-pink-500 to-purple-600 text-white shadow-[0_0_10px_rgba(236,72,153,0.5)]',
                            'hover_btn' => 'hover:bg-pink-500 hover:text-white',
                        ],
                        'orange-yellow' => [
                            'badge' => 'from-amber-400 to-orange-500 text-gray-900 shadow-[0_0_10px_rgba(245,158,11,0.5)]',
                            'hover_btn' => 'hover:bg-amber-400 hover:text-gray-900',
                        ],
                    ];
                    $estilo = array_key_exists($banner->tema_cor, $temas) ? $temas[$banner->tema_cor] : $temas['green-cyan'];

                    $prop = $banner->proporcao_imagem ?? '50';
                    $imgClass = $prop == '60' ? 'md:w-3/5' : ($prop == '40' ? 'md:w-2/5' : 'md:w-1/2');
                    $txtClass = $prop == '60' ? 'md:w-2/5' : ($prop == '40' ? 'md:w-3/5' : 'md:w-1/2');
                @endphp

                <div x-show="slide === {{ $loop->index }}"
                     x-cloak
                     x-transition:enter="transition ease-out duration-700 transform"
                     x-transition:enter-start="opacity-0 translate-x-8"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-500 transform absolute inset-0"
                     x-transition:leave-start="opacity-100 translate-x-0"
                     x-transition:leave-end="opacity-0 -translate-x-8"
                     class="absolute inset-0 w-full h-full flex flex-col {!! $direcaoFlex !!} items-stretch"> 
                     
                    <div class="h-1/2 md:h-full w-full {{ $txtClass }} p-8 md:p-12 text-left flex flex-col justify-center z-10 relative">
                        <span class="bg-gradient-to-r {{ $estilo['badge'] }} text-xs font-extrabold px-3 py-1 rounded-full uppercase tracking-wider mb-4 inline-block self-start z-10 w-max">
                            {{ $banner->categoria_tag ?: 'Destaque' }}
                        </span>
                        
                        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4 z-10 w-full">
                            {!! $banner->titulo_formatado !!}
                        </h2>
                        
                        <p class="text-gray-300 mb-6 text-sm md:text-base leading-relaxed z-10 w-full">
                            {!! $banner->descricao_formatada !!}
                        </p>
                        
                        @if($banner->link_destino)
                            <a href="{{ $banner->link_destino }}" class="bg-white text-gray-900 px-6 py-3 rounded-lg font-bold {{ $estilo['hover_btn'] }} transition duration-300 self-start text-center z-10 shadow-lg w-max">
                                {{ $banner->texto_botao ?: 'Saiba Mais' }}
                            </a>
                        @endif
                    </div>
                    
                    <div class="h-1/2 md:h-full w-full {{ $imgClass }} relative overflow-hidden bg-black flex-shrink-0">
                        <img src="{{ asset('storage/' . ($banner->caminho_imagem_mobile ?: $banner->caminho_imagem)) }}" 
                             alt="{{ strip_tags($banner->titulo) }}" 
                             style="object-position: {{ $banner->posicao_x ?? 50 }}% {{ $banner->posicao_y ?? 50 }}%; transform: scale({{ ($banner->zoom ?? 100) / 100 }});"
                             class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 ease-in-out md:hidden">

                        <img src="{{ asset('storage/' . $banner->caminho_imagem) }}" 
                             alt="{{ strip_tags($banner->titulo) }}" 
                             style="object-position: {{ $banner->posicao_x ?? 50 }}% {{ $banner->posicao_y ?? 50 }}%; transform: scale({{ ($banner->zoom ?? 100) / 100 }});"
                             class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 ease-in-out hidden md:block">
                        
                        <div class="absolute inset-0 bg-gradient-to-r {{ $inverter ? 'from-transparent to-gray-900/80' : 'from-gray-900/80 to-transparent' }} md:block hidden"></div>
                        <div class="absolute inset-0 bg-gradient-to-b from-gray-900 via-transparent to-transparent md:hidden block"></div>
                    </div>

                </div>
            @endforeach
        </div>

        @if($banners->count() > 1)
            <button @click="prev()" class="absolute left-2 md:left-4 top-1/2 -translate-y-1/2 bg-black/60 hover:bg-green-400 hover:text-gray-900 text-white p-2 md:p-3 rounded-full border border-white/20 transition opacity-0 group-hover:opacity-100 z-20">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <button @click="next()" class="absolute right-2 md:right-4 top-1/2 -translate-y-1/2 bg-black/60 hover:bg-green-400 hover:text-gray-900 text-white p-2 md:p-3 rounded-full border border-white/20 transition opacity-0 group-hover:opacity-100 z-20">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
            </button>
            <div class="absolute bottom-4 left-0 right-0 z-20 flex justify-center space-x-2">
                @foreach($banners as $banner)
                    <button @click="slide = {{ $loop->index }}" 
                            :class="slide === {{ $loop->index }} ? 'bg-green-400 w-8' : 'bg-white/40 w-3 hover:bg-white'" 
                            class="h-2.5 rounded-full transition-all duration-300">
                    </button>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endif