@php
  $canonicalRefundPolicy = \App\Support\EventRefundPolicy::canonicalPlainText();
@endphp
<div class="form-group event-refund-policy-fixed">
  <label>{{ __('Política de reembolsos') }} <span class="text-muted font-weight-normal">({{ __('texto fijo de Tukipass') }})</span></label>
  <textarea class="form-control bg-light" rows="6" readonly aria-readonly="true">{{ $canonicalRefundPolicy }}</textarea>
  <small class="form-text text-muted">
    {{ __('Este texto es el mismo en todos los eventos y no puede modificarse. Las condiciones completas están en') }}
    <a href="{{ url('/politica-de-reembolsos') }}" target="_blank" rel="noopener">{{ __('Política de reembolsos de Tukipass') }}</a>.
  </small>
</div>
