<?php

namespace App\Services;

use App\Models\BasicSettings\Basic;
use App\Models\Event\EventImage;
use Illuminate\Support\Facades\File;

/**
 * Slides del hero (home y listado de eventos): intercala imágenes de campaña (hero-campaign)
 * con fotos reales de eventos; fallback al breadcrumb del sitio.
 */
class HeroSlideUrlsService
{
    /**
     * @return array<int, string> URLs absolutas para background-image
     */
    public static function build(): array
    {
        $campaignDir = public_path('assets/front/img/hero-campaign');
        $campaignUrls = [];
        if (is_dir($campaignDir)) {
            $campaignFiles = collect(File::files($campaignDir))
                ->filter(function (\SplFileInfo $file) {
                    $ext = strtolower($file->getExtension());

                    return in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true);
                })
                ->sort(function (\SplFileInfo $a, \SplFileInfo $b) {
                    return strnatcasecmp($a->getFilename(), $b->getFilename());
                })
                ->values();

            foreach ($campaignFiles as $file) {
                $campaignUrls[] = asset('assets/front/img/hero-campaign/'.$file->getFilename());
            }
        }

        $eventFilenames = EventImage::query()
            ->orderByDesc('id')
            ->limit(30)
            ->pluck('image')
            ->filter(fn ($img) => $img && file_exists(public_path('assets/admin/img/event-gallery/'.$img)))
            ->values()
            ->all();

        $eventUrls = array_map(
            static fn (string $img) => asset('assets/admin/img/event-gallery/'.$img),
            $eventFilenames
        );

        $slides = [];
        $i = 0;
        $j = 0;
        $n = count($campaignUrls);
        $m = count($eventUrls);
        while ($i < $n || $j < $m) {
            if ($i < $n) {
                $slides[] = $campaignUrls[$i];
                $i++;
            }
            if ($j < $m) {
                $slides[] = $eventUrls[$j];
                $j++;
            }
        }

        if ($slides === []) {
            $breadcrumb = Basic::query()->value('breadcrumb');
            if (! empty($breadcrumb) && file_exists(public_path('assets/admin/img/'.$breadcrumb))) {
                $slides[] = asset('assets/admin/img/'.$breadcrumb);
            }
        }

        return $slides;
    }
}
