@extends('backend.layout')

@section('style')
  <style>
    .booking-detail-admin {
      color: #1e2532;
    }

    .bd-hero {
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto;
      gap: 18px;
      align-items: start;
      padding: 22px;
      margin-bottom: 18px;
      border: 1px solid #e7eaf0;
      border-radius: 8px;
      background: #fff;
      box-shadow: 0 8px 24px rgba(30, 37, 50, .05);
    }

    .bd-eyebrow {
      margin-bottom: 6px;
      color: #667085;
      font-size: 12px;
      font-weight: 800;
      letter-spacing: 0;
      text-transform: uppercase;
    }

    .bd-title {
      margin: 0;
      color: #1e2532;
      font-size: 22px;
      font-weight: 800;
      line-height: 1.25;
    }

    .bd-id {
      margin-top: 8px;
      color: #475467;
      font-size: 13px;
      overflow-wrap: anywhere;
    }

    .bd-actions {
      display: flex;
      flex-wrap: wrap;
      justify-content: flex-end;
      gap: 8px;
    }

    .bd-action {
      min-height: 36px;
      display: inline-flex;
      align-items: center;
      gap: 7px;
      border-radius: 6px;
      font-weight: 700;
    }

    .bd-kpis {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 12px;
      margin-bottom: 18px;
    }

    .bd-kpi,
    .bd-panel {
      border: 1px solid #e7eaf0;
      border-radius: 8px;
      background: #fff;
      box-shadow: 0 6px 18px rgba(30, 37, 50, .04);
    }

    .bd-kpi {
      min-height: 104px;
      padding: 16px;
    }

    .bd-kpi__label {
      margin-bottom: 8px;
      color: #667085;
      font-size: 12px;
      font-weight: 800;
      text-transform: uppercase;
    }

    .bd-kpi__value {
      color: #1e2532;
      font-size: 22px;
      font-weight: 800;
      line-height: 1.2;
    }

    .bd-kpi__meta {
      margin-top: 6px;
      color: #667085;
      font-size: 12px;
    }

    .bd-layout {
      display: grid;
      grid-template-columns: minmax(0, 1.35fr) minmax(320px, .65fr);
      gap: 18px;
      align-items: start;
    }

    .bd-stack {
      display: grid;
      gap: 18px;
    }

    .bd-panel__header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      padding: 16px 18px;
      border-bottom: 1px solid #eef1f5;
    }

    .bd-panel__title {
      margin: 0;
      color: #1e2532;
      font-size: 16px;
      font-weight: 800;
    }

    .bd-panel__body {
      padding: 18px;
    }

    .bd-info-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 12px;
    }

    .bd-info-grid--one {
      grid-template-columns: 1fr;
    }

    .bd-field {
      min-width: 0;
      padding: 12px;
      border: 1px solid #eef1f5;
      border-radius: 7px;
      background: #fbfcfe;
    }

    .bd-field__label {
      display: block;
      color: #667085;
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
    }

    .bd-field__value {
      display: block;
      margin-top: 5px;
      color: #1e2532;
      font-weight: 700;
      overflow-wrap: anywhere;
    }

    .bd-muted {
      color: #667085;
      font-size: 12px;
    }

    .bd-money {
      font-weight: 800;
      white-space: nowrap;
    }

    .bd-status {
      display: inline-flex;
      align-items: center;
      min-height: 28px;
      padding: 6px 10px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 800;
    }

    .bd-status i {
      margin-right: 6px;
    }

    .bd-progress {
      width: 100%;
      height: 8px;
      overflow: hidden;
      margin-top: 8px;
      border-radius: 999px;
      background: #e7eaf0;
    }

    .bd-progress span {
      display: block;
      height: 100%;
      border-radius: inherit;
      background: #F97316;
    }

    .bd-table-wrap {
      overflow-x: auto;
    }

    .bd-table {
      width: 100%;
      margin-bottom: 0;
    }

    .bd-table th {
      border-top: 0;
      color: #667085;
      font-size: 12px;
      text-transform: uppercase;
      white-space: nowrap;
    }

    .bd-table td {
      vertical-align: middle;
    }

    .bd-ticket-name {
      display: block;
      color: #1e2532;
      font-weight: 800;
      overflow-wrap: anywhere;
    }

    .bd-pill {
      display: inline-flex;
      align-items: center;
      min-height: 24px;
      padding: 4px 8px;
      border-radius: 999px;
      background: #fff7ed;
      color: #9a3412;
      font-size: 12px;
      font-weight: 800;
      white-space: nowrap;
    }

    .bd-empty {
      padding: 18px;
      border: 1px dashed #d6dce6;
      border-radius: 8px;
      color: #667085;
      text-align: center;
    }

    @media (max-width: 1199px) {
      .bd-layout,
      .bd-hero {
        grid-template-columns: 1fr;
      }

      .bd-actions {
        justify-content: flex-start;
      }
    }

    @media (max-width: 767px) {
      .bd-kpis,
      .bd-info-grid {
        grid-template-columns: 1fr;
      }

      .bd-hero,
      .bd-panel__body {
        padding: 14px;
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
    $organizer = $booking->organizer;
    $fiscalProfile = $booking->fiscalProfile;
    $arcaInvoice = $booking->arcaInvoice;
    $ticketBreakdown = $booking->ticketBreakdown();
    $addonBreakdown = $booking->addonBreakdown();
    $addonsCount = collect($addonBreakdown)->sum('quantity');
    $addonsTotal = collect($addonBreakdown)->sum('subtotal');
    $scannedCount = $booking->scannedTicketsCount();
    $pendingScanCount = $booking->pendingTicketsCount();
    $scanPercent = $booking->scanPercent();
    $invoiceExtension = pathinfo((string) $booking->invoice, PATHINFO_EXTENSION);
    $hasInvoiceFile = $invoiceExtension == 'pdf' && $booking->hasInvoiceFile();
    $currencyPosition = $booking->currencySymbolPosition ?: $booking->currencyTextPosition;
    $currency = $booking->currencySymbol ?: $booking->currencyText;
    $formatMoney = function ($amount) use ($currencyPosition, $currency) {
        $amount = number_format((float) $amount, 2, ',', '.');
        return ($currencyPosition == 'left' ? $currency . ' ' : '') . $amount . ($currencyPosition == 'right' ? ' ' . $currency : '');
    };
    $paidTotal = (float) ($booking->price ?? 0) + (float) ($booking->tax ?? 0);
    $organizerTotal = !empty($booking->organizer_id) ? (float) ($booking->price ?? 0) - (float) ($booking->commission ?? 0) : null;
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
    $organizerInfo = $organizer && $language
      ? $organizer->organizer_info()->where('language_id', $language->id)->first()
      : null;
  @endphp

  <div class="booking-detail-admin">
    <div class="page-header">
      <h4 class="page-title">{{ __('Detalle de reserva') }}</h4>
      <ul class="breadcrumbs">
        <li class="nav-home">
          <a href="{{ route('admin.dashboard') }}" aria-label="{{ __('Ir al panel') }}">
            <i class="flaticon-home"></i>
          </a>
        </li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="{{ route('admin.event.booking') }}">{{ __('Reservas') }}</a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="#">{{ __('Detalle') }}</a></li>
      </ul>
    </div>

    <section class="bd-hero" aria-labelledby="booking-detail-title">
      <div>
        <div class="bd-eyebrow">{{ __('Reserva') }} #{{ $booking->id }}</div>
        <h2 id="booking-detail-title" class="bd-title">
          {{ $eventInfo ? $eventInfo->title : __('Evento no disponible') }}
        </h2>
        <div class="bd-id">#{{ $booking->booking_id }}</div>
      </div>

      <div class="bd-actions">
        <a class="btn btn-light bd-action" href="{{ url()->previous() }}">
          <i class="fas fa-arrow-left" aria-hidden="true"></i>{{ __('Volver') }}
        </a>
        @if ($eventInfo)
          <a class="btn btn-outline-primary bd-action" href="{{ route('event.details', ['slug' => $eventInfo->slug, 'id' => $eventInfo->event_id]) }}"
            target="_blank" rel="noopener">
            <i class="fas fa-external-link-alt" aria-hidden="true"></i>{{ __('Ver evento') }}
          </a>
        @endif
        @if ($hasInvoiceFile)
          <a class="btn btn-outline-secondary bd-action" href="{{ route('booking.ticket.download', $booking->id) }}"
            target="_blank" rel="noopener">
            <i class="fas fa-file-pdf" aria-hidden="true"></i>{{ __('Entrada PDF') }}
          </a>
        @endif
        @if (!is_null($booking->attachmentFile))
          <a class="btn btn-outline-info bd-action" href="#" data-toggle="modal" data-target="#attachmentModal-{{ $booking->id }}">
            <i class="fas fa-paperclip" aria-hidden="true"></i>{{ __('Comprobante') }}
          </a>
        @endif
      </div>
    </section>

    <section class="bd-kpis" aria-label="{{ __('Resumen de la reserva') }}">
      <div class="bd-kpi">
        <div class="bd-kpi__label">{{ __('Estado de pago') }}</div>
        <span class="badge badge-{{ $status['class'] }} bd-status">
          <i class="fas {{ $status['icon'] }}" aria-hidden="true"></i>{{ $status['label'] }}
        </span>
        <div class="bd-kpi__meta">{{ $booking->paymentMethod ?: __('Sin método informado') }}</div>
      </div>
      <div class="bd-kpi">
        <div class="bd-kpi__label">{{ __('Total pagado') }}</div>
        <div class="bd-kpi__value">{{ $formatMoney($paidTotal) }}</div>
        <div class="bd-kpi__meta">{{ __('Base') }}: {{ $formatMoney($booking->price ?? 0) }}</div>
      </div>
      <div class="bd-kpi">
        <div class="bd-kpi__label">{{ __('Entradas') }}</div>
        <div class="bd-kpi__value">{{ (int) $booking->quantity }}</div>
        <div class="bd-kpi__meta">{{ count($ticketBreakdown) }} {{ count($ticketBreakdown) == 1 ? __('tipo') : __('tipos') }}</div>
      </div>
      <div class="bd-kpi">
        <div class="bd-kpi__label">{{ __('Escaneo') }}</div>
        <div class="bd-kpi__value">{{ $scannedCount }}/{{ (int) $booking->quantity }}</div>
        <div class="bd-progress" aria-hidden="true"><span style="width: {{ $scanPercent }}%"></span></div>
        <div class="bd-kpi__meta">{{ __('Faltan') }}: {{ $pendingScanCount }}</div>
      </div>
    </section>

    <div class="bd-layout">
      <div class="bd-stack">
        <section class="bd-panel" aria-labelledby="bd-event-title">
          <div class="bd-panel__header">
            <h3 id="bd-event-title" class="bd-panel__title">{{ __('Evento y función') }}</h3>
          </div>
          <div class="bd-panel__body">
            <div class="bd-info-grid">
              <div class="bd-field">
                <span class="bd-field__label">{{ __('Evento') }}</span>
                <span class="bd-field__value">{{ $eventInfo ? $eventInfo->title : '-' }}</span>
              </div>
              <div class="bd-field">
                <span class="bd-field__label">{{ __('Fecha de reserva') }}</span>
                <span class="bd-field__value">{{ FullDateTime($booking->created_at) }}</span>
              </div>
              <div class="bd-field">
                <span class="bd-field__label">{{ __('Función') }}</span>
                <span class="bd-field__value">{{ $eventStartLabel }}</span>
              </div>
              <div class="bd-field">
                <span class="bd-field__label">{{ __('Fin / duración') }}</span>
                <span class="bd-field__value">{{ $eventEndLabel }} <span class="bd-muted">{{ $eventDuration != '-' ? '· ' . $eventDuration : '' }}</span></span>
              </div>
            </div>
          </div>
        </section>

        <section class="bd-panel" aria-labelledby="bd-tickets-title">
          <div class="bd-panel__header">
            <h3 id="bd-tickets-title" class="bd-panel__title">{{ __('Info de entradas') }}</h3>
            <span class="bd-pill">{{ (int) $booking->quantity }} {{ (int) $booking->quantity == 1 ? __('entrada') : __('entradas') }}</span>
          </div>
          <div class="bd-panel__body">
            <div class="bd-table-wrap">
              <table class="table bd-table">
                <thead>
                  <tr>
                    <th scope="col">{{ __('Entrada') }}</th>
                    <th scope="col">{{ __('Cantidad') }}</th>
                    <th scope="col">{{ __('Precio unit.') }}</th>
                    <th scope="col">{{ __('Subtotal') }}</th>
                    <th scope="col">{{ __('Escaneo') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($ticketBreakdown as $ticketInfo)
                    <tr>
                      <td>
                        <span class="bd-ticket-name">{{ $ticketInfo['name'] }}</span>
                        @if ($ticketInfo['discount'] > 0)
                          <span class="bd-muted">{{ __('Descuento') }}: {{ $formatMoney($ticketInfo['discount']) }}</span>
                        @endif
                      </td>
                      <td><span class="bd-pill">{{ $ticketInfo['quantity'] }}</span></td>
                      <td>
                        <span class="bd-money">{{ $formatMoney($ticketInfo['unit_final']) }}</span>
                        @if ($ticketInfo['unit_discount'] > 0)
                          <del class="bd-muted">{{ $formatMoney($ticketInfo['unit_price']) }}</del>
                        @endif
                      </td>
                      <td><span class="bd-money">{{ $formatMoney($ticketInfo['subtotal']) }}</span></td>
                      <td>
                        <strong>{{ $ticketInfo['scanned'] }}/{{ $ticketInfo['quantity'] }}</strong>
                        <div class="bd-progress" aria-hidden="true"><span style="width: {{ $ticketInfo['scan_percent'] }}%"></span></div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </section>

        <section class="bd-panel" aria-labelledby="bd-payment-title">
          <div class="bd-panel__header">
            <h3 id="bd-payment-title" class="bd-panel__title">{{ __('Pago y liquidación') }}</h3>
          </div>
          <div class="bd-panel__body">
            <div class="bd-info-grid">
              <div class="bd-field">
                <span class="bd-field__label">{{ __('Método') }}</span>
                <span class="bd-field__value">{{ $booking->paymentMethod ?: '-' }}</span>
              </div>
              <div class="bd-field">
                <span class="bd-field__label">{{ __('Estado') }}</span>
                <span class="bd-field__value">{{ $status['label'] }}</span>
              </div>
              <div class="bd-field">
                <span class="bd-field__label">{{ __('Total pagado por cliente') }}</span>
                <span class="bd-field__value bd-money">{{ $formatMoney($paidTotal) }}</span>
              </div>
              <div class="bd-field">
                <span class="bd-field__label">{{ __('Impuestos') }}</span>
                <span class="bd-field__value">{{ $formatMoney($booking->tax ?? 0) }} <span class="bd-muted">{{ $booking->tax_percentage ? '(' . $booking->tax_percentage . '%)' : '' }}</span></span>
              </div>
              <div class="bd-field">
                <span class="bd-field__label">{{ __('Descuento cupón') }}</span>
                <span class="bd-field__value">{{ $formatMoney($booking->discount ?? 0) }}</span>
              </div>
              <div class="bd-field">
                <span class="bd-field__label">{{ __('Descuento anticipada') }}</span>
                <span class="bd-field__value">{{ $formatMoney($booking->early_bird_discount ?? 0) }}</span>
              </div>
              @if (!empty($booking->organizer_id))
                <div class="bd-field">
                  <span class="bd-field__label">{{ __('Comisión admin') }}</span>
                  <span class="bd-field__value">{{ $formatMoney($booking->commission ?? 0) }} <span class="bd-muted">{{ $booking->commission_percentage ? '(' . $booking->commission_percentage . '%)' : '' }}</span></span>
                </div>
                <div class="bd-field">
                  <span class="bd-field__label">{{ __('Recibe organizador') }}</span>
                  <span class="bd-field__value bd-money">{{ $formatMoney($organizerTotal) }}</span>
                </div>
              @endif
            </div>
          </div>
        </section>

        <section class="bd-panel" aria-labelledby="bd-addons-title">
          <div class="bd-panel__header">
            <h3 id="bd-addons-title" class="bd-panel__title">{{ __('Add-ons') }}</h3>
            <span class="bd-pill">{{ $addonsCount }} · {{ $formatMoney($addonsTotal) }}</span>
          </div>
          <div class="bd-panel__body">
            @if (count($addonBreakdown) > 0)
              <div class="bd-table-wrap">
                <table class="table bd-table">
                  <thead>
                    <tr>
                      <th scope="col">{{ __('Producto') }}</th>
                      <th scope="col">{{ __('Cantidad') }}</th>
                      <th scope="col">{{ __('Precio unit.') }}</th>
                      <th scope="col">{{ __('Subtotal') }}</th>
                      <th scope="col">{{ __('Estado') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($addonBreakdown as $addon)
                      <tr>
                        <td><span class="bd-ticket-name">{{ $addon['title'] }}</span></td>
                        <td><span class="bd-pill">{{ $addon['quantity'] }}</span></td>
                        <td>{{ $formatMoney($addon['unit_price']) }}</td>
                        <td><span class="bd-money">{{ $formatMoney($addon['subtotal']) }}</span></td>
                        <td>{{ $addon['redeemed'] ? __('Canjeado') : __('Pendiente') }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @else
              <div class="bd-empty">{{ __('Esta reserva no tiene add-ons.') }}</div>
            @endif
          </div>
        </section>
      </div>

      <aside class="bd-stack">
        <section class="bd-panel" aria-labelledby="bd-customer-title">
          <div class="bd-panel__header">
            <h3 id="bd-customer-title" class="bd-panel__title">{{ __('Comprador') }}</h3>
          </div>
          <div class="bd-panel__body">
            <div class="bd-info-grid bd-info-grid--one">
              <div class="bd-field">
                <span class="bd-field__label">{{ __('Cuenta') }}</span>
                <span class="bd-field__value">
                  @if ($customer)
                    <a href="{{ route('admin.customer_management.customer_details', ['id' => $customer->id, 'language' => optional($language)->code ?: 'es']) }}">
                      {{ trim($customer->fname . ' ' . $customer->lname) }}
                    </a>
                  @elseif (is_null($booking->customer_id))
                    {{ __('Invitado') }}
                  @else
                    -
                  @endif
                </span>
              </div>
              <div class="bd-field">
                <span class="bd-field__label">{{ __('Nombre usado en la reserva') }}</span>
                <span class="bd-field__value">{{ trim($booking->fname . ' ' . $booking->lname) ?: '-' }}</span>
              </div>
              <div class="bd-field">
                <span class="bd-field__label">{{ __('Email') }}</span>
                <span class="bd-field__value">{{ $booking->email ?: '-' }}</span>
              </div>
              <div class="bd-field">
                <span class="bd-field__label">{{ __('Teléfono') }}</span>
                <span class="bd-field__value">{{ $booking->phone ?: '-' }}</span>
              </div>
              <div class="bd-field">
                <span class="bd-field__label">{{ __('Ubicación') }}</span>
                <span class="bd-field__value">
                  {{ collect([$booking->address, $booking->city, $booking->state, $booking->country])->filter()->implode(', ') ?: '-' }}
                </span>
              </div>
            </div>
          </div>
        </section>

        @if (!empty($booking->organizer_id) && $organizer)
          <section class="bd-panel" aria-labelledby="bd-organizer-title">
            <div class="bd-panel__header">
              <h3 id="bd-organizer-title" class="bd-panel__title">{{ __('Organizador') }}</h3>
            </div>
            <div class="bd-panel__body">
              <div class="bd-info-grid bd-info-grid--one">
                <div class="bd-field">
                  <span class="bd-field__label">{{ __('Usuario') }}</span>
                  <span class="bd-field__value">
                    <a href="{{ route('admin.organizer_management.organizer_details', ['id' => $organizer->id, 'language' => optional($language)->code ?: 'es']) }}">
                      {{ $organizer->username }}
                    </a>
                  </span>
                </div>
                <div class="bd-field">
                  <span class="bd-field__label">{{ __('Email') }}</span>
                  <span class="bd-field__value">{{ $organizer->email ?: '-' }}</span>
                </div>
                <div class="bd-field">
                  <span class="bd-field__label">{{ __('Teléfono') }}</span>
                  <span class="bd-field__value">{{ $organizer->phone ?: '-' }}</span>
                </div>
                <div class="bd-field">
                  <span class="bd-field__label">{{ __('Ubicación') }}</span>
                  <span class="bd-field__value">
                    {{ collect([optional($organizerInfo)->address, optional($organizerInfo)->city, optional($organizerInfo)->state, optional($organizerInfo)->country])->filter()->implode(', ') ?: '-' }}
                  </span>
                </div>
              </div>
            </div>
          </section>
        @endif

        <section class="bd-panel" aria-labelledby="bd-fiscal-title">
          <div class="bd-panel__header">
            <h3 id="bd-fiscal-title" class="bd-panel__title">{{ __('Fiscal / ARCA') }}</h3>
          </div>
          <div class="bd-panel__body">
            <div class="bd-info-grid bd-info-grid--one">
              <div class="bd-field">
                <span class="bd-field__label">{{ __('Documento') }}</span>
                <span class="bd-field__value">
                  {{ $fiscalProfile ? trim($fiscalProfile->document_type . ' ' . $fiscalProfile->document_number) : '-' }}
                </span>
              </div>
              <div class="bd-field">
                <span class="bd-field__label">{{ __('Condición IVA') }}</span>
                <span class="bd-field__value">{{ $fiscalProfile ? ucwords(str_replace('_', ' ', $fiscalProfile->iva_condition)) : '-' }}</span>
              </div>
              <div class="bd-field">
                <span class="bd-field__label">{{ __('Razón / nombre fiscal') }}</span>
                <span class="bd-field__value">{{ optional($fiscalProfile)->full_name ?: '-' }}</span>
              </div>
              <div class="bd-field">
                <span class="bd-field__label">{{ __('Factura ARCA') }}</span>
                <span class="bd-field__value">
                  @if ($arcaInvoice)
                    <a href="{{ route('admin.arca_invoices.show', $arcaInvoice->id) }}">{{ strtoupper($arcaInvoice->status) }}</a>
                    <span class="bd-muted d-block">{{ $arcaInvoice->cbte_nro ? 'Cbte. ' . $arcaInvoice->cbte_nro : '' }}</span>
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
      @includeIf('backend.event.booking.show-attachment')
    @endif
  </div>
@endsection
