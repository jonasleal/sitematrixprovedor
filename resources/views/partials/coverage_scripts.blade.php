<script>
    window.MAP_PROVIDER = "{{ env('MAP_PROVIDER', 'osm') }}";

    window.initGoogleMaps = function() {
        if (window.MAP_PROVIDER !== 'google') return;

        const input = document.getElementById('address-input');
        if (!input) return;

        window.googleAutocomplete = new google.maps.places.Autocomplete(input, {
            componentRestrictions: { country: 'br' },
            fields: ['geometry', 'name', 'address_components', 'formatted_address']
        });

        window.googleAutocomplete.addListener('place_changed', () => {
            const place = window.googleAutocomplete.getPlace();
            if (!place || !place.geometry) return;
            window.dispatchEvent(new CustomEvent('google-place-selected', { detail: place }));
        });

        input.addEventListener('input', function(e) {
            // TRAVA DE SEGURANÇA: Impede que atualizações via script (Mapa) disparem nova requisição ViaCEP.
            if (!e.isTrusted) return; 

            let val = e.target.value;
            if (val.match(/^\d/)) {
                let apenasNumeros = val.replace(/\D/g, '');
                
                if (apenasNumeros.length > 0 && apenasNumeros.length <= 8) {
                    let cepFormatado = apenasNumeros;
                    if (cepFormatado.length > 5) cepFormatado = cepFormatado.substring(0, 5) + '-' + cepFormatado.substring(5, 8);
                    e.target.value = cepFormatado;

                    if (apenasNumeros.length === 8) {
                        fetch(`https://viacep.com.br/ws/${apenasNumeros}/json/`)
                        .then(res => res.json())
                        .then(data => {
                            if (!data.erro && data.logradouro) {
                                const enderecoCompletoViaCep = `${data.logradouro}, ${data.bairro}, ${data.localidade} - ${data.uf}`;
                                input.value = enderecoCompletoViaCep;
                                
                                window.dispatchEvent(new CustomEvent('viacep-success', { 
                                    detail: {
                                        enderecoCompleto: enderecoCompletoViaCep,
                                        logradouro: data.logradouro,
                                        bairro: data.bairro,
                                        localidade: data.localidade,
                                        uf: data.uf,
                                        cep: apenasNumeros
                                    }
                                }));
                                document.getElementById('numero-casa-input')?.focus();
                            }
                        });
                    }
                }
            }
        });
        
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); 
                document.getElementById('btn-verificar-cobertura')?.click();
            }
        });
    };
</script>

@if(env('MAP_PROVIDER', 'osm') == 'google')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places,marker&callback=initGoogleMaps" async defer></script>
@else
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endif

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('mapaCobertura', () => ({
            endereco: '',
            numero_casa: '',
            cepDigitado: '',
            buscando: false,
            
            modalMapaAberto: false,
            modalSucessoAberto: false,
            modalLeadAberto: false,
            
            mapaScriptsCarregados: false,
            carregandoMapaInterface: false,
            buscandoGps: false,
            
            mapInstance: null,
            markerInstance: null,
            
            enderecoTempRua: '',
            enderecoTempCompleto: '',
            
            // Isolamento de Estado: Dados ficam retidos aqui até o usuário clicar em "Fixar Endereço"
            tempDados: { lat: null, lng: null, bairro: '', cidade: '', estado: '', cep: '', rua: '', numero: '' },

            toast: { show: false, message: '', type: 'error' },
            
            leadDados: { lat: null, lng: null, bairro: '', cidade: '', estado: '' },
            enviandoLead: false,
            leadForm: { pronome: '', nome: '', whatsapp: '', endereco_pesquisado: '' },

            init() {
                window.addEventListener('google-place-selected', (e) => {
                    const place = e.detail;
                    this.processarGoogleAddress(place);
                    this.mostrarToast('Localização identificada! Insira o número da casa (se houver) e verifique.', 'success');
                });

                window.addEventListener('viacep-success', (e) => {
                    this.endereco = e.detail.enderecoCompleto;
                    this.leadDados.bairro = e.detail.bairro;
                    this.leadDados.cidade = e.detail.localidade;
                    this.leadDados.estado = e.detail.uf;
                    this.cepDigitado = e.detail.cep;
                    this.leadDados.lat = null;
                    this.leadDados.lng = null;
                });
            },

            mostrarToast(msg, type = 'error') {
                this.toast.message = msg;
                this.toast.type = type;
                this.toast.show = true;
                setTimeout(() => this.toast.show = false, 5000);
            },

            fecharModais() {
                this.modalSucessoAberto = false;
                this.modalLeadAberto = false;
            },

            linkPreCadastro() {
                let url = new URL("{{ route('precadastro') }}", window.location.origin);
                let ruaApenas = this.endereco ? this.endereco.split(',')[0].trim() : '';
                
                if(ruaApenas) url.searchParams.append('rua', ruaApenas);
                if(this.numero_casa) url.searchParams.append('numero', this.numero_casa);
                if(this.leadDados.bairro) url.searchParams.append('bairro', this.leadDados.bairro);
                if(this.leadDados.cidade) url.searchParams.append('cidade', this.leadDados.cidade);
                if(this.leadDados.estado) url.searchParams.append('estado', this.leadDados.estado);
                if(this.cepDigitado) url.searchParams.append('cep', this.cepDigitado);
                
                return url.toString();
            },

            abrirModalMapa() {
                this.modalMapaAberto = true;
                document.body.style.overflow = 'hidden';
                
                if (!this.mapaScriptsCarregados) {
                    this.carregandoMapaInterface = true;
                    this.mapaScriptsCarregados = true;
                    
                    if (window.MAP_PROVIDER === 'google') {
                        if (typeof google === 'undefined' || !google.maps.Map) {
                            const script = document.createElement('script');
                            script.src = `https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places,marker`;
                            script.async = true;
                            script.onload = () => this.inicializarMapaModal();
                            document.head.appendChild(script);
                        } else {
                            this.inicializarMapaModal();
                        }
                    } else {
                        if(typeof L === 'undefined') {
                            const css = document.createElement('link');
                            css.rel = 'stylesheet';
                            css.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                            document.head.appendChild(css);
                            const script = document.createElement('script');
                            script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                            script.onload = () => this.inicializarMapaModal();
                            document.head.appendChild(script);
                        } else {
                            this.inicializarMapaModal();
                        }
                    }
                } else {
                    setTimeout(() => {
                        if (window.MAP_PROVIDER !== 'google' && this.mapInstance) this.mapInstance.invalidateSize();
                    }, 300);
                }
            },

            fecharModalMapa() {
                this.modalMapaAberto = false;
                document.body.style.overflow = '';
            },

            inicializarMapaModal() {
                const center = this.leadDados.lat ? { lat: this.leadDados.lat, lng: this.leadDados.lng } : { lat: -8.8911, lng: -36.4950 };

                if (window.MAP_PROVIDER === 'google') {
                    this.mapInstance = new google.maps.Map(document.getElementById("map-modal-container"), {
                        zoom: 15,
                        center: center,
                        mapId: '{{ env("GOOGLE_MAPS_ID", "DEMO_MAP_ID") }}',
                        disableDefaultUI: false, 
                        mapTypeControl: true, 
                        mapTypeControlOptions: {
                            style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
                            position: google.maps.ControlPosition.TOP_LEFT,
                        },
                        fullscreenControl: false,
                        streetViewControl: false,
                        gestureHandling: 'greedy'
                    });

                    this.injetarBotaoGpsNativoGoogle();

                    this.mapInstance.addListener('click', (e) => {
                        this.moverPinoReverso(e.latLng.lat(), e.latLng.lng());
                    });
                } else {
                    this.mapInstance = L.map('map-modal-container', { zoomControl: false }).setView([center.lat, center.lng], 15);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(this.mapInstance);
                    L.control.zoom({ position: 'topright' }).addTo(this.mapInstance);

                    this.injetarBotaoGpsLeaflet();

                    this.mapInstance.on('click', (e) => {
                        this.moverPinoReverso(e.latlng.lat, e.latlng.lng);
                    });
                }
                
                this.carregandoMapaInterface = false;
                if(this.leadDados.lat) this.moverPinoReverso(this.leadDados.lat, this.leadDados.lng);
            },

            injetarBotaoGpsNativoGoogle() {
                const controlDiv = document.createElement('div');
                controlDiv.className = 'm-3';
                const controlUI = document.createElement('button');
                controlUI.type = 'button';
                controlUI.title = 'Minha Localização (GPS)';
                controlUI.className = 'bg-gray-900/90 text-cyan-400 p-3.5 rounded-full shadow-[0_0_15px_rgba(6,182,212,0.4)] border border-gray-700 hover:bg-gray-800 transition transform active:scale-95 flex items-center justify-center backdrop-blur-md cursor-pointer';
                controlUI.innerHTML = '<i class="fas fa-crosshairs text-xl" id="gps-icon-google"></i>';
                controlUI.addEventListener('click', () => { this.pegarLocalizacaoGPS(); });
                controlDiv.appendChild(controlUI);
                this.mapInstance.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(controlDiv);
            },

            injetarBotaoGpsLeaflet() {
                const container = document.getElementById("map-modal-container");
                let btn = document.getElementById("btn-gps-leaflet");
                if (!btn) {
                    btn = document.createElement('button');
                    btn.id = "btn-gps-leaflet";
                    btn.type = 'button';
                    btn.title = 'Minha Localização (GPS)';
                    btn.className = 'absolute bottom-6 right-4 sm:right-6 bg-gray-900/90 text-cyan-400 p-4 rounded-full shadow-[0_0_20px_rgba(6,182,212,0.4)] border border-gray-700 hover:bg-gray-800 transition transform active:scale-95 z-[1000] backdrop-blur-md cursor-pointer';
                    btn.innerHTML = '<i class="fas fa-crosshairs text-2xl" id="gps-icon-leaflet"></i>';
                    btn.addEventListener('click', () => this.pegarLocalizacaoGPS());
                    container.appendChild(btn);
                }
            },

            moverPinoReverso(lat, lng) {
                this.tempDados.lat = lat;
                this.tempDados.lng = lng;

                // Renderiza o Pino de forma robusta e garante que seja Único
                if (window.MAP_PROVIDER === 'google') {
                    if (this.markerInstance) {
                        this.markerInstance.map = null;
                    }
                    this.markerInstance = new google.maps.marker.AdvancedMarkerElement({
                        position: { lat, lng },
                        map: this.mapInstance
                    });
                } else {
                    if (this.markerInstance) this.mapInstance.removeLayer(this.markerInstance);
                    this.markerInstance = L.marker([lat, lng]).addTo(this.mapInstance);
                }

                this.enderecoTempCompleto = "Traduzindo coordenada...";
                this.enderecoTempRua = "";
                
                if (window.MAP_PROVIDER === 'google') {
                    new google.maps.Geocoder().geocode({ location: { lat, lng } }, (results, status) => {
                        if (status === 'OK' && results[0]) {
                            this.processarGoogleAddressParaTemp(results[0]); 
                        } else {
                            this.enderecoTempCompleto = "Endereço desconhecido";
                        }
                    });
                } else {
                    fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.display_name) this.processarOSMAddressParaTemp(data);
                            else this.enderecoTempCompleto = "Endereço desconhecido";
                        });
                }
            },

            // Salva dados na "Memória Temporária" antes de confirmar
            processarGoogleAddressParaTemp(googleResult) {
                let rua = '', bairro = '', cidade = '', estado = '', cep = '', numero = '';

                googleResult.address_components.forEach(comp => {
                    if (comp.types.includes('route')) rua = comp.long_name;
                    if (comp.types.includes('street_number')) numero = comp.long_name;
                    if (comp.types.includes('postal_code')) cep = comp.long_name;
                    if (comp.types.includes('sublocality') || comp.types.includes('neighborhood')) bairro = comp.long_name;
                    if (comp.types.includes('administrative_area_level_2')) cidade = comp.long_name;
                    if (comp.types.includes('administrative_area_level_1')) estado = comp.short_name;
                });
                
                if(!cidade) {
                    const locality = googleResult.address_components.find(c => c.types.includes('locality'));
                    if(locality) cidade = locality.long_name;
                }

                this.tempDados.rua = rua;
                this.tempDados.numero = numero;
                this.tempDados.bairro = bairro;
                this.tempDados.cidade = cidade;
                this.tempDados.estado = estado;
                this.tempDados.cep = cep ? cep.replace(/\D/g, '') : '';
                
                this.enderecoTempCompleto = googleResult.formatted_address;
                this.enderecoTempRua = rua || (googleResult.formatted_address.split(',')[0].trim());
            },

            processarOSMAddressParaTemp(data) {
                this.enderecoTempCompleto = data.display_name;
                this.tempDados.rua = data.address?.road || data.address?.pedestrian || '';
                this.tempDados.numero = data.address?.house_number || '';
                this.tempDados.bairro = data.address?.suburb || data.address?.neighbourhood || '';
                this.tempDados.cidade = data.address?.city || data.address?.town || data.address?.village || '';
                this.tempDados.estado = data.address?.state || '';
                if (data.address?.postcode) this.tempDados.cep = data.address.postcode.replace(/\D/g, '');

                this.enderecoTempRua = this.tempDados.rua || data.display_name.split(',')[0].trim();
            },

            pegarLocalizacaoGPS() {
                if (!navigator.geolocation) {
                    this.mostrarToast("Navegador incompatível com GPS.");
                    return;
                }

                this.buscandoGps = true;
                const iconGoogle = document.getElementById('gps-icon-google');
                const iconLeaflet = document.getElementById('gps-icon-leaflet');
                if (iconGoogle) iconGoogle.classList.add('fa-spin');
                if (iconLeaflet) iconLeaflet.classList.add('fa-spin');

                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        this.buscandoGps = false;
                        if (iconGoogle) iconGoogle.classList.remove('fa-spin');
                        if (iconLeaflet) iconLeaflet.classList.remove('fa-spin');

                        const lat = pos.coords.latitude;
                        const lng = pos.coords.longitude;
                        
                        if (window.MAP_PROVIDER === 'google') {
                            this.mapInstance.setCenter({ lat, lng });
                            this.mapInstance.setZoom(18);
                        } else {
                            this.mapInstance.setView([lat, lng], 18);
                        }
                        
                        this.moverPinoReverso(lat, lng);
                    },
                    (err) => {
                        this.buscandoGps = false;
                        if (iconGoogle) iconGoogle.classList.remove('fa-spin');
                        if (iconLeaflet) iconLeaflet.classList.remove('fa-spin');
                        this.mostrarToast("Sinal de GPS ausente ou negado. Mova o mapa manualmente.", "warning");
                    },
                    { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
                );
            },

            // Confirma a ação, populando o formulário sem disparar o ViaCEP
            confirmarEnderecoMapa() {
                if(!this.tempDados.lat) return;
                
                // Transfere os dados isolados para as variáveis principais do formulário
                this.leadDados.lat = this.tempDados.lat;
                this.leadDados.lng = this.tempDados.lng;
                this.leadDados.bairro = this.tempDados.bairro;
                this.leadDados.cidade = this.tempDados.cidade;
                this.leadDados.estado = this.tempDados.estado;
                
                if(this.tempDados.cep) this.cepDigitado = this.tempDados.cep;
                
                // Atualiza visualmente via ID e também sincroniza o modelo do Alpine
                const inputVisual = document.getElementById('address-input');
                if(inputVisual) inputVisual.value = this.enderecoTempRua;
                this.endereco = this.enderecoTempRua;
                
                if(this.tempDados.numero) this.numero_casa = this.tempDados.numero;
                
                this.fecharModalMapa();
                this.mostrarToast("Endereço fixado com sucesso!", "success");
            },

            async verificarCobertura() {
                const inputVisual = document.getElementById('address-input');
                let textoDigitado = inputVisual ? inputVisual.value.trim() : this.endereco;

                if (!textoDigitado) {
                    this.mostrarToast("Digite o CEP, a Rua ou marque a casa no Mapa Interativo.", "warning");
                    return;
                }
                
                this.buscando = true;
                let enderecoPesquisa = textoDigitado;

                let limpo = textoDigitado.replace(/\D/g, '');
                let isCepCompleto = (limpo.length === 8);

                if (isCepCompleto) {
                    this.cepDigitado = limpo;
                    try {
                        const viaCepReq = await fetch(`https://viacep.com.br/ws/${limpo}/json/`);
                        const viaCepRes = await viaCepReq.json();
                        if (!viaCepRes.erro) {
                            enderecoPesquisa = `${viaCepRes.logradouro}, ${viaCepRes.bairro}, ${viaCepRes.localidade} - ${viaCepRes.uf}`;
                            if(inputVisual) inputVisual.value = enderecoPesquisa; 
                            this.endereco = enderecoPesquisa; 
                            this.leadDados.bairro = viaCepRes.bairro;
                            this.leadDados.cidade = viaCepRes.localidade;
                            this.leadDados.estado = viaCepRes.uf;
                        }
                    } catch(e) {}
                }

                const buscaCompleta = enderecoPesquisa + (this.numero_casa ? ', ' + this.numero_casa : '');

                if (this.leadDados.lat && this.leadDados.lng && !isCepCompleto) {
                    this.dispararChecagemBackend(buscaCompleta);
                    return;
                }

                try {
                    if (window.MAP_PROVIDER === 'google') {
                        if (typeof google === 'undefined' || !google.maps.Geocoder) {
                            await this.geolocalizarViaApiHttp('google', buscaCompleta);
                        } else {
                            await new Promise((resolve, reject) => {
                                new google.maps.Geocoder().geocode({ address: buscaCompleta, componentRestrictions: { country: 'BR' } }, (results, status) => {
                                    if (status === 'OK') {
                                        this.processarGoogleAddress(results[0]);
                                        resolve(); 
                                    } else reject();
                                });
                            });
                        }
                    } else {
                        await this.geolocalizarViaApiHttp('osm', buscaCompleta);
                    }
                    this.dispararChecagemBackend(buscaCompleta);
                } catch (e) {
                    this.buscando = false;
                    this.mostrarToast("Não conseguimos processar as coordenadas. Use o Mapa Interativo.", "error");
                }
            },

            async geolocalizarViaApiHttp(provedor, busca) {
                if (provedor === 'google') {
                    const url = `https://maps.googleapis.com/maps/api/geocode/json?address=${encodeURIComponent(busca)}&key={{ env('GOOGLE_MAPS_API_KEY') }}`;
                    const res = await fetch(url);
                    const data = await res.json();
                    if (data.status === 'OK') this.processarGoogleAddress(data.results[0]);
                    else throw new Error();
                } else {
                    const url = `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(busca)}&format=json&addressdetails=1`;
                    const res = await fetch(url);
                    const data = await res.json();
                    if (data && data.length > 0) {
                        this.leadDados.lat = data[0].lat;
                        this.leadDados.lng = data[0].lon;
                        this.leadDados.bairro = data[0].address?.suburb || data[0].address?.neighbourhood || '';
                        this.leadDados.cidade = data[0].address?.city || data[0].address?.town || '';
                        if (data[0].address?.postcode) this.cepDigitado = data[0].address.postcode.replace(/\D/g, '');
                    } else throw new Error();
                }
            },

            // Preservado para manter a busca da Barra/Autocomplete funcionando normal
            processarGoogleAddress(googleResult) {
                this.leadDados.lat = googleResult.geometry.location.lat ? googleResult.geometry.location.lat() : googleResult.geometry.location.lat;
                this.leadDados.lng = googleResult.geometry.location.lng ? googleResult.geometry.location.lng() : googleResult.geometry.location.lng;
                
                let bairro = '', cidade = '', estado = '', rua = '', cep = '';

                googleResult.address_components.forEach(comp => {
                    if (comp.types.includes('route')) rua = comp.long_name;
                    if (comp.types.includes('postal_code')) cep = comp.long_name;
                    if (comp.types.includes('sublocality') || comp.types.includes('neighborhood')) bairro = comp.long_name;
                    if (comp.types.includes('administrative_area_level_2')) cidade = comp.long_name;
                    if (comp.types.includes('administrative_area_level_1')) estado = comp.short_name;
                });
                
                if(!cidade) {
                    const locality = googleResult.address_components.find(c => c.types.includes('locality'));
                    if(locality) cidade = locality.long_name;
                }

                this.leadDados.bairro = bairro;
                this.leadDados.cidade = cidade;
                this.leadDados.estado = estado;
                if(cep) this.cepDigitado = cep;
                
                if(rua) {
                    this.endereco = rua;
                    const inputVisual = document.getElementById('address-input');
                    if(inputVisual) inputVisual.value = rua;
                }
            },

            dispararChecagemBackend(buscaCompleta) {
                let ruaF = this.endereco ? this.endereco.split(',')[0].trim() : buscaCompleta.split(',')[0].trim();
                let numF = this.numero_casa ? `, Nº ${this.numero_casa}` : '';
                let baiF = this.leadDados.bairro ? `, ${this.leadDados.bairro}` : '';
                let cidF = this.leadDados.cidade ? `, ${this.leadDados.cidade}` : '';
                let ufF  = this.leadDados.estado ? ` - ${this.leadDados.estado}` : '';

                this.leadForm.endereco_pesquisado = `${ruaF}${numF}${baiF}${cidF}${ufF}`;

                fetch('/api/check-cobertura', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    body: JSON.stringify({ lat: this.leadDados.lat, lng: this.leadDados.lng })
                })
                .then(res => res.json())
                .then(data => {
                    this.buscando = false;
                    if (data.tem_cobertura) {
                        this.abrirModalSucesso();
                    } else {
                        this.modalLeadAberto = true;
                    }
                })
                .catch(() => {
                    this.buscando = false;
                    this.mostrarToast('A comunicação com o sistema falhou. Tente novamente.', 'error');
                });
            },

            abrirModalSucesso() {
                this.modalSucessoAberto = true;
                if (typeof confetti === 'function') {
                    confetti({ particleCount: 150, spread: 80, origin: { y: 0.6 }, colors: ['#22d3ee', '#34d399', '#818cf8'] });
                }
            },

            async salvarLead() {
                this.enviandoLead = true;
                
                const pacote = {
                    pronome: this.leadForm.pronome,
                    nome: this.leadForm.nome,
                    whatsapp: this.leadForm.whatsapp,
                    lat: this.leadDados.lat,
                    lng: this.leadDados.lng,
                    bairro: this.leadDados.bairro,
                    cidade: this.leadDados.cidade,
                    estado: this.leadDados.estado,
                    endereco: this.leadForm.endereco_pesquisado,
                    endereco_completo: this.leadForm.endereco_pesquisado 
                };

                try {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const res = await fetch('/api/leads', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                        body: JSON.stringify(pacote)
                    });
                    
                    if (res.ok) {
                        this.fecharModais();
                        this.mostrarToast(`Obrigado! Registramos seu interesse. Nossa equipe vai te chamar no WhatsApp em breve!`, 'success');
                        const inputVisual = document.getElementById('address-input');
                        if(inputVisual) inputVisual.value = '';
                        this.endereco = '';
                        this.numero_casa = '';
                        this.cepDigitado = '';
                        this.leadDados = { lat: null, lng: null, bairro: '', cidade: '', estado: '' };
                    } else {
                        this.mostrarToast('Ocorreu um erro ao salvar seus dados. Verifique as informações.', 'error');
                    }
                } catch (e) {
                    this.mostrarToast('Erro de conexão com o banco de dados.', 'error');
                }
                this.enviandoLead = false;
            }

        }));
    });
</script>