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

  {{-- Panel formulario derecho --}}
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

      {{-- Formulario --}}
      <form id="signup-form" action="{{ route('customer.create') }}" method="POST">
        @csrf

        <div class="au-form-grid">

          {{-- Nombre --}}
          <div class="ep-field">
            <label class="ep-field__label">Nombre <span class="ep-field__req">*</span></label>
            <div class="cp-input-wrap">
              <svg class="cp-input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              <input type="text" name="fname" id="fname" required
                     class="ep-field__input ep-field__input--icon @error('fname') is-invalid @enderror"
                     placeholder="Tu nombre" value="{{ old('fname') }}" autocomplete="given-name">
            </div>
            @error('fname')<p class="ep-field__error">{{ $message }}</p>@enderror
          </div>

          {{-- Apellido --}}
          <div class="ep-field">
            <label class="ep-field__label">Apellido <span class="ep-field__req">*</span></label>
            <div class="cp-input-wrap">
              <svg class="cp-input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              <input type="text" name="lname" id="lname" required
                     class="ep-field__input ep-field__input--icon @error('lname') is-invalid @enderror"
                     placeholder="Tu apellido" value="{{ old('lname') }}" autocomplete="family-name">
            </div>
            @error('lname')<p class="ep-field__error">{{ $message }}</p>@enderror
          </div>

          {{-- Usuario --}}
          <div class="ep-field">
            <label class="ep-field__label">Usuario <span class="ep-field__req">*</span></label>
            <div class="cp-input-wrap">
              <svg class="cp-input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
              <input type="text" name="username" id="username" required
                     class="ep-field__input ep-field__input--icon @error('username') is-invalid @enderror"
                     placeholder="Elegí un usuario" value="{{ old('username') }}" autocomplete="username">
            </div>
            @error('username')<p class="ep-field__error">{{ $message }}</p>@enderror
          </div>

          {{-- Email --}}
          <div class="ep-field">
            <label class="ep-field__label">Email <span class="ep-field__req">*</span></label>
            <div class="cp-input-wrap">
              <svg class="cp-input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
              <input type="email" name="email" id="email" required
                     class="ep-field__input ep-field__input--icon @error('email') is-invalid @enderror"
                     placeholder="tu@email.com" value="{{ old('email') }}" autocomplete="email">
            </div>
            @error('email')<p class="ep-field__error">{{ $message }}</p>@enderror
          </div>

          {{-- Contraseña --}}
          <div class="ep-field">
            <label class="ep-field__label">Contraseña <span class="ep-field__req">*</span></label>
            <div class="cp-input-wrap">
              <svg class="cp-input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
              <input type="password" name="password" id="su_password" required
                     class="ep-field__input ep-field__input--icon @error('password') is-invalid @enderror"
                     placeholder="Mínimo 6 caracteres" autocomplete="new-password">
              <button type="button" class="cp-eye-btn" data-target="su_password" tabindex="-1">
                <svg class="eye-show" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg class="eye-hide d-none" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
              </button>
            </div>
            @error('password')<p class="ep-field__error">{{ $message }}</p>@enderror
            <div class="cp-strength-wrap" id="suStrengthWrap" style="display:none;">
              <div class="cp-strength-bar"><div class="cp-strength-fill" id="suStrengthFill"></div></div>
              <span class="cp-strength-label" id="suStrengthLabel"></span>
            </div>
          </div>

          {{-- Repetir contraseña --}}
          <div class="ep-field">
            <label class="ep-field__label">Repetir contraseña <span class="ep-field__req">*</span></label>
            <div class="cp-input-wrap">
              <svg class="cp-input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
              <input type="password" name="password_confirmation" id="su_password_confirm" required
                     class="ep-field__input ep-field__input--icon"
                     placeholder="Repetí la contraseña" autocomplete="new-password">
              <button type="button" class="cp-eye-btn" data-target="su_password_confirm" tabindex="-1">
                <svg class="eye-show" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg class="eye-hide d-none" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
              </button>
            </div>
            <p class="ep-field__error" id="suMatchError" style="display:none;">Las contraseñas no coinciden.</p>
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

        <button type="submit" class="au-submit-btn mt-3">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
          Crear cuenta gratis
        </button>

      </form>

      <div class="auth-split__links">
        <span>¿Ya tenés cuenta? <a href="{{ route('customer.login') }}">Ingresá acá</a></span>
      </div>

    </div>
  </div>

</div>

@push('scripts')
<script>
  // Toggle show/hide password
  document.querySelectorAll('.cp-eye-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var input = document.getElementById(this.dataset.target);
      var isText = input.type === 'text';
      input.type = isText ? 'password' : 'text';
      this.querySelector('.eye-show').classList.toggle('d-none', !isText);
      this.querySelector('.eye-hide').classList.toggle('d-none', isText);
    });
  });

  // Password strength
  document.getElementById('su_password').addEventListener('input', function() {
    var val = this.value;
    var wrap = document.getElementById('suStrengthWrap');
    var fill = document.getElementById('suStrengthFill');
    var label = document.getElementById('suStrengthLabel');
    if (!val) { wrap.style.display = 'none'; return; }
    wrap.style.display = 'flex';
    var score = 0;
    if (val.length >= 6) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    var levels = [
      { w:'25%',  color:'#ef4444', text:'Muy débil' },
      { w:'50%',  color:'#f97316', text:'Débil' },
      { w:'75%',  color:'#eab308', text:'Buena' },
      { w:'100%', color:'#22c55e', text:'Muy fuerte' },
    ];
    var lvl = levels[score - 1] || levels[0];
    fill.style.width = lvl.w;
    fill.style.background = lvl.color;
    label.textContent = lvl.text;
    label.style.color = lvl.color;
  });

  // Password match check
  document.getElementById('su_password_confirm').addEventListener('input', function() {
    var err = document.getElementById('suMatchError');
    var pwd = document.getElementById('su_password').value;
    err.style.display = (this.value && this.value !== pwd) ? 'block' : 'none';
  });
</script>
@endpush
@endsection
