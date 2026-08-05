<section id="planos" class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative no-select">
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-white mb-2 text-glow">Escolha sua velocidade ideal</h2>
        <p class="text-gray-400">Instalação 100% grátis e roteador incluso nos planos de permanência de 12 meses.</p>
    </div>

    <div class="relative group">
        
        <button id="btn-prev" class="absolute left-2 md:-left-6 top-1/2 -translate-y-1/2 glass hover:bg-white/10 text-white p-3 rounded-full z-20 transition duration-300 opacity-0 pointer-events-none shadow-[0_0_15px_rgba(0,0,0,0.5)] border border-white/20">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
        </button>

        <button id="btn-next" class="absolute right-2 md:-right-6 top-1/2 -translate-y-1/2 glass hover:bg-white/10 text-white p-3 rounded-full z-20 transition duration-300 opacity-0 group-hover:opacity-100 shadow-[0_0_15px_rgba(0,0,0,0.5)] border border-white/20">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
        </button>

        <div id="planos-carousel" class="flex items-center overflow-x-auto snap-x snap-mandatory hide-scrollbar grab-scroll gap-8 pb-16 pt-20 px-4 md:px-8 w-full scroll-smooth">
            
            @if(count($planos) > 0)
                @foreach($planos as $index => $plano)
                    @php
                        // Conversão matemática direta do valor vindo do sistema (kbps)
                        $velDown = $plano['velocidade_down'] ?? 0;
                        $numero = '0';
                        $unidade = 'Mb/s';

                        if (is_numeric($velDown) && $velDown > 0) {
                            if ($velDown >= 1000000) {
                                // Exemplo: 1.000.000 kbps = 1 Gb/s
                                $numero = $velDown / 1000000;
                                $unidade = 'Gb/s';
                            } elseif ($velDown >= 1000) {
                                // Exemplo: 260.000 kbps = 260 Mb/s
                                $numero = floor($velDown / 1000);
                                $unidade = 'Mb/s';
                            } else {
                                // Valor já inserido em Mbps no Admin (ex: 350)
                                $numero = $velDown;
                                $unidade = 'Mb/s';
                            }
                        }

                        $isDestaque = $plano['destaque'] ?? false;
                    @endphp

                    <div class="snap-center shrink-0 w-80 md:w-96 glass rounded-2xl p-8 transition duration-300 transform relative {{ $isDestaque ? 'border-2 border-green-400 md:scale-105 shadow-[0_0_30px_rgba(0,199,169,0.3)] z-10' : 'border border-white/10 opacity-90 hover:opacity-100' }}" id="plano-card-{{ $index }}">
                        
                        @if($isDestaque)
                            <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-green-400 text-black px-4 py-1 rounded-full text-xs font-bold tracking-wide shadow-[0_0_15px_rgba(129,199,0,0.5)] z-20">
                                O MAIS ASSINADO
                            </div>
                        @endif

                        <h3 class="text-xl text-gray-300 font-semibold mb-2 text-center uppercase tracking-wide line-clamp-1" title="{{ $plano['nome'] }}">
                            {{ $plano['nome'] }}
                        </h3>
                        
                        <div class="flex items-baseline justify-center mb-4">
                            <span class="text-6xl font-extrabold text-white {{ $isDestaque ? 'text-glow' : '' }}">{{ $numero }}</span>
                            <span class="text-2xl text-green-400 ml-1 font-bold">{{ $unidade }}</span>
                        </div>
                        
                        <div class="text-center mb-6">
                            @if(!empty($plano['data_fim']))
                                <p class="text-xs text-pink-400 font-bold mb-1 uppercase tracking-wide">
                                    Válido até: {{ \Carbon\Carbon::parse($plano['data_fim'])->format('d/m/Y') }}
                                </p>
                            @endif

                            @if($plano['tem_desconto'])
                                <p class="text-gray-500 line-through text-sm">De: R$ {{ number_format($plano['valor_original'], 2, ',', '.') }}</p>
                            @endif
                            
                            <p class="text-4xl font-bold text-white {{ $isDestaque ? 'text-glow' : '' }}">
                                R$ {{ number_format($plano['valor_final'], 2, ',', '.') }}<span class="text-xl text-gray-400 font-normal">/mês</span>
                            </p>
                        </div>
                        
                        <ul class="space-y-4 mb-8 text-gray-300 text-sm min-h-[120px]">
                            @foreach($plano['beneficios'] as $beneficio)
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-400 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>{{ $beneficio }}</span>
                            </li>
                            @endforeach
                        </ul>
                        
                        <button onclick="escolherPlanoEPesquisar({{ $plano['id'] }})" class="w-full mt-auto {{ $isDestaque ? 'btn-accent text-white shadow-[0_0_15px_rgba(129,199,0,0.4)] hover:shadow-[0_0_25px_rgba(129,199,0,0.6)]' : 'bg-white/5 text-white border border-white/20 hover:bg-white hover:text-black' }} py-3 rounded-lg font-bold transition duration-300">
                            Assinar Agora
                        </button>
                    </div>
                @endforeach
            @else
                <div class="w-full text-center py-10">
                    <p class="text-gray-400">Nossos planos estão sendo atualizados no momento.</p>
                </div>
            @endif
        </div>
    </div>
</section>

<script>
// FUNÇÃO GLOBAL
function escolherPlanoEPesquisar(planoId) {
    sessionStorage.setItem('plano_escolhido_matrix', planoId);
    
    const secaoCobertura = document.getElementById('cobertura-noticias');
    if (secaoCobertura) {
        secaoCobertura.scrollIntoView({ behavior: 'smooth' });
    }
    
    setTimeout(() => {
        const inputEndereco = document.getElementById('input-endereco');
        if(inputEndereco) inputEndereco.focus();
    }, 800);
}

// MOTOR DO CARROSSEL
document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.getElementById('planos-carousel');
    const btnPrev = document.getElementById('btn-prev');
    const btnNext = document.getElementById('btn-next');
    const targetCard = document.getElementById('plano-card-{{ $indexDestaque ?? 0 }}');
    
    if (!carousel) return;

    if (targetCard) {
        carousel.classList.remove('scroll-smooth');
        const scrollPos = targetCard.offsetLeft - (carousel.clientWidth / 2) + (targetCard.clientWidth / 2);
        carousel.scrollLeft = scrollPos;
        setTimeout(() => carousel.classList.add('scroll-smooth'), 50);
    }

    function updateArrows() {
        if (carousel.scrollLeft <= 5) {
            btnPrev.style.opacity = '0';
            btnPrev.style.pointerEvents = 'none';
        } else {
            btnPrev.style.opacity = '1';
            btnPrev.style.pointerEvents = 'auto';
        }

        const maxScroll = carousel.scrollWidth - carousel.clientWidth;
        if (carousel.scrollLeft >= maxScroll - 5) {
            btnNext.style.opacity = '0';
            btnNext.style.pointerEvents = 'none';
        } else {
            btnNext.style.opacity = '1';
            btnNext.style.pointerEvents = 'auto';
        }
    }
    carousel.addEventListener('scroll', updateArrows);
    window.addEventListener('resize', updateArrows);
    setTimeout(updateArrows, 200);

    const getCardWidth = () => {
        const firstCard = carousel.querySelector('.snap-center');
        return firstCard ? firstCard.clientWidth + 32 : 350;
    };

    btnNext.addEventListener('click', () => carousel.scrollLeft += getCardWidth());
    btnPrev.addEventListener('click', () => carousel.scrollLeft -= getCardWidth());

    let isDown = false;
    let startX;
    let scrollLeft;

    carousel.addEventListener('mousedown', (e) => {
        isDown = true;
        carousel.classList.remove('scroll-smooth', 'snap-x', 'snap-mandatory');
        startX = e.pageX - carousel.offsetLeft;
        scrollLeft = carousel.scrollLeft;
    });

    const stopDragging = () => {
        if (!isDown) return;
        isDown = false;
        carousel.classList.add('scroll-smooth', 'snap-x', 'snap-mandatory');
    };

    carousel.addEventListener('mouseleave', stopDragging);
    carousel.addEventListener('mouseup', stopDragging);

    carousel.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - carousel.offsetLeft;
        const walk = (x - startX) * 1.5;
        carousel.scrollLeft = scrollLeft - walk;
    });
});
</script>