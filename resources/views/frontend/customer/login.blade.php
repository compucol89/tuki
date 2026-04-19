@extends('frontend.layout')
@section('body-class', 'auth-page')

@php
  $loginPageTitle = filled(optional($pageHeading)->customer_login_page_title)
    ? $pageHeading->customer_login_page_title
    : __('customer.login.page_heading');

  $metaKeywords = !empty($seo->meta_keyword_customer_login)
    ? trim($seo->meta_keyword_customer_login)
    : __('customer.login.seo.meta_keywords_default');

  $metaDescription = !empty($seo->meta_description_customer_login)
    ? trim($seo->meta_description_customer_login)
    : __('customer.login.seo.meta_description_default');

  $loginSeoCanonical = route('customer.login', [], true);
  $loginOgImage = asset('assets/admin/img/' . $websiteInfo->logo);
  $loginOgTitle = $loginPageTitle . ' | ' . $websiteInfo->website_title;

  $redirectPath = request()->input('redirectPath');
  $isEventCheckout = $redirectPath === 'event_checkout';
  $guestEvent = Session::get('event');
  $guestCheckoutEnabled = App\Models\BasicSettings\Basic::query()->value('event_guest_checkout_status') == 1;

  $visualImage = asset('assets/admin/img/' . $basicInfo->breadcrumb);
  $visualTitle = __('customer.login.visual_title');
  $visualSubtitle = __('customer.login.visual_subtitle');
  $visualDate = null;
  $visualLocation = null;
  $formEyebrow = __('customer.login.form_eyebrow');
  $formTitle = __('customer.login.form_title');
  $formSubtitle = __('customer.login.form_subtitle');

  if ($isEventCheckout && $guestEvent) {
    if (!empty($guestEvent->thumbnail)) {
      $visualImage = asset('assets/admin/img/event/thumbnail/' . $guestEvent->thumbnail);
    }

    $visualTitle = $guestEvent->title ?? __('customer.login.fallback_event_title');
    $visualSubtitle = __('customer.login.visual_subtitle_checkout');

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
        ? __('customer.login.event_online')
        : __('customer.login.event_on_tukipass');
    }

    $formEyebrow = __('customer.login.form_eyebrow_checkout');
    $formTitle = __('customer.login.form_title_checkout');
    $formSubtitle = __('customer.login.form_subtitle_checkout');
  }
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
{{ __('customer.login.seo.robots') }}
@endsection

@section('canonical')
{{ $loginSeoCanonical }}
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
{{ $loginSeoCanonical }}
@endsection

@section('og-image-alt')
{{ __('customer.login.seo.og_image_alt', ['site' => $websiteInfo->website_title]) }}
@endsection

@section('content')
<div class="auth-split auth-split--context auth-split--event">

  <div class="auth-split__visual" style="background-image: url('{{ $visualImage }}')">
    <div class="auth-split__visual-overlay"></div>
    <div class="auth-split__visual-content">
      <div class="auth-split__tagline auth-split__tagline--context">
        <h2>{{ $visualTitle }}</h2>
        @if ($isEventCheckout && $guestEvent)
          @php
            $visualLocationShort = !empty($guestEvent->event_type) && $guestEvent->event_type === 'online'
              ? __('customer.login.online_short')
              : $visualLocation;
            $visualMeta = collect([$visualDate, $visualLocationShort])->filter()->implode(' · ');
          @endphp
          @if (!empty($visualMeta))
            <div class="ed-hero__meta">
              <div class="ed-hero__meta-item">{{ $visualMeta }}</div>
            </div>
          @endif
        @elseif (!empty($visualSubtitle))
          <p>{{ $visualSubtitle }}</p>
        @endif
      </div>
    </div>
  </div>

  <div class="auth-split__form">
    <div class="auth-split__form-inner">
      <a href="{{ route('index') }}" class="auth-split__logo-mobile">
        <img src="{{ asset('assets/admin/img/' . $websiteInfo->logo) }}" alt="{{ __('Logo de Tukipass') }}">
      </a>

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
            {{ __('customer.login.guest_reserve_no_account') }}
          @else
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
            {{ __('customer.login.guest_buy_no_account') }}
          @endif
        </a>
      @endif

      @if (!onlyDigitalItemsInCart() && request()->input('redirected') == 'checkout')
        <a href="{{ route('shop.checkout', ['type' => 'guest']) }}" class="auth-guest-btn auth-guest-btn--cta">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
          {{ __('customer.login.guest_shop_checkout') }}
        </a>
      @endif

      @if (($guestCheckoutEnabled && $isEventCheckout) || (!onlyDigitalItemsInCart() && request()->input('redirected') == 'checkout'))
        <div class="auth-divider">{{ __('customer.login.divider_continue_account') }}</div>
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
              <i class="fab fa-facebook-f"></i> {{ __('customer.login.continue_facebook') }}
            </a>
          @endif
          @if ($basicInfo->google_login_status == 1)
            <a href="{{ route('auth.google', ['redirectPath' => request()->input('redirectPath')]) }}"
               class="auth-social__btn auth-social__btn--google">
              <i class="fab fa-google"></i> {{ __('customer.login.continue_google') }}
            </a>
          @endif
        </div>
        <div class="auth-divider">{{ __('customer.login.divider_email_login') }}</div>
      @endif

      <form id="login-form" action="{{ route('customer.authentication') }}" method="POST">
        @csrf

        <div class="form-group mb-4">
          <label for="username">{{ __('customer.login.username_label') }}</label>
          <input type="text" name="username" id="username"
                 class="form-control @error('username') is-invalid @enderror"
                 placeholder="{{ __('customer.login.username_placeholder') }}" value="{{ old('username') }}" autocomplete="username">
          @error('username')
            <p class="text-danger mt-1" style="font-size:13px">{{ $message }}</p>
          @enderror
        </div>

        <div class="form-group mb-4">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <label for="password" class="mb-0">{{ __('customer.login.password_label') }}</label>
            <a href="{{ route('customer.forget.password') }}" class="auth-forgot-link">{{ __('customer.login.forgot_password') }}</a>
          </div>
          <input type="password" name="password" id="password"
                 class="form-control @error('password') is-invalid @enderror"
                 placeholder="{{ __('customer.login.password_placeholder') }}" autocomplete="current-password">
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

        <button type="submit" class="theme-btn w-100" data-loading-text="{{ __('customer.login.loading') }}">
          {{ $isEventCheckout ? __('customer.login.submit_checkout') : __('customer.login.submit_login') }}
        </button>
      </form>

      <div class="auth-split__links">
        <span>{{ __('customer.login.no_account') }} <a href="{{ route('customer.signup') }}">{{ __('customer.login.register_free') }}</a></span>
      </div>
    </div>
  </div>

</div>
@endsection
