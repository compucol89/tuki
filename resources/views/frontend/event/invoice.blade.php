<!DOCTYPE html>
<html>
@php
  $languageCode = $language->code;
  App::setLocale($languageCode);
  $bg_color = '#' . ($websiteInfo->primary_color ?? '00c2f4');
  $position = $bookingInfo->currencyTextPosition ?? 'left';
  $currency = $bookingInfo->currencyText ?? 'ARS';
  function formatMoney($amount, $position, $currency) {
    $amt = number_format((float)$amount, 2, ',', '.');
    return $position == 'left' ? $currency . ' ' . $amt : $amt . ' ' . $currency;
  }
@endphp
<head>
  <meta charset="UTF-8">
  <title>{{ __('Booking INVOICE') }} | {{ config('app.name') }}</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    html, body { height: auto !important; margin: 0 !important; padding: 0 !important; }
    body { font-family: "DejaVu Sans", sans-serif; font-size: 12px; color: #333; background: #fff; }

    .ticket-wrapper { width: 100%; }

    /* ── HEADER ── */
    .ticket-header {
      background: {{ $bg_color }};
      color: #fff;
      padding: 24px 30px;
      border-radius: 8px 8px 0 0;
    }
    .ticket-header .app-name { font-size: 22px; font-weight: bold; letter-spacing: 1px; }
    .ticket-header .event-title { font-size: 17px; margin-top: 6px; font-weight: bold; }
    .ticket-header .event-meta { font-size: 11px; margin-top: 4px; opacity: 0.9; }

    /* ── BODY ── */
    .ticket-body {
      background: #fff;
      border-left: 1px solid #ddd;
      border-right: 1px solid #ddd;
      padding: 24px 30px;
    }

    /* ── MAIN INFO TABLE ── */
    .info-row { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    .info-row td { vertical-align: top; padding: 0; }
    .info-col { width: 65%; padding-right: 20px; }
    .qr-col { width: 35%; text-align: center; }
    .qr-col img { width: 150px; height: 150px; }
    .qr-label { font-size: 10px; color: #888; margin-top: 6px; }

    /* ── DETAILS ── */
    .section-title {
      font-size: 10px;
      font-weight: bold;
      color: {{ $bg_color }};
      text-transform: uppercase;
      letter-spacing: 1px;
      border-bottom: 2px solid {{ $bg_color }};
      padding-bottom: 4px;
      margin-bottom: 10px;
    }

    .detail-grid { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
    .detail-grid td { padding: 5px 0; vertical-align: top; }
    .detail-label { font-size: 10px; color: #888; text-transform: uppercase; display: block; margin-bottom: 2px; }
    .detail-value { font-size: 12px; color: #222; font-weight: bold; }
    .detail-cell { width: 50%; padding-right: 12px; }

    /* ── DIVIDER ── */
    .divider { border: none; border-top: 1px dashed #ccc; margin: 18px 0; }

    /* ── BILLING TABLE ── */
    .billing-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    .billing-table td { padding: 5px 0; font-size: 11px; }
    .billing-table .label { color: #555; }
    .billing-table .value { text-align: right; font-weight: bold; color: #222; }
    .billing-table .total-row td { font-size: 13px; color: {{ $bg_color }}; font-weight: bold; border-top: 1px solid #eee; padding-top: 8px; }

    /* ── FOOTER ── */
    .ticket-footer {
      background: #f8f8f8;
      border: 1px solid #ddd;
      border-top: none;
      border-radius: 0 0 8px 8px;
      padding: 14px 30px;
      text-align: center;
      font-size: 10px;
      color: #888;
    }
    .booking-id-badge {
      display: inline-block;
      background: {{ $bg_color }};
      color: #fff;
      padding: 4px 14px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: bold;
      margin-bottom: 6px;
    }

    .page-break { page-break-after: always; }
    .status-badge {
      display: inline-block;
      padding: 3px 10px;
      border-radius: 12px;
      font-size: 10px;
      font-weight: bold;
      background: #e6f9f0;
      color: #1a8a4a;
    }
  </style>
</head>
<body>

@php
  $tickets = $bookingInfo->variation != null ? json_decode($bookingInfo->variation, true) : null;
  $ticketCount = $tickets ? count($tickets) : $bookingInfo->quantity;
@endphp

@if ($tickets)
  @foreach ($tickets as $idx => $variation)
    @php
      $ticket_content = App\Models\Event\TicketContent::where([
        ['ticket_id', $variation['ticket_id']],
        ['language_id', $language->id],
      ])->first();
      $ticket = App\Models\Event\Ticket::where('id', $variation['ticket_id'])->select('pricing_type')->first();
      $qrPath = public_path('assets/admin/qrcodes/' . $bookingInfo->booking_id . '__' . $variation['unique_id'] . '.svg');
    @endphp

    <div class="ticket-wrapper">
      {{-- HEADER --}}
      <div class="ticket-header">
        <div class="app-name">{{ config('app.name') }}</div>
        <div class="event-title">{{ $eventInfo->title ?? '' }}</div>
        <div class="event-meta">
          {{ \Carbon\Carbon::parse($bookingInfo->event_date)->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY') }}
          &nbsp;·&nbsp;
          {{ $bookingInfo->city }}{{ $bookingInfo->state ? ', ' . $bookingInfo->state : '' }}, {{ $bookingInfo->country }}
        </div>
      </div>

      {{-- BODY --}}
      <div class="ticket-body">

        {{-- INFO + QR --}}
        <table class="info-row">
          <tr>
            <td class="info-col">
              <p class="section-title">{{ __('Booking Details') }}</p>
              <table class="detail-grid">
                <tr>
                  <td class="detail-cell">
                    <span class="detail-label">{{ __('BOOKING DATE') }}</span>
                    <span class="detail-value">{{ date_format($bookingInfo->created_at, 'd/m/Y') }}</span>
                  </td>
                  <td class="detail-cell">
                    <span class="detail-label">{{ __('BOOKING ID') }}</span>
                    <span class="detail-value">#{{ $bookingInfo->booking_id }}</span>
                  </td>
                </tr>
                <tr>
                  <td class="detail-cell" style="padding-top:10px">
                    <span class="detail-label">{{ __('Name') }}</span>
                    <span class="detail-value">{{ $bookingInfo->fname }} {{ $bookingInfo->lname }}</span>
                  </td>
                  <td class="detail-cell" style="padding-top:10px">
                    <span class="detail-label">{{ __('Email') }}</span>
                    <span class="detail-value" style="font-size:11px">{{ $bookingInfo->email }}</span>
                  </td>
                </tr>
                @if ($ticket_content && $ticket && $ticket->pricing_type == 'variation')
                <tr>
                  <td class="detail-cell" style="padding-top:10px" colspan="2">
                    <span class="detail-label">{{ __('Ticket Name') }}</span>
                    <span class="detail-value">{{ $ticket_content->title }} — {{ $variation['name'] }}</span>
                  </td>
                </tr>
                @endif
                <tr>
                  <td style="padding-top:10px" colspan="2">
                    <span class="detail-label">{{ __('Address') }}</span>
                    <span class="detail-value">{{ $bookingInfo->address }}</span>
                  </td>
                </tr>
              </table>
            </td>
            <td class="qr-col">
              @if (file_exists($qrPath))
                <img src="{{ $qrPath }}" alt="QR">
              @endif
              <p class="qr-label">{{ __('Ticket') }} {{ $idx + 1 }} / {{ $ticketCount }}</p>
            </td>
          </tr>
        </table>

        <hr class="divider">

        {{-- BILLING --}}
        <p class="section-title">{{ __('Payment Info') }}</p>
        <table class="billing-table">
          <tr>
            <td class="label">{{ __('Subtotal') }}</td>
            <td class="value">{{ formatMoney($bookingInfo->price, $position, $currency) }}</td>
          </tr>
          @if ($bookingInfo->tax > 0)
          <tr>
            <td class="label">{{ __('Tax') }}</td>
            <td class="value">{{ formatMoney($bookingInfo->tax, $position, $currency) }}</td>
          </tr>
          @endif
          @if ($bookingInfo->early_bird_discount > 0)
          <tr>
            <td class="label">{{ __('EARLY BIRD') }}</td>
            <td class="value">- {{ formatMoney($bookingInfo->early_bird_discount, $position, $currency) }}</td>
          </tr>
          @endif
          @if ($bookingInfo->discount > 0)
          <tr>
            <td class="label">{{ __('COUPON') }}</td>
            <td class="value">- {{ formatMoney($bookingInfo->discount, $position, $currency) }}</td>
          </tr>
          @endif
          <tr class="total-row">
            <td>{{ __('TOTAL PAID') }}</td>
            <td class="value">{{ formatMoney($bookingInfo->price + $bookingInfo->tax, $position, $currency) }}</td>
          </tr>
        </table>

        <table class="detail-grid">
          <tr>
            <td class="detail-cell">
              <span class="detail-label">{{ __('PAYMENT METHOD') }}</span>
              <span class="detail-value">{{ $bookingInfo->paymentMethod ?? '-' }}</span>
            </td>
            <td class="detail-cell">
              <span class="detail-label">{{ __('PAYMENT STATUS') }}</span>
              <span class="status-badge">
                @if ($bookingInfo->paymentStatus == 'free') Reserva confirmada · Gratis
                @elseif ($bookingInfo->paymentStatus == 'completed' || $bookingInfo->paymentStatus == 'paid') Pago confirmado
                @elseif ($bookingInfo->paymentStatus == 'pending') Pendiente de confirmación
                @else {{ ucfirst($bookingInfo->paymentStatus) }} @endif
              </span>
            </td>
          </tr>
        </table>

        @if (!empty($event->instructions))
          <hr class="divider">
          <p class="section-title">{{ __('Instructions') }}</p>
          <div style="font-size:11px; color:#555; line-height:1.5">{!! $event->instructions !!}</div>
        @endif

      </div>

      {{-- FOOTER --}}
      <div class="ticket-footer">
        <div class="booking-id-badge">#{{ $bookingInfo->booking_id }}</div>
        <p>{{ config('app.name') }} &nbsp;·&nbsp; {{ __('Thank you') }}</p>
      </div>
    </div>

    @if (!$loop->last)
      <div class="page-break"></div>
    @endif
  @endforeach

@else
  @for ($i = 1; $i <= $bookingInfo->quantity; $i++)
    @php
      $qrPath = public_path('assets/admin/qrcodes/' . $bookingInfo->booking_id . '__' . $i . '.svg');
    @endphp

    <div class="ticket-wrapper">
      <div class="ticket-header">
        <div class="app-name">{{ config('app.name') }}</div>
        <div class="event-title">{{ $eventInfo->title ?? '' }}</div>
        <div class="event-meta">
          {{ \Carbon\Carbon::parse($bookingInfo->event_date)->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY') }}
          &nbsp;·&nbsp;
          {{ $bookingInfo->city }}{{ $bookingInfo->state ? ', ' . $bookingInfo->state : '' }}, {{ $bookingInfo->country }}
        </div>
      </div>

      <div class="ticket-body">
        <table class="info-row">
          <tr>
            <td class="info-col">
              <p class="section-title">{{ __('Booking Details') }}</p>
              <table class="detail-grid">
                <tr>
                  <td class="detail-cell">
                    <span class="detail-label">{{ __('BOOKING DATE') }}</span>
                    <span class="detail-value">{{ date_format($bookingInfo->created_at, 'd/m/Y') }}</span>
                  </td>
                  <td class="detail-cell">
                    <span class="detail-label">{{ __('BOOKING ID') }}</span>
                    <span class="detail-value">#{{ $bookingInfo->booking_id }}</span>
                  </td>
                </tr>
                <tr>
                  <td class="detail-cell" style="padding-top:10px">
                    <span class="detail-label">{{ __('Name') }}</span>
                    <span class="detail-value">{{ $bookingInfo->fname }} {{ $bookingInfo->lname }}</span>
                  </td>
                  <td class="detail-cell" style="padding-top:10px">
                    <span class="detail-label">{{ __('Email') }}</span>
                    <span class="detail-value" style="font-size:11px">{{ $bookingInfo->email }}</span>
                  </td>
                </tr>
                <tr>
                  <td style="padding-top:10px" colspan="2">
                    <span class="detail-label">{{ __('Address') }}</span>
                    <span class="detail-value">{{ $bookingInfo->address }}</span>
                  </td>
                </tr>
                <tr>
                  <td style="padding-top:10px">
                    <span class="detail-label">{{ __('Ticket') }}</span>
                    <span class="detail-value">{{ $i }} / {{ $bookingInfo->quantity }}</span>
                  </td>
                </tr>
              </table>
            </td>
            <td class="qr-col">
              @if (file_exists($qrPath))
                <img src="{{ $qrPath }}" alt="QR">
              @endif
              <p class="qr-label">{{ __('Ticket') }} {{ $i }} / {{ $bookingInfo->quantity }}</p>
            </td>
          </tr>
        </table>

        <hr class="divider">

        <p class="section-title">{{ __('Payment Info') }}</p>
        <table class="billing-table">
          <tr>
            <td class="label">{{ __('Subtotal') }}</td>
            <td class="value">{{ formatMoney($bookingInfo->price, $position, $currency) }}</td>
          </tr>
          @if ($bookingInfo->tax > 0)
          <tr>
            <td class="label">{{ __('Tax') }}</td>
            <td class="value">{{ formatMoney($bookingInfo->tax, $position, $currency) }}</td>
          </tr>
          @endif
          @if ($bookingInfo->early_bird_discount > 0)
          <tr>
            <td class="label">{{ __('EARLY BIRD') }}</td>
            <td class="value">- {{ formatMoney($bookingInfo->early_bird_discount, $position, $currency) }}</td>
          </tr>
          @endif
          @if ($bookingInfo->discount > 0)
          <tr>
            <td class="label">{{ __('COUPON') }}</td>
            <td class="value">- {{ formatMoney($bookingInfo->discount, $position, $currency) }}</td>
          </tr>
          @endif
          <tr class="total-row">
            <td>{{ __('TOTAL PAID') }}</td>
            <td class="value">{{ formatMoney($bookingInfo->price + $bookingInfo->tax, $position, $currency) }}</td>
          </tr>
        </table>

        <table class="detail-grid">
          <tr>
            <td class="detail-cell">
              <span class="detail-label">{{ __('PAYMENT METHOD') }}</span>
              <span class="detail-value">{{ $bookingInfo->paymentMethod ?? '-' }}</span>
            </td>
            <td class="detail-cell">
              <span class="detail-label">{{ __('PAYMENT STATUS') }}</span>
              <span class="status-badge">
                @if ($bookingInfo->paymentStatus == 'free') Reserva confirmada · Gratis
                @elseif ($bookingInfo->paymentStatus == 'completed' || $bookingInfo->paymentStatus == 'paid') Pago confirmado
                @elseif ($bookingInfo->paymentStatus == 'pending') Pendiente de confirmación
                @else {{ ucfirst($bookingInfo->paymentStatus) }} @endif
              </span>
            </td>
          </tr>
        </table>

        @if (!empty($event->instructions))
          <hr class="divider">
          <p class="section-title">{{ __('Instructions') }}</p>
          <div style="font-size:11px; color:#555; line-height:1.5">{!! $event->instructions !!}</div>
        @endif
      </div>

      <div class="ticket-footer">
        <div class="booking-id-badge">#{{ $bookingInfo->booking_id }}</div>
        <p>{{ config('app.name') }} &nbsp;·&nbsp; {{ __('Thank you') }}</p>
      </div>
    </div>

    @if ($i < $bookingInfo->quantity)
      <div class="page-break"></div>
    @endif
  @endfor
@endif

</body>
</html>
