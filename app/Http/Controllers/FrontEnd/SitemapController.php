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
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

class SitemapController extends Controller
{
  public function index()
  {
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
        'lastmod' => null,
        'changefreq' => $page['changefreq'],
        'priority' => $page['priority'],
      ];
    });

    $events = EventContent::join('events', 'events.id', '=', 'event_contents.event_id')
      ->where('events.status', 1)
      ->whereDate('events.end_date_time', '>=', now()->toDateString())
      ->when($defaultLanguageId, function ($query, $defaultLanguageId) {
        return $query->where('event_contents.language_id', $defaultLanguageId);
      })
      ->select('events.id', 'events.updated_at', 'event_contents.slug')
      ->orderBy('events.updated_at', 'desc')
      ->get()
      ->map(function ($event) {
        return [
          'loc' => route('event.details', ['slug' => $event->slug, 'id' => $event->id], true),
          'lastmod' => $this->formatLastmod($event->updated_at),
          'changefreq' => 'daily',
          'priority' => '0.8',
        ];
      });

    $blogs = Blog::join('blog_informations', 'blogs.id', '=', 'blog_informations.blog_id')
      ->when($defaultLanguageId, function ($query, $defaultLanguageId) {
        return $query->where('blog_informations.language_id', $defaultLanguageId);
      })
      ->select('blogs.updated_at', 'blog_informations.slug')
      ->orderBy('blogs.updated_at', 'desc')
      ->get()
      ->map(function ($blog) {
        return [
          'loc' => route('blog_details', ['slug' => $blog->slug], true),
          'lastmod' => $this->formatLastmod($blog->updated_at),
          'changefreq' => 'weekly',
          'priority' => '0.7',
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
      'register',
      'sitemap.xml',
      'sitemap',
      'event',
      'eventos',
      'blog',
      'shop',
      'contacto',
      'sobre-nosotros',
      'faq',
      'faqs',
    ];

    $customPages = Page::join('page_contents', 'pages.id', '=', 'page_contents.page_id')
      ->where('pages.status', 1)
      ->when($defaultLanguageId, function ($query, $defaultLanguageId) {
        return $query->where('page_contents.language_id', $defaultLanguageId);
      })
      ->whereNotIn('page_contents.slug', $reservedSlugs)
      ->select('pages.updated_at', 'page_contents.slug')
      ->orderBy('pages.updated_at', 'desc')
      ->get()
      ->map(function ($page) {
        return [
          'loc' => route('dynamic_page', ['slug' => $page->slug], true),
          'lastmod' => $this->formatLastmod($page->updated_at),
          'changefreq' => 'monthly',
          'priority' => '0.5',
        ];
      });

    $organizers = Organizer::where('status', 1)
      ->select('id', 'username', 'updated_at')
      ->orderBy('updated_at', 'desc')
      ->get()
      ->map(function ($organizer) {
        return [
          'loc' => route('frontend.organizer.details', [$organizer->id, str_replace(' ', '-', $organizer->username)], true),
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
      ->header('Content-Type', 'application/xml; charset=UTF-8');
  }

  private function formatLastmod($value)
  {
    if (empty($value)) {
      return null;
    }

    return Carbon::parse($value)->toAtomString();
  }
}
