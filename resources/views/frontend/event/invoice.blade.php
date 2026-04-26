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
  $tickets    = $bookingInfo->variation != null ? json_decode($bookingInfo->variation, true) : null;
  $ticketCount = $tickets ? count($tickets) : $bookingInfo->quantity;
  $eventDate  = \Carbon\Carbon::parse($bookingInfo->event_date)->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY');
  $location   = trim(($bookingInfo->city ?? '') . ($bookingInfo->state ? ', ' . $bookingInfo->state : '') . ', ' . ($bookingInfo->country ?? ''), ', ');
@endphp
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ __('Comprobante de reserva') }} — {{ $eventInfo->title ?? config('app.name') }}</title>
  <link rel="stylesheet" href="{{ mix('css/app.css') }}">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { background: #f0f2f5; font-family: 'Inter', -apple-system, sans-serif; font-size: 13px; color: #1e2532; -webkit-print-color-adjust: exact; print-color-adjust: exact; }

    /* ── WRAPPER ── */
    .ticket { width: 680px; margin: 40px auto; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 40px rgba(0,0,0,0.13); background: #fff; }

    /* ── HEADER ── */
    .ticket__header { background: #1e2532; padding: 28px 32px 24px; position: relative; overflow: hidden; }
    .ticket__header::before { content: ''; position: absolute; right: -40px; top: -40px; width: 200px; height: 200px; border-radius: 50%; background: rgba(255,255,255,0.04); }
    .ticket__header::after  { content: ''; position: absolute; right: 60px; bottom: -60px; width: 160px; height: 160px; border-radius: 50%; background: rgba(255,255,255,0.03); }
    .ticket__header-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
    .ticket__brand { font-size: 13px; font-weight: 700; color: rgba(255,255,255,0.5); letter-spacing: 0.08em; text-transform: uppercase; }
    .ticket__status { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 99px; font-size: 11px; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; }
    .ticket__status--paid  { background: rgba(34,197,94,0.15); color: #22c55e; }
    .ticket__status--free  { background: rgba(34,197,94,0.15); color: #22c55e; }
    .ticket__status--pending { background: rgba(251,191,36,0.15); color: #fbbf24; }
    .ticket__status--default { background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.7); }
    .ticket__status-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .ticket__event-title { font-size: 24px; font-weight: 800; color: #fff; line-height: 1.2; margin-bottom: 8px; letter-spacing: -0.02em; }
    .ticket__event-meta { font-size: 12px; color: rgba(255,255,255,0.55); display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .ticket__event-meta-sep { width: 3px; height: 3px; border-radius: 50%; background: rgba(255,255,255,0.3); }

    /* ── ACCENT BAR ── */
    .ticket__accent { height: 4px; background: {{ $primary }}; }

    /* ── BODY ── */
    .ticket__body { padding: 28px 32px; }

    /* ── INFO ROW ── */
    .ticket__info { display: flex; gap: 24px; margin-bottom: 24px; }
    .ticket__info-main { flex: 1; }
    .ticket__info-qr { width: 140px; flex-shrink: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #f8f9fb; border-radius: 12px; padding: 16px; border: 1px solid #eaecf0; }
    .ticket__info-qr img { width: 108px; height: 108px; display: block; }
    .ticket__info-qr-label { font-size: 11px; color: #8b95a3; margin-top: 8px; font-weight: 500; }

    /* ── SECTION LABEL ── */
    .ticket__section-label { font-size: 10px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: {{ $primary }}; margin-bottom: 12px; }

    /* ── FIELDS GRID ── */
    .ticket__fields { display: grid; grid-template-columns: 1fr 1fr; gap: 14px 20px; }
    .ticket__fields--full { grid-template-columns: 1fr; }
    .ticket__field { display: flex; flex-direction: column; gap: 3px; }
    .ticket__field-label { font-size: 10px; font-weight: 600; letter-spacing: 0.07em; text-transform: uppercase; color: #9ca3af; }
    .ticket__field-value { font-size: 13px; font-weight: 600; color: #1e2532; line-height: 1.3; }

    /* ── DIVIDER ── */
    .ticket__divider { position: relative; margin: 24px -32px; border: none; }
    .ticket__divider::before { content: ''; display: block; border-top: 2px dashed #e5e7eb; }
    .ticket__divider-circle { position: absolute; top: 50%; width: 22px; height: 22px; background: #f0f2f5; border-radius: 50%; transform: translateY(-50%); }
    .ticket__divider-circle--left  { left: -11px; }
    .ticket__divider-circle--right { right: -11px; }

    /* ── BILLING ── */
    .ticket__billing { margin-bottom: 20px; }
    .ticket__billing-row { display: flex; justify-content: space-between; align-items: center; padding: 7px 0; font-size: 12px; border-bottom: 1px solid #f3f4f6; }
    .ticket__billing-row:last-child { border-bottom: none; }
    .ticket__billing-row-label { color: #6b7280; }
    .ticket__billing-row-value { font-weight: 600; color: #1e2532; }
    .ticket__billing-total { display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; background: #f8f9fb; border-radius: 10px; border: 1px solid #eaecf0; margin-top: 12px; }
    .ticket__billing-total-label { font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.06em; }
    .ticket__billing-total-value { font-size: 18px; font-weight: 800; color: {{ $primary }}; letter-spacing: -0.02em; }

    /* ── PAYMENT META ── */
    .ticket__payment-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 16px; }

    /* ── INSTRUCTIONS ── */
    .ticket__instructions { margin-top: 20px; padding: 16px; background: #fffbf5; border-left: 3px solid {{ $primary }}; border-radius: 0 8px 8px 0; font-size: 12px; color: #555; line-height: 1.6; }

    /* ── FOOTER ── */
    .ticket__footer { background: #f8f9fb; border-top: 1px solid #eaecf0; padding: 16px 32px; display: flex; align-items: center; justify-content: space-between; }
    .ticket__booking-id { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: #1e2532; color: #fff; border-radius: 99px; font-size: 11px; font-weight: 700; letter-spacing: 0.04em; }
    .ticket__footer-note { font-size: 11px; color: #9ca3af; }
    .ticket__footer-note strong { color: #6b7280; }

    /* ── PAGE BREAK ── */
    .page-break { page-break-after: always; margin: 40px 0; }

    @media print {
      body { background: #fff; }
      .ticket { margin: 0; box-shadow: none; border-radius: 0; width: 100%; }
      .ticket__divider-circle { background: #fff; }
    }
  </style>
</head>
<body>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- VARIACIÓN: un ticket por cada variación seleccionada       --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
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

    <div class="ticket">

      {{-- HEADER --}}
      <div class="ticket__header">
        <div class="ticket__header-top">
          <span class="ticket__brand">{{ config('app.name') }}</span>
          <span class="ticket__status {{ $isPaid ? 'ticket__status--paid' : ($isFree ? 'ticket__status--free' : ($isPending ? 'ticket__status--pending' : 'ticket__status--default')) }}">
            <span class="ticket__status-dot"></span>
            @if($isFree) Reserva gratuita confirmada
            @elseif($isPaid) Pago confirmado
            @elseif($isPending) Pendiente de confirmación
            @else {{ ucfirst($bookingInfo->paymentStatus) }}
            @endif
          </span>
        </div>
        <div class="ticket__event-title">{{ $eventInfo->title ?? '' }}</div>
        <div class="ticket__event-meta">
          <span>{{ ucfirst($eventDate) }}</span>
          @if($location)
            <span class="ticket__event-meta-sep"></span>
            <span>{{ $location }}</span>
          @endif
        </div>
      </div>

      <div class="ticket__accent"></div>

      {{-- BODY --}}
      <div class="ticket__body">

        {{-- INFO + QR --}}
        <div class="ticket__info">
          <div class="ticket__info-main">
            <div class="ticket__section-label">{{ __('Booking Details') }}</div>
            <div class="ticket__fields">
              <div class="ticket__field">
                <span class="ticket__field-label">{{ __('BOOKING DATE') }}</span>
                <span class="ticket__field-value">{{ date_format($bookingInfo->created_at, 'd/m/Y') }}</span>
              </div>
              <div class="ticket__field">
                <span class="ticket__field-label">{{ __('BOOKING ID') }}</span>
                <span class="ticket__field-value">#{{ $bookingInfo->booking_id }}</span>
              </div>
              <div class="ticket__field">
                <span class="ticket__field-label">{{ __('Name') }}</span>
                <span class="ticket__field-value">{{ $bookingInfo->fname }} {{ $bookingInfo->lname }}</span>
              </div>
              <div class="ticket__field">
                <span class="ticket__field-label">{{ __('Email') }}</span>
                <span class="ticket__field-value" style="font-size:12px;word-break:break-all">{{ $bookingInfo->email }}</span>
              </div>
              @if ($ticket_content && $ticket && $ticket->pricing_type == 'variation')
              <div class="ticket__field" style="grid-column:1/-1">
                <span class="ticket__field-label">{{ __('Ticket Name') }}</span>
                <span class="ticket__field-value">{{ $ticket_content->title }} — {{ $variation['name'] }}</span>
              </div>
              @endif
              <div class="ticket__field" style="grid-column:1/-1">
                <span class="ticket__field-label">{{ __('Address') }}</span>
                <span class="ticket__field-value">{{ $bookingInfo->address }}</span>
              </div>
            </div>
          </div>

          <div class="ticket__info-qr">
            @if (file_exists($qrPath))
              <img src="{{ $qrPath }}" alt="QR">
            @endif
            <span class="ticket__info-qr-label">{{ __('Ticket') }} {{ $idx + 1 }} / {{ $ticketCount }}</span>
          </div>
        </div>

        {{-- DIVIDER --}}
        <hr class="ticket__divider">
        <div class="ticket__divider-circle ticket__divider-circle--left"></div>
        <div class="ticket__divider-circle ticket__divider-circle--right"></div>

        {{-- BILLING --}}
        <div class="ticket__billing">
          <div class="ticket__section-label">{{ __('Payment Info') }}</div>
          @if ($bookingInfo->price > 0)
          <div class="ticket__billing-row">
            <span class="ticket__billing-row-label">{{ __('Subtotal') }}</span>
            <span class="ticket__billing-row-value">{{ formatMoney($bookingInfo->price, $position, $currency) }}</span>
          </div>
          @endif
          @if ($bookingInfo->tax > 0)
          <div class="ticket__billing-row">
            <span class="ticket__billing-row-label">{{ __('Tax') }}</span>
            <span class="ticket__billing-row-value">{{ formatMoney($bookingInfo->tax, $position, $currency) }}</span>
          </div>
          @endif
          @if ($bookingInfo->early_bird_discount > 0)
          <div class="ticket__billing-row">
            <span class="ticket__billing-row-label">{{ __('EARLY BIRD') }}</span>
            <span class="ticket__billing-row-value" style="color:#22c55e">− {{ formatMoney($bookingInfo->early_bird_discount, $position, $currency) }}</span>
          </div>
          @endif
          @if ($bookingInfo->discount > 0)
          <div class="ticket__billing-row">
            <span class="ticket__billing-row-label">{{ __('COUPON') }}</span>
            <span class="ticket__billing-row-value" style="color:#22c55e">− {{ formatMoney($bookingInfo->discount, $position, $currency) }}</span>
          </div>
          @endif
          <div class="ticket__billing-total">
            <span class="ticket__billing-total-label">{{ __('TOTAL PAID') }}</span>
            <span class="ticket__billing-total-value">
              @if($isFree) Gratis
              @else {{ formatMoney($bookingInfo->price + $bookingInfo->tax, $position, $currency) }}
              @endif
            </span>
          </div>
        </div>

        {{-- PAYMENT META --}}
        <div class="ticket__payment-meta">
          <div class="ticket__field">
            <span class="ticket__field-label">{{ __('PAYMENT METHOD') }}</span>
            <span class="ticket__field-value">{{ $bookingInfo->paymentMethod ?? '—' }}</span>
          </div>
          <div class="ticket__field">
            <span class="ticket__field-label">{{ __('PAYMENT STATUS') }}</span>
            <span class="ticket__field-value">
              @if($isFree) Reserva gratuita
              @elseif($isPaid) Pago confirmado
              @elseif($isPending) Pendiente
              @else {{ ucfirst($bookingInfo->paymentStatus) }}
              @endif
            </span>
          </div>
        </div>

        @if (!empty($event->instructions))
          <div class="ticket__instructions">
            <div class="ticket__section-label" style="margin-bottom:8px">{{ __('Instructions') }}</div>
            {!! $event->instructions !!}
          </div>
        @endif

      </div>

      {{-- FOOTER --}}
      <div class="ticket__footer">
        <span class="ticket__booking-id">#{{ $bookingInfo->booking_id }}</span>
        <span class="ticket__footer-note"><strong>{{ config('app.name') }}</strong> &nbsp;·&nbsp; {{ __('Thank you') }}</span>
      </div>

    </div>

    @if (!$loop->last)
      <div class="page-break"></div>
    @endif
  @endforeach

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- NORMAL: un ticket por cantidad comprada                    --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
@else
  @for ($i = 1; $i <= $bookingInfo->quantity; $i++)
    @php
      $qrPath   = public_path('assets/admin/qrcodes/' . $bookingInfo->booking_id . '__' . $i . '.svg');
      $isPaid   = in_array($bookingInfo->paymentStatus, ['completed', 'paid']);
      $isFree   = $bookingInfo->paymentStatus == 'free';
      $isPending = $bookingInfo->paymentStatus == 'pending';
    @endphp

    <div class="ticket">

      <div class="ticket__header">
        <div class="ticket__header-top">
          <span class="ticket__brand">{{ config('app.name') }}</span>
          <span class="ticket__status {{ $isPaid ? 'ticket__status--paid' : ($isFree ? 'ticket__status--free' : ($isPending ? 'ticket__status--pending' : 'ticket__status--default')) }}">
            <span class="ticket__status-dot"></span>
            @if($isFree) Reserva gratuita confirmada
            @elseif($isPaid) Pago confirmado
            @elseif($isPending) Pendiente de confirmación
            @else {{ ucfirst($bookingInfo->paymentStatus) }}
            @endif
          </span>
        </div>
        <div class="ticket__event-title">{{ $eventInfo->title ?? '' }}</div>
        <div class="ticket__event-meta">
          <span>{{ ucfirst($eventDate) }}</span>
          @if($location)
            <span class="ticket__event-meta-sep"></span>
            <span>{{ $location }}</span>
          @endif
        </div>
      </div>

      <div class="ticket__accent"></div>

      <div class="ticket__body">

        <div class="ticket__info">
          <div class="ticket__info-main">
            <div class="ticket__section-label">{{ __('Booking Details') }}</div>
            <div class="ticket__fields">
              <div class="ticket__field">
                <span class="ticket__field-label">{{ __('BOOKING DATE') }}</span>
                <span class="ticket__field-value">{{ date_format($bookingInfo->created_at, 'd/m/Y') }}</span>
              </div>
              <div class="ticket__field">
                <span class="ticket__field-label">{{ __('BOOKING ID') }}</span>
                <span class="ticket__field-value">#{{ $bookingInfo->booking_id }}</span>
              </div>
              <div class="ticket__field">
                <span class="ticket__field-label">{{ __('Name') }}</span>
                <span class="ticket__field-value">{{ $bookingInfo->fname }} {{ $bookingInfo->lname }}</span>
              </div>
              <div class="ticket__field">
                <span class="ticket__field-label">{{ __('Email') }}</span>
                <span class="ticket__field-value" style="font-size:12px;word-break:break-all">{{ $bookingInfo->email }}</span>
              </div>
              <div class="ticket__field" style="grid-column:1/-1">
                <span class="ticket__field-label">{{ __('Address') }}</span>
                <span class="ticket__field-value">{{ $bookingInfo->address }}</span>
              </div>
              <div class="ticket__field">
                <span class="ticket__field-label">{{ __('Ticket') }}</span>
                <span class="ticket__field-value">{{ $i }} / {{ $bookingInfo->quantity }}</span>
              </div>
            </div>
          </div>

          <div class="ticket__info-qr">
            @if (file_exists($qrPath))
              <img src="{{ $qrPath }}" alt="QR">
            @endif
            <span class="ticket__info-qr-label">{{ __('Ticket') }} {{ $i }} / {{ $bookingInfo->quantity }}</span>
          </div>
        </div>

        <hr class="ticket__divider">
        <div class="ticket__divider-circle ticket__divider-circle--left"></div>
        <div class="ticket__divider-circle ticket__divider-circle--right"></div>

        <div class="ticket__billing">
          <div class="ticket__section-label">{{ __('Payment Info') }}</div>
          @if ($bookingInfo->price > 0)
          <div class="ticket__billing-row">
            <span class="ticket__billing-row-label">{{ __('Subtotal') }}</span>
            <span class="ticket__billing-row-value">{{ formatMoney($bookingInfo->price, $position, $currency) }}</span>
          </div>
          @endif
          @if ($bookingInfo->tax > 0)
          <div class="ticket__billing-row">
            <span class="ticket__billing-row-label">{{ __('Tax') }}</span>
            <span class="ticket__billing-row-value">{{ formatMoney($bookingInfo->tax, $position, $currency) }}</span>
          </div>
          @endif
          @if ($bookingInfo->early_bird_discount > 0)
          <div class="ticket__billing-row">
            <span class="ticket__billing-row-label">{{ __('EARLY BIRD') }}</span>
            <span class="ticket__billing-row-value" style="color:#22c55e">− {{ formatMoney($bookingInfo->early_bird_discount, $position, $currency) }}</span>
          </div>
          @endif
          @if ($bookingInfo->discount > 0)
          <div class="ticket__billing-row">
            <span class="ticket__billing-row-label">{{ __('COUPON') }}</span>
            <span class="ticket__billing-row-value" style="color:#22c55e">− {{ formatMoney($bookingInfo->discount, $position, $currency) }}</span>
          </div>
          @endif
          <div class="ticket__billing-total">
            <span class="ticket__billing-total-label">{{ __('TOTAL PAID') }}</span>
            <span class="ticket__billing-total-value">
              @if($isFree) Gratis
              @else {{ formatMoney($bookingInfo->price + $bookingInfo->tax, $position, $currency) }}
              @endif
            </span>
          </div>
        </div>

        <div class="ticket__payment-meta">
          <div class="ticket__field">
            <span class="ticket__field-label">{{ __('PAYMENT METHOD') }}</span>
            <span class="ticket__field-value">{{ $bookingInfo->paymentMethod ?? '—' }}</span>
          </div>
          <div class="ticket__field">
            <span class="ticket__field-label">{{ __('PAYMENT STATUS') }}</span>
            <span class="ticket__field-value">
              @if($isFree) Reserva gratuita
              @elseif($isPaid) Pago confirmado
              @elseif($isPending) Pendiente
              @else {{ ucfirst($bookingInfo->paymentStatus) }}
              @endif
            </span>
          </div>
        </div>

        @if (!empty($event->instructions))
          <div class="ticket__instructions">
            <div class="ticket__section-label" style="margin-bottom:8px">{{ __('Instructions') }}</div>
            {!! $event->instructions !!}
          </div>
        @endif

      </div>

      <div class="ticket__footer">
        <span class="ticket__booking-id">#{{ $bookingInfo->booking_id }}</span>
        <span class="ticket__footer-note"><strong>{{ config('app.name') }}</strong> &nbsp;·&nbsp; {{ __('Thank you') }}</span>
      </div>

    </div>

    @if ($i < $bookingInfo->quantity)
      <div class="page-break"></div>
    @endif
  @endfor
@endif

</body>
</html>
