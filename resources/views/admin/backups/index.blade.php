<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
                    <i class="fab fa-google-drive text-blue-500"></i> {{ __('Gestão de Backups e Restauração') }}
                </h2>
                <span class="text-xs px-3 py-0.5 mt-1 bg-green-100 text-green-700 rounded-full font-bold uppercase tracking-widest border border-green-200 inline-flex items-center">
                    <i class="fas fa-link mr-1"></i> Conectado ao G-Drive
                </span>
            </div>
            
            <form action="{{ route('admin.backup.run') }}" method="POST" onsubmit="document.getElementById('btn-backup').disabled = true; document.getElementById('btn-backup').innerHTML = '<i class=\'fas fa-spinner fa-spin mr-2\'></i> Gerando...'; document.getElementById('btn-backup').classList.add('opacity-75', 'cursor-not-allowed');">
                @csrf
                <button type="submit" id="btn-backup" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 shadow-sm transition ease-in-out duration-150">
                    <i class="fas fa-cloud-upload-alt mr-2"></i> Forçar Backup Manual Agora
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ loadingRestore: false, loadingFetch: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-center font-semibold shadow-sm">
                    <i class="fas fa-check-circle mr-2 text-xl"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg flex items-center font-semibold shadow-sm">
                    <i class="fas fa-exclamation-triangle mr-2 text-xl"></i> {{ session('error') }}
                </div>
            @endif

            <div x-show="loadingRestore" class="fixed inset-0 z-[9999] bg-gray-900/80 backdrop-blur-sm flex flex-col items-center justify-center text-white" style="display: none;">
                <svg class="animate-spin h-16 w-16 text-blue-500 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <h2 class="text-2xl font-extrabold mb-2">Descarregando Ficheiros da Nuvem...</h2>
                <p class="text-gray-300 font-medium text-center max-w-md">Por favor, não feche esta janela.<br>Estamos a importar a sua base de dados e a reconstruir as informações.</p>
            </div>

            <div x-show="loadingFetch" class="fixed inset-0 z-[9999] bg-white/90 backdrop-blur-sm flex flex-col items-center justify-center text-gray-800" style="display: none;">
                <svg class="animate-spin h-12 w-12 text-blue-500 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <h2 class="text-xl font-bold mb-1">A ligar ao Google Drive...</h2>
                <p class="text-gray-500 text-sm">Procurando os ficheiros na nuvem.</p>
            </div>

            <div class="bg-white shadow-sm border border-gray-200 rounded-2xl overflow-hidden">
                <div class="p-6 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-gray-800 text-lg mb-1">Cofre de Segurança da Matrix</h3>
                        <p class="text-sm text-gray-500">Recupere o sistema utilizando um ficheiro salvo no seu Google Drive.</p>
                    </div>
                    
                    @if($backups !== null)
                        <a href="{{ route('backups.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-lg font-bold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 shadow-sm transition">
                            <i class="fas fa-times mr-2"></i> Fechar Cofre
                        </a>
                    @endif
                </div>
                
                @if($backups === null)
                    <div class="p-16 text-center flex flex-col items-center justify-center bg-white">
                        <i class="fab fa-google-drive text-gray-200 text-7xl mb-6"></i>
                        <h4 class="text-xl font-bold text-gray-700 mb-2">Proteção de Desempenho</h4>
                        <p class="text-gray-500 max-w-lg mx-auto mb-8 text-sm">Para garantir que o seu painel se mantenha ultrarrápido, a lista de ficheiros na nuvem não é carregada automaticamente. Deseja efetuar uma Restauração do Sistema agora?</p>
                        <a href="{{ route('backups.index', ['fetch' => 1]) }}" @click="loadingFetch = true" class="inline-flex items-center px-6 py-3 bg-blue-50 text-blue-600 border border-blue-200 rounded-xl font-extrabold text-sm uppercase tracking-widest hover:bg-blue-100 transition shadow-sm">
                            <i class="fas fa-search mr-2"></i> Abrir Cofre e Listar Ficheiros
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-white">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-extrabold text-gray-500 uppercase tracking-wider">Nome do Ficheiro</th>
                                    <th class="px-6 py-4 text-left text-xs font-extrabold text-gray-500 uppercase tracking-wider">Tamanho</th>
                                    <th class="px-6 py-4 text-left text-xs font-extrabold text-gray-500 uppercase tracking-wider">Data do Backup</th>
                                    <th class="px-6 py-4 text-right text-xs font-extrabold text-gray-500 uppercase tracking-wider">Ação Crítica</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($backups as $backup)
                                    <tr class="hover:bg-blue-50/50 transition">
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-3">
                                                <i class="fas fa-file-archive text-3xl text-yellow-500"></i>
                                                <span class="font-bold text-gray-800">{{ $backup['name'] }}</span>
                                            </div>
                                        </td>
                                        
                                        <td class="px-6 py-5">
                                            <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs font-bold">{{ $backup['size'] }}</span>
                                        </td>

                                        <td class="px-6 py-5 font-medium text-gray-600">
                                            <i class="far fa-clock mr-1 text-gray-400"></i> {{ $backup['date'] }}
                                        </td>
                                        
                                        <td class="px-6 py-5 text-right">
                                            <form action="{{ route('backups.restore') }}" method="POST" @submit="loadingRestore = true; return confirm('ALERTA MÁXIMO:\nIsso apagará o banco de dados de hoje e voltará o sistema exatamente como estava no dia do backup.\n\nTem a certeza absoluta?');">
                                                @csrf
                                                <input type="hidden" name="path" value="{{ $backup['path'] }}">
                                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition flex items-center justify-end gap-2 ml-auto">
                                                    <i class="fas fa-cloud-download-alt"></i> Restaurar Snapshot
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center bg-gray-50">
                                            <i class="fab fa-google-drive text-gray-300 text-5xl mb-3 block"></i>
                                            <span class="text-gray-500 font-medium text-lg block">Nenhum ficheiro encontrado!</span>
                                            <p class="text-sm text-gray-400 mt-1">A diretoria especificada no ficheiro .env não possui ficheiros .zip.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>