@php
  $freeLimitEnabled = (bool) optional($eventModel)->limit_free_tickets_per_person;
  $freeLimitValue = max((int) (optional($eventModel)->free_tickets_per_person_limit ?: 2), 1);
  $ticketPricingType = optional($ticket ?? null)->pricing_type ?: 'free';
  $ticketPrice = optional($ticket ?? null)->price;
  $showFreeLimit = $ticketPricingType === 'free'
    || ($ticketPricingType === 'normal' && !is_null($ticketPrice) && (float) $ticketPrice <= 0);
@endphp

<div class="col-lg-12 {{ $showFreeLimit ? '' : 'd-none' }}" id="free_ticket_limit_box">
  <div class="form-group mt-3 mb-0 p-3 border rounded bg-light">
    <input type="hidden" name="free_ticket_limit_settings_present" value="1">
    <label class="mb-2">{{ __('Límite para entradas gratis por persona') }}</label>
    <p class="text-muted mb-3">
      {{ __('Aplicá un límite real para entradas gratis o de precio $0. Se controla por evento usando email, teléfono y DNI/documento.') }}
    </p>
    <div class="row">
      <div class="col-lg-6">
        <div class="form-group mb-lg-0">
          <label>{{ __('Estado del límite') }}</label>
          <div class="selectgroup w-100">
            <label class="selectgroup-item">
              <input type="radio" name="limit_free_tickets_per_person" value="0"
                class="selectgroup-input" @checked(!$freeLimitEnabled)>
              <span class="selectgroup-button">{{ __('Desactivado') }}</span>
            </label>
            <label class="selectgroup-item">
              <input type="radio" name="limit_free_tickets_per_person" value="1"
                class="selectgroup-input" @checked($freeLimitEnabled)>
              <span class="selectgroup-button">{{ __('Activado') }}</span>
            </label>
          </div>
        </div>
      </div>
      <div class="col-lg-6 {{ $freeLimitEnabled ? '' : 'd-none' }}" id="free_ticket_limit_value">
        <div class="form-group mb-0">
          <label>{{ __('Máximo gratis por persona en este evento') }}</label>
          <input type="number" name="free_tickets_per_person_limit" min="1" max="10"
            value="{{ $freeLimitValue }}" class="form-control"
            placeholder="{{ __('Ej: 2') }}">
          <small class="form-text text-muted">
            {{ __('Ejemplo: si ponés 2, una misma persona no puede reservar más de 2 entradas gratis para este evento.') }}
          </small>
        </div>
      </div>
    </div>
  </div>
</div>
