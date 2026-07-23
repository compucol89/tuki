@if (config('features.event_ai_assistant_enabled', false))
  <div
    class="create-cover-ai-panel d-none mb-4"
    id="event-cover-ai-create"
    data-analysis-url="{{ $temporaryAnalysisUrl }}"
  >
    <div class="alert alert-light border mb-3" data-create-ai-status>
      {{ __('Cuando subas la portada, la IA puede leer el flyer y ayudarte a completar la informacion principal del evento.') }}
    </div>

    @include('organizer.partials.async-progress', ['progressId' => 'event-create-cover-ai-progress'])

    <div class="create-cover-ai-results d-none mt-3" data-create-ai-results>
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start mb-3">
        <div class="pr-md-3">
          <h5 class="mb-1">{{ __('Datos detectados en la portada') }}</h5>
          <p class="text-muted mb-0" data-create-ai-summary></p>
        </div>
        <button type="button" class="btn btn-success btn-sm mt-3 mt-md-0" data-create-ai-apply>
          <i class="fas fa-check mr-1"></i>{{ __('Aplicar datos al formulario') }}
        </button>
      </div>

      <div class="create-cover-ai-facts border rounded" data-create-ai-facts></div>
      <div class="create-cover-ai-guidance mt-2" data-create-ai-guidance></div>
      <small class="text-muted d-block mt-2">
        {{ __('La IA completa solo campos vacios o claramente detectados. Revisalos antes de guardar el evento.') }}
      </small>
    </div>
  </div>
@endif
