@php
  $progressId = $progressId ?? 'async-progress';
@endphp

<div
  class="async-progress-panel d-none"
  data-async-progress="{{ $progressId }}"
  role="status"
  aria-live="polite"
  aria-atomic="true"
>
  <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-start mb-2">
    <div class="pr-sm-3">
      <strong data-progress-title>{{ __('Procesando') }}</strong>
      <div class="small text-muted" data-progress-stage>{{ __('Preparando la tarea') }}</div>
    </div>
    <div class="async-progress-panel__percent mt-1 mt-sm-0" data-progress-percent>0%</div>
  </div>

  <div
    class="progress async-progress-panel__bar"
    role="progressbar"
    aria-valuemin="0"
    aria-valuemax="100"
    aria-valuenow="0"
    aria-label="{{ __('Progreso de la tarea') }}"
    data-progressbar
  >
    <div class="progress-bar" data-progress-fill style="width: 0%"></div>
  </div>

  <div class="async-progress-panel__meta mt-2">
    <span data-progress-elapsed>{{ __('Tiempo transcurrido: 0s') }}</span>
    <span class="mx-1">·</span>
    <span data-progress-estimate>{{ __('Normalmente tarda entre 20 segundos y 2 minutos.') }}</span>
  </div>

  <p class="mb-0 mt-2 small" data-progress-message>
    {{ __('No cierres ni recargues esta página mientras termina el proceso.') }}
  </p>
</div>
