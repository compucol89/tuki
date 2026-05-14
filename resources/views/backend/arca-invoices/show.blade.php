@extends('backend.layout')

@section('content')
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

  <div class="page-header">
    <h4 class="page-title">{{ __('Factura ARCA') }} #{{ $invoice->id }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home"><a href="{{ route('admin.dashboard') }}"><i class="flaticon-home"></i></a></li>
      <li class="separator"><i class="flaticon-right-arrow"></i></li>
      <li class="nav-item"><a href="{{ route('admin.arca_invoices.index') }}">{{ __('Facturas ARCA') }}</a></li>
      <li class="separator"><i class="flaticon-right-arrow"></i></li>
      <li class="nav-item"><a href="#">#{{ $invoice->id }}</a></li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <div class="card-title">{{ __('Detalle del comprobante') }}</div>
        </div>
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-md-6">
              <p><strong>{{ __('Número') }}:</strong> {{ $invoiceNumber }}</p>
              <p><strong>{{ __('Ambiente') }}:</strong> {{ $invoice->environment === 'production' ? 'Producción' : 'Homologación' }}</p>
              <p><strong>{{ __('Modelo') }}:</strong> {{ $invoice->invoice_model }}</p>
            </div>
            <div class="col-md-6">
              <p><strong>{{ __('Estado') }}:</strong> <span class="badge {{ $statusClass }}">{{ ucfirst($invoice->status) }}</span></p>
              <p><strong>{{ __('Fecha de creación') }}:</strong> {{ $invoice->created_at->format('d/m/Y H:i') }}</p>
              <p><strong>{{ __('Fecha de emisión') }}:</strong> {{ $invoice->issued_at ? $invoice->issued_at->format('d/m/Y H:i') : '-' }}</p>
            </div>
          </div>

          @if($invoice->cae)
            <div class="alert alert-success">
              <strong>CAE:</strong> {{ $invoice->cae }}<br>
              <strong>{{ __('Vencimiento CAE') }}:</strong> {{ $invoice->cae_due_date ? $invoice->cae_due_date->format('d/m/Y') : '-' }}
            </div>
          @endif

          @if($invoice->error_message)
            <div class="alert alert-danger">
              <strong>{{ __('Error') }}:</strong> {{ $invoice->error_message }}
              @if($invoice->error_code)
                <br><small>Código: {{ $invoice->error_code }}</small>
              @endif
            </div>
          @endif

          <hr>
          <h6 class="font-weight-bold">{{ __('Emisor') }}</h6>
          <div class="row">
            <div class="col-md-6">
              <p><strong>{{ __('CUIT usado') }}:</strong> {{ $invoice->issuer_cuit_used ?? '-' }}</p>
              <p><strong>{{ __('Punto de venta usado') }}:</strong> {{ $invoice->point_of_sale_used ?? '-' }}</p>
            </div>
            <div class="col-md-6">
              <p><strong>{{ __('Tipo de comprobante usado') }}:</strong> {{ $invoice->invoice_type_used ?? '-' }}</p>
              <p><strong>{{ __('% Comisión usado') }}:</strong> {{ $invoice->commission_rate ?? '-' }}</p>
            </div>
          </div>

          <hr>
          <h6 class="font-weight-bold">{{ __('Receptor') }}</h6>
          <div class="row">
            <div class="col-md-6">
              <p><strong>{{ __('Nombre') }}:</strong> {{ $invoice->recipient_name ?? ($invoice->booking?->fname . ' ' . $invoice->booking?->lname) ?? '-' }}</p>
              <p><strong>{{ __('Documento') }}:</strong> {{ $invoice->recipient_tax_id ?? $invoice->booking?->dni ?? '-' }}</p>
            </div>
            <div class="col-md-6">
              <p><strong>{{ __('Condición IVA') }}:</strong> {{ $invoice->recipient_tax_condition ?? '-' }}</p>
              <p><strong>{{ __('Dirección') }}:</strong> {{ $invoice->recipient_address ?? '-' }}</p>
            </div>
          </div>

          <hr>
          <h6 class="font-weight-bold">{{ __('Items') }}</h6>
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>{{ __('Descripción') }}</th>
                  <th>{{ __('Cant.') }}</th>
                  <th>{{ __('Precio unit.') }}</th>
                  <th>{{ __('Neto') }}</th>
                  <th>{{ __('IVA') }}</th>
                  <th>{{ __('Total') }}</th>
                </tr>
              </thead>
              <tbody>
                @foreach($invoice->items as $item)
                  <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format($item->unit_price ?? 0, 2, ',', '.') }}</td>
                    <td>${{ number_format($item->net_amount ?? 0, 2, ',', '.') }}</td>
                    <td>${{ number_format($item->vat_amount ?? 0, 2, ',', '.') }}</td>
                    <td>${{ number_format($item->total_amount ?? 0, 2, ',', '.') }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <div class="row justify-content-end">
            <div class="col-md-4">
              <table class="table table-sm">
                <tr>
                  <td><strong>{{ __('Neto') }}</strong></td>
                  <td class="text-right">${{ number_format($invoice->net_amount ?? 0, 2, ',', '.') }}</td>
                </tr>
                <tr>
                  <td><strong>{{ __('IVA') }}</strong></td>
                  <td class="text-right">${{ number_format($invoice->vat_amount ?? 0, 2, ',', '.') }}</td>
                </tr>
                <tr>
                  <td><strong>{{ __('Exento') }}</strong></td>
                  <td class="text-right">${{ number_format($invoice->exempt_amount ?? 0, 2, ',', '.') }}</td>
                </tr>
                <tr>
                  <td><strong>{{ __('No gravado') }}</strong></td>
                  <td class="text-right">${{ number_format($invoice->non_taxed_amount ?? 0, 2, ',', '.') }}</td>
                </tr>
                <tr class="table-active">
                  <td><strong>{{ __('Total') }}</strong></td>
                  <td class="text-right font-weight-bold">${{ number_format($invoice->total_amount ?? 0, 2, ',', '.') }}</td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      </div>

      @if($invoice->arca_request || $invoice->arca_response)
        <div class="card">
          <div class="card-header">
            <div class="card-title">{{ __('Datos técnicos') }}</div>
          </div>
          <div class="card-body">
            @if($invoice->arca_request)
              <h6>{{ __('Request ARCA') }}</h6>
              <pre class="bg-light p-3" style="font-size: 11px; max-height: 300px; overflow: auto;">{{ json_encode($invoice->arca_request, JSON_PRETTY_PRINT) }}</pre>
            @endif
            @if($invoice->arca_response)
              <h6>{{ __('Response ARCA') }}</h6>
              <pre class="bg-light p-3" style="font-size: 11px; max-height: 300px; overflow: auto;">{{ json_encode($invoice->arca_response, JSON_PRETTY_PRINT) }}</pre>
            @endif
          </div>
        </div>
      @endif
    </div>

    <div class="col-md-4">
      <div class="card">
        <div class="card-header">
          <div class="card-title">{{ __('Acciones') }}</div>
        </div>
        <div class="card-body">
          <a href="{{ route('admin.arca_invoices.pdf', $invoice->id) }}" class="btn btn-block btn-secondary mb-2">
            <i class="fas fa-file-pdf mr-2"></i> {{ __('Descargar PDF') }}
          </a>

          @if(in_array($invoice->status, ['error', 'blocked'], true))
            <form action="{{ route('admin.arca_invoices.retry', $invoice->id) }}" method="post" onsubmit="return confirm('{{ __('¿Reintentar emisión?') }}')">
              @csrf
              <button type="submit" class="btn btn-block btn-warning mb-2">
                <i class="fas fa-redo mr-2"></i> {{ __('Reintentar emisión') }}
              </button>
            </form>
          @endif

          <a href="{{ route('admin.arca_invoices.index') }}" class="btn btn-block btn-light">
            <i class="fas fa-arrow-left mr-2"></i> {{ __('Volver al listado') }}
          </a>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <div class="card-title">{{ __('Reserva asociada') }}</div>
        </div>
        <div class="card-body">
          @if($invoice->booking)
            <p><strong>{{ __('ID') }}:</strong> {{ $invoice->booking->id }}</p>
            <p><strong>{{ __('Evento') }}:</strong> {{ $invoice->booking->evnt?->title ?? '-' }}</p>
            <p><strong>{{ __('Cliente') }}:</strong> {{ $invoice->booking->fname }} {{ $invoice->booking->lname }}</p>
            <p><strong>{{ __('Email') }}:</strong> {{ $invoice->booking->email }}</p>
            <p><strong>{{ __('Estado pago') }}:</strong> {{ $invoice->booking->paymentStatus ?? '-' }}</p>
            <a href="{{ route('admin.event_booking.details', $invoice->booking->id) }}" class="btn btn-sm btn-info">
              {{ __('Ver reserva') }}
            </a>
          @else
            <p class="text-muted">{{ __('No hay reserva asociada.') }}</p>
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection
