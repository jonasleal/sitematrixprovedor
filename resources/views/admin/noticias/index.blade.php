<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Gerenciar Notícias e Avisos') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ modalTags: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-green-500/10 border border-green-500/50 text-green-500 px-4 py-3 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-500/10 border border-red-500/50 text-red-500 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">Publicar Nova Notícia</h3>
                
                <form action="{{ route('admin.noticias.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Título da Notícia</label>
                            <input type="text" name="titulo" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm sm:text-sm">
                        </div>

                        <!-- Chamada do Componente Universal de Tags -->
                        <div>
                            <x-select-tag :tags="$tags" :selecionada="old('tag_id', $noticia->tag_id ?? '')" />
                        </div>

                        <div>
                            <label for="publicado_em" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Data de Publicação
                            </label>
                            <input type="date" 
                                name="publicado_em" 
                                id="publicado_em"
                                value="{{ old('publicado_em', date('Y-m-d')) }}" 
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm sm:text-sm [color-scheme:dark]">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Imagem de Destaque (Opcional)</label>
                            <input type="file" name="imagem_destaque" accept="image/*" class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-md cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Resumo (Para exibir na página inicial)</label>
                        <textarea name="resumo" rows="2" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm sm:text-sm"></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <div class="flex justify-between items-center mb-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Conteúdo Completo</label>
                            <span class="text-xs text-indigo-400 font-semibold">Clique no ícone <b>&lt; &gt;</b> para editar o HTML diretamente.</span>
                        </div>
                        
                        <x-editor-html name="conteudo" id="conteudo_noticia" value="" />
                        
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="ativo" checked class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <span class="ml-2 text-gray-700 dark:text-gray-300 text-sm font-medium">Notícia Visível (Ativa)</span>
                        </label>
                        
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-md shadow-md transition duration-200">
                            Publicar Notícia
                        </button>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($noticias as $noticia)
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 flex flex-col">
                        @if($noticia->imagem_destaque)
                            <img src="{{ asset('storage/' . $noticia->imagem_destaque) }}" alt="{{ $noticia->titulo }}" class="w-full h-40 object-cover">
                        @else
                            <div class="w-full h-40 bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                <span class="text-gray-400 dark:text-gray-500 text-sm">Sem Imagem</span>
                            </div>
                        @endif
                        
                        <div class="p-4 flex-1 flex flex-col">
                            <h4 class="font-bold text-gray-900 dark:text-white line-clamp-2">
                                @if($noticia->tag)
                                    <span class="text-[10px] bg-indigo-500/20 text-indigo-400 px-2 py-0.5 rounded uppercase mr-1 inline-block align-middle">{{ $noticia->tag->nome }}</span>
                                @endif
                                <span class="align-middle">{{ $noticia->titulo }}</span>
                            </h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Publicado em: {{ \Carbon\Carbon::parse($noticia->publicado_em ?? $noticia->created_at)->format('d/m/Y') }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2 line-clamp-2 flex-1">{{ $noticia->resumo }}</p>
                            
                            <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                <span class="text-xs px-2 py-1 rounded {{ $noticia->ativo ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                    {{ $noticia->ativo ? 'Ativa' : 'Oculta' }}
                                </span>
                                
                                <!-- Agrupando os botões -->
                                <div class="flex items-center gap-3">
                                    <!-- Novo Botão Editar -->
                                    <a href="{{ route('admin.noticias.edit', $noticia->id) }}" class="text-indigo-400 hover:text-indigo-600 transition" title="Editar Notícia">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>

                                    <!-- Botão Excluir -->
                                    <form action="{{ route('admin.noticias.destroy', $noticia->id) }}" method="POST" onsubmit="return confirm('Tem a certeza que deseja eliminar esta notícia?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 transition" title="Excluir Notícia">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <x-modal-tags :tags="$tags" />
        </div>
    </div>
    
</x-app-layout>