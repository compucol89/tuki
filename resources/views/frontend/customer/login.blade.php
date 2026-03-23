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
        @php
          $guestEvent = Session::get('event');
          $isFreeEvent = $guestEvent && ($guestEvent->pricing_type == 'free' || Session::get('sub_total') == 0);
        @endphp
        <a href="{{ route('check-out', ['type' => 'guest']) }}" class="auth-guest-btn auth-guest-btn--cta auth-guest-btn--green">
          @if($isFreeEvent)
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            Reservar entrada sin cuenta
          @else
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
            Comprar entrada sin cuenta
          @endif
        </a>
        <p class="auth-guest-hint">Rápido, seguro y sin necesidad de registrarte</p>
      @endif
      @if (!onlyDigitalItemsInCart() && request()->input('redirected') == 'checkout')
        <a href="{{ route('shop.checkout', ['type' => 'guest']) }}" class="auth-guest-btn auth-guest-btn--cta">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
          Comprar como invitado — sin registrarme
        </a>
        <p class="auth-guest-hint">Rápido, seguro y sin necesidad de registrarte</p>
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
