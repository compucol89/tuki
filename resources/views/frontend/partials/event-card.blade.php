{{--
  Event Card v3 — Tukipass
  Variables: $event (stdClass de event_contents JOIN events)
  Contexto: $websiteInfo y $currentLanguageInfo disponibles en la vista padre
--}}
@php
  $ev_badge = \App\Services\EventBadgeService::getBadge($event);
@endphp
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

  // "Desde" — cuando hay más de un tipo de ticket de pago
  $ev_ticket_count = App\Models\Event\Ticket::where('event_id', $event->id)->count();
  $ev_show_desde = !$ev_is_free && $ev_ticket_count > 1;

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

  // Descripción para overlay (máx 120 chars)
  $ev_desc = \Illuminate\Support\Str::limit(strip_tags($event->description ?? ''), 120);

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
     tabindex="0"
     onclick="if(!event.target.closest('a,button')){window.location.href=this.dataset.eventUrl}"
     onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();window.location.href=this.dataset.eventUrl}">

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

    {{-- Overlay hover: fecha enorme (izq) + hora enorme (der) --}}
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

    {{-- Wishlist — top-right de la imagen --}}
    <a href="{{ $ev_wishlist_route }}"
       class="ev-card__wishlist{{ $ev_wishlisted ? ' ev-card__wishlist--active' : '' }}"
       aria-label="{{ $ev_wishlisted ? __('Remove from wishlist') : __('Add to wishlist') }}"
       onclick="event.stopPropagation()">
      <i class="{{ $ev_wishlisted ? 'fas' : 'far' }} fa-bookmark"></i>
    </a>

    {{-- Badge social proof — bottom-left de la imagen --}}
    @if($ev_badge)
      <span class="ev-badge {{ $ev_badge['class'] }}">
        <span class="ev-badge__icon">{{ $ev_badge['icon'] }}</span>
        {{ $ev_badge['label'] }}
      </span>
    @endif
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

    {{-- Meta: ubicación + fecha/hora --}}
    <div class="ev-card__meta">
      <div class="ev-card__meta-row">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
        <span>{{ $ev_location }}</span>
      </div>
      <div class="ev-card__meta-row">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <span>{{ $ev_carbon->translatedFormat('l d/m/Y') }} · {{ $ev_time->format('H:i') }}hs</span>
      </div>
    </div>

    {{-- Footer: precio + botón --}}
    <div class="ev-card__footer">
      @if($ev_is_free)
        <span class="ev-card__price ev-card__price--free">{{ __('Gratis') }}</span>
      @elseif(isset($ev_display_price))
        <span class="ev-card__price">{{ isset($ev_show_desde) && $ev_show_desde ? __('Desde') . ' ' : '' }}{{ symbolPrice($ev_display_price) }}</span>
      @else
        <span></span>
      @endif

      <span class="ev-card__btn">
        {{ __('Comprar entradas') }}
        <svg class="ev-card__btn-arrow" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </span>
    </div>

  </div>{{-- /.ev-card__body --}}
</div>{{-- /.ev-card --}}
