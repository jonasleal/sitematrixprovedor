<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gerenciar Banners do Site') }}
        </h2>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <div class="py-8" x-data="bannerAdmin()" x-init="initSortable()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-lg flex items-center shadow-sm font-semibold">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded-lg font-semibold">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-2">
                <div class="flex justify-between items-center px-2">
                    <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <span x-text="tituloPreview()"></span>
                    </h4>
                    
                    <div class="flex gap-2">
                        <button @click="viewMobile = false" :class="!viewMobile ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-600'" class="px-3 py-1 rounded text-xs font-bold transition shadow-sm">Desktop</button>
                        <button @click="viewMobile = true" :class="viewMobile ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-600'" class="px-3 py-1 rounded text-xs font-bold transition shadow-sm">Mobile</button>
                    </div>
                </div>

                <div class="bg-gray-900 rounded-3xl overflow-hidden relative border border-gray-700 flex shadow-2xl mx-auto transition-all duration-500"
                     :class="viewMobile ? 'w-[400px] h-[700px] flex-col' : 'w-full h-[450px] flex-row'">
                    
                    <div class="p-8 md:p-12 flex flex-col justify-center z-10 w-full" 
                         :class="[textoWidthClass(), viewMobile ? 'h-1/2' : 'h-full', {'items-center': form.titulo.includes('[centro]'), 'items-end': form.titulo.includes('[direita]')}]">
                        
                        <span class="text-xs font-extrabold px-3 py-1 rounded-full uppercase tracking-wider mb-4 inline-block shadow-md text-gray-900 w-max"
                              :class="badgeColorClass(form.tema_cor)">
                            <!-- A Mágica do Preview: Busca o nome da tag pelo ID selecionado -->
                            <span x-text="getNomeTag(form.tag_id) || 'DESTAQUE'"></span>
                        </span>
                        
                        <h3 class="font-bold text-white mb-4 leading-tight w-full" 
                            :class="viewMobile ? 'text-3xl' : 'text-4xl'"
                            x-html="parseShortcodes(form.titulo || 'Seu Título...', true)"></h3>
                        
                        <p class="text-gray-300 text-sm md:text-base leading-relaxed mb-6 w-full" x-html="parseShortcodes(form.descricao || 'Sua descrição detalhada aparecerá aqui...', false)"></p>
                        
                        <button class="bg-white text-gray-900 px-6 py-3 rounded-lg font-bold shadow-lg transition hover:bg-gray-200 w-max" x-text="form.texto_botao || 'Saiba Mais'"></button>
                    </div>

                    <div class="w-full relative overflow-hidden bg-black flex-shrink-0" :class="[imagemWidthClass(), viewMobile ? 'h-1/2' : 'h-full']">
                        <template x-if="(viewMobile && imageMobilePreview) || (!viewMobile && imagePreview)">
                            <img :src="viewMobile ? (imageMobilePreview || imagePreview) : imagePreview" 
                                 :style="`object-position: ${form.posicao_x}% ${form.posicao_y}%; transform: scale(${form.zoom / 100});`"
                                 class="absolute inset-0 w-full h-full object-cover transition-all duration-150">
                        </template>
                        <template x-if="!imagePreview && !imageMobilePreview">
                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 text-sm font-mono p-4 text-center border-l border-gray-700">
                                <svg class="w-10 h-10 mb-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Nenhuma imagem
                            </div>
                        </template>
                        <div class="absolute inset-0" :class="viewMobile ? 'bg-gradient-to-b from-gray-900 via-transparent to-transparent' : 'bg-gradient-to-r from-gray-900/80 to-transparent'"></div>
                    </div>
                </div>
            </div>

            <div id="form-container" class="bg-white border border-gray-200 shadow-md rounded-2xl p-6 md:p-8 space-y-6 mt-8">
                <div class="flex justify-between items-center border-b border-gray-200 pb-4">
                    <h3 class="text-xl font-extrabold text-gray-900 flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full" :class="modoEdicao ? 'bg-amber-500 animate-pulse' : 'bg-green-500'"></span>
                        <span x-text="modoEdicao ? 'Editando Banner #' + form.id : 'Cadastrar Novo Banner'"></span>
                    </h3>
                    <template x-if="modoEdicao">
                        <button type="button" @click="cancelarEdicao()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 border border-gray-300 rounded-lg font-bold text-xs">Cancelar Edição</button>
                    </template>
                </div>

                <form :action="modoEdicao ? `/admin/banners/${form.id}` : '{{ route('admin.banners.store') }}'" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <template x-if="modoEdicao"><input type="hidden" name="_method" value="PUT"></template>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <x-select-tag :tags="$tags" x-model="form.tag_id" />

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tema de Cores</label>
                            <select name="tema_cor" x-model="form.tema_cor" class="block w-full rounded-lg border-gray-300 text-sm">
                                <option value="green-cyan">Verde & Ciano (Matrix)</option>
                                <option value="pink-purple">Rosa & Roxo</option>
                                <option value="orange-yellow">Laranja & Amarelo</option>
                            </select>
                        </div>

                        <div class="lg:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Proporção da Tela (PC)</label>
                            <select name="proporcao_imagem" x-model="form.proporcao_imagem" class="block w-full rounded-lg border-gray-300 text-sm text-indigo-700 font-semibold bg-indigo-50">
                                <option value="50">50% Texto | Imagem Rec.: 640x450px</option>
                                <option value="60">40% Texto | Imagem Rec.: 768x450px</option>
                                <option value="40">60% Texto | Imagem Rec.: 512x450px</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-4 rounded-xl border border-gray-200">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">
                                <span x-text="modoEdicao ? 'Substituir Imagem PC' : 'Imagem PC (Obrigatório)'"></span>
                                <span class="text-xs font-normal text-indigo-600 block">Siga a resolução selecionada. Máx: 1MB</span>
                            </label>
                            <input type="file" name="caminho_imagem" @change="previewImage" :required="!modoEdicao" accept="image/*" class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer bg-white p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">
                                <span x-text="modoEdicao ? 'Substituir Imagem Celular' : 'Imagem Celular (Opcional)'"></span>
                                <span class="text-xs font-normal text-indigo-600 block">Vertical. Rec.: 400x700px. Máx: 1MB</span>
                            </label>
                            <input type="file" name="caminho_imagem_mobile" @change="previewMobileImage" accept="image/*" class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer bg-white p-2">
                        </div>
                    </div>

                    <div class="bg-white border border-gray-300 rounded-xl overflow-hidden focus-within:ring-1 focus-within:ring-indigo-500">
                        <div class="bg-gray-100 border-b border-gray-300 px-3 py-2 flex flex-wrap gap-2 items-center">
                            <span class="text-xs font-bold text-gray-600 mr-2">TÍTULO:</span>
                            <button type="button" @click.prevent="insertTag('tituloInput', 'titulo', 'destaque')" class="px-2 py-1 bg-indigo-100 text-indigo-700 text-xs font-bold rounded border border-indigo-200 hover:bg-indigo-200 shadow-sm transition">Cor Destaque</button>
                            <button type="button" @click.prevent="insertTag('tituloInput', 'titulo', 'negrito')" class="px-2 py-1 bg-white text-gray-700 text-xs font-bold rounded border border-gray-300 hover:bg-gray-50 shadow-sm transition">Negrito</button>
                            <button type="button" @click.prevent="insertTag('tituloInput', 'titulo', 'centro')" class="px-2 py-1 bg-white text-gray-700 text-xs font-bold rounded border border-gray-300 hover:bg-gray-50 shadow-sm transition">Centralizar</button>
                            <button type="button" @click.prevent="insertTag('tituloInput', 'titulo', 'direita')" class="px-2 py-1 bg-white text-gray-700 text-xs font-bold rounded border border-gray-300 hover:bg-gray-50 shadow-sm transition">Direita</button>
                        </div>
                        <input type="text" name="titulo" x-ref="tituloInput" x-model="form.titulo" required placeholder="Digite o título do banner..." class="block w-full border-none shadow-none text-sm focus:ring-0 p-3">
                    </div>

                    <div class="bg-white border border-gray-300 rounded-xl overflow-hidden focus-within:ring-1 focus-within:ring-indigo-500">
                        <div class="bg-gray-100 border-b border-gray-300 px-3 py-2 flex flex-wrap gap-2 items-center">
                            <span class="text-xs font-bold text-gray-600 mr-2">DESCRIÇÃO:</span>
                            <button type="button" @click.prevent="insertTag('descricaoInput', 'descricao', 'destaque')" class="px-2 py-1 bg-indigo-100 text-indigo-700 text-xs font-bold rounded border border-indigo-200 hover:bg-indigo-200 shadow-sm transition">Cor Destaque</button>
                            <button type="button" @click.prevent="insertTag('descricaoInput', 'descricao', 'negrito')" class="px-2 py-1 bg-white text-gray-700 text-xs font-bold rounded border border-gray-300 hover:bg-gray-50 shadow-sm transition">Negrito</button>
                            <button type="button" @click.prevent="insertTag('descricaoInput', 'descricao', 'riscado')" class="px-2 py-1 bg-white text-gray-700 text-xs font-bold rounded border border-gray-300 hover:bg-gray-50 shadow-sm transition">Riscado</button>
                            <button type="button" @click.prevent="insertTag('descricaoInput', 'descricao', 'sublinhado')" class="px-2 py-1 bg-white text-gray-700 text-xs font-bold rounded border border-gray-300 hover:bg-gray-50 shadow-sm transition">Sublinhado</button>
                        </div>
                        <textarea name="descricao" x-ref="descricaoInput" x-model="form.descricao" rows="3" placeholder="Digite o texto detalhado..." class="block w-full border-none shadow-none text-sm focus:ring-0 p-3"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <div>
                            <label class="block text-xs font-bold text-gray-700">Posição Horizontal (X%): <span class="text-indigo-600" x-text="form.posicao_x + '%'"></span></label>
                            <input type="range" min="0" max="100" name="posicao_x" x-model="form.posicao_x" class="w-full h-2 bg-gray-300 rounded-lg cursor-pointer mt-2">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700">Posição Vertical (Y%): <span class="text-indigo-600" x-text="form.posicao_y + '%'"></span></label>
                            <input type="range" min="0" max="100" name="posicao_y" x-model="form.posicao_y" class="w-full h-2 bg-gray-300 rounded-lg cursor-pointer mt-2">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700">Aproximação (Zoom %): <span class="text-indigo-600" x-text="form.zoom + '%'"></span></label>
                            <input type="range" min="80" max="180" name="zoom" x-model="form.zoom" class="w-full h-2 bg-gray-300 rounded-lg cursor-pointer mt-2">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Texto do Botão</label>
                            <input type="text" name="texto_botao" x-model="form.texto_botao" class="block w-full rounded-lg border-gray-300 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Link de Destino</label>
                            <input type="url" name="link_destino" x-model="form.link_destino" class="block w-full rounded-lg border-gray-300 text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Exibir A Partir De</label>
                            <input type="datetime-local" name="data_inicio" x-model="form.data_inicio" class="block w-full rounded-lg border-gray-300 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Exibir Até</label>
                            <input type="datetime-local" name="data_fim" x-model="form.data_fim" class="block w-full rounded-lg border-gray-300 text-sm">
                        </div>
                        <div class="flex items-center pt-5">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="ativo" x-model="form.ativo" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:bg-green-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                                <span class="ml-3 text-sm font-bold text-gray-700">Banner Ativo</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-gray-200">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-lg shadow-md transition duration-200">
                            <span x-text="modoEdicao ? 'Salvar Alterações' : 'Publicar Banner'"></span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="space-y-4">
                <div class="flex justify-between items-center px-2">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        Banners Cadastrados
                    </h3>
                    <span class="text-xs text-gray-500 bg-white px-3 py-1 rounded-full border border-gray-300 shadow-sm font-medium">
                        💡 Arraste o ícone de pontos para mudar a ordem
                    </span>
                </div>

                <div id="lista-banners" class="space-y-4">
                    @foreach($banners as $banner)
                        @php
                            $badgeColors = [
                                'green-cyan'    => 'bg-green-100 text-green-800 border-green-300',
                                'pink-purple'   => 'bg-pink-100 text-pink-800 border-pink-300',
                                'orange-yellow' => 'bg-amber-100 text-amber-800 border-amber-300',
                            ];
                            $badgeStyle = $badgeColors[$banner->tema_cor] ?? $badgeColors['green-cyan'];
                        @endphp

                        <div data-id="{{ $banner->id }}" class="bg-white shadow-md rounded-2xl overflow-hidden border border-gray-200 flex flex-col md:flex-row items-stretch justify-between relative group hover:border-indigo-300 transition">
                            
                            <div class="flex items-center gap-4 p-4 w-full md:w-auto">
                                <div class="drag-handle cursor-grab active:cursor-grabbing p-2 text-gray-400 hover:text-gray-700 transition rounded-lg hover:bg-gray-100">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
                                </div>

                                <img src="{{ asset('storage/' . $banner->caminho_imagem) }}" 
                                     class="w-32 h-20 object-cover rounded-xl border border-gray-200 flex-shrink-0" 
                                     style="object-position: {{ $banner->posicao_x }}% {{ $banner->posicao_y }}%; transform: scale({{ $banner->zoom / 100 }});">
                                
                                <div class="space-y-1">
                                    <span class="text-[10px] px-2.5 py-0.5 rounded-full font-extrabold uppercase border {{ $badgeStyle }}">
                                        <!-- Alterado para buscar a relação Tag -->
                                        {{ $banner->tag->nome ?? 'DESTAQUE' }}
                                    </span>
                                    <h4 class="font-bold text-gray-900 text-base leading-snug line-clamp-1">{!! strip_tags($banner->titulo_formatado) !!}</h4>
                                </div>
                            </div>

                            <div class="bg-gray-50 px-6 py-4 border-t md:border-t-0 md:border-l border-gray-200 flex flex-wrap items-center justify-between md:justify-end gap-4 text-xs w-full md:w-auto">
                                
                                <div class="text-gray-500 text-left mr-4">
                                    <p class="font-semibold text-gray-700">
                                        @if($banner->data_inicio || $banner->data_fim)
                                            {{ $banner->data_inicio ? $banner->data_inicio->format('d/m/Y H:i') : 'Início imediato' }}
                                            às
                                            {{ $banner->data_fim ? $banner->data_fim->format('d/m/Y H:i') : 'S/ expiração' }}
                                        @else
                                            Exibição Contínua
                                        @endif
                                    </p>
                                </div>

                                <form action="{{ route('admin.banners.toggle', $banner->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="px-3 py-1.5 rounded-full text-xs font-extrabold transition flex items-center gap-1.5 shadow-sm {{ $banner->ativo ? 'bg-green-100 text-green-700 border border-green-300 hover:bg-green-200' : 'bg-red-100 text-red-700 border border-red-300 hover:bg-red-200' }}">
                                        <span class="w-2 h-2 rounded-full {{ $banner->ativo ? 'bg-green-500 animate-ping' : 'bg-red-500' }}"></span>
                                        {{ $banner->ativo ? 'ATIVO' : 'DESATIVADO' }}
                                    </button>
                                </form>

                                <div class="flex items-center gap-2">
                                    <button @click="carregarParaEdicao({{ json_encode($banner) }})" class="bg-white hover:bg-indigo-50 border border-gray-300 text-indigo-600 px-3 py-1.5 rounded-lg font-bold flex items-center gap-1 transition shadow-sm">
                                        Editar
                                    </button>

                                    <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('Excluir banner?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="bg-white hover:bg-red-50 border border-gray-300 text-red-600 px-3 py-1.5 rounded-lg font-bold flex items-center gap-1 transition shadow-sm">
                                            Excluir
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Modal de Tags Mantido Intacto -->
            <x-modal-tags :tags="$tags" />
        </div>
    </div>

    <script>
        function bannerAdmin() {
            return {
                // Injeta as tags vindas do backend para o JavaScript
                tagsList: @json($tags),
                imagePreview: null,
                imageMobilePreview: null,
                modalTags: false,
                modoEdicao: false,
                viewMobile: false,
                form: {
                    id: null, 
                    tag_id: '', // Atualizado de categoria_tag para tag_id
                    tema_cor: 'green-cyan', 
                    proporcao_imagem: '50', 
                    titulo: '', 
                    descricao: '', 
                    texto_botao: '', 
                    link_destino: '', 
                    posicao_x: 50, 
                    posicao_y: 50, 
                    zoom: 100, 
                    ativo: true, 
                    data_inicio: '', 
                    data_fim: ''
                },
                
                // Nova função para buscar o NOME da tag baseado no ID selecionado
                getNomeTag(id) {
                    if (!id) return '';
                    const tag = this.tagsList.find(t => t.id == id);
                    return tag ? tag.nome : '';
                },

                // BARRA DE FERRAMENTAS MÁGICA
                insertTag(refName, fieldName, tag) {
                    const el = this.$refs[refName];
                    if (!el) return;
                    
                    const start = el.selectionStart;
                    const end = el.selectionEnd;
                    const text = this.form[fieldName] || '';
                    
                    const before = text.substring(0, start);
                    const selected = text.substring(start, end) || 'Texto';
                    const after = text.substring(end);
                    
                    this.form[fieldName] = before + '[' + tag + ']' + selected + '[/' + tag + ']' + after;
                    
                    setTimeout(() => {
                        el.focus();
                        el.setSelectionRange(start + tag.length + 2, start + tag.length + 2 + selected.length);
                    }, 50);
                },

                previewImage(e) { if(e.target.files[0]) this.imagePreview = URL.createObjectURL(e.target.files[0]); },
                previewMobileImage(e) { if(e.target.files[0]) this.imageMobilePreview = URL.createObjectURL(e.target.files[0]); },
                
                carregarParaEdicao(banner) {
                    this.modoEdicao = true;
                    this.form = { 
                        ...banner, 
                        tag_id: banner.tag_id || '', // Atualizado para carregar o ID da tag
                        proporcao_imagem: banner.proporcao_imagem || '50', 
                        ativo: Boolean(banner.ativo), 
                        data_inicio: banner.data_inicio ? banner.data_inicio.replace(' ', 'T').substring(0, 16) : '', 
                        data_fim: banner.data_fim ? banner.data_fim.replace(' ', 'T').substring(0, 16) : '' 
                    };
                    this.imagePreview = banner.caminho_imagem ? `/storage/${banner.caminho_imagem}` : null;
                    this.imageMobilePreview = banner.caminho_imagem_mobile ? `/storage/${banner.caminho_imagem_mobile}` : null;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },
                
                cancelarEdicao() {
                    this.modoEdicao = false; this.imagePreview = null; this.imageMobilePreview = null;
                    this.form = { id: null, tag_id: '', tema_cor: 'green-cyan', proporcao_imagem: '50', titulo: '', descricao: '', texto_botao: '', link_destino: '', posicao_x: 50, posicao_y: 50, zoom: 100, ativo: true, data_inicio: '', data_fim: '' };
                },
                
                parseShortcodes(text, isTitulo = false) {
                    if (!text) return ''; let result = text.replace(/\n/g, '<br>');
                    let grad = this.form.tema_cor === 'pink-purple' ? 'from-pink-400 to-purple-400' : (this.form.tema_cor === 'orange-yellow' ? 'from-amber-300 to-orange-400' : 'from-green-400 to-cyan-400');
                    const peso = isTitulo ? 'font-extrabold' : 'font-bold';
                    
                    result = result.replace(/\[destaque\](.*?)\[\/destaque\]/gi, `<span class="text-transparent bg-clip-text bg-gradient-to-r ${grad} ${peso}">$1</span>`);
                    result = result.replace(/\[negrito\](.*?)\[\/negrito\]/gi, `<strong class="font-black">$1</strong>`);
                    result = result.replace(/\[riscado\](.*?)\[\/riscado\]/gi, `<del class="opacity-70">$1</del>`);
                    result = result.replace(/\[sublinhado\](.*?)\[\/sublinhado\]/gi, `<u class="underline decoration-2 underline-offset-4">$1</u>`);
                    result = result.replace(/\[centro\](.*?)\[\/centro\]/gi, `<div class="text-center w-full block">$1</div>`);
                    result = result.replace(/\[direita\](.*?)\[\/direita\]/gi, `<div class="text-right w-full block">$1</div>`);
                    return result;
                },
                
                badgeColorClass(tema) { return tema === 'pink-purple' ? 'bg-gradient-to-r from-pink-500 to-purple-600 text-white' : (tema === 'orange-yellow' ? 'bg-gradient-to-r from-amber-400 to-orange-500 text-gray-900' : 'bg-gradient-to-r from-green-400 to-cyan-500 text-gray-900'); },
                
                tituloPreview() {
                    if (this.viewMobile) return "Preview Mobile (400x700px)";
                    let txt = 100 - parseInt(this.form.proporcao_imagem);
                    return `Preview Desktop | Área Exata do Site | Texto: ${txt}% - Imagem: ${this.form.proporcao_imagem}%`;
                },
                
                imagemWidthClass() {
                    if (this.viewMobile) return 'w-full';
                    if (this.form.proporcao_imagem == '60') return 'md:w-3/5';
                    if (this.form.proporcao_imagem == '40') return 'md:w-2/5';
                    return 'md:w-1/2';
                },
                
                textoWidthClass() {
                    if (this.viewMobile) return 'w-full';
                    if (this.form.proporcao_imagem == '60') return 'md:w-2/5';
                    if (this.form.proporcao_imagem == '40') return 'md:w-3/5';
                    return 'md:w-1/2';
                },
                
                initSortable() {
                    const el = document.getElementById('lista-banners');
                    if (!el) return;
                    Sortable.create(el, {
                        handle: '.drag-handle',
                        animation: 200,
                        onEnd: function () {
                            let ordem = [];
                            document.querySelectorAll('#lista-banners > div').forEach((card, index) => {
                                ordem.push({ id: card.getAttribute('data-id'), ordem: index + 1 });
                            });
                            fetch('{{ route('admin.banners.reordenar') }}', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                body: JSON.stringify({ ordem: ordem })
                            });
                        }
                    });
                }
            }
        }
    </script>
</x-app-layout>