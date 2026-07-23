@php
  $interRegular = resource_path('fonts/inter/Inter-Regular.ttf');
  $interMedium = resource_path('fonts/inter/Inter-Medium.ttf');
  $interSemiBold = resource_path('fonts/inter/Inter-SemiBold.ttf');
  $interBold = resource_path('fonts/inter/Inter-Bold.ttf');

  $formatMoney = static fn ($amount) => '$' . number_format((float) ($amount ?? 0), 2, ',', '.');
  $formatDate = static fn ($date) => $date ? $date->format('d/m/Y') : 'Sin dato';
  $formatDateTime = static fn ($date) => $date ? $date->format('d/m/Y H:i') : now()->format('d/m/Y H:i');
  $formatPercent = static fn ($value) => rtrim(rtrim(number_format((float) $value, 2, ',', '.'), '0'), ',') . '%';
  $formatCondition = static function ($value, string $fallback): string {
    $text = trim((string) $value);

    if ($text === '') {
      return $fallback;
    }

    $known = [
      'consumidor_final' => 'Consumidor Final',
      'responsable_inscripto' => 'Responsable Inscripto',
      'monotributo' => 'Monotributo',
      'exento' => 'Exento',
    ];

    return $known[$text] ?? ucwords(str_replace(['_', '-'], ' ', $text));
  };
  $formatCuit = static function ($value) {
    $digits = preg_replace('/\D+/', '', (string) $value);

    if (strlen($digits) === 11) {
      return substr($digits, 0, 2) . '-' . substr($digits, 2, 8) . '-' . substr($digits, 10, 1);
    }

    return $value ?: '30-71885087-4';
  };

  $issuerName = trim((string) ($billing->issuer_name ?: config('arca.issuer_name'))) ?: 'TAYRONA GROUP SAS';
  $issuerCuit = $formatCuit($invoice->issuer_cuit_used ?: $billing->issuer_cuit ?: '30718850874');
  $issuerIva = $formatCondition($billing->issuer_iva_condition_text ?: $billing->issuer_iva_condition, 'Responsable Inscripto');
  $issuerAddress = trim((string) $billing->issuer_address);
  $recipientName = trim((string) ($invoice->recipient_name ?: trim(($booking->fname ?? '') . ' ' . ($booking->lname ?? '')))) ?: 'Consumidor final';
  $recipientDocument = trim((string) ($invoice->recipient_tax_id ?: $booking->dni ?: 'Consumidor final'));
  $recipientIva = $formatCondition($invoice->recipient_tax_condition, 'Consumidor Final');
  $recipientEmail = trim((string) ($booking->email ?? 'Sin email'));
  $vatRate = $invoice->vat_percentage_used ?? ((float) config('arca.default_vat_rate', 0) * 100);
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Factura electrónica - TukiPass</title>
  <style>
    @if(file_exists($interRegular))
      @font-face { font-family: 'Inter'; font-style: normal; font-weight: 400; src: url('file://{{ $interRegular }}') format('truetype'); }
    @endif
    @if(file_exists($interMedium))
      @font-face { font-family: 'Inter'; font-style: normal; font-weight: 500; src: url('file://{{ $interMedium }}') format('truetype'); }
    @endif
    @if(file_exists($interSemiBold))
      @font-face { font-family: 'Inter'; font-style: normal; font-weight: 600; src: url('file://{{ $interSemiBold }}') format('truetype'); }
    @endif
    @if(file_exists($interBold))
      @font-face { font-family: 'Inter'; font-style: normal; font-weight: 700; src: url('file://{{ $interBold }}') format('truetype'); }
    @endif

    @page {
      size: A4 portrait;
      margin: 9mm 10mm;
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      color: #1e2532;
      background: #fff;
      font-family: 'Inter', 'DejaVu Sans', Arial, sans-serif;
      font-size: 9.2px;
      font-weight: 400;
      line-height: 1.28;
    }

    .invoice-shell {
      width: 100%;
    }

    .topbar {
      width: 100%;
      border-collapse: collapse;
      background: #1e2532;
      color: #fff;
    }

    .topbar td {
      padding: 9px 12px;
      vertical-align: middle;
    }

    .brand {
      color: #fff;
      font-size: 15px;
      font-weight: 700;
      letter-spacing: -0.1px;
      margin: 0;
    }

    .brand span {
      color: #F97316;
    }

    .topbar-subtitle {
      margin-top: 2px;
      color: #d7dde8;
      font-size: 7.4px;
      font-weight: 500;
    }

    .status-pill {
      display: inline-block;
      padding: 3px 8px;
      border-radius: 999px;
      color: #fff;
      background: #F97316;
      font-size: 7.2px;
      font-weight: 700;
      text-transform: uppercase;
    }

    .content {
      padding-top: 8px;
    }

    .section {
      margin-bottom: 7px;
      page-break-inside: avoid;
    }

    .section-title {
      margin: 0 0 5px;
      padding-bottom: 3px;
      border-bottom: 1.2px solid #F97316;
      color: #1e2532;
      font-size: 7.8px;
      font-weight: 700;
      letter-spacing: 0.25px;
      text-transform: uppercase;
    }

    .box {
      border: 1px solid #e4e9f0;
      border-radius: 5px;
      padding: 7px 8px;
      background: #fff;
    }

    .kv-table {
      width: 100%;
      border-collapse: collapse;
    }

    .kv-table td {
      padding: 2px 0;
      vertical-align: top;
    }

    .kv-label {
      width: 34%;
      color: #5c6b82;
      font-size: 7.6px;
      font-weight: 600;
    }

    .kv-value {
      color: #1e2532;
      font-size: 8.1px;
      font-weight: 500;
      text-align: right;
    }

    .meta-table,
    .party-table {
      width: 100%;
      border-collapse: collapse;
    }

    .meta-table td {
      width: 50%;
      padding-right: 7px;
      vertical-align: top;
    }

    .meta-table td:last-child {
      padding-right: 0;
    }

    .party-table td {
      width: 50%;
      padding-right: 7px;
      vertical-align: top;
    }

    .party-table td:last-child {
      padding-right: 0;
    }

    .cae-box {
      margin-top: 5px;
      padding: 6px 8px;
      border: 1px solid #bbf7d0;
      border-radius: 5px;
      background: #f0fdf4;
      color: #047857;
      text-align: center;
    }

    .cae-label {
      font-size: 7px;
      font-weight: 700;
      letter-spacing: 0.2px;
      text-transform: uppercase;
    }

    .cae-value {
      margin-top: 1px;
      font-size: 10.8px;
      font-weight: 700;
    }

    .items-table {
      width: 100%;
      border-collapse: collapse;
      border: 1px solid #dfe7ef;
      border-radius: 5px;
      overflow: hidden;
    }

    .items-table th {
      padding: 5px 6px;
      background: #1e2532;
      color: #fff;
      font-size: 7.1px;
      font-weight: 700;
      letter-spacing: 0.25px;
      text-transform: uppercase;
    }

    .items-table td {
      padding: 5px 6px;
      border-top: 1px solid #edf1f5;
      color: #1e2532;
      font-size: 8px;
      vertical-align: top;
    }

    .items-table .description {
      width: 61%;
      line-height: 1.25;
    }

    .text-right {
      text-align: right;
    }

    .totals-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 6px;
    }

    .totals-table td {
      vertical-align: top;
    }

    .notice {
      padding: 7px 9px;
      border-left: 3px solid #F97316;
      border-radius: 4px;
      background: #fff7ed;
      color: #7c2d12;
      font-size: 7.4px;
      line-height: 1.25;
    }

    .summary {
      width: 210px;
      margin-left: auto;
      border: 1px solid #e4e9f0;
      border-radius: 5px;
      padding: 6px 8px;
      background: #fafbfc;
    }

    .summary table {
      width: 100%;
      border-collapse: collapse;
    }

    .summary td {
      padding: 2px 0;
      font-size: 8px;
    }

    .summary .total td {
      padding-top: 4px;
      border-top: 1px solid #dfe7ef;
      color: #1e2532;
      font-size: 10.2px;
      font-weight: 700;
    }

    .footer {
      margin-top: 7px;
      padding: 7px 10px;
      border-top: 1px solid #edf1f5;
      background: #f9fafb;
      color: #64748b;
      font-size: 7.1px;
      line-height: 1.25;
      text-align: center;
      page-break-inside: avoid;
    }
  </style>
</head>
<body>
  <div class="invoice-shell">
    <table class="topbar">
      <tr>
        <td>
          <div class="brand"><span>Tuki</span>Pass</div>
          <div class="topbar-subtitle">Factura electrónica autorizada por ARCA</div>
        </td>
        <td class="text-right">
          <span class="status-pill">Comprobante válido</span>
        </td>
      </tr>
    </table>

    <div class="content">
      <div class="section">
        <table class="meta-table">
          <tr>
            <td>
              <div class="section-title">Comprobante</div>
              <div class="box">
                <table class="kv-table">
                  <tr>
                    <td class="kv-label">Número</td>
                    <td class="kv-value">{{ $invoiceNumber }}</td>
                  </tr>
                  <tr>
                    <td class="kv-label">Emisión</td>
                    <td class="kv-value">{{ $formatDateTime($invoice->issued_at) }}</td>
                  </tr>
                  <tr>
                    <td class="kv-label">Concepto</td>
                    <td class="kv-value">Servicios</td>
                  </tr>
                  <tr>
                    <td class="kv-label">Período</td>
                    <td class="kv-value">{{ $formatDate($invoice->service_from) }} al {{ $formatDate($invoice->service_to) }}</td>
                  </tr>
                  <tr>
                    <td class="kv-label">Vencimiento</td>
                    <td class="kv-value">{{ $formatDate($invoice->due_date) }}</td>
                  </tr>
                </table>
              </div>
            </td>
            <td>
              <div class="section-title">Autorización ARCA</div>
              <div class="cae-box">
                <div class="cae-label">Código de Autorización Electrónica (CAE)</div>
                <div class="cae-value">{{ $invoice->cae ?: 'Sin CAE' }}</div>
                <div style="margin-top: 2px; font-size: 7.2px;">Vencimiento CAE: {{ $formatDate($invoice->cae_due_date) }}</div>
              </div>
            </td>
          </tr>
        </table>
      </div>

      <div class="section">
        <table class="party-table">
          <tr>
            <td>
              <div class="section-title">Emisor</div>
              <div class="box">
                <table class="kv-table">
                  <tr>
                    <td class="kv-label">Razón social</td>
                    <td class="kv-value">{{ $issuerName }}</td>
                  </tr>
                  <tr>
                    <td class="kv-label">CUIT</td>
                    <td class="kv-value">{{ $issuerCuit }}</td>
                  </tr>
                  <tr>
                    <td class="kv-label">IVA</td>
                    <td class="kv-value">{{ $issuerIva }}</td>
                  </tr>
                  @if($issuerAddress !== '')
                    <tr>
                      <td class="kv-label">Domicilio</td>
                      <td class="kv-value">{{ $issuerAddress }}</td>
                    </tr>
                  @endif
                </table>
              </div>
            </td>
            <td>
              <div class="section-title">Receptor</div>
              <div class="box">
                <table class="kv-table">
                  <tr>
                    <td class="kv-label">Nombre</td>
                    <td class="kv-value">{{ $recipientName }}</td>
                  </tr>
                  <tr>
                    <td class="kv-label">Documento</td>
                    <td class="kv-value">{{ $recipientDocument }}</td>
                  </tr>
                  <tr>
                    <td class="kv-label">IVA</td>
                    <td class="kv-value">{{ $recipientIva }}</td>
                  </tr>
                  <tr>
                    <td class="kv-label">Email</td>
                    <td class="kv-value">{{ $recipientEmail }}</td>
                  </tr>
                </table>
              </div>
            </td>
          </tr>
        </table>
      </div>

      <div class="section">
        <div class="section-title">Detalle</div>
        <table class="items-table">
          <thead>
            <tr>
              <th>Descripción</th>
              <th class="text-right">Cant.</th>
              <th class="text-right">Precio unit.</th>
              <th class="text-right">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            @foreach($invoice->items as $item)
              <tr>
                <td class="description">{{ $item->description }}</td>
                <td class="text-right">{{ number_format((float) ($item->quantity ?? 0), 2, ',', '.') }}</td>
                <td class="text-right">{{ $formatMoney($item->unit_price) }}</td>
                <td class="text-right">{{ $formatMoney($item->total_amount) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <table class="totals-table">
        <tr>
          <td>
            <div class="notice">
              <strong>Importante:</strong> Este comprobante corresponde al cargo de servicio de TukiPass por la gestión de la reserva. TukiPass no organiza ni produce los eventos publicados; la realización, accesos, horarios, cambios, cancelaciones y reembolsos son responsabilidad exclusiva del organizador.
            </div>
          </td>
          <td style="width: 225px;">
            <div class="summary">
              <table>
                <tr>
                  <td>Subtotal</td>
                  <td class="text-right">{{ $formatMoney($invoice->net_amount) }}</td>
                </tr>
                @if(($invoice->vat_amount ?? 0) > 0)
                  <tr>
                    <td>IVA {{ $formatPercent($vatRate) }}</td>
                    <td class="text-right">{{ $formatMoney($invoice->vat_amount) }}</td>
                  </tr>
                @endif
                <tr class="total">
                  <td>Total</td>
                  <td class="text-right">{{ $formatMoney($invoice->total_amount) }}</td>
                </tr>
              </table>
            </div>
          </td>
        </tr>
      </table>

      <div class="footer">
        <strong>TukiPass</strong> - Entradas online para eventos en Argentina<br>
        TAYRONA GROUP SAS - CUIT 30-71885087-4 - Factura electrónica autorizada por ARCA
      </div>
    </div>
  </div>
</body>
</html>
