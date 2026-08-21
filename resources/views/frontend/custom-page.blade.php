@extends('frontend.layout')

@section('pageHeading')
  {{ $pageInfo->title }}
@endsection

@section('meta-keywords')
  {{ $pageInfo->meta_keywords }}
@endsection

@section('meta-description')
  {{ $pageInfo->meta_description }}
@endsection

@section('og-title', "$pageInfo->title")
@section('og-url', url()->current())
@section('og-type', 'website')
@section('canonical', url()->current())

@section('body-class', 'legal-page')

@php
  // ── Estructura de lectura: extraer secciones (H3 numerados) y fecha de
  //    actualización del contenido Summernote (sin duplicar H2 = título).
  $legalContent = $pageInfo->content;
  $legalSections = [];
  preg_match_all('/<h3[^>]*>(.*?)<\/h3>/i', $legalContent, $matches);
  if (!empty($matches[1])) {
    foreach ($matches[1] as $i => $h3) {
      $text = trim(strip_tags($h3));
      if ($text === '') continue;
      $anchor = 'seccion-' . ($i + 1);
      $legalSections[] = ['anchor' => $anchor, 'title' => $text];
      // agregar id al H3 para que el TOC ancle
      $legalContent = preg_replace(
        '/<h3([^>]*)>' . preg_quote($h3, '/') . '<\/h3>/i',
        '<h3$1 id="' . $anchor . '">' . $h3 . '</h3>',
        $legalContent,
        1
      );
    }
  }
  // fecha de actualización real del contenido
  $legalUpdated = null;
  if (preg_match('/Última actualización:\s*<\/strong>\s*([\d\/]+)/i', $legalContent, $m)) {
    $legalUpdated = $m[1];
  } elseif (preg_match('/(\d{1,2}\/\d{1,2}\/\d{4})/', $legalContent, $m)) {
    $legalUpdated = $m[1];
  }
  // H2 duplicado del H1 (título repetido en el contenido): convertirlo en un
  // lead de presentación (texto visible, sin heading duplicado → SEO jerárquico)
  $pageTitleTrim = trim(strip_tags($pageInfo->title ?? ''));
  if ($pageTitleTrim !== '' && preg_match('/<h2[^>]*>(.*?)<\/h2>/i', $legalContent, $h2m)) {
    $h2Text = trim(strip_tags($h2m[1]));
    if (mb_strtolower($h2Text) === mb_strtolower($pageTitleTrim)) {
      $legalContent = preg_replace(
        '/<h2[^>]*>' . preg_quote($h2m[1], '/') . '<\/h2>/i',
        '<p class="legal-lead" aria-label="' . e($pageTitleTrim) . '">' . $h2m[1] . '</p>',
        $legalContent,
        1
      );
    }
  }
  // páginas legales para navegación interna (enlazado SEO + wayfinding)
  $legalNav = [
    ['slug' => 'terminos-y-condiciones', 'title' => __('Términos y condiciones')],
    ['slug' => 'politica-de-privacidad', 'title' => __('Política de privacidad')],
    ['slug' => 'politica-de-cookies', 'title' => __('Política de cookies')],
    ['slug' => 'politica-de-reembolsos', 'title' => __('Política de reembolsos')],
    ['slug' => 'eliminacion-de-datos', 'title' => __('Eliminación de datos')],
    ['slug' => 'defensa-al-consumidor', 'title' => __('Defensa al consumidor')],
  ];
  $currentSlug = $pageInfo->slug ?? '';
  $isRefundPage = $currentSlug === 'politica-de-reembolsos';
@endphp

@section('custom-style')
  <link rel="stylesheet" href="{{ asset('assets/admin/css/summernote-content.css') }}">
  <style>
    /* ═══ Páginas legales — modo lectura ═══ */
    /* Banner compacto: menos scroll para llegar al contenido (estándar legal) */
    body.legal-page .page-banner {
      padding-top: 70px;
      padding-bottom: 70px;
    }

    body.legal-page .page-banner .page-title {
      font-size: clamp(1.7rem, 3vw, 2.4rem);
      margin-bottom: 12px;
    }

    .legal-layout {
      --legal-measure: 68ch;
      display: grid;
      grid-template-columns: minmax(0, 1fr) 260px;
      gap: clamp(32px, 5vw, 64px);
      align-items: start;
      max-width: 1080px;
      margin-inline: auto;
    }

    .legal-article {
      min-width: 0;
    }

    .legal-article__meta {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 8px 14px;
      margin-bottom: calc(var(--flow-space-heading, 1.6em) + 4px);
      padding-bottom: 18px;
      border-bottom: 1px solid var(--surface-card-border, var(--tuki-border, rgba(30, 37, 50, 0.1)));
    }

    .legal-article__updated {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      font-size: 13.5px;
      font-weight: 600;
      color: var(--card-foreground, #333333);
      letter-spacing: 0.01em;
    }

    .legal-article__updated i {
      font-size: 12px;
      opacity: 0.85;
    }

    .legal-article .summernote-content {
      max-width: var(--legal-measure);
    }

    /* introducción (primer párrafo tras el título) más presente */
    .legal-article .summernote-content > p:first-of-type {
      font-size: 17px;
      line-height: 1.7;
      color: var(--card-foreground, #333333);
    }

    /* lead: reemplaza el H2 duplicado del título (sin heading duplicado) */
    .legal-article .summernote-content > .legal-lead {
      font-size: 18px;
      font-weight: 700;
      line-height: 1.6;
      color: var(--card-foreground, #333333);
      margin-block-start: 0;
      margin-block-end: calc(var(--flow-space, 1.2em) * 0.6);
    }

    .legal-article .summernote-content h2 {
      font-size: clamp(1.5rem, 2.2vw, 1.8rem);
      letter-spacing: -0.02em;
      margin-block-start: calc(var(--flow-space-heading, 1.6em) * 1.4);
    }

    .legal-article .summernote-content h3 {
      font-size: clamp(1.08rem, 1.4vw, 1.2rem);
      letter-spacing: -0.01em;
      scroll-margin-top: 110px;
    }

    /* ── TOC sticky ── */
    .legal-toc {
      position: sticky;
      top: 96px;
      border: 1px solid var(--surface-card-border, var(--tuki-border, rgba(30, 37, 50, 0.1)));
      border-radius: 14px;
      background: var(--surface-card, var(--card, #ffffff));
      padding: 18px 8px 12px;
      max-height: calc(100vh - 140px);
      overflow-y: auto;
    }

    .legal-toc__title {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 0 12px 10px;
      font-size: 12px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.09em;
      color: var(--card-foreground, #333333);
    }

    .legal-toc__list {
      display: flex;
      flex-direction: column;
      gap: 1px;
      margin: 0;
      padding: 0;
      list-style: none;
    }

    .legal-toc__item a {
      display: block;
      padding: 7px 12px;
      border-radius: 8px;
      font-size: 13.5px;
      font-weight: 500;
      line-height: 1.45;
      color: var(--card-foreground, #333333);
      text-decoration: none;
      transition: background-color 0.15s ease, color 0.15s ease;
    }

    .legal-toc__item a:hover,
    .legal-toc__item a:focus-visible {
      background: var(--surface-hover, rgba(30, 37, 50, 0.06));
      color: var(--card-foreground, #333333);
      outline: none;
    }

    /* ── navegación entre documentos legales ── */
    .legal-nav {
      margin-top: clamp(40px, 6vw, 64px);
      padding-top: 26px;
      border-top: 1px solid var(--surface-card-border, var(--tuki-border, rgba(30, 37, 50, 0.1)));
    }

    .legal-nav__title {
      margin-bottom: 16px;
      font-size: 12px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.09em;
      color: var(--card-foreground, #333333);
    }

    .legal-nav__grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 10px;
    }

    .legal-nav__link {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 12px 14px;
      border: 1px solid var(--surface-card-border, var(--tuki-border, rgba(30, 37, 50, 0.1)));
      border-radius: 10px;
      font-size: 14px;
      font-weight: 600;
      color: var(--card-foreground, #333333);
      text-decoration: none;
      transition: border-color 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease;
    }

    .legal-nav__link:hover,
    .legal-nav__link:focus-visible {
      border-color: var(--primary, #f97316);
      box-shadow: 0 4px 14px rgba(249, 115, 22, 0.14);
      transform: translateY(-1px);
      outline: none;
    }

    .legal-nav__link.is-current {
      border-color: var(--primary, #f97316);
      background: var(--primary-soft, rgba(249, 115, 22, 0.12));
    }

    .legal-nav__link i {
      font-size: 13px;
      color: var(--primary, #f97316);
    }

    @media (max-width: 991.98px) {
      .legal-layout {
        grid-template-columns: 1fr;
      }
      .legal-toc {
        position: static;
        max-height: none;
      }
    }
  </style>
@endsection

@section('hero-section')
  <!-- Page Banner Start -->
  <section class="page-banner overlay pt-120 pb-125 rpt-90 rpb-95 lazy"
    data-bg="{{ asset('assets/admin/img/' . $basicInfo->breadcrumb) }}">
    <div class="container">
      <div class="banner-inner">
        <h1 class="page-title">{{ $pageInfo->title }}</h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">{{ __('Inicio') }}</a></li>
            <li class="breadcrumb-item active">{{ $pageInfo->title }}</li>
          </ol>
        </nav>
      </div>
    </div>
  </section>
  <!-- Page Banner End -->
@endsection

@php
  // ── JSON-LD por página legal: WebPage + BreadcrumbList (y MerchantReturnPolicy
  //    solo en reembolsos, con datos reales). Generado con json_encode como el
  //    layout global — evita el conflicto de llaves con el compilador Blade.
  $legalJsonLd = [
    '@context' => 'https://schema.org',
    '@graph' => [
      [
        '@type' => 'WebPage',
        '@id' => url()->current() . '#webpage',
        'url' => url()->current(),
        'name' => $pageInfo->title,
        'description' => $pageInfo->meta_description ?? $pageInfo->title,
        'inLanguage' => 'es-AR',
        'isPartOf' => ['@id' => url('/') . '#website'],
        'breadcrumb' => ['@id' => url()->current() . '#breadcrumb'],
      ],
      [
        '@type' => 'BreadcrumbList',
        '@id' => url()->current() . '#breadcrumb',
        'itemListElement' => [
          ['@type' => 'ListItem', 'position' => 1, 'name' => __('Inicio'), 'item' => url('/')],
          ['@type' => 'ListItem', 'position' => 2, 'name' => $pageInfo->title, 'item' => url()->current()],
        ],
      ],
    ],
  ];
  if ($isRefundPage) {
    $legalJsonLd['@graph'][] = [
      '@type' => 'MerchantReturnPolicy',
      '@id' => url()->current() . '#return-policy',
      'applicableCountry' => 'AR',
      'returnPolicyCategory' => 'https://schema.org/MerchantReturnUnspecified',
      'url' => url()->current(),
      'name' => $pageInfo->title,
    ];
  }
@endphp

@section('content')

  @push('schema')
    <script type="application/ld+json">{!! json_encode($legalJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
  @endpush

  <!--====== PAGE CONTENT PART START ======-->
  <section class="custom-page-area pt-100 pb-90">
    <div class="container">
      <div class="legal-layout">
        <div class="legal-article">
          @if ($legalUpdated)
            <div class="legal-article__meta">
              <span class="legal-article__updated">
                <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                {{ __('Última actualización:') }} {{ $legalUpdated }}
              </span>
            </div>
          @endif

          <div class="summernote-content">
            {!! $legalContent !!}
          </div>

          {{-- Navegación entre documentos legales --}}
          <nav class="legal-nav" aria-label="{{ __('Documentos legales') }}">
            <h2 class="legal-nav__title">{{ __('Documentos legales') }}</h2>
            <div class="legal-nav__grid">
              @foreach ($legalNav as $legalPage)
                @php
                  $isCurrent = $legalPage['slug'] === $currentSlug;
                  $href = $isCurrent ? url()->current() : url($legalPage['slug']);
                @endphp
                <a class="legal-nav__link {{ $isCurrent ? 'is-current' : '' }}" href="{{ $href }}"
                  @if ($isCurrent) aria-current="page" @endif>
                  <i class="fas fa-file-lines" aria-hidden="true"></i>
                  {{ $legalPage['title'] }}
                </a>
              @endforeach
            </div>
          </nav>
        </div>

        @if (count($legalSections) >= 4)
          <aside class="legal-toc" aria-label="{{ __('En esta página') }}">
            <p class="legal-toc__title">{{ __('En esta página') }}</p>
            <ul class="legal-toc__list">
              @foreach ($legalSections as $section)
                <li class="legal-toc__item">
                  <a href="#{{ $section['anchor'] }}">{{ $section['title'] }}</a>
                </li>
              @endforeach
            </ul>
          </aside>
        @endif
      </div>

      @if (!empty(showAd(3)))
        <div class="text-center mt-30">
          {!! showAd(3) !!}
        </div>
      @endif
    </div>
  </section>
  <!--====== PAGE CONTENT PART END ======-->
@endsection
