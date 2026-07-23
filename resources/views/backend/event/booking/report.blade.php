@extends('backend.layout')

@section('style')
  <style>
    .booking-report-admin {
      --br-ink: #1e2532;
      --br-ink-strong: #111827;
      --br-muted: #667085;
      --br-border: #e4e7ec;
      --br-soft: #f8fafc;
      --br-orange: #f97316;
      --br-orange-dark: #c2410c;
      --br-green: #16a34a;
      --br-blue: #2563eb;
      color: var(--br-ink);
      font-size: 13px;
      line-height: 1.45;
    }

    .booking-report-admin .page-title {
      color: var(--br-ink-strong) !important;
      font-size: 24px !important;
      font-weight: 750 !important;
      line-height: 1.2;
    }

    .booking-report-admin .breadcrumbs,
    .booking-report-admin .breadcrumbs a {
      color: var(--br-muted) !important;
      font-size: 12.5px;
      font-weight: 500;
    }

    .br-filter-card,
    .br-section,
    .br-event-card {
      border: 1px solid var(--br-border);
      border-radius: 8px;
      background: #fff;
      box-shadow: 0 1px 2px rgba(16, 24, 40, .04);
    }

    .br-filter-card {
      margin-bottom: 18px;
      padding: 18px;
    }

    .br-filter-grid {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr)) auto;
      gap: 14px;
      align-items: end;
    }

    .br-filter-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      justify-content: flex-end;
    }

    .br-filter-card label {
      color: #5f6f89;
      font-size: 12px;
      font-weight: 700;
    }

    .br-summary {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 14px;
      margin-bottom: 18px;
    }

    .br-metric {
      --br-accent: #94a3b8;
      --br-soft: #f8fafc;
      position: relative;
      display: grid;
      grid-template-columns: minmax(0, 1fr) 40px;
      gap: 12px;
      min-height: 96px;
      padding: 16px;
      overflow: hidden;
      border: 1px solid #e7eaf0;
      border-radius: 8px;
      background: linear-gradient(180deg, #fff 0%, var(--br-soft) 100%);
      box-shadow: 0 8px 22px rgba(30, 37, 50, .05);
    }

    .br-metric::before {
      position: absolute;
      inset: 0 0 auto;
      height: 4px;
      background: var(--br-accent);
      content: "";
    }

    .br-metric__label {
      margin-bottom: 7px;
      color: #64748b;
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
    }

    .br-metric__value {
      color: #1e2532;
      font-size: 23px;
      font-weight: 900;
      line-height: 1.15;
      overflow-wrap: anywhere;
    }

    .br-metric__hint {
      margin-top: 5px;
      color: #667085;
      font-size: 11px;
      line-height: 1.35;
    }

    .br-metric__icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 40px;
      height: 40px;
      border: 1px solid #e7eaf0;
      border: 1px solid color-mix(in srgb, var(--br-accent) 24%, transparent);
      border-radius: 12px;
      background: #fff;
      background: color-mix(in srgb, var(--br-accent) 10%, #fff);
      color: var(--br-accent);
    }

    .br-metric--primary {
      --br-accent: #f97316;
      --br-soft: #fff7ed;
      border-color: rgba(249, 115, 22, .20);
    }

    .br-metric--money,
    .br-metric--paid {
      --br-accent: #16a34a;
      --br-soft: #f0fdf4;
      border-color: rgba(22, 163, 74, .24);
    }

    .br-metric--pending {
      --br-accent: #f59e0b;
      --br-soft: #fffbeb;
      border-color: rgba(245, 158, 11, .24);
    }

    .br-metric--free {
      --br-accent: #2563eb;
      --br-soft: #eff6ff;
      border-color: rgba(37, 99, 235, .24);
    }

    .br-metric--rejected {
      --br-accent: #dc2626;
      --br-soft: #fff1f2;
      border-color: rgba(220, 38, 38, .18);
    }

    .br-section {
      overflow: hidden;
      margin-bottom: 18px;
    }

    .br-section__head {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 14px;
      padding: 16px 18px;
      border-bottom: 1px solid #eef1f5;
      background: #fbfcfe;
    }

    .br-section__title {
      margin: 0;
      color: var(--br-ink-strong);
      font-size: 17px;
      font-weight: 750;
      line-height: 1.25;
    }

    .br-section__hint {
      margin-top: 4px;
      color: var(--br-muted);
      font-size: 12px;
      line-height: 1.45;
    }

    .br-section__body {
      padding: 18px;
    }

    .br-event-list {
      display: grid;
      gap: 14px;
    }

    .br-event-card {
      --br-event-accent: #16a34a;
      --br-event-soft: #f0fdf4;
      position: relative;
      overflow: hidden;
      box-shadow: none;
    }

    .br-event-card::before {
      position: absolute;
      inset: 0 auto 0 0;
      width: 4px;
      background: var(--br-event-accent);
      content: "";
    }

    .br-event-card--pending {
      --br-event-accent: #f59e0b;
      --br-event-soft: #fffbeb;
    }

    .br-event-card--free {
      --br-event-accent: #2563eb;
      --br-event-soft: #eff6ff;
    }

    .br-event-card--rejected {
      --br-event-accent: #dc2626;
      --br-event-soft: #fff1f2;
    }

    .br-event-card__head {
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto;
      gap: 14px;
      padding: 15px 17px;
      background: linear-gradient(90deg, var(--br-event-soft) 0%, #fff 68%);
      border-bottom: 1px solid #eef1f5;
    }

    .br-event-card__title {
      display: block;
      margin-bottom: 7px;
      color: #1e2532;
      font-size: 15px;
      font-weight: 900;
      line-height: 1.25;
      overflow-wrap: anywhere;
    }

    .br-event-card__meta {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
    }

    .br-chip,
    .br-status {
      display: inline-flex;
      align-items: center;
      min-height: 24px;
      padding: 4px 8px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 800;
      line-height: 1.2;
      white-space: nowrap;
    }

    .br-chip {
      border: 1px solid #eef1f5;
      background: #fff;
      color: #5f6f89;
    }

    .br-event-card__amount {
      min-width: 138px;
      padding: 8px 10px;
      border: 1px solid #e7eaf0;
      border: 1px solid color-mix(in srgb, var(--br-event-accent) 18%, #e7eaf0);
      border-radius: 9px;
      background: #fff;
      text-align: right;
    }

    .br-event-card__amount span {
      display: block;
      color: #667085;
      font-size: 10px;
      font-weight: 800;
      text-transform: uppercase;
    }

    .br-event-card__amount strong {
      display: block;
      color: #1e2532;
      font-size: 17px;
      font-weight: 900;
      line-height: 1.15;
    }

    .br-event-card__stats {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 10px;
      padding: 14px;
      background: #fbfcfe;
    }

    .br-event-stat {
      padding: 10px 12px;
      border: 1px solid #eef1f5;
      border-radius: 9px;
      background: #fff;
    }

    .br-event-stat span {
      display: block;
      color: #667085;
      font-size: 10px;
      font-weight: 800;
      text-transform: uppercase;
    }

    .br-event-stat strong {
      display: block;
      margin-top: 3px;
      color: #1e2532;
      font-size: 18px;
      font-weight: 900;
    }

    .br-status--paid,
    .badge-success.br-status {
      background: #dcfce7 !important;
      color: #166534 !important;
    }

    .br-status--pending,
    .badge-warning.br-status {
      background: #fffbeb !important;
      color: #92400e !important;
    }

    .br-status--free,
    .badge-info.br-status {
      background: #dbeafe !important;
      color: #1d4ed8 !important;
    }

    .br-status--rejected,
    .badge-danger.br-status {
      background: #fee2e2 !important;
      color: #991b1b !important;
    }

    .br-table {
      margin-bottom: 0;
      table-layout: fixed;
      width: 100%;
    }

    .br-detail-table-wrap {
      border-top: 1px solid var(--br-border);
    }

    .br-table th {
      border-top: 0;
      border-bottom: 1px solid var(--br-border);
      background: #edf4f9;
      color: #344054;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: .045em;
      line-height: 1.25;
      padding: 13px 14px;
      text-transform: uppercase;
    }

    .br-table td {
      vertical-align: middle;
      color: var(--br-ink);
      font-size: 13px;
      font-weight: 400;
      line-height: 1.45;
      padding: 14px;
    }

    .br-table th:nth-child(1),
    .br-table td:nth-child(1) {
      width: 15%;
    }

    .br-table th:nth-child(2),
    .br-table td:nth-child(2) {
      width: 28%;
    }

    .br-table th:nth-child(3),
    .br-table td:nth-child(3) {
      width: 20%;
    }

    .br-table th:nth-child(4),
    .br-table td:nth-child(4) {
      width: 14%;
    }

    .br-table th:nth-child(5),
    .br-table td:nth-child(5) {
      width: 10%;
    }

    .br-table th:nth-child(6),
    .br-table td:nth-child(6) {
      width: 9%;
    }

    .br-table th:nth-child(7),
    .br-table td:nth-child(7) {
      width: 4%;
      text-align: center;
    }

    .br-booking-id,
    .br-money {
      color: var(--br-ink-strong);
      font-weight: 750;
    }

    .br-muted {
      color: var(--br-muted);
      font-size: 12px;
      line-height: 1.35;
    }

    .br-event-link {
      display: block;
      overflow: hidden;
      color: var(--br-ink-strong);
      font-weight: 650;
      line-height: 1.35;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .br-event-link:hover {
      color: #c2410c;
      text-decoration: none;
    }

    .br-contact {
      min-width: 180px;
    }

    .br-action-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 34px;
      height: 34px;
      padding: 0;
      border-radius: 8px;
    }

    .br-mobile-list {
      display: grid;
      gap: 12px;
      padding: 14px;
      border-top: 1px solid var(--br-border);
      background: #fbfcfd;
    }

    .br-booking-card {
      display: grid;
      gap: 12px;
      padding: 14px;
      border: 1px solid var(--br-border);
      border-radius: 8px;
      background: #fff;
      box-shadow: 0 1px 2px rgba(16, 24, 40, .04);
    }

    .br-booking-card__top {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 10px;
    }

    .br-booking-card__id {
      color: var(--br-ink-strong);
      font-size: 12.5px;
      font-weight: 750;
      line-height: 1.35;
      overflow-wrap: anywhere;
    }

    .br-booking-card__event {
      color: var(--br-ink-strong);
      font-size: 14px;
      font-weight: 700;
      line-height: 1.35;
      text-decoration: none;
    }

    .br-booking-card__event:hover {
      color: var(--br-orange-dark);
      text-decoration: none;
    }

    .br-booking-card__grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 10px;
      padding-top: 12px;
      border-top: 1px solid #eef1f5;
    }

    .br-booking-card__label {
      display: block;
      margin-bottom: 3px;
      color: var(--br-muted);
      font-size: 10.5px;
      font-weight: 700;
      letter-spacing: .045em;
      text-transform: uppercase;
    }

    .br-booking-card__value {
      color: var(--br-ink);
      font-size: 13px;
      font-weight: 500;
      line-height: 1.35;
      overflow-wrap: anywhere;
    }

    .br-booking-card__footer {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      padding-top: 12px;
      border-top: 1px solid #eef1f5;
    }

    .br-empty {
      padding: 46px 18px;
      text-align: center;
    }

    .br-empty i {
      color: #9aa4b2;
      font-size: 34px;
    }

    .br-empty h3 {
      margin-top: 14px;
      color: #1e2532;
      font-size: 18px;
      font-weight: 900;
    }

    @media (max-width: 1199px) {
      .br-filter-grid,
      .br-summary,
      .br-event-card__stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      .br-filter-actions {
        justify-content: flex-start;
      }
    }

    @media (max-width: 767px) {
      .br-filter-grid,
      .br-summary,
      .br-event-card__stats {
        grid-template-columns: 1fr;
      }

      .br-section__head,
      .br-event-card__head {
        grid-template-columns: 1fr;
      }

      .br-section__head {
        display: block;
      }

      .br-event-card__amount {
        min-width: 0;
        text-align: left;
      }
    }
  </style>
@endsection

@section('content')
  @php
    $formatMoney = function ($amount) use ($abs) {
        $symbol = optional($abs)->base_currency_symbol;
        $position = optional($abs)->base_currency_symbol_position;
        $amount = number_format((float) $amount, 0, ',', '.');
        return ($position == 'left' ? $symbol : '') . $amount . ($position == 'right' ? $symbol : '');
    };

    $allReportBookings = collect(session('booking_report', []));
    $hasFilters = request()->filled('from_date') && request()->filled('to_date');
    $hasReport = $allReportBookings->isNotEmpty();
    $pageBookings = $bookings instanceof \Illuminate\Contracts\Pagination\Paginator ? collect($bookings->items()) : collect($bookings);
    $statusMeta = [
        'completed' => ['label' => __('Pago'), 'class' => 'br-status--paid', 'metric' => 'br-metric--paid', 'event' => 'br-event-card--paid', 'icon' => 'fa-check-circle'],
        'pending' => ['label' => __('Pendiente'), 'class' => 'br-status--pending', 'metric' => 'br-metric--pending', 'event' => 'br-event-card--pending', 'icon' => 'fa-clock'],
        'free' => ['label' => __('Gratis'), 'class' => 'br-status--free', 'metric' => 'br-metric--free', 'event' => 'br-event-card--free', 'icon' => 'fa-gift'],
        'rejected' => ['label' => __('Rechazado'), 'class' => 'br-status--rejected', 'metric' => 'br-metric--rejected', 'event' => 'br-event-card--rejected', 'icon' => 'fa-times-circle'],
    ];
    $statusCounts = $allReportBookings->groupBy('paymentStatus')->map->count();
    $reportTotals = [
        'bookings' => $allReportBookings->count(),
        'quantity' => $allReportBookings->sum(fn ($booking) => (int) $booking->quantity),
        'charged' => $allReportBookings->sum(fn ($booking) => (float) $booking->price),
        'discounts' => $allReportBookings->sum(fn ($booking) => (float) $booking->discount + (float) $booking->early_bird_discount),
    ];
    $eventsReport = $allReportBookings
        ->groupBy('event_id')
        ->map(function ($items) use ($statusMeta) {
            $first = $items->first();
            $dominantStatus = $items->groupBy('paymentStatus')->sortByDesc->count()->keys()->first();
            return [
                'title' => $first->title,
                'slug' => $first->slug,
                'event_id' => $first->event_id,
                'status' => $dominantStatus,
                'status_meta' => $statusMeta[$dominantStatus] ?? $statusMeta['pending'],
                'bookings' => $items->count(),
                'quantity' => $items->sum(fn ($booking) => (int) $booking->quantity),
                'charged' => $items->sum(fn ($booking) => (float) $booking->price),
                'discounts' => $items->sum(fn ($booking) => (float) $booking->discount + (float) $booking->early_bird_discount),
                'completed' => $items->where('paymentStatus', 'completed')->count(),
                'pending' => $items->where('paymentStatus', 'pending')->count(),
                'free' => $items->where('paymentStatus', 'free')->count(),
                'rejected' => $items->where('paymentStatus', 'rejected')->count(),
                'methods' => $items->pluck('paymentMethod')->filter()->unique()->values(),
            ];
        })
        ->sortByDesc('charged')
        ->values();
  @endphp

  <div class="booking-report-admin">
    <div class="page-header">
      <h4 class="page-title">{{ __('Reportes de reservas') }}</h4>
      <ul class="breadcrumbs">
        <li class="nav-home">
          <a href="{{ route('admin.dashboard') }}" aria-label="{{ __('Ir al panel') }}">
            <i class="flaticon-home"></i>
          </a>
        </li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="{{ route('admin.event.booking') }}">{{ __('Reservas') }}</a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="#">{{ __('Reportes') }}</a></li>
      </ul>
    </div>

    <section class="br-filter-card" aria-label="{{ __('Filtros del reporte') }}">
      <form action="{{ url()->current() }}" method="GET">
        <div class="br-filter-grid">
          <div class="form-group mb-0">
            <label for="reportFromDate">{{ __('Desde') }}</label>
            <input id="reportFromDate" class="form-control datepicker" type="text" name="from_date"
              placeholder="{{ __('Desde') }}" value="{{ request()->input('from_date') }}" required autocomplete="off">
          </div>

          <div class="form-group mb-0">
            <label for="reportToDate">{{ __('Hasta') }}</label>
            <input id="reportToDate" class="form-control datepicker" type="text" name="to_date"
              placeholder="{{ __('Hasta') }}" value="{{ request()->input('to_date') }}" required autocomplete="off">
          </div>

          <div class="form-group mb-0">
            <label for="reportPaymentMethod">{{ __('Método de pago') }}</label>
            <select id="reportPaymentMethod" name="payment_method" class="form-control">
              <option value="">{{ __('Todos') }}</option>
              @if (!empty($onPms))
                @foreach ($onPms as $onPm)
                  <option value="{{ $onPm->keyword }}" {{ request()->input('payment_method') == $onPm->keyword ? 'selected' : '' }}>
                    {{ $onPm->name }}
                  </option>
                @endforeach
              @endif
              @if (!empty($offPms))
                @foreach ($offPms as $offPm)
                  <option value="{{ $offPm->name }}" {{ request()->input('payment_method') == $offPm->name ? 'selected' : '' }}>
                    {{ $offPm->name }}
                  </option>
                @endforeach
              @endif
            </select>
          </div>

          <div class="form-group mb-0">
            <label for="reportPaymentStatus">{{ __('Estado del pago') }}</label>
            <select id="reportPaymentStatus" name="payment_status" class="form-control">
              <option value="">{{ __('Todos') }}</option>
              <option value="completed" {{ request()->input('payment_status') == 'completed' ? 'selected' : '' }}>{{ __('Pago') }}</option>
              <option value="pending" {{ request()->input('payment_status') == 'pending' ? 'selected' : '' }}>{{ __('Pendiente') }}</option>
              <option value="free" {{ request()->input('payment_status') == 'free' ? 'selected' : '' }}>{{ __('Gratis') }}</option>
              <option value="rejected" {{ request()->input('payment_status') == 'rejected' ? 'selected' : '' }}>{{ __('Rechazado') }}</option>
            </select>
          </div>

          <div class="br-filter-actions">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-search mr-1" aria-hidden="true"></i>{{ __('Generar') }}
            </button>
            <a href="{{ route('admin.event_booking.report') }}" class="btn btn-light">{{ __('Limpiar') }}</a>
            @if ($hasReport)
              <a href="{{ route('admin.event_bookings.export') }}" class="btn btn-success">
                <i class="fas fa-file-csv mr-1" aria-hidden="true"></i>{{ __('Exportar') }}
              </a>
            @endif
          </div>
        </div>
      </form>
    </section>

    @if ($hasReport)
      <section class="br-summary" aria-label="{{ __('Resumen del reporte') }}">
        <div class="br-metric br-metric--primary">
          <div>
            <div class="br-metric__label">{{ __('Reservas') }}</div>
            <div class="br-metric__value">{{ number_format($reportTotals['bookings'], 0, ',', '.') }}</div>
            <div class="br-metric__hint">{{ __('Operaciones del rango') }}</div>
          </div>
          <span class="br-metric__icon"><i class="fas fa-ticket-alt" aria-hidden="true"></i></span>
        </div>
        <div class="br-metric br-metric--money">
          <div>
            <div class="br-metric__label">{{ __('Total cobrado') }}</div>
            <div class="br-metric__value">{{ $formatMoney($reportTotals['charged']) }}</div>
            <div class="br-metric__hint">{{ __('Importe bruto del reporte') }}</div>
          </div>
          <span class="br-metric__icon"><i class="fas fa-coins" aria-hidden="true"></i></span>
        </div>
        <div class="br-metric br-metric--paid">
          <div>
            <div class="br-metric__label">{{ __('Entradas') }}</div>
            <div class="br-metric__value">{{ number_format($reportTotals['quantity'], 0, ',', '.') }}</div>
            <div class="br-metric__hint">{{ __('Cantidad total vendida/reservada') }}</div>
          </div>
          <span class="br-metric__icon"><i class="fas fa-tags" aria-hidden="true"></i></span>
        </div>
        <div class="br-metric br-metric--pending">
          <div>
            <div class="br-metric__label">{{ __('Descuentos') }}</div>
            <div class="br-metric__value">{{ $formatMoney($reportTotals['discounts']) }}</div>
            <div class="br-metric__hint">{{ __('Cupones y anticipadas') }}</div>
          </div>
          <span class="br-metric__icon"><i class="fas fa-percent" aria-hidden="true"></i></span>
        </div>

        @foreach (['completed', 'pending', 'free', 'rejected'] as $statusKey)
          @php($meta = $statusMeta[$statusKey])
          <div class="br-metric {{ $meta['metric'] }}">
            <div>
              <div class="br-metric__label">{{ $meta['label'] }}</div>
              <div class="br-metric__value">{{ number_format($statusCounts[$statusKey] ?? 0, 0, ',', '.') }}</div>
              <div class="br-metric__hint">{{ __('Reservas con este estado') }}</div>
            </div>
            <span class="br-metric__icon"><i class="fas {{ $meta['icon'] }}" aria-hidden="true"></i></span>
          </div>
        @endforeach
      </section>

      <section class="br-section" aria-labelledby="reportByEventTitle">
        <div class="br-section__head">
          <div>
            <h2 id="reportByEventTitle" class="br-section__title">{{ __('Resumen por evento') }}</h2>
            <div class="br-section__hint">{{ __('Ordenado por total cobrado dentro del rango filtrado.') }}</div>
          </div>
          <span class="br-chip">{{ $eventsReport->count() }} {{ __('eventos') }}</span>
        </div>
        <div class="br-section__body">
          <div class="br-event-list">
            @foreach ($eventsReport as $eventReport)
              <article class="br-event-card {{ $eventReport['status_meta']['event'] }}">
                <div class="br-event-card__head">
                  <div>
                    <a class="br-event-card__title"
                      href="{{ route('event.details', ['slug' => $eventReport['slug'], 'id' => $eventReport['event_id']]) }}"
                      target="_blank">{{ $eventReport['title'] }}</a>
                    <div class="br-event-card__meta">
                      <span class="br-chip">{{ number_format($eventReport['bookings'], 0, ',', '.') }} {{ __('reservas') }}</span>
                      <span class="br-chip">{{ number_format($eventReport['quantity'], 0, ',', '.') }} {{ __('entradas') }}</span>
                      @foreach ($eventReport['methods']->take(3) as $method)
                        <span class="br-chip">{{ ucfirst($method) }}</span>
                      @endforeach
                    </div>
                  </div>
                  <div class="br-event-card__amount">
                    <span>{{ __('Total cobrado') }}</span>
                    <strong>{{ $formatMoney($eventReport['charged']) }}</strong>
                  </div>
                </div>
                <div class="br-event-card__stats">
                  <div class="br-event-stat">
                    <span>{{ __('Pago') }}</span>
                    <strong>{{ number_format($eventReport['completed'], 0, ',', '.') }}</strong>
                  </div>
                  <div class="br-event-stat">
                    <span>{{ __('Pendiente') }}</span>
                    <strong>{{ number_format($eventReport['pending'], 0, ',', '.') }}</strong>
                  </div>
                  <div class="br-event-stat">
                    <span>{{ __('Gratis') }}</span>
                    <strong>{{ number_format($eventReport['free'], 0, ',', '.') }}</strong>
                  </div>
                  <div class="br-event-stat">
                    <span>{{ __('Rechazado') }}</span>
                    <strong>{{ number_format($eventReport['rejected'], 0, ',', '.') }}</strong>
                  </div>
                </div>
              </article>
            @endforeach
          </div>
        </div>
      </section>

      <section class="br-section" aria-labelledby="reportDetailTitle">
        <div class="br-section__head">
          <div>
            <h2 id="reportDetailTitle" class="br-section__title">{{ __('Detalle de reservas') }}</h2>
            <div class="br-section__hint">{{ __('Listado paginado para revisar operación, cliente, dinero y estado.') }}</div>
          </div>
          <span class="br-chip">{{ $pageBookings->count() }} {{ __('reservas en esta página') }}</span>
        </div>
        <div class="br-mobile-list d-lg-none">
          @foreach ($pageBookings as $booking)
            @php($meta = $statusMeta[$booking->paymentStatus] ?? $statusMeta['pending'])
            <article class="br-booking-card">
              <div class="br-booking-card__top">
                <div>
                  <span class="br-booking-card__label">{{ __('Reserva') }}</span>
                  <div class="br-booking-card__id">#{{ $booking->booking_id }}</div>
                  <div class="br-muted">{{ __('Entradas') }}: {{ number_format((int) $booking->quantity, 0, ',', '.') }}</div>
                </div>
                <span class="br-status {{ $meta['class'] }}">
                  <i class="fas {{ $meta['icon'] }} mr-1" aria-hidden="true"></i>{{ $meta['label'] }}
                </span>
              </div>

              <a class="br-booking-card__event"
                href="{{ route('event.details', ['slug' => $booking->slug, 'id' => $booking->event_id]) }}"
                target="_blank">{{ $booking->title }}</a>

              <div class="br-booking-card__grid">
                <div>
                  <span class="br-booking-card__label">{{ __('Cliente') }}</span>
                  <div class="br-booking-card__value">
                    <strong>{{ trim($booking->fname . ' ' . $booking->lname) ?: trim($booking->customerfname . ' ' . $booking->customerlname) }}</strong>
                    <div class="br-muted">{{ $booking->email ?: '-' }}</div>
                    <div class="br-muted">{{ $booking->phone ?: '-' }}</div>
                  </div>
                </div>

                <div>
                  <span class="br-booking-card__label">{{ __('Dinero') }}</span>
                  <div class="br-booking-card__value">
                    <span class="br-money">{{ $formatMoney($booking->price) }}</span>
                    <div class="br-muted">{{ __('Descuento') }}: {{ $formatMoney((float) $booking->discount + (float) $booking->early_bird_discount) }}</div>
                  </div>
                </div>

                <div>
                  <span class="br-booking-card__label">{{ __('Método y fecha') }}</span>
                  <div class="br-booking-card__value">
                    {{ ucfirst((string) $booking->paymentMethod) ?: '-' }}
                    <div class="br-muted">{{ $booking->created_at ? \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y H:i') : '-' }}</div>
                  </div>
                </div>
              </div>

              <div class="br-booking-card__footer">
                <span class="br-muted">{{ __('Comprobante de entrada') }}</span>
                <button type="button" class="btn btn-outline-primary btn-sm br-action-btn" data-toggle="modal"
                  data-target="#receiptModal{{ $booking->id }}" title="{{ __('Ver comprobante') }}"
                  aria-label="{{ __('Ver comprobante') }} #{{ $booking->booking_id }}">
                  <i class="fas fa-eye" aria-hidden="true"></i>
                </button>
              </div>
            </article>
          @endforeach
        </div>

        <div class="table-responsive br-detail-table-wrap d-none d-lg-block">
          <table class="table br-table">
            <thead>
              <tr>
                <th scope="col">{{ __('Reserva') }}</th>
                <th scope="col">{{ __('Evento') }}</th>
                <th scope="col">{{ __('Cliente') }}</th>
                <th scope="col">{{ __('Dinero') }}</th>
                <th scope="col">{{ __('Estado') }}</th>
                <th scope="col">{{ __('Fecha') }}</th>
                <th scope="col">{{ __('Recibo') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($pageBookings as $booking)
                @php($meta = $statusMeta[$booking->paymentStatus] ?? $statusMeta['pending'])
                <tr>
                  <td>
                    <span class="br-booking-id">#{{ $booking->booking_id }}</span>
                    <div class="br-muted">{{ __('Entradas') }}: {{ number_format((int) $booking->quantity, 0, ',', '.') }}</div>
                  </td>
                  <td>
                    <a class="br-event-link"
                      href="{{ route('event.details', ['slug' => $booking->slug, 'id' => $booking->event_id]) }}"
                      target="_blank">{{ $booking->title }}</a>
                    <div class="br-muted">{{ ucfirst((string) $booking->paymentMethod) }}</div>
                  </td>
                  <td class="br-contact">
                    <strong>{{ trim($booking->fname . ' ' . $booking->lname) ?: trim($booking->customerfname . ' ' . $booking->customerlname) }}</strong>
                    <div class="br-muted">{{ $booking->email ?: '-' }}</div>
                    <div class="br-muted">{{ $booking->phone ?: '-' }}</div>
                  </td>
                  <td>
                    <span class="br-money">{{ $formatMoney($booking->price) }}</span>
                    <div class="br-muted">{{ __('Descuento') }}: {{ $formatMoney((float) $booking->discount + (float) $booking->early_bird_discount) }}</div>
                  </td>
                  <td>
                    <span class="br-status {{ $meta['class'] }}">
                      <i class="fas {{ $meta['icon'] }} mr-1" aria-hidden="true"></i>{{ $meta['label'] }}
                    </span>
                  </td>
                  <td>
                    {{ $booking->created_at ? \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y H:i') : '-' }}
                  </td>
                  <td>
                    <button type="button" class="btn btn-outline-primary btn-sm br-action-btn" data-toggle="modal"
                      data-target="#receiptModal{{ $booking->id }}" title="{{ __('Ver comprobante') }}">
                      <i class="fas fa-eye" aria-hidden="true"></i>
                    </button>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        @if (!empty($bookings) && count($bookings) > 0)
          <div class="card-footer bg-white">
            <div class="row">
              <div class="d-inline-block mx-auto">
                {{ $bookings->appends([
                        'from_date' => request()->input('from_date'),
                        'to_date' => request()->input('to_date'),
                        'payment_method' => request()->input('payment_method'),
                        'payment_status' => request()->input('payment_status'),
                    ])->links() }}
              </div>
            </div>
          </div>
        @endif
      </section>

      @foreach ($pageBookings as $booking)
        <div class="modal fade" id="receiptModal{{ $booking->id }}" tabindex="-1" role="dialog"
          aria-labelledby="receiptModalLabel{{ $booking->id }}" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="receiptModalLabel{{ $booking->id }}">{{ __('Comprobante') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Cerrar') }}">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <iframe src="{{ route('booking.ticket.download', $booking->id) }}" class="receipt"></iframe>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cerrar') }}</button>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    @else
      <section class="br-section">
        <div class="br-empty">
          <i class="fas fa-chart-line" aria-hidden="true"></i>
          <h3>{{ $hasFilters ? __('No encontramos reservas para este reporte') : __('Generá un reporte por rango de fechas') }}</h3>
          <p class="br-muted mb-0">
            {{ $hasFilters ? __('Probá otro rango, método o estado de pago.') : __('Elegí desde y hasta para ver totales, eventos y reservas ordenadas.') }}
          </p>
        </div>
      </section>
    @endif
  </div>
@endsection
