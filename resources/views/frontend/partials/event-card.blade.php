{{--
  Event Card v6 — Tukipass
  Variables: $event (stdClass de event_contents JOIN events)
  Contexto: $websiteInfo y $currentLanguageInfo disponibles en la vista padre
--}}
@php
  $ev_badge = isset($badgeMap) && is_array($badgeMap) ? ($badgeMap[$event->id] ?? null) : \App\Services\EventBadgeService::getBadge($event);

  // ── Fecha ──
  if ($event->date_type == 'multiple') {
    $ev_date_obj = $latestDatesMap[$event->id] ?? null;
    $ev_ts = $ev_date_obj ? strtotime($ev_date_obj->start_date) : null;
  } else {
    $ev_ts = strtotime($event->start_date);
  }
  $ev_carbon = \Carbon\Carbon::parse($ev_ts)->timezone($websiteInfo->timezone);
  $ev_time   = \Carbon\Carbon::parse(strtotime($event->start_time))->timezone($websiteInfo->timezone);

  // ── Tickets — datos pre-cargados del controller (subquery JOIN) ──
  $ev_is_free      = !$event->has_paid;
  $ev_is_mixed     = $event->has_free && $event->has_paid;
  $ev_min_price    = isset($event->min_price) ? (float) $event->min_price : null;
  $ev_currency_symbol = $basicInfo->base_currency_symbol ?? ($currencyInfo->base_currency_symbol ?? '');
  $ev_currency_position = $basicInfo->base_currency_symbol_position ?? ($currencyInfo->base_currency_symbol_position ?? 'left');

  // ── Ubicación ──
  if ($event->event_type == 'venue') {
    $ev_location = trim(($event->city ?? '') . ($event->city && $event->country ? ', ' : '') . ($event->country ?? ''));
    if (empty($ev_location)) $ev_location = __('Presencial');
  } else {
    $ev_location = __('Online');
  }

  // Badge barra: especial si existe, si no tipo corto (sin ciudad larga)
  $ev_bar_badge = $ev_badge ?? [
    'label' => ($event->event_type ?? '') === 'venue' ? __('Presencial') : __('Online'),
    'fa'    => ($event->event_type ?? '') === 'venue' ? 'fas fa-map-marker-alt' : 'fas fa-video',
    'class' => 'ev-badge--tipo',
  ];

  // CTA
  $ev_cta = $ev_is_free && !$ev_is_mixed
    ? __('Reservar entrada')
    : ($ev_is_mixed ? __('Ver entradas') : __('Comprar entradas'));

  // ── Wishlist — mapa pre-cargado del controller (puede ser vacío si no auth) ──
  $ev_wishlist_map = $ev_wishlist_map ?? [];
  $ev_wishlisted = isset($ev_wishlist_map[$event->id]);
  $ev_wishlist_route = $ev_wishlisted
    ? route('remove.wishlist', $event->id)
    : (Auth::guard('customer')->check() ? route('addto.wishlist', $event->id) : route('customer.login'));

  $ev_card_title_id = 'ev-card-title-' . $event->id;
@endphp

<article class="ev-card"
         data-event-url="{{ route('event.details', [$event->slug, $event->id]) }}"
         tabindex="0"
         role="link"
         aria-labelledby="{{ $ev_card_title_id }}">

  {{-- ── 1. IMAGEN (overlay solo aquí) ── --}}
  <div class="ev-card__visual">

    <div class="ev-card__img">
      <img class="lazy"
           data-src="{{ \App\Services\FileUploadService::imageUrl('assets/admin/img/event/thumbnail/', $event->thumbnail) }}"
           src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
           alt="{{ $event->title }}"
           loading="lazy"
           width="304" height="304">

      @if($ev_is_free && !$ev_is_mixed)
        <span class="ev-card__price-img ev-card__price-img--free">{{ __('Gratis') }}</span>
      @endif

      {{-- Wishlist --}}
      <a href="{{ $ev_wishlist_route }}"
         class="ev-card__wishlist{{ $ev_wishlisted ? ' ev-card__wishlist--active' : '' }}"
         aria-label="{{ $ev_wishlisted ? __('Remove from wishlist') : __('Add to wishlist') }}"
         onclick="event.stopPropagation()">
        <i class="{{ $ev_wishlisted ? 'fas' : 'far' }} fa-bookmark"></i>
      </a>
    </div>

    {{-- OVERLAY hover: solo sobre la imagen --}}
    <div class="ev-card__overlay" aria-hidden="true">
      <div class="ev-card__overlay-date">
        <span class="ev-card__overlay-day">{{ $ev_carbon->format('d') }}</span>
        <span class="ev-card__overlay-month">{{ ucfirst(mb_strtolower($ev_carbon->translatedFormat('M'))) }}</span>
        <span class="ev-card__overlay-year">{{ $ev_carbon->format('Y') }}</span>
      </div>
      <div class="ev-card__overlay-divider"></div>
      <div class="ev-card__overlay-time">
        <span class="ev-card__overlay-hhmm">{{ $ev_time->format('H:i') }}</span>
        <span class="ev-card__overlay-hs">hrs</span>
      </div>
    </div>

  </div>{{-- /.ev-card__visual --}}

  {{-- ── 2. TICKET SVG + INFO ── --}}
  <div class="ev-card__datetime-bar">

    <svg class="ev-card__ticket-bg ev-card__ticket-bg--left"
         viewBox="0 0 230 75" fill="none" preserveAspectRatio="none"
         aria-hidden="true" focusable="false">
      <path d="M228.872 65.0084C228.981 65.0084 229.09 65.0084 229.199 65.0084V63.9268C227.729 63.9268 227.456 62.323 228.763 62.0992C228.927 62.0992 229.036 62.0992 229.199 62.0992V60.9803C229.036 60.9803 228.872 60.9803 228.709 60.943C227.674 60.7192 227.729 59.4138 228.818 59.2274C228.981 59.2274 229.09 59.2274 229.253 59.2274V58.1085C229.199 58.1085 229.09 58.1085 229.036 58.1085C227.565 57.9966 227.674 56.4301 229.036 56.2809C229.09 56.2809 229.144 56.2809 229.253 56.2809V55.1993C229.199 55.1993 229.09 55.1993 229.036 55.1993C227.62 55.0874 227.62 53.5582 228.927 53.3718C229.036 53.3718 229.144 53.3718 229.253 53.3718V52.2901C228.219 52.2529 227.402 51.2831 228.328 50.6864C228.6 50.4999 228.927 50.4253 229.253 50.4253V49.3437C229.09 49.3437 228.927 49.3437 228.763 49.3064C227.783 49.1199 227.729 47.7772 228.872 47.5907C229.036 47.5907 229.144 47.5907 229.308 47.5907V46.4718C229.199 46.4718 229.09 46.4718 228.981 46.4718C227.729 46.3227 227.674 44.7562 229.253 44.6443C229.253 44.6443 229.253 44.6443 229.308 44.6443V43.5627C229.253 43.5627 229.144 43.5627 229.09 43.5627C227.62 43.4135 227.783 41.9216 228.981 41.7351C229.09 41.7351 229.199 41.7351 229.308 41.7351V40.6908C227.838 40.6908 227.511 39.0871 228.981 38.8633C229.09 38.8633 229.199 38.8633 229.308 38.8633V37.7817C227.838 37.7817 227.62 36.1779 228.872 35.9541C229.036 35.9541 229.144 35.9541 229.308 35.9541V34.8352C229.144 34.8352 228.981 34.8352 228.818 34.7979C227.783 34.5742 227.838 33.2688 228.927 33.0823C229.09 33.0823 229.199 33.0823 229.362 33.0823V32.0007C229.144 32.0007 228.872 31.9634 228.654 31.8515C227.456 31.3293 228.273 30.1731 229.362 30.1358V29.0915C227.783 29.0915 227.62 27.4505 229.036 27.264C229.144 27.264 229.253 27.264 229.362 27.264V26.2197C228.273 26.1824 227.456 24.9889 228.654 24.504C228.927 24.3921 229.144 24.3548 229.362 24.3548V23.2732C229.199 23.2732 229.036 23.2732 228.872 23.2359C227.892 23.0121 227.838 21.7068 228.981 21.5203C229.144 21.5203 229.253 21.5203 229.417 21.5203V20.4014C229.362 20.4014 229.253 20.4014 229.199 20.4014C227.729 20.2895 227.838 18.723 229.199 18.5738C229.253 18.5738 229.308 18.5738 229.417 18.5738V17.4922C229.362 17.4922 229.253 17.4922 229.199 17.4922C227.783 17.3803 227.783 15.8511 229.09 15.6647C229.199 15.6647 229.308 15.6647 229.417 15.6647V14.5831C229.362 14.5831 229.308 14.5831 229.199 14.5831C227.838 14.4339 227.674 12.9793 229.09 12.7928C229.199 12.7928 229.308 12.7928 229.417 12.7928V11.7112C227.946 11.7112 227.729 10.1074 228.981 9.88366C229.144 9.88366 229.253 9.88366 229.417 9.88366V8.76475C229.362 8.76475 229.253 8.76475 229.199 8.76475C227.783 8.65286 227.838 7.12369 229.199 6.93721C229.253 6.93721 229.308 6.93721 229.417 6.93721V5.8929C228.6 5.8556 227.838 5.25885 228.219 4.6621C224.897 4.32643 222.283 2.387 222.283 0H0V1.26809C1.52473 1.26809 2.72272 2.08862 2.72272 3.13293C2.72272 4.17724 1.52473 4.99777 0 4.99777V6.11668C1.52473 6.11668 2.72272 6.93721 2.72272 7.98152C2.72272 9.02583 1.52473 9.84636 0 9.84636V10.9653C1.52473 10.9653 2.72272 11.7858 2.72272 12.8301C2.72272 13.8744 1.52473 14.6949 0 14.6949V15.8139C1.52473 15.8139 2.72272 16.6344 2.72272 17.6787C2.72272 18.723 1.52473 19.5435 0 19.5435V20.6624C1.52473 20.6624 2.72272 21.483 2.72272 22.5273C2.72272 23.5716 1.52473 24.3921 0 24.3921V25.511C1.52473 25.511 2.72272 26.3316 2.72272 27.3759C2.72272 28.4202 1.52473 29.2407 0 29.2407V30.3596C1.52473 30.3596 2.72272 31.1801 2.72272 32.2245C2.72272 33.2688 1.52473 34.0893 0 34.0893V35.2082C1.52473 35.2082 2.72272 36.0287 2.72272 37.073C2.72272 38.1174 1.52473 38.9379 0 38.9379V40.0568C1.52473 40.0568 2.72272 40.8773 2.72272 41.9216C2.72272 42.9659 1.52473 43.7865 0 43.7865V44.9054C1.52473 44.9054 2.72272 45.7259 2.72272 46.7702C2.72272 47.8145 1.52473 48.6351 0 48.6351V49.754C1.52473 49.754 2.72272 50.5745 2.72272 51.6188C2.72272 52.6631 1.52473 53.4836 0 53.4836V54.6026C1.52473 54.6026 2.72272 55.4231 2.72272 56.4674C2.72272 57.5117 1.52473 58.3322 0 58.3322V59.4511C1.52473 59.4511 2.72272 60.2717 2.72272 61.316C2.72272 62.3603 1.52473 63.1808 0 63.1808V64.2997C1.52473 64.2997 2.72272 65.1203 2.72272 66.1646C2.72272 67.2089 1.52473 68.0294 0 68.0294V69.1483C1.52473 69.1483 2.72272 69.9688 2.72272 71.0132C2.72272 72.0575 1.52473 72.878 0 72.878V74.1461H222.065C222.283 71.8337 224.897 69.9688 228.219 69.7451C227.565 69.2975 227.838 68.3651 228.981 68.2159C229.036 68.2159 229.09 68.2159 229.199 68.2159V67.1343C227.62 67.1343 227.456 65.4932 228.872 65.3067V65.0084Z" fill="#F97316"/>
    </svg>

    <svg class="ev-card__ticket-bg ev-card__ticket-bg--right"
         viewBox="0 0 110 75" fill="none" preserveAspectRatio="none"
         aria-hidden="true" focusable="false">
      <path d="M109.028 1.2698V0H7.41027C7.41027 2.27817 4.79846 4.18288 1.33628 4.63104C1.5185 4.85513 1.5185 5.1539 1.27554 5.49003C0.971841 5.82615 0.485919 5.93819 0 5.93819V6.98391C1.76146 6.94656 2.12589 8.81392 0 8.81392V9.93433C1.88294 9.93433 1.94367 11.615 0.242956 11.7643C0.182216 11.7643 0.0607399 11.7643 0 11.7643V12.8474C2.00442 12.8101 1.88294 14.7148 0 14.6774V15.7605C1.94368 15.7605 1.94368 17.6278 0 17.5905V18.6736C1.8222 18.6736 2.06515 20.5036 0 20.5036V21.624C1.94368 21.624 1.88294 23.454 0 23.4166V24.4997C1.2148 24.4997 2.06516 25.5454 1.03258 26.1056C0.668146 26.3297 0.303699 26.3671 0 26.3671V27.4128C1.76146 27.4128 2.00442 29.0561 0.364443 29.2428C0.242963 29.2428 0.12148 29.2428 0 29.2428V30.2885C0.364439 30.2885 0.728886 30.3632 1.03258 30.5499C2.06516 31.1475 1.2148 32.1932 0 32.1559V33.2389C2.00442 33.2389 1.88294 35.0689 0 35.0316V36.152C1.88294 36.152 1.94367 37.8326 0.242956 37.982C0.182216 37.982 0.0607399 37.982 0 37.982V39.0651C1.76146 39.0651 2.00442 40.7084 0.364443 40.8951C0.242963 40.8951 0.12148 40.8951 0 40.8951V41.9408C1.8222 41.9408 2.00442 43.8082 0 43.8082V44.8912C1.76146 44.8165 2.06515 46.7212 0 46.7212V47.8417C0.425179 47.8417 0.85035 47.9164 1.09331 48.1404C1.94367 48.7753 1.27554 49.6717 0 49.6717V50.7547C1.15406 50.7547 2.06516 51.7631 1.03258 52.3607C0.668146 52.5474 0.303699 52.6221 0 52.6221V53.7052C1.94368 53.7052 1.94368 55.5725 0 55.5352V56.6182C1.8222 56.6182 2.06515 58.4482 0 58.4482V59.5687C2.00442 59.5687 1.88294 61.3987 0 61.3613V62.4817C1.88294 62.4817 1.94367 64.1624 0.242956 64.3117C0.182216 64.3117 0.0607399 64.3117 0 64.3117V65.3948C1.76146 65.3948 2.00442 67.0381 0.364443 67.2248C0.242963 67.2248 0.12148 67.2248 0 67.2248V68.3079C1.45776 68.3079 1.88294 69.391 0.971841 69.8765C4.43401 70.2126 7.16731 72.0052 7.41027 74.2461H109.028V72.9763C107.327 72.9763 105.991 72.1173 105.991 71.1089C105.991 70.1005 107.327 69.2416 109.028 69.2416V68.1212C107.327 68.1212 105.991 67.2622 105.991 66.2538C105.991 65.2454 107.327 64.3864 109.028 64.3864V63.266C107.327 63.266 105.991 62.407 105.991 61.3987C105.991 60.3903 107.327 59.5313 109.028 59.5313V58.4109C107.327 58.4109 105.991 57.5519 105.991 56.5435C105.991 55.5352 107.327 54.6762 109.028 54.6762V53.5558C107.327 53.5558 105.991 52.6968 105.991 51.6884C105.991 50.68 107.327 49.8211 109.028 49.8211V48.7006C107.327 48.7006 105.991 47.8417 105.991 46.8333C105.991 45.8249 107.327 44.9659 109.028 44.9659V43.8455C107.327 43.8455 105.991 42.9865 105.991 41.9782C105.991 40.9698 107.327 40.1108 109.028 40.1108V38.9904C107.327 38.9904 105.991 38.1314 105.991 37.123C105.991 36.1147 107.327 35.2557 109.028 35.2557V34.1353C107.327 34.1353 105.991 33.2763 105.991 32.2679C105.991 31.2595 107.327 30.4006 109.028 30.4006V29.2801C107.327 29.2801 105.991 28.4212 105.991 27.4128C105.991 26.4044 107.327 25.5454 109.028 25.5454V24.425C107.327 24.425 105.991 23.566 105.991 22.5577C105.991 21.5493 107.327 20.6903 109.028 20.6903V19.5699C107.327 19.5699 105.991 18.7109 105.991 17.7025C105.991 16.6942 107.327 15.8352 109.028 15.8352V14.7148C107.327 14.7148 105.991 13.8558 105.991 12.8474C105.991 11.839 107.327 10.9801 109.028 10.9801V9.85964C107.327 9.85964 105.991 9.00066 105.991 7.99228C105.991 6.98391 107.327 6.12493 109.028 6.12493V5.00451C107.327 5.00451 105.991 4.14553 105.991 3.13716C105.991 2.12879 107.327 1.2698 109.028 1.2698Z" fill="#F97316"/>
    </svg>

    <div class="ev-card__dtbar-content">

      <div class="ev-card__dtbar-left">
        {{-- Fecha: día grande + mes/año apilados --}}
        <div class="ev-card__dtbar-block">
          <span class="ev-card__img-day">{{ $ev_carbon->format('d') }}</span>
          <div class="ev-card__img-date-right">
            <span class="ev-card__img-month-text">{{ ucfirst(mb_strtolower($ev_carbon->translatedFormat('M'))) }}</span>
            <span class="ev-card__img-year-text">{{ $ev_carbon->format('Y') }}</span>
          </div>
        </div>

        <span class="ev-card__dtbar-sep" aria-hidden="true"></span>

        {{-- Hora: hora grande + minutos/hrs apilados --}}
        <div class="ev-card__dtbar-block">
          <span class="ev-card__img-day">{{ $ev_time->format('H') }}</span>
          <div class="ev-card__img-date-right">
            <span class="ev-card__img-month-text">{{ $ev_time->format('i') }}</span>
            <span class="ev-card__img-year-text">hrs</span>
          </div>
        </div>
      </div>

      <div class="ev-card__dtbar-right {{ $ev_bar_badge['class'] ?? '' }}" role="status" aria-label="{{ $ev_bar_badge['label'] }}">
        @if(!empty($ev_bar_badge['fa']))
          <i class="{{ $ev_bar_badge['fa'] }} ev-card__dtbar-badge-icon" aria-hidden="true"></i>
        @endif
      </div>

      {{-- CTA hover --}}
      <div class="ev-card__dtbar-cta" aria-hidden="true">
        <span class="ev-card__dtbar-cta-label">{{ strtoupper($ev_cta) }}</span>
        <span class="ev-card__dtbar-cta-arrow" aria-hidden="true">
          <svg width="28" height="28" stroke-width="2.75" aria-hidden="true"><use href="#icon-arrow-right"/></svg>
        </span>
      </div>

    </div>

  </div>

  {{-- ── BODY ── --}}
  <div class="ev-card__body-panel">
  <div class="ev-card__body">

    {{-- Ubicación — arriba del título --}}
    <div class="ev-card__loc-row">
      <svg width="11" height="11" stroke-width="2.5" aria-hidden="true"><use href="#icon-map-pin"/></svg>
      <span>{{ $ev_location }}</span>
    </div>

    {{-- Título — 2 líneas --}}
    <h3 class="ev-card__title" id="{{ $ev_card_title_id }}">{{ $event->title }}</h3>

  </div>{{-- /.ev-card__body --}}
  </div>{{-- /.ev-card__body-panel --}}
</article>{{-- /.ev-card --}}
