@extends('frontend.layout')
@section('pageHeading', 'Detalle de reserva')
@section('hero-section')
  <section class="page-banner overlay pt-120 pb-125 rpt-90 rpb-95 lazy"
    data-bg="{{ asset('assets/admin/img/' . $basicInfo->breadcrumb) }}">
    <div class="container">
      <div class="banner-inner">
        <h2 class="page-title">Detalle de reserva</h2>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            @auth('customer')
            <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customer.booking.my_booking') }}">Mis entradas</a></li>
            @endauth
            <li class="breadcrumb-item active">#{{ $booking->booking_id }}</li>
          </ol>
        </nav>
      </div>
    </div>
  </section>
@endsection

@section('content')
@php
  $position = $booking->currencyTextPosition;
  $currency = $booking->currencyText;

  $event = $booking->event()->where('language_id', $currentLanguageInfo->id)->select('title','slug','event_id')->first();
  if (!$event) {
    $defLang = App\Models\Language::where('is_default', 1)->first();
    $event = $booking->event()->where('language_id', $defLang->id)->select('title','slug','event_id')->first();
  }

  $statusMap = [
    'completed' => ['label' => 'Completado', 'class' => 'cd-status--paid'],
    'paid'      => ['label' => 'Pagado',      'class' => 'cd-status--paid'],
    'free'      => ['label' => 'Gratis',      'class' => 'cd-status--free'],
    'pending'   => ['label' => 'Pendiente',   'class' => 'cd-status--pending'],
  ];
  $st = $statusMap[$booking->paymentStatus] ?? ['label' => ucfirst($booking->paymentStatus), 'class' => 'cd-status--paid'];

  $exts = $booking->invoice ? pathinfo($booking->invoice, PATHINFO_EXTENSION) : null;
  $hasPdf = $exts === 'pdf';
@endphp

<section class="cd-page py-60">
  <div class="container">
    <div class="row g-4">

      @auth('customer')
      @includeIf('frontend.customer.partials.sidebar')
      @endauth

      <div class="{{ Auth::guard('customer')->check() ? 'col-lg-9' : 'col-lg-12' }}">

        {{-- HERO: ticket stub --}}
        @php $isPending = $booking->paymentStatus === 'pending'; @endphp
        <div class="cd-bk-hero {{ $isPending ? 'cd-bk-hero--pending' : 'cd-bk-hero--confirmed' }} mb-4">

          {{-- TOP: dark slate --}}
          <div class="cd-bk-hero__top">
            @auth('customer')
            <div class="cd-bk-hero__nav">
              <a href="{{ route('customer.booking.my_booking') }}" class="cd-bk-hero__back">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Mis entradas
              </a>
            </div>
            @endauth
            <div class="cd-bk-hero__confirm-row">
              <div class="cd-bk-hero__check-wrap">
                <div class="cd-bk-hero__check-icon">
                  @if($isPending)
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                  @else
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                  @endif
                </div>
              </div>
              <div class="cd-bk-hero__confirm-text">
                <p class="cd-bk-hero__label">{{ $isPending ? 'Pago en proceso' : '¡Reserva confirmada!' }}</p>
                @if($event)
                  <h2 class="cd-bk-hero__event">{{ $event->title }}</h2>
                @endif
              </div>
            </div>
          </div>

          {{-- TEAR LINE --}}
          <div class="cd-bk-hero__tear">
            <div class="cd-bk-hero__tear-dash"></div>
          </div>

          {{-- BOTTOM: white --}}
          <div class="cd-bk-hero__bottom">
            <div class="cd-bk-hero__bottom-row">
              <div class="cd-bk-hero__price-block">
                <span class="cd-bk-hero__price-label">Total pagado</span>
                <span class="cd-bk-hero__price-amount">{{ $booking->currencySymbol }}{{ number_format($booking->price + $booking->tax, 2) }}</span>
              </div>
              @if($hasPdf && empty($isGuest))
                <a href="{{ asset('assets/admin/file/invoices/' . $booking->invoice) }}" download class="cd-bk-hero__pdf-btn">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                  Descargar PDF
                </a>
              @endif
            </div>
            <div class="cd-bk-hero__chips">
              <span class="cd-bk-hero__chip cd-bk-hero__chip--id">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 3H8l-2 4h12l-2-4z"/></svg>
                #{{ $booking->booking_id }}
              </span>
              <span class="cd-bk-hero__chip">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                {{ \Carbon\Carbon::parse($booking->event_date)->translatedFormat('D j M Y · H:i') }}hs
              </span>
              <span class="cd-bk-hero__chip">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 9a3 3 0 010-6h20a3 3 0 010 6"/><path d="M2 15a3 3 0 000 6h20a3 3 0 000-6"/></svg>
                {{ $booking->quantity }} {{ $booking->quantity == 1 ? 'entrada' : 'entradas' }}
              </span>
            </div>
          </div>

        </div>

        {{-- Guest orientation --}}
        @if(!empty($isGuest))
        <div class="cd-guest-info mb-4">
          <div class="cd-guest-info__icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2-2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          </div>
          <div class="cd-guest-info__body">
            <p class="cd-guest-info__title">Tus entradas llegan por email</p>
            <ul class="cd-guest-info__list">
              <li>
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Tu reserva está confirmada. Los <strong>códigos QR</strong> llegarán a tu correo en los próximos minutos.
              </li>
              <li>
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Revisá tu bandeja de <strong>spam</strong> si no lo ves pronto.
              </li>
              <li>
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Guardá tu número de reserva: <strong class="cd-guest-info__id">#{{ $booking->booking_id }}</strong>
              </li>
            </ul>
            <div class="cd-guest-info__cta">
              <a href="{{ route('customer.signup') }}" class="cd-guest-info__link">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                Crear cuenta en Tukipass para gestionar tus reservas
              </a>
            </div>
          </div>
        </div>
        @endif

        {{-- Info grid: Billing + Payment + Organizer --}}
        <div class="cd-bk-grid mb-4">

          {{-- Datos de facturación --}}
          <div class="cd-card">
            <div class="cd-card__head">
              <div class="cd-card__head-icon cd-card__head-icon--blue">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              </div>
              <h3 class="cd-card__title">Datos de facturación</h3>
            </div>
            <div class="cd-info-list">
              @php
                $fiscalProfile = $booking->fiscalProfile;
                $dni = $fiscalProfile && $fiscalProfile->document_number
                    ? trim(($fiscalProfile->document_type ?? '') . ' ' . $fiscalProfile->document_number)
                    : null;
                $billingFields = [
                  'Nombre'        => trim($booking->fname . ' ' . $booking->lname),
                  'Email'         => $booking->email,
                  'Documento'     => $dni,
                  'Teléfono'      => $booking->phone,
                  'País'          => $booking->country,
                  'Provincia'     => $booking->state,
                  'Ciudad'        => $booking->city,
                  'Código postal' => $booking->zip_code,
                  'Dirección'     => $booking->address,
                ];
              @endphp
              @foreach($billingFields as $label => $value)
                @if($value && $value !== 'N/A' && $value !== 'n/a')
                  <div class="cd-info-row">
                    <span class="cd-info-row__label">{{ $label }}</span>
                    <span class="cd-info-row__val">{{ $value }}</span>
                  </div>
                @endif
              @endforeach
            </div>
          </div>

          {{-- Información de pago --}}
          <div class="cd-card">
            <div class="cd-card__head">
              <div class="cd-card__head-icon cd-card__head-icon--green">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
              </div>
              <h3 class="cd-card__title">Información de pago</h3>
            </div>
            <div class="cd-payment-hero">
              <span class="cd-payment-hero__label">Total pagado</span>
              <span class="cd-payment-hero__amount" dir="ltr">{{ $booking->currencySymbol }}{{ number_format($booking->price + $booking->tax, 2) }}</span>
              <span class="cd-status {{ $st['class'] }}">{{ $st['label'] }}</span>
            </div>
            <div class="cd-info-list">
              @if($booking->early_bird_discount)
                <div class="cd-info-row">
                  <span class="cd-info-row__label">Descuento Early Bird</span>
                  <span class="cd-info-row__val cd-info-row__val--discount">− {{ $booking->currencySymbol }}{{ $booking->early_bird_discount }}</span>
                </div>
              @endif
              @if($booking->discount)
                <div class="cd-info-row">
                  <span class="cd-info-row__label">Descuento cupón</span>
                  <span class="cd-info-row__val cd-info-row__val--discount">− {{ $booking->currencySymbol }}{{ $booking->discount }}</span>
                </div>
              @endif
              @if(!is_null($booking->tax))
                <div class="cd-info-row">
                  <span class="cd-info-row__label">Impuestos</span>
                  <span class="cd-info-row__val" dir="ltr">{{ $position == 'left' ? $currency . ' ' : '' }}{{ $booking->tax }}{{ $position == 'right' ? ' ' . $currency : '' }}</span>
                </div>
              @endif
              @if($booking->paymentMethod)
                <div class="cd-info-row">
                  <span class="cd-info-row__label">Método de pago</span>
                  <span class="cd-info-row__val">{{ ucfirst($booking->paymentMethod) }}</span>
                </div>
              @endif
              @if(is_null($booking->variation))
                <div class="cd-info-row">
                  <span class="cd-info-row__label">Cantidad</span>
                  <span class="cd-info-row__val">{{ $booking->quantity }}</span>
                </div>
              @endif
            </div>
          </div>

          {{-- Organizador --}}
          @if($booking->organizer)
            <div class="cd-card">
              <div class="cd-card__head">
                <div class="cd-card__head-icon cd-card__head-icon--purple">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                </div>
                <h3 class="cd-card__title">Organizador</h3>
              </div>
              <div class="cd-info-list">
                <div class="cd-info-row">
                  <span class="cd-info-row__label">Nombre</span>
                  <span class="cd-info-row__val">
                    <a href="{{ route('frontend.organizer.details', [$booking->organizer->id, str_replace(' ', '-', $booking->organizer->username)]) }}" target="_blank" class="cd-table__link">
                      {{ $booking->organizer->username }}
                    </a>
                  </span>
                </div>
                @if($booking->organizer->email)
                  <div class="cd-info-row"><span class="cd-info-row__label">Email</span><span class="cd-info-row__val">{{ $booking->organizer->email }}</span></div>
                @endif
                @if($booking->organizer->phone)
                  <div class="cd-info-row"><span class="cd-info-row__label">Teléfono</span><span class="cd-info-row__val">{{ $booking->organizer->phone }}</span></div>
                @endif
                @if(@$booking->organizer->organizer_info->city)
                  <div class="cd-info-row"><span class="cd-info-row__label">Ciudad</span><span class="cd-info-row__val">{{ $booking->organizer->organizer_info->city }}</span></div>
                @endif
                @if(@$booking->organizer->organizer_info->country)
                  <div class="cd-info-row"><span class="cd-info-row__label">País</span><span class="cd-info-row__val">{{ $booking->organizer->organizer_info->country }}</span></div>
                @endif
              </div>
            </div>
          @endif

        </div>

        {{-- Tickets con variaciones --}}
        @if($booking->variation != null)
          @php $variations = json_decode($booking->variation, true); @endphp
          <div class="cd-card">
            <div class="cd-card__head">
              <div class="cd-card__head-icon cd-card__head-icon--blue">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 9a3 3 0 010-6h20a3 3 0 010 6"/><path d="M2 15a3 3 0 000 6h20a3 3 0 000-6"/><path d="M12 3v18M8 3v18M16 3v18" opacity=".4"/></svg>
              </div>
              <h3 class="cd-card__title">Entradas reservadas</h3>
              <span class="cd-count-pill">{{ collect($variations)->sum('qty') }} {{ collect($variations)->sum('qty') == 1 ? 'entrada' : 'entradas' }}</span>
            </div>
            <div class="cd-table-wrap">
              <table class="cd-table">
                <thead>
                  <tr>
                    <th>Tipo de entrada</th>
                    <th>Cantidad</th>
                    <th>Precio unitario</th>
                    <th>Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($variations as $variation)
                    @php
                      $ticket = App\Models\Event\Ticket::find($variation['ticket_id']);
                      $ticketContent = $ticket
                        ? App\Models\Event\TicketContent::where([['ticket_id', $ticket->id], ['language_id', $currentLanguageInfo->id]])->first()
                        : null;
                      $evd = $variation['early_bird_dicount'] / max($variation['qty'], 1);
                      $unitPrice = $variation['price'] / max($variation['qty'], 1);
                      $finalUnit = $unitPrice - $evd;
                    @endphp
                    <tr>
                      <td>
                        <span class="cd-table__link" style="cursor:default">
                          {{ $ticketContent ? $ticketContent->title : '—' }}
                          @if($ticket && $ticket->pricing_type == 'variation')
                            @php
                              $vKey = App\Models\Event\VariationContent::where([['ticket_id', $ticket->id], ['name', $variation['name']]])->select('key')->first();
                              $vName = $vKey ? App\Models\Event\VariationContent::where([['ticket_id', $ticket->id], ['language_id', $currentLanguageInfo->id], ['key', $vKey->key]])->first() : null;
                            @endphp
                            @if($vName)<small class="text-muted"> · {{ $vName->name }}</small>@endif
                          @endif
                        </span>
                      </td>
                      <td>{{ $variation['qty'] }}</td>
                      <td>
                        {{ symbolPrice($finalUnit) }}
                        @if($variation['early_bird_dicount'] > 0)
                          <del class="text-muted ms-1" style="font-size:12px">{{ symbolPrice($unitPrice) }}</del>
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

      </div>
    </div>
  </div>
</section>
@endsection
