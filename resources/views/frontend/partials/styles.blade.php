<!-- app.css -->
<link rel="stylesheet" href="{{ mix('css/app.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ mix('css/app.css') }}"></noscript>
<!-- FlatIcon Font -->
<link rel="stylesheet" href="{{ asset('assets/front/css/flaticon.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ asset('assets/front/css/flaticon.css') }}"></noscript>
<!-- Font Awesome 6 (self-hosted via Laravel Mix) -->
<link rel="stylesheet" href="{{ mix('css/fontawesome.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ mix('css/fontawesome.css') }}"></noscript>
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
<link rel="stylesheet" href="{{ asset(app()->environment('production') ? 'assets/front/css/responsive.min.css' : 'assets/front/css/responsive.css') }}" media="print" onload="this.media='all'">
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
    font-weight: 500;
    src: url('/fonts/vendor/@fontsource/inter/files/inter-latin-500-normal.woff2?b7c27c60f848f2083f45be25012ce41d') format('woff2');
    unicode-range: U+00??, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
  }
  @font-face {
    font-display: swap;
    font-family: Inter;
    font-style: normal;
    font-weight: 600;
    src: url('/fonts/vendor/@fontsource/inter/files/inter-latin-600-normal.woff2?69a8d1d484967aba2389ef57577b76be') format('woff2');
    unicode-range: U+00??, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
  }
  @font-face {
    font-display: swap;
    font-family: Inter;
    font-style: normal;
    font-weight: 700;
    src: url('/fonts/vendor/@fontsource/inter/files/inter-latin-700-normal.woff2?1104236696a5d2d1f236f40aa0c491d1') format('woff2');
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
