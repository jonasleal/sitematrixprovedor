<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Notícia: ') }} {{ $noticia->titulo }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ modalTags: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                
                <div class="flex justify-between items-center mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Formulário de Edição</h3>
                    <a href="{{ route('admin.noticias.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">Voltar para listagem</a>
                </div>

                <form action="{{ route('admin.noticias.update', $noticia->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Título da Notícia</label>
                            <input type="text" name="titulo" value="{{ old('titulo', $noticia->titulo) }}" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm sm:text-sm">
                        </div>

                        <div>
                            <x-select-tag :tags="$tags" :selecionada="old('tag_id', $noticia->tag_id)" />
                        </div>

                        <div>
                            <label for="publicado_em" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Data de Publicação
                            </label>
                            <input type="date" 
                                name="publicado_em" 
                                id="publicado_em"
                                value="{{ old('publicado_em', $noticia->publicado_em ? \Carbon\Carbon::parse($noticia->publicado_em)->format('Y-m-d') : date('Y-m-d')) }}" 
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm sm:text-sm [color-scheme:dark]">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Substituir Imagem (Opcional)</label>
                            <input type="file" name="imagem_destaque" accept="image/*" class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-md cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600">
                            @if($noticia->imagem_destaque)
                                <div class="mt-2 text-xs text-gray-500">Imagem atual em uso. Envie uma nova apenas se quiser trocar.</div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Resumo</label>
                        <textarea name="resumo" rows="2" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm sm:text-sm">{{ old('resumo', $noticia->resumo) }}</textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Conteúdo Completo</label>
                        <x-editor-html name="conteudo" id="conteudo_noticia_edit" :value="old('conteudo', $noticia->conteudo)" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="ativo" {{ $noticia->ativo ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200">
                            <span class="ml-2 text-gray-700 dark:text-gray-300 text-sm font-medium">Notícia Visível (Ativa)</span>
                        </label>
                        
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-md shadow-md transition duration-200">
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
            
            <x-modal-tags :tags="$tags" />
        </div>
    </div>
</x-app-layout>