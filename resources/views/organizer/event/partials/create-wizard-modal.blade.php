@php
  $eventType = in_array(request()->input('type'), ['venue', 'online'], true) ? request()->input('type') : 'venue';
  $defaultCode = $defaultLang->code;
  $defaultId = $defaultLang->id;
  $singleLanguageMode = isset($languages) && count($languages) === 1;
  $aiEnabled = config('features.event_ai_assistant_enabled', false);
  $nonDefaultLanguages = $languages->filter(function ($language) {
    return $language->is_default != 1;
  });
@endphp

<div class="modal fade event-wizard-modal" id="createEventWizard" tabindex="-1" role="dialog"
  aria-modal="true" aria-labelledby="createEventWizardTitle">
  <div class="modal-dialog modal-dialog-scrollable" role="document">
    <div class="modal-content">

      <div class="modal-header event-wizard__header">
        <div class="event-wizard__titlebar">
          <div class="event-wizard__titlebox">
            <span class="event-wizard__eyebrow">
              <i class="fas fa-calendar-plus"></i>
              <span>{{ __('Nuevo evento') }}</span>
              <span>{{ $eventType == 'venue' ? __('Presencial') : __('Online') }}</span>
            </span>
            <h2 class="event-wizard__title" id="createEventWizardTitle">{{ __('Creá tu evento paso a paso') }}</h2>
            <p class="event-wizard__subtitle" id="createEventWizardSubtitle">{{ __('Te guiamos con todo: portada, copy, entradas y publicación.') }}</p>
          </div>
          <button type="button" class="event-wizard__close" aria-label="{{ __('Cerrar asistente') }}">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <ol class="event-wizard-stepper" role="list">
          <li class="event-wizard-stepper__item is-active" data-wizard-step="1">
            <button type="button" class="event-wizard-stepper__btn" data-wizard-go="1" aria-current="step">
              <span class="event-wizard-stepper__node"><i class="fas fa-image"></i></span>
              <span class="event-wizard-stepper__label">{{ __('Portada') }}</span>
            </button>
            <span class="event-wizard-stepper__connector" aria-hidden="true"></span>
          </li>
          <li class="event-wizard-stepper__item" data-wizard-step="2">
            <button type="button" class="event-wizard-stepper__btn" data-wizard-go="2" disabled>
              <span class="event-wizard-stepper__node"><i class="fas fa-magic"></i></span>
              <span class="event-wizard-stepper__label">{{ __('Copy con IA') }}</span>
            </button>
            <span class="event-wizard-stepper__connector" aria-hidden="true"></span>
          </li>
          <li class="event-wizard-stepper__item" data-wizard-step="3">
            <button type="button" class="event-wizard-stepper__btn" data-wizard-go="3" disabled>
              <span class="event-wizard-stepper__node"><i class="fas fa-ticket-alt"></i></span>
              <span class="event-wizard-stepper__label">{{ __('Entradas') }}</span>
            </button>
            <span class="event-wizard-stepper__connector" aria-hidden="true"></span>
          </li>
          <li class="event-wizard-stepper__item" data-wizard-step="4">
            <button type="button" class="event-wizard-stepper__btn" data-wizard-go="4" disabled>
              <span class="event-wizard-stepper__node"><i class="fas fa-map-marker-alt"></i></span>
              <span class="event-wizard-stepper__label">{{ $eventType == 'venue' ? __('Ubicación y fotos') : __('Fotos') }}</span>
            </button>
            <span class="event-wizard-stepper__connector" aria-hidden="true"></span>
          </li>
          <li class="event-wizard-stepper__item" data-wizard-step="5">
            <button type="button" class="event-wizard-stepper__btn" data-wizard-go="5" disabled>
              <span class="event-wizard-stepper__node"><i class="fas fa-rocket"></i></span>
              <span class="event-wizard-stepper__label">{{ __('Publicar') }}</span>
            </button>
            <span class="event-wizard-stepper__connector" aria-hidden="true"></span>
          </li>
          <li class="event-wizard-stepper__item" data-wizard-step="6">
            <button type="button" class="event-wizard-stepper__btn" data-wizard-go="6" disabled>
              <span class="event-wizard-stepper__node"><i class="fas fa-sliders-h"></i></span>
              <span class="event-wizard-stepper__label">
                {{ __('Avanzado') }}
                <span class="event-wizard-stepper__opt">{{ __('opcional') }}</span>
              </span>
            </button>
          </li>
        </ol>
      </div>

      <div class="modal-body event-wizard__body">
        <div class="event-wizard__errors">
          <div class="alert alert-danger pb-1 dis-none" id="eventErrors" role="alert" aria-live="assertive">
            <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('Cerrar aviso de errores') }}">×</button>
            <ul></ul>
          </div>
        </div>

        <form id="eventForm" action="{{ route('organizer.event_management.store_event') }}" method="POST"
          enctype="multipart/form-data" data-currency="{{ $getCurrencyInfo->base_currency_text }}">
          @csrf
          <input type="hidden" name="event_type" value="{{ $eventType }}">

          {{-- ============ PASO 1 · PORTADA ============ --}}
          <section class="event-wizard-step is-active" data-wizard-panel="1">
            <div class="event-wizard-step__head">
              <span class="event-wizard-step__kicker">{{ __('Paso 1 · Portada') }}</span>
              <h3 class="event-wizard-step__title">{{ __('Subí tu imagen y contanos lo básico') }}</h3>
              <p class="event-wizard-step__text">
                {{ __('La imagen es lo primero que va a ver tu público. Subila y dejá que la IA lea el flyer para completar título, fecha y más por vos.') }}
              </p>
            </div>

            <div class="ew-step1-grid">
              <div class="event-cover-box">
                <div class="thumb-preview event-cover-box__preview">
                  <img src="{{ asset('assets/admin/img/noimage.jpg') }}" alt="{{ __('Vista previa de la portada') }}" class="uploaded-img">
                </div>
                <div class="event-cover-box__actions">
                  <label class="event-cover-box__upload" role="button">
                    <span class="event-cover-box__upload-icon">
                      <i class="fas fa-cloud-upload-alt"></i>
                    </span>
                    <span class="event-cover-box__upload-copy">
                      <strong>{{ __('Elegir imagen de portada') }}</strong>
                      <small>{{ __('JPG, PNG o WebP. Tu flyer o afiche funciona perfecto.') }}</small>
                    </span>
                    <input type="file" class="img-input" name="thumbnail" accept="image/jpeg,image/png,image/webp">
                  </label>
                  <small class="event-cover-box__hint">{{ __('Si tu flyer tiene texto, la IA puede leerlo y completar los datos por vos.') }}</small>

                  <div class="event-cover-box__empty" data-cover-ai-empty>
                    <strong>{{ __('Subí una portada para empezar.') }}</strong>
                    <span>{{ __('Después vas a poder extraer los datos con IA.') }}</span>
                  </div>
                  <div class="event-cover-box__ai d-none" data-cover-ai-ready>
                    <div class="event-cover-box__state">
                      <i class="fas fa-check-circle"></i>
                      <div>
                        <strong>{{ __('Imagen de portada cargada correctamente.') }}</strong>
                        <span>{{ __('Podemos leer el flyer y ayudarte a completar título, fecha, lugar y descripción.') }}</span>
                      </div>
                    </div>
                    <div class="event-cover-box__manual d-none" data-cover-ai-manual>
                      <strong>{{ __('Modo manual activado.') }}</strong>
                      <span>{{ __('Podés completar el evento sin IA.') }}</span>
                      <button type="button" class="btn btn-outline-primary btn-sm mt-2" data-cover-ai-restore>
                        {{ __('Volver a usar IA') }}
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="ew-ai-extract-area" data-ew-analysis-url="{{ route('organizer.events.ai-assistant.temporary_cover_analysis') }}">
                <div class="row">
                  <div class="col-lg-8">
                    <div class="form-group">
                      <label for="{{ $defaultCode }}_title">{{ __('Título del evento') . '*' }}</label>
                      <input type="text" class="form-control" id="{{ $defaultCode }}_title" name="{{ $defaultCode }}_title" data-ew-gate="title"
                        placeholder="{{ __('Ej: Fiesta de música en vivo en Buenos Aires') }}">
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="form-group">
                      @php
                        $categories = DB::table('event_categories')
                            ->where('language_id', $defaultId)
                            ->where('status', 1)
                            ->orderBy('serial_number', 'asc')
                            ->get();
                      @endphp
                      <label for="{{ $defaultCode }}_category_id">{{ __('Categoría') . '*' }}</label>
                      <select name="{{ $defaultCode }}_category_id" id="{{ $defaultCode }}_category_id" class="form-control" data-ew-gate="category">
                        <option selected disabled>{{ __('Seleccioná una categoría') }}</option>
                        @foreach ($categories as $category)
                          <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-12">
                    <div class="form-group mt-1">
                      <label>{{ __('Tipo de fecha') . '*' }}</label>
                      <div class="selectgroup w-100" role="radiogroup" aria-label="{{ __('Tipo de fecha') }}">
                        <label class="selectgroup-item">
                          <input type="radio" name="date_type" value="single" class="selectgroup-input eventDateType" checked>
                          <span class="selectgroup-button">{{ __('Fecha única') }}</span>
                        </label>
                        <label class="selectgroup-item">
                          <input type="radio" name="date_type" value="multiple" class="selectgroup-input eventDateType">
                          <span class="selectgroup-button">{{ __('Varias fechas') }}</span>
                        </label>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row countDownStatus">
                  <div class="col-lg-12">
                    <div class="form-group mt-1">
                      <label>{{ __('Contador regresivo') . '*' }}</label>
                      <div class="selectgroup w-100" role="radiogroup" aria-label="{{ __('Contador regresivo') }}">
                        <label class="selectgroup-item">
                          <input type="radio" name="countdown_status" value="1" class="selectgroup-input" checked>
                          <span class="selectgroup-button">{{ __('Activo') }}</span>
                        </label>
                        <label class="selectgroup-item">
                          <input type="radio" name="countdown_status" value="0" class="selectgroup-input">
                          <span class="selectgroup-button">{{ __('Inactivo') }}</span>
                        </label>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row" id="single_dates">
                  <div class="col-lg-3 col-md-6">
                    <div class="form-group">
                      <label for="start_date">{{ __('Fecha de inicio') . '*' }}</label>
                      <input type="date" name="start_date" id="start_date" class="form-control" data-ew-gate="dates">
                    </div>
                  </div>
                  <div class="col-lg-3 col-md-6">
                    <div class="form-group">
                      <label for="start_time">{{ __('Hora de inicio') . '*' }}</label>
                      <input type="time" name="start_time" id="start_time" class="form-control" data-ew-gate="dates">
                    </div>
                  </div>
                  <div class="col-lg-3 col-md-6">
                    <div class="form-group">
                      <label for="end_date">{{ __('Fecha de fin') . '*' }}</label>
                      <input type="date" name="end_date" id="end_date" class="form-control" data-ew-gate="dates">
                    </div>
                  </div>
                  <div class="col-lg-3 col-md-6">
                    <div class="form-group">
                      <label for="end_time">{{ __('Hora de fin') . '*' }}</label>
                      <input type="time" name="end_time" id="end_time" class="form-control" data-ew-gate="dates">
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-12 d-none" id="multiple_dates">
                    <div class="form-group">
                      <table class="table table-bordered">
                        <thead>
                          <tr>
                            <th>{{ __('Fecha inicio') }}</th>
                            <th>{{ __('Hora inicio') }}</th>
                            <th>{{ __('Fecha fin') }}</th>
                            <th>{{ __('Hora fin') }}</th>
                            <th><button type="button" class="btn btn-success addDateRow" aria-label="{{ __('Agregar fecha') }}"><i class="fas fa-plus-circle"></i></button></th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>
                              <div class="form-group">
                                <input type="date" name="m_start_date[]" class="form-control">
                              </div>
                            </td>
                            <td>
                              <div class="form-group">
                                <input type="time" name="m_start_time[]" class="form-control">
                              </div>
                            </td>
                            <td>
                              <div class="form-group">
                                <input type="date" name="m_end_date[]" class="form-control">
                              </div>
                            </td>
                            <td>
                              <div class="form-group">
                                <input type="time" name="m_end_time[]" class="form-control">
                              </div>
                            </td>
                            <td>
                              <button type="button" class="btn btn-danger deleteDateRow" aria-label="{{ __('Quitar fecha') }}"><i class="fas fa-minus"></i></button>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>

                @if ($aiEnabled)
                  <div class="ew-ai-extract-block mt-3">
                    <button type="button" class="ew-ai-extract" id="ewAiExtractBtn" disabled>
                      <i class="fas fa-robot"></i>
                      <span data-ew-extract-label>{{ __('Extraer datos de la imagen con IA') }}</span>
                    </button>
                    <p class="text-muted small mt-2 mb-0" data-ew-extract-status aria-live="polite">
                      {{ __('Subí la imagen para que la IA lea el flyer y complete título, fecha y horarios.') }}
                    </p>
                    <div class="ew-ai-facts d-none mt-3" data-ew-facts>
                      <div class="ew-ai-facts__head">
                        <strong>{{ __('Datos detectados en la imagen') }}</strong>
                        <button type="button" class="btn btn-sm btn-success" data-ew-facts-apply>
                          <i class="fas fa-check mr-1"></i>{{ __('Aplicar datos') }}
                        </button>
                      </div>
                      <div class="ew-ai-facts__list" data-ew-facts-list></div>
                    </div>
                  </div>
                @endif
              </div>
            </div>
          </section>

          {{-- ============ PASO 2 · COPY CON IA ============ --}}
          <section class="event-wizard-step" data-wizard-panel="2">
            <div class="event-wizard-step__head">
              <span class="event-wizard-step__kicker">{{ __('Paso 2 · Copy con IA') }}</span>
              <h3 class="event-wizard-step__title">{{ __('Armá la descripción que vende') }}</h3>
              <p class="event-wizard-step__text">
                {{ __('Contale a la IA cómo querés comunicar y generá un copy completo: descripción, palabras clave y texto para Google. Después lo editás a tu gusto.') }}
              </p>
            </div>

            @include('organizer.event.partials.create-cover-ai-panel', [
              'temporaryAnalysisUrl' => route('organizer.events.ai-assistant.temporary_cover_analysis'),
            ])

            <div class="ew-copy-editor">
              <div class="ew-copy-editor__head">
                <i class="fas fa-pen-fancy text-primary"></i>
                <h5>{{ __('Tu descripción del evento') }}</h5>
              </div>
              <p class="ew-copy-editor__hint mb-3">
                {{ __('Generala con IA o escribila vos. Contá qué incluye la entrada, horarios, artistas y datos importantes para decidir la compra.') }}
              </p>
              <div class="form-group">
                <textarea id="descriptionTmce{{ $defaultId }}" class="form-control summernote"
                  name="{{ $defaultCode }}_description" data-height="300" data-ew-gate="description"
                  placeholder="{{ __('Contá de qué se trata el evento, qué incluye la entrada, horarios, accesos y datos importantes.') }}"></textarea>
              </div>

              <div class="row">
                <div class="col-lg-12">
                  @include('partials.event-canonical-refund-policy')
                </div>
              </div>

              <div class="row">
                <div class="col-lg-12">
                  <div class="form-group">
                    <label for="{{ $defaultCode }}_meta_keywords">{{ __('Palabras clave para Google') }}</label>
                    <input class="form-control" id="{{ $defaultCode }}_meta_keywords" name="{{ $defaultCode }}_meta_keywords"
                      placeholder="{{ __('Ej: festival, buenos aires, música en vivo') }}" data-role="tagsinput">
                  </div>
                </div>
                <div class="col-lg-12">
                  <div class="form-group">
                    <label for="{{ $defaultCode }}_meta_description">{{ __('Descripción corta para Google') }}</label>
                    <textarea class="form-control" id="{{ $defaultCode }}_meta_description" name="{{ $defaultCode }}_meta_description" rows="3"
                      placeholder="{{ __('Una descripción breve y clara para buscadores y enlaces compartidos.') }}"></textarea>
                  </div>
                </div>
              </div>
            </div>
          </section>

          {{-- ============ PASO 3 · ENTRADAS ============ --}}
          <section class="event-wizard-step" data-wizard-panel="3">
            <div class="event-wizard-step__head">
              <span class="event-wizard-step__kicker">{{ __('Paso 3 · Entradas') }}</span>
              <h3 class="event-wizard-step__title">{{ __('Configurá la venta de entradas') }}</h3>
              <p class="event-wizard-step__text">
                {{ __('Definí precio, disponibilidad y descuentos de una vez. Después vas a poder agregar más tipos de entrada desde la página del evento.') }}
              </p>
            </div>

            @if ($eventType == 'online')
              <div class="row">
                <div class="col-lg-6">
                  <div class="form-group mt-1">
                    <label>{{ __('Disponibilidad total de entradas') . '*' }}</label>
                    <div class="selectgroup w-100" role="radiogroup" aria-label="{{ __('Disponibilidad total de entradas') }}">
                      <label class="selectgroup-item">
                        <input type="radio" name="ticket_available_type" value="unlimited" class="selectgroup-input" checked>
                        <span class="selectgroup-button">{{ __('Sin límite') }}</span>
                      </label>
                      <label class="selectgroup-item">
                        <input type="radio" name="ticket_available_type" value="limited" class="selectgroup-input">
                        <span class="selectgroup-button">{{ __('Con límite') }}</span>
                      </label>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6 d-none" id="ticket_available">
                  <div class="form-group">
                    <label for="ticket_available_qty">{{ __('Cantidad total disponible') . '*' }}</label>
                    <input type="number" name="ticket_available" id="ticket_available_qty" placeholder="{{ __('Ej: 500') }}" class="form-control">
                  </div>
                </div>
                @if ($websiteInfo->event_guest_checkout_status != 1)
                  <div class="col-lg-6">
                    <div class="form-group mt-1">
                      <label>{{ __('Límite por comprador') . '*' }}</label>
                      <div class="selectgroup w-100" role="radiogroup" aria-label="{{ __('Límite por comprador') }}">
                        <label class="selectgroup-item">
                          <input type="radio" name="max_ticket_buy_type" value="unlimited" class="selectgroup-input" checked>
                          <span class="selectgroup-button">{{ __('Sin límite') }}</span>
                        </label>
                        <label class="selectgroup-item">
                          <input type="radio" name="max_ticket_buy_type" value="limited" class="selectgroup-input">
                          <span class="selectgroup-button">{{ __('Con límite') }}</span>
                        </label>
                      </div>
                    </div>
                  </div>
                @else
                  <input type="hidden" name="max_ticket_buy_type" value="unlimited">
                @endif
                <div class="col-lg-6 d-none" id="max_buy_ticket">
                  <div class="form-group">
                    <label for="max_buy_ticket_qty">{{ __('Cantidad máxima por comprador') . '*' }}</label>
                    <input type="number" name="max_buy_ticket" id="max_buy_ticket_qty" placeholder="{{ __('Ej: 4') }}" class="form-control">
                  </div>
                </div>

                <div class="col-lg-4">
                  <div class="form-group">
                    <label for="ticket-pricing">{{ __('Precio de la entrada') }} ({{ $getCurrencyInfo->base_currency_text }}) *</label>
                    <input type="number" name="price" id="ticket-pricing" class="form-control" placeholder="{{ __('Ej: 12000') }}" data-ew-gate="price">
                  </div>
                  <div class="form-group">
                    <input type="checkbox" name="pricing_type" value="free" class="" id="free_ticket">
                    <label for="free_ticket">{{ __('Este evento es gratuito') }}</label>
                  </div>
                </div>
                <div class="col-lg-8">
                  <div class="form-group">
                    <label for="meeting_url">{{ __('Enlace de acceso o meeting URL') }} *</label>
                    <input type="text" name="meeting_url" id="meeting_url" class="form-control" data-ew-gate="meeting"
                      placeholder="{{ __('Ej: enlace de Zoom, Meet o plataforma de acceso') }}">
                  </div>
                </div>
              </div>

              <div class="row" id="early_bird_discount_free">
                <div class="col-lg-12">
                  <div class="form-group mt-1">
                    <label>{{ __('Descuento anticipado') . '*' }}</label>
                    <div class="selectgroup w-100" role="radiogroup" aria-label="{{ __('Descuento anticipado') }}">
                      <label class="selectgroup-item">
                        <input type="radio" name="early_bird_discount_type" value="disable" class="selectgroup-input" checked>
                        <span class="selectgroup-button">{{ __('No usar') }}</span>
                      </label>
                      <label class="selectgroup-item">
                        <input type="radio" name="early_bird_discount_type" value="enable" class="selectgroup-input">
                        <span class="selectgroup-button">{{ __('Usar') }}</span>
                      </label>
                    </div>
                  </div>
                </div>
                <div class="col-lg-12 d-none" id="early_bird_dicount">
                  <div class="row">
                    <div class="col-lg-3 col-md-6">
                      <div class="form-group">
                        <label for="discount_type">{{ __('Descuento') }} *</label>
                        <select name="discount_type" id="discount_type" class="form-control">
                          <option disabled>{{ __('Seleccioná el tipo de descuento') }}</option>
                          <option value="fixed">{{ __('Monto fijo') }}</option>
                          <option value="percentage">{{ __('Porcentaje') }}</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                      <div class="form-group">
                        <label for="early_bird_discount_amount">{{ __('Importe') }} *</label>
                        <input type="number" name="early_bird_discount_amount" id="early_bird_discount_amount" class="form-control">
                      </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                      <div class="form-group">
                        <label for="early_bird_discount_date">{{ __('Fecha límite del descuento') }} *</label>
                        <input type="date" name="early_bird_discount_date" id="early_bird_discount_date" class="form-control">
                      </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                      <div class="form-group">
                        <label for="early_bird_discount_time">{{ __('Hora límite del descuento') }} *</label>
                        <input type="time" name="early_bird_discount_time" id="early_bird_discount_time" class="form-control">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            @else
              <div class="ew-ticket-toggle">
                <div class="custom-control custom-switch">
                  <input type="checkbox" class="custom-control-input" id="ewVenueTicketToggle" checked>
                  <label class="custom-control-label" for="ewVenueTicketToggle">{{ __('Configurar la venta de entradas ahora') }}</label>
                </div>
                <div class="ew-ticket-toggle__copy">
                  <strong>{{ __('Dejalo activado para vender desde el primer día') }}</strong>
                  <span>{{ __('Podés desactivarlo y cargar las entradas más tarde desde la página del evento.') }}</span>
                </div>
              </div>

              <div class="js-venue-ticket-fields">
                <div class="row">
                  <div class="col-lg-6">
                    <div class="form-group mt-1">
                      <label>{{ __('Disponibilidad total de entradas') . '*' }}</label>
                      <div class="selectgroup w-100" role="radiogroup" aria-label="{{ __('Disponibilidad total de entradas') }}">
                        <label class="selectgroup-item">
                          <input type="radio" name="ticket_available_type" value="unlimited" class="selectgroup-input" checked>
                          <span class="selectgroup-button">{{ __('Sin límite') }}</span>
                        </label>
                        <label class="selectgroup-item">
                          <input type="radio" name="ticket_available_type" value="limited" class="selectgroup-input">
                          <span class="selectgroup-button">{{ __('Con límite') }}</span>
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6 d-none" id="ticket_available">
                    <div class="form-group">
                      <label for="ticket_available_qty">{{ __('Cantidad total disponible') . '*' }}</label>
                      <input type="number" name="ticket_available" id="ticket_available_qty" placeholder="{{ __('Ej: 500') }}" class="form-control">
                    </div>
                  </div>
                  @if ($websiteInfo->event_guest_checkout_status != 1)
                    <div class="col-lg-6">
                      <div class="form-group mt-1">
                        <label>{{ __('Límite por comprador') . '*' }}</label>
                        <div class="selectgroup w-100" role="radiogroup" aria-label="{{ __('Límite por comprador') }}">
                          <label class="selectgroup-item">
                            <input type="radio" name="max_ticket_buy_type" value="unlimited" class="selectgroup-input" checked>
                            <span class="selectgroup-button">{{ __('Sin límite') }}</span>
                          </label>
                          <label class="selectgroup-item">
                            <input type="radio" name="max_ticket_buy_type" value="limited" class="selectgroup-input">
                            <span class="selectgroup-button">{{ __('Con límite') }}</span>
                          </label>
                        </div>
                      </div>
                    </div>
                  @else
                    <input type="hidden" name="max_ticket_buy_type" value="unlimited">
                  @endif
                  <div class="col-lg-6 d-none" id="max_buy_ticket">
                    <div class="form-group">
                      <label for="max_buy_ticket_qty">{{ __('Cantidad máxima por comprador') . '*' }}</label>
                      <input type="number" name="max_buy_ticket" id="max_buy_ticket_qty" placeholder="{{ __('Ej: 4') }}" class="form-control">
                    </div>
                  </div>

                  <div class="col-lg-4">
                    <div class="form-group">
                      <label for="ticket-pricing">{{ __('Precio de la entrada') }} ({{ $getCurrencyInfo->base_currency_text }}) *</label>
                      <input type="number" name="price" id="ticket-pricing" class="form-control" placeholder="{{ __('Ej: 12000') }}" data-ew-gate="price">
                    </div>
                    <div class="form-group">
                      <input type="checkbox" name="pricing_type" value="free" class="" id="free_ticket">
                      <label for="free_ticket">{{ __('Este evento es gratuito') }}</label>
                    </div>
                  </div>
                  <div class="col-lg-8">
                    <div class="form-group mt-1">
                      <div class="ew-review-note mb-0" style="border-left-color: var(--adm-primary);">
                        <i class="fas fa-lightbulb"></i>
                        <span>{{ __('Esta es la entrada general. Más adelante vas a poder crear zonas, categorías y variaciones de precio desde «Entradas» en la página del evento.') }}</span>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row" id="early_bird_discount_free">
                  <div class="col-lg-12">
                    <div class="form-group mt-1">
                      <label>{{ __('Descuento anticipado') . '*' }}</label>
                      <div class="selectgroup w-100" role="radiogroup" aria-label="{{ __('Descuento anticipado') }}">
                        <label class="selectgroup-item">
                          <input type="radio" name="early_bird_discount_type" value="disable" class="selectgroup-input" checked>
                          <span class="selectgroup-button">{{ __('No usar') }}</span>
                        </label>
                        <label class="selectgroup-item">
                          <input type="radio" name="early_bird_discount_type" value="enable" class="selectgroup-input">
                          <span class="selectgroup-button">{{ __('Usar') }}</span>
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-12 d-none" id="early_bird_dicount">
                    <div class="row">
                      <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                          <label for="discount_type">{{ __('Descuento') }} *</label>
                          <select name="discount_type" id="discount_type" class="form-control">
                            <option disabled>{{ __('Seleccioná el tipo de descuento') }}</option>
                            <option value="fixed">{{ __('Monto fijo') }}</option>
                            <option value="percentage">{{ __('Porcentaje') }}</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                          <label for="early_bird_discount_amount">{{ __('Importe') }} *</label>
                          <input type="number" name="early_bird_discount_amount" id="early_bird_discount_amount" class="form-control">
                        </div>
                      </div>
                      <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                          <label for="early_bird_discount_date">{{ __('Fecha límite del descuento') }} *</label>
                          <input type="date" name="early_bird_discount_date" id="early_bird_discount_date" class="form-control">
                        </div>
                      </div>
                      <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                          <label for="early_bird_discount_time">{{ __('Hora límite del descuento') }} *</label>
                          <input type="time" name="early_bird_discount_time" id="early_bird_discount_time" class="form-control">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            @endif
          </section>

          {{-- ============ PASO 4 · UBICACIÓN Y FOTOS ============ --}}
          <section class="event-wizard-step" data-wizard-panel="4">
            <div class="event-wizard-step__head">
              <span class="event-wizard-step__kicker">{{ __('Paso 4 · ' . ($eventType == 'venue' ? 'Ubicación y fotos' : 'Fotos')) }}</span>
              <h3 class="event-wizard-step__title">{{ $eventType == 'venue' ? __('¿Dónde es tu evento?') : __('Sumá fotos del evento') }}</h3>
              <p class="event-wizard-step__text">
                @if ($eventType == 'venue')
                  {{ __('Completá la dirección y la ubicamos en el mapa. Después sumá fotos del lugar, artistas o ediciones anteriores.') }}
                @else
                  {{ __('Agregá fotos del evento, artistas o ediciones anteriores. No reemplazan la portada, pero ayudan a mostrar la experiencia.') }}
                @endif
              </p>
            </div>

            @if ($eventType == 'venue')
              <div class="ew-location-grid">
                <div>
                  <div class="form-group">
                    <label for="{{ $defaultCode }}_address">{{ __('Dirección') . '*' }}</label>
                    <input type="text" name="{{ $defaultCode }}_address" id="{{ $defaultCode }}_address" class="form-control" data-ew-gate="address"
                      placeholder="{{ __('Ej: Av. Corrientes 1234') }}">
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="{{ $defaultCode }}_country">{{ __('País') . '*' }}</label>
                        <input type="text" name="{{ $defaultCode }}_country" id="{{ $defaultCode }}_country" placeholder="{{ __('Ej: Argentina') }}" class="form-control" data-ew-gate="address">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="{{ $defaultCode }}_state">{{ __('Provincia') }}</label>
                        <input type="text" name="{{ $defaultCode }}_state" id="{{ $defaultCode }}_state" class="form-control" placeholder="{{ __('Ej: Buenos Aires') }}">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="{{ $defaultCode }}_city">{{ __('Ciudad') . '*' }}</label>
                        <input type="text" name="{{ $defaultCode }}_city" id="{{ $defaultCode }}_city" class="form-control" data-ew-gate="address"
                          placeholder="{{ __('Ej: CABA') }}">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="{{ $defaultCode }}_zip_code">{{ __('Código postal') }}</label>
                        <input type="text" id="{{ $defaultCode }}_zip_code" placeholder="{{ __('Ej: C1043') }}" name="{{ $defaultCode }}_zip_code" class="form-control">
                      </div>
                    </div>
                  </div>
                </div>
                <div>
                  @include('partials.event-venue-location', [
                    'mapId' => 'eventVenueMapCreateOrganizer',
                    'languages' => $languages,
                    'geocodeUrl' => route('organizer.event.venue_geocode'),
                  ])
                </div>
              </div>
            @endif

            <div class="event-gallery-secondary event-gallery-secondary--inline mb-4">
              <div class="event-gallery-secondary__header">
                <span>{{ __('Opcional') }}</span>
                <h4>{{ __('Imágenes adicionales') }}</h4>
                <p>{{ __('Agregá fotos complementarias del ambiente, artistas, lugar o ediciones anteriores. No reemplazan la portada.') }}</p>
              </div>
              <div id="my-dropzone" class="dropzone create">
                <div class="fallback">
                  <input name="file" type="file" multiple />
                </div>
              </div>
              <div class="mb-0" id="errpreimg"></div>
              <p class="text-muted small mt-2 mb-0">{{ __('JPG, PNG o WebP. Mínimo aceptado: 600x450. Recomendado: 1170x570 o más para mejor calidad.') }}</p>
            </div>

            <div id="sliders"></div>
          </section>

          {{-- ============ PASO 5 · PUBLICAR ============ --}}
          <section class="event-wizard-step" data-wizard-panel="5">
            <div class="event-wizard-step__head">
              <span class="event-wizard-step__kicker">{{ __('Paso 5 · Publicar') }}</span>
              <h3 class="event-wizard-step__title">{{ __('Revisá todo y publicá tu evento') }}</h3>
              <p class="event-wizard-step__text">
                {{ __('Este es el resumen final. Si algo no te convence, podés volver a cualquier paso para ajustarlo.') }}
              </p>
            </div>

            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
              <span class="text-muted small">{{ __('¿Querés ajustar idiomas, píxeles o multimedia antes de publicar?') }}</span>
              <button type="button" class="ew-advanced-link" id="ewAdvancedLinkBtn">
                <i class="fas fa-sliders-h mr-1"></i>{{ __('Configuración avanzada') }} →
              </button>
            </div>

            <div class="ew-review-grid">
              <div class="ew-review-card ew-review-card--cover">
                <div class="ew-review-card__label"><i class="fas fa-image"></i>{{ __('Portada') }}</div>
                <img src="{{ asset('assets/admin/img/noimage.jpg') }}" alt="{{ __('Portada del evento') }}" class="ew-review-card__thumb" data-review-cover>
              </div>
              <div class="ew-review-card">
                <div class="ew-review-card__label"><i class="fas fa-heading"></i>{{ __('Título') }}</div>
                <div class="ew-review-card__value" data-review-title></div>
              </div>
              <div class="ew-review-card">
                <div class="ew-review-card__label"><i class="fas fa-calendar-alt"></i>{{ __('Fecha') }}</div>
                <div class="ew-review-card__value" data-review-dates></div>
              </div>
              <div class="ew-review-card">
                <div class="ew-review-card__label"><i class="fas fa-ticket-alt"></i>{{ __('Entradas') }}</div>
                <div class="ew-review-card__value" data-review-tickets></div>
              </div>
              <div class="ew-review-card">
                <div class="ew-review-card__label"><i class="fas fa-map-marker-alt"></i>{{ $eventType == 'venue' ? __('Ubicación') : __('Acceso') }}</div>
                <div class="ew-review-card__value" data-review-location></div>
              </div>
              <div class="ew-review-card">
                <div class="ew-review-card__label"><i class="fas fa-align-left"></i>{{ __('Descripción') }}</div>
                <div class="ew-review-card__value" data-review-description></div>
              </div>
            </div>

            <div class="ew-review-note">
              <i class="fas fa-shield-alt"></i>
              <span>{{ __('Al publicar, tu evento queda visible para el público según el estado que elijas. Podés dejarlo en borrador y seguir editándolo cuando quieras.') }}</span>
            </div>

            <div class="row">
              <div class="col-lg-4 col-md-6">
                <div class="form-group">
                  <label for="event_status">{{ __('Estado') . '*' }}</label>
                  <select name="status" id="event_status" class="form-control">
                    <option disabled>{{ __('Seleccioná un estado') }}</option>
                    <option value="1" selected>{{ __('Activo') }}</option>
                    <option value="0">{{ __('Borrador') }}</option>
                  </select>
                </div>
              </div>
              <div class="col-lg-4 col-md-6">
                <div class="form-group">
                  <label for="event_is_featured">{{ __('Destacado') . '*' }}</label>
                  <select name="is_featured" id="event_is_featured" class="form-control">
                    <option disabled>{{ __('Seleccioná una opción') }}</option>
                    <option value="yes">{{ __('Sí') }}</option>
                    <option value="no" selected>{{ __('No') }}</option>
                  </select>
                </div>
              </div>
            </div>
          </section>

          {{-- ============ PASO 6 · AVANZADO (OPCIONAL) ============ --}}
          <section class="event-wizard-step" data-wizard-panel="6">
            <div class="event-wizard-step__head">
              <span class="event-wizard-step__kicker">{{ __('Paso 6 · Avanzado · ' . __('opcional')) }}</span>
              <h3 class="event-wizard-step__title">{{ __('Ajustes extra para tu evento') }}</h3>
              <p class="event-wizard-step__text">
                {{ __('Idiomas adicionales, píxeles de seguimiento y multimedia. Podés saltar este paso: el contenido principal se copia automáticamente a los otros idiomas.') }}
              </p>
            </div>

            @if ($singleLanguageMode)
              <div class="ew-review-note">
                <i class="fas fa-info-circle"></i>
                <span>{{ __('Tu sitio tiene un solo idioma disponible. Pasá directo a los ajustes opcionales.') }}</span>
              </div>
            @else
              <div class="ew-lang-sync">
                <div class="custom-control custom-switch">
                  <input type="checkbox" class="custom-control-input" id="ewLangSync" checked>
                  <label class="custom-control-label" for="ewLangSync"><span class="sr-only">{{ __('Usar el mismo contenido para todos los idiomas') }}</span></label>
                </div>
                <div class="ew-lang-sync__copy">
                  <strong>{{ __('Usar el mismo contenido para todos los idiomas') }}</strong>
                  <span>{{ __('Al publicar, copiamos el título, la descripción y la ubicación del idioma principal a los demás. Desactivá esta opción para traducir cada idioma a mano.') }}</span>
                </div>
              </div>

              <div id="ewLangAccordion">
                @foreach ($nonDefaultLanguages as $language)
                  <div class="version event-content-panel">
                    <div class="version-header" id="ewHeading{{ $language->id }}">
                      <h5 class="mb-0">
                        <button type="button" class="btn btn-link" data-toggle="collapse"
                          data-target="#ewCollapse{{ $language->id }}" aria-expanded="false"
                          aria-controls="ewCollapse{{ $language->id }}">
                          <i class="fas fa-language mr-2 text-primary"></i>{{ $language->name }}
                        </button>
                      </h5>
                    </div>
                    <div id="ewCollapse{{ $language->id }}" class="collapse" aria-labelledby="ewHeading{{ $language->id }}"
                      data-parent="#ewLangAccordion">
                      <div class="version-body">
                        <button type="button" class="ew-lang-clone mb-3" data-ew-clone-lang="{{ $language->code }}">
                          <i class="fas fa-copy mr-2"></i>{{ __('Copiar del idioma principal') }}
                        </button>

                        <div class="row">
                          <div class="col-lg-6">
                            <div class="form-group">
                              <label for="{{ $language->code }}_title">{{ __('Título del evento') . '*' }}</label>
                              <input type="text" class="form-control" id="{{ $language->code }}_title" name="{{ $language->code }}_title"
                                placeholder="{{ __('Ej: Fiesta de música en vivo en Buenos Aires') }}">
                            </div>
                          </div>
                          <div class="col-lg-6">
                            <div class="form-group">
                              @php
                                $langCategories = DB::table('event_categories')
                                    ->where('language_id', $language->id)
                                    ->where('status', 1)
                                    ->orderBy('serial_number', 'asc')
                                    ->get();
                              @endphp
                              <label for="{{ $language->code }}_category_id">{{ __('Categoría') . '*' }}</label>
                              <select name="{{ $language->code }}_category_id" id="{{ $language->code }}_category_id" class="form-control">
                                <option selected disabled>{{ __('Seleccioná una categoría') }}</option>
                                @foreach ($langCategories as $category)
                                  <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                              </select>
                            </div>
                          </div>
                        </div>

                        @if ($eventType == 'venue')
                          <div class="row">
                            <div class="col-lg-8">
                              <div class="form-group">
                                <label for="{{ $language->code }}_address">{{ __('Dirección') . '*' }}</label>
                                <input type="text" name="{{ $language->code }}_address" id="{{ $language->code }}_address" class="form-control"
                                  placeholder="{{ __('Ej: Av. Corrientes 1234') }}">
                              </div>
                            </div>
                            <div class="col-lg-4">
                              <div class="form-group">
                                <label for="{{ $language->code }}_country">{{ __('País') . '*' }}</label>
                                <input type="text" name="{{ $language->code }}_country" id="{{ $language->code }}_country" placeholder="{{ __('Ej: Argentina') }}" class="form-control">
                              </div>
                            </div>
                            <div class="col-lg-4">
                              <div class="form-group">
                                <label for="{{ $language->code }}_state">{{ __('Provincia') }}</label>
                                <input type="text" name="{{ $language->code }}_state" id="{{ $language->code }}_state" class="form-control" placeholder="{{ __('Ej: Buenos Aires') }}">
                              </div>
                            </div>
                            <div class="col-lg-4">
                              <div class="form-group">
                                <label for="{{ $language->code }}_city">{{ __('Ciudad') . '*' }}</label>
                                <input type="text" name="{{ $language->code }}_city" id="{{ $language->code }}_city" class="form-control" placeholder="{{ __('Ej: CABA') }}">
                              </div>
                            </div>
                            <div class="col-lg-4">
                              <div class="form-group">
                                <label for="{{ $language->code }}_zip_code">{{ __('Código postal') }}</label>
                                <input type="text" id="{{ $language->code }}_zip_code" placeholder="{{ __('Ej: C1043') }}" name="{{ $language->code }}_zip_code" class="form-control">
                              </div>
                            </div>
                          </div>
                        @endif

                        <div class="form-group">
                          <label for="descriptionTmce{{ $language->id }}">{{ __('Descripción') . '*' }}</label>
                          <textarea id="descriptionTmce{{ $language->id }}" class="form-control summernote"
                            name="{{ $language->code }}_description" data-height="240"></textarea>
                        </div>

                        <div class="row">
                          <div class="col-lg-12">
                            <div class="form-group">
                              <label for="{{ $language->code }}_meta_keywords">{{ __('Palabras clave para Google') }}</label>
                              <input class="form-control" id="{{ $language->code }}_meta_keywords" name="{{ $language->code }}_meta_keywords"
                                placeholder="{{ __('Ej: festival, buenos aires, música en vivo') }}" data-role="tagsinput">
                            </div>
                          </div>
                          <div class="col-lg-12">
                            <div class="form-group">
                              <label for="{{ $language->code }}_meta_description">{{ __('Descripción corta para Google') }}</label>
                              <textarea class="form-control" id="{{ $language->code }}_meta_description" name="{{ $language->code }}_meta_description" rows="3"
                                placeholder="{{ __('Una descripción breve y clara para buscadores y enlaces compartidos.') }}"></textarea>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            @endif

            <div class="card ev-section-card event-optional-card mt-4">
              <div class="card-header ev-section-header">
                <button type="button" class="event-optional-toggle collapsed" data-toggle="collapse"
                  data-target="#eventMediaCreateOrganizer" aria-expanded="false" aria-controls="eventMediaCreateOrganizer">
                  <span>
                    <strong>{{ __('Multimedia del evento o artista') }}</strong>
                    <small>{{ __('Opcional. Sumá Spotify o YouTube si ayuda a vender la experiencia.') }}</small>
                  </span>
                  <i class="fas fa-chevron-down"></i>
                </button>
              </div>
              <div class="card-body collapse" id="eventMediaCreateOrganizer">
                <div class="row">
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label><i class="fab fa-spotify mr-1" style="color:#1DB954"></i> {{ __('Enlace de Spotify') }}</label>
                      <input type="url" class="form-control" name="spotify_url"
                        placeholder="{{ __('Ej: https://open.spotify.com/artist/12345') }}">
                      <small class="text-muted">{{ __('Abrí Spotify, buscá al artista, hacé clic en los tres puntos → Compartir → Copiar enlace del artista.') }}</small>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label><i class="fab fa-youtube mr-1" style="color:#FF0000"></i> {{ __('Enlace del video en YouTube') }}</label>
                      <input type="url" class="form-control" name="youtube_url"
                        placeholder="{{ __('Ej: https://www.youtube.com/watch?v=1234567890ab') }}">
                      <small class="text-muted">{{ __('Pegá el enlace del video de YouTube tal como aparece en el navegador.') }}</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="card ev-section-card event-optional-card mt-4">
              <div class="card-header ev-section-header">
                <button type="button" class="event-optional-toggle collapsed" data-toggle="collapse"
                  data-target="#eventTrackingCreateOrganizer" aria-expanded="false" aria-controls="eventTrackingCreateOrganizer">
                  <span>
                    <strong>{{ __('Píxeles de seguimiento') }}</strong>
                    <small>{{ __('Opcional. Agregá Meta, Google o TikTok solo si medís campañas.') }}</small>
                  </span>
                  <i class="fas fa-chevron-down"></i>
                </button>
              </div>
              <div class="card-body collapse" id="eventTrackingCreateOrganizer">
                <div class="row">
                  <div class="col-lg-4">
                    <div class="form-group">
                      <label><i class="fab fa-facebook mr-1"></i> {{ __('Meta Pixel ID (Facebook)') }}</label>
                      <input type="text" class="form-control" name="meta_pixel_id" placeholder="Ej: 1234567890123456">
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="form-group">
                      <label><i class="fab fa-google mr-1"></i> {{ __('Google Analytics ID') }}</label>
                      <input type="text" class="form-control" name="google_analytics_id" placeholder="Ej: G-XXXXXXXXXX">
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="form-group">
                      <label><i class="fab fa-tiktok mr-1"></i> {{ __('TikTok Pixel ID') }}</label>
                      <input type="text" class="form-control" name="tiktok_pixel_id" placeholder="Ej: CXXXXXXXXXXXXXXX">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </form>
      </div>

      <div class="modal-footer event-wizard__footer">
        <div class="event-wizard__footer-inner">
          <span class="event-wizard__step-hint" id="ewStepHint">{{ __('Paso 1 de 6') }}</span>
          <div class="event-wizard__footer-actions">
            <button type="button" class="btn btn-light" id="ewBackBtn">
              <i class="fas fa-arrow-left mr-1"></i>{{ __('Volver') }}
            </button>
            <button type="button" class="event-wizard__skip d-none" id="ewSkipAdvancedBtn">
              {{ __('Saltar este paso →') }}
            </button>
            <button type="button" class="btn btn-primary" id="ewNextBtn">
              {{ __('Continuar') }}<i class="fas fa-arrow-right ml-1"></i>
            </button>
            <button type="button" class="btn btn-success d-none" id="EventSubmit">
              <i class="fas fa-rocket mr-1"></i>{{ __('Publicar evento') }}
            </button>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
