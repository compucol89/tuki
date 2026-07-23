@if(config('features.event_ai_assistant_enabled'))
  @php
    $aiApplyUrl = route('organizer.events.ai-assistant.apply', [$event->id, '__DRAFT__']);
  @endphp
  <div
    class="card ai-assistant-card mb-4"
    id="event-ai-assistant"
    data-analysis-url="{{ route('organizer.events.ai-assistant.analysis', $event->id) }}"
    data-status-url="{{ route('organizer.events.ai-assistant.status', $event->id) }}"
    data-draft-url="{{ route('organizer.events.ai-assistant.draft', $event->id) }}"
    data-apply-url="{{ $aiApplyUrl }}"
  >
    <div class="card-body">
      <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start">
        <div class="mb-3 mb-lg-0 pr-lg-3">
          <span class="badge badge-primary mb-2">{{ __('Asistente IA') }}</span>
          <h5 class="mb-1">{{ __('Analizar flyer y crear copy') }}</h5>
          <p class="text-muted mb-0">{{ __('Extrae datos del flyer, marca conflictos y genera descripción, SEO y textos para compartir. Nada se aplica sin confirmación.') }}</p>
        </div>
        <div class="text-lg-right">
          <button type="button" class="btn btn-outline-primary btn-sm" data-ai-action="analysis" {{ empty($event->thumbnail) ? 'disabled' : '' }}>
            <i class="fas fa-search mr-1"></i>{{ __('Analizar flyer') }}
          </button>
          <div class="small text-muted mt-2" data-ai-usage>{{ __('Cargando límite disponible...') }}</div>
        </div>
      </div>

      @if(empty($event->thumbnail))
        <div class="alert alert-warning mt-3 mb-0">{{ __('Subí y guardá una imagen de portada para activar el asistente IA.') }}</div>
      @endif

      <div class="ai-assistant-status alert alert-light border mt-3 mb-0" data-ai-status>
        {{ __('El asistente todavia no se inicio para este evento.') }}
      </div>

      <div class="ai-assistant-results mt-3 d-none" data-ai-results>
        <div class="row">
          <div class="col-lg-7">
            <h6 class="mb-2">{{ __('Datos detectados para revisar') }}</h6>
            <div class="ai-assistant-facts border rounded" data-ai-facts></div>
            <div class="ai-assistant-guidance mt-2" data-ai-guidance></div>
          </div>
          <div class="col-lg-5 mt-3 mt-lg-0">
            <h6 class="mb-2">{{ __('Preferencias del copy') }}</h6>
            <div class="form-group mb-2">
              <label class="small mb-1">{{ __('Tono') }}</label>
              <select class="form-control form-control-sm" data-ai-tone>
                <option value="cercano_rioplatense">{{ __('Cercano y rioplatense') }}</option>
                <option value="directo_vendedor">{{ __('Directo y vendedor') }}</option>
                <option value="energico_festivo">{{ __('Energético y festivo') }}</option>
                <option value="emotivo_inspirador">{{ __('Emotivo e inspirador') }}</option>
                <option value="profesional_institucional">{{ __('Profesional e institucional') }}</option>
                <option value="exclusivo_premium">{{ __('Exclusivo y premium') }}</option>
                <option value="informativo_neutral">{{ __('Informativo y neutral') }}</option>
                <option value="familiar_accesible">{{ __('Familiar y accesible') }}</option>
                <option value="urgencia_responsable">{{ __('Urgencia responsable') }}</option>
              </select>
            </div>
            <div class="form-group mb-2">
              <label class="small mb-1">{{ __('Intensidad comercial') }}</label>
              <select class="form-control form-control-sm" data-ai-intensity>
                <option value="equilibrado">{{ __('Equilibrado') }}</option>
                <option value="informativo">{{ __('Informativo') }}</option>
                <option value="alta_conversion">{{ __('Alta conversión') }}</option>
              </select>
            </div>
            <div class="form-group mb-2">
              <label class="small mb-1">{{ __('Público objetivo') }}</label>
              <textarea class="form-control form-control-sm" rows="2" data-ai-audience placeholder="{{ __('Ej: jóvenes de CABA, familias, profesionales, fans del artista...') }}"></textarea>
            </div>
            <div class="form-group mb-2">
              <label class="small mb-1">{{ __('Objetivo principal') }}</label>
              <select class="form-control form-control-sm" data-ai-goal>
                <option value="reservas_equilibradas">{{ __('Generar reservas con claridad') }}</option>
                <option value="alta_demanda_responsable">{{ __('Impulsar demanda sin falsa urgencia') }}</option>
                <option value="posicionamiento_marca">{{ __('Posicionar el evento o la marca') }}</option>
                <option value="informativo_institucional">{{ __('Informar con tono institucional') }}</option>
                <option value="experiencia_premium">{{ __('Comunicar experiencia premium') }}</option>
              </select>
            </div>
            <div class="form-group mb-2">
              <label class="small mb-1">{{ __('Diferencial o dato clave') }}</label>
              <input type="text" class="form-control form-control-sm" data-ai-selling-angle placeholder="{{ __('Ej: cupos limitados reales, artista invitado, experiencia familiar...') }}">
            </div>
            <div class="form-group mb-2">
              <label class="small mb-1">{{ __('Notas para la IA') }}</label>
              <textarea class="form-control form-control-sm" rows="2" data-ai-notes placeholder="{{ __('Datos confirmados que no aparecen en el flyer o aclaraciones importantes.') }}"></textarea>
            </div>
            <button type="button" class="btn btn-primary btn-sm" data-ai-action="draft" disabled>
              <i class="fas fa-pen-nib mr-1"></i>{{ __('Generar copy y SEO') }}
            </button>
          </div>
        </div>
      </div>

      <div class="ai-assistant-draft mt-3 d-none" data-ai-draft>
        <div class="border rounded p-3">
          <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start">
            <div class="pr-lg-3">
              <h6 class="mb-1" data-ai-draft-title></h6>
              <p class="text-muted mb-2" data-ai-draft-summary></p>
            </div>
            <span class="badge badge-light border mb-2 mb-lg-0" data-ai-audit></span>
          </div>
          <div class="form-row mt-2">
            <div class="col-sm-6 col-lg-3">
              <label class="form-check-label small"><input type="checkbox" value="title" data-ai-field checked> {{ __('Título') }}</label>
            </div>
            <div class="col-sm-6 col-lg-3">
              <label class="form-check-label small"><input type="checkbox" value="description" data-ai-field checked> {{ __('Descripción') }}</label>
            </div>
            <div class="col-sm-6 col-lg-3">
              <label class="form-check-label small"><input type="checkbox" value="meta_description" data-ai-field checked> {{ __('Descripción corta para Google') }}</label>
            </div>
            <div class="col-sm-6 col-lg-3">
              <label class="form-check-label small"><input type="checkbox" value="meta_keywords" data-ai-field checked> {{ __('Tags') }}</label>
            </div>
          </div>
          <button type="button" class="btn btn-success btn-sm mt-3" data-ai-action="apply">
            <i class="fas fa-check mr-1"></i>{{ __('Aplicar campos seleccionados') }}
          </button>
        </div>
      </div>
    </div>
  </div>
@endif
