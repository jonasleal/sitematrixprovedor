<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Página: ') . $pagina->titulo }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('admin.paginas.update', $pagina->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <x-input-label for="titulo" :value="__('Título da Página')" />
                            <x-text-input id="titulo" class="block mt-1 w-full" type="text" name="titulo" :value="old('titulo', $pagina->titulo)" required />
                            <x-input-error :messages="$errors->get('titulo')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="slug" :value="__('URL Personalizada (Slug)')" />
                            <x-text-input id="slug" class="block mt-1 w-full" type="text" name="slug" :value="old('slug', $pagina->slug)" />
                            <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <x-input-label for="template" :value="__('Modelo / Template')" />
                            <select id="template" name="template" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="padrao" {{ $pagina->template == 'padrao' ? 'selected' : '' }}>Padrão Institucional</option>
                                <option value="faq" {{ $pagina->template == 'faq' ? 'selected' : '' }}>Perguntas Frequentes (FAQ)</option>
                                <option value="downloads" {{ $pagina->template == 'downloads' ? 'selected' : '' }}>Central de Downloads</option>
                            </select>
                        </div>

                        <div class="flex items-center mt-6">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="ativo" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ $pagina->ativo ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-600 font-bold">Página Ativa e Visível no Site</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-6">
                        <x-editor-html name="conteudo" label="Conteúdo da Página" :value="old('conteudo', $pagina->conteudo)" />
                    </div>

                    <div class="flex items-center justify-end space-x-3">
                        <a href="{{ route('admin.paginas.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Cancelar</a>
                        <x-primary-button>
                            {{ __('Atualizar Página') }}
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>