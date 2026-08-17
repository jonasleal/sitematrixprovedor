<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Central de Downloads e Aplicativos') }}
            </h2>
            <a href="{{ route('admin.downloads.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-5 rounded-lg shadow-sm transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Novo App / Documento
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ linhaExpandida: null }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-center font-semibold shadow-sm">
                    <i class="fas fa-check-circle mr-2 text-xl"></i> {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm border border-gray-200 rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-extrabold text-gray-500 uppercase tracking-wider">Item</th>
                                <th class="px-6 py-4 text-left text-xs font-extrabold text-gray-500 uppercase tracking-wider">Categoria / Versão</th>
                                <th class="px-6 py-4 text-center text-xs font-extrabold text-gray-500 uppercase tracking-wider">Métricas (Links)</th>
                                <th class="px-6 py-4 text-left text-xs font-extrabold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-right text-xs font-extrabold text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($downloads as $item)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-4">
                                            @if($item->imagem_path)
                                                <img src="{{ asset('storage/' . $item->imagem_path) }}" class="w-12 h-12 rounded-lg object-cover border border-gray-200 shadow-sm" alt="Logo">
                                            @else
                                                <div class="w-12 h-12 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400">
                                                    <i class="fas fa-file-alt text-xl"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-bold text-gray-900 text-base">{{ $item->titulo }}</div>
                                                <div class="text-sm text-gray-500 truncate max-w-xs" title="{{ $item->descricao }}">{{ $item->descricao ?? 'Sem descrição' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-5">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold capitalize bg-blue-100 text-blue-800 border border-blue-200 mb-1 block w-max">
                                            {{ $item->categoria }}
                                        </span>
                                        <div class="text-xs text-gray-500 font-medium">Versão: {{ $item->versao ?? 'N/A' }}</div>
                                    </td>

                                    <td class="px-6 py-5 text-center">
                                        @if($item->links->count() > 0)
                                            <button @click="linhaExpandida === {{ $item->id }} ? linhaExpandida = null : linhaExpandida = {{ $item->id }}" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 font-bold px-3 py-1.5 rounded-lg text-xs transition flex items-center gap-2 mx-auto focus:outline-none">
                                                <i class="fas fa-link"></i> Ver {{ $item->links->count() }} Link(s)
                                                <svg class="w-3 h-3 transform transition-transform" :class="linhaExpandida === {{ $item->id }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </button>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Sem links novos</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-5">
                                        <span class="px-3 py-1 text-xs font-bold rounded-full {{ $item->ativo ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                                            {{ $item->ativo ? 'Visível no Site' : 'Oculto' }}
                                        </span>
                                    </td>
                                    
                                    <td class="px-6 py-5 text-right text-sm font-medium space-x-3">
                                        <a href="{{ route('admin.downloads.edit', $item->id) }}" class="text-indigo-600 hover:text-indigo-900 font-bold"><i class="fas fa-edit mr-1"></i> Editar</a>
                                        
                                        <form action="{{ route('admin.downloads.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Deseja realmente apagar permanentemente este item?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-bold"><i class="fas fa-trash-alt mr-1"></i> Apagar</button>
                                        </form>
                                    </td>
                                </tr>

                                @if($item->links->count() > 0)
                                    <tr x-show="linhaExpandida === {{ $item->id }}" x-collapse class="bg-gray-50/80 border-b border-gray-200">
                                        <td colspan="5" class="px-8 py-5">
                                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                                
                                                @foreach($item->links as $link)
                                                    <div class="flex flex-col bg-white border border-gray-200 p-3 rounded-xl shadow-sm">
                                                        <div class="flex items-center gap-2 mb-3 border-b border-gray-100 pb-2">
                                                            @if(strtolower($link->plataforma) == 'android')
                                                                <i class="fab fa-android text-green-500 text-lg"></i>
                                                            @elseif(strtolower($link->plataforma) == 'ios' || strtolower($link->plataforma) == 'apple')
                                                                <i class="fab fa-apple text-gray-800 text-lg"></i>
                                                            @elseif(strtolower($link->plataforma) == 'windows')
                                                                <i class="fab fa-windows text-blue-500 text-lg"></i>
                                                            @elseif(strtolower($link->plataforma) == 'pdf' || strtolower($link->plataforma) == 'documento')
                                                                <i class="fas fa-file-pdf text-red-500 text-lg"></i>
                                                            @else
                                                                <i class="fas fa-link text-indigo-400 text-lg"></i>
                                                            @endif
                                                            <span class="font-extrabold text-gray-800 text-xs uppercase">{{ $link->plataforma }}</span>
                                                        </div>
                                                        
                                                        <div class="flex justify-between items-center">
                                                            <span class="text-xs text-gray-500 font-medium">Cliques/Downloads:</span>
                                                            <span class="bg-indigo-600 text-white font-black text-xs px-2.5 py-1 rounded-md shadow-sm">
                                                                {{ $link->hits }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endforeach

                                            </div>
                                        </td>
                                    </tr>
                                @endif

                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <i class="fas fa-folder-open text-gray-300 text-5xl mb-3 block"></i>
                                        <span class="text-gray-500 font-medium text-lg">Nenhum ficheiro ou aplicativo registado.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>