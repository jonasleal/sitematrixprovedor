<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Adicionar Novo Download</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg" x-data="{ tipoLink: 'upload' }">
                <form action="{{ route('admin.downloads.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                        <div>
                            <x-input-label for="titulo" value="Título do Ficheiro" />
                            <x-text-input id="titulo" name="titulo" type="text" class="w-full mt-1" required />
                        </div>
                        <div>
                            <x-input-label for="categoria" value="Categoria" />
                            <select name="categoria" id="categoria" class="w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                <option value="aplicativo">Aplicativos</option>
                                <option value="contrato">Contratos e Regulamentos</option>
                                <option value="manual">Manuais e Guias</option>
                                <option value="outros">Outros</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                        <x-input-label for="imagem" value="Miniatura / Ícone (Opcional - Formato quadrado ideal: 256x256)" />
                        <input type="file" name="imagem" id="imagem" accept="image/*" class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-green-50 file:text-green-700 hover:file:bg-green-100" />
                    </div>
                    <div class="mb-4">
                        <x-input-label for="descricao" value="Descrição Resumida" />
                        <textarea name="descricao" id="descricao" class="w-full mt-1 border-gray-300 rounded-md shadow-sm" rows="2"></textarea>
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
                            <x-text-input id="versao" name="versao" type="text" placeholder="Ex: v1.0.4" class="w-full mt-1" />
                        </div>
                        <div>
                            <x-input-label for="ordem" value="Ordem de Exibição" />
                            <x-text-input id="ordem" name="ordem" type="number" value="0" class="w-full mt-1" />
                        </div>
                    </div>

                    <div x-show="tipoLink === 'upload'" class="mb-4">
                        <x-input-label for="arquivo" value="Ficheiro (PDF, APK, DOCX, ZIP)" />
                        <input type="file" name="arquivo" id="arquivo" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                    </div>

                    <div x-show="tipoLink === 'externo'" class="mb-4" style="display: none;">
                        <x-input-label for="link_externo" value="URL do Link Externo" />
                        <x-text-input id="link_externo" name="link_externo" type="url" placeholder="https://..." class="w-full mt-1" />
                    </div>

                    <div class="flex items-center mb-6">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="ativo" value="1" checked class="rounded border-gray-300 text-indigo-600">
                            <span class="ml-2 text-sm text-gray-600 font-bold">Ficheiro Ativo no Site</span>
                        </label>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('admin.downloads.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">Cancelar</a>
                        <x-primary-button>Guardar Ficheiro</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>