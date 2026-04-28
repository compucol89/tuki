@extends('frontend.layout')
@section('pageHeading', 'Entradas y Tickets Online para Eventos en Argentina')
@section('body-class', 'home-page')

@php
  $metaKeywords    = !empty($seo->meta_keyword_home)    ? $seo->meta_keyword_home    : 'eventos, entradas, tickets, conciertos, shows, teatro, deportes, Argentina, Tukipass';
  $metaDescription = !empty($seo->meta_description_home) ? $seo->meta_description_home : 'Tukipass — Descubrí y comprá entradas para conciertos, teatro, deportes y más en Argentina. Si organizás eventos, también podés vender online con Tukipass.';
  $ogImage = !empty($firstHeroSlideUrl)
    ? $firstHeroSlideUrl
    : asset('assets/admin/img/' . $basicInfo->breadcrumb);
@endphp
@section('meta-keywords',    $metaKeywords)
@section('meta-description', $metaDescription)
@section('og-title',       'Tukipass — Entradas y Tickets Online para Eventos en Argentina')
@section('og-description', $metaDescription)
@section('og-image',       $ogImage)
@section('og-image-alt',   'Tukipass, plataforma para descubrir y comprar entradas de eventos en Argentina')
@section('og-image-width', '1200')
@section('og-image-height','630')
@section('og-type',        'website')
@section('og-url',         url()->current())
@section('canonical',      url()->current())

@section('hero-section')
  <!-- Hero Section Start -->
  <section class="hero-section hero-collage-section hero-collage-section--premium" id="heroSection" aria-labelledby="heroHeadingHome">

    {{-- Slideshow de fondo --}}
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
        <h1 id="heroHeadingHome">{{ __('Tu próximo evento a un solo clic') }}</h1>
        <p class="hero-lede">{{ __('Descubrí eventos y sacá tus entradas en minutos') }}</p>
        <div class="hero-actions justify-content-center">
          <a href="{{ route('events') }}" class="hero-btn hero-btn--primary">{{ __('Ver eventos') }}</a>
          <a href="{{ route('organizer.signup') }}" class="hero-btn hero-btn--secondary">{{ __('Vender mis entradas') }}</a>
        </div>
      </div>
    </div>
  </section>
  <!-- Hero Section End -->

  <!-- Event Images Marquee Start -->
  @if ($marqueeEvents->isNotEmpty())
    @php
      $mq_items = $marqueeEvents->take(10)->map(function ($ev) use ($marqueeGallery) {
        $galleryImage = isset($marqueeGallery[$ev->id]) && $marqueeGallery[$ev->id]->isNotEmpty()
          ? asset('assets/admin/img/event-gallery/' . $marqueeGallery[$ev->id]->first()->image)
          : asset('assets/admin/img/event/thumbnail/' . $ev->thumbnail);

        return [
          'event'  => $ev,
          'src'    => $galleryImage,
          'url'    => route('event.details', [$ev->slug, $ev->id]),
          'badge'  => \App\Services\EventBadgeService::getBadge($ev),
          'carbon' => \Carbon\Carbon::parse($ev->start_date)->locale('es'),
          'time'   => $ev->start_time ? \Carbon\Carbon::parse($ev->start_time)->format('H:i') : null,
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
            @for ($copy = 0; $copy < 3; $copy++)
              @foreach ($mq_items as $mqi)
                @php $ev = $mqi['event']; $mq_carbon = $mqi['carbon']; @endphp
                <a href="{{ $mqi['url'] }}" class="events-marquee-item" @if($copy > 0) aria-hidden="true" tabindex="-1" @endif>
                  <img src="{{ $mqi['src'] }}" alt="{{ $ev->title }}" loading="{{ $copy === 0 && $loop->index < 4 ? 'eager' : 'lazy' }}">

                  <div class="emq-date" aria-hidden="true">
                    <span class="emq-date__day">{{ $mq_carbon->format('d') }}</span>
                    <span class="emq-date__month">{{ strtoupper($mq_carbon->translatedFormat('M')) }}</span>
                  </div>
                  <span class="sr-only">{{ $mq_carbon->format('d') }} {{ $mq_carbon->translatedFormat('M') }}</span>

                  @if($mqi['badge'])
                    <span class="emq-badge">
                      <span>{{ $mqi['badge']['icon'] }}</span>
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
            @endfor
          </div>
        </div>
      </div>
    </section>
  @endif
  <!-- Event Images Marquee End -->
@endsection
@section('content')

  {{-- ── BUSCADOR HOME — Modern SaaS UI ── --}}
  @php
    $today = \Carbon\Carbon::now();
    $weekendStart = $today->copy()->next(\Carbon\Carbon::FRIDAY);
    $weekendEnd = $weekendStart->copy()->addDays(2);
  @endphp
  <section class="hs-search-wrap">
    <div class="container">
      <div class="hs-search-head">
        <div>
          <h2 class="hs-search-head__title">{{ __('Encontrá un plan que te cierre') }}</h2>
          <p class="hs-search-head__sub">{{ __('Buscá por evento, ciudad o categoría y filtrá en segundos.') }}</p>
        </div>
      </div>

      <form action="{{ route('events') }}" method="GET" class="hs-search-form" id="hsSearchForm">

        {{-- Keyword --}}
        <div class="hs-sf__field hs-sf__field--grow">
          <svg class="hs-sf__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input type="text" name="search-input" class="hs-sf__input" placeholder="¿Qué evento buscás?" autocomplete="off">
        </div>

        <div class="hs-sf__divider"></div>

        {{-- Ubicación --}}
        <div class="hs-sf__field hs-sf__field--location">
          <svg class="hs-sf__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
          <input type="text" name="location" class="hs-sf__input" placeholder="Ciudad o ubicación" autocomplete="off">
        </div>

        <div class="hs-sf__divider"></div>

        {{-- Categoría --}}
        <div class="hs-sf__field hs-sf__field--select">
          <svg class="hs-sf__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
          <select name="category" class="hs-sf__select">
            <option value="">{{ __('Categorías') }}</option>
            @foreach ($categories as $cat)
              <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>

        {{-- CTA — inner centra icono+texto sin depender del padding asimétrico --}}
        <button type="submit" class="hs-sf__btn">
          <span class="hs-sf__btn-inner">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            {{ __('Buscar') }}
          </span>
        </button>

      </form>

      {{-- Quick filters --}}
      <div class="hs-search-chips">
        <span class="hs-search-chips__label">{{ __('Explorá rápido:') }}</span>
        <a href="{{ route('events') }}" class="hs-chip hs-chip--active">{{ __('Todos') }}</a>
        <a href="{{ route('events', ['pricing' => 'free']) }}" class="hs-chip hs-chip--free">{{ __('Gratis') }}</a>
        <a href="{{ route('events', ['pricing' => 'paid']) }}" class="hs-chip">{{ __('Pagos') }}</a>
        <a href="{{ route('events', ['event' => 'venue']) }}" class="hs-chip">{{ __('Presenciales') }}</a>
        <a href="{{ route('events', ['event' => 'online']) }}" class="hs-chip">{{ __('En línea') }}</a>
        <a href="{{ route('events', ['dates' => $today->format('Y-m-d') . ' to ' . $today->format('Y-m-d')]) }}" class="hs-chip">{{ __('Hoy') }}</a>
        <a href="{{ route('events', ['dates' => $weekendStart->format('Y-m-d') . ' to ' . $weekendEnd->format('Y-m-d')]) }}" class="hs-chip">{{ __('Este finde') }}</a>
        @foreach ($categories->take(4) as $cat)
          <a href="{{ route('events', ['category' => $cat->slug]) }}" class="hs-chip hs-chip--category">{{ $cat->name }}</a>
        @endforeach
      </div>

    </div>
  </section>

  <!-- Events Section Start -->
  @if ($secInfo->featured_section_status == 1)
    <section class="events-section bg-lighter">
      <div class="container">

        @if ($eventCategories->isEmpty())
          <p class="text-center">{{ __('Por ahora no encontramos eventos destacados.') }}</p>
        @else
          @php
            // ── Pre-cargar wishlist: UNA query para todos los eventos ──
            $ev_wishlist_map = [];
            if (Auth::guard('customer')->check()) {
              $ev_wishlist_map = array_flip(
                DB::table('wishlists')
                  ->where('customer_id', Auth::guard('customer')->user()->id)
                  ->pluck('event_id')
                  ->toArray()
              );
            }
            // ── Subquery de tickets reutilizable ──
            $ticketSub = DB::raw("(SELECT event_id,
              COUNT(*) as ticket_count,
              MIN(CASE WHEN pricing_type != 'free' AND price > 0 THEN CAST(price AS DECIMAL(10,2)) END) as min_price,
              MAX(CASE WHEN pricing_type = 'free' THEN 1 ELSE 0 END) as has_free,
              MAX(CASE WHEN pricing_type = 'variation' OR (pricing_type != 'free' AND price > 0) THEN 1 ELSE 0 END) as has_paid
              FROM tickets GROUP BY event_id) as tk");
          @endphp
          <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-all" role="tabpanel" aria-labelledby="nav-all-tab">
              <div class="row">
                @php
                  $now_time = \Carbon\Carbon::now();
                  $eventsall = DB::table('event_contents')
                      ->join('events', 'events.id', '=', 'event_contents.event_id')
                      ->leftJoin($ticketSub, 'tk.event_id', '=', 'events.id')
                      ->leftJoin('organizers', 'organizers.id', '=', 'events.organizer_id')
                      ->where([
                          ['event_contents.language_id', '=', $currentLanguageInfo->id],
                          ['events.status', 1],
                          ['events.end_date_time', '>=', $now_time],
                          ['events.is_featured', '=', 'yes'],
                      ])
                      ->orderBy('events.created_at', 'desc')
                      ->select('event_contents.*', 'events.*',
                               'tk.ticket_count', 'tk.min_price', 'tk.has_free', 'tk.has_paid',
                               'organizers.id as org_id', 'organizers.username as org_username')
                      ->get();
                @endphp
                @foreach ($eventsall as $evLoop => $event)
                  <div class="col-lg-4 col-md-6 ev-card-col item motivational{{ $evLoop === 0 ? ' ev-card-col--featured' : '' }}">
                    @include('frontend.partials.event-card')
                  </div>
                @endforeach
              </div>
            </div>
            @foreach ($eventCategories as $item)
              @php
                $now_time = \Carbon\Carbon::now();
                $events = DB::table('event_contents')
                    ->join('events', 'events.id', '=', 'event_contents.event_id')
                    ->leftJoin($ticketSub, 'tk.event_id', '=', 'events.id')
                    ->leftJoin('organizers', 'organizers.id', '=', 'events.organizer_id')
                    ->where([
                        ['event_contents.event_category_id', '=', $item->id],
                        ['event_contents.language_id', '=', $currentLanguageInfo->id],
                        ['events.status', 1],
                        ['events.end_date_time', '>=', $now_time],
                        ['events.is_featured', '=', 'yes'],
                    ])
                    ->orderBy('events.created_at', 'desc')
                    ->select('event_contents.*', 'events.*',
                             'tk.ticket_count', 'tk.min_price', 'tk.has_free', 'tk.has_paid',
                             'organizers.id as org_id', 'organizers.username as org_username')
                    ->get();
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

  <!-- Feature Section Start -->
  <section class="feature-section bg-lighter">
    @if ($secInfo->features_section_status == 1)
      <div class="container">
        <div class="feature-shell">
          <div class="feature-shell__intro">
            <div class="section-title text-center mb-55">
              <h2>{{ $featureEventSection ? $featureEventSection->title : '' }}</h2>
              <p>{{ $featureEventSection ? $featureEventSection->text : '' }}</p>
            </div>
          </div>
          @if ($featureEventItems->isEmpty())
            <h2>{{ __('Pronto vas a ver más razones para elegir Tukipass.') }}</h2>
          @endif
          <div class="row justify-content-center feature-grid">
            @foreach ($featureEventItems as $item)
              <div class="col-xl-4 col-md-6">
                <div class="feature-item">
                  <div class="feature-item__icon" aria-hidden="true">
                    <i class="{{ $item->icon }}"></i>
                  </div>
                  <div class="feature-content">
                    <h5>{{ $item->title }}</h5>
                    <p>{{ $item->text }}</p>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>

      </div>
    @endif
  </section>
  <!-- Feature Section End -->

  <!-- Feature Section End -->


  <!-- Testimonial Section Start -->
  @if ($secInfo->testimonials_section_status == 1)
    <section class="testimonial-section">
      <div class="container">
        <div class="row pb-20 rpb-20">
          <div class="col-lg-4">
            <div class="testimonial-content pt-10 rmb-55">
              <div class="section-title mb-30">
                <h2>{{ $testimonialData ? $testimonialData->title : __('Lo que dicen quienes usan Tukipass') }}</h2>
              </div>
              <p>{{ $testimonialData ? $testimonialData->text : '' }}</p>
              <div class="total-client-reviews mt-40 bg-lighter">
                <div class="review-images mb-30">
                  @if (!is_null($testimonialData))
                    <img class="lazy"
                      data-src="{{ asset('assets/admin/img/testimonial/' . $testimonialData->image) }}"
                      alt="{{ __('Reseña destacada') }}">
                  @else
                    <img class="lazy" data-src="{{ asset('assets/admin/img/testimonial/clients.png') }}"
                      alt="{{ __('Reseña destacada') }}">
                  @endif
                  <span class="pluse"><i class="fas fa-plus"></i></span>
                </div>
                <h6>{{ $testimonialData ? $testimonialData->review_text : __('Opiniones de nuestra comunidad') }}</h6>
              </div>
            </div>
          </div>
          <div class="col-lg-8">
            <div class="testimonial-wrap">
              @if ($testimonials->isNotEmpty())
                <div class="row">
                  @foreach ($testimonials as $item)
                    <div class="col-md-6">
                      <div class="testimonial-item">
                        <div class="author">
                          <img class="lazy" data-src="{{ asset('assets/admin/img/clients/' . $item->image) }}"
                            alt="{{ __('Foto de quien dejó la reseña') }}">
                          <div class="content">
                            <h5>{{ $item->name }}</h5>
                            <span>{{ $item->occupation }}</span>
                            <div class="ratting">
                              @for ($i = 1; $i <= $item->rating; $i++)
                                <i class="fas fa-star"></i>
                              @endfor
                            </div>
                          </div>
                        </div>
                        <p>{{ $item->comment }}</p>
                      </div>
                    </div>
                  @endforeach
                </div>
              @else
                <h4 class="text-center">{{ __('Todavía no hay reseñas publicadas.') }}</h4>
              @endif
            </div>
          </div>
        </div>
        @if ($secInfo->partner_section_status == 1 && $partners->isNotEmpty())
          <div class="trust-partners" aria-label="{{ __('Aliados estratégicos') }}">
            <div class="trust-partners__intro">
              <h3>{{ __('También eligen Tukipass') }}</h3>
              <p>{{ __('Marcas y organizaciones que confían en nuestra plataforma para crecer.') }}</p>
            </div>
            <div class="client-logo-wrap trust-partners__logos">
              @foreach ($partners as $item)
                @php
                  $partnerUrl = trim((string) ($item->url ?? ''));
                @endphp
                <div class="client-logo-item">
                  @if ($partnerUrl !== '')
                    <a href="{{ $partnerUrl }}" target="_blank" rel="noopener noreferrer"
                      aria-label="{{ __('Visitar sitio del aliado estratégico') }}">
                      <img class="lazy" data-src="{{ asset('assets/admin/img/partner/' . $item->image) }}"
                        alt="{{ $item->name ?? $item->title ?? __('Logo de aliado estratégico') }}">
                    </a>
                  @else
                    <span aria-hidden="true">
                      <img class="lazy" data-src="{{ asset('assets/admin/img/partner/' . $item->image) }}"
                        alt="{{ $item->name ?? $item->title ?? __('Logo de aliado estratégico') }}">
                    </span>
                  @endif
                </div>
              @endforeach
            </div>
          </div>
        @endif
      </div>

    </section>
  @endif
  <!-- Testimonial Section End -->
@endsection

@push('scripts')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => $websiteInfo->website_title ?? 'TukiPass',
    'url' => url('/'),
    'inLanguage' => 'es-AR',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => $websiteInfo->website_title ?? 'TukiPass',
    'url' => url('/'),
    'logo' => !empty($websiteInfo->logo) ? asset('assets/admin/img/' . $websiteInfo->logo) : null,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

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

  sliderId = setInterval(nextSlide, 5000);

  // --- Parallax — se detiene cuando el hero no es visible ---
  var hero = document.getElementById('heroSection');
  var bg   = document.getElementById('heroCollageBg');
  if (!hero || !bg) return;

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
    '.events-section, .category-section, .about-section, .feature-section, .testimonial-section, .client-logo-area, .work-process'
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
