@if(config('features.event_ai_assistant_enabled'))
  @php
    $aiRoutePrefix = $aiRoutePrefix ?? 'organizer.events.ai-assistant';
    $aiApplyUrl = route($aiRoutePrefix . '.apply', [$event->id, '__DRAFT__']);
    $hasCover = !empty($event->thumbnail);
  @endphp
  <div
    class="card ai-assistant-card mb-4"
    id="event-ai-assistant"
    data-analysis-url="{{ route($aiRoutePrefix . '.analysis', $event->id) }}"
    data-status-url="{{ route($aiRoutePrefix . '.status', $event->id) }}"
    data-draft-url="{{ route($aiRoutePrefix . '.draft', $event->id) }}"
    data-apply-url="{{ $aiApplyUrl }}"
    data-has-cover="{{ $hasCover ? '1' : '0' }}"
  >
    <div class="card-body">
      <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start mb-3">
        <div class="pr-lg-3">
          <span class="badge badge-primary mb-2">{{ __('Asistente IA') }}</span>
          <h5 class="mb-1">{{ __('Armar evento con asistente IA') }}</h5>
          <p class="text-muted mb-0">{{ __('La IA lee la portada, cruza esa información con tu criterio y propone título, descripción, SEO, tags y textos para compartir. Nada se aplica sin revisión humana.') }}</p>
        </div>
        <span class="badge badge-light border mt-2 mt-lg-0">{{ __('Revisión humana obligatoria') }}</span>
      </div>

      <div class="ai-assistant-flow" data-ai-flow>
        <div class="ai-assistant-step" data-ai-step="cover">
          <div class="ai-assistant-step__number">1</div>
          <div class="ai-assistant-step__body">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start">
              <div class="pr-lg-3">
                <h6 class="mb-1">{{ __('Portada del evento') }}</h6>
                <p class="text-muted mb-0">
                  {{ $hasCover ? __('Usamos la portada guardada para detectar datos claros del evento.') : __('Subí y guardá una portada para activar el asistente IA.') }}
                </p>
              </div>
              <div class="text-lg-right mt-3 mt-lg-0">
                <button type="button" class="btn btn-outline-primary btn-sm" data-ai-action="analysis" {{ !$hasCover ? 'disabled' : '' }}>
                  <i class="fas fa-search mr-1"></i>{{ $hasCover ? __('Analizar portada existente') : __('Analizar portada') }}
                </button>
                <div class="small text-muted mt-2" data-ai-usage>{{ __('Cargando límite disponible...') }}</div>
              </div>
            </div>
            <div class="ai-assistant-status mt-3" data-ai-status>
              {{ $hasCover ? __('Todavía no analizamos esta portada en esta sesión.') : __('Guardá una portada y volvé para analizarla con IA.') }}
            </div>
          </div>
        </div>

        <div class="ai-assistant-step" data-ai-step="brief">
          <div class="ai-assistant-step__number">2</div>
          <div class="ai-assistant-step__body">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start mb-2">
              <div class="pr-lg-3">
                <h6 class="mb-1">{{ __('Brief del evento') }}</h6>
                <p class="text-muted mb-0">{{ __('Elegí cómo querés orientar el copy. Esto solo adapta el texto; no limita quién puede reservar o asistir.') }}</p>
              </div>
              <button type="button" class="btn btn-light btn-sm mt-2 mt-lg-0" data-ai-skip>
                {{ __('Completar manualmente') }}
              </button>
            </div>

            <div class="create-cover-ai-requirements mb-3" data-ai-readiness>
              <span class="create-cover-ai-requirement" data-ai-requirement="cover"><strong>1</strong>{{ __('Portada lista') }}</span>
              <span class="create-cover-ai-requirement" data-ai-requirement="analysis"><strong>2</strong>{{ __('Análisis listo') }}</span>
              <span class="create-cover-ai-requirement" data-ai-requirement="brief"><strong>3</strong>{{ __('Brief completo') }}</span>
            </div>

            <div class="ai-assistant-manual d-none" data-ai-manual>
              <strong>{{ __('Modo manual activado.') }}</strong>
              <span>{{ __('Podés editar el evento sin IA. Igual recomendamos usarla si querés mejorar descripción, SEO y datos para Google.') }}</span>
              <button type="button" class="btn btn-outline-primary btn-sm mt-2" data-ai-restore>
                {{ __('Volver a usar IA') }}
              </button>
            </div>

            <div class="ai-assistant-brief-fields" data-ai-brief-fields>
              <div class="row">
                <div class="form-group col-md-6 col-lg-4 mb-2">
                  <label class="small mb-1">{{ __('Tono de venta') }}*</label>
                  <select class="form-control form-control-sm" data-ai-tone data-ai-required data-ai-label="{{ __('tono de venta') }}">
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
                <div class="form-group col-md-6 col-lg-4 mb-2">
                  <label class="small mb-1">{{ __('Intensidad comercial') }}*</label>
                  <select class="form-control form-control-sm" data-ai-intensity data-ai-required data-ai-label="{{ __('intensidad comercial') }}">
                    <option value="equilibrado">{{ __('Equilibrado') }}</option>
                    <option value="informativo">{{ __('Informativo') }}</option>
                    <option value="alta_conversion">{{ __('Alta conversión') }}</option>
                  </select>
                </div>
                <div class="form-group col-md-6 col-lg-4 mb-2">
                  <label class="small mb-1">{{ __('Forma de comunicar') }}*</label>
                  <select class="form-control form-control-sm" data-ai-language-style data-ai-required data-ai-label="{{ __('forma de comunicar') }}">
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
                <div class="form-group col-md-6 col-lg-4 mb-2">
                  <label class="small mb-1">{{ __('Ubicación del público') }}*</label>
                  <select class="form-control form-control-sm ai-assistant-multiselect" multiple data-ai-audience-location data-ai-required data-ai-label="{{ __('ubicación del público') }}">
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
                <div class="form-group col-md-6 col-lg-4 mb-2">
                  <label class="small mb-1">{{ __('Comunidad principal') }}*</label>
                  <select class="form-control form-control-sm ai-assistant-multiselect" multiple data-ai-community data-ai-required data-ai-label="{{ __('comunidad principal') }}">
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
                </div>
                <div class="form-group col-md-6 col-lg-4 mb-2">
                  <label class="small mb-1">{{ __('Rango de edad') }}*</label>
                  <select class="form-control form-control-sm ai-assistant-multiselect" multiple data-ai-age-range data-ai-required data-ai-label="{{ __('rango de edad') }}">
                    <option value="menores_18">{{ __('Menores de 18') }}</option>
                    <option value="18_24" selected>{{ __('18 a 24') }}</option>
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
                <div class="form-group col-lg-6 mb-2">
                  <label class="small mb-1">{{ __('Intereses') }}*</label>
                  <select class="form-control form-control-sm ai-assistant-multiselect" multiple data-ai-interests data-ai-required data-ai-label="{{ __('intereses') }}">
                    <option value="fiestas" selected>{{ __('Fiestas') }}</option>
                    <option value="vida_nocturna" selected>{{ __('Vida nocturna') }}</option>
                    <option value="reggaeton">{{ __('Reggaetón') }}</option>
                    <option value="cumbia">{{ __('Cumbia') }}</option>
                    <option value="salsa">{{ __('Salsa') }}</option>
                    <option value="musica_colombiana">{{ __('Música colombiana') }}</option>
                    <option value="musica_venezolana">{{ __('Música venezolana') }}</option>
                    <option value="musica_latina">{{ __('Música latina') }}</option>
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
                <div class="form-group col-lg-6 mb-2">
                  <label class="small mb-1">{{ __('Objetivo principal') }}*</label>
                  <select class="form-control form-control-sm" data-ai-goal data-ai-required data-ai-label="{{ __('objetivo principal') }}">
                    <option value="reservas_equilibradas">{{ __('Generar reservas con claridad') }}</option>
                    <option value="alta_demanda_responsable">{{ __('Impulsar demanda sin falsa urgencia') }}</option>
                    <option value="posicionamiento_marca">{{ __('Posicionar el evento o la marca') }}</option>
                    <option value="informativo_institucional">{{ __('Informar con tono institucional') }}</option>
                    <option value="experiencia_premium">{{ __('Comunicar experiencia premium') }}</option>
                  </select>
                </div>
                <div class="form-group col-12 mb-2">
                  <label class="small mb-1">{{ __('Descripción breve del organizador') }}*</label>
                  <textarea class="form-control form-control-sm" rows="3" data-ai-audience data-ai-required data-ai-label="{{ __('descripción breve') }}" data-ai-min-length="20" placeholder="{{ __('Ej: Esta es una noche de reggaetón viejo en La Troja, con rumba colombiana, mujeres gratis hasta las 2 AM y promos reales en barra.') }}"></textarea>
                  <small class="text-muted">{{ __('Sumá tu idea, detalles importantes, enfoque de venta o copy propio. La IA lo usa junto con la portada y los datos del evento.') }}</small>
                </div>
                <div class="form-group col-md-6 mb-2">
                  <label class="small mb-1">{{ __('Diferencial o dato clave') }}</label>
                  <input type="text" class="form-control form-control-sm" data-ai-selling-angle placeholder="{{ __('Ej: cupos limitados reales, artista invitado, experiencia familiar...') }}">
                </div>
                <div class="form-group col-md-6 mb-2">
                  <label class="small mb-1">{{ __('Notas para la IA') }}</label>
                  <textarea class="form-control form-control-sm" rows="2" data-ai-notes placeholder="{{ __('Datos confirmados que no aparecen en la portada o aclaraciones importantes.') }}"></textarea>
                </div>
              </div>
            </div>

            <div class="create-cover-ai-brief-summary d-none" data-ai-brief-summary>
              <div>
                <strong>{{ __('Brief listo para esta propuesta') }}</strong>
                <span data-ai-brief-summary-text>{{ __('La IA ya usó tus preferencias y la descripción breve.') }}</span>
              </div>
              <button type="button" class="btn btn-outline-primary btn-sm" data-ai-edit-brief>
                {{ __('Editar brief') }}
              </button>
            </div>

            <div class="create-cover-ai-actionbar mt-3">
              <div class="small text-muted" data-ai-readiness-text>
                {{ __('Analizá la portada y completá el brief para generar copy y SEO.') }}
              </div>
              <button type="button" class="btn btn-primary btn-sm" data-ai-action="draft" disabled>
                <i class="fas fa-pen-nib mr-1"></i>{{ __('Generar copy y SEO') }}
              </button>
            </div>
            <div class="small text-muted mt-2" data-ai-draft-help></div>
          </div>
        </div>

        @include('organizer.partials.async-progress', ['progressId' => 'event-ai-assistant-progress'])

        <div class="ai-assistant-step ai-assistant-results d-none" data-ai-results data-ai-step="proposal">
          <div class="ai-assistant-step__number">3</div>
          <div class="ai-assistant-step__body">
            <h6 class="mb-2">{{ __('Propuesta IA para revisar') }}</h6>
            <div class="ai-assistant-draft d-none" data-ai-draft>
              <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start">
                <div class="pr-lg-3">
                  <span class="badge badge-primary mb-2">{{ __('Copy + SEO') }}</span>
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

            <div class="mt-3">
              <h6 class="mb-2">{{ __('Datos detectados en la portada') }}</h6>
              <div class="ai-assistant-facts border rounded" data-ai-facts></div>
              <div class="ai-assistant-guidance mt-2" data-ai-guidance></div>
              <small class="text-muted d-block mt-2">{{ __('El asistente propone mejoras y completa datos claros. Revisá todo antes de guardar el evento.') }}</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endif
