@extends('frontend.layout')
@section('body-class', 'about-page')
@section('pageHeading')
  @if (!empty($pageHeading))
    {{ $pageHeading->about_page_title ?? __('Sobre nosotros') }}
  @else
    {{ __('Sobre nosotros') }}
  @endif
@endsection
@php
  $metaKeywords = !empty($seo->meta_keyword_about) ? $seo->meta_keyword_about : '';
  $metaDescription = !empty($seo->meta_description_about) ? $seo->meta_description_about : '';
@endphp

@section('meta-keywords', "{{ $metaKeywords }}")
@section('meta-description', "$metaDescription")
@section('canonical', url()->current())
@section('og-url', url()->current())
@section('og-type', 'website')

@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/front/css/organizer.css') }}">
@endpush

@section('hero-section')
  <!-- Page Banner — sobre nosotros: editorial + chips (scoped solo esta página) -->
  <section
    class="page-banner page-banner--about-premium overlay lazy"
    data-bg="{{ asset('assets/admin/img/' . $basicInfo->breadcrumb) }}"
    aria-labelledby="about-hero-heading">
    <div class="page-banner--about-premium__scrim" aria-hidden="true"></div>
    <div class="page-banner--about-premium__grain" aria-hidden="true"></div>
    <div class="container page-banner--about-premium__container">
      <header class="banner-inner about-page-banner">
        <h1 id="about-hero-heading" class="page-title about-page-banner__title">
          {{ $pageHeading ? $pageHeading->about_page_title : __('Sobre nosotros') }}</h1>
        <nav class="about-page-banner__nav" aria-label="{{ __('about_banner_nav_aria') }}">
          <ol class="breadcrumb about-page-banner__crumbs">
            <li class="breadcrumb-item about-page-banner__crumb">
              <a href="{{ route('index') }}">{{ __('Home') }}</a>
            </li>
            <li class="breadcrumb-item active about-page-banner__crumb about-page-banner__crumb--current" aria-current="page">
              {{ $pageHeading ? $pageHeading->about_page_title : __('Sobre nosotros') }}
            </li>
          </ol>
        </nav>
      </header>
    </div>
  </section>
  <!-- Page Banner End -->
@endsection
@section('content')
  <a href="#contenido-principal" class="about-skip-link sr-only sr-only-focusable">{{ __('Saltar al contenido principal') }}</a>
  <main id="contenido-principal" class="about-page-main" tabindex="-1">
  <!-- About Section Start -->
  @if ($secInfo->about_section_status == 1)
    <section class="about-section about-page__band about-page__band--surface-a" id="contenido-principal-sobre-nosotros"
      @if (!is_null($aboutUsSection))
        aria-labelledby="about-section-heading"
        @if (!empty(trim((string) ($aboutUsSection->subtitle ?? ''))))
          aria-describedby="about-section-subtitle"
        @endif
      @else
        aria-label="{{ __('Sobre nosotros') }}"
      @endif>
      <div class="container">
        @if (is_null($aboutUsSection))
          <p class="text-center about-section__empty mb-0" role="status">{{ __('No data found for about section') }}</p>
        @else
          @php
            $aboutMetrics = $aboutMetrics ?? config('about_metrics', []);
          @endphp
          <div class="row align-items-stretch about-section__split">
            <div class="col-lg-6 about-metrics-column">
              @if (!empty($aboutMetrics['enabled']) && !empty($aboutMetrics['stats']))
                @php
                  $__am = $aboutMetrics;
                  $__stats = [];
                  foreach ($__am['stats'] ?? [] as $__row) {
                    if (!empty($__row['value'] ?? null) && !empty($__row['label_key'] ?? null)) {
                      $__stats[] = $__row;
                    }
                  }
                  $__hero = $__stats[0] ?? null;
                  $__secondary = array_slice($__stats, 1);
                  $__vis = $__am['visual'] ?? [];
                  $__heroBars = $__vis['hero_bars'] ?? [38, 52, 71, 64, 88, 76];
                  $__spark = $__vis['sparkline'] ?? [18, 22, 19, 28, 35, 32, 41, 48, 55, 62, 58, 70];
                  $__meters = $__vis['meters'] ?? [];
                  $__nSpark = max(count($__spark), 2);
                  $__sparkPts = [];
                  foreach ($__spark as $__i => $__v) {
                    $__x = 2 + ($__i / ($__nSpark - 1)) * 116;
                    $__y = 40 - ((float) $__v / 100) * 34;
                    $__sparkPts[] = round($__x, 2) . ',' . round($__y, 2);
                  }
                  $__sparkPoly = implode(' ', $__sparkPts);
                @endphp
                <div class="about-metrics about-metrics--dashboard" role="region"
                  aria-label="{{ __('about_metrics_region_aria') }}">
                  <header class="about-metrics__header">
                    <p class="about-metrics__eyebrow">{{ __('about_metrics_eyebrow') }}</p>
                    <p class="about-metrics__intro">{{ __('about_metrics_intro') }}</p>
                  </header>

                  @if ($__hero)
                    <div class="about-metrics__bento">
                      <article class="about-metrics__hero"
                        aria-labelledby="about-metrics-hero-title about-metrics-hero-desc">
                        <div class="about-metrics__hero-text">
                          <span id="about-metrics-hero-title" class="about-metrics__value about-metrics__value--hero">{{ $__hero['value'] }}</span>
                          <span id="about-metrics-hero-desc" class="about-metrics__label about-metrics__label--hero">{{ __($__hero['label_key']) }}</span>
                        </div>
                        <div class="about-metrics__hero-viz" role="group"
                          aria-label="{{ __('about_metrics_chart_aria') }}">
                          <span class="about-metrics__viz-title">{{ __('about_metrics_chart_title') }}</span>
                          <svg class="about-metrics__svg-bars" viewBox="0 0 200 76" preserveAspectRatio="xMidYMid meet"
                            aria-hidden="true" focusable="false">
                            <defs>
                              <linearGradient id="about-metrics-bar-fill" x1="0" y1="1" x2="0" y2="0">
                                <stop offset="0%" stop-color="#ea580c" />
                                <stop offset="100%" stop-color="#F97316" />
                              </linearGradient>
                            </defs>
                            @foreach ($__heroBars as $__i => $__h)
                              @php
                                $__bw = 24;
                                $__gap = 9;
                                $__x = 5 + $__i * ($__bw + $__gap);
                                $__barH = max(6, ((float) $__h / 100) * 54);
                                $__y = 72 - $__barH;
                              @endphp
                              <rect class="about-metrics__bar" x="{{ $__x }}" y="{{ $__y }}" width="{{ $__bw }}"
                                height="{{ $__barH }}" rx="8" fill="url(#about-metrics-bar-fill)" />
                            @endforeach
                          </svg>
                          <span class="about-metrics__viz-note">{{ __('about_metrics_viz_disclaimer') }}</span>
                        </div>
                      </article>

                      <div class="about-metrics__spark-block" role="group" aria-label="{{ __('about_metrics_spark_aria') }}">
                        <div class="about-metrics__spark-head">
                          <span class="about-metrics__viz-title">{{ __('about_metrics_spark_title') }}</span>
                        </div>
                        <svg class="about-metrics__svg-spark" viewBox="0 0 120 44" preserveAspectRatio="none"
                          aria-hidden="true" focusable="false">
                          <defs>
                            <linearGradient id="about-metrics-spark-line" x1="0" y1="0" x2="1" y2="0">
                              <stop offset="0%" stop-color="#C2410C" />
                              <stop offset="100%" stop-color="#F97316" />
                            </linearGradient>
                            <linearGradient id="about-metrics-spark-fill" x1="0" y1="0" x2="0" y2="1">
                              <stop offset="0%" stop-color="rgba(249,115,22,0.22)" />
                              <stop offset="100%" stop-color="rgba(249,115,22,0)" />
                            </linearGradient>
                          </defs>
                          <polyline class="about-metrics__spark-area" fill="url(#about-metrics-spark-fill)"
                            stroke="none"
                            points="2,42 {{ $__sparkPoly }} 118,42" />
                          <polyline class="about-metrics__spark-line" fill="none" stroke="url(#about-metrics-spark-line)"
                            stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"
                            points="{{ $__sparkPoly }}" />
                        </svg>
                        <span class="about-metrics__viz-note">{{ __('about_metrics_viz_disclaimer_short') }}</span>
                      </div>

                      @if (count($__secondary) > 0)
                        <ul class="about-metrics__subs">
                          @foreach ($__secondary as $__si => $__st)
                            @php
                              $__meter = (int) ($__meters[$__si] ?? (70 - $__si * 3));
                              $__meter = max(18, min(100, $__meter));
                            @endphp
                            <li class="about-metrics__sub">
                              <div class="about-metrics__sub-top">
                                <span class="about-metrics__value about-metrics__value--sub">{{ $__st['value'] }}</span>
                              </div>
                              <span class="about-metrics__label about-metrics__label--sub">{{ __($__st['label_key']) }}</span>
                              <div class="about-metrics__meter" role="presentation"
                                aria-label="{{ __('about_metrics_meter_aria') }}">
                                <span class="about-metrics__meter-fill" style="--meter: {{ $__meter }}%;"></span>
                              </div>
                            </li>
                          @endforeach
                        </ul>
                      @endif
                    </div>
                  @endif

                  <footer class="about-metrics__footer">
                    <p class="about-metrics__note">{{ __('about_metrics_note') }}</p>
                  </footer>
                </div>
              @else
                <figure class="about-image-part pt-10 rmb-55 mb-0">
                  <img class="lazy" data-src="{{ asset('assets/admin/img/about-us-section/' . $aboutUsSection->image) }}"
                    alt="{{ trim((string) $aboutUsSection->title) !== '' ? __('Imagen ilustrativa: :title', ['title' => trim(strip_tags((string) $aboutUsSection->title))]) : __('Fotografía de la sección Sobre nosotros') }}"
                    loading="lazy" decoding="async">
                </figure>
              @endif
            </div>
            <div class="col-lg-6 about-story-column">
              <div class="about-story-premium">
                <article class="about-content about-content--premium">
                  <header class="about-content__head section-title mb-0">
                    <h2 id="about-section-heading">{{ $aboutUsSection->title ?: __('Sobre nosotros') }}</h2>
                    @if (!empty(trim((string) $aboutUsSection->subtitle)))
                      <p id="about-section-subtitle" class="about-content__subtitle">{{ $aboutUsSection->subtitle }}</p>
                    @endif
                  </header>
                  @if (!empty(trim(strip_tags((string) ($aboutUsSection->text ?? '')))))
                    <div class="about-content__body">
                      {!! $aboutUsSection->text !!}
                    </div>
                  @endif
                </article>
              </div>
            </div>
          </div>
        @endif
      </div>
    </section>
  @endif
  <!-- About Section End -->

  {{-- Propuesta B2B organizadores: jerarquía clara + tarjetas beneficio + CTA (i18n con HTML en *_html) --}}
  <section class="about-organizer-pitch about-page__band about-page__band--surface-b" id="para-organizadores"
    aria-labelledby="about-organizer-pitch-heading">
    <div class="container about-organizer-pitch__shell">
      <header class="about-organizer-pitch__header">
        <p class="about-organizer-pitch__eyebrow">{{ __('about_organizer_pitch_eyebrow') }}</p>
        <h2 id="about-organizer-pitch-heading" class="about-organizer-pitch__title">{!! __('about_organizer_pitch_title_html') !!}</h2>
        <p class="about-organizer-pitch__subline">{{ __('about_organizer_pitch_subline') }}</p>
        <p class="about-organizer-pitch__lead">{!! __('about_organizer_pitch_lead_html') !!}</p>
      </header>

      <div class="about-organizer-pitch__benefits" role="region"
        aria-labelledby="about-organizer-benefits-heading">
        <h3 id="about-organizer-benefits-heading" class="about-organizer-pitch__section-label">
          {{ __('about_organizer_pitch_benefits_heading') }}</h3>
        <div class="about-organizer-pitch__grid">
          <article class="about-organizer-pitch__card">
            <h4 class="about-organizer-pitch__card-title">{!! __('about_organizer_pitch_card_commission_title_html') !!}</h4>
            <p class="about-organizer-pitch__card-text">{!! __('about_organizer_pitch_card_commission_body_html') !!}</p>
          </article>
          <article class="about-organizer-pitch__card">
            <h4 class="about-organizer-pitch__card-title">{!! __('about_organizer_pitch_card_payout_title_html') !!}</h4>
            <p class="about-organizer-pitch__card-text">{!! __('about_organizer_pitch_card_payout_body_html') !!}</p>
          </article>
          <article class="about-organizer-pitch__card">
            <h4 class="about-organizer-pitch__card-title">{!! __('about_organizer_pitch_card_ops_title_html') !!}</h4>
            <p class="about-organizer-pitch__card-text">{!! __('about_organizer_pitch_card_ops_body_html') !!}</p>
          </article>
        </div>
      </div>

      <aside class="about-organizer-pitch__pullquote" aria-label="{{ __('about_organizer_pitch_pullquote_label') }}">
        <p class="about-organizer-pitch__pullquote-text">{!! __('about_organizer_pitch_vs_html') !!}</p>
      </aside>

      <p class="about-organizer-pitch__disclaimer">{{ __('about_organizer_pitch_disclaimer') }}</p>
      <div class="about-organizer-pitch__actions" role="group"
        aria-label="{{ __('about_organizer_pitch_aria_actions') }}">
        <a href="{{ route('organizer.signup') }}"
          class="theme-btn about-organizer-pitch__cta">{{ __('about_organizer_pitch_cta') }}</a>
        <a href="{{ route('events') }}"
          class="about-organizer-pitch__link">{{ __('about_organizer_pitch_secondary') }}</a>
      </div>
    </div>
  </section>

  <!-- Feature Section Start -->
  @if ($secInfo->features_section_status == 1)
    <section class="feature-section feature-section--about-premium about-page__band about-page__band--surface-b" id="caracteristicas"
      aria-labelledby="features-section-heading">
      <div class="feature-section--about-premium__ambient" aria-hidden="true"></div>
      <div class="container feature-section--about-premium__inner">
        <header class="feature-section--about-premium__header">
          <p class="feature-section--about-premium__eyebrow">{{ __('about_features_eyebrow') }}</p>
          <h2 id="features-section-heading" class="feature-section--about-premium__title">
            {{ $featureEventSection ? $featureEventSection->title : __('Características') }}</h2>
          @if (!empty(trim((string) ($featureEventSection->text ?? ''))))
            <p class="feature-section--about-premium__lead">{{ $featureEventSection->text }}</p>
          @endif
          @if (count($featureEventItems) < 1)
            <p class="feature-section--about-premium__empty mb-0 mt-3" role="status">{{ __('No data found for features section') }}</p>
          @endif
        </header>
        @if (count($featureEventItems) > 0)
          <div class="row justify-content-center feature-section--about-premium__grid">
            @foreach ($featureEventItems as $item)
              <div class="col-xl-4 col-md-6 d-flex align-items-stretch">
                <div class="feature-item feature-item--about-premium w-100">
                  <div class="feature-item__icon" aria-hidden="true">
                    <i class="{{ $item->icon }}"></i>
                  </div>
                  <div class="feature-content">
                    <h3 class="h5">{{ $item->title }}</h3>
                    <p>{{ $item->text }}</p>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </section>
  @endif
  <!-- Feature Section End -->

  <!-- Testimonial Section Start -->
  @if ($secInfo->testimonials_section_status == 1)
    <section class="testimonial-section about-page__band about-page__band--surface-a" id="testimonios"
      aria-labelledby="about-testimonials-heading">
      <div class="container">
        <div class="row about-page__testimonial-row">
          <div class="col-lg-4">
            <div class="testimonial-content about-page__testimonial-aside">
              <div class="section-title mb-30">
                <h2 id="about-testimonials-heading">{{ $testimonialData ? $testimonialData->title : __('What say our client about us') }}</h2>
              </div>
              <p>{{ $testimonialData ? $testimonialData->text : '' }}</p>
              <div class="total-client-reviews mt-40 bg-lighter">
                <div class="review-images mb-30">
                  @if (!is_null($testimonialData))
                    <img class="lazy" data-src="{{ asset('assets/admin/img/testimonial/' . $testimonialData->image) }}"
                      alt="{{ __('Reseña destacada') }}">
                  @else
                    <img class="lazy" data-src="{{ asset('assets/admin/img/testimonial/clients.png') }}"
                      alt="{{ __('Reseña destacada') }}">
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
    <section class="client-logo-area text-center about-page__band about-page__band--surface-b" id="aliados"
      aria-labelledby="about-partners-heading">
      <div class="container">
        <div class="section-title about-page__section-head mb-0">
          <h2 id="about-partners-heading">{{ $partnerInfo ? $partnerInfo->title : __('Nuestros aliados') }}</h2>
          <p>{{ $partnerInfo ? $partnerInfo->text : '' }}</p>
        </div>
        <div class="client-logo-wrap trust-partners__logos">
          @if (count($partners) > 0)
            @foreach ($partners as $item)
              <div class="client-logo-item">
                @php
                  $partnerUrl = trim((string) ($item->url ?? ''));
                @endphp
                @if ($partnerUrl !== '')
                  <a href="{{ $partnerUrl }}" target="_blank" rel="noopener noreferrer"
                    aria-label="{{ __('Visitar sitio del aliado estratégico') }}"><img class="lazy"
                      data-src="{{ asset('assets/admin/img/partner/' . $item->image) }}" alt=""></a>
                @else
                  <span><img class="lazy"
                      data-src="{{ asset('assets/admin/img/partner/' . $item->image) }}" alt="{{ __('Logo de aliado') }}"></span>
                @endif
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
  </main>
@endsection

@push('styles')
<style>
/*
  Sistema de bandas + tokens «awesome-design-md»: Apple (jerarquía, aire, elevación suave)
  + Airbnb (radios acogedores, acento cálido = marca). Ver: .references/awesome-design-md/DESIGN.about-tuki-page.md
*/
body.about-page {
  --about-surface-a: #ffffff;
  --about-surface-b: #f8fafc;
  --about-band-space: 4.5rem;
  --about-band-space-mobile: 3rem;
  --about-section-head-space: 2.25rem;

  --about-ds-ink: #0f172a;
  --about-ds-text: #334155;
  --about-ds-text-secondary: var(--about-ds-text-secondary);
  --about-ds-muted: var(--about-ds-muted);
  --about-ds-muted-2: var(--about-ds-muted-2);

  --about-ds-border-hair: rgba(30, 37, 50, 0.05);
  --about-ds-border-faint: rgba(30, 37, 50, 0.06);
  --about-ds-border-soft: rgba(30, 37, 50, 0.07);
  --about-ds-border: rgba(30, 37, 50, 0.08);

  --about-ds-radius-xs: 14px;
  --about-ds-radius-icon: 14px;
  --about-ds-radius-sm: 16px;
  --about-ds-radius-md: 18px;
  --about-ds-radius-lg: 20px;
  --about-ds-radius-xl: 22px;
  --about-ds-radius-pill: 999px;

  --about-ds-inset-highlight: 0 1px 0 rgba(255, 255, 255, 1) inset;
  --about-ds-shadow-card: var(--about-ds-inset-highlight),
    0 22px 48px -26px rgba(23, 27, 38, 0.12),
    0 8px 24px -16px rgba(23, 27, 38, 0.06);
  --about-ds-shadow-card-soft: var(--about-ds-inset-highlight),
    0 20px 50px -28px rgba(23, 27, 38, 0.16),
    0 6px 16px -10px rgba(23, 27, 38, 0.05);
  --about-ds-shadow-organizer:
    0 1px 0 rgba(255, 255, 255, 0.9) inset,
    0 12px 28px rgba(15, 23, 42, 0.05);
  --about-ds-shadow-organizer-hover:
    0 1px 0 rgba(255, 255, 255, 1) inset,
    0 16px 36px rgba(15, 23, 42, 0.1);
  --about-ds-shadow-chip:
    0 1px 0 rgba(255, 255, 255, 0.22) inset,
    0 14px 42px rgba(0, 0, 0, 0.2);

  --about-ds-hero-scrim-1: rgba(15, 23, 42, 0.28);
  --about-ds-hero-scrim-2: rgba(15, 23, 42, 0.46);
  --about-ds-hero-scrim-3: rgba(15, 23, 42, 0.8);
  --about-ds-accent-wash: rgba(249, 115, 22, 0.045);
}

/* Hero “Sobre nosotros”: legibilidad sobre foto + migas chip (inspiración editorial / vidrio) */
body.about-page .page-banner.page-banner--about-premium {
  position: relative;
  isolation: isolate;
  background-repeat: no-repeat;
  background-size: cover;
  background-position: center;
  padding-top: clamp(3.25rem, 9vw, 6.25rem);
  padding-bottom: clamp(2.75rem, 8vw, 5.25rem);
  overflow: hidden;
}

@media (max-width: 575.98px) {
  body.about-page .page-banner.page-banner--about-premium {
    padding-top: clamp(2.75rem, 11vw, 4rem);
    padding-bottom: clamp(2.5rem, 9vw, 3.75rem);
  }
}

body.about-page .page-banner--about-premium__scrim {
  position: absolute;
  inset: 0;
  z-index: 0;
  pointer-events: none;
  background: linear-gradient(
    180deg,
    var(--about-ds-hero-scrim-1) 0%,
    var(--about-ds-hero-scrim-2) 48%,
    var(--about-ds-hero-scrim-3) 100%
  );
}

body.about-page .page-banner--about-premium__grain {
  position: absolute;
  inset: 0;
  z-index: 1;
  pointer-events: none;
  opacity: 0.085;
  mix-blend-mode: overlay;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='240' height='240'%3E%3Cfilter id='g'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='240' height='240' filter='url(%23g)'/%3E%3C/svg%3E");
  background-size: 240px 240px;
}

body.about-page .page-banner--about-premium__container {
  position: relative;
  z-index: 2;
}

body.about-page .about-page-banner {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  line-height: 1.45;
  gap: 1lh;
  max-width: 52rem;
  margin-inline: auto;
}

body.about-page .about-page-banner__title {
  margin: 0;
  padding: 0;
  color: #fafaf9;
  font-family: var(--heading-font);
  font-weight: 700;
  letter-spacing: -0.045em;
  line-height: 1.06;
  font-size: clamp(2rem, 3.2vw + 1rem, 3.15rem);
  text-wrap: balance;
  text-shadow: 0 2px 32px rgba(0, 0, 0, 0.38);
}

body.about-page .about-page-banner__nav {
  margin: 0;
}

body.about-page .about-page-banner__crumbs {
  display: inline-flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: center;
  gap: 0.25rem 0.5rem;
  margin: 0;
  padding: 0.65rem 1.15rem 0.65rem 1.2rem;
  list-style: none;
  border-radius: var(--about-ds-radius-pill);
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(18px);
  -webkit-backdrop-filter: blur(18px);
  border: 1px solid rgba(255, 255, 255, 0.22);
  box-shadow: var(--about-ds-shadow-chip);
}

body.about-page .about-page-banner__crumbs .breadcrumb-item {
  display: inline-flex;
  align-items: center;
  padding: 0;
  font-size: 0.8125rem;
  font-weight: 600;
  letter-spacing: -0.02em;
  line-height: 1.35;
}

body.about-page .about-page-banner__crumbs .breadcrumb-item + .breadcrumb-item {
  padding-left: 0;
}

body.about-page .about-page-banner__crumbs .breadcrumb-item + .breadcrumb-item::before {
  content: '';
  display: inline-block;
  float: none;
  padding: 0;
  margin-right: 0.55rem;
  margin-left: 0;
  width: 5px;
  height: 5px;
  border-radius: 1px;
  border-right: 2px solid rgba(255, 255, 255, 0.45);
  border-bottom: 2px solid rgba(255, 255, 255, 0.45);
  transform: rotate(-45deg);
  vertical-align: middle;
  color: transparent !important;
}

body.about-page .about-page-banner__crumbs a {
  color: rgba(255, 255, 255, 0.94) !important;
  text-decoration: none;
  font-weight: 600;
  font-family: inherit;
  border-radius: 6px;
  transition: color 0.2s ease, background 0.2s ease;
}

body.about-page .about-page-banner__crumbs a:hover {
  color: #fff !important;
  text-decoration: underline;
  text-underline-offset: 3px;
}

body.about-page .about-page-banner__crumbs a:focus-visible {
  outline: 2px solid rgba(255, 255, 255, 0.88);
  outline-offset: 3px;
}

body.about-page .about-page-banner__crumb--current,
body.about-page .about-page-banner__crumbs .breadcrumb-item.active {
  color: rgba(255, 255, 255, 0.76) !important;
  font-weight: 500;
  font-family: var(--heading-font);
}

body.about-page main#contenido-principal > section.about-page__band {
  margin: 0;
  padding-top: var(--about-band-space);
  padding-bottom: var(--about-band-space);
}

@media (max-width: 767.98px) {
  body.about-page main#contenido-principal > section.about-page__band {
    padding-top: var(--about-band-space-mobile);
    padding-bottom: var(--about-band-space-mobile);
  }
}

body.about-page .about-page__band--surface-a {
  background-color: var(--about-surface-a);
}

body.about-page .about-page__band--surface-b {
  background-color: var(--about-surface-b);
  border-top: 1px solid var(--about-ds-border-hair);
}

body.about-page main#contenido-principal > section.about-page__band:first-of-type {
  border-top: none;
}

body.about-page #caracteristicas.about-page__band--surface-b {
  border-top: 1px solid var(--about-ds-border-hair);
}

#contenido-principal-sobre-nosotros,
#para-organizadores,
#caracteristicas,
#testimonios,
#aliados {
  scroll-margin-top: 6rem;
}

/* Ritmo vertical con `lh` (line-height del elemento): márgenes entre bloques de texto coherentes con la grilla tipográfica */
body.about-page .about-organizer-pitch__shell {
  max-width: 58rem;
  margin-inline: auto;
}

body.about-page .about-organizer-pitch__header {
  text-align: center;
  max-width: 42rem;
  margin-inline: auto;
  margin-block: 0 2lh;
}

body.about-page .about-organizer-pitch__eyebrow {
  font-size: 0.7rem;
  font-weight: 700;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: var(--primary-text-color);
  line-height: 1.4;
  margin-block: 0 1lh;
}

body.about-page .about-organizer-pitch__title {
  font-family: var(--heading-font);
  font-size: clamp(1.5rem, 1.4vw + 1rem, 2.05rem);
  font-weight: 800;
  letter-spacing: -0.04em;
  line-height: 1.12;
  color: var(--heading-color);
  margin-block: 0 1lh;
  text-wrap: balance;
}

body.about-page .about-organizer-pitch__title strong {
  color: var(--primary-text-color);
  font-weight: 800;
}

body.about-page .about-organizer-pitch__subline {
  margin-block: 0 1lh;
  font-size: clamp(0.88rem, 0.25vw + 0.82rem, 0.98rem);
  font-weight: 600;
  letter-spacing: 0.02em;
  color: var(--about-ds-muted);
  line-height: 1.45;
}

body.about-page .about-organizer-pitch__lead {
  margin-block: 0;
  text-align: left;
  font-size: clamp(1rem, 0.35vw + 0.93rem, 1.1rem);
  line-height: 1.65;
  color: #334155;
}

body.about-page .about-organizer-pitch__lead strong {
  color: #0f172a;
  font-weight: 700;
}

body.about-page .about-organizer-pitch__benefits {
  margin-block: 0 2lh;
}

body.about-page .about-organizer-pitch__section-label {
  font-family: var(--heading-font);
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  color: var(--about-ds-muted);
  text-align: center;
  line-height: 1.4;
  margin-block: 0 1lh;
}

body.about-page .about-organizer-pitch__grid {
  display: grid;
  grid-template-columns: 1fr;
  align-items: stretch;
  gap: 1lh;
}

@media (min-width: 768px) {
  body.about-page .about-organizer-pitch__grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 1lh;
  }
}

body.about-page .about-organizer-pitch__card {
  display: flex;
  flex-direction: column;
  gap: 1lh;
  box-sizing: border-box;
  height: 100%;
  min-height: 100%;
  padding: 1.5lh;
  border-radius: var(--about-ds-radius-md);
  background: #ffffff;
  border: 1px solid var(--about-ds-border);
  /* `lh` del cuerpo: mismo ritmo para gap/padding entre título y párrafo */
  font-size: 0.94rem;
  line-height: 1.58;
  box-shadow: var(--about-ds-shadow-organizer);
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

body.about-page .about-organizer-pitch__card:hover {
  border-color: rgba(249, 115, 22, 0.22);
  box-shadow: var(--about-ds-shadow-organizer-hover);
}

body.about-page .about-organizer-pitch__card-title {
  font-family: var(--heading-font);
  font-size: 1.05rem;
  font-weight: 700;
  letter-spacing: -0.03em;
  line-height: 1.25;
  color: #0f172a;
  margin-block: 0;
}

body.about-page .about-organizer-pitch__card-title strong {
  color: var(--primary-text-color);
  font-weight: 800;
}

body.about-page .about-organizer-pitch__card-text {
  margin-block: 0;
  flex: 1 1 auto;
  color: var(--about-ds-text-secondary);
}

body.about-page .about-organizer-pitch__card-text strong {
  color: #172131;
  font-weight: 700;
}

body.about-page .about-organizer-pitch__pullquote {
  margin-block: 0 1lh;
  padding-block: 1.5lh;
  padding-inline: 1.5lh;
  border-radius: var(--about-ds-radius-xs);
  background: linear-gradient(135deg, rgba(255, 247, 237, 0.95) 0%, rgba(255, 255, 255, 0.88) 100%);
  border: 1px solid rgba(249, 115, 22, 0.2);
  border-left: 4px solid var(--primary-color);
}

body.about-page .about-organizer-pitch__pullquote-text {
  margin-block: 0;
  font-size: 1rem;
  line-height: 1.62;
  font-weight: 500;
  color: #334155;
}

body.about-page .about-organizer-pitch__pullquote-text strong {
  color: #c2410c;
  font-weight: 700;
}

body.about-page .about-organizer-pitch__disclaimer {
  font-size: 0.78rem;
  line-height: 1.45;
  color: var(--about-ds-muted-2);
  margin-block: 0 1lh;
  text-align: center;
  max-width: 40rem;
  margin-inline: auto;
}

body.about-page .about-organizer-pitch__actions {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: center;
  gap: 1lh;
}

body.about-page .about-organizer-pitch__cta.theme-btn {
  padding-block: 1lh;
  padding-inline: 2lh;
  border-radius: var(--about-ds-radius-pill);
  font-weight: 700;
}

body.about-page .about-organizer-pitch__link {
  font-weight: 600;
  color: var(--primary-text-color);
  text-decoration: underline;
  text-underline-offset: 3px;
}

body.about-page .about-organizer-pitch__link:hover {
  color: var(--secondary-color, #ea580c);
}

body.about-page .about-page__section-head {
  margin-bottom: var(--about-section-head-space) !important;
}

body.about-page .about-page__testimonial-row {
  padding-bottom: 0;
}

body.about-page .testimonial-section hr {
  display: none;
}

body.about-page .about-page__testimonial-aside {
  padding-top: 0;
  margin-bottom: 0;
}

@media (max-width: 991px) {
  body.about-page .about-page__testimonial-aside {
    margin-bottom: var(--about-section-head-space);
  }
}

/* Sobre nosotros — IA: lectura, landmarks, jerarquía (solo .about-page) */
/* Saltar contenido — 2.4.1 (Bypass Blocks); focus visible — 2.4.7 */
.about-page .about-skip-link.sr-only-focusable:focus {
  position: fixed;
  z-index: 10000;
  top: 12px;
  left: 12px;
  width: auto;
  height: auto;
  padding: 12px 16px;
  margin: 0;
  clip: auto;
  overflow: visible;
  background: #0f172a;
  color: #fff;
  font-size: 0.95rem;
  font-weight: 700;
  text-decoration: underline;
  border-radius: 8px;
  box-shadow: 0 10px 30px rgba(15, 23, 42, 0.35);
  outline: none;
}
.about-page .about-skip-link:focus-visible {
  outline: 3px solid #f97316;
  outline-offset: 2px;
}

.about-page #contenido-principal:focus-visible {
  outline: 2px solid #c2410c;
  outline-offset: 4px;
}

#contenido-principal,
#contenido-principal-sobre-nosotros {
  scroll-margin-top: 6rem;
}

.about-page .about-section__empty {
  font-size: 1.05rem;
  line-height: 1.6;
  color: var(--about-ds-muted);
  max-width: 36rem;
  margin-inline: auto;
}

/* Dos columnas: misma altura en lg+ (stretch + flex); misma “cápsula” que .about-story-premium */
@media (min-width: 992px) {
  #contenido-principal-sobre-nosotros .about-section__split {
    align-items: stretch;
  }

  #contenido-principal-sobre-nosotros .about-metrics-column,
  #contenido-principal-sobre-nosotros .about-story-column {
    display: flex;
    flex-direction: column;
  }

  #contenido-principal-sobre-nosotros .about-metrics--dashboard,
  #contenido-principal-sobre-nosotros .about-story-premium {
    flex: 1 1 auto;
    width: 100%;
    min-height: 100%;
    display: flex;
    flex-direction: column;
  }

  #contenido-principal-sobre-nosotros .about-metrics__bento {
    flex: 1 1 auto;
  }

  #contenido-principal-sobre-nosotros .about-metrics__footer {
    margin-top: auto;
  }
}

/* Columna izquierda — panel métricas + gráficos (config/about_metrics.php + visual) */
#contenido-principal-sobre-nosotros .about-metrics-column {
  position: relative;
}

#contenido-principal-sobre-nosotros .about-metrics--dashboard {
  position: relative;
  box-sizing: border-box;
  padding: clamp(2.35rem, 5vw, 3.5rem) clamp(2rem, 4.5vw, 2.85rem) clamp(2.5rem, 5vw, 3.65rem);
  padding-inline-start: clamp(2.35rem, 4.5vw, 3.15rem);
  border-radius: var(--about-ds-radius-xl);
  background:
    radial-gradient(ellipse 120% 80% at 100% -15%, rgba(249, 115, 22, 0.06), transparent 50%),
    linear-gradient(165deg, #fbfbfc 0%, #ffffff 45%, #f8f9fb 100%);
  border: 1px solid var(--about-ds-border);
  box-shadow: var(--about-ds-shadow-card);
  overflow: hidden;
}

#contenido-principal-sobre-nosotros .about-metrics__header {
  margin-bottom: 1.25rem;
}

#contenido-principal-sobre-nosotros .about-metrics__eyebrow {
  margin: 0 0 0.45rem;
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  color: var(--primary-text-color);
}

#contenido-principal-sobre-nosotros .about-metrics__intro {
  margin: 0;
  max-width: 32rem;
  font-size: clamp(0.9rem, 0.32vw + 0.84rem, 1rem);
  line-height: 1.55;
  letter-spacing: 0.01em;
  color: #5c6678;
}

#contenido-principal-sobre-nosotros .about-metrics__bento {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

#contenido-principal-sobre-nosotros .about-metrics__hero {
  display: grid;
  grid-template-columns: minmax(0, 1fr) minmax(0, 1.15fr);
  gap: clamp(1rem, 2.5vw, 1.5rem);
  align-items: stretch;
  padding: clamp(1.15rem, 2.5vw, 1.45rem);
  border-radius: var(--about-ds-radius-lg);
  background: #ffffff;
  border: 1px solid var(--about-ds-border-soft);
  box-shadow: 0 10px 32px -20px rgba(23, 27, 38, 0.12);
}

#contenido-principal-sobre-nosotros .about-metrics__hero-text {
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 0.55rem;
  min-width: 0;
}

#contenido-principal-sobre-nosotros .about-metrics__value--hero {
  font-family: var(--heading-font);
  font-size: clamp(2.1rem, 3.5vw + 1rem, 3.05rem);
  font-weight: 800;
  letter-spacing: -0.055em;
  line-height: 0.95;
  color: var(--heading-color);
  font-variant-numeric: tabular-nums;
}

#contenido-principal-sobre-nosotros .about-metrics__label--hero {
  font-size: clamp(0.8rem, 0.25vw + 0.74rem, 0.92rem);
  line-height: 1.4;
  color: #5b677a;
  font-weight: 600;
  letter-spacing: 0.01em;
  text-transform: none;
}

#contenido-principal-sobre-nosotros .about-metrics__hero-viz {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  min-width: 0;
}

#contenido-principal-sobre-nosotros .about-metrics__viz-title {
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: #8894a8;
}

#contenido-principal-sobre-nosotros .about-metrics__svg-bars {
  width: 100%;
  height: auto;
  max-height: 92px;
  display: block;
}

#contenido-principal-sobre-nosotros .about-metrics__svg-bars rect {
  transform-origin: bottom center;
  transform-box: fill-box;
  transform: scaleY(0.96);
}

@media (prefers-reduced-motion: no-preference) {
  #contenido-principal-sobre-nosotros .about-metrics__svg-bars rect:nth-of-type(1) {
    animation: about-metrics-bar-rise 0.85s cubic-bezier(0.22, 1, 0.36, 1) both;
    animation-delay: 0s;
  }
  #contenido-principal-sobre-nosotros .about-metrics__svg-bars rect:nth-of-type(2) {
    animation: about-metrics-bar-rise 0.85s cubic-bezier(0.22, 1, 0.36, 1) both;
    animation-delay: 0.07s;
  }
  #contenido-principal-sobre-nosotros .about-metrics__svg-bars rect:nth-of-type(3) {
    animation: about-metrics-bar-rise 0.85s cubic-bezier(0.22, 1, 0.36, 1) both;
    animation-delay: 0.14s;
  }
  #contenido-principal-sobre-nosotros .about-metrics__svg-bars rect:nth-of-type(4) {
    animation: about-metrics-bar-rise 0.85s cubic-bezier(0.22, 1, 0.36, 1) both;
    animation-delay: 0.21s;
  }
  #contenido-principal-sobre-nosotros .about-metrics__svg-bars rect:nth-of-type(5) {
    animation: about-metrics-bar-rise 0.85s cubic-bezier(0.22, 1, 0.36, 1) both;
    animation-delay: 0.28s;
  }
  #contenido-principal-sobre-nosotros .about-metrics__svg-bars rect:nth-of-type(6) {
    animation: about-metrics-bar-rise 0.85s cubic-bezier(0.22, 1, 0.36, 1) both;
    animation-delay: 0.35s;
  }
}

@keyframes about-metrics-bar-rise {
  from {
    transform: scaleY(0.15);
    opacity: 0.5;
  }
  to {
    transform: scaleY(0.96);
    opacity: 1;
  }
}

#contenido-principal-sobre-nosotros .about-metrics__viz-note {
  font-size: 0.68rem;
  line-height: 1.35;
  color: var(--about-ds-muted-2);
}

#contenido-principal-sobre-nosotros .about-metrics__spark-block {
  padding: 1rem 1.1rem 1.05rem;
  border-radius: var(--about-ds-radius-md);
  background: linear-gradient(180deg, #ffffff 0%, #fafbfc 100%);
  border: 1px solid var(--about-ds-border-faint);
  box-shadow: 0 8px 24px -18px rgba(23, 27, 38, 0.1);
}

#contenido-principal-sobre-nosotros .about-metrics__spark-head {
  margin-bottom: 0.45rem;
}

#contenido-principal-sobre-nosotros .about-metrics__svg-spark {
  width: 100%;
  height: 52px;
  display: block;
}

#contenido-principal-sobre-nosotros .about-metrics__subs {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 0.65rem;
  margin: 0;
  padding: 0;
  list-style: none;
}

#contenido-principal-sobre-nosotros .about-metrics__sub {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
  padding: 0.85rem 0.75rem 0.9rem;
  border-radius: var(--about-ds-radius-sm);
  background: #ffffff;
  border: 1px solid var(--about-ds-border-faint);
  box-shadow: 0 4px 16px -10px rgba(23, 27, 38, 0.1);
}

#contenido-principal-sobre-nosotros .about-metrics__sub-top {
  min-height: 2.5rem;
  display: flex;
  align-items: flex-end;
}

#contenido-principal-sobre-nosotros .about-metrics__value--sub {
  font-family: var(--heading-font);
  font-size: clamp(1.15rem, 1.2vw + 0.75rem, 1.45rem);
  font-weight: 700;
  letter-spacing: -0.04em;
  line-height: 1.05;
  color: var(--heading-color);
  font-variant-numeric: tabular-nums;
}

#contenido-principal-sobre-nosotros .about-metrics__label--sub {
  font-size: 0.65rem;
  line-height: 1.4;
  color: #6a7384;
  font-weight: 600;
  letter-spacing: 0.06em;
  text-transform: uppercase;
}

#contenido-principal-sobre-nosotros .about-metrics__meter {
  position: relative;
  height: 5px;
  margin-top: 0.35rem;
  border-radius: var(--about-ds-radius-pill);
  background: var(--about-ds-border-faint);
  overflow: hidden;
}

#contenido-principal-sobre-nosotros .about-metrics__meter-fill {
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: var(--meter, 70%);
  border-radius: inherit;
  background: linear-gradient(90deg, #c2410c, #f97316);
  transition: width 1s cubic-bezier(0.22, 1, 0.36, 1);
}

#contenido-principal-sobre-nosotros .about-metrics__footer {
  margin-top: 1.2rem;
}

#contenido-principal-sobre-nosotros .about-metrics__note {
  margin: 0;
  max-width: none;
  font-size: 0.78rem;
  line-height: 1.5;
  color: var(--about-ds-muted-2);
}

@media (max-width: 767.98px) {
  #contenido-principal-sobre-nosotros .about-metrics__hero {
    grid-template-columns: 1fr;
  }

  #contenido-principal-sobre-nosotros .about-metrics__subs {
    grid-template-columns: 1fr;
  }
}

@media (prefers-reduced-motion: reduce) {
  #contenido-principal-sobre-nosotros .about-metrics__svg-bars rect {
    animation: none !important;
    transform: scaleY(0.96);
    opacity: 1;
  }

  #contenido-principal-sobre-nosotros .about-metrics__meter-fill {
    transition: none;
  }
}

/*
  Columna de historia — tarjeta blanca, más aire interno; sombra suave sin tintes cálidos fuertes.
*/
#contenido-principal-sobre-nosotros .about-story-column {
  position: relative;
}

#contenido-principal-sobre-nosotros .about-story-premium {
  position: relative;
  isolation: isolate;
  padding: clamp(2.35rem, 5vw, 3.5rem) clamp(2rem, 4.5vw, 2.85rem) clamp(2.5rem, 5vw, 3.65rem);
  padding-inline-start: clamp(2.35rem, 4.5vw, 3.15rem);
  border-radius: var(--about-ds-radius-xl);
  background: #ffffff;
  border: 1px solid var(--about-ds-border);
  box-shadow: var(--about-ds-shadow-card);
  overflow: hidden;
}

@media (prefers-reduced-motion: no-preference) {
  #contenido-principal-sobre-nosotros .about-story-premium {
    animation: about-story-premium-in 0.75s cubic-bezier(0.22, 1, 0.36, 1) both;
  }
}

@keyframes about-story-premium-in {
  from {
    opacity: 0;
    transform: translateY(14px);
  }
  to {
    opacity: 1;
    transform: none;
  }
}

#contenido-principal-sobre-nosotros .about-story-premium::before {
  content: "";
  position: absolute;
  inset: 0;
  background: radial-gradient(ellipse 90% 55% at 50% -40%, var(--about-ds-accent-wash), transparent 52%);
  pointer-events: none;
  z-index: 0;
}

#contenido-principal-sobre-nosotros .about-story-premium::after {
  content: "";
  position: absolute;
  left: 0;
  top: 10%;
  bottom: 10%;
  width: 4px;
  border-radius: 0 4px 4px 0;
  background: linear-gradient(
    180deg,
    var(--primary-color) 0%,
    #ea580c 42%,
    rgba(249, 115, 22, 0.28) 100%
  );
  pointer-events: none;
  z-index: 0;
}

html[dir="rtl"] #contenido-principal-sobre-nosotros .about-story-premium::after {
  left: auto;
  right: 0;
  border-radius: 4px 0 0 4px;
}

/*
  Ritmo vertical con lh: márgenes en múltiplos de línea (requiere line-height en el bloque).
*/
#contenido-principal-sobre-nosotros article.about-content--premium {
  max-width: none;
  width: 100%;
}

#contenido-principal-sobre-nosotros .about-content--premium {
  position: relative;
  z-index: 1;
  font-size: 1.0625rem;
  line-height: 1.72;
  font-family: var(--base-font);
  color: #3d4a5c;
}

#contenido-principal-sobre-nosotros .about-content__head.section-title {
  display: flex;
  flex-direction: column;
  gap: 1.1lh;
  margin-block-end: 1.35lh;
  line-height: 1.22;
}

#contenido-principal-sobre-nosotros .about-content--premium .about-content__head h2 {
  margin-block: 0;
  max-width: 22rem;
  font-family: var(--heading-font);
  font-weight: 700;
  letter-spacing: -0.035em;
  line-height: 1.08;
  font-size: clamp(1.85rem, 2.4vw + 1rem, 2.65rem);
  color: var(--heading-color);
  text-wrap: balance;
}

#contenido-principal-sobre-nosotros .about-content__subtitle {
  margin-block: 0;
  max-width: 42rem;
  font-size: clamp(1.08rem, 0.55vw + 0.95rem, 1.2rem);
  font-weight: 500;
  line-height: 1.58;
  color: #4b5568;
  letter-spacing: -0.01em;
}

#contenido-principal-sobre-nosotros .about-content__body {
  font-size: 1.02rem;
  line-height: 1.78;
  color: #3f4a59;
  max-width: min(100%, 42rem);
  padding-block-start: 0.15rem;
}

/*
  Bloques .feature-item del CMS dentro del cuerpo — mini-tarjetas (superficie + tipo editorial).
  Pisa .about-content .feature-item { padding:0; border:none } del tema con mayor especificidad.
*/
#contenido-principal-sobre-nosotros .about-content--premium .about-content__body .feature-item {
  display: flex;
  flex-direction: row;
  align-items: flex-start;
  gap: 1.2rem;
  box-sizing: border-box;
  width: 100%;
  margin: 0;
  padding: 1.5rem 1.55rem 1.55rem 1.5rem;
  border-radius: var(--about-ds-radius-md);
  border: 1px solid var(--about-ds-border-soft);
  background: #ffffff;
  box-shadow:
    0 1px 0 rgba(255, 255, 255, 1) inset,
    0 10px 28px -16px rgba(23, 27, 38, 0.1),
    0 2px 8px -4px rgba(23, 27, 38, 0.05);
  transition: border-color 0.3s ease, box-shadow 0.35s ease, transform 0.35s cubic-bezier(0.22, 1, 0.36, 1);
}

@media (hover: hover) {
  #contenido-principal-sobre-nosotros .about-content--premium .about-content__body .feature-item:hover {
    border-color: rgba(249, 115, 22, 0.2);
    box-shadow:
      0 1px 0 rgba(255, 255, 255, 0.92) inset,
      0 18px 40px -22px rgba(23, 27, 38, 0.15),
      0 6px 20px -10px rgba(249, 115, 22, 0.12);
  }
}

@media (hover: hover) and (prefers-reduced-motion: no-preference) {
  #contenido-principal-sobre-nosotros .about-content--premium .about-content__body .feature-item:hover {
    transform: translateY(-3px);
  }
}

#contenido-principal-sobre-nosotros .about-content--premium .about-content__body .feature-item[class*="mt-"] {
  margin-top: 0 !important;
}

#contenido-principal-sobre-nosotros .about-content--premium .about-content__body .feature-item .feature-item__icon {
  flex-shrink: 0;
  margin-top: 0.12rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.75rem !important;
  height: 2.75rem !important;
  border-radius: var(--about-ds-radius-xs) !important;
  background: linear-gradient(
    165deg,
    rgba(249, 115, 22, 0.16) 0%,
    rgba(249, 115, 22, 0.05) 100%
  ) !important;
  border: 1px solid rgba(249, 115, 22, 0.12) !important;
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.65) !important;
}

#contenido-principal-sobre-nosotros .about-content--premium .about-content__body .feature-item .feature-item__icon i {
  font-size: 1.15rem !important;
  line-height: 1 !important;
  color: var(--primary-text-color) !important;
}

#contenido-principal-sobre-nosotros .about-content--premium .about-content__body .feature-item > i {
  flex-shrink: 0;
  margin-top: 0.2rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.75rem;
  height: 2.75rem;
  font-size: 1.15rem !important;
  border-radius: var(--about-ds-radius-xs);
  background: linear-gradient(
    165deg,
    rgba(249, 115, 22, 0.16) 0%,
    rgba(249, 115, 22, 0.05) 100%
  );
  border: 1px solid rgba(249, 115, 22, 0.12);
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.65);
  color: var(--primary-text-color) !important;
}

#contenido-principal-sobre-nosotros .about-content--premium .about-content__body .feature-item .feature-content {
  flex: 1;
  min-width: 0;
  padding-block: 0.2rem 0.08rem;
  padding-inline-end: 0.2rem;
  font-size: 0.98rem;
  line-height: 1.75;
  letter-spacing: 0.01em;
  color: #4b5568;
}

#contenido-principal-sobre-nosotros .about-content--premium .about-content__body .feature-item .feature-content h3,
#contenido-principal-sobre-nosotros .about-content--premium .about-content__body .feature-item .feature-content h4,
#contenido-principal-sobre-nosotros .about-content--premium .about-content__body .feature-item .feature-content h5 {
  margin: 0 0 0.55rem;
  font-family: var(--heading-font);
  font-size: clamp(1.05rem, 0.4vw + 0.95rem, 1.2rem);
  font-weight: 600;
  letter-spacing: -0.025em;
  line-height: 1.32;
  color: var(--heading-color);
}

#contenido-principal-sobre-nosotros .about-content--premium .about-content__body .feature-item .feature-content p {
  margin: 0;
  font-size: 0.98rem;
  line-height: 1.75;
  color: #4b5568;
}

#contenido-principal-sobre-nosotros .about-content--premium .about-content__body .feature-item .feature-content > p + p {
  margin-top: 0.65rem;
}

@media (max-width: 575.98px) {
  #contenido-principal-sobre-nosotros .about-content--premium .about-content__body .feature-item {
    flex-direction: column;
    align-items: stretch;
    gap: 1rem;
    padding: 1.35rem 1.25rem 1.4rem;
  }

  #contenido-principal-sobre-nosotros .about-content--premium .about-content__body .feature-item > i {
    margin-top: 0;
    width: 2.5rem;
    height: 2.5rem;
    font-size: 1.05rem !important;
  }

  #contenido-principal-sobre-nosotros .about-content--premium .about-content__body .feature-item .feature-item__icon {
    width: 2.5rem !important;
    height: 2.5rem !important;
  }

  #contenido-principal-sobre-nosotros .about-content--premium .about-content__body .feature-item .feature-item__icon i {
    font-size: 1.05rem !important;
  }
}

#contenido-principal-sobre-nosotros .about-content__body > :first-child {
  margin-block-start: 0;
}

#contenido-principal-sobre-nosotros .about-content__body > * + * {
  margin-block-start: 1lh;
}

/* Un poco más de aire entre mini-tarjetas consecutivas */
#contenido-principal-sobre-nosotros .about-content__body > .feature-item + .feature-item {
  margin-block-start: 1.12lh;
}

#contenido-principal-sobre-nosotros .about-content__body p {
  margin-block: 0;
}

#contenido-principal-sobre-nosotros .about-content__body h3,
#contenido-principal-sobre-nosotros .about-content__body h4 {
  line-height: 1.28;
  margin-block: 1lh 0;
  letter-spacing: -0.02em;
  color: var(--heading-color);
}

#contenido-principal-sobre-nosotros .about-content__body h3:first-child,
#contenido-principal-sobre-nosotros .about-content__body h4:first-child {
  margin-block-start: 0;
}

#contenido-principal-sobre-nosotros .about-content__body ul,
#contenido-principal-sobre-nosotros .about-content__body ol {
  margin-block: 1lh 0;
  padding-inline-start: 1.3rem;
  line-height: 1.72;
}

#contenido-principal-sobre-nosotros .about-content__body li + li {
  margin-block-start: 0.5lh;
}

.about-page .about-image-part img {
  border-radius: var(--about-ds-radius-sm);
  box-shadow: 0 20px 48px -24px rgba(23, 27, 38, 0.18);
}

/* Enlaces: contraste + subrayado (1.4.1) */
#contenido-principal-sobre-nosotros .about-content__body a {
  color: #9a3412;
  font-weight: 600;
  text-decoration: underline;
  text-decoration-thickness: 1.5px;
  text-underline-offset: 3px;
  transition: color 0.2s ease, text-underline-offset 0.2s ease;
}

#contenido-principal-sobre-nosotros .about-content__body a:hover {
  color: #7c2d12;
  text-underline-offset: 4px;
}

#contenido-principal-sobre-nosotros .about-content__body a:focus-visible {
  outline: 2px solid #c2410c;
  outline-offset: 3px;
  border-radius: 3px;
}

@media (max-width: 991.98px) {
  #contenido-principal-sobre-nosotros .about-story-premium {
    margin-top: 1.5rem;
  }

  #contenido-principal-sobre-nosotros .about-content--premium .about-content__head h2 {
    max-width: none;
  }
}

@media (prefers-reduced-motion: reduce) {
  #contenido-principal-sobre-nosotros .about-story-premium {
    animation: none;
  }

  #contenido-principal-sobre-nosotros .about-content__body a {
    transition: none;
  }

  #contenido-principal-sobre-nosotros .about-content--premium .about-content__body .feature-item {
    transition: none;
  }

  #contenido-principal-sobre-nosotros .about-content--premium .about-content__body .feature-item:hover {
    transform: none;
  }

  .about-page #caracteristicas.feature-section--about-premium .feature-item--about-premium {
    animation: none;
  }

  .about-page #caracteristicas.feature-section--about-premium .feature-item--about-premium:hover {
    transform: none;
  }
}

/*
  Características (#caracteristicas) — solo página Sobre nosotros: superficie editorial + rejilla cálida tipo Airbnb,
  tipografía y aire tipo Apple. No afecta otras vistas que usen .feature-section.
*/
/* Características: misma base que home — superficie b + acento muy suave (sin otro “template”) */
.about-page #caracteristicas.feature-section--about-premium {
  position: relative;
  overflow: hidden;
  margin-bottom: 0 !important;
  background: transparent;
}

.about-page #caracteristicas .feature-section--about-premium__ambient {
  pointer-events: none;
  position: absolute;
  inset: 0;
  opacity: 1;
  background-image: radial-gradient(ellipse 80% 50% at 50% 0%, var(--about-ds-accent-wash), transparent 58%);
}

.about-page #caracteristicas .feature-section--about-premium__ambient::after {
  display: none;
}

.about-page #caracteristicas .feature-section--about-premium__inner {
  position: relative;
  z-index: 1;
}

.about-page #caracteristicas .feature-section--about-premium__header {
  text-align: center;
  max-width: 46rem;
  margin-inline: auto;
  margin-bottom: var(--about-section-head-space);
}

.about-page #caracteristicas .feature-section--about-premium__eyebrow {
  margin: 0 0 0.65rem;
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.2em;
  text-transform: uppercase;
  color: var(--primary-text-color);
}

.about-page #caracteristicas .feature-section--about-premium__title {
  margin: 0 0 1rem;
  font-family: var(--heading-font);
  font-size: clamp(1.85rem, 2.5vw + 1rem, 2.65rem);
  font-weight: 700;
  letter-spacing: -0.045em;
  line-height: 1.1;
  color: var(--heading-color);
  text-wrap: balance;
}

.about-page #caracteristicas .feature-section--about-premium__lead {
  margin: 0 auto;
  max-width: 38rem;
  font-size: clamp(1rem, 0.45vw + 0.92rem, 1.12rem);
  line-height: 1.65;
  letter-spacing: 0.01em;
  color: #5a6474;
}

.about-page #caracteristicas .feature-section--about-premium__empty {
  font-size: 0.95rem;
  color: var(--about-ds-muted);
}

.about-page #caracteristicas .feature-section--about-premium__grid {
  /* Paridad con .feature-grid en la home (30px desktop, 18px en móvil) */
  --feature-gap: 30px;
  margin-left: calc(-0.5 * var(--feature-gap));
  margin-right: calc(-0.5 * var(--feature-gap));
}

@media (max-width: 767.98px) {
  .about-page #caracteristicas .feature-section--about-premium__grid {
    --feature-gap: 18px;
  }
}

.about-page #caracteristicas .feature-section--about-premium__grid > [class*="col-"] {
  padding-left: calc(0.5 * var(--feature-gap));
  padding-right: calc(0.5 * var(--feature-gap));
  margin-bottom: var(--feature-gap);
}

.about-page #caracteristicas .feature-item.feature-item--about-premium {
  position: relative;
  height: 100%;
  flex-direction: column;
  align-items: flex-start;
  gap: 1.15rem;
  padding: clamp(1.45rem, 3vw, 1.85rem) clamp(1.35rem, 2.8vw, 1.65rem);
  border-radius: var(--about-ds-radius-xl);
  background:
    linear-gradient(165deg, rgba(255, 255, 255, 0.97) 0%, rgba(255, 255, 255, 0.88) 100%);
  border: 1px solid var(--about-ds-border);
  box-shadow: var(--about-ds-shadow-card-soft);
  transition: transform 0.45s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.45s ease, border-color 0.35s ease;
  overflow: hidden;
}

.about-page #caracteristicas .feature-item--about-premium::before {
  content: "";
  position: absolute;
  top: 0;
  left: 1.25rem;
  right: 1.25rem;
  height: 1px;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(249, 115, 22, 0.35),
    transparent
  );
  opacity: 0.85;
  pointer-events: none;
}

@media (prefers-reduced-motion: no-preference) {
  .about-page #caracteristicas .feature-item--about-premium {
    animation: about-feature-card-in 0.75s cubic-bezier(0.22, 1, 0.36, 1) both;
  }

  .about-page #caracteristicas .feature-section--about-premium__grid > div:nth-child(1) .feature-item--about-premium {
    animation-delay: 0.04s;
  }
  .about-page #caracteristicas .feature-section--about-premium__grid > div:nth-child(2) .feature-item--about-premium {
    animation-delay: 0.11s;
  }
  .about-page #caracteristicas .feature-section--about-premium__grid > div:nth-child(3) .feature-item--about-premium {
    animation-delay: 0.18s;
  }
  .about-page #caracteristicas .feature-section--about-premium__grid > div:nth-child(4) .feature-item--about-premium {
    animation-delay: 0.25s;
  }
  .about-page #caracteristicas .feature-section--about-premium__grid > div:nth-child(5) .feature-item--about-premium {
    animation-delay: 0.32s;
  }
  .about-page #caracteristicas .feature-section--about-premium__grid > div:nth-child(6) .feature-item--about-premium {
    animation-delay: 0.39s;
  }
}

@keyframes about-feature-card-in {
  from {
    opacity: 0;
    transform: translateY(18px);
  }
  to {
    opacity: 1;
    transform: none;
  }
}

@media (hover: hover) and (prefers-reduced-motion: no-preference) {
  .about-page #caracteristicas .feature-item--about-premium:hover {
    transform: translateY(-5px);
    border-color: rgba(249, 115, 22, 0.22);
    box-shadow:
      0 1px 0 rgba(255, 255, 255, 1) inset,
      0 28px 56px -28px rgba(23, 27, 38, 0.18),
      0 12px 28px -14px rgba(249, 115, 22, 0.12);
  }
}

.about-page #caracteristicas .feature-item--about-premium .feature-item__icon {
  width: 3.25rem;
  height: 3.25rem;
  border-radius: var(--about-ds-radius-md);
  background: linear-gradient(155deg, rgba(255, 255, 255, 0.95) 0%, rgba(249, 115, 22, 0.1) 100%);
  border: 1px solid rgba(249, 115, 22, 0.15);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.85),
    0 8px 20px -12px rgba(249, 115, 22, 0.35);
}

.about-page #caracteristicas .feature-item--about-premium .feature-item__icon i {
  font-size: 1.45rem;
}

.about-page #caracteristicas .feature-item--about-premium .feature-content h3,
.about-page #caracteristicas .feature-item--about-premium .feature-content .h5 {
  font-family: var(--heading-font);
  font-size: clamp(1.05rem, 0.35vw + 0.95rem, 1.2rem);
  font-weight: 600;
  letter-spacing: -0.03em;
  line-height: 1.25;
  margin-bottom: 0.55rem;
  color: var(--heading-color);
}

.about-page #caracteristicas .feature-item--about-premium .feature-content p {
  margin: 0;
  font-size: 0.96rem;
  line-height: 1.68;
  color: #4f5968;
}

@media (max-width: 767.98px) {
  .about-page #caracteristicas .feature-item--about-premium .feature-item__icon {
    width: 2.85rem;
    height: 2.85rem;
    border-radius: 16px;
  }

  .about-page #caracteristicas .feature-item--about-premium .feature-item__icon i {
    font-size: 1.25rem;
  }
}
</style>
@endpush

@push('scripts')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => __('Inicio'),
            'item' => url('/'),
        ],
        [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => !empty($pageHeading) ? ($pageHeading->about_page_title ?? __('Sobre nosotros')) : __('Sobre nosotros'),
            'item' => url()->current(),
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush
