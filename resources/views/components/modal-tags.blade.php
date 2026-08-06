@props(['tags'])

<div x-show="modalTags" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4" x-cloak style="display: none;">
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl" @click.away="modalTags = false">
        
        <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-3">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Gerenciar Tags</h3>
            <button @click="modalTags = false" type="button" class="text-gray-500 hover:text-gray-900 dark:hover:text-white text-xl font-bold">&times;</button>
        </div>

        <!-- Formulário de Adicionar -->
        <form action="{{ route('admin.tags.store') }}" method="POST" class="flex gap-2">
            @csrf
            <input type="text" name="nome" required placeholder="Nova Tag..." class="block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm uppercase focus:border-indigo-500">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold px-4 py-2 rounded-lg text-xs transition">Add</button>
        </form>

        <!-- Lista de Tags com Editar e Deletar -->
        <div class="space-y-2 max-h-60 overflow-y-auto pt-2 pr-1">
            @foreach($tags as $t)
                <div x-data="{ editando: false, novoNome: '{{ $t->nome }}' }" class="flex justify-between items-center bg-gray-50 dark:bg-gray-900/50 p-2.5 rounded-lg border border-gray-200 dark:border-gray-700 text-xs text-gray-700 dark:text-gray-300">
                    
                    <!-- Modo de Visualização -->
                    <div x-show="!editando" class="flex justify-between items-center w-full">
                        <span class="font-bold uppercase">{{ $t->nome }}</span>
                        <div class="flex items-center gap-3">
                            <button type="button" @click="editando = true" class="text-indigo-600 hover:text-indigo-800 font-bold transition">Editar</button>
                            <form action="{{ route('admin.tags.destroy', $t->id) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 font-bold transition">&times; Remover</button>
                            </form>
                        </div>
                    </div>

                    <!-- Modo de Edição -->
                    <div x-show="editando" class="flex items-center w-full gap-2" x-cloak style="display: none;">
                        <form action="{{ route('admin.tags.update', $t->id) }}" method="POST" class="flex w-full gap-2">
                            @csrf @method('PUT')
                            <input type="text" name="nome" x-model="novoNome" required class="block w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white text-xs uppercase px-2 py-1">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-3 py-1 rounded text-xs transition">Salvar</button>
                            <button type="button" @click="editando = false" class="bg-gray-400 hover:bg-gray-500 text-white font-bold px-3 py-1 rounded text-xs transition">X</button>
                        </form>
                    </div>

                </div>
            @endforeach
        </div>

    </div>
</div>