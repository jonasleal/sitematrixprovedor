<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste Isolado de Autocomplete</title>

    <style>
        body { font-family: Arial, sans-serif; padding: 40px; background-color: #111827; color: #ffffff; }
        .container { max-width: 600px; margin: 0 auto; background: #1f2937; padding: 24px; border-radius: 12px; border: 1px solid #374151; }
        label { display: block; margin-bottom: 8px; font-weight: bold; font-size: 14px; color: #9ca3af; }
        input[type="text"] { width: 100%; padding: 14px; font-size: 16px; border: 1px solid #4b5563; border-radius: 8px; background-color: #111827; color: #ffffff; outline: none; }
        input[type="text"]:focus { border-color: #22d3ee; }
        .box-info { margin-top: 20px; padding: 16px; background-color: #111827; border: 1px solid #374151; border-radius: 8px; font-size: 14px; color: #38bdf8; }
    </style>
</head>
<body>

    <div class="container">
        <h2>Diagnóstico Isolado (Mecânica Original)</h2>
        <label for="input-endereco">Campo de Endereço / CEP</label>
        <input type="text" id="input-endereco" placeholder="Ex: Rua São Vicente ou 55294" autocomplete="off">

        <div class="box-info" id="debug-info">
            <strong>Status:</strong> Carregando API do Google...
        </div>
    </div>

    <script>
        let autocomplete;

        function initAutocomplete() {
            const input = document.getElementById('input-endereco');

            // 1. Configuração do Google Autocomplete
            autocomplete = new google.maps.places.Autocomplete(input, {
                componentRestrictions: { country: 'br' },
                fields: ['geometry', 'name', 'address_components', 'formatted_address']
            });

            autocomplete.addListener('place_changed', onPlaceChanged);
            document.getElementById('debug-info').innerHTML = '<strong>Status:</strong> <span style="color: #4ade80;">Google Places ativo! Teste um CEP.</span>';

            // 2. A MÁGICA ORIGINAL RESTAURADA: O Ouvinte de Digitação
            input.addEventListener('input', function(e) {
                let val = e.target.value;
                
                // Se começar digitando números, aplica a lógica de CEP
                if (val.match(/^\d/)) {
                    let apenasNumeros = val.replace(/\D/g, '');
                    
                    if (apenasNumeros.length > 0 && apenasNumeros.length <= 8) {
                        let cepFormatado = apenasNumeros;
                        
                        // Máscara em tempo real (O Hífen que faz o Google reconhecer os CEPs parciais)
                        if (cepFormatado.length > 5) {
                            cepFormatado = cepFormatado.substring(0, 5) + '-' + cepFormatado.substring(5, 8);
                        }
                        e.target.value = cepFormatado;

                        // Se bateu 8 números exatos, aciona o ViaCEP para trocar pelo nome da rua
                        if (apenasNumeros.length === 8) {
                            fetch(`https://viacep.com.br/ws/${apenasNumeros}/json/`)
                            .then(res => res.json())
                            .then(data => {
                                if (!data.erro && data.logradouro) {
                                    e.target.value = `${data.logradouro}, ${data.bairro}, ${data.localidade} - ${data.uf}`;
                                    // Foca no input e limpa o aviso para o usuário prosseguir
                                    input.focus();
                                }
                            });
                        }
                    }
                }
            });
        }

        function onPlaceChanged() {
            const place = autocomplete.getPlace();
            if (!place || !place.geometry) return;

            document.getElementById('debug-info').innerHTML = `
                <strong>Lugar selecionado:</strong> ${place.formatted_address || place.name}<br>
                <strong>Lat/Lng:</strong> ${place.geometry.location.lat()}, ${place.geometry.location.lng()}
            `;
        }
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initAutocomplete" async defer></script>

</body>
</html>