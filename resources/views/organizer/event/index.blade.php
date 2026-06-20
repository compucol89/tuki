@extends('organizer.layout')

@section('style')
  <style>
    .organizer-events {
      max-width: 100%;
      overflow-x: hidden;
      color: #1e2532;
    }

    .oe-summary {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
      gap: 12px;
      margin-bottom: 18px;
    }

    .oe-metric,
    .oe-panel,
    .oe-mobile-event {
      border: 1px solid #e7eaf0;
      border-radius: 8px;
      background: #fff;
      box-shadow: 0 6px 18px rgba(30, 37, 50, .04);
    }

    .oe-metric {
      min-height: 92px;
      padding: 15px;
    }

    .oe-metric__label,
    .oe-label {
      color: #667085;
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
    }

    .oe-metric__value {
      margin-top: 7px;
      color: #1e2532;
      font-size: 23px;
      font-weight: 800;
      line-height: 1.2;
    }

    .oe-muted {
      display: block;
      color: #667085;
      font-size: 12px;
      line-height: 1.35;
    }

    .oe-panel {
      overflow: visible;
    }

    .oe-panel__header {
      display: flex;
      justify-content: space-between;
      gap: 14px;
      padding: 16px 18px;
      border-bottom: 1px solid #eef1f5;
    }

    .oe-panel__title {
      margin: 0;
      color: #1e2532;
      font-size: 17px;
      font-weight: 800;
    }

    .oe-toolbar {
      display: flex;
      flex-wrap: wrap;
      align-items: flex-end;
      gap: 10px;
      padding: 16px 18px;
      border-bottom: 1px solid #eef1f5;
      background: #fbfcfe;
    }

    .oe-toolbar .form-group {
      min-width: 210px;
      margin-bottom: 0;
    }

    .oe-toolbar__search {
      flex: 1 1 280px;
    }

    .oe-toolbar__actions {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-left: auto;
    }

    .oe-table {
      width: 100%;
      table-layout: fixed;
      margin-bottom: 0;
      font-size: 12px;
    }

    .oe-table th {
      border-top: 0;
      color: #667085;
      font-size: 10px;
      line-height: 1.25;
      padding: 9px 6px;
      text-transform: uppercase;
      white-space: normal;
    }

    .oe-table td {
      padding: 10px 6px;
      vertical-align: middle;
      line-height: 1.35;
      overflow-wrap: anywhere;
    }

    .oe-col-check {
      width: 34px;
    }

    .oe-col-event {
      width: 29%;
    }

    .oe-col-meta {
      width: 15%;
    }

    .oe-col-sales {
      width: 16%;
    }

    .oe-col-settlement {
      width: 16%;
    }

    .oe-col-actions {
      width: 12%;
    }

    .oe-event {
      display: flex;
      align-items: center;
      gap: 10px;
      min-width: 0;
    }

    .oe-thumb {
      width: 54px;
      height: 54px;
      flex: 0 0 54px;
      overflow: hidden;
      border-radius: 7px;
      background: #eef1f5;
    }

    .oe-thumb img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .oe-title {
      display: block;
      color: #1e2532;
      font-weight: 800;
      text-decoration: none;
      overflow-wrap: anywhere;
    }

    .oe-title:hover {
      color: #2563eb;
      text-decoration: none;
    }

    .oe-money {
      color: #1e2532;
      font-weight: 800;
      white-space: nowrap;
    }

    .oe-pill {
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

    .oe-progress {
      width: 100%;
      max-width: 145px;
      height: 6px;
      overflow: hidden;
      margin-top: 5px;
      border-radius: 999px;
      background: #e7eaf0;
    }

    .oe-progress span {
      display: block;
      height: 100%;
      border-radius: inherit;
      background: #F97316;
    }

    .oe-status-select {
      min-width: 96px;
      min-height: 32px;
      border: 0;
      border-radius: 7px;
      color: #fff;
      font-size: 12px;
      font-weight: 700;
    }

    .oe-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
    }

    .oe-action-btn {
      min-height: 32px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      border-radius: 6px;
      font-weight: 700;
    }

    .oe-mobile-list {
      display: grid;
      gap: 12px;
      padding: 16px;
    }

    .oe-mobile-event {
      padding: 14px;
    }

    .oe-mobile-event__head {
      display: flex;
      gap: 10px;
      align-items: flex-start;
      margin-bottom: 12px;
    }

    .oe-mobile-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 10px;
      padding-top: 12px;
      border-top: 1px solid #eef1f5;
    }

    .oe-mobile-controls {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 8px;
      margin-top: 12px;
    }

    .oe-empty {
      padding: 40px 16px;
      color: #667085;
      text-align: center;
    }

    .oe-empty h3 {
      margin-bottom: 8px;
      color: #1e2532;
      font-size: 18px;
      font-weight: 800;
    }

    @media (max-width: 991px) {
      .oe-panel__header,
      .oe-toolbar {
        flex-direction: column;
        align-items: stretch;
      }

      .oe-toolbar .form-group,
      .oe-toolbar__actions {
        width: 100%;
        margin-left: 0;
      }

      .oe-toolbar__actions .btn {
        flex: 1 1 auto;
      }
    }

    @media (max-width: 575px) {
      .oe-summary,
      .oe-mobile-grid,
      .oe-mobile-controls {
        grid-template-columns: 1fr;
      }

      .oe-metric {
        min-height: 78px;
        padding: 12px;
      }
    }
  </style>
@endsection

@section('content')
  @php
    $currencySettings = $settings ?? null;
    $formatMoney = function ($amount) use ($currencySettings) {
        $symbol = optional($currencySettings)->base_currency_symbol ?: '$';
        $position = optional($currencySettings)->base_currency_symbol_position ?: 'left';
        $amount = number_format((float) $amount, 0, ',', '.');
        return ($position == 'left' ? $symbol : '') . $amount . ($position == 'right' ? $symbol : '');
    };
    $settlementStatusLabels = [
        'pending' => ['label' => __('Pendiente'), 'class' => 'warning text-dark'],
        'partial' => ['label' => __('Parcial'), 'class' => 'info'],
        'settled' => ['label' => __('Liquidado'), 'class' => 'success'],
        'no_balance' => ['label' => __('Sin saldo'), 'class' => 'secondary'],
    ];
  @endphp

  <div class="organizer-events">
    <div class="page-header">
      <h4 class="page-title">{{ __('Gestión de eventos') }}</h4>
      <ul class="breadcrumbs">
        <li class="nav-home">
          <a href="{{ route('organizer.dashboard') }}" aria-label="{{ __('Ir al panel') }}">
            <i class="flaticon-home"></i>
          </a>
        </li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="#">{{ __('Eventos') }}</a></li>
      </ul>
    </div>

    <section class="oe-summary" aria-label="{{ __('Resumen de eventos') }}">
      <div class="oe-metric">
        <div class="oe-metric__label">{{ __('Eventos') }}</div>
        <div class="oe-metric__value">{{ number_format($eventKpis['total'] ?? 0, 0, ',', '.') }}</div>
      </div>
      <div class="oe-metric">
        <div class="oe-metric__label">{{ __('Activos') }}</div>
        <div class="oe-metric__value">{{ number_format($eventKpis['active'] ?? 0, 0, ',', '.') }}</div>
      </div>
      <div class="oe-metric">
        <div class="oe-metric__label">{{ __('Pendiente por liquidar') }}</div>
        <div class="oe-metric__value">{{ $formatMoney($dashboardSettlementSummary['pending_organizer_amount'] ?? 0) }}</div>
      </div>
      <div class="oe-metric">
        <div class="oe-metric__label">{{ __('Liquidado') }}</div>
        <div class="oe-metric__value">{{ $formatMoney($dashboardSettlementSummary['covered_organizer_amount'] ?? 0) }}</div>
      </div>
    </section>

    <section class="oe-panel" aria-labelledby="organizer-events-title">
      <div class="oe-panel__header">
        <div>
          <h2 id="organizer-events-title" class="oe-panel__title">{{ __('Eventos') }}</h2>
          <span class="oe-muted">{{ optional($language)->name }} · {{ number_format($events->total(), 0, ',', '.') }} {{ __('resultados') }}</span>
        </div>
        <div class="oe-actions">
          <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle oe-action-btn" type="button" id="organizerEventCreateDropdown"
              data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fas fa-plus" aria-hidden="true"></i>{{ __('Crear') }}
            </button>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="organizerEventCreateDropdown">
              <a href="{{ route('organizer.add.event.event', ['type' => 'online']) }}" class="dropdown-item">{{ __('Evento online') }}</a>
              <a href="{{ route('organizer.add.event.event', ['type' => 'venue']) }}" class="dropdown-item">{{ __('Evento presencial') }}</a>
            </div>
          </div>
          <button class="btn btn-danger d-none bulk-delete oe-action-btn"
            data-href="{{ route('organizer.event_management.bulk_delete_event') }}">
            <i class="flaticon-interface-5" aria-hidden="true"></i>{{ __('Eliminar') }}
          </button>
        </div>
      </div>

      <form action="" method="get" class="oe-toolbar">
        @if (empty($langs) || count($langs) <= 1)
          <input type="hidden" name="language" value="{{ request()->input('language') ?: optional($language)->code }}">
        @endif
        <div class="form-group">
          <label>{{ __('Idioma') }}</label>
          @if (!empty($langs) && count($langs) > 1)
            <select name="language" class="form-control"
              onchange="window.location='{{ url()->current() . '?language=' }}' + this.value+'&event_type='+'{{ request()->input('event_type') }}'">
              @foreach ($langs as $lang)
                <option value="{{ $lang->code }}" {{ $lang->code == request()->input('language') ? 'selected' : '' }}>
                  {{ $lang->name }}
                </option>
              @endforeach
            </select>
          @else
            <input type="text" class="form-control" value="{{ optional($language)->name }}" disabled>
          @endif
        </div>
        <div class="form-group">
          <label>{{ __('Tipo') }}</label>
          <select name="event_type" class="form-control">
            <option value="">{{ __('Todos') }}</option>
            <option value="venue" {{ request()->input('event_type') == 'venue' ? 'selected' : '' }}>{{ __('Presencial') }}</option>
            <option value="online" {{ request()->input('event_type') == 'online' ? 'selected' : '' }}>{{ __('Online') }}</option>
          </select>
        </div>
        <div class="form-group oe-toolbar__search">
          <label>{{ __('Evento') }}</label>
          <input type="text" name="title" value="{{ request()->input('title') }}" class="form-control"
            placeholder="{{ __('Buscar por nombre del evento') }}">
        </div>
        <div class="oe-toolbar__actions">
          <button type="submit" class="btn btn-primary oe-action-btn">
            <i class="fas fa-search" aria-hidden="true"></i>{{ __('Buscar') }}
          </button>
          <a href="{{ route('organizer.event_management.event', ['language' => request()->input('language') ?: optional($language)->code]) }}"
            class="btn btn-light oe-action-btn">{{ __('Limpiar') }}</a>
        </div>
      </form>

      @if (count($events) == 0)
        <div class="oe-empty">
          <h3>{{ __('No encontramos eventos') }}</h3>
          <p class="mb-0">{{ __('Probá con otro filtro o creá un evento nuevo.') }}</p>
        </div>
      @else
        <div class="d-none d-lg-block">
          <table class="table oe-table">
            <colgroup>
              <col class="oe-col-check">
              <col class="oe-col-event">
              <col class="oe-col-meta">
              <col class="oe-col-sales">
              <col class="oe-col-settlement">
              <col class="oe-col-meta">
              <col class="oe-col-actions">
            </colgroup>
            <thead>
              <tr>
                <th scope="col"><input type="checkbox" class="bulk-check" data-val="all" aria-label="{{ __('Seleccionar todos') }}"></th>
                <th scope="col">{{ __('Evento') }}</th>
                <th scope="col">{{ __('Tipo / fecha') }}</th>
                <th scope="col">{{ __('Ventas') }}</th>
                <th scope="col">{{ __('Liquidación') }}</th>
                <th scope="col">{{ __('Publicación') }}</th>
                <th scope="col">{{ __('Acciones') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($events as $event)
                @php
                  $metrics = $eventMetrics[$event->id] ?? [];
                  $settlement = $settlementSummaries[$event->id] ?? null;
                  $settlementStatus = $settlement
                      ? ($settlementStatusLabels[$settlement['status']] ?? $settlementStatusLabels['pending'])
                      : $settlementStatusLabels['no_balance'];
                  $thumb = $event->thumbnail ? asset('assets/admin/img/event/thumbnail/' . $event->thumbnail) : asset('assets/admin/img/noimage.jpg');
                @endphp
                <tr>
                  <td><input type="checkbox" class="bulk-check" data-val="{{ $event->id }}" aria-label="{{ __('Seleccionar evento') }} {{ $event->title }}"></td>
                  <td>
                    <div class="oe-event">
                      <div class="oe-thumb">
                        <img src="{{ $thumb }}" alt="{{ $event->title }}" loading="lazy">
                      </div>
                      <div>
                        <a target="_blank" rel="noopener" href="{{ route('event.details', ['slug' => $event->slug, 'id' => $event->id]) }}"
                          class="oe-title">{{ $event->title }}</a>
                        <span class="oe-muted">{{ $event->category }}</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="oe-pill">{{ $event->event_type === 'venue' ? __('Presencial') : __('Online') }}</span>
                    <span class="oe-muted mt-1">{{ __('Función') }}: {{ $metrics['date_label'] ?? '-' }}</span>
                  </td>
                  <td>
                    <div class="oe-money">{{ $formatMoney($metrics['charged_amount'] ?? 0) }}</div>
                    <span class="oe-muted">{{ __('Reservas pagas') }}: {{ number_format($metrics['paid_bookings'] ?? 0, 0, ',', '.') }}</span>
                    <span class="oe-muted">{{ __('Gratis') }}: {{ number_format($metrics['free_bookings'] ?? 0, 0, ',', '.') }}</span>
                    <span class="oe-muted">{{ __('Entradas') }}: {{ number_format($metrics['tickets'] ?? 0, 0, ',', '.') }}</span>
                    <span class="oe-muted">{{ __('Escaneo') }}: {{ number_format($metrics['scanned'] ?? 0, 0, ',', '.') }}/{{ number_format($metrics['tickets'] ?? 0, 0, ',', '.') }}</span>
                    <div class="oe-progress" aria-hidden="true"><span style="width: {{ $metrics['scan_percent'] ?? 0 }}%"></span></div>
                  </td>
                  <td>
                    <span class="badge badge-{{ $settlementStatus['class'] }}">{{ $settlementStatus['label'] }}</span>
                    <span class="oe-muted mt-1">{{ __('Pendiente') }}: {{ $formatMoney($settlement['pending_organizer_amount'] ?? 0) }}</span>
                    <span class="oe-muted">{{ __('Recibís') }}: {{ $formatMoney($metrics['organizer_amount'] ?? 0) }}</span>
                  </td>
                  <td>
                    <form id="statusForm-{{ $event->id }}" class="mb-1"
                      action="{{ route('organizer.event_management.event.event_status', ['id' => $event->id, 'language' => request()->input('language')]) }}"
                      method="post">
                      @csrf
                      <select class="form-control form-control-sm oe-status-select {{ $event->status == 0 ? 'bg-warning text-dark' : 'bg-primary' }}"
                        name="status" onchange="document.getElementById('statusForm-{{ $event->id }}').submit()">
                        <option value="1" {{ $event->status == 1 ? 'selected' : '' }}>{{ __('Activo') }}</option>
                        <option value="0" {{ $event->status == 0 ? 'selected' : '' }}>{{ __('Inactivo') }}</option>
                      </select>
                    </form>
                    <form id="featuredForm-{{ $event->id }}"
                      action="{{ route('organizer.event_management.event.update_featured', ['id' => $event->id]) }}" method="post">
                      @csrf
                      <select class="form-control form-control-sm oe-status-select {{ $event->is_featured == 'yes' ? 'bg-success' : 'bg-danger' }}"
                        name="is_featured" onchange="document.getElementById('featuredForm-{{ $event->id }}').submit()">
                        <option value="yes" {{ $event->is_featured == 'yes' ? 'selected' : '' }}>{{ __('Destacado') }}</option>
                        <option value="no" {{ $event->is_featured == 'no' ? 'selected' : '' }}>{{ __('No destacado') }}</option>
                      </select>
                    </form>
                  </td>
                  <td>
                    <div class="dropdown">
                      <button class="btn btn-secondary dropdown-toggle btn-sm oe-action-btn" type="button"
                        id="organizerEventActions-{{ $event->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ __('Acciones') }}
                      </button>
                      <div class="dropdown-menu dropdown-menu-right" aria-labelledby="organizerEventActions-{{ $event->id }}">
                        <a href="{{ route('organizer.event_management.edit_event', ['id' => $event->id]) }}" class="dropdown-item">{{ __('Editar') }}</a>
                        <a href="{{ route('organizer.event_management.ticket_setting', ['id' => $event->id]) }}" class="dropdown-item">{{ __('Diseño de entrada') }}</a>
                        @if ($event->event_type == 'venue')
                          <a href="{{ route('organizer.event.ticket', ['language' => request()->input('language'), 'event_id' => $event->id, 'event_type' => $event->event_type]) }}"
                            class="dropdown-item">{{ __('Entradas') }}</a>
                        @endif
                        <form class="deleteForm d-block" action="{{ route('organizer.event_management.delete_event', ['id' => $event->id]) }}" method="post">
                          @csrf
                          <button type="submit" class="btn btn-sm deleteBtn">{{ __('Eliminar') }}</button>
                        </form>
                      </div>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="oe-mobile-list d-lg-none">
          @foreach ($events as $event)
            @php
              $metrics = $eventMetrics[$event->id] ?? [];
              $settlement = $settlementSummaries[$event->id] ?? null;
              $settlementStatus = $settlement
                  ? ($settlementStatusLabels[$settlement['status']] ?? $settlementStatusLabels['pending'])
                  : $settlementStatusLabels['no_balance'];
              $thumb = $event->thumbnail ? asset('assets/admin/img/event/thumbnail/' . $event->thumbnail) : asset('assets/admin/img/noimage.jpg');
            @endphp
            <article class="oe-mobile-event">
              <div class="oe-mobile-event__head">
                <div class="oe-thumb"><img src="{{ $thumb }}" alt="{{ $event->title }}" loading="lazy"></div>
                <div>
                  <a target="_blank" rel="noopener" href="{{ route('event.details', ['slug' => $event->slug, 'id' => $event->id]) }}"
                    class="oe-title">{{ $event->title }}</a>
                  <span class="oe-muted">{{ $event->category }}</span>
                  <span class="oe-muted">{{ __('Función') }}: {{ $metrics['date_label'] ?? '-' }}</span>
                </div>
              </div>
              <div class="oe-mobile-grid">
                <div>
                  <span class="oe-label">{{ __('Ventas') }}</span>
                  <span class="oe-money">{{ $formatMoney($metrics['charged_amount'] ?? 0) }}</span>
                  <span class="oe-muted">{{ __('Reservas pagas') }}: {{ $metrics['paid_bookings'] ?? 0 }}</span>
                  <span class="oe-muted">{{ __('Gratis') }}: {{ $metrics['free_bookings'] ?? 0 }}</span>
                </div>
                <div>
                  <span class="oe-label">{{ __('Escaneo') }}</span>
                  <span class="oe-money">{{ $metrics['scanned'] ?? 0 }}/{{ $metrics['tickets'] ?? 0 }}</span>
                  <div class="oe-progress" aria-hidden="true"><span style="width: {{ $metrics['scan_percent'] ?? 0 }}%"></span></div>
                </div>
                <div>
                  <span class="oe-label">{{ __('Liquidación') }}</span>
                  <span class="badge badge-{{ $settlementStatus['class'] }}">{{ $settlementStatus['label'] }}</span>
                  <span class="oe-muted">{{ __('Pendiente') }}: {{ $formatMoney($settlement['pending_organizer_amount'] ?? 0) }}</span>
                </div>
                <div>
                  <span class="oe-label">{{ __('Tipo') }}</span>
                  <span class="oe-pill">{{ $event->event_type === 'venue' ? __('Presencial') : __('Online') }}</span>
                </div>
              </div>
              <div class="oe-mobile-controls">
                <a href="{{ route('organizer.event_management.edit_event', ['id' => $event->id]) }}" class="btn btn-outline-primary btn-sm">
                  <i class="fas fa-edit mr-1" aria-hidden="true"></i>{{ __('Editar') }}
                </a>
                @if ($event->event_type == 'venue')
                  <a href="{{ route('organizer.event.ticket', ['language' => request()->input('language'), 'event_id' => $event->id, 'event_type' => $event->event_type]) }}"
                    class="btn btn-outline-success btn-sm">
                    <i class="fas fa-ticket-alt mr-1" aria-hidden="true"></i>{{ __('Entradas') }}
                  </a>
                @endif
              </div>
            </article>
          @endforeach
        </div>
      @endif

      @if (count($events) > 0)
        <div class="card-footer text-center">
          <div class="d-inline-block mt-3">
            {{ $events->appends([
                    'language' => request()->input('language'),
                    'title' => request()->input('title'),
                    'event_type' => request()->input('event_type'),
                ])->links() }}
          </div>
        </div>
      @endif
    </section>
  </div>
@endsection
