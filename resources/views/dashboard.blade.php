<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-indigo-600">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
            </svg>
            {{ __('Gestão de Leads & Demanda de Expansão') }}
        </h2>
    </x-slot>

    <div class="py-8" x-data="crmLeads()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-xl flex items-center font-semibold shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-xl flex items-center font-semibold shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                
                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Leads</span>
                        <h3 class="text-2xl font-black text-gray-800 mt-1">{{ $metricas['total'] }}</h3>
                    </div>
                    <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-red-500">Reprimidos</span>
                        <h3 class="text-2xl font-black text-red-600 mt-1">{{ $metricas['reprimido'] }}</h3>
                    </div>
                    <div class="p-3 bg-red-50 text-red-500 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-yellow-600">Em Estudo</span>
                        <h3 class="text-2xl font-black text-yellow-600 mt-1">{{ $metricas['estudo'] }}</h3>
                    </div>
                    <div class="p-3 bg-yellow-50 text-yellow-600 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9zm3.75 11.625a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-blue-600">Aprovados</span>
                        <h3 class="text-2xl font-black text-blue-600 mt-1">{{ $metricas['aprovado'] }}</h3>
                    </div>
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between col-span-2 md:col-span-1">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-green-600">Rede Liberada</span>
                        <h3 class="text-2xl font-black text-green-600 mt-1">{{ $metricas['liberado'] }}</h3>
                    </div>
                    <div class="p-3 bg-green-50 text-green-600 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 011.06 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200">
                <form method="GET" action="{{ route('dashboard') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                    
                    <div class="md:col-span-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Buscar por Nome, WhatsApp ou Endereço</label>
                        <div class="relative">
                            <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Digite para pesquisar..." class="w-full pl-9 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 absolute left-3 top-3 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Cidade</label>
                        <select name="cidade" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">Todas</option>
                            @foreach($cidades as $cid)
                                <option value="{{ $cid }}" {{ request('cidade') == $cid ? 'selected' : '' }}>{{ $cid }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Bairro</label>
                        <select name="bairro" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">Todos</option>
                            @foreach($bairros as $bai)
                                <option value="{{ $bai }}" {{ request('bairro') == $bai ? 'selected' : '' }}>{{ $bai }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status</label>
                        <select name="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">Todos</option>
                            <option value="Demanda Reprimida" {{ request('status') == 'Demanda Reprimida' ? 'selected' : '' }}>🔴 Demanda Reprimida</option>
                            <option value="Em Estudo" {{ request('status') == 'Em Estudo' ? 'selected' : '' }}>🟡 Em Estudo</option>
                            <option value="Projeto Aprovado" {{ request('status') == 'Projeto Aprovado' ? 'selected' : '' }}>🟠 Projeto Aprovado</option>
                            <option value="Rede Liberada" {{ request('status') == 'Rede Liberada' ? 'selected' : '' }}>🟢 Rede Liberada</option>
                            <option value="Descartado" {{ request('status') == 'Descartado' ? 'selected' : '' }}>⚫ Descartado</option>
                        </select>
                    </div>

                    <div class="md:col-span-2 flex gap-2">
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-3 rounded-lg shadow-sm transition text-sm flex items-center justify-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                            </svg> Filtrar
                        </button>
                        <a href="{{ route('dashboard') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg font-bold transition flex items-center justify-center" title="Limpar Filtros">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                        </a>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-sm border border-gray-200 rounded-2xl overflow-hidden">
                
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-bold text-gray-700">
                            Exibindo <span class="text-indigo-600 font-extrabold">{{ $leads->count() }}</span> registros na lista
                        </span>
                        
                        <template x-if="selectedIds.length > 0">
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-700 font-bold rounded-full text-xs">
                                <span x-text="selectedIds.length"></span> selecionado(s)
                            </span>
                        </template>
                    </div>

                    <div>
                        <button type="button" 
                                @click="abrirMapaSelecionados()" 
                                :disabled="selectedIds.length === 0"
                                :class="selectedIds.length > 0 ? 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-md' : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                                class="inline-flex items-center px-4 py-2 rounded-xl font-extrabold text-xs uppercase tracking-wider transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.704v10.519c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
                            </svg>
                            Mostrar no Mapa (<span x-text="selectedIds.length"></span>)
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-100/80">
                            <tr>
                                <th class="px-4 py-3 text-center w-12">
                                    <input type="checkbox" @change="toggleSelectAll($event)" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                </th>
                                <th class="px-4 py-3 text-left font-extrabold text-gray-600 uppercase tracking-wider">Cliente</th>
                                <th class="px-4 py-3 text-left font-extrabold text-gray-600 uppercase tracking-wider">Contato</th>
                                <th class="px-4 py-3 text-left font-extrabold text-gray-600 uppercase tracking-wider">Bairro / Cidade</th>
                                <th class="px-4 py-3 text-left font-extrabold text-gray-600 uppercase tracking-wider">Endereço Pesquisado</th>
                                <th class="px-4 py-3 text-center font-extrabold text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-center font-extrabold text-gray-600 uppercase tracking-wider">Data</th>
                                <th class="px-4 py-3 text-right font-extrabold text-gray-600 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($leads as $lead)
                                <tr class="hover:bg-indigo-50/40 transition group cursor-pointer" @click="abrirDetalhes({{ json_encode($lead) }})">
                                    
                                    <td class="px-4 py-4 text-center" @click.stop>
                                        <input type="checkbox" :value="{{ $lead->id }}" x-model="selectedIds" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                    </td>

                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="font-bold text-gray-900 group-hover:text-indigo-600 transition">
                                            @if($lead->pronome)<span class="text-xs text-gray-400 font-normal mr-1">{{ $lead->pronome }}</span>@endif{{ $lead->nome }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-4 whitespace-nowrap" @click.stop>
                                        @if($lead->whatsapp)
                                            <a href="https://wa.me/55{{ preg_replace('/\D/', '', $lead->whatsapp) }}" target="_blank" class="inline-flex items-center text-xs font-bold bg-green-50 text-green-700 hover:bg-green-100 px-2.5 py-1.5 rounded-lg border border-green-200 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-4 h-4 mr-1.5" viewBox="0 0 16 16">
                                                  <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>
                                                </svg>
                                                {{ $lead->whatsapp }}
                                            </a>
                                        @else
                                            <span class="text-gray-400 italic text-xs">N/D</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="font-semibold text-gray-800">{{ $lead->bairro ?? 'Não informado' }}</span>
                                        <span class="block text-xs text-gray-500">{{ $lead->cidade ?? 'N/D' }} {{ $lead->estado ? '- '.$lead->estado : '' }}</span>
                                    </td>

                                    <td class="px-4 py-4 max-w-xs truncate text-xs text-gray-600" title="{{ $lead->endereco_pesquisado }}">
                                        {{ $lead->endereco_pesquisado ?? 'Coordenada direta' }}
                                    </td>

                                    <td class="px-4 py-4 text-center whitespace-nowrap">
                                        <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider inline-block
                                            @if($lead->status == 'Demanda Reprimida') bg-red-100 text-red-700 border border-red-200
                                            @elseif($lead->status == 'Em Estudo') bg-yellow-100 text-yellow-800 border border-yellow-200
                                            @elseif($lead->status == 'Projeto Aprovado') bg-blue-100 text-blue-800 border border-blue-200
                                            @elseif($lead->status == 'Rede Liberada') bg-green-100 text-green-800 border border-green-200
                                            @else bg-gray-100 text-gray-700 border border-gray-200 @endif">
                                            {{ $lead->status }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-4 text-center whitespace-nowrap text-xs text-gray-500">
                                        {{ $lead->created_at ? $lead->created_at->format('d/m/Y H:i') : 'N/D' }}
                                    </td>

                                    <td class="px-4 py-4 text-right whitespace-nowrap space-x-1" @click.stop>
                                        
                                        <button type="button" @click="abrirDetalhes({{ json_encode($lead) }})" class="inline-flex items-center justify-center p-2 bg-gray-100 hover:bg-indigo-100 text-gray-600 hover:text-indigo-600 rounded-lg transition" title="Ver Detalhes">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </button>

                                        @can('excluir cobertura')
                                            <button type="button" @click="confirmarExclusao({{ json_encode($lead) }})" class="inline-flex items-center justify-center p-2 bg-gray-100 hover:bg-red-100 text-gray-600 hover:text-red-600 rounded-lg transition" title="Excluir Lead">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                            </button>
                                        @endcan
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center bg-gray-50">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 text-gray-300 mx-auto mb-3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776" />
                                        </svg>
                                        <span class="text-gray-500 font-bold text-base block">Nenhum lead encontrado com os filtros selecionados.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <div x-show="openDetailsModal" style="display: none;" class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/80 backdrop-blur-sm px-4">
            <div @click.away="openDetailsModal = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden transform transition-all">
                
                <div class="bg-indigo-600 p-5 flex justify-between items-center text-white">
                    <div>
                        <span class="text-xs uppercase font-extrabold tracking-widest text-indigo-200">Ficha do Cliente</span>
                        <h3 class="font-bold text-xl" x-text="(leadAtual?.pronome ? leadAtual?.pronome + ' ' : '') + leadAtual?.nome"></h3>
                    </div>
                    <button @click="openDetailsModal = false" class="text-white hover:text-gray-200 p-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form :action="`/admin/leads/${leadAtual?.id}`" method="POST" class="p-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-200">
                            <span class="text-xs font-bold text-gray-400 uppercase block">WhatsApp / Telefone</span>
                            <template x-if="leadAtual?.whatsapp">
                                <a :href="`https://wa.me/55${leadAtual?.whatsapp.replace(/\D/g,'')}`" target="_blank" class="text-green-700 font-bold text-sm inline-flex items-center hover:underline mt-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-4 h-4 mr-1.5" viewBox="0 0 16 16">
                                      <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>
                                    </svg>
                                    <span x-text="leadAtual?.whatsapp"></span>
                                </a>
                            </template>
                            <template x-if="!leadAtual?.whatsapp">
                                <span class="text-gray-500 text-sm font-semibold">Não cadastrado</span>
                            </template>
                        </div>

                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-200">
                            <span class="text-xs font-bold text-gray-400 uppercase block">Localização Mapeada</span>
                            <span class="text-gray-800 font-bold text-sm block mt-1" x-text="`${leadAtual?.bairro || 'Sem Bairro'} - ${leadAtual?.cidade || 'Sem Cidade'}`"></span>
                        </div>
                    </div>

                    <div class="mb-4 bg-gray-50 p-3 rounded-xl border border-gray-200">
                        <span class="text-xs font-bold text-gray-400 uppercase block">Endereço de Pesquisa / Coordenadas GPS</span>
                        <p class="text-gray-700 text-sm font-medium mt-1" x-text="leadAtual?.endereco_pesquisado || 'Endereço não geocodificado'"></p>
                        <div class="text-xs text-gray-400 font-mono mt-1" x-show="leadAtual?.lat && leadAtual?.lng">
                            LAT: <span x-text="leadAtual?.lat"></span> | LNG: <span x-text="leadAtual?.lng"></span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Status no Funil Comercial</label>
                        <select name="status" class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 font-semibold" :value="leadAtual?.status">
                            <option value="Demanda Reprimida">🔴 Demanda Reprimida (Fora de Área)</option>
                            <option value="Em Estudo">🟡 Em Estudo (Engenharia Analisando)</option>
                            <option value="Projeto Aprovado">🟠 Projeto Aprovado (Aguardando Obras)</option>
                            <option value="Rede Liberada">🟢 Rede Liberada (Pronto para Venda/Instalação)</option>
                            <option value="Descartado">⚫ Descartado (Sem Interesse)</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Anotações da Equipe / Histórico</label>
                        <textarea name="observacoes" rows="3" placeholder="Insira dados de contatos, conversas ou apontamentos da engenharia..." class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm" x-text="leadAtual?.observacoes"></textarea>
                    </div>

                    <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                        <span class="text-xs text-gray-400">Cadastrado em: <span x-text="leadAtual?.created_at ? new Date(leadAtual?.created_at).toLocaleString('pt-BR') : 'N/D'"></span></span>
                        
                        <div class="flex gap-2">
                            <button type="button" @click="openDetailsModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition text-sm">Fechar</button>
                            <button type="submit" class="inline-flex items-center px-5 py-2 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition text-sm shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 mr-1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                                Salvar Ficha
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openMapModal" style="display: none;" class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/80 backdrop-blur-sm p-4">
            <div @click.away="openMapModal = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl h-[85vh] flex flex-col overflow-hidden transform transition-all">
                
                <div class="bg-emerald-600 p-4 flex justify-between items-center text-white">
                    <div>
                        <h3 class="font-bold text-lg flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.704v10.519c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" /></svg>
                            Visualizando Leads Selecionados
                        </h3>
                        <span class="text-xs text-emerald-100 font-semibold" x-text="`${leadsSelecionadosLista.length} pino(s) renderizados no mapa`"></span>
                    </div>
                    <button @click="openMapModal = false" class="text-white hover:text-gray-200 p-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div id="map-selecionados" class="w-full flex-1"></div>
            </div>
        </div>

        <div x-show="openDeleteModal" style="display: none;" class="fixed inset-0 z-[70] flex items-center justify-center bg-gray-900/80 backdrop-blur-sm px-4">
            <div @click.away="openDeleteModal = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all">
                <div class="p-6 text-center">
                    
                    <div class="w-20 h-20 rounded-full bg-red-100 text-red-600 flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-10 h-10">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                    </div>
                    
                    <h3 class="text-xl font-extrabold text-gray-900 mb-2">Excluir Lead?</h3>
                    <p class="text-gray-500 mb-6">Tem a certeza que deseja apagar os dados de <span class="font-bold text-gray-800" x-text="leadParaExcluir?.nome"></span>? Esta ação não poderá ser desfeita.</p>

                    <div class="flex justify-center gap-3">
                        <button type="button" @click="openDeleteModal = false" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition">Cancelar</button>
                        
                        <form :action="`/admin/leads/${leadParaExcluir?.id}`" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-5 py-2.5 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 shadow-sm transition">Sim, Excluir</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <div x-show="openAlertModal" style="display: none;" class="fixed inset-0 z-[80] flex items-center justify-center bg-gray-900/80 backdrop-blur-sm px-4">
            <div @click.away="openAlertModal = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden transform transition-all">
                <div class="p-6 text-center">
                    
                    <div class="w-20 h-20 rounded-full bg-amber-100 text-amber-500 flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-10 h-10">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    
                    <h3 class="text-xl font-extrabold text-gray-900 mb-2">Atenção</h3>
                    <p class="text-gray-500 mb-6" x-text="mensagemAlerta"></p>

                    <button type="button" @click="openAlertModal = false" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 shadow-sm transition w-full">
                        Entendi
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>

    <script>
        function crmLeads() {
            return {
                allLeads: @json($leads),
                selectedIds: [],
                
                openDetailsModal: false,
                openMapModal: false,
                openDeleteModal: false,
                openAlertModal: false,
                
                leadAtual: null,
                leadParaExcluir: null,
                mensagemAlerta: '',
                leadsSelecionadosLista: [],
                
                mapInstance: null,
                googleApiLoaded: false,

                toggleSelectAll(event) {
                    if (event.target.checked) {
                        this.selectedIds = this.allLeads.map(l => l.id);
                    } else {
                        this.selectedIds = [];
                    }
                },

                abrirDetalhes(lead) {
                    this.leadAtual = lead;
                    this.openDetailsModal = true;
                },

                confirmarExclusao(lead) {
                    this.leadParaExcluir = lead;
                    this.openDeleteModal = true;
                },

                mostrarAlerta(mensagem) {
                    this.mensagemAlerta = mensagem;
                    this.openAlertModal = true;
                },

                abrirMapaSelecionados() {
                    if (this.selectedIds.length === 0) return;

                    // Filtra apenas os leads marcados que possuem latitude e longitude
                    this.leadsSelecionadosLista = this.allLeads.filter(l => this.selectedIds.includes(l.id) && l.lat && l.lng);

                    if (this.leadsSelecionadosLista.length === 0) {
                        // Usa o Modal elegante em vez do alert()
                        this.mostrarAlerta("Nenhum dos clientes selecionados possui coordenadas GPS (Latitude e Longitude) cadastradas no banco de dados para serem exibidas no mapa.");
                        return;
                    }

                    this.openMapModal = true;

                    // Renderiza o mapa com um leve delay para garantir a abertura da div do modal
                    setTimeout(() => {
                        this.iniciarMapa();
                    }, 200);
                },

                iniciarMapa() {
                    const API_KEY = "{{ env('GOOGLE_MAPS_API_KEY') }}";

                    if (!this.googleApiLoaded) {
                        this.googleApiLoaded = true;
                        const script = document.createElement('script');
                        script.src = `https://maps.googleapis.com/maps/api/js?key=${API_KEY}&libraries=marker,places`;
                        script.async = true;
                        script.defer = true;
                        script.onload = () => this.plotarPinosNoMapa();
                        document.head.appendChild(script);
                    } else {
                        this.plotarPinosNoMapa();
                    }
                },

                plotarPinosNoMapa() {
                    if (this.leadsSelecionadosLista.length === 0) return;

                    const primeiro = this.leadsSelecionadosLista[0];
                    const center = { lat: parseFloat(primeiro.lat), lng: parseFloat(primeiro.lng) };

                    this.mapInstance = new google.maps.Map(document.getElementById("map-selecionados"), {
                        zoom: 14,
                        center: center,
                        mapId: 'MAPA_LEADS_SELECIONADOS',
                        disableDefaultUI: false,
                    });

                    const bounds = new google.maps.LatLngBounds();
                    const infoWindow = new google.maps.InfoWindow();
                    const markers = [];

                    this.leadsSelecionadosLista.forEach(lead => {
                        let pinColor = '#EF4444';
                        if(lead.status === 'Em Estudo') pinColor = '#F59E0B';
                        if(lead.status === 'Projeto Aprovado') pinColor = '#3B82F6';
                        if(lead.status === 'Rede Liberada') pinColor = '#10B981';
                        if(lead.status === 'Descartado') pinColor = '#6B7280';

                        const pinConfig = new google.maps.marker.PinElement({
                            background: pinColor,
                            borderColor: '#ffffff',
                            glyphColor: '#ffffff',
                        });

                        const pos = { lat: parseFloat(lead.lat), lng: parseFloat(lead.lng) };
                        bounds.extend(pos);

                        const marker = new google.maps.marker.AdvancedMarkerElement({
                            position: pos,
                            map: this.mapInstance,
                            content: pinConfig.element,
                            title: lead.nome
                        });

                        marker.addListener("click", () => {
                            let wpp = '';
                            if(lead.whatsapp) {
                                let wppNum = lead.whatsapp.replace(/\D/g,'');
                                wpp = `<div class="mt-2"><a href="https://wa.me/55${wppNum}" target="_blank" class="text-xs text-blue-600 font-bold inline-flex items-center"><svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-3 h-3 mr-1" viewBox="0 0 16 16"><path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/></svg>Chamar no WhatsApp</a></div>`;
                            }

                            const html = `
                                <div class="p-2 min-w-[180px]">
                                    <h4 class="font-bold text-gray-900">${lead.nome}</h4>
                                    <p class="text-xs text-gray-500 mb-2">${lead.bairro || 'Sem Bairro'} - ${lead.cidade || 'Sem Cidade'}</p>
                                    <span class="text-xs font-bold px-2 py-0.5 rounded" style="background:${pinColor}33; color:${pinColor}">${lead.status}</span>
                                    ${wpp}
                                </div>
                            `;
                            infoWindow.setContent(html);
                            infoWindow.open(this.mapInstance, marker);
                        });

                        markers.push(marker);
                    });

                    // Ajusta o zoom automaticamente para enquadrar todos os pinos selecionados
                    if (this.leadsSelecionadosLista.length > 1) {
                        this.mapInstance.fitBounds(bounds);
                    }

                    if(typeof markerClusterer !== 'undefined') {
                        new markerClusterer.MarkerClusterer({ map: this.mapInstance, markers });
                    }
                }
            }
        }
    </script>
</x-app-layout>