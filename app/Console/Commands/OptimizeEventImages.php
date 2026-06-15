<?php

namespace App\Console\Commands;

use App\Services\FileUploadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class OptimizeEventImages extends Command
{
    private const MAX_WEBP_WIDTH = 1920;
    private const WEBP_QUALITY = 80;

    protected $signature = 'events:optimize-images';
    protected $description = 'Convertir JPG/PNG a WebP y recomprimir WebP existentes';

    public function handle(): int
    {
        $dirs = [
            public_path('assets/admin/img/event/thumbnail/'),
            public_path('assets/admin/img/event-gallery/'),
            public_path('assets/admin/img/clients/'),
            public_path('assets/admin/img/partner/'),
            public_path('assets/admin/img/testimonial/'),
            public_path('assets/front/img/hero-campaign/'),
            public_path('assets/admin/img/advertisements/'),
            public_path('assets/admin/img/product/gallery/'),
            public_path('assets/admin/img/product/feature_image/'),
            public_path('assets/admin/img/blogs/'),
            public_path('assets/admin/img/popups/'),
        ];

        $service = new FileUploadService();
        $total = 0;
        $converted = 0;
        $failed = 0;

        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                $this->warn("Directorio no encontrado: {$dir}");
                continue;
            }

            foreach (File::files($dir) as $file) {
                $ext = strtolower($file->getExtension());
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                    continue;
                }

                $total++;
                $path = $file->getPathname();

                if ($ext === 'webp') {
                    $this->line("Re-comprimiendo WebP: {$file->getFilename()}");
                    if ($this->recompressWebp($path)) {
                        $converted++;
                    } else {
                        $failed++;
                    }
                    continue;
                }

                $webpPath = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $path);
                if (file_exists($webpPath)) {
                    continue;
                }

                $this->line("Procesando: {$file->getFilename()}");
                if ($service->createResponsiveWebp($path, $ext, self::MAX_WEBP_WIDTH, self::WEBP_QUALITY)) {
                    $converted++;
                } else {
                    $failed++;
                }
            }
        }

        $this->info("{$converted} imágenes convertidas a WebP (de {$total} totales).");
        if ($failed > 0) {
            $this->warn("{$failed} imágenes no se pudieron convertir.");
        }

        return Command::SUCCESS;
    }

    private function recompressWebp(string $path): bool
    {
        if (!function_exists('imagecreatefromwebp') || !function_exists('imagewebp')) {
            return false;
        }

        $src = \App\Support\EventGalleryImageValidator::loadImageResource($path, 'webp');
        if ($src === false) {
            return false;
        }

        if (function_exists('imageistruecolor') && !imageistruecolor($src)) {
            imagepalettetotruecolor($src);
        }

        $width = imagesx($src);
        $height = imagesy($src);

        if ($width <= 0 || $height <= 0) {
            imagedestroy($src);

            return false;
        }

        if ($width > self::MAX_WEBP_WIDTH) {
            $newWidth = self::MAX_WEBP_WIDTH;
            $newHeight = (int) round($height * (self::MAX_WEBP_WIDTH / $width));
            $dst = imagecreatetruecolor($newWidth, $newHeight);
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($src);
            $src = $dst;
        }

        $result = imagewebp($src, $path, self::WEBP_QUALITY);
        imagedestroy($src);

        return $result;
    }
}
