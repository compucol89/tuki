@extends('frontend.layout')

@section('body-class', 'page-event-detail')

@php
  $cleanSeoText = function ($value) {
    return trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
  };

  $eventName = $cleanSeoText($content->title);
  $eventUrl = $canonical ?? route('event.details', ['slug' => $content->eventSlug ?? $content->slug, 'id' => $content->id], true);
  $eventMode = $content->event_type == 'online' ? 'online' : 'presencial';
  $eventDateLabel = !empty($startDateTime)
    ? \Carbon\Carbon::parse($startDateTime, $websiteTimezone ?? $websiteInfo->timezone)->locale('es')->translatedFormat('j \d\e F \d\e Y')
    : 'próximamente';

  $metaDescriptionSource = $cleanSeoText($content->meta_description ?? '');
  $descriptionSource = $cleanSeoText($content->description ?? '');
  $placeholderPatterns = ['lorem ipsum', 'pseudo-latin text', 'placeholder text'];

  if ($metaDescriptionSource !== '' && !\Illuminate\Support\Str::contains(\Illuminate\Support\Str::lower($metaDescriptionSource), $placeholderPatterns)) {
    $seoDescription = $metaDescriptionSource;
  } elseif ($descriptionSource !== '' && !\Illuminate\Support\Str::contains(\Illuminate\Support\Str::lower($descriptionSource), $placeholderPatterns)) {
    $seoDescription = $descriptionSource;
  } else {
    $seoDescription = "{$eventName} es un evento {$eventMode} en TukiPass. Reservá tu lugar para el {$eventDateLabel} y accedé a toda la información.";
  }

  $seoDescription = \Illuminate\Support\Str::limit($cleanSeoText($seoDescription), 158, '');
@endphp

@section('pageHeading', $eventName)
@section('meta-keywords', $content->meta_keywords ?? '')
@section('meta-description', $seoDescription)
@section('og-title', $eventName . ' | ' . $websiteInfo->website_title)
@section('og-description', $seoDescription)
@section('og-image', $og_image ?? asset('assets/admin/img/event/thumbnail/' . $content->thumbnail))
@section('og-image-alt', $og_image_alt ?? $eventName)
@section('og-image-width', '1200')
@section('og-image-height', '630')
@section('og-url', $eventUrl)
@section('og-type', 'event')
@section('canonical', $eventUrl)

@section('custom-style')
  <link rel="stylesheet" href="{{ asset('assets/admin/css/summernote-content.css') }}">
@endsection

@push('scripts')
@php
  $schemaStart = !empty($startDateTime)
    ? \Carbon\Carbon::parse($startDateTime, $websiteTimezone ?? $websiteInfo->timezone)
    : null;
  $schemaEnd = !empty($endDateTime)
    ? \Carbon\Carbon::parse($endDateTime, $websiteTimezone ?? $websiteInfo->timezone)
    : null;

  if (!empty($schemaStart) && !empty($schemaEnd)) {
    if ($schemaEnd->lessThanOrEqualTo($schemaStart) || $schemaStart->diffInDays($schemaEnd) > 31) {
      $schemaEnd = null;
    }
  }

  $schemaStartDate = !empty($schemaStart) ? $schemaStart->toIso8601String() : null;
  $schemaEndDate = !empty($schemaEnd) ? $schemaEnd->toIso8601String() : null;
  $schemaDescription = $seoDescription;
  $schemaLocationName = collect([$content->address, $content->city, $content->state, $content->country])->filter()->implode(', ');
  $schemaLocation = null;

  if ($content->event_type == 'online') {
    $schemaLocation = [
      '@type' => 'VirtualLocation',
      'url' => $eventUrl,
      'name' => __('Evento online'),
    ];
  } elseif ($schemaLocationName !== '') {
    $schemaLocation = [
      '@type' => 'Place',
      'name' => $schemaLocationName,
      'address' => array_filter([
        '@type' => 'PostalAddress',
        'streetAddress' => $content->address ?? '',
        'addressLocality' => $content->city ?? '',
        'addressRegion' => $content->state ?? '',
        'postalCode' => $content->zip_code ?? '',
        'addressCountry' => $content->country ?? '',
      ]),
    ];
  }

  $jsonLd = [
    '@context' => 'https://schema.org',
    '@type' => 'Event',
    'name' => $eventName,
    'description' => \Illuminate\Support\Str::limit($schemaDescription, 300, ''),
    'startDate' => $schemaStartDate,
    'endDate' => $schemaEndDate,
    'eventStatus' => 'https://schema.org/EventScheduled',
    'eventAttendanceMode' => 'https://schema.org/' . ($content->event_type == 'online' ? 'OnlineEventAttendanceMode' : 'OfflineEventAttendanceMode'),
    'location' => $schemaLocation,
    'image' => !empty($og_image) ? [$og_image] : null,
    'url' => $eventUrl,
    'organizer' => [
      '@type' => 'Organization',
      'name' => !empty($organizer) ? $organizer->username : $websiteInfo->website_title,
    ],
  ];
  if (
    !$over &&
    (
      (is_numeric($ticketSummary['min_ticket_price'] ?? null) && (float) $ticketSummary['min_ticket_price'] >= 0)
      || (($content->pricing_type ?? null) === 'free')
    )
  ) {
    $jsonLd['offers'] = [
      '@type' => 'Offer',
      'price' => is_numeric($ticketSummary['min_ticket_price'] ?? null) ? $ticketSummary['min_ticket_price'] : 0,
      'priceCurrency' => $event_currency ?? 'ARS',
      'availability' => 'https://schema.org/InStock',
      'url' => $eventUrl,
    ];
  }
  $jsonLd = array_filter($jsonLd, function ($value) {
    return !is_null($value) && $value !== '';
  });
@endphp
<script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>

@if(!empty($content->meta_pixel_id))
<!-- Meta Pixel -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '{{ $content->meta_pixel_id }}');
fbq('track', 'ViewContent', {content_name: {!! json_encode($content->title, JSON_UNESCAPED_UNICODE | JSON_HEX_AMP) !!}, content_type: 'event'});
</script>
@endif

@if(!empty($content->google_analytics_id))
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $content->google_analytics_id }}"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', '{{ $content->google_analytics_id }}');
</script>
@endif

@if(!empty($content->tiktok_pixel_id))
<!-- TikTok Pixel -->
<script>
!function(w,d,t){w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"];ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e};ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{};ttq._i[e]=[];ttq._i[e]._u=i;ttq._t=ttq._t||{};ttq._t[e]=+new Date;ttq._n=ttq._n||{};ttq._n[e]=n||{};var o=document.createElement("script");o.type="text/javascript";o.async=!0;o.src=i+"?sdkid="+e+"\x26lib="+t;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
ttq.load('{{ $content->tiktok_pixel_id }}');
ttq.page();
}(window, document, 'ttq');
</script>
@endif
@endpush

@push('scripts')
<script>
(function() {
  var form = document.querySelector('.sidebar-sticky form');
  if (!form) return;
  form.addEventListener('submit', function() {
    var btn = form.querySelector('button[type="submit"]');
    if (btn && !btn.classList.contains('btn-loading')) {
      btn.classList.add('btn-loading');
      btn.setAttribute('aria-busy', 'true');
      btn.setAttribute('disabled', 'disabled');
      btn.textContent = '{{ __("Procesando...") }}';
    }
  });
})();
</script>
@endpush

@section('content')
  <!-- Event Details V2 -->
  @php
    $map_address = preg_replace('/\s+/u', ' ', trim($content->address));
    $map_address = str_replace('/', ' ', $map_address);
    $map_address = str_replace('?', ' ', $map_address);
    $map_address = str_replace(',', ' ', $map_address);
    $eventDescriptionHtml = clean($content->description);
    $eventDescriptionHtml = preg_replace('/<\s*h1\b/i', '<h2', $eventDescriptionHtml);
    $eventDescriptionHtml = preg_replace('/<\s*\/\s*h1\s*>/i', '</h2>', $eventDescriptionHtml);
  @endphp

  {{-- Hero --}}
  <div class="ed-hero">
    {{-- Background image (blurred, like home hero) --}}
    <div class="ed-hero__slide" style="background-image: url('{{ asset('assets/admin/img/event/thumbnail/' . $content->thumbnail) }}');"></div>
    {{-- Overlay oscuro --}}
    <div class="ed-hero__overlay"></div>
    {{-- Textura noise --}}
    <div class="ed-hero__noise"></div>

    <div class="container ed-hero__actions-wrap">
      <div class="ed-hero__actions">
        @if (Auth::guard('customer')->check())
          @php
            $customer_id = Auth::guard('customer')->user()->id;
            $event_id = $content->id;
            $checkWishList = checkWishList($event_id, $customer_id);
          @endphp
        @else
          @php $checkWishList = false; @endphp
        @endif
        <a href="{{ $checkWishList == false ? route('addto.wishlist', $content->id) : route('remove.wishlist', $content->id) }}"
          class="ed-hero__btn {{ $checkWishList == true ? 'text-success' : '' }}"
          aria-label="{{ $checkWishList ? __('Remove from wishlist') : __('Add to wishlist') }}">
          <i class="fas fa-bookmark"></i>
        </a>
        <button type="button" class="ed-hero__btn" data-toggle="modal" data-target=".share-event" aria-label="{{ __('Share event') }}">
          <i class="fas fa-share-alt"></i>
        </button>
        @if ($content->event_type != 'online' && !empty($map_address))
          <button type="button" class="ed-hero__btn" data-toggle="modal" data-target=".bd-example-modal-lg" aria-label="{{ __('Map') }}">
            <i class="fas fa-map-marker-alt"></i>
          </button>
        @endif
      </div>
    </div>

    <div class="ed-hero__inner">
      <div class="container">
        @if (!$over)
          <div class="ed-hero__signalbar" aria-label="{{ __('Señales del evento') }}">
            <div class="ed-viewer-counter" aria-label="{{ $ev_viewers }} {{ __('personas viendo este evento ahora') }}">
              <span class="ed-viewer-counter__live" aria-hidden="true"></span>
              <span class="ed-viewer-counter__icon" aria-hidden="true">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </span>
              <span class="ed-viewer-counter__num">{{ $ev_viewers }}</span>
              <span class="ed-viewer-counter__label">{{ __('personas viendo ahora') }}</span>
            </div>

            <div class="ed-nudge" role="status" aria-live="polite" id="edNudge">
              <span class="ed-nudge__icon" aria-hidden="true" id="edNudgeIcon"></span>
              <span class="ed-nudge__text" id="edNudgeText"></span>
            </div>
          </div>
        @endif

        <h1 class="ed-hero__title">{{ $content->title }}</h1>

        @php $heroDate = \Carbon\Carbon::parse($heroDateTimestamp)->timezone($websiteInfo->timezone); @endphp
        <div class="ed-hero__meta">
          <span class="ed-hero__meta-item ed-hero__meta-item--date">
            <svg width="14" height="14" stroke-width="2" aria-hidden="true"><use href="#icon-calendar"/></svg>
            <span class="ed-date__part ed-date__part--day">{{ ucfirst($heroDate->translatedFormat('l')) }}</span>
            <span class="ed-date__sep" aria-hidden="true">·</span>
            <span class="ed-date__part ed-date__part--num">{{ $heroDate->format('j') }}</span>
            <span class="ed-date__sep" aria-hidden="true">·</span>
            <span class="ed-date__part ed-date__part--month">{{ ucfirst($heroDate->translatedFormat('F')) }}</span>
            <span class="ed-date__sep" aria-hidden="true">·</span>
            <span class="ed-date__part ed-date__part--year">{{ $heroDate->format('Y') }}</span>
          </span>
          @if ($content->event_type == 'venue')
            <span class="ed-hero__meta-item">
              <svg width="14" height="14" stroke-width="2" aria-hidden="true"><use href="#icon-map-pin"/></svg>
              @if ($content->city != null){{ $content->city }}@endif
              @if ($content->state), {{ $content->state }}@endif
              @if ($content->country), {{ $content->country }}@endif
            </span>
          @else
            <span class="ed-hero__meta-item">
              <svg width="14" height="14" stroke-width="2" aria-hidden="true"><use href="#icon-map-pin"/></svg>
              {{ __('Online') }}
            </span>
          @endif
        </div>
      </div>
    </div>
  </div>
  {{-- /Hero --}}

  <section class="ed-body">
    <div class="container">
      <div class="row">

        {{-- Left column --}}
        <div class="col-lg-8">

	          {{-- Gallery card --}}
	          @if($images->count() > 0)
	          <div class="ed-card ed-card--gallery">
	            <div class="ed-gallery-wrap">
	              {{-- Main image --}}
	              <div class="ed-gallery-main">
	                <button type="button" class="ed-gallery-main__link" id="edMainLink" aria-label="{{ __('Abrir galería del evento') }}">
	                  <img id="edMainImg"
	                       src="{{ asset('assets/admin/img/event-gallery/' . $images->first()->image) }}"
	                       alt="{{ $content->title }}"
	                       class="ed-gallery-main__img">
	                  <span class="ed-gallery-main__overlay" aria-hidden="true">
	                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
	                  </span>
	                  @if($images->count() > 1)
	                  <div class="ed-gallery-count">
	                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
	                    {{ $images->count() }} {{ __('fotos') }}
	                  </div>
	                  @endif
	                </button>
	              </div>
              {{-- Thumbnail strip --}}
              @if($images->count() > 1)
              <div class="ed-gallery-thumbs" id="edGalleryThumbs">
                @foreach($images as $i => $item)
                <button type="button"
                        class="ed-gallery-thumb {{ $i === 0 ? 'ed-gallery-thumb--active' : '' }}"
                        data-src="{{ asset('assets/admin/img/event-gallery/' . $item->image) }}"
                        data-action="thumb-switch">
                  <img src="{{ asset('assets/admin/img/event-gallery/' . $item->image) }}"
                       alt="{{ $content->title }} — foto {{ $i + 1 }}">
                </button>
                @endforeach
              </div>
              @endif
              {{-- Links ocultos para MagnificPopup --}}
              <div id="edGalleryLinks" style="display:none">
                @foreach($images as $item)
                <a href="{{ asset('assets/admin/img/event-gallery/' . $item->image) }}"
                   class="ed-gallery-popup-link"
                   aria-label="{{ $content->title }} — abrir imagen de galería">
                  <span class="sr-only">{{ $content->title }} — abrir imagen de galería</span>
                </a>
                @endforeach
              </div>
            </div>
          </div>
	          @endif

	          @php $summaryDate = \Carbon\Carbon::parse($startDateTime)->timezone($websiteInfo->timezone); @endphp

	          <div class="ed-card ed-card--summary">
	            <div class="ed-card__head">
	              <div>
	                <span class="ed-card__eyebrow">{{ __('Vista rápida') }}</span>
	                <h2 class="ed-card__title">{{ __('Resumen del evento') }}</h2>
	              </div>
	            </div>
	            <div class="ed-card__body">
	              <div class="ed-summary-grid">
	                <div class="ed-summary-item">
	                  <span class="ed-summary-item__label">{{ $content->date_type == 'multiple' ? __('Próxima fecha') : __('Fecha y hora') }}</span>
	                  <strong class="ed-summary-item__value">{{ ucfirst($summaryDate->translatedFormat('l j \\d\\e F')) }}</strong>
	                  <span class="ed-summary-item__meta">{{ $summaryDate->format('H:i') }} · {{ timeZoneOffset($websiteInfo->timezone) }} {{ __('GMT') }}</span>
	                </div>
	                <div class="ed-summary-item">
	                  <span class="ed-summary-item__label">{{ __('Modalidad') }}</span>
	                  <strong class="ed-summary-item__value">{{ $content->event_type == 'online' ? __('Online') : __('Presencial') }}</strong>
	                  <span class="ed-summary-item__meta">{{ $content->event_type == 'online' ? __('Acceso digital') : __('Asistencia en locación') }}</span>
	                </div>
	                <div class="ed-summary-item">
	                  <span class="ed-summary-item__label">{{ __('Ubicación') }}</span>
	                  <strong class="ed-summary-item__value">{{ $summaryLocation ?: __('A confirmar') }}</strong>
	                  <span class="ed-summary-item__meta">{{ $content->event_type == 'online' ? __('Participá desde cualquier lugar') : __('Revisá el mapa y dirección') }}</span>
	                </div>
	                <div class="ed-summary-item">
	                  <span class="ed-summary-item__label">{{ __('Organiza') }}</span>
	                  <strong class="ed-summary-item__value">{{ $summaryOrganizer }}</strong>
	                  <span class="ed-summary-item__meta">{{ __('Entradas gestionadas con Tukipass') }}</span>
	                </div>
	              </div>
                @if (
                  !$over &&
                  $content->date_type == 'single' &&
                  $content->countdown_status == 1
                )
                <div class="ed-summary-signals">
                  <div class="ed-signal-grid">
                      @if ($startDateTime >= $nowTime)
                      @php
                          $dt = Carbon\Carbon::parse($startDateTime);
                          $days_until = (int) \Carbon\Carbon::now()->diffInDays($dt);
                          $year = $dt->year; $month = $dt->month; $day = $dt->day;
                          $end_time = Carbon\Carbon::parse($startDateTime);
                          $hour = $end_time->hour; $minute = $end_time->minute;
                          $now = str_replace('+00:00', '.000' . timeZoneOffset($websiteInfo->timezone) . '00:00', gmdate('c'));
                        @endphp
                        @if ($days_until <= 14)
                          <div class="ed-countdown-wrap">
                            <p class="ed-countdown-label">{{ __('El evento comienza en') }}</p>
                            <div class="count-down" dir="ltr">
                              <div class="event-countdown" data-now="{{ $now }}" data-year="{{ $year }}"
                                data-month="{{ $month }}" data-day="{{ $day }}"
                                data-hour="{{ $hour }}" data-minute="{{ $minute }}"
                                data-timezone="{{ timeZoneOffset($websiteInfo->timezone) }}">
                              </div>
                            </div>
                          </div>
                        @else
                          <div class="ed-countdown-wrap">
                            <p class="ed-countdown-label">{{ __('Fecha del evento') }}</p>
                            <div class="count-down" dir="ltr">
                              <div class="syotimer">
                                <div class="syotimer__head"></div>
                                <div class="syotimer__body">
                                  <div class="syotimer-cell">
                                    <div class="syotimer-cell__value">{{ $dt->format('d') }}</div>
                                    <div class="syotimer-cell__unit">{{ $dt->translatedFormat('D') }}</div>
                                  </div>
                                  <div class="syotimer-cell">
                                    <div class="syotimer-cell__value">{{ $dt->translatedFormat('M') }}</div>
                                    <div class="syotimer-cell__unit">{{ $dt->format('Y') }}</div>
                                  </div>
                                  <div class="syotimer-cell">
                                    <div class="syotimer-cell__value">{{ $dt->format('H') }}</div>
                                    <div class="syotimer-cell__unit">{{ __('hora') }}</div>
                                  </div>
                                  <div class="syotimer-cell">
                                    <div class="syotimer-cell__value">{{ $dt->format('i') }}</div>
                                    <div class="syotimer-cell__unit">{{ __('min') }}</div>
                                  </div>
                                </div>
                                <div class="syotimer__footer"></div>
                              </div>
                            </div>
                          </div>
                        @endif
                        @elseif ($startDateTime <= $endDateTime && $endDateTime >= $nowTime)
                      <div class="ed-status-pill ed-status-pill--running">
                        <span class="ed-status-pill__dot"></span> {{ __('El evento está en curso') }}
                      </div>
                    @endif
                </div>
                </div>
                @endif

                @if (!$over)
                <script>
                (function(){
                  var icons = {
                    eye:'<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>',
                    fire:'<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 23c-3.87 0-7-3.13-7-7 0-2.38 1.19-4.47 3-5.74C8 10.26 8.5 9.5 8.5 9.5S9.77 11 12 11c2.1 0 3.5-2 3.5-2s.5 1.5.5 3c0 3.87-3.13 7-7 7z"/><path d="M12 23c-1.66 0-3-1.34-3-3 0-1.1.58-2.06 1.46-2.6.35-.22.54-.08.54.3v.8c0 1.1.9 2 2 2s2-.9 2-2c0-.53-.21-1.01-.55-1.36A3.001 3.001 0 0012 23z" opacity=".6"/></svg>',
                    zap:'<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>',
                    trending:'<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>',
                    heart:'<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>',
                    clock:'<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>',
                    star:'<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
                    shield:'<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>',
                    users:'<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>',
                    calendar:'<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>',
                    alert:'<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>'
                  };
                  var colors = {eye:'eye',fire:'fire',zap:'zap',trending:'trending',heart:'fire',clock:'zap',star:'trending',shield:'eye',users:'trending',calendar:'zap',alert:'alert'};
                  var pool = {!! json_encode($ed_nudge_pool, JSON_UNESCAPED_UNICODE | JSON_HEX_AMP) !!};
                  var idx = 0, wrap = document.getElementById('edNudge'), ic = document.getElementById('edNudgeIcon'), tx = document.getElementById('edNudgeText');
                  function show(i){
                    var n = pool[i];
                    wrap.style.opacity='0';
                    setTimeout(function(){
                      ic.className='ed-nudge__icon ed-nudge__icon--'+colors[n.icon];
                      ic.innerHTML=icons[n.icon]||'';
                      tx.innerHTML=n.text;
                      wrap.style.opacity='1';
                    },250);
                  }
                  show(0);
                  setInterval(function(){ idx=(idx+1)%pool.length; show(idx); }, 8000);
                })();
                </script>
                @endif
	            </div>
	          </div>

            @if ($spotifyEmbedUrl || $heroStatusLabel)
              <div class="ed-card ed-card--context">
                <div class="ed-card__body">
                  @if ($spotifyEmbedUrl)
                    <div class="ed-spotify-embed">
                      <iframe src="{{ $spotifyEmbedUrl }}"
                        frameborder="0"
                        allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                        loading="lazy"
                        title="{{ __('Spotify del evento') }}: {{ $content->title }}"></iframe>
                    </div>
                  @endif

                  <div class="ed-body-topline">
                    <a href="{{ route('events', ['category' => $content->slug]) }}" class="ed-body-chip ed-body-chip--category">
                      <i class="fas fa-tag"></i> {{ $content->name }}
                    </a>
                    @if ($heroStatusLabel)
                      <span class="ed-body-chip ed-hero__status-pill {{ $heroStatusClass }}">{{ $heroStatusLabel }}</span>
                    @endif
                  </div>
                </div>
              </div>
            @endif

	          {{-- Session errors --}}
	          @if (Session::has('paypal_error'))
            <div class="alert alert-danger">{{ Session::get('paypal_error') }}</div>
          @endif
          @php Session::forget('paypal_error'); @endphp

          {{-- Description card --}}
	          <div class="ed-card">
	            <div class="ed-card__head">
	              <div>
	                <span class="ed-card__eyebrow">{{ __('Información') }}</span>
	                <h2 class="ed-card__title">{{ __('Descripción') }}</h2>
	              </div>
	            </div>
	            <div class="ed-card__body">
	              <div class="summernote-content">
                {!! $eventDescriptionHtml !!}
              </div>
            </div>
          </div>

          {{-- Map card --}}
          @if ($content->event_type != 'online' && !empty($map_address))
	            <div class="ed-card">
	              <div class="ed-card__head">
	                <div>
	                  <span class="ed-card__eyebrow">{{ __('Ubicación') }}</span>
	                  <h2 class="ed-card__title">{{ __('Mapa') }}</h2>
	                </div>
	              </div>
	              <div class="ed-card__body ed-card__body--embed">
                <iframe
                  src="https://maps.google.com/maps?width=100%25&amp;height=385&amp;hl=es&amp;q={{ urlencode($map_address) }}&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"
                  height="385" class="ed-card__iframe" allow="fullscreen" loading="lazy"
                  title="{{ $content->title }} — {{ __('Mapa') }}"></iframe>
              </div>
            </div>
          @endif

          {{-- YouTube card --}}
          @php
            $youtubeEmbedUrl = null;
            if (!empty($content->youtube_url)) {
              preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $content->youtube_url, $ym);
              if (!empty($ym[1])) $youtubeEmbedUrl = 'https://www.youtube.com/embed/' . $ym[1];
            }
          @endphp
          @if($youtubeEmbedUrl)
	            <div class="ed-card">
	              <div class="ed-card__head">
	                <div>
	                  <span class="ed-card__eyebrow">{{ __('Contenido') }}</span>
	                  <h2 class="ed-card__title ed-card__title--with-icon">
                    <span class="ed-card__title-icon" aria-hidden="true"><i class="fab fa-youtube"></i></span>{{ __('Video') }}
                  </h2>
	                </div>
	              </div>
              <div class="ed-card__body ed-card__body--embed">
                <div class="ed-card__video-wrap">
                  <iframe src="{{ $youtubeEmbedUrl }}"
                    class="ed-card__video-iframe"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen"
                    loading="lazy" title="{{ $content->title }}"></iframe>
                </div>
              </div>
            </div>
          @endif

          {{-- Refund policy card --}}
          @if (!empty($content->refund_policy))
	            <div class="ed-card">
	              <div class="ed-card__head">
	                <div>
	                  <span class="ed-card__eyebrow">{{ __('Condiciones') }}</span>
	                  <h2 class="ed-card__title">{{ __('Política de reembolso') }}</h2>
	                </div>
	              </div>
              <div class="ed-card__body">
                <p>{{ $content->refund_policy }}</p>
              </div>
            </div>
          @endif

        </div>
        {{-- /Left column --}}

        {{-- Right column (sticky sidebar) --}}
        <div class="col-lg-4">
          <div class="sidebar-sticky">

            {{-- CARD 1: Ticket form --}}
            <div class="ed-ticket-card">
              <div class="ed-ticket-card__head">
                {{-- Status pill --}}
                <div class="ed-head-top">
                  <span class="ed-head-pill {{ $over ? 'ed-head-pill--over' : 'ed-head-pill--open' }}">
                    <span class="ed-head-pill__dot"></span>
                    {{ $over ? __('Venta cerrada') : __('Venta abierta') }}
                  </span>
                  <span class="ed-ticket-card__head-title">{{ __('Entradas') }}</span>
                </div>
                {{-- Price --}}
                <p class="ed-ticket-card__head-price">
                  @if ($content->pricing_type == 'free' || !is_numeric($ticketSummary['min_ticket_price']))
                    {{ __('Gratis') }}
                  @elseif($ticketSummary['min_ticket_price'] == 0 && $ticketSummary['max_ticket_price'] > 0)
                    {{ __('Gratis') }}<span class="ed-ticket-card__head-sep">—</span>{{ symbolPrice($ticketSummary['max_ticket_price']) }}
                  @elseif($ticketSummary['min_ticket_price'] == 0)
                    {{ __('Gratis') }}
                  @elseif($ticketSummary['has_price_range'])
                    {{ symbolPrice($ticketSummary['min_ticket_price']) }}<span class="ed-ticket-card__head-sep">—</span>{{ symbolPrice($ticketSummary['max_ticket_price']) }}
                  @else
                    {{ symbolPrice($ticketSummary['min_ticket_price']) }}
                  @endif
                </p>
                {{-- Stock indicator --}}
                @if (!$over)
                  <p class="ed-head-stock">
                    @if ($ticketSummary['has_unlimited_stock'])
                      <span class="ed-head-stock__dot"></span>{{ __('Disponible') }}
                    @elseif($ticketSummary['total_stock'] !== null && $ticketSummary['total_stock'] <= 10)
                      <span class="ed-head-stock__dot ed-head-stock__dot--low"></span>{{ __('¡Últimas') }} {{ $ticketSummary['total_stock'] }} {{ $ticketSummary['total_stock'] == 1 ? __('entrada') : __('entradas') }}!
                    @elseif($ticketSummary['total_stock'] !== null)
                      <span class="ed-head-stock__dot"></span>{{ $ticketSummary['total_stock'] }} {{ __('entradas disponibles') }}
                    @endif
                  </p>
                @endif
              </div>
              <div class="ed-ticket-card__body">
                <form action="{{ route('check-out2') }}" method="post">
                  @csrf
                  <input type="hidden" name="event_id" value="{{ $content->id }}">
                  <input type="hidden" name="pricing_type" value="{{ $content->pricing_type }}">
                  <div class="event-details-information">
                    <input type="hidden" name="date_type" value="{{ $content->date_type }}">
                    @if ($content->date_type == 'multiple')
                      @php
                        $dates = eventDates($content->id);
                        $exp_dates = eventExpDates($content->id);
                      @endphp
                      <div class="form-group mb-3">
                        <label class="ed-field-label">{{ __('Seleccioná fecha') }}</label>
                        <select name="event_date" class="form-control">
                          @if (count($dates) > 0)
                            @foreach ($dates as $date)
                              <option value="{{ FullDateTime($date->start_date_time) }}">
                                {{ FullDateTime($date->start_date_time) }}
                                ({{ timeZoneOffset($websiteInfo->timezone) }} {{ __('GMT') }})
                              </option>
                            @endforeach
                          @endif
                          @if (count($exp_dates) > 0)
                            @foreach ($exp_dates as $exp_date)
                              <option disabled value="">
                                {{ FullDateTime($exp_date->start_date_time) }}
                                ({{ timeZoneOffset($websiteInfo->timezone) }} {{ __('GMT') }})
                              </option>
                            @endforeach
                          @endif
                        </select>
                        @error('event_date')
                          <p class="text-danger">{{ $message }}</p>
                        @enderror
                      </div>
                    @else
                      <input type="hidden" name="event_date"
                        value="{{ FullDateTime($content->start_date . $content->start_time) }}">
                    @endif

                    <p class="ed-section-label">{{ __('Seleccioná tus entradas') }}</p>

                    @if ($content->event_type == 'online' && $content->pricing_type == 'normal')

                      @php
                        $ticket = App\Models\Event\Ticket::where('event_id', $content->id)->first();
                        $event_count = App\Models\Event\Ticket::where('event_id', $content->id)
                            ->get()
                            ->count();
                        if ($ticket->ticket_available_type == 'limited') {
                            $stock = $ticket->ticket_available;
                        } else {
                            $stock = 'unlimited';
                        }
                        //ticket purchase or not check
                        if (Auth::guard('customer')->user() && $ticket->max_ticket_buy_type == 'limited') {
                            $purchase = isTicketPurchaseOnline($ticket->event_id, $ticket->max_buy_ticket);
                        } else {
                            $purchase = ['status' => 'false', 'p_qty' => 0];
                        }
                      @endphp
                      @if ($ticket)

                        <div class="price-count">
                          <h6 dir="ltr">

                            @if ($ticket->early_bird_discount == 'enable')
                              @php
                                $discount_date = Carbon\Carbon::parse($ticket->early_bird_discount_date . $ticket->early_bird_discount_time);
                              @endphp

                              @if ($ticket->early_bird_discount_type == 'fixed' && !$discount_date->isPast())
                                @php
                                  $calculate_price = $ticket->price - $ticket->early_bird_discount_amount;
                                @endphp
                                {{ symbolPrice($calculate_price) }}
                                <del>
                                  {{ symbolPrice($ticket->price) }}
                                </del>
                              @elseif ($ticket->early_bird_discount_type == 'percentage' && !$discount_date->isPast())
                                @php
                                  $c_price = ($ticket->price * $ticket->early_bird_discount_amount) / 100;
                                  $calculate_price = $ticket->price - $c_price;
                                @endphp
                                {{ symbolPrice($calculate_price) }}
                                <del>
                                  {{ symbolPrice($ticket->price) }}
                                </del>
                              @else
                                @php
                                  $calculate_price = $ticket->price;
                                @endphp
                                {{ symbolPrice($calculate_price) }}
                              @endif
                            @else
                              @php
                                $calculate_price = $ticket->price;
                              @endphp
                              {{ symbolPrice($calculate_price) }}
                            @endif


                          </h6>
                          <div class="quantity-input">
                            <button class="quantity-down" type="button" aria-label="{{ __('Disminuir cantidad') }}">
                              -
                            </button>
                            <input class="quantity" type="number" readonly value="1"
                              data-price="{{ $calculate_price }}" data-max_buy_ticket="{{ $ticket->max_buy_ticket }}"
                              name="quantity" data-ticket_id="{{ $ticket->id }}" data-stock="{{ $stock }}"
                              data-purchase="{{ $purchase['status'] }}" data-p_qty="{{ $purchase['p_qty'] }}">
                            <button class="quantity-up" type="button" aria-label="{{ __('Aumentar cantidad') }}">
                              +
                            </button>
                          </div>



                          @if ($ticket->early_bird_discount == 'enable')
                            @php
                              $discount_date = Carbon\Carbon::parse($ticket->early_bird_discount_date . $ticket->early_bird_discount_time);
                            @endphp
                            @if (!$discount_date->isPast())
                              <p>{{ __('Discount available') . ' ' }} :
                                ({{ __('till') . ' ' }} :
                                <span
                                  dir="ltr">{{ \Carbon\Carbon::parse($discount_date)->timezone($websiteInfo->timezone)->translatedFormat('d/m/Y H:i') }}</span>)
                              </p>
                            @endif
                          @endif


                        </div>
                        <p
                          class="text-warning max_error_{{ $ticket->id }}{{ $ticket->max_ticket_buy_type == 'limited' ? $ticket->max_buy_ticket : '' }} ">
                        </p>

                      @endif
                    @elseif($content->event_type == 'online' && $content->pricing_type == 'free')
                      @php
                        $ticket = App\Models\Event\Ticket::where('event_id', $content->id)->first();
                        $event_count = App\Models\Event\Ticket::where('event_id', $content->id)
                            ->get()
                            ->count();

                        if ($ticket->ticket_available_type == 'limited') {
                            $stock = $ticket->ticket_available;
                        } else {
                            $stock = 'unlimited';
                        }

                        //ticket purchase or not check
                        if (Auth::guard('customer')->user() && $ticket->max_ticket_buy_type == 'limited') {
                            $purchase = isTicketPurchaseOnline($ticket->event_id, $ticket->max_buy_ticket);
                            $max_buy_ticket = $ticket->max_buy_ticket;
                        } else {
                            $purchase = ['status' => 'false', 'p_qty' => 0];
                            $max_buy_ticket = 999999;
                        }
                      @endphp
                      <div class="price-count">
                        <h6>
                          {{ __('Free') }}
                        </h6>
                        <div class="quantity-input">
                          <button class="quantity-down" type="button" aria-label="{{ __('Disminuir cantidad') }}">
                            -
                          </button>
                          <input class="quantity" readonly type="number" value="1"
                            data-price="{{ $content->price }}" data-max_buy_ticket="{{ $max_buy_ticket }}"
                            name="quantity" data-ticket_id="{{ $ticket->id }}" data-stock="{{ $stock }}"
                            data-purchase="{{ $purchase['status'] }}" data-p_qty="{{ $purchase['p_qty'] }}">
                          <button class="quantity-up" type="button" aria-label="{{ __('Aumentar cantidad') }}">
                            +
                          </button>
                        </div>

                      </div>
                      <p
                        class="text-warning max_error_{{ $ticket->id }}{{ $ticket->max_ticket_buy_type == 'limited' ? $ticket->max_buy_ticket : '' }} ">
                      </p>
                    @elseif($content->event_type == 'venue')
                      @php
                        $tickets = DB::table('tickets')
                            ->where('event_id', $content->id)
                            ->get();
                      @endphp
                      @if (count($tickets) > 0)
                        @foreach ($tickets as $ticket)
                          @if ($ticket->pricing_type == 'normal')
                            @php
                              if ($ticket->ticket_available_type == 'limited') {
                                  $stock = $ticket->ticket_available;
                              } else {
                                  $stock = 'unlimited';
                              }

                              //ticket purchase or not check
                              $ticket_content = App\Models\Event\TicketContent::where([['language_id', $currentLanguageInfo->id], ['ticket_id', $ticket->id]])->first();

                              if (Auth::guard('customer')->user() && $ticket->max_ticket_buy_type == 'limited') {
                                  $purchase = isTicketPurchaseVenue($ticket->event_id, $ticket->max_buy_ticket, $ticket->id, @$ticket_content->title);
                              } else {
                                  $purchase = ['status' => 'false', 'p_qty' => 0];
                              }

                            @endphp
                            <p class="mb-0"><strong>{{ __(@$ticket_content->title ?: '') }}</strong></p>
                            <div class="click-show">
                              <div class="show-content">
                                {!! clean(@$ticket_content->description ?? '') !!}
                              </div>
                              @if (strlen(@$ticket_content->description) > 50)
                                <div class="read-more-btn">
                                  <span>{{ __('Read more') }}</span>
                                  <span>{{ __('Read less') }}</span>
                                </div>
                              @endif
                            </div>
                            <div class="price-count">
                              <h6 dir="ltr">
                                @if ($ticket->early_bird_discount == 'enable')
                                  @php
                                    $discount_date = Carbon\Carbon::parse($ticket->early_bird_discount_date . $ticket->early_bird_discount_time);
                                  @endphp

                                  @if ($ticket->early_bird_discount_type == 'fixed' && !$discount_date->isPast())
                                    @php $calculate_price = $ticket->price - $ticket->early_bird_discount_amount; @endphp
                                    {{ symbolPrice($calculate_price) }}
                                    <del>{{ symbolPrice($ticket->price) }}</del>
                                  @elseif ($ticket->early_bird_discount_type == 'percentage' && !$discount_date->isPast())
                                    @php
                                      $c_price = ($ticket->price * $ticket->early_bird_discount_amount) / 100;
                                      $calculate_price = $ticket->price - $c_price;
                                    @endphp
                                    {{ symbolPrice($calculate_price) }}
                                    <del>{{ symbolPrice($ticket->price) }}</del>
                                  @else
                                    @php $calculate_price = $ticket->price; @endphp
                                    {{ symbolPrice($calculate_price) }}
                                  @endif
                                @else
                                  @php $calculate_price = $ticket->price; @endphp
                                  {{ symbolPrice($calculate_price) }}
                                @endif


                              </h6>
                              <div class="quantity-input">
                                <button class="quantity-down" type="button" aria-label="{{ __('Disminuir cantidad') }}">
                                  -
                                </button>
                                <input class="quantity" readonly type="number" value="0"
                                  data-price="{{ $calculate_price }}"
                                  data-max_buy_ticket="{{ $ticket->max_buy_ticket }}" name="quantity[]"
                                  data-ticket_id="{{ $ticket->id }}" data-stock="{{ $stock }}"
                                  data-purchase="{{ $purchase['status'] }}" data-p_qty="{{ $purchase['p_qty'] }}">
                                <button class="quantity-up" type="button" aria-label="{{ __('Aumentar cantidad') }}">
                                  +
                                </button>
                              </div>


                              @if ($ticket->early_bird_discount == 'enable')
                                @php
                                  $discount_date = Carbon\Carbon::parse($ticket->early_bird_discount_date . $ticket->early_bird_discount_time);
                                @endphp
                                @if (!$discount_date->isPast())
                                  <p>{{ __('Discount available') . ' ' }} :
                                    ({{ __('till') . ' ' }} :
                                    <span
                                      dir="ltr">{{ \Carbon\Carbon::parse($discount_date)->timezone($websiteInfo->timezone)->translatedFormat('d/m/Y H:i') }}</span>)
                                  </p>
                                @endif
                              @endif

                            </div>
                            <p
                              class="text-warning max_error_{{ $ticket->id }}{{ $ticket->max_ticket_buy_type == 'limited' ? $ticket->max_buy_ticket : '' }} ">
                            </p>
                          @elseif($ticket->pricing_type == 'variation')
                            @php
                              $variations = json_decode($ticket->variations);

                              $varition_names = App\Models\Event\VariationContent::where([['ticket_id', $ticket->id], ['language_id', $currentLanguageInfo->id]])->get();
                              if (empty($varition_names)) {
                                  $varition_names = App\Models\Event\VariationContent::where('ticket_id', $ticket->id)->get();
                              }

                              $de_lang = App\Models\Language::where('is_default', 1)->first();
                              $de_varition_names = App\Models\Event\VariationContent::where([['ticket_id', $ticket->id], ['language_id', $de_lang->id]])->get();
                              if (empty($de_varition_names)) {
                                  $de_varition_names = App\Models\Event\VariationContent::where([['ticket_id', $ticket->id]])->get();
                              }
                            @endphp
                            @foreach ($variations as $key => $item)
                              @php
                                //ticket purchase or not check
                                if (Auth::guard('customer')->user()) {
                                    if (count($de_varition_names) > 0) {
                                        $purchase = isTicketPurchaseVenue($ticket->event_id, $item->v_max_ticket_buy, $ticket->id, $de_varition_names[$key]['name']);
                                    }
                                } else {
                                    $purchase = ['status' => 'false', 'p_qty' => 0];
                                }
                                $ticket_content = App\Models\Event\TicketContent::where([['language_id', $currentLanguageInfo->id], ['ticket_id', $ticket->id]])->first();
                                if (empty($ticket_content)) {
                                    $ticket_content = App\Models\Event\TicketContent::where([['ticket_id', $ticket->id]])->first();
                                }
                              @endphp
                              <p class="mb-0"><strong>{{ __(@$ticket_content->title ?: '') }} -
                                  {{ __(@$varition_names[$key]['name'] ?: '') }}</strong>
                              </p>
                              <div class="click-show">
                                <div class="show-content">
                                  {!! clean(@$ticket_content->description ?? '') !!}
                                </div>
                                @if (strlen(@$ticket_content->description) > 50)
                                  <div class="read-more-btn">
                                    <span>{{ __('Read more') }}</span>
                                    <span>{{ __('Read less') }}</span>
                                  </div>
                                @endif
                              </div>
                              <div class="price-count">
                                <h6 dir="ltr">
                                  @if ($ticket->early_bird_discount == 'enable')
                                    @php
                                      $discount_date = Carbon\Carbon::parse($ticket->early_bird_discount_date . $ticket->early_bird_discount_time);
                                    @endphp
                                    @if ($ticket->early_bird_discount_type == 'fixed' && !$discount_date->isPast())
                                      @php
                                        $calculate_price = $item->price - $ticket->early_bird_discount_amount;
                                      @endphp
                                      {{ symbolPrice($calculate_price) }}

                                      <del>
                                        {{ symbolPrice($item->price) }}
                                      </del>
                                    @elseif ($ticket->early_bird_discount_type == 'percentage' && !$discount_date->isPast())
                                      @php
                                        $c_price = ($item->price * $ticket->early_bird_discount_amount) / 100;
                                        $calculate_price = $item->price - $c_price;
                                      @endphp
                                      {{ symbolPrice($calculate_price) }}

                                      <del>
                                        {{ symbolPrice($item->price) }}
                                      </del>
                                    @else
                                      @php
                                        $calculate_price = $item->price;
                                      @endphp
                                      {{ symbolPrice($calculate_price) }}
                                    @endif
                                  @else
                                    @php
                                      $calculate_price = $item->price;
                                    @endphp
                                    {{ symbolPrice($calculate_price) }}
                                  @endif

                                </h6>

                                <div class="quantity-input">
                                  <button class="quantity-down_variation" type="button" aria-label="{{ __('Disminuir cantidad') }}">
                                    -
                                  </button>
                                  <input type="hidden" name="v_name[]" value="{{ $item->name }}">
                                  @php
                                    if ($item->ticket_available_type == 'limited') {
                                        $stock = $item->ticket_available;
                                    } else {
                                        $stock = 'unlimited';
                                    }
                                    if ($item->max_ticket_buy_type == 'limited') {
                                        $max_buy = $item->v_max_ticket_buy;
                                    } else {
                                        $max_buy = 'unlimited';
                                    }
                                  @endphp
                                  <input type="number" value="0" class="quantity"
                                    data-price="{{ $calculate_price }}" data-max_buy_ticket="{{ $max_buy }}"
                                    data-name="{{ $item->name }}" name="quantity[]"
                                    data-ticket_id="{{ $ticket->id }}" readonly data-stock="{{ $stock }}"
                                    data-purchase="{{ $purchase['status'] }}" data-p_qty="{{ $purchase['p_qty'] }}">
                                  <button class="quantity-up" type="button" aria-label="{{ __('Aumentar cantidad') }}">
                                    +
                                  </button>
                                </div>
                                @if ($ticket->early_bird_discount == 'enable')
                                  @php
                                    $discount_date = Carbon\Carbon::parse($ticket->early_bird_discount_date . $ticket->early_bird_discount_time);
                                  @endphp
                                  @if (!$discount_date->isPast())
                                    <p>{{ __('Discount available') . ' ' }} :
                                      ({{ __('till') . ' ' }} :
                                      <span
                                        dir="ltr">{{ \Carbon\Carbon::parse($discount_date)->timezone($websiteInfo->timezone)->translatedFormat('d/m/Y H:i') }}</span>)
                                    </p>
                                  @endif
                                @endif
                              </div>
                              <p class="text-warning max_error_{{ $ticket->id }}{{ $item->v_max_ticket_buy }} ">
                              </p>
                            @endforeach
                          @elseif($ticket->pricing_type == 'free')
                            @php
                              if ($ticket->ticket_available_type == 'limited') {
                                  $stock = $ticket->ticket_available;
                              } else {
                                  $stock = 'unlimited';
                              }

                              //ticket purchase or not check
                              $de_lang = App\Models\Language::where('is_default', 1)->first();
                              $ticket_content_default = App\Models\Event\TicketContent::where([['language_id', $de_lang->id], ['ticket_id', $ticket->id]])->first();
                              if (Auth::guard('customer')->user() && $ticket->max_ticket_buy_type == 'limited') {
                                  $purchase = isTicketPurchaseVenue($ticket->event_id, $ticket->max_buy_ticket, $ticket->id, @$ticket_content_default->title);
                              } else {
                                  $purchase = ['status' => 'false', 'p_qty' => 1];
                              }
                              $ticket_content = App\Models\Event\TicketContent::where([['language_id', $currentLanguageInfo->id], ['ticket_id', $ticket->id]])->first();
                            @endphp
                            <p class="mb-0"><strong>{{ __(@$ticket_content->title ?: '') }}</strong></p>
                            <div class="click-show">
                              <div class="show-content">
                                {!! clean(@$ticket_content->description ?? '') !!}
                              </div>
                              @if (strlen(@$ticket_content->description) > 50)
                                <div class="read-more-btn">
                                  <span>{{ __('Read more') }}</span>
                                  <span>{{ __('Read less') }}</span>
                                </div>
                              @endif
                            </div>
                            <div class="price-count">
                              <h6>
                                <span class="">{{ __('free') }}</span>
                              </h6>
                              <div class="quantity-input">
                                <button class="quantity-down" type="button" aria-label="{{ __('Disminuir cantidad') }}">
                                  -
                                </button>
                                <input class="quantity" data-max_buy_ticket="{{ $ticket->max_buy_ticket }}"
                                  type="number" value="0" data-price="{{ $ticket->price }}" name="quantity[]"
                                  data-ticket_id="{{ $ticket->id }}" readonly data-stock="{{ $stock }}"
                                  data-purchase="{{ $purchase['status'] }}" data-p_qty="{{ $purchase['p_qty'] }}">
                                <button class="quantity-up" type="button" aria-label="{{ __('Aumentar cantidad') }}">
                                  +
                                </button>
                              </div>
                            </div>
                            <p
                              class="text-warning max_error_{{ $ticket->id }}{{ $ticket->max_ticket_buy_type == 'limited' ? $ticket->max_buy_ticket : '' }} ">
                            </p>
                          @endif
                        @endforeach
                      @endif
                    @endif

                    @if ($tickets_count > 0)
                      <div class="ed-total-row">
                        <span class="ed-total-label">{{ __('Total a pagar') }}</span>
                        <span class="ed-total-value" dir="ltr">
                          <span>{{ $basicInfo->base_currency_symbol_position == 'left' ? $basicInfo->base_currency_symbol : '' }}</span>
                          <span id="total_price">0</span>
                          <span>{{ $basicInfo->base_currency_symbol_position == 'right' ? $basicInfo->base_currency_symbol : '' }}</span>
                        </span>
                        <input type="hidden" name="total" id="total">
                      </div>
                      {{-- ed-order-recap removed: total already shown above --}}
                      <div class="ed-cta-zone">
                        <button class="ed-buy-btn" type="submit" {{ $over ? 'disabled' : '' }}>
                          {{ $over ? __('Evento finalizado') : __('Reservar mi lugar') }}
                          @if (!$over)
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                          @endif
                        </button>
                        @if (!$over)
                          <div class="ed-trust-row">
                            <span class="ed-trust-item">
                              <svg class="ed-trust-item__icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                              {{ __('Pago seguro') }}
                            </span>
                            <span class="ed-trust-item">
                              <svg class="ed-trust-item__icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>
                              {{ __('Reembolso') }}
                            </span>
                            <span class="ed-trust-item">
                              <svg class="ed-trust-item__icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 12V22H4V12"/><path d="M22 7H2v5h20V7z"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 010-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 000-5C13 2 12 7 12 7z"/></svg>
                              {{ __('Entradas oficiales') }}
                            </span>
                          </div>
                        @endif
                      </div>
                    @endif
                  </div>

                </form>
              </div>
            </div>
            {{-- /Ticket form card --}}

            {{-- CARD 2: Event info --}}
            <div class="ei-card">

              {{-- Organizer --}}
              @if ($organizer == '')
                @php $admin = App\Models\Admin::first(); @endphp
                <div class="ei-org">
                  <img class="ei-org__avatar lazy"
                    src="{{ asset('assets/front/images/user.png') }}"
                    data-src="{{ asset('assets/admin/img/admins/' . $admin->image) }}"
                    alt="{{ $admin->username }}">
                  <div class="ei-org__info">
                    <span class="ei-label">{{ __('Organizado por') }}</span>
                    <p class="ei-org__name">{{ $admin->username }}</p>
                    <a class="ei-org__link" href="{{ route('frontend.organizer.details', [$admin->id, str_replace(' ', '-', $admin->username), 'admin' => 'true']) }}">{{ __('Ver perfil') }} <i class="fas fa-arrow-right ei-org__arrow" aria-hidden="true"></i></a>
                  </div>
                </div>
              @else
                <div class="ei-org">
                  <img class="ei-org__avatar lazy"
                    src="{{ asset('assets/front/images/user.png') }}"
                    @if ($organizer->photo != null)
                      data-src="{{ asset('assets/admin/img/organizer-photo/' . $organizer->photo) }}"
                    @endif
                    alt="{{ $organizer->username }}">
                  <div class="ei-org__info">
                    <span class="ei-label">{{ __('Organizado por') }}</span>
                    <p class="ei-org__name">{{ $organizer->username }}</p>
                    <a class="ei-org__link" href="{{ route('frontend.organizer.details', [$organizer->id, str_replace(' ', '-', $organizer->username)]) }}">{{ __('Ver perfil') }} <i class="fas fa-arrow-right ei-org__arrow" aria-hidden="true"></i></a>
                  </div>
                </div>
              @endif

              {{-- Address --}}
              @if ($content->address != null)
                <div class="ei-meta">
                  <i class="fas fa-map-marker-alt ei-meta__icon"></i>
                  <div>
                    <span class="ei-label">{{ __('Ubicación') }}</span>
                    <p class="ei-meta__text">{{ $content->address }}</p>
                  </div>
                </div>
              @endif

              {{-- Add to Calendar --}}
              @php
                $start_date    = str_replace('-', '', $content->start_date);
                $start_time_cal = str_replace(':', '', $content->start_time);
                $end_date      = str_replace('-', '', $content->end_date);
                $end_time_cal  = str_replace(':', '', $content->end_time);
              @endphp
              <div class="ei-cal">
                <span class="ei-label"><i class="fas fa-calendar-plus ei-cal__icon" aria-hidden="true"></i>{{ __('Añadir al calendario') }}</span>
                <div class="ei-cal__btns">
                  <a target="_blank" rel="noopener noreferrer" class="ei-cal__btn ei-cal__btn--google"
                    href="//calendar.google.com/calendar/u/0/r/eventedit?text={{ urlencode($content->title) }}&dates={{ $start_date }}T{{ $start_time_cal }}/{{ $end_date }}T{{ $end_time_cal }}&ctz={{ $websiteInfo->timezone }}&details={{ urlencode('Más información: ' . route('event.details', [$content->eventSlug, $content->id])) }}&location={{ urlencode($content->event_type == 'online' ? 'En línea' : $content->address) }}&sf=true">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                    Google
                  </a>
                  <a target="_blank" rel="noopener noreferrer" class="ei-cal__btn"
                    href="//calendar.yahoo.com/?v=60&view=d&type=20&TITLE={{ urlencode($content->title) }}&ST={{ $start_date }}T{{ $start_time_cal }}&ET={{ $end_date }}T{{ $end_time_cal }}&DUR=9959&DESC={{ urlencode('Más información: ' . route('event.details', [$content->eventSlug, $content->id])) }}&in_loc={{ urlencode($content->event_type == 'online' ? 'En línea' : $content->address) }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 2v4M16 2v4M3 10h18M5 4h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
                    Yahoo
                  </a>
                </div>
              </div>

            </div>
            {{-- /Event info card --}}

          </div>
        </div>
        {{-- /Right column --}}

      </div>

      @if (!empty(showAd(3)))
        <div class="text-center mt-4">
          {!! showAd(3) !!}
        </div>
      @endif

    </div>
  </section>
  <!-- Event Details V2 End -->

  {{-- ── EVENTOS RELACIONADOS ── --}}
@endsection
@section('modals')
  @includeIf('frontend.partials.modals')
@endsection

@push('scripts')
<script>
/* ── Gallery thumbnail switch (delegated) ── */
document.addEventListener('click', function(e) {
  var btn = e.target.closest('[data-action="thumb-switch"]');
  if (!btn) return;
  document.querySelectorAll('.ed-gallery-thumb').forEach(function(t) {
    t.classList.remove('ed-gallery-thumb--active');
  });
  btn.classList.add('ed-gallery-thumb--active');
  var img = document.getElementById('edMainImg');
  if (img) { img.style.opacity = '0'; setTimeout(function(){ img.src = btn.dataset.src; img.style.opacity = '1'; }, 120); }
});
/* ── Total price: vanilla JS (independiente de jQuery/defer) ── */
document.addEventListener('DOMContentLoaded', function() {
  var symL = '{{ $basicInfo->base_currency_symbol_position == "left"  ? addslashes($basicInfo->base_currency_symbol) : "" }}';
  var symR = '{{ $basicInfo->base_currency_symbol_position == "right" ? addslashes($basicInfo->base_currency_symbol) : "" }}';

  function recalcTotal() {
    var total = 0;
    document.querySelectorAll('.quantity[data-price]').forEach(function(inp) {
      var qty   = parseInt(inp.value,  10) || 0;
      var price = parseFloat(inp.dataset.price) || 0;
      total += qty * price;
    });
    var elTotal   = document.getElementById('total_price');
    var elHidden  = document.getElementById('total');
    var elRecap   = document.getElementById('edRecapPrice');
    var formatted = total > 0 ? total.toFixed(2) : '0';
    if (elTotal)  elTotal.textContent = formatted;
    if (elHidden) elHidden.value      = formatted;
    if (elRecap)  elRecap.textContent = total > 0 ? ' · ' + symL + formatted + symR : '';
  }

  /* Ejecutar al cargar */
  recalcTotal();

  /* Ejecutar después de cada click en botones +/- (jQuery los modifica con .val()) */
  document.addEventListener('click', function(e) {
    if (e.target.closest('.quantity-up, .quantity-down, .quantity-down_variation')) {
      setTimeout(recalcTotal, 0);
    }
  });
});

</script>
<script>
(function () {
  var shareBtn = document.querySelector('[data-target=".share-event"]');
  if (!shareBtn || !navigator.share) return;
  shareBtn.addEventListener('click', function (e) {
    e.preventDefault();
    e.stopImmediatePropagation();
    navigator.share({
      title: {{ json_encode($content->title, JSON_UNESCAPED_UNICODE | JSON_HEX_AMP) }},
      text: {{ json_encode(\Illuminate\Support\Str::limit(strip_tags($content->description ?? ''), 120), JSON_UNESCAPED_UNICODE | JSON_HEX_AMP) }},
      url: window.location.href
    }).catch(function () {});
  });
})();
</script>
@endpush
