<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <title>@yield('title', 'Matrix Provedor - Garanhuns Conectada')</title>
    
    <meta name="description" content="@yield('meta_description', 'A melhor conexão de internet fibra óptica para você e sua empresa em Garanhuns e região.')">
    <meta name="keywords" content="@yield('meta_keywords', 'internet, fibra óptica, provedor, garanhuns, matrix provedor, banda larga, conexão ultra rápida')">
    <meta name="author" content="Matrix Provedor">
    
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'Matrix Provedor - Garanhuns Conectada')">
    <meta property="og:description" content="@yield('meta_description', 'A melhor conexão de internet fibra óptica para você e sua empresa em Garanhuns e região.')">
    <meta property="og:image" content="@yield('meta_image', asset('/assets/logo-matrix.png'))">
    
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="@yield('title', 'Matrix Provedor - Garanhuns Conectada')">
    <meta name="twitter:description" content="@yield('meta_description', 'A melhor conexão de internet fibra óptica para você e sua empresa em Garanhuns e região.')">
    <meta name="twitter:image" content="@yield('meta_image', asset('/assets/logo-matrix.png'))">

    <link rel="icon" href="/assets/favicon.png" type="image/png">
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <script src="/assets/tailwind.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <link rel="stylesheet" href="/assets/base.css?v=1.3">
    <link rel="stylesheet" href="/assets/site.css?v=1.3">
</head>
<body class="bg-gray-900 text-white hero-bg min-h-screen flex flex-col">
    
    @include('layouts.header') 
    
    <main class="flex-grow">
        @yield('content') 
    </main>

    @include('layouts.footer') 
    
</body>
</html>