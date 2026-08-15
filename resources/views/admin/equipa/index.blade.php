<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestão de Equipa e Permissões') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="equipaAdmin()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-lg flex items-center shadow-sm font-semibold">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded-lg flex items-center shadow-sm font-semibold">
                    <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow-sm border border-gray-200 rounded-2xl overflow-hidden">
                <div class="p-6 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Membros do Sistema</h3>
                        <p class="text-xs text-gray-500">Super Admin (ID 1) possui acesso oculto e absoluto.</p>
                    </div>
                    <button @click="abrirModalCriacao()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-5 rounded-lg shadow transition duration-200 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Novo Membro
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-100 text-gray-700 uppercase font-bold text-xs">
                            <tr>
                                <th class="px-6 py-4">Nome</th>
                                <th class="px-6 py-4">E-mail</th>
                                <th class="px-6 py-4">Status de Permissões</th>
                                <th class="px-6 py-4 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-bold text-gray-900">{{ $user->name }}</td>
                                    <td class="px-6 py-4">{{ $user->email }}</td>
                                    <td class="px-6 py-4">
                                        @if($user->permissions->count() > 0)
                                            <span class="bg-green-100 text-green-700 font-bold px-3 py-1 rounded-full text-xs">Personalizado ({{ $user->permissions->count() }})</span>
                                        @else
                                            <span class="bg-red-100 text-red-700 font-bold px-3 py-1 rounded-full text-xs">Sem Acesso</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-3">
                                            <button @click="abrirModalEdicao({{ $user }}, {{ $user->permissions->pluck('name') }})" class="text-indigo-600 hover:text-indigo-900 font-bold">Editar / Acessos</button>
                                            
                                            <form action="{{ route('admin.equipa.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja remover este membro?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 font-bold">Excluir</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500 italic">Nenhum membro registado (Apenas Super Admin).</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div x-show="modalAberto" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    
                    <div x-show="modalAberto" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" @click="modalAberto = false"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="modalAberto" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full border border-gray-200">
                        
                        <form :action="modoEdicao ? `/admin/equipa/${form.id}` : '{{ route('admin.equipa.store') }}'" method="POST">
                            @csrf
                            <template x-if="modoEdicao"><input type="hidden" name="_method" value="PUT"></template>

                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                
                                <div class="border-b border-gray-200 pb-4 mb-5 flex justify-between items-center">
                                    <h3 class="text-xl leading-6 font-extrabold text-gray-900" x-text="modoEdicao ? 'Editar Permissões do Membro' : 'Adicionar Novo Membro'"></h3>
                                    <button type="button" @click="modalAberto = false" class="text-gray-400 hover:text-gray-600"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                                    <div>
                                        <x-input-label for="name" value="Nome Completo" />
                                        <x-text-input id="name" name="name" type="text" x-model="form.name" class="w-full mt-1" required />
                                    </div>
                                    <div>
                                        <x-input-label for="email" value="E-mail de Acesso" />
                                        <x-text-input id="email" name="email" type="email" x-model="form.email" class="w-full mt-1" required />
                                    </div>
                                    <div>
                                        <x-input-label value="Senha (Mín. 8 char)" />
                                        <x-text-input id="password" name="password" type="text" x-model="form.password" class="w-full mt-1" x-bind:required="!modoEdicao" x-bind:placeholder="modoEdicao ? 'Preencha só p/ alterar' : 'Senha temporária'" />
                                    </div>
                                </div>

                                <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                                    <div class="flex flex-col sm:flex-row justify-between items-center border-b border-gray-200 pb-4 mb-4">
                                        <h4 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                            Matriz de Acessos
                                        </h4>

                                        <div class="flex items-center gap-3">
                                            <span class="text-sm font-semibold text-gray-500">Auto-preencher com:</span>
                                            <select @change="aplicarTemplate($event.target.value)" class="border-indigo-300 text-indigo-700 bg-indigo-50 rounded-lg text-sm font-bold focus:ring-indigo-500 cursor-pointer">
                                                <option value="">-- Template Em Branco --</option>
                                                <template x-for="role in rolesList" :key="role.name">
                                                    <option :value="role.name" x-text="role.name"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-4">
                                        @foreach($permissoesAgrupadas as $modulo => $acoes)
                                            <div class="bg-white p-4 rounded-lg border border-gray-100 shadow-sm flex flex-col justify-center">
                                                <h5 class="text-xs font-black text-gray-500 uppercase tracking-wider mb-3">{{ ucfirst($modulo) }}</h5>
                                                <div class="flex flex-wrap gap-4">
                                                    @foreach(['ver', 'criar', 'editar', 'excluir'] as $acaoPadrao)
                                                        @if(isset($acoes[$acaoPadrao]))
                                                            @php
                                                                $permName = $acoes[$acaoPadrao];
                                                                $corLabel = match($acaoPadrao) {
                                                                    'ver' => 'text-blue-700 bg-blue-50 border-blue-200',
                                                                    'criar' => 'text-green-700 bg-green-50 border-green-200',
                                                                    'editar' => 'text-amber-700 bg-amber-50 border-amber-200',
                                                                    'excluir' => 'text-red-700 bg-red-50 border-red-200',
                                                                    default => 'text-gray-700 bg-gray-50 border-gray-200'
                                                                };
                                                            @endphp
                                                            <label class="inline-flex items-center cursor-pointer border {{ $corLabel }} px-2.5 py-1.5 rounded-md hover:shadow-sm transition">
                                                                <input type="checkbox" name="permissions[]" value="{{ $permName }}" x-model="form.permissions" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 w-4 h-4 mr-2">
                                                                <span class="text-xs font-bold uppercase">{{ $acaoPadrao }}</span>
                                                            </label>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gray-100 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                                <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-5 py-2.5 bg-indigo-600 text-base font-bold text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition">
                                    <span x-text="modoEdicao ? 'Guardar Configurações' : 'Registar Membro'"></span>
                                </button>
                                <button type="button" @click="modalAberto = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-5 py-2.5 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition">
                                    Cancelar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function equipaAdmin() {
            return {
                modalAberto: false,
                modoEdicao: false,
                rolesList: @json($roles),
                form: {
                    id: null,
                    name: '',
                    email: '',
                    password: '',
                    permissions: [] // Alpine cuida do array de checkboxes sozinho!
                },

                abrirModalCriacao() {
                    this.modoEdicao = false;
                    this.form = { id: null, name: '', email: '', password: '', permissions: [] };
                    this.modalAberto = true;
                },

                abrirModalEdicao(user, userPermissions) {
                    this.modoEdicao = true;
                    this.form = {
                        id: user.id,
                        name: user.name,
                        email: user.email,
                        password: '', // Em branco para não alterar se não quiser
                        permissions: userPermissions // Preenche as caixinhas automaticamente
                    };
                    this.modalAberto = true;
                },

                // A MÁGICA: Ao escolher um "Cargo" no select, ele varre as permissões daquele cargo
                // e marca as caixinhas correspondentes para si.
                aplicarTemplate(roleName) {
                    if (!roleName) {
                        this.form.permissions = []; // Limpa tudo
                        return;
                    }
                    const roleEncontrada = this.rolesList.find(r => r.name === roleName);
                    if (roleEncontrada) {
                        // Extrai apenas os nomes das permissões e injeta no array dos checkboxes
                        this.form.permissions = roleEncontrada.permissions.map(p => p.name);
                    }
                }
            }
        }
    </script>
</x-app-layout>