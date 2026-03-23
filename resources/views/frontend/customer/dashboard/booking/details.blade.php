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
            <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customer.booking.my_booking') }}">Mis entradas</a></li>
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

      @includeIf('frontend.customer.partials.sidebar')

      <div class="col-lg-9">

        {{-- Header card --}}
        <div class="cd-card mb-4">
          <div class="cd-bk-header">
            <div class="cd-bk-header__left">
              <div class="cd-bk-header__id">#{{ $booking->booking_id }}</div>
              <span class="cd-status {{ $st['class'] }}">{{ $st['label'] }}</span>
            </div>
            <div class="cd-bk-header__right">
              @if($hasPdf)
                <a href="{{ asset('assets/admin/file/invoices/' . $booking->invoice) }}"
                   download class="cd-bk-dl-btn">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                  Descargar PDF
                </a>
              @endif
              <a href="{{ route('customer.booking.my_booking') }}" class="cd-bk-back-btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Mis entradas
              </a>
            </div>
          </div>

          <div class="cd-bk-meta">
            <div class="cd-bk-meta__item">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              <span><strong>Reservado el:</strong> {{ \Carbon\Carbon::parse($booking->created_at)->translatedFormat('D d/m/Y · H:i') }}hs</span>
            </div>
            <div class="cd-bk-meta__item">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              <span><strong>Fecha del evento:</strong> {{ \Carbon\Carbon::parse($booking->event_date)->translatedFormat('D d/m/Y · H:i') }}hs</span>
            </div>
            @if($event)
              <div class="cd-bk-meta__item">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V7z"/><polyline points="14 2 14 8 20 8"/></svg>
                <span><strong>Evento:</strong>
                  <a href="{{ route('event.details', ['slug' => $event->slug, 'id' => $event->event_id]) }}" target="_blank" class="cd-table__link">
                    {{ Str::limit($event->title, 60) }}
                  </a>
                </span>
              </div>
            @endif
          </div>
        </div>

        {{-- Info grid: Billing + Payment + Organizer --}}
        <div class="cd-bk-grid mb-4">

          {{-- Datos de facturación --}}
          <div class="cd-card">
            <div class="cd-card__head">
              <h3 class="cd-card__title">Datos de facturación</h3>
            </div>
            <div class="cd-info-list">
              @if($booking->fname || $booking->lname)
                <div class="cd-info-row"><span class="cd-info-row__label">Nombre</span><span class="cd-info-row__val">{{ trim($booking->fname . ' ' . $booking->lname) }}</span></div>
              @endif
              @if($booking->email)
                <div class="cd-info-row"><span class="cd-info-row__label">Email</span><span class="cd-info-row__val">{{ $booking->email }}</span></div>
              @endif
              @if($booking->phone)
                <div class="cd-info-row"><span class="cd-info-row__label">Teléfono</span><span class="cd-info-row__val">{{ $booking->phone }}</span></div>
              @endif
              @if($booking->country)
                <div class="cd-info-row"><span class="cd-info-row__label">País</span><span class="cd-info-row__val">{{ $booking->country }}</span></div>
              @endif
              @if($booking->state)
                <div class="cd-info-row"><span class="cd-info-row__label">Provincia</span><span class="cd-info-row__val">{{ $booking->state }}</span></div>
              @endif
              @if($booking->city)
                <div class="cd-info-row"><span class="cd-info-row__label">Ciudad</span><span class="cd-info-row__val">{{ $booking->city }}</span></div>
              @endif
              @if($booking->zip_code)
                <div class="cd-info-row"><span class="cd-info-row__label">Código postal</span><span class="cd-info-row__val">{{ $booking->zip_code }}</span></div>
              @endif
              @if($booking->address)
                <div class="cd-info-row"><span class="cd-info-row__label">Dirección</span><span class="cd-info-row__val">{{ $booking->address }}</span></div>
              @endif
            </div>
          </div>

          {{-- Información de pago --}}
          <div class="cd-card">
            <div class="cd-card__head">
              <h3 class="cd-card__title">Información de pago</h3>
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
              <div class="cd-info-row cd-info-row--total">
                <span class="cd-info-row__label">Total pagado</span>
                <span class="cd-info-row__val" dir="ltr">{{ $booking->currencySymbol }}{{ $booking->price + $booking->tax }}</span>
              </div>
              <div class="cd-info-row">
                <span class="cd-info-row__label">Estado</span>
                <span class="cd-status {{ $st['class'] }}">{{ $st['label'] }}</span>
              </div>
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
