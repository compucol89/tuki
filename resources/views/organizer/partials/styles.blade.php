<link rel="stylesheet" href="{{ mix('css/app.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ mix('css/app.css') }}"></noscript>

{{-- Preload de la fuente crítica de iconos del menú/navegación.
  Brands se carga solo cuando una vista usa un icono de marca. --}}
<link rel="preload" href="{{ asset('webfonts/fa-solid-900.woff2') }}" as="font" type="font/woff2" crossorigin>

{{-- Font Awesome 6 Free (self-hosted via Laravel Mix).
  Carga SINCRÓNICA: los iconos deben estar listos antes del primer paint
  (evita el flash/FOUT de iconos en cada navegación). --}}
<link rel="stylesheet" href="{{ mix('css/fontawesome.css') }}">

{{-- bootstrap css --}}
<link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap.min.css') }}">

{{-- bootstrap tags-input css --}}
<link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap-tagsinput.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap-tagsinput.css') }}"></noscript>

{{-- jQuery-timepicker css --}}
<link rel="stylesheet" href="{{ asset('assets/admin/css/jquery.timepicker.min.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ asset('assets/admin/css/jquery.timepicker.min.css') }}"></noscript>

{{-- bootstrap-datepicker css --}}
<link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap-datepicker.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap-datepicker.css') }}"></noscript>

{{-- select2 css --}}
<link rel="stylesheet" href="{{asset('assets/admin/css/select2.min.css')}}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{asset('assets/admin/css/select2.min.css')}}"></noscript>

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
<?php /* organizer-v1 */ ?>

@php
  $organizerCitrusPath = 'assets/admin/css/organizer-citrus.css';
  $organizerCitrusFullPath = public_path($organizerCitrusPath);
@endphp
{{-- organizer citrus: primitivas visuales compartidas del panel Organizer --}}
<link rel="stylesheet" href="{{ asset($organizerCitrusPath) }}{{ is_file($organizerCitrusFullPath) ? '?v=' . substr(md5_file($organizerCitrusFullPath), 0, 12) : '' }}">

{{-- theme-dark: capa dark de los paneles (html[data-theme="dark"]) --}}
<link rel="stylesheet" href="{{ frontAsset('assets/admin/css/theme-dark.css') }}">

{{-- legacy flaticon / simple-line-icons → Font Awesome --}}
<link rel="stylesheet" href="{{ asset('assets/admin/css/admin-icons-compat.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ asset('assets/admin/css/admin-icons-compat.css') }}"></noscript>
