<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Download</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg" x-data="{ tipoLink: '{{ $download->tipo_link }}' }">
                <form action="{{ route('admin.downloads.update', $download->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                        <div>
                            <x-input-label for="titulo" value="Título do Ficheiro" />
                            <x-text-input id="titulo" name="titulo" type="text" :value="old('titulo', $download->titulo)" class="w-full mt-1" required />
                        </div>
                        <div>
                            <x-input-label for="categoria" value="Categoria" />
                            <select name="categoria" id="categoria" class="w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                <option value="aplicativo" {{ $download->categoria == 'aplicativo' ? 'selected' : '' }}>Aplicativos</option>
                                <option value="contrato" {{ $download->categoria == 'contrato' ? 'selected' : '' }}>Contratos e Regulamentos</option>
                                <option value="manual" {{ $download->categoria == 'manual' ? 'selected' : '' }}>Manuais e Guias</option>
                                <option value="outros" {{ $download->categoria == 'outros' ? 'selected' : '' }}>Outros</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-4 p-4 bg-gray-50 border border-gray-200 rounded-lg flex items-center space-x-6">
                        @if($download->imagem_path)
                            <div class="shrink-0">
                                <img src="{{ asset('storage/' . $download->imagem_path) }}" alt="Miniatura Atual" class="h-20 w-20 object-cover rounded-xl shadow-md border border-gray-300">
                            </div>
                        @endif
                        <div class="flex-grow">
                            <x-input-label for="imagem" value="Atualizar Miniatura / Ícone (Opcional)" />
                            <input type="file" name="imagem" id="imagem" accept="image/*" class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-green-50 file:text-green-700 hover:file:bg-green-100" />
                        </div>
                    </div>
                    <div class="mb-4">
                        <x-input-label for="descricao" value="Descrição Resumida" />
                        <textarea name="descricao" id="descricao" class="w-full mt-1 border-gray-300 rounded-md shadow-sm" rows="2">{{ old('descricao', $download->descricao) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                        <div>
                            <x-input-label value="Origem do Ficheiro" />
                            <select name="tipo_link" x-model="tipoLink" class="w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                <option value="upload">Upload de Ficheiro (PDF/APK)</option>
                                <option value="externo">Link Externo (Play Store / App Store)</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="versao" value="Versão (Opcional)" />
                            <x-text-input id="versao" name="versao" type="text" :value="old('versao', $download->versao)" class="w-full mt-1" />
                        </div>
                        <div>
                            <x-input-label for="ordem" value="Ordem de Exibição" />
                            <x-text-input id="ordem" name="ordem" type="number" :value="old('ordem', $download->ordem)" class="w-full mt-1" />
                        </div>
                    </div>

                    <div x-show="tipoLink === 'upload'" class="mb-4">
                        <x-input-label for="arquivo" value="Substituir Ficheiro (Opcional)" />
                        <input type="file" name="arquivo" id="arquivo" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                        @if($download->tipo_link === 'upload' && $download->arquivo_path)
                            <p class="text-xs text-gray-500 mt-1">Ficheiro atual: <code>{{ $download->arquivo_path }}</code></p>
                        @endif
                    </div>

                    <div x-show="tipoLink === 'externo'" class="mb-4">
                        <x-input-label for="link_externo" value="URL do Link Externo" />
                        <x-text-input id="link_externo" name="link_externo" type="url" :value="old('link_externo', $download->tipo_link === 'externo' ? $download->arquivo_path : '')" class="w-full mt-1" />
                    </div>

                    <div class="flex items-center mb-6">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="ativo" value="1" {{ $download->ativo ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                            <span class="ml-2 text-sm text-gray-600 font-bold">Ficheiro Ativo no Site</span>
                        </label>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('admin.downloads.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">Cancelar</a>
                        <x-primary-button>Atualizar Ficheiro</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>