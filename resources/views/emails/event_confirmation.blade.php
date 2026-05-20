<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tus entradas — TukiPass</title>
  <style>
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background-color: #f5f5f5;
      margin: 0;
      padding: 20px;
      color: #333;
      -webkit-font-smoothing: antialiased;
    }
    .container {
      max-width: 600px;
      margin: 0 auto;
      background: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    }
    .header {
      background: linear-gradient(135deg, #1e2532 0%, #2d3748 100%);
      color: #fff;
      padding: 32px 32px 24px;
      text-align: center;
    }
    .header .logo {
      max-width: 140px;
      margin-bottom: 16px;
    }
    .header h1 {
      margin: 0 0 8px;
      font-size: 22px;
      font-weight: 700;
    }
    .header p {
      margin: 0;
      font-size: 14px;
      opacity: 0.85;
    }
    .badge {
      display: inline-block;
      background: #F97316;
      color: #fff;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      padding: 5px 14px;
      border-radius: 20px;
      margin-top: 14px;
    }
    .body {
      padding: 32px;
    }
    .section {
      margin-bottom: 28px;
    }
    .section-title {
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      color: #888;
      margin-bottom: 10px;
      border-bottom: 2px solid #F97316;
      padding-bottom: 6px;
      display: inline-block;
    }
    .event-name {
      font-size: 18px;
      font-weight: 700;
      color: #1e2532;
      margin: 0 0 12px;
    }
    .info-grid {
      display: table;
      width: 100%;
      margin-bottom: 8px;
    }
    .info-row {
      display: table-row;
    }
    .info-label {
      display: table-cell;
      font-size: 13px;
      color: #888;
      padding: 4px 0;
      width: 120px;
      vertical-align: top;
    }
    .info-value {
      display: table-cell;
      font-size: 14px;
      color: #333;
      padding: 4px 0;
      font-weight: 500;
    }
    .tickets-table {
      width: 100%;
      border-collapse: collapse;
      margin: 12px 0;
      font-size: 14px;
    }
    .tickets-table th {
      background: #f9fafb;
      padding: 10px 12px;
      text-align: left;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #666;
      border-bottom: 2px solid #e5e7eb;
    }
    .tickets-table td {
      padding: 10px 12px;
      border-bottom: 1px solid #f0f0f0;
      color: #333;
    }
    .tickets-table tr:last-child td {
      border-bottom: none;
    }
    .tickets-table .text-right {
      text-align: right;
    }
    .total-row td {
      font-weight: 700;
      font-size: 16px;
      color: #1e2532;
      padding-top: 14px;
      border-top: 2px solid #e5e7eb;
    }
    .qr-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 16px;
      justify-content: center;
      margin: 16px 0;
    }
    .qr-card {
      background: #f9fafb;
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      padding: 16px;
      text-align: center;
      flex: 0 0 auto;
    }
    .qr-card img {
      display: block;
      margin: 0 auto 8px;
      max-width: 200px;
      height: auto;
    }
    .qr-card .qr-label {
      font-size: 12px;
      font-weight: 600;
      color: #555;
      margin: 0;
    }
    .qr-card .qr-sub {
      font-size: 11px;
      color: #999;
      margin: 2px 0 0;
    }
    .buyer-info {
      background: #f9fafb;
      border-radius: 8px;
      padding: 16px;
    }
    .cta-button {
      display: inline-block;
      background: #F97316;
      color: #fff;
      text-decoration: none;
      font-size: 14px;
      font-weight: 600;
      padding: 12px 28px;
      border-radius: 8px;
      margin: 8px 8px 8px 0;
      text-align: center;
    }
    .cta-button:hover {
      background: #ea580c;
    }
    .cta-button.secondary {
      background: #1e2532;
    }
    .cta-button.secondary:hover {
      background: #2d3748;
    }
    .disclaimer {
      background: #fff7ed;
      border-left: 4px solid #F97316;
      padding: 14px 18px;
      margin: 20px 0;
      font-size: 13px;
      color: #7c2d12;
      border-radius: 0 6px 6px 0;
    }
    .disclaimer strong {
      display: block;
      margin-bottom: 4px;
    }
    .footer {
      background: #f9fafb;
      padding: 28px 32px;
      text-align: center;
      font-size: 12px;
      color: #888;
      border-top: 1px solid #eee;
    }
    .footer .brand {
      font-size: 14px;
      font-weight: 700;
      color: #1e2532;
      margin-bottom: 4px;
    }
    .footer p {
      margin: 4px 0;
    }
    .warning-box {
      background: #fef3c7;
      border: 1px solid #fbbf24;
      border-radius: 8px;
      padding: 14px 18px;
      margin: 16px 0;
      font-size: 13px;
      color: #92400e;
    }
    .warning-box strong {
      display: block;
      margin-bottom: 4px;
    }
    @media (max-width: 480px) {
      body { padding: 10px; }
      .header { padding: 24px 20px 20px; }
      .header h1 { font-size: 18px; }
      .body { padding: 20px; }
      .footer { padding: 20px; }
      .qr-card { flex: 0 0 100%; }
      .info-grid { display: block; }
      .info-row { display: block; margin-bottom: 6px; }
      .info-label, .info-value { display: block; width: auto; }
      .info-label { font-size: 11px; margin-bottom: 2px; }
      .tickets-table { font-size: 12px; }
      .tickets-table th, .tickets-table td { padding: 8px 6px; }
    }
  </style>
</head>
<body>
  <div class="container">
    {{-- Header --}}
    <div class="header">
      <img src="{{ asset('assets/admin/img/logo-white.png') }}" alt="TukiPass" class="logo" style="max-width:140px; margin-bottom:16px;">
      <h1>¡Tu compra está confirmada!</h1>
      <p>Entradas para <strong>{{ $eventTitle }}</strong></p>
      <div class="badge">Entrada(s) confirmada(s)</div>
    </div>

    <div class="body">
      {{-- Evento --}}
      <div class="section">
        <div class="section-title">Evento</div>
        <h2 class="event-name">{{ $eventTitle }}</h2>
        <div class="info-grid">
          @if($eventDate)
          <div class="info-row">
            <span class="info-label">Fecha</span>
            <span class="info-value">{{ $eventDate }}</span>
          </div>
          @endif
          @if($eventTime)
          <div class="info-row">
            <span class="info-label">Hora</span>
            <span class="info-value">{{ $eventTime }} hs</span>
          </div>
          @endif
          @if($event && $event->event_type === 'online' && !empty($event->meeting_url))
          <div class="info-row">
            <span class="info-label">Modalidad</span>
            <span class="info-value">Online — <a href="{{ $event->meeting_url }}" style="color:#F97316;">Acceder al evento</a></span>
          </div>
          @elseif($event && $event->event_type !== 'online')
          @if($eventLocation)
          <div class="info-row">
            <span class="info-label">Ubicación</span>
            <span class="info-value">{{ $eventLocation }}</span>
          </div>
          @endif
          @if($eventAddress)
          <div class="info-row">
            <span class="info-label">Dirección</span>
            <span class="info-value">{{ $eventAddress }}</span>
          </div>
          @endif
          @endif
        </div>
      </div>

      {{-- Entradas --}}
      <div class="section">
        <div class="section-title">Tus entradas</div>
        <table class="tickets-table">
          <thead>
            <tr>
              <th>Tipo</th>
              <th class="text-right">Cant.</th>
              <th class="text-right">Precio unit.</th>
              <th class="text-right">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            @foreach($tickets as $ticket)
            <tr>
              <td>{{ $ticket['name'] }}</td>
              <td class="text-right">{{ $ticket['qty'] }}</td>
              <td class="text-right">${{ number_format($ticket['price'], 2, ',', '.') }}</td>
              <td class="text-right">${{ number_format($ticket['price'] * $ticket['qty'], 2, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
              <td colspan="3">Total</td>
              <td class="text-right">${{ number_format($booking->price ?? 0, 2, ',', '.') }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      {{-- Entradas adjuntas --}}
      <div class="section" style="text-align:center; margin: 32px 0;">
        <div style="background: #fff7ed; border: 2px solid #F97316; border-radius: 12px; padding: 28px 24px;">
          <div style="font-size: 32px; margin-bottom: 12px;">🎟️</div>
          <h3 style="margin: 0 0 8px; font-size: 18px; color: #1e2532;">Tus entradas están en el PDF adjunto</h3>
          <p style="margin: 0 0 16px; font-size: 14px; color: #666;">
            Descargá el archivo <strong>"Entradas_{{ $booking->booking_id }}.pdf"</strong> que viene con este email.<br>
            Presentalo al ingresar al evento — cada entrada tiene su código QR único.
          </p>
          <div style="background: #F97316; color: #fff; display: inline-block; padding: 12px 24px; border-radius: 8px; font-weight: 700; font-size: 14px;">
            📎 Ver adjunto en este email
          </div>
        </div>
      </div>

      {{-- Datos del comprador --}}
      <div class="section">
        <div class="section-title">Datos del comprador</div>
        <div class="buyer-info">
          <div class="info-grid">
            <div class="info-row">
              <span class="info-label">Nombre</span>
              <span class="info-value">{{ $booking->fname }} {{ $booking->lname }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Email</span>
              <span class="info-value">{{ $booking->email }}</span>
            </div>
            @if(!empty($booking->phone))
            <div class="info-row">
              <span class="info-label">Teléfono</span>
              <span class="info-value">{{ $booking->phone }}</span>
            </div>
            @endif
            @if(!empty($booking->dni))
            <div class="info-row">
              <span class="info-label">DNI</span>
              <span class="info-value">{{ $booking->dni }}</span>
            </div>
            @endif
            <div class="info-row">
              <span class="info-label">Nº de reserva</span>
              <span class="info-value"><strong>{{ $booking->booking_id }}</strong></span>
            </div>
          </div>
        </div>
      </div>

      {{-- Links de acción --}}
      @if($guestLink || $invoiceLink)
      <div class="section" style="text-align:center;">
        @if($guestLink)
        <a href="{{ $guestLink }}" class="cta-button">Ver mi reserva</a>
        @endif
        @if($invoiceLink)
        <a href="{{ $invoiceLink }}" class="cta-button secondary">Ver mi factura electrónica</a>
        @endif
      </div>
      @endif

      {{-- Disclaimer --}}
      <div class="disclaimer">
        <strong>📌 Guardá este email</strong>
        Tus códigos QR son necesarios para ingresar al evento. Este comprobante es interno de TukiPass. La factura fiscal se emitirá por separado si corresponde.
      </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
      <p class="brand">TukiPass</p>
      <p>Entradas y Tickets Online para Eventos en Argentina</p>
      @php
        $billing = \App\Models\BillingSetting::current();
      @endphp
      @if($billing->issuer_name || $billing->issuer_cuit)
        <p style="margin-top: 8px; font-size: 11px; color: #666;">
          <strong>{{ $billing->issuer_name ?? 'TAYRONA GROUP SAS' }}</strong>
          @if($billing->issuer_cuit) · CUIT {{ $billing->issuer_cuit }}@endif
          @if($billing->issuer_address)<br>{{ $billing->issuer_address }}@endif
          @if($billing->issuer_iva_condition_text)<br>{{ $billing->issuer_iva_condition_text }}@endif
        </p>
      @else
        <p style="margin-top: 8px; font-size: 11px; color: #666;">
          <strong>TAYRONA GROUP SAS</strong> · CUIT 30-71885087-4
        </p>
      @endif
      <p style="margin-top: 12px; font-size: 11px; color: #aaa;">
        Este email fue generado automáticamente. No respondas a esta dirección.<br>
        Si tenés dudas, contactanos a través de nuestro centro de ayuda.
      </p>
    </div>
  </div>
</body>
</html>
