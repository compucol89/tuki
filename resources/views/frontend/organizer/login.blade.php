@extends('frontend.layout')
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
  $loginOgImage = asset('assets/admin/img/' . $websiteInfo->logo);
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

@section('canonical')
{{ $loginCanonical }}
@endsection

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
    <div class="auth-split__visual-content">
      <div class="auth-split__tagline auth-split__tagline--context auth-split__tagline--multiline">
        <h2>{{ __('organizer.login.visual_title_line1') }}<br>{{ __('organizer.login.visual_title_line2') }}</h2>
        <p>{{ __('organizer.login.visual_subtitle') }}</p>
      </div>

      <div class="auth-split__stats auth-split__stats--visual" aria-label="{{ __('Beneficios') }}">
        @foreach ($organizerStats as $stat)
          <div class="auth-split__stat">
            <span class="auth-split__stat-num">{{ $stat['num'] }}</span>
            <span class="auth-split__stat-label">{{ $stat['label'] }}</span>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  <div class="auth-split__form">
    <div class="auth-split__form-inner">

      <a href="{{ route('index') }}" class="auth-split__logo-mobile">
        <img src="{{ asset('assets/admin/img/' . $websiteInfo->logo) }}" alt="{{ __('organizer.login.logo_alt', ['site' => $websiteInfo->website_title]) }}">
      </a>

      <span class="auth-split__form-eyebrow">{{ __('organizer.login.form_eyebrow') }}</span>
      <h1 class="auth-split__title">{{ __('organizer.login.form_title') }}</h1>
      <p class="auth-split__subtitle">{{ __('organizer.login.form_subtitle') }}</p>

      @if (Auth::guard('customer')->check())
        <div class="alert mb-4" style="border: 1px solid rgba(249, 115, 22, 0.18); border-radius: 18px; background: linear-gradient(180deg, rgba(249, 115, 22, 0.08) 0%, rgba(255, 255, 255, 0.96) 100%); color: #1e2532; box-shadow: 0 14px 32px rgba(30, 37, 50, 0.06);">
          <div class="d-flex align-items-center mb-2">
            <span class="d-inline-flex align-items-center justify-content-center mr-2" style="width: 34px; height: 34px; border-radius: 10px; background: rgba(249, 115, 22, 0.14); color: #f97316; font-size: 16px;">
              <i class="fas fa-info-circle"></i>
            </span>
            <strong style="font-size: 18px;">Estás ingresando al panel de organizadores</strong>
          </div>
          <p class="mb-2" style="color: #4b5563;">
            Actualmente tenés una sesión activa como cliente. Las cuentas de cliente y organizador son accesos distintos en TukiPass.
          </p>
          <p class="mb-3" style="color: #4b5563;">
            Tu cuenta de cliente te permite comprar entradas y ver tus reservas. Para crear y gestionar eventos necesitás iniciar sesión o registrarte como organizador.
          </p>
          <div class="d-flex flex-wrap" style="gap: 10px;">
            <a href="{{ route('organizer.login') }}" class="theme-btn" style="min-width: 220px;">Continuar al login de organizador</a>
            <a href="{{ route('organizer.signup') }}" class="btn btn-light" style="min-width: 220px; border-radius: 10px; border: 1px solid rgba(30, 37, 50, 0.12); color: #1e2532;">Crear cuenta de organizador</a>
            <a href="{{ route('customer.dashboard') }}" class="btn btn-light" style="min-width: 220px; border-radius: 10px; border: 1px solid rgba(30, 37, 50, 0.12); color: #1e2532;">Ir a mi cuenta de cliente</a>
            <a href="{{ route('customer.logout') }}" class="btn btn-link p-0 align-self-center" style="color: #f97316; font-weight: 700;">Cerrar sesión de cliente</a>
          </div>
        </div>
      @endif

      @if (Session::has('success'))
        <div class="alert alert-success mb-3">{{ Session::get('success') }}</div>
      @endif
      @if (Session::has('alert'))
        <div class="alert alert-danger mb-3">{{ Session::get('alert') }}</div>
      @endif

      <form id="login-form" name="login_form" action="{{ route('organizer.authentication') }}" method="POST">
        @csrf

        <div class="form-group mb-4">
          <label for="username">{{ __('organizer.login.username_label') }}</label>
          <input type="text" name="username" id="username"
                 class="form-control @error('username') is-invalid @enderror"
                 placeholder="{{ __('organizer.login.username_placeholder') }}" value="{{ old('username') }}" autocomplete="username">
          @error('username')
            <p class="text-danger mt-1" style="font-size:13px">{{ $message }}</p>
          @enderror
        </div>

        <div class="form-group mb-4">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <label for="password" class="mb-0">{{ __('organizer.login.password_label') }}</label>
            <a href="{{ route('organizer.forget.password') }}" class="auth-forgot-link">{{ __('organizer.login.forgot_password') }}</a>
          </div>
          <input type="password" name="password" id="password"
                 class="form-control @error('password') is-invalid @enderror"
                 placeholder="{{ __('organizer.login.password_placeholder') }}" autocomplete="current-password">
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

        <button type="submit" class="theme-btn w-100" data-loading-text="{{ __('organizer.login.loading') }}">
          {{ __('organizer.login.submit') }}
        </button>
      </form>

      <div class="auth-split__links">
        <span>{{ __('organizer.login.footer_no_account') }} <a href="{{ route('organizer.signup') }}">{{ __('organizer.login.footer_signup') }}</a></span>
      </div>

    </div>
  </div>

</div>
@endsection
