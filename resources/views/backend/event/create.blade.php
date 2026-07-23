@extends('backend.layout')

@section('content')
  <div class="event-form-modern event-form-modern--create">
  <div class="page-header">
    <h4 class="page-title">{{ __('Crear evento') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ route('admin.dashboard') }}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Events Management') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a
          href="{{ route('admin.choose-event-type', ['language' => $defaultLang->code]) }}">{{ __('Elegir tipo de evento') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Crear evento') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">{{ __('Crear evento') }}</div>
          <a class="btn btn-info btn-sm float-right d-inline-block"
            href="{{ route('admin.event_management.event', ['language' => $defaultLang->code]) }}">
            <span class="btn-label">
              <i class="fas fa-backward"></i>
            </span>
            {{ __('Volver') }}
          </a>
        </div>

        <div class="card-body">
          <div class="row">
            <div class="col-lg-8 offset-lg-2">
              <div class="alert alert-danger pb-1 dis-none" id="eventErrors">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <ul></ul>
              </div>
              <div class="card ev-section-card mb-4" id="createChecklistCard">
                <div class="card-header ev-section-header">
                  <h4 class="card-title"><i class="fas fa-clipboard-check mr-2 text-primary"></i>{{ __('Checklist de publicacion') }}</h4>
                </div>
                <div class="card-body">
                  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
                    <div class="mb-3 mb-md-0">
                      <span class="badge badge-warning mb-2" id="createChecklistBadge">{{ __('0% completo') }}</span>
                      <h5 class="mb-1" id="createChecklistTitle">{{ __('Empieza a cargar tu evento') }}</h5>
                      <p class="text-muted mb-0">{{ __('Esta guia se completa sola mientras agregas la informacion principal.') }}</p>
                    </div>
                    <div class="text-md-right">
                      <div class="font-weight-bold" id="createChecklistCount">0/6 {{ __('puntos listos') }}</div>
                      <small class="text-muted">{{ __('Ideal para la primera publicacion.') }}</small>
                    </div>
                  </div>
                  <div class="progress mb-4" style="height: 10px;">
                    <div class="progress-bar bg-warning" id="createChecklistProgress" role="progressbar" style="width: 0%;"></div>
                  </div>
                  <div class="row">
                    <div class="col-lg-6 mb-3"><div class="border rounded p-3 h-100" id="check-thumbnail"><div class="font-weight-bold mb-1">{{ __('Imagen de portada') }}</div><small class="text-muted">{{ __('Sube una portada clara y facil de reconocer.') }}</small></div></div>
                    <div class="col-lg-6 mb-3"><div class="border rounded p-3 h-100" id="check-dates"><div class="font-weight-bold mb-1">{{ __('Fechas') }}</div><small class="text-muted">{{ __('Define bien cuando ocurre el evento.') }}</small></div></div>
                    <div class="col-lg-6 mb-3"><div class="border rounded p-3 h-100" id="check-title"><div class="font-weight-bold mb-1">{{ __('Titulo') }}</div><small class="text-muted">{{ __('Usa un nombre claro y facil de entender.') }}</small></div></div>
                    <div class="col-lg-6 mb-3"><div class="border rounded p-3 h-100" id="check-description"><div class="font-weight-bold mb-1">{{ __('Descripcion') }}</div><small class="text-muted">{{ __('Cuenta que incluye la entrada, horarios y datos clave.') }}</small></div></div>
                    <div class="col-lg-6 mb-3"><div class="border rounded p-3 h-100" id="check-sales"><div class="font-weight-bold mb-1">{{ __('Venta o acceso') }}</div><small class="text-muted">{{ __('Revisa precio, gratuidad o acceso online segun el tipo de evento.') }}</small></div></div>
                    <div class="col-lg-6 mb-0"><div class="border rounded p-3 h-100" id="check-status"><div class="font-weight-bold mb-1">{{ __('Estado') }}</div><small class="text-muted">{{ __('Elige si quieres dejarlo activo o seguir trabajandolo.') }}</small></div></div>
                  </div>
                </div>
              </div>

              <form id="eventForm" action="{{ route('admin.event_management.store_event') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="event_type" value="{{ request()->input('type') }}">
                <div class="event-cover-box mb-4">
                  <div class="event-cover-box__intro">
                    <span class="event-cover-box__eyebrow">{{ __('Portada principal') }}</span>
                    <h4 class="event-cover-box__title">{{ __('Imagen de portada') }}*</h4>
                    <p class="event-cover-box__text">{{ __('Es la imagen principal del evento. Aparece en el listado, la pagina del evento y cuando se comparte.') }}</p>
                  </div>
                  <div class="event-cover-box__body">
                    <div class="thumb-preview event-cover-box__preview">
                      <img src="{{ asset('assets/admin/img/noimage.jpg') }}" alt="..." class="uploaded-img">
                    </div>
                    <div class="event-cover-box__actions">
                      <label class="event-cover-box__upload" role="button">
                        <span class="event-cover-box__upload-icon">
                          <i class="fas fa-image"></i>
                        </span>
                        <span class="event-cover-box__upload-copy">
                          <strong>{{ __('Elegir imagen de portada') }}</strong>
                          <small>{{ __('Haz clic para subir tu flyer o reemplazarlo') }}</small>
                        </span>
                        <input type="file" class="img-input" name="thumbnail" accept="image/jpeg,image/png,image/webp">
                      </label>
                      <small class="event-cover-box__hint">{{ __('Puedes usar una imagen horizontal, cuadrada o vertical. Lo importante es que se vea bien y se lea claro.') }}</small>
                      <div class="event-cover-box__empty" data-cover-ai-empty>
                        <strong>{{ __('Subi una portada para empezar.') }}</strong>
                        <span>{{ __('Despues vas a poder analizarla con IA para completar el evento mas rapido.') }}</span>
                      </div>
                      <div class="event-cover-box__ai d-none" data-cover-ai-ready>
                        <div class="event-cover-box__state">
                          <i class="fas fa-check-circle"></i>
                          <div>
                            <strong>{{ __('Imagen de portada cargada correctamente.') }}</strong>
                            <span>{{ __('Podemos leer la imagen y ayudarte a completar titulo, fecha, lugar, promociones y descripcion.') }}</span>
                          </div>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm" data-cover-save-analyze>
                          <i class="fas fa-magic mr-1"></i>{{ __('Analizar portada con IA') }}
                        </button>
                        <small>{{ __('Analizamos la imagen antes de guardar para ayudarte a completar titulo, fecha, lugar y descripcion.') }}</small>
                      </div>
                    </div>
                  </div>
                </div>

                @include('organizer.event.partials.create-cover-ai-panel', [
                  'temporaryAnalysisUrl' => route('admin.events.ai-assistant.temporary_cover_analysis'),
                ])

                <div class="row">
                  <div class="col-lg-12">
                    <div class="form-group mt-1">
                      <label for="">{{ __('Tipo de fecha') . '*' }}</label>
                      <div class="selectgroup w-100">
                        <label class="selectgroup-item">
                          <input type="radio" name="date_type" value="single" class="selectgroup-input eventDateType"
                            checked>
                          <span class="selectgroup-button">{{ __('Fecha unica') }}</span>
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
                      <label for="">{{ __('Contador regresivo') . '*' }}</label>
                      <div class="selectgroup w-100">
                        <label class="selectgroup-item">
                          <input type="radio" name="countdown_status" value="1" class="selectgroup-input" checked>
                          <span class="selectgroup-button">{{ __('Active') }}</span>
                        </label>

                        <label class="selectgroup-item">
                          <input type="radio" name="countdown_status" value="0" class="selectgroup-input">
                          <span class="selectgroup-button">{{ __('Oculto') }}</span>
                        </label>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row" id="single_dates">
                  <div class="col-lg-3">
                    <div class="form-group">
                      <label>{{ __('Start Date') . '*' }}</label>
                      <input type="date" name="start_date" placeholder="{{ __('Fecha de inicio') }}" class="form-control">
                    </div>
                  </div>

                  <div class="col-lg-3">
                    <div class="form-group">
                      <label for="">{{ __('Start Time') . '*' }}</label>
                      <input type="time" name="start_time" class="form-control">
                    </div>
                  </div>
                  <div class="col-lg-3">
                    <div class="form-group">
                      <label>{{ __('End Date') . '*' }}</label>
                      <input type="date" name="end_date" placeholder="{{ __('Fecha de finalizacion') }}" class="form-control">
                    </div>
                  </div>

                  <div class="col-lg-3">
                    <div class="form-group">
                      <label for="">{{ __('End Time') . '*' }}</label>
                      <input type="time" name="end_time" class="form-control">
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-12 d-none" id="multiple_dates">
                    <div class="form-group">
                      <table class="table table-bordered ">
                        <thead>
                          <tr>
                            <th>{{ __('Start Date') }}</th>
                            <th>{{ __('Start Time') }}</th>
                            <th>{{ __('End Date') }}</th>
                            <th>{{ __('End Time') }}</th>
                            <th><a href="javascrit:void(0)" class="btn btn-success addDateRow"><i
                                  class="fas fa-plus-circle"></i></a></th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>
                              <div class="form-group">
                                <label for="">{{ __('Start Date') . '*' }}</label>
                                <input type="date" name="m_start_date[]" class="form-control">
                              </div>
                            </td>
                            <td>
                              <div class="form-group">
                                <label for="">{{ __('Start Time') . '*' }}</label>
                                <input type="time" name="m_start_time[]" class="form-control">
                              </div>
                            </td>
                            <td>
                              <div class="form-group">
                                <label for="">{{ __('End Date') . '*' }} </label>
                                <input type="date" name="m_end_date[]" class="form-control">
                              </div>
                            </td>
                            <td>
                              <div class="form-group">
                                <label for="">{{ __('End Time') . '*' }} </label>
                                <input type="time" name="m_end_time[]" class="form-control">
                              </div>
                            </td>
                            <td>
                              <a href="javascript:void(0)" class="btn btn-danger deleteDateRow">
                                <i class="fas fa-minus"></i></a>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>

                <div class="row ">
                  <div class="col-lg-4">
                    <div class="form-group">
                      <label for="">{{ __('Estado') . '*' }}</label>
                      <select name="status" class="form-control">
                        <option disabled>{{ __('Selecciona un estado') }}</option>
                        <option value="1" selected>{{ __('Active') }}</option>
                        <option value="0">{{ __('Oculto') }}</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="form-group">
                      <label for="">{{ __('Evento destacado') . '*' }}</label>
                      <select name="is_featured" class="form-control">
                        <option disabled>{{ __('Selecciona una opcion') }}</option>
                        <option value="yes">{{ __('Si') }}</option>
                        <option value="no" selected>{{ __('No') }}</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="form-group">
                      <label for="">{{ __('Organizador') . '*' }}</label>
                      <select name="organizer_id" class="form-control js-example-basic-single" required>
                        <option selected value="">{{ __('Selecciona un organizador') }}</option>
                        @foreach ($organizers as $organizer)
                          <option value="{{ $organizer->id }}">{{ $organizer->username }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>
                @if (request()->input('type') == 'online')
                  {{-- /*****--Ticekt limtit & ticket for each customer start--****** --}}

                  <div class="row">
                    <div class="col-lg-6">
                      <div class="form-group mt-1">
                        <label for="">{{ __('Disponibilidad total de entradas') . '*' }}</label>
                        <div class="selectgroup w-100">
                          <label class="selectgroup-item">
                            <input type="radio" name="ticket_available_type" value="unlimited"
                              class="selectgroup-input" checked>
                            <span class="selectgroup-button">{{ __('Sin limite') }}</span>
                          </label>

                          <label class="selectgroup-item">
                            <input type="radio" name="ticket_available_type" value="limited"
                              class="selectgroup-input">
                            <span class="selectgroup-button">{{ __('Con limite') }}</span>
                          </label>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-6 d-none" id="ticket_available">
                      <div class="form-group">
                        <label>{{ __('Cantidad total disponible') . '*' }}</label>
                        <input type="number" name="ticket_available"
                          placeholder="{{ __('Ej: 500') }}" class="form-control">
                      </div>
                    </div>
                    @if ($websiteInfo->event_guest_checkout_status != 1)
                      <div class="col-lg-6">
                        <div class="form-group mt-1">
                          <label for="">{{ __('Limite por comprador') . '*' }}</label>
                          <div class="selectgroup w-100">
                            <label class="selectgroup-item">
                              <input type="radio" name="max_ticket_buy_type" value="unlimited"
                                class="selectgroup-input" checked>
                              <span class="selectgroup-button">{{ __('Sin limite') }}</span>
                            </label>

                            <label class="selectgroup-item">
                              <input type="radio" name="max_ticket_buy_type" value="limited"
                                class="selectgroup-input">
                              <span class="selectgroup-button">{{ __('Con limite') }}</span>
                            </label>
                          </div>
                        </div>
                      </div>
                    @else
                      <input type="hidden" name="max_ticket_buy_type" value="unlimited">
                    @endif

                    <div class="col-lg-6 d-none" id="max_buy_ticket">
                      <div class="form-group">
                        <label>{{ __('Cantidad maxima por comprador') . '*' }}</label>
                        <input type="number" name="max_buy_ticket"
                          placeholder="{{ __('Ej: 4') }}" class="form-control">
                      </div>
                    </div>

                    <div class="col-lg-4">
                      <div class="">
                        <div class="form-group">
                          <label for="">{{ __('Precio de la entrada') }} ({{ $getCurrencyInfo->base_currency_text }}) *
                          </label>
                          <input type="number" name="price" id="ticket-pricing" class="form-control"
                            placeholder="{{ __('Ej: 12000') }}">
                        </div>
                      </div>
                      <div class="form-group">
                        <input type="checkbox" name="pricing_type" value="free" class="" id="free_ticket">
                        <label for="free_ticket">{{ __('Este evento es gratuito') }}</label>
                      </div>
                    </div>
                    <div class="col-lg-8">
                      <div class="">
                        <div class="form-group">
                          <label for="">{{ __('Enlace de acceso o meeting URL') }} *
                          </label>
                          <input type="text" name="meeting_url" class="form-control"
                            placeholder="{{ __('Ej: enlace de Zoom, Meet o plataforma de acceso') }}">
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row" id="early_bird_discount_free">
                    <div class="col-lg-12">
                      <div class="form-group mt-1">
                        <label for="">{{ __('Descuento anticipado') . '*' }}</label>
                        <div class="selectgroup w-100">
                          <label class="selectgroup-item">
                            <input type="radio" name="early_bird_discount_type" value="disable"
                              class="selectgroup-input" checked>
                            <span class="selectgroup-button">{{ __('No usar') }}</span>
                          </label>

                          <label class="selectgroup-item">
                            <input type="radio" name="early_bird_discount_type" value="enable"
                              class="selectgroup-input">
                            <span class="selectgroup-button">{{ __('Usar') }}</span>
                          </label>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-12 d-none" id="early_bird_dicount">
                      <div class="row">
                        <div class="col-lg-3">
                          <div class="form-group">
                            <label for="">{{ __('Discount') }} * </label>
                            <select name="discount_type" class="form-control">
                              <option disabled>{{ __('Selecciona el tipo de descuento') }}</option>
                              <option value="fixed">{{ __('Fixed') }}</option>
                              <option value="percentage">{{ __('Percentage') }}</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-lg-3">
                          <div class="form-group">
                            <label for="">{{ __('Amount') }} * </label>
                            <input type="number" name="early_bird_discount_amount" class="form-control">
                          </div>
                        </div>
                        <div class="col-lg-3">
                          <div class="form-group">
                            <label for="">{{ __('Fecha limite del descuento') }} *</label>
                            <input type="date" name="early_bird_discount_date" class="form-control">
                          </div>
                        </div>
                        <div class="col-lg-3">
                          <div class="form-group">
                            <label for="">{{ __('Hora limite del descuento') }} *</label>
                            <input type="time" name="early_bird_discount_time" class="form-control">
                          </div>
                        </div>

                      </div>
                    </div>
                  </div>
                @endif


                  <div class="event-content-shell__intro mt-4">
                    <span class="event-content-shell__eyebrow">{{ __('Paso 4') }}</span>
                    <h4 class="event-content-shell__title">{{ __('Contenido del evento') }}</h4>
                    <p class="event-content-shell__text">{{ __('Primero carga lo esencial para vender: titulo, categoria, ubicacion y descripcion. Lo de Google va aparte y es opcional.') }}</p>
                  </div>

                <div id="accordion" class="mt-3">
                  @foreach ($languages as $language)
                    <div class="version event-content-panel">
                      <div class="version-header" id="heading{{ $language->id }}">
                        <h5 class="mb-0">
                          <button type="button" class="btn btn-link" data-toggle="collapse"
                            data-target="#collapse{{ $language->id }}"
                            aria-expanded="{{ $language->is_default == 1 ? 'true' : 'false' }}"
                            aria-controls="collapse{{ $language->id }}">
                            {{ $language->name }} {{ $language->is_default == 1 ? '(' . __('Principal') . ')' : '' }}
                          </button>
                        </h5>
                      </div>

                      <div id="collapse{{ $language->id }}"
                        class="collapse {{ $language->is_default == 1 ? 'show' : '' }}"
                        aria-labelledby="heading{{ $language->id }}" data-parent="#accordion">
                        <div class="version-body">
                          <div class="event-content-block mb-4">
                            <div class="event-content-block__head">
                              <span class="event-content-block__kicker">{{ __('Lo principal') }}</span>
                              <h5 class="event-content-block__title">{{ __('Lo que ve primero el comprador') }}</h5>
                              <p class="event-content-block__text">{{ __('Aqui defines nombre, categoria, ubicacion y una descripcion clara del evento.') }}</p>
                            </div>
                          <div class="row">
                            <div class="col-lg-6">
                              <div class="form-group">
                                <label>{{ __('Titulo del evento') . '*' }}</label>
                                <input type="text" class="form-control" name="{{ $language->code }}_title"
                                  placeholder="{{ __('Ej: Festival de invierno en Buenos Aires') }}">
                              </div>
                            </div>

                            <div class="col-lg-6">
                              <div class="form-group">
                                @php
                                  $categories = $categoriesByLang->get($language->id, collect());
                                @endphp

                                <label for="">{{ __('Categoria') . '*' }}</label>
                                <select name="{{ $language->code }}_category_id" class="form-control">
                                  <option selected disabled>{{ __('Selecciona una categoria') }}</option>

                                  @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                  @endforeach
                                </select>
                              </div>
                            </div>
                          </div>

                          @if (request()->input('type') == 'venue')
                            <div class="row">
                              <div class="col-lg-8">
                                <div class="form-group">
                                  <label for="">{{ __('Direccion') . '*' }}</label>
                                  <input type="text" name="{{ $language->code }}_address"
                                    class="form-control"
                                    placeholder="{{ __('Ej: Av. Corrientes 1234') }}">
                                </div>
                              </div>
                              <div class="col-lg-4">
                                <div class="form-group">
                                  <label for="">{{ __('Pais') . '*' }}</label>
                                  <input type="text" name="{{ $language->code }}_country"
                                    placeholder="{{ __('Ej: Argentina') }}"
                                    class="form-control">
                                </div>
                              </div>
                              <div class="col-lg-4">
                                <div class="form-group">
                                  <label for="">{{ __('Provincia') }}</label>
                                  <input type="text" name="{{ $language->code }}_state"
                                    class="form-control"
                                    placeholder="{{ __('Ej: Buenos Aires') }}">
                                </div>
                              </div>
                              <div class="col-lg-4">
                                <div class="form-group">
                                  <label for="">{{ __('Ciudad') . '*' }}</label>
                                  <input type="text" name="{{ $language->code }}_city"
                                    class="form-control"
                                    placeholder="{{ __('Ej: CABA') }}">
                                </div>
                              </div>
                              <div class="col-lg-4">
                                <div class="form-group">
                                  <label for="">{{ __('Codigo postal') }}</label>
                                  <input type="text" placeholder="{{ __('Ej: C1043') }}"
                                    name="{{ $language->code }}_zip_code"
                                    class="form-control">
                                </div>
                              </div>
                            </div>
                            @if ($language->is_default == 1)
                              @include('partials.event-venue-location', [
                                'mapId' => 'eventVenueMapCreateAdmin',
                                'languages' => $languages,
                                'geocodeUrl' => route('admin.event.venue_geocode'),
                              ])
                            @endif
                          @endif

                          <div class="row">
                            <div class="col">
                              <div class="form-group">
                                <label>{{ __('Descripcion') . '*' }}</label>
                                <small class="d-block text-muted mb-2">{{ __('Cuenta que incluye la entrada, horarios, artistas, acceso y cualquier dato importante para decidir la compra.') }}</small>
                                <textarea id="descriptionTmce{{ $language->id }}" class="form-control summernote"
                                  name="{{ $language->code }}_description" data-height="300"></textarea>
                              </div>
                            </div>
                          </div>

                          <div class="event-content-block event-content-block--soft">
                            <div class="event-content-block__head">
                              <span class="event-content-block__kicker">{{ __('Extras utiles') }}</span>
                              <h5 class="event-content-block__title">{{ __('Politicas y datos para Google') }}</h5>
                              <p class="event-content-block__text">{{ __('Sirve para responder dudas frecuentes y para que el evento se vea mejor cuando lo comparten o lo encuentran en Google.') }}</p>
                            </div>
                            <div class="row">
                              <div class="col-lg-12">
                                @include('partials.event-canonical-refund-policy')
                              </div>
                            </div>

                            <div class="row">
                              <div class="col-lg-12">
                                <div class="form-group">
                                  <label>{{ __('Palabras clave para Google') }}</label>
                                  <input class="form-control" name="{{ $language->code }}_meta_keywords"
                                    placeholder="{{ __('Ej: festival, buenos aires, musica en vivo') }}" data-role="tagsinput">
                                </div>
                              </div>
                            </div>

                            <div class="row">
                              <div class="col-lg-12">
                                <div class="form-group">
                                  <label>{{ __('Descripcion corta para Google') }}</label>
                                  <textarea class="form-control" name="{{ $language->code }}_meta_description" rows="5"
                                    placeholder="{{ __('Una descripcion breve y clara para buscadores y enlaces compartidos.') }}"></textarea>
                                </div>
                              </div>
                            </div>
                          </div>
                          </div>

                          <div class="row">
                            <div class="col">
                              @php $currLang = $language; @endphp

                              @foreach ($languages as $language)
                                @continue($language->id == $currLang->id)

                                <div class="form-check py-0">
                                  <label class="form-check-label">
                                    <input class="form-check-input" type="checkbox"
                                      onchange="cloneInput('collapse{{ $currLang->id }}', 'collapse{{ $language->id }}', event)">
                                    <span class="form-check-sign">{{ __('Copiar para') }} <strong
                                        class="text-capitalize text-secondary">{{ $language->name }}</strong>
                                      {{ __('idioma') }}</span>
                                  </label>
                                </div>
                              @endforeach
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>

                <div id="sliders"></div>

                {{-- Multimedia del artista --}}
                <div class="card ev-section-card mt-4">
                  <div class="card-header ev-section-header">
                    <h4 class="card-title">{{ __('Multimedia del Artista') }}</h4>
                    <small class="text-muted">{{ __('Opcional. Se mostrará en la página del evento para que los compradores conozcan al artista.') }}</small>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label><i class="fab fa-spotify mr-1" style="color:#1DB954"></i> {{ __('Enlace del artista en Spotify') }}</label>
                          <input type="url" class="form-control" name="spotify_url"
                            placeholder="Ej: https://open.spotify.com/artist/4tZwfgrHOc3mvqYlEYSvVi">
                          <small class="text-muted">{{ __('Abrí Spotify, buscá al artista, hacé clic en los tres puntos → Compartir → Copiar enlace del artista.') }}</small>
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label><i class="fab fa-youtube mr-1" style="color:#FF0000"></i> {{ __('Enlace del video en YouTube') }}</label>
                          <input type="url" class="form-control" name="youtube_url"
                            placeholder="Ej: https://www.youtube.com/watch?v=dQw4w9WgXcQ">
                          <small class="text-muted">{{ __('Pegá el enlace del video de YouTube tal como aparece en el navegador.') }}</small>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                {{-- Píxeles de seguimiento --}}
                <div class="card ev-section-card mt-4">
                  <div class="card-header ev-section-header">
                    <h4 class="card-title">{{ __('Píxeles de Seguimiento') }}</h4>
                    <small class="text-muted">{{ __('Opcional. Agregá tus propios píxeles para medir conversiones de este evento.') }}</small>
                  </div>
                  <div class="card-body">
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

              </form>

              <div class="event-gallery-secondary mt-4">
                <div class="event-gallery-secondary__header">
                  <span>{{ __('Opcional') }}</span>
                  <h4>{{ __('Imagenes adicionales') }}</h4>
                  <p>{{ __('Agrega fotos complementarias para mostrar mejor el ambiente, los artistas, el lugar o experiencias de ediciones anteriores. No reemplazan la portada.') }}</p>
                </div>
                <form action="{{ route('admin.event.imagesstore') }}" id="my-dropzone" enctype="multipart/formdata"
                  class="dropzone create">
                  @csrf
                  <div class="fallback">
                    <input name="file" type="file" multiple />
                  </div>
                </form>
                <div class="mb-0" id="errpreimg"></div>
                <p class="text-muted small mt-2 mb-0">{{ __('JPG, PNG o WebP. Minimo aceptado: 600x450. Recomendado: 1170x570 o mas para mejor calidad.') }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="card-footer">
          <div class="row">
            <div class="col-12 text-center">
              <button type="submit" id="EventSubmit" class="btn btn-success">
                {{ __('Guardar evento') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
@endsection

@section('style')
  <style>
    .create-check-done {
      border-color: #86efac !important;
      background: #f0fdf4;
    }

    #my-dropzone {
      border: 2px dashed #d6d9e6;
      border-radius: 14px;
      background: #f8f9fc;
      min-height: 128px;
      padding: 18px;
    }

    #my-dropzone .dz-message {
      display: block !important;
      margin: 1rem 0 1.5rem;
      color: #334155;
      font-weight: 700;
      text-align: center;
    }

    #my-dropzone .dz-message::before {
      content: "";
      display: block;
      width: 52px;
      height: 52px;
      margin: 0 auto 14px;
      border-radius: 16px;
      background-color: #e8f1ff;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232564eb' stroke-width='1.8' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4'/%3E%3Cpolyline points='17 8 12 3 7 8'/%3E%3Cline x1='12' y1='3' x2='12' y2='15'/%3E%3Cpath d='M8 15a4 4 0 0 1 .9-7.9A5 5 0 0 1 18.8 8A3.5 3.5 0 0 1 19 15'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: center;
      background-size: 28px 28px;
      box-shadow: inset 0 0 0 1px rgba(37, 99, 235, 0.08);
    }

    #my-dropzone.dz-started .dz-message {
      opacity: 0.95;
    }

    #my-dropzone .dz-message span {
      font-size: 0;
      display: inline-block;
      line-height: 1.5;
    }

    #my-dropzone .dz-message span::before {
      content: "Agregar imagenes adicionales";
      display: block;
      font-size: 15px;
      margin-bottom: 6px;
    }

    #my-dropzone .dz-message span::after {
      content: "Arrastralas aqui o hace clic para elegirlas. Son opcionales y complementan la portada.";
      display: block;
      font-size: 12px;
      font-weight: 500;
      color: #64748b;
      max-width: 440px;
      margin: 0 auto;
    }

    .event-cover-box {
      padding: 22px;
      border: 1px solid #dbe5f3;
      border-radius: 18px;
      background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
      box-shadow: 0 14px 34px rgba(15, 23, 42, 0.04);
    }

    .event-cover-box__intro {
      margin-bottom: 18px;
    }

    .event-cover-box__eyebrow {
      display: inline-flex;
      align-items: center;
      padding: 6px 10px;
      border-radius: 999px;
      background: #e8f1ff;
      color: #1d4ed8;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: .08em;
      text-transform: uppercase;
      margin-bottom: 10px;
    }

    .event-cover-box__title {
      margin-bottom: 6px;
      font-size: 22px;
      font-weight: 700;
      color: #0f172a;
    }

    .event-cover-box__text,
    .event-cover-box__hint {
      color: #64748b;
      line-height: 1.7;
    }

    .event-cover-box__body {
      display: flex;
      align-items: center;
      gap: 22px;
      flex-wrap: wrap;
    }

    .event-cover-box__preview {
      margin-bottom: 0;
      flex: 0 0 220px;
    }

    .event-cover-box__actions {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      gap: 12px;
      max-width: 430px;
    }

    .event-cover-box__upload {
      display: flex;
      align-items: center;
      gap: 14px;
      width: 100%;
      margin: 0;
      padding: 14px 16px;
      border: 1px dashed #bfdbfe;
      border-radius: 16px;
      background: #eff6ff;
      cursor: pointer;
    }

    .event-cover-box__upload input {
      display: none;
    }

    .event-cover-box__upload-icon {
      width: 46px;
      height: 46px;
      border-radius: 14px;
      background: #dbeafe;
      color: #2563eb;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      flex-shrink: 0;
    }

    .event-cover-box__upload-copy {
      display: flex;
      flex-direction: column;
      gap: 2px;
      color: #0f172a;
    }

    .event-cover-box__upload-copy strong {
      font-size: 14px;
      font-weight: 700;
    }

    .event-cover-box__upload-copy small {
      color: #64748b;
      font-size: 12px;
    }

    .event-cover-box__empty,
    .event-cover-box__ai {
      width: 100%;
      padding: 14px;
      border-radius: 14px;
      background: #fff7ed;
      border: 1px solid #fed7aa;
      color: #9a3412;
    }

    .event-cover-box__empty strong,
    .event-cover-box__empty span,
    .event-cover-box__ai small {
      display: block;
    }

    .event-cover-box__empty span,
    .event-cover-box__ai small {
      margin-top: 4px;
      line-height: 1.6;
      color: #9a3412;
    }

    .event-cover-box__ai {
      background: #f0fdf4;
      border-color: #bbf7d0;
      color: #166534;
    }

    .event-cover-box__state {
      display: flex;
      gap: 10px;
      margin-bottom: 12px;
    }

    .event-cover-box__state i {
      margin-top: 2px;
      color: #16a34a;
    }

    .event-cover-box__state strong,
    .event-cover-box__state span {
      display: block;
    }

    .event-cover-box__state span {
      margin-top: 3px;
      color: #166534;
      line-height: 1.6;
    }

    .create-cover-ai-panel {
      padding: 18px;
      border: 1px solid #dbeafe;
      border-left: 4px solid #2563eb;
      border-radius: 14px;
      background: #f8fbff;
    }

    .create-cover-ai-facts {
      overflow: hidden;
      background: #fff;
    }

    .create-cover-ai-fact {
      display: grid;
      grid-template-columns: minmax(160px, 240px) 1fr;
      gap: 14px;
      padding: 12px 14px;
      border-bottom: 1px solid #eef2f7;
    }

    .create-cover-ai-fact:last-child {
      border-bottom: 0;
    }

    .create-cover-ai-fact__value {
      font-weight: 700;
      color: #1e2532;
    }

    .async-progress-panel {
      margin-top: 12px;
      padding: 12px;
      border: 1px solid #dbeafe;
      border-left: 4px solid #3b82f6;
      border-radius: 8px;
      background: #f8fbff;
      color: #1e2532;
    }

    .async-progress-panel.is-success {
      border-color: #bbf7d0;
      border-left-color: #16a34a;
      background: #f7fef9;
    }

    .async-progress-panel.is-danger {
      border-color: #fecaca;
      border-left-color: #dc2626;
      background: #fff7f7;
    }

    .async-progress-panel__percent {
      font-weight: 700;
      color: #1e40af;
      white-space: nowrap;
    }

    .async-progress-panel__bar {
      height: 10px;
      border-radius: 999px;
      background: #dbeafe;
      overflow: hidden;
    }

    .async-progress-panel__bar .progress-bar {
      background-color: #3b82f6;
      transition: width .35s ease;
    }

    .async-progress-panel.is-success .progress-bar {
      background-color: #16a34a;
    }

    .async-progress-panel.is-danger .progress-bar {
      background-color: #dc2626;
    }

    .async-progress-panel__meta {
      color: #64748b;
      font-size: 12px;
    }

    @media (max-width: 575px) {
      .create-cover-ai-fact {
        grid-template-columns: 1fr;
      }
    }

    .event-gallery-secondary {
      padding: 18px;
      border: 1px solid #e5e7eb;
      border-radius: 16px;
      background: #ffffff;
    }

    .event-gallery-secondary__header {
      margin-bottom: 14px;
    }

    .event-gallery-secondary__header span {
      display: inline-flex;
      margin-bottom: 8px;
      padding: 4px 8px;
      border-radius: 999px;
      background: #f1f5f9;
      color: #64748b;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
    }

    .event-gallery-secondary__header h4 {
      margin-bottom: 6px;
      color: #0f172a;
      font-size: 18px;
      font-weight: 700;
    }

    .event-gallery-secondary__header p {
      margin-bottom: 0;
      color: #64748b;
      line-height: 1.6;
    }

    .event-content-shell__eyebrow {
      display: inline-flex;
      align-items: center;
      padding: 6px 10px;
      border-radius: 999px;
      background: #e8f1ff;
      color: #1d4ed8;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: .08em;
      text-transform: uppercase;
      margin-bottom: 10px;
    }

    .event-content-shell__title {
      margin-bottom: 8px;
      font-size: 24px;
      font-weight: 700;
      color: #0f172a;
    }

    .event-content-shell__text {
      margin-bottom: 0;
      max-width: 760px;
      color: #475569;
      line-height: 1.7;
    }

    .event-content-panel {
      border: 1px solid #dbe5f3;
      border-radius: 16px;
      overflow: hidden;
      background: #ffffff;
      box-shadow: 0 14px 34px rgba(15, 23, 42, 0.04);
    }

    .event-content-panel + .event-content-panel {
      margin-top: 16px;
    }

    .event-content-panel .version-header {
      background: linear-gradient(180deg, #f8fbff 0%, #f3f7fd 100%);
      border-bottom: 1px solid #e5e7eb;
    }

    .event-content-panel .version-header .btn-link {
      width: 100%;
      padding: 18px 22px;
      color: #0f172a;
      font-size: 16px;
      font-weight: 700;
      text-align: left;
      text-decoration: none;
    }

    .event-content-panel .version-body {
      padding: 24px;
      background: #fff;
    }

    .event-content-block {
      padding: 20px;
      border: 1px solid #e5e7eb;
      border-radius: 16px;
      background: #fff;
    }

    .event-content-block--soft {
      background: #f8fafc;
    }

    .event-content-block__head {
      margin-bottom: 18px;
    }

    .event-content-block__kicker {
      display: inline-block;
      margin-bottom: 8px;
      color: #2563eb;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: .08em;
      text-transform: uppercase;
    }

    .event-content-block__title {
      margin-bottom: 6px;
      color: #0f172a;
      font-size: 19px;
      font-weight: 700;
    }

    .event-content-block__text {
      margin-bottom: 0;
      color: #64748b;
      line-height: 1.7;
    }

    .event-content-shell .tox .tox-menubar,
    .event-content-shell .tox .tox-statusbar__branding,
    .event-content-shell .tox .tox-statusbar__wordcount {
      display: none !important;
    }

    .event-content-shell .tox .tox-editor-header {
      border-bottom: 1px solid #e5e7eb;
    }

    .event-content-shell .tox .tox-toolbar-overlord,
    .event-content-shell .tox .tox-toolbar,
    .event-content-shell .tox .tox-toolbar__primary {
      background: #ffffff !important;
      background-image: none !important;
    }

    .event-content-shell .tox.tox-tinymce {
      border: 1px solid #dbe5f3;
      border-radius: 14px;
      overflow: hidden;
      box-shadow: none;
    }
  </style>
  @php($eventFormModernCss = 'assets/admin/css/event-form-modern.css')
  <link rel="stylesheet" href="{{ asset($eventFormModernCss) }}{{ is_file(public_path($eventFormModernCss)) ? '?v=' . filemtime(public_path($eventFormModernCss)) : '' }}">
@endsection

@section('script')
  @php
    $languages = App\Models\Language::get();
  @endphp
  <script>
    let languages = @json($languages);
  </script>
  <script type="text/javascript" src="{{ asset('assets/admin/js/admin-partial.js') }}"></script>
  <script src="{{ asset('assets/admin/js/admin_dropzone.js') }}"></script>
  <script>
    $(document).ready(function() {
      $('.js-example-basic-single').select2();
    });

    function updateCreateChecklist() {
      const eventType = document.querySelector('input[name="event_type"]')?.value;
      const titleInput = document.querySelector('input[name$="_title"]');
      const descriptionInput = document.querySelector('textarea[name$="_description"]');
      const statusInput = document.querySelector('select[name="status"]');
      const thumbnailInput = document.querySelector('input[name="thumbnail"]');
      const isSingle = document.querySelector('input[name="date_type"]:checked')?.value === 'single';
      const priceInput = document.querySelector('input[name="price"]');
      const freeInput = document.getElementById('free_ticket');
      const meetingUrlInput = document.querySelector('input[name="meeting_url"]');

      const titleOk = titleInput && titleInput.value.trim().length >= 8;
      const descriptionText = descriptionInput ? descriptionInput.value.replace(/<[^>]*>/g, '').trim() : '';
      const descriptionOk = descriptionText.length >= 80;
      const thumbnailOk = thumbnailInput && thumbnailInput.files && thumbnailInput.files.length > 0;
      const statusOk = statusInput && statusInput.value !== '';
      const singleDatesOk = document.querySelector('input[name="start_date"]')?.value && document.querySelector('input[name="start_time"]')?.value && document.querySelector('input[name="end_date"]')?.value && document.querySelector('input[name="end_time"]')?.value;
      const multipleDatesOk = Array.from(document.querySelectorAll('input[name="m_start_date[]"]')).some((input, index) => {
        return input.value &&
          document.querySelectorAll('input[name="m_start_time[]"]')[index]?.value &&
          document.querySelectorAll('input[name="m_end_date[]"]')[index]?.value &&
          document.querySelectorAll('input[name="m_end_time[]"]')[index]?.value;
      });
      const datesOk = isSingle ? !!singleDatesOk : !!multipleDatesOk;
      const salesOk = eventType === 'online'
        ? ((freeInput && freeInput.checked) || ((priceInput?.value || '') !== '' && (meetingUrlInput?.value || '').trim() !== ''))
        : true;

      const checks = {
        thumbnail: !!thumbnailOk,
        dates: !!datesOk,
        title: !!titleOk,
        description: !!descriptionOk,
        sales: !!salesOk,
        status: !!statusOk
      };

      let completed = 0;
      Object.keys(checks).forEach((key) => {
        const el = document.getElementById('check-' + key);
        if (!el) return;
        el.classList.toggle('create-check-done', checks[key]);
        completed += checks[key] ? 1 : 0;
      });

      const total = 6;
      const score = Math.round((completed / total) * 100);
      const badge = document.getElementById('createChecklistBadge');
      const title = document.getElementById('createChecklistTitle');
      const count = document.getElementById('createChecklistCount');
      const progress = document.getElementById('createChecklistProgress');

      if (badge) badge.textContent = score + '% completo';
      if (count) count.textContent = completed + '/' + total + ' puntos listos';
      if (progress) progress.style.width = score + '%';

      if (title) {
        if (score >= 85) {
          title.textContent = 'Muy bien encaminado';
        } else if (score >= 50) {
          title.textContent = 'Vas bien, sigue completando';
        } else if (thumbnailOk) {
          title.textContent = 'Portada lista para analizar';
        } else {
          title.textContent = 'Empieza a cargar tu evento';
        }
      }
    }

    function bindCoverAiCreateFlow() {
      const form = document.getElementById('eventForm');
      const thumbnailInput = document.querySelector('input[name="thumbnail"]');
      const emptyState = document.querySelector('[data-cover-ai-empty]');
      const readyState = document.querySelector('[data-cover-ai-ready]');
      const analyzeButton = document.querySelector('[data-cover-save-analyze]');
      const panel = document.getElementById('event-cover-ai-create');
      const statusBox = panel ? panel.querySelector('[data-create-ai-status]') : null;
      const progressPanel = panel ? panel.querySelector('[data-async-progress]') : null;
      const progressFill = panel ? panel.querySelector('[data-progress-fill]') : null;
      const progressBar = panel ? panel.querySelector('[data-progressbar]') : null;
      const results = panel ? panel.querySelector('[data-create-ai-results]') : null;
      const factsBox = panel ? panel.querySelector('[data-create-ai-facts]') : null;
      const guidanceBox = panel ? panel.querySelector('[data-create-ai-guidance]') : null;
      const summaryBox = panel ? panel.querySelector('[data-create-ai-summary]') : null;
      const applyButton = panel ? panel.querySelector('[data-create-ai-apply]') : null;
      let active = false;
      let lastReview = null;
      let progressTimer = null;
      let elapsedTimer = null;
      let startedAt = null;

      if (!form || !thumbnailInput) return;

      const toggleCoverState = function () {
        const hasCover = thumbnailInput.files && thumbnailInput.files.length > 0;

        if (emptyState) emptyState.classList.toggle('d-none', hasCover);
        if (readyState) readyState.classList.toggle('d-none', !hasCover);

      };

      thumbnailInput.addEventListener('change', toggleCoverState);
      thumbnailInput.addEventListener('change', function () {
        lastReview = null;
        if (results) results.classList.add('d-none');
        if (progressPanel) progressPanel.classList.add('d-none');
        setStatus('Portada lista. Podés analizarla antes de completar el resto del evento.', 'light');
      });

      if (analyzeButton) {
        analyzeButton.addEventListener('click', function (event) {
          event.preventDefault();
          analyzeTemporaryCover();
        });
      }

      if (applyButton) {
        applyButton.addEventListener('click', function () {
          applyDetectedFields();
        });
      }

      function analyzeTemporaryCover() {
        if (active) return;
        if (!panel || !thumbnailInput.files || !thumbnailInput.files.length) {
          setStatus('Subí una portada antes de analizarla con IA.', 'warning');
          return;
        }

        const file = thumbnailInput.files[0];
        const payload = buildAnalysisPayload(file);
        active = true;
        lastReview = null;
        panel.classList.remove('d-none');
        if (results) results.classList.add('d-none');
        analyzeButton.disabled = true;
        analyzeButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Analizando portada...';
        setStatus('Estamos leyendo la portada. No recargues la pagina; esto puede tardar unos segundos.', 'info');
        startProgress();

        $.ajax({
          url: panel.getAttribute('data-analysis-url'),
          method: 'POST',
          data: payload,
          processData: false,
          contentType: false
        }).done(function (response) {
          stopProgress();
          setProgress(100, 'Analisis listo', 'Ya organizamos los datos detectados en la portada.', 'success');
          lastReview = response.review || null;
          renderReview(lastReview);
          setStatus('Análisis listo. Revisá los datos detectados y aplicalos si te sirven.', 'success');
        }).fail(function (xhr) {
          stopProgress();
          setProgress(null, 'No se pudo analizar', errorMessage(xhr, 'No pudimos analizar la portada en este momento.'), 'danger');
          setStatus(errorMessage(xhr, 'No pudimos analizar la portada en este momento.'), 'danger');
        }).always(function () {
          active = false;
          analyzeButton.disabled = false;
          analyzeButton.innerHTML = '<i class="fas fa-magic mr-1"></i>Analizar portada con IA';
        });
      }

      function buildAnalysisPayload(file) {
        const payload = new FormData();
        const csrf = document.querySelector('meta[name="csrf-token"]');
        if (csrf) payload.append('_token', csrf.getAttribute('content'));
        payload.append('thumbnail', file);

        form.querySelectorAll('input, textarea, select').forEach(function (field) {
          if (!field.name || field.type === 'file') return;
          if ((field.type === 'radio' || field.type === 'checkbox') && !field.checked) return;
          payload.append(field.name, field.value || '');
        });

        return payload;
      }

      function startProgress() {
        startedAt = Date.now();
        let percent = 8;
        setProgress(percent, 'Preparando imagen', 'Validamos formato, tamaño y legibilidad de la portada.', 'info');
        clearInterval(progressTimer);
        clearInterval(elapsedTimer);

        progressTimer = setInterval(function () {
          percent = Math.min(percent + Math.floor(Math.random() * 7) + 3, 92);
          const stage = percent < 35 ? 'Leyendo textos del flyer' : (percent < 70 ? 'Detectando datos utiles' : 'Ordenando sugerencias');
          const message = percent < 35
            ? 'Buscamos titulo, fecha, lugar, horarios, promos y datos relevantes.'
            : (percent < 70 ? 'Separamos informacion util de marcas, logos y textos secundarios.' : 'Preparamos una guia para completar el formulario sin inventar datos.');
          setProgress(percent, stage, message, 'info');
        }, 1800);

        elapsedTimer = setInterval(function () {
          const elapsed = Math.floor((Date.now() - startedAt) / 1000);
          if (progressPanel) {
            progressPanel.querySelector('[data-progress-elapsed]').textContent = 'Tiempo transcurrido: ' + formatDuration(elapsed);
          }
        }, 1000);
      }

      function stopProgress() {
        clearInterval(progressTimer);
        clearInterval(elapsedTimer);
        progressTimer = null;
        elapsedTimer = null;
      }

      function setProgress(percent, stage, message, state) {
        if (!progressPanel) return;
        progressPanel.classList.remove('d-none', 'is-success', 'is-danger', 'is-indeterminate');
        if (state === 'success') progressPanel.classList.add('is-success');
        if (state === 'danger') progressPanel.classList.add('is-danger');
        progressPanel.querySelector('[data-progress-title]').textContent = state === 'success' ? 'Analisis de portada completado' : 'Analizando portada';
        progressPanel.querySelector('[data-progress-stage]').textContent = stage;
        progressPanel.querySelector('[data-progress-message]').textContent = message;
        progressPanel.querySelector('[data-progress-estimate]').textContent = 'Normalmente tarda entre 20 segundos y 2 minutos.';

        if (typeof percent === 'number') {
          progressPanel.querySelector('[data-progress-percent]').textContent = Math.round(percent) + '% estimado';
          progressFill.style.width = Math.max(0, Math.min(100, percent)) + '%';
          progressBar.setAttribute('aria-valuenow', Math.round(percent));
        } else {
          progressPanel.classList.add('is-indeterminate');
          progressPanel.querySelector('[data-progress-percent]').textContent = 'Revisar';
          progressFill.style.width = '100%';
          progressBar.removeAttribute('aria-valuenow');
        }
      }

      function renderReview(review) {
        const imageAnalysis = review && review.canonical_event_facts ? review.canonical_event_facts.image_analysis || {} : {};
        const facts = (imageAnalysis.extracted_fields || []).concat(imageAnalysis.sponsors || []).filter(function (field) {
          const value = $.trim(field.value || field.raw_text || '');
          const label = String(field.label || field.key || '').toLowerCase();
          return value && value !== '-' && label.indexOf('comparacion') === -1 && label.indexOf('comparación') === -1;
        });

        if (summaryBox) summaryBox.textContent = imageAnalysis.summary || 'Encontramos informacion que puede ayudarte a completar el evento.';
        if (factsBox) {
          factsBox.innerHTML = '';
          facts.slice(0, 18).forEach(function (field) {
            const row = document.createElement('div');
            row.className = 'create-cover-ai-fact';
            row.innerHTML = '<div><strong>' + escapeHtml(field.label || field.key) + '</strong><br><small class="text-muted">' + fieldMeta(field) + '</small></div>'
              + '<div class="create-cover-ai-fact__value">' + escapeHtml(field.value || field.raw_text) + '</div>';
            factsBox.appendChild(row);
          });
          if (!facts.length) factsBox.innerHTML = '<div class="p-3 text-muted">No encontramos datos claros para aplicar automaticamente.</div>';
        }
        if (guidanceBox) renderGuidance(guidanceBox, imageAnalysis);
        if (results) results.classList.remove('d-none');
      }

      function applyDetectedFields() {
        if (!lastReview || !lastReview.canonical_event_facts) return;

        const imageAnalysis = lastReview.canonical_event_facts.image_analysis || {};
        const fields = (imageAnalysis.extracted_fields || []).concat(imageAnalysis.sponsors || []);
        const title = pickField(fields, [/titulo del evento/i, /título del evento/i, /nombre del evento/i, /event.*title/i], [/subtitulo/i, /subtítulo/i]);
        const address = pickField(fields, [/direccion/i, /dirección/i, /ubicacion/i, /ubicación/i]);
        const startTime = pickField(fields, [/horario de inicio/i, /hora de inicio/i, /^inicio$/i]);
        const endTime = pickField(fields, [/horario de cierre/i, /hora de cierre/i, /hora de fin/i, /^cierre$/i]);
        const dateValue = pickField(fields, [/fecha/i], [/promocion/i, /promoción/i]);

        setIfEmpty('input[name$="_title"]', title);
        setIfEmpty('input[name$="_address"]', address);
        setIfEmpty('input[name$="_country"]', address ? 'Argentina' : '');
        setIfEmpty('input[name="start_time"]', parseTime(startTime));
        setIfEmpty('input[name="end_time"]', parseTime(endTime));
        setIfEmpty('input[name="start_date"]', parseDate(dateValue));
        setIfEmpty('input[name="end_date"]', parseDate(dateValue));
        setDescriptionIfEmpty(buildStarterDescription(imageAnalysis, title, address));
        setCategoryFromText([title, imageAnalysis.summary].concat(imageAnalysis.found_information || []).join(' '));

        updateCreateChecklist();
        setStatus('Aplicamos los datos claros en campos vacios. Revisalos y completá lo que falte antes de guardar.', 'success');
      }

      function setIfEmpty(selector, value) {
        if (!value) return;
        const field = document.querySelector(selector);
        if (!field || $.trim(field.value || '') !== '') return;
        field.value = value;
        field.dispatchEvent(new Event('input', { bubbles: true }));
        field.dispatchEvent(new Event('change', { bubbles: true }));
      }

      function setDescriptionIfEmpty(value) {
        if (!value) return;
        const field = document.querySelector('textarea[name$="_description"]');
        if (!field || $.trim(field.value || '') !== '') return;
        const html = '<p>' + escapeHtml(value).replace(/\n/g, '<br>') + '</p>';
        if ($.fn.summernote && $(field).next('.note-editor').length) {
          $(field).summernote('code', html);
        } else {
          field.value = value;
        }
        field.dispatchEvent(new Event('input', { bubbles: true }));
        field.dispatchEvent(new Event('change', { bubbles: true }));
      }

      function setCategoryFromText(text) {
        const select = document.querySelector('select[name$="_category_id"]');
        if (!select || select.value) return;

        const normalizedText = normalizeText(text);
        let fallback = null;
        Array.from(select.options).forEach(function (option) {
          if (!option.value || option.disabled) return;
          const optionText = normalizeText(option.textContent || '');
          if (!fallback && /fiesta|show|concierto|musica|música|festival|rumba|reggaeton|boliche/.test(normalizedText) && /fiesta|show|concierto|musica|festival|evento/.test(optionText)) {
            fallback = option.value;
          }
          if (!select.value && optionText && normalizedText.indexOf(optionText) !== -1) {
            select.value = option.value;
          }
        });

        if (!select.value && fallback) select.value = fallback;
        if (select.value) select.dispatchEvent(new Event('change', { bubbles: true }));
      }

      function buildStarterDescription(imageAnalysis, title, address) {
        const lines = [];
        if (title) lines.push(title);
        (imageAnalysis.found_information || []).slice(0, 4).forEach(function (item) { if (item) lines.push(item); });
        (imageAnalysis.complementary_information || []).slice(0, 2).forEach(function (item) { if (item) lines.push(item); });
        if (address && !lines.join(' ').includes(address)) lines.push('Lugar: ' + address + '.');
        return lines.join('\n');
      }

      function pickField(fields, patterns, exclusions) {
        exclusions = exclusions || [];
        const field = fields.find(function (candidate) {
          const haystack = String((candidate.key || '') + ' ' + (candidate.label || '')).toLowerCase();
          const value = $.trim(candidate.value || candidate.raw_text || '');
          if (!value || value === '-') return false;
          if (exclusions.some(function (pattern) { return pattern.test(haystack); })) return false;
          return patterns.some(function (pattern) { return pattern.test(haystack); });
        });
        return field ? $.trim(field.value || field.raw_text || '') : '';
      }

      function parseDate(value) {
        value = $.trim(value || '');
        let match = value.match(/(\d{4})-(\d{2})-(\d{2})/);
        if (match) return match[1] + '-' + match[2] + '-' + match[3];
        match = value.match(/\b(\d{1,2})[\/.-](\d{1,2})[\/.-](\d{4})\b/);
        if (match) return match[3] + '-' + String(match[2]).padStart(2, '0') + '-' + String(match[1]).padStart(2, '0');
        return '';
      }

      function parseTime(value) {
        value = $.trim(value || '').toLowerCase();
        const match = value.match(/\b(\d{1,2})(?::(\d{2}))?\s*(am|pm|hs|h)?\b/);
        if (!match) return '';
        let hour = parseInt(match[1], 10);
        const minute = match[2] || '00';
        const suffix = match[3] || '';
        if (suffix === 'pm' && hour < 12) hour += 12;
        if (suffix === 'am' && hour === 12) hour = 0;
        if (hour > 23) return '';
        return String(hour).padStart(2, '0') + ':' + minute;
      }

      function renderGuidance(target, imageAnalysis) {
        const items = []
          .concat(imageAnalysis.found_information || [])
          .concat(imageAnalysis.complementary_information || [])
          .concat(imageAnalysis.optional_suggestions || [])
          .concat(imageAnalysis.missing_information || [])
          .slice(0, 8);

        target.innerHTML = items.length
          ? '<div class="alert alert-info mb-0 small"><strong>Guia para completar el evento</strong><ul class="mb-0 mt-2 pl-3">' + items.map(function (item) { return '<li>' + escapeHtml(item) + '</li>'; }).join('') + '</ul></div>'
          : '';
      }

      function fieldMeta(field) {
        const confidence = Math.round((Number(field.confidence || 0)) * 100);
        const relation = String(field.category || '').toLowerCase();
        let label = 'detectado';
        if (field.needs_review || relation.indexOf('critica') !== -1 || relation.indexOf('crítica') !== -1) label = 'conviene confirmar';
        else if (relation.indexOf('compatible') !== -1) label = 'compatible';
        else if (relation.indexOf('complement') !== -1) label = 'complementa';
        else if (relation.indexOf('sponsor') !== -1 || relation.indexOf('marca') !== -1) label = 'marca visible';
        else if (relation.indexOf('coincid') !== -1) label = 'coincide';
        return (confidence > 0 ? confidence + '% · ' : '') + label;
      }

      function setStatus(message, type) {
        if (!panel || !statusBox) return;
        panel.classList.remove('d-none');
        statusBox.className = 'alert mb-3 alert-' + (type || 'light');
        if (type === 'light') statusBox.className += ' border';
        statusBox.textContent = message;
      }

      function errorMessage(xhr, fallback) {
        return (xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON.error)) || fallback;
      }

      function escapeHtml(value) {
        return $('<div>').text(value || '').html();
      }

      function formatDuration(seconds) {
        seconds = Math.max(0, Number(seconds || 0));
        const minutes = Math.floor(seconds / 60);
        const remaining = seconds % 60;
        return minutes ? (minutes + 'm ' + String(remaining).padStart(2, '0') + 's') : (remaining + 's');
      }

      function normalizeText(value) {
        return String(value || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
      }

      toggleCoverState();
    }

    bindCoverAiCreateFlow();
    document.addEventListener('input', updateCreateChecklist);
    document.addEventListener('change', updateCreateChecklist);
    setInterval(updateCreateChecklist, 1200);
    setTimeout(updateCreateChecklist, 300);
  </script>
@endsection

@section('variables')
  <script>
    "use strict";
    var storeUrl = "{{ route('admin.event.imagesstore') }}";
    var removeUrl = "{{ route('admin.event.imagermv') }}";
    var loadImgs = 0;
  </script>
@endsection
