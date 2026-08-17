<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class BackupController extends Controller
{
    /**
     * Lista a página de backups. A consulta ao Google Drive é feita sob demanda (Lazy Loading).
     */
    public function index(Request $request)
    {
        // Começa como nulo. A página carregará instantaneamente.
        $backups = null; 

        // Só executa a varredura se o usuário clicar no botão de listar
        if ($request->has('fetch')) {
            try {
                $disk = Storage::disk('google');
                $pastaAlvo = env('GOOGLE_DRIVE_BACKUP_PATH');

                if (empty($pastaAlvo)) {
                    throw new \Exception("A variável GOOGLE_DRIVE_BACKUP_PATH não está definida no ficheiro .env.");
                }

                $files = $disk->files($pastaAlvo);
                $backups = [];

                foreach ($files as $file) {
                    if (str_ends_with(strtolower($file), '.zip')) {
                        $backups[] = [
                            'name' => basename($file),
                            'path' => $file,
                            'size' => round($disk->size($file) / 1048576, 2) . ' MB',
                            'timestamp' => $disk->lastModified($file),
                            'date' => \Carbon\Carbon::createFromTimestamp($disk->lastModified($file))->format('d/m/Y H:i:s'),
                        ];
                    }
                }

                usort($backups, function ($a, $b) {
                    return $b['timestamp'] <=> $a['timestamp'];
                });

            } catch (\Exception $e) {
                Log::error("Erro ao listar backups do Drive: " . $e->getMessage());
                // Em vez de dar 'back()', enviamos para a view normal com a mensagem de erro para evitar loops de sessão
                return view('admin.backups.index', compact('backups'))->with('error', 'Falha ao aceder ao cofre: ' . $e->getMessage());
            }
        }

        return view('admin.backups.index', compact('backups'));
    }

    /**
     * Baixa e restaura um backup do Google Drive
     */
    public function restore(Request $request)
    {
        $request->validate(['path' => 'required|string']);
        $path = $request->path;
        $disk = Storage::disk('google');

        if (!$disk->exists($path)) {
            return back()->with('error', 'O ficheiro de backup não foi encontrado na nuvem.');
        }

        // Retira o limite de tempo do PHP para permitir o download e extração de ficheiros grandes
        set_time_limit(0);

        $localZipPath = storage_path('app/temp_backup.zip');
        $extractPath = storage_path('app/temp_restore');

        try {
            // 1. Download Cirúrgico da Nuvem
            $zipContent = $disk->get($path);
            file_put_contents($localZipPath, $zipContent);

            // 2. Prepara a diretoria de Extração Segura
            File::deleteDirectory($extractPath);
            mkdir($extractPath, 0755, true);

            $zip = new ZipArchive;
            if ($zip->open($localZipPath) === TRUE) {
                $zip->extractTo($extractPath);
                $zip->close();
            } else {
                throw new \Exception("O ficheiro ZIP está corrompido ou é inválido.");
            }

            // 3. Restauração Bruta da Base de Dados (Via Binário Linux)
            $sqlFiles = File::allFiles($extractPath);
            $sqlFileToRestore = null;
            
            foreach ($sqlFiles as $file) {
                if ($file->getExtension() === 'sql') {
                    $sqlFileToRestore = $file->getRealPath();
                    break;
                }
            }

            if ($sqlFileToRestore) {
                $dbUser = env('DB_USERNAME');
                $dbPass = env('DB_PASSWORD');
                $dbName = env('DB_DATABASE');
                $dbHost = env('DB_HOST', '127.0.0.1');

                // Comando nativo do MariaDB
                $command = sprintf(
                    'mysql -h %s -u %s -p%s %s < %s',
                    escapeshellarg($dbHost),
                    escapeshellarg($dbUser),
                    escapeshellarg($dbPass),
                    escapeshellarg($dbName),
                    escapeshellarg($sqlFileToRestore)
                );
                exec($command);
            } else {
                throw new \Exception("Nenhum ficheiro de base de dados (.sql) foi encontrado dentro do backup.");
            }

            // 4. Restauração de Imagens/Uploads (Se existirem no ZIP)
            $publicStorageBackupPath = $extractPath . '/var/www/matrix/site/storage/app/public';
            if (is_dir($publicStorageBackupPath)) {
                File::copyDirectory($publicStorageBackupPath, storage_path('app/public'));
            }

            // 5. Autodestruição e Limpeza
            unlink($localZipPath);
            File::deleteDirectory($extractPath);

            return back()->with('success', 'Máquina do Tempo ativada! Base de Dados e Ficheiros restaurados com sucesso.');

        } catch (\Exception $e) {
            // Em caso de erro, limpa os rastros para não lotar o disco do servidor
            @unlink($localZipPath);
            @File::deleteDirectory($extractPath);
            Log::error("Falha Crítica na Restauração de Backup: " . $e->getMessage());
            return back()->with('error', 'Erro ao restaurar: ' . $e->getMessage());
        }
    }
}