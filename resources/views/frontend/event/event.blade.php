@extends('frontend.layout')
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

{{-- ─── CONTENT ─── --}}
@section('content')

{{-- Explorar eventos: fondo único (filtros + grid) --}}
@php
  $evf_total = $information['events']->total();
  $evf_base  = request()->except(['event', 'pricing']);
@endphp
<div class="ev-explore ev-explore--premium">
  <div class="ev-explore__ambient" aria-hidden="true"></div>
  <div class="ev-explore__grain" aria-hidden="true"></div>
  <div class="ev-explore__mesh" aria-hidden="true"></div>

<div class="evf-bar-wrap evf-bar-wrap--premium">
  <div class="container">
      <header class="evf-panel__head">
        <div class="evf-panel__intro">
          <p class="evf-panel__eyebrow">{{ __('Explorar') }}</p>
          <h2 class="evf-panel__title">{{ __('Filtrá y encontrá eventos') }}</h2>
        </div>
        <div class="evf-result-badge" aria-live="polite">
          <span class="evf-result-badge__num">{{ $evf_total }}</span>
          <span class="evf-result-badge__label">{{ $evf_total === 1 ? __('evento') : __('eventos') }}</span>
        </div>
      </header>

      <form action="{{ route('events') }}" method="GET" class="evf-form" id="evfForm" novalidate>
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
            <svg class="evf-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="search" name="search-input" class="evf-input"
                   value="{{ request('search-input') }}"
                   placeholder="{{ __('Nombre del evento…') }}"
                   autocomplete="off"
                   aria-label="{{ __('Buscar por nombre') }}">
          </div>
          <div class="evf-form__actions">
            <button type="submit" class="evf-btn evf-btn--primary">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              {{ __('Buscar') }}
            </button>
            @if (request()->hasAny(['search-input', 'category', 'location', 'dates', 'event', 'pricing', 'min', 'max']))
              <a href="{{ route('events') }}" class="evf-clear" title="{{ __('Limpiar filtros') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                <span class="evf-clear__txt">{{ __('Limpiar') }}</span>
              </a>
            @endif
          </div>
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
                     autocomplete="off" readonly>
            </div>
          </div>
        </div>
      </form>

      <div class="evf-toolbar" aria-label="{{ __('Filtros rápidos') }}">
        <div class="evf-seg">
          <span class="evf-seg__label" id="evf-lbl-format">{{ __('Formato') }}</span>
          <div class="evf-seg__track" role="group" aria-labelledby="evf-lbl-format">
            <a href="{{ route('events', array_merge($evf_base, ['event' => ''])) }}"
               class="evf-seg__opt {{ ! request('event') ? 'is-active' : '' }}">{{ __('Todos') }}</a>
            <a href="{{ route('events', array_merge($evf_base, ['event' => 'online'])) }}"
               class="evf-seg__opt {{ request('event') == 'online' ? 'is-active' : '' }}">{{ __('En línea') }}</a>
            <a href="{{ route('events', array_merge($evf_base, ['event' => 'venue'])) }}"
               class="evf-seg__opt {{ request('event') == 'venue' ? 'is-active' : '' }}">{{ __('Presencial') }}</a>
          </div>
        </div>
        <div class="evf-seg">
          <span class="evf-seg__label" id="evf-lbl-price">{{ __('Precio') }}</span>
          <div class="evf-seg__track" role="group" aria-labelledby="evf-lbl-price">
            <a href="{{ route('events', array_merge(request()->except('pricing'), ['pricing' => ''])) }}"
               class="evf-seg__opt {{ ! request('pricing') ? 'is-active' : '' }}">{{ __('Todos') }}</a>
            <a href="{{ route('events', array_merge(request()->except('pricing'), ['pricing' => 'free'])) }}"
               class="evf-seg__opt evf-seg__opt--free {{ request('pricing') == 'free' ? 'is-active' : '' }}">{{ __('Gratis') }}</a>
            <a href="{{ route('events', array_merge(request()->except('pricing'), ['pricing' => 'paid'])) }}"
               class="evf-seg__opt {{ request('pricing') == 'paid' ? 'is-active' : '' }}">{{ __('De pago') }}</a>
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
          <div class="col-md-6 col-lg-4 ev-card-col">
            @include('frontend.partials.event-card')
          </div>
        @endforeach
      </div>
    @else
      <div class="ev-empty">
        <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <p>No se encontraron eventos con esos filtros</p>
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
<form id="filtersForm" class="d-none" action="{{ route('events') }}" method="GET">
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

@section('custom-script')
<script src="{{ asset('assets/front/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/front/js/daterangepicker.min.js') }}"></script>
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

  // Daterangepicker
  $('#evf-dates').daterangepicker({
    autoUpdateInput: false,
    locale: { cancelLabel: 'Limpiar', applyLabel: 'Aplicar', format: 'YYYY-MM-DD', separator: ' a ' }
  });
  $('#evf-dates').on('apply.daterangepicker', function(ev, picker) {
    $(this).val(picker.startDate.format('YYYY-MM-DD') + ' a ' + picker.endDate.format('YYYY-MM-DD'));
  });
  $('#evf-dates').on('cancel.daterangepicker', function() { $(this).val(''); });
</script>
@endsection
