@extends('frontend.layout')
@section('pageHeading', 'Nueva contraseña')

@section('content')
<div class="auth-centered">
  <div class="auth-centered__box">

    {{-- Ícono --}}
    <div class="auth-centered__icon auth-centered__icon--green">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
    </div>

    <h1 class="auth-centered__title">Creá tu nueva contraseña</h1>
    <p class="auth-centered__desc">Elegí una contraseña segura para proteger tu cuenta.</p>

    {{-- Alertas --}}
    @if (Session::has('success'))
      <div class="ep-alert ep-alert--success mb-4">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        {{ Session::get('success') }}
      </div>
    @endif
    @if (Session::has('alert'))
      <div class="ep-alert ep-alert--error mb-4">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        {{ Session::get('alert') }}
      </div>
    @endif

    <form action="{{ route('customer.update-forget-password') }}" method="POST">
      @csrf
      <input type="hidden" name="token" value="{{ request()->input('token') }}">

      {{-- Nueva contraseña --}}
      <div class="ep-field mb-3">
        <label class="ep-field__label">Nueva contraseña <span class="ep-field__req">*</span></label>
        <div class="cp-input-wrap">
          <svg class="cp-input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
          <input type="password" name="password" id="rp_password" required
                 class="ep-field__input ep-field__input--icon @error('password') is-invalid @enderror"
                 placeholder="Mínimo 6 caracteres" autocomplete="new-password">
          <button type="button" class="cp-eye-btn" data-target="rp_password" tabindex="-1">
            <svg class="eye-show" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            <svg class="eye-hide d-none" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
          </button>
        </div>
        @error('password')<p class="ep-field__error">{{ $message }}</p>@enderror
        <div class="cp-strength-wrap" id="rpStrengthWrap" style="display:none;">
          <div class="cp-strength-bar"><div class="cp-strength-fill" id="rpStrengthFill"></div></div>
          <span class="cp-strength-label" id="rpStrengthLabel"></span>
        </div>
      </div>

      {{-- Confirmar contraseña --}}
      <div class="ep-field mb-4">
        <label class="ep-field__label">Confirmar contraseña <span class="ep-field__req">*</span></label>
        <div class="cp-input-wrap">
          <svg class="cp-input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
          <input type="password" name="password_confirmation" id="rp_password_confirm" required
                 class="ep-field__input ep-field__input--icon"
                 placeholder="Repetí la contraseña" autocomplete="new-password">
          <button type="button" class="cp-eye-btn" data-target="rp_password_confirm" tabindex="-1">
            <svg class="eye-show" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            <svg class="eye-hide d-none" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
          </button>
        </div>
        <p class="ep-field__error" id="rpMatchError" style="display:none;">Las contraseñas no coinciden.</p>
      </div>

      <button type="submit" class="au-submit-btn">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        Guardar nueva contraseña
      </button>

    </form>

    <div class="auth-centered__links">
      <a href="{{ route('customer.login') }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Volver al inicio de sesión
      </a>
    </div>

  </div>
</div>

@push('scripts')
<script>
  document.querySelectorAll('.cp-eye-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var input = document.getElementById(this.dataset.target);
      var isText = input.type === 'text';
      input.type = isText ? 'password' : 'text';
      this.querySelector('.eye-show').classList.toggle('d-none', !isText);
      this.querySelector('.eye-hide').classList.toggle('d-none', isText);
    });
  });

  document.getElementById('rp_password').addEventListener('input', function() {
    var val = this.value;
    var wrap = document.getElementById('rpStrengthWrap');
    var fill = document.getElementById('rpStrengthFill');
    var label = document.getElementById('rpStrengthLabel');
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

  document.getElementById('rp_password_confirm').addEventListener('input', function() {
    var err = document.getElementById('rpMatchError');
    var pwd = document.getElementById('rp_password').value;
    err.style.display = (this.value && this.value !== pwd) ? 'block' : 'none';
  });
</script>
@endpush
@endsection
