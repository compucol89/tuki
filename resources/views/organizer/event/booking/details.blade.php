@extends('organizer.layout')

@section('style')
  <style>
    .organizer-booking-detail {
      max-width: 100%;
      overflow-x: hidden;
      color: #1e2532;
    }

    .bod-hero,
    .bod-kpi,
    .bod-panel {
      border: 1px solid #e7eaf0;
      border-radius: 8px;
      background: #fff;
      box-shadow: 0 6px 18px rgba(30, 37, 50, .04);
    }

    .bod-hero {
      display: grid;
      gap: 14px;
      padding: 16px;
      margin-bottom: 16px;
    }

    .bod-eyebrow {
      margin-bottom: 5px;
      color: #667085;
      font-size: 12px;
      font-weight: 800;
      letter-spacing: 0;
      text-transform: uppercase;
    }

    .bod-title {
      margin: 0;
      color: #1e2532;
      font-size: 21px;
      font-weight: 800;
      line-height: 1.25;
      overflow-wrap: anywhere;
    }

    .bod-id {
      margin-top: 7px;
      color: #667085;
      font-size: 13px;
      overflow-wrap: anywhere;
    }

    .bod-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
    }

    .bod-action {
      min-height: 38px;
      display: inline-flex;
      align-items: center;
      gap: 7px;
      border-radius: 6px;
      font-weight: 700;
    }

    .bod-kpis {
      display: grid;
      grid-template-columns: 1fr;
      gap: 12px;
      margin-bottom: 16px;
    }

    .bod-kpi {
      min-width: 0;
      min-height: 94px;
      padding: 14px;
    }

    .bod-kpi__label,
    .bod-label {
      color: #667085;
      font-size: 11px;
      font-weight: 800;
      letter-spacing: 0;
      text-transform: uppercase;
    }

    .bod-kpi__value {
      margin-top: 7px;
      color: #1e2532;
      font-size: 22px;
      font-weight: 800;
      line-height: 1.2;
      overflow-wrap: anywhere;
    }

    .bod-kpi__meta,
    .bod-muted {
      color: #667085;
      font-size: 12px;
      line-height: 1.35;
    }

    .bod-kpi__meta {
      margin-top: 6px;
    }

    .bod-layout,
    .bod-stack {
      display: grid;
      gap: 16px;
    }

    .bod-panel {
      min-width: 0;
      overflow: hidden;
    }

    .bod-panel__header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      padding: 14px 16px;
      border-bottom: 1px solid #eef1f5;
    }

    .bod-panel__title {
      margin: 0;
      color: #1e2532;
      font-size: 16px;
      font-weight: 800;
    }

    .bod-panel__body {
      padding: 16px;
    }

    .bod-info-grid,
    .bod-ledger,
    .bod-side-list {
      display: grid;
      gap: 0;
    }

    .bod-info-item,
    .bod-ledger-row,
    .bod-side-item {
      display: grid;
      gap: 4px;
      min-width: 0;
      padding: 10px 0;
      border-bottom: 1px solid #eef1f5;
    }

    .bod-info-item:last-child,
    .bod-ledger-row:last-child,
    .bod-side-item:last-child {
      border-bottom: 0;
      padding-bottom: 0;
    }

    .bod-value {
      color: #1e2532;
      font-weight: 700;
      line-height: 1.35;
      overflow-wrap: anywhere;
    }

    .bod-money {
      color: #1e2532;
      font-weight: 800;
      white-space: nowrap;
    }

    .bod-ledger-row {
      grid-template-columns: minmax(0, 1fr) auto;
      align-items: center;
      column-gap: 12px;
    }

    .bod-ledger-row--highlight {
      margin-top: 6px;
      padding: 12px;
      border: 1px solid #fed7aa;
      border-radius: 7px;
      background: #fff7ed;
    }

    .bod-status {
      display: inline-flex;
      align-items: center;
      min-height: 28px;
      padding: 6px 10px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 800;
    }

    .bod-status i {
      margin-right: 6px;
    }

    .bod-pill {
      display: inline-flex;
      align-items: center;
      min-height: 24px;
      padding: 3px 8px;
      border-radius: 999px;
      background: #fff7ed;
      color: #9a3412;
      font-size: 12px;
      font-weight: 800;
      white-space: nowrap;
    }

    .bod-progress {
      width: 100%;
      max-width: 170px;
      height: 7px;
      overflow: hidden;
      margin-top: 6px;
      border-radius: 999px;
      background: #e7eaf0;
    }

    .bod-progress span {
      display: block;
      height: 100%;
      border-radius: inherit;
      background: #F97316;
    }

    .bod-table {
      width: 100%;
      table-layout: fixed;
      margin-bottom: 0;
      font-size: 12px;
    }

    .bod-table th {
      border-top: 0;
      color: #667085;
      font-size: 10px;
      line-height: 1.25;
      padding: 9px 6px;
      text-transform: uppercase;
      white-space: normal;
    }

    .bod-table td {
      padding: 10px 6px;
      vertical-align: middle;
      line-height: 1.35;
      overflow-wrap: anywhere;
    }

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

    .bod-ticket-name {
      display: block;
      color: #1e2532;
      font-weight: 800;
      overflow-wrap: anywhere;
    }

    .bod-empty {
      padding: 18px 10px;
      color: #667085;
      text-align: center;
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

      .bod-kpis {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      .bod-info-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        column-gap: 18px;
      }
    }

    @media (min-width: 1200px) {
      .bod-kpis {
        grid-template-columns: repeat(4, minmax(0, 1fr));
      }

      .bod-layout {
        grid-template-columns: minmax(0, 1.42fr) minmax(300px, .58fr);
        align-items: start;
      }
    }

    @media (max-width: 767px) {
      .bod-title {
        font-size: 19px;
      }

      .bod-panel__header {
        align-items: flex-start;
        flex-direction: column;
      }

      .bod-table,
      .bod-table thead,
      .bod-table tbody,
      .bod-table tr,
      .bod-table th,
      .bod-table td {
        display: block;
        width: 100%;
      }

      .bod-table thead {
        position: absolute;
        width: 1px;
        height: 1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
      }

      .bod-table tr {
        padding: 10px 0;
        border-bottom: 1px solid #eef1f5;
      }

      .bod-table tr:last-child {
        border-bottom: 0;
      }

      .bod-table td {
        display: grid;
        grid-template-columns: 112px minmax(0, 1fr);
        gap: 8px;
        padding: 5px 0;
        border-top: 0;
      }

      .bod-table td::before {
        content: attr(data-label);
        color: #667085;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
      }

      .bod-ledger-row {
        grid-template-columns: 1fr;
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
      <h4 class="page-title">{{ __('Detalle de reserva') }}</h4>
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
        <div class="bod-id">#{{ $booking->booking_id }}</div>
      </div>

      <div class="bod-actions">
        <a class="btn btn-light bod-action" href="{{ route('organizer.event.booking') }}">
          <i class="fas fa-arrow-left" aria-hidden="true"></i>{{ __('Volver') }}
        </a>
        @if ($eventInfo)
          <a class="btn btn-outline-primary bod-action" href="{{ route('event.details', ['slug' => $eventInfo->slug, 'id' => $eventInfo->event_id]) }}"
            target="_blank" rel="noopener">
            <i class="fas fa-external-link-alt" aria-hidden="true"></i>{{ __('Ver evento') }}
          </a>
        @endif
        @if ($hasInvoiceFile)
          <a class="btn btn-outline-secondary bod-action" href="{{ route('booking.ticket.download', $booking->id) }}"
            target="_blank" rel="noopener">
            <i class="fas fa-file-pdf" aria-hidden="true"></i>{{ __('Entrada PDF') }}
          </a>
        @endif
        @if (!is_null($booking->attachmentFile))
          <button class="btn btn-outline-info bod-action" type="button" data-toggle="modal" data-target="#attachmentModal-{{ $booking->id }}">
            <i class="fas fa-paperclip" aria-hidden="true"></i>{{ __('Comprobante') }}
          </button>
        @endif
      </div>
    </section>

    <section class="bod-kpis" aria-label="{{ __('Resumen de la reserva') }}">
      <div class="bod-kpi">
        <div class="bod-kpi__label">{{ __('Estado') }}</div>
        <span class="badge badge-{{ $status['class'] }} bod-status">
          <i class="fas {{ $status['icon'] }}" aria-hidden="true"></i>{{ $status['label'] }}
        </span>
        <div class="bod-kpi__meta">{{ $booking->paymentMethod ?: __('Sin método informado') }}</div>
      </div>
      <div class="bod-kpi">
        <div class="bod-kpi__label">{{ __('Total cobrado') }}</div>
        <div class="bod-kpi__value">{{ $formatMoney($paidTotal) }}</div>
        <div class="bod-kpi__meta">{{ __('Base entradas') }}: {{ $formatMoney($booking->price ?? 0) }}</div>
      </div>
      <div class="bod-kpi">
        <div class="bod-kpi__label">{{ __('Recibís') }}</div>
        <div class="bod-kpi__value">{{ $formatMoney($organizerTotal) }}</div>
        <div class="bod-kpi__meta">{{ __('Comisión plataforma') }}: {{ $formatMoney($booking->commission ?? 0) }}</div>
      </div>
      <div class="bod-kpi">
        <div class="bod-kpi__label">{{ __('Escaneo') }}</div>
        <div class="bod-kpi__value">{{ $scannedCount }}/{{ (int) $booking->quantity }}</div>
        <div class="bod-progress" aria-hidden="true"><span style="width: {{ $scanPercent }}%"></span></div>
        <div class="bod-kpi__meta">{{ __('Faltan') }}: {{ $pendingScanCount }}</div>
      </div>
    </section>

    <div class="bod-layout">
      <div class="bod-stack">
        <section class="bod-panel" aria-labelledby="bod-event-title">
          <div class="bod-panel__header">
            <h3 id="bod-event-title" class="bod-panel__title">{{ __('Evento y función') }}</h3>
          </div>
          <div class="bod-panel__body">
            <div class="bod-info-grid">
              <div class="bod-info-item">
                <span class="bod-label">{{ __('Evento') }}</span>
                <span class="bod-value">{{ $eventInfo ? $eventInfo->title : '-' }}</span>
              </div>
              <div class="bod-info-item">
                <span class="bod-label">{{ __('Fecha de reserva') }}</span>
                <span class="bod-value">{{ FullDateTime($booking->created_at) }}</span>
              </div>
              <div class="bod-info-item">
                <span class="bod-label">{{ __('Función') }}</span>
                <span class="bod-value">{{ $eventStartLabel }}</span>
              </div>
              <div class="bod-info-item">
                <span class="bod-label">{{ __('Fin / duración') }}</span>
                <span class="bod-value">{{ $eventEndLabel }} <span class="bod-muted">{{ $eventDuration != '-' ? '- ' . $eventDuration : '' }}</span></span>
              </div>
            </div>
          </div>
        </section>

        <section class="bod-panel" aria-labelledby="bod-tickets-title">
          <div class="bod-panel__header">
            <h3 id="bod-tickets-title" class="bod-panel__title">{{ __('Info de entradas') }}</h3>
            <span class="bod-pill">{{ (int) $booking->quantity }} {{ (int) $booking->quantity == 1 ? __('entrada') : __('entradas') }}</span>
          </div>
          <div class="bod-panel__body">
            <table class="table bod-table">
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
                        <span class="bod-muted">{{ __('Descuento') }}: {{ $formatMoney($ticketInfo['discount']) }}</span>
                      @endif
                    </td>
                    <td data-label="{{ __('Cant.') }}"><span class="bod-pill">{{ $ticketInfo['quantity'] }}</span></td>
                    <td data-label="{{ __('Precio unit.') }}">
                      <span class="bod-money">{{ $formatMoney($ticketInfo['unit_final']) }}</span>
                      @if ($ticketInfo['unit_discount'] > 0)
                        <del class="bod-muted">{{ $formatMoney($ticketInfo['unit_price']) }}</del>
                      @endif
                    </td>
                    <td data-label="{{ __('Subtotal') }}"><span class="bod-money">{{ $formatMoney($ticketInfo['subtotal']) }}</span></td>
                    <td data-label="{{ __('Escaneo') }}">
                      <strong>{{ $ticketInfo['scanned'] }}/{{ $ticketInfo['quantity'] }}</strong>
                      <span class="bod-muted">{{ __('Faltan') }}: {{ $ticketInfo['pending'] }}</span>
                      <div class="bod-progress" aria-hidden="true"><span style="width: {{ $ticketInfo['scan_percent'] }}%"></span></div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </section>

        <section class="bod-panel" aria-labelledby="bod-payment-title">
          <div class="bod-panel__header">
            <h3 id="bod-payment-title" class="bod-panel__title">{{ __('Pago y liquidación') }}</h3>
          </div>
          <div class="bod-panel__body">
            <div class="bod-ledger">
              <div class="bod-ledger-row">
                <div>
                  <span class="bod-label">{{ __('Método') }}</span>
                  <span class="bod-muted">{{ __('Medio de pago usado por el cliente') }}</span>
                </div>
                <span class="bod-value">{{ $booking->paymentMethod ?: '-' }}</span>
              </div>
              <div class="bod-ledger-row">
                <div>
                  <span class="bod-label">{{ __('Base entradas') }}</span>
                  <span class="bod-muted">{{ __('Importe antes de impuestos') }}</span>
                </div>
                <span class="bod-money">{{ $formatMoney($booking->price ?? 0) }}</span>
              </div>
              <div class="bod-ledger-row">
                <div>
                  <span class="bod-label">{{ __('Impuestos / cargos') }}</span>
                  <span class="bod-muted">{{ $booking->tax_percentage ? $booking->tax_percentage . '%' : __('Sin porcentaje informado') }}</span>
                </div>
                <span class="bod-money">{{ $formatMoney($booking->tax ?? 0) }}</span>
              </div>
              <div class="bod-ledger-row">
                <div>
                  <span class="bod-label">{{ __('Total cobrado') }}</span>
                  <span class="bod-muted">{{ __('Total pagado por el cliente') }}</span>
                </div>
                <span class="bod-money">{{ $formatMoney($paidTotal) }}</span>
              </div>
              <div class="bod-ledger-row">
                <div>
                  <span class="bod-label">{{ __('Comisión plataforma') }}</span>
                  <span class="bod-muted">{{ $booking->commission_percentage ? $booking->commission_percentage . '%' : __('Sin porcentaje informado') }}</span>
                </div>
                <span class="bod-money">{{ $formatMoney($booking->commission ?? 0) }}</span>
              </div>
              <div class="bod-ledger-row">
                <div>
                  <span class="bod-label">{{ __('Descuentos') }}</span>
                  <span class="bod-muted">{{ __('Cupón') }}: {{ $formatMoney($booking->discount ?? 0) }} - {{ __('Anticipada') }}: {{ $formatMoney($booking->early_bird_discount ?? 0) }}</span>
                </div>
                <span class="bod-money">{{ $formatMoney((float) ($booking->discount ?? 0) + (float) ($booking->early_bird_discount ?? 0)) }}</span>
              </div>
              <div class="bod-ledger-row bod-ledger-row--highlight">
                <div>
                  <span class="bod-label">{{ __('Recibís') }}</span>
                  <span class="bod-muted">{{ __('Base entradas menos comisión de plataforma') }}</span>
                </div>
                <span class="bod-money">{{ $formatMoney($organizerTotal) }}</span>
              </div>
            </div>
          </div>
        </section>

        <section class="bod-panel" aria-labelledby="bod-addons-title">
          <div class="bod-panel__header">
            <h3 id="bod-addons-title" class="bod-panel__title">{{ __('Add-ons') }}</h3>
            <span class="bod-pill">{{ $addonsCount }} - {{ $formatMoney($addonsTotal) }}</span>
          </div>
          <div class="bod-panel__body">
            @if (count($addonBreakdown) > 0)
              <table class="table bod-table">
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
                      <td data-label="{{ __('Cant.') }}"><span class="bod-pill">{{ $addon['quantity'] }}</span></td>
                      <td data-label="{{ __('Precio unit.') }}"><span class="bod-money">{{ $formatMoney($addon['unit_price']) }}</span></td>
                      <td data-label="{{ __('Subtotal') }}"><span class="bod-money">{{ $formatMoney($addon['subtotal']) }}</span></td>
                      <td data-label="{{ __('Estado') }}">{{ $addon['redeemed'] ? __('Canjeado') : __('Pendiente') }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            @else
              <div class="bod-empty">{{ __('Esta reserva no tiene add-ons.') }}</div>
            @endif
          </div>
        </section>
      </div>

      <aside class="bod-stack">
        <section class="bod-panel" aria-labelledby="bod-customer-title">
          <div class="bod-panel__header">
            <h3 id="bod-customer-title" class="bod-panel__title">{{ __('Comprador') }}</h3>
          </div>
          <div class="bod-panel__body">
            <div class="bod-side-list">
              <div class="bod-side-item">
                <span class="bod-label">{{ __('Cuenta') }}</span>
                <span class="bod-value">
                  @if ($customer)
                    {{ $accountName ?: '-' }}
                  @elseif (is_null($booking->customer_id))
                    {{ __('Invitado') }}
                  @else
                    -
                  @endif
                </span>
              </div>
              <div class="bod-side-item">
                <span class="bod-label">{{ __('Nombre en la reserva') }}</span>
                <span class="bod-value">{{ $customerName ?: '-' }}</span>
              </div>
              <div class="bod-side-item">
                <span class="bod-label">{{ __('Email') }}</span>
                <span class="bod-value">{{ $booking->email ?: '-' }}</span>
              </div>
              <div class="bod-side-item">
                <span class="bod-label">{{ __('Teléfono') }}</span>
                <span class="bod-value">{{ $booking->phone ?: '-' }}</span>
              </div>
              <div class="bod-side-item">
                <span class="bod-label">{{ __('Ubicación') }}</span>
                <span class="bod-value">{{ $location ?: '-' }}</span>
              </div>
            </div>
          </div>
        </section>

        <section class="bod-panel" aria-labelledby="bod-booking-title">
          <div class="bod-panel__header">
            <h3 id="bod-booking-title" class="bod-panel__title">{{ __('Reserva') }}</h3>
          </div>
          <div class="bod-panel__body">
            <div class="bod-side-list">
              <div class="bod-side-item">
                <span class="bod-label">{{ __('ID interno') }}</span>
                <span class="bod-value">#{{ $booking->id }}</span>
              </div>
              <div class="bod-side-item">
                <span class="bod-label">{{ __('Código') }}</span>
                <span class="bod-value">#{{ $booking->booking_id }}</span>
              </div>
              <div class="bod-side-item">
                <span class="bod-label">{{ __('Entradas') }}</span>
                <span class="bod-value">{{ (int) $booking->quantity }}</span>
              </div>
              <div class="bod-side-item">
                <span class="bod-label">{{ __('Tipos') }}</span>
                <span class="bod-value">{{ count($ticketBreakdown) }}</span>
              </div>
              <div class="bod-side-item">
                <span class="bod-label">{{ __('Add-ons') }}</span>
                <span class="bod-value">{{ $addonsCount > 0 ? $addonsCount . ' - ' . $formatMoney($addonsTotal) : '-' }}</span>
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
