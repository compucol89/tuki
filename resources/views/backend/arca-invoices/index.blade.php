@extends('backend.layout')

@section('style')
  <style>
    .arca-invoices-page {
      --arca-border: #e5eaf0;
      --arca-muted: #64748b;
      --arca-ink: #1e2532;
      --arca-soft: #f8fafc;
      --arca-orange: #f97316;
    }

    .arca-invoices-page .card {
      border: 1px solid var(--arca-border);
      border-radius: 8px;
      box-shadow: 0 10px 24px rgba(30, 37, 50, 0.04);
      overflow: hidden;
    }

    .arca-invoices-page .card-header {
      background: #fbfbfc;
      border-bottom: 1px solid var(--arca-border);
      padding: 18px 22px;
    }

    .arca-invoices-page .card-title {
      color: var(--arca-ink);
      font-size: 15px;
      font-weight: 600;
      margin: 0;
    }

    .arca-filter-grid {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 16px;
      align-items: end;
    }

    .arca-filter-actions {
      display: flex;
      gap: 10px;
      justify-content: flex-end;
    }

    .arca-invoices-table {
      color: var(--arca-ink);
      margin-bottom: 0;
      table-layout: fixed;
    }

    .arca-invoices-table thead th {
      background: #eaf2f8;
      border-bottom: 1px solid #d8e4ee;
      color: #111827;
      font-size: 11px;
      font-weight: 600;
      letter-spacing: .03em;
      padding: 14px 16px;
      text-transform: uppercase;
      vertical-align: middle;
      white-space: nowrap;
    }

    .arca-invoices-table tbody td {
      border-color: #eef2f6;
      font-size: 13px;
      line-height: 1.45;
      padding: 16px;
      vertical-align: middle;
    }

    .arca-id-col {
      width: 58px;
    }

    .arca-status-col {
      width: 132px;
    }

    .arca-money-col,
    .arca-date-col {
      width: 126px;
    }

    .arca-actions-col {
      width: 188px;
    }

    .arca-money-cell {
      padding-right: 22px !important;
    }

    .arca-actions-cell {
      padding-left: 24px !important;
    }

    .arca-invoice-link {
      background: transparent;
      border: 0;
      color: var(--arca-ink);
      cursor: pointer;
      display: block;
      font-weight: 600;
      max-width: 100%;
      overflow: hidden;
      padding: 0;
      text-align: left;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .arca-line-muted {
      color: var(--arca-muted);
      display: block;
      font-size: 12px;
      margin-top: 4px;
      max-width: 100%;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .arca-status-pill {
      border-radius: 999px;
      display: inline-flex;
      font-size: 12px;
      font-weight: 600;
      line-height: 1;
      padding: 8px 10px;
      white-space: nowrap;
    }

    .arca-status-success {
      background: #dcfce7;
      color: #15803d;
    }

    .arca-status-info {
      background: #e0f2fe;
      color: #0369a1;
    }

    .arca-status-warning {
      background: #fff7ed;
      color: #c2410c;
    }

    .arca-status-danger {
      background: #fee2e2;
      color: #b91c1c;
    }

    .arca-status-muted {
      background: #f1f5f9;
      color: #475569;
    }

    .arca-action-stack {
      align-items: center;
      column-gap: 12px;
      display: grid;
      grid-auto-columns: max-content;
      grid-auto-flow: column;
      justify-content: flex-end;
      white-space: nowrap;
    }

    .arca-action-stack .btn {
      align-items: center;
      border-radius: 8px;
      display: inline-flex;
      font-size: 12px;
      font-weight: 600;
      gap: 6px;
      height: 40px;
      justify-content: center;
      line-height: 1;
      margin-left: 0 !important;
      min-height: 40px;
      padding: 0 14px !important;
      box-shadow: none !important;
    }

    .arca-invoices-page .table .arca-action-stack .btn + .btn,
    .arca-invoices-page .table .arca-action-stack .btn + form,
    .arca-invoices-page .table .arca-action-stack form + .btn,
    .arca-invoices-page .table .arca-action-stack form + form {
      margin-left: 0 !important;
    }

    .arca-action-stack form {
      margin: 0;
    }

    .arca-action-stack .arca-btn-view {
      min-width: 76px;
    }

    .arca-action-stack .arca-btn-icon {
      min-width: 40px;
      padding: 0 !important;
      width: 40px;
    }

    .arca-action-stack .arca-btn-icon i {
      margin: 0;
    }

    .arca-modal .modal-content {
      border: 0;
      border-radius: 10px;
      overflow: hidden;
    }

    .arca-modal .modal-header {
      align-items: flex-start;
      background: #fbfbfc;
      border-bottom: 1px solid var(--arca-border);
      padding: 20px 24px;
    }

    .arca-modal-title {
      color: var(--arca-ink);
      font-size: 18px;
      font-weight: 800;
      margin: 0;
    }

    .arca-detail-grid {
      display: grid;
      gap: 14px;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      margin-bottom: 18px;
    }

    .arca-detail-box {
      background: var(--arca-soft);
      border: 1px solid var(--arca-border);
      border-radius: 8px;
      padding: 14px;
    }

    .arca-detail-label {
      color: var(--arca-muted);
      display: block;
      font-size: 11px;
      font-weight: 800;
      letter-spacing: .04em;
      margin-bottom: 6px;
      text-transform: uppercase;
    }

    .arca-detail-value {
      color: var(--arca-ink);
      font-size: 14px;
      font-weight: 700;
      overflow-wrap: anywhere;
    }

    .arca-modal-section {
      border-top: 1px solid var(--arca-border);
      padding-top: 18px;
    }

    .arca-modal-section-title {
      color: var(--arca-ink);
      font-size: 14px;
      font-weight: 800;
      margin-bottom: 12px;
    }

    .arca-pdf-frame {
      background: #f8fafc;
      border: 1px solid var(--arca-border);
      border-radius: 8px;
      height: 520px;
      width: 100%;
    }

    @media (max-width: 1199.98px) {
      .arca-filter-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      .arca-table-cae,
      .arca-date-col,
      .arca-date-cell {
        display: none;
      }

      .arca-actions-col {
        width: 156px;
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

      .arca-invoices-table {
        min-width: 720px;
      }

      .arca-pdf-frame {
        height: 420px;
      }
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

    <div class="card mb-4">
      <div class="card-header">
        <div class="card-title">{{ __('Filtros') }}</div>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.arca_invoices.index') }}" method="get">
          <div class="arca-filter-grid">
            <div class="form-group mb-0">
              <label>{{ __('Estado') }}</label>
              <select name="status" class="form-control">
                <option value="">{{ __('Todos') }}</option>
                <option value="ready" {{ request('status') === 'ready' ? 'selected' : '' }}>{{ __('Pendiente de emisión') }}</option>
                <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>{{ __('Bloqueado') }}</option>
                <option value="issuing" {{ request('status') === 'issuing' ? 'selected' : '' }}>{{ __('Emitiendo') }}</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>{{ __('Aprobado') }}</option>
                <option value="error" {{ request('status') === 'error' ? 'selected' : '' }}>{{ __('Error') }}</option>
              </select>
            </div>
            <div class="form-group mb-0">
              <label>{{ __('Desde') }}</label>
              <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="form-group mb-0">
              <label>{{ __('Hasta') }}</label>
              <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="form-group mb-0">
              <label>{{ __('Búsqueda') }}</label>
              <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="{{ __('CAE, nombre, correo...') }}">
            </div>
          </div>
          <div class="arca-filter-actions mt-3">
            <a href="{{ route('admin.arca_invoices.index') }}" class="btn btn-secondary">{{ __('Limpiar') }}</a>
            <button type="submit" class="btn btn-primary">{{ __('Filtrar') }}</button>
          </div>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
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
                <th class="arca-money-col text-right">{{ __('Total') }}</th>
                <th class="arca-date-col">{{ __('Fecha') }}</th>
                <th class="arca-actions-col text-right">{{ __('Acciones') }}</th>
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
                    <button type="button" class="arca-invoice-link" data-toggle="modal" data-target="#arcaInvoiceModal{{ $invoice->id }}">
                      {{ $invoiceNumber }}
                    </button>
                    <span class="arca-line-muted">{{ $invoice->environment === 'production' ? __('Producción') : __('Homologación') }}</span>
                  </td>
                  <td class="arca-table-cae">
                    <span class="arca-line-muted">{{ $invoice->cae ?: __('Sin CAE') }}</span>
                  </td>
                  <td>
                    <span class="arca-status-pill {{ $statusClass }}">{{ __($label) }}</span>
                  </td>
                  <td>
                    <strong>{{ $name }}</strong>
                    @if($invoice->booking?->email)
                      <span class="arca-line-muted">{{ $invoice->booking->email }}</span>
                    @endif
                  </td>
                  <td class="text-right text-nowrap arca-money-cell">${{ number_format($invoice->total_amount ?? 0, 2, ',', '.') }}</td>
                  <td class="arca-date-cell">
                    {{ $invoice->created_at->format('d/m/Y') }}
                    <span class="arca-line-muted">{{ $invoice->created_at->format('H:i') }}</span>
                  </td>
                  <td class="arca-actions-cell">
                    <div class="arca-action-stack">
                      <button type="button" class="btn btn-primary arca-btn-view" data-toggle="modal" data-target="#arcaInvoiceModal{{ $invoice->id }}">
                        <i class="fas fa-eye"></i> {{ __('Ver') }}
                      </button>
                      @if($invoice->booking)
                        <a href="{{ route('admin.arca_invoices.pdf', $invoice->id) }}" class="btn btn-light arca-btn-icon" title="{{ __('Descargar factura') }}" aria-label="{{ __('Descargar factura') }}">
                          <i class="fas fa-file-pdf"></i>
                        </a>
                      @endif
                      @if(in_array($invoice->status, ['error', 'blocked'], true))
                        <form action="{{ route('admin.arca_invoices.retry', $invoice->id) }}" method="post" class="d-inline" onsubmit="return confirm('{{ __('¿Reintentar emisión?') }}')">
                          @csrf
                          <button type="submit" class="btn btn-warning arca-btn-icon" title="{{ __('Reintentar') }}" aria-label="{{ __('Reintentar') }}">
                            <i class="fas fa-redo"></i>
                          </button>
                        </form>
                      @endif
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="text-center py-5">{{ __('No se encontraron comprobantes.') }}</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
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
              <div>
                <h5 class="arca-modal-title" id="arcaInvoiceModalTitle{{ $invoice->id }}">{{ __('Factura ARCA') }} {{ $invoiceNumber }}</h5>
                <span class="arca-status-pill {{ $statusClass }} mt-2">{{ __($label) }}</span>
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
                    <div class="d-flex align-items-center justify-content-between mb-2">
                      <div class="arca-modal-section-title mb-0">{{ __('Vista del comprobante') }}</div>
                      @if($invoice->booking)
                        <a href="{{ route('admin.arca_invoices.pdf', $invoice->id) }}" class="btn btn-sm btn-light">
                          <i class="fas fa-download mr-1"></i> {{ __('Descargar') }}
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
