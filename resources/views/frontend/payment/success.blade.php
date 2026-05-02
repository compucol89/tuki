@extends('frontend.layout')
@section('pageHeading', '¡Reserva confirmada!')

@section('content')
@php
  $isFree    = $booking->paymentStatus === 'free' || $booking->price == 0;
  $isPending = $booking->paymentStatus === 'pending';
  $exts      = $booking->invoice ? pathinfo($booking->invoice, PATHINFO_EXTENSION) : null;
  $hasPdf    = $exts === 'pdf';

  if ($event->date_type == 'multiple' && !empty($event_date)) {
    $calStartDate = str_replace('-', '', $event_date->start_date);
    $calStartTime = str_replace(':', '', $event_date->start_time);
    $calEndDate   = str_replace('-', '', $event_date->end_date);
    $calEndTime   = str_replace(':', '', $event_date->end_time);
  } else {
    $calStartDate = str_replace('-', '', $event->start_date ?? '');
    $calStartTime = str_replace(':', '', $event->start_time ?? '');
    $calEndDate   = str_replace('-', '', $event->end_date ?? '');
    $calEndTime   = str_replace(':', '', $event->end_time ?? '');
  }
  $calSTime = $calStartTime ? $calStartTime - 5 : '0000';
  $calETime = $calEndTime   ? $calEndTime - 5   : '0000';

  $eventTitle   = @$event->information->title ?? '';
  $eventSlug    = @$event->information->slug  ?? '';
  $eventAddress = @$event->information->address ?? ($event->event_type == 'online' ? __('Online') : '');

  $position   = $booking->currencyTextPosition;
  $currency   = $booking->currencyText;
  $variations = $booking->variation ? json_decode($booking->variation, true) : null;
@endphp

{{-- HERO --}}
<div class="ps-hero {{ $isFree ? 'ps-hero--free' : ($isPending ? 'ps-hero--pending' : 'ps-hero--paid') }}">
  <div class="ps-hero__inner">
    <div class="ps-hero__icon">
      @if($isPending)
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      @else
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
      @endif
    </div>
    <h1 class="ps-hero__title">
      @if($isPending) Solicitud recibida
      @elseif($isFree) ¡Tu lugar está reservado!
      @else ¡Pago confirmado!
      @endif
    </h1>
    <p class="ps-hero__sub">
      @if($isPending)
        Tu solicitud fue enviada. Te avisamos por email cuando sea confirmada.
      @else
        Enviamos la confirmación a <strong>{{ $booking->email }}</strong>
      @endif
    </p>
    <div class="ps-hero__id">Reserva #{{ $booking->booking_id }}</div>
  </div>
</div>

{{-- BODY --}}
<section class="ps-body">
  <div class="container">
    <div class="ps-layout">

      {{-- Columna principal --}}
      <div class="ps-main">

        {{-- Resumen del evento --}}
        <div class="ps-card">
          <div class="ps-card__head">
            <h3 class="ps-card__title">Resumen del evento</h3>
          </div>
          <div class="ps-event-row">
            @if($event->thumbnail)
              <img class="ps-event-row__thumb lazy"
                   data-src="{{ asset('assets/admin/img/event/thumbnail/' . $event->thumbnail) }}"
                   src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                   alt="{{ $eventTitle }}">
            @endif
            <div class="ps-event-row__info">
              <h4 class="ps-event-row__name">{{ $eventTitle }}</h4>
              <div class="ps-event-row__metas">
                @if($booking->event_date)
                  <span class="ps-meta">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    {{ \Carbon\Carbon::parse($booking->event_date)->translatedFormat('D d \d\e F \d\e Y · H:i') }}hs
                  </span>
                @endif
                @if($eventAddress)
                  <span class="ps-meta">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    {{ $eventAddress }}
                  </span>
                @endif
                <span class="ps-meta {{ $event->event_type == 'online' ? 'ps-meta--online' : 'ps-meta--venue' }}">
                  {{ $event->event_type == 'online' ? 'Online' : 'Presencial' }}
                </span>
              </div>
              @if($eventSlug)
                <a href="{{ route('event.details', [$eventSlug, $event->id]) }}" target="_blank" class="ps-event-row__link">
                  Ver página del evento
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
                </a>
              @endif
            </div>
          </div>
        </div>

        {{-- Entradas (variaciones) --}}
        @if($variations)
          <div class="ps-card">
            <div class="ps-card__head">
              <h3 class="ps-card__title">Entradas reservadas</h3>
              <span class="ps-pill">{{ collect($variations)->sum('qty') }} {{ collect($variations)->sum('qty') == 1 ? 'entrada' : 'entradas' }}</span>
            </div>
            <div class="cd-table-wrap">
              <table class="cd-table">
                <thead>
                  <tr><th>Tipo</th><th>Cant.</th><th>Precio unit.</th><th>Subtotal</th></tr>
                </thead>
                <tbody>
                  @foreach($variations as $variation)
                    @php
                      $ticket = App\Models\Event\Ticket::find($variation['ticket_id']);
                      $tc     = $ticket ? App\Models\Event\TicketContent::where([['ticket_id',$ticket->id],['language_id',$currentLanguageInfo->id]])->first() : null;
                      $qty       = max($variation['qty'], 1);
                      $evd       = $variation['early_bird_dicount'] / $qty;
                      $unitRaw   = $variation['price'] / $qty;
                      $unitFinal = $unitRaw - $evd;
                    @endphp
                    <tr>
                      <td><strong>{{ $tc ? $tc->title : '—' }}</strong></td>
                      <td>{{ $variation['qty'] }}</td>
                      <td>
                        {{ symbolPrice($unitFinal) }}
                        @if($variation['early_bird_dicount'] > 0)
                          <del class="text-muted ms-1" style="font-size:12px">{{ symbolPrice($unitRaw) }}</del>
                        @endif
                      </td>
                      <td><strong>{{ symbolPrice($variation['price'] - $variation['early_bird_dicount']) }}</strong></td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        @endif

        {{-- Datos de facturación --}}
        <div class="ps-card">
          <div class="ps-card__head">
            <h3 class="ps-card__title">Datos de facturación</h3>
          </div>
          <div class="ps-info-grid">
            @if($booking->fname || $booking->lname)
              <div class="ps-info-item"><span class="ps-info-item__label">Nombre</span><span class="ps-info-item__val">{{ trim($booking->fname . ' ' . $booking->lname) }}</span></div>
            @endif
            @if($booking->email)
              <div class="ps-info-item"><span class="ps-info-item__label">Email</span><span class="ps-info-item__val">{{ $booking->email }}</span></div>
            @endif
            @if($booking->phone)
              <div class="ps-info-item"><span class="ps-info-item__label">Teléfono</span><span class="ps-info-item__val">{{ $booking->phone }}</span></div>
            @endif
            @if($booking->country || $booking->city)
              <div class="ps-info-item"><span class="ps-info-item__label">Ubicación</span><span class="ps-info-item__val">{{ implode(', ', array_filter([$booking->city, $booking->state, $booking->country])) }}</span></div>
            @endif
          </div>
        </div>

      </div>

      {{-- Sidebar --}}
      <div class="ps-side">

        {{-- Detalle del pago --}}
        <div class="ps-card ps-card--sticky">
          <div class="ps-card__head">
            <h3 class="ps-card__title">Detalle del pago</h3>
          </div>
          <div class="ps-payment-list">
            @if(!is_null($booking->quantity) && is_null($booking->variation))
              <div class="ps-pay-row"><span>Cantidad</span><span>{{ $booking->quantity }} {{ $booking->quantity == 1 ? 'entrada' : 'entradas' }}</span></div>
            @endif
            @if($booking->early_bird_discount)
              <div class="ps-pay-row ps-pay-row--discount"><span>Descuento early bird</span><span>− {{ $booking->currencySymbol }}{{ $booking->early_bird_discount }}</span></div>
            @endif
            @if($booking->discount)
              <div class="ps-pay-row ps-pay-row--discount"><span>Descuento cupón</span><span>− {{ $booking->currencySymbol }}{{ $booking->discount }}</span></div>
            @endif
            @if(!is_null($booking->tax) && $booking->tax > 0)
              <div class="ps-pay-row"><span>Impuestos</span><span dir="ltr">{{ $position == 'left' ? $currency . ' ' : '' }}{{ $booking->tax }}{{ $position == 'right' ? ' ' . $currency : '' }}</span></div>
            @endif
            <div class="ps-pay-row ps-pay-row--total">
              <span>Total</span>
              <span dir="ltr">{{ $isFree ? 'Gratis' : $booking->currencySymbol . ($booking->price + $booking->tax) }}</span>
            </div>
            @if($booking->paymentMethod)
              <div class="ps-pay-row ps-pay-row--method"><span>Método de pago</span><span>{{ ucfirst($booking->paymentMethod) }}</span></div>
            @endif
          </div>

          <div class="ps-status-block {{ $isFree ? 'ps-status-block--free' : ($isPending ? 'ps-status-block--pending' : 'ps-status-block--paid') }}">
            @if($isPending)
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              Pendiente de confirmación
            @elseif($isFree)
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              Reserva confirmada · Gratis
            @else
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              Pago acreditado
            @endif
          </div>

          {{-- Guest QR orientation --}}
          @if($booking->access_token)
          <div class="cd-guest-info" style="border-radius:0;border-left:none;border-right:none;border-bottom:none;">
            <div class="cd-guest-info__icon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2-2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            </div>
            <div class="cd-guest-info__body">
              <p class="cd-guest-info__title">Revisá tu correo — tus entradas están en camino</p>
              <ul class="cd-guest-info__list">
                <li>
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                  Los <strong>códigos QR</strong> se envían por email por seguridad.
                </li>
                <li>
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                  Revisá tu carpeta de <strong>spam</strong> si no lo ves pronto.
                </li>
                <li>
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                  Guardá tu reserva: <strong class="cd-guest-info__id">#{{ $booking->booking_id }}</strong>
                </li>
              </ul>
              <div class="cd-guest-info__cta">
                <a href="{{ route('customer.signup') }}" class="cd-guest-info__link">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                  Crear cuenta para gestionar tus reservas futuras
                </a>
              </div>
            </div>
          </div>
          @endif

          <div class="ps-actions">
            @if($hasPdf)
              <a href="{{ asset('assets/admin/file/invoices/' . $booking->invoice) }}" download class="ps-btn ps-btn--primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Descargar comprobante de reserva
              </a>
              <p class="mt-2 mb-0" style="font-size: 0.875rem; color: #6c757d;">
                <small>Este comprobante es interno y no reemplaza una factura fiscal. La factura fiscal, si corresponde, se emitirá por separado.</small>
              </p>
            @endif
            <a href="{{ $booking->access_token ? route('booking.guest_view', $booking->id) . '?token=' . $booking->access_token : route('customer.booking_details', $booking->id) }}" class="ps-btn ps-btn--secondary">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              Ver detalle de reserva
            </a>

            @if($calStartDate)
              <div class="ps-cal-group">
                <p class="ps-cal-group__label">Agregar al calendario</p>
                <div class="ps-cal-btns">
                  <a target="_blank" class="ps-cal-btn"
                     href="//calendar.google.com/calendar/u/0/r/eventedit?text={{ urlencode($eventTitle) }}&dates={{ $calStartDate }}T{{ $calSTime }}/{{ $calEndDate }}T{{ $calETime }}&ctz={{ $websiteInfo->timezone }}&details={{ urlencode('Ver evento: ' . route('event.details', [$eventSlug, $event->id])) }}&location={{ urlencode($eventAddress) }}&sf=true">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Google
                  </a>
                  <a target="_blank" class="ps-cal-btn"
                     href="//calendar.yahoo.com/?v=60&view=d&type=20&TITLE={{ urlencode($eventTitle) }}&ST={{ $calStartDate }}T{{ $calStartTime }}&ET={{ $calEndDate }}T{{ $calEndTime }}&DESC={{ urlencode(route('event.details', [$eventSlug, $event->id])) }}&in_loc={{ urlencode($eventAddress) }}">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12V22H4V12"/><path d="M22 7H2v5h20V7z"/><path d="M12 22V7"/></svg>
                    Yahoo
                  </a>
                </div>
              </div>
            @endif
          </div>
        </div>

        {{-- CTA --}}
        <div class="ps-cta-card">
          <p class="ps-cta-card__text">¿Buscás más experiencias?</p>
          <a href="{{ route('events') }}" class="ps-btn ps-btn--ghost">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            Explorar más eventos
          </a>
          <a href="{{ route('customer.dashboard') }}" class="ps-btn ps-btn--ghost" style="margin-top:8px">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            Ir a mi panel
          </a>
        </div>

      </div>
    </div>
  </div>
</section>
@endsection
