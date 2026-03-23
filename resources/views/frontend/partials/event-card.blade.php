{{--
  Event Card v6 — Tukipass
  Variables: $event (stdClass de event_contents JOIN events)
  Contexto: $websiteInfo y $currentLanguageInfo disponibles en la vista padre
--}}
@php
  $ev_badge = \App\Services\EventBadgeService::getBadge($event);
  // Badge para la barra: siempre uno — el especial si existe, o el por defecto
  $ev_bar_badge = $ev_badge ?? ['icon' => '🎫', 'label' => __('Evento'), 'class' => ''];
@endphp
@php
  // ── Fecha ──
  if ($event->date_type == 'multiple') {
    $ev_date_obj = eventLatestDates($event->id);
    $ev_ts = strtotime(@$ev_date_obj->start_date);
  } else {
    $ev_ts = strtotime($event->start_date);
  }
  $ev_carbon = \Carbon\Carbon::parse($ev_ts)->timezone($websiteInfo->timezone);
  $ev_time   = \Carbon\Carbon::parse(strtotime($event->start_time))->timezone($websiteInfo->timezone);

  // ── Tickets — usa datos pre-cargados (JOIN) cuando están disponibles ──
  if (isset($event->ticket_count)) {
    $ev_is_free      = !$event->has_paid;
    $ev_is_mixed     = $event->has_free && $event->has_paid;
    $ev_display_price = $ev_is_free ? null : $event->min_price;
  } else {
    // Fallback: queries individuales (páginas que no pre-cargan)
    if ($event->event_type == 'online') {
      $ev_ticket = App\Models\Event\Ticket::where('event_id', $event->id)->orderBy('price', 'asc')->first();
    } else {
      $ev_ticket = App\Models\Event\Ticket::where([['event_id', $event->id], ['price', '!=', null]])->orderBy('price', 'asc')->first();
      if (empty($ev_ticket)) {
        $ev_ticket = App\Models\Event\Ticket::where([['event_id', $event->id], ['f_price', '!=', null]])->orderBy('f_price', 'asc')->first();
      }
    }
    $ev_is_free      = !$ev_ticket || $ev_ticket->pricing_type == 'free' || ($ev_ticket->price == null && $ev_ticket->f_price == null);
    $ev_ticket_count = App\Models\Event\Ticket::where('event_id', $event->id)->count();
    $ev_is_mixed     = false;
    if ($ev_is_free && $ev_ticket_count > 1) {
      $ev_is_mixed = App\Models\Event\Ticket::where('event_id', $event->id)
        ->where('pricing_type', '!=', 'free')->where('price', '>', 0)->exists();
    }
    $ev_display_price = null;
    if (!$ev_is_free && $ev_ticket) {
      if ($ev_ticket->pricing_type == 'variation') {
        $ev_vars = json_decode($ev_ticket->variations, true);
        $ev_display_price = collect($ev_vars)->min('price');
      } else {
        $ev_display_price = $ev_ticket->price ?? $ev_ticket->f_price;
      }
      if ($ev_ticket->early_bird_discount == 'enable') {
        $ev_discount_date = \Carbon\Carbon::parse($ev_ticket->early_bird_discount_date . $ev_ticket->early_bird_discount_time);
        if (!$ev_discount_date->isPast()) {
          if ($ev_ticket->early_bird_discount_type == 'fixed') {
            $ev_display_price -= $ev_ticket->early_bird_discount_amount;
          } elseif ($ev_ticket->early_bird_discount_type == 'percentage') {
            $ev_display_price -= ($ev_display_price * $ev_ticket->early_bird_discount_amount / 100);
          }
        }
      }
    }
    if ($ev_is_mixed) {
      $ev_display_price = App\Models\Event\Ticket::where('event_id', $event->id)
        ->where('pricing_type', '!=', 'free')->where('price', '>', 0)
        ->orderBy('price', 'asc')->value('price');
    }
  }

  // ── Ubicación ──
  if ($event->event_type == 'venue') {
    $ev_location = trim(($event->city ?? '') . ($event->city && $event->country ? ', ' : '') . ($event->country ?? ''));
    if (empty($ev_location)) $ev_location = __('Presencial');
  } else {
    $ev_location = __('Online');
  }

  // CTA
  $ev_cta = $ev_is_free && !$ev_is_mixed
    ? __('Reservar entrada')
    : ($ev_is_mixed ? __('Ver entradas') : __('Comprar entradas'));

  // ── Organizador — usa datos pre-cargados (JOIN) cuando están disponibles ──
  if (isset($event->org_username)) {
    $ev_organizer_name = $event->org_username;
    $ev_organizer_url  = $event->org_id
      ? route('frontend.organizer.details', [$event->org_id, str_replace(' ', '-', $event->org_username)])
      : '#';
  } elseif (!empty($event->organizer_id)) {
    $ev_org = App\Models\Organizer::where('id', $event->organizer_id)->select('id', 'username')->first();
    $ev_organizer_name = $ev_org ? $ev_org->username : null;
    $ev_organizer_url  = $ev_org ? route('frontend.organizer.details', [$ev_org->id, str_replace(' ', '-', $ev_org->username)]) : '#';
  } else {
    $ev_admin = App\Models\Admin::first(['id', 'username']);
    $ev_organizer_name = $ev_admin ? $ev_admin->username : null;
    $ev_organizer_url  = $ev_admin ? route('frontend.organizer.details', [$ev_admin->id, str_replace(' ', '-', $ev_admin->username), 'admin' => 'true']) : '#';
  }

  // ── Wishlist — usa mapa pre-cargado cuando está disponible ──
  $ev_wishlisted = false;
  if (isset($ev_wishlist_map)) {
    $ev_wishlisted     = isset($ev_wishlist_map[$event->id]);
    $ev_wishlist_route = $ev_wishlisted ? route('remove.wishlist', $event->id) : route('addto.wishlist', $event->id);
  } elseif (Auth::guard('customer')->check()) {
    $ev_cid            = Auth::guard('customer')->user()->id;
    $ev_wishlisted     = checkWishList($event->id, $ev_cid);
    $ev_wishlist_route = $ev_wishlisted ? route('remove.wishlist', $event->id) : route('addto.wishlist', $event->id);
  } else {
    $ev_wishlist_route = route('customer.login');
  }
@endphp

<div class="ev-card"
     data-event-url="{{ route('event.details', [$event->slug, $event->id]) }}"
     role="button"
     tabindex="0">

  {{-- ── VISUAL: imagen + barra + overlay (sin overflow:hidden en el wrapper) ── --}}
  <div class="ev-card__visual">

    <div class="ev-card__img">
      <img class="lazy"
           data-src="{{ asset('assets/admin/img/event/thumbnail/' . $event->thumbnail) }}"
           src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
           alt="{{ $event->title }}"
           loading="lazy">

      <div class="ev-card__gradient"></div>

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

    {{-- ── BARRA FECHA / HORA / BADGE ── --}}
    <div class="ev-card__datetime-bar">

      {{-- Fecha: día grande + mes/año apilados --}}
      <div class="ev-card__dtbar-block">
        <span class="ev-card__img-day">{{ $ev_carbon->format('d') }}</span>
        <div class="ev-card__img-date-right">
          <span class="ev-card__img-month-text">{{ ucfirst(mb_strtolower($ev_carbon->translatedFormat('M'))) }}</span>
          <span class="ev-card__img-year-text">{{ $ev_carbon->format('Y') }}</span>
        </div>
      </div>

      <div class="ev-card__dtbar-sep"></div>

      {{-- Hora: hora grande + minutos/hrs apilados --}}
      <div class="ev-card__dtbar-block">
        <span class="ev-card__img-day">{{ $ev_time->format('H') }}</span>
        <div class="ev-card__img-date-right">
          <span class="ev-card__img-month-text">{{ $ev_time->format('i') }}</span>
          <span class="ev-card__img-year-text">hrs</span>
        </div>
      </div>

      <div class="ev-card__dtbar-sep"></div>

      {{-- Badge: siempre presente, estilo neutro --}}
      <div class="ev-card__dtbar-block ev-card__dtbar-block--badge">
        <span class="ev-card__dtbar-badge-icon">{{ $ev_bar_badge['icon'] }}</span>
        <span class="ev-card__dtbar-badge-label">{{ $ev_bar_badge['label'] }}</span>
      </div>

      {{-- CTA hover --}}
      <div class="ev-card__dtbar-cta" aria-hidden="true">
        <svg width="13" height="13" stroke-width="2.5"><use href="#icon-ticket"/></svg>
        <span>{{ strtoupper($ev_cta) }}</span>
        <svg width="13" height="13" stroke-width="2.5"><use href="#icon-arrow-right"/></svg>
      </div>

    </div>

    {{-- ── HOVER OVERLAY (fuera de ev-card__img, sin overflow:hidden) ── --}}
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

  {{-- ── BODY ── --}}
  <div class="ev-card__body">

    {{-- Ubicación — arriba del título --}}
    <div class="ev-card__loc-row">
      <svg width="11" height="11" stroke-width="2.5" aria-hidden="true"><use href="#icon-map-pin"/></svg>
      <span>{{ $ev_location }}</span>
    </div>

    {{-- Título — 3 líneas --}}
    <h3 class="ev-card__title">{{ $event->title }}</h3>

    {{-- Entradas: reserva Y entradas desde --}}
    <div class="ev-card__ticket-row">
      <svg width="12" height="12" stroke-width="2" aria-hidden="true"><use href="#icon-ticket"/></svg>
      @if($ev_is_free && !$ev_is_mixed)
        <span>{{ __('Entrada gratuita · Reservá tu lugar') }}</span>
      @elseif($ev_is_mixed && $ev_display_price)
        <span>{{ __('Reserva gratis') }} <strong>{{ __('y') }}</strong> {{ __('entradas desde') }} <strong>{{ symbolPrice($ev_display_price) }}</strong></span>
      @elseif(isset($ev_display_price) && $ev_display_price !== null)
        <span>{{ __('Entradas desde') }} <strong>{{ symbolPrice($ev_display_price) }}</strong></span>
      @else
        <span>{{ __('Ver entradas disponibles') }}</span>
      @endif
    </div>

    {{-- Organizador --}}
    @if($ev_organizer_name)
      <div class="ev-card__org-row">
        <svg width="12" height="12" stroke-width="2" aria-hidden="true"><use href="#icon-user"/></svg>
        <span>{{ __('Por') }} <a href="{{ $ev_organizer_url }}" onclick="event.stopPropagation()">{{ $ev_organizer_name }}</a></span>
      </div>
    @endif

  </div>{{-- /.ev-card__body --}}
</div>{{-- /.ev-card --}}
