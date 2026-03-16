@extends('frontend.layout')
@section('pageHeading', __('Organizer Signup'))
@section('body-class', 'auth-page')

@php
  $metaKeywords = !empty($seo->meta_keyword_organizer_signup) ? $seo->meta_keyword_organizer_signup : '';
  $metaDescription = !empty($seo->meta_description_organizer_signup) ? $seo->meta_description_organizer_signup : '';
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
        <h2>Empezá a vender<br>entradas hoy.</h2>
        <p>Creá tu cuenta de organizador gratis y lanzá tu primer evento en minutos.</p>
      </div>

      <div class="auth-split__stats">
        <div class="auth-split__stat">
          <span class="auth-split__stat-num">Gratis</span>
          <span class="auth-split__stat-label">Registro</span>
        </div>
        <div class="auth-split__stat">
          <span class="auth-split__stat-num">∞</span>
          <span class="auth-split__stat-label">Eventos</span>
        </div>
        <div class="auth-split__stat">
          <span class="auth-split__stat-num">24/7</span>
          <span class="auth-split__stat-label">Soporte</span>
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

      <h1 class="auth-split__title">{{ __('Create Organizer Account') }}</h1>
      <p class="auth-split__subtitle">{{ __('Fill in your details to get started') }}</p>

      {{-- Alertas --}}
      @if (Session::has('success'))
        <div class="alert alert-success mb-3">{{ Session::get('success') }}</div>
      @endif
      @if (Session::has('alert'))
        <div class="alert alert-danger mb-3">{{ Session::get('alert') }}</div>
      @endif

      {{-- Formulario --}}
      <form id="signup-form" name="login_form" action="{{ route('organizer.create') }}" method="POST">
        @csrf

        <div class="row">
          <div class="col-sm-6">
            <div class="form-group mb-4">
              <label for="name">{{ __('Name') }}</label>
              <input type="text" name="name" id="name" value="{{ old('name') }}"
                     class="form-control @error('name') is-invalid @enderror"
                     placeholder="{{ __('Enter Your Full Name') }}" autocomplete="name">
              @error('name')
                <p class="text-danger mt-1" style="font-size:13px">{{ $message }}</p>
              @enderror
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group mb-4">
              <label for="username">{{ __('Username') }}</label>
              <input type="text" name="username" id="username" value="{{ old('username') }}"
                     class="form-control @error('username') is-invalid @enderror"
                     placeholder="{{ __('Enter Your Username') }}" autocomplete="username">
              @error('username')
                <p class="text-danger mt-1" style="font-size:13px">{{ $message }}</p>
              @enderror
            </div>
          </div>
        </div>

        <div class="form-group mb-4">
          <label for="email">{{ __('Email Address') }}</label>
          <input type="email" name="email" id="email" value="{{ old('email') }}"
                 class="form-control @error('email') is-invalid @enderror"
                 placeholder="{{ __('Enter Your Email Address') }}" autocomplete="email">
          @error('email')
            <p class="text-danger mt-1" style="font-size:13px">{{ $message }}</p>
          @enderror
        </div>

        <div class="row">
          <div class="col-sm-6">
            <div class="form-group mb-4">
              <label for="password">{{ __('Password') }}</label>
              <input type="password" name="password" id="password"
                     class="form-control @error('password') is-invalid @enderror"
                     placeholder="{{ __('Enter Password') }}" autocomplete="new-password">
              @error('password')
                <p class="text-danger mt-1" style="font-size:13px">{{ $message }}</p>
              @enderror
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group mb-4">
              <label for="re-password">{{ __('Re-enter Password') }}</label>
              <input type="password" name="password_confirmation" id="re-password"
                     class="form-control"
                     placeholder="{{ __('Re-enter Password') }}" autocomplete="new-password">
            </div>
          </div>
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
          {{ __('Signup') }}
        </button>

      </form>

      <div class="auth-split__links">
        <span>{{ __('Already have an account') }}? <a href="{{ route('organizer.login') }}">{{ __('Login Now') }}</a></span>
      </div>

    </div>
  </div>

</div>
@endsection
