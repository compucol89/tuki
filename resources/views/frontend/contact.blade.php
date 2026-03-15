@extends('frontend.layout')
@section('pageHeading')
  {{ $pageHeading->contact_page_title ?? __('Contacto') }}
@endsection
@php
  $metaKeywords    = !empty($seo->meta_keyword_contact)    ? $seo->meta_keyword_contact    : '';
  $metaDescription = !empty($seo->meta_description_contact) ? $seo->meta_description_contact : '';
@endphp
@section('meta-keywords',    $metaKeywords)
@section('meta-description', $metaDescription)

@push('styles')
<style>
/* ── Contact Page — Modern SaaS UI ── */
/* ── Layout ── */
.ctp-section {
  padding: 72px 0 80px;
  background: #fff;
}
.ctp-grid {
  display: grid;
  grid-template-columns: 1fr 1.5fr;
  gap: 48px;
  align-items: start;
}
@media (max-width: 991px) {
  .ctp-grid { grid-template-columns: 1fr; gap: 40px; }
}

/* ── Info Column ── */
.ctp-info__label {
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: #F97316;
  margin-bottom: 10px;
}
.ctp-info__heading {
  font-size: 22px;
  font-weight: 800;
  color: #1e2532;
  margin: 0 0 8px;
  letter-spacing: -0.01em;
}
.ctp-info__desc {
  font-size: 14px;
  color: #6b7280;
  line-height: 1.65;
  margin: 0 0 32px;
}
.ctp-cards {
  display: flex;
  flex-direction: column;
  gap: 14px;
}
.ctp-card {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  background: #f8fafc;
  border: 1px solid #e9ecf0;
  border-radius: 14px;
  padding: 18px 20px;
  transition: border-color 0.18s, box-shadow 0.18s;
}
.ctp-card:hover {
  border-color: #fde0c7;
  box-shadow: 0 4px 16px rgba(249,115,22,0.08);
}
.ctp-card__icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: #fff3e8;
  color: #F97316;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  flex-shrink: 0;
  border: 1px solid #fde0c7;
}
.ctp-card__label {
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.07em;
  text-transform: uppercase;
  color: #9ca3af;
  margin-bottom: 4px;
}
.ctp-card__value {
  font-size: 14px;
  font-weight: 600;
  color: #1e2532;
  line-height: 1.5;
  word-break: break-word;
}
.ctp-card__value a {
  color: #1e2532;
  text-decoration: none;
}
.ctp-card__value a:hover { color: #F97316; }

.ctp-divider {
  height: 1px;
  background: #e9ecf0;
  margin: 28px 0;
}
.ctp-social__label {
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 0.07em;
  text-transform: uppercase;
  color: #9ca3af;
  margin-bottom: 12px;
}
.ctp-social {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}
.ctp-social a {
  width: 36px;
  height: 36px;
  border-radius: 9px;
  background: #f3f4f6;
  border: 1px solid #e5e7eb;
  color: #6b7280;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  text-decoration: none;
  transition: background 0.15s, color 0.15s, border-color 0.15s;
}
.ctp-social a:hover {
  background: #F97316;
  color: #fff;
  border-color: #F97316;
}

/* ── Form Column ── */
.ctp-form-wrap {
  background: #fff;
  border: 1px solid #e9ecf0;
  border-radius: 20px;
  padding: 36px 36px 32px;
  box-shadow: 0 4px 24px rgba(30,37,50,0.06);
}
@media (max-width: 575px) {
  .ctp-form-wrap { padding: 24px 18px; }
}
.ctp-form-wrap__title {
  font-size: 20px;
  font-weight: 800;
  color: #1e2532;
  margin: 0 0 6px;
  letter-spacing: -0.01em;
}
.ctp-form-wrap__sub {
  font-size: 13px;
  color: #9ca3af;
  margin: 0 0 28px;
}
.ctp-form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
}
@media (max-width: 575px) {
  .ctp-form-grid { grid-template-columns: 1fr; }
}
.ctp-form-grid .ctp-field--full {
  grid-column: 1 / -1;
}
.ctp-field label {
  display: block;
  font-size: 12px;
  font-weight: 700;
  color: #374151;
  letter-spacing: 0.04em;
  margin-bottom: 6px;
}
.ctp-field input,
.ctp-field textarea {
  width: 100%;
  background: #f8fafc;
  border: 1.5px solid #e5e7eb;
  border-radius: 10px;
  padding: 11px 14px;
  font-size: 14px;
  color: #1e2532;
  font-family: inherit;
  transition: border-color 0.15s, box-shadow 0.15s;
  outline: none;
  resize: none;
}
.ctp-field input::placeholder,
.ctp-field textarea::placeholder {
  color: #b0b8c4;
}
.ctp-field input:focus,
.ctp-field textarea:focus {
  border-color: #F97316;
  box-shadow: 0 0 0 3px rgba(249,115,22,0.1);
  background: #fff;
}
.ctp-field .ctp-error {
  font-size: 12px;
  color: #ef4444;
  margin: 5px 0 0;
}
.ctp-recaptcha {
  grid-column: 1 / -1;
}
.ctp-submit {
  grid-column: 1 / -1;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
  margin-top: 4px;
}
.ctp-submit__note {
  font-size: 12px;
  color: #9ca3af;
  display: flex;
  align-items: center;
  gap: 5px;
}
.ctp-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: #1e2532;
  color: #fff;
  font-size: 14px;
  font-weight: 700;
  padding: 12px 24px;
  border-radius: 10px;
  border: none;
  cursor: pointer;
  text-decoration: none;
  transition: background 0.18s, transform 0.15s;
  font-family: inherit;
}
.ctp-btn:hover {
  background: #F97316;
  transform: translateY(-1px);
}
.ctp-btn svg { flex-shrink: 0; }

/* ── Alert ── */
.ctp-alert {
  border-radius: 10px;
  padding: 12px 16px;
  font-size: 13px;
  font-weight: 600;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 10px;
}
.ctp-alert--success {
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  color: #15803d;
}
.ctp-alert--error {
  background: #fef2f2;
  border: 1px solid #fecaca;
  color: #b91c1c;
}

/* ── Map ── */
.ctp-map {
  border-top: 1px solid #e9ecf0;
}
.ctp-map iframe {
  display: block;
  width: 100%;
  height: 420px;
  filter: grayscale(20%) contrast(1.05);
}
</style>
@endpush


@section('content')
{{-- ── SECCIÓN PRINCIPAL ── --}}
<section class="ctp-section">
  <div class="container">
    <div class="ctp-grid">

      {{-- ── COLUMNA INFO ── --}}
      <div class="ctp-info">
        <p class="ctp-info__label">Información de contacto</p>
        <h2 class="ctp-info__heading">Hablemos</h2>
        <p class="ctp-info__desc">Respondemos en menos de 24 horas en días hábiles.</p>

        <div class="ctp-cards">
          @if(!empty($info->contact_addresses))
          <div class="ctp-card">
            <div class="ctp-card__icon">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            </div>
            <div>
              <p class="ctp-card__label">Dirección</p>
              <p class="ctp-card__value">{{ $info->contact_addresses }}</p>
            </div>
          </div>
          @endif

          @if(!empty($info->contact_mails))
          <div class="ctp-card">
            <div class="ctp-card__icon">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            </div>
            <div>
              <p class="ctp-card__label">Email</p>
              @foreach(explode(',', $info->contact_mails) as $mail)
                <p class="ctp-card__value"><a href="mailto:{{ trim($mail) }}">{{ trim($mail) }}</a></p>
              @endforeach
            </div>
          </div>
          @endif

          @if(!empty($info->contact_numbers))
          <div class="ctp-card">
            <div class="ctp-card__icon">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.8 19.79 19.79 0 01.06 1.18 2 2 0 012.03 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
            </div>
            <div>
              <p class="ctp-card__label">Teléfono</p>
              @foreach(explode(',', $info->contact_numbers) as $phone)
                <p class="ctp-card__value"><a href="tel:{{ trim($phone) }}">{{ trim($phone) }}</a></p>
              @endforeach
            </div>
          </div>
          @endif
        </div>

        @if(count($socialMediaInfos) > 0)
        <div class="ctp-divider"></div>
        <p class="ctp-social__label">Seguinos</p>
        <div class="ctp-social">
          @foreach($socialMediaInfos as $social)
            <a href="{{ $social->url }}" target="_blank" rel="noopener" aria-label="{{ $social->title ?? 'Social' }}">
              <i class="{{ $social->icon }}"></i>
            </a>
          @endforeach
        </div>
        @endif
      </div>

      {{-- ── COLUMNA FORMULARIO ── --}}
      <div class="ctp-form-wrap">
        <h3 class="ctp-form-wrap__title">Envianos un mensaje</h3>
        <p class="ctp-form-wrap__sub">Te respondemos a la brevedad.</p>

        @if(Session::has('success'))
          <div class="ctp-alert ctp-alert--success">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ Session::get('success') }}
          </div>
        @endif
        @if(Session::has('error'))
          <div class="ctp-alert ctp-alert--error">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ Session::get('error') }}
          </div>
          @php Session::forget('error'); @endphp
        @endif

        <form action="{{ route('contact.send_mail') }}" method="post">
          @csrf
          <div class="ctp-form-grid">

            <div class="ctp-field">
              <label for="ctp-name">Nombre completo</label>
              <input type="text" id="ctp-name" name="name"
                placeholder="Tu nombre"
                value="{{ old('name') }}">
              @error('name')<p class="ctp-error">{{ $message }}</p>@enderror
            </div>

            <div class="ctp-field">
              <label for="ctp-email">Email</label>
              <input type="email" id="ctp-email" name="email"
                placeholder="tu@email.com"
                value="{{ old('email') }}">
              @error('email')<p class="ctp-error">{{ $message }}</p>@enderror
            </div>

            <div class="ctp-field ctp-field--full">
              <label for="ctp-subject">Asunto</label>
              <input type="text" id="ctp-subject" name="subject"
                placeholder="¿Sobre qué nos escribís?"
                value="{{ old('subject') }}">
              @error('subject')<p class="ctp-error">{{ $message }}</p>@enderror
            </div>

            <div class="ctp-field ctp-field--full">
              <label for="ctp-message">Mensaje</label>
              <textarea id="ctp-message" name="message" rows="5"
                placeholder="Contanos en qué podemos ayudarte...">{{ old('message') }}</textarea>
              @error('message')<p class="ctp-error">{{ $message }}</p>@enderror
            </div>

            @if($basicInfo->google_recaptcha_status == 1)
            <div class="ctp-recaptcha">
              {!! NoCaptcha::renderJs() !!}
              {!! NoCaptcha::display() !!}
              @error('g-recaptcha-response')<p class="ctp-error">{{ $message }}</p>@enderror
            </div>
            @endif

            <div class="ctp-submit">
              <span class="ctp-submit__note">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                Tus datos están seguros
              </span>
              <button type="submit" class="ctp-btn showLoader">
                Enviar mensaje
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
              </button>
            </div>

          </div>
        </form>
      </div>

    </div>

    @if(!empty(showAd(3)))
      <div class="text-center mt-40">{!! showAd(3) !!}</div>
    @endif

  </div>
</section>

{{-- ── MAPA ── --}}
@if(!empty($contact_info->latitude) && !empty($contact_info->longitude))
<div class="ctp-map">
  <iframe
    loading="lazy"
    src="//maps.google.com/maps?width=100%25&height=420&hl=es&q={{ $contact_info->latitude }},%20{{ $contact_info->longitude }}+({{ $websiteInfo->website_title }})&t=&z=14&ie=UTF8&iwloc=B&output=embed"
    title="Ubicación {{ $websiteInfo->website_title }}"
    frameborder="0"
    scrolling="no"
    aria-hidden="true">
  </iframe>
</div>
@endif
@endsection
