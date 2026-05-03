<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Factura electrónica — TukiPass</title>
  <style>
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background-color: #f5f5f5;
      margin: 0;
      padding: 20px;
      color: #333;
    }
    .container {
      max-width: 600px;
      margin: 0 auto;
      background: #fff;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .header {
      background: #1e2532;
      color: #fff;
      padding: 24px 32px;
      text-align: center;
    }
    .header h1 {
      margin: 0 0 4px;
      font-size: 20px;
      font-weight: 600;
    }
    .header p {
      margin: 0;
      font-size: 13px;
      opacity: 0.9;
    }
    .badge {
      display: inline-block;
      background: #F97316;
      color: #fff;
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      padding: 4px 12px;
      border-radius: 4px;
      margin-top: 12px;
    }
    .body {
      padding: 32px;
    }
    .section {
      margin-bottom: 24px;
    }
    .section-title {
      font-size: 12px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #666;
      margin-bottom: 8px;
      border-bottom: 1px solid #eee;
      padding-bottom: 4px;
    }
    .row {
      display: flex;
      justify-content: space-between;
      padding: 6px 0;
      font-size: 14px;
    }
    .row strong {
      color: #555;
    }
    .highlight {
      background: #f9fafb;
      border: 1px solid #e5e7eb;
      border-radius: 6px;
      padding: 16px;
      margin: 16px 0;
    }
    .highlight .row {
      padding: 8px 0;
    }
    .total {
      font-size: 18px;
      font-weight: 700;
      color: #1e2532;
    }
    .footer {
      background: #f9fafb;
      padding: 24px 32px;
      text-align: center;
      font-size: 12px;
      color: #666;
      border-top: 1px solid #eee;
    }
    .footer p {
      margin: 4px 0;
    }
    .disclaimer {
      background: #fff7ed;
      border-left: 4px solid #F97316;
      padding: 12px 16px;
      margin: 16px 0;
      font-size: 13px;
      color: #7c2d12;
    }
    .cae-box {
      background: #ecfdf5;
      border: 1px solid #a7f3d0;
      border-radius: 6px;
      padding: 12px 16px;
      margin: 12px 0;
      text-align: center;
    }
    .cae-box .label {
      font-size: 11px;
      color: #065f46;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .cae-box .value {
      font-size: 16px;
      font-weight: 700;
      color: #047857;
      margin-top: 4px;
      font-family: monospace;
    }
    @media (max-width: 480px) {
      .body { padding: 20px; }
      .header { padding: 20px; }
      .footer { padding: 20px; }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>TukiPass</h1>
      <p>Factura electrónica autorizada por ARCA/AFIP</p>
      <div class="badge">{{ $invoice->environment === 'production' ? 'Producción' : 'Homologación' }}</div>
    </div>

    <div class="body">
      <div class="section">
        <div class="section-title">Comprobante</div>
        <div class="row">
          <strong>Número:</strong>
          <span>{{ $invoiceNumber }}</span>
        </div>
        <div class="row">
          <strong>Fecha de emisión:</strong>
          <span>{{ $invoice->issued_at ? $invoice->issued_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</span>
        </div>
        <div class="row">
          <strong>Concepto:</strong>
          <span>Servicios</span>
        </div>
      </div>

      <div class="cae-box">
        <div class="label">Código de Autorización Electrónica (CAE)</div>
        <div class="value">{{ $invoice->cae }}</div>
        <div style="font-size: 12px; color: #065f46; margin-top: 4px;">
          Vencimiento CAE: {{ $invoice->cae_due_date ? $invoice->cae_due_date->format('d/m/Y') : 'N/A' }}
        </div>
      </div>

      <div class="section">
        <div class="section-title">Emisor</div>
        <div class="row">
          <strong>Razón social:</strong>
          <span>{{ config('arca.issuer_name', 'TukiPass') }}</span>
        </div>
        <div class="row">
          <strong>CUIT:</strong>
          <span>{{ $invoice->issuer_cuit_used ?? config('arca.cuit', 'N/A') }}</span>
        </div>
        <div class="row">
          <strong>Condición IVA:</strong>
          <span>Responsable Inscripto</span>
        </div>
      </div>

      <div class="section">
        <div class="section-title">Receptor</div>
        <div class="row">
          <strong>Nombre:</strong>
          <span>{{ $invoice->recipient_name ?? ($booking->fname . ' ' . $booking->lname) }}</span>
        </div>
        <div class="row">
          <strong>Documento:</strong>
          <span>{{ $invoice->recipient_tax_id ?? $booking->dni ?? 'N/A' }}</span>
        </div>
        <div class="row">
          <strong>Condición IVA:</strong>
          <span>{{ $invoice->recipient_tax_condition ?? 'Consumidor Final' }}</span>
        </div>
        <div class="row">
          <strong>Email:</strong>
          <span>{{ $booking->email }}</span>
        </div>
      </div>

      <div class="section">
        <div class="section-title">Detalle</div>
        @foreach($invoice->items as $item)
        <div class="row">
          <span>{{ $item->description ?? 'Comisión TukiPass por venta de entradas' }}</span>
          <span>${{ number_format($item->total_amount ?? $item->amount ?? 0, 2, ',', '.') }}</span>
        </div>
        @endforeach
      </div>

      <div class="highlight">
        <div class="row">
          <strong>Subtotal (neto):</strong>
          <span>${{ number_format($invoice->net_amount ?? 0, 2, ',', '.') }}</span>
        </div>
        @if(($invoice->vat_amount ?? 0) > 0)
        <div class="row">
          <strong>IVA ({{ $invoice->vat_percentage_used ?? config('arca.default_vat_rate', 0) * 100 }}%):</strong>
          <span>${{ number_format($invoice->vat_amount, 2, ',', '.') }}</span>
        </div>
        @endif
        <div class="row total">
          <strong>Total:</strong>
          <span>${{ number_format($invoice->total_amount ?? 0, 2, ',', '.') }}</span>
        </div>
      </div>

      <div class="disclaimer">
        <strong>Importante:</strong> Este comprobante corresponde al cargo de servicio/comisión de TukiPass por la gestión de la venta de entradas. La compra de las entradas propiamente dicha se realizó a través de nuestra plataforma. Si tenés dudas, contactanos por el centro de ayuda.
      </div>
    </div>

    <div class="footer">
      <p><strong>TukiPass</strong> — Entradas y Tickets Online para Eventos en Argentina</p>
      <p>Factura electrónica autorizada por ARCA/AFIP</p>
      <p>Homologación: {{ $invoice->environment ?? 'homologation' }}</p>
      <p style="margin-top: 12px; font-size: 11px; color: #999;">
        Este email fue generado automáticamente. No respondas a esta dirección.
      </p>
    </div>
  </div>
</body>
</html>
