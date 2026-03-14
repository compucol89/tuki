@extends('frontend.layout')
@section('pageHeading', 'Iniciar sesión')
@section('body-class', 'auth-page')

@php
  $metaKeywords = !empty($seo->meta_keyword_customer_login) ? $seo->meta_keyword_customer_login : '';
  $metaDescription = !empty($seo->meta_description_customer_login) ? $seo->meta_description_customer_login : '';
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

      <a href="{{ route('index') }}" class="auth-split__logo">
        <img src="{{ asset('assets/admin/img/' . $websiteInfo->logo) }}" alt="Logo">
      </a>

      <div class="auth-split__tagline">
        <h2>Tu próximo evento,<br>a un clic.</h2>
        <p>Comprá entradas, gestioná tus reservas y no te pierdas nada.</p>
      </div>

      <div class="auth-split__stats">
        <div class="auth-split__stat">
          <span class="auth-split__stat-num">100%</span>
          <span class="auth-split__stat-label">Seguro</span>
        </div>
        <div class="auth-split__stat">
          <span class="auth-split__stat-num">24/7</span>
          <span class="auth-split__stat-label">Disponible</span>
        </div>
        <div class="auth-split__stat">
          <span class="auth-split__stat-num">Gratis</span>
          <span class="auth-split__stat-label">Registrarse</span>
        </div>
      </div>

    </div>
  </div>

  {{-- Panel de formulario derecho --}}
  <div class="auth-split__form">
    <div class="auth-split__form-inner">

      {{-- Logo mobile --}}
      <a href="{{ route('index') }}" class="auth-split__logo-mobile">
        <img src="{{ asset('assets/admin/img/' . $websiteInfo->logo) }}" alt="Logo">
      </a>

      <h1 class="auth-split__title">Bienvenido de vuelta</h1>
      <p class="auth-split__subtitle">Ingresá a tu cuenta para continuar</p>

      {{-- Checkout como invitado --}}
      @php
        $event_setting = App\Models\BasicSettings\Basic::select('event_guest_checkout_status')->first();
      @endphp
      @if ($event_setting->event_guest_checkout_status == 1 && request()->input('redirectPath') == 'event_checkout')
        <a href="{{ route('check-out', ['type' => 'guest']) }}" class="auth-guest-btn">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          Continuar como invitado
        </a>
      @endif
      @if (!onlyDigitalItemsInCart() && request()->input('redirected') == 'checkout')
        <a href="{{ route('shop.checkout', ['type' => 'guest']) }}" class="auth-guest-btn">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          Continuar como invitado
        </a>
      @endif

      {{-- Alertas --}}
      @if (Session::has('success'))
        <div class="alert alert-success mb-3">{{ Session::get('success') }}</div>
      @endif
      @if (Session::has('alert'))
        <div class="alert alert-danger mb-3">{{ Session::get('alert') }}</div>
      @endif

      {{-- Botones sociales --}}
      @if ($basicInfo->facebook_login_status == 1 || $basicInfo->google_login_status == 1)
        <div class="auth-social">
          @if ($basicInfo->facebook_login_status == 1)
            <a href="{{ route('auth.facebook', ['redirectPath' => request()->input('redirectPath')]) }}"
               class="auth-social__btn auth-social__btn--fb">
              <i class="fab fa-facebook-f"></i> Facebook
            </a>
          @endif
          @if ($basicInfo->google_login_status == 1)
            <a href="{{ route('auth.google', ['redirectPath' => request()->input('redirectPath')]) }}"
               class="auth-social__btn auth-social__btn--google">
              <i class="fab fa-google"></i> Google
            </a>
          @endif
        </div>
        <div class="auth-divider">o ingresá con tu cuenta</div>
      @endif

      {{-- Formulario --}}
      <form id="login-form" action="{{ route('customer.authentication') }}" method="POST">
        @csrf

        <div class="form-group mb-4">
          <label for="username">Usuario</label>
          <input type="text" name="username" id="username"
                 class="form-control @error('username') is-invalid @enderror"
                 placeholder="Tu nombre de usuario" value="{{ old('username') }}" autocomplete="username">
          @error('username')
            <p class="text-danger mt-1" style="font-size:13px">{{ $message }}</p>
          @enderror
        </div>

        <div class="form-group mb-4">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <label for="password" class="mb-0">Contraseña</label>
            <a href="{{ route('customer.forget.password') }}" class="auth-forgot-link">¿Olvidaste tu contraseña?</a>
          </div>
          <input type="password" name="password" id="password"
                 class="form-control @error('password') is-invalid @enderror"
                 placeholder="Tu contraseña" autocomplete="current-password">
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

        <button type="submit" class="theme-btn w-100" data-loading-text="Por favor esperá...">
          Ingresar
        </button>

      </form>

      <div class="auth-split__links">
        <span>¿No tenés cuenta? <a href="{{ route('customer.signup') }}">Registrate gratis</a></span>
      </div>

    </div>
  </div>

</div>
@endsection
