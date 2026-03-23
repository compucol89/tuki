<!DOCTYPE html>
<html lang="{{ $currentLanguageInfo->code == 'es' ? 'es-AR' : ($currentLanguageInfo->code ?? 'es-AR') }}" dir="{{ $currentLanguageInfo->direction == 1 ? 'rtl' : 'ltr' }}">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8" />
  <meta http-equiv="x-ua-compatible" content="ie=edge" />
  <meta name="description" content="@yield('meta-description')">
  <meta name="keywords" content="@yield('meta-keywords')">

  <meta property="og:title" content="@yield('og-title')" />
  <meta property="og:description" content="@yield('og-description')" />
  <meta property="og:image" content="@yield('og-image')" />
  <meta property="og:url" content="@yield('og-url', url()->current())" />
  <meta property="og:type" content="@yield('og-type', 'website')" />
  <meta property="og:site_name" content="{{ $websiteInfo->website_title }}" />
  <meta property="og:locale" content="es_AR" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="@yield('og-title')" />
  <meta name="twitter:description" content="@yield('og-description')" />
  <meta name="twitter:image" content="@yield('og-image')" />
  <link rel="canonical" href="@yield('canonical', url()->current())" />


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
</head>

<body class="@yield('body-class')">
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
