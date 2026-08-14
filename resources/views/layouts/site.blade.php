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
    @php
        $configSite = Cache::remember('config_site_array_v1', 86400, function () {
            $config = \App\Models\Configuracao::first();
            return $config ? $config->toArray() : [];
        });
    @endphp

    @if(!empty($configSite['google_analytics_id']))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $configSite['google_analytics_id'] }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $configSite['google_analytics_id'] }}');
        </script>
    @endif

    @if(!empty($configSite['meta_pixel_id']))
        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{{ $configSite['meta_pixel_id'] }}');
            fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ $configSite['meta_pixel_id'] }}&ev=PageView&noscript=1"/></noscript>
    @endif
    <script src="/assets/tailwind.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <link rel="stylesheet" href="/assets/base.css?v=1.3">
    <link rel="stylesheet" href="/assets/site.css?v=1.3">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-900 text-white hero-bg min-h-screen flex flex-col">
    
    @include('layouts.header') 
    
    <main class="flex-grow">
        @yield('content') 
    </main>

    @include('layouts.footer') 
    
</body>
</html>