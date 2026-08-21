@extends('frontend.layout')

@push('styles')
  <link rel="stylesheet" href="{{ frontAsset('assets/front/css/organizer.css') }}">
@endpush

@section('meta-robots', 'noindex,follow')

@php
  $organizerForgetPasswordTitle = !empty($pageHeading)
    ? ($pageHeading->organizer_forget_password_page_title ?? 'Recuperar contraseña')
    : 'Recuperar contraseña';
  $metaKeywords = !empty($seo->meta_keyword_organizer_forget_password) ? $seo->meta_keyword_organizer_forget_password : '';
  $metaDescription = !empty($seo->meta_description_organizer_forget_password) ? $seo->meta_description_organizer_forget_password : '';

  if ($metaKeywords === '' || str_contains(strtolower($metaKeywords), 'organizer')) {
    $metaKeywords = 'recuperar contraseña organizador, productor Tukipass, acceso organizador';
  }

  if ($metaDescription === '' || str_contains(strtolower($metaDescription), 'organizer')) {
    $metaDescription = 'Recuperá la contraseña de tu cuenta de productor en Tukipass.';
  }
@endphp
@section('pageHeading', $organizerForgetPasswordTitle)
@section('meta-keywords', $metaKeywords)
@section('meta-description', $metaDescription)
@section('hero-section')
  <!-- Page Banner Start -->
  <section class="page-banner overlay pt-120 pb-125 rpt-90 rpb-95 lazy"
    data-bg="{{ asset('assets/admin/img/' . $basicInfo->breadcrumb) }}">
    <div class="container">
      <div class="banner-inner">
        <h1 class="page-title">{{ $organizerForgetPasswordTitle }}</h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">{{ __('Home') }}</a></li>
            <li class="breadcrumb-item active">{{ $organizerForgetPasswordTitle }}</li>
          </ol>
        </nav>
      </div>
    </div>
  </section>
  <!-- Page Banner End -->
@endsection
@section('content')
  <!-- LogIn Area Start -->
  <div class="login-area pt-115 rpt-95 pb-120 rpb-100">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8">

          @if (Session::has('error'))
            <div class="alert alert-danger">{{ Session::get('error') }}</div>
          @endif
          @if (Session::has('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
          @endif
          <form id="login-form" name="login_form" class="login-form" action="{{ route('organizer.forget.mail') }}"
            method="POST">
            @csrf
            <div class="form-group">
              <label for="email">{{ __('Email Address') }} *</label>
              <input type="email" name="email" value="{{ old('email') }}" id="email" class="form-control"
                placeholder="{{ __('Ingresá tu correo') }}" required>
              @error('email')
                <p class="text-danger">{{ $message }}</p>
              @enderror
            </div>
            <div class="form-group mb-0">
              <button class="theme-btn br-30" type="submit">Enviar enlace de recuperación</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- LogIn Area End -->
@endsection
