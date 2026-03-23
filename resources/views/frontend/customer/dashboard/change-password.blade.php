@extends('frontend.layout')
@section('pageHeading', 'Cambiar contraseña')
@section('hero-section')
  <section class="page-banner overlay pt-120 pb-125 rpt-90 rpb-95 lazy"
    data-bg="{{ asset('assets/admin/img/' . $basicInfo->breadcrumb) }}">
    <div class="container">
      <div class="banner-inner">
        <h2 class="page-title">Cambiar contraseña</h2>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active">Cambiar contraseña</li>
          </ol>
        </nav>
      </div>
    </div>
  </section>
@endsection

@section('content')
<section class="cd-page py-60">
  <div class="container">
    <div class="row g-4">

      @includeIf('frontend.customer.partials.sidebar')

      <div class="col-lg-9">

        @if (Session::has('success'))
          <div class="ep-alert ep-alert--success mb-4">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ Session::get('success') }}
          </div>
        @endif

        <div class="cd-card">
          <div class="cd-card__head">
            <div style="display:flex;align-items:center;gap:10px;">
              <div class="cp-icon-wrap">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
              </div>
              <h3 class="cd-card__title">Cambiar contraseña</h3>
            </div>
          </div>

          <div class="ep-card-body">

            <p class="cp-hint">Usá una contraseña segura con al menos 8 caracteres, combinando letras, números y símbolos.</p>

            <form action="{{ route('customer.password.update') }}" method="POST">
              @csrf

              {{-- Contraseña actual --}}
              <div class="cp-field-group">
                <div class="ep-field">
                  <label class="ep-field__label">
                    Contraseña actual <span class="ep-field__req">*</span>
                  </label>
                  <div class="cp-input-wrap">
                    <svg class="cp-input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    <input type="password" name="current_password" id="current_password" required
                           class="ep-field__input ep-field__input--icon @error('current_password') is-invalid @enderror"
                           placeholder="Tu contraseña actual"
                           autocomplete="current-password">
                    <button type="button" class="cp-eye-btn" data-target="current_password" tabindex="-1">
                      <svg class="eye-show" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                      <svg class="eye-hide d-none" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                  </div>
                  @error('current_password')<p class="ep-field__error">{{ $message }}</p>@enderror
                </div>
              </div>

              <div class="cp-divider"></div>

              {{-- Nueva contraseña --}}
              <div class="cp-field-group">
                <div class="ep-form-grid">

                  <div class="ep-field">
                    <label class="ep-field__label">
                      Nueva contraseña <span class="ep-field__req">*</span>
                    </label>
                    <div class="cp-input-wrap">
                      <svg class="cp-input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                      <input type="password" name="new_password" id="new_password" required
                             class="ep-field__input ep-field__input--icon @error('new_password') is-invalid @enderror"
                             placeholder="Mínimo 8 caracteres"
                             autocomplete="new-password">
                      <button type="button" class="cp-eye-btn" data-target="new_password" tabindex="-1">
                        <svg class="eye-show" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="eye-hide d-none" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                      </button>
                    </div>
                    @error('new_password')<p class="ep-field__error">{{ $message }}</p>@enderror
                  </div>

                  <div class="ep-field">
                    <label class="ep-field__label">
                      Confirmar contraseña <span class="ep-field__req">*</span>
                    </label>
                    <div class="cp-input-wrap">
                      <svg class="cp-input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                      <input type="password" name="new_password_confirmation" id="new_password_confirmation" required
                             class="ep-field__input ep-field__input--icon @error('new_password_confirmation') is-invalid @enderror"
                             placeholder="Repetí la nueva contraseña"
                             autocomplete="new-password">
                      <button type="button" class="cp-eye-btn" data-target="new_password_confirmation" tabindex="-1">
                        <svg class="eye-show" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="eye-hide d-none" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                      </button>
                    </div>
                    @error('new_password_confirmation')<p class="ep-field__error">{{ $message }}</p>@enderror
                  </div>

                </div>
              </div>

              {{-- Strength indicator --}}
              <div class="cp-strength-wrap" id="strengthWrap" style="display:none;">
                <div class="cp-strength-bar">
                  <div class="cp-strength-fill" id="strengthFill"></div>
                </div>
                <span class="cp-strength-label" id="strengthLabel"></span>
              </div>

              <div class="ep-actions mt-4">
                <button type="submit" class="ep-btn-save">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                  Actualizar contraseña
                </button>
              </div>

            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

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

  // Password strength meter
  var newPwdInput = document.getElementById('new_password');
  var strengthWrap = document.getElementById('strengthWrap');
  var strengthFill = document.getElementById('strengthFill');
  var strengthLabel = document.getElementById('strengthLabel');

  newPwdInput.addEventListener('input', function() {
    var val = this.value;
    if (!val) { strengthWrap.style.display = 'none'; return; }
    strengthWrap.style.display = 'flex';
    var score = 0;
    if (val.length >= 8) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    var levels = [
      { w:'25%',  color:'#ef4444', label:'Muy débil' },
      { w:'50%',  color:'#f97316', label:'Débil' },
      { w:'75%',  color:'#eab308', label:'Buena' },
      { w:'100%', color:'#22c55e', label:'Muy fuerte' },
    ];
    var lvl = levels[score - 1] || levels[0];
    strengthFill.style.width = lvl.w;
    strengthFill.style.background = lvl.color;
    strengthLabel.textContent = lvl.label;
    strengthLabel.style.color = lvl.color;
  });
</script>
@endpush
@endsection
