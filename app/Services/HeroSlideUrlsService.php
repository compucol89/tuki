<?php

namespace App\Services;

use App\Models\BasicSettings\Basic;
use App\Models\Event\EventImage;
use Illuminate\Support\Facades\Cache;
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
    public static function build(int $maxSlides = 8): array
    {
        $cacheKey = 'hero_slide_urls_'.$maxSlides;

        return Cache::remember($cacheKey, 3600, function () use ($maxSlides) {
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
                    $campaignUrls[] = FileUploadService::imageUrl('assets/front/img/hero-campaign/', $file->getFilename());
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
                static fn (string $img) => FileUploadService::imageUrl('assets/admin/img/event-gallery/', $img),
                $eventFilenames
            );

            $slides = [];
            $i = 0;
            $j = 0;
            $n = count($campaignUrls);
            $m = count($eventUrls);
            $totalSlides = min($n + $m, $maxSlides);
            $campaignCount = min($n, (int) ceil($totalSlides / 2));
            $eventCount = min($m, (int) floor($totalSlides / 2));

            while (count($slides) < $totalSlides && ($i < $campaignCount || $j < $eventCount)) {
                if ($i < $campaignCount) {
                    $slides[] = $campaignUrls[$i];
                    $i++;
                }
                if (count($slides) >= $totalSlides) {
                    break;
                }
                if ($j < $eventCount) {
                    $slides[] = $eventUrls[$j];
                    $j++;
                }
            }

            if ($slides === []) {
                $breadcrumb = Basic::query()->value('breadcrumb');
                if (! empty($breadcrumb) && file_exists(public_path('assets/admin/img/'.$breadcrumb))) {
                    $slides[] = FileUploadService::imageUrl('assets/admin/img/', $breadcrumb);
                }
            }

            return $slides;
        });
    }
}
