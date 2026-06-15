@extends('frontend.layout')

@section('hero-preload')
  @if (!empty($firstHeroSlideUrl))
    <link rel="preload" as="image" href="{{ $firstHeroSlideUrl }}" fetchpriority="high">
  @endif
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset(app()->environment('production') ? 'assets/front/css/home.min.css' : 'assets/front/css/home.css') }}">
@endpush

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
@section('canonical',      route('index', [], true))

@push('critical-styles')
  <style>
    body.home-page {
      line-height: 1.75;
    }
    body.home-page .page-wrapper {
      position: relative;
      width: 100%;
      min-width: 300px;
      overflow: hidden;
    }
    body.home-page .hero-section {
      display: flex;
      align-items: center;
      justify-content: center;
    }
    body.home-page .hero-collage-section,
    body.home-page .hero-collage-section--premium {
      position: relative;
      min-height: 440px !important;
      height: auto !important;
      overflow: hidden;
      background: #111827;
      user-select: none;
      -webkit-user-select: none;
    }
    body.home-page .hero-slideshow,
    body.home-page .hero-overlay,
    body.home-page .hero-vignette,
    body.home-page .hero-noise,
    body.home-page .hero-ambient {
      position: absolute;
      inset: 0;
      pointer-events: none;
    }
    body.home-page .hero-slideshow {
      z-index: 0;
    }
    body.home-page .hero-slide {
      position: absolute;
      inset: -20px;
      background-size: cover;
      background-position: center;
      opacity: 0;
      pointer-events: none;
    }
    body.home-page .hero-slide:first-child {
      opacity: 1;
    }
    body.home-page .hero-overlay {
      z-index: 1;
      background:
        radial-gradient(120% 90% at 50% 120%, rgba(15, 20, 32, 0.92) 0%, transparent 55%),
        linear-gradient(180deg, rgba(18, 22, 34, 0.35) 0%, rgba(12, 16, 26, 0.82) 48%, rgba(8, 11, 18, 0.94) 100%);
    }
    body.home-page .hero-vignette {
      z-index: 2;
      background: radial-gradient(ellipse 85% 75% at 50% 45%, transparent 0%, rgba(5, 8, 14, 0.55) 100%);
    }
    body.home-page .hero-noise,
    body.home-page .hero-ambient {
      z-index: 3;
    }
    body.home-page .hero-content-wrapper {
      position: relative;
      z-index: 5;
      width: 100%;
      padding-top: clamp(56px, 8vw, 96px);
      padding-bottom: clamp(44px, 6vw, 72px);
    }
    body.home-page .hero-content {
      max-width: 760px;
      margin: 0 auto;
      text-align: center;
    }
    body.home-page .hero-content.hero-content--premium {
      max-width: 920px;
    }
    body.home-page .hero-content h1,
    body.home-page .hero-content p {
      color: #fff;
    }
    body.home-page .hero-content--premium h1,
    body.home-page #heroHeadingHome {
      max-width: 980px;
      margin: 0 auto;
      font-family: var(--heading-font);
      font-size: clamp(2.15rem, 1.2vw + 1.85rem, 3.65rem);
      font-weight: 800;
      line-height: 1.05;
      letter-spacing: 0;
      text-wrap: balance;
      text-shadow: 0 24px 48px rgba(0, 0, 0, 0.35);
    }
    body.home-page .hero-content .hero-lede {
      max-width: 38rem;
      margin: 18px auto 0;
      font-size: clamp(0.9rem, 0.65vw + 0.78rem, 1.05rem);
      line-height: 1.58;
      font-weight: 500;
      color: rgba(248, 250, 252, 0.88);
    }
    body.home-page .hero-actions {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      flex-wrap: wrap;
      margin-top: clamp(22px, 3vw, 30px);
    }
    body.home-page .hero-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 48px;
      padding: 0 24px;
      border: 1px solid transparent;
      border-radius: 16px;
      font-size: 13px;
      font-weight: 700;
      letter-spacing: 0;
      text-decoration: none;
      box-shadow: 0 10px 24px rgba(15, 23, 42, 0.12);
    }
    body.home-page .hero-btn--primary {
      background: #fff;
      color: #111827;
    }
    body.home-page .hero-btn--secondary {
      background: rgba(255, 255, 255, 0.04);
      border-color: rgba(255, 255, 255, 0.20);
      color: #fff;
    }
    body.home-page .events-marquee {
      position: relative;
      overflow: hidden;
      background: #fff;
      padding: 24px 0 34px;
    }
    body.home-page .hs-header {
      display: flex;
      align-items: flex-end;
      justify-content: space-between;
      gap: 16px;
      flex-wrap: wrap;
    }
    body.home-page .hs-header__left {
      display: flex;
      flex-direction: column;
      gap: 4px;
    }
    body.home-page .hs-header__title {
      margin: 0;
      color: #111827;
      font-size: 26px;
      font-weight: 800;
      line-height: 1.2;
      letter-spacing: 0;
    }
    body.home-page .hs-header__sub {
      margin: 0;
      color: #6b7280;
      font-size: 14px;
      line-height: 1.75;
    }
    body.home-page .hs-header__cta {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      flex-shrink: 0;
      padding: 9px 18px;
      border: 1.5px solid #e5e7eb;
      border-radius: 9px;
      color: #374151;
      font-size: 13px;
      font-weight: 700;
      line-height: 1.75;
      text-decoration: none;
      white-space: nowrap;
    }
    body.home-page .mb-32 {
      margin-bottom: 32px;
    }
    body.home-page .events-marquee-track {
      position: relative;
      display: block;
      width: 100vw;
      margin-left: calc(50% - 50vw);
      overflow: hidden;
    }
    body.home-page .events-marquee-inner {
      display: flex;
      width: max-content;
      gap: 12px;
    }
    body.home-page .events-marquee-item {
      position: relative;
      display: block;
      flex-shrink: 0;
      width: 328px;
      height: 208px;
      overflow: hidden;
      border-radius: 18px;
      background: #f8fafc;
      text-decoration: none;
      box-shadow: 0 12px 30px rgba(15, 23, 42, 0.09);
    }
    body.home-page .events-marquee-item img {
      display: block;
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    body.home-page .emq-date {
      position: absolute;
      top: 14px;
      left: 14px;
      z-index: 3;
      display: inline-flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-width: 46px;
      padding: 7px 8px;
      border-radius: 12px;
      background: rgba(255,255,255,0.92);
      box-shadow: 0 8px 18px rgba(15,23,42,0.16);
    }
    body.home-page .emq-date__day {
      color: #1e2532;
      font-family: var(--tuki-font-sans);
      font-size: 20px;
      font-weight: 800;
      line-height: 1;
    }
    body.home-page .emq-date__month {
      color: #f97316;
      font-family: var(--tuki-font-sans);
      font-size: 10px;
      font-weight: 800;
      line-height: 1.1;
      text-transform: uppercase;
    }
    body.home-page .emq-bottom {
      position: absolute;
      right: 0;
      bottom: 0;
      left: 0;
      z-index: 3;
      padding: 30px 16px 14px;
      background: linear-gradient(to top, rgba(17,24,39,0.94) 0%, rgba(17,24,39,0.76) 42%, rgba(17,24,39,0.30) 68%, rgba(17,24,39,0) 100%);
      pointer-events: none;
    }
    body.home-page .emq-bottom__eyebrow {
      display: inline-flex;
      margin-bottom: 8px;
      color: rgba(255,255,255,0.70);
      font-size: 11px;
      font-weight: 700;
      line-height: 1.2;
      text-transform: uppercase;
    }
    body.home-page .emq-bottom__title {
      display: -webkit-box;
      min-height: calc(15px * 1.25 * 2);
      overflow: hidden;
      color: #fff;
      font-family: var(--tuki-font-sans);
      font-size: 15px;
      font-weight: 800;
      line-height: 1.25;
      -webkit-box-orient: vertical;
      -webkit-line-clamp: 2;
    }
    body.home-page .hs-search-wrap {
      position: relative;
      z-index: 10;
      background: #fff;
      padding: 8px 0 44px;
    }
    body.home-page .hs-search-form {
      display: flex;
      align-items: center;
      overflow: hidden;
      margin-bottom: 16px;
      border: 1px solid rgba(30, 37, 50, 0.12);
      border-radius: 12px;
      background: #fff;
    }
    body.home-page .hs-sf__field {
      display: flex;
      align-items: center;
      gap: 10px;
      height: 56px;
      min-width: 0;
      padding: 0 14px;
    }
    body.home-page .hs-sf__field--grow {
      flex: 1 1 0%;
      max-width: min(440px, 42%);
    }
    body.home-page .hs-sf__input,
    body.home-page .hs-sf__select {
      width: 100%;
      min-width: 0;
      border: 0;
      outline: 0;
      background: transparent;
      color: #1e2532;
      font-family: var(--tuki-font-sans);
      font-size: 15px;
      font-weight: 500;
      line-height: 1;
    }
    body.home-page .hs-sf__divider {
      flex-shrink: 0;
      width: 1px;
      height: 30px;
      background: rgba(30,37,50,0.08);
    }
    body.home-page .hs-sf__btn {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 56px;
      flex: 0 0 clamp(128px, 11vw, 168px);
      padding: 0;
      border: 0;
      background: #f97316;
      color: #1e2532;
      font-family: var(--tuki-font-sans);
      font-size: 14px;
      font-weight: 600;
      line-height: 1;
    }
    @media (max-width: 767px) {
      body.home-page .hero-collage-section,
      body.home-page .hero-collage-section--premium {
        min-height: 420px !important;
      }
      body.home-page .hero-content-wrapper {
        padding-top: 56px;
        padding-bottom: 44px;
      }
      body.home-page .hero-content--premium h1,
      body.home-page #heroHeadingHome {
        font-size: clamp(2.05rem, 10vw, 3.05rem);
        line-height: 1.04;
      }
      body.home-page .events-marquee-item {
        width: 270px;
        height: 178px;
      }
      body.home-page .events-marquee {
        padding-bottom: 30px;
      }
      body.home-page .hs-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
      }
      body.home-page .emq-bottom {
        padding: 26px 14px 13px;
      }
      body.home-page .emq-bottom__title {
        min-height: calc(14px * 1.25 * 2);
        font-size: 14px;
      }
    }
    @media (max-width: 575px) {
      body.home-page .events-marquee-item {
        width: 250px;
        height: 170px;
      }
      body.home-page .hero-actions {
        gap: 10px;
      }
      body.home-page .hero-btn {
        width: 100%;
        max-width: 280px;
      }
      body.home-page .hs-search-form {
        flex-direction: column;
        align-items: stretch;
      }
      body.home-page .hs-sf__field,
      body.home-page .hs-sf__btn {
        width: 100%;
      }
    }
    @media (max-width: 420px) {
      body.home-page .events-marquee-item {
        width: 234px;
        height: 162px;
      }
    }
  </style>
@endpush

@section('hero-section')
  <!-- Hero Section Start -->
  <section class="hero-section hero-collage-section hero-collage-section--premium" id="heroSection" aria-labelledby="heroHeadingHome">

    {{-- Slideshow de fondo --}}
    <div class="hero-slideshow" id="heroCollageBg">
      @forelse($heroSlideUrls ?? [] as $slideUrl)
        <div class="hero-slide" style="background-image: url('{{ $slideUrl }}'); aspect-ratio: 1920 / 800;"></div>
      @empty
        <div class="hero-slide" style="background-image: url('{{ asset('assets/admin/img/' . $basicInfo->breadcrumb) }}'); aspect-ratio: 1920 / 800;"></div>
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
        $galleryImage = \App\Services\FileUploadService::eventVisualUrl(
          $marqueeGallery[$ev->id] ?? null,
          $ev->thumbnail
        );

        return [
          'event'  => $ev,
          'src'    => $galleryImage,
          'url'    => route('event.details', [$ev->slug, $ev->id]),
          'badge'  => $badgeMap[$ev->id] ?? null,
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
            @endfor
          </div>
        </div>
      </div>
    </section>
  @endif
  <!-- Event Images Marquee End -->
@endsection
@section('content')
<main id="main-content" tabindex="-1">

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
            $ev_wishlist_map = $wishlistMap ?? [];
            $eventsall = $featuredEventsAll ?? collect();
          @endphp
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
            @foreach ($eventCategories as $item)
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
</main>
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
