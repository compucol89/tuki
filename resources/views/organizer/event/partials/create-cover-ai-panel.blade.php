@if (config('features.event_ai_assistant_enabled', false))
  <div
    class="create-cover-ai-panel d-none mb-4"
    id="event-cover-ai-create"
    data-analysis-url="{{ $temporaryAnalysisUrl }}"
  >
    <div class="alert alert-light border mb-3" data-create-ai-status>
      {{ __('Cuando subas la portada, el asistente puede leer el flyer, proponer mejoras y ayudarte a completar la publicación. Revisás todo antes de aplicarlo.') }}
    </div>

    <div class="create-cover-ai-preferences border rounded p-3 mb-3" data-create-ai-preferences>
      <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start mb-2">
        <div class="pr-lg-3">
          <h5 class="mb-1">{{ __('Armar evento con asistente IA') }}</h5>
          <p class="text-muted mb-0">{{ __('Elegí cómo querés orientar el copy. Esto solo adapta el texto; no limita quién puede reservar o asistir.') }}</p>
        </div>
        <span class="badge badge-light border mt-2 mt-lg-0">{{ __('Revisión humana obligatoria') }}</span>
      </div>

      <div class="row">
        <div class="form-group col-md-6 col-lg-4 mb-2">
          <label class="small mb-1">{{ __('Tono de venta') }}</label>
          <select class="form-control form-control-sm" name="ai_tone" data-create-ai-tone>
            <option value="cercano_rioplatense">{{ __('Cercano y rioplatense') }}</option>
            <option value="directo_vendedor">{{ __('Directo y vendedor') }}</option>
            <option value="energico_festivo" selected>{{ __('Energético y festivo') }}</option>
            <option value="emotivo_inspirador">{{ __('Emotivo e inspirador') }}</option>
            <option value="profesional_institucional">{{ __('Profesional e institucional') }}</option>
            <option value="exclusivo_premium">{{ __('Exclusivo y premium') }}</option>
            <option value="familiar_accesible">{{ __('Familiar y accesible') }}</option>
            <option value="urgencia_responsable">{{ __('Urgencia responsable') }}</option>
          </select>
        </div>
        <div class="form-group col-md-6 col-lg-4 mb-2">
          <label class="small mb-1">{{ __('Intensidad comercial') }}</label>
          <select class="form-control form-control-sm" name="ai_intensity" data-create-ai-intensity>
            <option value="equilibrado" selected>{{ __('Equilibrado') }}</option>
            <option value="informativo">{{ __('Informativo') }}</option>
            <option value="alta_conversion">{{ __('Alta conversión') }}</option>
          </select>
        </div>
        <div class="form-group col-md-6 col-lg-4 mb-2">
          <label class="small mb-1">{{ __('Forma de comunicar') }}</label>
          <select class="form-control form-control-sm" name="ai_language_style" data-create-ai-language-style>
            <option value="automatico" selected>{{ __('Automático según el público') }}</option>
            <option value="es_ar_voseo">{{ __('Argentino con voseo') }}</option>
            <option value="es_latam_tuteo">{{ __('Latino neutro con tuteo') }}</option>
            <option value="es_co_natural">{{ __('Colombiano natural') }}</option>
            <option value="es_ve_natural">{{ __('Venezolano natural') }}</option>
          </select>
        </div>
        <div class="form-group col-md-6 col-lg-4 mb-2">
          <label class="small mb-1">{{ __('Ubicación del público') }}</label>
          <select class="form-control form-control-sm" name="ai_audience_location[]" multiple data-create-ai-audience-location>
            <option value="argentina" selected>{{ __('Argentina') }}</option>
            <option value="caba">{{ __('CABA') }}</option>
            <option value="gran_buenos_aires">{{ __('Gran Buenos Aires') }}</option>
            <option value="provincia_buenos_aires">{{ __('Provincia de Buenos Aires') }}</option>
            <option value="turistas_en_argentina">{{ __('Turistas en Argentina') }}</option>
            <option value="publico_internacional">{{ __('Público internacional') }}</option>
          </select>
        </div>
        <div class="form-group col-md-6 col-lg-4 mb-2">
          <label class="small mb-1">{{ __('Comunidad principal') }}</label>
          <select class="form-control form-control-sm" name="ai_community[]" multiple data-create-ai-community>
            <option value="publico_argentino" selected>{{ __('Público argentino') }}</option>
            <option value="colombianos_en_argentina">{{ __('Colombianos en Argentina') }}</option>
            <option value="venezolanos_en_argentina">{{ __('Venezolanos en Argentina') }}</option>
            <option value="chilenos_en_argentina">{{ __('Chilenos en Argentina') }}</option>
            <option value="peruanos_en_argentina">{{ __('Peruanos en Argentina') }}</option>
            <option value="comunidad_latinoamericana">{{ __('Comunidad latinoamericana') }}</option>
            <option value="publico_mixto">{{ __('Público mixto') }}</option>
          </select>
        </div>
        <div class="form-group col-md-6 col-lg-4 mb-2">
          <label class="small mb-1">{{ __('Rango de edad') }}</label>
          <select class="form-control form-control-sm" name="ai_age_range[]" multiple data-create-ai-age-range>
            <option value="18_24">{{ __('18 a 24') }}</option>
            <option value="25_34" selected>{{ __('25 a 34') }}</option>
            <option value="35_44">{{ __('35 a 44') }}</option>
            <option value="45_54">{{ __('45 a 54') }}</option>
            <option value="publico_adulto">{{ __('Público adulto') }}</option>
            <option value="todas_las_edades">{{ __('Todas las edades') }}</option>
          </select>
        </div>
        <div class="form-group col-lg-6 mb-2">
          <label class="small mb-1">{{ __('Intereses') }}</label>
          <select class="form-control form-control-sm" name="ai_interests[]" multiple data-create-ai-interests>
            <option value="fiestas" selected>{{ __('Fiestas') }}</option>
            <option value="vida_nocturna">{{ __('Vida nocturna') }}</option>
            <option value="reggaeton">{{ __('Reggaetón') }}</option>
            <option value="musica_latina">{{ __('Música latina') }}</option>
            <option value="musica_colombiana">{{ __('Música colombiana') }}</option>
            <option value="cumbia">{{ __('Cumbia') }}</option>
            <option value="salsa">{{ __('Salsa') }}</option>
            <option value="cultura">{{ __('Cultura') }}</option>
            <option value="familias">{{ __('Familias') }}</option>
          </select>
        </div>
        <div class="form-group col-lg-6 mb-2">
          <label class="small mb-1">{{ __('Objetivo principal') }}</label>
          <select class="form-control form-control-sm" name="ai_goal" data-create-ai-goal>
            <option value="reservas_equilibradas" selected>{{ __('Generar reservas con claridad') }}</option>
            <option value="alta_demanda_responsable">{{ __('Impulsar demanda sin falsa urgencia') }}</option>
            <option value="posicionamiento_marca">{{ __('Posicionar el evento o la marca') }}</option>
            <option value="experiencia_premium">{{ __('Comunicar experiencia premium') }}</option>
          </select>
        </div>
        <div class="form-group col-md-6 mb-2">
          <label class="small mb-1">{{ __('Público objetivo adicional') }}</label>
          <textarea class="form-control form-control-sm" name="ai_audience" rows="2" data-create-ai-audience placeholder="{{ __('Ej: jóvenes de CABA, comunidad colombiana, fans del reggaetón viejo...') }}"></textarea>
        </div>
        <div class="form-group col-md-6 mb-2">
          <label class="small mb-1">{{ __('Diferencial o dato fuerte') }}</label>
          <input class="form-control form-control-sm" name="ai_selling_angle" data-create-ai-selling-angle placeholder="{{ __('Ej: entrada gratis hasta cierta hora, cupos reales, artista invitado...') }}">
        </div>
        <div class="form-group col-12 mb-0">
          <label class="small mb-1">{{ __('Notas para la IA') }}</label>
          <textarea class="form-control form-control-sm" name="ai_notes" rows="2" data-create-ai-notes placeholder="{{ __('Promos reales, artistas, ambiente, aclaraciones o datos que no aparecen en el flyer.') }}"></textarea>
        </div>
      </div>
    </div>

    @include('organizer.partials.async-progress', ['progressId' => 'event-create-cover-ai-progress'])

    <div class="create-cover-ai-results d-none mt-3" data-create-ai-results>
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start mb-3">
        <div class="pr-md-3">
          <h5 class="mb-1">{{ __('Propuesta para completar el evento') }}</h5>
          <p class="text-muted mb-0" data-create-ai-summary></p>
        </div>
        <button type="button" class="btn btn-success btn-sm mt-3 mt-md-0" data-create-ai-apply>
          <i class="fas fa-check mr-1"></i>{{ __('Aplicar propuesta seleccionada') }}
        </button>
      </div>

      <div class="create-cover-ai-draft border rounded p-3 mb-3 d-none" data-create-ai-draft>
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start">
          <div class="pr-lg-3">
            <span class="badge badge-primary mb-2">{{ __('Copy + SEO') }}</span>
            <h6 class="mb-1" data-create-ai-draft-title></h6>
            <p class="text-muted mb-2" data-create-ai-draft-summary></p>
          </div>
          <span class="badge badge-light border mb-2 mb-lg-0" data-create-ai-audit></span>
        </div>
        <div class="row mt-2">
          <div class="col-sm-6 col-lg-3">
            <label class="form-check-label small"><input type="checkbox" value="title" data-create-ai-field checked> {{ __('Título') }}</label>
          </div>
          <div class="col-sm-6 col-lg-3">
            <label class="form-check-label small"><input type="checkbox" value="description" data-create-ai-field checked> {{ __('Descripción') }}</label>
          </div>
          <div class="col-sm-6 col-lg-3">
            <label class="form-check-label small"><input type="checkbox" value="meta_description" data-create-ai-field checked> {{ __('Descripción corta para Google') }}</label>
          </div>
          <div class="col-sm-6 col-lg-3">
            <label class="form-check-label small"><input type="checkbox" value="meta_keywords" data-create-ai-field checked> {{ __('Palabras clave') }}</label>
          </div>
        </div>
      </div>

      <h6 class="mb-2">{{ __('Datos detectados en la portada') }}</h6>
      <div class="create-cover-ai-facts border rounded" data-create-ai-facts></div>
      <div class="create-cover-ai-guidance mt-2" data-create-ai-guidance></div>
      <small class="text-muted d-block mt-2">
        {{ __('El asistente propone mejoras y completa datos claros. Revisá todo antes de guardar el evento.') }}
      </small>
    </div>
  </div>
@endif
