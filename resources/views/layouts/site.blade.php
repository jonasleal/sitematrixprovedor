<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Matrix Provedor - Garanhuns Conectada')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="/assets/tailwind.js"></script>
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <link rel="stylesheet" href="/assets/base.css?v=1.3">
    <link rel="stylesheet" href="/assets/site.css?v=1.3">
</head>
<body class="bg-gray-900 text-white hero-bg min-h-screen">
    
    @include('layouts.header') 
    
    <main>
        @yield('content') 
    </main>

    @include('layouts.footer') 
    
</body>
</html>