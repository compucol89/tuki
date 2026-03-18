@extends('frontend.layout')
@section('pageHeading', 'Entradas y Tickets Online para Eventos en Argentina')
@section('body-class', 'home-page')

@php
  $metaKeywords    = !empty($seo->meta_keyword_home)    ? $seo->meta_keyword_home    : 'eventos, entradas, tickets, conciertos, shows, teatro, deportes, Argentina, Tukipass';
  $metaDescription = !empty($seo->meta_description_home) ? $seo->meta_description_home : 'Tukipass — Comprá entradas y tickets para los mejores eventos en Argentina. Conciertos, teatro, deportes y más. Fácil, rápido y seguro.';
  $ogImage = !empty($firstHeroImage)
    ? asset('assets/admin/img/event-gallery/' . $firstHeroImage)
    : asset('assets/admin/img/' . $basicInfo->breadcrumb);
@endphp
@section('meta-keywords',    $metaKeywords)
@section('meta-description', $metaDescription)
@section('og-title',       'Tukipass — Entradas y Tickets Online para Eventos en Argentina')
@section('og-description', $metaDescription)
@section('og-image',       $ogImage)
@section('og-type',        'website')

@push('styles')
@if(!empty($firstHeroImage))
<link rel="preload" as="image" href="{{ asset('assets/admin/img/event-gallery/' . $firstHeroImage) }}">
@endif
@endpush

@section('hero-section')
  <!-- Hero Section Start -->
  <section class="hero-section hero-collage-section" id="heroSection">

    {{-- Slideshow de fondo --}}
    <div class="hero-slideshow" id="heroCollageBg">
      @foreach($heroGalleryImages->values() as $i => $thumb)
        <div class="hero-slide" style="background-image: url('{{ asset('assets/admin/img/event-gallery/' . $thumb) }}');"></div>
      @endforeach
    </div>

    {{-- Overlay oscuro --}}
    <div class="hero-overlay"></div>
    {{-- Textura noise --}}
    <div class="hero-noise"></div>

    <div class="container hero-content-wrapper">
      <div class="hero-content">
        <h1>
          {{ $heroSection ? $heroSection->first_title : __('Event Ticketing and Booking System') }}
        </h1>
        <p>
          {{ $heroSection ? $heroSection->second_title : __('La plataforma de venta de entradas y gestión de eventos más completa.') }}
        </p>
      </div>
    </div>
  </section>
  <!-- Hero Section End -->

  <!-- Event Images Marquee Start -->
  @if ($marqueeEvents->isNotEmpty())
    @php
      // Construir lista plana: thumbnail + galería de cada evento, mezclados
      $mq_flat = collect();
      $mq_badges_map = [];
      foreach ($marqueeEvents as $ev) {
        $mq_badges_map[$ev->id] = \App\Services\EventBadgeService::getBadge($ev);
        $mq_carbon = \Carbon\Carbon::parse($ev->start_date)->locale('es');
        $mq_time   = $ev->start_time ? \Carbon\Carbon::parse($ev->start_time)->format('H:i') : null;
        $mq_free   = ($ev->pricing_type === 'free' || !$ev->min_price);
        $mq_meta   = [
          'event'   => $ev,
          'carbon'  => $mq_carbon,
          'time'    => $mq_time,
          'free'    => $mq_free,
          'badge'   => $mq_badges_map[$ev->id],
          'url'     => route('event.details', [$ev->slug, $ev->id]),
        ];
        // Thumbnail
        $mq_flat->push(array_merge($mq_meta, [
          'src' => asset('assets/admin/img/event/thumbnail/' . $ev->thumbnail),
        ]));
        // Galería
        if (isset($marqueeGallery[$ev->id])) {
          foreach ($marqueeGallery[$ev->id] as $gi) {
            $mq_flat->push(array_merge($mq_meta, [
              'src' => asset('assets/admin/img/event-gallery/' . $gi->image),
            ]));
          }
        }
      }
      $mq_flat = $mq_flat->shuffle()->values();
    @endphp
    <div class="events-marquee">
      <div class="events-marquee-track">
        <div class="events-marquee-inner">
          @for ($copy = 0; $copy < 3; $copy++)
            @foreach ($mq_flat as $mqi)
              @php $ev = $mqi['event']; $mq_carbon = $mqi['carbon']; @endphp
              <a href="{{ $mqi['url'] }}" class="events-marquee-item">
                <img src="{{ $mqi['src'] }}" alt="{{ $ev->title }}" loading="lazy">

                {{-- Fecha — top-left --}}
                <div class="emq-date">
                  <span class="emq-date__day">{{ $mq_carbon->format('d') }}</span>
                  <span class="emq-date__month">{{ strtoupper($mq_carbon->translatedFormat('M')) }}</span>
                </div>

                {{-- Badge — top-right --}}
                @if($mqi['badge'])
                  <span class="emq-badge">
                    <span>{{ $mqi['badge']['icon'] }}</span>
                    <span>{{ $mqi['badge']['label'] }}</span>
                  </span>
                @endif

                {{-- Gradiente inferior + título siempre visible --}}
                <div class="emq-bottom">
                  <span class="emq-bottom__title">{{ $ev->title }}</span>
                </div>

              </a>
            @endforeach
          @endfor
        </div>
      </div>
    </div>
  @endif
  <!-- Event Images Marquee End -->
@endsection
@section('content')

  {{-- ── BUSCADOR HOME — Modern SaaS UI ── --}}
  <section class="hs-search-wrap">
    <div class="container">

      <form action="{{ route('events') }}" method="GET" class="hs-search-form" id="hsSearchForm">

        {{-- Keyword --}}
        <div class="hs-sf__field hs-sf__field--grow">
          <svg class="hs-sf__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input type="text" name="search-input" class="hs-sf__input" placeholder="¿Qué evento buscás?" autocomplete="off">
        </div>

        <div class="hs-sf__divider"></div>

        {{-- Categoría --}}
        <div class="hs-sf__field hs-sf__field--select">
          <svg class="hs-sf__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
          <select name="category" class="hs-sf__select">
            <option value="">Todas las categorías</option>
            @foreach ($categories as $cat)
              <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="hs-sf__divider"></div>

        {{-- Tipo --}}
        <div class="hs-sf__field hs-sf__field--select hs-sf__field--type">
          <svg class="hs-sf__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
          <select name="event" class="hs-sf__select">
            <option value="">Presencial u Online</option>
            <option value="venue">Presencial</option>
            <option value="online">Online</option>
          </select>
        </div>

        {{-- CTA --}}
        <button type="submit" class="hs-sf__btn">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          Buscar eventos
        </button>

      </form>

      {{-- Quick filters --}}
      <div class="hs-search-chips">
        <span class="hs-search-chips__label">Popular:</span>
        <a href="{{ route('events') }}" class="hs-chip hs-chip--active">Todos</a>
        @foreach ($categories->take(6) as $cat)
          <a href="{{ route('events', ['category' => $cat->slug]) }}" class="hs-chip">{{ $cat->name }}</a>
        @endforeach
        <a href="{{ route('events', ['pricing' => 'free']) }}" class="hs-chip hs-chip--free">🎫 Gratis</a>
      </div>

    </div>
  </section>

  <!-- Events Section Start -->
  @if ($secInfo->featured_section_status == 1)
    <section class="events-section bg-lighter">
      <div class="container">

        @if ($eventCategories->isEmpty())
          <p class="text-center">{{ __('No Events Found') }}</p>
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

  <!-- Category Section Start -->
  @if ($secInfo->categories_section_status == 1)
    <section class="category-section">
      <div class="container">
        <div class="section-title mb-60">
          <h2>{{ $secTitleInfo ? $secTitleInfo->category_section_title : __('Categories') }}</h2>
        </div>
        <div class="category-wrap text-white">
          @if ($eventCategories->isNotEmpty())
            @foreach ($eventCategories as $item)
              <a href="{{ route('events', ['category' => $item->slug]) }}" class="category-item">
                <img class="lazy" data-src="{{ asset('assets/admin/img/event-category/' . $item->image) }}"
                  alt="{{ $item->name }}">
                <div class="category-content">
                  <h5>{{ $item->name }}</h5>
                </div>
              </a>
            @endforeach
          @else
            <h3 class="text-dark">{{ __('No Category Found') }}</h3>
          @endif


        </div>
      </div>
    </section>
  @endif
  <!-- Category Section End -->

  <!-- About Section Start -->
  @if ($secInfo->about_section_status == 1)
    <section class="about-section">
      <div class="container">
        @if (is_null($aboutUsSection))
          <h2 class="text-center">{{ __('No data found for about section') }}</h2>
        @endif
        <div class="row align-items-center">
          <div class="col-lg-6">
            <div class="about-image-part pt-10 rmb-55">
              @if (!is_null($aboutUsSection))
                <img class="lazy"
                  data-src="{{ asset('assets/admin/img/about-us-section/' . $aboutUsSection->image) }}"
                  alt="About">
              @endif
            </div>
          </div>
          <div class="col-lg-6">
            <div class="about-content">
              <div class="section-title mb-30">
                <h2>{{ $aboutUsSection ? $aboutUsSection->title : '' }}</h2>
              </div>
              <p>{{ $aboutUsSection ? $aboutUsSection->subtitle : '' }}</p>
              <div>
                {!! $aboutUsSection ? $aboutUsSection->text : '' !!}
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  @endif
  <!-- About Section End -->


  <!-- Feature Section Start -->
  <section class="feature-section bg-lighter">
    @if ($secInfo->features_section_status == 1)
      <div class="container pb-40 rpb-30">
        <div class="section-title text-center mb-55">
          <h2>{{ $featureEventSection ? $featureEventSection->title : '' }}</h2>
          <p>{{ $featureEventSection ? $featureEventSection->text : '' }}</p>
          @if ($featureEventItems->isEmpty())
            <h2>{{ __('No data found for features section') }}</h2>
          @endif
        </div>
        <div class="row justify-content-center">
          @foreach ($featureEventItems as $item)
            <div class="col-xl-4 col-md-6">
              <div class="feature-item">
                <i class="{{ $item->icon }}"></i>
                <div class="feature-content">
                  <h5>{{ $item->title }}</h5>
                  <p>{{ $item->text }}</p>
                </div>
              </div>
            </div>
          @endforeach
        </div>

      </div>
    @endif
    @if ($secInfo->how_work_section_status == 1)
      @if ($howWork)
        <div class="work-process text-center">
          <div class="container">
            <div class="work-process-inner">

              <div class="section-title mb-60">
                <h2>{{ $howWork->title }}</h2>
                <p>{{ $howWork->text }}</p>
              </div>
              <div class="row justify-content-center">
                @foreach ($howWorkItems as $item)
                  <div class="col-xl-3 col-md-6">
                    <div class="work-process-item">
                      <div class="icon">
                        <span class="number">{{ $item->serial_number }}</span>
                        <i class="{{ $item->icon }}"></i>
                      </div>
                      <div class="content">
                        <h4>{{ $item->title }}</h4>
                        <p>{{ $item->text }}</p>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      @else
        <div class="work-process text-center">
          <div class="container">
            <h2>{{ __('No Data Found for how work section') }}</h2>
          </div>
        </div>
      @endif
    @endif
  </section>
  <!-- Feature Section End -->


  <!-- Testimonial Section Start -->
  @if ($secInfo->testimonials_section_status == 1)
    <section class="testimonial-section">
      <div class="container">
        <div class="row pb-20 rpb-20">
          <div class="col-lg-4">
            <div class="testimonial-content pt-10 rmb-55">
              <div class="section-title mb-30">
                <h2>{{ $testimonialData ? $testimonialData->title : __('What say our client about us') }}</h2>
              </div>
              <p>{{ $testimonialData ? $testimonialData->text : '' }}</p>
              <div class="total-client-reviews mt-40 bg-lighter">
                <div class="review-images mb-30">
                  @if (!is_null($testimonialData))
                    <img class="lazy"
                      data-src="{{ asset('assets/admin/img/testimonial/' . $testimonialData->image) }}"
                      alt="Reviewer">
                  @else
                    <img class="lazy" data-src="{{ asset('assets/admin/img/testimonial/clients.png') }}"
                      alt="Reviewer">
                  @endif
                  <span class="pluse"><i class="fas fa-plus"></i></span>
                </div>
                <h6>{{ $testimonialData ? $testimonialData->review_text : __('0 Clients Reviews') }}</h6>
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
                            alt="Author">
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
                <h4 class="text-center">{{ __('No Review Found') }}</h4>
              @endif
            </div>
          </div>
        </div>
        <hr>
      </div>

    </section>
  @endif
  <!-- Testimonial Section End -->

  <!-- Client Logo Start -->
  @if ($secInfo->partner_section_status == 1)
    <section class="client-logo-area text-center">
      <div class="container">
        <div class="section-title mb-55">
          <h2>{{ $partnerInfo ? $partnerInfo->title : __('Our Partner') }}</h2>
          <p>{{ $partnerInfo ? $partnerInfo->text : '' }}</p>
        </div>
        <div class="client-logo-wrap">
          @if ($partners->isNotEmpty())
            @foreach ($partners as $item)
              <div class="client-logo-item">
                <a href="{{ $item->url }}" target="_blank"><img class="lazy"
                    data-src="{{ asset('assets/admin/img/partner/' . $item->image) }}" alt="Client Logo"></a>
              </div>
            @endforeach
          @else
            <h5>{{ __('No Partner Found') }}</h5>
          @endif
        </div>
      </div>
    </section>
  @endif
  <!-- Client Logo End -->
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
</script>
@endpush
