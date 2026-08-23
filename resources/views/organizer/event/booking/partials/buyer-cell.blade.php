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
      <span class="badge badge-secondary ob-buyer__badge">{{ __('Invitado') }}</span>
    @endif
  </div>
  @if ($buyerEmail !== '')
    <div class="ob-buyer__meta"><a href="mailto:{{ $buyerEmail }}">{{ $buyerEmail }}</a></div>
  @endif
  @if ($buyerPhone !== '')
    <div class="ob-buyer__meta"><a href="tel:{{ preg_replace('/\s+/', '', $buyerPhone) }}">{{ $buyerPhone }}</a></div>
  @endif
</div>
