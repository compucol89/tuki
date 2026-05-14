<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Factura electrónica — TukiPass</title>
  <style>
    * { box-sizing: border-box; }
    body {
      font-family: 'Inter', Helvetica, Arial, sans-serif;
      font-size: 12px;
      color: #333;
      margin: 0;
      padding: 0;
    }
    .header {
      background: #1e2532;
      color: #fff;
      padding: 24px 32px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .header-left h1 {
      margin: 0 0 4px;
      font-size: 18px;
      font-weight: 600;
    }
    .header-left p {
      margin: 0;
      font-size: 11px;
      opacity: 0.9;
    }
    .header-right img {
      max-height: 48px;
      max-width: 160px;
    }
    .badge {
      display: inline-block;
      background: #F97316;
      color: #fff;
      font-size: 10px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      padding: 3px 10px;
      border-radius: 3px;
      margin-top: 8px;
    }
    .content { padding: 24px 32px; }
    .section { margin-bottom: 20px; }
    .section-title {
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #1e2532;
      border-bottom: 2px solid #F97316;
      padding-bottom: 4px;
      margin-bottom: 10px;
    }
    .row {
      display: flex;
      justify-content: space-between;
      padding: 4px 0;
    }
    .row strong {
      color: #555;
    }
    .two-col {
      display: flex;
      gap: 24px;
    }
    .two-col .col {
      flex: 1;
    }
    .highlight {
      background: #f9fafb;
      border: 1px solid #e5e7eb;
      border-radius: 4px;
      padding: 12px;
      margin: 12px 0;
    }
    .total {
      font-size: 14px;
      font-weight: 700;
      color: #1e2532;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 8px;
    }
    th, td {
      text-align: left;
      padding: 8px;
      border-bottom: 1px solid #e5e7eb;
    }
    th {
      background: #1e2532;
      color: #fff;
      font-size: 10px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .footer {
      background: #f9fafb;
      padding: 16px 32px;
      text-align: center;
      font-size: 10px;
      color: #666;
      border-top: 1px solid #eee;
      margin-top: 24px;
    }
    .cae-box {
      background: #ecfdf5;
      border: 1px solid #a7f3d0;
      border-radius: 4px;
      padding: 10px;
      margin: 10px 0;
      text-align: center;
    }
    .cae-box .label {
      font-size: 10px;
      color: #065f46;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .cae-box .value {
      font-size: 14px;
      font-weight: 700;
      color: #047857;
      margin-top: 2px;
      font-family: monospace;
    }
  </style>
</head>
<body>
  <div class="header">
    <div class="header-left">
      <h1>TukiPass</h1>
      <p>Factura electrónica autorizada por ARCA/AFIP</p>
      <div class="badge">{{ $invoice->environment === 'production' ? 'Producción' : 'Homologación' }}</div>
    </div>
    <div class="header-right">
      @if($billing->pdf_logo_path)
        <img src="{{ storage_path('app/public/' . $billing->pdf_logo_path) }}" alt="Logo">
      @endif
    </div>
  </div>

  <div class="content">
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
      <div class="row">
        <strong>Período facturado:</strong>
        <span>{{ $invoice->service_from ? $invoice->service_from->format('d/m/Y') : 'N/A' }} al {{ $invoice->service_to ? $invoice->service_to->format('d/m/Y') : 'N/A' }}</span>
      </div>
      <div class="row">
        <strong>Vencimiento de pago:</strong>
        <span>{{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : 'N/A' }}</span>
      </div>

      <div class="cae-box">
        <div class="label">Código de Autorización Electrónica (CAE)</div>
        <div class="value">{{ $invoice->cae }}</div>
        <div style="font-size: 10px; color: #065f46; margin-top: 2px;">
          Vencimiento CAE: {{ $invoice->cae_due_date ? $invoice->cae_due_date->format('d/m/Y') : 'N/A' }}
        </div>
      </div>
    </div>

    <div class="two-col">
      <div class="col">
        <div class="section">
          <div class="section-title">Emisor</div>
          <div class="row">
            <strong>Razón social:</strong>
            <span>{{ $billing->issuer_name ?? config('arca.issuer_name', 'TukiPass') }}</span>
          </div>
          <div class="row">
            <strong>CUIT:</strong>
            <span>{{ $invoice->issuer_cuit_used ?? config('arca.cuit', 'N/A') }}</span>
          </div>
          <div class="row">
            <strong>Condición IVA:</strong>
            <span>{{ $billing->issuer_iva_condition_text ?? 'Responsable Inscripto' }}</span>
          </div>
          @if($billing->issuer_address)
          <div class="row">
            <strong>Dirección:</strong>
            <span>{{ $billing->issuer_address }}</span>
          </div>
          @endif
        </div>
      </div>

      <div class="col">
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
      </div>
    </div>

    <div class="section">
      <div class="section-title">Detalle</div>
      <table>
        <thead>
          <tr>
            <th>Descripción</th>
            <th style="text-align: right;">Cant.</th>
            <th style="text-align: right;">Precio unit.</th>
            <th style="text-align: right;">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          @foreach($invoice->items as $item)
          <tr>
            <td>{{ $item->description }}</td>
            <td style="text-align: right;">{{ $item->quantity }}</td>
            <td style="text-align: right;">${{ number_format($item->unit_price ?? 0, 2, ',', '.') }}</td>
            <td style="text-align: right;">${{ number_format($item->total_amount ?? 0, 2, ',', '.') }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
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

    <div style="background: #fff7ed; border-left: 3px solid #F97316; padding: 10px 12px; margin: 12px 0; font-size: 11px; color: #7c2d12;">
      <strong>Importante:</strong> Este comprobante corresponde al cargo de servicio/comisión de TukiPass por la gestión de la venta de entradas. La compra de las entradas propiamente dicha se realizó a través de nuestra plataforma. Si tenés dudas, contactanos por el centro de ayuda.
    </div>
  </div>

  <div class="footer">
    <p><strong>TukiPass</strong> — Entradas y Tickets Online para Eventos en Argentina</p>
    <p>Factura electrónica autorizada por ARCA/AFIP</p>
    <p>Homologación: {{ $invoice->environment ?? 'homologation' }}</p>
  </div>
</body>
</html>
