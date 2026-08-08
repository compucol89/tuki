@php
  $customer = $booking->customerInfo;
  $buyerName = $booking->buyerName();
  $buyerEmail = $booking->buyerEmail();
  $buyerPhone = $booking->buyerPhone();
  $displayName = $buyerName !== '' ? $buyerName : __('Invitado');
  $defaultLanguageCode = $defaultLanguageCode ?? (optional($defaultLanguage ?? null)->code ?: 'es');
@endphp
<div class="eb-buyer">
  <div class="eb-buyer__name">
    @if ($customer)
      <a href="{{ route('admin.customer_management.customer_details', ['id' => $customer->id, 'language' => $defaultLanguageCode]) }}">
        {{ $displayName }}
      </a>
    @else
      <strong>{{ $displayName }}</strong>
    @endif
    @if ($booking->isGuestBuyer())
      <span class="badge badge-secondary eb-buyer__guest">{{ __('Invitado') }}</span>
    @endif
  </div>
  <div class="eb-muted">
    <span class="eb-buyer__label">{{ __('Email') }}:</span>
    @if ($buyerEmail !== '')
      <a href="mailto:{{ $buyerEmail }}">{{ $buyerEmail }}</a>
    @else
      -
    @endif
  </div>
  <div class="eb-muted">
    <span class="eb-buyer__label">{{ __('Teléfono') }}:</span>
    @if ($buyerPhone !== '')
      <a href="tel:{{ preg_replace('/\s+/', '', $buyerPhone) }}">{{ $buyerPhone }}</a>
    @else
      -
    @endif
  </div>
</div>
