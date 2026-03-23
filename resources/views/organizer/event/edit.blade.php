@extends('organizer.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Edit Event') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ route('organizer.dashboard') }}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Event Management') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a
          href="{{ route('organizer.event_management.event', ['language' => $defaultLang->code]) }}">{{ __('All Events') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      @php
        $event_title = DB::table('event_contents')
            ->where('language_id', $defaultLang->id)
            ->where('event_id', $event->id)
            ->select('title')
            ->first();
        if (empty($event_title)) {
            $event_title = DB::table('event_contents')
                ->where('event_id', $event->id)
                ->select('title')
                ->first();
        }

        $defaultEventContent = DB::table('event_contents')
            ->where('language_id', $defaultLang->id)
            ->where('event_id', $event->id)
            ->first();

        if (empty($defaultEventContent)) {
            $defaultEventContent = DB::table('event_contents')
                ->where('event_id', $event->id)
                ->first();
        }

        $galleryCount = DB::table('event_images')->where('event_id', $event->id)->count();
        $multipleDateCount = DB::table('event_dates')->where('event_id', $event->id)->count();

        $hasTitle = !empty(optional($defaultEventContent)->title) && mb_strlen(trim($defaultEventContent->title)) >= 8;
        $hasDescription = !empty(optional($defaultEventContent)->description) && mb_strlen(trim(strip_tags($defaultEventContent->description))) >= 80;
        $hasRefundPolicy = !empty(optional($defaultEventContent)->refund_policy) && mb_strlen(trim($defaultEventContent->refund_policy)) >= 20;
        $hasThumbnail = !empty($event->thumbnail);
        $hasGallery = $galleryCount > 0;
        $hasDates = $event->date_type === 'single' ? !empty($event->start_date) && !empty($event->end_date) : $multipleDateCount > 0;
        $hasSalesSetup = $event->event_type === 'online'
            ? !empty(optional($event->ticket)->pricing_type) || !is_null(optional($event->ticket)->price)
            : $event->tickets()->count() > 0;
        $isPublishedReady = (int) $event->status === 1;

        $publicationChecks = [
            ['done' => $hasThumbnail, 'label' => __('Miniatura cargada'), 'help' => __('Una buena portada mejora mucho la confianza del comprador.')],
            ['done' => $hasGallery, 'label' => __('Galeria con imagenes'), 'help' => __('Sube al menos una imagen adicional para que la publicacion se vea mas completa.')],
            ['done' => $hasDates, 'label' => __('Fechas configuradas'), 'help' => __('El comprador necesita ver claramente cuando ocurre el evento.')],
            ['done' => $hasTitle, 'label' => __('Titulo claro'), 'help' => __('Intenta usar nombre del evento, artista o ciudad si aplica.')],
            ['done' => $hasDescription, 'label' => __('Descripcion util'), 'help' => __('Explica que incluye la entrada, horarios, acceso y detalles importantes.')],
            ['done' => $hasSalesSetup, 'label' => __('Venta configurada'), 'help' => __('Revisa precio, disponibilidad o tickets antes de publicar.')],
            ['done' => $hasRefundPolicy, 'label' => __('Politica de reembolso'), 'help' => __('Aclara que pasa si alguien no puede asistir o necesita cambios.')],
            ['done' => $isPublishedReady, 'label' => __('Estado activo'), 'help' => __('Cuando todo este listo, activa el evento para que pueda verse.')],
        ];

        $completedChecks = collect($publicationChecks)->where('done', true)->count();
        $totalChecks = count($publicationChecks);
        $publicationScore = (int) round(($completedChecks / max($totalChecks, 1)) * 100);
        $publicationTone = $publicationScore >= 85 ? 'success' : ($publicationScore >= 60 ? 'warning' : 'danger');
        $publicationHeadline = $publicationScore >= 85 ? __('Muy bien encaminado') : ($publicationScore >= 60 ? __('Vas bien, pero aun falta') : __('Todavia le falta informacion clave'));
        $singleLanguageMode = isset($languages) && count($languages) === 1;

      @endphp
      <li class="nav-item">
        <a href="#">
          {{ strlen($event_title->title) > 35 ? mb_substr($event_title->title, 0, 35, 'UTF-8') . '...' : $event_title->title }}
        </a>

      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Edit Event') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card border-0 shadow-none bg-transparent">
        <div class="card-header p-0 bg-transparent border-0 mb-4">
          <div class="d-flex justify-content-between align-items-center">
            <a class="btn btn-secondary btn-sm" href="{{ url()->previous() }}">
              <span class="btn-label"><i class="fas fa-backward"></i></span> {{ __('Back') }}
            </a>
            <div>
              <a class="btn btn-success btn-sm mr-2"
                href="{{ route('event.details', ['slug' => eventSlug($defaultLang->id, $event->id), 'id' => $event->id]) }}"
                target="_blank">
                <span class="btn-label"><i class="fas fa-eye"></i></span> {{ __('Preview') }}
              </a>
              @if ($event->event_type == 'venue')
                <a class="btn btn-info btn-sm mr-2"
                  href="{{ route('organizer.event.ticket', ['language' => $defaultLang->code, 'event_id' => $event->id, 'event_type' => $event->event_type]) }}"
                  target="_blank">
                  <span class="btn-label"><i class="far fa-ticket"></i></span> {{ __('Tickets') }}
                </a>
              @endif
              <button type="submit" id="EventSubmitTop" class="btn btn-primary btn-sm" onclick="document.getElementById('EventSubmit').click(); return false;">
                <span class="btn-label"><i class="fas fa-save"></i></span> {{ __('Update') }}
              </button>
            </div>
          </div>
        </div>

        <div class="card-body p-0">
          <div class="row">
            <div class="col-lg-8 offset-lg-2">
              <div class="alert alert-danger pb-1 dis-none" id="eventErrors">
                <button type="button" class="close" data-dismiss="alert">x</button>
                <ul></ul>
              </div>
              <div class="card border mb-4">
                <div class="card-body py-3">
                  <div class="mb-3">
                    <span class="badge badge-primary mb-2">{{ __('Guia rapida') }}</span>
                    <h5 class="mb-1">{{ __('Edita tu evento paso a paso') }}</h5>
                    <p class="text-muted mb-0">{{ __('Sigue este orden para no perderte: imagenes, fechas, configuracion, contenido y extras.') }}</p>
                  </div>
                  <div class="row">
                    <div class="col-md-6 col-xl-4 mb-2">
                      <a href="#section-media" class="btn btn-light btn-block text-left py-3">
                        <span class="badge badge-primary mr-2">1</span>{{ __('Imagenes') }}
                        <small class="d-block text-muted mt-1">{{ __('Galeria y miniatura') }}</small>
                      </a>
                    </div>
                    <div class="col-md-6 col-xl-4 mb-2">
                      <a href="#section-schedule" class="btn btn-light btn-block text-left py-3">
                        <span class="badge badge-primary mr-2">2</span>{{ __('Fechas') }}
                        <small class="d-block text-muted mt-1">{{ __('Fecha unica o multiples funciones') }}</small>
                      </a>
                    </div>
                    <div class="col-md-6 col-xl-4 mb-2">
                      <a href="#section-settings" class="btn btn-light btn-block text-left py-3">
                        <span class="badge badge-primary mr-2">3</span>{{ __('Configuracion') }}
                        <small class="d-block text-muted mt-1">{{ __('Estado, visibilidad y venta') }}</small>
                      </a>
                    </div>
                    <div class="col-md-6 col-xl-4 mb-2">
                      <a href="#section-content" class="btn btn-light btn-block text-left py-3">
                        <span class="badge badge-primary mr-2">4</span>{{ __('Contenido') }}
                        <small class="d-block text-muted mt-1">{{ __('Titulos, descripcion y SEO') }}</small>
                      </a>
                    </div>
                    <div class="col-md-6 col-xl-4 mb-2">
                      <a href="#section-media-links" class="btn btn-light btn-block text-left py-3">
                        <span class="badge badge-primary mr-2">5</span>{{ __('Multimedia') }}
                        <small class="d-block text-muted mt-1">{{ __('Spotify y YouTube') }}</small>
                      </a>
                    </div>
                    <div class="col-md-6 col-xl-4 mb-2">
                      <a href="#section-tracking" class="btn btn-light btn-block text-left py-3">
                        <span class="badge badge-primary mr-2">6</span>{{ __('Pixeles') }}
                        <small class="d-block text-muted mt-1">{{ __('Meta, Google y TikTok') }}</small>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card ev-section-card">
                <div class="card-header ev-section-header">
                  <h4 class="card-title"><i class="fas fa-clipboard-check mr-2 text-primary"></i>{{ __('Checklist de publicacion') }}</h4>
                </div>
                <div class="card-body">
                  @if ($publicationScore >= 100)
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                      <div class="mb-3 mb-md-0">
                        <span class="badge badge-success mb-2">{{ __('Listo para vender') }}</span>
                        <h5 class="mb-1">{{ __('El evento ya esta bien armado') }}</h5>
                        <p class="text-muted mb-0">{{ __('En esta etapa ya no necesitas una checklist larga. Solo revisa cambios puntuales y guarda cuando termines.') }}</p>
                      </div>
                      <div class="text-md-right">
                        <div class="font-weight-bold">{{ $completedChecks }}/{{ $totalChecks }} {{ __('puntos completos') }}</div>
                        <small class="text-muted">{{ __('La checklist detallada tiene mas sentido durante la creacion.') }}</small>
                      </div>
                    </div>
                  @else
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
                      <div class="mb-3 mb-md-0">
                        <span class="badge badge-{{ $publicationTone }} mb-2">{{ $publicationScore }}% {{ __('completo') }}</span>
                        <h5 class="mb-1">{{ $publicationHeadline }}</h5>
                        <p class="text-muted mb-0">{{ __('Usa esta guia para revisar rapido si tu evento ya se entiende bien y esta listo para vender.') }}</p>
                      </div>
                      <div class="text-md-right">
                        <div class="font-weight-bold">{{ $completedChecks }}/{{ $totalChecks }} {{ __('puntos listos') }}</div>
                        <small class="text-muted">{{ __('No bloquea el guardado. Solo te orienta.') }}</small>
                      </div>
                    </div>
                    <div class="progress mb-4" style="height: 10px;">
                      <div class="progress-bar bg-{{ $publicationTone }}" role="progressbar" style="width: {{ $publicationScore }}%;"
                        aria-valuenow="{{ $publicationScore }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="row">
                      @foreach ($publicationChecks as $check)
                        <div class="col-lg-6 mb-3">
                          <div class="border rounded p-3 h-100 {{ $check['done'] ? 'border-success' : 'border-light' }}">
                            <div class="d-flex align-items-start">
                              <span class="mr-2 mt-1 text-{{ $check['done'] ? 'success' : 'muted' }}">
                                <i class="fas {{ $check['done'] ? 'fa-check-circle' : 'fa-circle' }}"></i>
                              </span>
                              <div>
                                <div class="font-weight-bold mb-1">{{ $check['label'] }}</div>
                                <small class="text-muted d-block">{{ $check['help'] }}</small>
                              </div>
                            </div>
                          </div>
                        </div>
                      @endforeach
                    </div>
                    @if ($publicationScore < 85)
                      <div class="alert alert-light border mb-0">
                        <strong>{{ __('Consejo rapido:') }}</strong>
                        {{ __('Antes de publicar, prioriza imagenes, descripcion y fechas. Son las tres cosas que mas ayudan a vender y evitar dudas.') }}
                      </div>
                    @endif
                  @endif
                </div>
              </div>
              <div class="card ev-section-card">
                <div class="card-header ev-section-header">
                  <h4 class="card-title"><i class="fas fa-images mr-2 text-primary"></i>{{ __('Imagenes del evento') }}</h4>
                </div>
              <div class="card-body">
              <div id="section-media" class="mb-3">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center compact-media-toolbar">
                  <div class="pr-lg-3 mb-2 mb-lg-0">
                    <p class="text-muted mb-1">{{ __('Carga galeria y miniatura antes de seguir con el resto del formulario.') }}</p>
                    <small class="text-muted">{{ __('Usa imagenes limpias y evita flyers con texto demasiado chico.') }}</small>
                  </div>
                  <div class="d-flex flex-wrap align-items-center">
                    <span class="badge badge-light border px-3 py-2 mr-2 mb-2 mb-lg-0">{{ $galleryCount }} {{ __('imagenes cargadas') }}</span>
                    <small class="text-muted mb-0">{{ __('Recomendado: 1170x570 o mas, sin recortar el flyer') }}</small>
                  </div>
                </div>
              </div>
              <div class="col-lg-12 px-0">
                <label class="ev-label-section">{{ __('Imagenes de la galeria') }} <span class="text-warning">**</span></label>
                <div id="reload-slider-div">
                  <div class="row mt-2">
                    <div class="col">
                      <table class="table mb-0" id="img-table">

                      </table>
                    </div>
                  </div>
                </div>
                <div class="media-upload-separator d-flex align-items-center my-4">
                  <span class="text-muted small pr-3">{{ __('Agregar mas imagenes') }}</span>
                  <div class="flex-grow-1 border-top"></div>
                </div>
                <form action="{{ route('organizer.event.imagesstore') }}" id="my-dropzone" enctype="multipart/formdata"
                  class="dropzone create">
                  @csrf
                  <div class="fallback">
                    <input name="file" type="file" multiple />
                  </div>
                  <input type="hidden" value="{{ $event->id }}" name="event_id">
                </form>
                <div class=" mb-0" id="errpreimg">

                </div>
                <p class="text-warning small mt-2 mb-0">{{ __('La galeria acepta imagenes horizontales, cuadradas o verticales. Minimo aceptado: 600x450. Recomendado: 1170x570 o mas para mejor calidad.') }}</p>
              </div>
              </div>
              </div>

              <form id="eventForm" action="{{ route('organizer.event.update') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="event_id" value="{{ $event->id }}">
                <input type="hidden" name="event_type" value="{{ $event->event_type }}">
                <input type="hidden" name="gallery_images" value="0">
                <div class="card ev-section-card">
                  <div class="card-header ev-section-header">
                    <h4 class="card-title"><i class="fas fa-calendar-alt mr-2 text-primary"></i>{{ __('Fechas y horarios') }}</h4>
                  </div>
                  <div class="card-body">
                <div class="event-cover-box mb-4">
                  <div class="event-cover-box__intro">
                    <span class="event-cover-box__eyebrow">{{ __('Portada principal') }}</span>
                    <h4 class="event-cover-box__title">{{ __('Imagen de portada') }}*</h4>
                    <p class="event-cover-box__text">{{ __('El sistema acepta cualquier tamano. Se respeta la proporcion original del flyer para que no se corte ni se deforme.') }}</p>
                  </div>
                  <div class="event-cover-box__body">
                    <div class="thumb-preview event-cover-box__preview">
                      <img
                        src="{{ $event->thumbnail ? asset('assets/admin/img/event/thumbnail/' . $event->thumbnail) : asset('assets/admin/img/noimage.jpg') }}"
                        alt="..." class="uploaded-img ev-thumbnail-preview">
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
                <div id="section-schedule" class="mb-3">
                  <p class="text-muted mb-0">{{ __('Define si el evento tiene una sola fecha o varias funciones.') }}</p>
                </div>

                <div class="row">
                  <div class="col-lg-12">
                    <div class="form-group mt-1">
                      <label for="">{{ __('Tipo de fecha') . '*' }}</label>
                      <div class="selectgroup w-100">
                        <label class="selectgroup-item">
                          <input type="radio" name="date_type" {{ $event->date_type == 'single' ? 'checked' : '' }}
                            value="single" class="selectgroup-input eventDateType" checked>
                          <span class="selectgroup-button">{{ __('Fecha unica') }}</span>
                        </label>

                        <label class="selectgroup-item">
                          <input type="radio" name="date_type" {{ $event->date_type == 'multiple' ? 'checked' : '' }}
                            value="multiple" class="selectgroup-input eventDateType">
                          <span class="selectgroup-button">{{ __('Varias fechas') }}</span>
                        </label>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row countDownStatus {{ $event->date_type == 'multiple' ? 'd-none' : '' }} ">
                  <div class="col-lg-12">
                    <div class="form-group mt-1">
                      <label for="">{{ __('Contador regresivo') . '*' }}</label>
                      <div class="selectgroup w-100">
                        <label class="selectgroup-item">
                          <input type="radio" name="countdown_status" value="1" class="selectgroup-input"
                            {{ $event->countdown_status == 1 ? 'checked' : '' }}>
                          <span class="selectgroup-button">{{ __('Active') }}</span>
                        </label>

                        <label class="selectgroup-item">
                          <input type="radio" name="countdown_status" value="0" class="selectgroup-input"
                            {{ $event->countdown_status == 0 ? 'checked' : '' }}>
                          <span class="selectgroup-button">{{ __('Oculto') }}</span>
                        </label>
                      </div>
                    </div>
                  </div>
                </div>

                {{-- single dates --}}
                <div class="row {{ $event->date_type == 'multiple' ? 'd-none' : '' }}" id="single_dates">
                  <div class="col-lg-3">
                    <div class="form-group">
                      <label>{{ __('Start Date') . '*' }}</label>
                      <input type="date" name="start_date" value="{{ $event->start_date }}"
                        placeholder="Enter Start Date" class="form-control">
                    </div>
                  </div>

                  <div class="col-lg-3">
                    <div class="form-group">
                      <label for="">{{ __('Start Time') . '*' }}</label>
                      <input type="time" name="start_time" value="{{ $event->start_time }}" class="form-control">
                    </div>
                  </div>

                  <div class="col-lg-3">
                    <div class="form-group">
                      <label>{{ __('End Date') . '*' }}</label>
                      <input type="date" name="end_date" value="{{ $event->end_date }}"
                        placeholder="Enter End Date" class="form-control">
                    </div>
                  </div>

                  <div class="col-lg-3">
                    <div class="form-group">
                      <label for="">{{ __('End Time') . '*' }}</label>
                      <input type="time" name="end_time" value="{{ $event->end_time }}" class="form-control">
                    </div>
                  </div>
                </div>

                {{-- multiple dates --}}
                <div class="row">
                  <div class="col-lg-12 {{ $event->date_type == 'single' ? 'd-none' : '' }}" id="multiple_dates">
                    @if ($event->date_type == 'multiple')
                      @php
                        $event_dates = $event->dates()->get();
                      @endphp
                    @else
                      @php
                        $event_dates = [];
                      @endphp
                    @endif
                    <div class="form-group">
                      <div class="table-responsive">
                        <table class="table table-bordered">
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
                            @if (count($event_dates) > 0)
                              @foreach ($event_dates as $date)
                                <tr>
                                  <td>
                                    <div class="form-group">
                                      <label for="">{{ __('Start Date') . '*' }}</label>
                                      <input type="date" name="m_start_date[]" class="form-control"
                                        value="{{ $date->start_date }}">
                                    </div>
                                  </td>
                                  <td>
                                    <div class="form-group">
                                      <label for="">{{ __('Start Time') . '*' }}</label>
                                      <input type="time" name="m_start_time[]" class="form-control"
                                        value="{{ $date->start_time }}">
                                    </div>
                                  </td>
                                  <td>
                                    <div class="form-group">
                                      <label for="">{{ __('End Date') . '*' }}
                                      </label>
                                      <input type="date" name="m_end_date[]" class="form-control"
                                        value="{{ $date->end_date }}">
                                    </div>
                                  </td>
                                  <td>
                                    <div class="form-group">
                                      <label for="">{{ __('End Time') . '*' }}
                                      </label>
                                      <input type="time" name="m_end_time[]" class="form-control"
                                        value="{{ $date->end_time }}">
                                    </div>
                                  </td>
                                  <input type="hidden" name="date_ids[]" value="{{ $date->id }}">
                                  <td>
                                    <a href="javascript:void(0)"
                                      data-url="{{ route('organizer.event.delete.date', $date->id) }}"
                                      class="btn btn-danger deleteDateDbRow">
                                      <i class="fas fa-minus"></i></a>
                                  </td>
                                </tr>
                              @endforeach
                            @else
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
                                    <label for="">{{ __('End Date') . '*' }}
                                    </label>
                                    <input type="date" name="m_end_date[]" class="form-control">
                                  </div>
                                </td>
                                <td>
                                  <div class="form-group">
                                    <label for="">{{ __('End Time') . '*' }}
                                    </label>
                                    <input type="time" name="m_end_time[]" class="form-control">
                                  </div>
                                </td>
                                <td>
                                  <a href="javascript:void(0)" class="btn btn-danger deleteDateRow">
                                    <i class="fas fa-minus"></i></a>
                                </td>
                              </tr>
                            @endif

                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>

                </div>
                </div>
                <div class="card ev-section-card">
                  <div class="card-header ev-section-header">
                    <h4 class="card-title"><i class="fas fa-cog mr-2 text-primary"></i>{{ __('Configuracion') }}</h4>
                  </div>
                  <div class="card-body">
                <div id="section-settings" class="mb-3">
                  <p class="text-muted mb-0">{{ __('Ajusta estado, visibilidad, ubicacion y condiciones de venta del evento.') }}</p>
                </div>
                <div class="row ">

                  <div class="col-lg-4">
                    <div class="form-group">
                      <label for="">{{ __('Estado') . '*' }}</label>
                      <select name="status" class="form-control">
                        <option selected disabled>{{ __('Selecciona un estado') }}</option>
                        <option {{ $event->status == '1' ? 'selected' : '' }} value="1">
                          {{ __('Active') }}
                        </option>
                        <option {{ $event->status == '0' ? 'selected' : '' }} value="0">
                          {{ __('Oculto') }}
                        </option>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="form-group">
                      <label for="">{{ __('Evento destacado') . '*' }}</label>
                      <select name="is_featured" class="form-control">
                        <option selected disabled>{{ __('Selecciona una opcion') }}</option>
                        <option value="yes" {{ $event->is_featured == 'yes' ? 'selected' : '' }}>
                          {{ __('Yes') }}
                        </option>
                        <option value="no" {{ $event->is_featured == 'no' ? 'selected' : '' }}>
                          {{ __('No') }}
                        </option>
                      </select>
                    </div>
                  </div>


                  @if ($event->event_type == 'venue')
                    <div class="col-lg-4">
                      <div class="form-group">
                        <label for="">{{ __('Latitude') }}</label>
                        <input type="text" placeholder="{{ __('Latitude') }}" name="latitude"
                          value="{{ $event->latitude }}" class="form-control">
                      </div>
                    </div>
                    <div class="col-lg-4">
                      <div class="form-group">
                        <label for="">{{ __('Longitude') }}</label>
                        <input type="text" placeholder="{{ __('Longitude') }}" name="longitude"
                          value="{{ $event->longitude }}" class="form-control">
                      </div>
                    </div>
                  @endif
                </div>
                @if ($event->event_type == 'online')
                  <div class="event-sales-note mb-5 mt-2">
                    <div class="event-sales-note-title">{{ __('Como vender este evento') }}</div>
                    <p class="mb-0">{{ __('Primero define si el evento va a ser gratis o pago. Despues ajusta cupos, limite por persona y, si te sirve, un descuento anticipado para mover las primeras ventas.') }}</p>
                  </div>
                  <div class="row">
                    <div class="col-lg-6">
                      <div class="form-group mt-1">
                        <label for="">{{ __('Disponibilidad total de entradas') . '*' }}</label>
                        <div class="selectgroup w-100">
                          <label class="selectgroup-item">
                            <input type="radio" name="ticket_available_type" value="unlimited"
                              class="selectgroup-input"
                              {{ @$event->ticket->ticket_available_type == 'unlimited' ? 'checked' : '' }}>
                            <span class="selectgroup-button">{{ __('Sin limite') }}</span>
                          </label>

                          <label class="selectgroup-item">
                            <input type="radio" name="ticket_available_type" value="limited"
                              class="selectgroup-input"
                              {{ @$event->ticket->ticket_available_type == 'limited' ? 'checked' : '' }}>
                            <span class="selectgroup-button">{{ __('Con limite') }}</span>
                          </label>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-6 {{ @$event->ticket->ticket_available_type == 'limited' ? '' : 'd-none' }}"
                      id="ticket_available">
                      <div class="form-group">
                        <label>{{ __('Cantidad total disponible') . '*' }}</label>
                        <input type="number" name="ticket_available"
                          placeholder="{{ __('Enter total number of available tickets') }}" class="form-control"
                          value="{{ @$event->ticket->ticket_available }}">
                      </div>
                    </div>
                    @if ($websiteInfo->event_guest_checkout_status != 1)
                      <div class="col-lg-6">
                        <div class="form-group mt-1">
                          <label for="">{{ __('Limite por comprador') . '*' }}</label>
                          <div class="selectgroup w-100">
                            <label class="selectgroup-item">
                              <input type="radio" name="max_ticket_buy_type" value="unlimited"
                                class="selectgroup-input"
                                {{ @$event->ticket->max_ticket_buy_type == 'unlimited' ? 'checked' : '' }}>
                              <span class="selectgroup-button">{{ __('Sin limite') }}</span>
                            </label>

                            <label class="selectgroup-item">
                              <input type="radio" name="max_ticket_buy_type" value="limited"
                                class="selectgroup-input"
                                {{ @$event->ticket->max_ticket_buy_type == 'limited' ? 'checked' : '' }}>
                              <span class="selectgroup-button">{{ __('Con limite') }}</span>
                            </label>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-6 {{ @$event->ticket->max_ticket_buy_type == 'limited' ? '' : 'd-none' }}"
                        id="max_buy_ticket">
                        <div class="form-group">
                          <label>{{ __('Cantidad maxima por comprador') . '*' }}</label>
                          <input type="number" name="max_buy_ticket"
                            placeholder="{{ __('Enter Maximum number of tickets for each customer') }}"
                            class="form-control" value="{{ @$event->ticket->max_buy_ticket }}">
                        </div>
                      </div>
                    @else
                      <input type="hidden" name="max_ticket_buy_type" value="unlimited">
                    @endif

                    <div class="col-lg-4">
                      <div class="">
                        <div class="form-group">
                          <label for="">{{ __('Precio de la entrada') }}
                            ({{ $getCurrencyInfo->base_currency_text }})
                            *</label>
                          <input type="number" name="price" id="ticket-pricing"
                            value="{{ $event->ticket->price }}" placeholder="{{ __('Ej: 12000') }}"
                            class="form-control {{ optional($event->ticket)->pricing_type == 'free' ? 'd-none' : '' }}">
                        </div>
                      </div>
                      <div class="form-group">
                        <input type="checkbox" name="pricing_type"
                          {{ optional($event->ticket)->pricing_type == 'free' ? 'checked' : '' }} value="free"
                          class="" id="free_ticket"> <label
                          for="free_ticket">{{ __('Este evento es gratuito') }}</label>
                      </div>
                    </div>
                    <div class="col-lg-8">
                      <div class="">
                        <div class="form-group">
                          <label for="">{{ __('Enlace de acceso o meeting URL') }}
                            *</label>
                          <input type="text" name="meeting_url" value="{{ $event->meeting_url }}"
                            placeholder="{{ __('Ej: enlace de Zoom, Meet o plataforma de acceso') }}" class="form-control">
                        </div>
                      </div>
                    </div>
                  </div>



                  <div class="row {{ optional($event->ticket)->pricing_type == 'free' ? 'd-none' : '' }}"
                    id="early_bird_discount_free">
                    <div class="col-lg-12">
                      <div class="form-group mt-1">
                        <label for="">{{ __('Descuento anticipado') . '*' }}</label>
                        <div class="selectgroup w-100">
                          <label class="selectgroup-item">
                            <input type="radio" name="early_bird_discount_type"
                              {{ optional($event->ticket)->early_bird_discount == 'disable' ? 'checked' : '' }}
                              value="disable" class="selectgroup-input" checked>
                            <span class="selectgroup-button">{{ __('Disable') }}</span>
                          </label>

                          <label class="selectgroup-item">
                            <input type="radio" name="early_bird_discount_type"
                              {{ optional($event->ticket)->early_bird_discount == 'enable' ? 'checked' : '' }}
                              value="enable" class="selectgroup-input">
                            <span class="selectgroup-button">{{ __('Enable') }}</span>
                          </label>
                        </div>
                      </div>
                    </div>
                    <div
                      class="col-lg-12 {{ optional($event->ticket)->early_bird_discount == 'disable' ? 'd-none' : '' }}"
                      id="early_bird_dicount">
                      <div class="row">
                        <div class="col-lg-3">
                          <div class="form-group">
                            <label for="">{{ __('Discount') }} *</label>
                            <select name="discount_type" class="form-control discount_type">
                              <option disabled>{{ __('Selecciona el tipo de descuento') }}</option>
                              <option
                                {{ optional($event->ticket)->early_bird_discount_type == 'fixed' ? 'selected' : '' }}
                                value="fixed">{{ __('Fixed') }}</option>
                              <option
                                {{ optional($event->ticket)->early_bird_discount_type == 'percentage' ? 'selected' : '' }}
                                value="percentage">{{ __('Percentage') }}</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-lg-3">
                          <div class="form-group">
                            <label for="">{{ __('Amount') }} *</label>
                            <input type="number" name="early_bird_discount_amount"
                              value="{{ optional($event->ticket)->early_bird_discount_amount }}"
                              class="form-control early_bird_discount_amount">
                          </div>
                        </div>
                        <div class="col-lg-3">
                          <div class="form-group">
                            <label for="">{{ __('Fecha limite del descuento') }} *</label>
                            <input type="date" name="early_bird_discount_date"
                              value="{{ optional($event->ticket)->early_bird_discount_date }}" class="form-control">
                          </div>
                        </div>
                        <div class="col-lg-3">
                          <div class="form-group">
                            <label for="">{{ __('Hora limite del descuento') }} *</label>
                            <input type="time" name="early_bird_discount_time"
                              value="{{ optional($event->ticket)->early_bird_discount_time }}" class="form-control">
                          </div>
                        </div>

                      </div>
                    </div>
                  </div>
                @endif


                </div>
                </div>
                <div id="section-content" class="pt-3 mb-3">
                  <h5 class="mb-1">{{ $singleLanguageMode ? __('Contenido principal') : __('Contenido por idioma') }}</h5>
                  <p class="text-muted mb-0">
                    {{ $singleLanguageMode ? __('Aqui editas el contenido principal del evento en espanol.') : __('Aqui editas titulo, categoria, descripcion, politica de reembolso y SEO para cada idioma.') }}
                  </p>
                </div>
                <div id="accordion" class="mt-3">
                  @foreach ($languages as $language)
                    <div class="version">
                      <div class="version-header" id="heading{{ $language->id }}">
                        <h5 class="mb-0">
                          <button type="button" class="btn btn-link" data-toggle="collapse"
                            data-target="#collapse{{ $language->id }}"
                            aria-expanded="{{ $language->is_default == 1 ? 'true' : 'false' }}"
                            aria-controls="collapse{{ $language->id }}">
                            {{ $singleLanguageMode ? __('Contenido del evento') : $language->name }}
                            {{ !$singleLanguageMode && $language->is_default == 1 ? '(' . __('Principal') . ')' : '' }}
                          </button>
                        </h5>
                      </div>
                      @php
                        $event_content = DB::table('event_contents')
                            ->where('language_id', $language->id)
                            ->where('event_id', $event->id)
                            ->first();
                      @endphp
                      <div id="collapse{{ $language->id }}"
                        class="collapse {{ $language->is_default == 1 ? 'show' : '' }}"
                        aria-labelledby="heading{{ $language->id }}" data-parent="#accordion">
                        <div class="version-body">
                          <div class="row">
                            <div class="col-lg-6">
                              <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                <label>{{ __('Event Title') . '*' }}</label>
                                <input type="text" class="form-control" name="{{ $language->code }}_title"
                                  value="{{ @$event_content->title }}" placeholder="{{ __('Enter Event Name') }}">
                              </div>
                            </div>

                            <div class="col-lg-6">
                              <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                @php
                                  $categories = DB::table('event_categories')
                                      ->where('language_id', $language->id)
                                      ->where('status', 1)
                                      ->orderBy('serial_number', 'asc')
                                      ->get();
                                @endphp

                                <label for="">{{ __('Category') . '*' }}</label>
                                <select name="{{ $language->code }}_category_id" class="form-control">
                                  <option selected disabled>{{ __('Selecciona una categoria') }}
                                  </option>

                                  @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                      {{ @$event_content->event_category_id == $category->id ? 'selected' : '' }}>
                                      {{ $category->name }}</option>
                                  @endforeach
                                </select>
                              </div>
                            </div>
                          </div>

                          @if ($event->event_type == 'venue')
                            <div class="row">
                              <div class="col-lg-8">
                                <div class="form-group">
                                  <label for="">{{ __('Address') . '*' }}</label>
                                  <input type="text" name="{{ $language->code }}_address"
                                    class="form-control {{ $language->direction == 1 ? 'rtl text-right' : '' }}"
                                    placeholder="{{ __('Enter Address') }}" value="{{ @$event_content->address }}">
                                </div>
                              </div>
                              <div class="col-lg-4">
                                <div class="form-group">
                                  <label for="">{{ __('County') . '*' }}</label>
                                  <input type="text" name="{{ $language->code }}_country"
                                    placeholder="{{ __('Enter Country') }}"
                                    class="form-control {{ $language->direction == 1 ? 'rtl text-right' : '' }}"
                                    value="{{ @$event_content->country }}">
                                </div>
                              </div>
                              <div class="col-lg-4">
                                <div class="form-group">
                                  <label for="">{{ __('State') }}</label>
                                  <input type="text" name="{{ $language->code }}_state"
                                    class="form-control {{ $language->direction == 1 ? 'rtl text-right' : '' }}"
                                    placeholder="{{ __('Enter State') }}" value="{{ @$event_content->state }}">
                                </div>
                              </div>
                              <div class="col-lg-4">
                                <div class="form-group">
                                  <label for="">{{ __('City') . '*' }}</label>
                                  <input type="text" name="{{ $language->code }}_city"
                                    class="form-control {{ $language->direction == 1 ? 'rtl text-right' : '' }}"
                                    placeholder="{{ __('Enter City') }}" value="{{ @$event_content->city }}">
                                </div>
                              </div>
                              <div class="col-lg-4">
                                <div class="form-group">
                                  <label for="">{{ __('Zip/Post Code ') }}</label>
                                  <input type="text" placeholder="{{ __('Enter Zip/Post Code') }}"
                                    name="{{ $language->code }}_zip_code"
                                    class="form-control {{ $language->direction == 1 ? 'rtl text-right' : '' }}"
                                    value="{{ @$event_content->zip_code }}">
                                </div>
                              </div>
                            </div>
                          @endif

                          <div class="row">
                            <div class="col">
                              <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                <label>{{ __('Description') . '*' }}</label>
                                <textarea id="descriptionTmce{{ $language->id }}" class="form-control summernote"
                                  name="{{ $language->code }}_description" placeholder="{{ __('Cuenta de que se trata el evento, que incluye la entrada y cualquier dato importante.') }}" data-height="300">{!! @$event_content->description !!}</textarea>
                              </div>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-lg-12">
                              <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                <label>{{ __('Politica de reembolso') }} *</label>
                                <textarea class="form-control" name="{{ $language->code }}_refund_policy" rows="5"
                                  placeholder="{{ __('Explica que pasa si alguien no puede asistir, pide un cambio o solicita devolucion.') }}">{{ @$event_content->refund_policy }}</textarea>
                              </div>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-lg-12">
                              <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                <label>{{ __('Palabras clave para Google') }}</label>
                                <input class="form-control" name="{{ $language->code }}_meta_keywords"
                                  value="{{ @$event_content->meta_keywords }}"
                                  placeholder="{{ __('Ej: recital, cumbia, buenos aires') }}" data-role="tagsinput">
                              </div>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-lg-12">
                              <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                <label>{{ __('Descripcion corta para Google') }}</label>
                                <textarea class="form-control" name="{{ $language->code }}_meta_description" rows="5"
                                  placeholder="{{ __('Una descripcion breve y clara para buscadores y enlaces compartidos.') }}">{{ @$event_content->meta_description }}</textarea>
                              </div>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col">
                              @if (!$singleLanguageMode)
                                @php $currLang = $language; @endphp

                                @foreach ($languages as $language)
                                  @continue($language->id == $currLang->id)

                                  <div class="form-check py-0">
                                    <label class="form-check-label">
                                      <input class="form-check-input" type="checkbox"
                                        onchange="cloneInput('collapse{{ $currLang->id }}', 'collapse{{ $language->id }}', event)">
                                      <span class="form-check-sign">{{ __('Clone for') }}
                                        <strong class="text-capitalize text-secondary">{{ $language->name }}</strong>
                                        {{ __('language') }}</span>
                                    </label>
                                  </div>
                                @endforeach
                              @endif
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>

                <div id="sliders"></div>

                <div id="section-media-links"></div>
                {{-- Multimedia del artista --}}
                <div class="card mt-4">
                  <div class="card-header">
                    <h4 class="card-title">{{ __('Multimedia del artista') }}</h4>
                    <small class="text-muted">{{ __('Opcional. Se mostrara en la pagina del evento para que los compradores conozcan al artista.') }}</small>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label><i class="fab fa-spotify mr-1" style="color:#1DB954"></i> {{ __('Enlace del artista en Spotify') }}</label>
                          <input type="url" class="form-control" name="spotify_url" value="{{ $event->spotify_url }}"
                            placeholder="Ej: https://open.spotify.com/artist/4tZwfgrHOc3mvqYlEYSvVi">
                          <small class="text-muted">{{ __('Abre Spotify, busca al artista y copia el enlace del perfil.') }}</small>
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label><i class="fab fa-youtube mr-1" style="color:#FF0000"></i> {{ __('Enlace del video en YouTube') }}</label>
                          <input type="url" class="form-control" name="youtube_url" value="{{ $event->youtube_url }}"
                            placeholder="Ej: https://www.youtube.com/watch?v=dQw4w9WgXcQ">
                          <small class="text-muted">{{ __('Pega el enlace completo del video tal como aparece en el navegador.') }}</small>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                {{-- Pixeles de seguimiento --}}
                <div id="section-tracking"></div>
                <div class="card mt-4">
                  <div class="card-header">
                    <h4 class="card-title">{{ __('Pixeles de seguimiento') }}</h4>
                    <small class="text-muted">{{ __('Opcional. Agrega tus propios pixeles para medir conversiones de este evento.') }}</small>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-lg-4">
                        <div class="form-group">
                          <label><i class="fab fa-facebook mr-1"></i> {{ __('Meta Pixel ID (Facebook)') }}</label>
                          <input type="text" class="form-control" name="meta_pixel_id" value="{{ $event->meta_pixel_id }}" placeholder="Ej: 1234567890123456">
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="form-group">
                          <label><i class="fab fa-google mr-1"></i> {{ __('Google Analytics ID') }}</label>
                          <input type="text" class="form-control" name="google_analytics_id" value="{{ $event->google_analytics_id }}" placeholder="Ej: G-XXXXXXXXXX">
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="form-group">
                          <label><i class="fab fa-tiktok mr-1"></i> {{ __('TikTok Pixel ID') }}</label>
                          <input type="text" class="form-control" name="tiktok_pixel_id" value="{{ $event->tiktok_pixel_id }}" placeholder="Ej: CXXXXXXXXXXXXXXX">
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
              <p class="text-muted mb-3 mx-auto" style="max-width: 640px;">
                {{ __('Guarda al final. Si falta un dato obligatorio, el sistema te mostrara el error arriba.') }}
              </p>
              <button type="submit" id="EventSubmit" class="btn btn-primary px-4">
                {{ __('Guardar cambios del evento') }}
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
    #my-dropzone {
      border: 2px dashed #d6d9e6;
      border-radius: 14px;
      background: #f8f9fc;
      min-height: 160px;
      padding: 24px;
    }

    #my-dropzone .dz-message {
      display: block !important;
      margin: 1.5rem 0;
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

    #my-dropzone .dz-message span::before {
      content: "Subi las imagenes del evento";
      display: block;
      font-size: 17px;
      margin-bottom: 6px;
    }

    #my-dropzone .dz-message span {
      font-size: 0;
      display: inline-block;
      line-height: 1.5;
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

    .compact-media-toolbar {
      gap: 12px;
    }

    .event-sales-note {
      padding: 18px 20px;
      border: 1px solid #dbe7ff;
      border-radius: 14px;
      background: linear-gradient(180deg, #f8fbff 0%, #f2f7ff 100%);
      color: #334155;
    }

    .event-sales-note-title {
      font-size: 15px;
      font-weight: 700;
      color: #1e3a8a;
      margin-bottom: 6px;
    }

    .event-sales-note p {
      font-size: 14px;
      line-height: 1.6;
      color: #334155;
    }

    .media-upload-separator .border-top {
      border-color: #e5e7eb !important;
    }

    #img-table,
    #img-table tbody {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      width: 100%;
    }

    #img-table .table-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      width: 170px;
      background: #f8f9fc;
      border: 1px solid #eaecf0;
      border-radius: 14px;
      padding: 10px;
      margin-bottom: 0;
    }

    #img-table td {
      border: none;
      padding: 0;
      background: transparent;
    }

    #img-table .thumb-preview {
      width: 116px;
      height: 72px;
      object-fit: cover;
      border-radius: 10px;
      border: 1px solid #dbe1ea;
      display: block;
    }

    #img-table .rmvbtndb {
      width: 34px;
      height: 34px;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: #fff1f2;
      color: #dc2626;
      cursor: pointer;
      transition: transform 0.18s ease, background-color 0.18s ease;
    }

    #img-table .rmvbtndb:hover {
      transform: scale(1.05);
      background: #ffe4e6;
    }

    @media (max-width: 575.98px) {
      #img-table .table-row {
        width: 100%;
      }
    }
  </style>
@endsection

@section('script')
  @php
    $languages = App\Models\Language::get();
  @endphp
  <script>
    let languages = "{{ $languages }}";
  </script>
  <script type="text/javascript" src="{{ asset('assets/admin/js/admin-partial.js') }}"></script>
  <script src="{{ asset('assets/admin/js/admin_dropzone.js') }}"></script>
@endsection

@section('variables')
  <script>
    "use strict";
    var storeUrl = "{{ route('organizer.event.imagesstore') }}";
    var removeUrl = "{{ route('organizer.event.imagermv') }}";

    var rmvdbUrl = "{{ route('organizer.event.imgdbrmv') }}";
    var loadImgs = "{{ route('organizer.event.images', $event->id) }}";
  </script>
@endsection
