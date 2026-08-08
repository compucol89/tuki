@extends('backend.layout')

@section('style')
  <style>
    .admin-organizers-index {
      --oi-ink: #1e2532;
      --oi-ink-strong: #111827;
      --oi-muted: #667085;
      --oi-border: #e4e7ec;
      --oi-soft: #f8fafc;
      --oi-card: #ffffff;
      --oi-header: #fbfcfd;
      --oi-thead: #edf4f9;
      --oi-primary: #e05d38;
      --oi-label-bg: #f3f4f6;
      --oi-label-fg: #64748b;
      --oi-bar-bg: #edf0f2;
      --oi-chip-bg: #f9fafb;
      --oi-strong-bg: rgba(22, 163, 74, .12);
      --oi-strong-fg: #15803d;
      --oi-mid-bg: rgba(224, 93, 56, .12);
      --oi-mid-fg: #bf4424;
      --oi-low-bg: rgba(220, 38, 38, .12);
      --oi-low-fg: #b91c1c;
      --oi-success-bg: #14352a;
      --oi-success-fg: #4ade80;
      --oi-danger-bg: #3a2228;
      --oi-danger-fg: #f87171;
      color: var(--oi-ink);
    }

    .admin-organizers-index .page-title {
      color: var(--oi-ink-strong) !important;
      font-size: 24px !important;
      font-weight: 750 !important;
    }

    .admin-organizers-index .breadcrumbs,
    .admin-organizers-index .breadcrumbs a {
      color: var(--oi-muted) !important;
    }

    .admin-organizers-index > .row > [class*="col-"] > .card {
      background: var(--oi-card) !important;
      border-color: var(--oi-border) !important;
      box-shadow: none !important;
    }

    .admin-organizers-index .card-header,
    .admin-organizers-index .card-footer {
      background: var(--oi-header) !important;
      border-color: var(--oi-border) !important;
    }

    .admin-organizers-index .card-title {
      color: var(--oi-ink-strong) !important;
    }

    .admin-organizers-index .card-body {
      background: var(--oi-card) !important;
      color: var(--oi-ink) !important;
    }

    .admin-profile-quality {
      min-width: 190px;
      color: var(--oi-ink);
    }

    .admin-profile-quality__head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      margin-bottom: 7px;
    }

    .admin-profile-quality__head strong {
      color: var(--oi-ink-strong);
      font-size: 17px;
      font-weight: 800;
      line-height: 1;
    }

    .admin-profile-quality__label {
      display: inline-flex;
      align-items: center;
      min-height: 22px;
      padding: 4px 8px;
      border-radius: 999px;
      background: var(--oi-label-bg);
      color: var(--oi-label-fg);
      font-size: 10px;
      font-weight: 800;
      line-height: 1;
      text-transform: uppercase;
      white-space: nowrap;
    }

    .admin-profile-quality__label.is-strong {
      background: var(--oi-strong-bg);
      color: var(--oi-strong-fg);
    }

    .admin-profile-quality__label.is-mid {
      background: var(--oi-mid-bg);
      color: var(--oi-mid-fg);
    }

    .admin-profile-quality__label.is-low {
      background: var(--oi-low-bg);
      color: var(--oi-low-fg);
    }

    .admin-profile-quality__bar {
      height: 7px;
      overflow: hidden;
      border-radius: 999px;
      background: var(--oi-bar-bg);
    }

    .admin-profile-quality__bar span {
      display: block;
      height: 100%;
      border-radius: inherit;
      background: var(--oi-primary);
    }

    .admin-profile-quality__missing {
      display: flex;
      flex-wrap: wrap;
      gap: 5px;
      margin-top: 8px;
    }

    .admin-profile-quality__missing span {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 4px 7px;
      border-radius: 999px;
      background: var(--oi-chip-bg);
      color: var(--oi-muted);
      font-size: 10px;
      font-weight: 700;
      line-height: 1;
    }

    .admin-profile-quality__missing i {
      color: var(--oi-primary);
      font-size: 9px;
    }

    .admin-profile-filters {
      display: flex;
      flex-wrap: wrap;
      justify-content: flex-end;
      gap: 8px;
    }

    .admin-profile-filters .form-control {
      width: auto;
      min-width: 190px;
      height: 38px;
      border-radius: 8px;
      background: var(--oi-soft) !important;
      border-color: var(--oi-border) !important;
      color: var(--oi-ink) !important;
    }

    .admin-profile-filters .form-control:focus {
      border-color: var(--oi-primary) !important;
      box-shadow: 0 0 0 3px rgba(224, 93, 56, .22);
    }

    .admin-profile-filters .btn {
      height: 38px;
      border-radius: 8px;
      font-weight: 700;
    }

    .admin-profile-filters .btn-light {
      background: var(--oi-soft) !important;
      border-color: var(--oi-border) !important;
      color: var(--oi-ink) !important;
    }

    .admin-organizers-index .table {
      color: var(--oi-ink) !important;
    }

    .admin-organizers-index .table thead th {
      background: var(--oi-thead) !important;
      border-color: var(--oi-border) !important;
      color: var(--oi-muted) !important;
    }

    .admin-organizers-index .table td,
    .admin-organizers-index .table th {
      border-color: var(--oi-border) !important;
      color: var(--oi-ink) !important;
      vertical-align: middle;
    }

    .admin-organizers-index .table-striped tbody tr:nth-of-type(odd) {
      background: var(--oi-soft) !important;
    }

    .admin-organizers-index .table-striped tbody tr:nth-of-type(even) {
      background: var(--oi-card) !important;
    }

    .admin-organizers-index select.form-control-sm.bg-success {
      background: #ecfdf5 !important;
      border-color: #bbf7d0 !important;
      color: #047857 !important;
    }

    .admin-organizers-index select.form-control-sm.bg-danger {
      background: #fef2f2 !important;
      border-color: #fecdd3 !important;
      color: #be123c !important;
    }

    .admin-organizers-index .dropdown-menu {
      background: var(--oi-card) !important;
      border-color: var(--oi-border) !important;
    }

    .admin-organizers-index .dropdown-item,
    .admin-organizers-index .deleteBtn {
      background: var(--oi-card) !important;
      color: var(--oi-ink) !important;
    }

    .admin-organizers-index .dropdown-item:hover,
    .admin-organizers-index .deleteBtn:hover {
      background: var(--oi-soft) !important;
      color: var(--oi-ink-strong) !important;
    }

    .admin-organizers-index .admin-organizers-empty {
      padding: 36px 16px;
      color: var(--oi-muted);
      text-align: center;
      font-size: 16px;
      font-weight: 700;
    }

    @media (max-width: 991.98px) {
      .admin-profile-filters {
        justify-content: flex-start;
        margin-top: 12px;
      }

      .admin-profile-filters .form-control,
      .admin-profile-filters .btn {
        width: 100%;
      }
    }

    html[data-theme="dark"] .admin-organizers-index {
      --oi-ink: #e5e5e5;
      --oi-ink-strong: #ffffff;
      --oi-muted: #a3a3a3;
      --oi-border: #3d4354;
      --oi-soft: #1f2838;
      --oi-card: #2a3040;
      --oi-header: #252b38;
      --oi-thead: #1f2838;
      --oi-label-bg: #303750;
      --oi-label-fg: #c8cdd6;
      --oi-bar-bg: #303750;
      --oi-chip-bg: #252b38;
      --oi-strong-bg: #14352a;
      --oi-strong-fg: #4ade80;
      --oi-mid-bg: #3a2c26;
      --oi-mid-fg: #fdba74;
      --oi-low-bg: #3a2228;
      --oi-low-fg: #f87171;
    }

    html[data-theme="dark"] .admin-organizers-index > .row > [class*="col-"] > .card,
    html[data-theme="dark"] .admin-organizers-index .card-body {
      background: var(--oi-card) !important;
      border-color: var(--oi-border) !important;
    }

    html[data-theme="dark"] .admin-organizers-index .card-header,
    html[data-theme="dark"] .admin-organizers-index .card-footer {
      background: var(--oi-header) !important;
      border-color: var(--oi-border) !important;
    }

    html[data-theme="dark"] .admin-organizers-index select.form-control-sm.bg-success {
      background: #14352a !important;
      border-color: #166534 !important;
      color: #4ade80 !important;
    }

    html[data-theme="dark"] .admin-organizers-index select.form-control-sm.bg-danger {
      background: #3a2228 !important;
      border-color: #7f1d1d !important;
      color: #f87171 !important;
    }
  </style>
@endsection

@section('content')
  <div class="admin-organizers-index">
  <div class="page-header">
    <h4 class="page-title">{{ __('Registered Organizers') }}</h4>
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
        <a href="#">{{ __('Organizers Management') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Registered Organizers') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-lg-3">
              <div class="card-title">{{ __('All Organizers') }}</div>
            </div>

            <div class="col-lg-9">
              <button class="btn btn-danger btn-sm float-right d-none bulk-delete mr-2 ml-3 mt-1"
                data-href="{{ route('admin.organizer_management.bulk_delete_organizer') }}">
                <i class="flaticon-interface-5"></i> {{ __('Delete') }}
              </button>

              <form class="admin-profile-filters" action="{{ route('admin.organizer_management.registered_organizer') }}"
                method="GET">
                <select name="profile_filter" class="form-control">
                  <option value="">{{ __('Todas las oportunidades') }}</option>
                  @foreach($profileOpportunityFilters as $filterValue => $filterLabel)
                    <option value="{{ $filterValue }}" {{ (string) $profileFilter === (string) $filterValue ? 'selected' : '' }}>
                      {{ $filterLabel }}
                    </option>
                  @endforeach
                </select>
                <input name="info" type="text" class="form-control"
                  placeholder="{{ __('Search By Username or Email ID') }}"
                  value="{{ !empty(request()->input('info')) ? request()->input('info') : '' }}">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-filter" aria-hidden="true"></i>
                  {{ __('Filtrar') }}
                </button>
                @if(request()->filled('info') || request()->filled('profile_filter'))
                  <a class="btn btn-light" href="{{ route('admin.organizer_management.registered_organizer') }}">
                    {{ __('Limpiar') }}
                  </a>
                @endif
              </form>
            </div>
          </div>
        </div>

        <div class="card-body">
          <div class="row">
            <div class="col-lg-12">
              @if (count($organizers) == 0)
                <h3 class="admin-organizers-empty">{{ __('NO ORGANIZER FOUND') . '!' }}</h3>
              @else
                <div class="table-responsive">
                  <table class="table table-striped mt-3">
                    <thead>
                      <tr>
                        <th scope="col">
                          <input type="checkbox" class="bulk-check" data-val="all">
                        </th>
                        <th scope="col">{{ __('Username') }}</th>
                        <th scope="col">{{ __('Email ID') }}</th>
                        <th scope="col">{{ __('Phone') }}</th>
                        <th scope="col">{{ __('Perfil público') }}</th>
                        <th scope="col">{{ __('Account Status') }}</th>
                        <th scope="col">{{ __('Email Status') }}</th>
                        <th scope="col">{{ __('Actions') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($organizers as $organizer)
                        @php
                          $profileQuality = $profileQualityByOrganizer[$organizer->id] ?? null;
                        @endphp
                        <tr>
                          <td>
                            <input type="checkbox" class="bulk-check" data-val="{{ $organizer->id }}">
                          </td>
                          <td>{{ $organizer->username }}</td>
                          <td>{{ $organizer->email }}</td>
                          <td>{{ empty($organizer->phone) ? '-' : $organizer->phone }}</td>
                          <td>
                            @if($profileQuality)
                              <div class="admin-profile-quality">
                                <div class="admin-profile-quality__head">
                                  <strong>{{ $profileQuality['percent'] }}%</strong>
                                  <span class="admin-profile-quality__label {{ $profileQuality['tone'] }}">
                                    {{ $profileQuality['label'] }}
                                  </span>
                                </div>
                                <div class="admin-profile-quality__bar" aria-hidden="true">
                                  <span style="width: {{ $profileQuality['percent'] }}%;"></span>
                                </div>
                                @if($profileQuality['missing']->isNotEmpty())
                                  <div class="admin-profile-quality__missing" aria-label="{{ __('Señales pendientes') }}">
                                    @foreach($profileQuality['missing'] as $missingSignal)
                                      <span>
                                        <i class="fas fa-circle" aria-hidden="true"></i>
                                        {{ $missingSignal['label'] }}
                                      </span>
                                    @endforeach
                                  </div>
                                @else
                                  <div class="admin-profile-quality__missing" aria-label="{{ __('Perfil completo') }}">
                                    <span>
                                      <i class="fas fa-check" aria-hidden="true"></i>
                                      {{ __('Listo') }}
                                    </span>
                                  </div>
                                @endif
                              </div>
                            @else
                              -
                            @endif
                          </td>
                          <td>
                            <form id="accountStatusForm-{{ $organizer->id }}" class="d-inline-block"
                              action="{{ route('admin.organizer_management.organizer.update_account_status', ['id' => $organizer->id]) }}"
                              method="post">
                              @csrf
                              <select
                                class="form-control form-control-sm {{ $organizer->status == 1 ? 'bg-success' : 'bg-danger' }}"
                                name="account_status"
                                onchange="document.getElementById('accountStatusForm-{{ $organizer->id }}').submit()">
                                <option value="1" {{ $organizer->status == 1 ? 'selected' : '' }}>
                                  {{ __('Active') }}
                                </option>
                                <option value="0" {{ $organizer->status == 0 ? 'selected' : '' }}>
                                  {{ __('Deactive') }}
                                </option>
                              </select>
                            </form>
                          </td>
                          <td>
                            <form id="emailStatusForm-{{ $organizer->id }}" class="d-inline-block"
                              action="{{ route('admin.organizer_management.organizer.update_email_status', ['id' => $organizer->id]) }}"
                              method="post">
                              @csrf
                              <select
                                class="form-control form-control-sm {{ !is_null($organizer->email_verified_at) ? 'bg-success' : 'bg-danger' }}"
                                name="email_status"
                                onchange="document.getElementById('emailStatusForm-{{ $organizer->id }}').submit()">
                                <option value="1" {{ !is_null($organizer->email_verified_at) ? 'selected' : '' }}>
                                  {{ __('Verified') }}
                                </option>
                                <option value="0" {{ is_null($organizer->email_verified_at) ? 'selected' : '' }}>
                                  {{ __('Not Verified') }}
                                </option>
                              </select>
                            </form>
                          </td>
                          <td>
                            <div class="dropdown">
                              <button class="btn btn-secondary dropdown-toggle btn-sm" type="button"
                                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ __('Select') }}
                              </button>

                              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a href="{{ route('admin.organizer_management.organizer_details', ['id' => $organizer->id, 'language' => $defaultLang->code]) }}"
                                  class="dropdown-item">
                                  {{ __('Details') }}
                                </a>

                                <a href="{{ route('admin.edit_management.organizer_edit', ['id' => $organizer->id]) }}"
                                  class="dropdown-item">
                                  {{ __('Edit') }}
                                </a>

                                <a href="{{ route('admin.organizer_management.organizer.change_password', ['id' => $organizer->id]) }}"
                                  class="dropdown-item">
                                  {{ __('Change Password') }}
                                </a>

                                <form class="deleteForm d-block"
                                  action="{{ route('admin.organizer_management.organizer.delete', ['id' => $organizer->id]) }}"
                                  method="post">
                                  @csrf
                                  <button type="submit" class="deleteBtn">
                                    {{ __('Delete') }}
                                  </button>
                                </form>

                                <form class="d-block"
                                  action="{{ route('admin.organizer_management.organizer.secret_login', ['id' => $organizer->id]) }}"
                                  method="post" target="_blank">
                                  @csrf
                                  <button type="submit" class="dropdown-item border-0 bg-transparent text-left">
                                    {{ __('Secret Login') }}
                                  </button>
                                </form>
                              </div>
                            </div>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @endif
            </div>
          </div>
        </div>

        <div class="card-footer text-center">
          <div class="d-inline-block mt-3">
            {{ $organizers->appends(['info' => request()->input('info'), 'profile_filter' => request()->input('profile_filter')])->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
@endsection
