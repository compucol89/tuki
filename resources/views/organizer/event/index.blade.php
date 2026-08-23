@extends('organizer.layout')

@section('style')
  <style>
    .organizer-events {
      --oe-gap-tight: 8px;
      --oe-gap: 12px;
      --oe-gap-loose: 18px;
      --oe-label-size: 12px;
      --oe-meta-size: 12px;
      --oe-title-size: 17px;
      --oe-value-size: 24px;
      --oe-card-bg: var(--surface-card);
      --oe-card-alt-bg: #fffaf6;
      --oe-card-alt-border: rgba(194, 65, 12, .26);
      --oe-card-focus-border: rgba(194, 65, 12, .56);
      --oe-control-border: #9A3412;
      --oe-control-hover-bg: rgba(249, 115, 22, .08);
      --oe-button-primary-bg: #9A3412;
      --oe-button-primary-hover-bg: #7C2D12;
      --oe-warning-bg: #9A3412;
      --oe-warning-fg: #ffffff;
      --oe-success-bg: #166534;
      --oe-success-fg: #ffffff;
      max-width: 100%;
      overflow-x: clip;
      overflow-y: visible;
      color: var(--text-primary);
    }

    html[data-theme="dark"] .organizer-events {
      --oe-card-bg: var(--surface-card);
      --oe-card-alt-bg: #283242;
      --oe-card-alt-border: rgba(253, 186, 116, .38);
      --oe-card-focus-border: rgba(253, 186, 116, .72);
      --oe-control-border: #fdba74;
      --oe-control-hover-bg: rgba(253, 186, 116, .10);
      --oe-button-primary-bg: #9A3412;
      --oe-button-primary-hover-bg: #7C2D12;
      --oe-warning-bg: rgba(253, 186, 116, .16);
      --oe-warning-fg: #fdba74;
      --oe-success-bg: rgba(134, 239, 172, .16);
      --oe-success-fg: #86efac;
    }

    .oe-summary {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: var(--oe-gap);
      margin-bottom: var(--oe-gap-loose);
    }

    .oe-metric,
    .oe-panel,
    .oe-mobile-event {
      border: 1px solid var(--border-default);
      border-radius: 8px;
      background: var(--oe-card-bg);
      box-shadow: 0 6px 18px rgba(30, 37, 50, .04);
    }

    .oe-metric {
      display: flex;
      min-height: 78px;
      flex-direction: column;
      justify-content: center;
      gap: 5px;
      padding: 14px 15px;
    }

    .oe-metric__label,
    .oe-label {
      color: var(--text-muted);
      font-size: var(--oe-label-size);
      font-weight: 600;
      line-height: 1.25;
      letter-spacing: 0;
      text-transform: none;
    }

    .oe-metric__value {
      margin-top: 0;
      color: var(--text-primary);
      font-family: var(--tuki-font-data, 'IBM Plex Mono', ui-monospace, 'SFMono-Regular', Consolas, 'Liberation Mono', monospace);
      font-size: var(--oe-value-size);
      font-weight: 720;
      line-height: 1.05;
      letter-spacing: 0;
      font-variant-numeric: tabular-nums lining-nums;
    }

    .oe-muted {
      display: block;
      color: var(--text-muted);
      font-size: var(--oe-meta-size);
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
      border-bottom: 1px solid var(--border-subtle);
    }

    .oe-panel__title {
      margin: 0;
      color: var(--text-primary);
      font-size: var(--oe-title-size);
      font-weight: 700;
      line-height: 1.2;
    }

    .oe-toolbar {
      display: grid;
      grid-template-columns: minmax(150px, .75fr) minmax(150px, .75fr) minmax(240px, 1.35fr) auto;
      align-items: flex-end;
      gap: var(--oe-gap);
      padding: 16px 18px;
      border-bottom: 1px solid var(--border-subtle);
      background: var(--surface-toolbar);
    }

    .oe-toolbar .form-group {
      min-width: 0;
      margin-bottom: 0;
    }

    .oe-toolbar__search {
      min-width: 0;
    }

    .oe-toolbar__actions {
      display: flex;
      flex-wrap: wrap;
      gap: var(--oe-gap-tight);
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
      color: var(--text-muted);
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
      border-radius: var(--adm-radius-lg);
      background: var(--surface-hover);
    }

    .oe-thumb img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .oe-title {
      display: block;
      color: var(--text-primary);
      font-weight: 700;
      text-decoration: none;
      overflow-wrap: anywhere;
    }

    .oe-title:hover,
    .oe-title:focus {
      color: var(--adm-primary-dark);
      text-decoration: none;
    }

    .oe-money {
      color: var(--text-primary);
      font-family: var(--tuki-font-data, 'IBM Plex Mono', ui-monospace, 'SFMono-Regular', Consolas, 'Liberation Mono', monospace);
      font-weight: 700;
      font-variant-numeric: tabular-nums lining-nums;
      white-space: nowrap;
    }

    .oe-data-value {
      font-family: var(--tuki-font-data, 'IBM Plex Mono', ui-monospace, 'SFMono-Regular', Consolas, 'Liberation Mono', monospace);
      font-variant-numeric: tabular-nums lining-nums;
      white-space: nowrap;
    }

    .oe-pill {
      display: inline-flex;
      align-items: center;
      min-height: 24px;
      padding: 3px 8px;
      border-radius: 999px;
      background: var(--status-warning-bg);
      color: var(--status-warning-fg);
      font-size: 12px;
      font-weight: 700;
      white-space: nowrap;
    }

    .oe-progress {
      width: 100%;
      max-width: 145px;
      height: 6px;
      overflow: hidden;
      margin-top: 5px;
      border-radius: 999px;
      background: var(--border-default);
    }

    .oe-progress span {
      display: block;
      height: 100%;
      border-radius: inherit;
      background: var(--sidebar-accent);
    }

    .oe-status-select {
      min-width: 96px;
      min-height: 32px;
      border: 0;
      border-radius: var(--adm-radius);
      color: var(--text-on-accent);
      font-size: 12px;
      font-weight: 650;
    }

    .oe-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
    }

    .oe-action-btn {
      min-height: 40px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      border-radius: var(--adm-radius);
      font-weight: 650;
    }

    .oe-mobile-list {
      display: grid;
      gap: 12px;
      padding: 16px;
    }

    .oe-mobile-event {
      padding: 13px 14px 14px;
    }

    .oe-mobile-event:nth-child(even) {
      border-color: var(--oe-card-alt-border);
      background: var(--oe-card-alt-bg);
    }

    .oe-mobile-event:focus-within {
      border-color: var(--oe-card-focus-border);
      box-shadow: 0 0 0 3px var(--focus-ring), 0 10px 24px rgba(30, 37, 50, .08);
    }

    .oe-mobile-event .badge-warning {
      border: 1px solid var(--oe-warning-bg) !important;
      background-color: var(--oe-warning-bg) !important;
      color: var(--oe-warning-fg) !important;
    }

    .oe-mobile-event .badge-success {
      border: 1px solid var(--oe-success-bg) !important;
      background-color: var(--oe-success-bg) !important;
      color: var(--oe-success-fg) !important;
    }

    html[data-theme="dark"] .organizer-events .oe-mobile-event .badge-warning,
    html[data-theme="dark"] .organizer-events .oe-mobile-event .badge-success {
      border-color: currentColor !important;
    }

    .oe-mobile-event__head {
      display: grid;
      grid-template-columns: 54px minmax(0, 1fr) auto;
      gap: 10px;
      align-items: flex-start;
      margin-bottom: 11px;
    }

    .oe-mobile-event__main {
      display: grid;
      flex: 1 1 auto;
      min-width: 0;
      gap: 3px;
    }

    .oe-mobile-event__badges {
      display: grid;
      gap: 6px;
      justify-items: end;
      min-width: 0;
    }

    .oe-mobile-event__badges .badge,
    .oe-mobile-event__badges .oe-pill {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      max-width: 104px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .oe-mobile-event .oe-title {
      display: -webkit-box;
      overflow: hidden;
      -webkit-box-orient: vertical;
      -webkit-line-clamp: 2;
      font-size: 14px;
      line-height: 1.25;
    }

    .oe-mobile-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      column-gap: 16px;
      row-gap: 10px;
      padding-top: 12px;
      border-top: 1px solid var(--border-subtle);
    }

    .oe-mobile-stat {
      min-width: 0;
    }

    .oe-mobile-stat .oe-money {
      display: block;
      margin-top: 3px;
      font-size: 15px;
      line-height: 1.25;
    }

    .oe-mobile-stat .oe-muted {
      margin-top: 3px;
    }

    .oe-mobile-settlement {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      margin-top: 10px;
      padding-top: 10px;
      border-top: 1px solid var(--border-subtle);
    }

    .oe-mobile-settlement__copy {
      display: inline-flex;
      flex-wrap: wrap;
      gap: 6px;
      align-items: center;
      min-width: 0;
    }

    .oe-mobile-controls {
      display: grid;
      grid-template-columns: minmax(0, .95fr) minmax(0, 1.05fr);
      gap: 8px;
      margin-top: 12px;
    }

    .oe-mobile-controls--single {
      grid-template-columns: 1fr;
    }

    .oe-mobile-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 40px;
      gap: 7px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 700;
      line-height: 1.2;
      transition: color .18s ease, background-color .18s ease, border-color .18s ease, box-shadow .18s ease;
    }

    .oe-mobile-btn--secondary {
      border-color: var(--oe-control-border) !important;
      background-color: transparent !important;
      color: var(--text-primary) !important;
    }

    .oe-mobile-btn--secondary:hover,
    .oe-mobile-btn--secondary:focus {
      border-color: var(--oe-control-border) !important;
      background-color: var(--oe-control-hover-bg) !important;
      color: var(--oe-button-primary-hover-bg) !important;
    }

    .oe-mobile-btn--primary {
      border-color: var(--oe-button-primary-bg) !important;
      background-color: var(--oe-button-primary-bg) !important;
      color: var(--text-on-accent) !important;
    }

    .oe-mobile-btn--primary:hover,
    .oe-mobile-btn--primary:focus {
      border-color: var(--oe-button-primary-hover-bg) !important;
      background-color: var(--oe-button-primary-hover-bg) !important;
      color: var(--text-on-accent) !important;
    }

    html[data-theme="dark"] .organizer-events .oe-mobile-btn--secondary:hover,
    html[data-theme="dark"] .organizer-events .oe-mobile-btn--secondary:focus {
      color: var(--oe-control-border) !important;
    }

    .oe-mobile-btn:focus {
      box-shadow: 0 0 0 3px var(--focus-ring);
    }

    .oe-empty {
      padding: 32px 16px;
      color: var(--text-muted);
      text-align: center;
    }

    .oe-empty h3 {
      margin-bottom: 8px;
      color: var(--text-primary);
      font-size: 18px;
      font-weight: 700;
    }

    @media (max-width: 991px) {
      .oe-toolbar {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      .oe-panel__header {
        flex-direction: column;
        align-items: stretch;
      }

      .oe-toolbar .form-group,
      .oe-toolbar__actions {
        width: 100%;
        margin-left: 0;
      }

      .oe-toolbar__search,
      .oe-toolbar__actions {
        grid-column: 1 / -1;
      }

      .oe-toolbar__actions {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    @media (max-width: 575px) {
      .organizer-events {
        --oe-gap-loose: 16px;
        --oe-title-size: 16px;
        --oe-value-size: 23px;
      }

      .oe-summary {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      .oe-metric {
        min-height: 72px;
        padding: 12px;
      }

      .oe-toolbar {
        padding: 14px 16px;
      }
    }

    @media (max-width: 420px) {
      .oe-summary,
      .oe-toolbar,
      .oe-toolbar__actions {
        grid-template-columns: 1fr;
      }

      .oe-mobile-grid,
      .oe-mobile-controls {
        grid-template-columns: 1fr;
      }

      .oe-mobile-settlement {
        align-items: flex-start;
        flex-direction: column;
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
      <h1 class="page-title">{{ __('Gestión de eventos') }}</h1>
      <ul class="breadcrumbs">
        <li class="nav-home">
          <a href="{{ route('organizer.dashboard') }}" aria-label="{{ __('Ir al panel') }}">
            <i class="flaticon-home"></i>
          </a>
        </li>
        <li class="separator"><i class="flaticon-right-arrow" aria-hidden="true"></i></li>
        <li class="nav-item"><a href="#">{{ __('Eventos') }}</a></li>
      </ul>
    </div>

    <section class="oe-summary" aria-label="{{ __('Resumen de eventos') }}">
      <div class="oe-metric">
        <div class="oe-metric__label">{{ __('Eventos') }}</div>
        <div class="oe-metric__value tuki-data tuki-data-count">{{ number_format($eventKpis['total'] ?? 0, 0, ',', '.') }}</div>
      </div>
      <div class="oe-metric">
        <div class="oe-metric__label">{{ __('Activos') }}</div>
        <div class="oe-metric__value tuki-data tuki-data-count">{{ number_format($eventKpis['active'] ?? 0, 0, ',', '.') }}</div>
      </div>
      <div class="oe-metric">
        <div class="oe-metric__label">{{ __('Pendiente por liquidar') }}</div>
        <div class="oe-metric__value tuki-data tuki-data-money">{{ $formatMoney($dashboardSettlementSummary['pending_organizer_amount'] ?? 0) }}</div>
      </div>
      <div class="oe-metric">
        <div class="oe-metric__label">{{ __('Liquidado') }}</div>
        <div class="oe-metric__value tuki-data tuki-data-money">{{ $formatMoney($dashboardSettlementSummary['covered_organizer_amount'] ?? 0) }}</div>
      </div>
    </section>

    <section class="oe-panel" aria-labelledby="organizer-events-title">
      <div class="oe-panel__header">
        <div>
          <h2 id="organizer-events-title" class="oe-panel__title">{{ __('Eventos') }}</h2>
          <span class="oe-muted">{{ optional($language)->name }} · <span class="oe-data-value tuki-data tuki-data-count">{{ number_format($events->total(), 0, ',', '.') }}</span> {{ __('resultados') }}</span>
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
          <label for="organizerEventsLanguage">{{ __('Idioma') }}</label>
          @if (!empty($langs) && count($langs) > 1)
            <select id="organizerEventsLanguage" name="language" class="form-control"
              onchange="window.location='{{ url()->current() . '?language=' }}' + this.value+'&event_type='+'{{ request()->input('event_type') }}'">
              @foreach ($langs as $lang)
                <option value="{{ $lang->code }}" {{ $lang->code == request()->input('language') ? 'selected' : '' }}>
                  {{ $lang->name }}
                </option>
              @endforeach
            </select>
          @else
            <input id="organizerEventsLanguage" type="text" class="form-control" value="{{ optional($language)->name }}" disabled>
          @endif
        </div>
        <div class="form-group">
          <label for="organizerEventsType">{{ __('Tipo') }}</label>
          <select id="organizerEventsType" name="event_type" class="form-control">
            <option value="">{{ __('Todos') }}</option>
            <option value="venue" {{ request()->input('event_type') == 'venue' ? 'selected' : '' }}>{{ __('Presencial') }}</option>
            <option value="online" {{ request()->input('event_type') == 'online' ? 'selected' : '' }}>{{ __('Online') }}</option>
          </select>
        </div>
        <div class="form-group oe-toolbar__search">
          <label for="organizerEventsTitle">{{ __('Evento') }}</label>
          <input id="organizerEventsTitle" type="text" name="title" value="{{ request()->input('title') }}" class="form-control"
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
          <div class="table-responsive">
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
                <th scope="col">{{ __('Tipo / categoría') }}</th>
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
                  $thumbName = trim((string) $event->thumbnail);
                  $thumbPath = $thumbName !== '' ? public_path('assets/admin/img/event/thumbnail/' . $thumbName) : '';
                  $thumb = ($thumbPath !== '' && is_file($thumbPath)) ? asset('assets/admin/img/event/thumbnail/' . $thumbName) : asset('assets/admin/img/noimage.jpg');
                  $fallbackThumb = asset('assets/admin/img/noimage.jpg');
                @endphp
                <tr>
                  <td><input type="checkbox" class="bulk-check" data-val="{{ $event->id }}" aria-label="{{ __('Seleccionar evento') }} {{ $event->title }}"></td>
                  <td>
                    <div class="oe-event">
                      <div class="oe-thumb">
                        <img src="{{ $thumb }}" alt="{{ $event->title }}" loading="lazy" onerror="this.onerror=null;this.src='{{ $fallbackThumb }}';">
                      </div>
                      <div>
                        <a target="_blank" rel="noopener" href="{{ route('event.details', ['slug' => $event->slug, 'id' => $event->id]) }}"
                          class="oe-title">{{ $event->title }}</a>
                        <span class="oe-muted">{{ __('Función') }}: {{ $metrics['date_label'] ?? '-' }}</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="oe-pill">{{ $event->event_type === 'venue' ? __('Presencial') : __('Online') }}</span>
                    <span class="oe-muted mt-1">{{ __('Categoría') }}: {{ $event->category ?: '-' }}</span>
                  </td>
                  <td>
                    <div class="oe-money tuki-data tuki-data-money">{{ $formatMoney($metrics['charged_amount'] ?? 0) }}</div>
                    <span class="oe-muted">{{ __('Reservas pagas') }}: <span class="oe-data-value tuki-data tuki-data-count">{{ number_format($metrics['paid_bookings'] ?? 0, 0, ',', '.') }}</span></span>
                    <span class="oe-muted">{{ __('Gratis') }}: <span class="oe-data-value tuki-data tuki-data-count">{{ number_format($metrics['free_bookings'] ?? 0, 0, ',', '.') }}</span></span>
                    <span class="oe-muted">{{ __('Entradas') }}: <span class="oe-data-value tuki-data tuki-data-count">{{ number_format($metrics['tickets'] ?? 0, 0, ',', '.') }}</span></span>
                    <span class="oe-muted">{{ __('Escaneo') }}: <span class="oe-data-value tuki-data tuki-data-count">{{ number_format($metrics['scanned'] ?? 0, 0, ',', '.') }}/{{ number_format($metrics['tickets'] ?? 0, 0, ',', '.') }}</span></span>
                    <div class="oe-progress" aria-hidden="true"><span style="width: {{ $metrics['scan_percent'] ?? 0 }}%"></span></div>
                  </td>
                  <td>
                    <span class="badge badge-{{ $settlementStatus['class'] }}">{{ $settlementStatus['label'] }}</span>
                    <span class="oe-muted mt-1">{{ __('Pendiente') }}: <span class="oe-data-value tuki-data tuki-data-money">{{ $formatMoney($settlement['pending_organizer_amount'] ?? 0) }}</span></span>
                    <span class="oe-muted">{{ __('Recibís') }}: <span class="oe-data-value tuki-data tuki-data-money">{{ $formatMoney($metrics['organizer_amount'] ?? 0) }}</span></span>
                  </td>
                  <td>
                    <form id="statusForm-{{ $event->id }}" class="mb-1"
                      action="{{ route('organizer.event_management.event.event_status', ['id' => $event->id, 'language' => request()->input('language')]) }}"
                      method="post">
                      @csrf
                      <select class="form-control form-control-sm oe-status-select {{ $event->status == 0 ? 'bg-warning text-dark' : 'bg-primary' }}"
                        name="status" aria-label="{{ __('Estado del evento') }}" onchange="document.getElementById('statusForm-{{ $event->id }}').submit()">
                        <option value="1" {{ $event->status == 1 ? 'selected' : '' }}>{{ __('Activo') }}</option>
                        <option value="0" {{ $event->status == 0 ? 'selected' : '' }}>{{ __('Inactivo') }}</option>
                      </select>
                    </form>
                    <form id="featuredForm-{{ $event->id }}"
                      action="{{ route('organizer.event_management.event.update_featured', ['id' => $event->id]) }}" method="post">
                      @csrf
                      <select class="form-control form-control-sm oe-status-select {{ $event->is_featured == 'yes' ? 'bg-success' : 'bg-danger' }}"
                        name="is_featured" aria-label="{{ __('Evento destacado') }}" onchange="document.getElementById('featuredForm-{{ $event->id }}').submit()">
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
        </div>

        <div class="oe-mobile-list d-lg-none">
          @foreach ($events as $event)
            @php
              $metrics = $eventMetrics[$event->id] ?? [];
              $settlement = $settlementSummaries[$event->id] ?? null;
              $settlementStatus = $settlement
                  ? ($settlementStatusLabels[$settlement['status']] ?? $settlementStatusLabels['pending'])
                  : $settlementStatusLabels['no_balance'];
              $thumbName = trim((string) $event->thumbnail);
              $thumbPath = $thumbName !== '' ? public_path('assets/admin/img/event/thumbnail/' . $thumbName) : '';
              $thumb = ($thumbPath !== '' && is_file($thumbPath)) ? asset('assets/admin/img/event/thumbnail/' . $thumbName) : asset('assets/admin/img/noimage.jpg');
              $fallbackThumb = asset('assets/admin/img/noimage.jpg');
            @endphp
            <article class="oe-mobile-event">
              <div class="oe-mobile-event__head">
                <div class="oe-thumb">
                  <img src="{{ $thumb }}" alt="{{ $event->title }}" loading="lazy" onerror="this.onerror=null;this.src='{{ $fallbackThumb }}';">
                </div>
                <div class="oe-mobile-event__main">
                  <a target="_blank" rel="noopener" href="{{ route('event.details', ['slug' => $event->slug, 'id' => $event->id]) }}"
                    class="oe-title">{{ $event->title }}</a>
                  <span class="oe-muted">{{ __('Función') }}: <span class="oe-data-value">{{ $metrics['date_label'] ?? '-' }}</span></span>
                  <span class="oe-muted">{{ __('Categoría') }}: {{ $event->category ?: '-' }}</span>
                </div>
                <div class="oe-mobile-event__badges">
                  <span class="badge badge-{{ $settlementStatus['class'] }}">{{ $settlementStatus['label'] }}</span>
                  <span class="oe-pill">{{ $event->event_type === 'venue' ? __('Presencial') : __('Online') }}</span>
                </div>
              </div>
              <div class="oe-mobile-grid">
                <div class="oe-mobile-stat">
                  <span class="oe-label">{{ __('Ventas') }}</span>
                  <span class="oe-money tuki-data tuki-data-money">{{ $formatMoney($metrics['charged_amount'] ?? 0) }}</span>
                  <span class="oe-muted">{{ __('Reservas pagas') }}: <span class="oe-data-value tuki-data tuki-data-count">{{ number_format($metrics['paid_bookings'] ?? 0, 0, ',', '.') }}</span></span>
                  <span class="oe-muted">{{ __('Gratis') }}: <span class="oe-data-value tuki-data tuki-data-count">{{ number_format($metrics['free_bookings'] ?? 0, 0, ',', '.') }}</span></span>
                </div>
                <div class="oe-mobile-stat">
                  <span class="oe-label">{{ __('Escaneo') }}</span>
                  <span class="oe-money tuki-data tuki-data-count">{{ number_format($metrics['scanned'] ?? 0, 0, ',', '.') }}/{{ number_format($metrics['tickets'] ?? 0, 0, ',', '.') }}</span>
                  <div class="oe-progress" aria-hidden="true"><span style="width: {{ $metrics['scan_percent'] ?? 0 }}%"></span></div>
                </div>
              </div>
              <div class="oe-mobile-settlement">
                <span class="oe-mobile-settlement__copy">
                  <span class="oe-label">{{ __('Liquidación') }}</span>
                  <span class="oe-muted">{{ __('Pendiente') }}: <span class="oe-data-value tuki-data tuki-data-money">{{ $formatMoney($settlement['pending_organizer_amount'] ?? 0) }}</span></span>
                </span>
              </div>
              <div class="oe-mobile-controls {{ $event->event_type == 'venue' ? '' : 'oe-mobile-controls--single' }}">
                <a href="{{ route('organizer.event_management.edit_event', ['id' => $event->id]) }}" class="btn btn-sm oe-mobile-btn oe-mobile-btn--secondary"
                  aria-label="{{ __('Editar') }} {{ $event->title }}">
                  <i class="fas fa-edit" aria-hidden="true"></i>{{ __('Editar') }}
                </a>
                @if ($event->event_type == 'venue')
                  <a href="{{ route('organizer.event.ticket', ['language' => request()->input('language'), 'event_id' => $event->id, 'event_type' => $event->event_type]) }}"
                    class="btn btn-sm oe-mobile-btn oe-mobile-btn--primary"
                    aria-label="{{ __('Entradas') }} {{ $event->title }}">
                    <i class="fas fa-ticket-alt" aria-hidden="true"></i>{{ __('Entradas') }}
                  </a>
                @endif
              </div>
            </article>
          @endforeach
        </div>
      @endif

      @if (count($events) > 0 && $events->hasPages())
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
