<!-- app.css -->
<link rel="stylesheet" href="{{ mix('css/app.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ mix('css/app.css') }}"></noscript>
<!-- FlatIcon Font -->
<link rel="stylesheet" href="{{ asset('assets/front/css/flaticon.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ asset('assets/front/css/flaticon.css') }}"></noscript>
<!-- Font Awesome 6 (self-hosted via Laravel Mix) -->
@if (request()->routeIs('index'))
<script>
  (function() {
    var loaded = false;
    var href = @json(mix('css/fontawesome.css'));

    function loadFontAwesome() {
      if (loaded) return;
      loaded = true;
      var link = document.createElement('link');
      link.rel = 'stylesheet';
      link.href = href;
      document.head.appendChild(link);
    }

    ['pointerdown', 'touchstart', 'keydown'].forEach(function(eventName) {
      window.addEventListener(eventName, loadFontAwesome, { passive: true, once: true });
    });
    window.addEventListener('load', function() {
      setTimeout(loadFontAwesome, 8000);
    }, { once: true });
  })();
</script>
<noscript><link rel="stylesheet" href="{{ mix('css/fontawesome.css') }}"></noscript>
@else
<link rel="stylesheet" href="{{ mix('css/fontawesome.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ mix('css/fontawesome.css') }}"></noscript>
@endif
<!-- Bootstrap css -->
<link rel="stylesheet" href="{{ asset('assets/front/css/bootstrap.4.5.3.min.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ asset('assets/front/css/bootstrap.4.5.3.min.css') }}"></noscript>
<!-- Padding Margin -->
<link rel="stylesheet" href="{{ asset('assets/front/css/spacing.min.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ asset('assets/front/css/spacing.min.css') }}"></noscript>
<!-- Menu css -->
<link rel="stylesheet" href="{{ asset(app()->environment('production') ? 'assets/front/css/menu.min.css' : 'assets/front/css/menu.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ asset(app()->environment('production') ? 'assets/front/css/menu.min.css' : 'assets/front/css/menu.css') }}"></noscript>
<!-- Main css -->
<link rel="stylesheet" href="{{ asset(app()->environment('production') ? 'assets/front/css/style.min.css' : 'assets/front/css/style.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ asset(app()->environment('production') ? 'assets/front/css/style.min.css' : 'assets/front/css/style.css') }}"></noscript>
<!-- Responsive css -->
<link rel="stylesheet" href="{{ asset(app()->environment('production') ? 'assets/front/css/responsive.min.css' : 'assets/front/css/responsive.css') }}" media="print" onload="this.onload=null; this.media='all'">
<noscript><link rel="stylesheet" href="{{ asset(app()->environment('production') ? 'assets/front/css/responsive.min.css' : 'assets/front/css/responsive.css') }}"></noscript>
<!-- Toastr css -->
<link rel="stylesheet" href="{{ asset('assets/front/css/toastr.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ asset('assets/front/css/toastr.css') }}"></noscript>
<style>
  @font-face {
    font-display: swap;
    font-family: Inter;
    font-style: normal;
    font-weight: 400;
    src: url('/fonts/vendor/@fontsource/inter/files/inter-latin-400-normal.woff2?eca1e21531598d5db58f56b3ba23a8cc') format('woff2');
    unicode-range: U+00??, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
  }
  @font-face {
    font-display: swap;
    font-family: Inter;
    font-style: normal;
    font-weight: 800;
    src: url('/fonts/vendor/@fontsource/inter/files/inter-latin-800-normal.woff2?d2cf8417dfce77f8f2bea87245ce39ee') format('woff2');
    unicode-range: U+00??, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
  }

  :root {
    scroll-behavior: auto;
    --base-color: #454545;
    --heading-color: #1e2532;
    --primary-color: #{{ $basicInfo->primary_color }};
    --primary-text-color: #C2410C;
    --secondary-color: #ea580c;
    --light-color: #F7F7F7;
    --tuki-font-sans: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    --base-font: var(--tuki-font-sans);
    --heading-font: var(--tuki-font-sans);
  }

  *,
  *::before,
  *::after {
    box-sizing: border-box;
  }

  html {
    -webkit-text-size-adjust: 100%;
  }

  body {
    margin: 0;
    font-family: var(--base-font);
    color: var(--base-color);
    background: #fff;
  }

  .skip-link {
    position: absolute;
    left: 16px;
    top: -48px;
    z-index: 2000;
    padding: 10px 14px;
    border-radius: 8px;
    background: #1e2532;
    color: #fff;
    text-decoration: none;
  }

  .skip-link:focus {
    top: 16px;
    color: #fff;
    outline: 3px solid #fff;
    outline-offset: 3px;
    box-shadow: 0 0 0 4px rgba(30, 37, 50, 0.55);
  }

  img,
  svg {
    vertical-align: middle;
  }

  img {
    max-width: 100%;
    height: auto;
    border-style: none;
  }

  a {
    color: inherit;
    text-decoration: none;
    background-color: transparent;
  }

  button,
  input,
  select {
    font-family: inherit;
    font-size: inherit;
    line-height: inherit;
  }

  .container {
    width: 100%;
    padding-right: 15px;
    padding-left: 15px;
    margin-right: auto;
    margin-left: auto;
  }

  .clearfix::after {
    display: block;
    clear: both;
    content: "";
  }

  .d-none {
    display: none !important;
  }

  .collapse:not(.show) {
    display: none;
  }

  .text-center {
    text-align: center !important;
  }

  .justify-content-center {
    justify-content: center !important;
  }

  .ml-lg-auto {
    margin-left: auto !important;
  }

  .main-header--premium {
    padding-top: 10px;
    background: transparent;
  }
  .main-header--premium .header-upper {
    padding-top: 0 !important;
    padding-bottom: 0 !important;
    background: transparent;
  }
  .main-header--premium .header-inner {
    min-height: 74px;
    padding: 0;
    background: transparent;
    border: none;
    box-shadow: none;
  }
  .main-header--premium .main-menu,
  .main-header--premium .nav-outer {
    display: flex;
    align-items: center;
    width: 100%;
  }
  .main-header--premium .logo img,
  .main-header--premium .logo-mobile img {
    width: auto;
    max-width: 190px;
    height: 38px;
    object-fit: contain;
  }

  @media only screen and (max-width: 1199px) {
    .main-header--premium {
      padding-top: 0;
    }
    .main-header--premium .header-inner {
      min-height: auto;
      padding: 14px 0;
    }
    .main-header--premium .main-menu {
      flex-wrap: nowrap;
      justify-content: flex-start;
      gap: 10px;
      position: relative;
    }
    .main-header--premium .main-menu .navbar-header {
      order: 1;
      display: flex;
      align-items: center;
      justify-content: flex-start;
      flex: 1 1 auto;
      min-width: 0;
      gap: 10px;
      position: relative;
      z-index: 10001;
    }
    .main-header--premium .main-menu .navbar-header .logo-mobile {
      flex: 0 0 auto;
      min-width: auto;
      margin-inline-start: 0;
    }
    .main-header--premium .main-menu .navbar-header .navbar-toggle {
      display: inline-flex;
      flex: 0 0 auto;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 3.5px;
      min-width: 44px;
      min-height: 44px;
      width: auto;
      height: auto;
      padding: 10px;
      margin: 0;
      color: #1e2532;
      background: transparent;
      border: none;
      cursor: pointer;
      appearance: none;
    }
    .main-header--premium .main-menu .navbar-header .navbar-toggle .icon-bar {
      display: block !important;
      width: 23px;
      height: 3.5px;
      margin: 0 !important;
      border-radius: 999px;
      background: currentColor;
      opacity: 1;
      visibility: visible;
      flex-shrink: 0;
    }
    .main-header--premium .header-ingresar-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      min-height: 40px;
      padding: 9px 14px;
      margin-left: auto;
      border-radius: 10px;
      color: #1e2532;
      background: #F97316;
      border: none;
      font-family: var(--tuki-font-sans, var(--base-font));
      font-size: 13px;
      font-weight: 700;
      line-height: 1.2;
      text-decoration: none;
    }
  }

  @media (min-width: 576px) {
    .container {
      max-width: 540px;
    }
  }

  @media (min-width: 768px) {
    .container {
      max-width: 720px;
    }
  }

  @media (min-width: 992px) {
    .container {
      max-width: 960px;
    }
  }

  @media (min-width: 1200px) {
    .container {
      max-width: 1140px;
    }

    .navbar-expand-xl .navbar-collapse {
      display: flex !important;
      flex-basis: auto;
    }
  }

  .overlay:before {
    position: absolute;
    content: '';
    height: 100%;
    width: 100%;
    left: 0;
    top: 0;
    z-index: -1;
    opacity: {{ $basicInfo->breadcrumb_overlay_opacity }};
    background: #{{ $basicInfo->breadcrumb_overlay_color }};
  }
</style>
