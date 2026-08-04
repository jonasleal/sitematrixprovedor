<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mapa de Cobertura (Geofencing)') }}
        </h2>
    </x-slot>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-6">
            
            <div class="bg-white p-6 shadow-sm sm:rounded-lg col-span-1 h-fit">
                <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">Salvar Nova Área</h3>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Nome da Área/CTO</label>
                    <input type="text" id="poly-nome" placeholder="Ex: Bairro Magano (CTO 1 a 5)" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Cor de Identificação</label>
                    <input type="color" id="poly-cor" value="#81c700" class="mt-1 block w-full h-10 rounded-md border-gray-300 shadow-sm cursor-pointer">
                </div>

                <p class="text-xs text-gray-500 mb-4">
                    1. Desenhe no mapa ao lado.<br>
                    2. Use o botão de Editar (lápis) no mapa para ajustar pontos.<br>
                    3. Clique em Salvar abaixo.
                </p>

                <button onclick="salvarPoligono()" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition">
                    Salvar Área no Sistema
                </button>

                <meta name="csrf-token" content="{{ csrf_token() }}">
            </div>

            <div class="bg-white p-2 shadow-sm sm:rounded-lg col-span-1 md:col-span-3">
                <div id="mapa-matrix" style="height: 600px; width: 100%; border-radius: 8px; z-index: 1;"></div>
            </div>

        </div>
    </div>

    <script>
        // Variável global para guardar as coordenadas atuais antes de salvar um NOVO
		let coordenadasAtuais = null;

		document.addEventListener('DOMContentLoaded', function() {
        var map = L.map('mapa-matrix').setView([-8.891, -36.495], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap',
            maxZoom: 19
        }).addTo(map);

        var drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        var drawControl = new L.Control.Draw({
            draw: {
                polygon: { shapeOptions: { color: document.getElementById('poly-cor').value, weight: 3 } },
                polyline: false, rectangle: false, circle: false, circlemarker: false, marker: false
            },
            edit: { featureGroup: drawnItems }
        });
        map.addControl(drawControl);

        document.getElementById('poly-cor').addEventListener('change', function() {
            drawControl.setDrawingOptions({ polygon: { shapeOptions: { color: this.value } } });
        });

        // BUSCAR POLÍGONOS E GUARDAR O ID
        fetch('/admin/mapa-cobertura/data')
            .then(async response => {
                if (!response.ok) throw new Error('Erro ao buscar dados');
                return response.json();
            })
            .then(data => {
                data.forEach(item => {
                    var polygon = L.polygon(item.coordenadas, { color: item.cor, weight: 3 }).addTo(drawnItems);
                    polygon.bindPopup("<b>" + item.nome + "</b>");
                    
                    // --- NOVO: SALVA O ID DO BANCO DE DADOS DENTRO DO DESENHO ---
                    polygon.db_id = item.id; 
                });
            });

        // EVENTOS DO MAPA
        map.on(L.Draw.Event.CREATED, function (e) {
            drawnItems.clearLayers(); // Força a pessoa a salvar um por vez
            drawnItems.addLayer(e.layer);
            coordenadasAtuais = e.layer.getLatLngs()[0];
        });

        // --- NOVO: QUANDO CLICAR NO LÁPIS E DEPOIS EM SAVE NO MAPA ---
        map.on(L.Draw.Event.EDITED, function (e) {
            e.layers.eachLayer(function (layer) {
                if (layer.db_id) {
                    // Se já existe no banco, atualiza!
                    atualizarPoligono(layer.db_id, layer.getLatLngs()[0]);
                }
            });
        });

        // --- NOVO: QUANDO CLICAR NA LIXEIRA E DEPOIS EM SAVE NO MAPA ---
        map.on(L.Draw.Event.DELETED, function (e) {
            e.layers.eachLayer(function (layer) {
                if (layer.db_id) {
                    // Se existe no banco, deleta!
                    deletarPoligono(layer.db_id);
                }
            });
        });
    });

    // --- FUNÇÕES DE COMUNICAÇÃO COM O SERVIDOR ---
    const getToken = () => document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Salvar Novo (Permanece igual)
    async function salvarPoligono() {
        const nome = document.getElementById('poly-nome').value;
        const cor = document.getElementById('poly-cor').value;
        if(!nome || !coordenadasAtuais) return alert('Preencha o nome e desenhe o polígono!');

        try {
            const response = await fetch('/admin/mapa-cobertura', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getToken() },
                body: JSON.stringify({ nome: nome, cor: cor, coordenadas: coordenadasAtuais })
            });
            if(response.ok) {
                window.location.reload();
            } else {
                alert('Erro ao salvar nova área.');
            }
        } catch (error) { alert('Erro no servidor.'); }
    }

    // Atualizar Existente
    async function atualizarPoligono(id, novasCoordenadas) {
        try {
            const response = await fetch('/admin/mapa-cobertura/' + id, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getToken() },
                body: JSON.stringify({ coordenadas: novasCoordenadas })
            });
            
            if(!response.ok) alert('Erro ao atualizar a área no banco de dados!');
        } catch (error) { console.error(error); }
    }

    // Deletar Existente
    async function deletarPoligono(id) {
        try {
            const response = await fetch('/admin/mapa-cobertura/' + id, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': getToken() }
            });
            
            if(!response.ok) alert('Erro ao deletar a área do banco de dados!');
        } catch (error) { console.error(error); }
    }
    </script>
</x-app-layout>