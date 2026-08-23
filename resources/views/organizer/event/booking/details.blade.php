@extends('organizer.layout')

@section('style')
  <style>
    .organizer-booking-detail {
      max-width: 100%;
      overflow-x: hidden;
      color: var(--text-primary);
    }

    /* Dominio: cabecera de detalle de reserva */
    .bod-hero {
      display: grid;
      gap: 14px;
      padding: 16px;
      margin-bottom: 16px;
      border: 1px solid var(--border-default);
      border-radius: 8px;
      background: var(--surface-card);
      box-shadow: 0 6px 18px rgba(30, 37, 50, .04);
    }

    .bod-eyebrow {
      margin-bottom: 5px;
      color: var(--text-muted);
      font-size: 12px;
      font-weight: 600;
      letter-spacing: 0;
      text-transform: uppercase;
    }

    .bod-title {
      margin: 0;
      color: var(--text-primary);
      font-size: 21px;
      font-weight: 700;
      line-height: 1.25;
      overflow-wrap: anywhere;
    }

    .bod-id {
      margin-top: 7px;
      color: var(--text-muted);
      font-size: 13px;
      font-weight: 500;
      overflow-wrap: anywhere;
    }

    .bod-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
    }

    /* Dominio: layout de columnas y apilado */
    .bod-layout,
    .bod-stack {
      display: grid;
      gap: 16px;
    }

    /* Dominio: grilla de info de evento */
    .bod-info-grid {
      display: grid;
      gap: 0;
    }

    .bod-info-item {
      display: grid;
      gap: 4px;
      min-width: 0;
      padding: 10px 0;
      border-bottom: 1px solid var(--border-subtle);
    }

    .bod-info-item:last-child {
      border-bottom: 0;
      padding-bottom: 0;
    }

    /* Dominio: libro de pago y liquidación */
    .bod-ledger {
      display: grid;
      gap: 0;
    }

    .bod-ledger-row {
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto;
      align-items: center;
      column-gap: 12px;
      gap: 4px;
      min-width: 0;
      padding: 10px 0;
      border-bottom: 1px solid var(--border-subtle);
    }

    .bod-ledger-row:last-child {
      border-bottom: 0;
      padding-bottom: 0;
    }

    .bod-ledger-row--highlight {
      margin-top: 6px;
      padding: 12px;
      border: 1px solid rgba(249, 115, 22, 0.35);
      border-radius: 7px;
      background: var(--status-warning-bg);
    }

    /* Dominio: estado de pago */
    .bod-status {
      display: inline-flex;
      align-items: center;
      min-height: 28px;
      padding: 6px 10px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 700;
    }

    .bod-status i {
      margin-right: 6px;
    }

    /* Dominio: anchos de columnas de tablas */
    .bod-col-ticket {
      width: 36%;
    }

    .bod-col-small {
      width: 11%;
    }

    .bod-col-money {
      width: 16%;
    }

    .bod-col-scan {
      width: 21%;
    }

    /* Dominio: nombre de entrada en tabla */
    .bod-ticket-name {
      display: block;
      color: var(--text-primary);
      font-weight: 700;
      overflow-wrap: anywhere;
    }

    /* Dominio: thumb de la card mobile de entrada */
    .bod-ticket-thumb {
      width: 54px;
      height: 54px;
      display: grid;
      place-items: center;
      overflow: hidden;
      border-radius: var(--adm-radius-lg);
      background: var(--surface-hover);
      color: var(--adm-primary-dark);
    }

    html[data-theme="dark"] .organizer-booking-detail .bod-ticket-thumb {
      color: var(--adm-primary);
    }

    .bod-ticket-thumb i {
      font-size: 18px;
    }

    /* Dominio: stat de la card mobile de entrada */
    .bod-ticket-mobile-stat {
      display: grid;
      align-content: start;
      gap: 3px;
      min-width: 0;
    }

    @media (min-width: 768px) {
      .bod-hero {
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: start;
        padding: 20px;
      }

      .bod-actions {
        justify-content: flex-end;
      }

      .bod-info-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        column-gap: 18px;
      }
    }

    @media (min-width: 1200px) {
      .bod-layout {
        grid-template-columns: minmax(0, 1.42fr) minmax(300px, .58fr);
        align-items: start;
      }
    }

    @media (max-width: 767px) {
      .bod-title {
        font-size: 19px;
      }

      .bod-panel__body--tickets {
        padding: 12px;
      }

      .oc-panel__header {
        align-items: flex-start;
        flex-direction: column;
      }

      .bod-ledger-row {
        grid-template-columns: 1fr;
      }

      .bod-table--tickets {
        display: none;
      }

      .oc-mobile-list {
        display: grid;
      }
    }

    @media (min-width: 768px) {
      .oc-mobile-list {
        display: none;
      }
    }

    @media (max-width: 360px) {
      .oc-mobile-card__head,
      .oc-mobile-card__grid {
        grid-template-columns: 1fr;
      }

      .oc-mobile-card__badges {
        justify-items: start;
      }

      .oc-mobile-card__grid {
        row-gap: 10px;
      }
    }
  </style>
@endsection

@section('content')
  @php
    $language = $defaultLang ?? \App\Models\Language::where('code', 'es')->first();
    $eventInfo = \App\Models\Event\EventContent::where('language_id', optional($language)->id)
        ->where('event_id', $booking->event_id)
        ->select('slug', 'event_id', 'title')
        ->first();

    if (empty($eventInfo)) {
        $eventInfo = \App\Models\Event\EventContent::where('event_id', $booking->event_id)
            ->select('slug', 'event_id', 'title')
            ->first();
    }

    $eventModel = $booking->evnt;
    $customer = $booking->customerInfo;
    $ticketBreakdown = $booking->ticketBreakdown();
    $addonBreakdown = $booking->addonBreakdown();
    $addonsCount = collect($addonBreakdown)->sum('quantity');
    $addonsTotal = collect($addonBreakdown)->sum('subtotal');
    $scannedCount = $booking->scannedTicketsCount();
    $pendingScanCount = $booking->pendingTicketsCount();
    $scanPercent = $booking->scanPercent();
    $invoiceExtension = pathinfo((string) $booking->invoice, PATHINFO_EXTENSION);
    $hasInvoiceFile = $invoiceExtension == 'pdf' && $booking->hasInvoiceFile();
    $currencyPosition = $booking->currencySymbolPosition ?: $booking->currencyTextPosition ?: 'left';
    $currency = $booking->currencySymbol ?: $booking->currencyText ?: '$';
    $formatMoney = function ($amount) use ($currencyPosition, $currency) {
        $amount = number_format((float) $amount, 0, ',', '.');
        return ($currencyPosition == 'left' ? $currency : '') . $amount . ($currencyPosition == 'right' ? $currency : '');
    };
    $paidTotal = (float) ($booking->price ?? 0) + (float) ($booking->tax ?? 0);
    $organizerTotal = (float) ($booking->price ?? 0) - (float) ($booking->commission ?? 0);
    $eventStartLabel = $booking->event_date ? \Carbon\Carbon::parse($booking->event_date)->format('d/m/Y H:i') : '-';
    $eventEndLabel = '-';
    $eventDuration = '-';

    if ($eventModel && $eventModel->date_type == 'single') {
        $eventEndLabel = $eventModel->end_date ? \Carbon\Carbon::parse($eventModel->end_date . ' ' . $eventModel->end_time)->format('d/m/Y H:i') : '-';
        $eventDuration = $eventModel->duration ?: '-';
    } elseif ($eventModel && $booking->event_date) {
        $date = \Carbon\Carbon::parse($booking->event_date)->format('Y-m-d');
        $time = \Carbon\Carbon::parse($booking->event_date)->format('H:i');
        $eventDate = $eventModel->dates()->where('start_date', $date)->where('start_time', $time)->first();

        if ($eventDate) {
            $eventEndLabel = $eventDate->end_date ? \Carbon\Carbon::parse($eventDate->end_date . ' ' . $eventDate->end_time)->format('d/m/Y H:i') : '-';
            $eventDuration = $eventDate->duration ?: '-';
        }
    }

    $statusOptions = [
        'completed' => ['label' => __('Completado'), 'class' => 'success', 'icon' => 'fa-check-circle'],
        'pending' => ['label' => __('Pendiente'), 'class' => 'warning text-dark', 'icon' => 'fa-clock'],
        'rejected' => ['label' => __('Rechazado'), 'class' => 'danger', 'icon' => 'fa-times-circle'],
        'free' => ['label' => __('Gratis'), 'class' => 'primary', 'icon' => 'fa-gift'],
    ];
    $status = $statusOptions[$booking->paymentStatus] ?? ['label' => ucfirst((string) $booking->paymentStatus), 'class' => 'secondary', 'icon' => 'fa-circle'];
    $customerName = trim(($booking->fname ?? '') . ' ' . ($booking->lname ?? ''));
    $accountName = $customer ? trim(($customer->fname ?? '') . ' ' . ($customer->lname ?? '')) : null;
    $location = collect([$booking->address, $booking->city, $booking->state, $booking->country])->filter()->implode(', ');
  @endphp

  <div class="organizer-booking-detail">
    <div class="page-header">
      <h1 class="page-title">{{ __('Detalle de reserva') }}</h1>
      <ul class="breadcrumbs">
        <li class="nav-home">
          <a href="{{ route('organizer.dashboard') }}" aria-label="{{ __('Ir al panel') }}">
            <i class="flaticon-home"></i>
          </a>
        </li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="{{ route('organizer.event.booking') }}">{{ __('Reservas') }}</a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="#">{{ __('Detalle') }}</a></li>
      </ul>
    </div>

    <section class="bod-hero" aria-labelledby="booking-detail-title">
      <div>
        <div class="bod-eyebrow">{{ __('Reserva') }} #{{ $booking->id }}</div>
        <h2 id="booking-detail-title" class="bod-title">
          {{ $eventInfo ? $eventInfo->title : __('Evento no disponible') }}
        </h2>
        <div class="bod-id tuki-data tuki-data-id">#{{ $booking->booking_id }}</div>
      </div>

      <div class="bod-actions">
        <a class="btn btn-light oc-btn" href="{{ route('organizer.event.booking') }}">
          <i class="fas fa-arrow-left" aria-hidden="true"></i>{{ __('Volver') }}
        </a>
        @if ($eventInfo)
          <a class="btn btn-outline-primary oc-btn" href="{{ route('event.details', ['slug' => $eventInfo->slug, 'id' => $eventInfo->event_id]) }}"
            target="_blank" rel="noopener">
            <i class="fas fa-external-link-alt" aria-hidden="true"></i>{{ __('Ver evento') }}
          </a>
        @endif
        @if ($hasInvoiceFile)
          <a class="btn btn-outline-secondary oc-btn" href="{{ route('booking.ticket.download', $booking->id) }}"
            target="_blank" rel="noopener">
            <i class="fas fa-file-pdf" aria-hidden="true"></i>{{ __('Entrada PDF') }}
          </a>
        @endif
        @if (!is_null($booking->attachmentFile))
          <button class="btn btn-outline-info oc-btn" type="button" data-toggle="modal" data-target="#attachmentModal-{{ $booking->id }}">
            <i class="fas fa-paperclip" aria-hidden="true"></i>{{ __('Comprobante') }}
          </button>
        @endif
      </div>
    </section>

    <section class="oc-summary" aria-label="{{ __('Resumen de la reserva') }}">
      <div class="oc-metric">
        <div class="oc-metric__label">{{ __('Estado') }}</div>
        <span class="badge badge-{{ $status['class'] }} bod-status">
          <i class="fas {{ $status['icon'] }}" aria-hidden="true"></i>{{ $status['label'] }}
        </span>
        <div class="oc-muted">{{ $booking->paymentMethod ?: __('Sin método informado') }}</div>
      </div>
      <div class="oc-metric">
        <div class="oc-metric__label">{{ __('Total cobrado') }}</div>
        <div class="oc-metric__value tuki-data tuki-data-money">{{ $formatMoney($paidTotal) }}</div>
        <div class="oc-muted">{{ __('Base entradas') }}: <span class="tuki-data tuki-data-money">{{ $formatMoney($booking->price ?? 0) }}</span></div>
      </div>
      <div class="oc-metric">
        <div class="oc-metric__label">{{ __('Recibís') }}</div>
        <div class="oc-metric__value tuki-data tuki-data-money">{{ $formatMoney($organizerTotal) }}</div>
        <div class="oc-muted">{{ __('Comisión plataforma') }}: <span class="tuki-data tuki-data-money">{{ $formatMoney($booking->commission ?? 0) }}</span></div>
      </div>
      <div class="oc-metric">
        <div class="oc-metric__label">{{ __('Escaneo') }}</div>
        <div class="oc-metric__value tuki-data tuki-data-count">{{ $scannedCount }}/{{ (int) $booking->quantity }}</div>
        <div class="oc-progress" aria-hidden="true"><span style="width: {{ $scanPercent }}%"></span></div>
        <div class="oc-muted">{{ __('Faltan') }}: <span class="tuki-data tuki-data-count">{{ $pendingScanCount }}</span></div>
      </div>
    </section>

    <div class="bod-layout">
      <div class="bod-stack">
        <section class="oc-panel" aria-labelledby="bod-event-title">
          <div class="oc-panel__header">
            <h3 id="bod-event-title" class="oc-panel__title">{{ __('Evento y función') }}</h3>
          </div>
          <div class="oc-panel__body">
            <div class="bod-info-grid">
              <div class="bod-info-item">
                <span class="oc-data-label">{{ __('Evento') }}</span>
                <span class="oc-data-value">{{ $eventInfo ? $eventInfo->title : '-' }}</span>
              </div>
              <div class="bod-info-item">
                <span class="oc-data-label">{{ __('Fecha de reserva') }}</span>
                <span class="oc-data-value">{{ FullDateTime($booking->created_at) }}</span>
              </div>
              <div class="bod-info-item">
                <span class="oc-data-label">{{ __('Función') }}</span>
                <span class="oc-data-value">{{ $eventStartLabel }}</span>
              </div>
              <div class="bod-info-item">
                <span class="oc-data-label">{{ __('Fin / duración') }}</span>
                <span class="oc-data-value">{{ $eventEndLabel }} <span class="oc-muted">{{ $eventDuration != '-' ? '- ' . $eventDuration : '' }}</span></span>
              </div>
            </div>
          </div>
        </section>

        <section class="oc-panel" aria-labelledby="bod-tickets-title">
          <div class="oc-panel__header">
            <h3 id="bod-tickets-title" class="oc-panel__title">{{ __('Info de entradas') }}</h3>
            <span class="oc-pill">{{ (int) $booking->quantity }} {{ (int) $booking->quantity == 1 ? __('entrada') : __('entradas') }}</span>
          </div>
          <div class="oc-panel__body bod-panel__body--tickets">
            <table class="table oc-table bod-table--tickets">
              <colgroup>
                <col class="bod-col-ticket">
                <col class="bod-col-small">
                <col class="bod-col-money">
                <col class="bod-col-money">
                <col class="bod-col-scan">
              </colgroup>
              <thead>
                <tr>
                  <th scope="col">{{ __('Entrada') }}</th>
                  <th scope="col">{{ __('Cant.') }}</th>
                  <th scope="col">{{ __('Precio unit.') }}</th>
                  <th scope="col">{{ __('Subtotal') }}</th>
                  <th scope="col">{{ __('Escaneo') }}</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($ticketBreakdown as $ticketInfo)
                  <tr>
                    <td data-label="{{ __('Entrada') }}">
                      <span class="bod-ticket-name">{{ $ticketInfo['name'] }}</span>
                      @if ($ticketInfo['discount'] > 0)
                        <span class="oc-muted">{{ __('Descuento') }}: {{ $formatMoney($ticketInfo['discount']) }}</span>
                      @endif
                    </td>
                    <td data-label="{{ __('Cant.') }}"><span class="oc-pill tuki-data tuki-data-count">{{ $ticketInfo['quantity'] }}</span></td>
                    <td data-label="{{ __('Precio unit.') }}">
                      <span class="oc-money tuki-data tuki-data-money">{{ $formatMoney($ticketInfo['unit_final']) }}</span>
                      @if ($ticketInfo['unit_discount'] > 0)
                        <del class="oc-muted tuki-data tuki-data-money">{{ $formatMoney($ticketInfo['unit_price']) }}</del>
                      @endif
                    </td>
                    <td data-label="{{ __('Subtotal') }}"><span class="oc-money tuki-data tuki-data-money">{{ $formatMoney($ticketInfo['subtotal']) }}</span></td>
                    <td data-label="{{ __('Escaneo') }}">
                      <strong class="oc-data-value tuki-data tuki-data-count">{{ $ticketInfo['scanned'] }}/{{ $ticketInfo['quantity'] }}</strong>
                      <span class="oc-muted">{{ __('Faltan') }}: <span class="tuki-data tuki-data-count">{{ $ticketInfo['pending'] }}</span></span>
                      <div class="oc-progress" aria-hidden="true"><span style="width: {{ $ticketInfo['scan_percent'] }}%"></span></div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>

            <div class="oc-mobile-list" role="list" aria-label="{{ __('Entradas de la reserva') }}">
              @foreach ($ticketBreakdown as $ticketInfo)
                @php
                  $ticketName = trim((string) $ticketInfo['name']);
                  $ticketDisplayName = trim((string) preg_replace('/^\s*Entrada\s+/iu', '', $ticketName));
                  $ticketDisplayName = $ticketDisplayName !== '' ? Str::ucfirst($ticketDisplayName) : ($ticketName ?: __('Entrada'));
                  $ticketQuantity = (int) ($ticketInfo['quantity'] ?? 0);
                  $ticketScanned = (int) ($ticketInfo['scanned'] ?? 0);
                  $ticketPending = (int) ($ticketInfo['pending'] ?? max(0, $ticketQuantity - $ticketScanned));
                  $ticketIsComplete = $ticketQuantity > 0 && $ticketPending <= 0;
                @endphp
                <article class="oc-mobile-card" role="listitem" aria-labelledby="bookingTicketTitle{{ $loop->index }}">
                  <div class="oc-mobile-card__head">
                    <div class="bod-ticket-thumb" aria-hidden="true">
                      <i class="fas fa-ticket-alt"></i>
                    </div>
                    <div class="oc-mobile-card__main">
                      <h4 id="bookingTicketTitle{{ $loop->index }}" class="oc-title">{{ $ticketDisplayName }}</h4>
                      <div class="oc-mobile-card__meta">
                        <span>{{ __('Precio unit.') }}: <span class="oc-data-value tuki-data tuki-data-money">{{ $formatMoney($ticketInfo['unit_final']) }}</span></span>
                        @if ($ticketInfo['discount'] > 0)
                          <span>{{ __('Descuento') }}: <span class="oc-data-value tuki-data tuki-data-money">{{ $formatMoney($ticketInfo['discount']) }}</span></span>
                        @endif
                      </div>
                    </div>
                    <div class="oc-mobile-card__badges">
                      <span class="oc-pill tuki-data tuki-data-count">{{ $ticketQuantity }} {{ $ticketQuantity == 1 ? __('entrada') : __('entradas') }}</span>
                      <span class="oc-pill {{ $ticketIsComplete ? 'oc-pill--success' : 'oc-pill--warning' }}">
                        {{ $ticketIsComplete ? __('Escaneada') : __('Pendiente') }}
                      </span>
                    </div>
                  </div>

                  <div class="oc-mobile-card__grid" role="group" aria-label="{{ __('Resumen de entrada') }}">
                    <div class="bod-ticket-mobile-stat">
                      <span class="oc-data-label">{{ __('Subtotal') }}</span>
                      <span class="oc-money tuki-data tuki-data-money">{{ $formatMoney($ticketInfo['subtotal']) }}</span>
                      @if ($ticketInfo['unit_discount'] > 0)
                        <span class="oc-muted">{{ __('Antes') }}: <del class="tuki-data tuki-data-money">{{ $formatMoney($ticketInfo['unit_price']) }}</del></span>
                      @endif
                    </div>
                    <div class="bod-ticket-mobile-stat">
                      <span class="oc-data-label">{{ __('Escaneo') }}</span>
                      <span class="oc-money tuki-data tuki-data-count">{{ $ticketScanned }}/{{ $ticketQuantity }}</span>
                      <div class="oc-progress" aria-hidden="true"><span style="width: {{ $ticketInfo['scan_percent'] }}%"></span></div>
                      <span class="oc-muted">{{ __('Faltan') }}: <span class="tuki-data tuki-data-count">{{ $ticketPending }}</span></span>
                    </div>
                  </div>
                </article>
              @endforeach
            </div>
          </div>
        </section>

        <section class="oc-panel" aria-labelledby="bod-payment-title">
          <div class="oc-panel__header">
            <h3 id="bod-payment-title" class="oc-panel__title">{{ __('Pago y liquidación') }}</h3>
          </div>
          <div class="oc-panel__body">
            <div class="bod-ledger">
              <div class="bod-ledger-row">
                <div>
                  <span class="oc-data-label">{{ __('Método') }}</span>
                  <span class="oc-muted">{{ __('Medio de pago usado por el cliente') }}</span>
                </div>
                <span class="oc-data-value">{{ $booking->paymentMethod ?: '-' }}</span>
              </div>
              <div class="bod-ledger-row">
                <div>
                  <span class="oc-data-label">{{ __('Base entradas') }}</span>
                  <span class="oc-muted">{{ __('Importe antes de impuestos') }}</span>
                </div>
                <span class="oc-money tuki-data tuki-data-money">{{ $formatMoney($booking->price ?? 0) }}</span>
              </div>
              <div class="bod-ledger-row">
                <div>
                  <span class="oc-data-label">{{ __('Impuestos / cargos') }}</span>
                  <span class="oc-muted">{{ $booking->tax_percentage ? $booking->tax_percentage . '%' : __('Sin porcentaje informado') }}</span>
                </div>
                <span class="oc-money tuki-data tuki-data-money">{{ $formatMoney($booking->tax ?? 0) }}</span>
              </div>
              <div class="bod-ledger-row">
                <div>
                  <span class="oc-data-label">{{ __('Total cobrado') }}</span>
                  <span class="oc-muted">{{ __('Total pagado por el cliente') }}</span>
                </div>
                <span class="oc-money tuki-data tuki-data-money">{{ $formatMoney($paidTotal) }}</span>
              </div>
              <div class="bod-ledger-row">
                <div>
                  <span class="oc-data-label">{{ __('Comisión plataforma') }}</span>
                  <span class="oc-muted">{{ $booking->commission_percentage ? $booking->commission_percentage . '%' : __('Sin porcentaje informado') }}</span>
                </div>
                <span class="oc-money tuki-data tuki-data-money">{{ $formatMoney($booking->commission ?? 0) }}</span>
              </div>
              <div class="bod-ledger-row">
                <div>
                  <span class="oc-data-label">{{ __('Descuentos') }}</span>
                  <span class="oc-muted">{{ __('Cupón') }}: {{ $formatMoney($booking->discount ?? 0) }} - {{ __('Anticipada') }}: {{ $formatMoney($booking->early_bird_discount ?? 0) }}</span>
                </div>
                <span class="oc-money tuki-data tuki-data-money">{{ $formatMoney((float) ($booking->discount ?? 0) + (float) ($booking->early_bird_discount ?? 0)) }}</span>
              </div>
              <div class="bod-ledger-row bod-ledger-row--highlight">
                <div>
                  <span class="oc-data-label">{{ __('Recibís') }}</span>
                  <span class="oc-muted">{{ __('Base entradas menos comisión de plataforma') }}</span>
                </div>
                <span class="oc-money tuki-data tuki-data-money">{{ $formatMoney($organizerTotal) }}</span>
              </div>
            </div>
          </div>
        </section>

        <section class="oc-panel" aria-labelledby="bod-addons-title">
          <div class="oc-panel__header">
            <h3 id="bod-addons-title" class="oc-panel__title">{{ __('Add-ons') }}</h3>
            <span class="oc-pill tuki-data tuki-data-count">{{ $addonsCount }} - <span class="tuki-data-money">{{ $formatMoney($addonsTotal) }}</span></span>
          </div>
          <div class="oc-panel__body">
            @if (count($addonBreakdown) > 0)
              <table class="table oc-table">
                <colgroup>
                  <col class="bod-col-ticket">
                  <col class="bod-col-small">
                  <col class="bod-col-money">
                  <col class="bod-col-money">
                  <col class="bod-col-scan">
                </colgroup>
                <thead>
                  <tr>
                    <th scope="col">{{ __('Producto') }}</th>
                    <th scope="col">{{ __('Cant.') }}</th>
                    <th scope="col">{{ __('Precio unit.') }}</th>
                    <th scope="col">{{ __('Subtotal') }}</th>
                    <th scope="col">{{ __('Estado') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($addonBreakdown as $addon)
                    <tr>
                      <td data-label="{{ __('Producto') }}"><span class="bod-ticket-name">{{ $addon['title'] }}</span></td>
                      <td data-label="{{ __('Cant.') }}"><span class="oc-pill tuki-data tuki-data-count">{{ $addon['quantity'] }}</span></td>
                      <td data-label="{{ __('Precio unit.') }}"><span class="oc-money tuki-data tuki-data-money">{{ $formatMoney($addon['unit_price']) }}</span></td>
                      <td data-label="{{ __('Subtotal') }}"><span class="oc-money tuki-data tuki-data-money">{{ $formatMoney($addon['subtotal']) }}</span></td>
                      <td data-label="{{ __('Estado') }}">{{ $addon['redeemed'] ? __('Canjeado') : __('Pendiente') }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            @else
              <div class="oc-empty">{{ __('Esta reserva no tiene add-ons.') }}</div>
            @endif
          </div>
        </section>
      </div>

      <aside class="bod-stack">
        <section class="oc-panel" aria-labelledby="bod-customer-title">
          <div class="oc-panel__header">
            <h3 id="bod-customer-title" class="oc-panel__title">{{ __('Comprador') }}</h3>
          </div>
          <div class="oc-panel__body">
            <div class="oc-data-list">
              <div class="oc-data-row">
                <span class="oc-data-label">{{ __('Cuenta') }}</span>
                <span class="oc-data-value">
                  @if ($customer)
                    {{ $accountName ?: '-' }}
                  @elseif (is_null($booking->customer_id))
                    {{ __('Invitado') }}
                  @else
                    -
                  @endif
                </span>
              </div>
              <div class="oc-data-row">
                <span class="oc-data-label">{{ __('Nombre en la reserva') }}</span>
                <span class="oc-data-value">{{ $customerName ?: '-' }}</span>
              </div>
              <div class="oc-data-row">
                <span class="oc-data-label">{{ __('Email') }}</span>
                <span class="oc-data-value">{{ $booking->email ?: '-' }}</span>
              </div>
              <div class="oc-data-row">
                <span class="oc-data-label">{{ __('Teléfono') }}</span>
                <span class="oc-data-value">{{ $booking->phone ?: '-' }}</span>
              </div>
              <div class="oc-data-row">
                <span class="oc-data-label">{{ __('Ubicación') }}</span>
                <span class="oc-data-value">{{ $location ?: '-' }}</span>
              </div>
            </div>
          </div>
        </section>

        <section class="oc-panel" aria-labelledby="bod-booking-title">
          <div class="oc-panel__header">
            <h3 id="bod-booking-title" class="oc-panel__title">{{ __('Reserva') }}</h3>
          </div>
          <div class="oc-panel__body">
            <div class="oc-data-list">
              <div class="oc-data-row">
                <span class="oc-data-label">{{ __('ID interno') }}</span>
                <span class="oc-data-value tuki-data tuki-data-id">#{{ $booking->id }}</span>
              </div>
              <div class="oc-data-row">
                <span class="oc-data-label">{{ __('Código') }}</span>
                <span class="oc-data-value tuki-data tuki-data-id">#{{ $booking->booking_id }}</span>
              </div>
              <div class="oc-data-row">
                <span class="oc-data-label">{{ __('Entradas') }}</span>
                <span class="oc-data-value tuki-data tuki-data-count">{{ (int) $booking->quantity }}</span>
              </div>
              <div class="oc-data-row">
                <span class="oc-data-label">{{ __('Tipos') }}</span>
                <span class="oc-data-value tuki-data tuki-data-count">{{ count($ticketBreakdown) }}</span>
              </div>
              <div class="oc-data-row">
                <span class="oc-data-label">{{ __('Add-ons') }}</span>
                <span class="oc-data-value">
                  @if ($addonsCount > 0)
                    <span class="tuki-data tuki-data-count">{{ $addonsCount }}</span> -
                    <span class="tuki-data tuki-data-money">{{ $formatMoney($addonsTotal) }}</span>
                  @else
                    -
                  @endif
                </span>
              </div>
            </div>
          </div>
        </section>
      </aside>
    </div>

    @if (!is_null($booking->attachmentFile))
      @includeIf('organizer.event.booking.show-attachment')
    @endif
  </div>
@endsection
