@extends('organizer.layout')

@section('style')
  <style>
    .od-profile-score {
      display: grid;
      grid-template-columns: minmax(0, 1.1fr) minmax(280px, .9fr) auto;
      gap: 18px;
      align-items: center;
      margin-bottom: 22px;
      padding: 18px 20px;
      border: 1px solid var(--border-default);
      border-radius: var(--adm-radius-2xl);
      background: linear-gradient(180deg, var(--surface-card) 0%, var(--surface-card-soft) 100%);
      box-shadow: 0 14px 30px rgba(30, 37, 50, .07);
    }

    .od-profile-score__eyebrow {
      margin: 0 0 6px;
      color: var(--adm-primary-dark);
      font-size: 11px;
      font-weight: 600;
      letter-spacing: .10em;
      line-height: 1;
      text-transform: uppercase;
    }

    .od-profile-score h3 {
      margin: 0;
      color: var(--text-primary);
      font-size: 22px;
      font-weight: 600;
      line-height: 1.12;
    }

    .od-profile-score__copy {
      margin: 6px 0 0;
      color: var(--text-secondary);
      font-size: 13px;
      line-height: 1.5;
    }

    .od-profile-score__meter {
      min-width: 0;
    }

    .od-profile-score__value {
      display: flex;
      align-items: baseline;
      justify-content: space-between;
      gap: 10px;
      margin-bottom: 8px;
      color: var(--text-primary);
    }

    .od-profile-score__value strong {
      font-size: 28px;
      font-weight: 600;
      line-height: 1;
    }

    .od-profile-score__value span {
      color: var(--text-secondary);
      font-size: 12px;
      font-weight: 600;
    }

    .od-profile-score__bar {
      height: 9px;
      overflow: hidden;
      border-radius: 999px;
      background: var(--border-default);
    }

    .od-profile-score__bar span {
      display: block;
      height: 100%;
      border-radius: inherit;
      background: var(--adm-primary);
    }

    .od-profile-score__actions {
      display: grid;
      gap: 8px;
      margin-top: 10px;
    }

    .od-profile-score__actions a,
    .od-profile-score__buttons a {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 7px;
      min-height: 38px;
      padding: 9px 12px;
      border-radius: 10px;
      font-size: 12px;
      font-weight: 600;
      line-height: 1.1;
      text-decoration: none;
      transition: transform .16s ease, border-color .16s ease, color .16s ease, background .16s ease, box-shadow .16s ease;
    }

    .od-profile-score__actions a {
      border: 1px solid rgba(249, 115, 22, .22);
      background: var(--adm-primary-soft);
      color: var(--adm-primary-strong);
    }

    .od-profile-score__buttons {
      display: grid;
      gap: 8px;
      min-width: 164px;
    }

    .od-profile-score__buttons a {
      border: 1px solid var(--border-default);
      background: var(--surface-card);
      color: var(--text-primary);
      white-space: nowrap;
    }

    .od-profile-score__buttons a:first-child {
      border-color: var(--adm-primary-dark);
      background: var(--adm-primary-dark);
      color: #fff;
    }

    .od-profile-score__actions a:hover,
    .od-profile-score__actions a:focus,
    .od-profile-score__buttons a:hover,
    .od-profile-score__buttons a:focus {
      border-color: var(--adm-primary);
      color: var(--adm-primary-strong);
      text-decoration: none;
      transform: translateY(-1px);
      box-shadow: 0 0 0 4px var(--adm-ring);
    }

    .od-profile-score__actions a {
      justify-content: flex-start;
      min-height: 52px;
      text-align: left;
    }

    .od-profile-score__actions a i {
      display: inline-grid;
      flex: 0 0 28px;
      width: 28px;
      height: 28px;
      place-items: center;
      border-radius: 999px;
      background: rgba(249, 115, 22, .12);
      font-size: 13px;
    }

    .od-profile-score__action-text {
      min-width: 0;
    }

    .od-profile-score__action-label {
      display: block;
    }

    .od-profile-score__action-hint {
      display: block;
      margin-top: 2px;
      color: var(--text-secondary);
      font-size: 11px;
      font-weight: 500;
      line-height: 1.28;
    }

    .od-profile-score__buttons a:first-child:hover,
    .od-profile-score__buttons a:first-child:focus {
      background: var(--adm-primary-strong);
      border-color: var(--adm-primary-strong);
      color: #fff;
    }

    @media (max-width: 991.98px) {
      .od-profile-score {
        grid-template-columns: 1fr;
      }

      .od-profile-score__buttons {
        display: flex;
        flex-wrap: wrap;
      }
    }

    .organizer-dashboard-page {
      --od-section-gap: 24px;
      --od-card-gap: 16px;
    }

    .organizer-dashboard-page .od-page-header {
      margin: 0 0 var(--od-section-gap) !important;
    }

    .organizer-dashboard-page .od-page-header h1 {
      margin: 0;
      color: var(--text-primary);
      font-size: 28px;
      font-weight: 500;
      line-height: 1.18;
    }

    .organizer-dashboard-page .od-profile-score {
      grid-template-columns: 1fr;
      gap: 16px;
      align-items: start;
      margin-bottom: var(--od-section-gap);
      padding: 20px;
    }

    .organizer-dashboard-page .od-profile-score__actions {
      gap: 8px;
      margin-top: 12px;
    }

    .organizer-dashboard-page .od-profile-score__buttons {
      min-width: 0;
    }

    .organizer-dashboard-page .dashboard-items.row {
      gap: var(--od-card-gap);
      grid-template-columns: 1fr;
      margin-bottom: var(--od-section-gap);
    }

    .organizer-dashboard-page .dashboard-items > .col-lg-6:not(.col-xl-3) {
      grid-column: 1 / -1;
    }

    .organizer-dashboard-page .dashboard-items .card-stats {
      min-height: 108px;
    }

    .organizer-dashboard-page .dashboard-items .card-stats .card-body {
      padding: 18px !important;
    }

    .organizer-dashboard-page .dashboard-items .card-stats .card-body > .row {
      gap: 14px;
      grid-template-columns: 50px minmax(0, 1fr);
    }

    .organizer-dashboard-page .dashboard-items .card-stats .icon-big {
      height: 50px;
      width: 50px;
    }

    .organizer-dashboard-page .dashboard-items .card-stats .numbers .card-category {
      margin-bottom: 6px !important;
    }

    .organizer-dashboard-page .dashboard-items .card-stats .numbers .card-title {
      font-family: var(--tuki-font-data, 'IBM Plex Mono', ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace) !important;
      font-size: 21px !important;
      font-weight: 600 !important;
      letter-spacing: 0 !important;
    }

    .organizer-dashboard-page .dashboard-items .card-stats .numbers small {
      color: var(--text-secondary);
      font-size: 12px;
      line-height: 1.35;
    }

    .organizer-dashboard-page .od-chart-card {
      height: 100%;
      margin-bottom: 0 !important;
      border: 1px solid var(--border-default);
      border-radius: var(--adm-radius-xl);
      box-shadow: var(--adm-shadow-sm);
    }

    .organizer-dashboard-page .od-chart-card .card-header {
      min-height: 56px;
      padding: 16px 20px !important;
    }

    .organizer-dashboard-page .od-chart-card .card-body {
      padding: 18px !important;
    }

    .organizer-dashboard-page .od-chart-card .chart-container {
      height: 260px;
      min-height: 260px;
    }

    @media (min-width: 480px) {
      .organizer-dashboard-page .dashboard-items.row {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    @media (min-width: 1200px) {
      .organizer-dashboard-page .od-profile-score {
        grid-template-columns: minmax(260px, .75fr) minmax(500px, 1.25fr) minmax(154px, auto);
        gap: 24px;
        align-items: center;
        padding: 20px 22px;
      }

      .organizer-dashboard-page .od-profile-score__actions {
        grid-template-columns: repeat(3, minmax(0, 1fr));
      }

      .organizer-dashboard-page .od-profile-score__actions a {
        min-height: 58px;
      }

      .organizer-dashboard-page .dashboard-items.row {
        grid-template-columns: repeat(4, minmax(0, 1fr));
      }

      .organizer-dashboard-page .dashboard-items > .col-lg-6:not(.col-xl-3) {
        grid-column: span 2;
      }

      .organizer-dashboard-page .od-chart-card .chart-container {
        height: 300px;
        min-height: 300px;
      }
    }

    @media (max-width: 479.98px) {
      .organizer-dashboard-page {
        --od-section-gap: 22px;
        --od-card-gap: 12px;
      }

      .organizer-dashboard-page .od-page-header h1 {
        font-size: 26px;
      }

      .organizer-dashboard-page .dashboard-items .card-stats {
        min-height: 102px;
      }

      .organizer-dashboard-page .dashboard-items .card-stats .card-body {
        padding: 16px !important;
      }

      .organizer-dashboard-page .dashboard-items .card-stats .card-body > .row {
        gap: 12px;
        grid-template-columns: 48px minmax(0, 1fr);
      }

      .organizer-dashboard-page .od-chart-card .chart-container {
        height: 230px;
        min-height: 230px;
      }
    }
  </style>
@endsection

@section('content')
  @php
    $dashboardCurrencySettings = $settings ?? null;
    $formatDashboardMoney = function ($amount) use ($dashboardCurrencySettings) {
        $symbol = optional($dashboardCurrencySettings)->base_currency_symbol ?: '$';
        $position = optional($dashboardCurrencySettings)->base_currency_symbol_position ?: 'left';
        $amount = number_format((float) $amount, 0, ',', '.');
        return ($position == 'left' ? $symbol : '') . $amount . ($position == 'right' ? $symbol : '');
    };
    $profileDashboard = $profileDashboard ?? [
      'percent' => 0,
      'done' => 0,
      'total' => 1,
      'label' => __('Perfil por completar'),
      'copy' => __('Completá tu perfil público para que venda mejor tu agenda.'),
      'next_actions' => collect(),
      'public_url' => route('frontend.all.organizer'),
      'upcoming' => 0,
    ];
  @endphp

  <div class="organizer-dashboard-page">
  <div class="mt-2 mb-4 od-page-header">
    <h1>{{ __('Bienvenido de vuelta') .','}} {{ Auth::guard('organizer')->user()->username . '!' }}</h1>
  </div>

  @if (Session::get('secret_login') != true)
    @if (Auth::guard('organizer')->user()->status == 0 && $admin_setting->organizer_admin_approval == 1)
      <div class="mt-2 mb-4">
        <div class="alert alert-danger text-dark" role="alert">
          {{ $admin_setting->admin_approval_notice != null ? $admin_setting->admin_approval_notice : __('Tu cuenta esta pendiente de aprobacion por parte del equipo administrador.') }}
        </div>
      </div>
    @endif
  @endif

  <section class="od-profile-score" aria-labelledby="organizer-dashboard-profile-title">
    <div>
      <p class="od-profile-score__eyebrow">{{ __('Perfil público') }}</p>
      <h3 id="organizer-dashboard-profile-title">{{ $profileDashboard['label'] }}</h3>
      <p class="od-profile-score__copy">{{ $profileDashboard['copy'] }}</p>
    </div>

    <div class="od-profile-score__meter" aria-label="{{ __('Calidad del perfil público') }}">
      <div class="od-profile-score__value">
        <strong class="tuki-data">{{ $profileDashboard['percent'] }}%</strong>
        <span class="tuki-data">{{ $profileDashboard['done'] }}/{{ $profileDashboard['total'] }} {{ __('listo') }}</span>
      </div>
      <div class="od-profile-score__bar" aria-hidden="true">
        <span style="width: {{ $profileDashboard['percent'] }}%;"></span>
      </div>

      @if($profileDashboard['next_actions']->isNotEmpty())
        <div class="od-profile-score__actions">
          @foreach($profileDashboard['next_actions'] as $action)
            <a href="{{ $action['href'] }}">
              <i class="{{ $action['icon'] ?? 'fas fa-arrow-right' }}" aria-hidden="true"></i>
              <span class="od-profile-score__action-text">
                <span class="od-profile-score__action-label">{{ $action['label'] }}</span>
                <span class="od-profile-score__action-hint">{{ $action['hint'] }}</span>
              </span>
            </a>
          @endforeach
        </div>
      @endif
    </div>

    <div class="od-profile-score__buttons">
      <a href="{{ route('organizer.edit.profile') }}">
        <i class="fas fa-user-edit" aria-hidden="true"></i>
        {{ __('Completar perfil') }}
      </a>
      <a href="{{ $profileDashboard['public_url'] }}" target="_blank" rel="noopener">
        <i class="fas fa-external-link-alt" aria-hidden="true"></i>
        {{ __('Ver perfil público') }}
      </a>
    </div>
  </section>

  <div class="row dashboard-items">
    <div class="col-xl-3 col-lg-6">
      <a href="{{ route('organizer.monthly_income') }}">
        <div class="card card-stats card-info card-round">
          <div class="card-body">
            <div class="row">
              <div class="col-5">
                <div class="icon-big text-center">
                   <i class="fas fa-sack-dollar" aria-hidden="true"></i>
                </div>
              </div>

              <div class="col-7 col-stats">
                <div class="numbers">
                  <p class="card-category">{{ __('Pendiente por liquidar') }}</p>
                  <h4 class="card-title tuki-data tuki-data-money">
                    {{ $formatDashboardMoney($settlementSummary['pending_organizer_amount'] ?? 0) }}
                  </h4>
                  <small class="d-block">{{ __('Disponible') }}: {{ $formatDashboardMoney(Auth::guard('organizer')->user()->amount) }}</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-xl-3 col-lg-6">
      <a href="{{ route('organizer.event_management.event', ['language' => $defaultLang->code]) }}">
        <div class="card card-stats card-success card-round">
          <div class="card-body">
            <div class="row">
              <div class="col-5">
                <div class="icon-big text-center">
                   <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                </div>
              </div>

              <div class="col-7 col-stats">
                <div class="numbers">
                  <p class="card-category">{{ __('Events') }}</p>
                  <h4 class="card-title tuki-data tuki-data-count">{{ $total_events }}</h4>
                  <small class="d-block">{{ __('Pendientes') }}: {{ $settlementSummary['pending_events_count'] ?? 0 }}</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-xl-3 col-lg-6">
      <a href="{{ route('organizer.event.booking') }}">
        <div class="card card-stats card-danger card-round">
          <div class="card-body">
            <div class="row">
              <div class="col-5">
                <div class="icon-big text-center">
                   <i class="fas fa-presentation" aria-hidden="true"></i>
                </div>
              </div>
              <div class="col-7 col-stats">
                <div class="numbers">
                  <p class="card-category">{{ __('Total Event Bookings') }}</p>
                  <h4 class="card-title tuki-data tuki-data-count">{{ $total_event_bookings }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-xl-3 col-lg-6">
      <a href="{{ route('organizer.transcation') }}">
        <div class="card card-stats card-secondary card-round">
          <div class="card-body">
            <div class="row">
              <div class="col-5">
                <div class="icon-big text-center">
                   <i class="fas fa-exchange-alt" aria-hidden="true"></i>
                </div>
              </div>

              <div class="col-7 col-stats">
                <div class="numbers">
                  <p class="card-category">{{ __('Total Transcation') }}</p>
                  <h4 class="card-title tuki-data tuki-data-count">{{ $transcation_count }}
                  </h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-lg-6">
      <div class="card od-chart-card">
        <div class="card-header">
          <div class="card-title">{{ __('Event Booking Monthly Income') }} ({{ date('Y') }})</div>
        </div>

        <div class="card-body">
          <div class="chart-container">
            <canvas id="incomeChart" role="img" aria-label="{{ __('Event Booking Monthly Income') }} ({{ date('Y') }})"><span class="visually-hidden">{{ __('Gráfico de ingresos mensuales por reservas de eventos.') }}</span></canvas>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card od-chart-card">
        <div class="card-header">
          <div class="card-title">{{ __('Monthly Event Bookings') }} ({{ date('Y') }})</div>
        </div>

        <div class="card-body">
          <div class="chart-container">
            <canvas id="TotalEventBookingChart" role="img" aria-label="{{ __('Monthly Event Bookings') }} ({{ date('Y') }})"><span class="visually-hidden">{{ __('Gráfico de reservas mensuales por eventos.') }}</span></canvas>
          </div>
        </div>
      </div>
    </div>

  </div>
  </div>
@endsection

@section('script')
  {{-- chart js --}}
  <script type="text/javascript" src="{{ asset('assets/admin/js/chart.min.js') }}"></script>

  <script>
    "use strict";
    const monthArr = @php echo json_encode($eventMonths) @endphp;
    const incomeArr = @php echo json_encode($eventIncomes) @endphp;
    const totalBookings = @php echo json_encode($totalBookings) @endphp;
  </script>

  <script type="text/javascript" src="{{ asset('assets/admin/js/chart-init.js') }}"></script>
@endsection
