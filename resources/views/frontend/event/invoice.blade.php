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
      margin: 10mm 15mm;
    }
    
    * { box-sizing: border-box; margin: 0; padding: 0; }
    
    body { 
      font-family: Helvetica, Arial, sans-serif;
      font-size: 10px;
      line-height: 1.3;
      color: #1a1a1a;
      background: #ffffff;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    .ticket-container {
      width: 100%;
      max-width: 100%;
      margin: 0 auto;
    }

    /* Ticket con forma de ticket real */
    .ticket {
      background: #ffffff;
      border: 2px solid #F97316;
      border-radius: 15px;
      overflow: hidden;
      page-break-inside: avoid;
      position: relative;
    }

    /* Perforaciones circulares en los bordes */
    .ticket::before,
    .ticket::after {
      content: '';
      position: absolute;
      width: 30px;
      height: 30px;
      background: #ffffff;
      border: 2px solid #F97316;
      border-radius: 50%;
      top: 50%;
      transform: translateY(-50%);
      z-index: 10;
    }

    .ticket::before {
      left: -17px;
      border-left: none;
    }

    .ticket::after {
      right: -17px;
      border-right: none;
    }

    /* Header */
    .ticket-header {
      background: #F97316;
      color: #ffffff;
      padding: 15px 25px;
      text-align: center;
    }

    .logo-container {
      margin-bottom: 10px;
    }

    .logo-container img {
      height: 28px;
      filter: brightness(0) invert(1); /* Hace el logo blanco */
    }

    .event-title {
      font-size: 18px;
      font-weight: bold;
      line-height: 1.2;
      margin-bottom: 5px;
    }

    .event-date {
      font-size: 11px;
      font-weight: bold;
    }

    .event-location {
      font-size: 10px;
      margin-top: 3px;
    }

    /* QR Section */
    .qr-section {
      background: #F97316;
      padding: 15px;
      text-align: center;
    }

    .qr-container {
      background: #ffffff;
      border-radius: 10px;
      padding: 12px;
      display: inline-block;
    }

    .qr-container img {
      width: 100px;
      height: 100px;
    }

    .qr-label {
      font-size: 9px;
      color: #666;
      margin-top: 5px;
      font-weight: bold;
      text-transform: uppercase;
    }

    /* Ticket Details */
    .ticket-details {
      padding: 15px 25px;
      background: #ffffff;
    }

    .ticket-type {
      text-align: center;
      margin-bottom: 12px;
      padding-bottom: 10px;
      border-bottom: 1px dashed #ddd;
    }

    .ticket-type-label {
      font-size: 8px;
      color: #F97316;
      font-weight: bold;
      text-transform: uppercase;
    }

    .ticket-type-name {
      font-size: 16px;
      font-weight: bold;
      color: #1a1a1a;
      margin-top: 3px;
    }

    /* Attendee */
    .attendee-section {
      text-align: center;
      padding: 10px 0;
      margin-bottom: 12px;
      border-bottom: 1px dashed #ddd;
    }

    .attendee-label {
      font-size: 8px;
      color: #999;
      text-transform: uppercase;
    }

    .attendee-name {
      font-size: 14px;
      font-weight: bold;
      color: #1a1a1a;
    }

    .attendee-email {
      font-size: 9px;
      color: #666;
    }

    /* Info Grid - 3 columnas */
    .info-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 12px;
    }

    .info-table td {
      padding: 6px 4px;
      border-bottom: 1px solid #f0f0f0;
      width: 33.33%;
      vertical-align: top;
    }

    .info-label-small {
      font-size: 7px;
      color: #999;
      text-transform: uppercase;
    }

    .info-value-small {
      font-size: 10px;
      font-weight: bold;
      color: #333;
    }

    .payment-method-cell img {
      height: 14px;
      margin-top: 2px;
    }

    /* Payment Summary */
    .payment-section {
      background: #fafafa;
      border: 1px solid #eee;
      border-radius: 8px;
      padding: 12px;
      margin-bottom: 12px;
    }

    .payment-title {
      font-size: 9px;
      color: #F97316;
      font-weight: bold;
      text-transform: uppercase;
      margin-bottom: 8px;
      text-align: center;
    }

    .payment-table {
      width: 100%;
      border-collapse: collapse;
    }

    .payment-table td {
      padding: 3px 0;
      font-size: 9px;
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
      font-size: 10px;
    }

    .payment-total td:last-child {
      font-size: 14px;
      color: #F97316;
    }

    /* Instructions */
    .instructions {
      background: #fffbeb;
      border-left: 3px solid #F97316;
      padding: 10px;
      margin-bottom: 12px;
    }

    .instructions-title {
      font-size: 9px;
      font-weight: bold;
      color: #1a1a1a;
      margin-bottom: 5px;
    }

    .instructions ul {
      margin: 0;
      padding-left: 12px;
    }

    .instructions li {
      font-size: 8px;
      color: #555;
      margin-bottom: 2px;
    }

    /* Footer */
    .ticket-footer {
      background: #1a1a1a;
      color: #ffffff;
      padding: 12px;
      text-align: center;
    }

    .footer-code {
      font-size: 12px;
      font-weight: bold;
      letter-spacing: 2px;
      margin-bottom: 3px;
    }

    .footer-brand {
      font-size: 9px;
      color: #ccc;
    }

    .footer-disclaimer {
      font-size: 7px;
      color: #999;
      margin-top: 5px;
      font-style: italic;
    }

    /* Page break */
    .page-break {
      page-break-after: always;
      height: 15px;
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
              <span style="font-size:18px;font-weight:bold;color:#ffffff;">TUKIPASS</span>
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
              <div style="width:100px;height:100px;background:#f0f0f0;"></div>
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
            <div class="instructions-title">Instrucciones</div>
            <ul>
              <li>Presentá esta entrada junto con tu DNI al ingresar.</li>
              <li>No compartas el código QR. Es único e intransferible.</li>
              <li>Válida para una sola persona.</li>
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
              <span style="font-size:18px;font-weight:bold;color:#ffffff;">TUKIPASS</span>
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
              <div style="width:100px;height:100px;background:#f0f0f0;"></div>
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
            <div class="instructions-title">Instrucciones</div>
            <ul>
              <li>Presentá esta entrada junto con tu DNI al ingresar.</li>
              <li>No compartas el código QR. Es único e intransferible.</li>
              <li>Válida para una sola persona.</li>
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
