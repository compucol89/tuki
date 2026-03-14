@extends('frontend.layout')
@section('pageHeading')
  {{ \Illuminate\Support\Str::limit($content->title, 50, '...') }}
@endsection

@section('meta-keywords', $content->meta_keywords ?? '')
@section('meta-description', $og_description ?? '')
@section('og-title', $og_title ?? $content->title)
@section('og-description', $og_description ?? '')
@section('og-image', $og_image ?? asset('assets/admin/img/event/thumbnail/' . $content->thumbnail))
@section('og-url', $og_url ?? url()->current())
@section('og-type', 'event')
@section('canonical', $canonical ?? url()->current())

@section('custom-style')
  <link rel="stylesheet" href="{{ asset('assets/admin/css/summernote-content.css') }}">
  <style>
    .event-spotify-embed {
      margin: 0 30px 0 70px;
      width: 100%;
      max-width: calc(100% - 100px);
      box-sizing: border-box;
    }
    .event-spotify-embed iframe {
      width: 100% !important;
      max-width: 100% !important;
      height: 320px !important;
      border: 0;
      border-radius: 12px;
    }
    @media (max-width: 1199px) {
      .event-spotify-embed {
        margin-left: 30px;
        margin-right: 30px;
        max-width: calc(100% - 60px);
      }
    }
    @media (max-width: 767px) {
      .event-spotify-embed {
        margin-inline: 0;
        max-width: 100%;
      }
    }
  </style>
@endsection

@push('scripts')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Event",
  "name": "{{ addslashes($content->title) }}",
  "description": "{{ addslashes(strip_tags(substr($content->description ?? '', 0, 300))) }}",
  "startDate": "{{ \Carbon\Carbon::parse($content->start_date)->toIso8601String() }}",
  "endDate": "{{ \Carbon\Carbon::parse($content->end_date)->toIso8601String() }}",
  "eventStatus": "https://schema.org/EventScheduled",
  "eventAttendanceMode": "https://schema.org/{{ $content->event_type == 'online' ? 'OnlineEventAttendanceMode' : 'OfflineEventAttendanceMode' }}",
  "location": {
    "@type": "{{ $content->event_type == 'online' ? 'VirtualLocation' : 'Place' }}",
    "name": "{{ addslashes($content->address ?? $content->city ?? '') }}",
    "address": {
      "@type": "PostalAddress",
      "addressLocality": "{{ addslashes($content->city ?? '') }}",
      "addressCountry": "{{ addslashes($content->country ?? 'AR') }}"
    }
  },
  "image": "{{ $og_image ?? asset('assets/admin/img/event/thumbnail/' . $content->thumbnail) }}",
  "url": "{{ url()->current() }}",
  "organizer": {
    "@type": "Organization",
    "name": "{{ addslashes($websiteInfo->website_title) }}"
  }
  @if(isset($content->price) && $content->price > 0)
  ,"offers": {
    "@type": "Offer",
    "price": "{{ $content->price }}",
    "priceCurrency": "ARS",
    "availability": "https://schema.org/InStock",
    "url": "{{ url()->current() }}"
  }
  @endif
}
</script>

@if(!empty($content->meta_pixel_id))
<!-- Meta Pixel -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '{{ $content->meta_pixel_id }}');
fbq('track', 'ViewContent', {content_name: '{{ addslashes($content->title) }}', content_type: 'event'});
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
!function(w,d,t){w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"];ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e};ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{};ttq._i[e]=[];ttq._i[e]._u=i;ttq._t=ttq._t||{};ttq._t[e]=+new Date;ttq._n=ttq._n||{};ttq._n[e]=n||{};var o=document.createElement("script");o.type="text/javascript";o.async=!0;o.src=i+"?sdkid="+e+"&lib="+t;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
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

    if ($content->date_type == 'multiple') {
        $event_date = eventLatestDates($content->id);
        $date = strtotime(@$event_date->start_date);
    } else {
        $date = strtotime($content->start_date);
    }

    if ($content->date_type == 'multiple') {
        $event_date = eventLatestDates($content->id);
        $startDateTime = @$event_date->start_date_time;
        $endDateTime = @$event_date->end_date_time;
        $last_end_date = eventLastEndDates($content->id);
        $last_end_date = $last_end_date->end_date_time;
        $now_time = \Carbon\Carbon::now()
            ->timezone($websiteInfo->timezone)
            ->translatedFormat('Y-m-d H:i:s');
    } else {
        $now_time = \Carbon\Carbon::now()
            ->timezone($websiteInfo->timezone)
            ->translatedFormat('Y-m-d H:i:s');
        $startDateTime = $content->start_date . ' ' . $content->start_time;
        $endDateTime = $content->end_date . ' ' . $content->end_time;
    }
    $over = false;
  @endphp

  {{-- Hero --}}
  <div class="ed-hero" style="background-image: url('{{ asset('assets/admin/img/event/thumbnail/' . $content->thumbnail) }}');">
    <div class="container" style="position:relative;">
      <div class="ed-hero__actions">
        @if (Auth::guard('customer')->check())
          @php
            $customer_id = Auth::guard('customer')->user()->id;
            $event_id = $content->id;
            $checkWishList = checkWishList($event_id, $customer_id);
          @endphp
        @else
          @php
            $checkWishList = false;
          @endphp
        @endif
        <a href="{{ $checkWishList == false ? route('addto.wishlist', $content->id) : route('remove.wishlist', $content->id) }}"
          class="ed-hero__btn {{ $checkWishList == true ? 'text-success' : '' }}"
          title="{{ $checkWishList ? __('Remove from wishlist') : __('Add to wishlist') }}">
          <i class="fas fa-bookmark"></i>
        </a>
        <a href="javascript:void(0)" data-toggle="modal" data-target=".share-event" class="ed-hero__btn" title="{{ __('Share event') }}">
          <i class="fas fa-share-alt"></i>
        </a>
        @if ($content->event_type != 'online')
          <a href="javascript:void(0)" data-toggle="modal" data-target=".bd-example-modal-lg" class="ed-hero__btn" title="{{ __('Map') }}">
            <i class="fas fa-map-marker-alt"></i>
          </a>
        @endif
      </div>
    </div>
    <div class="ed-hero__inner">
      <div class="container">
        <a href="{{ route('events', ['category' => $content->slug]) }}" class="ed-hero__category">
          <i class="fas fa-tag"></i> {{ $content->name }}
        </a>
        <h1 class="ed-hero__title">
          {{ $content->title }}
          @if ($content->date_type == 'single' && $content->countdown_status == 1)
            @if ($startDateTime >= $now_time)
              <span class="badge badge-info" style="font-size:14px;vertical-align:middle;">{{ __('Upcoming') }}</span>
            @elseif ($startDateTime <= $endDateTime && $endDateTime >= $now_time)
              <span class="badge badge-success" style="font-size:14px;vertical-align:middle;">{{ __('Running') }}</span>
            @else
              @php $over = true; @endphp
              <span class="badge badge-danger" style="font-size:14px;vertical-align:middle;">{{ __('Over') }}</span>
            @endif
          @elseif ($content->date_type == 'multiple')
            @if ($startDateTime >= $now_time)
              <span class="badge badge-info" style="font-size:14px;vertical-align:middle;">{{ __('Upcoming') }}</span>
            @elseif ($startDateTime <= $last_end_date && $last_end_date >= $now_time)
              <span class="badge badge-success" style="font-size:14px;vertical-align:middle;">{{ __('Running') }}</span>
            @else
              @php $over = true; @endphp
              <span class="badge badge-danger" style="font-size:14px;vertical-align:middle;">{{ __('Over') }}</span>
            @endif
          @endif
        </h1>
        <div class="ed-hero__meta">
          <span class="ed-hero__meta-item">
            <i class="far fa-calendar-alt"></i>
            {{ \Carbon\Carbon::parse($date)->timezone($websiteInfo->timezone)->translatedFormat('D d/m/Y') }}
          </span>
          <span class="ed-hero__meta-item">
            <i class="far fa-clock"></i>
            {{ $content->date_type == 'multiple' ? @$event_date->duration : $content->duration }}
          </span>
          @if ($content->event_type == 'venue')
            <span class="ed-hero__meta-item">
              <i class="fas fa-map-marker-alt"></i>
              @if ($content->city != null){{ $content->city }}@endif
              @if ($content->state), {{ $content->state }}@endif
              @if ($content->country), {{ $content->country }}@endif
            </span>
          @else
            <span class="ed-hero__meta-item">
              <i class="fas fa-map-marker-alt"></i> {{ __('Online') }}
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
          <div class="ed-card">
            <div class="ed-card__body" style="padding:16px;">
              <div class="event-details-images">
                @foreach ($images as $item)
                  <a href="{{ asset('assets/admin/img/event-gallery/' . $item->image) }}"><img class="lazy"
                      data-src="{{ asset('assets/admin/img/event-gallery/' . $item->image) }}" alt="{{ $content->title }}"></a>
                @endforeach
              </div>
            </div>
          </div>

          {{-- Session errors --}}
          @if (Session::has('paypal_error'))
            <div class="alert alert-danger">{{ Session::get('paypal_error') }}</div>
          @endif
          @php Session::put('paypal_error', null); @endphp

          {{-- Description card --}}
          <div class="ed-card">
            <div class="ed-card__head">
              <h2 class="ed-card__title">{{ __('Description') }}</h2>
            </div>
            <div class="ed-card__body">
              <div class="summernote-content">
                {!! clean($content->description) !!}
              </div>
            </div>
          </div>

          {{-- Map card --}}
          @if ($content->event_type != 'online')
            <div class="ed-card">
              <div class="ed-card__head">
                <h2 class="ed-card__title">{{ __('Map') }}</h2>
              </div>
              <div class="ed-card__body" style="padding:0;overflow:hidden;border-radius:0 0 16px 16px;">
                <iframe
                  src="//maps.google.com/maps?width=100%25&amp;height=385&amp;hl=es&amp;q={{ $map_address }}&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"
                  height="385" style="width:100%;display:block;border:0;" allowfullscreen="" loading="lazy"
                  title="{{ $content->title }} — {{ __('Map') }}"></iframe>
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
                <h2 class="ed-card__title"><i class="fab fa-youtube" style="color:#F97316;margin-right:8px;"></i>{{ __('Video') }}</h2>
              </div>
              <div class="ed-card__body" style="padding:0;overflow:hidden;border-radius:0 0 16px 16px;">
                <div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;">
                  <iframe src="{{ $youtubeEmbedUrl }}"
                    style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen loading="lazy" title="{{ $content->title }}"></iframe>
                </div>
              </div>
            </div>
          @endif

          {{-- Refund policy card --}}
          @if (!empty($content->refund_policy))
            <div class="ed-card">
              <div class="ed-card__head">
                <h2 class="ed-card__title">{{ __('Return Policy') }}</h2>
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
                <p class="ed-ticket-card__head-title">{{ __('Entradas') }}</p>
                <p class="ed-ticket-card__head-price">
                  @if ($content->pricing_type == 'free')
                    {{ __('Gratis') }}
                  @else
                    @php $min_ticket_price = DB::table('tickets')->where('event_id', $content->id)->min('price'); @endphp
                    @if($min_ticket_price)
                      <span style="font-size:13px;font-weight:400;opacity:0.6;">desde</span> {{ symbolPrice($min_ticket_price) }}
                    @else
                      {{ symbolPrice($content->price ?? 0) }}
                    @endif
                  @endif
                </p>
              </div>
              <div class="ed-ticket-card__body">
                <form action="{{ route('check-out2') }}" method="post"
                  @if ($over == true) onsubmit="return false" @endif>
                  @csrf
                  <input type="hidden" name="event_id" value="{{ $content->id }}" id="">
                  <input type="hidden" name="pricing_type" value="{{ $content->pricing_type }}" id="">
                  <div class="event-details-information">
                    <input type="hidden" name="date_type" value="{{ $content->date_type }}">
                    @if ($content->date_type == 'multiple')
                      @php
                        $dates = eventDates($content->id);
                        $exp_dates = eventExpDates($content->id);
                      @endphp
                      <div class="form-group mb-3">
                        <label class="ed-field-label">{{ __('Select Date') }}</label>
                        <select name="event_date" id="" class="form-control">
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

                    {{-- Countdown --}}
                    @if ($content->date_type == 'single' && $content->countdown_status == 1)
                      @if ($startDateTime >= $now_time)
                        @php
                          $dt = Carbon\Carbon::parse($startDateTime);
                          $year = $dt->year;
                          $month = $dt->month;
                          $day = $dt->day;
                          $end_time = Carbon\Carbon::parse($startDateTime);
                          $hour = $end_time->hour;
                          $minute = $end_time->minute;
                          $now = str_replace('+00:00', '.000' . timeZoneOffset($websiteInfo->timezone) . '00:00', gmdate('c'));
                        @endphp
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
                      @elseif ($startDateTime <= $endDateTime && $endDateTime >= $now_time)
                        <div class="ed-status-pill ed-status-pill--running">
                          <i class="fas fa-circle" style="font-size:8px;"></i> {{ __('El evento está en curso') }}
                        </div>
                      @else
                        <div class="ed-status-pill ed-status-pill--over">
                          <i class="fas fa-times-circle"></i> {{ __('El evento finalizó') }}
                        </div>
                      @endif
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
                            <button class="quantity-down" type="button">
                              -
                            </button>
                            <input class="quantity" type="number" readonly value="0"
                              data-price="{{ $calculate_price }}" data-max_buy_ticket="{{ $ticket->max_buy_ticket }}"
                              name="quantity" data-ticket_id="{{ $ticket->id }}" data-stock="{{ $stock }}"
                              data-purchase="{{ $purchase['status'] }}" data-p_qty="{{ $purchase['p_qty'] }}">
                            <button class="quantity-up" type="button">
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
                          <button class="quantity-down" type="button">
                            -
                          </button>
                          <input class="quantity" readonly type="number" value="0"
                            data-price="{{ $content->price }}" data-max_buy_ticket="{{ $max_buy_ticket }}"
                            name="quantity" data-ticket_id="{{ $ticket->id }}" data-stock="{{ $stock }}"
                            data-purchase="{{ $purchase['status'] }}" data-p_qty="{{ $purchase['p_qty'] }}">
                          <button class="quantity-up" type="button">
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
                                <button class="quantity-down" type="button">
                                  -
                                </button>
                                <input class="quantity" readonly type="number" value="0"
                                  data-price="{{ $calculate_price }}"
                                  data-max_buy_ticket="{{ $ticket->max_buy_ticket }}" name="quantity[]"
                                  data-ticket_id="{{ $ticket->id }}" data-stock="{{ $stock }}"
                                  data-purchase="{{ $purchase['status'] }}" data-p_qty="{{ $purchase['p_qty'] }}">
                                <button class="quantity-up" type="button">
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
                                  <button class="quantity-down_variation" type="button">
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
                                  <button class="quantity-up" type="button">
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
                                <button class="quantity-down" type="button">
                                  -
                                </button>
                                <input class="quantity" data-max_buy_ticket="{{ $ticket->max_buy_ticket }}"
                                  type="number" value="0" data-price="{{ $ticket->price }}" name="quantity[]"
                                  data-ticket_id="{{ $ticket->id }}" readonly data-stock="{{ $stock }}"
                                  data-purchase="{{ $purchase['status'] }}" data-p_qty="{{ $purchase['p_qty'] }}">
                                <button class="quantity-up" type="button">
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
                        <span class="ed-total-label">{{ __('Total Price') }}</span>
                        <span class="ed-total-value" dir="ltr">
                          <span>{{ $basicInfo->base_currency_symbol_position == 'left' ? $basicInfo->base_currency_symbol : '' }}</span>
                          <span id="total_price">0</span>
                          <span>{{ $basicInfo->base_currency_symbol_position == 'right' ? $basicInfo->base_currency_symbol : '' }}</span>
                        </span>
                        <input type="hidden" name="total" id="total">
                      </div>
                      <button class="ed-buy-btn" type="submit">
                        {{ __('Comprar Entradas') }}
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                      </button>
                      <div class="ed-trust">
                        <i class="fas fa-lock"></i> {{ __('Pago seguro') }}
                      </div>
                    @endif
                  </div>

                  @php
                    $spotifyEmbedUrl = null;
                    if (!empty($content->spotify_url)) {
                      preg_match('/spotify\.com\/(?:intl-[a-z-]+\/)?artist\/([a-zA-Z0-9]+)/', $content->spotify_url, $sm);
                      if (!empty($sm[1])) $spotifyEmbedUrl = 'https://open.spotify.com/embed/artist/' . $sm[1] . '?utm_source=generator&theme=0';
                    }
                  @endphp
                  @if($spotifyEmbedUrl)
                    <div class="event-spotify-embed mt-4">
                      <iframe src="{{ $spotifyEmbedUrl }}"
                        width="100%" height="320" frameborder="0" style="height:320px;width:100%;max-width:100%;"
                        allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                        loading="lazy"
                        allowfullscreen
                        title="Spotify"></iframe>
                    </div>
                  @endif
                </form>
              </div>
            </div>
            {{-- /Ticket form card --}}

            {{-- CARD 2: Event info --}}
            <div class="ed-info-card">

              {{-- Organizer --}}
              <div class="ed-info-row">
                <div class="ed-info-icon"><i class="fas fa-user"></i></div>
                <div class="ed-info-text">
                  <strong>{{ __('Organised By') }}</strong>
                  @if ($organizer == '')
                    @php $admin = App\Models\Admin::first(); @endphp
                    <div class="ed-organizer">
                      <img class="lazy" data-src="{{ asset('assets/admin/img/admins/' . $admin->image) }}" alt="{{ $admin->username }}">
                      <div>
                        <p class="ed-organizer__name mb-0">{{ $admin->username }}</p>
                        <a class="ed-organizer__link" href="{{ route('frontend.organizer.details', [$admin->id, str_replace(' ', '-', $admin->username), 'admin' => 'true']) }}">{{ __('View Profile') }}</a>
                      </div>
                    </div>
                  @else
                    <div class="ed-organizer">
                      @if ($organizer->photo != null)
                        <img class="lazy" data-src="{{ asset('assets/admin/img/organizer-photo/' . $organizer->photo) }}" alt="{{ $organizer->username }}">
                      @else
                        <img class="lazy" data-src="{{ asset('assets/front/images/user.png') }}" alt="{{ $organizer->username }}">
                      @endif
                      <div>
                        <p class="ed-organizer__name mb-0">{{ $organizer->username }}</p>
                        <a class="ed-organizer__link" href="{{ route('frontend.organizer.details', [$organizer->id, str_replace(' ', '-', $organizer->username)]) }}">{{ __('View Profile') }}</a>
                      </div>
                    </div>
                  @endif
                </div>
              </div>

              {{-- Address --}}
              @if ($content->address != null)
                <div class="ed-info-row">
                  <div class="ed-info-icon"><i class="fas fa-map-marker-alt"></i></div>
                  <div class="ed-info-text">
                    <strong>{{ __('Ubicación') }}</strong>
                    <span>{{ $content->address }}</span>
                  </div>
                </div>
              @endif

              {{-- Add to Calendar --}}
              @php
                $start_date = str_replace('-', '', $content->start_date);
                $start_time_cal = str_replace(':', '', $content->start_time);
                $end_date = str_replace('-', '', $content->end_date);
                $end_time_cal = str_replace(':', '', $content->end_time);
              @endphp
              <div class="ed-info-row" style="border-bottom:none;padding-bottom:0;">
                <div class="ed-info-icon"><i class="fas fa-calendar-plus"></i></div>
                <div class="ed-info-text">
                  <strong>{{ __('Add to Calendar') }}</strong>
                  <div style="display:flex;gap:8px;margin-top:8px;flex-wrap:wrap;">
                    <a target="_blank"
                      href="//calendar.google.com/calendar/u/0/r/eventedit?text={{ $content->title }}&dates={{ $start_date }}T{{ $start_time_cal }}/{{ $end_date }}T{{ $end_time_cal }}&ctz={{ $websiteInfo->timezone }}&details=For+details,+click+here:+{{ route('event.details', [$content->eventSlug, $content->id]) }}&location={{ $content->event_type == 'online' ? 'Online' : $content->address }}&sf=true"
                      class="ed-cal-btn">
                      <i class="fab fa-google"></i> Google
                    </a>
                    <a target="_blank"
                      href="//calendar.yahoo.com/?v=60&view=d&type=20&TITLE={{ $content->title }}&ST={{ $start_date }}T{{ $start_time_cal }}&ET={{ $end_date }}T{{ $end_time_cal }}&DUR=9959&DESC=For%20details%2C%20click%20here%3A%20{{ route('event.details', [$content->eventSlug, $content->id]) }}&in_loc={{ $content->event_type == 'online' ? 'Online' : $content->address }}"
                      class="ed-cal-btn">
                      Yahoo
                    </a>
                  </div>
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

@endsection
@section('modals')
  @includeIf('frontend.partials.modals')
@endsection

@push('scripts')
<script>
(function () {
  var shareBtn = document.querySelector('[data-target=".share-event"]');
  if (!shareBtn || !navigator.share) return;
  shareBtn.addEventListener('click', function (e) {
    e.preventDefault();
    e.stopImmediatePropagation();
    navigator.share({
      title: {{ json_encode($content->title) }},
      text: {{ json_encode(\Illuminate\Support\Str::limit(strip_tags($content->description ?? ''), 120)) }},
      url: window.location.href
    }).catch(function () {});
  });
})();
</script>
@endpush
