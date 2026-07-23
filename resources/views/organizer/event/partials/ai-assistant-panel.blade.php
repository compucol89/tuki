@if(config('features.event_ai_assistant_enabled'))
  @php
    $aiRoutePrefix = $aiRoutePrefix ?? 'organizer.events.ai-assistant';
    $aiApplyUrl = route($aiRoutePrefix . '.apply', [$event->id, '__DRAFT__']);
  @endphp
  <div
    class="card ai-assistant-card mb-4"
    id="event-ai-assistant"
    data-analysis-url="{{ route($aiRoutePrefix . '.analysis', $event->id) }}"
    data-status-url="{{ route($aiRoutePrefix . '.status', $event->id) }}"
    data-draft-url="{{ route($aiRoutePrefix . '.draft', $event->id) }}"
    data-apply-url="{{ $aiApplyUrl }}"
  >
    <div class="card-body">
      <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start">
        <div class="mb-3 mb-lg-0 pr-lg-3">
          <span class="badge badge-primary mb-2">{{ __('Asistente IA') }}</span>
          <h5 class="mb-1">{{ __('Analizar portada y crear copy') }}</h5>
          <p class="text-muted mb-0">{{ __('Lee la portada, complementa los datos del formulario y genera descripción, SEO y textos para compartir. Nada se aplica sin confirmación.') }}</p>
        </div>
        <div class="text-lg-right">
          <button type="button" class="btn btn-outline-primary btn-sm" data-ai-action="analysis" {{ empty($event->thumbnail) ? 'disabled' : '' }}>
            <i class="fas fa-search mr-1"></i>{{ empty($event->thumbnail) ? __('Analizar portada') : __('Analizar portada existente') }}
          </button>
          <div class="small text-muted mt-2" data-ai-usage>{{ __('Cargando límite disponible...') }}</div>
        </div>
      </div>

      @if(empty($event->thumbnail))
        <div class="alert alert-warning mt-3 mb-0">{{ __('Subí y guardá una imagen de portada para activar el asistente IA.') }}</div>
      @else
        <div class="alert alert-info mt-3 mb-0">{{ __('Esta portada se puede analizar sin volver a subirla. Si ya la cambiaste, volvé a analizarla para actualizar las sugerencias.') }}</div>
      @endif

      <div class="ai-assistant-status alert alert-light border mt-3 mb-0" data-ai-status>
        {{ __('El asistente todavia no se inicio para este evento.') }}
      </div>

      @include('organizer.partials.async-progress', ['progressId' => 'event-ai-assistant-progress'])

      <div class="ai-assistant-results mt-3 d-none" data-ai-results>
        <div class="row">
          <div class="col-lg-7">
            <h6 class="mb-2">{{ __('Datos detectados para completar el evento') }}</h6>
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
              <label class="small mb-1">{{ __('Ubicación del público') }}</label>
              <select class="form-control form-control-sm ai-assistant-multiselect" multiple data-ai-audience-location>
                <option value="argentina" selected>{{ __('Argentina') }}</option>
                <option value="caba">{{ __('CABA') }}</option>
                <option value="gran_buenos_aires">{{ __('Gran Buenos Aires') }}</option>
                <option value="provincia_buenos_aires">{{ __('Provincia de Buenos Aires') }}</option>
                <option value="otras_provincias">{{ __('Otras provincias') }}</option>
                <option value="turistas_en_argentina">{{ __('Turistas en Argentina') }}</option>
                <option value="publico_internacional">{{ __('Público internacional') }}</option>
                <option value="personalizado">{{ __('Personalizado') }}</option>
              </select>
            </div>
            <div class="form-group mb-2">
              <label class="small mb-1">{{ __('Comunidad principal') }}</label>
              <select class="form-control form-control-sm ai-assistant-multiselect" multiple data-ai-community>
                <option value="publico_argentino" selected>{{ __('Público argentino') }}</option>
                <option value="colombianos_en_argentina">{{ __('Colombianos en Argentina') }}</option>
                <option value="venezolanos_en_argentina">{{ __('Venezolanos en Argentina') }}</option>
                <option value="chilenos_en_argentina">{{ __('Chilenos en Argentina') }}</option>
                <option value="peruanos_en_argentina">{{ __('Peruanos en Argentina') }}</option>
                <option value="ecuatorianos_en_argentina">{{ __('Ecuatorianos en Argentina') }}</option>
                <option value="bolivianos_en_argentina">{{ __('Bolivianos en Argentina') }}</option>
                <option value="paraguayos_en_argentina">{{ __('Paraguayos en Argentina') }}</option>
                <option value="uruguayos_en_argentina">{{ __('Uruguayos en Argentina') }}</option>
                <option value="brasilenos_en_argentina">{{ __('Brasileños en Argentina') }}</option>
                <option value="comunidad_latinoamericana">{{ __('Comunidad latinoamericana') }}</option>
                <option value="publico_internacional">{{ __('Público internacional') }}</option>
                <option value="publico_mixto">{{ __('Público mixto') }}</option>
                <option value="otra_comunidad">{{ __('Otra comunidad') }}</option>
              </select>
              <small class="text-muted">{{ __('Solo adapta el lenguaje y el enfoque del copy; no limita quién puede reservar o asistir.') }}</small>
            </div>
            <div class="form-row">
              <div class="form-group col-sm-6 mb-2">
                <label class="small mb-1">{{ __('Rango de edad') }}</label>
                <select class="form-control form-control-sm ai-assistant-multiselect" multiple data-ai-age-range>
                  <option value="menores_18">{{ __('Menores de 18') }}</option>
                  <option value="18_24">{{ __('18 a 24') }}</option>
                  <option value="25_34">{{ __('25 a 34') }}</option>
                  <option value="35_44">{{ __('35 a 44') }}</option>
                  <option value="45_54">{{ __('45 a 54') }}</option>
                  <option value="55_plus">{{ __('55+') }}</option>
                  <option value="familias">{{ __('Familias') }}</option>
                  <option value="todas_las_edades">{{ __('Todas las edades') }}</option>
                  <option value="publico_adulto">{{ __('Público adulto') }}</option>
                  <option value="personalizado">{{ __('Personalizado') }}</option>
                </select>
              </div>
              <div class="form-group col-sm-6 mb-2">
                <label class="small mb-1">{{ __('Estilo de comunicación') }}</label>
                <select class="form-control form-control-sm" data-ai-language-style>
                  <option value="automatico">{{ __('Automático según el público') }}</option>
                  <option value="es_ar_voseo">{{ __('Español argentino con voseo') }}</option>
                  <option value="es_latam_tuteo">{{ __('Español latino neutro con tuteo') }}</option>
                  <option value="es_co_natural">{{ __('Español colombiano natural') }}</option>
                  <option value="es_ve_natural">{{ __('Español venezolano natural') }}</option>
                  <option value="es_cl_neutral">{{ __('Español chileno neutral') }}</option>
                  <option value="internacional">{{ __('Comunicación internacional') }}</option>
                  <option value="personalizado">{{ __('Personalizado') }}</option>
                </select>
              </div>
            </div>
            <div class="form-group mb-2">
              <label class="small mb-1">{{ __('Intereses') }}</label>
              <select class="form-control form-control-sm ai-assistant-multiselect" multiple data-ai-interests>
                <option value="reggaeton">{{ __('Reggaetón') }}</option>
                <option value="cumbia">{{ __('Cumbia') }}</option>
                <option value="salsa">{{ __('Salsa') }}</option>
                <option value="musica_colombiana">{{ __('Música colombiana') }}</option>
                <option value="musica_venezolana">{{ __('Música venezolana') }}</option>
                <option value="musica_latina">{{ __('Música latina') }}</option>
                <option value="fiestas">{{ __('Fiestas') }}</option>
                <option value="vida_nocturna">{{ __('Vida nocturna') }}</option>
                <option value="gastronomia">{{ __('Gastronomía') }}</option>
                <option value="deportes">{{ __('Deportes') }}</option>
                <option value="cultura">{{ __('Cultura') }}</option>
                <option value="networking">{{ __('Networking') }}</option>
                <option value="formacion">{{ __('Formación') }}</option>
                <option value="familias">{{ __('Familias') }}</option>
                <option value="turismo">{{ __('Turismo') }}</option>
                <option value="otro">{{ __('Otro') }}</option>
              </select>
            </div>
            <div class="form-group mb-2">
              <label class="small mb-1">{{ __('Público objetivo adicional') }}</label>
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
              <textarea class="form-control form-control-sm" rows="2" data-ai-notes placeholder="{{ __('Datos confirmados que no aparecen en la portada o aclaraciones importantes.') }}"></textarea>
            </div>
            <button type="button" class="btn btn-primary btn-sm" data-ai-action="draft" disabled>
              <i class="fas fa-pen-nib mr-1"></i>{{ __('Generar copy y SEO') }}
            </button>
            <div class="small text-muted mt-2" data-ai-draft-help></div>
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
