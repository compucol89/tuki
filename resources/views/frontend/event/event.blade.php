@extends('frontend.layout')

@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/front/css/events.css') }}">
@endpush

@section('pageHeading', 'Eventos en Argentina · Entradas y Tickets Online')

@php
  $metaKeywords    = !empty($seo->meta_keyword_event)    ? $seo->meta_keyword_event    : 'eventos, entradas, tickets, conciertos, shows, teatro, Argentina';
  $metaDescription = !empty($seo->meta_description_event) ? $seo->meta_description_event : 'Encontrá los mejores eventos en Argentina. Comprá entradas y tickets online de forma fácil, rápida y segura en Tukipass.';
  $ogImage = asset('assets/admin/img/' . $basicInfo->breadcrumb);
@endphp

@section('meta-keywords',    "{{ $metaKeywords }}")
@section('meta-description', "$metaDescription")
@section('og-title',       'Eventos en Argentina · Entradas y Tickets Online | Tukipass')
@section('og-description', $metaDescription)
@section('og-image',       $ogImage)
@section('og-type',        'website')
@section('canonical',      url()->current())
@section('og-url',         url()->current())

@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/front/css/daterangepicker.css') }}">
@endpush

{{-- ─── HERO — premium (collage + capas editoriales) ─── --}}
@section('hero-section')
<section class="hero-section hero-collage-section hero-collage-section--premium" id="heroSection" aria-labelledby="heroHeading">

  <div class="hero-slideshow" id="heroCollageBg">
    @forelse($heroSlideUrls ?? [] as $slideUrl)
      <div class="hero-slide" style="background-image: url('{{ $slideUrl }}');"></div>
    @empty
      <div class="hero-slide" style="background-image: url('{{ asset('assets/admin/img/' . $basicInfo->breadcrumb) }}');"></div>
    @endforelse
  </div>

  <div class="hero-overlay hero-overlay--premium" aria-hidden="true"></div>
  <div class="hero-vignette" aria-hidden="true"></div>
  <div class="hero-ambient" aria-hidden="true">
    <span class="hero-ambient__orb hero-ambient__orb--a"></span>
    <span class="hero-ambient__orb hero-ambient__orb--b"></span>
    <span class="hero-ambient__orb hero-ambient__orb--c"></span>
  </div>
  <div class="hero-noise" aria-hidden="true"></div>

  <div class="container hero-content-wrapper">
    <div class="hero-content hero-content--premium text-center">
      <h1 id="heroHeading">Todos los eventos,<br>en un solo lugar</h1>
      <p class="hero-lede">Conciertos, shows, teatro, deportes y más. Comprá tus entradas fácil, rápido y seguro.</p>
      <ul class="hero-trust" aria-label="{{ __('Beneficios') }}">
        <li>{{ __('Compra segura') }}</li>
        <li>{{ __('Entradas oficiales') }}</li>
        <li>{{ __('Soporte en tu idioma') }}</li>
      </ul>
    </div>
  </div>

</section>
@endsection

@push('styles')
<style>
  /* ─── EVENTOS — rediseño premium radical ─── */

  /* Fondo ultra limpio: blanco puro con acento naranja sutil */
  .ev-explore--premium {
    background: #ffffff;
  }
  .ev-explore--premium .ev-explore__ambient {
    opacity: 0.15;
    background: radial-gradient(ellipse 80% 50% at 50% 0%, rgba(249, 115, 22, 0.08) 0%, transparent 60%);
  }

  /* Barra sticky: blanco sólido con borde fino */
  .ev-explore--premium .evf-bar-wrap--premium {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px) saturate(1.1);
    -webkit-backdrop-filter: blur(20px) saturate(1.1);
    border-bottom: 1px solid rgba(226, 232, 240, 0.8);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    padding: 28px 0 24px;
  }

  /* Header: distribución limpia */
  .ev-explore--premium .evf-panel__head {
    align-items: flex-end;
    justify-content: space-between;
    margin-bottom: 24px;
    padding-bottom: 20px;
    border-bottom: 1px solid rgba(226, 232, 240, 0.6);
  }
  .ev-explore--premium .evf-panel__eyebrow {
    display: none;
  }
  .ev-explore--premium .evf-panel__title {
    font-size: clamp(1.5rem, 3vw, 2rem);
    font-weight: 800;
    letter-spacing: -0.03em;
    color: #0f172a;
    line-height: 1.1;
  }

  /* Badge: número grande + label chica, alineado abajo */
  .ev-explore--premium .evf-result-badge {
    background: transparent;
    box-shadow: none;
    color: #64748b;
    padding: 0;
    gap: 4px;
    align-items: baseline;
  }
  .ev-explore--premium .evf-result-badge__num {
    font-size: 1.5rem;
    font-weight: 800;
    color: #f97316;
    line-height: 1;
  }
  .ev-explore--premium .evf-result-badge__label {
    font-size: 13px;
    font-weight: 500;
    color: #94a3b8;
    text-transform: none;
    letter-spacing: 0;
    opacity: 1;
  }

  /* ─── FORMULARIO: tarjeta única (Airbnb style) ─── */
  .ev-explore--premium .evf-form {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    gap: 0;
  }

  .ev-explore--premium .evf-form__search {
    display: flex;
    flex-wrap: nowrap;
    align-items: center;
    gap: 16px;
    padding: 12px 14px;
    min-height: 56px;
  }

  /* Campo de búsqueda: crece; separador suave antes de CTAs */
  .ev-explore--premium .evf-field--search {
    flex: 1 1 auto;
    min-width: min(100%, 12rem);
    min-height: 48px;
    padding: 0 14px 0 10px;
    margin: 0;
    border: none;
    border-radius: 12px;
    border-inline-end: 1px solid #e2e8f0;
    background: transparent;
    box-shadow: none;
  }
  .ev-explore--premium .evf-field--search:focus-within {
    background: #f8fafc;
    box-shadow: none;
    border: none;
  }
  .ev-explore--premium .evf-field--search .evf-icon {
    color: #94a3b8;
    width: 20px;
    height: 20px;
  }
  .ev-explore--premium .evf-field--search .evf-input {
    font-size: 15px;
    font-weight: 500;
  }
  .ev-explore--premium .evf-field--search .evf-input::placeholder {
    color: #94a3b8;
    font-weight: 400;
  }

  .ev-explore--premium .evf-form__search > .evf-btn--primary {
    margin-inline-start: auto;
    flex-shrink: 0;
  }

  .ev-explore--premium .evf-form__search > .evf-clear {
    flex-shrink: 0;
  }

  /* ─── BOTÓN BUSCAR: exactamente igual que "Organizador" en header ─── */
  .ev-explore--premium .evf-btn--primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 48px;
    padding: 0 28px;
    border: 1px solid rgba(234, 88, 12, 0.92);
    border-radius: 10px;
    font-family: var(--heading-font);
    font-size: 14px;
    font-weight: 700;
    letter-spacing: -0.01em;
    color: #ffffff;
    cursor: pointer;
    background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
    box-shadow: none;
    transition: border-color 0.18s ease, filter 0.18s ease, transform 0.15s ease;
    white-space: nowrap;
  }
  .ev-explore--premium .evf-btn--primary:hover,
  .ev-explore--premium .evf-btn--primary:focus {
    background: linear-gradient(135deg, #fb923c 0%, #f97316 100%);
    color: #ffffff;
    border-color: rgba(249, 115, 22, 0.95);
    box-shadow: none;
    filter: brightness(1.02);
    transform: translateY(-1px);
  }
  .ev-explore--premium .evf-btn--primary:active {
    transform: scale(0.98) translateY(0);
  }
  .ev-explore--premium .evf-btn--primary:focus-visible {
    outline: 2px solid #1e2532;
    outline-offset: 3px;
  }
  .ev-explore--premium .evf-clear:focus-visible {
    outline: 2px solid #f97316;
    outline-offset: 2px;
  }
  .ev-explore--premium .evf-input:focus-visible,
  .ev-explore--premium .evf-select:focus-visible {
    outline: 2px solid #f97316;
    outline-offset: 2px;
  }
  .ev-explore--premium .evf-seg__opt:focus-visible {
    outline: 2px solid #f97316;
    outline-offset: 2px;
    z-index: 1;
    position: relative;
  }
  @media (prefers-reduced-motion: reduce) {
    .ev-explore--premium .evf-btn--primary,
    .ev-explore--premium .evf-clear,
    .ev-explore--premium .evf-seg__opt {
      transition: none !important;
    }
    .ev-explore--premium .evf-btn--primary:hover,
    .ev-explore--premium .evf-btn--primary:focus,
    .ev-explore--premium .evf-btn--primary:active {
      transform: none;
    }
  }

  /* Botón limpiar: outline sutil */
  .ev-explore--premium .evf-clear {
    min-height: 48px;
    padding: 0 18px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    color: #64748b;
    background: transparent;
    border: 1px solid #e2e8f0;
    transition: all 0.2s ease;
  }
  .ev-explore--premium .evf-clear:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
    color: #475569;
  }

  /* ─── FILTROS SECUNDARIOS: fila horizontal limpia ─── */
  .ev-explore--premium .evf-form__meta {
    display: flex;
    flex-wrap: nowrap;
    gap: 0;
    padding: 0;
    border-top: 1px solid #e2e8f0;
    margin-top: 0;
    border-radius: 0 0 10px 10px;
    overflow: hidden;
  }

  .ev-explore--premium .evf-field-block {
    flex: 1 1 0;
    flex-direction: row;
    align-items: center;
    gap: 0;
    padding: 10px 14px;
    border-right: 1px solid #e2e8f0;
    min-width: 0;
  }
  .ev-explore--premium .evf-field-block:last-child {
    border-right: none;
  }

  .ev-explore--premium .evf-lbl {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #94a3b8;
    margin: 0 10px 0 0;
    white-space: nowrap;
    flex-shrink: 0;
  }

  .ev-explore--premium .evf-field {
    flex: 1;
    min-height: 36px;
    padding: 0;
    background: transparent;
    border: none;
    border-radius: 0;
    gap: 8px;
  }
  .ev-explore--premium .evf-field:focus-within {
    background: transparent;
    box-shadow: none;
    border: none;
  }
  .ev-explore--premium .evf-field .evf-icon {
    color: #94a3b8;
    width: 16px;
    height: 16px;
    flex-shrink: 0;
  }
  .ev-explore--premium .evf-input,
  .ev-explore--premium .evf-select {
    font-size: 14px;
    font-weight: 500;
    color: #0f172a;
  }
  .ev-explore--premium .evf-input::placeholder {
    color: #94a3b8;
    font-weight: 400;
  }

  /* ─── TOOLBAR: segmentos planos y limpios ─── */
  .ev-explore--premium .evf-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 20px;
    margin-top: 20px;
    padding-top: 0;
    border-top: none;
  }
  .ev-explore--premium .evf-seg__label {
    font-size: 11px;
    font-weight: 700;
    color: #94a3b8;
    letter-spacing: 0.06em;
    margin-bottom: 6px;
  }
  .ev-explore--premium .evf-seg__track {
    display: flex;
    gap: 2px;
    padding: 3px;
    background: #f1f5f9;
    border-radius: 10px;
    box-shadow: none;
  }
  .ev-explore--premium .evf-seg__opt {
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    color: #64748b;
    transition: all 0.15s ease;
  }
  .ev-explore--premium .evf-seg__opt:hover {
    color: #0f172a;
    background: rgba(255,255,255,0.5);
  }
  .ev-explore--premium .evf-seg__opt.is-active {
    background: #ffffff;
    color: #0f172a;
    font-weight: 700;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
  }

  /* ─── RESPONSIVE ─── */
  @media (max-width: 991px) {
    .ev-explore--premium .evf-form__search {
      flex-wrap: wrap;
      row-gap: 14px;
      padding: 12px;
    }
    .ev-explore--premium .evf-field--search {
      flex: 1 1 100%;
      min-width: 0;
      width: 100%;
      border-inline-end: none;
      padding: 0 4px 14px 4px;
      border-bottom: 1px solid #e2e8f0;
      border-radius: 0;
    }
    .ev-explore--premium .evf-form__search > .evf-btn--primary {
      margin-inline-start: 0;
    }
    .ev-explore--premium .evf-form__meta {
      flex-wrap: wrap;
    }
    .ev-explore--premium .evf-field-block {
      flex: 1 1 50%;
      border-right: none;
      border-bottom: 1px solid #e2e8f0;
    }
    .ev-explore--premium .evf-field-block:last-child {
      border-bottom: none;
    }
  }

  @media (max-width: 767px) {
    .ev-explore--premium .evf-panel__head {
      flex-direction: column;
      align-items: flex-start;
      gap: 8px;
    }
    .ev-explore--premium .evf-form__meta {
      flex-direction: column;
    }
    .ev-explore--premium .evf-field-block {
      flex: 1 1 auto;
      border-right: none;
      border-bottom: 1px solid #e2e8f0;
    }
    .ev-explore--premium .evf-field {
      min-height: 44px;
    }
    .ev-explore--premium .evf-field-block--loc,
    .ev-explore--premium .evf-field-block--date {
      display: flex !important;
    }
    .ev-explore--premium .evf-toolbar {
      flex-direction: column;
      align-items: stretch;
      gap: 12px;
    }
    .ev-explore--premium .evf-seg {
      flex: 1 1 auto;
    }
  }

  @media (max-width: 575px) {
    .ev-explore--premium .evf-field-block {
      flex: 1 1 100%;
    }
    .ev-explore--premium .evf-field {
      min-height: 44px;
    }
    .ev-explore--premium .evf-seg__opt {
      min-height: 44px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }
  }
</style>
@endpush

{{-- ─── CONTENT ─── --}}
@section('content')

{{-- Explorar eventos: fondo único (filtros + grid) --}}
@php
  $evf_total = $information['events']->total();
  $evf_base  = request()->except(['event', 'pricing']);
@endphp
<div class="ev-explore ev-explore--premium">
  <div class="ev-explore__ambient" aria-hidden="true"></div>

<div class="evf-bar-wrap evf-bar-wrap--premium">
  <div class="container">
      <header class="evf-panel__head">
        <div class="evf-panel__intro">
          <p class="evf-panel__eyebrow">{{ __('Explorar') }}</p>
          <h2 class="evf-panel__title" id="evf-form-heading">{{ __('Filtrá y encontrá eventos') }}</h2>
        </div>
        <div class="evf-result-badge" aria-live="polite">
          <span class="evf-result-badge__num">{{ $evf_total }}</span>
          <span class="evf-result-badge__label">{{ $evf_total === 1 ? __('evento') : __('eventos') }}</span>
        </div>
      </header>

      <form action="{{ route('events') }}" method="GET" class="evf-form" id="evfForm" novalidate role="search" aria-labelledby="evf-form-heading">
        @if (request('event'))
          <input type="hidden" name="event" value="{{ request('event') }}">
        @endif
        @if (request('pricing'))
          <input type="hidden" name="pricing" value="{{ request('pricing') }}">
        @endif
        @if (request('min'))
          <input type="hidden" name="min" value="{{ request('min') }}">
        @endif
        @if (request('max'))
          <input type="hidden" name="max" value="{{ request('max') }}">
        @endif

        <div class="evf-form__search">
          <div class="evf-field evf-field--search">
            <label for="evf-search" class="sr-only">{{ __('Buscar por nombre del evento') }}</label>
            <svg class="evf-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="search" name="search-input" id="evf-search" class="evf-input"
                   value="{{ request('search-input') }}"
                   placeholder="{{ __('Nombre del evento…') }}"
                   autocomplete="off"
                   enterkeyhint="search">
          </div>
          <button type="submit" class="evf-btn evf-btn--primary">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            {{ __('Buscar') }}
          </button>
          @if (request()->hasAny(['search-input', 'category', 'location', 'dates', 'event', 'pricing', 'min', 'max']))
            <a href="{{ route('events') }}" class="evf-clear" aria-label="{{ __('Limpiar todos los filtros de búsqueda') }}">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
              <span class="evf-clear__txt">{{ __('Limpiar') }}</span>
            </a>
          @endif
        </div>

        <div class="evf-form__meta">
          <div class="evf-field-block">
            <label class="evf-lbl" for="evf-category">{{ __('Categoría') }}</label>
            <div class="evf-field evf-field--select">
              <svg class="evf-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
              <select name="category" id="evf-category" class="evf-select">
                <option value="">{{ __('Todas las categorías') }}</option>
                @foreach ($categories as $cat)
                  <option value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="evf-field-block evf-field-block--loc">
            <label class="evf-lbl" for="evf-location">{{ __('Ubicación') }}</label>
            <div class="evf-field evf-field--loc">
              <svg class="evf-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
              <input type="text" name="location" id="evf-location" class="evf-input"
                     value="{{ request('location') }}"
                     placeholder="{{ __('Ciudad o país') }}"
                     autocomplete="address-level2">
            </div>
          </div>

          <div class="evf-field-block evf-field-block--date">
            <label class="evf-lbl" for="evf-dates">{{ __('Fecha') }}</label>
            <div class="evf-field evf-field--date">
              <svg class="evf-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              <input type="text" name="dates" id="evf-dates" class="evf-input"
                     value="{{ request('dates') }}"
                     placeholder="{{ __('Rango de fechas') }}"
                     autocomplete="off" readonly
                     aria-describedby="evf-dates-hint">
              <span id="evf-dates-hint" class="sr-only">{{ __('Se abre un calendario para elegir un rango de fechas.') }}</span>
            </div>
          </div>
        </div>
      </form>

      <div class="evf-toolbar" aria-label="{{ __('Filtros rápidos') }}">
        <div class="evf-seg">
          <span class="evf-seg__label" id="evf-lbl-format">{{ __('Formato') }}</span>
          <div class="evf-seg__track" role="group" aria-labelledby="evf-lbl-format">
            <a href="{{ route('events', array_merge($evf_base, ['event' => ''])) }}"
               class="evf-seg__opt {{ ! request('event') ? 'is-active' : '' }}"
               @if (! request('event')) aria-current="page" @endif>{{ __('Todos') }}</a>
            <a href="{{ route('events', array_merge($evf_base, ['event' => 'online'])) }}"
               class="evf-seg__opt {{ request('event') == 'online' ? 'is-active' : '' }}"
               @if (request('event') == 'online') aria-current="page" @endif>{{ __('En línea') }}</a>
            <a href="{{ route('events', array_merge($evf_base, ['event' => 'venue'])) }}"
               class="evf-seg__opt {{ request('event') == 'venue' ? 'is-active' : '' }}"
               @if (request('event') == 'venue') aria-current="page" @endif>{{ __('Presencial') }}</a>
          </div>
        </div>
        <div class="evf-seg">
          <span class="evf-seg__label" id="evf-lbl-price">{{ __('Precio') }}</span>
          <div class="evf-seg__track" role="group" aria-labelledby="evf-lbl-price">
            <a href="{{ route('events', array_merge(request()->except('pricing'), ['pricing' => ''])) }}"
               class="evf-seg__opt {{ ! request('pricing') ? 'is-active' : '' }}"
               @if (! request('pricing')) aria-current="page" @endif>{{ __('Todos') }}</a>
            <a href="{{ route('events', array_merge(request()->except('pricing'), ['pricing' => 'free'])) }}"
               class="evf-seg__opt evf-seg__opt--free {{ request('pricing') == 'free' ? 'is-active' : '' }}"
               @if (request('pricing') == 'free') aria-current="page" @endif>{{ __('Gratis') }}</a>
            <a href="{{ route('events', array_merge(request()->except('pricing'), ['pricing' => 'paid'])) }}"
               class="evf-seg__opt {{ request('pricing') == 'paid' ? 'is-active' : '' }}"
               @if (request('pricing') == 'paid') aria-current="page" @endif>{{ __('De pago') }}</a>
          </div>
        </div>
      </div>
  </div>
</div>

{{-- Grid de eventos — capa premium (editorial / Apple × hospitality) --}}
<section class="ev-listing ev-listing--premium">
  <div class="container ev-listing__inner">

    @if (count($information['events']) > 0)
      <div class="row">
        @foreach ($information['events'] as $event)
          <div class="col-lg-4 col-md-6 ev-card-col item motivational">
            @include('frontend.partials.event-card', ['badgeMap' => $information['badgeMap'] ?? []])
          </div>
        @endforeach
      </div>
    @else
      <div class="ev-empty">
        <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <p>{{ __('No se encontraron eventos con esos filtros') }}</p>
        <a href="{{ route('events') }}" class="evf-btn evf-btn--primary">{{ __('Limpiar filtros') }}</a>
      </div>
    @endif

    {{-- Paginación --}}
    <div class="ev-pagination mt-5">
      {{ $information['events']->links() }}
    </div>

    @if (!empty(showAd(3)))
      <div class="text-center mt-4">{!! showAd(3) !!}</div>
    @endif

  </div>
</section>

</div>{{-- /.ev-explore --}}

{{-- Form oculto para filtros JS (precio) --}}
<form id="filtersForm" class="d-none" action="{{ route('events') }}" method="GET" aria-hidden="true">
  <input type="hidden" id="category-id"  name="category"    value="{{ request('category','') }}">
  <input type="hidden" id="event"         name="event"       value="{{ request('event','') }}">
  <input type="hidden" id="min-id"        name="min"         value="{{ request('min','') }}">
  <input type="hidden" id="max-id"        name="max"         value="{{ request('max','') }}">
  <input type="hidden"                    name="search-input" value="{{ request('search-input','') }}">
  <input type="hidden"                    name="location"    value="{{ request('location','') }}">
  <input type="hidden" id="dates-id"      name="dates"       value="{{ request('dates','') }}">
  <input type="hidden"                    name="pricing"     value="{{ request('pricing','') }}">
  <button type="submit" id="submitBtn"></button>
</form>

@endsection

@push('scripts')
<script src="{{ asset('assets/front/js/moment.min.js') }}" defer></script>
<script src="{{ asset('assets/front/js/daterangepicker.min.js') }}" defer></script>
<script>
  // Hero: misma lógica que home (crossfade + parallax con IntersectionObserver)
  (function() {
    var slides = Array.from(document.querySelectorAll('#heroCollageBg .hero-slide'));
    var n = slides.length;
    if (n === 0) return;

    slides[0].style.opacity = '1';
    slides[0].style.zIndex  = '0';

    if (n === 1) return;

    var cur = 0;

    function nextSlide() {
      var nxt  = (cur + 1) % n;
      var prev = cur;
      slides[prev].style.zIndex = '0';
      slides[nxt].style.zIndex  = '1';
      slides[nxt].style.transition = 'opacity 1.2s ease-in-out';
      slides[nxt].style.opacity    = '1';
      setTimeout(function() {
        slides[prev].style.transition = 'none';
        slides[prev].style.opacity    = '0';
      }, 1200);
      cur = nxt;
    }

    setInterval(nextSlide, 5000);

    var hero = document.getElementById('heroSection');
    var bg   = document.getElementById('heroCollageBg');
    if (!hero || !bg) return;

    var tx = 0, ty = 0, cx = 0, cy = 0;
    var rafId = null;
    var heroRect = hero.getBoundingClientRect();

    window.addEventListener('resize', function() { heroRect = hero.getBoundingClientRect(); }, { passive: true });

    hero.addEventListener('mousemove', function(e) {
      tx = -(e.clientX - heroRect.left) / heroRect.width  * 14 + 7;
      ty = -(e.clientY - heroRect.top)  / heroRect.height * 14 + 7;
    }, { passive: true });

    hero.addEventListener('mouseleave', function() { tx = 0; ty = 0; }, { passive: true });

    function parallaxLoop() {
      cx += (tx - cx) * 0.04;
      cy += (ty - cy) * 0.04;
      bg.style.transform = 'translate(' + cx.toFixed(2) + 'px,' + cy.toFixed(2) + 'px)';
      rafId = requestAnimationFrame(parallaxLoop);
    }

    var observer = new IntersectionObserver(function(entries) {
      if (entries[0].isIntersecting) {
        if (!rafId) rafId = requestAnimationFrame(parallaxLoop);
      } else {
        if (rafId) { cancelAnimationFrame(rafId); rafId = null; }
        cx = 0; cy = 0; tx = 0; ty = 0;
        bg.style.transform = 'translate(0px,0px)';
      }
    }, { threshold: 0 });

    observer.observe(hero);
  })();

  document.addEventListener('DOMContentLoaded', function() {
    var evfForm = document.getElementById('evfForm');

    // Daterangepicker
    $('#evf-dates').daterangepicker({
      autoUpdateInput: false,
      locale: {
        cancelLabel: @json(__('Limpiar')),
        applyLabel: @json(__('Aplicar')),
        format: 'YYYY-MM-DD',
        separator: @json(__(' a '))
      }
    });
    $('#evf-dates').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('YYYY-MM-DD') + ' a ' + picker.endDate.format('YYYY-MM-DD'));
      if (evfForm) evfForm.submit();
    });
    $('#evf-dates').on('cancel.daterangepicker', function() {
      $(this).val('');
      if (evfForm) evfForm.submit();
    });

    // Auto-submit categoría
    var evfCategory = document.getElementById('evf-category');
    if (evfCategory) {
      evfCategory.addEventListener('change', function() {
        if (evfForm) evfForm.submit();
      });
    }
  });
</script>
@endpush
