@extends('backend.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Edit Event') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ route('admin.dashboard') }}"><i class="flaticon-home"></i></a>
      </li>
      <li class="separator"><i class="flaticon-right-arrow"></i></li>
      <li class="nav-item"><a href="#">{{ __('Events Management') }}</a></li>
      <li class="separator"><i class="flaticon-right-arrow"></i></li>
      <li class="nav-item">
        <a href="{{ route('admin.event_management.event', ['language' => $defaultLang->code]) }}">{{ __('All Events') }}</a>
      </li>
      <li class="separator"><i class="flaticon-right-arrow"></i></li>
      @php
        $event_title = DB::table('event_contents')
            ->where('language_id', $defaultLang->id)
            ->where('event_id', $event->id)
            ->select('title')->first();
        if (empty($event_title)) {
            $event_title = DB::table('event_contents')
                ->where('event_id', $event->id)
                ->select('title')->first();
        }
      @endphp
      <li class="nav-item">
        <a href="#">{{ strlen($event_title->title) > 35 ? mb_substr($event_title->title, 0, 35, 'UTF-8') . '...' : $event_title->title }}</a>
      </li>
      <li class="separator"><i class="flaticon-right-arrow"></i></li>
      <li class="nav-item"><a href="#">{{ __('Edit Event') }}</a></li>
    </ul>
  </div>

  <div class="row">
    <div class="col-lg-8 offset-lg-2">

      {{-- Botones de acción --}}
      <div class="d-flex justify-content-between align-items-center mb-4">
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
              href="{{ route('admin.event.ticket', ['language' => $defaultLang->code, 'event_id' => $event->id, 'event_type' => $event->event_type]) }}"
              target="_blank">
              <span class="btn-label"><i class="far fa-ticket"></i></span> {{ __('Tickets') }}
            </a>
          @endif
          <button type="submit" form="eventForm" class="btn btn-primary btn-sm">
            <span class="btn-label"><i class="fas fa-save"></i></span> {{ __('Update') }}
          </button>
        </div>
      </div>

      {{-- Error alert --}}
      <div class="alert alert-danger pb-1 dis-none" id="eventErrors">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <ul></ul>
      </div>

      {{-- ===== CARD: IMÁGENES ===== --}}
      <div class="card ev-section-card">
        <div class="card-header ev-section-header">
          <h4 class="card-title"><i class="fas fa-images mr-2 text-primary"></i>{{ __('Imágenes del evento') }}</h4>
        </div>
        <div class="card-body">
          {{-- Galería (form propio — dropzone AJAX) --}}
          <label class="ev-label-section">{{ __('Gallery Images') }} <span class="text-warning">**</span></label>
          <div id="reload-slider-div">
            <div class="row mt-1 mb-2">
              <div class="col">
                <table class="table" id="img-table"></table>
              </div>
            </div>
          </div>
          <form action="{{ route('admin.event.imagesstore') }}" id="my-dropzone" enctype="multipart/formdata" class="dropzone create">
            @csrf
            <div class="fallback"><input name="file" type="file" multiple /></div>
            <input type="hidden" value="{{ $event->id }}" name="event_id">
          </form>
          <div class="mb-0" id="errpreimg"></div>
          <p class="text-warning small mt-2 mb-4">{{ __('Image Size : 1170 x 570') }}</p>

          {{-- Miniatura (pertenece al form principal via form="eventForm") --}}
          <label class="ev-label-section">{{ __('Thumbnail Image') }}*</label>
          <div class="d-flex align-items-center mt-2">
            <div class="thumb-preview mr-4">
              <img src="{{ $event->thumbnail ? asset('assets/admin/img/event/thumbnail/' . $event->thumbnail) : asset('assets/admin/img/noimage.jpg') }}"
                alt="..." class="uploaded-img ev-thumbnail-preview">
            </div>
            <div>
              <div role="button" class="btn btn-primary btn-sm upload-btn">
                {{ __('Choose Image') }}
                <input type="file" class="img-input" name="thumbnail" form="eventForm">
              </div>
              <p class="text-warning small mt-2 mb-0">{{ __('Image Size : 320x230') }}</p>
            </div>
          </div>
        </div>
      </div>

      {{-- ===== FORM PRINCIPAL ===== --}}
      <form id="eventForm" action="{{ route('admin.event.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="event_id" value="{{ $event->id }}">
        <input type="hidden" name="event_type" value="{{ $event->event_type }}">
        <input type="hidden" name="gallery_images" value="0">

        {{-- ===== CARD: FECHAS Y HORARIOS ===== --}}
        <div class="card ev-section-card">
          <div class="card-header ev-section-header">
            <h4 class="card-title"><i class="fas fa-calendar-alt mr-2 text-primary"></i>{{ __('Fechas y horarios') }}</h4>
          </div>
          <div class="card-body">

            <div class="row">
              <div class="col-lg-12">
                <div class="form-group">
                  <label>{{ __('Date Type') }}*</label>
                  <div class="selectgroup w-100">
                    <label class="selectgroup-item">
                      <input type="radio" name="date_type" {{ $event->date_type == 'single' ? 'checked' : '' }}
                        value="single" class="selectgroup-input eventDateType" checked>
                      <span class="selectgroup-button">{{ __('Single') }}</span>
                    </label>
                    <label class="selectgroup-item">
                      <input type="radio" name="date_type" {{ $event->date_type == 'multiple' ? 'checked' : '' }}
                        value="multiple" class="selectgroup-input eventDateType">
                      <span class="selectgroup-button">{{ __('Multiple') }}</span>
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <div class="row countDownStatus {{ $event->date_type == 'multiple' ? 'd-none' : '' }}">
              <div class="col-lg-12">
                <div class="form-group">
                  <label>{{ __('Countdown Status') }}*</label>
                  <div class="selectgroup w-100">
                    <label class="selectgroup-item">
                      <input type="radio" name="countdown_status" value="1" class="selectgroup-input"
                        {{ $event->countdown_status == 1 ? 'checked' : '' }}>
                      <span class="selectgroup-button">{{ __('Active') }}</span>
                    </label>
                    <label class="selectgroup-item">
                      <input type="radio" name="countdown_status" value="0" class="selectgroup-input"
                        {{ $event->countdown_status == 0 ? 'checked' : '' }}>
                      <span class="selectgroup-button">{{ __('Deactive') }}</span>
                    </label>
                  </div>
                </div>
              </div>
            </div>

            {{-- Fechas individuales --}}
            <div class="row {{ $event->date_type == 'multiple' ? 'd-none' : '' }}" id="single_dates">
              <div class="col-lg-3">
                <div class="form-group">
                  <label>{{ __('Start Date') }}*</label>
                  <input type="date" name="start_date" value="{{ $event->start_date }}" class="form-control">
                </div>
              </div>
              <div class="col-lg-3">
                <div class="form-group">
                  <label>{{ __('Start Time') }}*</label>
                  <input type="time" name="start_time" value="{{ $event->start_time }}" class="form-control">
                </div>
              </div>
              <div class="col-lg-3">
                <div class="form-group">
                  <label>{{ __('End Date') }}*</label>
                  <input type="date" name="end_date" value="{{ $event->end_date }}" class="form-control">
                </div>
              </div>
              <div class="col-lg-3">
                <div class="form-group">
                  <label>{{ __('End Time') }}*</label>
                  <input type="time" name="end_time" value="{{ $event->end_time }}" class="form-control">
                </div>
              </div>
            </div>

            {{-- Fechas múltiples --}}
            <div class="row">
              <div class="col-lg-12 {{ $event->date_type == 'single' ? 'd-none' : '' }}" id="multiple_dates">
                @if ($event->date_type == 'multiple')
                  @php $event_dates = $event->dates()->get(); @endphp
                @else
                  @php $event_dates = []; @endphp
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
                          <th><a href="javascript:void(0)" class="btn btn-success addDateRow"><i class="fas fa-plus-circle"></i></a></th>
                        </tr>
                      </thead>
                      <tbody>
                        @if (count($event_dates) > 0)
                          @foreach ($event_dates as $date)
                            <tr>
                              <td><div class="form-group"><input type="date" name="m_start_date[]" class="form-control" value="{{ $date->start_date }}"></div></td>
                              <td><div class="form-group"><input type="time" name="m_start_time[]" class="form-control" value="{{ $date->start_time }}"></div></td>
                              <td><div class="form-group"><input type="date" name="m_end_date[]" class="form-control" value="{{ $date->end_date }}"></div></td>
                              <td><div class="form-group"><input type="time" name="m_end_time[]" class="form-control" value="{{ $date->end_time }}"></div></td>
                              <input type="hidden" name="date_ids[]" value="{{ $date->id }}">
                              <td><a href="javascript:void(0)" data-url="{{ route('admin.event.delete.date', $date->id) }}" class="btn btn-danger deleteDateDbRow"><i class="fas fa-minus"></i></a></td>
                            </tr>
                          @endforeach
                        @else
                          <tr>
                            <td><div class="form-group"><input type="date" name="m_start_date[]" class="form-control"></div></td>
                            <td><div class="form-group"><input type="time" name="m_start_time[]" class="form-control"></div></td>
                            <td><div class="form-group"><input type="date" name="m_end_date[]" class="form-control"></div></td>
                            <td><div class="form-group"><input type="time" name="m_end_time[]" class="form-control"></div></td>
                            <td><a href="javascript:void(0)" class="btn btn-danger deleteDateRow"><i class="fas fa-minus"></i></a></td>
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

        {{-- ===== CARD: CONFIGURACIÓN ===== --}}
        <div class="card ev-section-card">
          <div class="card-header ev-section-header">
            <h4 class="card-title"><i class="fas fa-cog mr-2 text-primary"></i>{{ __('Configuración') }}</h4>
          </div>
          <div class="card-body">

            <div class="row">
              <div class="col-lg-4">
                <div class="form-group">
                  <label>{{ __('Status') }}*</label>
                  <select name="status" class="form-control">
                    <option selected disabled>{{ __('Select a Status') }}</option>
                    <option {{ $event->status == '1' ? 'selected' : '' }} value="1">{{ __('Active') }}</option>
                    <option {{ $event->status == '0' ? 'selected' : '' }} value="0">{{ __('Deactive') }}</option>
                  </select>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="form-group">
                  <label>{{ __('Is Feature') }}*</label>
                  <select name="is_featured" class="form-control">
                    <option selected disabled>{{ __('Select') }}</option>
                    <option value="yes" {{ $event->is_featured == 'yes' ? 'selected' : '' }}>{{ __('Yes') }}</option>
                    <option value="no" {{ $event->is_featured == 'no' ? 'selected' : '' }}>{{ __('No') }}</option>
                  </select>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="form-group">
                  <label for="manual_badge">{{ __('Badge especial') }}</label>
                  <select name="manual_badge" id="manual_badge" class="form-control">
                    <option value="" {{ ($event->manual_badge ?? '') == '' ? 'selected' : '' }}>{{ __('Automático') }}</option>
                    <option value="destacado" {{ ($event->manual_badge ?? '') == 'destacado' ? 'selected' : '' }}>⭐ {{ __('Destacado') }}</option>
                    <option value="imperdible" {{ ($event->manual_badge ?? '') == 'imperdible' ? 'selected' : '' }}>🎪 {{ __('Imperdible') }}</option>
                  </select>
                  <small class="form-text text-muted">{{ __('Automático: el sistema asigna badge por visitas/ventas/stock. Destacado e Imperdible tienen prioridad máxima.') }}</small>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="form-group">
                  <label>{{ __('Organizer') }}</label>
                  <select name="organizer_id" class="form-control js-example-basic-single">
                    <option value="" selected>{{ __('Select Organizer') }}</option>
                    @foreach ($organizers as $organizer)
                      <option {{ $organizer->id == $event->organizer_id ? 'selected' : '' }} value="{{ $organizer->id }}">{{ $organizer->username }}</option>
                    @endforeach
                  </select>
                  <small class="form-text text-muted">{{ __("Please leave it blank for Admin's event") }}</small>
                </div>
              </div>
              @if ($event->event_type == 'venue')
                <div class="col-lg-4">
                  <div class="form-group">
                    <label>{{ __('Latitude') }}</label>
                    <input type="text" placeholder="Latitude" name="latitude" value="{{ $event->latitude }}" class="form-control">
                  </div>
                </div>
                <div class="col-lg-4">
                  <div class="form-group">
                    <label>{{ __('Longitude') }}</label>
                    <input type="text" placeholder="Longitude" name="longitude" value="{{ $event->longitude }}" class="form-control">
                  </div>
                </div>
              @endif
            </div>

            @if ($event->event_type == 'online')
              <hr class="ev-divider">
              <p class="ev-subsection-title"><i class="fas fa-ticket-alt mr-1"></i> {{ __('Tickets y precio') }}</p>
              <div class="row">
                <div class="col-lg-6">
                  <div class="form-group">
                    <label>{{ __('Total Number of Available Tickets') }}*</label>
                    <div class="selectgroup w-100">
                      <label class="selectgroup-item">
                        <input type="radio" name="ticket_available_type" value="unlimited" class="selectgroup-input"
                          {{ @$event->ticket->ticket_available_type == 'unlimited' ? 'checked' : '' }}>
                        <span class="selectgroup-button">{{ __('Unlimited') }}</span>
                      </label>
                      <label class="selectgroup-item">
                        <input type="radio" name="ticket_available_type" value="limited" class="selectgroup-input"
                          {{ @$event->ticket->ticket_available_type == 'limited' ? 'checked' : '' }}>
                        <span class="selectgroup-button">{{ __('Limited') }}</span>
                      </label>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6 {{ @$event->ticket->ticket_available_type == 'limited' ? '' : 'd-none' }}" id="ticket_available">
                  <div class="form-group">
                    <label>{{ __('Enter total number of available tickets') }}*</label>
                    <input type="number" name="ticket_available" class="form-control" value="{{ @$event->ticket->ticket_available }}">
                  </div>
                </div>
                @if ($websiteInfo->event_guest_checkout_status != 1)
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>{{ __('Maximum number of tickets for each customer') }}*</label>
                      <div class="selectgroup w-100">
                        <label class="selectgroup-item">
                          <input type="radio" name="max_ticket_buy_type" value="unlimited" class="selectgroup-input"
                            {{ @$event->ticket->max_ticket_buy_type == 'unlimited' ? 'checked' : '' }}>
                          <span class="selectgroup-button">{{ __('Unlimited') }}</span>
                        </label>
                        <label class="selectgroup-item">
                          <input type="radio" name="max_ticket_buy_type" value="limited" class="selectgroup-input"
                            {{ @$event->ticket->max_ticket_buy_type == 'limited' ? 'checked' : '' }}>
                          <span class="selectgroup-button">{{ __('Limited') }}</span>
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6 {{ @$event->ticket->max_ticket_buy_type == 'limited' ? '' : 'd-none' }}" id="max_buy_ticket">
                    <div class="form-group">
                      <label>{{ __('Enter Maximum number of tickets for each customer') }}*</label>
                      <input type="number" name="max_buy_ticket" class="form-control" value="{{ @$event->ticket->max_buy_ticket }}">
                    </div>
                  </div>
                @else
                  <input type="hidden" name="max_ticket_buy_type" value="unlimited">
                @endif
                <div class="col-lg-4">
                  <div class="form-group">
                    <label>{{ __('Price') }} ({{ $getCurrencyInfo->base_currency_text }})*</label>
                    <input type="number" name="price" id="ticket-pricing" value="{{ $event->ticket->price }}"
                      class="form-control {{ optional($event->ticket)->pricing_type == 'free' ? 'd-none' : '' }}">
                    <div class="mt-2">
                      <input type="checkbox" name="pricing_type"
                        {{ optional($event->ticket)->pricing_type == 'free' ? 'checked' : '' }} value="free" id="free_ticket">
                      <label for="free_ticket" class="d-inline ml-1 font-weight-normal">{{ __('Tickets are Free') }}</label>
                    </div>
                  </div>
                </div>
                <div class="col-lg-8">
                  <div class="form-group">
                    <label>{{ __('Meeting Url') }}*</label>
                    <input type="text" name="meeting_url" value="{{ $event->meeting_url }}" class="form-control">
                  </div>
                </div>
              </div>

              <div class="row {{ optional($event->ticket)->pricing_type == 'free' ? 'd-none' : '' }}" id="early_bird_discount_free">
                <div class="col-lg-12">
                  <div class="form-group">
                    <label>{{ __('Early Bird Discount') }}*</label>
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
                <div class="col-lg-12 {{ optional($event->ticket)->early_bird_discount == 'disable' ? 'd-none' : '' }}" id="early_bird_dicount">
                  <div class="row">
                    <div class="col-lg-3">
                      <div class="form-group">
                        <label>{{ __('Discount') }}*</label>
                        <select name="discount_type" class="form-control discount_type">
                          <option disabled>{{ __('Select Discount Type') }}</option>
                          <option {{ optional($event->ticket)->early_bird_discount_type == 'fixed' ? 'selected' : '' }} value="fixed">{{ __('Fixed') }}</option>
                          <option {{ optional($event->ticket)->early_bird_discount_type == 'percentage' ? 'selected' : '' }} value="percentage">{{ __('Percentage') }}</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-lg-3">
                      <div class="form-group">
                        <label>{{ __('Amount') }}*</label>
                        <input type="number" name="early_bird_discount_amount" value="{{ optional($event->ticket)->early_bird_discount_amount }}" class="form-control early_bird_discount_amount">
                      </div>
                    </div>
                    <div class="col-lg-3">
                      <div class="form-group">
                        <label>{{ __('Discount End Date') }}*</label>
                        <input type="date" name="early_bird_discount_date" value="{{ optional($event->ticket)->early_bird_discount_date }}" class="form-control">
                      </div>
                    </div>
                    <div class="col-lg-3">
                      <div class="form-group">
                        <label>{{ __('Discount End Time') }}*</label>
                        <input type="time" name="early_bird_discount_time" value="{{ optional($event->ticket)->early_bird_discount_time }}" class="form-control">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            @endif

          </div>
        </div>

        {{-- ===== CONTENIDO (accordion por idioma) ===== --}}
        <div id="accordion" class="mt-2">
          @foreach ($languages as $language)
            <div class="version ev-section-card" style="margin-bottom:12px;">
              <div class="version-header ev-section-header" id="heading{{ $language->id }}">
                <h5 class="mb-0">
                  <button type="button" class="btn btn-link ev-accordion-btn" data-toggle="collapse"
                    data-target="#collapse{{ $language->id }}"
                    aria-expanded="{{ $language->is_default == 1 ? 'true' : 'false' }}"
                    aria-controls="collapse{{ $language->id }}">
                    <i class="fas fa-file-alt mr-2 text-primary"></i>
                    {{ __('Contenido') }} — {{ $language->name }}
                    {{ $language->is_default == 1 ? '(Default)' : '' }}
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
                <div class="version-body p-4">

                  <div class="row">
                    <div class="col-lg-6">
                      <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                        <label>{{ __('Event Title') }}*</label>
                        <input type="text" class="form-control" name="{{ $language->code }}_title"
                          value="{{ @$event_content->title }}" placeholder="Enter Event Name">
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
                        <label>{{ __('Category') }}*</label>
                        <select name="{{ $language->code }}_category_id" class="form-control">
                          <option selected disabled>{{ __('Select Category') }}</option>
                          @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ @$event_content->event_category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                  </div>

                  @if ($event->event_type == 'venue')
                    <div class="row">
                      <div class="col-lg-8">
                        <div class="form-group">
                          <label>{{ __('Address') }}*</label>
                          <input type="text" name="{{ $language->code }}_address"
                            class="form-control {{ $language->direction == 1 ? 'rtl text-right' : '' }}"
                            placeholder="Enter Address" value="{{ @$event_content->address }}">
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="form-group">
                          <label>{{ __('County') }}*</label>
                          <input type="text" name="{{ $language->code }}_country"
                            class="form-control {{ $language->direction == 1 ? 'rtl text-right' : '' }}"
                            placeholder="Enter Country" value="{{ @$event_content->country }}">
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="form-group">
                          <label>{{ __('Sate') }}</label>
                          <input type="text" name="{{ $language->code }}_state"
                            class="form-control {{ $language->direction == 1 ? 'rtl text-right' : '' }}"
                            placeholder="Enter State" value="{{ @$event_content->state }}">
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="form-group">
                          <label>{{ __('City') }}*</label>
                          <input type="text" name="{{ $language->code }}_city"
                            class="form-control {{ $language->direction == 1 ? 'rtl text-right' : '' }}"
                            placeholder="Enter City" value="{{ @$event_content->city }}">
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="form-group">
                          <label>{{ __('Zip/Post Code') }}</label>
                          <input type="text" placeholder="Enter Zip/Post Code" name="{{ $language->code }}_zip_code"
                            class="form-control {{ $language->direction == 1 ? 'rtl text-right' : '' }}"
                            value="{{ @$event_content->zip_code }}">
                        </div>
                      </div>
                    </div>
                  @endif

                  <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                    <label>{{ __('Description') }}*</label>
                    <textarea id="descriptionTmce{{ $language->id }}" class="form-control summernote"
                      name="{{ $language->code }}_description" placeholder="Enter Event Description"
                      data-height="300">{!! @$event_content->description !!}</textarea>
                  </div>

                  <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                    <label>{{ __('Refund Policy') }}*</label>
                    <textarea class="form-control" name="{{ $language->code }}_refund_policy" rows="4"
                      placeholder="Enter Refund Policy">{{ @$event_content->refund_policy }}</textarea>
                  </div>

                  <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                    <label>{{ __('Event Meta Keywords') }}</label>
                    <input class="form-control" name="{{ $language->code }}_meta_keywords"
                      value="{{ @$event_content->meta_keywords }}" placeholder="Enter Meta Keywords"
                      data-role="tagsinput">
                  </div>

                  <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                    <label>{{ __('Event Meta Description') }}</label>
                    <textarea class="form-control" name="{{ $language->code }}_meta_description" rows="4"
                      placeholder="Enter Meta Description">{{ @$event_content->meta_description }}</textarea>
                  </div>

                  @php $currLang = $language; @endphp
                  @foreach ($languages as $language)
                    @continue($language->id == $currLang->id)
                    <div class="form-check py-0">
                      <label class="form-check-label">
                        <input class="form-check-input" type="checkbox"
                          onchange="cloneInput('collapse{{ $currLang->id }}', 'collapse{{ $language->id }}', event)">
                        <span class="form-check-sign">{{ __('Clone for') }} <strong class="text-capitalize text-secondary">{{ $language->name }}</strong> {{ __('language') }}</span>
                      </label>
                    </div>
                  @endforeach

                </div>
              </div>
            </div>
          @endforeach
        </div>

        <div id="sliders"></div>

        {{-- ===== CARD: MULTIMEDIA ===== --}}
        <div class="card ev-section-card mt-2">
          <div class="card-header ev-section-header">
            <h4 class="card-title"><i class="fas fa-music mr-2 text-primary"></i>{{ __('Multimedia del Artista') }}</h4>
            <p class="card-category">{{ __('Opcional. Se mostrará en la página del evento para que los compradores conozcan al artista.') }}</p>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label><i class="fab fa-spotify mr-1" style="color:#1DB954"></i> {{ __('Enlace del artista en Spotify') }}</label>
                  <input type="url" class="form-control" name="spotify_url" value="{{ $event->spotify_url }}"
                    placeholder="Ej: https://open.spotify.com/artist/4tZwfgrHOc3mvqYlEYSvVi">
                  <small class="text-muted">{{ __('Abrí Spotify, buscá al artista, hacé clic en los tres puntos → Compartir → Copiar enlace del artista.') }}</small>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label><i class="fab fa-youtube mr-1" style="color:#FF0000"></i> {{ __('Enlace del video en YouTube') }}</label>
                  <input type="url" class="form-control" name="youtube_url" value="{{ $event->youtube_url }}"
                    placeholder="Ej: https://www.youtube.com/watch?v=dQw4w9WgXcQ">
                  <small class="text-muted">{{ __('Pegá el enlace del video de YouTube tal como aparece en el navegador.') }}</small>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- ===== CARD: PÍXELES ===== --}}
        <div class="card ev-section-card mt-2">
          <div class="card-header ev-section-header">
            <h4 class="card-title"><i class="fas fa-chart-line mr-2 text-primary"></i>{{ __('Píxeles de Seguimiento') }}</h4>
            <p class="card-category">{{ __('Opcional. Agregá tus propios píxeles para medir conversiones de este evento.') }}</p>
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

        {{-- Botón guardar --}}
        <div class="text-center mt-4 mb-5">
          <button type="submit" id="EventSubmit" class="btn btn-primary btn-lg px-5">
            <i class="fas fa-save mr-2"></i>{{ __('Actualizar evento') }}
          </button>
        </div>

      </form>

    </div>
  </div>
@endsection

@section('script')
  @php $languages = App\Models\Language::get(); @endphp
  <script>
    let languages = "{{ $languages }}";
  </script>
  <script type="text/javascript" src="{{ asset('assets/admin/js/admin-partial.js') }}"></script>
  <script src="{{ asset('assets/admin/js/admin_dropzone.js') }}"></script>
  <script>
    $(document).ready(function() {
      $('.js-example-basic-single').select2();
    });
  </script>
@endsection

@section('variables')
  <script>
    "use strict";
    var storeUrl = "{{ route('admin.event.imagesstore') }}";
    var removeUrl = "{{ route('admin.event.imagermv') }}";
    var rmvdbUrl = "{{ route('admin.event.imgdbrmv') }}";
    var loadImgs = "{{ route('admin.event.images', $event->id) }}";
  </script>
@endsection
