@extends('organizer.layout')

@section('style')
  <style>
    .organizer-booking-admin {
      color: #1e2532;
    }

    .ob-summary {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 12px;
      margin-bottom: 18px;
    }

    .ob-metric {
      min-height: 96px;
      padding: 16px;
      border: 1px solid #e7eaf0;
      border-radius: 8px;
      background: #fff;
      box-shadow: 0 6px 18px rgba(30, 37, 50, .04);
    }

    .ob-metric__label {
      margin-bottom: 8px;
      color: #64748b;
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
    }

    .ob-metric__value {
      color: #1e2532;
      font-size: 24px;
      font-weight: 800;
      line-height: 1.2;
    }

    .ob-metric__hint {
      margin-top: 5px;
      color: #667085;
      font-size: 11px;
      line-height: 1.35;
    }

    .ob-toolbar {
      border-bottom: 1px solid #eef1f5;
      background: #fbfcfe;
    }

    .ob-type-summary {
      max-width: 100%;
      overflow: hidden;
      margin-bottom: 18px;
      border: 1px solid #e7eaf0;
      border-radius: 8px;
      background: #fff;
      box-shadow: 0 6px 18px rgba(30, 37, 50, .04);
    }

    .ob-type-summary__head {
      display: flex;
      justify-content: space-between;
      gap: 14px;
      padding: 16px 18px;
      border-bottom: 1px solid #eef1f5;
    }

    .ob-type-summary__title {
      margin: 0;
      color: #1e2532;
      font-size: 16px;
      font-weight: 800;
    }

    .ob-type-summary__body {
      padding: 18px;
    }

    .ob-event-summary-list {
      display: grid;
      gap: 14px;
    }

    .ob-event-summary-card {
      overflow: hidden;
      border: 1px solid #e7eaf0;
      border-radius: 10px;
      background: #fff;
    }

    .ob-event-summary-card__head {
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto;
      gap: 16px;
      align-items: start;
      padding: 16px 18px;
      border-bottom: 1px solid #eef1f5;
      background: linear-gradient(180deg, #fbfcfe 0%, #fff 100%);
    }

    .ob-event-summary-card__head > div {
      min-width: 0;
    }

    .ob-event-summary-card__title {
      margin: 0 0 7px;
      color: #1e2532;
      font-size: 16px;
      font-weight: 800;
      line-height: 1.25;
    }

    .ob-event-summary-card__meta {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      color: #667085;
      font-size: 12px;
      font-weight: 600;
    }

    .ob-event-summary-card__meta span {
      display: inline-flex;
      align-items: center;
      min-height: 24px;
      padding: 4px 8px;
      border: 1px solid #eef1f5;
      border-radius: 999px;
      background: #fff;
      line-height: 1.2;
      white-space: nowrap;
    }

    .ob-event-summary-card__date {
      color: #f05a28;
      font-weight: 800;
    }

    .ob-event-summary-card__status {
      display: inline-flex;
      align-items: center;
      min-height: 24px;
      padding: 5px 9px;
      border: 1px solid rgba(240, 90, 40, .22);
      border-radius: 999px;
      background: #fff7f2;
      color: #d94a1e;
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
    }

    .ob-event-summary-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(124px, 1fr));
      gap: 1px;
      padding: 1px;
      border-bottom: 1px solid #eef1f5;
      background: #eef1f5;
    }

    .ob-event-summary-stat {
      min-width: 0;
      padding: 12px 14px;
      background: #fbfcfe;
    }

    .ob-event-summary-stat span {
      display: block;
      color: #667085;
      font-size: 10px;
      font-weight: 800;
      text-transform: uppercase;
    }

    .ob-event-summary-stat strong {
      display: block;
      margin-top: 3px;
      color: #1e2532;
      font-size: 18px;
      font-weight: 800;
      line-height: 1.15;
      overflow-wrap: anywhere;
    }

    .ob-type-table {
      width: 100%;
      table-layout: fixed;
      margin-bottom: 0;
      font-size: 11px;
    }

    .ob-type-table th {
      border-top: 0;
      color: #667085;
      font-size: 10px;
      line-height: 1.25;
      padding: 8px 6px;
      text-transform: uppercase;
      white-space: normal;
    }

    .ob-type-table td {
      padding: 9px 6px;
      vertical-align: middle;
      line-height: 1.35;
      overflow-wrap: anywhere;
    }

    .ob-type-table__ticket {
      width: 43%;
    }

    .ob-type-table__counts {
      width: 10%;
    }

    .ob-type-table__scan {
      width: 15%;
    }

    .ob-type-table__money {
      width: 12%;
    }

    .ob-type-name {
      display: block;
      color: #1e2532;
      font-weight: 800;
      overflow-wrap: anywhere;
    }

    .ob-type-event {
      display: block;
      max-width: 100%;
      overflow: hidden;
      color: #667085;
      font-size: 12px;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .ob-toolbar .form-group {
      margin-bottom: 12px;
    }

    .ob-table {
      margin-bottom: 0;
    }

    .ob-table th {
      border-top: 0;
      color: #667085;
      font-size: 12px;
      text-transform: uppercase;
      white-space: nowrap;
    }

    .ob-table td {
      vertical-align: middle;
    }

    .ob-title {
      display: block;
      max-width: 280px;
      overflow: hidden;
      color: #1e2532;
      font-weight: 700;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .ob-muted {
      display: block;
      color: #667085;
      font-size: 12px;
    }

    .ob-money {
      color: #1e2532;
      font-weight: 800;
      white-space: nowrap;
    }

    .ob-status {
      display: inline-flex;
      align-items: center;
      min-height: 26px;
      padding: 6px 10px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 700;
    }

    .ob-status i {
      margin-right: 5px;
    }

    .ob-expand-btn,
    .ob-action-btn {
      width: 34px;
      height: 34px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0;
      border-radius: 6px;
    }

    .ob-actions {
      display: flex;
      flex-wrap: nowrap;
      gap: 6px;
    }

    .ob-detail-row td {
      background: #fbfcfe;
      border-top: 0;
    }

    .ob-detail-grid {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 12px;
      padding: 14px 16px;
      border: 1px solid #eef1f5;
      border-radius: 8px;
      background: #fff;
    }

    .ob-detail-section {
      grid-column: 1 / -1;
      padding-top: 12px;
      border-top: 1px solid #eef1f5;
    }

    .ob-detail-label {
      display: block;
      color: #667085;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
    }

    .ob-detail-value {
      display: block;
      margin-top: 4px;
      color: #1e2532;
      font-weight: 700;
    }

    .ob-mini-list {
      display: grid;
      gap: 8px;
      margin-top: 8px;
    }

    .ob-mini-row {
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto auto;
      gap: 10px;
      align-items: center;
      padding: 8px 10px;
      border: 1px solid #eef1f5;
      border-radius: 7px;
      background: #fbfcfe;
    }

    .ob-mini-title {
      display: block;
      overflow-wrap: anywhere;
      color: #1e2532;
      font-weight: 800;
    }

    .ob-pill {
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

    .ob-progress {
      width: 100%;
      height: 6px;
      overflow: hidden;
      margin-top: 5px;
      border-radius: 999px;
      background: #e7eaf0;
    }

    .ob-progress span {
      display: block;
      height: 100%;
      border-radius: inherit;
      background: #F97316;
    }

    .ob-mobile-list {
      display: grid;
      gap: 12px;
    }

    .ob-mobile-booking {
      padding: 14px;
      border: 1px solid #e7eaf0;
      border-radius: 8px;
      background: #fff;
    }

    .ob-mobile-booking__head {
      display: flex;
      justify-content: space-between;
      gap: 12px;
      margin-bottom: 10px;
    }

    .ob-mobile-booking__title {
      margin-bottom: 2px;
      color: #1e2532;
      font-weight: 800;
    }

    .ob-mobile-booking__grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 10px;
      margin: 12px 0;
    }

    .ob-mobile-extra {
      padding-top: 10px;
      margin-top: 10px;
      border-top: 1px solid #eef1f5;
    }

    .ob-empty {
      padding: 42px 16px;
      text-align: center;
    }

    .ob-empty i {
      color: #9aa4b2;
      font-size: 34px;
    }

    .ob-empty h3 {
      margin-top: 14px;
      color: #1e2532;
      font-size: 18px;
      font-weight: 800;
    }

    @media (max-width: 1199px) {
      .ob-summary,
      .ob-detail-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    @media (max-width: 767px) {
      .ob-summary,
      .ob-detail-grid,
      .ob-mobile-booking__grid,
      .ob-mini-row {
        grid-template-columns: 1fr;
      }

      .ob-type-summary__body {
        padding: 12px;
      }

      .ob-event-summary-list {
        gap: 12px;
      }

      .ob-event-summary-card__head {
        grid-template-columns: 1fr;
        gap: 10px;
        padding: 14px;
      }

      .ob-event-summary-card__title {
        margin-bottom: 10px;
        font-size: 15px;
      }

      .ob-event-summary-card__status {
        justify-self: start;
      }

      .ob-event-summary-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
        padding: 12px;
        background: #fbfcfe;
      }

      .ob-event-summary-stat {
        padding: 11px 12px;
        border: 1px solid #eef1f5;
        border-radius: 9px;
        background: #fff;
      }

      .ob-event-summary-stat strong {
        font-size: 17px;
      }

      .ob-metric {
        min-height: 82px;
        padding: 12px;
      }

      .ob-metric__value {
        font-size: 20px;
      }

      .ob-type-summary__head {
        flex-direction: column;
      }

      .ob-type-table {
        padding: 10px;
        border-top: 1px solid #eef1f5;
        background: #fbfcfe;
        font-size: 12px;
      }

      .ob-type-table,
      .ob-type-table thead,
      .ob-type-table tbody,
      .ob-type-table tr,
      .ob-type-table th,
      .ob-type-table td {
        display: block;
        width: 100%;
      }

      .ob-type-table thead {
        position: absolute;
        width: 1px;
        height: 1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
      }

      .ob-type-table tbody {
        display: grid;
        gap: 10px;
      }

      .ob-type-table tr {
        padding: 10px 12px;
        border: 1px solid #eef1f5;
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 8px 18px rgba(30, 37, 50, .04);
      }

      .ob-type-table td {
        display: grid;
        grid-template-columns: minmax(92px, 38%) minmax(0, 1fr);
        gap: 10px;
        align-items: center;
        min-height: 28px;
        padding: 6px 0;
        border-top: 0;
      }

      .ob-type-table td:first-child {
        display: block;
        min-height: 0;
        margin-bottom: 4px;
        padding: 0 0 9px;
        border-bottom: 1px solid #f1f3f7;
      }

      .ob-type-table td:first-child::before {
        display: none;
      }

      .ob-type-table td::before {
        content: attr(data-label);
        color: #667085;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
      }

      .ob-type-table td:not(:first-child) {
        color: #1e2532;
        font-weight: 700;
      }

      .ob-type-name {
        font-size: 13px;
        line-height: 1.3;
      }

      .ob-progress {
        max-width: 180px;
      }
    }

    @media (max-width: 360px) {
      .ob-event-summary-stats {
        grid-template-columns: 1fr;
      }
    }
  </style>
@endsection

@section('content')
  @php
    $formatBaseMoney = function ($amount) use ($currencySettings) {
        $symbol = optional($currencySettings)->base_currency_symbol;
        $position = optional($currencySettings)->base_currency_symbol_position;
        $amount = number_format((float) $amount, 0, ',', '.');
        return ($position == 'left' ? $symbol : '') . $amount . ($position == 'right' ? $symbol : '');
    };
    $statusOptions = [
        'completed' => ['label' => __('Completado'), 'class' => 'success', 'icon' => 'fa-check-circle'],
        'pending' => ['label' => __('Pendiente'), 'class' => 'warning text-dark', 'icon' => 'fa-clock'],
        'rejected' => ['label' => __('Rechazado'), 'class' => 'danger', 'icon' => 'fa-times-circle'],
        'free' => ['label' => __('Gratis'), 'class' => 'primary', 'icon' => 'fa-gift'],
    ];
  @endphp

  <div class="organizer-booking-admin">
    <div class="page-header">
      <h4 class="page-title">{{ __('Reservas de eventos') }}</h4>
      <ul class="breadcrumbs">
        <li class="nav-home">
          <a href="{{ route('organizer.dashboard') }}" aria-label="{{ __('Ir al panel') }}">
            <i class="flaticon-home"></i>
          </a>
        </li>
        <li class="separator">
          <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
          <a href="#">{{ __('Reservas') }}</a>
        </li>
      </ul>
    </div>

    <div class="ob-summary" aria-label="{{ __('Resumen de reservas') }}">
      <div class="ob-metric">
        <div class="ob-metric__label">{{ __('Reservas') }}</div>
        <div class="ob-metric__value">{{ number_format($kpis['total'] ?? 0, 0, ',', '.') }}</div>
      </div>
      <div class="ob-metric">
        <div class="ob-metric__label">{{ __('Total cobrado') }}</div>
        <div class="ob-metric__value">{{ $formatBaseMoney($kpis['charged'] ?? 0) }}</div>
        <div class="ob-metric__hint">{{ __('Lo que pagaron los clientes') }}</div>
      </div>
      <div class="ob-metric">
        <div class="ob-metric__label">{{ __('Recibís') }}</div>
        <div class="ob-metric__value">{{ $formatBaseMoney($kpis['organizer_net'] ?? 0) }}</div>
        <div class="ob-metric__hint">{{ __('Entradas menos comisión') }}</div>
      </div>
      <div class="ob-metric">
        <div class="ob-metric__label">{{ __('Completadas') }}</div>
        <div class="ob-metric__value">{{ number_format($kpis['completed'] ?? 0, 0, ',', '.') }}</div>
      </div>
      <div class="ob-metric">
        <div class="ob-metric__label">{{ __('Pendientes') }}</div>
        <div class="ob-metric__value">{{ number_format($kpis['pending'] ?? 0, 0, ',', '.') }}</div>
      </div>
      <div class="ob-metric">
        <div class="ob-metric__label">{{ __('Gratis') }}</div>
        <div class="ob-metric__value">{{ number_format($kpis['free'] ?? 0, 0, ',', '.') }}</div>
      </div>
    </div>

    <section class="ob-type-summary" aria-labelledby="organizerTicketTypeSummaryTitle">
      <div class="ob-type-summary__head">
        <div>
          <h2 id="organizerTicketTypeSummaryTitle" class="ob-type-summary__title">{{ __('Ventas por evento y tipo de entrada') }}</h2>
          <div class="ob-muted">{{ __('Ordenado por fecha del evento; respeta los filtros aplicados.') }}</div>
        </div>
        <div class="ob-muted">{{ __('Vendido') }} = {{ __('completado') }} + {{ __('gratis') }}</div>
      </div>
      <div class="ob-type-summary__body">
        @if (empty($ticketSalesByEvent ?? []))
          <div class="ob-empty py-3">
            <p class="text-muted mb-0">{{ __('No hay entradas para resumir con estos filtros.') }}</p>
          </div>
        @else
          <div class="ob-event-summary-list">
            @foreach ($ticketSalesByEvent as $eventSummary)
              <article class="ob-event-summary-card">
                <div class="ob-event-summary-card__head">
                  <div>
                    <h3 class="ob-event-summary-card__title">{{ $eventSummary['event_title'] }}</h3>
                    <div class="ob-event-summary-card__meta">
                      <span class="ob-event-summary-card__date">{{ $eventSummary['date_label'] }}</span>
                      <span>{{ number_format($eventSummary['bookings_count'], 0, ',', '.') }} {{ __('reservas') }}</span>
                      <span>{{ count($eventSummary['tickets']) }} {{ __('tipos de entrada') }}</span>
                    </div>
                  </div>
                  <span class="ob-event-summary-card__status">{{ $eventSummary['date_status'] }}</span>
                </div>

                <div class="ob-event-summary-stats" aria-label="{{ __('Totales del evento') }}">
                  <div class="ob-event-summary-stat">
                    <span>{{ __('Entradas vendidas') }}</span>
                    <strong>{{ number_format($eventSummary['sold'], 0, ',', '.') }}</strong>
                  </div>
                  <div class="ob-event-summary-stat">
                    <span>{{ __('Pendientes') }}</span>
                    <strong>{{ number_format($eventSummary['pending'], 0, ',', '.') }}</strong>
                  </div>
                  <div class="ob-event-summary-stat">
                    <span>{{ __('Rechazadas') }}</span>
                    <strong>{{ number_format($eventSummary['rejected'], 0, ',', '.') }}</strong>
                  </div>
                  <div class="ob-event-summary-stat">
                    <span>{{ __('Escaneadas') }}</span>
                    <strong>{{ number_format($eventSummary['scanned'], 0, ',', '.') }}/{{ number_format($eventSummary['total'], 0, ',', '.') }}</strong>
                  </div>
                  <div class="ob-event-summary-stat">
                    <span>{{ __('Neto estimado') }}</span>
                    <strong>{{ $formatBaseMoney($eventSummary['organizer_amount']) }}</strong>
                  </div>
                </div>

                <table class="table ob-type-table">
                  <colgroup>
                    <col class="ob-type-table__ticket">
                    <col class="ob-type-table__counts">
                    <col class="ob-type-table__counts">
                    <col class="ob-type-table__counts">
                    <col class="ob-type-table__scan">
                    <col class="ob-type-table__money">
                  </colgroup>
                  <thead>
                    <tr>
                      <th scope="col">{{ __('Entrada') }}</th>
                      <th scope="col">{{ __('Vendidas') }}</th>
                      <th scope="col">{{ __('Pendientes') }}</th>
                      <th scope="col">{{ __('Rechazadas') }}</th>
                      <th scope="col">{{ __('Escaneo') }}</th>
                      <th scope="col">{{ __('Ingresos') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($eventSummary['tickets'] as $summaryRow)
                      <tr>
                        <td data-label="{{ __('Entrada') }}">
                          <span class="ob-type-name">{{ $summaryRow['ticket_name'] }}</span>
                        </td>
                        <td data-label="{{ __('Vendidas') }}"><span class="ob-pill">{{ number_format($summaryRow['sold'], 0, ',', '.') }}</span></td>
                        <td data-label="{{ __('Pendientes') }}">{{ number_format($summaryRow['pending'], 0, ',', '.') }}</td>
                        <td data-label="{{ __('Rechazadas') }}">{{ number_format($summaryRow['rejected'], 0, ',', '.') }}</td>
                        <td data-label="{{ __('Escaneo') }}">
                          <strong>{{ number_format($summaryRow['scanned'], 0, ',', '.') }}/{{ number_format($summaryRow['total'], 0, ',', '.') }}</strong>
                          <div class="ob-progress" aria-hidden="true"><span style="width: {{ $summaryRow['scan_percent'] }}%"></span></div>
                        </td>
                        <td data-label="{{ __('Ingresos') }}"><span class="ob-money">{{ $formatBaseMoney($summaryRow['organizer_amount']) }}</span></td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </article>
            @endforeach
          </div>
        @endif
      </div>
    </section>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header ob-toolbar">
            <form id="organizerBookingFiltersForm" action="{{ route('organizer.event.booking') }}" method="GET">
              <div class="row align-items-end">
                <div class="col-lg-4">
                  <div class="card-title mb-2">{{ __('Reservas') }}</div>
                  <button class="btn btn-danger btn-sm d-none bulk-delete"
                    data-href="{{ route('organizer.event_booking.bulk_delete') }}" type="button">
                    <i class="flaticon-interface-5" aria-hidden="true"></i> {{ __('Eliminar') }}
                  </button>
                </div>
                <div class="col-lg-3">
                  <div class="form-group px-0">
                    <label for="organizerBookingId">{{ __('Reserva') }}</label>
                    <input id="organizerBookingId" name="booking_id" type="text" class="form-control"
                      placeholder="{{ __('Buscar por reserva') }}" value="{{ request()->input('booking_id') }}">
                  </div>
                </div>
                <div class="col-lg-3">
                  <div class="form-group px-0">
                    <label for="organizerEventTitle">{{ __('Evento') }}</label>
                    <input id="organizerEventTitle" name="event_title" type="text" class="form-control"
                      placeholder="{{ __('Buscar por evento') }}" value="{{ request()->input('event_title') }}">
                  </div>
                </div>
                <div class="col-lg-2">
                  <div class="form-group px-0">
                    <label for="organizerPaymentStatus">{{ __('Pago') }}</label>
                    <select id="organizerPaymentStatus" class="form-control" name="status"
                      onchange="document.getElementById('organizerBookingFiltersForm').submit()">
                      <option value="" {{ empty(request()->input('status')) ? 'selected' : '' }}>{{ __('Todos') }}</option>
                      <option value="completed" {{ request()->input('status') == 'completed' ? 'selected' : '' }}>{{ __('Completado') }}</option>
                      <option value="pending" {{ request()->input('status') == 'pending' ? 'selected' : '' }}>{{ __('Pendiente') }}</option>
                      <option value="free" {{ request()->input('status') == 'free' ? 'selected' : '' }}>{{ __('Gratis') }}</option>
                      <option value="rejected" {{ request()->input('status') == 'rejected' ? 'selected' : '' }}>{{ __('Rechazado') }}</option>
                    </select>
                  </div>
                </div>
              </div>
            </form>
          </div>

          <div class="card-body">
            @if (count($bookings) == 0)
              <div class="ob-empty">
                <i class="fas fa-ticket-alt" aria-hidden="true"></i>
                <h3>{{ __('No se encontraron reservas') }}</h3>
                <p class="text-muted mb-0">{{ __('Probá ajustar los filtros de búsqueda.') }}</p>
              </div>
            @else
              <div class="table-responsive d-none d-lg-block">
                <table class="table ob-table">
                  <thead>
                    <tr>
                      <th scope="col">
                        <input type="checkbox" class="bulk-check" data-val="all" aria-label="{{ __('Seleccionar todas las reservas') }}">
                      </th>
                      <th scope="col">{{ __('Reserva') }}</th>
                      <th scope="col">{{ __('Evento') }}</th>
                      <th scope="col">{{ __('Cliente') }}</th>
                      <th scope="col">{{ __('Importe') }}</th>
                      <th scope="col">{{ __('Pago') }}</th>
                      <th scope="col">{{ __('Escaneo') }}</th>
                      <th scope="col">{{ __('Acciones') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($bookings as $booking)
                      @php
                        $eventInfo = $eventInfos[$booking->event_id] ?? null;
                        $title = $eventInfo ? $eventInfo->title : '-';
                        $slug = $eventInfo ? $eventInfo->slug : '';
                        $customer = $booking->customerInfo;
                        $position = $booking->currencyTextPosition;
                        $symbol = $booking->currencySymbol;
                        $formatMoney = function ($amount) use ($position, $symbol) {
                            $amount = number_format((float) $amount, 0, ',', '.');
                            return ($position == 'left' ? $symbol : '') . $amount . ($position == 'right' ? $symbol : '');
                        };
                        $paidTotal = ($booking->price ?? 0) + ($booking->tax ?? 0);
                        $organizerTotal = ($booking->price ?? 0) - ($booking->commission ?? 0);
                        $ticketBreakdown = $booking->ticketBreakdown();
                        $addonBreakdown = $booking->addonBreakdown();
                        $addonsCount = collect($addonBreakdown)->sum('quantity');
                        $addonsTotal = collect($addonBreakdown)->sum('subtotal');
                        $scannedCount = $booking->scannedTicketsCount();
                        $pendingScanCount = $booking->pendingTicketsCount();
                        $scanPercent = $booking->scanPercent();
                        $eventDateLabel = !empty($booking->event_date) ? \Carbon\Carbon::parse($booking->event_date)->format('d/m/Y H:i') : '-';
                        $status = $statusOptions[$booking->paymentStatus] ?? ['label' => ucfirst((string) $booking->paymentStatus), 'class' => 'secondary', 'icon' => 'fa-circle'];
                        $invoiceExtension = pathinfo((string) $booking->invoice, PATHINFO_EXTENSION);
                        $hasInvoiceFile = $invoiceExtension == 'pdf' && $booking->hasInvoiceFile();
                      @endphp
                      <tr>
                        <td>
                          <input type="checkbox" class="bulk-check" data-val="{{ $booking->id }}"
                            aria-label="{{ __('Seleccionar reserva') }} #{{ $booking->booking_id }}">
                        </td>
                        <td>
                          <button class="btn btn-light ob-expand-btn mr-1" type="button"
                            data-target="#organizerBookingDetail{{ $booking->id }}" aria-expanded="false"
                            aria-controls="organizerBookingDetail{{ $booking->id }}"
                            aria-label="{{ __('Ver datos adicionales de la reserva') }} #{{ $booking->booking_id }}">
                            <i class="fas fa-chevron-down" aria-hidden="true"></i>
                          </button>
                          <strong>#{{ Str::limit($booking->booking_id, 12, '') }}</strong>
                          <span class="ob-muted">{{ optional($booking->created_at)->format('d/m/Y H:i') }}</span>
                        </td>
                        <td>
                          @if ($eventInfo)
                            <a class="ob-title" href="{{ route('event.details', ['slug' => $slug, 'id' => $eventInfo->event_id]) }}"
                              target="_blank" rel="noopener" title="{{ $title }}">{{ $title }}</a>
                          @else
                            <span class="ob-title">-</span>
                          @endif
                          <span class="ob-muted">{{ __('Función') }}: {{ $eventDateLabel }}</span>
                        </td>
                        <td>
                          @if ($customer)
                            {{ $customer->fname }} {{ $customer->lname }}
                          @elseif (is_null($booking->customer_id))
                            {{ __('Invitado') }}
                          @else
                            -
                          @endif
                          <span class="ob-muted">{{ $booking->email ?: '-' }}</span>
                        </td>
                        <td>
                          <div class="ob-money">{{ $formatMoney($paidTotal) }}</div>
                          <span class="ob-muted">{{ __('Recibís') }}: {{ $formatMoney($organizerTotal) }}</span>
                        </td>
                        <td>
                          <span class="badge badge-{{ $status['class'] }} ob-status">
                            <i class="fas {{ $status['icon'] }}" aria-hidden="true"></i>{{ $status['label'] }}
                          </span>
                          <span class="ob-muted">{{ $booking->paymentMethod ?: '-' }}</span>
                        </td>
                        <td>
                          @if ((int) $booking->quantity <= 0)
                            <strong>{{ __('Datos incompletos') }}</strong>
                            <span class="ob-muted">{{ __('Sin entradas registradas') }}</span>
                          @else
                            <strong>{{ $scannedCount }}/{{ $booking->quantity }}</strong>
                            <span class="ob-muted">{{ __('Faltan') }}: {{ $pendingScanCount }}</span>
                          @endif
                          <div class="ob-progress" aria-hidden="true"><span style="width: {{ $scanPercent }}%"></span></div>
                        </td>
                        <td>
                          <div class="ob-actions">
                            <a href="{{ route('organizer.event_booking.details', ['id' => $booking->id]) }}"
                              class="btn btn-outline-primary ob-action-btn" title="{{ __('Ver detalles') }}"
                              aria-label="{{ __('Ver detalles de la reserva') }} #{{ $booking->booking_id }}">
                              <i class="fas fa-eye" aria-hidden="true"></i>
                            </a>
                            @if ($hasInvoiceFile)
                              <a href="{{ route('booking.ticket.download', $booking->id) }}"
                                class="btn btn-outline-secondary ob-action-btn" target="_blank" rel="noopener" title="{{ __('Descargar entrada') }}"
                                aria-label="{{ __('Descargar entrada de la reserva') }} #{{ $booking->booking_id }}">
                                <i class="fas fa-file-pdf" aria-hidden="true"></i>
                              </a>
                            @endif
                            @if (!is_null($booking->attachmentFile))
                              <a href="#" data-toggle="modal" data-target="#attachmentModal-{{ $booking->id }}"
                                class="btn btn-outline-info ob-action-btn" title="{{ __('Ver comprobante') }}"
                                aria-label="{{ __('Ver comprobante de la reserva') }} #{{ $booking->booking_id }}">
                                <i class="fas fa-paperclip" aria-hidden="true"></i>
                              </a>
                            @endif
                            <form class="deleteForm d-inline-block" action="{{ route('organizer.event_booking.delete', ['id' => $booking->id]) }}" method="post">
                              @csrf
                              <button type="submit" class="btn btn-outline-danger ob-action-btn deleteBtn"
                                title="{{ __('Eliminar') }}" aria-label="{{ __('Eliminar reserva') }} #{{ $booking->booking_id }}">
                                <i class="fas fa-trash" aria-hidden="true"></i>
                              </button>
                            </form>
                          </div>
                        </td>
                      </tr>
                      <tr id="organizerBookingDetail{{ $booking->id }}" class="ob-detail-row d-none">
                        <td colspan="8">
                          <div class="ob-detail-grid">
                            <div>
                              <span class="ob-detail-label">{{ __('Método de pago') }}</span>
                              <span class="ob-detail-value">{{ $booking->paymentMethod ?: '-' }}</span>
                            </div>
                            <div>
                              <span class="ob-detail-label">{{ __('Fecha / función') }}</span>
                              <span class="ob-detail-value">{{ $eventDateLabel }}</span>
                            </div>
                            <div>
                              <span class="ob-detail-label">{{ __('Comisión') }}</span>
                              <span class="ob-detail-value">{{ $formatMoney($booking->commission ?? 0) }}</span>
                            </div>
                            <div>
                              <span class="ob-detail-label">{{ __('Add-ons') }}</span>
                              <span class="ob-detail-value">{{ $addonsCount > 0 ? $addonsCount . ' - ' . $formatMoney($addonsTotal) : '-' }}</span>
                            </div>
                            <div class="ob-detail-section">
                              <span class="ob-detail-label">{{ __('Tipos de entrada') }}</span>
                              <div class="ob-mini-list">
                                @foreach ($ticketBreakdown as $ticketItem)
                                  <div class="ob-mini-row">
                                    <div>
                                      <span class="ob-mini-title">{{ $ticketItem['name'] }}</span>
                                      <span class="ob-muted">{{ __('Escaneo') }}: {{ $ticketItem['scanned'] }}/{{ $ticketItem['quantity'] }} · {{ __('Faltan') }} {{ $ticketItem['pending'] }}</span>
                                      <div class="ob-progress" aria-hidden="true"><span style="width: {{ $ticketItem['scan_percent'] }}%"></span></div>
                                    </div>
                                    <span class="ob-pill">{{ $ticketItem['quantity'] }} {{ $ticketItem['quantity'] == 1 ? __('entrada') : __('entradas') }}</span>
                                    <span class="ob-detail-value">{{ $formatMoney($ticketItem['subtotal']) }}</span>
                                  </div>
                                @endforeach
                              </div>
                            </div>
                            @if (count($addonBreakdown) > 0)
                              <div class="ob-detail-section">
                                <span class="ob-detail-label">{{ __('Detalle de add-ons') }}</span>
                                <div class="ob-mini-list">
                                  @foreach ($addonBreakdown as $addonItem)
                                    <div class="ob-mini-row">
                                      <div>
                                        <span class="ob-mini-title">{{ $addonItem['title'] }}</span>
                                        <span class="ob-muted">{{ $addonItem['redeemed'] ? __('Canjeado') : __('Pendiente de canje') }}</span>
                                      </div>
                                      <span class="ob-pill">{{ $addonItem['quantity'] }} x {{ $formatMoney($addonItem['unit_price']) }}</span>
                                      <span class="ob-detail-value">{{ $formatMoney($addonItem['subtotal']) }}</span>
                                    </div>
                                  @endforeach
                                </div>
                              </div>
                            @endif
                          </div>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>

              <div class="ob-mobile-list d-lg-none">
                @foreach ($bookings as $booking)
                  @php
                    $eventInfo = $eventInfos[$booking->event_id] ?? null;
                    $title = $eventInfo ? $eventInfo->title : '-';
                    $customer = $booking->customerInfo;
                    $position = $booking->currencyTextPosition;
                    $symbol = $booking->currencySymbol;
                    $formatMoney = function ($amount) use ($position, $symbol) {
                        $amount = number_format((float) $amount, 0, ',', '.');
                        return ($position == 'left' ? $symbol : '') . $amount . ($position == 'right' ? $symbol : '');
                    };
                    $paidTotal = ($booking->price ?? 0) + ($booking->tax ?? 0);
                    $organizerTotal = ($booking->price ?? 0) - ($booking->commission ?? 0);
                    $ticketBreakdown = $booking->ticketBreakdown();
                    $addonBreakdown = $booking->addonBreakdown();
                    $addonsCount = collect($addonBreakdown)->sum('quantity');
                    $addonsTotal = collect($addonBreakdown)->sum('subtotal');
                    $scannedCount = $booking->scannedTicketsCount();
                    $pendingScanCount = $booking->pendingTicketsCount();
                    $scanPercent = $booking->scanPercent();
                    $eventDateLabel = !empty($booking->event_date) ? \Carbon\Carbon::parse($booking->event_date)->format('d/m/Y H:i') : '-';
                    $status = $statusOptions[$booking->paymentStatus] ?? ['label' => ucfirst((string) $booking->paymentStatus), 'class' => 'secondary', 'icon' => 'fa-circle'];
                    $invoiceExtension = pathinfo((string) $booking->invoice, PATHINFO_EXTENSION);
                    $hasInvoiceFile = $invoiceExtension == 'pdf' && $booking->hasInvoiceFile();
                  @endphp
                  <div class="ob-mobile-booking">
                    <div class="ob-mobile-booking__head">
                      <div>
                        <div class="ob-mobile-booking__title">{{ Str::limit($title, 44) }}</div>
                        <span class="ob-muted">#{{ $booking->booking_id }}</span>
                        <span class="ob-muted">{{ __('Función') }}: {{ $eventDateLabel }}</span>
                      </div>
                      <span class="badge badge-{{ $status['class'] }} ob-status">
                        <i class="fas {{ $status['icon'] }}" aria-hidden="true"></i>{{ $status['label'] }}
                      </span>
                    </div>

                    <div class="ob-mobile-booking__grid">
                      <div>
                        <span class="ob-detail-label">{{ __('Cliente') }}</span>
                        <span class="ob-detail-value">
                          @if ($customer)
                            {{ $customer->fname }} {{ $customer->lname }}
                          @elseif (is_null($booking->customer_id))
                            {{ __('Invitado') }}
                          @else
                            -
                          @endif
                        </span>
                      </div>
                      <div>
                        <span class="ob-detail-label">{{ __('Importe') }}</span>
                        <span class="ob-detail-value">{{ $formatMoney($paidTotal) }}</span>
                        <span class="ob-muted">{{ __('Recibís') }}: {{ $formatMoney($organizerTotal) }}</span>
                      </div>
                      <div>
                        <span class="ob-detail-label">{{ __('Escaneo') }}</span>
                        @if ((int) $booking->quantity <= 0)
                          <span class="ob-detail-value">{{ __('Datos incompletos') }}</span>
                          <span class="ob-muted">{{ __('Sin entradas registradas') }}</span>
                        @else
                          <span class="ob-detail-value">{{ $scannedCount }}/{{ $booking->quantity }}</span>
                          <span class="ob-muted">{{ __('Faltan') }}: {{ $pendingScanCount }}</span>
                        @endif
                        <div class="ob-progress" aria-hidden="true"><span style="width: {{ $scanPercent }}%"></span></div>
                      </div>
                      <div>
                        <span class="ob-detail-label">{{ __('Pago') }}</span>
                        <span class="ob-detail-value">{{ $booking->paymentMethod ?: '-' }}</span>
                      </div>
                    </div>

                    <div class="ob-mobile-extra">
                      <span class="ob-detail-label">{{ __('Entradas') }}</span>
                      <div class="ob-mini-list">
                        @foreach ($ticketBreakdown as $ticketItem)
                          <div class="ob-mini-row">
                            <div>
                              <span class="ob-mini-title">{{ $ticketItem['name'] }}</span>
                              <span class="ob-muted">{{ __('Escaneo') }}: {{ $ticketItem['scanned'] }}/{{ $ticketItem['quantity'] }}</span>
                            </div>
                            <span class="ob-pill">{{ $ticketItem['quantity'] }}</span>
                            <span class="ob-detail-value">{{ $formatMoney($ticketItem['subtotal']) }}</span>
                          </div>
                        @endforeach
                      </div>
                    </div>

                    @if (count($addonBreakdown) > 0)
                      <div class="ob-mobile-extra">
                        <span class="ob-detail-label">{{ __('Add-ons') }}: {{ $addonsCount }} · {{ $formatMoney($addonsTotal) }}</span>
                        <div class="ob-mini-list">
                          @foreach ($addonBreakdown as $addonItem)
                            <div class="ob-mini-row">
                              <div>
                                <span class="ob-mini-title">{{ $addonItem['title'] }}</span>
                                <span class="ob-muted">{{ $addonItem['redeemed'] ? __('Canjeado') : __('Pendiente de canje') }}</span>
                              </div>
                              <span class="ob-pill">{{ $addonItem['quantity'] }} x {{ $formatMoney($addonItem['unit_price']) }}</span>
                              <span class="ob-detail-value">{{ $formatMoney($addonItem['subtotal']) }}</span>
                            </div>
                          @endforeach
                        </div>
                      </div>
                    @endif

                    <div class="ob-actions mt-3">
                      <a href="{{ route('organizer.event_booking.details', ['id' => $booking->id]) }}"
                        class="btn btn-outline-primary btn-sm" aria-label="{{ __('Ver detalles de la reserva') }} #{{ $booking->booking_id }}">
                        <i class="fas fa-eye mr-1" aria-hidden="true"></i>{{ __('Ver') }}
                      </a>
                      @if ($hasInvoiceFile)
                        <a href="{{ route('booking.ticket.download', $booking->id) }}" class="btn btn-outline-secondary btn-sm" target="_blank" rel="noopener">
                          <i class="fas fa-file-pdf mr-1" aria-hidden="true"></i>{{ __('Entrada') }}
                        </a>
                      @endif
                      @if (!is_null($booking->attachmentFile))
                        <a href="#" data-toggle="modal" data-target="#attachmentModal-{{ $booking->id }}" class="btn btn-outline-info btn-sm">
                          <i class="fas fa-paperclip mr-1" aria-hidden="true"></i>{{ __('Comprobante') }}
                        </a>
                      @endif
                      <form class="deleteForm d-inline-block" action="{{ route('organizer.event_booking.delete', ['id' => $booking->id]) }}" method="post">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm deleteBtn">
                          <i class="fas fa-trash mr-1" aria-hidden="true"></i>{{ __('Eliminar') }}
                        </button>
                      </form>
                    </div>
                  </div>
                @endforeach
              </div>

              @foreach ($bookings as $booking)
                @includeIf('organizer.event.booking.show-attachment')
              @endforeach
            @endif
          </div>

          @if (count($bookings) > 0)
            <div class="card-footer text-center">
              <div class="d-inline-block mt-3">
                {{ $bookings->links() }}
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection

@section('script')
  <script>
    'use strict';

    (function($) {
      $('.ob-expand-btn').on('click', function() {
        var target = $($(this).data('target'));
        var expanded = $(this).attr('aria-expanded') === 'true';

        target.toggleClass('d-none', expanded);
        $(this).attr('aria-expanded', expanded ? 'false' : 'true');
        $(this).find('i').toggleClass('fa-chevron-down', expanded).toggleClass('fa-chevron-up', !expanded);
      });
    })(jQuery);
  </script>
@endsection
