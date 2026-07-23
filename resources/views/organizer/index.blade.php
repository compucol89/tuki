@extends('organizer.layout')

@section('style')
  <style>
    .od-profile-score {
      --od-primary: #e05d38;
      --od-primary-strong: #bf4424;
      --od-text: #1e2532;
      --od-muted: #6b7280;
      --od-surface: #ffffff;
      --od-border: #dcdfe2;
      --od-soft: #f3f4f6;
      display: grid;
      grid-template-columns: minmax(0, 1.1fr) minmax(280px, .9fr) auto;
      gap: 18px;
      align-items: center;
      margin-bottom: 22px;
      padding: 18px 20px;
      border: 1px solid var(--od-border);
      border-radius: 12px;
      background: var(--od-surface);
      box-shadow: 0 14px 30px rgba(30, 37, 50, .07);
    }

    .od-profile-score__eyebrow {
      margin: 0 0 6px;
      color: var(--od-primary);
      font-size: 11px;
      font-weight: 800;
      letter-spacing: .12em;
      line-height: 1;
      text-transform: uppercase;
    }

    .od-profile-score h3 {
      margin: 0;
      color: var(--od-text);
      font-size: 22px;
      font-weight: 800;
      line-height: 1.12;
    }

    .od-profile-score__copy {
      margin: 6px 0 0;
      color: var(--od-muted);
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
      color: var(--od-text);
    }

    .od-profile-score__value strong {
      font-size: 28px;
      font-weight: 800;
      line-height: 1;
    }

    .od-profile-score__value span {
      color: var(--od-muted);
      font-size: 12px;
      font-weight: 800;
    }

    .od-profile-score__bar {
      height: 9px;
      overflow: hidden;
      border-radius: 999px;
      background: var(--od-soft);
    }

    .od-profile-score__bar span {
      display: block;
      height: 100%;
      border-radius: inherit;
      background: var(--od-primary);
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
      font-weight: 800;
      line-height: 1.1;
      text-decoration: none;
      transition: transform .16s ease, border-color .16s ease, color .16s ease, background .16s ease, box-shadow .16s ease;
    }

    .od-profile-score__actions a {
      border: 1px solid rgba(224, 93, 56, .18);
      background: rgba(224, 93, 56, .08);
      color: var(--od-primary-strong);
    }

    .od-profile-score__buttons {
      display: grid;
      gap: 8px;
      min-width: 164px;
    }

    .od-profile-score__buttons a {
      border: 1px solid var(--od-border);
      background: #fff;
      color: var(--od-text);
      white-space: nowrap;
    }

    .od-profile-score__buttons a:first-child {
      border-color: var(--od-primary);
      background: var(--od-primary);
      color: #fff;
    }

    .od-profile-score__actions a:hover,
    .od-profile-score__actions a:focus,
    .od-profile-score__buttons a:hover,
    .od-profile-score__buttons a:focus {
      border-color: var(--od-primary);
      color: var(--od-primary-strong);
      text-decoration: none;
      transform: translateY(-1px);
      box-shadow: 0 0 0 4px rgba(224, 93, 56, .14);
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
      background: rgba(224, 93, 56, .12);
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
      color: var(--od-muted);
      font-size: 11px;
      font-weight: 600;
      line-height: 1.28;
    }

    .od-profile-score__buttons a:first-child:hover,
    .od-profile-score__buttons a:first-child:focus {
      background: var(--od-primary-strong);
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

  <div class="mt-2 mb-4">
    <h2 class=" pb-2 ">{{ __('Welcome back') .','}} {{ Auth::guard('organizer')->user()->username . '!' }}</h2>
  </div>

  @if (Session::get('secret_login') != true)
    @if (Auth::guard('organizer')->user()->status == 0 && $admin_setting->organizer_admin_approval == 1)
      <div class="mt-2 mb-4">
        <div class="alert alert-danger text-dark">
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
        <strong>{{ $profileDashboard['percent'] }}%</strong>
        <span>{{ $profileDashboard['done'] }}/{{ $profileDashboard['total'] }} {{ __('listo') }}</span>
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
                  <i class="fas fa-sack-dollar"></i>
                </div>
              </div>

              <div class="col-7 col-stats">
                <div class="numbers">
                  <p class="card-category">{{ __('Pendiente por liquidar') }}</p>
                  <h4 class="card-title">
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
                  <i class="fas fa-calendar-alt"></i>
                </div>
              </div>

              <div class="col-7 col-stats">
                <div class="numbers">
                  <p class="card-category">{{ __('Events') }}</p>
                  <h4 class="card-title">{{ $total_events }}</h4>
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
                  <i class="fas fa-presentation"></i>
                </div>
              </div>
              <div class="col-7 col-stats">
                <div class="numbers">
                  <p class="card-category">{{ __('Total Event Bookings') }}</p>
                  <h4 class="card-title">{{ $total_event_bookings }}</h4>
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
                  <i class="fas fa-exchange-alt"></i>
                </div>
              </div>

              <div class="col-7 col-stats">
                <div class="numbers">
                  <p class="card-category">{{ __('Total Transcation') }}</p>
                  <h4 class="card-title">{{ $transcation_count }}
                  </h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header">
          <div class="card-title">{{ __('Event Booking Monthly Income') }} ({{ date('Y') }})</div>
        </div>

        <div class="card-body">
          <div class="chart-container">
            <canvas id="incomeChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card">
        <div class="card-header">
          <div class="card-title">{{ __('Monthly Event Bookings') }} ({{ date('Y') }})</div>
        </div>

        <div class="card-body">
          <div class="chart-container">
            <canvas id="TotalEventBookingChart"></canvas>
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
