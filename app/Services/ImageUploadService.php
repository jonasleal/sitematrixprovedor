<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageUploadService
{
    /**
     * Otimiza uma imagem usando o motor GD nativo do PHP de forma pura, sem pacotes de terceiros.
     *
     * @param UploadedFile $file O arquivo vindo do request
     * @param string $path O diretório destino
     * @param int $quality Qualidade do WebP (0 a 100)
     * @return string O caminho final da imagem salva
     */
    public static function uploadAndOptimize(UploadedFile $file, string $path, int $quality = 80): string
    {
        $realPath = $file->getRealPath();
        
        // Verifica se o arquivo é realmente uma imagem e descobre o tipo
        $info = @getimagesize($realPath);
        if (!$info) {
            throw new \Exception("O arquivo enviado não é uma imagem válida ou está corrompido.");
        }

        $mime = $info['mime'];
        $image = null;

        // Carrega a imagem original na memória RAM usando as funções NATIVAS do Ubuntu/PHP
        switch ($mime) {
            case 'image/jpeg':
                $image = @imagecreatefromjpeg($realPath);
                break;
            case 'image/png':
                $image = @imagecreatefrompng($realPath);
                if ($image) {
                    // Mantém o fundo transparente seguro para PNGs
                    imagepalettetotruecolor($image);
                    imagealphablending($image, false);
                    imagesavealpha($image, true);
                }
                break;
            case 'image/gif':
                $image = @imagecreatefromgif($realPath);
                break;
            case 'image/webp':
                $image = @imagecreatefromwebp($realPath);
                break;
            default:
                // Se for SVG ou outro formato vetorial, salva o arquivo exatamente como veio, sem mexer
                $extension = $file->getClientOriginalExtension();
                $filename = uniqid(time() . '_') . '.' . $extension;
                $fullPath = $path . '/' . $filename;
                Storage::disk('public')->put($fullPath, file_get_contents($realPath));
                return $fullPath;
        }

        if (!$image) {
            throw new \Exception("Falha do Servidor: Não foi possível processar a imagem com o motor GD nativo.");
        }

        // Prepara o caminho final do novo arquivo WebP
        $filename = uniqid(time() . '_') . '.webp';
        $fullPath = $path . '/' . $filename;

        // Captura a conversão para WebP diretamente na memória (sem salvar no disco temporário)
        ob_start();
        imagewebp($image, null, $quality);
        $imageContent = ob_get_clean();
        
        // Esvazia a RAM do servidor
        imagedestroy($image);

        // Salva o binário leve no Storage do Laravel
        Storage::disk('public')->put($fullPath, $imageContent);

        return $fullPath;
    }
}