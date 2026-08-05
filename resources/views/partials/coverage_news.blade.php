<!-- CHAVE SELETORA: Define o provedor para o Javascript saber quem chamar -->
<script>const MAP_PROVIDER = "{{ env('MAP_PROVIDER', 'osm') }}";</script>

@if(env('MAP_PROVIDER', 'osm') == 'google')
    <!-- Script do Google atualizado, chamando a função initGoogleMaps quando terminar de carregar -->
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places,marker&callback=initGoogleMaps" async defer></script>
@else
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endif

<section id="cobertura-noticias" class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
    <div class="grid md:grid-cols-2 gap-12">
        
        <div>
            <h2 class="text-3xl font-bold text-white mb-6 flex items-center">
                <svg class="w-8 h-8 mr-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Consulte nossa Cobertura
            </h2>
            
            <div class="glass p-8 rounded-2xl border border-white/10 relative overflow-visible">
                <p class="text-gray-300 mb-6">A Matrix está em constante expansão. Verifique se a nossa rede já passa no seu endereço:</p>
                
                <div id="box-pesquisa">
                    <div class="relative max-w-2xl mx-auto mb-8">
                        <div class="flex flex-col md:flex-row gap-2">
                            <div class="relative flex-grow">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input type="text" id="input-endereco" class="block w-full pl-12 pr-4 py-4 bg-black/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-400 transition text-lg" placeholder="Digite sua rua ou bairro..." autocomplete="off">
                                
                                <!-- Dropdown de Autocompletar Otimizado -->
                                <ul id="sugestoes-endereco" class="absolute z-50 w-full bg-gray-800 border border-gray-700 rounded-lg mt-1 hidden max-h-60 overflow-y-auto shadow-2xl"></ul>
                            </div>
                            
                            <div class="flex gap-2">
                                <button type="button" onclick="abrirMapaPublico()" class="flex-shrink-0 bg-gray-800 hover:bg-gray-700 text-white border border-gray-600 px-4 py-4 rounded-xl font-bold transition flex items-center justify-center" title="Apontar localização no mapa">
                                    📍 Mapa
                                </button>
                                <button type="button" onclick="buscarCobertura()" class="flex-shrink-0 btn-accent text-white px-8 py-4 rounded-xl font-bold text-lg transition hover:shadow-[0_0_20px_rgba(129,199,0,0.4)]">
                                    Consultar
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <h4 class="text-sm font-semibold text-gray-400 mb-4 uppercase tracking-wider">Principais áreas atendidas</h4>
                    <ul class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm font-semibold text-white">
                        <li class="flex items-center"><span class="w-2 h-2 rounded-full bg-green-400 mr-2 shadow-[0_0_5px_rgba(129,199,0,1)]"></span> Garanhuns (Centro)</li>
                        <li class="flex items-center"><span class="w-2 h-2 rounded-full bg-green-400 mr-2"></span> Magano</li>
                        <li class="flex items-center"><span class="w-2 h-2 rounded-full bg-green-400 mr-2"></span> Brasília</li>
                        <li class="flex items-center"><span class="w-2 h-2 rounded-full bg-green-400 mr-2"></span> São José</li>
                    </ul>
                </div>

                <!-- CARREGANDO -->
                <div id="box-loading" class="hidden text-center py-6">
                    <svg class="animate-spin h-8 w-8 text-green-400 mx-auto mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <p class="text-gray-300 font-semibold animate-pulse text-sm">Cruzando coordenadas com nossa rede...</p>
                </div>

                <!-- COM COBERTURA -->
                <div id="box-sucesso" class="hidden text-center py-4">
                    <h3 class="text-xl font-bold text-green-400 mb-2">Cobertura Confirmada! 🎉</h3>
                    <p class="text-gray-300 text-sm mb-4">Temos rede disponível no endereço: <br><strong id="lbl-endereco-sucesso" class="text-white"></strong></p>
                    <button onclick="irParaCadastro()" class="w-full bg-white text-black py-3 rounded-lg font-bold hover:bg-gray-200 transition mb-3 shadow-[0_0_15px_rgba(255,255,255,0.3)]">
                        Ver Planos e Assinar
                    </button>
                    <button onclick="resetarPesquisa()" class="text-gray-500 text-xs hover:text-white transition">Pesquisar outro local</button>
                </div>

                <!-- SEM COBERTURA -->
                <div id="box-sem-cobertura" class="hidden text-center py-2">
                    <h3 class="text-lg font-bold text-pink-500 mb-2">Quase lá...</h3>
                    <p class="text-gray-300 text-sm mb-4">Ainda não chegamos na <span id="lbl-endereco-falha" class="text-white font-bold">sua rua</span>. Deixe seu contato para avisarmos assim que a rede expandir!</p>
                    <form onsubmit="salvarLead(event)" class="text-left">
                        <div class="flex gap-2 mb-3">
                            <select id="input-pronome" class="w-1/3 bg-black/50 border border-gray-600 rounded-lg px-2 py-2 text-white focus:border-cyan-400 focus:outline-none text-sm">
                                <option value="Sr.">Sr.</option>
                                <option value="Sra.">Sra.</option>
                            </select>
                            <input type="text" id="input-nome" placeholder="Seu nome completo" required class="w-2/3 bg-black/50 border border-gray-600 rounded-lg px-3 py-2 text-white focus:border-cyan-400 focus:outline-none text-sm">
                        </div>
                        <input type="text" id="input-whatsapp" placeholder="Seu WhatsApp" required class="w-full bg-black/50 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-cyan-400 focus:outline-none mb-3 text-sm">
                        <button type="submit" class="w-full bg-cyan-400 text-black py-2 rounded-lg font-bold hover:bg-cyan-300 transition text-sm">Me avise quando chegar!</button>
                    </form>
                    <button onclick="resetarPesquisa()" class="mt-3 text-gray-500 text-xs hover:text-white transition">Pesquisar outro local</button>
                </div>
            </div>
        </div>

        <div>
                <h2 class="text-3xl font-bold text-white mb-6 flex items-center">
                    <svg class="w-8 h-8 mr-3 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15M9 11l3 3m0 0l3-3m-3 3V8"></path></svg>
                    Mural de Avisos
                </h2>
                <div class="space-y-4">
                    
                    @forelse($noticias as $index => $noticia)
                        @php
                            // Alterna as cores da borda para manter o seu design original
                            $cores = ['border-l-pink-500', 'border-l-green-400', 'border-l-cyan-400'];
                            $corTexto = ['group-hover:text-pink-400', 'group-hover:text-green-400', 'group-hover:text-cyan-400'];
                            $corAtual = $cores[$index % 3];
                            $textoAtual = $corTexto[$index % 3];
                        @endphp
                        
                        <a href="{{ url('/noticia/' . $noticia->slug) }}" class="block glass p-5 rounded-xl hover:bg-white/10 transition border-l-4 {{ $corAtual }} group">
                            <span class="text-xs text-gray-500 font-bold mb-1 block {{ $textoAtual }}">{{ \Carbon\Carbon::parse($noticia->publicado_em)->format('d/m/Y') }}</span>
                            <h4 class="text-white font-semibold mb-1">{{ $noticia->titulo }}</h4>
                            <p class="text-gray-400 text-sm line-clamp-2">{{ $noticia->resumo }}</p>
                        </a>
                    @empty
                        <div class="glass p-5 rounded-xl border border-white/10">
                            <p class="text-gray-400 text-sm">Nenhum aviso publicado no momento.</p>
                        </div>
                    @endforelse

                </div>
                
                @if($noticias->count() >= 3)
                <button class="mt-6 text-green-400 text-sm font-bold hover:underline flex items-center">
                    Ver todas as notícias
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
                @endif
            </div>
    </div>
</section>

<!-- MODAL DO MAPA -->
<div id="modal-mapa" class="fixed inset-0 z-50 hidden bg-black/90 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-gray-800 w-full max-w-4xl rounded-2xl overflow-hidden shadow-2xl border border-gray-600">
        <div class="p-4 border-b border-gray-700 flex justify-between items-center bg-gray-900">
            <h3 class="text-white font-bold text-lg">📍 Aponte o local exato no mapa</h3>
            <button onclick="fecharMapa()" class="text-gray-400 hover:text-white font-bold text-2xl">&times;</button>
        </div>
        <div class="p-2 bg-gray-200">
            <div id="mapa-cliente" style="height: 60vh; width: 100%; border-radius: 8px;"></div>
        </div>
        <div class="p-4 bg-gray-900 flex justify-end gap-3">
            <button onclick="fecharMapa()" class="px-4 py-2 text-gray-400 hover:text-white font-semibold">Cancelar</button>
            <button onclick="confirmarLocalMapa()" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg font-bold shadow-lg">Confirmar Local</button>
        </div>
    </div>
</div>

<script>
    const inputEndereco = document.getElementById('input-endereco');
    const ulSugestoes = document.getElementById('sugestoes-endereco');
    
    let enderecoAtual = { rua: '', bairro: '', cidade: 'Garanhuns', estado: 'PE', cep: '', lat: null, lng: null, endereco_completo: '' };
    
    let mapaCliente = null;
    let marcadorCliente = null;
    let googlePlacesService = null;
    let googleGeocoder = null;

    // ==========================================
    // INICIALIZADOR DO GOOGLE MAPS (Moderno 2026)
    // ==========================================
    window.initGoogleMaps = function() {
        if (MAP_PROVIDER !== 'google') return;

        // O Motor Invisível que busca os endereços sem injetar UI
        const autocompleteService = new google.maps.places.AutocompleteService();
        googlePlacesService = new google.maps.places.PlacesService(document.createElement('div'));
        googleGeocoder = new google.maps.Geocoder();

        let timerBusca;

        inputEndereco.addEventListener('input', function() {
            clearTimeout(timerBusca);
            const query = this.value;
            if(query.length < 4) { ulSugestoes.classList.add('hidden'); return; }

            timerBusca = setTimeout(() => {
                autocompleteService.getPlacePredictions({
                    input: query + ' Garanhuns PE', // Direciona para a sua cidade
                    componentRestrictions: { country: 'br' }
                }, (predictions, status) => {
                    ulSugestoes.innerHTML = '';
                    if (status === google.maps.places.PlacesServiceStatus.OK && predictions) {
                        predictions.forEach(prediction => {
                            const li = document.createElement('li');
                            li.className = 'px-4 py-3 hover:bg-gray-700 cursor-pointer text-sm text-gray-200 border-b border-gray-700 last:border-0';
                            li.innerText = prediction.description;
                            li.onclick = () => selecionarEnderecoGoogle(prediction);
                            ulSugestoes.appendChild(li);
                        });
                        ulSugestoes.classList.remove('hidden');
                    }
                });
            }, 400);
        });
    };

    function selecionarEnderecoGoogle(prediction) {
        inputEndereco.value = prediction.description;
        ulSugestoes.classList.add('hidden');

        // Pega os detalhes matemáticos usando o ID do lugar
        googlePlacesService.getDetails({
            placeId: prediction.place_id,
            fields: ['geometry', 'address_components', 'formatted_address']
        }, (place, status) => {
            if (status === google.maps.places.PlacesServiceStatus.OK) {
                enderecoAtual.lat = place.geometry.location.lat();
                enderecoAtual.lng = place.geometry.location.lng();
                enderecoAtual.endereco_completo = place.formatted_address;
                enderecoAtual.rua = ''; enderecoAtual.bairro = '';
                
                for (const comp of place.address_components) {
                    if (comp.types.includes("route")) enderecoAtual.rua = comp.long_name;
                    if (comp.types.includes("sublocality") || comp.types.includes("neighborhood")) enderecoAtual.bairro = comp.long_name;
                    if (comp.types.includes("administrative_area_level_2")) enderecoAtual.cidade = comp.long_name;
                    if (comp.types.includes("administrative_area_level_1")) enderecoAtual.estado = comp.short_name;
                    if (comp.types.includes("postal_code")) enderecoAtual.cep = comp.long_name;
                }
                if(!enderecoAtual.rua) enderecoAtual.rua = place.formatted_address.split(',')[0];
                
                buscarCobertura(); // Dispara automático!
            }
        });
    }

    // ==========================================
    // INICIALIZADOR OPENSTREETMAP (Se o Google estiver OFF)
    // ==========================================
    if (MAP_PROVIDER === 'osm') {
        let timerBuscaOSM;
        inputEndereco.addEventListener('input', function() {
            clearTimeout(timerBuscaOSM);
            const query = this.value;
            if(query.length < 4) { ulSugestoes.classList.add('hidden'); return; }

            timerBuscaOSM = setTimeout(async () => {
                try {
                    const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&q=${encodeURIComponent(query + ', Garanhuns, PE')}&limit=5`);
                    const dados = await res.json();
                    ulSugestoes.innerHTML = '';
                    if(dados.length > 0) {
                        dados.forEach(item => {
                            const li = document.createElement('li');
                            li.className = 'px-4 py-3 hover:bg-gray-700 cursor-pointer text-sm text-gray-200 border-b border-gray-700 last:border-0';
                            li.innerText = item.display_name;
                            li.onclick = () => {
                                inputEndereco.value = item.display_name;
                                ulSugestoes.classList.add('hidden');
                                enderecoAtual.lat = item.lat; enderecoAtual.lng = item.lon;
                                enderecoAtual.rua = item.address.road || '';
                                enderecoAtual.bairro = item.address.suburb || item.address.neighbourhood || '';
                                enderecoAtual.endereco_completo = item.display_name;
                                buscarCobertura();
                            };
                            ulSugestoes.appendChild(li);
                        });
                        ulSugestoes.classList.remove('hidden');
                    }
                } catch (e) {}
            }, 500);
        });
    }

    // ==========================================
    // COMPORTAMENTOS GERAIS DA BARRA
    // ==========================================
    inputEndereco.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); ulSugestoes.classList.add('hidden'); buscarCobertura(); }
    });

    document.addEventListener('click', (e) => {
        if(!inputEndereco.contains(e.target) && !ulSugestoes.contains(e.target)) ulSugestoes.classList.add('hidden');
    });

    // ==========================================
    // O MAPA VISUAL DO CLIENTE (Modal)
    // ==========================================
    function abrirMapaPublico() {
		document.getElementById('modal-mapa').classList.remove('hidden');
		
		if (MAP_PROVIDER === 'google') {
			if(!mapaCliente) {
				// Instancia o mapa requerendo um MAP_ID para suportar o novo AdvancedMarkerElement
				mapaCliente = new google.maps.Map(document.getElementById("mapa-cliente"), {
					center: { lat: -8.891, lng: -36.495 },
					zoom: 15,
					gestureHandling: 'greedy',
					mapId: "DEMO_MAP_ID",
					mapTypeControl: true, // Habilita o botão Mapa/Satélite
					mapTypeControlOptions: {
						style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
						position: google.maps.ControlPosition.TOP_LEFT
					} // <--- FALTAVA ESTA CHAVE AQUI PARA FECHAR AS OPÇÕES
				});
				
				mapaCliente.addListener("click", (e) => {
					if (marcadorCliente) marcadorCliente.map = null; // Apaga o pino velho
					// NOVO PINO DO GOOGLE (Substitui o Marker descontinuado)
					marcadorCliente = new google.maps.marker.AdvancedMarkerElement({
						position: e.latLng,
						map: mapaCliente
					});
					enderecoAtual.lat = e.latLng.lat();
					enderecoAtual.lng = e.latLng.lng();
				});
			}
		} else {
			if(!mapaCliente) {
				mapaCliente = L.map('mapa-cliente').setView([-8.891, -36.495], 14);
				L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mapaCliente);
				mapaCliente.on('click', function(e) {
					if(marcadorCliente) mapaCliente.removeLayer(marcadorCliente);
					marcadorCliente = L.marker(e.latlng).addTo(mapaCliente);
					enderecoAtual.lat = e.latlng.lat; enderecoAtual.lng = e.latlng.lng;
				});
			}
			setTimeout(() => mapaCliente.invalidateSize(), 200);
		}
	}

    function fecharMapa() { document.getElementById('modal-mapa').classList.add('hidden'); }

    async function confirmarLocalMapa() {
        if(!enderecoAtual.lat) return alert("Clique no mapa para apontar o local.");
        fecharMapa();
        
        if (MAP_PROVIDER === 'google') {
            googleGeocoder.geocode({ location: { lat: enderecoAtual.lat, lng: enderecoAtual.lng } }, (results, status) => {
				if (status === "OK" && results[0]) {
					enderecoAtual.endereco_completo = results[0].formatted_address;
					// Tenta puxar a rua formatada antes da primeira vírgula como plano B
					enderecoAtual.rua = results[0].formatted_address.split(',')[0]; 
					
					for (const comp of results[0].address_components) {
						// Suas variáveis originais
						if (comp.types.includes("route")) enderecoAtual.rua = comp.long_name;
						if (comp.types.includes("sublocality") || comp.types.includes("neighborhood")) enderecoAtual.bairro = comp.long_name;
						
						// NOVAS ADIÇÕES PARA O PRÉ-CADASTRO (Cidade, UF e CEP)
						if (comp.types.includes("administrative_area_level_2")) enderecoAtual.cidade = comp.long_name;
						if (comp.types.includes("administrative_area_level_1")) enderecoAtual.estado = comp.short_name; // Ex: "PE"
						if (comp.types.includes("postal_code")) enderecoAtual.cep = comp.long_name;
					}
					
					inputEndereco.value = enderecoAtual.rua + (enderecoAtual.bairro ? ', ' + enderecoAtual.bairro : '');
				} else {
					// Se falhar de vez, avisa que foi por coordenada
					enderecoAtual.rua = "Endereço por Coordenada";
					inputEndereco.value = "Localização capturada pelo mapa 📍";
				}
				buscarCobertura();
			});
        } else {
            try {
                const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${enderecoAtual.lat}&lon=${enderecoAtual.lng}&addressdetails=1`);
                const dados = await res.json();
                if(dados.address) {
                    // Usa rua, se não tiver usa via de pedestre, se não tiver usa bairro
                    enderecoAtual.rua = dados.address.road || dados.address.pedestrian || dados.address.suburb || 'Endereço por Coordenada';
                    enderecoAtual.bairro = dados.address.suburb || dados.address.neighbourhood || '';
                    enderecoAtual.endereco_completo = dados.display_name;
                    inputEndereco.value = enderecoAtual.rua + (enderecoAtual.bairro ? ', ' + enderecoAtual.bairro : '');
                }
            } catch (e) {
                enderecoAtual.rua = "Endereço por Coordenada";
                inputEndereco.value = "Localização capturada pelo mapa 📍";
            }
            buscarCobertura();
        }
    }

    // ==========================================
    // REQUISIÇÕES AO NOSSO SERVIDOR
    // ==========================================
    async function buscarCobertura() {
        if(!enderecoAtual.lat) {
            enderecoAtual.endereco_completo = inputEndereco.value;
            enderecoAtual.rua = inputEndereco.value;
        }
        if(enderecoAtual.rua.length < 3) return alert('Digite um endereço válido ou aponte no mapa.');

        document.getElementById('box-pesquisa').classList.add('hidden');
        document.getElementById('box-loading').classList.remove('hidden');

        try {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const res = await fetch('/api/check-cobertura', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                body: JSON.stringify(enderecoAtual)
            });
            const data = await res.json();
            document.getElementById('box-loading').classList.add('hidden');

            if(data.tem_cobertura) {
                document.getElementById('lbl-endereco-sucesso').innerText = enderecoAtual.rua;
                document.getElementById('box-sucesso').classList.remove('hidden');
            } else {
                document.getElementById('lbl-endereco-falha').innerText = enderecoAtual.rua || 'sua região';
                document.getElementById('box-sem-cobertura').classList.remove('hidden');
            }
        } catch (e) {
            alert("Erro ao consultar viabilidade.");
            resetarPesquisa();
        }
    }

    function irParaCadastro() {
        const params = new URLSearchParams({
            rua: enderecoAtual.rua, 
            bairro: enderecoAtual.bairro, 
            cidade: enderecoAtual.cidade, 
            estado: enderecoAtual.estado, 
            cep: enderecoAtual.cep
        });
        
        // Verifica se ele escolheu um plano no carrossel e anexa na viagem
        const planoSalvo = sessionStorage.getItem('plano_escolhido_matrix');
        if (planoSalvo) {
            params.append('plano_id', planoSalvo);
        }

        window.location.href = `/planos-disponiveis?${params.toString()}`;
    }

    async function salvarLead(e) {
        e.preventDefault();
        const zap = document.getElementById('input-whatsapp').value;
        if(zap.length < 8) return alert('Digite um WhatsApp válido!');
        
        const pacote = {
            pronome: document.getElementById('input-pronome').value,
            nome: document.getElementById('input-nome').value,
            whatsapp: zap,
            ...enderecoAtual
        };

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        try {
            const res = await fetch('/api/leads', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                body: JSON.stringify(pacote)
            });

            if(res.ok) {
                alert(`Obrigado! Registramos seu interesse na rua ${enderecoAtual.rua}. Entraremos em contato no número ${zap} assim que expandirmos.`);
                resetarPesquisa();
            } else { alert('Erro ao salvar os dados.'); }
        } catch (e) { alert('Erro de conexão com a Matrix.'); }
    }

    function resetarPesquisa() {
        inputEndereco.value = '';
        enderecoAtual = { rua: '', bairro: '', cidade: 'Garanhuns', estado: 'PE', cep: '', lat: null, lng: null, endereco_completo: '' };
        if(marcadorCliente) {
            if (MAP_PROVIDER === 'google') marcadorCliente.map = null;
            else mapaCliente.removeLayer(marcadorCliente);
        }
        document.getElementById('box-sucesso').classList.add('hidden');
        document.getElementById('box-sem-cobertura').classList.add('hidden');
        document.getElementById('box-pesquisa').classList.remove('hidden');
    }
</script>