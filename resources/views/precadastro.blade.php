@extends('layouts.site')

@section('content')
    <main 
        x-data="{ 
            mostrarToast: false, 
            mensagemToast: '', 
            tipoToast: 'sucesso', // 'sucesso' ou 'erro'
            
            dispararAlerta(mensagem, tipo) {
                this.mensagemToast = mensagem;
                this.tipoToast = tipo;
                this.mostrarToast = true;
                setTimeout(() => this.mostrarToast = false, 5000); // Some após 5 segundos
            }
        }"
        @mostrar-toast.window="dispararAlerta($event.detail.msg, $event.detail.tipo)"
        class="flex-grow pt-28 pb-12 w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative"
    >
        <div class="text-center mb-10">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-4 text-glow">Falta pouco para você ser Matrix!</h1>
            <p class="text-gray-300">Complete seus dados abaixo para analisarmos a viabilidade final e agendarmos sua instalação.</p>
        </div>

        <form id="form-precadastro" onsubmit="enviarParaSGP(event)" class="glass p-8 rounded-2xl border border-white/10 space-y-8">
            
            <div>
                <h3 class="text-xl font-bold text-green-400 mb-4 border-b border-gray-700 pb-2">1. Escolha seu Plano</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    
                    @if(isset($planos) && count($planos) > 0)
                        @foreach($planos as $plano)
                        <label class="cursor-pointer relative block h-full">
                            <input type="radio" name="plano_id" value="{{ $plano['id'] }}" class="peer sr-only" required>
                            
                            <div class="p-5 rounded-2xl border-2 border-gray-700 bg-black/40 peer-checked:border-green-400 peer-checked:bg-green-400/10 transition-all duration-300 text-center hover:bg-white/5 h-full flex flex-col justify-center relative overflow-hidden">
                                
                                <div class="absolute top-3 right-3 hidden peer-checked:block text-green-400 z-20">
                                    <svg class="w-6 h-6 drop-shadow-[0_0_8px_rgba(129,199,0,0.8)]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                </div>

                                @if($plano['destaque'])
                                    <div class="absolute top-0 left-1/2 transform -translate-x-1/2 bg-green-400 text-black px-4 py-1 rounded-b-lg text-[10px] font-extrabold tracking-widest shadow-md z-10">
                                        RECOMENDADO
                                    </div>
                                @endif

                                <h4 class="font-bold text-lg text-white mb-3 mt-4 leading-tight">{{ $plano['nome'] }}</h4>
                                
                                <div class="mt-auto">
                                    @if($plano['tem_desconto'])
                                        <p class="text-gray-500 line-through text-xs mb-0.5">De: R$ {{ number_format($plano['valor_original'], 2, ',', '.') }}</p>
                                    @endif
                                    <p class="text-green-400 font-extrabold text-2xl drop-shadow-md">R$ {{ number_format($plano['valor_final'], 2, ',', '.') }}</p>
                                </div>
                                
                            </div>
                        </label>
                        @endforeach
                    @else
                        <div class="col-span-3 text-center py-4 text-gray-400 bg-black/50 rounded-xl">
                            Nenhum plano disponível no momento. Tente novamente mais tarde.
                        </div>
                    @endif

                </div>
            </div>

            <div>
                <h3 class="text-xl font-bold text-cyan-400 mb-4 border-b border-gray-700 pb-2">2. Dados Pessoais</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Nome Completo</label>
                        <input type="text" id="nome" required class="w-full bg-black/50 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">CPF</label>
                        <input type="text" id="cpfcnpj" required maxlength="14" placeholder="000.000.000-00" class="w-full bg-black/50 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">WhatsApp / Celular</label>
                        <input type="text" id="celular" required placeholder="(87) 90000-0000" class="w-full bg-black/50 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">E-mail</label>
                        <input type="email" id="email" required class="w-full bg-black/50 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Data de Nascimento</label>
                        <input type="date" id="datanasc" required class="w-full bg-black/50 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-cyan-400 focus:outline-none [color-scheme:dark]">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">RG (Opcional)</label>
                        <input type="text" id="rg" class="w-full bg-black/50 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-cyan-400 focus:outline-none">
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-xl font-bold text-pink-500 mb-4 border-b border-gray-700 pb-2">3. Endereço de Instalação</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-1">
                        <label class="block text-sm text-gray-400 mb-1">CEP</label>
                        <input type="text" id="cep" onkeyup="buscarCep()" maxlength="9" placeholder="00000-000" required class="w-full bg-black/50 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-pink-400 focus:outline-none transition">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-sm text-gray-400 mb-1">Rua / Logradouro</label>
                        <input type="text" id="logradouro" required class="w-full bg-black/50 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-pink-400 focus:outline-none transition">
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-sm text-gray-400 mb-1">Número</label>
                        <input type="number" id="numero" required class="w-full bg-black/50 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-pink-400 focus:outline-none transition">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-sm text-gray-400 mb-1">Complemento (Opcional)</label>
                        <input type="text" id="complemento" placeholder="Apto, Bloco, Casa A..." class="w-full bg-black/50 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-pink-400 focus:outline-none transition">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm text-gray-400 mb-1">Bairro</label>
                        <input type="text" id="bairro" required class="w-full bg-black/50 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-pink-400 focus:outline-none transition">
                    </div>
                    <div class="md:col-span-2 flex gap-2">
                        <div class="flex-grow">
                            <label class="block text-sm text-gray-400 mb-1">Cidade</label>
                            <input type="text" id="cidade" required class="w-full bg-black/50 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-pink-400 focus:outline-none transition">
                        </div>
                        <div class="w-24">
                            <label class="block text-sm text-gray-400 mb-1">UF</label>
                            <input type="text" id="uf" maxlength="2" required class="w-full bg-black/50 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-pink-400 focus:outline-none transition text-center uppercase">
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" id="btn-submit" class="w-full btn-accent text-white py-4 rounded-xl font-bold text-xl hover:shadow-[0_0_25px_rgba(129,199,0,0.6)] transition duration-300">
                Concluir Pré-Cadastro
            </button>
        </form>

        <div id="modal-cpf-duplicado" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 hidden opacity-0 transition-opacity duration-300 backdrop-blur-sm">
            <div class="glass p-8 rounded-2xl border border-white/10 max-w-md w-full mx-4 text-center relative transform scale-95 transition-transform duration-300" id="modal-cpf-content">
                <button onclick="fecharModalWhatsapp()" class="absolute top-4 right-4 text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-500/20 mb-6">
                    <svg class="h-10 w-10 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                
                <h3 class="text-2xl font-bold text-white mb-2">Você já é de casa!</h3>
                <p class="text-gray-300 mb-6">
                    Identificamos que este CPF já possui um pré-cadastro ou assinatura em nosso sistema. Para darmos continuidade rapidamente, fale com um de nossos atendentes!
                </p>
                
                <a href="#" id="btn-whatsapp-duplicado" target="_blank" class="flex items-center justify-center gap-2 w-full bg-green-500 hover:bg-green-600 text-black py-4 rounded-xl font-bold text-lg hover:shadow-[0_0_20px_rgba(34,197,94,0.5)] transition duration-300">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.484-1.459-1.657-1.756-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    Falar no WhatsApp
                </a>
            </div>
        </div>
        <div id="modal-sistema-caiu" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 hidden opacity-0 transition-opacity duration-300 backdrop-blur-sm">
            <div class="glass p-8 rounded-2xl border border-white/10 max-w-md w-full mx-4 text-center relative transform scale-95 transition-transform duration-300" id="modal-sistema-content">
                <button onclick="fecharModalFalha()" class="absolute top-4 right-4 text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-500/20 mb-6 border-2 border-red-500/50">
                    <svg class="h-10 w-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                
                <h3 class="text-2xl font-bold text-white mb-2">Instabilidade na Conexão</h3>
                <p class="text-gray-300 mb-6">
                    Estávamos quase lá! Nosso sistema principal está passando por uma lentidão temporária. Não se preocupe, envie seus dados pelo WhatsApp para garantirmos sua instalação:
                </p>
                
                <a href="#" id="btn-whatsapp-fallback" target="_blank" onclick="fecharModalFalha()" class="flex items-center justify-center gap-2 w-full bg-green-500 hover:bg-green-600 text-black py-4 rounded-xl font-bold text-lg hover:shadow-[0_0_20px_rgba(34,197,94,0.5)] transition duration-300">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.484-1.459-1.657-1.756-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    Enviar Dados via WhatsApp
                </a>
            </div>
        </div>

        <div id="modal-sucesso" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 hidden opacity-0 transition-opacity duration-300 backdrop-blur-sm">
            <div class="glass p-8 rounded-2xl border border-white/10 max-w-md w-full mx-4 text-center relative transform scale-95 transition-transform duration-300" id="modal-sucesso-content">
                
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-500/20 mb-6 border-2 border-green-500/50">
                    <svg class="h-10 w-10 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                
                <h3 class="text-3xl font-bold text-white mb-2 text-glow">Tudo Certo!</h3>
                <p class="text-gray-300 mb-6 text-lg">
                    Seu pré-cadastro foi recebido com sucesso. Nossa equipe entrará em contato pelo WhatsApp em breve para confirmar o seu cadastro!
                </p>
                
                <a href="/" class="block w-full bg-green-500 hover:bg-green-600 text-black py-4 rounded-xl font-bold text-lg hover:shadow-[0_0_20px_rgba(34,197,94,0.5)] transition duration-300">
                    Voltar para o Início
                </a>
            </div>
        </div>

        <div x-show="mostrarToast" 
             x-transition.duration.500ms
             class="fixed bottom-5 right-5 z-50 flex items-center p-4 mb-4 w-full max-w-xs rounded-lg shadow text-white backdrop-blur-md bg-white/10 border"
             :class="tipoToast === 'sucesso' ? 'border-green-500/50' : 'border-red-500/50'"
             style="display: none;">
             
            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg"
                 :class="tipoToast === 'sucesso' ? 'bg-green-100 text-green-500' : 'bg-red-100 text-red-500'">
                <svg x-show="tipoToast === 'sucesso'" class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/></svg>
                <svg x-show="tipoToast === 'erro'" class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.5 11.5a1 1 0 0 1-1.414 1.414L10 11.414l-2.086 2.086a1 1 0 0 1-1.414-1.414L8.586 10 6.5 7.914a1 1 0 0 1 1.414-1.414L10 8.586l2.086-2.086a1 1 0 0 1 1.414 1.414L11.414 10l2.086 2.086Z"/></svg>
            </div>
            
            <div class="ms-3 text-sm font-normal" x-text="mensagemToast"></div>
            
            <button @click="mostrarToast = false" type="button" class="ms-auto -mx-1.5 -my-1.5 rounded-lg p-1.5 inline-flex items-center justify-center h-8 w-8 hover:bg-white/20 text-gray-300 hover:text-white">
                <span class="sr-only">Fechar</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
            </button>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const params = new URLSearchParams(window.location.search);
            
            if(params.has('rua')) document.getElementById('logradouro').value = params.get('rua');
            if(params.has('bairro')) document.getElementById('bairro').value = params.get('bairro');
            if(params.has('cep')) document.getElementById('cep').value = params.get('cep');
            if(params.has('cidade')) document.getElementById('cidade').value = params.get('cidade');
            if(params.has('estado')) document.getElementById('uf').value = params.get('estado');
            
            const planoId = params.get('plano_id') || sessionStorage.getItem('plano_escolhido_matrix');
            if(planoId) {
                const radioPlano = document.querySelector(`input[name="plano_id"][value="${planoId}"]`);
                if(radioPlano) { radioPlano.checked = true; }
            }
            
            if(document.getElementById('logradouro').value) document.getElementById('numero').focus();
            else document.getElementById('nome').focus();
        });

        // Função utilitária para chamar o Toast do Alpine via Javascript Puro
        function notificar(mensagem, tipo = 'erro') {
            window.dispatchEvent(new CustomEvent('mostrar-toast', { detail: { msg: mensagem, tipo: tipo } }));
        }

        async function buscarCep() {
            const inputCep = document.getElementById('cep');
            let cepNum = inputCep.value.replace(/\D/g, ''); 
            
            if(cepNum.length === 8) {
                try {
                    const res = await fetch(`https://viacep.com.br/ws/${cepNum}/json/`);
                    const dados = await res.json();
                    
                    if(!dados.erro) {
                        document.getElementById('cidade').value = dados.localidade;
                        document.getElementById('uf').value = dados.uf;
                        if(dados.logradouro) document.getElementById('logradouro').value = dados.logradouro;
                        if(dados.bairro) document.getElementById('bairro').value = dados.bairro;
                        
                        if(document.getElementById('logradouro').value) document.getElementById('numero').focus();
                        else document.getElementById('logradouro').focus();
                    }
                } catch(e) {}
            }
        }

        // ==========================================
        // MOTOR DE VALIDAÇÃO VISUAL (UX)
        // ==========================================
        function mostrarErroVisual(idCampo, mensagem) {
            const input = document.getElementById(idCampo);
            if (!input) return;

            // Pinta a borda de vermelho e adiciona um anel de destaque (Ring) piscante
            input.classList.remove('border-gray-600', 'focus:border-cyan-400', 'focus:border-pink-400');
            input.classList.add('border-red-500', 'focus:border-red-500', 'ring-4', 'ring-red-500/30', 'animate-pulse');

            // Remove o efeito de piscar após 2 segundos, mas mantém a borda vermelha
            setTimeout(() => {
                input.classList.remove('ring-4', 'ring-red-500/30', 'animate-pulse');
            }, 2000);

            let msgErro = document.getElementById('erro-msg-' + idCampo);
            if (!msgErro) {
                msgErro = document.createElement('p');
                msgErro.id = 'erro-msg-' + idCampo;
                msgErro.className = 'text-red-500 text-xs mt-1 font-semibold erro-dinamico';
                input.parentNode.appendChild(msgErro);
            }
            msgErro.innerText = mensagem;
        }

        function limparErrosVisuais() {
            document.querySelectorAll('input').forEach(input => {
                input.classList.remove('border-red-500', 'focus:border-red-500');
                if(!input.classList.contains('border-gray-600')) input.classList.add('border-gray-600');
            });
            document.querySelectorAll('.erro-dinamico').forEach(el => el.remove());
        }

        function validarCpf(cpf) {
            cpf = cpf.replace(/\D/g, '');
            if(cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) return false;
            let soma = 0, resto;
            for(let i = 1; i <= 9; i++) soma += parseInt(cpf.substring(i-1, i)) * (11 - i);
            resto = (soma * 10) % 11;
            if((resto === 10) || (resto === 11)) resto = 0;
            if(resto !== parseInt(cpf.substring(9, 10))) return false;
            soma = 0;
            for(let i = 1; i <= 10; i++) soma += parseInt(cpf.substring(i-1, i)) * (12 - i);
            resto = (soma * 10) % 11;
            if((resto === 10) || (resto === 11)) resto = 0;
            if(resto !== parseInt(cpf.substring(10, 11))) return false;
            return true;
        }
        
        function abrirModalWhatsappDuplicado(cpf) {
            const modal = document.getElementById('modal-cpf-duplicado');
            const content = document.getElementById('modal-cpf-content');
            const btnWhats = document.getElementById('btn-whatsapp-duplicado');

            const numeroEmpresa = '558796136109'; 
            const textoPronto = `Olá! Tentei assinar um plano pelo site, mas o sistema informou que meu CPF (${cpf}) já possui cadastro. Podem me ajudar a dar andamento?`;
            
            btnWhats.href = `https://wa.me/${numeroEmpresa}?text=${encodeURIComponent(textoPronto)}`;

            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                content.classList.remove('scale-95');
            }, 50);
        }

        function fecharModalWhatsapp() {
            const modal = document.getElementById('modal-cpf-duplicado');
            const content = document.getElementById('modal-cpf-content');
            
            modal.classList.add('opacity-0');
            content.classList.add('scale-95');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }
        function fecharModalFalha() {
            const modal = document.getElementById('modal-sistema-caiu');
            const content = document.getElementById('modal-sistema-content');
            modal.classList.add('opacity-0');
            content.classList.add('scale-95');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }

        // ==========================================
        // FLUXO FALLBACK WHATSAPP (SGP FORA DO AR)
        // ==========================================
        function abrirFallbackWhatsapp(payload, planoNome) {
            const numeroEmpresa = '558796136109'; 
            // Formatação do texto de contingência
            const textoPronto = `Olá! Estava tentando fazer o pré-cadastro pelo site e apresentou problema de conexão. Aqui estão meus dados:\n\n*Nome:* ${payload.nome}\n*CPF:* ${payload.cpfcnpj}\n*Celular:* ${payload.celular}\n*Email:* ${payload.email}\n*Endereço:* ${payload.logradouro}, ${payload.numero} - ${payload.bairro}, ${payload.cidade}-${payload.uf}\n*Plano Escolhido:* ${planoNome}`;
            
            const btnWhats = document.getElementById('btn-whatsapp-fallback');
            btnWhats.href = `https://wa.me/${numeroEmpresa}?text=${encodeURIComponent(textoPronto)}`;

            const modal = document.getElementById('modal-sistema-caiu');
            const content = document.getElementById('modal-sistema-content');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                content.classList.remove('scale-95');
            }, 50);
        }

        // ==========================================
        // FLUXO PRINCIPAL DE ENVIO
        // ==========================================
        async function enviarParaSGP(e) {
            e.preventDefault();
            limparErrosVisuais();
            
            const btn = document.getElementById('btn-submit');
            
            let temErroFront = false;
            let primeiroCampoErro = null;

            const planoSelecionado = document.querySelector('input[name="plano_id"]:checked');
            if(!planoSelecionado) {
                // SUBSTITUIÇÃO 1
                notificar("Por favor, selecione um plano de internet acima.", "erro");
                return;
            }

            const cpfDigitado = document.getElementById('cpfcnpj').value;
            if (!validarCpf(cpfDigitado)) {
                mostrarErroVisual('cpfcnpj', 'O CPF informado é inválido.');
                temErroFront = true;
                primeiroCampoErro = document.getElementById('cpfcnpj');
            }

            const celularDigitado = document.getElementById('celular').value.replace(/\D/g, '');
            if (celularDigitado.length < 10) {
                mostrarErroVisual('celular', 'Digite um celular válido (com DDD).');
                if(!temErroFront) primeiroCampoErro = document.getElementById('celular');
                temErroFront = true;
            }

            if (temErroFront) {
                primeiroCampoErro.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return; 
            }

            btn.innerText = "Processando...";
            btn.disabled = true;

            const payload = {
                nome: document.getElementById('nome').value,
                cpfcnpj: cpfDigitado.replace(/\D/g, ''),
                celular: celularDigitado,
                email: document.getElementById('email').value,
                datanasc: document.getElementById('datanasc').value,
                rg: document.getElementById('rg').value,
                logradouro: document.getElementById('logradouro').value,
                numero: document.getElementById('numero').value,
                complemento: document.getElementById('complemento').value,
                bairro: document.getElementById('bairro').value,
                cidade: document.getElementById('cidade').value,
                cep: document.getElementById('cep').value.replace(/\D/g, ''),
                uf: document.getElementById('uf').value,
                plano_id: planoSelecionado.value
            };

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const res = await fetch('/api/enviar-precadastro', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                    body: JSON.stringify(payload)
                });

                if(res.ok) {
                    // 1. Dispara o Confete!
                    confetti({
                        particleCount: 150,
                        spread: 80,
                        origin: { y: 0.6 },
                        colors: ['#22c55e', '#008CCC', '#ffffff', '#f2167dff'] // Cores da Matrix (Verde, Azul, Branco, Rosa)
                    });

                    // 2. Mostra o Modal de Sucesso Escurecendo a tela
                    const modalSucesso = document.getElementById('modal-sucesso');
                    const contentSucesso = document.getElementById('modal-sucesso-content');
                    
                    modalSucesso.classList.remove('hidden');
                    setTimeout(() => {
                        modalSucesso.classList.remove('opacity-0');
                        contentSucesso.classList.remove('scale-95');
                    }, 50);

                    sessionStorage.removeItem('plano_escolhido_matrix');
                    
                    
                } else {
                    const err = await res.json();
                    
                    if (err.whatsapp_fallback) {
                        // Captura o nome do plano selecionado visualmente para enviar
                        const nomePlanoDiv = planoSelecionado.closest('label').querySelector('h4');
                        const planoNome = nomePlanoDiv ? nomePlanoDiv.innerText : 'Plano Matrix';
                        
                        abrirFallbackWhatsapp(payload, planoNome);
                        btn.innerText = "Concluir Pré-Cadastro";
                        btn.disabled = false;
                        return;
                    }

                    if (err.is_cpf_duplicado) {
                        abrirModalWhatsappDuplicado(cpfDigitado);
                        btn.innerText = "Concluir Pré-Cadastro";
                        btn.disabled = false;
                        return;
                    }

                    if (err.details && typeof err.details === 'object') {
                        let focado = false;
                        for (const [campo, mensagens] of Object.entries(err.details)) {
                            mostrarErroVisual(campo, mensagens[0]);
                            if (!focado) {
                                const inputAlvo = document.getElementById(campo);
                                if(inputAlvo) {
                                    inputAlvo.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                    focado = true;
                                }
                            }
                        }
                    } else {
                        notificar(err.message || "Erro desconhecido. Tente novamente.", "erro");
                    }
                    
                    btn.innerText = "Concluir Pré-Cadastro";
                    btn.disabled = false;
                }
            } catch (error) {
                // SUBSTITUIÇÃO 4
                notificar("Falha de conexão com os servidores Matrix.", "erro");
                btn.innerText = "Concluir Pré-Cadastro";
                btn.disabled = false;
            }
        }

    </script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
@endsection