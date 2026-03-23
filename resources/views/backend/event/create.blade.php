@extends('backend.layout')

@section('content')
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
                      <div class="font-weight-bold" id="createChecklistCount">0/7 {{ __('puntos listos') }}</div>
                      <small class="text-muted">{{ __('Ideal para la primera publicacion.') }}</small>
                    </div>
                  </div>
                  <div class="progress mb-4" style="height: 10px;">
                    <div class="progress-bar bg-warning" id="createChecklistProgress" role="progressbar" style="width: 0%;"></div>
                  </div>
                  <div class="row">
                    <div class="col-lg-6 mb-3"><div class="border rounded p-3 h-100" id="check-thumbnail"><div class="font-weight-bold mb-1">{{ __('Imagen de portada') }}</div><small class="text-muted">{{ __('Sube una portada clara y facil de reconocer.') }}</small></div></div>
                    <div class="col-lg-6 mb-3"><div class="border rounded p-3 h-100" id="check-gallery"><div class="font-weight-bold mb-1">{{ __('Galeria') }}</div><small class="text-muted">{{ __('Suma imagenes extra para que la publicacion se vea mas completa.') }}</small></div></div>
                    <div class="col-lg-6 mb-3"><div class="border rounded p-3 h-100" id="check-dates"><div class="font-weight-bold mb-1">{{ __('Fechas') }}</div><small class="text-muted">{{ __('Define bien cuando ocurre el evento.') }}</small></div></div>
                    <div class="col-lg-6 mb-3"><div class="border rounded p-3 h-100" id="check-title"><div class="font-weight-bold mb-1">{{ __('Titulo') }}</div><small class="text-muted">{{ __('Usa un nombre claro y facil de entender.') }}</small></div></div>
                    <div class="col-lg-6 mb-3"><div class="border rounded p-3 h-100" id="check-description"><div class="font-weight-bold mb-1">{{ __('Descripcion') }}</div><small class="text-muted">{{ __('Cuenta que incluye la entrada, horarios y datos clave.') }}</small></div></div>
                    <div class="col-lg-6 mb-3"><div class="border rounded p-3 h-100" id="check-sales"><div class="font-weight-bold mb-1">{{ __('Venta o acceso') }}</div><small class="text-muted">{{ __('Revisa precio, gratuidad o acceso online segun el tipo de evento.') }}</small></div></div>
                    <div class="col-lg-6 mb-0"><div class="border rounded p-3 h-100" id="check-status"><div class="font-weight-bold mb-1">{{ __('Estado') }}</div><small class="text-muted">{{ __('Elige si quieres dejarlo activo o seguir trabajandolo.') }}</small></div></div>
                  </div>
                </div>
              </div>

              <div class="col-lg-12">
                <label for="" class="mb-2"><strong>{{ __('Imagenes de la galeria') }} **</strong></label>
                <form action="{{ route('admin.event.imagesstore') }}" id="my-dropzone" enctype="multipart/formdata"
                  class="dropzone create">
                  @csrf
                  <div class="fallback">
                    <input name="file" type="file" multiple />
                  </div>
                </form>
                <div class=" mb-0" id="errpreimg">

                </div>
                <p class="text-warning">{{ __('La galeria acepta imagenes horizontales, cuadradas o verticales. Minimo aceptado: 600x450. Recomendado: 1170x570 o mas para mejor calidad.') }}</p>
              </div>
              <form id="eventForm" action="{{ route('admin.event_management.store_event') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="event_type" value="{{ request()->input('type') }}">
                <div class="event-cover-box mb-4">
                  <div class="event-cover-box__intro">
                    <span class="event-cover-box__eyebrow">{{ __('Portada principal') }}</span>
                    <h4 class="event-cover-box__title">{{ __('Imagen de portada') }}*</h4>
                    <p class="event-cover-box__text">{{ __('El sistema acepta cualquier tamano. Se respeta la proporcion original del flyer para que no se corte ni se deforme.') }}</p>
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
                        <input type="file" class="img-input" name="thumbnail">
                      </label>
                      <small class="event-cover-box__hint">{{ __('Puedes usar una imagen horizontal, cuadrada o vertical. Lo importante es que se vea bien y se lea claro.') }}</small>
                    </div>
                  </div>
                </div>

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
                        <option selected disabled>{{ __('Selecciona un estado') }}</option>
                        <option value="1">{{ __('Active') }}</option>
                        <option value="0">{{ __('Oculto') }}</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="form-group">
                      <label for="">{{ __('Evento destacado') . '*' }}</label>
                      <select name="is_featured" class="form-control">
                        <option selected disabled>{{ __('Selecciona una opcion') }}</option>
                        <option value="yes">{{ __('Si') }}</option>
                        <option value="no">{{ __('No') }}</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="form-group">
                      <label for="">{{ __('Organizador') }}</label>
                      <select name="organizer_id" class="form-control js-example-basic-single">
                        <option selected value="">{{ __('Selecciona un organizador') }}</option>
                        @foreach ($organizers as $organizer)
                          <option value="{{ $organizer->id }}">{{ $organizer->username }}</option>
                        @endforeach
                      </select>
                      <p class="text-warning">{{ __('Dejalo vacio solo si quieres que quede como evento propio del admin.') }}</p>
                    </div>
                  </div>
                  @if (request()->input('type') == 'venue')
                    <div class="col-lg-4">
                      <div class="form-group">
                        <label for="">{{ __('Latitude') }}</label>
                        <input type="text" name="latitude" placeholder="{{ __('Latitud') }}" class="form-control">
                      </div>
                    </div>
                    <div class="col-lg-4">
                      <div class="form-group">
                        <label for="">{{ __('Longitude') }}</label>
                        <input type="text" placeholder="{{ __('Longitud') }}" name="longitude" class="form-control">
                      </div>
                    </div>
                  @endif
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
                              <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                <label>{{ __('Titulo del evento') . '*' }}</label>
                                <input type="text" class="form-control" name="{{ $language->code }}_title"
                                  placeholder="{{ __('Ej: Festival de invierno en Buenos Aires') }}">
                              </div>
                            </div>

                            <div class="col-lg-6">
                              <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
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
                                    class="form-control {{ $language->direction == 1 ? 'rtl text-right' : '' }}"
                                    placeholder="{{ __('Ej: Av. Corrientes 1234') }}">
                                </div>
                              </div>
                              <div class="col-lg-4">
                                <div class="form-group">
                                  <label for="">{{ __('Pais') . '*' }}</label>
                                  <input type="text" name="{{ $language->code }}_country"
                                    placeholder="{{ __('Ej: Argentina') }}"
                                    class="form-control {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                </div>
                              </div>
                              <div class="col-lg-4">
                                <div class="form-group">
                                  <label for="">{{ __('Provincia') }}</label>
                                  <input type="text" name="{{ $language->code }}_state"
                                    class="form-control {{ $language->direction == 1 ? 'rtl text-right' : '' }}"
                                    placeholder="{{ __('Ej: Buenos Aires') }}">
                                </div>
                              </div>
                              <div class="col-lg-4">
                                <div class="form-group">
                                  <label for="">{{ __('Ciudad') . '*' }}</label>
                                  <input type="text" name="{{ $language->code }}_city"
                                    class="form-control {{ $language->direction == 1 ? 'rtl text-right' : '' }}"
                                    placeholder="{{ __('Ej: CABA') }}">
                                </div>
                              </div>
                              <div class="col-lg-4">
                                <div class="form-group">
                                  <label for="">{{ __('Codigo postal') }}</label>
                                  <input type="text" placeholder="{{ __('Ej: C1043') }}"
                                    name="{{ $language->code }}_zip_code"
                                    class="form-control {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                </div>
                              </div>
                            </div>
                          @endif

                          <div class="row">
                            <div class="col">
                              <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
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
                                <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                  <label>{{ __('Politica de reembolso') }}</label>
                                  <textarea class="form-control" name="{{ $language->code }}_refund_policy" rows="5"
                                    placeholder="{{ __('Explica que pasa si alguien no puede asistir, pide un cambio o solicita devolucion.') }}"></textarea>
                                </div>
                              </div>
                            </div>

                            <div class="row">
                              <div class="col-lg-12">
                                <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                  <label>{{ __('Palabras clave para Google') }}</label>
                                  <input class="form-control" name="{{ $language->code }}_meta_keywords"
                                    placeholder="{{ __('Ej: festival, buenos aires, musica en vivo') }}" data-role="tagsinput">
                                </div>
                              </div>
                            </div>

                            <div class="row">
                              <div class="col-lg-12">
                                <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
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
      min-height: 170px;
      padding: 24px;
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
      width: 68px;
      height: 68px;
      margin: 0 auto 14px;
      border-radius: 20px;
      background-color: #e8f1ff;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232564eb' stroke-width='1.8' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4'/%3E%3Cpolyline points='17 8 12 3 7 8'/%3E%3Cline x1='12' y1='3' x2='12' y2='15'/%3E%3Cpath d='M8 15a4 4 0 0 1 .9-7.9A5 5 0 0 1 18.8 8A3.5 3.5 0 0 1 19 15'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: center;
      background-size: 34px 34px;
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
      content: "Subi las imagenes del evento";
      display: block;
      font-size: 17px;
      margin-bottom: 6px;
    }

    #my-dropzone .dz-message span::after {
      content: "Arrastralas aqui o hace clic para elegirlas. Puedes seguir sumando imagenes aunque ya hayas cargado una.";
      display: block;
      font-size: 13px;
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
      max-width: 360px;
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
      const galleryInputs = document.querySelectorAll('#sliders input[name="slider_images[]"]');
      const isSingle = document.querySelector('input[name="date_type"]:checked')?.value === 'single';
      const priceInput = document.querySelector('input[name="price"]');
      const freeInput = document.getElementById('free_ticket');
      const meetingUrlInput = document.querySelector('input[name="meeting_url"]');

      const titleOk = titleInput && titleInput.value.trim().length >= 8;
      const descriptionText = descriptionInput ? descriptionInput.value.replace(/<[^>]*>/g, '').trim() : '';
      const descriptionOk = descriptionText.length >= 80;
      const thumbnailOk = thumbnailInput && thumbnailInput.files && thumbnailInput.files.length > 0;
      const galleryOk = galleryInputs.length > 0;
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
        gallery: !!galleryOk,
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

      const total = 7;
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
        } else {
          title.textContent = 'Empieza a cargar tu evento';
        }
      }
    }

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
