@extends('backend.layout')

@section('style')
  <style>
    .event-booking-admin {
      color: #1e2532;
    }

    .eb-summary {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 12px;
      margin-bottom: 18px;
    }

    .eb-metric {
      min-height: 96px;
      padding: 16px;
      border: 1px solid #e7eaf0;
      border-radius: 8px;
      background: #fff;
      box-shadow: 0 6px 18px rgba(30, 37, 50, .04);
    }

    .eb-metric__label {
      margin-bottom: 8px;
      color: #64748b;
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
    }

    .eb-metric__value {
      color: #1e2532;
      font-size: 24px;
      font-weight: 800;
      line-height: 1.2;
    }

    .eb-metric__hint {
      margin-top: 5px;
      color: #667085;
      font-size: 11px;
      line-height: 1.35;
    }

    .eb-toolbar {
      border-bottom: 1px solid #eef1f5;
      background: #fbfcfe;
    }

    .eb-type-summary {
      max-width: 100%;
      overflow: hidden;
      margin-bottom: 18px;
      border: 1px solid #e7eaf0;
      border-radius: 8px;
      background: #fff;
      box-shadow: 0 6px 18px rgba(30, 37, 50, .04);
    }

    .eb-type-summary__head {
      display: flex;
      justify-content: space-between;
      gap: 14px;
      padding: 16px 18px;
      border-bottom: 1px solid #eef1f5;
    }

    .eb-type-summary__title {
      margin: 0;
      color: #1e2532;
      font-size: 16px;
      font-weight: 800;
    }

    .eb-type-summary__body {
      padding: 18px;
    }

    .eb-event-summary-list {
      display: grid;
      gap: 14px;
    }

    .eb-event-summary-card {
      overflow: hidden;
      border: 1px solid #e7eaf0;
      border-radius: 10px;
      background: #fff;
    }

    .eb-event-summary-card__head {
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto;
      gap: 16px;
      align-items: start;
      padding: 16px 18px;
      border-bottom: 1px solid #eef1f5;
      background: linear-gradient(180deg, #fbfcfe 0%, #fff 100%);
    }

    .eb-event-summary-card__head > div {
      min-width: 0;
    }

    .eb-event-summary-card__title {
      margin: 0 0 7px;
      color: #1e2532;
      font-size: 16px;
      font-weight: 800;
      line-height: 1.25;
    }

    .eb-event-summary-card__meta {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      color: #667085;
      font-size: 12px;
      font-weight: 600;
    }

    .eb-event-summary-card__meta span {
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

    .eb-event-summary-card__date {
      color: #f05a28;
      font-weight: 800;
    }

    .eb-event-summary-card__status {
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

    .eb-event-summary-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(124px, 1fr));
      gap: 1px;
      padding: 1px;
      border-bottom: 1px solid #eef1f5;
      background: #eef1f5;
    }

    .eb-event-summary-stat {
      min-width: 0;
      padding: 12px 14px;
      background: #fbfcfe;
    }

    .eb-event-summary-stat span {
      display: block;
      color: #667085;
      font-size: 10px;
      font-weight: 800;
      text-transform: uppercase;
    }

    .eb-event-summary-stat strong {
      display: block;
      margin-top: 3px;
      color: #1e2532;
      font-size: 18px;
      font-weight: 800;
      line-height: 1.15;
      overflow-wrap: anywhere;
    }

    .eb-type-summary__formula {
      padding: 10px 18px;
      border-bottom: 1px solid #eef1f5;
      background: #fbfcfe;
      color: #667085;
      font-size: 12px;
    }

    .eb-type-table {
      width: 100%;
      table-layout: fixed;
      margin-bottom: 0;
      font-size: 11px;
    }

    .eb-type-table th {
      border-top: 0;
      color: #667085;
      font-size: 10px;
      line-height: 1.25;
      padding: 8px 6px;
      text-transform: uppercase;
      white-space: normal;
    }

    .eb-type-table td {
      padding: 9px 6px;
      vertical-align: middle;
      line-height: 1.35;
      overflow-wrap: anywhere;
    }

    .eb-type-table__ticket {
      width: 43%;
    }

    .eb-type-table__counts {
      width: 10%;
    }

    .eb-type-table__scan {
      width: 15%;
    }

    .eb-type-table__money {
      width: 12%;
    }

    .eb-type-lines {
      display: grid;
      gap: 2px;
    }

    .eb-type-line {
      display: flex;
      justify-content: space-between;
      gap: 6px;
      min-width: 0;
    }

    .eb-type-line span:first-child {
      color: #667085;
    }

    .eb-type-line strong,
    .eb-type-line span:last-child {
      color: #1e2532;
      font-weight: 800;
    }

    .eb-type-name {
      display: block;
      color: #1e2532;
      font-weight: 800;
      overflow-wrap: anywhere;
    }

    .eb-type-event {
      display: block;
      max-width: 100%;
      overflow: hidden;
      color: #667085;
      font-size: 12px;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .eb-toolbar .form-group {
      margin-bottom: 12px;
    }

    .eb-table {
      margin-bottom: 0;
    }

    .eb-table th {
      border-top: 0;
      color: #667085;
      font-size: 12px;
      text-transform: uppercase;
      white-space: nowrap;
    }

    .eb-table td {
      vertical-align: middle;
    }

    .eb-title {
      display: block;
      max-width: 280px;
      overflow: hidden;
      color: #1e2532;
      font-weight: 700;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .eb-muted {
      color: #667085;
      font-size: 12px;
    }

    .eb-money {
      color: #1e2532;
      font-weight: 800;
      overflow-wrap: anywhere;
    }

    .eb-status {
      display: inline-flex;
      align-items: center;
      min-height: 26px;
      padding: 6px 10px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 700;
    }

    .eb-status i {
      margin-right: 5px;
    }

    .eb-expand-btn,
    .eb-action-btn {
      width: 34px;
      height: 34px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0;
      border-radius: 6px;
    }

    .eb-actions {
      display: flex;
      flex-wrap: nowrap;
      gap: 6px;
    }

    .eb-detail-row td {
      background: #fbfcfe;
      border-top: 0;
    }

    .eb-detail-grid {
      display: grid;
      grid-template-columns: repeat(5, minmax(0, 1fr));
      gap: 12px;
      padding: 14px 16px;
      border: 1px solid #eef1f5;
      border-radius: 8px;
      background: #fff;
    }

    .eb-detail-section {
      grid-column: 1 / -1;
      padding-top: 12px;
      border-top: 1px solid #eef1f5;
    }

    .eb-mini-list {
      display: grid;
      gap: 8px;
      margin-top: 8px;
    }

    .eb-mini-row {
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto auto;
      gap: 10px;
      align-items: center;
      padding: 8px 10px;
      border: 1px solid #eef1f5;
      border-radius: 7px;
      background: #fbfcfe;
    }

    .eb-mini-title {
      display: block;
      overflow-wrap: anywhere;
      color: #1e2532;
      font-weight: 800;
    }

    .eb-pill {
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

    .eb-scan-cell {
      min-width: 120px;
    }

    .eb-progress {
      width: 100%;
      height: 6px;
      overflow: hidden;
      margin-top: 5px;
      border-radius: 999px;
      background: #e7eaf0;
    }

    .eb-progress span {
      display: block;
      height: 100%;
      border-radius: inherit;
      background: #F97316;
    }

    .eb-mobile-extra {
      padding-top: 10px;
      margin-top: 10px;
      border-top: 1px solid #eef1f5;
    }

    .eb-detail-label {
      display: block;
      color: #667085;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
    }

    .eb-detail-value {
      display: block;
      margin-top: 4px;
      color: #1e2532;
      font-weight: 700;
    }

    .eb-mobile-list {
      display: grid;
      gap: 12px;
    }

    .eb-mobile-booking {
      padding: 14px;
      border: 1px solid #e7eaf0;
      border-radius: 8px;
      background: #fff;
    }

    .eb-mobile-booking__head {
      display: flex;
      justify-content: space-between;
      gap: 12px;
      margin-bottom: 10px;
    }

    .eb-mobile-booking__title {
      margin-bottom: 2px;
      color: #1e2532;
      font-weight: 800;
    }

    .eb-mobile-booking__grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 10px;
      margin: 12px 0;
    }

    .eb-empty {
      padding: 42px 16px;
      text-align: center;
    }

    .eb-empty i {
      color: #9aa4b2;
      font-size: 34px;
    }

    .eb-empty h3 {
      margin-top: 14px;
      color: #1e2532;
      font-size: 18px;
      font-weight: 800;
    }

    @media (max-width: 1199px) {
      .eb-summary,
      .eb-detail-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
      }
    }

    @media (max-width: 767px) {
      .eb-summary,
      .eb-detail-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      .eb-type-summary__body {
        padding: 12px;
      }

      .eb-event-summary-list {
        gap: 12px;
      }

      .eb-event-summary-card__head {
        grid-template-columns: 1fr;
        gap: 10px;
        padding: 14px;
      }

      .eb-event-summary-card__title {
        margin-bottom: 10px;
        font-size: 15px;
      }

      .eb-event-summary-card__status {
        justify-self: start;
      }

      .eb-event-summary-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
        padding: 12px;
        background: #fbfcfe;
      }

      .eb-event-summary-stat {
        padding: 11px 12px;
        border: 1px solid #eef1f5;
        border-radius: 9px;
        background: #fff;
      }

      .eb-event-summary-stat strong {
        font-size: 17px;
      }

      .eb-mini-row {
        grid-template-columns: 1fr;
      }

      .eb-metric {
        min-height: 82px;
        padding: 12px;
      }

      .eb-metric__value {
        font-size: 20px;
      }

      .eb-type-summary__head {
        flex-direction: column;
      }
    }

    @media (max-width: 480px) {
      .eb-summary,
      .eb-detail-grid,
      .eb-mobile-booking__grid {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 360px) {
      .eb-event-summary-stats {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 767px) {
      .eb-type-table,
      .eb-type-table thead,
      .eb-type-table tbody,
      .eb-type-table tr,
      .eb-type-table th,
      .eb-type-table td {
        display: block;
        width: 100%;
      }

      .eb-type-table {
        padding: 10px;
        border-top: 1px solid #eef1f5;
        background: #fbfcfe;
        font-size: 12px;
      }

      .eb-type-table thead {
        position: absolute;
        width: 1px;
        height: 1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
      }

      .eb-type-table tbody {
        display: grid;
        gap: 10px;
      }

      .eb-type-table tr {
        padding: 10px 12px;
        border: 1px solid #eef1f5;
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 8px 18px rgba(30, 37, 50, .04);
      }

      .eb-type-table td {
        display: grid;
        grid-template-columns: minmax(92px, 38%) minmax(0, 1fr);
        gap: 10px;
        align-items: center;
        min-height: 28px;
        padding: 6px 0;
        border-top: 0;
      }

      .eb-type-table td:first-child {
        display: block;
        min-height: 0;
        margin-bottom: 4px;
        padding: 0 0 9px;
        border-bottom: 1px solid #f1f3f7;
      }

      .eb-type-table td:first-child::before {
        display: none;
      }

      .eb-type-table td::before {
        content: attr(data-label);
        color: #667085;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
      }

      .eb-type-table td:not(:first-child) {
        color: #1e2532;
        font-weight: 700;
      }

      .eb-type-name {
        font-size: 13px;
        line-height: 1.3;
      }

      .eb-progress {
        max-width: 180px;
      }
    }
  </style>
@endsection

@section('content')
  @php
    $hasAdvancedFilters = request()->filled('status') || request()->filled('from_date') || request()->filled('to_date') || request()->filled('document_number');
    $formatBaseMoney = function ($amount) use ($currencySettings) {
        $symbol = optional($currencySettings)->base_currency_symbol;
        $position = optional($currencySettings)->base_currency_symbol_position;
        $amount = number_format((float) $amount, 0, ',', '.');
        return ($position == 'left' ? $symbol : '') . $amount . ($position == 'right' ? $symbol : '');
    };
    $defaultLanguageCode = optional($defaultLanguage)->code ?: 'es';
    $statusOptions = [
        'completed' => ['label' => __('Completado'), 'class' => 'success', 'icon' => 'fa-check-circle'],
        'pending' => ['label' => __('Pendiente'), 'class' => 'warning text-dark', 'icon' => 'fa-clock'],
        'rejected' => ['label' => __('Rechazado'), 'class' => 'danger', 'icon' => 'fa-times-circle'],
        'free' => ['label' => __('Gratis'), 'class' => 'primary', 'icon' => 'fa-gift'],
    ];
  @endphp

  <div class="event-booking-admin">
    <div class="page-header">
      <h4 class="page-title">{{ __('Reservas de eventos') }}</h4>
      <ul class="breadcrumbs">
        <li class="nav-home">
          <a href="{{ route('admin.dashboard') }}" aria-label="{{ __('Ir al panel') }}">
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

    <div class="eb-summary" aria-label="{{ __('Resumen de reservas') }}">
      <div class="eb-metric">
        <div class="eb-metric__label">{{ __('Reservas') }}</div>
        <div class="eb-metric__value">{{ number_format($kpis['total'] ?? 0, 0, ',', '.') }}</div>
      </div>
      <div class="eb-metric">
        <div class="eb-metric__label">{{ __('Total cobrado') }}</div>
        <div class="eb-metric__value">{{ $formatBaseMoney($kpis['charged'] ?? 0) }}</div>
        <div class="eb-metric__hint">{{ __('Lo que pagó el cliente') }}</div>
      </div>
      <div class="eb-metric">
        <div class="eb-metric__label">{{ __('Base entradas') }}</div>
        <div class="eb-metric__value">{{ $formatBaseMoney($kpis['ticket_revenue'] ?? 0) }}</div>
        <div class="eb-metric__hint">{{ __('Valor de entradas sin cargo de servicio') }}</div>
      </div>
      <div class="eb-metric">
        <div class="eb-metric__label">{{ __('Neto organizadores') }}</div>
        <div class="eb-metric__value">{{ $formatBaseMoney($kpis['organizer_net'] ?? 0) }}</div>
        <div class="eb-metric__hint">{{ __('Base menos comisión descontada') }}</div>
      </div>
      <div class="eb-metric">
        <div class="eb-metric__label">{{ __('Plataforma') }}</div>
        <div class="eb-metric__value">{{ $formatBaseMoney($kpis['platform_earning'] ?? 0) }}</div>
        <div class="eb-metric__hint">{{ __('Cargo de servicio más comisión') }}</div>
      </div>
      <div class="eb-metric">
        <div class="eb-metric__label">{{ __('Completadas') }}</div>
        <div class="eb-metric__value">{{ number_format($kpis['completed'] ?? 0, 0, ',', '.') }}</div>
      </div>
      <div class="eb-metric">
        <div class="eb-metric__label">{{ __('Pendientes') }}</div>
        <div class="eb-metric__value">{{ number_format($kpis['pending'] ?? 0, 0, ',', '.') }}</div>
      </div>
      <div class="eb-metric">
        <div class="eb-metric__label">{{ __('Gratis') }}</div>
        <div class="eb-metric__value">{{ number_format($kpis['free'] ?? 0, 0, ',', '.') }}</div>
      </div>
    </div>

    <section class="eb-type-summary" aria-labelledby="ticketTypeSummaryTitle">
      <div class="eb-type-summary__head">
        <div>
          <h2 id="ticketTypeSummaryTitle" class="eb-type-summary__title">{{ __('Ventas por evento y tipo de entrada') }}</h2>
          <div class="eb-muted">{{ __('Ordenado por fecha del evento; respeta los filtros aplicados.') }}</div>
        </div>
        <div class="eb-muted">{{ __('Vendido') }} = {{ __('completado') }} + {{ __('gratis') }}</div>
      </div>
      <div class="eb-type-summary__body">
        @if (empty($ticketSalesByEvent ?? []))
          <div class="eb-empty py-3">
            <p class="text-muted mb-0">{{ __('No hay entradas para resumir con estos filtros.') }}</p>
          </div>
        @else
          <div class="eb-event-summary-list">
            @foreach ($ticketSalesByEvent as $eventSummary)
              <article class="eb-event-summary-card">
                <div class="eb-event-summary-card__head">
                  <div>
                    <h3 class="eb-event-summary-card__title">{{ $eventSummary['event_title'] }}</h3>
                    <div class="eb-event-summary-card__meta">
                      <span class="eb-event-summary-card__date">{{ $eventSummary['date_label'] }}</span>
                      <span>{{ number_format($eventSummary['bookings_count'], 0, ',', '.') }} {{ __('reservas') }}</span>
                      <span>{{ count($eventSummary['tickets']) }} {{ __('tipos de entrada') }}</span>
                    </div>
                  </div>
                  <span class="eb-event-summary-card__status">{{ $eventSummary['date_status'] }}</span>
                </div>

                <div class="eb-event-summary-stats" aria-label="{{ __('Totales del evento') }}">
                  <div class="eb-event-summary-stat">
                    <span>{{ __('Entradas vendidas') }}</span>
                    <strong>{{ number_format($eventSummary['sold'], 0, ',', '.') }}</strong>
                  </div>
                  <div class="eb-event-summary-stat">
                    <span>{{ __('Pendientes') }}</span>
                    <strong>{{ number_format($eventSummary['pending'], 0, ',', '.') }}</strong>
                  </div>
                  <div class="eb-event-summary-stat">
                    <span>{{ __('Rechazadas') }}</span>
                    <strong>{{ number_format($eventSummary['rejected'], 0, ',', '.') }}</strong>
                  </div>
                  <div class="eb-event-summary-stat">
                    <span>{{ __('Escaneadas') }}</span>
                    <strong>{{ number_format($eventSummary['scanned'], 0, ',', '.') }}/{{ number_format($eventSummary['total'], 0, ',', '.') }}</strong>
                  </div>
                  <div class="eb-event-summary-stat">
                    <span>{{ __('Base entradas') }}</span>
                    <strong>{{ $formatBaseMoney($eventSummary['revenue']) }}</strong>
                  </div>
                </div>

                <table class="table eb-type-table">
                  <colgroup>
                    <col class="eb-type-table__ticket">
                    <col class="eb-type-table__counts">
                    <col class="eb-type-table__counts">
                    <col class="eb-type-table__counts">
                    <col class="eb-type-table__scan">
                    <col class="eb-type-table__money">
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
                          <span class="eb-type-name">{{ $summaryRow['ticket_name'] }}</span>
                        </td>
                        <td data-label="{{ __('Vendidas') }}"><span class="eb-pill">{{ number_format($summaryRow['sold'], 0, ',', '.') }}</span></td>
                        <td data-label="{{ __('Pendientes') }}">{{ number_format($summaryRow['pending'], 0, ',', '.') }}</td>
                        <td data-label="{{ __('Rechazadas') }}">{{ number_format($summaryRow['rejected'], 0, ',', '.') }}</td>
                        <td data-label="{{ __('Escaneo') }}">
                          <strong>{{ number_format($summaryRow['scanned'], 0, ',', '.') }}/{{ number_format($summaryRow['total'], 0, ',', '.') }}</strong>
                          <div class="eb-progress" aria-hidden="true"><span style="width: {{ $summaryRow['scan_percent'] }}%"></span></div>
                        </td>
                        <td data-label="{{ __('Ingresos') }}"><span class="eb-money">{{ $formatBaseMoney($summaryRow['revenue']) }}</span></td>
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

    <div class="card">
      <div class="card-header eb-toolbar">
        <form id="bookingFiltersForm" action="{{ route('admin.event.booking') }}" method="GET">
          <div class="row align-items-end">
            <div class="col-xl-5 col-lg-6">
              <div class="form-group">
                <label for="bookingSearch">{{ __('Buscar') }}</label>
                <input id="bookingSearch" name="search" type="search" class="form-control"
                  placeholder="{{ __('ID, evento, cliente, email o teléfono') }}"
                  value="{{ request()->input('search', request()->input('booking_id', request()->input('event_title'))) }}"
                  autocomplete="off">
              </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-6">
              <div class="form-group">
                <button class="btn btn-primary btn-block" type="submit">
                  <i class="fas fa-search mr-1" aria-hidden="true"></i>{{ __('Filtrar') }}
                </button>
              </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-6">
              <div class="form-group">
                <button class="btn btn-outline-secondary btn-block" type="button" data-toggle="collapse"
                  data-target="#advancedBookingFilters" aria-expanded="{{ $hasAdvancedFilters ? 'true' : 'false' }}"
                  aria-controls="advancedBookingFilters">
                  <i class="fas fa-sliders-h mr-1" aria-hidden="true"></i>{{ __('Más filtros') }}
                </button>
              </div>
            </div>
            <div class="col-xl-3 text-xl-right">
              <div class="form-group">
                <a href="{{ route('admin.event.booking') }}" class="btn btn-light">
                  {{ __('Limpiar') }}
                </a>
                <button class="btn btn-danger d-none bulk-delete ml-2" type="button"
                  data-href="{{ route('admin.event_booking.bulk_delete') }}">
                  <i class="flaticon-interface-5 mr-1" aria-hidden="true"></i>{{ __('Eliminar') }}
                </button>
              </div>
            </div>
          </div>

          <div id="advancedBookingFilters" class="collapse {{ $hasAdvancedFilters ? 'show' : '' }}">
            <div class="row">
              <div class="col-xl-3 col-md-6">
                <div class="form-group">
                  <label for="bookingStatus">{{ __('Estado del pago') }}</label>
                  <select id="bookingStatus" class="form-control" name="status">
                    <option value="">{{ __('Todos') }}</option>
                    <option value="completed" {{ request()->input('status') == 'completed' ? 'selected' : '' }}>{{ __('Completado') }}</option>
                    <option value="pending" {{ request()->input('status') == 'pending' ? 'selected' : '' }}>{{ __('Pendiente') }}</option>
                    <option value="free" {{ request()->input('status') == 'free' ? 'selected' : '' }}>{{ __('Gratis') }}</option>
                    <option value="rejected" {{ request()->input('status') == 'rejected' ? 'selected' : '' }}>{{ __('Rechazado') }}</option>
                  </select>
                </div>
              </div>
              <div class="col-xl-3 col-md-6">
                <div class="form-group">
                  <label for="bookingFromDate">{{ __('Desde') }}</label>
                  <input id="bookingFromDate" class="form-control" type="date" name="from_date"
                    value="{{ request()->input('from_date') }}">
                </div>
              </div>
              <div class="col-xl-3 col-md-6">
                <div class="form-group">
                  <label for="bookingToDate">{{ __('Hasta') }}</label>
                  <input id="bookingToDate" class="form-control" type="date" name="to_date"
                    value="{{ request()->input('to_date') }}">
                </div>
              </div>
              <div class="col-xl-3 col-md-6">
                <div class="form-group">
                  <label for="bookingDocument">{{ __('Documento') }}</label>
                  <input id="bookingDocument" class="form-control" type="text" name="document_number"
                    value="{{ request()->input('document_number') }}" placeholder="{{ __('DNI, CUIT o CUIL') }}">
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>

      <div class="card-body">
        @if (count($bookings) == 0)
          <div class="eb-empty">
            <i class="fas fa-search" aria-hidden="true"></i>
            <h3>{{ __('No se encontraron reservas') }}</h3>
            <p class="text-muted mb-0">{{ __('Probá limpiar filtros o buscar con otro dato.') }}</p>
          </div>
        @else
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="text-muted small">
              {{ __('Resultados') }}: {{ $bookings->total() }}
            </div>
          </div>

          <div class="table-responsive d-none d-lg-block">
            <table class="table eb-table">
              <thead>
                <tr>
                  <th scope="col">
                    <input type="checkbox" class="bulk-check" data-val="all" aria-label="{{ __('Seleccionar todas las reservas') }}">
                  </th>
                  <th scope="col">{{ __('Reserva') }}</th>
                  <th scope="col">{{ __('Evento') }}</th>
                  <th scope="col">{{ __('Cliente') }}</th>
                  <th scope="col">{{ __('Dinero') }}</th>
                  <th scope="col">{{ __('Estado') }}</th>
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
                    $organizerTotal = $booking->organizer ? (($booking->price ?? 0) - ($booking->commission ?? 0)) : null;
                    $platformTotal = $booking->organizer ? (($booking->tax ?? 0) + ($booking->commission ?? 0)) : $paidTotal;
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
                      <button class="btn btn-light eb-expand-btn mr-1" type="button"
                        data-target="#bookingDetail{{ $booking->id }}"
                        aria-expanded="false" aria-controls="bookingDetail{{ $booking->id }}"
                        aria-label="{{ __('Ver datos adicionales de la reserva') }} #{{ $booking->booking_id }}">
                        <i class="fas fa-chevron-down" aria-hidden="true"></i>
                      </button>
                      <strong>#{{ Str::limit($booking->booking_id, 12, '') }}</strong>
                      <div class="eb-muted">{{ optional($booking->created_at)->format('d/m/Y H:i') }}</div>
                    </td>
                    <td>
                      @if ($eventInfo)
                        <a class="eb-title" href="{{ route('event.details', ['slug' => $slug, 'id' => $eventInfo->event_id]) }}"
                          target="_blank" rel="noopener" title="{{ $title }}">{{ $title }}</a>
                      @else
                        <span class="eb-title">-</span>
                      @endif
                      <span class="eb-muted">{{ __('Organizador') }}:
                        @if ($booking->organizer)
                          <a href="{{ route('admin.organizer_management.organizer_details', ['id' => $booking->organizer_id, 'language' => $defaultLanguageCode]) }}"
                            target="_blank">{{ Str::limit($booking->organizer->username, 22) }}</a>
                        @else
                          {{ __('Admin') }}
                        @endif
                      </span>
                      <span class="eb-muted">{{ __('Función') }}: {{ $eventDateLabel }}</span>
                    </td>
                    <td>
                      @if ($customer)
                        <a href="{{ route('admin.customer_management.customer_details', ['id' => $customer->id, 'language' => $defaultLanguageCode]) }}">
                          {{ $customer->fname }} {{ $customer->lname }}
                        </a>
                      @elseif (is_null($booking->customer_id))
                        {{ __('Invitado') }}
                      @else
                        -
                      @endif
                      <div class="eb-muted">{{ $booking->email ?: '-' }}</div>
                    </td>
                    <td>
                      <div class="eb-money">{{ __('Cobrado') }}: {{ $formatMoney($paidTotal) }}</div>
                      <div class="eb-muted">{{ __('Neto org.') }}: {{ $organizerTotal !== null ? $formatMoney($organizerTotal) : '-' }}</div>
                      <div class="eb-muted">{{ __('Plataforma') }}: {{ $formatMoney($platformTotal) }}</div>
                    </td>
                    <td>
                      @if ($booking->gatewayType == 'offline' && $booking->paymentStatus == 'pending')
                        <form class="paymentStatusForm" action="{{ route('admin.event_booking.update_payment_status', $booking->id) }}" method="post">
                          @csrf
                          <select class="form-control paymentStatusBtn form-control-sm bg-warning text-dark" name="payment_status"
                            aria-label="{{ __('Cambiar estado de pago') }}">
                            <option value="completed">{{ __('Completado') }}</option>
                            <option value="pending" selected>{{ __('Pendiente') }}</option>
                            <option value="rejected">{{ __('Rechazado') }}</option>
                          </select>
                        </form>
                      @else
                        <span class="badge badge-{{ $status['class'] }} eb-status">
                          <i class="fas {{ $status['icon'] }}" aria-hidden="true"></i>{{ $status['label'] }}
                        </span>
                      @endif
                      <div class="eb-muted">{{ $booking->paymentMethod ?: '-' }}</div>
                    </td>
                    <td>
                      <div class="eb-scan-cell">
                        @if ($booking->isFullyScanned())
                          <span class="badge badge-success">{{ __('Completo') }}</span>
                        @elseif ((int) $booking->quantity <= 0)
                          <span class="badge badge-warning text-dark">{{ __('Datos incompletos') }}</span>
                          <div class="eb-muted">{{ __('Sin entradas registradas') }}</div>
                        @else
                          <strong>{{ $scannedCount }}/{{ $booking->quantity }}</strong>
                          <div class="eb-muted">{{ __('Faltan') }}: {{ $pendingScanCount }}</div>
                        @endif
                        <div class="eb-progress" aria-hidden="true"><span style="width: {{ $scanPercent }}%"></span></div>
                      </div>
                    </td>
                    <td>
                      <div class="eb-actions">
                        <a href="{{ route('admin.event_booking.details', ['id' => $booking->id]) }}"
                          class="btn btn-outline-primary eb-action-btn" title="{{ __('Ver detalles') }}"
                          aria-label="{{ __('Ver detalles de la reserva') }} #{{ $booking->booking_id }}">
                          <i class="fas fa-eye" aria-hidden="true"></i>
                        </a>
                        @if ($hasInvoiceFile)
                          <a href="{{ route('booking.ticket.download', $booking->id) }}"
                            class="btn btn-outline-secondary eb-action-btn" target="_blank" rel="noopener" title="{{ __('Descargar entrada') }}"
                            aria-label="{{ __('Descargar entrada de la reserva') }} #{{ $booking->booking_id }}">
                            <i class="fas fa-file-pdf" aria-hidden="true"></i>
                          </a>
                        @endif
                        @if (!is_null($booking->attachmentFile))
                          <a href="#" data-toggle="modal" data-target="#attachmentModal-{{ $booking->id }}"
                            class="btn btn-outline-info eb-action-btn" title="{{ __('Ver comprobante') }}"
                            aria-label="{{ __('Ver comprobante de la reserva') }} #{{ $booking->booking_id }}">
                            <i class="fas fa-paperclip" aria-hidden="true"></i>
                          </a>
                        @endif
                        <form class="deleteForm d-inline-block" action="{{ route('admin.event_booking.delete', ['id' => $booking->id]) }}" method="post">
                          @csrf
                          <button type="submit" class="btn btn-outline-danger eb-action-btn deleteBtn"
                            title="{{ __('Eliminar') }}" aria-label="{{ __('Eliminar reserva') }} #{{ $booking->booking_id }}">
                            <i class="fas fa-trash" aria-hidden="true"></i>
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                  <tr id="bookingDetail{{ $booking->id }}" class="eb-detail-row d-none">
                    <td colspan="8">
                      <div class="eb-detail-grid">
                        <div>
                          <span class="eb-detail-label">{{ __('Método de pago') }}</span>
                          <span class="eb-detail-value">{{ $booking->paymentMethod ?: '-' }}</span>
                        </div>
                        <div>
                          <span class="eb-detail-label">{{ __('Fecha / función') }}</span>
                          <span class="eb-detail-value">{{ $eventDateLabel }}</span>
                        </div>
                        <div>
                          <span class="eb-detail-label">{{ __('Cargo de servicio') }}</span>
                          <span class="eb-detail-value">{{ $formatMoney($booking->tax ?? 0) }} <span class="eb-muted">{{ $booking->tax_percentage ? '(' . $booking->tax_percentage . '%)' : '' }}</span></span>
                        </div>
                        <div>
                          <span class="eb-detail-label">{{ __('Comisión descontada') }}</span>
                          <span class="eb-detail-value">{{ $formatMoney($booking->commission ?? 0) }} <span class="eb-muted">{{ $booking->commission_percentage ? '(' . $booking->commission_percentage . '%)' : '' }}</span></span>
                        </div>
                        <div>
                          <span class="eb-detail-label">{{ __('Add-ons') }}</span>
                          <span class="eb-detail-value">{{ $addonsCount > 0 ? $addonsCount . ' - ' . $formatMoney($addonsTotal) : '-' }}</span>
                        </div>
                        <div>
                          <span class="eb-detail-label">{{ __('Documento') }}</span>
                          <span class="eb-detail-value">{{ $booking->fiscalProfile ? $booking->fiscalProfile->document_type . ' ' . $booking->fiscalProfile->document_number : '-' }}</span>
                        </div>
                        <div>
                          <span class="eb-detail-label">{{ __('Factura ARCA') }}</span>
                          <span class="eb-detail-value">
                            @if ($booking->arcaInvoice)
                              <a href="{{ route('admin.arca_invoices.show', $booking->arcaInvoice->id) }}">{{ strtoupper($booking->arcaInvoice->status) }}</a>
                            @else
                              -
                            @endif
                          </span>
                        </div>
                        <div class="eb-detail-section">
                          <span class="eb-detail-label">{{ __('Tipos de entrada') }}</span>
                          <div class="eb-mini-list">
                            @foreach ($ticketBreakdown as $ticketItem)
                              <div class="eb-mini-row">
                                <div>
                                  <span class="eb-mini-title">{{ $ticketItem['name'] }}</span>
                                  <span class="eb-muted">{{ __('Escaneo') }}: {{ $ticketItem['scanned'] }}/{{ $ticketItem['quantity'] }} · {{ __('Faltan') }} {{ $ticketItem['pending'] }}</span>
                                  <div class="eb-progress" aria-hidden="true"><span style="width: {{ $ticketItem['scan_percent'] }}%"></span></div>
                                </div>
                                <span class="eb-pill">{{ $ticketItem['quantity'] }} {{ $ticketItem['quantity'] == 1 ? __('entrada') : __('entradas') }}</span>
                                <span class="eb-detail-value">{{ $formatMoney($ticketItem['subtotal']) }}</span>
                              </div>
                            @endforeach
                          </div>
                        </div>
                        @if (count($addonBreakdown) > 0)
                          <div class="eb-detail-section">
                            <span class="eb-detail-label">{{ __('Detalle de add-ons') }}</span>
                            <div class="eb-mini-list">
                              @foreach ($addonBreakdown as $addonItem)
                                <div class="eb-mini-row">
                                  <div>
                                    <span class="eb-mini-title">{{ $addonItem['title'] }}</span>
                                    <span class="eb-muted">{{ $addonItem['redeemed'] ? __('Canjeado') : __('Pendiente de canje') }}</span>
                                  </div>
                                  <span class="eb-pill">{{ $addonItem['quantity'] }} x {{ $formatMoney($addonItem['unit_price']) }}</span>
                                  <span class="eb-detail-value">{{ $formatMoney($addonItem['subtotal']) }}</span>
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

          <div class="eb-mobile-list d-lg-none">
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
                $organizerTotal = $booking->organizer ? (($booking->price ?? 0) - ($booking->commission ?? 0)) : null;
                $platformTotal = $booking->organizer ? (($booking->tax ?? 0) + ($booking->commission ?? 0)) : $paidTotal;
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
              <div class="eb-mobile-booking">
                <div class="eb-mobile-booking__head">
                  <div>
                    <div class="eb-mobile-booking__title">{{ Str::limit($title, 44) }}</div>
                    <div class="eb-muted">#{{ $booking->booking_id }}</div>
                    <div class="eb-muted">{{ __('Función') }}: {{ $eventDateLabel }}</div>
                  </div>
                  @if ($booking->gatewayType == 'offline' && $booking->paymentStatus == 'pending')
                    <form class="paymentStatusForm" action="{{ route('admin.event_booking.update_payment_status', $booking->id) }}" method="post">
                      @csrf
                      <select class="form-control paymentStatusBtn form-control-sm bg-warning text-dark" name="payment_status"
                        aria-label="{{ __('Cambiar estado de pago') }}">
                        <option value="completed">{{ __('Completado') }}</option>
                        <option value="pending" selected>{{ __('Pendiente') }}</option>
                        <option value="rejected">{{ __('Rechazado') }}</option>
                      </select>
                    </form>
                  @else
                    <span class="badge badge-{{ $status['class'] }} eb-status">
                      <i class="fas {{ $status['icon'] }}" aria-hidden="true"></i>{{ $status['label'] }}
                    </span>
                  @endif
                </div>

                <div class="eb-mobile-booking__grid">
                  <div>
                    <span class="eb-detail-label">{{ __('Cliente') }}</span>
                    <span class="eb-detail-value">
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
                    <span class="eb-detail-label">{{ __('Total cobrado') }}</span>
                    <span class="eb-detail-value">{{ $formatMoney($paidTotal) }}</span>
                    <span class="eb-muted">{{ __('Neto org.') }}: {{ $organizerTotal !== null ? $formatMoney($organizerTotal) : '-' }}</span>
                    <span class="eb-muted">{{ __('Plataforma') }}: {{ $formatMoney($platformTotal) }}</span>
                  </div>
                  <div>
                    <span class="eb-detail-label">{{ __('Escaneo') }}</span>
                    @if ((int) $booking->quantity <= 0)
                      <span class="eb-detail-value">{{ __('Datos incompletos') }}</span>
                      <span class="eb-muted">{{ __('Sin entradas registradas') }}</span>
                    @else
                      <span class="eb-detail-value">{{ $scannedCount }}/{{ $booking->quantity }}</span>
                      <span class="eb-muted">{{ __('Faltan') }}: {{ $pendingScanCount }}</span>
                    @endif
                    <div class="eb-progress" aria-hidden="true"><span style="width: {{ $scanPercent }}%"></span></div>
                  </div>
                  <div>
                    <span class="eb-detail-label">{{ __('Pago') }}</span>
                    <span class="eb-detail-value">{{ $booking->paymentMethod ?: '-' }}</span>
                  </div>
                </div>

                <div class="eb-mobile-extra">
                  <span class="eb-detail-label">{{ __('Entradas') }}</span>
                  <div class="eb-mini-list">
                    @foreach ($ticketBreakdown as $ticketItem)
                      <div class="eb-mini-row">
                        <div>
                          <span class="eb-mini-title">{{ $ticketItem['name'] }}</span>
                          <span class="eb-muted">{{ __('Escaneo') }}: {{ $ticketItem['scanned'] }}/{{ $ticketItem['quantity'] }}</span>
                        </div>
                        <span class="eb-pill">{{ $ticketItem['quantity'] }}</span>
                        <span class="eb-detail-value">{{ $formatMoney($ticketItem['subtotal']) }}</span>
                      </div>
                    @endforeach
                  </div>
                </div>

                @if (count($addonBreakdown) > 0)
                  <div class="eb-mobile-extra">
                    <span class="eb-detail-label">{{ __('Add-ons') }}: {{ $addonsCount }} · {{ $formatMoney($addonsTotal) }}</span>
                    <div class="eb-mini-list">
                      @foreach ($addonBreakdown as $addonItem)
                        <div class="eb-mini-row">
                          <div>
                            <span class="eb-mini-title">{{ $addonItem['title'] }}</span>
                            <span class="eb-muted">{{ $addonItem['redeemed'] ? __('Canjeado') : __('Pendiente de canje') }}</span>
                          </div>
                          <span class="eb-pill">{{ $addonItem['quantity'] }} x {{ $formatMoney($addonItem['unit_price']) }}</span>
                          <span class="eb-detail-value">{{ $formatMoney($addonItem['subtotal']) }}</span>
                        </div>
                      @endforeach
                    </div>
                  </div>
                @endif

                <div class="eb-actions">
                  <a href="{{ route('admin.event_booking.details', ['id' => $booking->id]) }}"
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
                  <form class="deleteForm d-inline-block" action="{{ route('admin.event_booking.delete', ['id' => $booking->id]) }}" method="post">
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
            @includeIf('backend.event.booking.show-attachment')
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
@endsection

@section('script')
  <script>
    'use strict';

    (function($) {
      var searchTimer = null;
      var initialSearch = $('#bookingSearch').val();

      $('#bookingSearch').on('input', function() {
        var value = $(this).val();

        clearTimeout(searchTimer);

        if (value.length > 0 && value.length < 3) {
          return;
        }

        searchTimer = setTimeout(function() {
          if (value !== initialSearch) {
            $('#bookingFiltersForm').trigger('submit');
          }
        }, 700);
      });

      $('.eb-expand-btn').on('click', function() {
        var target = $($(this).data('target'));
        var expanded = $(this).attr('aria-expanded') === 'true';

        target.toggleClass('d-none', expanded);
        $(this).attr('aria-expanded', expanded ? 'false' : 'true');
        $(this).find('i').toggleClass('fa-chevron-down', expanded).toggleClass('fa-chevron-up', !expanded);
      });
    })(jQuery);
  </script>
@endsection
