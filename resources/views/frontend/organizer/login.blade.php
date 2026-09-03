@extends('frontend.layout')

@push('styles')
  <link rel="stylesheet" href="{{ frontAsset('assets/front/css/auth.css') }}">
  <link rel="stylesheet" href="{{ frontAsset('assets/front/css/organizer.css') }}">
@endpush

@section('body-class', 'auth-page')

@php
  $loginPageTitle = filled(optional($pageHeading)->organizer_login_page_title)
    ? $pageHeading->organizer_login_page_title
    : __('organizer.login.page_heading');

  $metaKeywords = !empty($seo->meta_keyword_organizer_login)
    ? trim($seo->meta_keyword_organizer_login)
    : __('organizer.login.seo.meta_keywords_default');

  $metaDescription = !empty($seo->meta_description_organizer_login)
    ? trim($seo->meta_description_organizer_login)
    : __('organizer.login.seo.meta_description_default');

  $loginCanonical = route('organizer.login', [], true);
  $loginOgImage = asset('assets/front/img/og/tukipass-og.jpg');
  $loginOgTitle = $loginPageTitle . ' | ' . $websiteInfo->website_title;

  $organizerStats = __('organizer.login.stats');
@endphp

@section('pageHeading')
{{ $loginPageTitle }}
@endsection

@section('meta-keywords')
{{ $metaKeywords }}
@endsection

@section('meta-description')
{{ $metaDescription }}
@endsection

@section('meta-robots')
{{ __('organizer.login.seo.robots') }}
@endsection

@section('canonical', url()->current())

@section('og-title')
{{ $loginOgTitle }}
@endsection

@section('og-description')
{{ $metaDescription }}
@endsection

@section('og-image')
{{ $loginOgImage }}
@endsection

@section('og-url')
{{ $loginCanonical }}
@endsection

@section('og-image-alt')
{{ __('organizer.login.seo.og_image_alt', ['site' => $websiteInfo->website_title]) }}
@endsection

@section('content')
<div class="auth-split auth-split--context auth-split--event auth-split--organizer-login">

  <div class="auth-split__visual"
       style="background-image: url('{{ asset('assets/admin/img/' . $basicInfo->breadcrumb) }}')">
    <div class="auth-split__visual-overlay"></div>
    <div class="auth-split__visual-content auth-split__visual-content--organizer">
      <div class="auth-split__tagline auth-split__tagline--context auth-split__tagline--multiline">
        <h2>{{ __('organizer.login.visual_title_line1') }}<br>{{ __('organizer.login.visual_title_line2') }}</h2>
        <p>{{ __('organizer.login.visual_subtitle') }}</p>
      </div>

      <div class="auth-split__stats auth-split__stats--visual auth-split__stats--organizer" role="list" aria-label="{{ __('organizer.login.stats_aria_label') }}">
        @foreach ($organizerStats as $stat)
          <div class="auth-split__stat" role="listitem">
            @if (!empty($stat['icon']))
              <span class="auth-split__stat-icon" aria-hidden="true"><i class="{{ $stat['icon'] }}"></i></span>
            @endif
            <span class="auth-split__stat-copy">
              <span class="auth-split__stat-num">{{ $stat['num'] }}</span>
              <span class="auth-split__stat-label">{{ $stat['label'] }}</span>
            </span>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  <div class="auth-split__form">
    <div class="auth-split__form-inner auth-surface auth-login-surface">

      <div class="auth-login-head">
        <h1 class="auth-split__title">{{ __('organizer.login.form_title') }}</h1>
        <p class="auth-split__subtitle" id="organizer-login-summary">{{ __('organizer.login.form_subtitle') }}</p>
      </div>

      <div class="auth-split__mobile-value" role="list" aria-label="{{ __('organizer.login.stats_aria_label') }}">
        @foreach ($organizerStats as $stat)
          <span role="listitem">
            @if (!empty($stat['icon']))
              <i class="{{ $stat['icon'] }}" aria-hidden="true"></i>
            @endif
            {{ $stat['num'] }}
          </span>
        @endforeach
      </div>

      @if (Auth::guard('customer')->check())
        <div class="alert mb-4" style="border: 1px solid color-mix(in srgb, var(--primary) 18%, transparent); border-radius: 18px; background: linear-gradient(180deg, color-mix(in srgb, var(--primary) 8%, transparent) 0%, var(--card) 100%); color: var(--foreground); box-shadow: var(--shadow-md);">
          <div class="d-flex align-items-center mb-2">
            <span class="d-inline-flex align-items-center justify-content-center mr-2" style="width: 34px; height: 34px; border-radius: 10px; background: color-mix(in srgb, var(--primary) 14%, transparent); color: var(--primary); font-size: 16px;">
              <i class="fas fa-info-circle"></i>
            </span>
            <strong style="font-size: 18px;">Estás ingresando al panel de organizadores</strong>
          </div>
          <p class="mb-2" style="color: var(--secondary-foreground);">
            Actualmente tenés una sesión activa como cliente. Las cuentas de cliente y organizador son accesos distintos en TukiPass.
          </p>
          <p class="mb-3" style="color: var(--secondary-foreground);">
            Tu cuenta de cliente te permite comprar entradas y ver tus reservas. Para crear y gestionar eventos necesitás iniciar sesión o registrarte como organizador.
          </p>
          <div class="d-flex flex-wrap" style="gap: 10px;">
            <a href="{{ route('organizer.login') }}" class="theme-btn" style="min-width: 220px;">Continuar al login de organizador</a>
            <a href="{{ route('organizer.signup') }}" class="btn btn-light" style="min-width: 220px; border-radius: 10px; border: 1px solid color-mix(in srgb, var(--foreground) 12%, transparent); color: var(--foreground);">Crear cuenta de organizador</a>
            <a href="{{ route('customer.dashboard') }}" class="btn btn-light" style="min-width: 220px; border-radius: 10px; border: 1px solid color-mix(in srgb, var(--foreground) 12%, transparent); color: var(--foreground);">Ir a mi cuenta de cliente</a>
            <a href="{{ route('customer.logout') }}" class="btn btn-link p-0 align-self-center" style="color: var(--primary); font-weight: 700;">Cerrar sesión de cliente</a>
          </div>
        </div>
      @endif

      @if (Session::has('success'))
        <div class="alert alert-success mb-3" role="status">{{ Session::get('success') }}</div>
      @endif
      @if (Session::has('alert'))
        <div class="alert alert-danger mb-3" role="alert">{{ Session::get('alert') }}</div>
      @endif

      <form id="login-form" name="login_form" class="auth-login-form" action="{{ route('organizer.authentication') }}" method="POST" aria-describedby="organizer-login-summary">
        @csrf

        <div class="form-group auth-login-field mb-4">
          <label for="username">{{ __('organizer.login.username_label') }}</label>
          <div class="auth-login-control">
            <span class="auth-login-control__icon" aria-hidden="true">
              <i class="fas fa-user"></i>
            </span>
            <input type="text" name="username" id="username"
                   class="form-control auth-login-input @error('username') is-invalid @enderror"
                   placeholder="{{ __('organizer.login.username_placeholder') }}" value="{{ old('username') }}"
                   autocomplete="username" autocapitalize="none" spellcheck="false"
                   aria-invalid="{{ $errors->has('username') ? 'true' : 'false' }}"
                   @if ($errors->has('username')) autofocus @endif
                   @if ($errors->has('username')) aria-describedby="username-error" @endif>
          </div>
          @error('username')
            <p class="auth-login-error" id="username-error" role="alert">
              <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
              {{ $message }}
            </p>
          @enderror
        </div>

        <div class="form-group auth-login-field mb-4">
          <div class="d-flex justify-content-between align-items-center auth-login-label-row mb-1">
            <label for="password" class="mb-0">{{ __('organizer.login.password_label') }}</label>
            <a href="{{ route('organizer.forget.password') }}" class="auth-forgot-link">{{ __('organizer.login.forgot_password') }}</a>
          </div>
          <div class="position-relative auth-login-control auth-login-control--password">
            <span class="auth-login-control__icon" aria-hidden="true">
              <i class="fas fa-lock"></i>
            </span>
            <input type="password" name="password" id="password"
                   class="form-control auth-login-input @error('password') is-invalid @enderror"
                   placeholder="{{ __('organizer.login.password_placeholder') }}" autocomplete="current-password"
                   aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                   @if (!$errors->has('username') && $errors->has('password')) autofocus @endif
                   @if ($errors->has('password')) aria-describedby="password-error" @endif>
            <button type="button" class="auth-password-toggle" aria-label="{{ __('Mostrar contraseña') }}"
              aria-controls="password" aria-pressed="false" data-toggle-target="password">
              <i class="fas fa-eye" aria-hidden="true"></i>
            </button>
          </div>
          @error('password')
            <p class="auth-login-error" id="password-error" role="alert">
              <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
              {{ $message }}
            </p>
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

        <button type="submit" class="theme-btn auth-login-submit w-100" data-loading-text="{{ __('organizer.login.loading') }}">
          <span>{{ __('organizer.login.submit') }}</span>
          <i class="fas fa-arrow-right" aria-hidden="true"></i>
        </button>
      </form>

      <div class="auth-split__links auth-split__links--organizer">
        <span>{{ __('organizer.login.footer_no_account') }}</span>
        <a href="{{ route('organizer.signup') }}">
          {{ __('organizer.login.footer_signup') }}
          <i class="fas fa-arrow-right" aria-hidden="true"></i>
        </a>
      </div>

    </div>
  </div>

</div>
@endsection

@section('script')
  <script>
    document.querySelectorAll('.auth-password-toggle').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var input = document.getElementById(this.getAttribute('data-toggle-target'));
        if (!input) return;
        var show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        this.setAttribute('aria-pressed', show ? 'true' : 'false');
        this.setAttribute('aria-label', show ? '{{ __('Ocultar contraseña') }}' : '{{ __('Mostrar contraseña') }}');
        this.querySelector('i').className = show ? 'fas fa-eye-slash' : 'fas fa-eye';
      });
    });
  </script>
@endsection
