<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nós - Matrix Provedor</title>
    
    <script src="/assets/tailwind.js"></script>
    <link rel="stylesheet" href="/assets/base.css?v=1.3">
    <link rel="stylesheet" href="/assets/site.css?v=1.3">
</head>
<body class="bg-gray-900 text-white hero-bg min-h-screen flex flex-col">
    
    @include('layouts.header') 

    <main class="flex-grow flex items-center justify-center pt-24 pb-12 px-4 sm:px-6 lg:px-8">
        
        <div class="max-w-3xl w-full text-center">
            <div class="glass p-10 md:p-16 rounded-3xl border border-white/10 relative overflow-hidden shadow-2xl">
                
                <div class="absolute -top-24 -right-24 w-48 h-48 bg-green-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
                <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-cyan-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>

                <div class="flex justify-center mb-6 relative z-10">
                    <div class="p-4 bg-white/5 rounded-full border border-white/10">
                        <svg class="w-16 h-16 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </div>
                </div>
                
                <div class="relative z-10">
                    <h1 class="text-4xl md:text-5xl font-bold text-white mb-4 text-glow">
                        Nossa História
                    </h1>
                    <h2 class="text-2xl text-cyan-400 font-semibold mb-6">
                        Página em Desenvolvimento
                    </h2>
                    <p class="text-gray-300 text-lg mb-8 leading-relaxed">
                        Estamos construindo este espaço com muito carinho para contar como a Matrix nasceu e nosso compromisso com a conectividade de Garanhuns. Muito em breve você poderá conferir todos os detalhes por aqui!
                    </p>
                    
                    <a href="/" class="inline-flex items-center justify-center px-8 py-4 text-base font-bold text-white transition-all duration-200 btn-accent rounded-xl hover:shadow-[0_0_20px_rgba(129,199,0,0.4)]">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Voltar para a Página Inicial
                    </a>
                </div>
            </div>
        </div>

    </main>

    @include('layouts.footer')

</body>
</html>