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
  
  // Logo del admin
  $tukiLogoPath = public_path('assets/admin/img/logo.png');
  $tukiLogoExists = file_exists($tukiLogoPath);
@endphp
<head>
  <meta charset="UTF-8">
  <title>Entrada — {{ $eventInfo->title ?? config('app.name') }}</title>
  <style>
    @page { 
      size: A4; 
      margin: 55mm 20mm 30mm 20mm;
    }
    
    * { box-sizing: border-box; margin: 0; padding: 0; }
    
    body { 
      font-family: Helvetica, Arial, sans-serif;
      font-size: 11px;
      line-height: 1.4;
      color: #1a1a1a;
      background: #ffffff;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    .ticket-container {
      width: 100%;
      max-width: 400px;
      margin: 0 auto;
      padding-top: 0;
    }

    /* Ticket con forma de ticket real */
    .ticket {
      background: #ffffff;
      border: 2px solid #F97316;
      border-radius: 20px;
      overflow: hidden;
      page-break-inside: avoid;
      position: relative;
      box-shadow: 0 4px 15px rgba(15, 23, 42, 0.08);
    }

    /* Perforaciones circulares en los bordes */
    .ticket::before,
    .ticket::after {
      content: '';
      position: absolute;
      width: 28px;
      height: 28px;
      background: #ffffff;
      border-radius: 50%;
      top: 50%;
      transform: translateY(-50%);
      z-index: 10;
    }

    .ticket::before {
      left: -14px;
    }

    .ticket::after {
      right: -14px;
    }

    /* Header */
    .ticket-header {
      background: linear-gradient(135deg, #F97316 0%, #EA580C 100%);
      color: #ffffff;
      padding: 20px 15px 15px;
      text-align: center;
    }

    .logo-container {
      margin-bottom: 12px;
    }

    .logo-container img {
      height: 32px;
      filter: brightness(0) invert(1);
    }

    .event-title {
      font-size: 18px;
      font-weight: bold;
      line-height: 1.2;
      margin-bottom: 6px;
      color: #1a1a1a;
    }

    .event-date {
      font-size: 11px;
      font-weight: 600;
      opacity: 0.95;
    }

    .event-location {
      font-size: 10px;
      opacity: 0.85;
      margin-top: 4px;
    }

    /* QR Section */
    .qr-section {
      background: linear-gradient(135deg, #F97316 0%, #EA580C 100%);
      padding: 15px;
      text-align: center;
    }

    .qr-container {
      background: #ffffff;
      border-radius: 12px;
      padding: 12px;
      display: inline-block;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .qr-container img {
      width: 120px;
      height: 120px;
    }

    .qr-label {
      font-size: 9px;
      color: #666;
      margin-top: 6px;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    /* Ticket Details */
    .ticket-details {
      padding: 18px 20px;
      background: #ffffff;
    }

    .ticket-type {
      text-align: center;
      margin-bottom: 15px;
      padding-bottom: 12px;
      border-bottom: 1px dashed #e5e5e5;
    }

    .ticket-type-label {
      font-size: 9px;
      color: #F97316;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .ticket-type-name {
      font-size: 16px;
      font-weight: bold;
      color: #1a1a1a;
      margin-top: 4px;
    }

    /* Attendee */
    .attendee-section {
      text-align: center;
      padding: 12px 0;
      margin-bottom: 15px;
      border-bottom: 1px dashed #e5e5e5;
    }

    .attendee-label {
      font-size: 8px;
      color: #999;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 3px;
    }

    .attendee-name {
      font-size: 15px;
      font-weight: bold;
      color: #1a1a1a;
    }

    .attendee-email {
      font-size: 9px;
      color: #666;
      margin-top: 2px;
    }

    /* Info Grid - 3 columnas */
    .info-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 15px;
    }

    .info-table td {
      padding: 8px 4px;
      border-bottom: 1px solid #f0f0f0;
      width: 33.33%;
      vertical-align: top;
      text-align: center;
    }

    .info-label-small {
      font-size: 7px;
      color: #999;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 2px;
    }

    .info-value-small {
      font-size: 10px;
      font-weight: bold;
      color: #333;
    }

    .payment-method-cell .info-value-small {
      color: #009EE3;
    }

    .payment-method-cell img {
      height: 16px;
      margin-top: 2px;
    }

    /* Payment Summary */
    .payment-section {
      background: #fafafa;
      border: 1px solid #eee;
      border-radius: 10px;
      padding: 12px;
      margin-bottom: 15px;
    }

    .payment-title {
      font-size: 9px;
      color: #F97316;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 8px;
      text-align: center;
    }

    .payment-table {
      width: 100%;
      border-collapse: collapse;
    }

    .payment-table td {
      padding: 4px 0;
      font-size: 10px;
    }

    .payment-table td:first-child {
      color: #666;
    }

    .payment-table td:last-child {
      text-align: right;
      font-weight: bold;
      color: #333;
    }

    .payment-table .discount td:last-child {
      color: #16a34a;
    }

    .payment-total {
      margin-top: 8px;
      padding-top: 8px;
      border-top: 2px solid #F97316;
    }

    .payment-total td {
      font-weight: bold;
      font-size: 11px;
    }

    .payment-total td:last-child {
      font-size: 15px;
      color: #F97316;
    }

    /* Instructions */
    .instructions {
      background: #fffbeb;
      border-left: 3px solid #F97316;
      padding: 10px 12px;
      margin-bottom: 15px;
      border-radius: 0 8px 8px 0;
    }

    .instructions-title {
      font-size: 9px;
      font-weight: bold;
      color: #1a1a1a;
      margin-bottom: 6px;
    }

    .instructions ul {
      margin: 0;
      padding-left: 12px;
    }

    .instructions li {
      font-size: 8px;
      color: #555;
      margin-bottom: 3px;
      line-height: 1.4;
    }

    /* Footer */
    .ticket-footer {
      background: #1a1a1a;
      color: #ffffff;
      padding: 15px;
      text-align: center;
    }

    .footer-code {
      font-size: 13px;
      font-weight: bold;
      letter-spacing: 2px;
      margin-bottom: 4px;
    }

    .footer-brand {
      font-size: 9px;
      color: rgba(255,255,255,0.7);
    }

    .footer-disclaimer {
      font-size: 7px;
      color: rgba(255,255,255,0.5);
      margin-top: 6px;
      font-style: italic;
    }

    /* Page break */
    .page-break {
      page-break-after: always;
      height: 20px;
    }

    @media print {
      body { background: #ffffff; }
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
          <div class="logo-container">
            @if($tukiLogoExists)
              <img src="{{ $tukiLogoPath }}" alt="TukiPass">
            @else
              <span style="font-size:20px;font-weight:bold;color:#ffffff;">TUKIPASS</span>
            @endif
          </div>
          <div class="event-title">{{ $eventInfo->title ?? '' }}</div>
          <div class="event-date">{{ ucfirst($eventDate) }} · {{ $eventTime }} hs</div>
          @if($location)
            <div class="event-location">{{ $location }}</div>
          @endif
        </div>

        <!-- QR Section -->
        <div class="qr-section">
          <div class="qr-container">
            @if (file_exists($qrPath))
              <img src="{{ $qrPath }}" alt="QR">
            @else
              <div style="width:120px;height:120px;background:#f0f0f0;border-radius:4px;"></div>
            @endif
            <div class="qr-label">Entrada {{ $idx + 1 }} / {{ $ticketCount }}</div>
          </div>
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

          <!-- Info Grid - 3 columnas -->
          <table class="info-table">
            <tr>
              <td>
                <div class="info-label-small">Nº de Reserva</div>
                <div class="info-value-small">#{{ $bookingInfo->booking_id }}</div>
              </td>
              <td>
                <div class="info-label-small">Fecha de Reserva</div>
                <div class="info-value-small">{{ date_format($bookingInfo->created_at, 'd/m/Y') }}</div>
              </td>
              <td class="payment-method-cell">
                <div class="info-label-small">Método de Pago</div>
                <div class="info-value-small">
                  @if(strtolower($bookingInfo->paymentMethod ?? '') == 'mercadopago' || strtolower($bookingInfo->paymentMethod ?? '') == 'mercado pago')
                    @if($mpLogoExists)
                      <img src="{{ $mpLogoPath }}" alt="Mercado Pago">
                    @else
                      Mercado Pago
                    @endif
                  @else
                    {{ $bookingInfo->paymentMethod ?? '—' }}
                  @endif
                </div>
              </td>
            </tr>
            @if ($address)
            <tr>
              <td colspan="3">
                <div class="info-label-small">Dirección del Evento</div>
                <div class="info-value-small">{{ $address }}</div>
              </td>
            </tr>
            @endif
          </table>

          <!-- Payment -->
          @if (!$isFree)
          <div class="payment-section">
            <div class="payment-title">Detalle de Pago</div>
            <table class="payment-table">
              @if ($subtotal > 0)
              <tr>
                <td>{{ $quantity }} x {{ formatMoney($unitPrice, $position, $currency) }}</td>
                <td>{{ formatMoney($subtotal, $position, $currency) }}</td>
              </tr>
              @endif
              @if ($tax > 0)
              <tr>
                <td>Impuestos</td>
                <td>{{ formatMoney($tax, $position, $currency) }}</td>
              </tr>
              @endif
              @if ($discount > 0)
              <tr class="discount">
                <td>Descuentos</td>
                <td>− {{ formatMoney($discount, $position, $currency) }}</td>
              </tr>
              @endif
              <tr class="payment-total">
                <td>TOTAL</td>
                <td>{{ formatMoney($total, $position, $currency) }}</td>
              </tr>
            </table>
          </div>
          @else
          <div class="payment-section" style="text-align:center;">
            <div class="payment-title">Entrada Gratuita</div>
          </div>
          @endif

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
          <div class="logo-container">
            @if($tukiLogoExists)
              <img src="{{ $tukiLogoPath }}" alt="TukiPass">
            @else
              <span style="font-size:20px;font-weight:bold;color:#ffffff;">TUKIPASS</span>
            @endif
          </div>
          <div class="event-title">{{ $eventInfo->title ?? '' }}</div>
          <div class="event-date">{{ ucfirst($eventDate) }} · {{ $eventTime }} hs</div>
          @if($location)
            <div class="event-location">{{ $location }}</div>
          @endif
        </div>

        <!-- QR Section -->
        <div class="qr-section">
          <div class="qr-container">
            @if (file_exists($qrPath))
              <img src="{{ $qrPath }}" alt="QR">
            @else
              <div style="width:120px;height:120px;background:#f0f0f0;border-radius:4px;"></div>
            @endif
            <div class="qr-label">Entrada {{ $i }} / {{ $bookingInfo->quantity }}</div>
          </div>
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

          <!-- Info Grid - 3 columnas -->
          <table class="info-table">
            <tr>
              <td>
                <div class="info-label-small">Nº de Reserva</div>
                <div class="info-value-small">#{{ $bookingInfo->booking_id }}</div>
              </td>
              <td>
                <div class="info-label-small">Fecha de Reserva</div>
                <div class="info-value-small">{{ date_format($bookingInfo->created_at, 'd/m/Y') }}</div>
              </td>
              <td class="payment-method-cell">
                <div class="info-label-small">Método de Pago</div>
                <div class="info-value-small">
                  @if(strtolower($bookingInfo->paymentMethod ?? '') == 'mercadopago' || strtolower($bookingInfo->paymentMethod ?? '') == 'mercado pago')
                    @if($mpLogoExists)
                      <img src="{{ $mpLogoPath }}" alt="Mercado Pago">
                    @else
                      Mercado Pago
                    @endif
                  @else
                    {{ $bookingInfo->paymentMethod ?? '—' }}
                  @endif
                </div>
              </td>
            </tr>
            @if ($address)
            <tr>
              <td colspan="3">
                <div class="info-label-small">Dirección del Evento</div>
                <div class="info-value-small">{{ $address }}</div>
              </td>
            </tr>
            @endif
          </table>

          <!-- Payment -->
          @if (!$isFree)
          <div class="payment-section">
            <div class="payment-title">Detalle de Pago</div>
            <table class="payment-table">
              @if ($subtotal > 0)
              <tr>
                <td>{{ $quantity }} x {{ formatMoney($unitPrice, $position, $currency) }}</td>
                <td>{{ formatMoney($subtotal, $position, $currency) }}</td>
              </tr>
              @endif
              @if ($tax > 0)
              <tr>
                <td>Impuestos</td>
                <td>{{ formatMoney($tax, $position, $currency) }}</td>
              </tr>
              @endif
              @if ($discount > 0)
              <tr class="discount">
                <td>Descuentos</td>
                <td>− {{ formatMoney($discount, $position, $currency) }}</td>
              </tr>
              @endif
              <tr class="payment-total">
                <td>TOTAL</td>
                <td>{{ formatMoney($total, $position, $currency) }}</td>
              </tr>
            </table>
          </div>
          @else
          <div class="payment-section" style="text-align:center;">
            <div class="payment-title">Entrada Gratuita</div>
          </div>
          @endif

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
