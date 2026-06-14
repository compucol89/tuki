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
    protected $description = 'Convertir imagenes JPG/PNG existentes a WebP sin modificar los originales';

    public function handle(): int
    {
        $dirs = [
            public_path('assets/admin/img/event/thumbnail/'),
            public_path('assets/admin/img/event-gallery/'),
            public_path('assets/admin/img/clients/'),
            public_path('assets/admin/img/partner/'),
            public_path('assets/admin/img/testimonial/'),
            public_path('assets/front/img/hero-campaign/'),
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
                if (!in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
                    continue;
                }

                $total++;
                $path = $file->getPathname();
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
}
