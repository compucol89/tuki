@extends('frontend.layout')
@section('pageHeading', 'Checkout de eventos')
@section('body-class', 'checkout-page')
@section('custom-style')
  <link rel="stylesheet" href="{{ asset('assets/admin/css/summernote-content.css') }}">
@endsection
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
@php
  $authUser = Auth::guard('customer')->user();
  $isGuestCheckout = request()->query('type') === 'guest';
  // Compute totals
  $selTickets           = Session::get('selTickets');
  $s_sub_total          = Session::get('sub_total');
  $s_discount           = Session::get('discount') ?: 0;
  $s_early_bird         = Session::get('total_early_bird_dicount') ?: 0;
  $tax_base             = $s_sub_total - ($s_early_bird + $s_discount);
  $computed_tax         = round(($tax_base * $basicData->tax) / 100, 2);
  $grand_total          = round($s_sub_total + $computed_tax - ($s_discount + $s_early_bird), 2);
  Session::put('tax', $computed_tax);
  Session::put('grand_total', $grand_total);

  // Gateway meta
  $gwMeta = [
    'mercadopago' => ['label' => 'Mercado Pago', 'sub' => 'Débito, crédito y efectivo'],
  ];

  // First available gateway for default selection
  $firstGateway = $online_gateways->first() ?? null;
  $defaultGw = $firstGateway ? $firstGateway->keyword : ($offline_gateways->first() ? (string)$offline_gateways->first()->id : '');
@endphp

<section class="checkout-v2 pt-60 pb-80{{ $isGuestCheckout ? ' checkout-v2--guest' : '' }}">
  <div class="container">

    {{-- Page header --}}
    <div class="co-page-header mb-40">
      <a href="{{ url()->previous() }}" class="co-back-link">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
        Volver
      </a>
      <div class="co-page-header__text">
        <h2 class="co-page-title">{{ $isGuestCheckout ? __('Terminá tu compra') : __('Finalizar compra') }}</h2>
        @if ($isGuestCheckout)
          <p class="co-page-subtitle">{{ __('Completá tus datos y pagá con Mercado Pago. Te enviamos el PDF al instante.') }}</p>
        @endif
      </div>
    </div>

    @if (!$isGuestCheckout)
      {{-- Countdown timer (default position) --}}
      <div class="co-timer" id="co-timer">
        <svg class="co-timer__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        <span class="co-timer__text">Tenés <strong id="co-timer-display" class="co-timer__digits" aria-live="polite">10:00</strong> para completar tu compra</span>
      </div>
    @endif

    @if ($isGuestCheckout)
      <div class="co-guest-note" role="note">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
        <div class="co-guest-note__text">
          <strong>{{ __('Después del pago') }}</strong>
          <span>{{ __('Vas a ver la confirmación en Tukipass. Para ver tu QR en el sitio necesitás registrarte con el mismo email.') }}</span>
        </div>
      </div>

      {{-- Countdown timer (guest: más visible, debajo de la nota) --}}
      <div class="co-timer co-timer--guest" id="co-timer" aria-live="polite">
        <svg class="co-timer__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        <span class="co-timer__text">Tenés <strong id="co-timer-display" class="co-timer__digits" aria-live="polite">10:00</strong> para completar tu compra</span>
      </div>
    @endif

    <form class="form" action="{{ route('ticket.booking', [$event->id, 'type' => 'guest']) }}"
          method="POST" enctype="multipart/form-data" id="payment-form">
      @csrf

      {{-- Hidden compatibility fields --}}
      <input type="hidden" name="total"    value="{{ $total }}">
      <input type="hidden" name="quantity" value="{{ $quantity }}">
      <input type="hidden" name="country"  value="{{ old('country', $authUser?->country ?? 'Argentina') }}">
      <input type="hidden" name="state"    value="{{ old('state',   $authUser?->state ?? 'N/A') }}">
      <input type="hidden" name="city"     value="{{ old('city',    $authUser?->city ?? 'N/A') }}">
      <input type="hidden" name="zip_code" value="{{ old('zip_code',$authUser?->zip_code ?? '0000') }}">
      <input type="hidden" name="address"  value="{{ old('address', $authUser?->address ?? 'N/A') }}">

      @if ($selTickets != '')
        @php Session::put('selTickets', $selTickets); @endphp
      @endif

      <div class="row">

        {{-- ===== LEFT: Formulario ===== --}}
        <div class="col-lg-7 mb-4 mb-lg-0">

          {{-- PASO 1: Tus datos --}}
          <div class="co-card mb-4">
            <div class="co-card__head">
              <div class="co-card__step">1</div>
              <div>
                <h3 class="co-card__title">Tus datos</h3>
                <p class="co-card__desc">Te enviamos el ticket a tu email</p>
              </div>
            </div>
            <div class="co-card__body">
              <div class="row">
                <div class="col-sm-6">
                  <div class="co-field">
                    <label for="fname">Nombre *</label>
                    <input type="text" name="fname" id="fname" required
                           class="form-control @error('fname') is-invalid @enderror"
                           value="{{ old('fname', $authUser?->fname ?? '') }}"
                           placeholder="Tu nombre" autocomplete="given-name">
                    @error('fname')<p class="co-field__error">{{ $message }}</p>@enderror
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="co-field">
                    <label for="lname">Apellido *</label>
                    <input type="text" name="lname" id="lname" required
                           class="form-control @error('lname') is-invalid @enderror"
                           value="{{ old('lname', $authUser?->lname ?? '') }}"
                           placeholder="Tu apellido" autocomplete="family-name">
                    @error('lname')<p class="co-field__error">{{ $message }}</p>@enderror
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="co-field">
                    <label for="email">Email *</label>
                    <input type="email" name="email" id="email" required
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $authUser?->email ?? '') }}"
                           placeholder="tu@email.com" autocomplete="email">
                    @error('email')<p class="co-field__error">{{ $message }}</p>@enderror
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="co-field">
                    <label for="phone">Teléfono / WhatsApp *</label>
                    <input type="text" name="phone" id="phone" required
                           class="form-control @error('phone') is-invalid @enderror"
                           value="{{ old('phone', $authUser?->phone ?? '') }}"
                           placeholder="+54 11 XXXX-XXXX" autocomplete="tel">
                    @error('phone')<p class="co-field__error">{{ $message }}</p>@enderror
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="co-field">
                    <label for="dni">DNI *</label>
                    <input type="text" name="dni" id="dni" required
                           class="form-control @error('dni') is-invalid @enderror"
                           value="{{ old('dni') }}"
                           placeholder="Número de documento" inputmode="numeric">
                    @error('dni')<p class="co-field__error">{{ $message }}</p>@enderror
                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- PASO 2: Método de pago (solo si hay total) --}}
          @if ($total != 0 || Session::get('sub_total') != 0)
          <div class="co-card">
            <div class="co-card__head">
              <div class="co-card__step">2</div>
              <div>
                <h3 class="co-card__title">Método de pago</h3>
                <p class="co-card__desc">Todos los pagos son seguros y encriptados</p>
              </div>
            </div>
            <div class="co-card__body">

              {{-- Errores de pago --}}
              @if (Session::has('paypal_error'))
                <p class="text-danger mb-2">{{ Session::get('paypal_error') }}</p>
                @php Session::forget('paypal_error'); @endphp
              @endif
              @if (Session::has('error'))
                <p class="text-danger mb-2">{{ Session::get('error') }}</p>
              @endif
              @if (Session::has('currency_error'))
                <p class="text-danger mb-2">{{ Session::get('currency_error') }}</p>
              @endif

              {{-- Select oculto — el JS lo escucha --}}
              <select name="gateway" id="payment" class="d-none">
                <option value=""></option>
                @foreach ($online_gateways as $og)
                  <option value="{{ $og->keyword }}"
                    {{ (old('gateway', $defaultGw) == $og->keyword) ? 'selected' : '' }}>
                    {{ $og->name }}
                  </option>
                @endforeach
                @foreach ($offline_gateways as $og)
                  <option value="{{ $og->id }}"
                    {{ (old('gateway', $defaultGw) == $og->id) ? 'selected' : '' }}>
                    {{ $og->name }}
                  </option>
                @endforeach
              </select>

              {{-- Tarjetas visuales de pago --}}
              <div class="pgw-grid">
                @foreach ($online_gateways as $og)
                  @php
                    $meta = $gwMeta[$og->keyword] ?? ['label' => $og->name, 'sub' => 'Pago online'];
                    $isDefault = (old('gateway', $defaultGw) == $og->keyword);
                  @endphp
                  <div class="pgw-card {{ $isDefault ? 'pgw-card--active' : '' }}"
                       data-gateway="{{ $og->keyword }}" role="button" tabindex="0">
                    <div class="pgw-card__radio">
                      <div class="pgw-card__dot"></div>
                    </div>
                    <div class="pgw-card__logo pgw-card__logo--{{ $og->keyword }}">
                      @if ($og->keyword == 'mercadopago')
                        <img src="{{ asset('assets/front/images/mercadopago_logo.svg') }}" alt="Mercado Pago" style="width:130px;height:auto;display:block;">
                      @elseif ($og->keyword == 'stripe')
                        <svg viewBox="0 0 56 36" xmlns="http://www.w3.org/2000/svg">
                          <rect width="56" height="36" rx="5" fill="#635BFF"/>
                          <text x="28" y="22" font-family="Arial,sans-serif" font-size="11" font-weight="700" fill="white" text-anchor="middle">stripe</text>
                        </svg>
                      @elseif ($og->keyword == 'paypal')
                        <svg viewBox="0 0 56 36" xmlns="http://www.w3.org/2000/svg">
                          <rect width="56" height="36" rx="5" fill="#003087"/>
                          <text x="28" y="22" font-family="Arial,sans-serif" font-size="10" font-weight="700" fill="#009CDE" text-anchor="middle">Pay</text>
                          <text x="38" y="22" font-family="Arial,sans-serif" font-size="10" font-weight="700" fill="white" text-anchor="middle">Pal</text>
                        </svg>
                      @else
                        <svg viewBox="0 0 56 36" xmlns="http://www.w3.org/2000/svg">
                          <rect width="56" height="36" rx="5" fill="#1e2532"/>
                          <text x="28" y="22" font-family="Arial,sans-serif" font-size="8" font-weight="700" fill="white" text-anchor="middle">{{ strtoupper(substr($og->keyword,0,7)) }}</text>
                        </svg>
                      @endif
                    </div>
                    <div class="pgw-card__info">
                      <span class="pgw-card__name">{{ $meta['label'] }}</span>
                      <span class="pgw-card__sub">{{ $meta['sub'] }}</span>
                    </div>
                  </div>
                @endforeach

                @foreach ($offline_gateways as $og)
                  @php $isDefault = (old('gateway', $defaultGw) == (string)$og->id); @endphp
                  <div class="pgw-card {{ $isDefault ? 'pgw-card--active' : '' }}"
                       data-gateway="{{ $og->id }}" role="button" tabindex="0">
                    <div class="pgw-card__radio">
                      <div class="pgw-card__dot"></div>
                    </div>
                    <div class="pgw-card__logo">
                      <svg viewBox="0 0 56 36" xmlns="http://www.w3.org/2000/svg">
                        <rect width="56" height="36" rx="5" fill="#374151"/>
                        <text x="28" y="16" font-family="Arial,sans-serif" font-size="7" fill="white" text-anchor="middle">Pago</text>
                        <text x="28" y="26" font-family="Arial,sans-serif" font-size="7" fill="white" text-anchor="middle">Manual</text>
                      </svg>
                    </div>
                    <div class="pgw-card__info">
                      <span class="pgw-card__name">{{ $og->name }}</span>
                      <span class="pgw-card__sub">Pago presencial / transferencia</span>
                    </div>
                  </div>
                @endforeach
              </div>

              {{-- Offline gateway info panels --}}
              @foreach ($offline_gateways as $offlineGateway)
                <div class="@if ($errors->has('attachment') && request()->session()->get('gatewayId') == $offlineGateway->id) d-block @else d-none @endif offline-gateway-info co-offline-info mt-3"
                     id="{{ 'offline-gateway-' . $offlineGateway->id }}">
                  @if (!is_null($offlineGateway->short_description))
                    <div class="mb-3">
                      <label class="fw-semibold">Descripción</label>
                      <p class="text-muted">{{ $offlineGateway->short_description }}</p>
                    </div>
                  @endif
                  @if (!is_null($offlineGateway->instructions))
                    <div class="mb-3">
                      <label class="fw-semibold">Instrucciones</label>
                      <div class="summernote-content">{!! clean($offlineGateway->instructions) !!}</div>
                    </div>
                  @endif
                  @if ($offlineGateway->has_attachment == 1)
                    <div class="mb-3">
                      <label class="fw-semibold">Adjunto *</label><br>
                      <input type="file" name="attachment">
                      @error('attachment')<p class="co-field__error mt-1">{{ $message }}</p>@enderror
                    </div>
                  @endif
                </div>
              @endforeach

            </div>
          </div>
          @endif

        </div>

        {{-- ===== RIGHT: Resumen del pedido (sticky) ===== --}}
        <div class="col-lg-5">
          <div class="co-summary">

            {{-- Info del evento --}}
            <div class="co-summary__event">
              <a href="{{ route('event.details', [$event->slug, $event->id]) }}" class="co-summary__thumb">
                <img src="{{ asset('assets/admin/img/event/thumbnail/' . $event->thumbnail) }}"
                     alt="{{ $event->title }}">
              </a>
              <div class="co-summary__event-info">
                <h5 class="co-summary__event-title">
                  <a href="{{ route('event.details', [$event->slug, $event->id]) }}">{{ $event->title }}</a>
                </h5>
                <p class="co-summary__event-meta">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                  {{ \Carbon\Carbon::parse($event->start_date)->translatedFormat('D d/m/Y') }}
                  &nbsp;
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                  {{ $event->start_time }}
                </p>
                @if ($event->event_type == 'venue')
                  <p class="co-summary__event-meta">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    {{ implode(', ', array_filter([$event->city, $event->country])) }}
                  </p>
                @else
                  <p class="co-summary__event-meta">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    Evento online
                  </p>
                @endif
              </div>
            </div>

            <div class="co-summary__divider"></div>

            {{-- Desglose tickets --}}
            <div id="couponReload">
              @php
                $ticketIds = $selTickets ? array_column($selTickets, 'ticket_id') : [];
                $ticketsMap = $ticketIds ? App\Models\Event\Ticket::whereIn('id', $ticketIds)->select('id','pricing_type')->get()->keyBy('id') : collect();
                $ticketContentsMap = $ticketIds ? App\Models\Event\TicketContent::whereIn('ticket_id', $ticketIds)->where('language_id', $currentLanguageInfo->id)->select('ticket_id','title')->get()->keyBy('ticket_id') : collect();
                $ticketContentsDefault = $ticketIds ? App\Models\Event\TicketContent::whereIn('ticket_id', $ticketIds)->select('ticket_id','title')->get()->keyBy('ticket_id') : collect();
              @endphp

              @if ($selTickets)
                <div class="co-ticket-list mb-3">
                  @foreach ($selTickets as $selTicket)
                    @php
                      $t = $ticketsMap[$selTicket['ticket_id']] ?? null;
                      if ($t && $t->pricing_type == 'variation') {
                        $varContent = App\Models\Event\VariationContent::where([['ticket_id', $selTicket['ticket_id']], ['name', $selTicket['name']], ['language_id', $currentLanguageInfo->id]])->value('name');
                        $ticketName = $varContent ?: $selTicket['name'];
                      } else {
                        $tc = $ticketContentsMap[$selTicket['ticket_id']] ?? $ticketContentsDefault[$selTicket['ticket_id']] ?? null;
                        $ticketName = $tc ? $tc->title : $selTicket['name'];
                      }
                    @endphp
                    <div class="co-ticket-row">
                      <span class="co-ticket-row__name">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#F97316" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                        {{ $ticketName }}
                      </span>
                      <span class="co-ticket-row__qty">{{ $selTicket['qty'] }}x</span>
                    </div>
                  @endforeach
                </div>
                <div class="co-summary__divider"></div>
              @endif

              <div class="co-price-rows">
                <div class="co-price-row">
                  <span>Tickets ({{ $quantity }})</span>
                  <span dir="ltr">
                    @if ($s_early_bird)
                      {{ symbolPrice($s_sub_total - $s_early_bird) }}
                      <del class="text-muted ms-1" style="font-size:12px">{{ symbolPrice($s_sub_total) }}</del>
                    @else
                      {{ symbolPrice($s_sub_total) }}
                    @endif
                  </span>
                </div>

                @if ($s_discount)
                  <div class="co-price-row co-price-row--discount">
                    <span>Descuento cupón</span>
                    <span dir="ltr" class="text-success">− {{ symbolPrice($s_discount) }}</span>
                  </div>
                @endif

                @if ($s_early_bird)
                  <div class="co-price-row co-price-row--discount">
                    <span>Desc. early bird</span>
                    <span dir="ltr" class="text-success">− {{ symbolPrice($s_early_bird) }}</span>
                  </div>
                @endif

                @if ($basicData->tax > 0)
                  <div class="co-price-row">
                    <span>Impuestos ({{ $basicData->tax }}%)</span>
                    <span dir="ltr" class="text-danger">+ {{ symbolPrice($computed_tax) }}</span>
                  </div>
                @endif
              </div>

              <div class="co-summary__divider"></div>

              <div class="co-price-row co-price-row--total">
                <span>Total</span>
                <span dir="ltr">{{ symbolPrice($grand_total) }}</span>
              </div>
            </div>

            {{-- Cupón --}}
            @if ($total != 0 || Session::get('sub_total') != 0)
              <div class="co-coupon mt-3">
                <div class="co-coupon__toggle" id="couponToggle">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                  Tengo un cupón de descuento
                </div>
                <div class="co-coupon__body" id="couponBody" style="display:none">
                  <div class="co-coupon__row mt-2">
                    <input type="text" id="coupon-code" class="form-control" placeholder="Ingresá el código">
                    <button type="button" class="base-btn co-coupon__btn">Aplicar</button>
                  </div>
                </div>
              </div>
            @endif

            {{-- Botón de pago --}}
            <div class="mt-4">
              @if ($total != 0 || Session::get('sub_total') != 0)
                <button type="submit" class="co-pay-btn">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                  Pagar {{ symbolPrice($grand_total) }}
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </button>
              @else
                <button type="submit" class="co-pay-btn">
                  Confirmar reserva gratis
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </button>
              @endif
            </div>

            {{-- Trust badges --}}
            <div class="co-trust mt-3">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
              @if ($isGuestCheckout)
                {{ __('Pago seguro con Mercado Pago · PDF con QR al mail al instante') }}
              @else
                Pago 100% seguro · Tu ticket llega al email en segundos
              @endif
            </div>

            <div class="co-trust-logos mt-3">
              {{-- Visa --}}
              <svg viewBox="0 0 56 36" xmlns="http://www.w3.org/2000/svg" width="46" height="30">
                <rect width="56" height="36" rx="5" fill="#1A1F71"/>
                <text x="28" y="24" font-family="Arial,sans-serif" font-size="16" font-weight="900" font-style="italic" fill="white" text-anchor="middle" letter-spacing="1">VISA</text>
              </svg>
              {{-- Mastercard --}}
              <svg viewBox="0 0 56 36" xmlns="http://www.w3.org/2000/svg" width="46" height="30">
                <rect width="56" height="36" rx="5" fill="#252525"/>
                <circle cx="22" cy="18" r="10" fill="#EB001B"/>
                <circle cx="34" cy="18" r="10" fill="#F79E1B"/>
                <path d="M28 10.4a10 10 0 0 1 0 15.2A10 10 0 0 1 28 10.4z" fill="#FF5F00"/>
              </svg>
              {{-- Amex --}}
              <svg viewBox="0 0 56 36" xmlns="http://www.w3.org/2000/svg" width="46" height="30">
                <rect width="56" height="36" rx="5" fill="#2557D6"/>
                <text x="28" y="23" font-family="Arial,sans-serif" font-size="11" font-weight="700" fill="white" text-anchor="middle" letter-spacing="1">AMEX</text>
              </svg>
              {{-- Mercado Pago --}}
              <img src="{{ asset('assets/front/images/mercadopago_logo.svg') }}" alt="Mercado Pago" style="height:30px;width:auto;display:block;">
              {{-- Cabal (tarjeta argentina) --}}
              <svg viewBox="0 0 56 36" xmlns="http://www.w3.org/2000/svg" width="46" height="30">
                <rect width="56" height="36" rx="5" fill="#005B9E"/>
                <text x="28" y="23" font-family="Arial,sans-serif" font-size="11" font-weight="700" fill="white" text-anchor="middle">Cabal</text>
              </svg>
              {{-- Naranja --}}
              <svg viewBox="0 0 56 36" xmlns="http://www.w3.org/2000/svg" width="46" height="30">
                <rect width="56" height="36" rx="5" fill="#F97316"/>
                <text x="28" y="23" font-family="Arial,sans-serif" font-size="9" font-weight="700" fill="white" text-anchor="middle">Naranja</text>
              </svg>
            </div>

          </div>
        </div>
        {{-- / RIGHT --}}

      </div>
    </form>
  </div>
</section>
@endsection

@section('custom-script')
  <script type="text/javascript">
    let url = "{{ route('apply-coupon') }}";
  </script>
  <script src="{{ asset('assets/front/js/event_checkout.js') }}"></script>
  <script>
    (function initCheckoutUi() {
      try {
        // Payment cards → drive hidden select
        document.querySelectorAll('.pgw-card').forEach(function(card) {
          card.addEventListener('click', function() {
            document.querySelectorAll('.pgw-card').forEach(c => c.classList.remove('pgw-card--active'));
            this.classList.add('pgw-card--active');
            var gw = this.dataset.gateway;
            var sel = document.getElementById('payment');
            if (!sel) return;
            sel.value = gw;
            if (window.jQuery) {
              window.jQuery(sel).trigger('change');
            } else {
              sel.dispatchEvent(new Event('change', { bubbles: true }));
            }
          });
          card.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); this.click(); }
          });
        });

        // Trigger default gateway on load
        document.addEventListener('DOMContentLoaded', function() {
          try {
            var active = document.querySelector('.pgw-card--active');
            var sel = document.getElementById('payment');
            if (!active || !sel) return;
            sel.value = active.dataset.gateway;
            if (window.jQuery) {
              window.jQuery(sel).trigger('change');
            } else {
              sel.dispatchEvent(new Event('change', { bubbles: true }));
            }
          } catch (e) {}
        });

        // Coupon toggle
        var couponToggle = document.getElementById('couponToggle');
        if (couponToggle) {
          couponToggle.addEventListener('click', function() {
            var body = document.getElementById('couponBody');
            if (!body) return;
            body.style.display = body.style.display === 'none' ? 'block' : 'none';
            this.classList.toggle('co-coupon__toggle--open');
          });
        }
      } catch (e) {
        // No bloquear el countdown si algo falla acá
        console && console.warn && console.warn('Checkout UI init warning:', e);
      }
    })();

    // Checkout countdown — 10 minutes
    (function() {
      var TOTAL = 10 * 60;
      var remaining = TOTAL;
      var display  = document.getElementById('co-timer-display');
      var banner   = document.getElementById('co-timer');
      if (!display || !banner) return;

      function pad2(n) {
        n = Math.floor(n);
        return (n < 10 ? '0' : '') + n;
      }

      function formatTime(seconds) {
        var m = Math.floor(seconds / 60);
        var s = seconds % 60;
        return pad2(m) + ':' + pad2(s);
      }

      function pulseTick() {
        banner.classList.remove('co-timer--tick');
        // Re-trigger animation reliably
        void banner.offsetWidth;
        banner.classList.add('co-timer--tick');
      }

      function render() {
        display.textContent = formatTime(Math.max(remaining, 0));
        pulseTick();

        if (remaining <= 120) {
          banner.classList.add('co-timer--urgent');
        } else {
          banner.classList.remove('co-timer--urgent');
        }
      }

      function tick() {
        remaining--;
        render();

        if (remaining <= 0) {
          clearInterval(interval);
          banner.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span class="co-timer__text">Tu tiempo expiró. Serás redirigido...</span>';
          setTimeout(function() { window.location.href = document.referrer || '/'; }, 3000);
        }
      }

      render();
      var interval = setInterval(tick, 1000);
    })();
  </script>
@endsection
