@extends('frontend.layout')

@section('hero-preload')
  @if (!empty($firstHeroSlideUrl))
    <link rel="preload" as="image" href="{{ $firstHeroSlideUrl }}" fetchpriority="high">
  @endif
@endsection

@php
  $homeCssPath = app()->environment('production') ? 'assets/front/css/home.min.css' : 'assets/front/css/home.css';
  $homeCssUrl = asset($homeCssPath) . (is_file(public_path($homeCssPath)) ? '?v=' . substr(md5_file(public_path($homeCssPath)), 0, 12) : '');
@endphp

@push('styles')
  <link rel="stylesheet" href="{{ $homeCssUrl }}">
@endpush

@section('pageHeading', 'Entradas y Tickets Online para Eventos en Argentina')
@section('body-class', 'home-page')

@php
  $metaKeywords    = !empty($seo->meta_keyword_home)    ? $seo->meta_keyword_home    : 'eventos, entradas, tickets, conciertos, shows, teatro, deportes, Argentina, TukiPass';
  $metaDescription = !empty($seo->meta_description_home) ? $seo->meta_description_home : 'TukiPass — Descubrí y comprá entradas para conciertos, teatro, deportes y más en Argentina. Si organizás eventos, también podés vender online con TukiPass.';
  $ogImage = asset('assets/front/img/og/tukipass-og.jpg');
@endphp
@section('meta-keywords',    $metaKeywords)
@section('meta-description', $metaDescription)
@section('og-title',       'TukiPass — Entradas y Tickets Online para Eventos en Argentina')
@section('og-description', $metaDescription)
@section('og-image',       $ogImage)
@section('og-image-alt',   'TukiPass, plataforma para descubrir y comprar entradas de eventos en Argentina')
@section('og-image-width', '1200')
@section('og-image-height','630')
@section('og-type',        'website')
@section('og-url',         url()->current())
@section('canonical',      route('index', [], true))

@push('critical-styles')
@endpush

@php
  $today = \Carbon\Carbon::now();
  $weekendStart = $today->copy()->next(\Carbon\Carbon::FRIDAY);
  $weekendEnd = $weekendStart->copy()->addDays(2);
@endphp

@section('hero-section')
  <!-- Hero Section Start -->
  <section class="hero-section hero-collage-section hero-collage-section--premium" id="heroSection" aria-labelledby="heroHeadingHome">

    {{-- Slideshow de fondo --}}
    <div class="hero-slideshow" id="heroCollageBg">
      @forelse($heroSlideUrls ?? [] as $slideUrl)
        <div class="hero-slide" @unless($loop->first) data-bg="{{ $slideUrl }}" @endunless style="aspect-ratio: 1920 / 800;">
          @if($loop->first)
            <img class="hero-slide__image" src="{{ $slideUrl }}" width="1920" height="800" alt="" loading="eager" fetchpriority="high" decoding="sync" aria-hidden="true">
          @endif
        </div>
      @empty
        <div class="hero-slide" style="aspect-ratio: 1920 / 800;">
          <img class="hero-slide__image" src="{{ \App\Services\FileUploadService::imagePath('assets/admin/img/', $basicInfo->breadcrumb ?? 'noimage.jpg') }}" width="1920" height="800" alt="" loading="eager" fetchpriority="high" decoding="sync" aria-hidden="true">
        </div>
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
      <div class="hero-content hero-content--premium">
        <span class="hero-kicker">{{ __('TukiPass Argentina') }}</span>
        <h1 id="heroHeadingHome">{{ __('Reservá entradas para vivir lo que viene') }}</h1>
        <p class="hero-lede">{{ __('Agenda real, eventos cerca tuyo y una experiencia de reserva simple, clara y segura.') }}</p>
        <div class="hero-actions">
          <a href="{{ route('events') }}" class="hero-btn hero-btn--primary">{{ __('Explorar eventos') }}</a>
          <a href="{{ route('organizer.signup') }}" class="hero-btn hero-btn--secondary">{{ __('Publicar un evento') }}</a>
        </div>
        <div class="hero-proof" aria-label="{{ __('Beneficios de TukiPass') }}">
          <span>{{ __('Entrada digital') }}</span>
          <span>{{ __('Pago seguro') }}</span>
          <span>{{ __('Soporte local') }}</span>
        </div>
      </div>
    </div>
  </section>
  <!-- Hero Section End -->

  <!-- Event Images Marquee Start -->
  @if ($marqueeEvents->isNotEmpty())
    @php
      $mq_source = $marqueeEvents->take(10)->values();
      $mq_source_count = $mq_source->count();
      $mq_min_items = max(10, $mq_source_count);
      $mq_items = collect(range(0, $mq_min_items - 1))->map(function ($index) use ($mq_source, $mq_source_count, $badgeMap) {
        $ev = $mq_source[$index % $mq_source_count];
        $eventImage = \App\Services\FileUploadService::eventVisualUrl(null, $ev->thumbnail ?? null);

        return [
          'event'  => $ev,
          'src'    => $eventImage,
          'url'    => route('event.details', [$ev->slug, $ev->id]),
          'badge'  => $badgeMap[$ev->id] ?? null,
          'carbon' => \Carbon\Carbon::parse($ev->start_date)->locale('es'),
          'time'   => $ev->start_time ? \Carbon\Carbon::parse($ev->start_time)->format('H:i') : null,
          'is_duplicate' => $index >= $mq_source_count,
        ];
      });
    @endphp
    <section class="events-marquee" aria-label="{{ __('Eventos destacados') }}">
      <div class="container">
        <div class="hs-header mb-32">
          <div class="hs-header__left">
            <h2 class="hs-header__title">{{ __('Lo que no te podés perder') }}</h2>
            <p class="hs-header__sub">{{ __('Descubrí destacados, elegí tu fecha y sacá tu entrada sin vueltas.') }}</p>
          </div>
          <a href="{{ route('events') }}" class="hs-header__cta">{{ __('Ver agenda') }}</a>
        </div>

        <div class="events-marquee-track">
          <div class="events-marquee-inner">
            @for ($copy = 0; $copy < 2; $copy++)
              <div class="events-marquee-group" @if($copy > 0) aria-hidden="true" @endif>
              @foreach ($mq_items as $mqi)
                @php $ev = $mqi['event']; $mq_carbon = $mqi['carbon']; @endphp
                <a href="{{ $mqi['url'] }}" class="events-marquee-item" @if($copy > 0 || $mqi['is_duplicate']) aria-hidden="true" tabindex="-1" @endif>
                  <img src="{{ $mqi['src'] }}" alt="{{ $ev->title }}" width="400" height="267" loading="lazy">

                  <div class="emq-date" aria-hidden="true">
                    <span class="emq-date__day">{{ $mq_carbon->format('d') }}</span>
                    <span class="emq-date__month">{{ strtoupper($mq_carbon->translatedFormat('M')) }}</span>
                  </div>
                  <span class="sr-only">{{ $mq_carbon->format('d') }} {{ $mq_carbon->translatedFormat('M') }}</span>

                  @if($mqi['badge'])
                    <span class="emq-badge {{ $mqi['badge']['class'] ?? '' }}">
                      @if(!empty($mqi['badge']['fa']))
                        <span class="emq-badge__icon" aria-hidden="true"><i class="{{ $mqi['badge']['fa'] }}"></i></span>
                      @endif
                      <span>{{ $mqi['badge']['label'] }}</span>
                    </span>
                  @endif

                  <div class="emq-bottom">
                    <span class="emq-bottom__eyebrow">
                      {{ $mqi['time'] ? $mq_carbon->translatedFormat('d M') . ' · ' . $mqi['time'] : $mq_carbon->translatedFormat('d M') }}
                    </span>
                    <span class="emq-bottom__title">{{ $ev->title }}</span>
                  </div>
                </a>
              @endforeach
              </div>
            @endfor
          </div>
        </div>
      </div>
    </section>
  @endif
  <!-- Event Images Marquee End -->
@endsection
@section('content')
<div id="main-content" tabindex="-1">

  {{-- ── BUSCADOR HOME — Modern SaaS UI ── --}}
  <section class="hs-search-wrap">
    <div class="container">
      <div class="hs-search-head">
        <div>
          <h2 class="hs-search-head__title">{{ __('Encontrá tu próxima salida') }}</h2>
          <p class="hs-search-head__sub">{{ __('Buscá por nombre, ciudad o categoría y reservá sin vueltas.') }}</p>
        </div>
      </div>

      <form action="{{ route('events') }}" method="GET" class="hs-search-form" id="hsSearchForm">
        <div class="hs-sf__field hs-sf__field--grow">
          <svg class="hs-sf__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <label for="hs-search-input" class="sr-only">{{ __('Buscar por nombre del evento') }}</label>
          <input type="text" id="hs-search-input" name="search-input" class="hs-sf__input" placeholder="¿Qué evento buscás?" autocomplete="off">
        </div>

        <div class="hs-sf__divider"></div>

        <div class="hs-sf__field hs-sf__field--location">
          <svg class="hs-sf__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
          <label for="hs-search-location" class="sr-only">{{ __('Ciudad o ubicación') }}</label>
          <input type="text" id="hs-search-location" name="location" class="hs-sf__input" placeholder="Ciudad o ubicación" autocomplete="off">
        </div>

        <div class="hs-sf__divider"></div>

        <div class="hs-sf__field hs-sf__field--select">
          <svg class="hs-sf__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
          <select name="category" class="hs-sf__select" aria-label="{{ __('Categoría del evento') }}">
            <option value="">{{ __('Categorías') }}</option>
            @foreach ($categories as $cat)
              <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>

        <button type="submit" class="hs-sf__btn">
          <span class="hs-sf__btn-inner">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            {{ __('Buscar') }}
          </span>
        </button>
      </form>

      <div class="hs-search-chips">
        <span class="hs-search-chips__label">{{ __('Explorá:') }}</span>
        <a href="{{ route('events') }}" class="hs-chip hs-chip--active">{{ __('Todos') }}</a>
        <a href="{{ route('events', ['pricing' => 'free']) }}" class="hs-chip hs-chip--free">{{ __('Gratis') }}</a>
        <a href="{{ route('events', ['pricing' => 'paid']) }}" class="hs-chip">{{ __('Pagos') }}</a>
        <a href="{{ route('events', ['dates' => $today->format('Y-m-d') . ' a ' . $today->format('Y-m-d')]) }}" class="hs-chip">{{ __('Hoy') }}</a>
        <a href="{{ route('events', ['dates' => $weekendStart->format('Y-m-d') . ' a ' . $weekendEnd->format('Y-m-d')]) }}" class="hs-chip">{{ __('Este finde') }}</a>
        @foreach ($categories->take(4) as $cat)
          <a href="{{ route('events', ['category' => $cat->slug]) }}" class="hs-chip hs-chip--category">{{ $cat->name }}</a>
        @endforeach
      </div>
    </div>
  </section>

  <!-- Events Section Start -->
  @if ($secInfo->featured_section_status == 1)
    <section class="events-section bg-lighter" aria-labelledby="featuredEventsHeading">
      <div class="container">

        @if ($eventCategories->isEmpty())
          <p class="text-center">{{ __('Por ahora no encontramos eventos destacados.') }}</p>
        @else
          @php
            $ev_wishlist_map = $wishlistMap ?? [];
            $eventsall = $featuredEventsAll ?? collect();
            $eventCategoriesWithEvents = $eventCategories->filter(function ($item) use ($featuredEventsByCategory) {
              return ($featuredEventsByCategory[$item->id] ?? collect())->isNotEmpty();
            });
          @endphp
          <div class="hs-header mb-32">
            <div class="hs-header__left">
              <h2 class="hs-header__title" id="featuredEventsHeading">{{ __('Eventos destacados') }}</h2>
              <p class="hs-header__sub">{{ __('Elegí una categoría o mirá toda la selección destacada.') }}</p>
            </div>
            <a href="{{ route('events') }}" class="hs-header__cta">{{ __('Ver todos') }}</a>
          </div>

          <nav class="hs-tabs-nav mb-32" aria-label="{{ __('Filtrar eventos destacados') }}">
            <div class="nav nav-tabs events-tabs" id="nav-tab" role="tablist">
              <a class="nav-link active" id="nav-all-tab" data-toggle="tab" href="#nav-all" role="tab"
                 aria-controls="nav-all" aria-selected="true">{{ __('Todos') }}</a>
              @foreach ($eventCategoriesWithEvents as $item)
                <a class="nav-link" id="nav-{{ $item->id }}-tab" data-toggle="tab" href="#nav-{{ $item->id }}" role="tab"
                   aria-controls="nav-{{ $item->id }}" aria-selected="false">{{ $item->name }}</a>
              @endforeach
            </div>
          </nav>

          <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-all" role="tabpanel" aria-labelledby="nav-all-tab">
              <div class="row">
                @foreach ($eventsall as $evLoop => $event)
                  <div class="col-lg-4 col-md-6 ev-card-col item motivational{{ $evLoop === 0 ? ' ev-card-col--featured' : '' }}">
                    @include('frontend.partials.event-card')
                  </div>
                @endforeach
              </div>
            </div>
            @foreach ($eventCategoriesWithEvents as $item)
              @php
                $events = $featuredEventsByCategory[$item->id] ?? collect();
              @endphp
              <div class="tab-pane fade" id="nav-{{ $item->id }}" role="tabpanel"
                aria-labelledby="nav-{{ $item->id }}-tab">
                <div class="row">
                  @foreach ($events as $event)
                    <div class="col-lg-4 col-md-6 ev-card-col item motivational">
                      @include('frontend.partials.event-card')
                    </div>
                  @endforeach
                </div>
              </div>
            @endforeach
          </div>
        @endif

      </div>
      @if (!empty(showAd(3)))
        <div class="text-center mt-4">
          {!! showAd(3) !!}
        </div>
      @endif
    </section>
  @endif
  <!-- Events Section End -->
</div>
@endsection

@push('scripts')
<script>
(function() {
  // --- Crossfade slideshow ---
  var slides = Array.from(document.querySelectorAll('#heroCollageBg .hero-slide'));
  var n = slides.length;
  if (n === 0) return;

  slides[0].style.opacity = '1';
  slides[0].style.zIndex  = '0';

  if (n === 1) return;

  var cur = 0;
  var sliderId = null;

  function hydrateSlide(slide) {
    var bg = slide.getAttribute('data-bg');
    if (!bg) return;

    slide.style.backgroundImage = "url('" + bg.replace(/'/g, "\\'") + "')";
    slide.removeAttribute('data-bg');
  }

  function nextSlide() {
    var nxt  = (cur + 1) % n;
    var prev = cur;
    hydrateSlide(slides[nxt]);
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

  function startSlideshow() {
    if (sliderId) return;

    nextSlide();
    sliderId = setInterval(nextSlide, 5000);
    if (startFallbackId) clearTimeout(startFallbackId);
    startEvents.forEach(function(eventName) {
      window.removeEventListener(eventName, startSlideshow);
    });
  }

  var startEvents = ['pointerdown', 'touchstart', 'keydown', 'click'];
  startEvents.forEach(function(eventName) {
    window.addEventListener(eventName, startSlideshow, { passive: true, once: true });
  });

  var startFallbackId = setTimeout(startSlideshow, 30000);

  // --- Parallax — se detiene cuando el hero no es visible ---
  var hero = document.getElementById('heroSection');
  var bg   = document.getElementById('heroCollageBg');
  if (!hero || !bg) return;
  if (!window.matchMedia || !window.matchMedia('(pointer: fine)').matches) return;

  var tx = 0, ty = 0, cx = 0, cy = 0;
  var rafId = null;
  var heroRect = hero.getBoundingClientRect(); // caché — se actualiza solo en resize

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

  // Solo corre el RAF cuando el hero es visible en pantalla
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

// Scroll reveal
(function() {
  var els = document.querySelectorAll(
    '.events-section, .category-section, .about-section, .client-logo-area, .work-process'
  );
  els.forEach(function(el, i) {
    el.classList.add('reveal-on-scroll');
    // Primera sección siempre visible (above the fold)
    if (i === 0) el.classList.add('revealed');
  });
  var io = new IntersectionObserver(function(entries) {
    entries.forEach(function(e) {
      if (e.isIntersecting) { e.target.classList.add('revealed'); io.unobserve(e.target); }
    });
  }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });
  document.querySelectorAll('.reveal-on-scroll:not(.revealed)').forEach(function(el) { io.observe(el); });
})();

// Marquee click/tap to pause
(function() {
  var marquee = document.querySelector('.events-marquee');
  if (!marquee) return;
  marquee.addEventListener('click', function(e) {
    if (e.target.closest('a')) return;
    marquee.classList.toggle('is-paused');
  });
})();
</script>
@endpush
