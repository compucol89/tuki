@php
  $buyerName = $booking->buyerName();
  $buyerEmail = $booking->buyerEmail();
  $buyerPhone = $booking->buyerPhone();
  $displayName = $buyerName !== '' ? $buyerName : __('Invitado');
@endphp
<div class="ob-buyer">
  <div class="ob-buyer__name">
    <strong>{{ $displayName }}</strong>
    @if ($booking->isGuestBuyer())
      <span class="badge badge-secondary">{{ __('Invitado') }}</span>
    @endif
  </div>
  <div class="text-muted small">
    {{ __('Email') }}:
    @if ($buyerEmail !== '')
      <a href="mailto:{{ $buyerEmail }}">{{ $buyerEmail }}</a>
    @else
      -
    @endif
  </div>
  <div class="text-muted small">
    {{ __('Teléfono') }}:
    @if ($buyerPhone !== '')
      <a href="tel:{{ preg_replace('/\s+/', '', $buyerPhone) }}">{{ $buyerPhone }}</a>
    @else
      -
    @endif
  </div>
</div>
