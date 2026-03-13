{{--
  Event Card v2 — Tukipass
  Variables: $event (stdClass de event_contents JOIN events)
  Contexto: $websiteInfo y $currentLanguageInfo disponibles en la vista padre
--}}
@php
  // Fecha
  if ($event->date_type == 'multiple') {
    $ev_date_obj = eventLatestDates($event->id);
    $ev_ts = strtotime(@$ev_date_obj->start_date);
  } else {
    $ev_ts = strtotime($event->start_date);
  }
  $ev_carbon = \Carbon\Carbon::parse($ev_ts)->timezone($websiteInfo->timezone);
  $ev_time   = \Carbon\Carbon::parse(strtotime($event->start_time))->timezone($websiteInfo->timezone);

  // Precio — ticket mínimo
  if ($event->event_type == 'online') {
    $ev_ticket = App\Models\Event\Ticket::where('event_id', $event->id)->orderBy('price', 'asc')->first();
  } else {
    $ev_ticket = App\Models\Event\Ticket::where([['event_id', $event->id], ['price', '!=', null]])->orderBy('price', 'asc')->first();
    if (empty($ev_ticket)) {
      $ev_ticket = App\Models\Event\Ticket::where([['event_id', $event->id], ['f_price', '!=', null]])->orderBy('f_price', 'asc')->first();
    }
  }

  $ev_is_free = !$ev_ticket || $ev_ticket->pricing_type == 'free' || ($ev_ticket->price == null && $ev_ticket->f_price == null);

  if (!$ev_is_free && $ev_ticket) {
    if ($ev_ticket->pricing_type == 'variation') {
      $ev_vars = json_decode($ev_ticket->variations, true);
      $ev_display_price = collect($ev_vars)->min('price');
    } else {
      $ev_display_price = $ev_ticket->price ?? $ev_ticket->f_price;
    }
    if ($ev_ticket->early_bird_discount == 'enable') {
      $ev_discount_date = Carbon\Carbon::parse($ev_ticket->early_bird_discount_date . $ev_ticket->early_bird_discount_time);
      if (!$ev_discount_date->isPast()) {
        if ($ev_ticket->early_bird_discount_type == 'fixed') {
          $ev_display_price = $ev_display_price - $ev_ticket->early_bird_discount_amount;
        } elseif ($ev_ticket->early_bird_discount_type == 'percentage') {
          $ev_display_price = $ev_display_price - (($ev_display_price * $ev_ticket->early_bird_discount_amount) / 100);
        }
      }
    }
  }

  // Ubicación
  if ($event->event_type == 'venue') {
    $ev_location = trim(($event->city ?? '') . ($event->city && $event->country ? ', ' : '') . ($event->country ?? ''));
    if (empty($ev_location)) $ev_location = __('Presencial');
  } else {
    $ev_location = __('Online');
  }

  // Organizador
  if (!empty($event->organizer_id)) {
    $ev_org = App\Models\Organizer::where('id', $event->organizer_id)->select('id', 'username')->first();
    $ev_organizer_name = $ev_org ? $ev_org->username : null;
    $ev_organizer_url  = $ev_org ? route('frontend.organizer.details', [$ev_org->id, str_replace(' ', '-', $ev_org->username)]) : '#';
  } else {
    $ev_admin = App\Models\Admin::first(['id', 'username']);
    $ev_organizer_name = $ev_admin ? $ev_admin->username : null;
    $ev_organizer_url  = $ev_admin ? route('frontend.organizer.details', [$ev_admin->id, str_replace(' ', '-', $ev_admin->username), 'admin' => 'true']) : '#';
  }

  // Descripción para overlay (máx 160 chars)
  $ev_desc = \Illuminate\Support\Str::limit(strip_tags($event->description ?? ''), 160);

  // Wishlist
  $ev_wishlisted = false;
  if (Auth::guard('customer')->check()) {
    $ev_cid = Auth::guard('customer')->user()->id;
    $ev_wishlisted = checkWishList($event->id, $ev_cid);
    $ev_wishlist_route = $ev_wishlisted ? route('remove.wishlist', $event->id) : route('addto.wishlist', $event->id);
  } else {
    $ev_wishlist_route = route('customer.login');
  }
@endphp

<div class="ev-card"
     data-event-url="{{ route('event.details', [$event->slug, $event->id]) }}"
     role="button"
     tabindex="0">

  {{-- ── IMAGEN ── --}}
  <div class="ev-card__img">
    <img class="lazy"
         data-src="{{ asset('assets/admin/img/event/thumbnail/' . $event->thumbnail) }}"
         src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
         alt="{{ $event->title }}"
         loading="lazy">

    <div class="ev-card__gradient"></div>

    {{-- Badge fecha — top-left --}}
    <div class="ev-card__date">
      <span class="ev-card__date-day">{{ $ev_carbon->format('d') }}</span>
      <span class="ev-card__date-month">{{ strtoupper($ev_carbon->translatedFormat('M')) }}</span>
    </div>

    {{-- Badge precio — top-right --}}
    @if($ev_is_free)
      <span class="ev-card__price ev-card__price--free">{{ __('Gratis') }}</span>
    @elseif(isset($ev_display_price))
      <span class="ev-card__price">{{ symbolPrice($ev_display_price) }}</span>
    @endif

    {{-- Overlay hover: fecha enorme + hora enorme --}}
    <div class="ev-card__overlay" aria-hidden="true">
      <div class="ev-card__overlay-date">
        <span class="ev-card__overlay-day">{{ $ev_carbon->format('d') }}</span>
        <span class="ev-card__overlay-month">{{ strtoupper($ev_carbon->translatedFormat('M')) }}</span>
        <span class="ev-card__overlay-year">{{ $ev_carbon->format('Y') }}</span>
      </div>
      <div class="ev-card__overlay-divider"></div>
      <div class="ev-card__overlay-time">
        <span class="ev-card__overlay-hhmm">{{ $ev_time->format('H:i') }}</span>
        <span class="ev-card__overlay-hs">hs</span>
      </div>
    </div>

    {{-- Wishlist dentro de la imagen para posicionamiento correcto --}}
    <a href="{{ $ev_wishlist_route }}"
       class="ev-card__wishlist{{ $ev_wishlisted ? ' ev-card__wishlist--active' : '' }}"
       aria-label="{{ $ev_wishlisted ? __('Remove from wishlist') : __('Add to wishlist') }}"
       onclick="event.stopPropagation()">
      <i class="{{ $ev_wishlisted ? 'fas' : 'far' }} fa-bookmark"></i>
    </a>
  </div>

  {{-- ── BODY ── --}}
  <div class="ev-card__body">

    {{-- Organizador --}}
    @if($ev_organizer_name)
      <p class="ev-card__organizer">
        {{ __('Por') }} <a href="{{ $ev_organizer_url }}" onclick="event.stopPropagation()"><strong>{{ $ev_organizer_name }}</strong></a>
      </p>
    @endif

    {{-- Título --}}
    <h3 class="ev-card__title">{{ $event->title }}</h3>

    {{-- Meta: ubicación + fecha/hora en filas separadas --}}
    <div class="ev-card__meta">
      <div class="ev-card__meta-row">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
        <span>{{ $ev_location }}</span>
      </div>
      <div class="ev-card__meta-row">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        <span>{{ $ev_carbon->translatedFormat('D d/m') }} · {{ $ev_time->format('H:i') }}hs</span>
      </div>
    </div>

    {{-- CTA --}}
    <div class="ev-card__action">
      <span class="ev-card__btn">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M7 15h0M2 9.5h20"/></svg>
        {{ __('Comprar entradas') }}
        <svg class="ev-card__btn-arrow" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </span>
    </div>

  </div>{{-- /.ev-card__body --}}
</div>{{-- /.ev-card --}}
