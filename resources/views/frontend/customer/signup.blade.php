@extends('frontend.layout')
@section('pageHeading', 'Crear cuenta')
@section('body-class', 'auth-page')

@php
  $metaKeywords = !empty($seo->meta_keyword_customer_signup) ? $seo->meta_keyword_customer_signup : '';
  $metaDescription = !empty($seo->meta_description_customer_signup) ? $seo->meta_description_customer_signup : '';
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
        <h2>Creá tu cuenta<br>y viví el evento.</h2>
        <p>Registrate gratis y accedé a todos tus tickets en un solo lugar.</p>
      </div>

      <div class="auth-split__stats">
        <div class="auth-split__stat">
          <span class="auth-split__stat-num">Gratis</span>
          <span class="auth-split__stat-label">Siempre</span>
        </div>
        <div class="auth-split__stat">
          <span class="auth-split__stat-num">100%</span>
          <span class="auth-split__stat-label">Seguro</span>
        </div>
        <div class="auth-split__stat">
          <span class="auth-split__stat-num">24/7</span>
          <span class="auth-split__stat-label">Disponible</span>
        </div>
      </div>

    </div>
  </div>

  {{-- Panel de formulario derecho --}}
  <div class="auth-split__form">
    <div class="auth-split__form-inner auth-split__form-inner--wide">

      {{-- Logo mobile --}}
      <a href="{{ route('index') }}" class="auth-split__logo-mobile">
        <img src="{{ asset('assets/admin/img/' . $websiteInfo->logo) }}" alt="Logo">
      </a>

      <h1 class="auth-split__title">Crear cuenta</h1>
      <p class="auth-split__subtitle">Unite gratis y comprá entradas en segundos</p>

      {{-- Botones sociales --}}
      @if ($basicInfo->facebook_login_status == 1 || $basicInfo->google_login_status == 1)
        <div class="auth-social">
          @if ($basicInfo->facebook_login_status == 1)
            <a href="{{ route('auth.facebook') }}" class="auth-social__btn auth-social__btn--fb">
              <i class="fab fa-facebook-f"></i> Facebook
            </a>
          @endif
          @if ($basicInfo->google_login_status == 1)
            <a href="{{ route('auth.google') }}" class="auth-social__btn auth-social__btn--google">
              <i class="fab fa-google"></i> Google
            </a>
          @endif
        </div>
        <div class="auth-divider">o completá tus datos</div>
      @endif

      {{-- Alertas --}}
      @if (Session::has('success'))
        <div class="alert alert-success mb-3">{{ Session::get('success') }}</div>
      @endif
      @if (Session::has('alert'))
        <div class="alert alert-danger mb-3">{{ Session::get('alert') }}</div>
      @endif

      {{-- Formulario --}}
      <form id="signup-form" action="{{ route('customer.create') }}" method="POST">
        @csrf

        <div class="row">
          <div class="col-sm-6">
            <div class="form-group mb-3">
              <label for="fname">Nombre *</label>
              <input type="text" name="fname" id="fname"
                     class="form-control @error('fname') is-invalid @enderror"
                     placeholder="Tu nombre" value="{{ old('fname') }}" autocomplete="given-name">
              @error('fname')
                <p class="text-danger mt-1" style="font-size:13px">{{ $message }}</p>
              @enderror
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group mb-3">
              <label for="lname">Apellido</label>
              <input type="text" name="lname" id="lname"
                     class="form-control @error('lname') is-invalid @enderror"
                     placeholder="Tu apellido" value="{{ old('lname') }}" autocomplete="family-name">
              @error('lname')
                <p class="text-danger mt-1" style="font-size:13px">{{ $message }}</p>
              @enderror
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group mb-3">
              <label for="username">Usuario *</label>
              <input type="text" name="username" id="username"
                     class="form-control @error('username') is-invalid @enderror"
                     placeholder="Elegí un usuario" value="{{ old('username') }}" autocomplete="username">
              @error('username')
                <p class="text-danger mt-1" style="font-size:13px">{{ $message }}</p>
              @enderror
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group mb-3">
              <label for="email">Email *</label>
              <input type="email" name="email" id="email"
                     class="form-control @error('email') is-invalid @enderror"
                     placeholder="tu@email.com" value="{{ old('email') }}" autocomplete="email">
              @error('email')
                <p class="text-danger mt-1" style="font-size:13px">{{ $message }}</p>
              @enderror
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group mb-3">
              <label for="password">Contraseña *</label>
              <input type="password" name="password" id="password"
                     class="form-control @error('password') is-invalid @enderror"
                     placeholder="Mínimo 6 caracteres" autocomplete="new-password">
              @error('password')
                <p class="text-danger mt-1" style="font-size:13px">{{ $message }}</p>
              @enderror
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group mb-3">
              <label for="re-password">Repetir contraseña *</label>
              <input type="password" name="password_confirmation" id="re-password"
                     class="form-control"
                     placeholder="Repetí la contraseña" autocomplete="new-password">
            </div>
          </div>
        </div>

        @if ($basicInfo->google_recaptcha_status == 1)
          <div class="form-group mb-3">
            {!! NoCaptcha::renderJs() !!}
            {!! NoCaptcha::display() !!}
            @error('g-recaptcha-response')
              <p class="text-danger mt-1" style="font-size:13px">{{ $message }}</p>
            @enderror
          </div>
        @endif

        <button type="submit" class="theme-btn w-100 showLoader" style="height:50px;font-size:15px;font-weight:700;border-radius:10px">
          Crear cuenta gratis
        </button>

      </form>

      <div class="auth-split__links">
        <span>¿Ya tenés cuenta? <a href="{{ route('customer.login') }}">Ingresá acá</a></span>
      </div>

    </div>
  </div>

</div>
@endsection
