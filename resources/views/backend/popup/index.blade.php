@extends('backend.layout')

@section('style')
  <style>
    .admin-popups-page {
      --ap-ink: #1e2532;
      --ap-ink-strong: #111827;
      --ap-muted: #667085;
      --ap-border: #e4e7ec;
      --ap-soft: #f8fafc;
      --ap-card: #ffffff;
      --ap-header: #fbfcfd;
      --ap-warning-bg: #fff7ed;
      --ap-warning-border: #fdba74;
      --ap-warning-ink: #9a3412;
      --ap-radius: 8px;
      color: var(--ap-ink);
    }

    .admin-popups-page .page-header { margin-bottom: 16px; }
    .admin-popups-page .page-title {
      color: var(--ap-ink-strong) !important;
      font-size: 24px !important;
      font-weight: 750 !important;
    }
    .admin-popups-page .breadcrumbs,
    .admin-popups-page .breadcrumbs a {
      color: var(--ap-muted) !important;
      font-size: 12.5px;
    }

    .admin-popups-page .card {
      background: var(--ap-card) !important;
      border: 1px solid var(--ap-border) !important;
      border-radius: var(--ap-radius);
      box-shadow: none !important;
      overflow: hidden;
    }

    .admin-popups-page .card-header {
      background: var(--ap-header) !important;
      border-bottom: 1px solid var(--ap-border) !important;
      padding: 14px 16px;
    }

    .admin-popups-page .card-title {
      color: var(--ap-ink-strong);
      font-size: 15px;
      font-weight: 700;
      margin: 0;
    }

    .admin-popups-page .card-body {
      background: var(--ap-card) !important;
      padding: 16px;
    }

    .admin-popups-page .admin-popups-toolbar {
      align-items: center;
      display: grid;
      gap: 12px;
      grid-template-columns: 1fr auto auto;
    }

    .admin-popups-page .admin-popups-actions {
      align-items: center;
      display: flex;
      gap: 8px;
      justify-content: flex-end;
    }

    .admin-popups-page .admin-popups-actions .btn {
      align-items: center;
      border-radius: var(--ap-radius);
      display: inline-flex;
      font-weight: 650;
      gap: 6px;
      min-height: 36px;
    }

    .admin-popups-page .admin-popups-alert {
      background: var(--ap-warning-bg) !important;
      border: 1px solid var(--ap-warning-border) !important;
      border-left: 4px solid #f97316 !important;
      border-radius: var(--ap-radius);
      color: var(--ap-warning-ink) !important;
      font-size: 13.5px;
      font-weight: 600;
      line-height: 1.45;
      margin: 0 0 16px;
      padding: 12px 14px;
      text-align: left;
    }

    .admin-popups-page .table thead th {
      background: var(--ap-soft) !important;
      border-color: var(--ap-border) !important;
      color: var(--ap-muted) !important;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: .04em;
      text-transform: uppercase;
      vertical-align: middle;
      white-space: nowrap;
    }

    .admin-popups-page .table td {
      border-color: var(--ap-border) !important;
      color: var(--ap-ink) !important;
      vertical-align: middle;
    }

    .admin-popups-page .admin-popup-thumb {
      border: 1px solid var(--ap-border);
      border-radius: 6px;
      display: block;
      height: 48px;
      object-fit: cover;
      width: 64px;
    }

    .admin-popups-page .admin-popup-type {
      align-items: center;
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .admin-popups-page .admin-popup-type img {
      border: 1px solid var(--ap-border);
      border-radius: 6px;
      display: block;
      height: 48px;
      object-fit: cover;
      width: 64px;
    }

    .admin-popups-page .admin-popup-type span {
      color: var(--ap-muted);
      font-size: 12px;
    }

    .admin-popups-page .admin-popup-actions {
      align-items: center;
      display: flex;
      gap: 8px;
      white-space: nowrap;
    }

    .admin-popups-page .admin-popup-actions .btn {
      margin: 0 !important;
      min-height: 32px;
    }

    .admin-popups-page .admin-popup-actions form {
      margin: 0;
    }

    .admin-popups-page .admin-popups-empty {
      color: var(--ap-muted);
      font-weight: 600;
      padding: 40px 16px;
      text-align: center;
    }

    @media (max-width: 991.98px) {
      .admin-popups-page .admin-popups-toolbar {
        grid-template-columns: 1fr;
      }
      .admin-popups-page .admin-popups-actions {
        justify-content: flex-start;
      }
    }

    html[data-theme="dark"] .admin-popups-page {
      --ap-ink: #e5e5e5;
      --ap-ink-strong: #ffffff;
      --ap-muted: #a3a3a3;
      --ap-border: #3d4354;
      --ap-soft: #1f2838;
      --ap-card: #2a3040;
      --ap-header: #252b38;
      --ap-warning-bg: #3a2c26;
      --ap-warning-border: #9a5b2f;
      --ap-warning-ink: #fdba74;
    }
  </style>
@endsection

@section('content')
  <div class="admin-popups-page">
    <div class="page-header">
      <h4 class="page-title">{{ __('Pop-ups de anuncio') }}</h4>
      <ul class="breadcrumbs">
        <li class="nav-home">
          <a href="{{ route('admin.dashboard') }}">
            <i class="flaticon-home" aria-hidden="true"></i>
            <span class="sr-only">{{ __('Inicio') }}</span>
          </a>
        </li>
        <li class="separator"><i class="flaticon-right-arrow" aria-hidden="true"></i></li>
        <li class="nav-item"><a href="#" aria-current="page">{{ __('Pop-ups de anuncio') }}</a></li>
      </ul>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <div class="admin-popups-toolbar">
              <div class="card-title">{{ __('Pop-ups') }}</div>
              <div>@includeIf('backend.partials.languages')</div>
              <div class="admin-popups-actions">
                <button type="button" class="btn btn-danger btn-sm d-none bulk-delete" data-href="{{ route('admin.announcement_popups.bulk_delete_popup') }}">
                  <i class="flaticon-interface-5" aria-hidden="true"></i> {{ __('Eliminar') }}
                </button>
                <a href="{{ route('admin.announcement_popups.select_popup_type') }}" class="btn btn-primary btn-sm">
                  <i class="fas fa-plus" aria-hidden="true"></i> {{ __('Agregar pop-up') }}
                </a>
              </div>
            </div>
          </div>

          <div class="card-body">
            @if (count($popups) == 0)
              <div class="admin-popups-empty">{{ __('No hay pop-ups cargados.') }}</div>
            @else
              <div class="admin-popups-alert" role="status">
                {{ __('Los pop-ups activos aparecen en el sitio en orden de número de serie. El delay se interpreta en segundos si es 120 o menos; si no, en milisegundos. Probá en una ventana de incógnito (sessionStorage guarda los cerrados).') }}
              </div>

              <div class="table-responsive">
                <table class="table table-striped" id="basic-datatables">
                  <thead>
                    <tr>
                      <th scope="col">
                        <input type="checkbox" class="bulk-check" data-val="all" aria-label="{{ __('Seleccionar todos') }}">
                      </th>
                      <th scope="col">{{ __('Imagen') }}</th>
                      <th scope="col">{{ __('Nombre') }}</th>
                      <th scope="col">{{ __('Tipo') }}</th>
                      <th scope="col">{{ __('Estado') }}</th>
                      <th scope="col">{{ __('Serie') }}</th>
                      <th scope="col">{{ __('Acciones') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($popups as $popup)
                      <tr>
                        <td>
                          <input type="checkbox" class="bulk-check" data-val="{{ $popup->id }}" aria-label="{{ __('Seleccionar') }} {{ convertUtf8($popup->name) }}">
                        </td>
                        <td>
                          <img class="admin-popup-thumb" src="{{ asset('assets/admin/img/popups/' . $popup->image) }}" alt="{{ convertUtf8($popup->name) }}">
                        </td>
                        <td>{{ convertUtf8($popup->name) }}</td>
                        <td>
                          <div class="admin-popup-type">
                            <img src="{{ asset('assets/admin/img/popup-samples/' . $popup->type . '.jpg') }}" alt="{{ __('Tipo') }} {{ $popup->type }}">
                            <span>{{ __('Tipo') }} {{ $popup->type }}</span>
                          </div>
                        </td>
                        <td>
                          <form id="statusForm-{{ $popup->id }}" action="{{ route('admin.announcement_popups.update_popup_status', ['id' => $popup->id]) }}" method="post">
                            @csrf
                            <label class="sr-only" for="popup-status-{{ $popup->id }}">{{ __('Estado') }}</label>
                            <select id="popup-status-{{ $popup->id }}" class="form-control form-control-sm {{ $popup->status == 1 ? 'bg-success' : 'bg-danger' }}" name="status" onchange="document.getElementById('statusForm-{{ $popup->id }}').submit()">
                              <option value="1" {{ $popup->status == 1 ? 'selected' : '' }}>{{ __('Activo') }}</option>
                              <option value="0" {{ $popup->status == 0 ? 'selected' : '' }}>{{ __('Inactivo') }}</option>
                            </select>
                          </form>
                        </td>
                        <td>{{ $popup->serial_number }}</td>
                        <td>
                          <div class="admin-popup-actions">
                            <a class="btn btn-secondary btn-xs" href="{{ route('admin.announcement_popups.edit_popup', ['id' => $popup->id]) }}" aria-label="{{ __('Editar') }}">
                              <i class="fas fa-edit" aria-hidden="true"></i>
                            </a>
                            <form class="deleteForm" action="{{ route('admin.announcement_popups.delete_popup', ['id' => $popup->id]) }}" method="post">
                              @csrf
                              <button type="submit" class="btn btn-danger btn-xs deleteBtn" aria-label="{{ __('Eliminar') }}">
                                <i class="fas fa-trash" aria-hidden="true"></i>
                              </button>
                            </form>
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
    </div>
  </div>
@endsection
