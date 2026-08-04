<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Configuracao;

class AppServiceProvider extends ServiceProvider
{
    public function register() { }

    public function boot()
    {
        // Variável estática para guardar a configuração apenas durante a vida útil do clique do cliente
        $configGlobal = null;

        // Compartilha a variável com todas as views (Header, Footer, etc)
        View::composer('*', function ($view) use (&$configGlobal) {
            
            // Se a variável ainda estiver vazia, ele vai no banco (Uma única vez por acesso)
            if (!$configGlobal) {
                $configGlobal = Configuracao::first() ?? new Configuracao();
            }
            
            $view->with('configGlobal', $configGlobal);
        });
    }
}