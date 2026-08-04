@if(isset($campanhas) && count($campanhas) > 0)
<section class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <div class="relative overflow-hidden rounded-3xl border border-green-400/30 glass">
        
        <div id="campanhas-track" class="flex transition-transform duration-700 ease-in-out w-full">
            
            @foreach($campanhas as $campanha)
            <div class="w-full flex-shrink-0 flex flex-col md:flex-row items-stretch">
                
                <div class="md:w-1/2 p-8 md:p-12 text-left flex flex-col justify-center">
                    <span class="bg-gradient-to-r from-pink-500 to-purple-500 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-4 self-start">
                        {{ $campanha->tag }}
                    </span>
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                        {{ $campanha->titulo }}
                    </h2>
                    <p class="text-gray-300 mb-6">
                        {{ $campanha->descricao }}
                    </p>
                    <a href="{{ $campanha->link }}" class="bg-white text-gray-900 px-6 py-3 rounded-lg font-bold hover:bg-gray-200 transition self-start shadow-lg">
                        {{ $campanha->texto_botao }}
                    </a>
                </div>
                
                <div class="md:w-1/2 w-full h-64 md:h-auto relative bg-black/80">
                    @if($campanha->imagem_desktop)
                        <img src="{{ asset('storage/' . $campanha->imagem_desktop) }}" alt="Campanha Desktop" class="hidden md:block w-full h-full object-cover absolute inset-0">
                    @endif
                    
                    @if($campanha->imagem_mobile)
                        <img src="{{ asset('storage/' . $campanha->imagem_mobile) }}" alt="Campanha Mobile" class="block md:hidden w-full h-full object-cover absolute inset-0">
                    @endif
                    
                    @if(!$campanha->imagem_desktop && !$campanha->imagem_mobile)
                        <div class="w-full h-full flex flex-col items-center justify-center text-center absolute inset-0 border-4 border-dashed border-cyan-400/30 p-8">
                            <svg class="w-16 h-16 text-cyan-400 mb-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            <span class="text-cyan-400 font-bold">NOVIDADE MATRIX</span>
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
            
        </div>
        
        @if(count($campanhas) > 1)
        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
            @foreach($campanhas as $index => $campanha)
            <button onclick="mudarCampanha({{ $index }})" id="dot-{{ $index }}" class="w-3 h-3 rounded-full bg-white/30 transition-all duration-300 hover:bg-white dot-indicador"></button>
            @endforeach
        </div>
        @endif

    </div>
</section>

@if(count($campanhas) > 1)
<script>
    let campanhaAtual = 0;
    const totalCampanhas = {{ count($campanhas) }};
    const track = document.getElementById('campanhas-track');
    
    function mudarCampanha(index) {
        campanhaAtual = index;
        track.style.transform = `translateX(-${campanhaAtual * 100}%)`;
        
        document.querySelectorAll('.dot-indicador').forEach((dot, i) => {
            if(i === index) {
                dot.classList.remove('bg-white/30');
                dot.classList.add('bg-green-400', 'w-6');
            } else {
                dot.classList.add('bg-white/30');
                dot.classList.remove('bg-green-400', 'w-6');
            }
        });
    }
    
    mudarCampanha(0);
    setInterval(() => {
        let proximo = campanhaAtual + 1;
        if(proximo >= totalCampanhas) proximo = 0;
        mudarCampanha(proximo);
    }, 6000);
</script>
@endif
@endif