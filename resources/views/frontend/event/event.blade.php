@extends('frontend.layout')

@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/front/css/events.css') }}" media="print" onload="this.media='all'">
  <noscript><link rel="stylesheet" href="{{ asset('assets/front/css/events.css') }}"></noscript>
  <link rel="stylesheet" href="{{ asset(app()->environment('production') ? 'assets/front/css/home.min.css' : 'assets/front/css/home.css') }}" media="print" onload="this.media='all'">
  <noscript><link rel="stylesheet" href="{{ asset(app()->environment('production') ? 'assets/front/css/home.min.css' : 'assets/front/css/home.css') }}"></noscript>
@endpush

@section('hero-preload')
  @if (!empty($firstHeroSlideUrl))
    <link rel="preload" as="image" href="{{ $firstHeroSlideUrl }}" fetchpriority="high">
  @endif
@endsection

@section('pageHeading', 'Eventos en Argentina · Entradas y Tickets Online')

@php
  $metaKeywords    = !empty($seo->meta_keyword_event)    ? $seo->meta_keyword_event    : 'eventos, entradas, tickets, conciertos, shows, teatro, Argentina';
  $metaDescription = !empty($seo->meta_description_event) ? $seo->meta_description_event : 'Encontrá los mejores eventos en Argentina. Comprá entradas y tickets online de forma fácil, rápida y segura en Tukipass.';
  $ogImage = asset('assets/admin/img/' . $basicInfo->breadcrumb);
@endphp

@section('meta-keywords', $metaKeywords)
@section('meta-description', $metaDescription)
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

@php
  $evf_total = $information['events']->total();
  $evf_base  = request()->except(['event', 'pricing']);
  $evf_has_filters = request()->hasAny(['search-input', 'category', 'location', 'dates', 'event', 'pricing', 'min', 'max']);
@endphp

<section class="hs-search-wrap hs-search-wrap--events" aria-labelledby="evf-form-heading">
  <div class="container">
    <div class="hs-search-head">
      <div>
        <h2 class="hs-search-head__title" id="evf-form-heading">{{ __('Filtrá y encontrá eventos') }}</h2>
        <p class="hs-search-head__sub">{{ __('Buscá por evento, ciudad o categoría.') }}</p>
      </div>
      <p class="hs-search-head__count" aria-live="polite">
        <span class="hs-search-head__count-num">{{ $evf_total }}</span>
        {{ $evf_total === 1 ? __('evento') : __('eventos') }}
      </p>
    </div>

    <form action="{{ route('events') }}" method="GET" class="hs-search-form" id="evfForm" role="search">
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

      <div class="hs-sf__field hs-sf__field--grow">
        <label for="evf-search" class="sr-only">{{ __('Buscar por nombre del evento') }}</label>
        <svg class="hs-sf__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="search" name="search-input" id="evf-search" class="hs-sf__input"
               value="{{ request('search-input') }}"
               placeholder="{{ __('¿Qué evento buscás?') }}"
               autocomplete="off"
               enterkeyhint="search">
      </div>

      <div class="hs-sf__divider" aria-hidden="true"></div>

      <div class="hs-sf__field hs-sf__field--location">
        <label for="evf-location" class="sr-only">{{ __('Ubicación') }}</label>
        <svg class="hs-sf__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
        <input type="text" name="location" id="evf-location" class="hs-sf__input"
               value="{{ request('location') }}"
               placeholder="{{ __('Ciudad o ubicación') }}"
               autocomplete="address-level2">
      </div>

      <div class="hs-sf__divider" aria-hidden="true"></div>

      <div class="hs-sf__field hs-sf__field--select">
        <label for="evf-category" class="sr-only">{{ __('Categoría') }}</label>
        <svg class="hs-sf__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
        <select name="category" id="evf-category" class="hs-sf__select">
          <option value="">{{ __('Categorías') }}</option>
          @foreach ($categories as $cat)
            <option value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="hs-sf__divider" aria-hidden="true"></div>

      <div class="hs-sf__field hs-sf__field--dates hs-sf__field--location">
        <label for="evf-dates" class="sr-only">{{ __('Rango de fechas') }}</label>
        <svg class="hs-sf__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
        <input type="text" name="dates" id="evf-dates" class="hs-sf__input"
               value="{{ request('dates') }}"
               placeholder="{{ __('AAAA-MM-DD a AAAA-MM-DD') }}"
               pattern="\d{4}-\d{2}-\d{2} a \d{4}-\d{2}-\d{2}"
               title="{{ __('Usá el formato AAAA-MM-DD a AAAA-MM-DD') }}"
               autocomplete="off">
      </div>

      <button type="submit" class="hs-sf__btn">
        <span class="hs-sf__btn-inner">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          {{ __('Buscar') }}
        </span>
      </button>
    </form>

    <div class="hs-search-chips" aria-label="{{ __('Filtros rápidos') }}">
      <span class="hs-search-chips__label">{{ __('Explorá rápido:') }}</span>
      @if ($evf_has_filters)
        <a href="{{ route('events') }}" class="hs-chip">{{ __('Limpiar filtros') }}</a>
      @endif
      <a href="{{ route('events', array_merge($evf_base, ['event' => ''])) }}"
         class="hs-chip {{ ! request('event') ? 'hs-chip--active' : '' }}"
         @if (! request('event')) aria-current="true" @endif>{{ __('Todos') }}</a>
      <a href="{{ route('events', array_merge($evf_base, ['event' => 'venue'])) }}"
         class="hs-chip {{ request('event') == 'venue' ? 'hs-chip--active' : '' }}"
         @if (request('event') == 'venue') aria-current="true" @endif>{{ __('Presenciales') }}</a>
      <a href="{{ route('events', array_merge($evf_base, ['event' => 'online'])) }}"
         class="hs-chip {{ request('event') == 'online' ? 'hs-chip--active' : '' }}"
         @if (request('event') == 'online') aria-current="true" @endif>{{ __('En línea') }}</a>
      <a href="{{ route('events', array_merge(request()->except('pricing'), ['pricing' => 'free'])) }}"
         class="hs-chip hs-chip--free {{ request('pricing') == 'free' ? 'hs-chip--active' : '' }}"
         @if (request('pricing') == 'free') aria-current="true" @endif>{{ __('Gratis') }}</a>
      <a href="{{ route('events', array_merge(request()->except('pricing'), ['pricing' => 'paid'])) }}"
         class="hs-chip {{ request('pricing') == 'paid' ? 'hs-chip--active' : '' }}"
         @if (request('pricing') == 'paid') aria-current="true" @endif>{{ __('Pagos') }}</a>
    </div>
  </div>
</section>

{{-- Sort options --}}
<div class="container ev-sort-container">
  <form action="{{ route('events') }}" method="GET" id="evfSortForm">
    @foreach (app('request')->except('sort') as $key => $value)
      @if (is_array($value))
        @foreach ($value as $k => $v)
          <input type="hidden" name="{{ $key }}[{{ $k }}]" value="{{ $v }}">
        @endforeach
      @else
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
      @endif
    @endforeach
    <div class="ev-sort">
      <label for="ev-sort-select" class="ev-sort__label">Ordenar por:</label>
      <select id="ev-sort-select" class="ev-sort__select" name="sort" onchange="this.form.submit()">
        <option value="start_date" {{ request('sort') == 'start_date' || !request('sort') ? 'selected' : '' }}>Fecha (más próximos)</option>
        <option value="-start_date" {{ request('sort') == '-start_date' ? 'selected' : '' }}>Fecha (más lejanos)</option>
        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Precio (menor a mayor)</option>
        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Precio (mayor a menor)</option>
        <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Nombre (A-Z)</option>
      </select>
    </div>
  </form>
</div>

<style>
.ev-sort-container { margin-bottom: 16px; }
.ev-sort { display: flex; align-items: center; gap: 8px; }
.ev-sort__label { font-size: 14px; color: #6b7280; white-space: nowrap; }
.ev-sort__select { padding: 6px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; background: white; cursor: pointer; }
</style>

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
        <a href="{{ route('events') }}" class="hs-chip hs-chip--active">{{ __('Limpiar filtros') }}</a>
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

@endsection

@push('scripts')
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
    var evfCategory = document.getElementById('evf-category');
    if (evfCategory) {
      evfCategory.addEventListener('change', function() {
        if (evfForm) evfForm.submit();
      });
    }
  });
</script>
@endpush
