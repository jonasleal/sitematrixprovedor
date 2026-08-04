<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

// O caminho correto que você encontrou na documentação!
$url = 'https://matrix.sgp.tsmx.app/api/ura/consultaplano/';

echo "=== TESTANDO NOVO ENDPOINT: /api/ura/consultaplano/ ===\n\n";

// TESTE 1: Passando as credenciais na URL (Query String)
echo "--- TESTE 1: Credenciais na URL ---\n";
$resp1 = Http::get($url, [
    'app' => 'SiteMatrix',
    'token' => '5f510256-6c73-44fe-8af3-889536242230'
]);
echo "Status: " . $resp1->status() . "\nBody: " . $resp1->body() . "\n\n";

// TESTE 2: Passando as credenciais no Cabeçalho (Headers - Padrão de APIs REST)
echo "--- TESTE 2: Credenciais no Header ---\n";
$resp2 = Http::withHeaders([
    'App' => 'SiteMatrix',
    'Token' => '5f510256-6c73-44fe-8af3-889536242230'
])->get($url);
echo "Status: " . $resp2->status() . "\nBody: " . $resp2->body() . "\n\n";
