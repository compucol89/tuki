<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Models\BasicSettings\Basic;
use App\Models\CustomPage\Page;
use App\Models\Event\EventContent;
use App\Models\Event\EventImage;
use App\Models\Journal\Blog;
use App\Models\Language;
use App\Models\Organizer;
use App\Models\ShopManagement\Product;
use App\Models\ShopManagement\ProductImage;
use App\Services\FileUploadService;
use App\Support\DemoEventExclusion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Throwable;

class ImageSitemapController extends Controller
{
  private const DEMO_BLOG_SLUG_PREFIXES = [
    'vivamus-vestibulum',
    'vestibulum-commodo',
    'nam-dui-mi',
    'phasellus-ultrices',
    'donec-nec-justo',
    'morbi-in-sem',
  ];

  public function index()
  {
    URL::forceRootUrl($this->rootUrl());

    $urls = $this->eventEntries()
      ->concat($this->blogEntries())
      ->concat($this->productEntries())
      ->concat($this->organizerEntries())
      ->concat($this->staticPageEntries())
      ->filter(fn ($entry) => !empty($entry['images']))
      ->unique('loc')
      ->values();

    return response()
      ->view('frontend.sitemap-images', compact('urls'))
      ->header('Content-Type', 'application/xml; charset=UTF-8')
      ->header('Cache-Control', 'public, max-age=900');
  }

  private function eventEntries(): Collection
  {
    $defaultLanguageId = $this->defaultLanguageId();

    return $this->safeCollect(function () use ($defaultLanguageId) {
      $events = EventContent::join('events', 'events.id', '=', 'event_contents.event_id')
        ->where('events.status', 1)
        ->whereDate('events.end_date_time', '>=', now()->toDateString())
        ->whereNotIn('event_contents.slug', DemoEventExclusion::EVENT_SLUGS)
        ->whereNotIn('events.id', DemoEventExclusion::EVENT_IDS)
        ->when($defaultLanguageId, function ($query, $defaultLanguageId) {
          return $query->where('event_contents.language_id', $defaultLanguageId);
        })
        ->select('events.id', 'events.thumbnail', 'events.og_image', 'events.updated_at', 'event_contents.slug')
        ->orderBy('events.updated_at', 'desc')
        ->get();

      $galleryImages = EventImage::whereIn('event_id', $events->pluck('id'))
        ->select('event_id', 'image')
        ->get()
        ->groupBy('event_id');

      return $events->map(function ($event) use ($galleryImages) {
        $images = collect([
          $this->imageUrlIfExists('assets/admin/img/event-ai/' . $event->id . '/', $event->og_image),
          $this->imageUrlIfExists('assets/admin/img/event/thumbnail/', $event->thumbnail),
        ]);

        foreach ($galleryImages->get($event->id, collect()) as $galleryImage) {
          $images->push($this->imageUrlIfExists('assets/admin/img/event-gallery/', $galleryImage->image));
        }

        return $this->entry(
          $this->routeUrl('event.details', ['slug' => $event->slug, 'id' => $event->id], '/' . $event->slug . '/' . $event->id),
          $images
        );
      });
    });
  }

  private function blogEntries(): Collection
  {
    $defaultLanguageId = $this->defaultLanguageId();

    return $this->safeCollect(function () use ($defaultLanguageId) {
      return Blog::join('blog_informations', 'blogs.id', '=', 'blog_informations.blog_id')
        ->whereDate('blogs.updated_at', '>=', '2024-01-01')
        ->where(function ($query) {
          foreach (self::DEMO_BLOG_SLUG_PREFIXES as $prefix) {
            $query->where('blog_informations.slug', 'not like', $prefix . '%');
          }
        })
        ->when($defaultLanguageId, function ($query, $defaultLanguageId) {
          return $query->where('blog_informations.language_id', $defaultLanguageId);
        })
        ->select('blogs.image', 'blog_informations.slug')
        ->orderBy('blogs.updated_at', 'desc')
        ->get()
        ->map(function ($blog) {
          return $this->entry(
            $this->routeUrl('blog_details', ['slug' => $blog->slug], '/blog/' . $blog->slug),
            collect([$this->imageUrlIfExists('assets/admin/img/blogs/', $blog->image)])
          );
        });
    });
  }

  private function productEntries(): Collection
  {
    if (!$this->shopIsActive()) {
      return collect();
    }

    $defaultLanguageId = $this->defaultLanguageId();

    return $this->safeCollect(function () use ($defaultLanguageId) {
      $products = Product::join('product_contents', 'products.id', '=', 'product_contents.product_id')
        ->where('products.status', 1)
        ->when($defaultLanguageId, function ($query, $defaultLanguageId) {
          return $query->where('product_contents.language_id', $defaultLanguageId);
        })
        ->select('products.id', 'products.feature_image', 'product_contents.slug')
        ->orderBy('products.updated_at', 'desc')
        ->get();

      $galleryImages = ProductImage::whereIn('product_id', $products->pluck('id'))
        ->select('product_id', 'image')
        ->get()
        ->groupBy('product_id');

      return $products->map(function ($product) use ($galleryImages) {
        $images = collect([
          $this->imageUrlIfExists('assets/admin/img/product/feature_image/', $product->feature_image),
        ]);

        foreach ($galleryImages->get($product->id, collect()) as $galleryImage) {
          $images->push($this->imageUrlIfExists('assets/admin/img/product/gallery/', $galleryImage->image));
        }

        return $this->entry(
          $this->routeUrl('shop.details', ['slug' => $product->slug, 'id' => $product->id], '/shop/details/' . $product->slug . '/' . $product->id),
          $images
        );
      });
    });
  }

  private function organizerEntries(): Collection
  {
    $defaultLanguageId = $this->defaultLanguageId();

    return $this->safeCollect(function () use ($defaultLanguageId) {
      return Organizer::leftJoin('organizer_infos', function ($join) use ($defaultLanguageId) {
          $join->on('organizer_infos.organizer_id', '=', 'organizers.id');
          if ($defaultLanguageId) {
            $join->where('organizer_infos.language_id', $defaultLanguageId);
          }
        })
        ->where('organizers.status', 1)
        ->whereNotNull('organizers.username')
        ->where('organizers.username', '!=', '')
        ->select('organizers.id', 'organizers.username', 'organizers.photo', 'organizers.cover_photo', 'organizer_infos.name as profile_name')
        ->orderBy('organizers.updated_at', 'desc')
        ->get()
        ->map(function ($organizer) {
          $profileName = trim((string) ($organizer->profile_name ?: $organizer->username));
          $profileSlug = Str::slug($profileName);
          $usernameSlug = str_replace(' ', '-', $organizer->username);
          $slug = $profileSlug !== '' ? $profileSlug : $usernameSlug;

          return $this->entry(
            $this->routeUrl('frontend.organizer.details', [$organizer->id, $slug], '/organizer/details/' . $organizer->id . '/' . $slug),
            collect([
              $this->imageUrlIfExists('assets/admin/img/organizer-photo/', $organizer->photo),
              $this->imageUrlIfExists('assets/admin/img/organizer-cover-photo/', $organizer->cover_photo),
            ])
          );
        });
    });
  }

  private function staticPageEntries(): Collection
  {
    return $this->safeCollect(function () {
      $basic = Basic::select('logo', 'breadcrumb')->first();
      $images = collect([
        $this->imageUrlIfExists('assets/admin/img/', $basic->logo ?? null),
        $this->imageUrlIfExists('assets/admin/img/', $basic->breadcrumb ?? null),
      ]);

      $entries = collect([
        $this->entry($this->url('/'), $images),
        $this->entry($this->routeUrl('events', [], '/eventos'), $images),
        $this->entry($this->routeUrl('frontend.all.organizer', [], '/organizadores'), $images),
      ]);

      return $entries->concat($this->customPageEntries($images));
    });
  }

  private function customPageEntries(Collection $fallbackImages): Collection
  {
    $defaultLanguageId = $this->defaultLanguageId();

    return Page::join('page_contents', 'pages.id', '=', 'page_contents.page_id')
      ->where('pages.status', 1)
      ->when($defaultLanguageId, function ($query, $defaultLanguageId) {
        return $query->where('page_contents.language_id', $defaultLanguageId);
      })
      ->whereNotIn('page_contents.slug', [
        'admin',
        'customer',
        'organizer',
        'checkout',
        'cart',
        'privacy-policy',
        'terms-&-conditions',
      ])
      ->where('page_contents.slug', 'not like', '%&%')
      ->select('page_contents.slug')
      ->orderBy('pages.updated_at', 'desc')
      ->get()
      ->map(function ($page) use ($fallbackImages) {
        return $this->entry(
          $this->routeUrl('dynamic_page', ['slug' => $page->slug], '/' . $page->slug),
          $fallbackImages
        );
      });
  }

  private function entry(string $loc, Collection $images): array
  {
    return [
      'loc' => $loc,
      'images' => $images
        ->filter()
        ->unique()
        ->take(1000)
        ->values()
        ->all(),
    ];
  }

  private function imageUrlIfExists(string $relativeDir, $filename): ?string
  {
    $filename = trim((string) $filename);

    if ($filename === '' || !FileUploadService::imageExists($relativeDir, $filename)) {
      return null;
    }

    return FileUploadService::imageUrl($relativeDir, $filename);
  }

  private function routeUrl(string $route, array $parameters, string $fallbackPath): string
  {
    try {
      if (Route::has($route)) {
        return route($route, $parameters, true);
      }
    } catch (Throwable $e) {
      return $this->url($fallbackPath);
    }

    return $this->url($fallbackPath);
  }

  private function url(string $path): string
  {
    $root = $this->rootUrl();

    if ($path === '/' || $path === '') {
      return $root . '/';
    }

    return $root . '/' . ltrim($path, '/');
  }

  private function rootUrl(): string
  {
    $root = rtrim((string) config('app.url'), '/');

    if ($root === '' || Str::contains($root, ['localhost', '127.0.0.1'])) {
      return 'https://www.tukipass.com';
    }

    return $root;
  }

  private function defaultLanguageId(): ?int
  {
    try {
      return optional(Language::where('is_default', 1)->first())->id;
    } catch (Throwable $e) {
      return null;
    }
  }

  private function shopIsActive(): bool
  {
    try {
      return (int) Basic::query()->value('shop_status') === 1;
    } catch (Throwable $e) {
      return true;
    }
  }

  private function safeCollect(callable $callback): Collection
  {
    try {
      return collect($callback());
    } catch (Throwable $e) {
      return collect();
    }
  }
}
