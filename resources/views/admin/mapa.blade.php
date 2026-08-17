<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center justify-between">
            <span>{{ __('Mapa de Cobertura (Geofencing)') }}</span>
            <span class="text-xs px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full font-bold uppercase tracking-widest border border-indigo-200">
                Motor: {{ env('MAP_PROVIDER', 'osm') == 'google' ? 'Google Maps Satélite' : 'OpenStreetMap' }}
            </span>
        </h2>
    </x-slot>

    @if(env('MAP_PROVIDER', 'osm') == 'google')
        <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places,marker"></script>
    @else
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>

    <div class="py-8" x-data="geoNocAdmin()">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-12 gap-6 h-[80vh]">
            
            <div class="bg-white p-6 shadow-md rounded-2xl lg:col-span-3 h-full flex flex-col border border-gray-200">
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 mb-6 transition-all duration-300" :class="modoEdicao ? 'ring-2 ring-amber-400' : ''">
                    <h3 class="text-sm font-extrabold mb-4 text-gray-800 uppercase tracking-wider flex items-center justify-between">
                        <span x-text="modoEdicao ? '✏️ Editando Área' : '📍 Nova Área'"></span>
                        <button x-show="modoEdicao" @click="cancelarEdicao()" class="text-xs bg-gray-200 hover:bg-gray-300 text-gray-700 px-2 py-1 rounded">Cancelar</button>
                    </h3>
                    
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 mb-1">Nome da Área / Bairro</label>
                        <input type="text" x-model="form.nome" x-ref="inputNome" placeholder="Ex: Setor Sul..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 mb-1">Cor do Polígono</label>
                        <div class="flex items-center gap-2">
                            <input type="color" x-model="form.cor" class="block w-12 h-10 rounded-md border-gray-300 shadow-sm cursor-pointer p-0.5">
                            <input type="text" x-model="form.cor" class="block w-full rounded-md border-gray-300 shadow-sm sm:text-sm uppercase font-mono" readonly>
                        </div>
                    </div>

                    <button @click="salvarDados()" class="w-full font-bold py-2.5 px-4 rounded-lg shadow-sm transition flex justify-center items-center gap-2 text-white" :class="modoEdicao ? 'bg-amber-500 hover:bg-amber-600' : 'bg-indigo-600 hover:bg-indigo-700'">
                        <span x-text="modoEdicao ? 'Salvar Alterações' : 'Salvar Nova Área'"></span>
                    </button>
                    
                    <div x-show="!modoEdicao && (!coordenadasPendentes || coordenadasPendentes.length === 0)" class="mt-3 text-xs text-indigo-600 bg-indigo-50 border border-indigo-100 p-2 rounded-lg text-center animate-pulse">
                        <i class="fas fa-info-circle mr-1"></i> Desenhe no mapa ou use a Inteligência SGP primeiro.
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto pr-2">
                    <h4 class="text-xs font-extrabold text-gray-400 uppercase tracking-wider mb-3">Áreas Mapeadas (<span x-text="poligonos.length"></span>)</h4>
                    <template x-if="poligonos.length === 0">
                        <p class="text-sm text-gray-400 italic text-center mt-4">Nenhuma área cadastrada.</p>
                    </template>
                    <div class="space-y-2">
                        <template x-for="poly in poligonos" :key="poly.id">
                            <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg shadow-sm hover:border-indigo-300 hover:shadow transition group">
                                <div class="flex items-center gap-3 overflow-hidden cursor-pointer w-3/4" @click="focarArea(poly.id)">
                                    <div class="w-4 h-4 rounded-full flex-shrink-0 shadow-sm" :style="`background-color: ${poly.cor}`"></div>
                                    <span class="text-sm font-bold text-gray-700 truncate w-full" x-text="poly.nome"></span>
                                </div>
                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition w-1/4 justify-end">
                                    <button @click="iniciarEdicao(poly)" title="Editar Nome/Cor" class="text-amber-500 hover:text-amber-700 p-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button>
                                    <button @click="deletarArea(poly.id)" title="Excluir" class="text-red-500 hover:text-red-700 p-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="bg-gray-200 rounded-2xl lg:col-span-9 h-[500px] lg:h-full relative overflow-hidden shadow-md border border-gray-300">
                
                <div x-show="provider === 'google' && !modoEdicao && !painelCtosAberto" class="absolute top-4 left-4 z-[1000] bg-white p-2 rounded-lg shadow-lg border border-gray-200 flex flex-col gap-2">
                    
                    <div class="flex gap-2">
                        <button x-show="!desenhandoAgora" @click="iniciarModoDesenhoGoogle()" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold px-4 py-2 rounded transition border border-indigo-200 flex items-center gap-2 text-sm shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            Desenho Manual
                        </button>
                        
                        <div x-show="desenhandoAgora" class="flex items-center gap-2 bg-indigo-50 p-1 rounded border border-indigo-200">
                            <div class="text-[11px] font-bold text-indigo-700 mx-2 flex items-center gap-1.5 uppercase tracking-wider">
                                <span class="w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse"></span> Desenhando...
                            </div>
                            <button @click="desfazerUltimoPonto()" title="Desfazer Último Ponto" class="bg-white hover:bg-gray-100 text-gray-700 px-3 py-1.5 rounded border border-gray-200 transition font-bold text-xs shadow-sm flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg> Desfazer
                            </button>
                            <button @click="descartarDesenho()" title="Descartar Desenho" class="bg-white hover:bg-red-50 text-red-600 px-3 py-1.5 rounded border border-red-200 transition font-bold text-xs shadow-sm flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg> Descartar
                            </button>
                        </div>
                    </div>
                </div>

                <button @click="abrirPainelCtos()" class="absolute top-4 left-1/2 -translate-x-1/2 z-[1000] bg-white text-gray-800 font-extrabold px-5 py-2.5 rounded-full shadow-lg border border-gray-200 hover:bg-green-50 flex items-center gap-2 transition" title="Inteligência SGP">
                    <svg class="w-4 h-4 text-green-500 animate-pulse" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                    Inteligência SGP (Gerar por CTOs)
                </button>

                <div x-show="modoEdicao || previewPolygon || desenhandoAgora" class="absolute bottom-6 left-1/2 -translate-x-1/2 z-[1000] bg-gray-900/80 backdrop-blur-sm text-white text-xs font-bold px-4 py-2 rounded-full shadow-lg pointer-events-none">
                    💡 Dica: Clique com o <b class="text-amber-400">botão direito</b> numa quina branca para apagá-la.
                </div>

                <div x-show="painelCtosAberto" x-cloak class="absolute top-16 left-1/2 -translate-x-1/2 z-[1000] w-96 bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden flex flex-col max-h-[85%]">
                    <div class="bg-gray-900 text-white p-4 flex justify-between items-center">
                        <h4 class="font-bold text-sm flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path></svg> 
                            Rede FTTH (Agrupamento)
                        </h4>
                        <button @click="fecharPainelCtos()" class="text-gray-400 hover:text-white font-bold text-lg">&times;</button>
                    </div>
                    
                    <div class="p-4 overflow-y-auto flex-1 text-sm">
                        <div x-show="carregandoCtos" class="text-center text-gray-500 py-6 font-bold animate-pulse flex flex-col items-center">
                            <svg class="w-8 h-8 text-green-500 mb-2 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                            Buscando OLTs no SGP...
                        </div>
                        
                        <div x-show="!carregandoCtos && ctosGerais.length > 0">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Raio de cada Caixa (m)</label>
                                <input type="number" x-model.number="raioCtos" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 sm:text-sm font-bold text-gray-900">
                                <p class="text-[10px] text-gray-500 mt-1">Isso formará os blocos (quadrados) de cobertura.</p>
                            </div>

                            <div class="mt-4 border-t border-gray-200 pt-4">
                                <div class="flex justify-between items-end mb-2">
                                    <label class="block text-xs font-bold text-gray-700">Selecione as PONs (<span x-text="ctosGerais.length"></span> CTOs)</label>
                                    <div class="flex items-center gap-2">
                                        <label class="flex items-center gap-1 cursor-pointer text-[10px] font-bold text-indigo-700 bg-indigo-50 px-2 py-1 rounded hover:bg-indigo-100 transition border border-indigo-200 select-none">
                                            <input type="checkbox" class="rounded border-gray-300 w-3 h-3 text-indigo-600 focus:ring-0" 
                                                   :checked="ponsSelecionadas.length === ponsDisponiveis.length && ponsDisponiveis.length > 0"
                                                   @change="toggleTodasPons($event.target.checked)">
                                            MARCAR TODAS
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="max-h-60 overflow-y-auto bg-gray-50 p-2 rounded-md border border-gray-200 mb-3 shadow-inner space-y-2">
                                    <template x-for="(slots, oltName) in ponsAgrupadas" :key="oltName">
                                        <div x-data="{ oltAberta: false }" class="border border-gray-300 bg-white rounded shadow-sm overflow-hidden">
                                            
                                            <div class="bg-gray-800 text-white px-2 py-2 flex justify-between items-center cursor-pointer hover:bg-gray-700 transition select-none" @click="oltAberta = !oltAberta">
                                                <div class="flex items-center gap-2" @click.stop>
                                                    <input type="checkbox" class="rounded border-gray-500 w-3 h-3 text-indigo-500 focus:ring-0 cursor-pointer"
                                                           :checked="todasDaOltSelecionadas(slots)"
                                                           @change="toggleOlt(slots, $event.target.checked)">
                                                    <span class="text-[11px] font-black uppercase tracking-wider" x-text="oltName"></span>
                                                </div>
                                                <svg class="w-4 h-4 transform transition-transform duration-200 text-gray-300" :class="oltAberta ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </div>
                                            
                                            <div x-show="oltAberta" class="p-2 space-y-3 bg-white" style="display: none;">
                                                <template x-for="(ponsArray, slotNum) in slots" :key="slotNum">
                                                    <div x-data="{ slotAberto: true }" class="border-l-2 border-indigo-300 pl-2 ml-1">
                                                        
                                                        <div class="flex justify-between items-center cursor-pointer mb-1.5 bg-indigo-50 p-1 rounded hover:bg-indigo-100 transition select-none" @click="slotAberto = !slotAberto">
                                                            <div class="flex items-center gap-1.5" @click.stop>
                                                                <input type="checkbox" class="rounded border-gray-300 w-3 h-3 text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                                                                       :checked="todasDoSlotSelecionadas(ponsArray)"
                                                                       @change="toggleSlot(ponsArray, $event.target.checked)">
                                                                <h6 class="text-[10px] font-extrabold text-indigo-900 tracking-wider uppercase">SLOT <span x-text="slotNum"></span></h6>
                                                            </div>
                                                            <svg class="w-3 h-3 text-indigo-400 transform transition-transform duration-200" :class="slotAberto ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                        </div>
                                                        
                                                        <div x-show="slotAberto" class="grid grid-cols-2 gap-1.5 mt-1" style="display: none;">
                                                            <template x-for="pon in ponsArray" :key="pon.id">
                                                                <label class="flex items-start gap-1.5 cursor-pointer hover:bg-gray-50 p-1.5 rounded transition border border-gray-100 hover:border-indigo-200 select-none">
                                                                    <input type="checkbox" :value="String(pon.id)" x-model="ponsSelecionadas" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mt-0.5 w-3 h-3">
                                                                    <span class="text-[10px] text-gray-700 leading-tight">
                                                                        <span class="font-bold text-gray-900">PON <span x-text="pon.pon"></span></span>
                                                                        <template x-if="pon.description"><span class="block text-gray-500 font-normal truncate w-[90px]" :title="pon.description" x-text="pon.description"></span></template>
                                                                    </span>
                                                                </label>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                
                                <button @click="atualizarPreviaMapa()" class="w-full bg-blue-50 hover:bg-blue-100 text-blue-800 font-extrabold py-2 px-3 rounded-lg border border-blue-200 shadow-sm transition text-xs flex justify-center items-center gap-2">
                                    👁️ Aplicar Filtro no Mapa
                                </button>
                            </div>
                        </div>
                        
                        <div x-show="!carregandoCtos && ctosGerais.length === 0" class="text-center text-red-500 text-xs font-bold py-4">
                            Nenhuma CTO com coordenadas encontrada no SGP.
                        </div>
                    </div>

                    <div class="p-4 bg-gray-50 border-t border-gray-200" x-show="!carregandoCtos && ctosGerais.length > 0">
                        <button @click="aplicarPreviewComoEdicao()" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg shadow transition text-sm flex justify-center items-center gap-2" :disabled="!previewPolygon">
                            ✅ Gerar e Editar Polígono Mágico
                        </button>
                    </div>
                </div>

                <div id="mapa-matrix" class="w-full h-full cursor-crosshair"></div>
            </div>

        </div>
    </div>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        function geoNocAdmin() {
            return {
                provider: '{{ env('MAP_PROVIDER', 'osm') }}',
                map: null,
                googlePolygons: {}, 
                poligonoRascunhoGoogle: null,
                previewPolygon: null,
                listenerCliqueMapa: null,
                pontosManuais: [],
                
                poligonos: [],
                modoEdicao: false,
                desenhandoAgora: false,
                coordenadasPendentes: null,
                
                painelCtosAberto: false,
                carregandoCtos: false,
                ctosGerais: [],
                ponsDisponiveis: [],
                ponsAgrupadas: {}, 
                ponsSelecionadas: [],
                raioCtos: 150,
                marcadoresCtos: [],
                
                form: { id: null, nome: '', cor: '#81c700' },

                init() {
                    this.carregarDadosBanco().then(() => {
                        if (this.provider === 'google') this.initGoogleMaps();
                        else this.initLeafletMaps();
                    });

                    this.$watch('form.cor', (value) => {
                        if (this.poligonoRascunhoGoogle) this.poligonoRascunhoGoogle.setOptions({fillColor: value, strokeColor: value});
                        if (this.previewPolygon) this.previewPolygon.setOptions({fillColor: value, strokeColor: value});
                        if (this.modoEdicao && this.form.id && this.googlePolygons[this.form.id]) this.googlePolygons[this.form.id].setOptions({fillColor: value, strokeColor: value});
                    });
                },

                async carregarDadosBanco() {
                    try {
                        const resp = await fetch('/admin/mapa-cobertura/data');
                        if (resp.ok) this.poligonos = await resp.json();
                    } catch (e) {}
                },

                initGoogleMaps() {
                    const centro = this.poligonos.length > 0 ? { lat: this.poligonos[0].coordenadas[0].lat, lng: this.poligonos[0].coordenadas[0].lng } : { lat: -8.891, lng: -36.495 };
                    this.map = new google.maps.Map(document.getElementById('mapa-matrix'), {
                        center: centro, 
                        zoom: 14, 
                        mapTypeId: google.maps.MapTypeId.SATELLITE,
                        disableDefaultUI: false, 
                        zoomControl: true, 
                        mapTypeControl: false,
                        gestureHandling: 'greedy' 
                    });

                    this.poligonos.forEach(p => {
                        const googlePoly = new google.maps.Polygon({
                            paths: p.coordenadas, fillColor: p.cor, fillOpacity: 0.3, strokeColor: p.cor, strokeWeight: 3, map: this.map, clickable: true, editable: false
                        });
                        this.googlePolygons[p.id] = googlePoly;

                        const atualizarCoords = () => this.sincronizarEdicaoGoogle(p.id);
                        google.maps.event.addListener(googlePoly.getPath(), 'set_at', atualizarCoords);
                        google.maps.event.addListener(googlePoly.getPath(), 'insert_at', atualizarCoords);
                        
                        // EVENTO DELETAR PONTO EM ÁREA EXISTENTE
                        this.adicionarEventoDeletarVertice(googlePoly, false, p.id);

                        google.maps.event.addListener(googlePoly, 'click', () => {
                            if(!this.desenhandoAgora && !this.painelCtosAberto) this.iniciarEdicao(p);
                        });
                    });
                },

                // LÓGICA REUTILIZÁVEL PARA BOTÃO DIREITO (DELETAR VÉRTICE)
                adicionarEventoDeletarVertice(polygon, isRascunho = false, idBanco = null) {
                    google.maps.event.addListener(polygon, 'contextmenu', (e) => {
                        if (e.vertex == undefined) return; // Se não clicou num ponto branco, ignora
                        
                        const pathIndex = e.path == undefined ? 0 : e.path;
                        const path = polygon.getPaths().getAt(pathIndex);
                        
                        if (path.getLength() > 3) {
                            path.removeAt(e.vertex);
                            
                            if (isRascunho) {
                                this.atualizarPendentesDoRascunho();
                            } else if (idBanco) {
                                this.sincronizarEdicaoGoogle(idBanco);
                            }
                        } else {
                            alert('Atenção: Uma área não pode ter menos do que 3 pontos!');
                        }
                    });
                },

                iniciarModoDesenhoGoogle() {
                    if (this.modoEdicao) this.cancelarEdicao();
                    this.desenhandoAgora = true;
                    this.pontosManuais = [];
                    this.coordenadasPendentes = [];
                    
                    if(this.poligonoRascunhoGoogle) this.poligonoRascunhoGoogle.setMap(null);

                    const mvcArray = new google.maps.MVCArray();
                    this.poligonoRascunhoGoogle = new google.maps.Polygon({
                        paths: mvcArray, fillColor: this.form.cor, fillOpacity: 0.3, strokeWeight: 3, strokeColor: this.form.cor, map: this.map, editable: true 
                    });

                    // EVENTO DELETAR PONTO NO RASCUNHO MANUAL
                    this.adicionarEventoDeletarVertice(this.poligonoRascunhoGoogle, true, null);

                    this.listenerCliqueMapa = google.maps.event.addListener(this.map, 'click', (e) => {
                        const path = this.poligonoRascunhoGoogle.getPath();
                        path.push(e.latLng);
                        this.atualizarPendentesDoRascunho();
                    });

                    google.maps.event.addListener(this.poligonoRascunhoGoogle.getPath(), 'set_at', () => this.atualizarPendentesDoRascunho());
                    google.maps.event.addListener(this.poligonoRascunhoGoogle.getPath(), 'insert_at', () => this.atualizarPendentesDoRascunho());
                },

                desfazerUltimoPonto() {
                    if(this.poligonoRascunhoGoogle) {
                        const path = this.poligonoRascunhoGoogle.getPath();
                        if(path.getLength() > 0) {
                            path.pop();
                            this.atualizarPendentesDoRascunho();
                        }
                    }
                },

                descartarDesenho() {
                    this.cancelarDesenhoGoogle();
                },

                cancelarDesenhoGoogle() {
                    this.desenhandoAgora = false;
                    if(this.listenerCliqueMapa) {
                        google.maps.event.removeListener(this.listenerCliqueMapa);
                        this.listenerCliqueMapa = null;
                    }
                    if(this.poligonoRascunhoGoogle) {
                        this.poligonoRascunhoGoogle.setMap(null);
                        this.poligonoRascunhoGoogle = null;
                    }
                    this.coordenadasPendentes = null;
                    this.pontosManuais = [];
                },

                sincronizarEdicaoGoogle(id) {
                    if(this.googlePolygons[id]) {
                        const pathsGerados = this.googlePolygons[id].getPaths();
                        const newPaths = [];
                        for(let i=0; i<pathsGerados.getLength(); i++) {
                            const anel = pathsGerados.getAt(i);
                            const anelCoords = [];
                            for(let j=0; j<anel.getLength(); j++) anelCoords.push({ lat: anel.getAt(j).lat(), lng: anel.getAt(j).lng() });
                            newPaths.push(anelCoords);
                        }
                        this.salvarDiretoBanco(id, null, null, newPaths.length === 1 ? newPaths[0] : newPaths);
                    }
                },

                atualizarPendentesDoRascunho() {
                    if(!this.poligonoRascunhoGoogle) return;
                    const paths = this.poligonoRascunhoGoogle.getPaths();
                    const newPaths = [];
                    for(let i=0; i<paths.getLength(); i++) {
                        const anel = paths.getAt(i);
                        const anelCoords = [];
                        for(let j=0; j<anel.getLength(); j++) anelCoords.push({ lat: anel.getAt(j).lat(), lng: anel.getAt(j).lng() });
                        newPaths.push(anelCoords);
                    }
                    this.coordenadasPendentes = newPaths.length === 1 ? newPaths[0] : newPaths;
                },

                toggleTodasPons(checked) {
                    if(checked) this.ponsSelecionadas = this.ponsDisponiveis.map(p => String(p.id));
                    else this.ponsSelecionadas = [];
                },

                todasDaOltSelecionadas(slots) {
                    let allPons = [];
                    Object.values(slots).forEach(arr => allPons = allPons.concat(arr));
                    return allPons.length > 0 && allPons.every(p => this.ponsSelecionadas.includes(String(p.id)));
                },

                toggleOlt(slots, checked) {
                    let allPons = [];
                    Object.values(slots).forEach(arr => allPons = allPons.concat(arr));
                    if (checked) {
                        allPons.forEach(p => {
                            if(!this.ponsSelecionadas.includes(String(p.id))) this.ponsSelecionadas.push(String(p.id));
                        });
                    } else {
                        const idsToRemove = allPons.map(p => String(p.id));
                        this.ponsSelecionadas = this.ponsSelecionadas.filter(id => !idsToRemove.includes(id));
                    }
                },

                todasDoSlotSelecionadas(ponsArray) {
                    return ponsArray.length > 0 && ponsArray.every(p => this.ponsSelecionadas.includes(String(p.id)));
                },

                toggleSlot(ponsArray, checked) {
                    if (checked) {
                        ponsArray.forEach(p => {
                            if(!this.ponsSelecionadas.includes(String(p.id))) this.ponsSelecionadas.push(String(p.id));
                        });
                    } else {
                        const idsToRemove = ponsArray.map(p => String(p.id));
                        this.ponsSelecionadas = this.ponsSelecionadas.filter(id => !idsToRemove.includes(id));
                    }
                },

                abrirPainelCtos() {
                    if(this.provider !== 'google') return alert('Esta inteligência requer o Google Maps.');
                    this.cancelarDesenhoGoogle();
                    this.painelCtosAberto = true;
                    if(this.ctosGerais.length === 0) this.carregarCtosSGP();
                },
                
                fecharPainelCtos() {
                    this.painelCtosAberto = false;
                    this.limparMarcadoresCtos();
                    if(this.previewPolygon) {
                        this.previewPolygon.setMap(null);
                        this.previewPolygon = null;
                    }
                },

                async carregarCtosSGP() {
                    this.carregandoCtos = true;
                    try {
                        const resp = await fetch('/admin/mapa-cobertura/ctos');
                        if (resp.ok) {
                            const data = await resp.json();
                            this.ctosGerais = data.ctos;
                            this.ponsDisponiveis = data.pons;
                            
                            let group = {};
                            this.ponsDisponiveis.forEach(p => {
                                if(!group[p.olt_name]) group[p.olt_name] = {};
                                if(!group[p.olt_name][p.slot]) group[p.olt_name][p.slot] = [];
                                group[p.olt_name][p.slot].push(p);
                            });
                            this.ponsAgrupadas = group;
                        }
                    } catch (e) { alert('Falha ao conectar com SGP'); }
                    this.carregandoCtos = false;
                },

                atualizarPreviaMapa() {
                    this.limparMarcadoresCtos();
                    if(this.previewPolygon) {
                        this.previewPolygon.setMap(null);
                        this.previewPolygon = null;
                    }
                    
                    if(this.ponsSelecionadas.length === 0) return alert('Selecione pelo menos uma PON.');
                    if(this.raioCtos <= 0) return alert('Raio inválido.');

                    const ctosFiltradas = this.ctosGerais.filter(c => this.ponsSelecionadas.includes(String(c.pon_id)));
                    if(ctosFiltradas.length === 0) return alert('Nenhuma CTO visível nas opções selecionadas.');

                    const iconSVG = { path: google.maps.SymbolPath.CIRCLE, fillColor: '#22c55e', fillOpacity: 1, strokeWeight: 2, strokeColor: '#ffffff', scale: 5 };

                    const quadrados = ctosFiltradas.map(c => {
                        const marker = new google.maps.Marker({ position: { lat: c.lat, lng: c.lng }, map: this.map, icon: iconSVG, title: c.nome });
                        this.marcadoresCtos.push(marker);
                        
                        const pt = turf.point([c.lng, c.lat]);
                        return turf.bboxPolygon(turf.bbox(turf.buffer(pt, this.raioCtos, { units: 'meters' })));
                    });

                    let poligonoUnido = quadrados[0];
                    try {
                        for(let i = 1; i < quadrados.length; i++) {
                            poligonoUnido = turf.union(poligonoUnido, quadrados[i]);
                        }
                    } catch (err) { console.error('Falha Turf.js', err); }

                    const geomType = turf.getType(poligonoUnido);
                    const coordsTurf = turf.getCoords(poligonoUnido);
                    let pathsParaGoogle = [];

                    if (geomType === 'Polygon') {
                        pathsParaGoogle = coordsTurf.map(anel => anel.map(c => ({ lat: c[1], lng: c[0] })));
                    } else if (geomType === 'MultiPolygon') {
                        coordsTurf.forEach(poly => { poly.forEach(anel => { pathsParaGoogle.push(anel.map(c => ({ lat: c[1], lng: c[0] }))); }); });
                    }

                    this.previewPolygon = new google.maps.Polygon({
                        paths: pathsParaGoogle, fillColor: this.form.cor, fillOpacity: 0.5, strokeWeight: 2, strokeColor: this.form.cor, map: this.map, editable: false 
                    });

                    const bounds = new google.maps.LatLngBounds();
                    pathsParaGoogle.forEach(anel => anel.forEach(pt => bounds.extend(new google.maps.LatLng(pt.lat, pt.lng))));
                    this.map.fitBounds(bounds);
                },

                limparMarcadoresCtos() {
                    this.marcadoresCtos.forEach(m => m.setMap(null));
                    this.marcadoresCtos = [];
                },

                aplicarPreviewComoEdicao() {
                    if(!this.previewPolygon) return;
                    
                    const pathsGerados = this.previewPolygon.getPaths();
                    const newPaths = [];
                    for(let i=0; i<pathsGerados.getLength(); i++) {
                        const anel = pathsGerados.getAt(i);
                        const anelCoords = [];
                        for(let j=0; j<anel.getLength(); j++) anelCoords.push({ lat: anel.getAt(j).lat(), lng: anel.getAt(j).lng() });
                        newPaths.push(anelCoords);
                    }

                    this.coordenadasPendentes = newPaths.length === 1 ? newPaths[0] : newPaths;

                    if (this.modoEdicao && this.form.id && this.googlePolygons[this.form.id]) {
                        this.googlePolygons[this.form.id].setPaths(newPaths);
                        this.salvarDiretoBanco(this.form.id, null, null, newPaths);
                        this.googlePolygons[this.form.id].setEditable(true);
                    } 
                    else {
                        this.desenhandoAgora = true;
                        const mvcArrayPaths = new google.maps.MVCArray();
                        
                        if(Array.isArray(newPaths[0])) {
                            newPaths.forEach(anel => {
                                const mvcAnel = new google.maps.MVCArray();
                                anel.forEach(pt => mvcAnel.push(new google.maps.LatLng(pt.lat, pt.lng)));
                                mvcArrayPaths.push(mvcAnel);
                            });
                        } else {
                            newPaths.forEach(pt => mvcArrayPaths.push(new google.maps.LatLng(pt.lat, pt.lng)));
                        }

                        this.poligonoRascunhoGoogle = new google.maps.Polygon({
                            paths: mvcArrayPaths, fillColor: this.form.cor, fillOpacity: 0.3, strokeWeight: 3, strokeColor: this.form.cor, map: this.map, editable: true 
                        });
                        
                        // EVENTO DELETAR PONTO NA ÁREA GERADA POR INTELIGÊNCIA
                        this.adicionarEventoDeletarVertice(this.poligonoRascunhoGoogle, true, null);

                        google.maps.event.addListener(this.poligonoRascunhoGoogle.getPath(), 'set_at', () => this.atualizarPendentesDoRascunho());
                        google.maps.event.addListener(this.poligonoRascunhoGoogle.getPath(), 'insert_at', () => this.atualizarPendentesDoRascunho());
                    }

                    this.fecharPainelCtos();
                    if (!this.modoEdicao) this.$refs.inputNome.focus();
                },

                iniciarEdicao(polyDados) {
                    this.cancelarDesenhoGoogle();
                    this.fecharPainelCtos();
                    this.modoEdicao = true;
                    this.form.id = polyDados.id;
                    this.form.nome = polyDados.nome;
                    this.form.cor = polyDados.cor;
                    this.coordenadasPendentes = null;

                    if (this.provider === 'google') {
                        Object.values(this.googlePolygons).forEach(p => p.setEditable(false));
                        if(this.googlePolygons[polyDados.id]) this.googlePolygons[polyDados.id].setEditable(true);
                    }
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                cancelarEdicao() {
                    this.modoEdicao = false;
                    this.form = { id: null, nome: '', cor: '#81c700' };
                    if (this.provider === 'google') Object.values(this.googlePolygons).forEach(p => p.setEditable(false)); 
                },

                focarArea(id) {
                    const poly = this.poligonos.find(p => p.id === id);
                    if(!poly) return;
                    if (this.provider === 'google') {
                        const bounds = new google.maps.LatLngBounds();
                        if(Array.isArray(poly.coordenadas[0]) && Array.isArray(poly.coordenadas[0][0])) {
                            poly.coordenadas.forEach(anel => anel.forEach(c => bounds.extend(new google.maps.LatLng(c.lat, c.lng))));
                        } else {
                            poly.coordenadas.forEach(c => bounds.extend(new google.maps.LatLng(c.lat, c.lng)));
                        }
                        this.map.fitBounds(bounds);
                    }
                },

                async salvarDados() {
                    if (this.modoEdicao) {
                        if (!this.form.nome) return alert('Digite o nome da área.');
                        await this.salvarDiretoBanco(this.form.id, this.form.nome, this.form.cor, null);
                        window.location.reload(); 
                    } else {
                        if (this.desenhandoAgora && this.listenerCliqueMapa) {
                            this.desenhandoAgora = false;
                            google.maps.event.removeListener(this.listenerCliqueMapa);
                        }

                        let arrayValido = this.coordenadasPendentes && (
                            (Array.isArray(this.coordenadasPendentes[0]) && this.coordenadasPendentes.length > 0) || 
                            this.coordenadasPendentes.length >= 3
                        );

                        if (!this.form.nome || !arrayValido) return alert('Desenhe a área no mapa (mín. 3 pontos) e preencha o nome.');
                        
                        try {
                            const response = await fetch('/admin/mapa-cobertura', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                                body: JSON.stringify({ nome: this.form.nome, cor: this.form.cor, coordenadas: this.coordenadasPendentes })
                            });
                            if(response.ok) window.location.reload();
                        } catch (e) { alert('Erro no servidor'); }
                    }
                },

                async salvarDiretoBanco(id, nome, cor, coords) {
                    let payload = {};
                    if(nome) payload.nome = nome;
                    if(cor) payload.cor = cor;
                    if(coords) payload.coordenadas = coords;
                    try {
                        await fetch('/admin/mapa-cobertura/' + id, {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                            body: JSON.stringify(payload)
                        });
                    } catch (e) {}
                },

                async deletarArea(id) {
                    if(!confirm('Certeza absoluta que quer apagar essa área?')) return;
                    try {
                        const res = await fetch('/admin/mapa-cobertura/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token\\\"]').content } });
                        if(res.ok) window.location.reload();
                    } catch (e) {}
                }
            }
        }
    </script>
</x-app-layout>