<!DOCTYPE html>
<html lang="es">
@php
  $languageCode = $language->code;
  App::setLocale($languageCode);
  $primary     = '#' . ($websiteInfo->primary_color ?? 'f97316');
  $position    = $bookingInfo->currencyTextPosition ?? 'left';
  $currency    = $bookingInfo->currencyText ?? 'ARS';
  
  function formatMoney($amount, $position, $currency) {
    $amt = number_format((float)$amount, 2, ',', '.');
    return $position == 'left' ? $currency . ' ' . $amt : $amt . ' ' . $currency;
  }
  
  function cleanLocationPart($part) {
    if (empty($part)) return null;
    $cleaned = trim($part);
    if (strtoupper($cleaned) === 'N/A') return null;
    if (strtoupper($cleaned) === 'NA') return null;
    if ($cleaned === '-') return null;
    return $cleaned;
  }
  
  $tickets    = $bookingInfo->variation != null ? json_decode($bookingInfo->variation, true) : null;
  $ticketCount = $tickets ? count($tickets) : $bookingInfo->quantity;
  $eventDate  = \Carbon\Carbon::parse($bookingInfo->event_date)->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY');

  // Construir ubicación solo con datos reales
  $locationParts = array_filter([
    cleanLocationPart($bookingInfo->city ?? null),
    cleanLocationPart($bookingInfo->state ?? null),
    cleanLocationPart($bookingInfo->country ?? null),
  ]);
  $location = implode(', ', $locationParts);
  
  // Limpiar dirección
  $address = cleanLocationPart($bookingInfo->address ?? null);

  // Calcular valores económicos
  $quantity = $bookingInfo->quantity ?? 1;
  $unitPrice = ($quantity > 0) ? ($bookingInfo->price / $quantity) : 0;
  $subtotal = $bookingInfo->price ?? 0;
  $tax = $bookingInfo->tax ?? 0;
  $earlyBirdDiscount = $bookingInfo->early_bird_discount ?? 0;
  $couponDiscount = $bookingInfo->discount ?? 0;
  $totalDiscount = $earlyBirdDiscount + $couponDiscount;
  $total = $subtotal + $tax - $totalDiscount;
  
  // Verificar logo MercadoPago
  $mpLogoPath = public_path('assets/front/images/mercadopago_logo.svg');
  $mpLogoExists = file_exists($mpLogoPath);
@endphp
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Comprobante de Entrada — {{ $eventInfo->title ?? config('app.name') }}</title>
  <style>
    @page { 
      size: A4; 
      margin: 20mm 15mm;
    }
    
    * { 
      box-sizing: border-box; 
      margin: 0; 
      padding: 0; 
    }
    
    body { 
      font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
      font-size: 11px;
      line-height: 1.4;
      color: #1a1a1a;
      background: #ffffff;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    /* Contenedor principal */
    .ticket-wrapper {
      width: 100%;
      max-width: 100%;
      margin: 0 auto;
    }
    
    .ticket {
      width: 100%;
      border: 1px solid #e0e0e0;
      border-radius: 8px;
      overflow: hidden;
      background: #ffffff;
      page-break-inside: avoid;
      margin-bottom: 20px;
    }

    /* Header */
    .ticket-header {
      background: linear-gradient(135deg, #1e2532 0%, #2d3748 100%);
      color: #ffffff;
      padding: 24px 20px;
      position: relative;
    }
    
    .ticket-header-top {
      display: table;
      width: 100%;
      margin-bottom: 16px;
    }
    
    .ticket-brand {
      display: table-cell;
      font-size: 11px;
      font-weight: 700;
      color: rgba(255,255,255,0.6);
      letter-spacing: 1px;
      text-transform: uppercase;
    }
    
    .ticket-status {
      display: table-cell;
      text-align: right;
    }
    
    .ticket-status-badge {
      display: inline-block;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .status-paid { background: #22c55e; color: #ffffff; }
    .status-free { background: #22c55e; color: #ffffff; }
    .status-pending { background: #f59e0b; color: #ffffff; }
    
    .ticket-event-title {
      font-size: 22px;
      font-weight: 700;
      margin-bottom: 8px;
      line-height: 1.3;
    }
    
    .ticket-event-meta {
      font-size: 12px;
      color: rgba(255,255,255,0.8);
    }
    
    .ticket-event-meta span {
      display: inline;
    }

    /* Body */
    .ticket-body {
      padding: 24px 20px;
    }

    /* Sección título */
    .section-title {
      font-size: 10px;
      font-weight: 700;
      color: {{ $primary }};
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 16px;
      padding-bottom: 8px;
      border-bottom: 2px solid {{ $primary }};
    }

    /* Layout de 2 columnas con tabla */
    .info-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 24px;
    }
    
    .info-table td {
      vertical-align: top;
      padding: 0;
    }
    
    .info-table td.info-col {
      width: 65%;
      padding-right: 20px;
    }
    
    .info-table td.qr-col {
      width: 35%;
      text-align: center;
      background: #f8f9fa;
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      padding: 16px;
    }

    /* Campos de información */
    .info-row {
      margin-bottom: 12px;
    }
    
    .info-label {
      font-size: 9px;
      font-weight: 700;
      color: #6b7280;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 2px;
    }
    
    .info-value {
      font-size: 12px;
      font-weight: 600;
      color: #1a1a1a;
    }

    /* QR */
    .qr-container img {
      width: 100px;
      height: 100px;
      margin-bottom: 8px;
    }
    
    .qr-label {
      font-size: 10px;
      font-weight: 600;
      color: #374151;
    }
    
    .qr-note {
      font-size: 8px;
      color: #6b7280;
      margin-top: 4px;
    }

    /* Información de pago */
    .billing-section {
      background: #f8f9fa;
      border-radius: 8px;
      padding: 20px;
      margin-bottom: 20px;
    }
    
    .billing-table {
      width: 100%;
      border-collapse: collapse;
    }
    
    .billing-table td {
      padding: 6px 0;
      border-bottom: 1px solid #e5e7eb;
    }
    
    .billing-table td:first-child {
      text-align: left;
      color: #6b7280;
      font-size: 11px;
    }
    
    .billing-table td:last-child {
      text-align: right;
      font-weight: 600;
      font-size: 11px;
      color: #1a1a1a;
    }
    
    .billing-table tr.discount td:last-child {
      color: #16a34a;
    }
    
    .billing-table tr.total {
      background: #1e2532;
    }
    
    .billing-table tr.total td {
      padding: 12px 0;
      border-bottom: none;
    }
    
    .billing-table tr.total td:first-child {
      color: rgba(255,255,255,0.8);
      font-weight: 700;
      padding-left: 12px;
    }
    
    .billing-table tr.total td:last-child {
      color: #ffffff;
      font-weight: 700;
      font-size: 16px;
      padding-right: 12px;
    }

    /* Método de pago */
    .payment-method-section {
      margin-bottom: 20px;
    }
    
    .payment-method-box {
      display: inline-block;
      padding: 8px 16px;
      background: #ffffff;
      border: 1px solid #e5e7eb;
      border-radius: 6px;
    }
    
    .mp-logo {
      height: 24px;
      vertical-align: middle;
    }
    
    .mp-text {
      font-weight: 700;
      color: #009EE3;
      font-size: 14px;
    }

    /* Instrucciones */
    .instructions-section {
      background: #fffbeb;
      border-left: 4px solid {{ $primary }};
      padding: 16px;
      margin-bottom: 20px;
      border-radius: 0 8px 8px 0;
    }
    
    .instructions-title {
      font-size: 11px;
      font-weight: 700;
      color: #1a1a1a;
      margin-bottom: 10px;
    }
    
    .instructions-list {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    
    .instructions-list li {
      font-size: 10px;
      color: #4b5563;
      margin-bottom: 6px;
      padding-left: 12px;
      position: relative;
      line-height: 1.5;
    }
    
    .instructions-list li:before {
      content: "•";
      position: absolute;
      left: 0;
      color: {{ $primary }};
      font-weight: bold;
    }

    /* Footer */
    .ticket-footer {
      background: #1e2532;
      color: #ffffff;
      padding: 16px 20px;
      text-align: center;
    }
    
    .footer-booking-id {
      font-size: 14px;
      font-weight: 700;
      margin-bottom: 6px;
      letter-spacing: 1px;
    }
    
    .footer-brand {
      font-size: 11px;
      color: rgba(255,255,255,0.7);
      margin-bottom: 8px;
    }
    
    .footer-disclaimer {
      font-size: 9px;
      color: rgba(255,255,255,0.5);
      font-style: italic;
    }

    /* Page break */
    .page-break {
      page-break-after: always;
      height: 20px;
    }

    @media print {
      body { background: #ffffff; }
      .ticket { border: 1px solid #ccc; }
    }
  </style>
</head>
<body>

{{-- TICKET POR VARIACIÓN --}}
@if ($tickets)
  @foreach ($tickets as $idx => $variation)
    @php
      $ticket_content = App\Models\Event\TicketContent::where([
        ['ticket_id', $variation['ticket_id']],
        ['language_id', $language->id],
      ])->first();
      $ticket  = App\Models\Event\Ticket::where('id', $variation['ticket_id'])->select('pricing_type')->first();
      $qrPath  = public_path('assets/admin/qrcodes/' . $bookingInfo->booking_id . '__' . $variation['unique_id'] . '.svg');
      $isPaid  = in_array($bookingInfo->paymentStatus, ['completed', 'paid']);
      $isFree  = $bookingInfo->paymentStatus == 'free';
      $isPending = $bookingInfo->paymentStatus == 'pending';
    @endphp

    <div class="ticket-wrapper">
      <div class="ticket">
        
        {{-- Header --}}
        <div class="ticket-header">
          <div class="ticket-header-top">
            <div class="ticket-brand">{{ config('app.name') }}</div>
            <div class="ticket-status">
              @if($isFree)
                <span class="ticket-status-badge status-free">Reserva Gratuita</span>
              @elseif($isPaid)
                <span class="ticket-status-badge status-paid">Pago Confirmado</span>
              @elseif($isPending)
                <span class="ticket-status-badge status-pending">Pendiente</span>
              @else
                <span class="ticket-status-badge">{{ ucfirst($bookingInfo->paymentStatus) }}</span>
              @endif
            </div>
          </div>
          <div class="ticket-event-title">{{ $eventInfo->title ?? '' }}</div>
          <div class="ticket-event-meta">
            <span>{{ ucfirst($eventDate) }}</span>
            @if($location)
              <span> &nbsp;|&nbsp; {{ $location }}</span>
            @endif
          </div>
        </div>

        {{-- Body --}}
        <div class="ticket-body">
          
          {{-- Datos de la reserva + QR --}}
          <div class="section-title">Datos de la Reserva</div>
          <table class="info-table">
            <tr>
              <td class="info-col">
                <div class="info-row">
                  <div class="info-label">Fecha de Reserva</div>
                  <div class="info-value">{{ date_format($bookingInfo->created_at, 'd/m/Y') }}</div>
                </div>
                <div class="info-row">
                  <div class="info-label">Número de Reserva</div>
                  <div class="info-value">#{{ $bookingInfo->booking_id }}</div>
                </div>
                <div class="info-row">
                  <div class="info-label">Titular</div>
                  <div class="info-value">{{ $bookingInfo->fname }} {{ $bookingInfo->lname }}</div>
                </div>
                <div class="info-row">
                  <div class="info-label">Correo Electrónico</div>
                  <div class="info-value">{{ $bookingInfo->email }}</div>
                </div>
                @if ($address)
                <div class="info-row">
                  <div class="info-label">Dirección</div>
                  <div class="info-value">{{ $address }}</div>
                </div>
                @endif
                @if ($ticket_content && $ticket && $ticket->pricing_type == 'variation')
                <div class="info-row">
                  <div class="info-label">Tipo de Entrada</div>
                  <div class="info-value">{{ $ticket_content->title }} — {{ $variation['name'] }}</div>
                </div>
                @endif
                <div class="info-row">
                  <div class="info-label">Entrada</div>
                  <div class="info-value">{{ $idx + 1 }} de {{ $ticketCount }}</div>
                </div>
              </td>
              <td class="qr-col">
                <div class="qr-container">
                  @if (file_exists($qrPath))
                    <img src="{{ $qrPath }}" alt="Código QR de Acceso">
                  @else
                    <div style="width:100px;height:100px;background:#e5e7eb;border-radius:4px;margin:0 auto;"></div>
                  @endif
                  <div class="qr-label">Entrada {{ $idx + 1 }} / {{ $ticketCount }}</div>
                  <div class="qr-note">Presentá este código al ingresar</div>
                </div>
              </td>
            </tr>
          </table>

          {{-- Información de Pago --}}
          <div class="section-title">Detalle de Pago</div>
          <div class="billing-section">
            <table class="billing-table">
              @if ($subtotal > 0)
              <tr>
                <td>Valor Unitario</td>
                <td>{{ formatMoney($unitPrice, $position, $currency) }}</td>
              </tr>
              <tr>
                <td>Cantidad</td>
                <td>x {{ $quantity }}</td>
              </tr>
              <tr>
                <td>Subtotal</td>
                <td>{{ formatMoney($subtotal, $position, $currency) }}</td>
              </tr>
              @endif
              @if ($tax > 0)
              <tr>
                <td>Impuestos</td>
                <td>{{ formatMoney($tax, $position, $currency) }}</td>
              </tr>
              @endif
              @if ($earlyBirdDiscount > 0)
              <tr class="discount">
                <td>Descuento Anticipado</td>
                <td>− {{ formatMoney($earlyBirdDiscount, $position, $currency) }}</td>
              </tr>
              @endif
              @if ($couponDiscount > 0)
              <tr class="discount">
                <td>Cupón de Descuento</td>
                <td>− {{ formatMoney($couponDiscount, $position, $currency) }}</td>
              </tr>
              @endif
              <tr class="total">
                <td>TOTAL PAGADO</td>
                <td>
                  @if($isFree) GRATIS
                  @else {{ formatMoney($total, $position, $currency) }}
                  @endif
                </td>
              </tr>
            </table>
          </div>

          {{-- Método de Pago --}}
          <div class="section-title">Método de Pago</div>
          <div class="payment-method-section">
            <div class="payment-method-box">
              @if(strtolower($bookingInfo->paymentMethod ?? '') == 'mercadopago' || strtolower($bookingInfo->paymentMethod ?? '') == 'mercado pago')
                @if($mpLogoExists)
                  <img src="{{ $mpLogoPath }}" alt="Mercado Pago" class="mp-logo">
                @else
                  <span class="mp-text">Mercado Pago</span>
                @endif
              @else
                <span style="font-weight:600;">{{ $bookingInfo->paymentMethod ?? 'No especificado' }}</span>
              @endif
            </div>
            <span style="margin-left:12px;font-size:10px;color:#6b7280;">
              @if($isFree) Reserva gratuita
              @elseif($isPaid) Pago confirmado
              @elseif($isPending) Pendiente de confirmación
              @else {{ ucfirst($bookingInfo->paymentStatus) }}
              @endif
            </span>
          </div>

          {{-- Instrucciones --}}
          <div class="instructions-section">
            <div class="instructions-title">Instrucciones Importantes</div>
            <ul class="instructions-list">
              <li>Somos {{ config('app.name') }}, una plataforma de venta de entradas. No somos los organizadores del evento, por lo que no nos hacemos responsables de las condiciones del establecimiento, reprogramaciones o cancelaciones.</li>
              <li>Ante cualquier eventualidad, deberás contactar directamente a la empresa organizadora del evento.</li>
              <li>Si adquirís tu entrada en puntos de venta no autorizados, la responsabilidad es tuya. La entrada podría ser falsa o adulterada.</li>
              <li>No compartas ni reveles tu código QR con terceros. Conservá esta entrada en un lugar seguro.</li>
              <li><strong>IMPORTANTE:</strong> Al llegar al evento, presentá esta entrada (impresa o digital) junto con tu documento de identidad.</li>
              <li>Si contrataste un seguro, el certificado será enviado por {{ config('app.name') }} al correo electrónico indicado.</li>
            </ul>
          </div>

        </div>

        {{-- Footer --}}
        <div class="ticket-footer">
          <div class="footer-booking-id">#{{ $bookingInfo->booking_id }}</div>
          <div class="footer-brand">{{ config('app.name') }} · Gracias por tu compra</div>
          <div class="footer-disclaimer">Este comprobante es interno y no reemplaza una factura fiscal válida.</div>
        </div>

      </div>
    </div>

    @if (!$loop->last)
      <div class="page-break"></div>
    @endif
  @endforeach

{{-- TICKET NORMAL --}}
@else
  @for ($i = 1; $i <= $bookingInfo->quantity; $i++)
    @php
      $qrPath   = public_path('assets/admin/qrcodes/' . $bookingInfo->booking_id . '__' . $i . '.svg');
      $isPaid   = in_array($bookingInfo->paymentStatus, ['completed', 'paid']);
      $isFree   = $bookingInfo->paymentStatus == 'free';
      $isPending = $bookingInfo->paymentStatus == 'pending';
    @endphp

    <div class="ticket-wrapper">
      <div class="ticket">
        
        {{-- Header --}}
        <div class="ticket-header">
          <div class="ticket-header-top">
            <div class="ticket-brand">{{ config('app.name') }}</div>
            <div class="ticket-status">
              @if($isFree)
                <span class="ticket-status-badge status-free">Reserva Gratuita</span>
              @elseif($isPaid)
                <span class="ticket-status-badge status-paid">Pago Confirmado</span>
              @elseif($isPending)
                <span class="ticket-status-badge status-pending">Pendiente</span>
              @else
                <span class="ticket-status-badge">{{ ucfirst($bookingInfo->paymentStatus) }}</span>
              @endif
            </div>
          </div>
          <div class="ticket-event-title">{{ $eventInfo->title ?? '' }}</div>
          <div class="ticket-event-meta">
            <span>{{ ucfirst($eventDate) }}</span>
            @if($location)
              <span> &nbsp;|&nbsp; {{ $location }}</span>
            @endif
          </div>
        </div>

        {{-- Body --}}
        <div class="ticket-body">
          
          {{-- Datos de la reserva + QR --}}
          <div class="section-title">Datos de la Reserva</div>
          <table class="info-table">
            <tr>
              <td class="info-col">
                <div class="info-row">
                  <div class="info-label">Fecha de Reserva</div>
                  <div class="info-value">{{ date_format($bookingInfo->created_at, 'd/m/Y') }}</div>
                </div>
                <div class="info-row">
                  <div class="info-label">Número de Reserva</div>
                  <div class="info-value">#{{ $bookingInfo->booking_id }}</div>
                </div>
                <div class="info-row">
                  <div class="info-label">Titular</div>
                  <div class="info-value">{{ $bookingInfo->fname }} {{ $bookingInfo->lname }}</div>
                </div>
                <div class="info-row">
                  <div class="info-label">Correo Electrónico</div>
                  <div class="info-value">{{ $bookingInfo->email }}</div>
                </div>
                @if ($address)
                <div class="info-row">
                  <div class="info-label">Dirección</div>
                  <div class="info-value">{{ $address }}</div>
                </div>
                @endif
                <div class="info-row">
                  <div class="info-label">Entrada</div>
                  <div class="info-value">{{ $i }} de {{ $bookingInfo->quantity }}</div>
                </div>
              </td>
              <td class="qr-col">
                <div class="qr-container">
                  @if (file_exists($qrPath))
                    <img src="{{ $qrPath }}" alt="Código QR de Acceso">
                  @else
                    <div style="width:100px;height:100px;background:#e5e7eb;border-radius:4px;margin:0 auto;"></div>
                  @endif
                  <div class="qr-label">Entrada {{ $i }} / {{ $bookingInfo->quantity }}</div>
                  <div class="qr-note">Presentá este código al ingresar</div>
                </div>
              </td>
            </tr>
          </table>

          {{-- Información de Pago --}}
          <div class="section-title">Detalle de Pago</div>
          <div class="billing-section">
            <table class="billing-table">
              @if ($subtotal > 0)
              <tr>
                <td>Valor Unitario</td>
                <td>{{ formatMoney($unitPrice, $position, $currency) }}</td>
              </tr>
              <tr>
                <td>Cantidad</td>
                <td>x {{ $quantity }}</td>
              </tr>
              <tr>
                <td>Subtotal</td>
                <td>{{ formatMoney($subtotal, $position, $currency) }}</td>
              </tr>
              @endif
              @if ($tax > 0)
              <tr>
                <td>Impuestos</td>
                <td>{{ formatMoney($tax, $position, $currency) }}</td>
              </tr>
              @endif
              @if ($earlyBirdDiscount > 0)
              <tr class="discount">
                <td>Descuento Anticipado</td>
                <td>− {{ formatMoney($earlyBirdDiscount, $position, $currency) }}</td>
              </tr>
              @endif
              @if ($couponDiscount > 0)
              <tr class="discount">
                <td>Cupón de Descuento</td>
                <td>− {{ formatMoney($couponDiscount, $position, $currency) }}</td>
              </tr>
              @endif
              <tr class="total">
                <td>TOTAL PAGADO</td>
                <td>
                  @if($isFree) GRATIS
                  @else {{ formatMoney($total, $position, $currency) }}
                  @endif
                </td>
              </tr>
            </table>
          </div>

          {{-- Método de Pago --}}
          <div class="section-title">Método de Pago</div>
          <div class="payment-method-section">
            <div class="payment-method-box">
              @if(strtolower($bookingInfo->paymentMethod ?? '') == 'mercadopago' || strtolower($bookingInfo->paymentMethod ?? '') == 'mercado pago')
                @if($mpLogoExists)
                  <img src="{{ $mpLogoPath }}" alt="Mercado Pago" class="mp-logo">
                @else
                  <span class="mp-text">Mercado Pago</span>
                @endif
              @else
                <span style="font-weight:600;">{{ $bookingInfo->paymentMethod ?? 'No especificado' }}</span>
              @endif
            </div>
            <span style="margin-left:12px;font-size:10px;color:#6b7280;">
              @if($isFree) Reserva gratuita
              @elseif($isPaid) Pago confirmado
              @elseif($isPending) Pendiente de confirmación
              @else {{ ucfirst($bookingInfo->paymentStatus) }}
              @endif
            </span>
          </div>

          {{-- Instrucciones --}}
          <div class="instructions-section">
            <div class="instructions-title">Instrucciones Importantes</div>
            <ul class="instructions-list">
              <li>Somos {{ config('app.name') }}, una plataforma de venta de entradas. No somos los organizadores del evento, por lo que no nos hacemos responsables de las condiciones del establecimiento, reprogramaciones o cancelaciones.</li>
              <li>Ante cualquier eventualidad, deberás contactar directamente a la empresa organizadora del evento.</li>
              <li>Si adquirís tu entrada en puntos de venta no autorizados, la responsabilidad es tuya. La entrada podría ser falsa o adulterada.</li>
              <li>No compartas ni reveles tu código QR con terceros. Conservá esta entrada en un lugar seguro.</li>
              <li><strong>IMPORTANTE:</strong> Al llegar al evento, presentá esta entrada (impresa o digital) junto con tu documento de identidad.</li>
              <li>Si contrataste un seguro, el certificado será enviado por {{ config('app.name') }} al correo electrónico indicado.</li>
            </ul>
          </div>

        </div>

        {{-- Footer --}}
        <div class="ticket-footer">
          <div class="footer-booking-id">#{{ $bookingInfo->booking_id }}</div>
          <div class="footer-brand">{{ config('app.name') }} · Gracias por tu compra</div>
          <div class="footer-disclaimer">Este comprobante es interno y no reemplaza una factura fiscal válida.</div>
        </div>

      </div>
    </div>

    @if ($i < $bookingInfo->quantity)
      <div class="page-break"></div>
    @endif
  @endfor
@endif

</body>
</html>
