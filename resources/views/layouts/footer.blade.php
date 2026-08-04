<footer class="bg-black py-12 border-t border-white/10 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid md:grid-cols-4 gap-8">
        <div class="col-span-1 md:col-span-2">
            <img src="/assets/logo-matrix.png" alt="Logo Matrix" class="h-10 mb-4 opacity-70 grayscale hover:grayscale-0 transition duration-500">
            <p class="text-gray-600 text-sm mb-4 max-w-sm">{{ $configGlobal->texto_sobre_nos ?? 'Matrix Provedor, conectando Garanhuns e região com a melhor tecnologia de fibra óptica.' }}</p>
        </div>
        <div>
            <h4 class="text-white font-bold mb-4">Fale Conosco</h4>
                <ul class="text-gray-600 text-sm space-y-2">
                    <li>WhatsApp: {{ $configGlobal->whatsapp ?? '(87) 9XXXX-XXXX' }}</li>
                    <li>Telefone: {{ $configGlobal->telefone_principal ?? 'Não informado' }}</li>
                    <li>{{ $configGlobal->endereco_fisico ?? 'Magano, Garanhuns - PE' }}</li>
                </ul>
        </div>
        <div>
            <h4 class="text-white font-bold mb-4">Área do Assinante</h4>
            <ul class="text-gray-600 text-sm space-y-2">
                <li><a href="/planos-disponiveis" class="hover:text-green-400 transition">Nova Assinatura (Pré-cadastro)</a></li>
                <li><a href="https://central.suamatrix.com.br" target="_blank" class="hover:text-green-400 transition">Emitir 2ª Via do Boleto</a></li>
                <!-- Link para o arquivo PDF do contrato -->
                <li><a href="/assets/contrato_prestacao_servicos.pdf" target="_blank" class="hover:text-green-400 transition flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Contrato de Prestação de Serviços
                </a></li>
            </ul>
        </div>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 pt-8 border-t border-white/10 text-center text-gray-700 text-xs">
        &copy; 2026 Matrix Provedor de Internet. Desenvolvido com carinho em Garanhuns.
    </div>
</footer>