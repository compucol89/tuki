@extends('frontend.layout')
@section('pageHeading')
  {{ __('Home') }}
@endsection
@section('body-class', 'home-page')

@php
  $metaKeywords = !empty($seo->meta_keyword_home) ? $seo->meta_keyword_home : '';
  $metaDescription = !empty($seo->meta_description_home) ? $seo->meta_description_home : '';
@endphp
@section('meta-keywords', "{{ $metaKeywords }}")
@section('meta-description', "$metaDescription")

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
        <form id="event-search" class="event-search mt-35" name="event-search" action="{{ route('events') }}" method="get">
          <div class="search-item">
            <label for="borwseby"><i class="fas fa-list"></i></label>
            <select name="category" id="borwseby">
              <option value="">{{ __('All Category') }}</option>
              @foreach ($categories as $category)
                <option value="{{ $category->slug }}">{{ $category->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="search-item">
            <label for="search"><i class="fas fa-search"></i></label>
            <input type="search" id="search" name="search-input" placeholder="{{ __('Search Anything') }}">
          </div>
          <button type="submit" class="theme-btn">{{ $heroSection ? $heroSection->first_button : __('Search') }}</button>
        </form>
      </div>
    </div>
  </section>
  <!-- Hero Section End -->

  <!-- Event Images Marquee Start -->
  @if ($marqueeEvents->isNotEmpty())
    <div class="events-marquee">

      {{-- Fila 1 — izquierda --}}
      <div class="events-marquee-track">
        <div class="events-marquee-inner">
          @for ($copy = 0; $copy < 4; $copy++)
            @foreach ($marqueeEvents as $event)
              <a href="{{ route('event.details', [$event->slug, $event->id]) }}" class="events-marquee-item">
                <img src="{{ asset('assets/admin/img/event/thumbnail/' . $event->thumbnail) }}" alt="{{ $event->title }}" loading="lazy">
                <div class="events-marquee-info">
                  <span class="events-marquee-title">{{ Str::limit($event->title, 28) }}</span>
                  @if($event->pricing_type === 'free' || !$event->min_price)
                    <span class="events-marquee-price events-marquee-price--free">Gratis</span>
                  @else
                    <span class="events-marquee-price">{{ symbolPrice($event->min_price) }}</span>
                  @endif
                </div>
              </a>
            @endforeach
          @endfor
        </div>
      </div>

      {{-- Fila 2 — derecha --}}
      <div class="events-marquee-track">
        <div class="events-marquee-inner events-marquee-inner--reverse">
          @for ($copy = 0; $copy < 4; $copy++)
            @foreach ($marqueeEvents->reverse() as $event)
              <a href="{{ route('event.details', [$event->slug, $event->id]) }}" class="events-marquee-item">
                <img src="{{ asset('assets/admin/img/event/thumbnail/' . $event->thumbnail) }}" alt="{{ $event->title }}" loading="lazy">
                <div class="events-marquee-info">
                  <span class="events-marquee-title">{{ Str::limit($event->title, 28) }}</span>
                  @if($event->pricing_type === 'free' || !$event->min_price)
                    <span class="events-marquee-price events-marquee-price--free">Gratis</span>
                  @else
                    <span class="events-marquee-price">{{ symbolPrice($event->min_price) }}</span>
                  @endif
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

  <!-- Events Section Start -->
  @if ($secInfo->featured_section_status == 1)
    <section class="events-section pt-20 rpt-30 pb-90 rpb-70 bg-lighter">
      <div class="container">

        @if ($eventCategories->isEmpty())
          <p class="text-center">{{ __('No Events Found') }}</p>
        @else
          <nav>
            <div class="nav nav-tabs events-tabs mb-40" id="nav-tab" role="tablist">
              <button class="nav-link active" id="nav-all-tab" data-toggle="tab" data-target="#nav-all" type="button"
                role="tab" aria-controls="nav-all" aria-selected="true">{{ __('All') }}</button>
              @foreach ($eventCategories as $item)
                <button class="nav-link" id="nav-{{ $item->id }}-tab" data-toggle="tab"
                  data-target="#nav-{{ $item->id }}" type="button" role="tab"
                  aria-controls="nav-{{ $item->id }}" aria-selected="false">{{ $item->name }}</button>
              @endforeach
            </div>
          </nav>

          <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-all" role="tabpanel" aria-labelledby="nav-all-tab">
              <div class="row">
                @php
                  $now_time = \Carbon\Carbon::now();
                  $eventsall = DB::table('event_contents')
                      ->join('events', 'events.id', '=', 'event_contents.event_id')
                      ->where([
                          ['event_contents.language_id', '=', $currentLanguageInfo->id],
                          ['events.status', 1],
                          ['events.end_date_time', '>=', $now_time],
                          ['events.is_featured', '=', 'yes'],
                      ])
                      ->orderBy('events.created_at', 'desc')
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
                    ->where([
                        ['event_contents.event_category_id', '=', $item->id],
                        ['event_contents.language_id', '=', $currentLanguageInfo->id],
                        ['events.status', 1],
                        ['events.end_date_time', '>=', $now_time],
                        ['events.is_featured', '=', 'yes'],
                    ])
                    ->orderBy('events.created_at', 'desc')
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
    <section class="category-section pt-110 rpt-90 pb-80 rpb-60">
      <div class="container">
        <div class="section-title mb-60">
          <h2>{{ $secTitleInfo ? $secTitleInfo->category_section_title : __('Categories') }}</h2>
        </div>
        <div class="category-wrap text-white">
          @if (count($eventCategories) > 0)
            @foreach ($eventCategories as $item)
              <a href="{{ route('events', ['category' => $item->slug]) }}" class="category-item">
                <img class="lazy" data-src="{{ asset('assets/admin/img/event-category/' . $item->image) }}"
                  alt="Category">
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
    <section class="about-section pb-120 rpb-95">
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
  <section class="feature-section pt-110 rpt-90 bg-lighter">
    @if ($secInfo->features_section_status == 1)
      <div class="container pb-40 rpb-30">
        <div class="section-title text-center mb-55">
          <h2>{{ $featureEventSection ? $featureEventSection->title : '' }}</h2>
          <p>{{ $featureEventSection ? $featureEventSection->text : '' }}</p>
          @if (count($featureEventItems) < 1)
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
            <div class="work-process-inner pt-50 rpt-90 pb-40 rpb-60">

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
    <section class="testimonial-section pt-120 rpt-80">
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
              @if (count($testimonials) > 0)
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
    <section class="client-logo-area text-center pt-95 rpt-80 pb-90 rpb-70">
      <div class="container">
        <div class="section-title mb-55">
          <h2>{{ $partnerInfo ? $partnerInfo->title : __('Our Partner') }}</h2>
          <p>{{ $partnerInfo ? $partnerInfo->text : '' }}</p>
        </div>
        <div class="client-logo-wrap">
          @if (count($partners) > 0)
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
  // --- Crossfade slideshow (sin gap) ---
  var slides = Array.from(document.querySelectorAll('#heroCollageBg .hero-slide'));
  var n = slides.length;
  if (n === 0) return;

  // Mostrar el primer slide SIN transición (evita el gris inicial al cargar)
  slides[0].style.transition = 'none';
  slides[0].style.opacity    = '1';
  slides[0].style.zIndex     = '0';

  if (n === 1) return; // nada más que hacer

  var cur = 0;
  setInterval(function() {
    var nxt = (cur + 1) % n;

    // Nuevo slide arriba, actual queda debajo
    slides[cur].style.zIndex = '0';
    slides[nxt].style.zIndex = '1';

    // Fade-in del nuevo (con transición)
    slides[nxt].style.transition = 'opacity 1.2s ease-in-out';
    slides[nxt].style.opacity    = '1';

    // Cuando termina el fade, ocultar el anterior SIN transición
    // (ya está tapado, no se ve, no hace falta desvanecer)
    var prev = cur;
    setTimeout(function() {
      slides[prev].style.transition = 'none';
      slides[prev].style.opacity    = '0';
    }, 1200);

    cur = nxt;
  }, 5000);

  // --- Parallax ---
  var hero = document.getElementById('heroSection');
  var bg   = document.getElementById('heroCollageBg');
  if (!hero || !bg) return;

  var tx = 0, ty = 0, cx = 0, cy = 0;

  hero.addEventListener('mousemove', function(e) {
    var r = hero.getBoundingClientRect();
    tx = -(e.clientX - r.left) / r.width  * 14 + 7;
    ty = -(e.clientY - r.top)  / r.height * 14 + 7;
  });

  hero.addEventListener('mouseleave', function() { tx = 0; ty = 0; });

  (function loop() {
    cx += (tx - cx) * 0.04;
    cy += (ty - cy) * 0.04;
    bg.style.transform = 'translate(' + cx.toFixed(2) + 'px,' + cy.toFixed(2) + 'px)';
    requestAnimationFrame(loop);
  })();
})();

// Scroll reveal
(function() {
  var els = document.querySelectorAll(
    '.events-section, .category-section, .about-section, .feature-section, .testimonial-section, .client-logo-area, .work-process-area, .partner-area'
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
