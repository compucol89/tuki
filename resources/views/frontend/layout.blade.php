<!DOCTYPE html>
<html lang="{{ $currentLanguageInfo->code == 'es' ? 'es-AR' : ($currentLanguageInfo->code ?? 'es-AR') }}" dir="{{ $currentLanguageInfo->direction == 1 ? 'rtl' : 'ltr' }}">

<head>
  @php
    $metaDescription = trim($__env->yieldContent('meta-description'));
    $metaKeywords = trim($__env->yieldContent('meta-keywords'));
    $metaRobots = trim($__env->yieldContent('meta-robots')) ?: 'index,follow,max-image-preview:large';
    $ogTitle = trim($__env->yieldContent('og-title')) ?: trim($__env->yieldContent('pageHeading'));
    $ogDescription = trim($__env->yieldContent('og-description')) ?: $metaDescription;
    $ogImage = trim($__env->yieldContent('og-image'));
    $ogImageSecure = trim($__env->yieldContent('og-image-secure')) ?: $ogImage;
    $ogImageWidth = trim($__env->yieldContent('og-image-width')) ?: '1200';
    $ogImageHeight = trim($__env->yieldContent('og-image-height')) ?: '630';
    $ogImageType = trim($__env->yieldContent('og-image-type')) ?: 'image/jpeg';
    $ogImageAlt = trim($__env->yieldContent('og-image-alt')) ?: $ogTitle;
    $ogUrl = trim($__env->yieldContent('og-url')) ?: url()->current();
    $ogType = trim($__env->yieldContent('og-type')) ?: 'website';
    $canonicalUrl = trim($__env->yieldContent('canonical')) ?: url()->current();
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
  <meta property="og:image:secure_url" content="{{ $ogImageSecure }}" />
  <meta property="og:image:width" content="{{ $ogImageWidth }}" />
  <meta property="og:image:height" content="{{ $ogImageHeight }}" />
  <meta property="og:image:type" content="{{ $ogImageType }}" />
  <meta property="og:image:alt" content="{{ $ogImageAlt }}" />
  <meta property="og:url" content="{{ $ogUrl }}" />
  <meta property="og:type" content="{{ $ogType }}" />
  <meta property="og:site_name" content="{{ $websiteInfo->website_title }}" />
  <meta property="og:locale" content="es_AR" />
  <meta property="og:locale:alternate" content="es_ES" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="{{ $ogTitle }}" />
  <meta name="twitter:description" content="{{ $ogDescription }}" />
  <meta name="twitter:image" content="{{ $ogImage }}" />
  <meta name="twitter:image:alt" content="{{ $ogImageAlt }}" />
  <meta name="twitter:url" content="{{ $ogUrl }}" />
  <link rel="canonical" href="{{ $canonicalUrl }}" />


  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Title -->
  <title>@yield('pageHeading') {{ '| ' . $websiteInfo->website_title }}</title>
  <!-- Favicon Icon -->
  <link rel="shortcut icon" href="{{ asset('assets/admin/img/' . $websiteInfo->favicon) }}" type="image/x-icon">
  {{-- include styles --}}
  @includeIf('frontend.partials.styles')
  @yield('custom-style')
  @stack('styles')
  <style>
    .skip-link {
      position: absolute;
      left: 16px;
      top: -48px;
      z-index: 2000;
      padding: 10px 14px;
      border-radius: 10px;
      background: #1e2532;
      color: #fff;
      text-decoration: none;
      transition: top 0.2s ease;
    }

    .skip-link:focus {
      top: 16px;
      color: #fff;
      outline: 3px solid #fff;
      outline-offset: 3px;
      box-shadow: 0 0 0 4px rgba(30, 37, 50, 0.55);
    }
  </style>
  {{-- Prerender sólo "Sobre nosotros" (/sobre-nosotros): hover / intención, sin masificar todo el sitio --}}
  <script type="speculationrules">
  {
    "prerender": [{
      "where": {
        "and": [
          { "href_matches": "*sobre-nosotros*" },
          { "not": { "href_matches": "*logout*" } },
          { "not": { "selector_matches": "[rel~=nofollow]" } },
          { "not": { "selector_matches": "[data-no-prerender]" } }
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

    <div class="request-loader">
      <img src="{{ asset('assets/admin/img/loader.gif') }}" alt="loader">
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
    var rtl = {{ $currentLanguageInfo->direction }};
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
  <script>document.addEventListener('DOMContentLoaded',function(){document.body.classList.add('loaded');});</script>
</body>

</html>
