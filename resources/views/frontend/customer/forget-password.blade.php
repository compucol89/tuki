@extends('frontend.layout')
@section('pageHeading', 'Olvidé mi contraseña')
@php
  $metaKeywords = !empty($seo->meta_keyword_customer_forget_password) ? $seo->meta_keyword_customer_forget_password : '';
  $metaDescription = !empty($seo->meta_description_customer_forget_password) ? $seo->meta_description_customer_forget_password : '';
@endphp
@section('meta-keywords', "{{ $metaKeywords }}")
@section('meta-description', "$metaDescription")

@section('content')
<div class="auth-centered">
  <div class="auth-centered__box">

    {{-- Ícono --}}
    <div class="auth-centered__icon auth-centered__icon--orange">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
    </div>

    <h1 class="auth-centered__title">¿Olvidaste tu contraseña?</h1>
    <p class="auth-centered__desc">Ingresá tu email y te enviamos un enlace para restablecerla.</p>

    {{-- Alertas --}}
    @if (Session::has('error'))
      <div class="ep-alert ep-alert--error mb-4">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        {{ Session::get('error') }}
      </div>
    @endif
    @if (Session::has('success'))
      <div class="ep-alert ep-alert--success mb-4">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        {{ Session::get('success') }}
      </div>
    @endif

    {{-- Formulario --}}
    <form action="{{ route('customer.forget.mail') }}" method="POST">
      @csrf

      <div class="ep-field mb-4">
        <label class="ep-field__label">Email <span class="ep-field__req">*</span></label>
        <div class="cp-input-wrap">
          <svg class="cp-input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          <input type="email" name="email" id="email" required
                 class="ep-field__input ep-field__input--icon @error('email') is-invalid @enderror"
                 placeholder="tu@email.com" value="{{ old('email') }}" autocomplete="email">
        </div>
        @error('email')<p class="ep-field__error">{{ $message }}</p>@enderror
      </div>

      <button type="submit" class="au-submit-btn">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
        Enviar enlace de recuperación
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
@endsection
