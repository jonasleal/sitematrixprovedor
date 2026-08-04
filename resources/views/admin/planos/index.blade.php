<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Personalização de Planos (Site Público)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 overflow-x-auto">
                    
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700 text-sm uppercase tracking-wider">
                                <th class="p-4">ID SGP</th>
                                <th class="p-4">Nome SGP / Personalizado</th>
                                <th class="p-4">Valor SGP / Personalizado</th>
                                <th class="p-4 text-center">Destaque</th>
                                <th class="p-4 text-center">Status Site</th>
                                <th class="p-4 text-right">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            @forelse($planosCompletos as $p)
                                @php
                                    $local = $p['personalizacao'];
                                    $isAtivo = $local ? $local->ativo : true; 
                                    $isDestaque = $local ? $local->destaque : false;
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <td class="p-4 font-mono font-bold">#{{ $p['sgp_id'] }}</td>
                                    <td class="p-4">
                                        <div class="font-bold">
                                            {{ $local && $local->nome_personalizado ? $local->nome_personalizado : $p['nome_sgp'] }}
                                        </div>
                                        @if($local && $local->nome_personalizado)
                                            <span class="text-xs text-gray-500">SGP: {{ $p['nome_sgp'] }}</span>
                                        @endif
                                    </td>
                                    <td class="p-4">
                                        <div class="font-bold text-green-600 dark:text-green-400">
                                            R$ {{ number_format($local && $local->preco_personalizado ? $local->preco_personalizado : $p['preco_sgp'], 2, ',', '.') }}
                                        </div>
                                        @if($local && $local->preco_personalizado)
                                            <span class="text-xs text-gray-500">SGP: R$ {{ number_format($p['preco_sgp'], 2, ',', '.') }}</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($isDestaque)
                                            <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-1 rounded-full">Sim ⭐</span>
                                        @else
                                            <span class="text-gray-500 text-xs">Não</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($isAtivo)
                                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Exibindo</span>
                                        @else
                                            <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">Oculto</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-right">
                                        <button onclick='abrirModalModalPersonalizar(@json($p))' class="bg-blue-500 hover:bg-blue-600 text-white font-bold px-4 py-2 rounded text-xs transition">
                                            Editar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-gray-500">
                                        Nenhum plano retornado pelo SGP no momento.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

    <div id="modal-editar" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 w-full max-w-2xl rounded-lg shadow-xl overflow-hidden">
            <div class="p-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-gray-900 dark:text-white font-bold text-lg" id="modal-titulo">Personalizar Plano</h3>
                <button onclick="fecharModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-white font-bold text-xl">&times;</button>
            </div>

            <form action="{{ route('admin.planos.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="sgp_plano_id" id="modal-sgp-id">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Velocidade Down (ex: 500)</label>
                        <input type="number" name="velocidade_down" id="modal-vel-down" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Desconto R$ (Opcional)</label>
                        <input type="number" step="0.01" name="desconto" id="modal-desconto" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Ordem (1, 2, 3...)</label>
                        <input type="number" name="ordem" id="modal-ordem" value="999" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Tópicos de Benefícios (Um por linha)</label>
                    <textarea name="beneficios_texto" id="modal-beneficios" rows="4" placeholder="Ex: 100% Fibra Óptica" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md text-sm"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div>
                        <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Validade: Início (Opcional)</label>
                        <input type="datetime-local" name="data_inicio" id="modal-data-inicio" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Validade: Fim (Opcional)</label>
                        <input type="datetime-local" name="data_fim" id="modal-data-fim" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="flex items-center space-x-2 cursor-pointer mt-2">
                            <input type="checkbox" name="ocultar_apos_vencimento" id="modal-ocultar-vencimento" value="1" class="rounded border-gray-300 dark:border-gray-700 text-indigo-600">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Esconder o plano do site automaticamente se passar da Data Fim</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center space-x-6 pt-2">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="ativo" id="modal-ativo" value="1" class="rounded border-gray-300 dark:border-gray-700 text-indigo-600">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Exibir no Site</span>
                    </label>

                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="destaque" id="modal-destaque" value="1" class="rounded border-gray-300 dark:border-gray-700 text-yellow-500">
                        <span class="text-sm font-semibold text-yellow-600 dark:text-yellow-500">Marcar como Destaque ⭐</span>
                    </label>
                </div>

                <div class="pt-4 flex justify-end gap-3 mt-4">
                    <button type="button" onclick="fecharModal()" class="px-4 py-2 text-gray-600 dark:text-gray-400 text-sm">Cancelar</button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-2 rounded-md text-sm">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
                function abrirModalModalPersonalizar(dados) {
                    const local = dados.personalizacao;
                    document.getElementById('modal-sgp-id').value = dados.sgp_id;
                    document.getElementById('modal-titulo').innerText = `Personalizar Plano #${dados.sgp_id}`;
                    
                    document.getElementById('modal-vel-down').value = local ? (local.velocidade_down || '') : '';
                    document.getElementById('modal-desconto').value = local ? (local.desconto || '') : '';
                    document.getElementById('modal-ordem').value = local ? (local.ordem || '999') : '999';
                    
                    document.getElementById('modal-data-inicio').value = local && local.data_inicio ? local.data_inicio.replace(' ', 'T').substring(0, 16) : '';
                    document.getElementById('modal-data-fim').value = local && local.data_fim ? local.data_fim.replace(' ', 'T').substring(0, 16) : '';
                    
                    document.getElementById('modal-ativo').checked = local ? Boolean(local.ativo) : true;
                    document.getElementById('modal-destaque').checked = local ? Boolean(local.destaque) : false;
                    document.getElementById('modal-ocultar-vencimento').checked = local ? Boolean(local.ocultar_apos_vencimento) : true;

                    if (local && local.topicos_beneficios) {
                        try {
                            const arr = JSON.parse(local.topicos_beneficios);
                            document.getElementById('modal-beneficios').value = Array.isArray(arr) ? arr.join('\n') : '';
                        } catch(e) { document.getElementById('modal-beneficios').value = ''; }
                    } else { document.getElementById('modal-beneficios').value = ''; }

                    document.getElementById('modal-editar').classList.remove('hidden');
                }
                function fecharModal() { document.getElementById('modal-editar').classList.add('hidden'); }
            </script>
</x-app-layout>