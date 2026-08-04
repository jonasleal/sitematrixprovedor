<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notícias e Comunicados - Matrix Provedor</title>
    <script src="/assets/tailwind.js"></script>
    <link rel="stylesheet" href="/assets/base.css?v=1.3">
    <link rel="stylesheet" href="/assets/site.css?v=1.3">
</head>
<body class="bg-gray-900 text-white hero-bg min-h-screen flex flex-col">
    
    @include('layouts.header') 

    <main class="flex-grow pt-32 pb-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4 text-glow">Matrix Informa</h1>
            <p class="text-xl text-gray-300">Fique por dentro das novidades, expansões e comunicados oficiais.</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <article class="glass rounded-3xl overflow-hidden border border-white/10 hover:-translate-y-2 transition duration-300 group flex flex-col h-full">
                <div class="h-48 bg-gradient-to-r from-green-500 to-cyan-500 relative overflow-hidden">
                    <div class="absolute inset-0 bg-black/20 group-hover:bg-transparent transition duration-300"></div>
                </div>
                <div class="p-8 flex flex-col flex-grow">
                    <div class="flex justify-between items-center mb-4">
                        <span class="bg-green-400/20 text-green-400 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider border border-green-400/30">Expansão</span>
                        <span class="text-gray-400 text-sm">25 Abril, 2026</span>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-4 group-hover:text-green-400 transition">Chegamos no Bairro Magano!</h2>
                    <p class="text-gray-300 mb-6 flex-grow line-clamp-3">A Matrix continua avançando. Nossa rede 100% fibra óptica agora cobre toda a extensão do bairro Magano. Consulte sua viabilidade e agende a instalação.</p>
                    <a href="/planos-disponiveis" class="text-cyan-400 font-bold hover:text-white transition inline-flex items-center">
                        Assine Agora <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            </article>

            <article class="glass rounded-3xl overflow-hidden border border-white/10 hover:-translate-y-2 transition duration-300 group flex flex-col h-full">
                <div class="p-8 flex flex-col flex-grow">
                    <div class="flex justify-between items-center mb-4">
                        <span class="bg-pink-500/20 text-pink-400 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider border border-pink-500/30">Comunicado</span>
                        <span class="text-gray-400 text-sm">18 Abril, 2026</span>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-4 group-hover:text-pink-400 transition">Novas Formas de Pagamento Liberadas</h2>
                    <p class="text-gray-300 mb-6 flex-grow">Pensando na sua comodidade, a Central do Assinante agora aceita pagamentos via PIX Automático e Cartão de Crédito recorrente. Mais praticidade para você!</p>
                    <a href="https://central.suamatrix.com.br" target="_blank" class="text-cyan-400 font-bold hover:text-white transition inline-flex items-center">
                        Acessar Central <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            </article>

        </div>
    </main>

    @include('layouts.footer')
</body>
</html>