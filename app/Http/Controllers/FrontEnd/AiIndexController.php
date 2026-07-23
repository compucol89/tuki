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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Throwable;

class AiIndexController extends Controller
{
  private const DEMO_BLOG_SLUG_PREFIXES = [
    'vivamus-vestibulum',
    'vestibulum-commodo',
    'nam-dui-mi',
    'phasellus-ultrices',
    'donec-nec-justo',
    'morbi-in-sem',
  ];

  private const RESERVED_PAGE_SLUGS = [
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
    'privacy-policy',
    'terms-&-conditions',
  ];

  public function llms()
  {
    URL::forceRootUrl($this->rootUrl());

    $records = $this->publicUrlRecords();
    $lines = [
      '# Tukipass',
      '',
      '> Tukipass es una plataforma argentina para descubrir eventos y reservar entradas online.',
      '> Este archivo orienta a agentes IA hacia las paginas publicas canonicas del sitio.',
      '',
      'Tukipass opera comercialmente por TAYRONA GROUP SAS, CUIT 30-71885087-4. Tukipass no organiza ni produce los eventos publicados, salvo indicacion expresa; la realizacion, calidad, accesos, horarios, cambios, cancelaciones, reembolsos y condiciones particulares son responsabilidad exclusiva del organizador.',
      '',
      'Ultima actualizacion automatica: ' . Carbon::now()->toDateString(),
      '',
      '## Mapas de indexacion',
      '',
      '- [Sitemap XML](' . $this->url('/sitemap.xml') . '): inventario canonico para buscadores y crawlers.',
      '- [Sitemap de imagenes](' . $this->url('/sitemap-images.xml') . '): inventario de imagenes publicas de eventos, organizadores, blog y tienda.',
      '- [Referencia completa para agentes IA](' . $this->url('/llms-full.txt') . '): listado ampliado de URLs publicas indexables.',
      '- [Robots.txt](' . $this->url('/robots.txt') . '): politica de acceso para crawlers.',
      '',
    ];

    $this->appendSection($lines, 'Páginas principales', $records->where('section', 'Páginas principales'));
    $this->appendSection($lines, 'Eventos vigentes', $records->where('section', 'Eventos vigentes')->take(50));
    $this->appendSection($lines, 'Organizadores públicos', $records->where('section', 'Organizadores públicos')->take(50));
    $this->appendSection($lines, 'Contenido editorial', $records->where('section', 'Contenido editorial')->take(30));
    $this->appendSection($lines, 'Páginas informativas', $records->where('section', 'Páginas informativas')->take(50));

    if ($this->shopIsActive()) {
      $this->appendSection($lines, 'Tienda', $records->where('section', 'Tienda')->take(30));
    }

    $lines[] = '## Optional';
    $lines[] = '';
    $lines[] = '- [Contacto](' . $this->url('/contacto') . '): canal para consultas comerciales y soporte.';
    $lines[] = '- [Preguntas frecuentes](' . $this->url('/preguntas-frecuentes') . '): respuestas visibles para usuarios y asistentes.';
    $lines[] = '';

    return $this->textResponse($lines);
  }

  public function full()
  {
    URL::forceRootUrl($this->rootUrl());

    $records = $this->publicUrlRecords();
    $lines = [
      '# Tukipass',
      '',
      '> Tukipass es una plataforma argentina para descubrir eventos, revisar organizadores y reservar entradas online.',
      '> Esta referencia completa enumera las URLs publicas que pueden ser usadas por buscadores y agentes IA.',
      '',
      'Entidad operadora: TAYRONA GROUP SAS, CUIT 30-71885087-4.',
      '',
      'Importante: Tukipass no organiza ni produce los eventos publicados, salvo indicacion expresa. Tukipass presta un servicio tecnologico de publicacion, gestion y venta online de entradas. La realizacion, calidad, accesos, horarios, cambios, cancelaciones, reembolsos y condiciones particulares del evento son responsabilidad exclusiva del organizador.',
      '',
      'Al utilizar el sitio o reservar una entrada, el usuario acepta los Terminos y Condiciones de Tukipass y las politicas aplicables de cada evento.',
      '',
      'Ultima actualizacion automatica: ' . Carbon::now()->toDateString(),
      '',
      '## Politica de rastreo IA',
      '',
      '- OAI-SearchBot, Claude-SearchBot, PerplexityBot, Googlebot, Bingbot y Applebot pueden rastrear contenido publico.',
      '- GPTBot, ClaudeBot y Google-Extended estan bloqueados para entrenamiento de modelos.',
      '- Admin, cuenta de cliente, panel de organizador, checkout, reservas, facturas y rutas transaccionales no son indexables.',
      '',
      '## URLs públicas indexables',
      '',
    ];

    $records->groupBy('section')->each(function (Collection $sectionRecords, string $section) use (&$lines) {
      $this->appendSection($lines, $section, $sectionRecords);
    });

    return $this->textResponse($lines);
  }

  private function publicUrlRecords(): Collection
  {
    return collect($this->staticRecords())
      ->concat($this->eventRecords())
      ->concat($this->organizerRecords())
      ->concat($this->blogRecords())
      ->concat($this->customPageRecords())
      ->concat($this->productRecords())
      ->unique('url')
      ->values();
  }

  private function staticRecords(): array
  {
    $records = [
      $this->record('Páginas principales', 'Inicio', '/', 'Resumen de Tukipass, eventos destacados y acceso a la reserva de entradas.'),
      $this->record('Páginas principales', 'Eventos', '/eventos', 'Agenda publica de eventos en Argentina con filtros por busqueda, ubicacion, fecha y categoria.'),
      $this->record('Páginas principales', 'Organizadores', '/organizadores', 'Listado publico de organizadores y productores con perfil en Tukipass.'),
      $this->record('Páginas informativas', 'Sobre nosotros', '/sobre-nosotros', 'Informacion institucional sobre Tukipass.'),
      $this->record('Páginas informativas', 'Blog', '/blog', 'Contenido editorial y novedades relacionadas con eventos y tecnologia de entradas.'),
      $this->record('Páginas informativas', 'Preguntas frecuentes', '/preguntas-frecuentes', 'Respuestas para usuarios, compradores y organizadores.'),
      $this->record('Páginas informativas', 'Contacto', '/contacto', 'Canal publico de contacto con Tukipass.'),
    ];

    if ($this->shopIsActive()) {
      $records[] = $this->record('Tienda', 'Tienda', '/tienda', 'Catalogo publico de productos disponibles en Tukipass.');
    }

    return $records;
  }

  private function eventRecords(): Collection
  {
    $defaultLanguageId = $this->defaultLanguageId();

    return $this->safeCollect(function () use ($defaultLanguageId) {
      return EventContent::join('events', 'events.id', '=', 'event_contents.event_id')
        ->where('events.status', 1)
        ->whereDate('events.end_date_time', '>=', now()->toDateString())
        ->whereNotIn('event_contents.slug', DemoEventExclusion::EVENT_SLUGS)
        ->whereNotIn('events.id', DemoEventExclusion::EVENT_IDS)
        ->when($defaultLanguageId, function ($query, $defaultLanguageId) {
          return $query->where('event_contents.language_id', $defaultLanguageId);
        })
        ->select('events.id', 'events.updated_at', 'events.end_date_time', 'event_contents.slug', 'event_contents.title', 'event_contents.meta_description', 'event_contents.description')
        ->orderBy('events.updated_at', 'desc')
        ->get()
        ->map(function ($event) {
          $title = $this->cleanText($event->title, 90);
          $description = $this->cleanText($event->meta_description ?: $event->description, 180);

          return [
            'section' => 'Eventos vigentes',
            'title' => $title !== '' ? $title : 'Evento ' . $event->id,
            'url' => $this->routeUrl('event.details', ['slug' => $event->slug, 'id' => $event->id], '/' . $event->slug . '/' . $event->id),
            'description' => $description !== '' ? $description : 'Detalle publico del evento, fechas, ubicacion y opciones para reservar entradas.',
          ];
        });
    });
  }

  private function organizerRecords(): Collection
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
        ->select('organizers.id', 'organizers.username', 'organizers.updated_at', 'organizer_infos.name as profile_name', 'organizer_infos.details')
        ->orderBy('organizers.updated_at', 'desc')
        ->get()
        ->map(function ($organizer) {
          $profileName = trim((string) ($organizer->profile_name ?: $organizer->username));
          $profileSlug = Str::slug($profileName);
          $usernameSlug = str_replace(' ', '-', $organizer->username);

          return [
            'section' => 'Organizadores públicos',
            'title' => $profileName !== '' ? $profileName : $organizer->username,
            'url' => $this->routeUrl('frontend.organizer.details', [$organizer->id, $profileSlug !== '' ? $profileSlug : $usernameSlug], '/organizer/details/' . $organizer->id . '/' . ($profileSlug !== '' ? $profileSlug : $usernameSlug)),
            'description' => $this->cleanText($organizer->details, 170) ?: 'Perfil publico del organizador en Tukipass.',
          ];
        });
    });
  }

  private function blogRecords(): Collection
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
        ->select('blogs.updated_at', 'blog_informations.slug', 'blog_informations.title', 'blog_informations.meta_description', 'blog_informations.content')
        ->orderBy('blogs.updated_at', 'desc')
        ->get()
        ->map(function ($blog) {
          return [
            'section' => 'Contenido editorial',
            'title' => $this->cleanText($blog->title, 90) ?: 'Articulo de Tukipass',
            'url' => $this->routeUrl('blog_details', ['slug' => $blog->slug], '/blog/' . $blog->slug),
            'description' => $this->cleanText($blog->meta_description ?: $blog->content, 180) ?: 'Articulo editorial publicado por Tukipass.',
          ];
        });
    });
  }

  private function customPageRecords(): Collection
  {
    $defaultLanguageId = $this->defaultLanguageId();

    return $this->safeCollect(function () use ($defaultLanguageId) {
      return Page::join('page_contents', 'pages.id', '=', 'page_contents.page_id')
        ->where('pages.status', 1)
        ->when($defaultLanguageId, function ($query, $defaultLanguageId) {
          return $query->where('page_contents.language_id', $defaultLanguageId);
        })
        ->whereNotIn('page_contents.slug', self::RESERVED_PAGE_SLUGS)
        ->where('page_contents.slug', 'not like', '%&%')
        ->select('pages.updated_at', 'page_contents.slug', 'page_contents.title', 'page_contents.meta_description', 'page_contents.content')
        ->orderBy('pages.updated_at', 'desc')
        ->get()
        ->map(function ($page) {
          return [
            'section' => 'Páginas informativas',
            'title' => $this->cleanText($page->title, 90) ?: Str::headline((string) $page->slug),
            'url' => $this->routeUrl('dynamic_page', ['slug' => $page->slug], '/' . $page->slug),
            'description' => $this->cleanText($page->meta_description ?: $page->content, 180) ?: 'Pagina informativa publica de Tukipass.',
          ];
        });
    });
  }

  private function productRecords(): Collection
  {
    if (!$this->shopIsActive()) {
      return collect();
    }

    $defaultLanguageId = $this->defaultLanguageId();

    return $this->safeCollect(function () use ($defaultLanguageId) {
      return Product::join('product_contents', 'products.id', '=', 'product_contents.product_id')
        ->where('products.status', 1)
        ->when($defaultLanguageId, function ($query, $defaultLanguageId) {
          return $query->where('product_contents.language_id', $defaultLanguageId);
        })
        ->select('products.id', 'products.updated_at', 'product_contents.slug', 'product_contents.title', 'product_contents.meta_description', 'product_contents.summary', 'product_contents.description')
        ->orderBy('products.updated_at', 'desc')
        ->get()
        ->map(function ($product) {
          return [
            'section' => 'Tienda',
            'title' => $this->cleanText($product->title, 90) ?: 'Producto ' . $product->id,
            'url' => $this->routeUrl('shop.details', ['slug' => $product->slug, 'id' => $product->id], '/shop/details/' . $product->slug . '/' . $product->id),
            'description' => $this->cleanText($product->meta_description ?: $product->summary ?: $product->description, 180) ?: 'Producto publicado en la tienda de Tukipass.',
          ];
        });
    });
  }

  private function record(string $section, string $title, string $path, string $description): array
  {
    return [
      'section' => $section,
      'title' => $title,
      'url' => $this->url($path),
      'description' => $description,
    ];
  }

  private function appendSection(array &$lines, string $title, Collection $records): void
  {
    $records = $records->values();

    if ($records->isEmpty()) {
      return;
    }

    $lines[] = '## ' . $title;
    $lines[] = '';

    foreach ($records as $record) {
      $description = trim((string) ($record['description'] ?? ''));
      $line = '- [' . $this->escapeMarkdownLinkText($record['title']) . '](' . $record['url'] . ')';

      if ($description !== '') {
        $line .= ': ' . $description;
      }

      $lines[] = $line;
    }

    $lines[] = '';
  }

  private function textResponse(array $lines)
  {
    return response(implode("\n", $lines), 200, [
      'Content-Type' => 'text/plain; charset=UTF-8',
      'Cache-Control' => 'public, max-age=900',
    ]);
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

  private function cleanText($value, int $limit): string
  {
    $text = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8')));

    return Str::limit($text, $limit, '');
  }

  private function escapeMarkdownLinkText(string $value): string
  {
    return str_replace([']', '['], ['\]', '\['], $value);
  }
}
