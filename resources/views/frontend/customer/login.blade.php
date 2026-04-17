@extends('frontend.layout')
@section('pageHeading', 'Iniciar sesión')
@section('body-class', 'auth-page')

@php
  $metaKeywords = !empty($seo->meta_keyword_customer_login) ? $seo->meta_keyword_customer_login : '';
  $metaDescription = !empty($seo->meta_description_customer_login) ? $seo->meta_description_customer_login : '';

  $redirectPath = request()->input('redirectPath');
  $isEventCheckout = $redirectPath === 'event_checkout';
  $guestEvent = Session::get('event');
  $checkoutSubtotal = Session::get('sub_total');
  $guestCheckoutEnabled = App\Models\BasicSettings\Basic::query()->value('event_guest_checkout_status') == 1;

  $visualImage = asset('assets/admin/img/' . $basicInfo->breadcrumb);
  $visualEyebrow = __('Acceso seguro');
  $visualTitle = __('Tu próximo evento, a un clic.');
  $visualSubtitle = __('Comprá entradas, gestioná tus reservas y no te pierdas nada.');
  $visualDate = null;
  $visualLocation = null;
  $visualPrice = null;
  $formEyebrow = __('Acceso a tu cuenta');
  $formTitle = __('Bienvenido de vuelta');
  $formSubtitle = __('Ingresá a tu cuenta para continuar.');

  if ($isEventCheckout && $guestEvent) {
    if (!empty($guestEvent->thumbnail)) {
      $visualImage = asset('assets/admin/img/event/thumbnail/' . $guestEvent->thumbnail);
    }

    $visualEyebrow = __('Estás comprando este evento');
    $visualTitle = $guestEvent->title ?? __('Estás a un paso de confirmar tu entrada');
    $visualSubtitle = __('Ingresá o seguí como invitado para terminar tu compra sin perder tu selección.');

    if (!empty($guestEvent->start_date)) {
      $visualDateCarbon = \Carbon\Carbon::parse(trim(($guestEvent->start_date ?? '') . ' ' . ($guestEvent->start_time ?? '')))->locale('es');
      $visualDate = ucfirst($visualDateCarbon->translatedFormat('l j \d\e F'));

      if (!empty($guestEvent->start_time)) {
        $visualDate .= ' · ' . $visualDateCarbon->format('H:i');
      }
    }

    $visualLocation = collect([
      $guestEvent->city ?? null,
      $guestEvent->country ?? null,
    ])->filter()->implode(', ');

    if (empty($visualLocation)) {
      $visualLocation = !empty($guestEvent->event_type) && $guestEvent->event_type === 'online'
        ? __('Evento online')
        : __('Evento en Tukipass');
    }

    if (is_numeric($checkoutSubtotal)) {
      $visualPrice = (float) $checkoutSubtotal > 0
        ? __('Total estimado') . ': ' . symbolPrice($checkoutSubtotal)
        : __('Reserva gratuita');
    }

    $formEyebrow = __('Último paso');
    $formTitle = __('Terminá tu compra');
    $formSubtitle = __('Elegí si querés seguir como invitado o entrar con tu cuenta.');
  }
@endphp
@section('meta-keywords', "{{ $metaKeywords }}")
@section('meta-description', "$metaDescription")

@section('content')
<div class="auth-split{{ $isEventCheckout ? ' auth-split--context auth-split--event' : '' }}">

  <div class="auth-split__visual" style="background-image: url('{{ $visualImage }}')">
    <div class="auth-split__visual-overlay"></div>
    <div class="auth-split__visual-content">
      <div class="auth-split__tagline{{ $isEventCheckout ? ' auth-split__tagline--context' : '' }}">
        <h2>{{ $visualTitle }}</h2>
        @if ($isEventCheckout && $guestEvent)
          @php
            $visualLocationShort = !empty($guestEvent->event_type) && $guestEvent->event_type === 'online'
              ? __('Online')
              : $visualLocation;
            $visualMeta = collect([$visualDate, $visualLocationShort])->filter()->implode(' · ');
          @endphp
          @if (!empty($visualMeta))
            <div class="ed-hero__meta">
              <div class="ed-hero__meta-item">{{ $visualMeta }}</div>
            </div>
          @endif
        @endif
        @if (!$isEventCheckout)
          <p>{{ $visualSubtitle }}</p>
        @endif
      </div>
    </div>
  </div>

  <div class="auth-split__form">
    <div class="auth-split__form-inner">
      <a href="{{ route('index') }}" class="auth-split__logo-mobile">
        <img src="{{ asset('assets/admin/img/' . $websiteInfo->logo) }}" alt="Logo">
      </a>

      @if (!$isEventCheckout)
        <div class="auth-surface">
      @endif
        @if (!$isEventCheckout)
          <span class="auth-split__form-eyebrow">{{ $formEyebrow }}</span>
        @endif
        <h1 class="auth-split__title">{{ $formTitle }}</h1>
        <p class="auth-split__subtitle">{{ $formSubtitle }}</p>

        @if ($guestCheckoutEnabled && $isEventCheckout)
          @php
            $isFreeEvent = $guestEvent && ($guestEvent->pricing_type == 'free' || Session::get('sub_total') == 0);
          @endphp
          <a href="{{ route('check-out', ['type' => 'guest']) }}" class="auth-guest-btn auth-guest-btn--cta auth-guest-btn--green">
            @if($isFreeEvent)
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              {{ __('Reservar entrada sin cuenta') }}
            @else
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
              {{ __('Comprar entrada sin cuenta') }}
            @endif
          </a>
        @endif

        @if (!onlyDigitalItemsInCart() && request()->input('redirected') == 'checkout')
          <a href="{{ route('shop.checkout', ['type' => 'guest']) }}" class="auth-guest-btn auth-guest-btn--cta">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
            {{ __('Comprar como invitado — sin registrarme') }}
          </a>
        @endif

        @if (($guestCheckoutEnabled && $isEventCheckout) || (!onlyDigitalItemsInCart() && request()->input('redirected') == 'checkout'))
          <div class="auth-divider">{{ __('o seguí con tu cuenta') }}</div>
        @endif

        @if (Session::has('success'))
          <div class="alert alert-success mb-3">{{ Session::get('success') }}</div>
        @endif
        @if (Session::has('alert'))
          <div class="alert alert-danger mb-3">{{ Session::get('alert') }}</div>
        @endif

        @if ($basicInfo->facebook_login_status == 1 || $basicInfo->google_login_status == 1)
          <div class="auth-social">
            @if ($basicInfo->facebook_login_status == 1)
              <a href="{{ route('auth.facebook', ['redirectPath' => request()->input('redirectPath')]) }}"
                 class="auth-social__btn auth-social__btn--fb">
                <i class="fab fa-facebook-f"></i> {{ __('Continuar con Facebook') }}
              </a>
            @endif
            @if ($basicInfo->google_login_status == 1)
              <a href="{{ route('auth.google', ['redirectPath' => request()->input('redirectPath')]) }}"
                 class="auth-social__btn auth-social__btn--google">
                <i class="fab fa-google"></i> {{ __('Continuar con Google') }}
              </a>
            @endif
          </div>
          <div class="auth-divider">{{ __('o ingresá con tu cuenta') }}</div>
        @endif

        <form id="login-form" action="{{ route('customer.authentication') }}" method="POST">
          @csrf

          <div class="form-group mb-4">
            <label for="username">{{ __('Usuario') }}</label>
            <input type="text" name="username" id="username"
                   class="form-control @error('username') is-invalid @enderror"
                   placeholder="{{ __('Tu nombre de usuario') }}" value="{{ old('username') }}" autocomplete="username">
            @error('username')
              <p class="text-danger mt-1" style="font-size:13px">{{ $message }}</p>
            @enderror
          </div>

          <div class="form-group mb-4">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label for="password" class="mb-0">{{ __('Contraseña') }}</label>
              <a href="{{ route('customer.forget.password') }}" class="auth-forgot-link">{{ __('¿Olvidaste tu contraseña?') }}</a>
            </div>
            <input type="password" name="password" id="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="{{ __('Tu contraseña') }}" autocomplete="current-password">
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
            {{ $isEventCheckout ? __('Continuar con mi cuenta') : __('Ingresar') }}
          </button>
        </form>

        <div class="auth-split__links">
          <span>{{ __('¿No tenés cuenta?') }} <a href="{{ route('customer.signup') }}">{{ __('Registrate gratis') }}</a></span>
        </div>
      @if (!$isEventCheckout)
        </div>
      @endif
    </div>
  </div>

</div>
@endsection
