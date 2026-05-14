@extends('backend.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Facturas ARCA') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ route('admin.dashboard') }}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator"><i class="flaticon-right-arrow"></i></li>
      <li class="nav-item"><a href="#">{{ __('Auditoría de Facturación') }}</a></li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title">{{ __('Filtros') }}</div>
        </div>
        <div class="card-body">
          <form action="{{ route('admin.arca_invoices.index') }}" method="get">
            <div class="row">
              <div class="col-lg-3">
                <div class="form-group">
                  <label>{{ __('Estado') }}</label>
                  <select name="status" class="form-control">
                    <option value="">{{ __('Todos') }}</option>
                    <option value="ready" {{ request('status') === 'ready' ? 'selected' : '' }}>{{ __('Listo') }}</option>
                    <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>{{ __('Bloqueado') }}</option>
                    <option value="issuing" {{ request('status') === 'issuing' ? 'selected' : '' }}>{{ __('Emitiendo') }}</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>{{ __('Aprobado') }}</option>
                    <option value="error" {{ request('status') === 'error' ? 'selected' : '' }}>{{ __('Error') }}</option>
                  </select>
                </div>
              </div>
              <div class="col-lg-3">
                <div class="form-group">
                  <label>{{ __('Desde') }}</label>
                  <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
              </div>
              <div class="col-lg-3">
                <div class="form-group">
                  <label>{{ __('Hasta') }}</label>
                  <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
              </div>
              <div class="col-lg-3">
                <div class="form-group">
                  <label>{{ __('Búsqueda') }}</label>
                  <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="CAE, nombre, email...">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12 text-right">
                <a href="{{ route('admin.arca_invoices.index') }}" class="btn btn-secondary btn-sm">{{ __('Limpiar') }}</a>
                <button type="submit" class="btn btn-primary btn-sm">{{ __('Filtrar') }}</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title">{{ __('Comprobantes') }}</div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>{{ __('Comprobante') }}</th>
                  <th>{{ __('CAE') }}</th>
                  <th>{{ __('Estado') }}</th>
                  <th>{{ __('Receptor') }}</th>
                  <th>{{ __('Total') }}</th>
                  <th>{{ __('Fecha') }}</th>
                  <th>{{ __('Acciones') }}</th>
                </tr>
              </thead>
              <tbody>
                @forelse($invoices as $invoice)
                  @php
                    $invoiceNumber = str_pad((string) ($invoice->cbte_tipo ?? 0), 3, '0', STR_PAD_LEFT) . '-'
                        . str_pad((string) ($invoice->point_of_sale ?? 0), 5, '0', STR_PAD_LEFT) . '-'
                        . str_pad((string) ($invoice->cbte_nro ?? 0), 8, '0', STR_PAD_LEFT);
                    $statusClass = match($invoice->status) {
                        'approved' => 'badge-success',
                        'ready' => 'badge-info',
                        'blocked' => 'badge-warning',
                        'issuing' => 'badge-primary',
                        'error' => 'badge-danger',
                        default => 'badge-secondary',
                    };
                  @endphp
                  <tr>
                    <td>{{ $invoice->id }}</td>
                    <td>{{ $invoiceNumber }}</td>
                    <td>{{ $invoice->cae ?? '-' }}</td>
                    <td><span class="badge {{ $statusClass }}">{{ ucfirst($invoice->status) }}</span></td>
                    <td>{{ $invoice->recipient_name ?? ($invoice->booking?->fname . ' ' . $invoice->booking?->lname) ?? '-' }}</td>
                    <td>${{ number_format($invoice->total_amount ?? 0, 2, ',', '.') }}</td>
                    <td>{{ $invoice->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                      <a href="{{ route('admin.arca_invoices.show', $invoice->id) }}" class="btn btn-sm btn-info" title="{{ __('Ver') }}">
                        <i class="fas fa-eye"></i>
                      </a>
                      @if(in_array($invoice->status, ['error', 'blocked'], true))
                        <form action="{{ route('admin.arca_invoices.retry', $invoice->id) }}" method="post" class="d-inline" onsubmit="return confirm('{{ __('¿Reintentar emisión?') }}')">
                          @csrf
                          <button type="submit" class="btn btn-sm btn-warning" title="{{ __('Reintentar') }}">
                            <i class="fas fa-redo"></i>
                          </button>
                        </form>
                      @endif
                      <a href="{{ route('admin.arca_invoices.pdf', $invoice->id) }}" class="btn btn-sm btn-secondary" title="{{ __('Descargar PDF') }}">
                        <i class="fas fa-file-pdf"></i>
                      </a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="8" class="text-center">{{ __('No se encontraron comprobantes.') }}</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="d-flex justify-content-center">
            {{ $invoices->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
