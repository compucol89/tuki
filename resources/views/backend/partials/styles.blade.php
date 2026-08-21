<link rel="stylesheet" href="{{ mix('css/app.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ mix('css/app.css') }}"></noscript>

{{-- Preload de las fuentes críticas de iconos: descarga en paralelo con el HTML,
  listas antes del primer paint (elimina el retraso del FOUT). Solid = iconos
  del menú/navegación; Brands = Telegram/redes del sidebar. --}}
<link rel="preload" href="{{ asset('webfonts/fa-solid-900.woff2') }}" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="{{ asset('webfonts/fa-brands-400.woff2') }}" as="font" type="font/woff2" crossorigin>

{{-- Font Awesome 6 Free (self-hosted via Laravel Mix).
  Carga SINCRÓNICA: los iconos deben estar listos antes del primer paint
  (evita el flash/FOUT de iconos en cada navegación). --}}
<link rel="stylesheet" href="{{ mix('css/fontawesome.css') }}">

{{-- fontawesome icon picker css --}}
<link rel="stylesheet" href="{{ asset('assets/admin/css/fontawesome-iconpicker.min.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ asset('assets/admin/css/fontawesome-iconpicker.min.css') }}"></noscript>

{{-- bootstrap css --}}
<link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap.min.css') }}">

{{-- bootstrap tags-input css --}}
<link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap-tagsinput.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap-tagsinput.css') }}"></noscript>

{{-- jQuery-ui css --}}
<link rel="stylesheet" href="{{ asset('assets/admin/css/jquery-ui.min.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ asset('assets/admin/css/jquery-ui.min.css') }}"></noscript>

{{-- jQuery-timepicker css --}}
<link rel="stylesheet" href="{{ asset('assets/admin/css/jquery.timepicker.min.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ asset('assets/admin/css/jquery.timepicker.min.css') }}"></noscript>

{{-- bootstrap-datepicker css --}}
<link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap-datepicker.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap-datepicker.css') }}"></noscript>

{{-- select2 css --}}
<link rel="stylesheet" href="{{asset('assets/admin/css/select2.min.css')}}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{asset('assets/admin/css/select2.min.css')}}"></noscript>

{{-- dropzone css --}}
<link rel="stylesheet" href="{{ asset('assets/admin/css/dropzone.min.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ asset('assets/admin/css/dropzone.min.css') }}"></noscript>

{{-- monokai css --}}
<link rel="stylesheet" href="{{ asset('assets/admin/css/monokai-sublime.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ asset('assets/admin/css/monokai-sublime.css') }}"></noscript>

{{-- atlantis css --}}
<link rel="stylesheet" href="{{ asset('assets/admin/css/atlantis.css') }}">

{{-- admin-main css --}}
<link rel="stylesheet" href="{{ asset('assets/admin/css/admin-main.css') }}">

@php
  $adminSkinPath = 'assets/admin/css/admin-skin.css';
  $adminSkinFullPath = public_path($adminSkinPath);
@endphp
{{-- admin skin: Modern SaaS UI override --}}
<link rel="stylesheet" href="{{ asset($adminSkinPath) }}{{ is_file($adminSkinFullPath) ? '?v=' . substr(md5_file($adminSkinFullPath), 0, 12) : '' }}">
<?php /* v11 */ ?>

{{-- theme-dark: capa dark de los paneles (html[data-theme="dark"]) --}}
<link rel="stylesheet" href="{{ frontAsset('assets/admin/css/theme-dark.css') }}">

{{-- legacy flaticon / simple-line-icons → Font Awesome --}}
<link rel="stylesheet" href="{{ asset('assets/admin/css/admin-icons-compat.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ asset('assets/admin/css/admin-icons-compat.css') }}"></noscript>
