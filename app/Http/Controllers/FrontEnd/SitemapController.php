<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Models\BasicSettings\Basic;
use App\Models\CustomPage\Page;
use App\Models\Event\EventContent;
use App\Models\Journal\Blog;
use App\Models\Language;
use App\Models\Organizer;
use App\Models\ShopManagement\Product;
use App\Support\DemoEventExclusion;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class SitemapController extends Controller
{

  private const DEMO_BLOG_SLUG_PREFIXES = [
    'vivamus-vestibulum',
    'vestibulum-commodo',
    'nam-dui-mi',
    'phasellus-ultrices',
    'donec-nec-justo',
    'morbi-in-sem',
  ];

  private const OBSOLETE_PAGE_SLUGS = [
    'privacy-policy',
    'terms-&-conditions',
  ];

  public function index()
  {
    URL::forceRootUrl(rtrim(config('app.url'), '/'));

    $defaultLanguageId = optional(Language::where('is_default', 1)->first())->id;
    $shopIsActive = (int) Basic::query()->value('shop_status') === 1;

    $staticPages = collect([
      ['route' => 'index', 'priority' => '1.0', 'changefreq' => 'daily'],
      ['route' => 'events', 'priority' => '0.9', 'changefreq' => 'daily'],
      ['route' => 'about', 'priority' => '0.7', 'changefreq' => 'monthly'],
      ['route' => 'contact', 'priority' => '0.6', 'changefreq' => 'monthly'],
      ['route' => 'faqs', 'priority' => '0.6', 'changefreq' => 'monthly'],
      ['route' => 'blogs', 'priority' => '0.7', 'changefreq' => 'weekly'],
      ['route' => 'frontend.all.organizer', 'priority' => '0.6', 'changefreq' => 'weekly'],
      ['route' => 'shop', 'priority' => '0.6', 'changefreq' => 'daily'],
    ])->filter(function ($page) use ($shopIsActive) {
      if ($page['route'] === 'shop' && !$shopIsActive) {
        return false;
      }

      return Route::has($page['route']);
    })->map(function ($page) {
      return [
        'loc' => route($page['route'], [], true),
        'lastmod' => Carbon::now()->format('c'),
        'changefreq' => $page['changefreq'],
        'priority' => $page['priority'],
      ];
    });

    $events = EventContent::join('events', 'events.id', '=', 'event_contents.event_id')
      ->where('events.status', 1)
      ->whereDate('events.end_date_time', '>=', now()->toDateString())
      ->whereNotIn('event_contents.slug', DemoEventExclusion::EVENT_SLUGS)
      ->whereNotIn('events.id', DemoEventExclusion::EVENT_IDS)
      ->when($defaultLanguageId, function ($query, $defaultLanguageId) {
        return $query->where('event_contents.language_id', $defaultLanguageId);
      })
      ->select('events.id', 'events.updated_at', 'events.end_date_time', 'event_contents.slug')
      ->orderBy('events.updated_at', 'desc')
      ->get()
      ->map(function ($event) {
        $isPast = !empty($event->end_date_time)
          && Carbon::parse($event->end_date_time)->lt(now());

        return [
          'loc' => route('event.details', ['slug' => $event->slug, 'id' => $event->id], true),
          'lastmod' => $this->formatLastmod($event->updated_at),
          'changefreq' => 'daily',
          'priority' => $isPast ? '0.1' : '0.8',
        ];
      });

    $blogs = Blog::join('blog_informations', 'blogs.id', '=', 'blog_informations.blog_id')
      ->whereDate('blogs.updated_at', '>=', '2024-01-01')
      ->where(function ($query) {
        foreach (self::DEMO_BLOG_SLUG_PREFIXES as $prefix) {
          $query->where('blog_informations.slug', 'not like', $prefix . '%');
        }
      })
      ->when($defaultLanguageId, function ($query, $defaultLanguageId) {
        return $query->where('blog_informations.language_id', $defaultLanguageId);
      })
      ->select('blogs.updated_at', 'blog_informations.slug')
      ->orderBy('blogs.updated_at', 'desc')
      ->get()
      ->map(function ($blog) {
        $ageMonths = !empty($blog->updated_at)
          ? Carbon::parse($blog->updated_at)->diffInMonths(now())
          : 0;
        $priority = $ageMonths > 12 ? '0.3' : '0.7';

        return [
          'loc' => route('blog_details', ['slug' => $blog->slug], true),
          'lastmod' => $this->formatLastmod($blog->updated_at),
          'changefreq' => 'weekly',
          'priority' => $priority,
        ];
      });

    $products = collect();

    if ($shopIsActive) {
      $products = Product::join('product_contents', 'products.id', '=', 'product_contents.product_id')
        ->where('products.status', 1)
        ->when($defaultLanguageId, function ($query, $defaultLanguageId) {
          return $query->where('product_contents.language_id', $defaultLanguageId);
        })
        ->select('products.id', 'products.updated_at', 'product_contents.slug')
        ->orderBy('products.updated_at', 'desc')
        ->get()
        ->map(function ($product) {
          return [
            'loc' => route('shop.details', ['slug' => $product->slug, 'id' => $product->id], true),
            'lastmod' => $this->formatLastmod($product->updated_at),
            'changefreq' => 'weekly',
            'priority' => '0.6',
          ];
        });
    }

    $reservedSlugs = [
      'admin',
      'customer',
      'organizer',
      'organizers',
      'checkout',
      'check-out2',
      'cart',
      'login',
      'registro',
      'recuperar-contrasena',
      'register',
      'sitemap.xml',
      'sitemap',
      'event',
      'eventos',
      'organizadores',
      'blog',
      'shop',
      'tienda',
      'contacto',
      'sobre-nosotros',
      'faq',
      'faqs',
      'preguntas-frecuentes',
    ];

    $customPages = Page::join('page_contents', 'pages.id', '=', 'page_contents.page_id')
      ->where('pages.status', 1)
      ->when($defaultLanguageId, function ($query, $defaultLanguageId) {
        return $query->where('page_contents.language_id', $defaultLanguageId);
      })
      ->whereNotIn('page_contents.slug', $reservedSlugs)
      ->whereNotIn('page_contents.slug', self::OBSOLETE_PAGE_SLUGS)
      ->where('page_contents.slug', 'not like', '%&%')
      ->select('pages.updated_at', 'page_contents.slug')
      ->orderBy('pages.updated_at', 'desc')
      ->get()
      ->map(function ($page) {
        $ageMonths = !empty($page->updated_at)
          ? Carbon::parse($page->updated_at)->diffInMonths(now())
          : 0;
        $priority = $ageMonths > 24 ? '0.3' : '0.5';

        return [
          'loc' => route('dynamic_page', ['slug' => $page->slug], true),
          'lastmod' => $this->formatLastmod($page->updated_at),
          'changefreq' => 'monthly',
          'priority' => $priority,
        ];
      });

    $organizers = Organizer::leftJoin('organizer_infos', function ($join) use ($defaultLanguageId) {
        $join->on('organizer_infos.organizer_id', '=', 'organizers.id');
        if ($defaultLanguageId) {
          $join->where('organizer_infos.language_id', $defaultLanguageId);
        }
      })
      ->where('organizers.status', 1)
      ->whereNotNull('organizers.username')
      ->where('organizers.username', '!=', '')
      ->select('organizers.id', 'organizers.username', 'organizers.updated_at', 'organizer_infos.name as profile_name')
      ->orderBy('organizers.updated_at', 'desc')
      ->get()
      ->map(function ($organizer) {
        $profileName = trim((string) ($organizer->profile_name ?: $organizer->username));
        $profileSlug = Str::slug($profileName);

        return [
          'loc' => route('frontend.organizer.details', [$organizer->id, $profileSlug !== '' ? $profileSlug : str_replace(' ', '-', $organizer->username)], true),
          'lastmod' => $this->formatLastmod($organizer->updated_at),
          'changefreq' => 'weekly',
          'priority' => '0.5',
        ];
      });

    $urls = $staticPages
      ->concat($events)
      ->concat($blogs)
      ->concat($products)
      ->concat($customPages)
      ->concat($organizers)
      ->unique('loc')
      ->values();

    return response()
      ->view('frontend.sitemap', compact('urls'))
      ->header('Content-Type', 'application/xml; charset=UTF-8')
      ->header('Cache-Control', 'public, max-age=900');
  }

  private function formatLastmod($value)
  {
    if (empty($value)) {
      return null;
    }

    return Carbon::parse($value)->toAtomString();
  }
}
