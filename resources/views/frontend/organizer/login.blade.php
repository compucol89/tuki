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
