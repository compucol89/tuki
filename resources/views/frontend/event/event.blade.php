@extends('frontend.layout')

@php
  $frontCssAsset = static function (string $path): string {
    $fullPath = public_path($path);

    return asset($path) . (is_file($fullPath) ? '?v=' . filemtime($fullPath) : '');
  };
  $eventsCssPath = app()->environment('production') ? 'assets/front/css/events.min.css' : 'assets/front/css/events.css';
  $homeCssPath = app()->environment('production') ? 'assets/front/css/home.min.css' : 'assets/front/css/home.css';
@endphp

@push('styles')
  <link rel="stylesheet" href="{{ $frontCssAsset('assets/front/css/daterangepicker.css') }}" media="print" onload="this.media='all'">
  <noscript><link rel="stylesheet" href="{{ $frontCssAsset('assets/front/css/daterangepicker.css') }}"></noscript>
  <link rel="stylesheet" href="{{ $frontCssAsset($eventsCssPath) }}">
  <link rel="stylesheet" href="{{ $frontCssAsset($homeCssPath) }}">
@endpush

@section('body-class', 'events-page')

@push('critical-styles')
<style>
  body.events-page {
    background: #eef1f3;
  }

  body.events-page .hs-search-wrap--events {
    background: #eef1f3;
    padding: 30px 0 18px;
  }

  body.events-page .hs-search-wrap--events .hs-search-form {
    display: flex;
    align-items: center;
    min-height: 56px;
    overflow: hidden;
    background: #fff;
    border: 1px solid rgba(30, 37, 50, 0.08);
    border-radius: 12px;
  }

  body.events-page .hs-search-wrap--events .hs-sf__field {
    display: flex;
    align-items: center;
    gap: 10px;
    height: 56px;
    min-width: 0;
    padding: 0 14px;
  }

  body.events-page .hs-search-wrap--events .hs-sf__field--grow {
    flex: 1 1 320px;
    max-width: none;
  }

  body.events-page .hs-search-wrap--events .hs-sf__field--location {
    flex: 0 1 190px;
  }

  body.events-page .hs-search-wrap--events .hs-sf__field--select {
    flex: 0 1 210px;
  }

  body.events-page .hs-search-wrap--events .hs-sf__field--dates {
    flex: 1 1 240px;
  }

  body.events-page .hs-search-wrap--events .hs-sf__divider {
    width: 1px;
    height: 30px;
    flex-shrink: 0;
    background: rgba(30, 37, 50, 0.08);
  }

  body.events-page .hs-search-wrap--events .hs-sf__icon {
    flex-shrink: 0;
  }

  body.events-page .hs-search-wrap--events .hs-sf__input,
  body.events-page .hs-search-wrap--events .hs-sf__select {
    width: 100%;
    min-width: 0;
    border: 0;
    outline: 0;
    background: transparent;
  }

  body.events-page .hs-search-wrap--events .hs-sf__btn {
    display: flex;
    align-items: center;
    justify-content: center;
    align-self: stretch;
    flex: 0 0 clamp(138px, 11vw, 164px);
    margin-left: 0;
    background: #f97316;
    color: #1e2532;
    font-family: var(--tuki-font-sans, 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif);
    font-size: 14px;
    font-weight: 600;
    border: 0;
    cursor: pointer;
  }

  body.events-page .hs-search-wrap--events .hs-sf__btn-inner {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
  }

  @media (max-width: 991px) {
    body.events-page .hs-search-wrap--events .hs-search-form {
      flex-wrap: wrap;
    }

    body.events-page .hs-search-wrap--events .hs-sf__divider {
      display: none;
    }

    body.events-page .hs-search-wrap--events .hs-sf__field--grow,
    body.events-page .hs-search-wrap--events .hs-sf__btn {
      flex: 1 0 100%;
    }
  }
</style>
@endpush

@section('hero-preload')
  @if (!empty($firstHeroSlideUrl))
    <link rel="preload" as="image" href="{{ $firstHeroSlideUrl }}" fetchpriority="high">
  @endif
@endsection

@section('pageHeading', 'Eventos en Argentina · Entradas y Tickets Online')

@php
  $metaKeywords    = !empty($seo->meta_keyword_event)    ? $seo->meta_keyword_event    : 'eventos, entradas, tickets, conciertos, shows, teatro, Argentina';
  $metaDescription = !empty($seo->meta_description_event) ? $seo->meta_description_event : 'Encontrá los mejores eventos en Argentina. Reservá entradas online de forma fácil, rápida y segura en Tukipass.';
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
<section class="hero-section hero-collage-section hero-collage-section--premium hero-collage-section--events" id="heroSection" aria-labelledby="heroHeading">

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
      <span class="hero-kicker">{{ __('Catálogo Tukipass') }}</span>
      <h1 id="heroHeading">Eventos para elegir<br>sin perder tiempo</h1>
      <p class="hero-lede">Filtrá la agenda real, compará opciones y reservá tu entrada con una experiencia clara y segura.</p>
      <ul class="hero-trust" aria-label="{{ __('Beneficios') }}">
        <li>{{ __('Reserva segura') }}</li>
        <li>{{ __('Entradas oficiales') }}</li>
        <li>{{ __('Soporte en tu idioma') }}</li>
      </ul>
    </div>
  </div>

</section>
@endsection


{{-- ─── CONTENT ─── --}}
@section('content')
<main id="main-content" class="events-page-main" tabindex="-1">

@php
  $evf_total = $information['events']->total();
  $evf_base  = request()->except(['event', 'pricing']);
  $evf_has_filters = request()->hasAny(['search-input', 'category', 'location', 'dates', 'event', 'pricing', 'min', 'max']);
  $today = \Carbon\Carbon::now();
  $weekendStart = $today->copy()->next(\Carbon\Carbon::FRIDAY);
  $weekendEnd = $weekendStart->copy()->addDays(2);
@endphp

<section class="hs-search-wrap hs-search-wrap--events" aria-labelledby="evf-form-heading">
  <div class="container">
    <div class="hs-search-head">
      <div class="hs-search-head__copy">
        <h2 class="hs-search-head__title" id="evf-form-heading">{{ __('Encontrá tu próxima salida') }}</h2>
        <p class="hs-search-head__sub">{{ __('Buscá por nombre, ciudad o categoría y reservá sin vueltas.') }}</p>
      </div>

      <div class="ev-catalog-summary" aria-live="polite">
        <p class="ev-catalog-count">
          <span class="ev-catalog-count__main">
            <span class="ev-catalog-count__num">{{ $evf_total }}</span>
            {{ $evf_total === 1 ? __('evento disponible') : __('eventos disponibles') }}
          </span>
          @if ($evf_has_filters)
            <span class="ev-catalog-count__meta">{{ __('Filtros activos') }}</span>
          @else
            <span class="ev-catalog-count__meta">{{ __('Agenda para explorar') }}</span>
          @endif
        </p>
      </div>
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

      <div class="hs-sf__field hs-sf__field--dates">
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
      <span class="hs-search-chips__label">{{ __('Explorá:') }}</span>
      @if ($evf_has_filters)
        <a href="{{ route('events') }}" class="hs-chip">{{ __('Limpiar filtros') }}</a>
      @endif
      <a href="{{ route('events', array_merge($evf_base, ['event' => ''])) }}"
         class="hs-chip {{ ! request('event') ? 'hs-chip--active' : '' }}"
         @if (! request('event')) aria-current="true" @endif>{{ __('Todos') }}</a>
      <a href="{{ route('events', array_merge(request()->except('pricing'), ['pricing' => 'free'])) }}"
         class="hs-chip hs-chip--free {{ request('pricing') == 'free' ? 'hs-chip--active' : '' }}"
         @if (request('pricing') == 'free') aria-current="true" @endif>{{ __('Gratis') }}</a>
      <a href="{{ route('events', array_merge(request()->except('pricing'), ['pricing' => 'paid'])) }}"
         class="hs-chip {{ request('pricing') == 'paid' ? 'hs-chip--active' : '' }}"
         @if (request('pricing') == 'paid') aria-current="true" @endif>{{ __('Pagos') }}</a>
      <a href="{{ route('events', array_merge(request()->except('dates'), ['dates' => $today->format('Y-m-d') . ' a ' . $today->format('Y-m-d')])) }}"
         class="hs-chip {{ request('dates') == $today->format('Y-m-d') . ' a ' . $today->format('Y-m-d') ? 'hs-chip--active' : '' }}"
         @if (request('dates') == $today->format('Y-m-d') . ' a ' . $today->format('Y-m-d')) aria-current="true" @endif>{{ __('Hoy') }}</a>
      <a href="{{ route('events', array_merge(request()->except('dates'), ['dates' => $weekendStart->format('Y-m-d') . ' a ' . $weekendEnd->format('Y-m-d')])) }}"
         class="hs-chip {{ request('dates') == $weekendStart->format('Y-m-d') . ' a ' . $weekendEnd->format('Y-m-d') ? 'hs-chip--active' : '' }}"
         @if (request('dates') == $weekendStart->format('Y-m-d') . ' a ' . $weekendEnd->format('Y-m-d')) aria-current="true" @endif>{{ __('Este finde') }}</a>
      @foreach ($categories->take(4) as $cat)
        <a href="{{ route('events', array_merge(request()->except('category'), ['category' => $cat->slug])) }}"
           class="hs-chip hs-chip--category {{ request('category') == $cat->slug ? 'hs-chip--active' : '' }}"
           @if (request('category') == $cat->slug) aria-current="true" @endif>{{ $cat->name }}</a>
      @endforeach
    </div>

  </div>
</section>

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
    <div class="ev-pagination">
      {{ $information['events']->links() }}
    </div>

    @if (!empty(showAd(3)))
      <div class="text-center mt-4">{!! showAd(3) !!}</div>
    @endif

  </div>
</section>

</main>
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
    var evfCategory = document.getElementById('evf-category');
    if (evfCategory) {
      evfCategory.addEventListener('change', function() {
        if (evfForm) evfForm.submit();
      });
    }

    var evfDates = document.getElementById('evf-dates');
    if (evfDates && window.jQuery && jQuery.fn.daterangepicker && window.moment) {
      var initialDates = (evfDates.value || '').split(' ');
      var pickerOptions = {
        autoUpdateInput: false,
        locale: {
          format: 'YYYY-MM-DD',
          separator: ' a ',
          applyLabel: '{{ __('Aplicar') }}',
          cancelLabel: '{{ __('Limpiar') }}',
          fromLabel: '{{ __('Desde') }}',
          toLabel: '{{ __('Hasta') }}',
          customRangeLabel: '{{ __('Personalizado') }}',
          daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
          monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
          firstDay: 1
        }
      };

      if (initialDates.length >= 3 && initialDates[0] && initialDates[2]) {
        pickerOptions.startDate = initialDates[0];
        pickerOptions.endDate = initialDates[2];
      }

      jQuery(evfDates).daterangepicker(pickerOptions);
      jQuery(evfDates).on('apply.daterangepicker', function(event, picker) {
        this.value = picker.startDate.format('YYYY-MM-DD') + ' a ' + picker.endDate.format('YYYY-MM-DD');
      });
      jQuery(evfDates).on('cancel.daterangepicker', function() {
        this.value = '';
      });
    }
  });
</script>
@endpush
