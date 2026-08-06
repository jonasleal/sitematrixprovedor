<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aviso - Matrix Provedor</title>
    <link rel="stylesheet" href="/assets/estilo.css">
</head>
<body>
    <div class="glass-panel glow-pink">
        <img src="/assets/logo-matrix.png" alt="Matrix Provedor" class="logo-matrix">

        <div class="icon-box icon-pink">
            <svg style="width: 40px; height: 40px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
        </div>

        <h1 class="titulo">Acesso Temporariamente Suspenso</h1>
        
        <p class="mensagem">
            Olá, <strong>{{ $nomeCliente ?? 'Cliente' }}</strong>! Identificamos uma pendência em seu cadastro ou equipamento que gerou a suspensão temporária da sua conexão.
        </p>

        <div class="options-box">
            <p class="op-titulo">Opções de Regularização:</p>
            
            <div class="option-item">
                <svg class="ico-verde" style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                <span>Acesse: <strong>matrixprovedor.net/central</strong></span>
            </div>
            
            <div class="option-item">
                <svg class="ico-ciano" style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                <span>Suporte: <strong>(87) 99964-4914</strong></span>
            </div>
        </div>

        <a href="http://matrixprovedor.net/central" class="btn-accent" style="text-align: center; display: block;">
            Acessar Central do Assinante
        </a>
    </div>
</body>
</html>