@extends('frontend.layout')

@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/front/css/payment.css') }}">
@endpush

@section('title', 'Factura electrónica — TukiPass')

@section('content')
<div class="container" style="max-width:680px; margin:60px auto; padding:0 16px 60px;">

  <div style="text-align:center; margin-bottom:32px;">
    <img src="{{ asset('assets/front/img/logo.png') }}" alt="TukiPass" style="height:40px;">
  </div>

  {{-- Encabezado --}}
  <div style="background:#1e2532; border-radius:12px 12px 0 0; padding:28px 32px; color:#fff;">
    <p style="margin:0 0 4px; font-size:13px; color:#94a3b8; letter-spacing:.05em; text-transform:uppercase;">Factura electrónica</p>
    <h1 style="margin:0; font-size:22px; font-weight:700;">{{ $eventTitle ?: 'Tu evento' }}</h1>
    <p style="margin:8px 0 0; font-size:14px; color:#94a3b8;">Reserva #{{ $booking->booking_id }}</p>
  </div>

  {{-- Cuerpo --}}
  <div style="background:#fff; border:1px solid #e2e8f0; border-top:none; border-radius:0 0 12px 12px; padding:32px;">

    @if(!$invoice)
      {{-- Sin factura aún --}}
      <div style="text-align:center; padding:24px 0;">
        <div style="font-size:48px; margin-bottom:16px;">⏳</div>
        <h2 style="font-size:18px; font-weight:700; color:#1e2532; margin:0 0 8px;">Tu comprobante está en proceso</h2>
        <p style="color:#64748b; font-size:15px; margin:0;">
          Estamos generando tu factura electrónica. Recibirás un email cuando esté lista.<br>
          Podés volver a esta página en unos minutos.
        </p>
      </div>

    @elseif($invoice->status === 'approved')
      {{-- Factura aprobada --}}
      <div style="display:flex; align-items:center; gap:12px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:16px 20px; margin-bottom:28px;">
        <span style="font-size:24px;">✅</span>
        <div>
          <p style="margin:0; font-weight:700; color:#15803d; font-size:15px;">Factura emitida correctamente</p>
          <p style="margin:2px 0 0; font-size:13px; color:#166534;">
            CAE válido hasta {{ $invoice->cae_due_date?->format('d/m/Y') ?? '—' }}
          </p>
        </div>
      </div>

      {{-- Datos del comprobante --}}
      <table style="width:100%; border-collapse:collapse; font-size:14px; color:#334155;">
        <tr>
          <td style="padding:10px 0; border-bottom:1px solid #f1f5f9; color:#64748b; width:50%;">Tipo de comprobante</td>
          <td style="padding:10px 0; border-bottom:1px solid #f1f5f9; font-weight:600;">
            @php
              $tipos = [1=>'Factura A', 6=>'Factura B', 11=>'Factura C', 51=>'Factura M'];
              echo $tipos[$invoice->cbte_tipo] ?? 'Comprobante ' . $invoice->cbte_tipo;
            @endphp
          </td>
        </tr>
        <tr>
          <td style="padding:10px 0; border-bottom:1px solid #f1f5f9; color:#64748b;">Número</td>
          <td style="padding:10px 0; border-bottom:1px solid #f1f5f9; font-weight:600; font-family:monospace; font-size:15px;">
            {{ str_pad($invoice->cbte_tipo ?? 0, 3, '0', STR_PAD_LEFT) }}-{{ str_pad($invoice->point_of_sale ?? 0, 5, '0', STR_PAD_LEFT) }}-{{ str_pad($invoice->cbte_nro ?? 0, 8, '0', STR_PAD_LEFT) }}
          </td>
        </tr>
        <tr>
          <td style="padding:10px 0; border-bottom:1px solid #f1f5f9; color:#64748b;">Fecha de emisión</td>
          <td style="padding:10px 0; border-bottom:1px solid #f1f5f9; font-weight:600;">
            {{ $invoice->issued_at?->format('d/m/Y H:i') ?? '—' }}
          </td>
        </tr>
        <tr>
          <td style="padding:10px 0; border-bottom:1px solid #f1f5f9; color:#64748b;">Período facturado</td>
          <td style="padding:10px 0; border-bottom:1px solid #f1f5f9; font-weight:600;">
            {{ $invoice->service_from?->format('d/m/Y') ?? '—' }} al {{ $invoice->service_to?->format('d/m/Y') ?? '—' }}
          </td>
        </tr>
        <tr>
          <td style="padding:10px 0; border-bottom:1px solid #f1f5f9; color:#64748b;">Vencimiento de pago</td>
          <td style="padding:10px 0; border-bottom:1px solid #f1f5f9; font-weight:600;">
            {{ $invoice->due_date?->format('d/m/Y') ?? '—' }}
          </td>
        </tr>
        <tr>
          <td style="padding:10px 0; border-bottom:1px solid #f1f5f9; color:#64748b;">CAE</td>
          <td style="padding:10px 0; border-bottom:1px solid #f1f5f9; font-weight:600; font-family:monospace;">{{ $invoice->cae ?? '—' }}</td>
        </tr>
        <tr>
          <td style="padding:10px 0; border-bottom:1px solid #f1f5f9; color:#64748b;">Receptor</td>
          <td style="padding:10px 0; border-bottom:1px solid #f1f5f9; font-weight:600;">{{ $invoice->recipient_name ?? '—' }}</td>
        </tr>
        @if($invoice->recipient_tax_id)
        <tr>
          <td style="padding:10px 0; border-bottom:1px solid #f1f5f9; color:#64748b;">CUIT / DNI</td>
          <td style="padding:10px 0; border-bottom:1px solid #f1f5f9; font-weight:600;">{{ $invoice->recipient_tax_id }}</td>
        </tr>
        @endif
      </table>

      {{-- Detalle del importe --}}
      <div style="background:#f8fafc; border-radius:8px; padding:20px; margin-top:24px;">
        <p style="margin:0 0 12px; font-size:13px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.05em;">Detalle</p>
        @foreach($invoice->items as $item)
          <div style="display:flex; justify-content:space-between; font-size:14px; margin-bottom:8px; color:#334155;">
            <span>{{ $item->description }}</span>
            <span style="font-weight:600;">$ {{ number_format($item->net_amount, 2, ',', '.') }}</span>
          </div>
        @endforeach
        @if($invoice->vat_amount > 0)
          <div style="display:flex; justify-content:space-between; font-size:14px; margin-bottom:8px; color:#64748b;">
            <span>IVA ({{ number_format(($invoice->vat_percentage_used ?? 0) * 100, 0) }}%)</span>
            <span>$ {{ number_format($invoice->vat_amount, 2, ',', '.') }}</span>
          </div>
        @endif
        <div style="display:flex; justify-content:space-between; font-size:16px; font-weight:700; border-top:2px solid #e2e8f0; padding-top:12px; margin-top:8px; color:#1e2532;">
          <span>Total</span>
          <span>$ {{ number_format($invoice->total_amount, 2, ',', '.') }}</span>
        </div>
      </div>

      <p style="font-size:12px; color:#94a3b8; margin:20px 0 0; line-height:1.6;">
        Este comprobante fue emitido por <strong>Tayrona Group SAS</strong> ante ARCA/AFIP en concepto de cargo de servicio por gestión de compra de entradas a través de TukiPass. No corresponde a la entrada ni al precio del evento.
      </p>

    @elseif($invoice->status === 'error')
      {{-- Error --}}
      <div style="text-align:center; padding:24px 0;">
        <div style="font-size:48px; margin-bottom:16px;">⚠️</div>
        <h2 style="font-size:18px; font-weight:700; color:#1e2532; margin:0 0 8px;">Hubo un problema al generar tu factura</h2>
        <p style="color:#64748b; font-size:15px; margin:0 0 20px;">
          Estamos trabajando para resolverlo. Si necesitás el comprobante con urgencia,
          contactanos y lo gestionamos manualmente.
        </p>
        <a href="mailto:hola@tukipass.com" style="display:inline-block; background:#F97316; color:#fff; font-weight:700; padding:12px 28px; border-radius:8px; text-decoration:none; font-size:15px;">Contactar soporte</a>
      </div>

    @else
      {{-- Estado pendiente/blocked/ready --}}
      <div style="text-align:center; padding:24px 0;">
        <div style="font-size:48px; margin-bottom:16px;">🔄</div>
        <h2 style="font-size:18px; font-weight:700; color:#1e2532; margin:0 0 8px;">Factura en proceso</h2>
        <p style="color:#64748b; font-size:15px; margin:0;">
          Estamos procesando tu comprobante fiscal. Recibirás un email cuando esté disponible.
        </p>
      </div>
    @endif

  </div>

  <p style="text-align:center; font-size:12px; color:#94a3b8; margin-top:24px;">
    TukiPass · <a href="{{ route('index') }}" style="color:#F97316; text-decoration:none;">Volver al inicio</a>
  </p>

</div>
@endsection
