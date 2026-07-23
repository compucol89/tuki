@extends('backend.layout')

@section('style')
  <style>
    .transaction-admin {
      color: #1f2937;
      font-size: 13px;
      font-weight: 400;
      letter-spacing: 0;
      -webkit-font-smoothing: antialiased;
      text-rendering: optimizeLegibility;
    }

    .transaction-admin .page-header {
      align-items: center;
      gap: 18px;
    }

    .transaction-admin .page-header .page-title,
    .transaction-admin .page-title {
      font-size: 23px !important;
      font-weight: 600 !important;
      letter-spacing: 0;
    }

    .transaction-admin .tx-panel,
    .transaction-admin .tx-card,
    .transaction-admin .tx-table-card,
    .transaction-admin .tx-mobile-card {
      background: #fff;
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      box-shadow: 0 10px 24px rgba(15, 23, 42, .04);
    }

    .transaction-admin .tx-panel {
      overflow: hidden;
    }

    .transaction-admin .tx-panel-header {
      padding: 22px 24px;
      border-bottom: 1px solid #e5e7eb;
      background: #fbfbfc;
    }

    .transaction-admin .tx-panel-title {
      margin: 0;
      color: #111827;
      font-size: 18px;
      font-weight: 600;
    }

    .transaction-admin .tx-panel-subtitle {
      margin: 6px 0 0;
      color: #64748b;
      font-size: 13px;
      line-height: 1.5;
    }

    .transaction-admin .tx-filter {
      padding: 22px 24px;
      border-bottom: 1px solid #e5e7eb;
    }

    .transaction-admin label {
      color: #475569;
      font-size: 12px;
      font-weight: 500;
      margin-bottom: 7px;
    }

    .transaction-admin .form-control {
      min-height: 42px;
      border-color: #dbe3ef;
      border-radius: 7px;
      color: #1f2937;
      font-size: 14px;
      font-weight: 400;
    }

    .transaction-admin .form-control:focus {
      border-color: #f97316;
      box-shadow: 0 0 0 3px rgba(249, 115, 22, .12);
    }

    .transaction-admin .tx-action-row {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      align-items: center;
      justify-content: flex-end;
    }

    .transaction-admin .tx-btn {
      display: inline-flex;
      min-height: 40px;
      align-items: center;
      justify-content: center;
      gap: 8px;
      border-radius: 7px;
      padding: 9px 16px;
      border: 1px solid transparent;
      font-size: 13px;
      font-weight: 600;
      line-height: 1.2;
      white-space: nowrap;
    }

    .transaction-admin .tx-btn-primary {
      background: #d63d08;
      border-color: #d63d08;
      color: #fff;
    }

    .transaction-admin .tx-btn-light {
      background: #fff;
      border-color: #dbe3ef;
      color: #475569;
    }

    .transaction-admin .tx-kpis {
      display: grid;
      grid-template-columns: repeat(6, minmax(0, 1fr));
      gap: 14px;
      padding: 22px 24px;
      background: #f5f7fa;
      border-bottom: 1px solid #e5e7eb;
    }

    .transaction-admin .tx-card {
      padding: 17px;
      min-height: 112px;
      border-top: 3px solid #e5e7eb;
    }

    .transaction-admin .tx-card-orange {
      border-top-color: #f97316;
    }

    .transaction-admin .tx-card-green {
      border-top-color: #16a34a;
    }

    .transaction-admin .tx-card-red {
      border-top-color: #dc2626;
    }

    .transaction-admin .tx-card-blue {
      border-top-color: #2563eb;
    }

    .transaction-admin .tx-card-purple {
      border-top-color: #7c3aed;
    }

    .transaction-admin .tx-kpi-label {
      display: flex;
      align-items: center;
      gap: 8px;
      color: #64748b;
      font-size: 11px;
      font-weight: 600;
      letter-spacing: .02em;
      text-transform: uppercase;
    }

    .transaction-admin .tx-kpi-value {
      margin-top: 12px;
      color: #111827;
      font-size: 23px;
      font-weight: 700;
      line-height: 1.1;
    }

    .transaction-admin .tx-kpi-note {
      margin-top: 6px;
      color: #64748b;
      font-size: 12px;
      line-height: 1.35;
    }

    .transaction-admin .tx-body {
      padding: 22px 24px;
    }

    .transaction-admin .tx-table-card {
      overflow: hidden;
    }

    .transaction-admin .table {
      margin-bottom: 0;
      color: #1f2937;
    }

    .transaction-admin .table thead th {
      background: #eaf2f8;
      border-color: #dce6ef;
      color: #111827;
      font-size: 11px;
      font-weight: 600;
      letter-spacing: .03em;
      text-transform: uppercase;
      vertical-align: middle;
      white-space: nowrap;
    }

    .transaction-admin .table tbody td {
      border-color: #edf1f5;
      font-size: 13px;
      vertical-align: middle;
    }

    .transaction-admin .tx-id {
      color: #111827;
      font-size: 14px;
      font-weight: 600;
      white-space: nowrap;
    }

    .transaction-admin .tx-muted {
      color: #64748b;
      font-size: 12px;
      font-weight: 400;
      line-height: 1.45;
    }

    .transaction-admin .tx-context-title {
      color: #111827;
      font-size: 13.5px;
      font-weight: 600;
      line-height: 1.35;
    }

    .transaction-admin .tx-context-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      margin-top: 8px;
    }

    .transaction-admin .tx-badge {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      border-radius: 999px;
      padding: 4px 8px;
      font-size: 11px;
      font-weight: 600;
      line-height: 1.1;
      white-space: nowrap;
    }

    .transaction-admin .tx-badge-green {
      background: #dcfce7;
      color: #15803d;
    }

    .transaction-admin .tx-badge-blue {
      background: #dbeafe;
      color: #1d4ed8;
    }

    .transaction-admin .tx-badge-amber {
      background: #fff7ed;
      color: #c2410c;
    }

    .transaction-admin .tx-badge-red {
      background: #fee2e2;
      color: #b91c1c;
    }

    .transaction-admin .tx-badge-gray {
      background: #eef2f7;
      color: #475569;
    }

    .transaction-admin .tx-badge-dark {
      background: #1f2937;
      color: #fff;
    }

    .transaction-admin .tx-badge-code {
      max-width: 260px;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .transaction-admin .tx-amount {
      color: #111827;
      font-size: 15px;
      font-weight: 700;
      white-space: nowrap;
    }

    .transaction-admin .tx-value {
      color: #111827;
      font-size: 13px;
      font-weight: 500;
      line-height: 1.35;
    }

    .transaction-admin .tx-amount.positive {
      color: #15803d;
    }

    .transaction-admin .tx-amount.negative {
      color: #b91c1c;
    }

    .transaction-admin .tx-balance {
      min-width: 128px;
    }

    .transaction-admin .tx-balance span {
      display: block;
    }

    .transaction-admin .tx-empty {
      padding: 60px 20px;
      text-align: center;
      color: #64748b;
    }

    .transaction-admin .tx-empty h3 {
      color: #111827;
      font-size: 22px;
      font-weight: 600;
      margin-bottom: 8px;
    }

    .transaction-admin .tx-mobile-card {
      padding: 16px;
      margin-bottom: 12px;
    }

    .transaction-admin .tx-mobile-top {
      display: flex;
      gap: 12px;
      align-items: flex-start;
      justify-content: space-between;
    }

    .transaction-admin .tx-mobile-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 12px;
      margin-top: 14px;
      padding-top: 14px;
      border-top: 1px solid #edf1f5;
    }

    .transaction-admin .tx-pagination {
      padding: 18px 24px;
      border-top: 1px solid #e5e7eb;
      text-align: center;
    }

    body[data-background-color="dark"] .transaction-admin .tx-panel,
    body[data-background-color="dark"] .transaction-admin .tx-card,
    body[data-background-color="dark"] .transaction-admin .tx-table-card,
    body[data-background-color="dark"] .transaction-admin .tx-mobile-card {
      background: #fff;
      color: #1f2937;
    }

    @media (max-width: 1399.98px) {
      .transaction-admin .tx-kpis {
        grid-template-columns: repeat(3, minmax(0, 1fr));
      }
    }

    @media (max-width: 767.98px) {
      .transaction-admin .tx-panel-header,
      .transaction-admin .tx-filter,
      .transaction-admin .tx-body,
      .transaction-admin .tx-pagination {
        padding-left: 14px;
        padding-right: 14px;
      }

      .transaction-admin .tx-kpis {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        padding: 14px;
      }

      .transaction-admin .tx-kpi-value {
        font-size: 20px;
      }

      .transaction-admin .tx-action-row {
        justify-content: stretch;
      }

      .transaction-admin .tx-action-row .tx-btn {
        flex: 1 1 140px;
      }
    }

    @media (min-width: 576px) and (max-width: 767.98px) {
      .transaction-admin .tx-filter .row > div {
        flex: 0 0 50%;
        max-width: 50%;
      }

      .transaction-admin .tx-filter .row > div:nth-child(1),
      .transaction-admin .tx-filter .row > div:nth-child(2),
      .transaction-admin .tx-filter .row > div:nth-child(7),
      .transaction-admin .tx-filter .row > div:nth-child(10) {
        flex-basis: 100%;
        max-width: 100%;
      }
    }
  </style>
@endsection

@section('content')
  @php
    $formatMoney = function ($amount, $symbol = '$', $position = 'left') {
        $value = number_format(abs((float) $amount), 0, ',', '.');
        return $position === 'right' ? $value . ' ' . $symbol : $symbol . ' ' . $value;
    };

    $typeMeta = function ($type, $paymentMethod = null) {
        $items = [
            1 => ['label' => 'Reserva de evento', 'class' => 'green', 'icon' => 'fa-calendar-check'],
            2 => ['label' => 'Orden de tienda', 'class' => 'blue', 'icon' => 'fa-shopping-bag'],
            3 => ['label' => 'Retiro', 'class' => 'red', 'icon' => 'fa-arrow-circle-up'],
            4 => ['label' => 'Carga de saldo', 'class' => 'blue', 'icon' => 'fa-plus-circle'],
            5 => ['label' => $paymentMethod === 'event_settlement' ? 'Liquidación de evento' : 'Descuento de saldo', 'class' => 'amber', 'icon' => 'fa-file-invoice-dollar'],
        ];

        return $items[(int) $type] ?? ['label' => 'Movimiento', 'class' => 'gray', 'icon' => 'fa-exchange-alt'];
    };

    $statusMeta = function ($status) {
        $key = strtolower((string) $status);

        if (in_array($key, ['1', 'paid', 'completed', 'complete', 'success', 'approved'], true)) {
            return ['label' => 'Pagado', 'class' => 'green'];
        }

        if (in_array($key, ['free', 'gratis'], true)) {
            return ['label' => 'Gratis', 'class' => 'blue'];
        }

        if (in_array($key, ['pending', 'processing', 'process'], true)) {
            return ['label' => 'Pendiente', 'class' => 'amber'];
        }

        if (in_array($key, ['2', 'declined', 'rejected', 'failed', 'cancelled', 'canceled'], true)) {
            return ['label' => 'Rechazado', 'class' => 'red'];
        }

        return ['label' => 'Sin pagar', 'class' => 'gray'];
    };

    $methodLabel = function ($transaction) {
        if ((int) $transaction->transcation_type === 3) {
            return optional($transaction->method)->name ?? '-';
        }

        if ($transaction->payment_method === 'event_settlement') {
            return 'Liquidación de evento';
        }

        return $transaction->payment_method ?: '-';
    };

    $gatewayLabel = function ($transaction) {
        if ($transaction->gateway_type === 'online') {
            return 'Online';
        }

        if ($transaction->gateway_type === 'offline') {
            return 'Offline';
        }

        return $transaction->gateway_type ?: '-';
    };
  @endphp

  <div class="transaction-admin">
    <div class="page-header">
      <h4 class="page-title">Transacciones</h4>
      <ul class="breadcrumbs">
        <li class="nav-home">
          <a href="{{ route('admin.dashboard') }}">
            <i class="flaticon-home"></i>
          </a>
        </li>
        <li class="separator">
          <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
          <a href="#">Transacciones</a>
        </li>
      </ul>
    </div>

    <div class="tx-panel">
      <div class="tx-panel-header">
        <h2 class="tx-panel-title">Movimientos financieros</h2>
        <p class="tx-panel-subtitle">
          Datos registrados por TukiPass: reservas, MercadoPago, tienda, retiros y liquidaciones.
        </p>
      </div>

      <form class="tx-filter" action="{{ route('admin.transcation') }}" method="get">
        <div class="row">
          <div class="col-lg-4 col-md-6 mb-3">
            <label for="tx-q">Buscar</label>
            <input id="tx-q" type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control"
              placeholder="ID, reserva, evento, cliente u organizador">
          </div>

          <div class="col-lg-4 col-md-6 mb-3">
            <label for="tx-event">Evento</label>
            <select id="tx-event" name="event_id" class="form-control">
              <option value="">Todos los eventos</option>
              @foreach ($eventOptions as $eventOption)
                <option value="{{ $eventOption->event_id }}" @selected(($filters['event_id'] ?? '') == $eventOption->event_id)>
                  {{ $eventOption->title }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-lg-2 col-md-6 mb-3">
            <label for="tx-period">Período</label>
            <select id="tx-period" name="period" class="form-control">
              <option value="">Todo</option>
              <option value="today" @selected(($filters['period'] ?? '') === 'today')>Hoy</option>
              <option value="week" @selected(($filters['period'] ?? '') === 'week')>Esta semana</option>
              <option value="month" @selected(($filters['period'] ?? '') === 'month')>Este mes</option>
              <option value="custom" @selected(($filters['period'] ?? '') === 'custom')>Rango manual</option>
            </select>
          </div>

          <div class="col-lg-2 col-md-6 mb-3">
            <label for="tx-method">Medio de pago</label>
            <select id="tx-method" name="payment_method" class="form-control">
              <option value="">Todos</option>
              @foreach ($paymentMethods as $paymentMethod)
                <option value="{{ $paymentMethod }}" @selected(($filters['payment_method'] ?? '') === $paymentMethod)>
                  {{ $paymentMethod === 'event_settlement' ? 'Liquidación de evento' : $paymentMethod }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-lg-2 col-md-6 mb-3">
            <label for="tx-status">Estado</label>
            <select id="tx-status" name="payment_status" class="form-control">
              <option value="">Todos</option>
              <option value="completed" @selected(($filters['payment_status'] ?? '') === 'completed')>Pagado</option>
              <option value="pending" @selected(($filters['payment_status'] ?? '') === 'pending')>Pendiente</option>
              <option value="free" @selected(($filters['payment_status'] ?? '') === 'free')>Gratis</option>
              <option value="rejected" @selected(($filters['payment_status'] ?? '') === 'rejected')>Rechazado</option>
              <option value="legacy_paid" @selected(($filters['payment_status'] ?? '') === 'legacy_paid')>Pagado anterior</option>
              <option value="legacy_declined" @selected(($filters['payment_status'] ?? '') === 'legacy_declined')>Declinado anterior</option>
              <option value="legacy_unpaid" @selected(($filters['payment_status'] ?? '') === 'legacy_unpaid')>Sin pagar anterior</option>
            </select>
          </div>

          <div class="col-lg-2 col-md-6 mb-3">
            <label for="tx-type">Tipo</label>
            <select id="tx-type" name="transcation_type" class="form-control">
              <option value="">Todos</option>
              <option value="1" @selected(($filters['transcation_type'] ?? '') === '1')>Reservas</option>
              <option value="2" @selected(($filters['transcation_type'] ?? '') === '2')>Tienda</option>
              <option value="3" @selected(($filters['transcation_type'] ?? '') === '3')>Retiros</option>
              <option value="4" @selected(($filters['transcation_type'] ?? '') === '4')>Carga de saldo</option>
              <option value="5" @selected(($filters['transcation_type'] ?? '') === '5')>Liquidaciones</option>
            </select>
          </div>

          <div class="col-lg-2 col-md-6 mb-3">
            <label for="tx-gateway">Canal</label>
            <select id="tx-gateway" name="gateway_type" class="form-control">
              <option value="">Todos</option>
              <option value="online" @selected(($filters['gateway_type'] ?? '') === 'online')>Online</option>
              <option value="offline" @selected(($filters['gateway_type'] ?? '') === 'offline')>Offline</option>
            </select>
          </div>

          <div class="col-lg-2 col-md-6 mb-3">
            <label for="tx-from">Desde</label>
            <input id="tx-from" type="date" name="from_date" value="{{ $filters['from_date'] ?? '' }}"
              class="form-control">
          </div>

          <div class="col-lg-2 col-md-6 mb-3">
            <label for="tx-to">Hasta</label>
            <input id="tx-to" type="date" name="to_date" value="{{ $filters['to_date'] ?? '' }}"
              class="form-control">
          </div>

          <div class="col-lg-4 col-md-12 mb-3 d-flex align-items-end">
            <div class="tx-action-row w-100">
              <button class="tx-btn tx-btn-primary" type="submit">
                <i class="fas fa-search"></i>
                Filtrar
              </button>
              <a class="tx-btn tx-btn-light" href="{{ route('admin.transcation') }}">
                <i class="fas fa-times"></i>
                Limpiar
              </a>
            </div>
          </div>
        </div>
      </form>

      <div class="tx-kpis">
        <div class="tx-card tx-card-orange">
          <div class="tx-kpi-label"><i class="fas fa-list"></i> Movimientos</div>
          <div class="tx-kpi-value">{{ $transactionSummary['count'] }}</div>
          <div class="tx-kpi-note">Resultado con filtros activos</div>
        </div>

        <div class="tx-card tx-card-green">
          <div class="tx-kpi-label"><i class="fas fa-arrow-down"></i> Ingresos</div>
          <div class="tx-kpi-value">{{ $formatMoney($transactionSummary['income']) }}</div>
          <div class="tx-kpi-note">Reservas, tienda y cargas</div>
        </div>

        <div class="tx-card tx-card-red">
          <div class="tx-kpi-label"><i class="fas fa-arrow-up"></i> Egresos</div>
          <div class="tx-kpi-value">{{ $formatMoney($transactionSummary['expenses']) }}</div>
          <div class="tx-kpi-note">Retiros y liquidaciones</div>
        </div>

        <div class="tx-card tx-card-green">
          <div class="tx-kpi-label"><i class="fas fa-wallet"></i> Neto</div>
          <div class="tx-kpi-value">{{ $transactionSummary['net'] < 0 ? '-' : '' }}{{ $formatMoney($transactionSummary['net']) }}</div>
          <div class="tx-kpi-note">Ingresos menos egresos</div>
        </div>

        <div class="tx-card tx-card-blue">
          <div class="tx-kpi-label"><i class="fas fa-credit-card"></i> MercadoPago</div>
          <div class="tx-kpi-value">{{ $transactionSummary['mercadopago_total'] < 0 ? '-' : '' }}{{ $formatMoney($transactionSummary['mercadopago_total']) }}</div>
          <div class="tx-kpi-note">{{ $transactionSummary['mercadopago_count'] }} movimientos locales</div>
        </div>

        <div class="tx-card tx-card-purple">
          <div class="tx-kpi-label"><i class="fas fa-percentage"></i> Comisión</div>
          <div class="tx-kpi-value">{{ $formatMoney($transactionSummary['commission']) }}</div>
          <div class="tx-kpi-note">Comisión registrada</div>
        </div>
      </div>

      <div class="tx-body">
        @if (count($transcations) == 0)
          <div class="tx-empty">
            <h3>No encontramos transacciones</h3>
            <p>Probá con otro evento, período, medio de pago o ID.</p>
          </div>
        @else
          <div class="tx-table-card d-none d-xl-block">
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>Transacción</th>
                    <th>Contexto</th>
                    <th>Organizador</th>
                    <th>Medio</th>
                    <th>Movimiento</th>
                    <th>Saldo</th>
                    <th>Estado</th>
                    <th class="text-center">Acción</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($transcations as $transcation)
                    @php
                      $booking = $transcation->event_booking;
                      $order = $transcation->product_order;
                      $organizer = $transcation->organizer;
                      $type = $typeMeta($transcation->transcation_type, $transcation->payment_method);
                      $status = $statusMeta($transcation->payment_status);
                      $method = $methodLabel($transcation);
                      $gateway = $gatewayLabel($transcation);
                      $isNegative = in_array((int) $transcation->transcation_type, [3, 5], true);
                      $amount = (float) $transcation->grand_total - (float) $transcation->commission;
                      $eventTitle = optional(optional($booking)->event)->title;
                      $customerName = trim((optional($booking)->fname ?? '') . ' ' . (optional($booking)->lname ?? ''));
                      $mpPaymentId = optional($booking)->conversation_id ?: optional($order)->conversation_id;
                    @endphp

                    <tr>
                      <td>
                        <div class="tx-id">#{{ $transcation->transcation_id }}</div>
                        <div class="tx-muted">{{ optional($transcation->created_at)->format('d/m/Y H:i') }}</div>
                      </td>
                      <td>
                        <div class="tx-context-title">
                          @if ($eventTitle)
                            {{ $eventTitle }}
                          @elseif ($order)
                            Orden #{{ $order->order_number ?? $order->id }}
                          @else
                            {{ $type['label'] }}
                          @endif
                        </div>
                        <div class="tx-context-meta">
                          <span class="tx-badge tx-badge-{{ $type['class'] }}">
                            <i class="fas {{ $type['icon'] }}"></i>
                            {{ $type['label'] }}
                          </span>
                          @if ($booking)
                            <span class="tx-badge tx-badge-gray">Reserva #{{ $booking->booking_id }}</span>
                            <span class="tx-badge tx-badge-gray">{{ $booking->quantity }} entrada(s)</span>
                          @endif
                          @if ($mpPaymentId)
                            <span class="tx-badge tx-badge-blue tx-badge-code">MP #{{ $mpPaymentId }}</span>
                          @endif
                        </div>
                        @if ($customerName || optional($booking)->email)
                          <div class="tx-muted mt-2">{{ $customerName ?: 'Invitado' }} · {{ optional($booking)->email }}</div>
                        @endif
                      </td>
                      <td>
                        @if ($organizer)
                          <a target="_blank"
                            href="{{ route('admin.organizer_management.organizer_details', ['id' => $organizer->id, 'language' => $defaultLang->code]) }}">
                            {{ $organizer->username }}
                          </a>
                        @else
                          <span class="tx-badge tx-badge-dark">Admin</span>
                        @endif
                      </td>
                      <td>
                        <div class="tx-value">{{ $method }}</div>
                        <div class="tx-muted">{{ $gateway }}</div>
                      </td>
                      <td>
                        <div class="tx-amount {{ $isNegative ? 'negative' : 'positive' }}">
                          {{ $isNegative ? '-' : '+' }} {{ $formatMoney($amount, $transcation->currency_symbol ?: '$', $transcation->currency_symbol_position ?: 'left') }}
                        </div>
                        @if ((float) $transcation->commission > 0)
                          <div class="tx-muted">Comisión {{ $formatMoney($transcation->commission, $transcation->currency_symbol ?: '$', $transcation->currency_symbol_position ?: 'left') }}</div>
                        @endif
                      </td>
                      <td>
                        <div class="tx-balance">
                          <span class="tx-muted">Antes: {{ $formatMoney($transcation->pre_balance, $transcation->currency_symbol ?: '$', $transcation->currency_symbol_position ?: 'left') }}</span>
                          <span class="tx-muted">Después: {{ $formatMoney($transcation->after_balance, $transcation->currency_symbol ?: '$', $transcation->currency_symbol_position ?: 'left') }}</span>
                        </div>
                      </td>
                      <td>
                        <span class="tx-badge tx-badge-{{ $status['class'] }}">{{ $status['label'] }}</span>
                      </td>
                      <td class="text-center">
                        @if ((int) $transcation->transcation_type === 1 && $booking)
                          <a target="_blank" class="tx-btn tx-btn-light" href="{{ route('booking.ticket.download', $booking->id) }}">
                            <i class="fas fa-eye"></i>
                            Ver
                          </a>
                        @elseif ((int) $transcation->transcation_type === 2 && $order)
                          @php
                            $invoiceFile = $order->invoice ?? $order->receipt ?? null;
                          @endphp
                          @if ($invoiceFile)
                            <a target="_blank" class="tx-btn tx-btn-light"
                              href="{{ asset('assets/admin/file/order/invoices/' . $invoiceFile) }}">
                              <i class="fas fa-eye"></i>
                              Ver
                            </a>
                          @else
                            <span class="tx-muted">-</span>
                          @endif
                        @else
                          <span class="tx-muted">-</span>
                        @endif
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>

          <div class="d-xl-none">
            @foreach ($transcations as $transcation)
              @php
                $booking = $transcation->event_booking;
                $order = $transcation->product_order;
                $organizer = $transcation->organizer;
                $type = $typeMeta($transcation->transcation_type, $transcation->payment_method);
                $status = $statusMeta($transcation->payment_status);
                $method = $methodLabel($transcation);
                $gateway = $gatewayLabel($transcation);
                $isNegative = in_array((int) $transcation->transcation_type, [3, 5], true);
                $amount = (float) $transcation->grand_total - (float) $transcation->commission;
                $eventTitle = optional(optional($booking)->event)->title;
                $customerName = trim((optional($booking)->fname ?? '') . ' ' . (optional($booking)->lname ?? ''));
                $mpPaymentId = optional($booking)->conversation_id ?: optional($order)->conversation_id;
              @endphp

              <div class="tx-mobile-card">
                <div class="tx-mobile-top">
                  <div>
                    <div class="tx-id">#{{ $transcation->transcation_id }}</div>
                    <div class="tx-muted">{{ optional($transcation->created_at)->format('d/m/Y H:i') }}</div>
                  </div>
                  <span class="tx-badge tx-badge-{{ $status['class'] }}">{{ $status['label'] }}</span>
                </div>

                <div class="tx-context-title mt-3">
                  {{ $eventTitle ?: ($order ? 'Orden #' . ($order->order_number ?? $order->id) : $type['label']) }}
                </div>
                <div class="tx-context-meta">
                  <span class="tx-badge tx-badge-{{ $type['class'] }}">
                    <i class="fas {{ $type['icon'] }}"></i>
                    {{ $type['label'] }}
                  </span>
                  @if ($booking)
                    <span class="tx-badge tx-badge-gray">Reserva #{{ $booking->booking_id }}</span>
                  @endif
                  @if ($mpPaymentId)
                    <span class="tx-badge tx-badge-blue tx-badge-code">MP #{{ $mpPaymentId }}</span>
                  @endif
                </div>

                @if ($customerName || optional($booking)->email)
                  <div class="tx-muted mt-2">{{ $customerName ?: 'Invitado' }} · {{ optional($booking)->email }}</div>
                @endif

                <div class="tx-mobile-grid">
                  <div>
                    <div class="tx-muted">Medio</div>
                    <div class="tx-value">{{ $method }}</div>
                    <div class="tx-muted">{{ $gateway }}</div>
                  </div>
                  <div>
                    <div class="tx-muted">Organizador</div>
                    <div class="tx-value">{{ optional($organizer)->username ?? 'Admin' }}</div>
                  </div>
                  <div>
                    <div class="tx-muted">Monto</div>
                    <div class="tx-amount {{ $isNegative ? 'negative' : 'positive' }}">
                      {{ $isNegative ? '-' : '+' }} {{ $formatMoney($amount, $transcation->currency_symbol ?: '$', $transcation->currency_symbol_position ?: 'left') }}
                    </div>
                  </div>
                  <div>
                    <div class="tx-muted">Saldo</div>
                    <div class="tx-muted">Antes {{ $formatMoney($transcation->pre_balance, $transcation->currency_symbol ?: '$', $transcation->currency_symbol_position ?: 'left') }}</div>
                    <div class="tx-muted">Después {{ $formatMoney($transcation->after_balance, $transcation->currency_symbol ?: '$', $transcation->currency_symbol_position ?: 'left') }}</div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>

      <div class="tx-pagination">
        {{ $transcations->appends(request()->query())->links() }}
      </div>
    </div>
  </div>
@endsection
