<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
<link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
<link rel="stylesheet" href="{{ mix('css/app.css') }}">
<!-- FlatIcon Font -->
<link rel="stylesheet" href="{{ asset('assets/front/css/flaticon.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ asset('assets/front/css/flaticon.css') }}"></noscript>
<!-- Font Awesome 5.15.4 (CDN) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></noscript>
<!-- Bootstrap css -->
<link rel="stylesheet" href="{{ asset('assets/front/css/bootstrap.4.5.3.min.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ asset('assets/front/css/bootstrap.4.5.3.min.css') }}"></noscript>
<!-- Padding Margin -->
<link rel="stylesheet" href="{{ asset('assets/front/css/spacing.min.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ asset('assets/front/css/spacing.min.css') }}"></noscript>
<!-- Menu css -->
<link rel="stylesheet" href="{{ asset(app()->environment('production') ? 'assets/front/css/menu.min.css' : 'assets/front/css/menu.css') }}">
<!-- Main css -->
<link rel="stylesheet" href="{{ asset(app()->environment('production') ? 'assets/front/css/style.min.css' : 'assets/front/css/style.css') }}">
<!-- Responsive css -->
<link rel="stylesheet" href="{{ asset(app()->environment('production') ? 'assets/front/css/responsive.min.css' : 'assets/front/css/responsive.css') }}">
<link rel="stylesheet" href="{{ asset('assets/front/css/toastr.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ asset('assets/front/css/toastr.css') }}"></noscript>
<style>
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
