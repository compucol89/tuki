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
  $eventTime  = \Carbon\Carbon::parse($bookingInfo->event_date)->format('H:i');

  $locationParts = array_filter([
    cleanLocationPart($bookingInfo->city ?? null),
    cleanLocationPart($bookingInfo->state ?? null),
  ]);
  $location = implode(', ', $locationParts);
  
  $address = cleanLocationPart($bookingInfo->address ?? null);

  $quantity = $bookingInfo->quantity ?? 1;
  $unitPrice = ($quantity > 0) ? ($bookingInfo->price / $quantity) : 0;
  $subtotal = $bookingInfo->price ?? 0;
  $tax = $bookingInfo->tax ?? 0;
  $discount = ($bookingInfo->early_bird_discount ?? 0) + ($bookingInfo->discount ?? 0);
  $total = $subtotal + $tax - $discount;
  
  $mpLogoPath = public_path('assets/front/images/mercadopago_logo.svg');
  $mpLogoExists = file_exists($mpLogoPath);
  
  $tukiLogoPath = public_path('assets/front/images/logos/logo.png');
  $tukiLogoExists = file_exists($tukiLogoPath);
@endphp
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Entrada — {{ $eventInfo->title ?? config('app.name') }}</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    @page { 
      size: A4; 
      margin: 15mm 10mm;
    }
    
    * { box-sizing: border-box; margin: 0; padding: 0; }
    
    body { 
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      background: #ffffff;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
      padding-top: 15px;
    }

    .ticket-container {
      width: 100%;
      max-width: 420px;
      margin: 0 auto;
      padding-top: 20px;
    }

    .ticket {
      background: #ffffff;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 8px 30px rgba(0,0,0,0.12);
      page-break-inside: avoid;
      margin-bottom: 20px;
    }

    /* Header con gradiente naranja */
    .ticket-header {
      background: linear-gradient(135deg, #F97316 0%, #EA580C 50%, #C2410C 100%);
      color: #ffffff;
      padding: 30px 20px 25px;
      text-align: center;
      position: relative;
    }

    .ticket-header::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
      pointer-events: none;
    }

    .event-type {
      font-size: 10px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 2px;
      opacity: 0.9;
      margin-bottom: 10px;
    }

    .event-title {
      font-size: 22px;
      font-weight: 800;
      line-height: 1.2;
      margin-bottom: 8px;
    }

    .event-date {
      font-size: 13px;
      font-weight: 500;
      opacity: 0.95;
    }

    .event-location {
      font-size: 12px;
      font-weight: 400;
      opacity: 0.85;
      margin-top: 5px;
    }

    /* Logo Section */
    .logo-section {
      background: linear-gradient(135deg, #F97316 0%, #EA580C 50%, #C2410C 100%);
      padding: 15px 20px;
      text-align: center;
    }

    .logo-container {
      background: #ffffff;
      border-radius: 12px;
      padding: 12px 25px;
      display: inline-block;
      box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }

    .logo-container img {
      height: 35px;
      display: block;
    }

    /* QR Section */
    .qr-section {
      background: linear-gradient(135deg, #F97316 0%, #EA580C 50%, #C2410C 100%);
      padding: 20px;
      text-align: center;
      position: relative;
    }

    .qr-container {
      background: #ffffff;
      border-radius: 16px;
      padding: 20px;
      display: inline-block;
      box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    }

    .qr-container img {
      width: 140px;
      height: 140px;
      display: block;
    }

    .qr-label {
      font-size: 10px;
      color: #666;
      margin-top: 10px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    /* Perforation line */
    .perforation {
      height: 14px;
      background: #ffffff;
      position: relative;
      overflow: hidden;
    }

    .perforation::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 20px;
      right: 20px;
      border-top: 2px dashed #ddd;
      transform: translateY(-50%);
    }

    .perforation-circle {
      position: absolute;
      top: 50%;
      width: 24px;
      height: 24px;
      background: #f5f5f5;
      border-radius: 50%;
      transform: translateY(-50%);
    }

    .perforation-circle.left { left: -12px; }
    .perforation-circle.right { right: -12px; }

    /* Ticket Details */
    .ticket-details {
      padding: 25px 20px;
      background: #ffffff;
    }

    .ticket-type {
      text-align: center;
      margin-bottom: 20px;
    }

    .ticket-type-label {
      font-size: 10px;
      color: #F97316;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .ticket-type-name {
      font-size: 20px;
      font-weight: 700;
      color: #1a1a1a;
      margin-top: 5px;
    }

    /* Info Grid */
    .info-grid {
      display: table;
      width: 100%;
      margin-bottom: 20px;
    }

    .info-row-table {
      display: table-row;
    }

    .info-cell {
      display: table-cell;
      width: 50%;
      padding: 10px 5px;
      border-bottom: 1px solid #f0f0f0;
    }

    .info-cell.full {
      width: 100%;
    }

    .info-label-small {
      font-size: 9px;
      color: #999;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 3px;
      font-weight: 500;
    }

    .info-value-small {
      font-size: 12px;
      font-weight: 600;
      color: #333;
    }

    /* Payment Summary */
    .payment-section {
      background: #fafafa;
      border-radius: 12px;
      padding: 18px;
      margin-bottom: 20px;
    }

    .payment-title {
      font-size: 10px;
      color: #F97316;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 12px;
      text-align: center;
    }

    .payment-row {
      display: table;
      width: 100%;
      margin-bottom: 6px;
    }

    .payment-label {
      display: table-cell;
      font-size: 11px;
      color: #666;
      font-weight: 400;
    }

    .payment-value {
      display: table-cell;
      text-align: right;
      font-size: 11px;
      font-weight: 600;
      color: #333;
    }

    .payment-value.discount {
      color: #16a34a;
    }

    .payment-total {
      display: table;
      width: 100%;
      margin-top: 12px;
      padding-top: 12px;
      border-top: 2px solid #F97316;
    }

    .payment-total-label {
      display: table-cell;
      font-size: 12px;
      font-weight: 700;
      color: #1a1a1a;
    }

    .payment-total-value {
      display: table-cell;
      text-align: right;
      font-size: 18px;
      font-weight: 800;
      color: #F97316;
    }

    /* Attendee */
    .attendee-section {
      text-align: center;
      padding: 18px 0;
      border-top: 1px dashed #ddd;
      border-bottom: 1px dashed #ddd;
      margin-bottom: 20px;
    }

    .attendee-label {
      font-size: 9px;
      color: #999;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 5px;
      font-weight: 500;
    }

    .attendee-name {
      font-size: 18px;
      font-weight: 700;
      color: #1a1a1a;
    }

    .attendee-email {
      font-size: 10px;
      color: #666;
      margin-top: 3px;
      font-weight: 400;
    }

    /* Footer */
    .ticket-footer {
      background: #1a1a1a;
      color: #ffffff;
      padding: 20px;
      text-align: center;
    }

    .footer-code {
      font-size: 16px;
      font-weight: 800;
      letter-spacing: 2px;
      margin-bottom: 6px;
    }

    .footer-brand {
      font-size: 11px;
      color: rgba(255,255,255,0.7);
      font-weight: 500;
    }

    .footer-disclaimer {
      font-size: 9px;
      color: rgba(255,255,255,0.5);
      margin-top: 10px;
      font-style: italic;
    }

    /* Instructions */
    .instructions {
      background: #fffbeb;
      border-left: 3px solid #F97316;
      padding: 15px;
      margin: 0 20px 20px;
      border-radius: 0 10px 10px 0;
    }

    .instructions-title {
      font-size: 10px;
      font-weight: 700;
      color: #1a1a1a;
      margin-bottom: 10px;
    }

    .instructions ul {
      margin: 0;
      padding-left: 15px;
    }

    .instructions li {
      font-size: 9px;
      color: #555;
      margin-bottom: 5px;
      line-height: 1.5;
    }

    /* Page break */
    .page-break {
      page-break-after: always;
      height: 20px;
    }

    @media print {
      body { background: #ffffff; padding-top: 10px; }
      .ticket { box-shadow: none; }
    }
  </style>
</head>
<body>

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
    @endphp

    <div class="ticket-container">
      <div class="ticket">
        
        <!-- Header -->
        <div class="ticket-header">
          <div class="event-type">Entrada para</div>
          <div class="event-title">{{ $eventInfo->title ?? '' }}</div>
          <div class="event-date">{{ ucfirst($eventDate) }} · {{ $eventTime }} hs</div>
          @if($location)
            <div class="event-location">{{ $location }}</div>
          @endif
        </div>

        <!-- Logo Section -->
        <div class="logo-section">
          <div class="logo-container">
            @if($tukiLogoExists)
              <img src="{{ $tukiLogoPath }}" alt="TukiPass">
            @else
              <span style="font-size:24px;font-weight:800;color:#F97316;">TUKIPASS</span>
            @endif
          </div>
        </div>

        <!-- QR Section -->
        <div class="qr-section">
          <div class="qr-container">
            @if (file_exists($qrPath))
              <img src="{{ $qrPath }}" alt="QR">
            @else
              <div style="width:140px;height:140px;background:#f0f0f0;"></div>
            @endif
            <div class="qr-label">Entrada {{ $idx + 1 }} / {{ $ticketCount }}</div>
          </div>
        </div>

        <!-- Perforation -->
        <div class="perforation">
          <div class="perforation-circle left"></div>
          <div class="perforation-circle right"></div>
        </div>

        <!-- Ticket Details -->
        <div class="ticket-details">
          
          <!-- Ticket Type -->
          <div class="ticket-type">
            <div class="ticket-type-label">Tipo de Entrada</div>
            <div class="ticket-type-name">
              @if ($ticket_content && $ticket && $ticket->pricing_type == 'variation')
                {{ $ticket_content->title }} — {{ $variation['name'] }}
              @else
                Entrada General
              @endif
            </div>
          </div>

          <!-- Attendee -->
          <div class="attendee-section">
            <div class="attendee-label">Titular</div>
            <div class="attendee-name">{{ $bookingInfo->fname }} {{ $bookingInfo->lname }}</div>
            <div class="attendee-email">{{ $bookingInfo->email }}</div>
          </div>

          <!-- Info Grid -->
          <div class="info-grid">
            <div class="info-row-table">
              <div class="info-cell">
                <div class="info-label-small">Nº de Reserva</div>
                <div class="info-value-small">#{{ $bookingInfo->booking_id }}</div>
              </div>
              <div class="info-cell">
                <div class="info-label-small">Fecha de Reserva</div>
                <div class="info-value-small">{{ date_format($bookingInfo->created_at, 'd/m/Y') }}</div>
              </div>
            </div>
            @if ($address)
            <div class="info-row-table">
              <div class="info-cell full">
                <div class="info-label-small">Dirección del Evento</div>
                <div class="info-value-small">{{ $address }}</div>
              </div>
            </div>
            @endif
          </div>

          <!-- Payment -->
          @if (!$isFree)
          <div class="payment-section">
            <div class="payment-title">Detalle de Pago</div>
            @if ($subtotal > 0)
            <div class="payment-row">
              <span class="payment-label">{{ $quantity }} x {{ formatMoney($unitPrice, $position, $currency) }}</span>
              <span class="payment-value">{{ formatMoney($subtotal, $position, $currency) }}</span>
            </div>
            @endif
            @if ($tax > 0)
            <div class="payment-row">
              <span class="payment-label">Impuestos</span>
              <span class="payment-value">{{ formatMoney($tax, $position, $currency) }}</span>
            </div>
            @endif
            @if ($discount > 0)
            <div class="payment-row">
              <span class="payment-label">Descuentos</span>
              <span class="payment-value discount">− {{ formatMoney($discount, $position, $currency) }}</span>
            </div>
            @endif
            <div class="payment-total">
              <span class="payment-total-label">TOTAL</span>
              <span class="payment-total-value">{{ formatMoney($total, $position, $currency) }}</span>
            </div>
          </div>
          @else
          <div class="payment-section" style="text-align:center;">
            <div class="payment-title">Entrada Gratuita</div>
          </div>
          @endif

          <!-- Payment Method -->
          <div style="text-align:center;margin-bottom:5px;">
            <div class="info-label-small" style="margin-bottom:6px;">Método de Pago</div>
            @if(strtolower($bookingInfo->paymentMethod ?? '') == 'mercadopago' || strtolower($bookingInfo->paymentMethod ?? '') == 'mercado pago')
              @if($mpLogoExists)
                <img src="{{ $mpLogoPath }}" height="22" alt="Mercado Pago">
              @else
                <span style="font-weight:700;color:#009EE3;font-size:14px;">Mercado Pago</span>
              @endif
            @else
              <span style="font-weight:600;font-size:14px;">{{ $bookingInfo->paymentMethod ?? 'No especificado' }}</span>
            @endif
          </div>

        </div>

        <!-- Instructions -->
        <div class="instructions">
          <div class="instructions-title">Instrucciones Importantes</div>
          <ul>
            <li>Presentá esta entrada junto con tu DNI al ingresar al evento.</li>
            <li>No compartas el código QR con terceros. Es único e intransferible.</li>
            <li>La entrada es válida para una sola persona.</li>
            <li>{{ config('app.name') }} es plataforma de venta; el organizador es responsable del evento.</li>
          </ul>
        </div>

        <!-- Footer -->
        <div class="ticket-footer">
          <div class="footer-code">#{{ $bookingInfo->booking_id }}</div>
          <div class="footer-brand">{{ config('app.name') }} · Gracias por tu compra</div>
          <div class="footer-disclaimer">Comprobante interno - No es factura fiscal</div>
        </div>

      </div>
    </div>

    @if (!$loop->last)
      <div class="page-break"></div>
    @endif
  @endforeach

@else
  @for ($i = 1; $i <= $bookingInfo->quantity; $i++)
    @php
      $qrPath   = public_path('assets/admin/qrcodes/' . $bookingInfo->booking_id . '__' . $i . '.svg');
      $isPaid   = in_array($bookingInfo->paymentStatus, ['completed', 'paid']);
      $isFree   = $bookingInfo->paymentStatus == 'free';
    @endphp

    <div class="ticket-container">
      <div class="ticket">
        
        <!-- Header -->
        <div class="ticket-header">
          <div class="event-type">Entrada para</div>
          <div class="event-title">{{ $eventInfo->title ?? '' }}</div>
          <div class="event-date">{{ ucfirst($eventDate) }} · {{ $eventTime }} hs</div>
          @if($location)
            <div class="event-location">{{ $location }}</div>
          @endif
        </div>

        <!-- Logo Section -->
        <div class="logo-section">
          <div class="logo-container">
            @if($tukiLogoExists)
              <img src="{{ $tukiLogoPath }}" alt="TukiPass">
            @else
              <span style="font-size:24px;font-weight:800;color:#F97316;">TUKIPASS</span>
            @endif
          </div>
        </div>

        <!-- QR Section -->
        <div class="qr-section">
          <div class="qr-container">
            @if (file_exists($qrPath))
              <img src="{{ $qrPath }}" alt="QR">
            @else
              <div style="width:140px;height:140px;background:#f0f0f0;"></div>
            @endif
            <div class="qr-label">Entrada {{ $i }} / {{ $bookingInfo->quantity }}</div>
          </div>
        </div>

        <!-- Perforation -->
        <div class="perforation">
          <div class="perforation-circle left"></div>
          <div class="perforation-circle right"></div>
        </div>

        <!-- Ticket Details -->
        <div class="ticket-details">
          
          <!-- Ticket Type -->
          <div class="ticket-type">
            <div class="ticket-type-label">Tipo de Entrada</div>
            <div class="ticket-type-name">Entrada General</div>
          </div>

          <!-- Attendee -->
          <div class="attendee-section">
            <div class="attendee-label">Titular</div>
            <div class="attendee-name">{{ $bookingInfo->fname }} {{ $bookingInfo->lname }}</div>
            <div class="attendee-email">{{ $bookingInfo->email }}</div>
          </div>

          <!-- Info Grid -->
          <div class="info-grid">
            <div class="info-row-table">
              <div class="info-cell">
                <div class="info-label-small">Nº de Reserva</div>
                <div class="info-value-small">#{{ $bookingInfo->booking_id }}</div>
              </div>
              <div class="info-cell">
                <div class="info-label-small">Fecha de Reserva</div>
                <div class="info-value-small">{{ date_format($bookingInfo->created_at, 'd/m/Y') }}</div>
              </div>
            </div>
            @if ($address)
            <div class="info-row-table">
              <div class="info-cell full">
                <div class="info-label-small">Dirección del Evento</div>
                <div class="info-value-small">{{ $address }}</div>
              </div>
            </div>
            @endif
          </div>

          <!-- Payment -->
          @if (!$isFree)
          <div class="payment-section">
            <div class="payment-title">Detalle de Pago</div>
            @if ($subtotal > 0)
            <div class="payment-row">
              <span class="payment-label">{{ $quantity }} x {{ formatMoney($unitPrice, $position, $currency) }}</span>
              <span class="payment-value">{{ formatMoney($subtotal, $position, $currency) }}</span>
            </div>
            @endif
            @if ($tax > 0)
            <div class="payment-row">
              <span class="payment-label">Impuestos</span>
              <span class="payment-value">{{ formatMoney($tax, $position, $currency) }}</span>
            </div>
            @endif
            @if ($discount > 0)
            <div class="payment-row">
              <span class="payment-label">Descuentos</span>
              <span class="payment-value discount">− {{ formatMoney($discount, $position, $currency) }}</span>
            </div>
            @endif
            <div class="payment-total">
              <span class="payment-total-label">TOTAL</span>
              <span class="payment-total-value">{{ formatMoney($total, $position, $currency) }}</span>
            </div>
          </div>
          @else
          <div class="payment-section" style="text-align:center;">
            <div class="payment-title">Entrada Gratuita</div>
          </div>
          @endif

          <!-- Payment Method -->
          <div style="text-align:center;margin-bottom:5px;">
            <div class="info-label-small" style="margin-bottom:6px;">Método de Pago</div>
            @if(strtolower($bookingInfo->paymentMethod ?? '') == 'mercadopago' || strtolower($bookingInfo->paymentMethod ?? '') == 'mercado pago')
              @if($mpLogoExists)
                <img src="{{ $mpLogoPath }}" height="22" alt="Mercado Pago">
              @else
                <span style="font-weight:700;color:#009EE3;font-size:14px;">Mercado Pago</span>
              @endif
            @else
              <span style="font-weight:600;font-size:14px;">{{ $bookingInfo->paymentMethod ?? 'No especificado' }}</span>
            @endif
          </div>

        </div>

        <!-- Instructions -->
        <div class="instructions">
          <div class="instructions-title">Instrucciones Importantes</div>
          <ul>
            <li>Presentá esta entrada junto con tu DNI al ingresar al evento.</li>
            <li>No compartas el código QR con terceros. Es único e intransferible.</li>
            <li>La entrada es válida para una sola persona.</li>
            <li>{{ config('app.name') }} es plataforma de venta; el organizador es responsable del evento.</li>
          </ul>
        </div>

        <!-- Footer -->
        <div class="ticket-footer">
          <div class="footer-code">#{{ $bookingInfo->booking_id }}</div>
          <div class="footer-brand">{{ config('app.name') }} · Gracias por tu compra</div>
          <div class="footer-disclaimer">Comprobante interno - No es factura fiscal</div>
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
