<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Criar Novo Aplicativo / Download</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg" x-data="{ 
                links: [ { id: Date.now(), plataforma: 'android', tipo: 'externo' } ],
                adicionarLink() {
                    this.links.push({ id: Date.now(), plataforma: 'windows', tipo: 'upload' });
                },
                removerLink(id) {
                    this.links = this.links.filter(link => link.id !== id);
                }
            }">
                <form action="{{ route('admin.downloads.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">1. Dados do Aplicativo/Documento</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                        <div>
                            <x-input-label for="titulo" value="Título Exibido (Ex: Matrix TV)" />
                            <x-text-input id="titulo" name="titulo" type="text" class="w-full mt-1" required />
                        </div>
                        <div>
                            <x-input-label for="categoria" value="Categoria" />
                            <select name="categoria" id="categoria" class="w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                <option value="aplicativo">Aplicativos e Softwares</option>
                                <option value="contrato">Contratos e Regulamentos</option>
                                <option value="manual">Manuais e Guias</option>
                                <option value="outros">Outros</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                        <x-input-label for="imagem" value="Logo do App / Ícone (Recomendado: 256x256 transparente)" />
                        <input type="file" name="imagem" id="imagem" accept="image/*" class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <x-input-label for="versao" value="Versão Geral (Opcional)" />
                            <x-text-input id="versao" name="versao" type="text" placeholder="Ex: v1.0.4" class="w-full mt-1" />
                        </div>
                        <div>
                            <x-input-label for="ordem" value="Ordem de Exibição" />
                            <x-text-input id="ordem" name="ordem" type="number" value="0" class="w-full mt-1" />
                        </div>
                    </div>
                    
                    <div class="mb-8">
                        <x-input-label for="descricao" value="Descrição Curta" />
                        <textarea name="descricao" id="descricao" class="w-full mt-1 border-gray-300 rounded-md shadow-sm" rows="2"></textarea>
                    </div>

                    <div class="flex justify-between items-end border-b pb-2 mb-4">
                        <h3 class="text-lg font-bold text-gray-800">2. Botões de Download (Plataformas)</h3>
                        <button type="button" @click="adicionarLink()" class="px-3 py-1 bg-green-500 text-white text-xs font-bold uppercase rounded shadow hover:bg-green-600 transition">
                            + Adicionar Plataforma
                        </button>
                    </div>

                    <template x-for="(link, index) in links" :key="link.id">
                        <div class="relative p-4 mb-4 bg-gray-50 border border-gray-200 rounded-lg shadow-sm">
                            
                            <button type="button" @click="removerLink(link.id)" class="absolute top-2 right-2 text-red-500 hover:text-red-700">
                                <i class="fas fa-times-circle text-lg"></i>
                            </button>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                                <div>
                                    <x-input-label value="Sistema / Plataforma" />
                                    <select x-bind:name="'links[' + index + '][plataforma]'" x-model="link.plataforma" class="w-full mt-1 border-gray-300 rounded-md shadow-sm text-sm" required>
                                        <option value="android">Android (APK ou PlayStore)</option>
                                        <option value="ios">Apple iOS (iPhone/iPad)</option>
                                        <option value="windows">Windows (PC)</option>
                                        <option value="webos">LG WebOS</option>
                                        <option value="tizen">Samsung Tizen</option>
                                        <option value="pdf">Documento PDF Genérico</option>
                                        <option value="web">Acesso Web (Navegador)</option>
                                    </select>
                                </div>

                                <div>
                                    <x-input-label value="Origem do Ficheiro" />
                                    <select x-bind:name="'links[' + index + '][tipo_link]'" x-model="link.tipo" class="w-full mt-1 border-gray-300 rounded-md shadow-sm text-sm" required>
                                        <option value="externo">Link Externo (Loja Oficial)</option>
                                        <option value="upload">Fazer Upload de Ficheiro Interno</option>
                                    </select>
                                </div>

                                <div class="mt-1">
                                    <div x-show="link.tipo === 'externo'">
                                        <x-input-label value="URL Externa" />
                                        <x-text-input x-bind:name="'links[' + index + '][link_externo]'" type="url" placeholder="https://play.google.com..." class="w-full mt-1 text-sm" />
                                    </div>
                                    <div x-show="link.tipo === 'upload'">
                                        <x-input-label value="Upload (.apk, .pdf, .zip)" />
                                        <input type="file" x-bind:name="'links[' + index + '][arquivo]'" class="mt-1 block w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div class="mt-8 flex items-center mb-6">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="ativo" value="1" checked class="rounded border-gray-300 text-indigo-600">
                            <span class="ml-2 text-sm text-gray-600 font-bold">Publicar App/Documento no Site</span>
                        </label>
                    </div>

                    <div class="flex justify-end space-x-3 border-t pt-4">
                        <a href="{{ route('admin.downloads.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">Cancelar</a>
                        <x-primary-button>Salvar Download Completo</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>