@extends('organizer.layout')

@section('style')
  <style>
    .organizer-income-admin {
      --oi-gap-tight: 8px;
      --oi-gap: 12px;
      --oi-gap-loose: 18px;
      --oi-label-size: 12px;
      --oi-meta-size: 12px;
      --oi-title-size: 17px;
      --oi-value-size: 24px;
      --oi-card-bg: var(--surface-card);
      --oi-card-alt-bg: #fffaf6;
      --oi-card-alt-border: rgba(194, 65, 12, .26);
      --oi-control-border: #9A3412;
      --oi-positive-bg: #166534;
      --oi-positive-fg: #ffffff;
      --oi-negative-bg: #991B1B;
      --oi-negative-fg: #ffffff;
      --oi-neutral-bg: var(--surface-active);
      --oi-neutral-fg: var(--text-secondary);
      max-width: 100%;
      overflow-x: clip;
      color: var(--text-primary);
    }

    html[data-theme="dark"] .organizer-income-admin {
      --oi-card-bg: var(--surface-card);
      --oi-card-alt-bg: #283242;
      --oi-card-alt-border: rgba(253, 186, 116, .38);
      --oi-control-border: #fdba74;
      --oi-positive-bg: rgba(134, 239, 172, .16);
      --oi-positive-fg: #86efac;
      --oi-negative-bg: rgba(252, 165, 165, .16);
      --oi-negative-fg: #fca5a5;
      --oi-neutral-bg: rgba(148, 163, 184, .14);
      --oi-neutral-fg: var(--text-secondary);
    }

    .oi-summary {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: var(--oi-gap);
      margin-bottom: var(--oi-gap-loose);
    }

    .oi-metric,
    .oi-panel,
    .oi-mobile-month {
      border: 1px solid var(--border-default);
      border-radius: 8px;
      background: var(--oi-card-bg);
      box-shadow: 0 6px 18px rgba(30, 37, 50, .04);
    }

    .oi-metric {
      display: flex;
      min-height: 86px;
      flex-direction: column;
      justify-content: center;
      gap: 5px;
      padding: 14px 15px;
    }

    .oi-metric__label,
    .oi-label {
      color: var(--text-muted);
      font-size: var(--oi-label-size);
      font-weight: 600;
      line-height: 1.25;
      letter-spacing: 0;
      text-transform: none;
    }

    .oi-metric__value,
    .oi-money,
    .oi-data {
      color: var(--text-primary);
      font-family: var(--tuki-font-data, 'IBM Plex Mono', ui-monospace, 'SFMono-Regular', Consolas, 'Liberation Mono', monospace);
      font-variant-numeric: tabular-nums lining-nums;
      letter-spacing: 0;
    }

    .oi-metric__value {
      font-size: var(--oi-value-size);
      font-weight: 720;
      line-height: 1.05;
    }

    .oi-metric__hint,
    .oi-muted {
      color: var(--text-muted);
      font-size: var(--oi-meta-size);
      line-height: 1.35;
    }

    .oi-panel__header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
      padding: 16px 18px;
      border-bottom: 1px solid var(--border-subtle);
    }

    .oi-panel__title {
      margin: 0;
      color: var(--text-primary);
      font-size: var(--oi-title-size);
      font-weight: 700;
      line-height: 1.2;
    }

    .oi-panel__copy {
      margin-top: 3px;
      color: var(--text-muted);
      font-size: var(--oi-meta-size);
      line-height: 1.35;
    }

    .oi-year-form {
      min-width: 184px;
    }

    .oi-year-form .form-group {
      margin-bottom: 0;
    }

    .oi-year-form label {
      display: block;
      margin-bottom: 6px;
      color: var(--text-muted);
      font-size: var(--oi-label-size);
      font-weight: 600;
      line-height: 1.25;
    }

    .oi-year-form .form-control {
      min-height: 40px;
      border-color: var(--border-default);
      border-radius: 8px;
      background-color: var(--surface-input);
      color: var(--text-primary);
      font-size: 13px;
      font-weight: 500;
    }

    .oi-year-form .form-control:focus {
      border-color: var(--oi-control-border);
      box-shadow: 0 0 0 3px var(--focus-ring);
    }

    .oi-panel__body {
      padding: 16px 18px 18px;
    }

    .oi-table {
      width: 100%;
      table-layout: fixed;
      margin-bottom: 0;
      font-size: 13px;
    }

    .oi-table th {
      border-top: 0;
      color: var(--text-muted);
      font-size: 11px;
      font-weight: 700;
      line-height: 1.25;
      padding: 10px 8px;
      text-transform: uppercase;
      white-space: normal;
    }

    .oi-table td {
      padding: 12px 8px;
      vertical-align: middle;
      line-height: 1.35;
    }

    .oi-month-name {
      color: var(--text-primary);
      font-size: 14px;
      font-weight: 650;
      line-height: 1.25;
    }

    .oi-month-bar {
      width: 100%;
      max-width: 180px;
      height: 6px;
      overflow: hidden;
      margin-top: 7px;
      border-radius: 999px;
      background: var(--border-subtle);
    }

    .oi-month-bar span {
      display: block;
      height: 100%;
      border-radius: inherit;
      background: var(--sidebar-accent);
    }

    .oi-money {
      display: inline-block;
      font-size: 14px;
      font-weight: 700;
      line-height: 1.25;
      white-space: nowrap;
    }

    .oi-money--negative {
      color: var(--status-danger-fg, #991b1b);
    }

    .oi-status {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 24px;
      padding: 3px 8px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 700;
      line-height: 1.2;
      white-space: nowrap;
    }

    .oi-status--positive {
      border: 1px solid var(--oi-positive-bg);
      background: var(--oi-positive-bg);
      color: var(--oi-positive-fg);
    }

    .oi-status--negative {
      border: 1px solid var(--oi-negative-bg);
      background: var(--oi-negative-bg);
      color: var(--oi-negative-fg);
    }

    .oi-status--neutral {
      border: 1px solid var(--border-default);
      background: var(--oi-neutral-bg);
      color: var(--oi-neutral-fg);
    }

    html[data-theme="dark"] .organizer-income-admin .oi-status--positive,
    html[data-theme="dark"] .organizer-income-admin .oi-status--negative {
      border-color: currentColor;
    }

    .oi-mobile-list {
      display: grid;
      gap: var(--oi-gap);
    }

    .oi-mobile-month {
      padding: 13px 14px 14px;
    }

    .oi-mobile-month:nth-child(even) {
      border-color: var(--oi-card-alt-border);
      background: var(--oi-card-alt-bg);
    }

    .oi-mobile-month__head,
    .oi-mobile-month__meta {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
    }

    .oi-mobile-month__meta {
      margin-top: 11px;
      padding-top: 10px;
      border-top: 1px solid var(--border-subtle);
    }

    @media (max-width: 991.98px) {
      .oi-summary {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    @media (max-width: 575.98px) {
      .organizer-income-admin {
        --oi-gap-loose: 16px;
        --oi-title-size: 16px;
        --oi-value-size: 23px;
      }

      .oi-summary {
        grid-template-columns: 1fr 1fr;
      }

      .oi-metric {
        min-height: 78px;
        padding: 12px;
      }

      .oi-panel__header {
        display: grid;
        padding: 15px 16px;
      }

      .oi-year-form {
        min-width: 0;
      }

      .oi-panel__body {
        padding: 16px;
      }
    }
  </style>
@endsection

@section('content')
  @php
    $selectedYear = request()->input('year') ?: date('Y');
    $monthNames = [
        __('Enero'),
        __('Febrero'),
        __('Marzo'),
        __('Abril'),
        __('Mayo'),
        __('Junio'),
        __('Julio'),
        __('Agosto'),
        __('Septiembre'),
        __('Octubre'),
        __('Noviembre'),
        __('Diciembre'),
    ];

    $formatMoney = function ($amount) use ($settings) {
        $amount = (float) $amount;
        $symbol = $settings->base_currency_symbol ?? '$';
        $number = number_format(abs($amount), 2, ',', '.');
        $prefix = $amount < 0 ? '-' : '';

        if (($settings->base_currency_symbol_position ?? 'left') === 'right') {
            return $prefix . $number . ' ' . $symbol;
        }

        return $prefix . $symbol . ' ' . $number;
    };

    $monthlyRows = [];
    $yearTotal = 0;
    $activeMonths = 0;
    $negativeMonths = 0;
    $bestMonthName = '-';
    $bestMonthValue = null;

    foreach ($monthNames as $index => $monthName) {
        $value = round(
            (float) ($incomes[$index] ?? 0) + (float) ($rejects[$index] ?? 0) - ((float) ($commissions[$index] ?? 0) + (float) ($expenses[$index] ?? 0)),
            2,
        );
        $yearTotal += $value;

        if (abs($value) > 0.009) {
            $activeMonths++;
        }

        if ($value < 0) {
            $negativeMonths++;
        }

        if ($bestMonthValue === null || $value > $bestMonthValue) {
            $bestMonthValue = $value;
            $bestMonthName = $monthName;
        }

        $monthlyRows[] = [
            'name' => $monthName,
            'value' => $value,
            'status' => $value > 0 ? 'positive' : ($value < 0 ? 'negative' : 'neutral'),
            'status_label' => $value > 0 ? __('Con ingresos') : ($value < 0 ? __('Negativo') : __('Sin movimiento')),
        ];
    }

    $maxAbsValue = max(1, ...array_map(fn ($row) => abs($row['value']), $monthlyRows));
  @endphp

  <div class="page-header">
    <h1 class="page-title">{{ __('Ingresos mensuales') }}</h1>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ route('organizer.dashboard') }}" aria-label="{{ __('Ir al panel') }}">
          <i class="flaticon-home" aria-hidden="true"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow" aria-hidden="true"></i>
      </li>
      <li class="nav-item">
        <span aria-current="page">{{ __('Ingresos mensuales') }}</span>
      </li>
    </ul>
  </div>

  <div class="organizer-income-admin">
    <section class="oi-summary" aria-label="{{ __('Resumen de ingresos mensuales') }}">
      <div class="oi-metric">
        <div class="oi-metric__label">{{ __('Neto del año') }}</div>
        <div class="oi-metric__value tuki-data tuki-data-money">{{ $formatMoney($yearTotal) }}</div>
        <div class="oi-metric__hint">{{ $selectedYear }}</div>
      </div>
      <div class="oi-metric">
        <div class="oi-metric__label">{{ __('Mejor mes') }}</div>
        <div class="oi-metric__value tuki-data tuki-data-money">{{ $formatMoney($bestMonthValue ?? 0) }}</div>
        <div class="oi-metric__hint">{{ $bestMonthName }}</div>
      </div>
      <div class="oi-metric">
        <div class="oi-metric__label">{{ __('Meses con ingresos') }}</div>
        <div class="oi-metric__value tuki-data tuki-data-count">{{ number_format($activeMonths, 0, ',', '.') }}/12</div>
      </div>
      <div class="oi-metric">
        <div class="oi-metric__label">{{ __('Meses negativos') }}</div>
        <div class="oi-metric__value tuki-data tuki-data-count">{{ number_format($negativeMonths, 0, ',', '.') }}</div>
      </div>
    </section>

    <section class="oi-panel" aria-labelledby="monthlyIncomeTitle">
      <div class="oi-panel__header">
        <div>
          <h2 id="monthlyIncomeTitle" class="oi-panel__title">{{ __('Detalle mensual') }}</h2>
          <div class="oi-panel__copy">{{ __('Ingresos netos por mes, descontando comisiones y ajustes.') }}</div>
        </div>
        <form action="{{ route('organizer.monthly_income') }}" id="monthlyIncomeYearForm" class="oi-year-form" method="get">
          <div class="form-group">
            <label for="monthlyIncomeYear">{{ __('Año') }}</label>
            <select id="monthlyIncomeYear" class="form-control" name="year"
              onchange="document.getElementById('monthlyIncomeYearForm').submit()">
              @for ($year = 2023; $year <= date('Y'); $year++)
                <option value="{{ $year }}" @selected((string) $selectedYear === (string) $year)>{{ $year }}</option>
              @endfor
            </select>
          </div>
        </form>
      </div>

      <div class="oi-panel__body">
        <div class="table-responsive d-none d-lg-block">
          <table class="table oi-table">
            <caption class="sr-only">{{ __('Ingresos mensuales') }} {{ $selectedYear }}</caption>
            <thead>
              <tr>
                <th scope="col">{{ __('Mes') }}</th>
                <th scope="col">{{ __('Estado') }}</th>
                <th scope="col" class="text-right">{{ __('Ingreso neto') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($monthlyRows as $row)
                @php
                  $barWidth = min(100, round((abs($row['value']) / $maxAbsValue) * 100));
                @endphp
                <tr>
                  <td>
                    <div class="oi-month-name">{{ $row['name'] }}</div>
                    <div class="oi-month-bar" aria-hidden="true"><span style="width: {{ $barWidth }}%"></span></div>
                  </td>
                  <td>
                    <span class="oi-status oi-status--{{ $row['status'] }}">{{ $row['status_label'] }}</span>
                  </td>
                  <td class="text-right">
                    <span class="oi-money tuki-data tuki-data-money {{ $row['value'] < 0 ? 'oi-money--negative' : '' }}">
                      {{ $formatMoney($row['value']) }}
                    </span>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="oi-mobile-list d-lg-none">
          @foreach ($monthlyRows as $row)
            @php
              $barWidth = min(100, round((abs($row['value']) / $maxAbsValue) * 100));
            @endphp
            <article class="oi-mobile-month">
              <div class="oi-mobile-month__head">
                <div>
                  <div class="oi-month-name">{{ $row['name'] }}</div>
                  <div class="oi-month-bar" aria-hidden="true"><span style="width: {{ $barWidth }}%"></span></div>
                </div>
                <span class="oi-status oi-status--{{ $row['status'] }}">{{ $row['status_label'] }}</span>
              </div>
              <div class="oi-mobile-month__meta">
                <span class="oi-label">{{ __('Ingreso neto') }}</span>
                <span class="oi-money tuki-data tuki-data-money {{ $row['value'] < 0 ? 'oi-money--negative' : '' }}">
                  {{ $formatMoney($row['value']) }}
                </span>
              </div>
            </article>
          @endforeach
        </div>
      </div>
    </section>
  </div>
@endsection
