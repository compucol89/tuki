@extends('frontend.layout')
@section('pageHeading', __('Organizer Login'))
@section('body-class', 'auth-page')

@php
  $metaKeywords = !empty($seo->meta_keyword_organizer_login) ? $seo->meta_keyword_organizer_login : '';
  $metaDescription = !empty($seo->meta_description_organizer_login) ? $seo->meta_description_organizer_login : '';
@endphp
@section('meta-keywords', "{{ $metaKeywords }}")
@section('meta-description', "$metaDescription")

@section('content')
<div class="auth-split">

  {{-- Panel visual izquierdo --}}
  <div class="auth-split__visual"
       style="background-image: url('{{ asset('assets/admin/img/' . $basicInfo->breadcrumb) }}')">
    <div class="auth-split__visual-overlay"></div>
    <div class="auth-split__visual-content">

      <div class="auth-split__tagline">
        <h2>Creá eventos<br>que dejan huella.</h2>
        <p>Gestioná tus eventos, controlá ventas y conectá con tu audiencia desde un solo lugar.</p>
      </div>

      <div class="auth-split__stats">
        <div class="auth-split__stat">
          <span class="auth-split__stat-num">100%</span>
          <span class="auth-split__stat-label">Online</span>
        </div>
        <div class="auth-split__stat">
          <span class="auth-split__stat-num">24/7</span>
          <span class="auth-split__stat-label">Panel activo</span>
        </div>
        <div class="auth-split__stat">
          <span class="auth-split__stat-num">0%</span>
          <span class="auth-split__stat-label">Comisión inicial</span>
        </div>
      </div>

    </div>
  </div>

  {{-- Panel de formulario derecho --}}
  <div class="auth-split__form">
    <div class="auth-split__form-inner">

      {{-- Logo --}}
      <a href="{{ route('index') }}" class="auth-split__logo-mobile">
        <img src="{{ asset('assets/admin/img/' . $websiteInfo->logo) }}" alt="Logo">
      </a>

      <h1 class="auth-split__title">{{ __('Organizer Login') }}</h1>
      <p class="auth-split__subtitle">{{ __('Access your organizer dashboard') }}</p>

      {{-- Alertas --}}
      @if (Session::has('success'))
        <div class="alert alert-success mb-3">{{ Session::get('success') }}</div>
      @endif
      @if (Session::has('alert'))
        <div class="alert alert-danger mb-3">{{ Session::get('alert') }}</div>
      @endif

      {{-- Formulario --}}
      <form id="login-form" name="login_form" action="{{ route('organizer.authentication') }}" method="POST">
        @csrf

        <div class="form-group mb-4">
          <label for="username">{{ __('Username') }}</label>
          <input type="text" name="username" id="username"
                 class="form-control @error('username') is-invalid @enderror"
                 placeholder="{{ __('Enter Username') }}" value="{{ old('username') }}" autocomplete="username">
          @error('username')
            <p class="text-danger mt-1" style="font-size:13px">{{ $message }}</p>
          @enderror
        </div>

        <div class="form-group mb-4">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <label for="password" class="mb-0">{{ __('Password') }}</label>
            <a href="{{ route('organizer.forget.password') }}" class="auth-forgot-link">{{ __('Lost your password') }}?</a>
          </div>
          <input type="password" name="password" id="password"
                 class="form-control @error('password') is-invalid @enderror"
                 placeholder="{{ __('Enter Password') }}" autocomplete="current-password">
          @error('password')
            <p class="text-danger mt-1" style="font-size:13px">{{ $message }}</p>
          @enderror
        </div>

        @if ($basicInfo->google_recaptcha_status == 1)
          <div class="form-group mb-4">
            {!! NoCaptcha::renderJs() !!}
            {!! NoCaptcha::display() !!}
            @error('g-recaptcha-response')
              <p class="text-danger mt-1" style="font-size:13px">{{ $message }}</p>
            @enderror
          </div>
        @endif

        <button type="submit" class="theme-btn w-100" data-loading-text="{{ __('Please wait') }}...">
          {{ __('Login') }}
        </button>

      </form>

      <div class="auth-split__links">
        <span>{{ __('Don`t have an account') }}? <a href="{{ route('organizer.signup') }}">{{ __('Signup Now') }}</a></span>
      </div>

    </div>
  </div>

</div>
@endsection
