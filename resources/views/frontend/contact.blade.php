@extends('frontend.layout')
@section('body-class', 'contact-page-premium')
@section('pageHeading')
  {{ $pageHeading->contact_page_title ?? __('Contacto') }}
@endsection
@php
  $metaKeywords    = !empty($seo->meta_keyword_contact)    ? $seo->meta_keyword_contact    : '';
  $metaDescription = !empty($seo->meta_description_contact) ? $seo->meta_description_contact : '';
@endphp
@section('meta-keywords',    $metaKeywords)
@section('meta-description', $metaDescription)
@section('canonical', url()->current())
@section('og-url', url()->current())
@section('og-type', 'website')

@push('styles')
<style>
/**
 * Contacto — premium (Apple: vidrio / tipografía / foco · Airbnb: cards / sombras cálidas)
 * Solo aplica con body.contact-page-premium
 */
.contact-page-premium .ctp-premium {
  --ctp-ink: #0f172a;
  --ctp-ink-soft: #475569;
  --ctp-line: rgba(15, 23, 42, 0.07);
  --ctp-accent: #ea580c;
  --ctp-accent-soft: rgba(234, 88, 12, 0.12);
  --ctp-surface: rgba(255, 255, 255, 0.82);
  position: relative;
  z-index: 0;
  padding: clamp(56px, 8vw, 96px) 0 clamp(64px, 9vw, 104px);
  background:
    radial-gradient(ellipse 100% 70% at 0% -18%, rgba(249, 115, 22, 0.07) 0%, transparent 52%),
    radial-gradient(ellipse 80% 55% at 100% 0%, rgba(59, 130, 246, 0.05) 0%, transparent 48%),
    linear-gradient(180deg, #fbfcfd 0%, #f1f5f9 42%, #eef2f7 100%);
}

.contact-page-premium .ctp-premium::before {
  content: '';
  position: absolute;
  inset: 0;
  z-index: -1;
  pointer-events: none;
  opacity: 0.45;
  background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='g'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.8' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23g)' opacity='.35'/%3E%3C/svg%3E");
  mix-blend-mode: multiply;
}

.contact-page-premium .ctp-grid {
  display: grid;
  grid-template-columns: minmax(0, 1fr) minmax(0, 1.42fr);
  gap: clamp(36px, 5vw, 72px);
  align-items: start;
}

@media (max-width: 991px) {
  .contact-page-premium .ctp-grid {
    grid-template-columns: 1fr;
    gap: 40px;
  }
}

/* —— Columna info —— */
.contact-page-premium .ctp-info__label {
  margin: 0 0 10px;
  font-family: var(--heading-font), system-ui, sans-serif;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.2em;
  text-transform: uppercase;
  color: #94a3b8;
}

.contact-page-premium .ctp-info__heading {
  margin: 0 0 12px;
  font-family: var(--heading-font), system-ui, sans-serif;
  font-size: clamp(1.75rem, 1.4vw + 1.1rem, 2.35rem);
  font-weight: 800;
  letter-spacing: -0.045em;
  line-height: 1.1;
  color: var(--ctp-ink);
}

.contact-page-premium .ctp-info__desc {
  margin: 0 0 36px;
  max-width: 36ch;
  font-size: 15px;
  line-height: 1.65;
  color: var(--ctp-ink-soft);
}

.contact-page-premium .ctp-cards {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.contact-page-premium .ctp-card {
  display: flex;
  align-items: flex-start;
  gap: 18px;
  padding: 20px 22px;
  border-radius: 20px;
  background: linear-gradient(165deg, rgba(255, 255, 255, 0.96) 0%, rgba(248, 250, 252, 0.98) 100%);
  border: 1px solid var(--ctp-line);
  box-shadow:
    0 1px 0 rgba(255, 255, 255, 1) inset,
    0 14px 40px rgba(15, 23, 42, 0.06),
    0 2px 6px rgba(15, 23, 42, 0.03);
  transition: transform 0.3s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.3s ease, border-color 0.2s ease;
}

.contact-page-premium .ctp-card:nth-child(1) { animation: ctp-reveal 0.55s cubic-bezier(0.22, 1, 0.36, 1) 0.05s both; }
.contact-page-premium .ctp-card:nth-child(2) { animation: ctp-reveal 0.55s cubic-bezier(0.22, 1, 0.36, 1) 0.1s both; }
.contact-page-premium .ctp-card:nth-child(3) { animation: ctp-reveal 0.55s cubic-bezier(0.22, 1, 0.36, 1) 0.15s both; }

.contact-page-premium .ctp-card:hover {
  transform: translateY(-3px);
  border-color: rgba(249, 115, 22, 0.2);
  box-shadow:
    0 1px 0 rgba(255, 255, 255, 1) inset,
    0 22px 56px rgba(15, 23, 42, 0.09),
    0 8px 16px rgba(234, 88, 12, 0.06);
}

.contact-page-premium .ctp-card__icon {
  flex-shrink: 0;
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 14px;
  color: var(--ctp-accent);
  background: linear-gradient(145deg, #fff7ed 0%, #ffedd5 100%);
  border: 1px solid rgba(249, 115, 22, 0.22);
  box-shadow: 0 1px 0 rgba(255, 255, 255, 0.9) inset;
}

.contact-page-premium .ctp-card__label {
  margin: 0 0 5px;
  font-family: var(--heading-font), system-ui, sans-serif;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: #94a3b8;
}

.contact-page-premium .ctp-card__value {
  margin: 0;
  font-size: 14px;
  font-weight: 600;
  line-height: 1.55;
  color: var(--ctp-ink);
  word-break: break-word;
}

.contact-page-premium .ctp-card__value a {
  color: inherit;
  text-decoration: none;
  border-bottom: 1px solid transparent;
  transition: color 0.2s ease, border-color 0.2s ease;
}

.contact-page-premium .ctp-card__value a:hover {
  color: var(--ctp-accent);
  border-bottom-color: rgba(234, 88, 12, 0.35);
}

.contact-page-premium .ctp-divider {
  height: 1px;
  margin: 30px 0;
  background: linear-gradient(90deg, transparent, rgba(15, 23, 42, 0.08), transparent);
}

.contact-page-premium .ctp-social__label {
  margin: 0 0 14px;
  font-family: var(--heading-font), system-ui, sans-serif;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  color: #94a3b8;
}

.contact-page-premium .ctp-social {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.contact-page-premium .ctp-social a {
  width: 44px;
  height: 44px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  font-size: 15px;
  color: var(--ctp-ink-soft);
  text-decoration: none;
  background: rgba(255, 255, 255, 0.65);
  border: 1px solid var(--ctp-line);
  box-shadow: 0 1px 0 rgba(255, 255, 255, 1) inset, 0 8px 20px rgba(15, 23, 42, 0.05);
  transition: transform 0.22s ease, background 0.22s ease, color 0.22s ease, border-color 0.22s ease, box-shadow 0.22s ease;
}

.contact-page-premium .ctp-social a:hover {
  color: #fff;
  background: linear-gradient(135deg, #f97316 0%, var(--ctp-accent) 100%);
  border-color: transparent;
  transform: translateY(-4px);
  box-shadow: 0 12px 28px rgba(234, 88, 12, 0.35);
}

.contact-page-premium .ctp-social a:focus-visible {
  outline: 2px solid rgba(234, 88, 12, 0.55);
  outline-offset: 3px;
}

/* —— Formulario —— */
.contact-page-premium .ctp-form-wrap {
  position: relative;
  padding: clamp(28px, 4vw, 44px) clamp(24px, 3.5vw, 44px) clamp(26px, 3.5vw, 40px);
  border-radius: clamp(22px, 2.5vw, 30px);
  background: var(--ctp-surface);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.85);
  box-shadow:
    0 1px 0 rgba(255, 255, 255, 1) inset,
    0 0 0 1px rgba(15, 23, 42, 0.04),
    0 28px 64px rgba(15, 23, 42, 0.1),
    0 10px 24px rgba(15, 23, 42, 0.05);
  animation: ctp-reveal 0.65s cubic-bezier(0.22, 1, 0.36, 1) 0.12s both;
}

.contact-page-premium .ctp-form-wrap::after {
  content: '';
  position: absolute;
  inset: 0;
  border-radius: inherit;
  pointer-events: none;
  background: radial-gradient(ellipse 90% 45% at 50% -10%, var(--ctp-accent-soft) 0%, transparent 55%);
  z-index: 0;
}

.contact-page-premium .ctp-form-wrap > * {
  position: relative;
  z-index: 1;
}

.contact-page-premium .ctp-form-wrap__title {
  margin: 0 0 8px;
  font-family: var(--heading-font), system-ui, sans-serif;
  font-size: clamp(1.2rem, 0.6vw + 1rem, 1.45rem);
  font-weight: 800;
  letter-spacing: -0.035em;
  color: var(--ctp-ink);
}

.contact-page-premium .ctp-form-wrap__sub {
  margin: 0 0 28px;
  font-size: 14px;
  line-height: 1.5;
  color: #64748b;
}

.contact-page-premium .ctp-form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

@media (max-width: 575px) {
  .contact-page-premium .ctp-form-grid {
    grid-template-columns: 1fr;
  }
}

.contact-page-premium .ctp-form-grid .ctp-field--full {
  grid-column: 1 / -1;
}

.contact-page-premium .ctp-field label {
  display: block;
  margin-bottom: 7px;
  font-family: var(--heading-font), system-ui, sans-serif;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: #64748b;
}

.contact-page-premium .ctp-field input,
.contact-page-premium .ctp-field textarea {
  width: 100%;
  padding: 13px 16px;
  font-size: 15px;
  font-family: inherit;
  color: var(--ctp-ink);
  background: rgba(248, 250, 252, 0.95);
  border: 1px solid rgba(15, 23, 42, 0.08);
  border-radius: 14px;
  outline: none;
  resize: vertical;
  transition: background 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
  box-shadow: 0 1px 0 rgba(255, 255, 255, 0.9) inset;
}

.contact-page-premium .ctp-field input::placeholder,
.contact-page-premium .ctp-field textarea::placeholder {
  color: #94a3b8;
}

.contact-page-premium .ctp-field input:hover,
.contact-page-premium .ctp-field textarea:hover {
  border-color: rgba(15, 23, 42, 0.12);
}

.contact-page-premium .ctp-field input:focus,
.contact-page-premium .ctp-field textarea:focus {
  background: #fff;
  border-color: rgba(234, 88, 12, 0.45);
  box-shadow:
    0 0 0 4px rgba(249, 115, 22, 0.11),
    0 1px 0 rgba(255, 255, 255, 1) inset;
}

.contact-page-premium .ctp-field .ctp-error {
  margin: 6px 0 0;
  font-size: 12px;
  font-weight: 600;
  color: #dc2626;
}

.contact-page-premium .ctp-recaptcha {
  grid-column: 1 / -1;
}

.contact-page-premium .ctp-submit {
  grid-column: 1 / -1;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
  padding-top: 8px;
  margin-top: 4px;
}

.contact-page-premium .ctp-submit__note {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 12px;
  color: #94a3b8;
}

.contact-page-premium .ctp-btn {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 14px 26px;
  font-family: var(--heading-font), system-ui, sans-serif;
  font-size: 14px;
  font-weight: 700;
  letter-spacing: -0.02em;
  color: #fff;
  cursor: pointer;
  border: none;
  border-radius: 14px;
  text-decoration: none;
  background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
  box-shadow:
    0 1px 0 rgba(255, 255, 255, 0.18) inset,
    0 14px 32px rgba(15, 23, 42, 0.25);
  transition: transform 0.22s ease, box-shadow 0.22s ease, filter 0.22s ease;
}

.contact-page-premium .ctp-btn:hover {
  filter: brightness(1.06);
  transform: translateY(-2px);
  box-shadow:
    0 1px 0 rgba(255, 255, 255, 0.22) inset,
    0 18px 40px rgba(15, 23, 42, 0.28);
}

.contact-page-premium .ctp-btn:focus-visible {
  outline: 2px solid rgba(249, 115, 22, 0.65);
  outline-offset: 3px;
}

.contact-page-premium .ctp-btn svg {
  flex-shrink: 0;
}

/* —— Alertas —— */
.contact-page-premium .ctp-alert {
  margin-bottom: 22px;
  padding: 14px 18px;
  display: flex;
  align-items: flex-start;
  gap: 12px;
  font-size: 14px;
  font-weight: 600;
  line-height: 1.45;
  border-radius: 14px;
}

.contact-page-premium .ctp-alert svg {
  flex-shrink: 0;
  margin-top: 1px;
}

.contact-page-premium .ctp-alert--success {
  background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
  border: 1px solid #86efac;
  color: #166534;
}

.contact-page-premium .ctp-alert--error {
  background: linear-gradient(135deg, #fef2f2 0%, #fff1f2 100%);
  border: 1px solid #fecaca;
  color: #b91c1c;
}

/* —— Mapa —— */
.contact-page-premium .ctp-map {
  position: relative;
  border-top: 1px solid rgba(15, 23, 42, 0.06);
  background: #e2e8f0;
}

.contact-page-premium .ctp-map iframe {
  display: block;
  width: 100%;
  height: min(52vh, 460px);
  border: 0;
  filter: grayscale(12%) contrast(1.04) saturate(0.92);
}

@keyframes ctp-reveal {
  from {
    opacity: 0;
    transform: translateY(14px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@media (prefers-reduced-motion: reduce) {
  .contact-page-premium .ctp-card,
  .contact-page-premium .ctp-form-wrap {
    animation: none;
  }
  .contact-page-premium .ctp-card:hover,
  .contact-page-premium .ctp-social a:hover,
  .contact-page-premium .ctp-btn:hover {
    transform: none;
  }
}
</style>
@endpush


@section('content')
@php
  $contactAddresses = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $info->contact_addresses ?? ''))));
  $contactMails = array_values(array_filter(array_map('trim', explode(',', $info->contact_mails ?? ''))));
  $contactPhones = array_values(array_filter(array_map('trim', explode(',', $info->contact_numbers ?? ''))));
  $validSocials = collect($socialMediaInfos ?? [])->filter(function ($social) {
      $url = trim((string) ($social->url ?? ''));
      return $url !== '';
  });
@endphp
{{-- ── SECCIÓN PRINCIPAL — contacto premium ── --}}
<section class="ctp-section ctp-premium" id="contacto">
  <div class="container">
    <div class="ctp-grid">

      {{-- ── COLUMNA INFO ── --}}
      <div class="ctp-info">
        <p class="ctp-info__label">Información de contacto</p>
        <h2 class="ctp-info__heading">Hablemos</h2>
        <p class="ctp-info__desc">Si necesitás ayuda con una compra, una entrada o tu evento, escribinos y te respondemos a la brevedad.</p>

        <div class="ctp-cards">
          @if(!empty($contactAddresses))
          <div class="ctp-card">
            <div class="ctp-card__icon">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            </div>
            <div>
              <p class="ctp-card__label">Dirección</p>
              @foreach($contactAddresses as $address)
                <p class="ctp-card__value">{{ $address }}</p>
              @endforeach
            </div>
          </div>
          @endif

          @if(!empty($contactMails))
          <div class="ctp-card">
            <div class="ctp-card__icon">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            </div>
            <div>
              <p class="ctp-card__label">Email</p>
              @foreach($contactMails as $mail)
                <p class="ctp-card__value"><a href="mailto:{{ $mail }}">{{ $mail }}</a></p>
              @endforeach
            </div>
          </div>
          @endif

          @if(!empty($contactPhones))
          <div class="ctp-card">
            <div class="ctp-card__icon">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.8 19.79 19.79 0 01.06 1.18 2 2 0 012.03 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
            </div>
            <div>
              <p class="ctp-card__label">Teléfono</p>
              @foreach($contactPhones as $phone)
                <p class="ctp-card__value"><a href="tel:{{ $phone }}">{{ $phone }}</a></p>
              @endforeach
            </div>
          </div>
          @endif

        </div>

        @if($validSocials->isNotEmpty())
        <div class="ctp-divider"></div>
        <p class="ctp-social__label">Seguinos</p>
        <div class="ctp-social">
          @foreach($validSocials as $social)
            <a href="{{ trim((string) $social->url) === '#' ? 'javascript:void(0)' : $social->url }}" target="_blank" rel="noopener" aria-label="{{ $social->title ?? 'Social' }}" title="{{ $social->url }}">
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
    src="https://maps.google.com/maps?width=100%25&height=420&hl=es&q={{ $contact_info->latitude }},%20{{ $contact_info->longitude }}+({{ $websiteInfo->website_title }})&t=&z=14&ie=UTF8&iwloc=B&output=embed"
    title="Ubicación {{ $websiteInfo->website_title }}"
    frameborder="0"
    scrolling="no"
    aria-hidden="true">
  </iframe>
</div>
@endif
@endsection
