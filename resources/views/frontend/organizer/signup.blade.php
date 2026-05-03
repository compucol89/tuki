@extends('frontend.layout')
@section('body-class', 'auth-page')

@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/front/css/organizer.css') }}">
@endpush

@php
  $signupPageTitle = filled(optional($pageHeading)->organizer_signup_page_title)
    ? $pageHeading->organizer_signup_page_title
    : __('organizer.signup.page_heading');

  $metaKeywords = !empty($seo->meta_keyword_organizer_signup)
    ? trim($seo->meta_keyword_organizer_signup)
    : __('organizer.signup.seo.meta_keywords_default');

  $metaDescription = !empty($seo->meta_description_organizer_signup)
    ? trim($seo->meta_description_organizer_signup)
    : __('organizer.signup.seo.meta_description_default');

  $signupCanonical = route('organizer.signup', [], true);
  $signupOgImage = asset('assets/admin/img/' . $websiteInfo->logo);
  $signupOgTitle = $signupPageTitle . ' | ' . $websiteInfo->website_title;

  $signupStats = __('organizer.signup.stats');
  $passwordStrengthLabels = [
    __('organizer.signup.password_strength.very_weak'),
    __('organizer.signup.password_strength.weak'),
    __('organizer.signup.password_strength.good'),
    __('organizer.signup.password_strength.strong'),
  ];
@endphp

@section('pageHeading')
{{ $signupPageTitle }}
@endsection

@section('meta-keywords')
{{ $metaKeywords }}
@endsection

@section('meta-description')
{{ $metaDescription }}
@endsection

@section('meta-robots')
{{ __('organizer.signup.seo.robots') }}
@endsection

@section('canonical')
{{ $signupCanonical }}
@endsection

@section('og-title')
{{ $signupOgTitle }}
@endsection

@section('og-description')
{{ $metaDescription }}
@endsection

@section('og-image')
{{ $signupOgImage }}
@endsection

@section('og-url')
{{ $signupCanonical }}
@endsection

@section('og-image-alt')
{{ __('organizer.signup.seo.og_image_alt', ['site' => $websiteInfo->website_title]) }}
@endsection

@section('content')
<div class="auth-split auth-split--context auth-split--event">

  <div class="auth-split__visual"
       style="background-image: url('{{ asset('assets/admin/img/' . $basicInfo->breadcrumb) }}')">
    <div class="auth-split__visual-overlay"></div>
    <div class="auth-split__visual-content">
      <div class="auth-split__tagline auth-split__tagline--context auth-split__tagline--multiline">
        <h2>{{ __('organizer.signup.visual_title_line1') }}<br>{{ __('organizer.signup.visual_title_line2') }}</h2>
        <p>{{ __('organizer.signup.visual_subtitle') }}</p>
      </div>
      <div class="auth-split__stats auth-split__stats--visual" aria-label="{{ __('organizer.signup.stats_aria_label') }}">
        @foreach ($signupStats as $stat)
          <div class="auth-split__stat">
            <span class="auth-split__stat-num">{{ $stat['num'] }}</span>
            <span class="auth-split__stat-label">{{ $stat['label'] }}</span>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  <div class="auth-split__form">
    <div class="auth-split__form-inner auth-split__form-inner--wide">

      <a href="{{ route('index') }}" class="auth-split__logo-mobile">
        <img src="{{ asset('assets/admin/img/' . $websiteInfo->logo) }}" alt="{{ __('organizer.signup.logo_alt', ['site' => $websiteInfo->website_title]) }}">
      </a>

      <h1 class="auth-split__title">{{ __('organizer.signup.form_title') }}</h1>
      <p class="auth-split__subtitle">{{ __('organizer.signup.form_subtitle') }}</p>

      @if (Session::has('success'))
        <div class="ep-alert ep-alert--success mb-3">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          {{ Session::get('success') }}
        </div>
      @endif
      @if (Session::has('alert'))
        <div class="ep-alert ep-alert--error mb-3">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          {{ Session::get('alert') }}
        </div>
      @endif

      <form id="organizer-signup-form" action="{{ route('organizer.create') }}" method="POST">
        @csrf

        <div class="au-form-grid">

          <div class="ep-field ep-field--full">
            <label class="ep-field__label" for="name">{{ __('organizer.signup.field_name_label') }} <span class="ep-field__req">*</span></label>
            <div class="cp-input-wrap">
              <svg class="cp-input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              <input type="text" name="name" id="name" value="{{ old('name') }}" required
                     class="ep-field__input ep-field__input--icon @error('name') is-invalid @enderror"
                     placeholder="{{ __('organizer.signup.field_name_placeholder') }}" autocomplete="name">
            </div>
            @error('name')<p class="ep-field__error">{{ $message }}</p>@enderror
          </div>

          <div class="ep-field">
            <label class="ep-field__label" for="username">{{ __('organizer.signup.field_username_label') }} <span class="ep-field__req">*</span></label>
            <div class="cp-input-wrap">
              <svg class="cp-input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
              <input type="text" name="username" id="username" value="{{ old('username') }}" required
                     class="ep-field__input ep-field__input--icon @error('username') is-invalid @enderror"
                     placeholder="{{ __('organizer.signup.field_username_placeholder') }}" autocomplete="username">
            </div>
            @error('username')<p class="ep-field__error">{{ $message }}</p>@enderror
          </div>

          <div class="ep-field">
            <label class="ep-field__label" for="email">{{ __('organizer.signup.field_email_label') }} <span class="ep-field__req">*</span></label>
            <div class="cp-input-wrap">
              <svg class="cp-input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
              <input type="email" name="email" id="email" value="{{ old('email') }}" required
                     class="ep-field__input ep-field__input--icon @error('email') is-invalid @enderror"
                     placeholder="{{ __('organizer.signup.field_email_placeholder') }}" autocomplete="email">
            </div>
            @error('email')<p class="ep-field__error">{{ $message }}</p>@enderror
          </div>

          <div class="ep-field">
            <label class="ep-field__label" for="org_password">{{ __('organizer.signup.field_password_label') }} <span class="ep-field__req">*</span></label>
            <div class="cp-input-wrap">
              <svg class="cp-input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
              <input type="password" name="password" id="org_password" required
                     class="ep-field__input ep-field__input--icon @error('password') is-invalid @enderror"
                     placeholder="{{ __('organizer.signup.field_password_placeholder') }}" autocomplete="new-password">
              <button type="button" class="cp-eye-btn" data-target="org_password" tabindex="-1" aria-label="{{ __('organizer.signup.aria_toggle_password') }}">
                <svg class="eye-show" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg class="eye-hide d-none" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
              </button>
            </div>
            @error('password')<p class="ep-field__error">{{ $message }}</p>@enderror
            <div class="cp-strength-wrap" id="orgStrengthWrap" style="display:none;">
              <div class="cp-strength-bar"><div class="cp-strength-fill" id="orgStrengthFill"></div></div>
              <span class="cp-strength-label" id="orgStrengthLabel"></span>
            </div>
          </div>

          <div class="ep-field">
            <label class="ep-field__label" for="org_password_confirm">{{ __('organizer.signup.field_password_confirm_label') }} <span class="ep-field__req">*</span></label>
            <div class="cp-input-wrap">
              <svg class="cp-input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
              <input type="password" name="password_confirmation" id="org_password_confirm" required
                     class="ep-field__input ep-field__input--icon"
                     placeholder="{{ __('organizer.signup.field_password_confirm_placeholder') }}" autocomplete="new-password">
              <button type="button" class="cp-eye-btn" data-target="org_password_confirm" tabindex="-1" aria-label="{{ __('organizer.signup.aria_toggle_password') }}">
                <svg class="eye-show" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg class="eye-hide d-none" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
              </button>
            </div>
            <p class="ep-field__error" id="orgMatchError" style="display:none;">{{ __('organizer.signup.password_mismatch') }}</p>
          </div>

        </div>

        @if ($basicInfo->google_recaptcha_status == 1)
          <div class="mb-3 mt-3">
            {!! NoCaptcha::renderJs() !!}
            {!! NoCaptcha::display() !!}
            @error('g-recaptcha-response')
              <p class="ep-field__error mt-1">{{ $message }}</p>
            @enderror
          </div>
        @endif

        <button type="submit" class="au-submit-btn mt-3" data-loading-text="{{ __('organizer.signup.loading') }}">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
          {{ __('organizer.signup.submit') }}
        </button>

      </form>

      <div class="auth-split__links">
        <span>{{ __('organizer.signup.footer_has_account') }} <a href="{{ route('organizer.login') }}">{{ __('organizer.signup.footer_login') }}</a></span>
      </div>

    </div>
  </div>

</div>

@push('scripts')
<script>
  (function() {
    var strengthLabels = @json($passwordStrengthLabels);

    document.querySelectorAll('#organizer-signup-form .cp-eye-btn').forEach(function(btn) {
      btn.addEventListener('click', function() {
        var input = document.getElementById(this.dataset.target);
        if (!input) return;
        var isText = input.type === 'text';
        input.type = isText ? 'password' : 'text';
        var show = this.querySelector('.eye-show');
        var hide = this.querySelector('.eye-hide');
        if (show) show.classList.toggle('d-none', !isText);
        if (hide) hide.classList.toggle('d-none', isText);
      });
    });

    var orgPwd = document.getElementById('org_password');
    var orgPwd2 = document.getElementById('org_password_confirm');
    if (orgPwd) {
      orgPwd.addEventListener('input', function() {
        var val = this.value;
        var wrap = document.getElementById('orgStrengthWrap');
        var fill = document.getElementById('orgStrengthFill');
        var label = document.getElementById('orgStrengthLabel');
        if (!wrap || !fill || !label) return;
        if (!val) { wrap.style.display = 'none'; return; }
        wrap.style.display = 'flex';
        var score = 0;
        if (val.length >= 6) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;
        var idx = Math.max(0, Math.min(score - 1, strengthLabels.length - 1));
        var widths = ['25%', '50%', '75%', '100%'];
        var colors = ['#ef4444', '#f97316', '#eab308', '#22c55e'];
        fill.style.width = widths[idx];
        fill.style.background = colors[idx];
        label.textContent = strengthLabels[idx];
        label.style.color = colors[idx];
      });
    }

    if (orgPwd && orgPwd2) {
      orgPwd2.addEventListener('input', function() {
        var err = document.getElementById('orgMatchError');
        if (!err) return;
        err.style.display = (this.value && this.value !== orgPwd.value) ? 'block' : 'none';
      });
    }
  })();
</script>
@endpush
@endsection
