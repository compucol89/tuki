@php
  $frontCssAsset = static function (string $path): string {
    $fullPath = public_path($path);

    return asset($path) . (is_file($fullPath) ? '?v=' . filemtime($fullPath) : '');
  };

  $menuCssPath = app()->environment('production') ? 'assets/front/css/menu.min.css' : 'assets/front/css/menu.css';
  $styleCssPath = app()->environment('production') ? 'assets/front/css/style.min.css' : 'assets/front/css/style.css';
  $responsiveCssPath = app()->environment('production') ? 'assets/front/css/responsive.min.css' : 'assets/front/css/responsive.css';
@endphp
<!-- app.css -->
<link rel="stylesheet" href="{{ mix('css/app.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ mix('css/app.css') }}"></noscript>
<!-- FlatIcon Font -->
<link rel="stylesheet" href="{{ $frontCssAsset('assets/front/css/flaticon.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ $frontCssAsset('assets/front/css/flaticon.css') }}"></noscript>
<!-- Font Awesome 6 (self-hosted via Laravel Mix) -->
<link rel="stylesheet" href="{{ mix('css/fontawesome.css') }}">
<!-- Bootstrap css -->
<link rel="stylesheet" href="{{ $frontCssAsset('assets/front/css/bootstrap.4.5.3.min.css') }}">
<!-- Padding Margin -->
<link rel="stylesheet" href="{{ $frontCssAsset('assets/front/css/spacing.min.css') }}">
<!-- Menu css -->
<link rel="stylesheet" href="{{ $frontCssAsset($menuCssPath) }}">
<!-- Main css -->
<link rel="stylesheet" href="{{ $frontCssAsset($styleCssPath) }}">
<!-- Responsive css -->
<link rel="stylesheet" href="{{ $frontCssAsset($responsiveCssPath) }}">
<!-- Toastr css -->
<link rel="stylesheet" href="{{ $frontCssAsset('assets/front/css/toastr.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ $frontCssAsset('assets/front/css/toastr.css') }}"></noscript>
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

  .dropdown-menu {
    display: none;
  }

  .dropdown-menu.show {
    display: block;
  }

  .dropdown-toggle::after {
    display: inline-block;
    margin-left: .255em;
    vertical-align: .255em;
    content: "";
    border-top: .3em solid;
    border-right: .3em solid transparent;
    border-bottom: 0;
    border-left: .3em solid transparent;
  }

  .dropdown-toggle:empty::after {
    margin-left: 0;
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

  .mr-1 {
    margin-right: .25rem !important;
  }

  .main-header {
    position: relative;
    left: 0;
    top: 0;
    z-index: 999;
    width: 100%;
  }

  .main-header .container {
    max-width: 1620px;
  }

  .main-header .header-upper {
    position: relative;
    z-index: 5;
    width: 100%;
    left: 0;
    top: 0;
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
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
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
  .main-header--premium .logo-outer {
    position: relative;
    z-index: 9;
    flex: 0 0 auto;
  }
  .main-header--premium .main-menu .navbar-header {
    display: none;
  }
  .main-header--premium .main-menu .navbar-collapse {
    flex: 1 1 auto;
  }
  .main-header--premium .main-menu .navbar-collapse .mobile-drawer-body {
    display: contents;
  }
  .main-header--premium .navigation {
    display: flex;
    align-items: center;
    margin: 0;
    padding: 0;
    list-style: none;
  }
  .main-header--premium .navigation > li {
    display: flex;
    align-items: center;
    margin: 0;
    padding: 0;
    list-style: none;
  }
  .main-header--premium .navigation > li > a {
    position: relative;
    display: block;
    padding: 11px 16px;
    margin-left: 2px;
    border-radius: 12px;
    color: #334155;
    font-family: var(--tuki-font-sans);
    font-size: 15px;
    font-weight: 700;
    line-height: 1.35;
    letter-spacing: 0;
    text-decoration: none;
    white-space: nowrap;
  }
  .main-header--premium .navigation > li > a::after {
    content: '';
    position: absolute;
    left: 50%;
    bottom: 4px;
    width: calc(100% - 22px);
    height: 2px;
    border-radius: 999px;
    background: linear-gradient(90deg, rgba(249,115,22,0.9) 0%, rgba(249,115,22,0.24) 100%);
    transform: translateX(-50%) scaleX(0);
    transform-origin: center;
  }
  .main-header--premium .navigation > li > a:hover::after,
  .main-header--premium .navigation > li > a[aria-current="page"]::after {
    transform: translateX(-50%) scaleX(1);
  }
  .main-header--premium .navigation li ul {
    display: none;
  }
  .main-header--premium .menu-right--premium {
    display: flex;
    align-items: center;
    flex: 0 0 auto;
    margin-inline-start: clamp(18px, 4vw, 44px);
    gap: 10px;
  }
  .main-header--premium .menu-dropdown {
    position: relative;
  }
  .main-header--premium .menu-right .menu-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 42px;
    padding: 9px 16px;
    border-radius: 14px;
    font-family: var(--tuki-font-sans);
    font-size: 14px;
    font-weight: 700;
    line-height: 1.2;
    letter-spacing: 0;
    text-decoration: none;
    white-space: nowrap;
    box-shadow: none !important;
  }
  .main-header--premium .menu-right .menu-btn--customer {
    color: #1f2937;
    background: rgba(255,255,255,0.68);
    border: 1px solid rgba(30,37,50,0.08);
  }
  .main-header--premium .menu-right .menu-btn--organizer,
  .main-header--premium .menu-right .dropdown:last-child .menu-btn.menu-btn--organizer {
    color: #ffffff !important;
    background: linear-gradient(135deg, #f97316 0%, #ea580c 100%) !important;
    border: 1px solid rgba(234,88,12,0.92) !important;
  }
  .main-header--premium .menu-right .dropdown-menu {
    position: absolute;
    right: 0;
    top: 100%;
    z-index: 1000;
    min-width: 188px;
    margin-top: 10px;
    padding: 8px;
    border: 1px solid rgba(30,37,50,0.08);
    border-radius: 16px;
    background: rgba(255,255,255,0.94);
    box-shadow: 0 16px 40px rgba(15,23,42,0.10);
  }
  .main-header--premium .menu-right .dropdown-menu .dropdown-item {
    display: block;
    padding: 10px 12px;
    border-radius: 10px;
    color: #334155;
    font-family: var(--tuki-font-sans);
    font-size: 14px;
    font-weight: 600;
    line-height: 1.25;
    text-decoration: none;
    white-space: nowrap;
  }
  .main-header--premium .header-ingresar-btn {
    display: none;
  }
  .mobile-menu-overlay {
    display: none;
  }

  @media only screen and (max-width: 1199px) {
    .main-header--premium {
      padding-top: 0;
    }
    .main-header--premium .logo-outer {
      display: none;
    }
    .main-header--premium .header-inner {
      display: block;
      min-height: auto;
      padding: 14px 0;
    }
    body.mobile-drawer-open .main-header.main-header--premium,
    body.mobile-drawer-open .main-header--premium .header-upper {
      z-index: 11050;
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
    .main-header--premium .main-menu #main-menu.navbar-collapse {
      position: fixed;
      top: 0;
      left: 0;
      z-index: 10000;
      display: flex !important;
      flex-direction: column;
      align-items: stretch;
      width: min(320px, 86vw);
      height: 100vh;
      max-height: 100vh;
      margin: 0;
      padding: 80px 20px 24px;
      border: none;
      border-radius: 0;
      background: #1e2532;
      box-shadow: 4px 0 24px rgba(0, 0, 0, 0.3);
      overflow: hidden;
      transform: translate3d(-100%, 0, 0);
      pointer-events: none;
    }
    .main-header--premium .main-menu #main-menu.navbar-collapse.show {
      transform: translate3d(0, 0, 0);
      pointer-events: auto;
    }
    .main-header--premium .main-menu .navbar-collapse .mobile-drawer-body {
      display: flex;
      flex-direction: column;
      flex: 1 1 auto;
      min-height: 0;
      overflow-x: hidden;
      overflow-y: auto;
      -webkit-overflow-scrolling: touch;
    }
    .main-header--premium .main-menu .navigation {
      display: block;
      width: 100%;
      max-width: 100%;
      max-height: none !important;
      margin: 0;
      padding: 0;
      list-style: none;
      background: transparent !important;
      overflow-x: hidden;
      overflow-y: visible !important;
    }
    .main-header--premium .main-menu .navigation li {
      display: block;
      float: none !important;
      width: 100%;
      padding: 0;
      border-top: 1px solid rgba(255, 255, 255, 0.08);
    }
    .main-header--premium .main-menu .navigation > li:last-child {
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    .main-header--premium .main-menu .navigation li a {
      display: block;
      width: 100%;
      max-width: 100%;
      padding: 14px 16px;
      margin: 0;
      color: rgba(255, 255, 255, 0.92) !important;
      font-size: 15px;
      font-weight: 700;
      line-height: 1.35;
      white-space: normal;
      overflow-wrap: break-word;
    }
    .main-header--premium .main-menu .navigation > li > a::after {
      display: none;
    }
    .main-header--premium .menu-right--premium {
      display: flex;
      flex-direction: column;
      align-items: stretch;
      width: 100%;
      margin-inline-start: 0;
      margin-top: 20px;
      padding-top: 20px;
      border-top: 1px solid rgba(255, 255, 255, 0.12);
      gap: 10px;
    }
    .main-header--premium .menu-right--premium .menu-dropdown {
      width: 100%;
    }
    .main-header--premium .menu-right .menu-btn {
      justify-content: center;
      width: 100%;
      min-height: 48px;
    }
    .main-header--premium .menu-right .dropdown-menu {
      position: static;
      width: 100%;
      min-width: 0;
      margin-top: 8px;
    }
    .mobile-menu-overlay {
      display: block;
      position: fixed;
      top: 0;
      left: 0;
      z-index: 9999;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      opacity: 0;
      pointer-events: none;
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

    .main-header .container {
      max-width: 1620px;
    }

    .main-header--premium .main-menu .navbar-collapse {
      position: static;
      width: auto;
      height: auto !important;
      max-height: none;
      padding: 0;
      margin: 0;
      background: transparent;
      box-shadow: none;
      overflow: visible;
      flex-direction: row;
      align-items: center;
      flex-wrap: nowrap;
      justify-content: flex-end;
    }

    .main-header--premium .main-menu .navbar-collapse .navigation {
      flex: 1 1 auto;
      min-width: 0;
      max-height: none;
      overflow: visible;
    }

    .main-header--premium .main-menu .navbar-collapse .menu-right--premium {
      flex-direction: row;
      align-items: center;
      width: auto;
      margin-top: 0;
      padding-top: 0;
      border-top: none;
      gap: 10px;
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
