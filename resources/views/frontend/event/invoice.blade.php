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
  <title>Entrada — {{ $eventInfo->title ?? config('app.name') }}</title>
  <style>
    @page { 
      size: A4; 
      margin: 15mm 10mm;
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
      border: 2px solid #F97316;
      border-radius: 15px;
      overflow: hidden;
      page-break-inside: avoid;
      margin-bottom: 20px;
    }

    /* Header */
    .ticket-header {
      background: #F97316;
      color: #ffffff;
      padding: 25px 20px;
      text-align: center;
    }

    .event-type {
      font-size: 10px;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 2px;
      margin-bottom: 8px;
    }

    .event-title {
      font-size: 20px;
      font-weight: bold;
      line-height: 1.2;
      margin-bottom: 8px;
    }

    .event-date {
      font-size: 12px;
      font-weight: bold;
    }

    .event-location {
      font-size: 11px;
      margin-top: 5px;
    }

    /* Logo Section */
    .logo-section {
      background: #F97316;
      padding: 15px;
      text-align: center;
    }

    .logo-container {
      background: #ffffff;
      border-radius: 10px;
      padding: 10px 20px;
      display: inline-block;
    }

    .logo-container img {
      height: 30px;
    }

    /* QR Section */
    .qr-section {
      background: #F97316;
      padding: 20px;
      text-align: center;
    }

    .qr-container {
      background: #ffffff;
      border-radius: 10px;
      padding: 15px;
      display: inline-block;
    }

    .qr-container img {
      width: 130px;
      height: 130px;
    }

    .qr-label {
      font-size: 10px;
      color: #666;
      margin-top: 8px;
      font-weight: bold;
      text-transform: uppercase;
    }

    /* Perforation */
    .perforation {
      height: 15px;
      background: #ffffff;
      position: relative;
      border-top: 2px dashed #ddd;
      border-bottom: 2px dashed #ddd;
    }

    /* Ticket Details */
    .ticket-details {
      padding: 20px;
      background: #ffffff;
    }

    .ticket-type {
      text-align: center;
      margin-bottom: 20px;
      padding-bottom: 15px;
      border-bottom: 1px solid #eee;
    }

    .ticket-type-label {
      font-size: 9px;
      color: #F97316;
      font-weight: bold;
      text-transform: uppercase;
    }

    .ticket-type-name {
      font-size: 18px;
      font-weight: bold;
      color: #1a1a1a;
      margin-top: 5px;
    }

    /* Info Grid - usando tablas para DomPDF */
    .info-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 15px;
    }

    .info-table td {
      padding: 8px 5px;
      border-bottom: 1px solid #f0f0f0;
      width: 50%;
    }

    .info-label-small {
      font-size: 8px;
      color: #999;
      text-transform: uppercase;
    }

    .info-value-small {
      font-size: 11px;
      font-weight: bold;
      color: #333;
    }

    /* Attendee */
    .attendee-section {
      text-align: center;
      padding: 15px 0;
      border-top: 1px dashed #ddd;
      border-bottom: 1px dashed #ddd;
      margin-bottom: 15px;
    }

    .attendee-label {
      font-size: 9px;
      color: #999;
      text-transform: uppercase;
    }

    .attendee-name {
      font-size: 16px;
      font-weight: bold;
      color: #1a1a1a;
    }

    .attendee-email {
      font-size: 10px;
      color: #666;
      margin-top: 3px;
    }

    /* Payment Summary */
    .payment-section {
      background: #fafafa;
      border: 1px solid #eee;
      border-radius: 10px;
      padding: 15px;
      margin-bottom: 15px;
    }

    .payment-title {
      font-size: 10px;
      color: #F97316;
      font-weight: bold;
      text-transform: uppercase;
      margin-bottom: 10px;
      text-align: center;
    }

    .payment-table {
      width: 100%;
      border-collapse: collapse;
    }

    .payment-table td {
      padding: 5px 0;
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
      margin-top: 10px;
      padding-top: 10px;
      border-top: 2px solid #F97316;
    }

    .payment-total td {
      font-weight: bold;
      font-size: 12px;
    }

    .payment-total td:last-child {
      font-size: 16px;
      color: #F97316;
    }

    /* Footer */
    .ticket-footer {
      background: #1a1a1a;
      color: #ffffff;
      padding: 15px;
      text-align: center;
    }

    .footer-code {
      font-size: 14px;
      font-weight: bold;
      letter-spacing: 2px;
      margin-bottom: 5px;
    }

    .footer-brand {
      font-size: 10px;
      color: #ccc;
    }

    .footer-disclaimer {
      font-size: 8px;
      color: #999;
      margin-top: 8px;
      font-style: italic;
    }

    /* Instructions */
    .instructions {
      background: #fffbeb;
      border: 1px solid #F97316;
      border-left: 3px solid #F97316;
      padding: 12px;
      margin: 0 15px 15px;
    }

    .instructions-title {
      font-size: 10px;
      font-weight: bold;
      color: #1a1a1a;
      margin-bottom: 8px;
    }

    .instructions ul {
      margin: 0;
      padding-left: 15px;
    }

    .instructions li {
      font-size: 9px;
      color: #555;
      margin-bottom: 4px;
    }

    /* Page break */
    .page-break {
      page-break-after: always;
      height: 20px;
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
              <span style="font-size:20px;font-weight:bold;color:#F97316;">TUKIPASS</span>
            @endif
          </div>
        </div>

        <!-- QR Section -->
        <div class="qr-section">
          <div class="qr-container">
            @if (file_exists($qrPath))
              <img src="{{ $qrPath }}" alt="QR">
            @else
              <div style="width:130px;height:130px;background:#f0f0f0;"></div>
            @endif
            <div class="qr-label">Entrada {{ $idx + 1 }} / {{ $ticketCount }}</div>
          </div>
        </div>

        <!-- Perforation -->
        <div class="perforation"></div>

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
            </tr>
            @if ($address)
            <tr>
              <td colspan="2">
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

          <!-- Payment Method -->
          <div style="text-align:center;margin-bottom:5px;">
            <div class="info-label-small" style="margin-bottom:6px;">Método de Pago</div>
            @if(strtolower($bookingInfo->paymentMethod ?? '') == 'mercadopago' || strtolower($bookingInfo->paymentMethod ?? '') == 'mercado pago')
              @if($mpLogoExists)
                <img src="{{ $mpLogoPath }}" height="20" alt="Mercado Pago">
              @else
                <span style="font-weight:bold;color:#009EE3;font-size:14px;">Mercado Pago</span>
              @endif
            @else
              <span style="font-weight:bold;font-size:14px;">{{ $bookingInfo->paymentMethod ?? 'No especificado' }}</span>
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
              <span style="font-size:20px;font-weight:bold;color:#F97316;">TUKIPASS</span>
            @endif
          </div>
        </div>

        <!-- QR Section -->
        <div class="qr-section">
          <div class="qr-container">
            @if (file_exists($qrPath))
              <img src="{{ $qrPath }}" alt="QR">
            @else
              <div style="width:130px;height:130px;background:#f0f0f0;"></div>
            @endif
            <div class="qr-label">Entrada {{ $i }} / {{ $bookingInfo->quantity }}</div>
          </div>
        </div>

        <!-- Perforation -->
        <div class="perforation"></div>

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
            </tr>
            @if ($address)
            <tr>
              <td colspan="2">
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

          <!-- Payment Method -->
          <div style="text-align:center;margin-bottom:5px;">
            <div class="info-label-small" style="margin-bottom:6px;">Método de Pago</div>
            @if(strtolower($bookingInfo->paymentMethod ?? '') == 'mercadopago' || strtolower($bookingInfo->paymentMethod ?? '') == 'mercado pago')
              @if($mpLogoExists)
                <img src="{{ $mpLogoPath }}" height="20" alt="Mercado Pago">
              @else
                <span style="font-weight:bold;color:#009EE3;font-size:14px;">Mercado Pago</span>
              @endif
            @else
              <span style="font-weight:bold;font-size:14px;">{{ $bookingInfo->paymentMethod ?? 'No especificado' }}</span>
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
