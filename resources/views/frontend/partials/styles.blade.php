@php
  $frontCssAsset = static function (string $path): string {
    static $hashes = [];
    $fullPath = public_path($path);
    if (!is_file($fullPath)) {
      return asset($path);
    }
    // Hash de contenido (no filemtime): sobrevive a deploys que preservan mtimes
    $hash = $hashes[$path] ??= substr(md5_file($fullPath), 0, 12);

    return asset($path) . '?v=' . $hash;
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
  /* Única regla dependiente del servidor (tokens DB en layout :root).
     El resto de estilos globales vive en style.css / menu.css. */
  .overlay:before {
    position: absolute;
    content: '';
    height: 100%;
    width: 100%;
    left: 0;
    top: 0;
    z-index: -1;
    opacity: var(--breadcrumb-overlay-opacity, {{ $basicInfo->breadcrumb_overlay_opacity }});
    background: var(--breadcrumb-overlay-color, #{{ $basicInfo->breadcrumb_overlay_color }});
  }
</style>
