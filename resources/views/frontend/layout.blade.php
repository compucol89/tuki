<!DOCTYPE html>
<html lang="es-AR" dir="ltr" prefix="og: https://ogp.me/ns#">

<head>
  @php
    $siteName = trim((string) ($websiteInfo->website_title ?? config('app.name', 'Tukipass'))) ?: 'Tukipass';
    $siteDefaultDescription = 'Tukipass es una plataforma argentina para descubrir eventos y reservar entradas online de forma segura.';
    $defaultOgImagePath = 'assets/front/img/og/tukipass-og.jpg';
    $defaultOgImageBase = asset($defaultOgImagePath);
    $defaultOgImage = $defaultOgImageBase . (is_file(public_path($defaultOgImagePath)) ? '?v=' . filemtime(public_path($defaultOgImagePath)) : '');
    $metaDescription = trim($__env->yieldContent('meta-description')) ?: $siteDefaultDescription;
    $metaKeywords = trim($__env->yieldContent('meta-keywords'));
    $metaRobots = trim($__env->yieldContent('meta-robots')) ?: 'index,follow,max-image-preview:large';
    $ogTitle = trim($__env->yieldContent('og-title')) ?: trim($__env->yieldContent('pageHeading')) ?: $siteName;
    $ogDescription = trim($__env->yieldContent('og-description')) ?: $metaDescription;
    $ogImage = trim($__env->yieldContent('og-image'));
    if ($ogImage === '' || $ogImage === $defaultOgImageBase) {
      $ogImage = $defaultOgImage;
    }
    $ogImageSecure = trim($__env->yieldContent('og-image-secure')) ?: $ogImage;
    $ogImageWidth = trim($__env->yieldContent('og-image-width')) ?: '1200';
    $ogImageHeight = trim($__env->yieldContent('og-image-height')) ?: '630';
    $ogImageType = trim($__env->yieldContent('og-image-type')) ?: 'image/jpeg';
    $ogImageAlt = trim($__env->yieldContent('og-image-alt')) ?: $ogTitle;
    $ogUrl = trim($__env->yieldContent('og-url')) ?: url()->current();
    $ogType = trim($__env->yieldContent('og-type')) ?: 'website';
    $canonicalUrl = trim($__env->yieldContent('canonical')) ?: url()->current();
    $facebookAppId = trim((string) config('services.facebook.client_id'));
    $facebookDomainVerification = trim((string) config('services.facebook.domain_verification'));
  @endphp
  <!-- Required meta tags -->
  <meta charset="utf-8" />
  <meta http-equiv="x-ua-compatible" content="ie=edge" />
  <meta name="description" content="{{ $metaDescription }}">
  <meta name="keywords" content="{{ $metaKeywords }}">
  <meta name="robots" content="{{ $metaRobots }}">

  <meta property="og:title" content="{{ $ogTitle }}" />
  <meta property="og:description" content="{{ $ogDescription }}" />
  <meta property="og:image" content="{{ $ogImage }}" />
  <meta property="og:image:url" content="{{ $ogImage }}" />
  <meta property="og:image:secure_url" content="{{ $ogImageSecure }}" />
  <meta property="og:image:width" content="{{ $ogImageWidth }}" />
  <meta property="og:image:height" content="{{ $ogImageHeight }}" />
  <meta property="og:image:type" content="{{ $ogImageType }}" />
  <meta property="og:image:alt" content="{{ $ogImageAlt }}" />
  <meta property="og:url" content="{{ $ogUrl }}" />
  <meta property="og:type" content="{{ $ogType }}" />
  <meta property="og:site_name" content="{{ $siteName }}" />
  <meta property="og:locale" content="es_AR" />
  @if ($facebookAppId !== '')
  <meta property="fb:app_id" content="{{ $facebookAppId }}" />
  @endif
  @if ($facebookDomainVerification !== '')
  <meta name="facebook-domain-verification" content="{{ $facebookDomainVerification }}" />
  @endif
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="{{ $ogTitle }}" />
  <meta name="twitter:description" content="{{ $ogDescription }}" />
  <meta name="twitter:image" content="{{ $ogImage }}" />
  <meta name="twitter:image:alt" content="{{ $ogImageAlt }}" />
  <meta name="twitter:url" content="{{ $ogUrl }}" />
  {{-- Preconnect to own origin for CSS/fonts/assets --}}
  <link rel="preconnect" href="https://www.tukipass.com">
  <link rel="dns-prefetch" href="https://www.tukipass.com">
  <link rel="canonical" href="{{ $canonicalUrl }}" />
  <link rel="alternate" type="text/plain" title="Mapa para agentes IA" href="{{ url('/llms.txt') }}" />
  <link rel="alternate" type="text/plain" title="Referencia completa para agentes IA" href="{{ url('/llms-full.txt') }}" />
  <link rel="preload" as="font" href="{{ asset('fonts/vendor/@fontsource/inter/files/inter-latin-400-normal.woff2') }}?eca1e21531598d5db58f56b3ba23a8cc" type="font/woff2" crossorigin>
  <link rel="preload" as="font" href="{{ asset('fonts/vendor/@fontsource/inter/files/inter-latin-800-normal.woff2') }}?d2cf8417dfce77f8f2bea87245ce39ee" type="font/woff2" crossorigin>

  {{-- hreflang tags --}}
  @php
    $hreflangUrl = url()->current();
    $allLanguages = \App\Models\Language::all();
  @endphp
  @if ($allLanguages->count() > 1)
    @foreach ($allLanguages as $lang)
  <link rel="alternate" hreflang="{{ $lang->code }}" href="{{ $hreflangUrl }}">
    @endforeach
  @else
  <link rel="alternate" hreflang="es-AR" href="{{ $hreflangUrl }}">
  @endif
  <link rel="alternate" hreflang="x-default" href="{{ $hreflangUrl }}">


  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Title -->
  <title>@yield('pageHeading') {{ '| ' . $websiteInfo->website_title }}</title>
  @php
    $schemaOrganization = array_filter([
      '@context' => 'https://schema.org',
      '@type' => 'Organization',
      '@id' => url('/#organization'),
      'name' => $websiteInfo->website_title ?? 'Tukipass',
      'url' => url('/'),
      'logo' => !empty($websiteInfo->logo) ? asset('assets/admin/img/' . $websiteInfo->logo) : null,
      'description' => 'Tukipass es una plataforma argentina para descubrir eventos y reservar entradas online.',
      'sameAs' => collect($socialMediaInfos ?? [])
        ->pluck('url')
        ->filter(fn ($url) => is_string($url) && filter_var($url, FILTER_VALIDATE_URL))
        ->values()
        ->all(),
    ], fn ($value) => $value !== null && $value !== '' && $value !== []);

    $eventsSearchUrl = route('events', [], true) . '?search-input={search_term_string}';
    $schemaWebsite = [
      '@context' => 'https://schema.org',
      '@type' => 'WebSite',
      '@id' => url('/#website'),
      'name' => $websiteInfo->website_title ?? 'Tukipass',
      'url' => url('/'),
      'inLanguage' => 'es-AR',
      'publisher' => ['@id' => url('/#organization')],
      'potentialAction' => [
        '@type' => 'SearchAction',
        'target' => [
          '@type' => 'EntryPoint',
          'urlTemplate' => $eventsSearchUrl,
        ],
        'query-input' => 'required name=search_term_string',
      ],
    ];
  @endphp
  <script type="application/ld+json">{!! json_encode($schemaOrganization, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
  <script type="application/ld+json">{!! json_encode($schemaWebsite, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
  @stack('schema')
  @stack('head-scripts')
  <!-- Favicon Icon -->
  <link rel="shortcut icon" href="{{ asset('assets/admin/img/' . $websiteInfo->favicon) }}" type="image/x-icon">
  @hasSection('hero-preload')
    @yield('hero-preload')
  @endif
  {{-- include styles --}}
  @includeIf('frontend.partials.styles')
  @stack('critical-styles')
  @yield('custom-style')
  @stack('styles')
  <style>
    :root {
      --tuki-primary: #F97316;
      --tuki-primary-accessible: #C2410C;
      --tuki-primary-hover: #9A3412;
      --tuki-dark: #1e2532;
      --tuki-dark-rgb: 30, 37, 50;
      --tuki-surface: #ffffff;
      --tuki-surface-alt: #f8fafc;
      --tuki-muted: #6b7280;
      --tuki-muted-light: #9ca3af;
      --tuki-border: #e5e7eb;
      --tuki-border-light: #f3f4f6;
      --tuki-success: #059669;
      --tuki-success-light: #d1fae5;
      --tuki-danger: #dc2626;
      --tuki-danger-light: #fee2e2;
      --tuki-warning: #d97706;
      --tuki-warning-light: #fef3c7;
      --tuki-radius-sm: 8px;
      --tuki-radius-md: 12px;
      --tuki-radius-lg: 18px;
      --tuki-radius-xl: 24px;
      --tuki-radius-full: 9999px;
      --tuki-shadow-sm: 0 2px 8px rgba(var(--tuki-dark-rgb), 0.06);
      --tuki-shadow-md: 0 8px 24px rgba(var(--tuki-dark-rgb), 0.08);
      --tuki-shadow-lg: 0 18px 40px rgba(var(--tuki-dark-rgb), 0.12);
      --tuki-shadow-focus: 0 0 0 4px rgba(255, 255, 255, 0.9);
      --tuki-space-1: 4px;
      --tuki-space-2: 8px;
      --tuki-space-3: 12px;
      --tuki-space-4: 16px;
      --tuki-space-5: 20px;
      --tuki-space-6: 24px;
      --tuki-space-8: 32px;
      --tuki-space-10: 40px;
      --tuki-space-12: 48px;
      --tuki-font-body: inherit;
      --tuki-text-xs: 12px;
      --tuki-text-sm: 14px;
      --tuki-text-base: 16px;
      --tuki-text-lg: 18px;
      --tuki-text-xl: 20px;
      --tuki-text-2xl: 24px;
      --tuki-text-3xl: 28px;
      --tuki-text-4xl: 36px;
      --tuki-icon-sm: 14px;
      --tuki-icon-md: 16px;
      --tuki-icon-lg: 20px;
      --tuki-transition-fast: 150ms ease;
      --tuki-transition-base: 200ms ease;
      --tuki-transition-slow: 300ms ease;
      --tuki-z-dropdown: 100;
      --tuki-z-sticky: 500;
      --tuki-z-overlay: 1000;
      --tuki-z-modal: 1050;
      --tuki-z-skip: 2000;
    }

    .skip-link {
      position: absolute;
      left: var(--tuki-space-4);
      top: -48px;
      z-index: var(--tuki-z-skip);
      padding: 10px 14px;
      border-radius: var(--tuki-radius-sm);
      background: var(--tuki-dark);
      color: var(--tuki-surface);
      text-decoration: none;
      transition: top var(--tuki-transition-base);
    }

    .skip-link:focus {
      top: var(--tuki-space-4);
      color: var(--tuki-surface);
      outline: 3px solid var(--tuki-surface);
      outline-offset: 3px;
      box-shadow: 0 0 0 4px rgba(var(--tuki-dark-rgb), 0.55);
    }
  </style>
  {{-- Prerender: páginas del sitio con intención de navegación (hover/mousedown) --}}
  <script type="speculationrules">
  {
    "prerender": [{
      "where": {
        "and": [
          { "href_matches": "/*" },
          { "not": { "href_matches": ["*logout*", "*checkout*", "*admin*", "*organizer*", "*usuario*"] } },
          { "not": { "selector_matches": "[rel~=nofollow], [data-no-prerender]" } }
        ]
      },
      "eagerness": "moderate"
    }]
  }
  </script>
</head>

<body class="@yield('body-class')">
  <a href="#main-content" class="skip-link">{{ __('Saltar al contenido principal') }}</a>
  <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
    <symbol id="icon-ticket" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M2 9a3 3 0 010-6h20a3 3 0 010 6"/><path d="M2 9v11a2 2 0 002 2h16a2 2 0 002-2V9"/><path d="M9 21V9"/></symbol>
    <symbol id="icon-arrow-right" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></symbol>
    <symbol id="icon-map-pin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></symbol>
    <symbol id="icon-user" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></symbol>
    <symbol id="icon-calendar" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></symbol>
    <symbol id="icon-clock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></symbol>
  </svg>
  <div class="page-wrapper">

    <div class="request-loader" aria-label="Cargando..." role="status">
      <div class="request-loader__spinner"></div>
    </div>



    <!-- Header Part Start -->
    @includeIf('frontend.partials.header.header-nav')
    <!-- Header Part End -->

    @yield('hero-section')

    @yield('content')

    @includeIf('frontend.partials.popups')


    @includeIf('frontend.partials.footer.footer')

  </div>
  <!--End pagewrapper-->

  {{-- modals --}}
  @yield('modals')
  {{-- include scripts --}}
  <script>
    "use strict";
    var rtl = 0;
  </script>
  @includeIf('frontend.partials.scripts')

  {{-- additional script --}}
  @yield('script')
  @yield('custom-script')

  {{-- Cookie alert dialog start --}}
  @if (!empty($cookieAlertInfo) && $cookieAlertInfo->cookie_alert_status == 1)
    <div class="cookie">
      @include('cookie-consent::index')
    </div>
  @endif
  {{-- Cookie alert dialog end --}}

  @stack('scripts')
</body>

</html>
