@extends('backend.layout')

@section('style')
  <style>
    /* Ritmo: 8 / 12 / 16 / 20. Ejes alineados en filtros, filas y acciones. */
    .arca-invoices-page {
      --arca-ink: #1e2532;
      --arca-ink-strong: #111827;
      --arca-muted: #667085;
      --arca-border: #e4e7ec;
      --arca-soft: #f8fafc;
      --arca-card: #ffffff;
      --arca-header: #fbfcfd;
      --arca-thead: #edf4f9;
      --arca-primary: #f97316;
      --arca-radius: 8px;
      --arca-control-h: 40px;
      --arca-gap: 12px;
      --arca-pad: 16px;
      color: var(--arca-ink);
    }

    .arca-invoices-page .page-header {
      margin-bottom: 16px;
    }

    .arca-invoices-page .page-title {
      color: var(--arca-ink-strong) !important;
      font-size: 24px !important;
      font-weight: 750 !important;
      line-height: 1.2;
    }

    .arca-invoices-page .breadcrumbs,
    .arca-invoices-page .breadcrumbs a {
      color: var(--arca-muted) !important;
      font-size: 12.5px;
      font-weight: 500;
    }

    .arca-invoices-page .card {
      background: var(--arca-card) !important;
      border: 1px solid var(--arca-border) !important;
      border-radius: var(--arca-radius);
      box-shadow: none !important;
      margin-bottom: 16px;
      overflow: hidden;
    }

    .arca-invoices-page .card-header {
      align-items: center;
      background: var(--arca-header) !important;
      border-bottom: 1px solid var(--arca-border) !important;
      display: flex;
      gap: 12px;
      justify-content: space-between;
      min-height: 56px;
      padding: 14px var(--arca-pad);
    }

    .arca-invoices-page .card-header > div:first-child {
      display: flex;
      flex-direction: column;
      gap: 4px;
      min-width: 0;
    }

    .arca-invoices-page .card-title {
      color: var(--arca-ink-strong);
      font-size: 15px;
      font-weight: 700;
      line-height: 1.25;
      margin: 0;
    }

    .arca-invoices-page .card-body {
      background: var(--arca-card) !important;
      padding: var(--arca-pad);
    }

    .arca-invoices-page .card-body .form-group {
      margin-bottom: 0;
    }

    .arca-invoices-page .card-body label {
      color: var(--arca-muted);
      display: block;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: .04em;
      margin-bottom: 6px;
      text-transform: uppercase;
    }

    .arca-invoices-page .form-control {
      background: var(--arca-soft) !important;
      border-color: var(--arca-border) !important;
      border-radius: var(--arca-radius);
      color: var(--arca-ink) !important;
      height: var(--arca-control-h);
      min-height: var(--arca-control-h);
    }

    .arca-invoices-page .form-control:focus {
      border-color: var(--arca-primary) !important;
      box-shadow: 0 0 0 3px rgba(249, 115, 22, .18);
    }

    .arca-filter-grid {
      align-items: end;
      display: grid;
      gap: var(--arca-gap);
      grid-template-columns: repeat(4, minmax(0, 1fr)) auto;
    }

    .arca-filter-actions {
      align-items: center;
      display: flex;
      gap: 8px;
      justify-content: flex-end;
      min-height: var(--arca-control-h);
    }

    .arca-filter-actions .btn {
      align-items: center;
      border-radius: var(--arca-radius);
      box-shadow: none !important;
      display: inline-flex;
      font-size: 13px;
      font-weight: 650;
      gap: 6px;
      height: var(--arca-control-h);
      justify-content: center;
      min-height: var(--arca-control-h);
      padding: 0 16px;
      white-space: nowrap;
    }

    .arca-filter-actions .btn-secondary {
      background: var(--arca-soft) !important;
      border-color: var(--arca-border) !important;
      color: var(--arca-ink) !important;
    }

    .arca-invoices-table {
      color: var(--arca-ink);
      margin-bottom: 0;
      table-layout: fixed;
      width: 100%;
    }

    .arca-invoices-table thead th {
      background: var(--arca-thead) !important;
      border-bottom: 1px solid var(--arca-border) !important;
      border-top: 0;
      color: var(--arca-muted) !important;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: .04em;
      line-height: 1.25;
      padding: 12px 14px;
      text-transform: uppercase;
      vertical-align: middle;
      white-space: nowrap;
    }

    .arca-invoices-table tbody td {
      background: transparent !important;
      border-color: var(--arca-border) !important;
      color: var(--arca-ink) !important;
      font-size: 13px;
      line-height: 1.4;
      padding: 12px 14px;
      vertical-align: middle;
    }

    .arca-invoices-table tbody tr:hover td {
      background: var(--arca-soft) !important;
    }

    .arca-id-col { width: 56px; }
    .arca-status-col { width: 140px; }
    .arca-money-col { width: 112px; }
    .arca-date-col { width: 104px; }
    .arca-actions-col { width: 168px; }

    .arca-money-col,
    .arca-money-cell,
    .arca-actions-col,
    .arca-actions-cell {
      text-align: right;
    }

    .arca-cell-stack {
      display: flex;
      flex-direction: column;
      gap: 4px;
      min-width: 0;
    }

    .arca-invoice-link {
      background: transparent;
      border: 0;
      color: var(--arca-ink-strong);
      cursor: pointer;
      display: block;
      font-weight: 700;
      max-width: 100%;
      overflow: hidden;
      padding: 0;
      text-align: left;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .arca-invoice-link:hover,
    .arca-invoice-link:focus {
      color: var(--arca-primary);
      outline: 0;
      text-decoration: none;
    }

    .arca-line-muted {
      color: var(--arca-muted);
      display: block;
      font-size: 12px;
      line-height: 1.35;
      max-width: 100%;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .arca-status-pill {
      align-items: center;
      border-radius: 999px;
      display: inline-flex;
      font-size: 11px;
      font-weight: 700;
      justify-content: center;
      line-height: 1;
      min-height: 28px;
      padding: 0 10px;
      white-space: nowrap;
    }

    .arca-status-success {
      background: #ecfdf5;
      color: #047857;
    }

    .arca-status-info {
      background: #eff6ff;
      color: #1d4ed8;
    }

    .arca-status-warning {
      background: #fff7ed;
      color: #c2410c;
    }

    .arca-status-danger {
      background: #fef2f2;
      color: #be123c;
    }

    .arca-status-muted {
      background: var(--arca-soft);
      color: var(--arca-muted);
    }

    .arca-action-stack {
      align-items: center;
      display: flex;
      gap: 8px;
      justify-content: flex-end;
      white-space: nowrap;
    }

    .arca-action-stack .btn {
      align-items: center;
      border-radius: var(--arca-radius);
      box-shadow: none !important;
      display: inline-flex;
      font-size: 12px;
      font-weight: 650;
      gap: 6px;
      height: 36px;
      justify-content: center;
      line-height: 1;
      margin: 0 !important;
      min-height: 36px;
      padding: 0 12px !important;
    }

    .arca-action-stack form {
      display: inline-flex;
      margin: 0;
    }

    .arca-action-stack .arca-btn-view {
      min-width: 72px;
    }

    .arca-action-stack .arca-btn-icon {
      min-width: 36px;
      padding: 0 !important;
      width: 36px;
    }

    .arca-action-stack .arca-btn-icon i {
      margin: 0;
    }

    .arca-action-stack .btn-light {
      background: var(--arca-soft) !important;
      border-color: var(--arca-border) !important;
      color: var(--arca-ink) !important;
    }

    .arca-pagination {
      border-top: 1px solid var(--arca-border);
      display: flex;
      justify-content: center;
      margin-top: 0;
      padding-top: 16px;
    }

    .arca-empty {
      color: var(--arca-muted);
      font-weight: 600;
      padding: 40px 16px !important;
      text-align: center;
    }

    .arca-modal .modal-content {
      background: var(--arca-card);
      border: 0;
      border-radius: 10px;
      color: var(--arca-ink);
      overflow: hidden;
    }

    .arca-modal .modal-header {
      align-items: flex-start;
      background: var(--arca-header);
      border-bottom: 1px solid var(--arca-border);
      gap: 12px;
      padding: 16px 20px;
    }

    .arca-modal .modal-body {
      padding: 20px;
    }

    .arca-modal .modal-footer {
      background: var(--arca-header);
      border-top: 1px solid var(--arca-border);
      gap: 8px;
      padding: 12px 20px;
    }

    .arca-modal-title {
      color: var(--arca-ink-strong);
      font-size: 18px;
      font-weight: 750;
      line-height: 1.25;
      margin: 0;
    }

    .arca-detail-grid {
      display: grid;
      gap: 12px;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      margin-bottom: 16px;
    }

    .arca-detail-box {
      background: var(--arca-soft);
      border: 1px solid var(--arca-border);
      border-radius: var(--arca-radius);
      min-height: 72px;
      padding: 12px 14px;
    }

    .arca-detail-label {
      color: var(--arca-muted);
      display: block;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: .04em;
      margin-bottom: 6px;
      text-transform: uppercase;
    }

    .arca-detail-value {
      color: var(--arca-ink-strong);
      font-size: 14px;
      font-weight: 700;
      line-height: 1.35;
      overflow-wrap: anywhere;
    }

    .arca-modal-section {
      border-top: 1px solid var(--arca-border);
      padding-top: 16px;
    }

    .arca-modal-section-title {
      color: var(--arca-ink-strong);
      font-size: 13px;
      font-weight: 750;
      margin-bottom: 12px;
    }

    .arca-modal-section-head {
      align-items: center;
      display: flex;
      gap: 12px;
      justify-content: space-between;
      margin-bottom: 12px;
    }

    .arca-modal-section-head .arca-modal-section-title {
      margin-bottom: 0;
    }

    .arca-pdf-frame {
      background: var(--arca-soft);
      border: 1px solid var(--arca-border);
      border-radius: var(--arca-radius);
      height: 520px;
      width: 100%;
    }

    .arca-modal .table th,
    .arca-modal .table td {
      border-color: var(--arca-border) !important;
      color: var(--arca-ink) !important;
      padding: 10px 12px;
      vertical-align: middle;
    }

    .arca-modal .table thead th {
      background: var(--arca-thead) !important;
      color: var(--arca-muted) !important;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
    }

    @media (max-width: 1199.98px) {
      .arca-filter-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      .arca-filter-actions {
        grid-column: 1 / -1;
        justify-content: flex-end;
      }

      .arca-table-cae,
      .arca-date-col,
      .arca-date-cell {
        display: none;
      }

      .arca-actions-col {
        width: 148px;
      }
    }

    @media (max-width: 767.98px) {
      .arca-filter-grid,
      .arca-detail-grid {
        grid-template-columns: 1fr;
      }

      .arca-filter-actions,
      .arca-action-stack {
        justify-content: flex-start;
      }

      .arca-filter-actions {
        width: 100%;
      }

      .arca-filter-actions .btn {
        flex: 1 1 auto;
      }

      .arca-invoices-table {
        min-width: 720px;
      }

      .arca-pdf-frame {
        height: 420px;
      }
    }

    html[data-theme="dark"] .arca-invoices-page {
      --arca-ink: #e5e5e5;
      --arca-ink-strong: #ffffff;
      --arca-muted: #a3a3a3;
      --arca-border: #3d4354;
      --arca-soft: #1f2838;
      --arca-card: #2a3040;
      --arca-header: #252b38;
      --arca-thead: #1f2838;
      --arca-primary: #e05d38;
    }

    html[data-theme="dark"] .arca-status-success {
      background: #14352a;
      color: #4ade80;
    }

    html[data-theme="dark"] .arca-status-info {
      background: #1e3a5f;
      color: #93c5fd;
    }

    html[data-theme="dark"] .arca-status-warning {
      background: #3a2c26;
      color: #fdba74;
    }

    html[data-theme="dark"] .arca-status-danger {
      background: #3a2228;
      color: #f87171;
    }

    html[data-theme="dark"] .arca-modal .modal-content,
    html[data-theme="dark"] .arca-modal .modal-body {
      background: var(--arca-card) !important;
      color: var(--arca-ink) !important;
    }

    html[data-theme="dark"] .arca-modal .modal-header,
    html[data-theme="dark"] .arca-modal .modal-footer {
      background: var(--arca-header) !important;
      border-color: var(--arca-border) !important;
    }

    html[data-theme="dark"] .arca-modal .close {
      color: var(--arca-ink-strong);
      text-shadow: none;
    }
  </style>
@endsection

@section('content')
  @php
    $statusLabels = [
        'approved' => 'Aprobado',
        'ready' => 'Pendiente de emisión',
        'blocked' => 'Bloqueado',
        'issuing' => 'Emitiendo',
        'error' => 'Error',
    ];

    $statusClasses = [
        'approved' => 'arca-status-success',
        'ready' => 'arca-status-info',
        'blocked' => 'arca-status-warning',
        'issuing' => 'arca-status-info',
        'error' => 'arca-status-danger',
    ];

    $formatInvoiceNumber = static function ($invoice) {
        return str_pad((string) ($invoice->cbte_tipo ?? 0), 3, '0', STR_PAD_LEFT) . '-'
            . str_pad((string) ($invoice->point_of_sale ?? 0), 5, '0', STR_PAD_LEFT) . '-'
            . str_pad((string) ($invoice->cbte_nro ?? 0), 8, '0', STR_PAD_LEFT);
    };

    $recipientName = static function ($invoice) {
        $name = trim((string) ($invoice->recipient_name ?? ''));

        if ($name === '' && $invoice->booking) {
            $name = trim((string) ($invoice->booking->fname ?? '') . ' ' . (string) ($invoice->booking->lname ?? ''));
        }

        return $name !== '' ? $name : 'Sin receptor';
    };
  @endphp

  <div class="arca-invoices-page">
    <div class="page-header">
      <h4 class="page-title">{{ __('Facturas ARCA') }}</h4>
      <ul class="breadcrumbs">
        <li class="nav-home">
          <a href="{{ route('admin.dashboard') }}">
            <i class="flaticon-home"></i>
          </a>
        </li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="#">{{ __('Auditoría de facturación') }}</a></li>
      </ul>
    </div>

    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-title">{{ __('Filtros') }}</div>
          <span class="arca-line-muted">{{ __('Filtrá por estado, rango de fechas o búsqueda.') }}</span>
        </div>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.arca_invoices.index') }}" method="get">
          <div class="arca-filter-grid">
            <div class="form-group">
              <label for="arcaStatus">{{ __('Estado') }}</label>
              <select id="arcaStatus" name="status" class="form-control">
                <option value="">{{ __('Todos') }}</option>
                <option value="ready" {{ request('status') === 'ready' ? 'selected' : '' }}>{{ __('Pendiente de emisión') }}</option>
                <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>{{ __('Bloqueado') }}</option>
                <option value="issuing" {{ request('status') === 'issuing' ? 'selected' : '' }}>{{ __('Emitiendo') }}</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>{{ __('Aprobado') }}</option>
                <option value="error" {{ request('status') === 'error' ? 'selected' : '' }}>{{ __('Error') }}</option>
              </select>
            </div>
            <div class="form-group">
              <label for="arcaDateFrom">{{ __('Desde') }}</label>
              <input id="arcaDateFrom" type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="form-group">
              <label for="arcaDateTo">{{ __('Hasta') }}</label>
              <input id="arcaDateTo" type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="form-group">
              <label for="arcaSearch">{{ __('Búsqueda') }}</label>
              <input id="arcaSearch" type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="{{ __('CAE, nombre, correo...') }}">
            </div>
            <div class="arca-filter-actions">
              <a href="{{ route('admin.arca_invoices.index') }}" class="btn btn-secondary">{{ __('Limpiar') }}</a>
              <button type="submit" class="btn btn-primary">{{ __('Filtrar') }}</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-title">{{ __('Comprobantes') }}</div>
          <span class="arca-line-muted">{{ __('Revisá el estado, abrí el detalle o descargá la factura.') }}</span>
        </div>
        <span class="arca-line-muted">{{ $invoices->total() }} {{ __('registros') }}</span>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table arca-invoices-table">
            <thead>
              <tr>
                <th class="arca-id-col">#</th>
                <th>{{ __('Comprobante') }}</th>
                <th class="arca-table-cae">{{ __('CAE') }}</th>
                <th class="arca-status-col">{{ __('Estado') }}</th>
                <th>{{ __('Receptor') }}</th>
                <th class="arca-money-col">{{ __('Total') }}</th>
                <th class="arca-date-col">{{ __('Fecha') }}</th>
                <th class="arca-actions-col">{{ __('Acciones') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse($invoices as $invoice)
                @php
                  $invoiceNumber = $formatInvoiceNumber($invoice);
                  $label = $statusLabels[$invoice->status] ?? 'Sin estado';
                  $statusClass = $statusClasses[$invoice->status] ?? 'arca-status-muted';
                  $name = $recipientName($invoice);
                @endphp
                <tr>
                  <td>#{{ $invoice->id }}</td>
                  <td>
                    <div class="arca-cell-stack">
                      <button type="button" class="arca-invoice-link" data-toggle="modal" data-target="#arcaInvoiceModal{{ $invoice->id }}">
                        {{ $invoiceNumber }}
                      </button>
                      <span class="arca-line-muted">{{ $invoice->environment === 'production' ? __('Producción') : __('Homologación') }}</span>
                    </div>
                  </td>
                  <td class="arca-table-cae">
                    <span class="arca-line-muted">{{ $invoice->cae ?: __('Sin CAE') }}</span>
                  </td>
                  <td>
                    <span class="arca-status-pill {{ $statusClass }}">{{ __($label) }}</span>
                  </td>
                  <td>
                    <div class="arca-cell-stack">
                      <strong>{{ $name }}</strong>
                      @if($invoice->booking?->email)
                        <span class="arca-line-muted">{{ $invoice->booking->email }}</span>
                      @endif
                    </div>
                  </td>
                  <td class="arca-money-cell text-nowrap">${{ number_format($invoice->total_amount ?? 0, 2, ',', '.') }}</td>
                  <td class="arca-date-cell">
                    <div class="arca-cell-stack">
                      <span>{{ $invoice->created_at->format('d/m/Y') }}</span>
                      <span class="arca-line-muted">{{ $invoice->created_at->format('H:i') }}</span>
                    </div>
                  </td>
                  <td class="arca-actions-cell">
                    <div class="arca-action-stack">
                      <button type="button" class="btn btn-primary arca-btn-view" data-toggle="modal" data-target="#arcaInvoiceModal{{ $invoice->id }}">
                        <i class="fas fa-eye" aria-hidden="true"></i> {{ __('Ver') }}
                      </button>
                      @if($invoice->booking)
                        <a href="{{ route('admin.arca_invoices.pdf', $invoice->id) }}" class="btn btn-light arca-btn-icon" title="{{ __('Descargar factura') }}" aria-label="{{ __('Descargar factura') }}">
                          <i class="fas fa-file-pdf" aria-hidden="true"></i>
                        </a>
                      @endif
                      @if(in_array($invoice->status, ['error', 'blocked'], true))
                        <form action="{{ route('admin.arca_invoices.retry', $invoice->id) }}" method="post" onsubmit="return confirm('{{ __('¿Reintentar emisión?') }}')">
                          @csrf
                          <button type="submit" class="btn btn-warning arca-btn-icon" title="{{ __('Reintentar') }}" aria-label="{{ __('Reintentar') }}">
                            <i class="fas fa-redo" aria-hidden="true"></i>
                          </button>
                        </form>
                      @endif
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="arca-empty">{{ __('No se encontraron comprobantes.') }}</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="arca-pagination">
          {{ $invoices->links() }}
        </div>
      </div>
    </div>

    @foreach($invoices as $invoice)
      @php
        $invoiceNumber = $formatInvoiceNumber($invoice);
        $label = $statusLabels[$invoice->status] ?? 'Sin estado';
        $statusClass = $statusClasses[$invoice->status] ?? 'arca-status-muted';
        $name = $recipientName($invoice);
      @endphp
      <div class="modal fade arca-modal arca-invoice-modal" id="arcaInvoiceModal{{ $invoice->id }}" tabindex="-1" role="dialog" aria-labelledby="arcaInvoiceModalTitle{{ $invoice->id }}" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <div class="arca-cell-stack">
                <h5 class="arca-modal-title" id="arcaInvoiceModalTitle{{ $invoice->id }}">{{ __('Factura ARCA') }} {{ $invoiceNumber }}</h5>
                <span class="arca-status-pill {{ $statusClass }}">{{ __($label) }}</span>
              </div>
              <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Cerrar') }}">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="arca-detail-grid">
                <div class="arca-detail-box">
                  <span class="arca-detail-label">{{ __('Comprobante') }}</span>
                  <span class="arca-detail-value">{{ $invoiceNumber }}</span>
                </div>
                <div class="arca-detail-box">
                  <span class="arca-detail-label">{{ __('CAE') }}</span>
                  <span class="arca-detail-value">{{ $invoice->cae ?: __('Sin CAE') }}</span>
                </div>
                <div class="arca-detail-box">
                  <span class="arca-detail-label">{{ __('Vencimiento CAE') }}</span>
                  <span class="arca-detail-value">{{ $invoice->cae_due_date ? $invoice->cae_due_date->format('d/m/Y') : __('Sin dato') }}</span>
                </div>
                <div class="arca-detail-box">
                  <span class="arca-detail-label">{{ __('Receptor') }}</span>
                  <span class="arca-detail-value">{{ $name }}</span>
                </div>
                <div class="arca-detail-box">
                  <span class="arca-detail-label">{{ __('Documento') }}</span>
                  <span class="arca-detail-value">{{ $invoice->recipient_tax_id ?? $invoice->booking?->dni ?? __('Sin dato') }}</span>
                </div>
                <div class="arca-detail-box">
                  <span class="arca-detail-label">{{ __('Total') }}</span>
                  <span class="arca-detail-value">${{ number_format($invoice->total_amount ?? 0, 2, ',', '.') }}</span>
                </div>
                <div class="arca-detail-box">
                  <span class="arca-detail-label">{{ __('Fecha de emisión') }}</span>
                  <span class="arca-detail-value">{{ $invoice->issued_at ? $invoice->issued_at->format('d/m/Y H:i') : __('Sin emitir') }}</span>
                </div>
                <div class="arca-detail-box">
                  <span class="arca-detail-label">{{ __('Ambiente') }}</span>
                  <span class="arca-detail-value">{{ $invoice->environment === 'production' ? __('Producción') : __('Homologación') }}</span>
                </div>
                <div class="arca-detail-box">
                  <span class="arca-detail-label">{{ __('Reserva') }}</span>
                  <span class="arca-detail-value">{{ $invoice->booking ? '#' . $invoice->booking->id : __('Sin reserva') }}</span>
                </div>
              </div>

              @if($invoice->error_message)
                <div class="alert alert-danger">
                  <strong>{{ __('Error') }}:</strong> {{ $invoice->error_message }}
                  @if($invoice->error_code)
                    <br><small>{{ __('Código') }}: {{ $invoice->error_code }}</small>
                  @endif
                </div>
              @endif

              <div class="row">
                <div class="col-lg-5">
                  <div class="arca-modal-section">
                    <div class="arca-modal-section-title">{{ __('Datos fiscales') }}</div>
                    <div class="table-responsive">
                      <table class="table table-sm">
                        <tbody>
                          <tr>
                            <th>{{ __('Neto') }}</th>
                            <td class="text-right">${{ number_format($invoice->net_amount ?? 0, 2, ',', '.') }}</td>
                          </tr>
                          <tr>
                            <th>{{ __('IVA') }}</th>
                            <td class="text-right">${{ number_format($invoice->vat_amount ?? 0, 2, ',', '.') }}</td>
                          </tr>
                          <tr>
                            <th>{{ __('Exento') }}</th>
                            <td class="text-right">${{ number_format($invoice->exempt_amount ?? 0, 2, ',', '.') }}</td>
                          </tr>
                          <tr>
                            <th>{{ __('No gravado') }}</th>
                            <td class="text-right">${{ number_format($invoice->non_taxed_amount ?? 0, 2, ',', '.') }}</td>
                          </tr>
                          <tr>
                            <th>{{ __('Comisión') }}</th>
                            <td class="text-right">{{ number_format((float) ($invoice->commission_rate ?? 0), 2, ',', '.') }}%</td>
                          </tr>
                          <tr>
                            <th>{{ __('CUIT emisor') }}</th>
                            <td class="text-right">{{ $invoice->issuer_cuit_used ?? __('Sin dato') }}</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>

                    <div class="arca-modal-section mt-3">
                      <div class="arca-modal-section-title">{{ __('Conceptos') }}</div>
                      <div class="table-responsive">
                        <table class="table table-sm">
                          <thead>
                            <tr>
                              <th>{{ __('Descripción') }}</th>
                              <th class="text-right">{{ __('Total') }}</th>
                            </tr>
                          </thead>
                          <tbody>
                            @forelse($invoice->items as $item)
                              <tr>
                                <td>
                                  {{ $item->description }}
                                  <span class="arca-line-muted">{{ number_format((float) ($item->quantity ?? 0), 2, ',', '.') }} x ${{ number_format($item->unit_price ?? 0, 2, ',', '.') }}</span>
                                </td>
                                <td class="text-right text-nowrap">${{ number_format($item->total_amount ?? 0, 2, ',', '.') }}</td>
                              </tr>
                            @empty
                              <tr>
                                <td colspan="2">{{ __('Sin conceptos cargados.') }}</td>
                              </tr>
                            @endforelse
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-7">
                  <div class="arca-modal-section">
                    <div class="arca-modal-section-head">
                      <div class="arca-modal-section-title">{{ __('Vista del comprobante') }}</div>
                      @if($invoice->booking)
                        <a href="{{ route('admin.arca_invoices.pdf', $invoice->id) }}" class="btn btn-sm btn-light">
                          <i class="fas fa-download" aria-hidden="true"></i> {{ __('Descargar') }}
                        </a>
                      @endif
                    </div>

                    @if($invoice->booking)
                      <iframe class="arca-pdf-frame" data-src="{{ route('admin.arca_invoices.pdf', $invoice->id) }}?inline=1" title="{{ __('Factura ARCA') }} {{ $invoiceNumber }}"></iframe>
                    @else
                      <div class="alert alert-warning mb-0">{{ __('No se puede mostrar el comprobante porque no tiene una reserva asociada.') }}</div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              @if($invoice->booking)
                <a href="{{ route('admin.event_booking.details', ['id' => $invoice->booking->id]) }}" class="btn btn-light">
                  {{ __('Ver reserva') }}
                </a>
              @endif
              <button type="button" class="btn btn-primary" data-dismiss="modal">{{ __('Cerrar') }}</button>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endsection

@section('script')
  <script>
    'use strict';

    $('.arca-invoice-modal').on('shown.bs.modal', function () {
      $(this).find('.modal-body').scrollTop(0);

      var frame = $(this).find('.arca-pdf-frame');
      var source = frame.data('src');

      if (source && !frame.attr('src')) {
        frame.attr('src', source);
      }
    });
  </script>
@endsection
