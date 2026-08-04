<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Matrix Provedor') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="/assets/base.css?v=1.1">
        <link rel="stylesheet" href="/assets/site.css?v=1.1">

        <style>
            /* Força os títulos, textos e labels a ficarem brancos/claros */
            label, .text-gray-600, .text-gray-900, p { color: #e5e7eb !important; }
            
            /* Estiliza as caixas de texto (email e senha) para o visual Matrix */
            input[type="email"], input[type="password"], input[type="text"] {
                background-color: rgba(0, 0, 0, 0.5) !important;
                border: 1px solid rgba(255, 255, 255, 0.2) !important;
                color: #ffffff !important;
            }
            
            /* Efeito neon ao clicar na caixa de texto */
            input:focus {
                border-color: #81c700 !important;
                box-shadow: 0 0 10px rgba(129,199,0,0.5) !important;
            }

            /* Links inferiores (Esqueceu a senha?) */
            a { color: #9ca3af !important; }
            a:hover { color: #81c700 !important; }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-900">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-900 hero-bg">
            <div>
                <a href="/">
                    <img src="/assets/logo-matrix.png" alt="Logo Matrix" class="h-20 w-auto hover:scale-105 transition duration-300">
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-8 glass shadow-[0_0_30px_rgba(0,0,0,0.8)] overflow-hidden sm:rounded-2xl border border-white/10">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>