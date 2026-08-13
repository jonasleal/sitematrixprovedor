<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;
use App\Models\Configuracao;
use League\Flysystem\Filesystem;
use Google\Client;
use Google\Service\Drive;
use Masbug\Flysystem\GoogleDriveAdapter;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // =========================================================
        // 1. COMPARTILHAMENTO GLOBAL DE CONFIGURAÇÕES (SITE MATRIX)
        // =========================================================
        $configGlobal = null;

        View::composer('*', function ($view) use (&$configGlobal) {
            if (!$configGlobal) {
                $configGlobal = Configuracao::first() ?? new Configuracao();
            }
            $view->with('configGlobal', $configGlobal);
        });

        // =========================================================
        // 2. MOTOR DO GOOGLE DRIVE (BACKUP)
        // =========================================================
        Storage::extend('google', function ($app, $config) {
            $client = new Client();
            $client->setClientId($config['clientId']);
            $client->setClientSecret($config['clientSecret']);
            $client->refreshToken($config['refreshToken']);

            $service = new Drive($client);

            // BLINDAGEM: O trim() remove espaços acidentais ou quebras de linha do .env
            $folderId = trim($config['folderId'] ?? '');

            $adapter = new GoogleDriveAdapter($service, $folderId, $config);
            $driver = new Filesystem($adapter);

            return new FilesystemAdapter($driver, $adapter, $config);
        });
    }
}