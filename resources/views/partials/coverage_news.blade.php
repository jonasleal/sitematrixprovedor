<style>
    [x-cloak] { display: none !important; }
    .pac-container {
        background-color: rgba(17, 24, 39, 0.95) !important;
        backdrop-filter: blur(10px) !important;
        border: 1px solid rgba(55, 65, 81, 0.5) !important;
        border-radius: 0.75rem !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5) !important;
        font-family: 'Inter', sans-serif !important;
        margin-top: 5px !important;
    }
    .pac-item {
        border-top: 1px solid rgba(55, 65, 81, 0.5) !important;
        padding: 12px 15px !important;
        color: #9ca3af !important;
        cursor: pointer !important;
    }
    .pac-item:hover, .pac-item-selected {
        background-color: rgba(31, 41, 55, 0.8) !important;
    }
    .pac-item-query {
        color: #22d3ee !important;
        font-weight: 700 !important;
        font-size: 1rem !important;
    }
    .pac-matched {
        color: #34d399 !important;
    }
    .pac-logo::after, .pac-container::after {
        display: none !important;
    }
</style>

<section id="cobertura" class="py-16 md:py-24 relative" x-data="mapaCobertura()">
    
    <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-1/4 left-0 w-96 h-96 bg-cyan-500/10 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-green-500/10 rounded-full blur-[120px]"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-8 items-start">
            
            <div class="flex flex-col relative bg-gray-800/60 backdrop-blur-md border border-gray-700/50 rounded-3xl p-6 lg:p-8 shadow-2xl lg:sticky lg:top-24 h-fit">
                
                <div class="mb-6">
                    <span class="text-green-400 font-extrabold tracking-wider uppercase text-xs drop-shadow-sm">Viabilidade Técnica</span>
                    <h2 class="text-3xl md:text-4xl font-black text-white mt-1 tracking-tight">
                        Consulte sua <span class="text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-cyan-500">Cobertura</span>
                    </h2>
                </div>

                <div class="bg-cyan-900/20 border border-cyan-500/30 rounded-xl p-4 flex items-start gap-3 mb-8">
                    <div class="mt-0.5 bg-cyan-500/20 p-2 rounded-full text-cyan-400 shadow-[0_0_10px_rgba(6,182,212,0.2)]">
                        <i class="fas fa-search-location"></i>
                    </div>
                    <p class="text-sm text-cyan-50 leading-relaxed font-medium">
                        Pesquise pelo <strong>nome da sua rua</strong> ou digite os primeiros dígitos do seu <strong>CEP</strong>. Se preferir, utilize o nosso mapa interativo para encontrar sua casa!
                    </p>
                </div>

                <form @submit.prevent="verificarCobertura" class="space-y-4 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="relative md:col-span-2">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                                <i class="fas fa-map-marker-alt text-gray-400"></i>
                            </div>
                            <input type="text" id="address-input" x-model="endereco" placeholder="Digite CEP ou a Rua" class="w-full pl-11 py-4 rounded-xl bg-gray-900/60 border border-gray-700 text-white placeholder-gray-500 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition shadow-sm text-sm font-medium">
                        </div>

                        <div class="relative md:col-span-1">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                                <i class="fas fa-home text-gray-400 text-xs"></i>
                            </div>
                            <input type="text" id="numero-casa-input" x-model="numero_casa" placeholder="Nº da casa" class="w-full pl-10 py-4 rounded-xl bg-gray-900/60 border border-gray-700 text-white placeholder-gray-500 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition shadow-sm text-sm font-medium">
                        </div>
                    </div>

                    <button type="submit" id="btn-verificar-cobertura" :disabled="buscando" class="w-full py-4 px-4 bg-gradient-to-r from-green-400 to-cyan-500 hover:from-green-500 hover:to-cyan-600 text-gray-900 font-extrabold rounded-xl shadow-[0_0_20px_rgba(6,182,212,0.4)] transform transition hover:-translate-y-0.5 flex items-center justify-center gap-2 disabled:opacity-70 disabled:hover:translate-y-0">
                        <i class="fas fa-search" x-show="!buscando"></i>
                        <i class="fas fa-spinner fa-spin" x-show="buscando"></i>
                        <span x-text="buscando ? 'Analisando Malha de Rede...' : 'Verificar Disponibilidade'"></span>
                    </button>
                </form>

                <div class="relative flex py-2 items-center">
                    <div class="flex-grow border-t border-gray-700"></div>
                    <span class="flex-shrink-0 mx-4 text-gray-500 text-xs font-bold uppercase tracking-wider">OU</span>
                    <div class="flex-grow border-t border-gray-700"></div>
                </div>

                <button type="button" @click="abrirModalMapa()" class="mt-2 w-full py-4 px-4 bg-gray-700/50 hover:bg-gray-700 text-white font-extrabold rounded-xl border border-gray-600 shadow-sm transition flex items-center justify-center gap-2 group">
                    <i class="fas fa-map-marked-alt text-cyan-400 group-hover:scale-110 transition"></i> 
                    Localizar no Mapa Interativo
                </button>
            </div>

            <div class="flex flex-col space-y-6">
                <div>
                    <h3 class="text-2xl font-bold text-white drop-shadow-md">Últimas Atualizações</h3>
                    <p class="text-gray-300 mt-1 text-sm">Acompanhe as novidades e expansões da nossa rede.</p>
                </div>

                <div class="space-y-4">
                    @forelse($noticias as $noticia)
                        <a href="{{ route('noticia.show', $noticia->slug) }}" class="group flex bg-gray-800/40 backdrop-blur-sm rounded-2xl border border-gray-700/50 hover:border-cyan-500/50 hover:bg-gray-800/60 transition duration-300 overflow-hidden shadow-lg h-32">
                            <div class="w-1/3 sm:w-32 relative overflow-hidden flex-shrink-0">
                                <img src="{{ Storage::url($noticia->imagem_destaque) }}" alt="{{ $noticia->titulo }}" class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500">
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent to-gray-900/60 sm:hidden"></div>
                            </div>
                            <div class="p-4 flex flex-col justify-center flex-1 relative z-10">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[10px] font-bold text-cyan-400 uppercase tracking-wider">{{ $noticia->tag->nome ?? 'Novidade' }}</span>
                                    <span class="text-gray-500">•</span>
                                    <span class="text-[10px] text-gray-400"><i class="far fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::parse($noticia->data_publicacao)->format('d/m/Y') }}</span>
                                </div>
                                <h4 class="text-sm sm:text-base font-bold text-white leading-tight group-hover:text-green-400 transition line-clamp-2 mb-1">
                                    {{ $noticia->titulo }}
                                </h4>
                                <p class="text-gray-400 text-xs line-clamp-1">
                                    {{ $noticia->resumo }}
                                </p>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-12 bg-gray-800/40 backdrop-blur-sm rounded-2xl border border-dashed border-gray-700/50">
                            <i class="far fa-newspaper text-4xl text-gray-500 mb-3"></i>
                            <p class="text-gray-400 font-medium">Nenhuma notícia publicada no momento.</p>
                        </div>
                    @endforelse
                </div>

                @if($noticias->count() > 0)
                    <div class="text-center pt-2">
                        <a href="{{ route('noticias.index') }}" class="inline-flex items-center justify-center px-6 py-3 border border-gray-700/50 shadow-[0_0_15px_rgba(0,0,0,0.2)] text-sm font-bold rounded-xl text-white bg-gray-800/60 hover:bg-gray-700/80 backdrop-blur-md transition">
                            Ver todas as postagens
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div x-show="toast.show" 
         x-cloak
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="fixed top-24 right-4 sm:top-4 sm:right-4 max-w-sm w-full bg-gray-900/90 backdrop-blur-md border shadow-2xl rounded-2xl p-4 flex items-start gap-3" 
         style="z-index: 2147483647;"
         :class="toast.type === 'error' ? 'border-red-500/50' : (toast.type === 'success' ? 'border-green-500/50' : 'border-amber-500/50')">
        <div class="flex-shrink-0 mt-0.5">
            <i class="fas fa-exclamation-circle text-2xl" :class="toast.type === 'error' ? 'text-red-400' : (toast.type === 'success' ? 'text-green-400' : 'text-amber-400')"></i>
        </div>
        <div class="flex-1">
            <h4 class="text-sm font-bold text-white" x-text="toast.type === 'error' ? 'Ops! Atenção' : (toast.type === 'success' ? 'Tudo Certo!' : 'Aviso')"></h4>
            <p class="text-sm text-gray-300 mt-1" x-text="toast.message"></p>
        </div>
        <button @click="toast.show = false" class="text-gray-400 hover:text-white transition">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div x-show="modalMapaAberto" x-cloak class="fixed inset-0 flex flex-col bg-gray-900" style="z-index: 2147483646;">
        <div class="bg-gray-900 p-4 border-b border-gray-800 flex justify-between items-center shadow-lg z-10 pt-safe">
            <div>
                <h3 class="text-white font-extrabold text-lg flex items-center"><i class="fas fa-map-marked-alt text-cyan-400 mr-2"></i> Marque sua Residência</h3>
                <p class="text-gray-400 text-xs mt-0.5 hidden sm:block">Mova o mapa e clique exatamente onde fica sua casa.</p>
            </div>
            <button @click="fecharModalMapa()" class="text-gray-400 hover:text-white p-2 transition bg-gray-800 rounded-full h-12 w-12 flex items-center justify-center">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div id="map-modal-container" class="w-full flex-1 relative bg-gray-800">
            <div x-show="carregandoMapaInterface" class="absolute inset-0 flex items-center justify-center bg-gray-900/80 z-[11]">
                <i class="fas fa-spinner fa-spin text-4xl text-cyan-400"></i>
            </div>
        </div>

        <div class="bg-gray-900 p-4 sm:p-6 border-t border-gray-800 flex flex-col sm:flex-row gap-4 items-center justify-between z-10 pb-safe">
            <div class="text-sm text-gray-300 flex-1 w-full truncate bg-gray-800 p-3 rounded-lg border border-gray-700 flex items-center">
                <i class="fas fa-map-pin text-red-500 mr-3 text-lg"></i>
                <span class="truncate" x-text="enderecoTempCompleto || 'Clique no mapa para marcar sua casa...'"></span>
            </div>
            <button type="button" @click="confirmarEnderecoMapa()" :disabled="!enderecoTempCompleto || enderecoTempCompleto.includes('desconhecido')" class="w-full sm:w-auto px-8 py-3.5 bg-gradient-to-r from-green-400 to-cyan-500 text-gray-900 font-extrabold rounded-xl shadow-[0_0_15px_rgba(6,182,212,0.4)] transition hover:scale-105 disabled:opacity-50 disabled:hover:scale-100 disabled:cursor-not-allowed flex justify-center items-center gap-2">
                <i class="fas fa-check-circle"></i> Fixar Endereço
            </button>
        </div>
    </div>

    <div x-show="modalSucessoAberto" x-cloak class="fixed inset-0 flex items-center justify-center p-4 bg-gray-900/90 backdrop-blur-md"
         style="z-index: 2147483646;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-90">
         
        <div @click.away="fecharModais()" class="bg-gray-800 border border-gray-700 rounded-3xl shadow-[0_0_40px_rgba(52,211,153,0.3)] w-full max-w-lg overflow-hidden relative text-center p-8 transform transition-all">
            <div class="mx-auto w-24 h-24 bg-green-500/20 text-green-400 rounded-full flex items-center justify-center mb-6 shadow-[0_0_20px_rgba(52,211,153,0.4)]">
                <i class="fas fa-check-circle text-6xl"></i>
            </div>
            <h3 class="text-3xl font-black text-white mb-2">Cobertura Confirmada! 🎉</h3>
            <p class="text-gray-300 mb-8 text-base">Temos rede disponível no endereço: <strong x-text="leadForm.endereco_pesquisado" class="text-white block mt-2 text-lg"></strong></p>
            
            <a :href="linkPreCadastro()" class="w-full inline-flex justify-center items-center py-4 px-6 bg-gradient-to-r from-green-400 to-cyan-500 text-gray-900 font-extrabold rounded-xl shadow-lg transition hover:scale-105 hover:-translate-y-1">
                Ver Planos e Assinar Agora <i class="fas fa-rocket ml-2"></i>
            </a>
            <button @click="fecharModais()" class="mt-6 text-gray-400 hover:text-white font-medium text-sm transition underline underline-offset-4">
                Voltar para a consulta
            </button>
        </div>
    </div>

    <div x-show="modalLeadAberto" x-cloak class="fixed inset-0 flex items-center justify-center p-4 bg-gray-900/90 backdrop-blur-md overflow-y-auto"
         style="z-index: 2147483646;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-90">
         
        <div @click.away="fecharModais()" class="bg-gray-800 border border-gray-700 rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden relative text-left p-6 sm:p-8 transform transition-all my-8">
            <button @click="fecharModais()" class="absolute top-4 right-4 text-gray-400 hover:text-white transition"><i class="fas fa-times text-xl"></i></button>
            
            <div class="mb-5 flex items-center gap-4">
                <div class="w-16 h-16 bg-red-500/20 text-red-400 rounded-full flex items-center justify-center shadow-[0_0_15px_rgba(239,68,68,0.3)]">
                    <i class="fas fa-map-marker-alt text-3xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-white">Quase lá...</h3>
                    <p class="text-gray-400 text-sm">Ainda não chegamos na sua rua.</p>
                </div>
            </div>

            <p class="text-gray-300 text-sm mb-6 bg-gray-900/50 p-4 rounded-xl border border-gray-700">Deixe seu contato abaixo para avisarmos assim que a rede expandir e a fibra chegar na sua porta!</p>
            
            <form @submit.prevent="salvarLead" class="space-y-4">
                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-1">
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Contato</label>
                        <select x-model="leadForm.pronome" required class="w-full py-3.5 rounded-xl bg-gray-900 border border-gray-700 text-white focus:ring-cyan-500 focus:border-cyan-500 text-sm">
                            <option value="">Tipo</option>
                            <option value="Sr.">Sr.</option>
                            <option value="Sra.">Sra.</option>
                            <option value="Empresa">Empresa</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Seu Nome</label>
                        <input type="text" x-model="leadForm.nome" required placeholder="Ex: João Silva" class="w-full py-3.5 rounded-xl bg-gray-900 border border-gray-700 text-white focus:ring-cyan-500 focus:border-cyan-500 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">WhatsApp</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fab fa-whatsapp text-green-400 text-lg"></i>
                        </div>
                        <input type="text" x-model="leadForm.whatsapp" required placeholder="(00) 00000-0000" class="w-full pl-11 py-3.5 rounded-xl bg-gray-900 border border-gray-700 text-white focus:ring-cyan-500 focus:border-cyan-500 text-sm">
                    </div>
                </div>

                <button type="submit" :disabled="enviandoLead" class="w-full mt-4 py-4 px-4 bg-cyan-600 hover:bg-cyan-500 text-white font-extrabold rounded-xl shadow-[0_0_15px_rgba(6,182,212,0.4)] transition disabled:opacity-50 flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane" x-show="!enviandoLead"></i>
                    <i class="fas fa-spinner fa-spin" x-show="enviandoLead"></i>
                    <span x-text="enviandoLead ? 'Registrando Interesse...' : 'Avise-me quando chegar!'"></span>
                </button>
            </form>
        </div>
    </div>

</section>

@include('partials.coverage_scripts')