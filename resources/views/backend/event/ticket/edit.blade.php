@extends('backend.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Editar entrada') }}</h4>
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
        <a href="#">{{ __('Gestión de eventos') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a
          href="{{ route('admin.event_management.event', ['language' => $defaultLang->code]) }}">{{ __('Eventos') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>

      <li class="nav-item">
        <a href="#">
          {{ strlen($event->title) > 35 ? mb_substr($event->title, 0, 35, 'UTF-8') . '...' : $event->title }}
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a
          href="{{ route('admin.event.ticket', ['language' => $defaultLang->code, 'event_id' => request()->input('event_id'), 'event_type' => request()->input('event_type')]) }}">{{ __('Entradas') }}</a>
      </li>

      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Editar entrada') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card ticket-form-card">
        <div class="card-header">
          <div class="ticket-form-header">
            <div class="ticket-form-header__intro">
              <span class="ticket-form-header__eyebrow">{{ __('Venta') }}</span>
              <h3 class="ticket-form-header__title">{{ __('Editar entrada') }}</h3>
              <p class="ticket-form-header__text">{{ __('Ajustá precio, cupo, límite por comprador o descuento anticipado sin perder claridad comercial.') }}</p>
              <span class="ticket-form-header__event">{{ __('Evento') }}: {{ $event->title }}</span>
            </div>
            <div class="ticket-form-header__actions">
              <a class="btn btn-outline-primary ticket-form-header__btn"
                href="{{ route('event.details', ['slug' => eventSlug($defaultLang->id, request()->input('event_id')), 'id' => request()->input('event_id')]) }}"
                target="_blank">
                <i class="fas fa-eye mr-1"></i>{{ __('Vista previa') }}
              </a>
              <a class="btn btn-light ticket-form-header__btn"
                href="{{ route('admin.event.ticket', ['language' => $defaultLang->code, 'event_id' => request()->input('event_id'), 'event_type' => request()->input('event_type')]) }}">
                <i class="fas fa-arrow-left mr-1"></i>{{ __('Volver') }}
              </a>
            </div>
          </div>
        </div>

        <div class="card-body">
          <div class="row">
            <div class="col-xl-9 col-lg-10 mx-auto">
              <div class="alert alert-danger pb-1 dis-none" id="eventErrors">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <ul></ul>
              </div>
              <form id="eventForm" action="{{ route('admin.ticket_management.update_ticket') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="event_type" value="{{ request()->input('event_type') }}">
                <input type="hidden" name="event_id" value="{{ request()->input('event_id') }}">
                <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                <div class="ticket-form-intro">
                  <span class="ticket-form-intro__eyebrow">{{ __('Revisión') }}</span>
                  <p class="ticket-form-intro__text">{{ __('Revisá precio, stock y límites antes de guardar. Un cambio acá impacta directo en la venta de esta entrada.') }}</p>
                </div>
                @if (request()->input('event_type') == 'venue')
                  <div class="row ticket-form-section">
                    {{-- /*****--variationwise ticket & early bird discount--****** --}}
                    <div class="col-lg-12">
                      <div class="ticket-form-section-heading">
                        <span class="ticket-form-section-heading__number">1</span>
                        <div>
                          <h4>{{ __('Precio y modalidad') }}</h4>
                          <p>{{ __('Definí si esta entrada es gratis, tiene precio fijo o usa variaciones.') }}</p>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-12">
                      <div class="form-group mt-1">
                        <label for="">{{ __('Tipo de precio') . '*' }}</label>
                        <div class="selectgroup w-100">
                          <label class="selectgroup-item">
                            <input type="radio" name="pricing_type_2"
                              {{ $ticket->pricing_type == 'free' ? 'checked' : '' }} value="free"
                              class="selectgroup-input" checked>
                            <span class="selectgroup-button">{{ __('Entrada gratis') }}</span>
                          </label>

                          <label class="selectgroup-item">
                            <input type="radio" name="pricing_type_2" value="variation"
                              {{ $ticket->pricing_type == 'variation' ? 'checked' : '' }} class="selectgroup-input">
                            <span class="selectgroup-button">{{ __('Por variaciones') }}</span>
                          </label>

                          <label class="selectgroup-item">
                            <input type="radio" name="pricing_type_2" value="normal"
                              {{ $ticket->pricing_type == 'normal' ? 'checked' : '' }} class="selectgroup-input">
                            <span class="selectgroup-button">{{ __('Precio fijo') }}</span>
                          </label>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-12 {{ $ticket->pricing_type == 'variation' ? '' : 'd-none' }}"
                      id="variation_pricing">
                      <div class="form-group">
                        <div class="table-responsive">
                          <table class="table table-bordered">
                            <thead>
                              <tr>
                                <th>{{ __('Nombre de la variación') }}</th>
                                <th>{{ __('Precio') }}</th>
                                <th>{{ __('Entradas disponibles') }}</th>
                                @if ($websiteInfo->event_guest_checkout_status != 1)
                                  <th>{{ __('Máximo por cliente') }}</th>
                                @endif
                                <th><a href="javascript:void(0)" class="btn btn-success btn-sm addRow"><i
                                      class="fas fa-plus-circle"></i></a></th>
                              </tr>
                            </thead>
                            <tbody>
                              @if ($variations != null)
                                @foreach ($variations as $key => $item)
                                  <tr>
                                    <td>
                                      @php
                                        $variation_contents = App\Models\Event\VariationContent::where([['ticket_id', $ticket->id], ['key', $key]])->get();
                                      @endphp
                                      @foreach ($variation_contents as $variation_content)
                                        @php
                                          $language = App\Models\Language::where('id', $variation_content->language_id)->first();
                                        @endphp
                                        <div class="form-group">
                                          <label for="">{{ __('Nombre de la variación') . '*' }}
                                            ({{ $language->name }})
                                          </label>
                                          <input type="text" name="{{ $language->code }}_variation_name[]"
                                            class="form-control" value="{{ $variation_content['name'] }}">
                                        </div>
                                      @endforeach
                                    </td>
                                    <td>
                                      <div class="form-group">
                                        <label
                                          for="">{{ __('Precio') }} ({{ $getCurrencyInfo->base_currency_text }})
                                          *</label>
                                        <input type="text" name="variation_price[]" value="{{ $item['price'] }}"
                                          class="form-control">
                                      </div>
                                    </td>
                                    <td>
                                      <div class="from-group mt-1">
                                        <input type="checkbox" @checked($item['ticket_available_type'] == 'limited')
                                          name="v_ticket_available_type[]" value="limited"
                                          class="ticket_available_type {{ $item['ticket_available_type'] == 'unlimited' ? 'd-none' : '' }}"
                                          id="limited_{{ $loop->iteration }}" data-id="{{ $loop->iteration }}">
                                        <label for="limited_{{ $loop->iteration }}"
                                          class="limited_{{ $loop->iteration }} {{ $item['ticket_available_type'] == 'unlimited' ? 'd-none' : '' }}">{{ __('Limitado') }}</label>

                                        <input type="checkbox" @checked($item['ticket_available_type'] == 'unlimited')
                                          name="v_ticket_available_type[]" value="unlimited"
                                          class="ticket_available_type {{ $item['ticket_available_type'] == 'limited' ? 'd-none' : '' }}"
                                          id="unlimited_{{ $loop->iteration }}" data-id="{{ $loop->iteration }}">
                                        <label for="unlimited_{{ $loop->iteration }}"
                                          class="unlimited_{{ $loop->iteration }} {{ $item['ticket_available_type'] == 'limited' ? 'd-none' : '' }}">{{ __('Ilimitado') }}</label>

                                      </div>

                                      <div
                                        class="form-group {{ $item['ticket_available_type'] == 'unlimited' ? 'd-none' : '' }}"
                                        id="input_{{ $loop->iteration }}">
                                        <label for="">{{ __('Entradas disponibles') . '*' }} </label>
                                        <input type="text" name="v_ticket_available[]"
                                          value="{{ $item['ticket_available'] }}" class="form-control">
                                      </div>
                                    </td>
                                    @if ($websiteInfo->event_guest_checkout_status != 1)
                                      <td>
                                        <div class="from-group mt-1">
                                          <input type="checkbox" @checked($item['max_ticket_buy_type'] == 'limited')
                                            name="v_max_ticket_buy_type[]" value="limited"
                                            class="max_ticket_buy_type {{ $item['max_ticket_buy_type'] == 'unlimited' ? 'd-none' : '' }}"
                                            id="buy_limited_{{ $loop->iteration }}" data-id="{{ $loop->iteration }}">
                                          <label for="buy_limited_{{ $loop->iteration }}"
                                            class="buy_limited_{{ $loop->iteration }} {{ $item['max_ticket_buy_type'] == 'unlimited' ? 'd-none' : '' }}">{{ __('Limitado') }}</label>

                                          <input type="checkbox" @checked($item['max_ticket_buy_type'] == 'unlimited')
                                            name="v_max_ticket_buy_type[]" value="unlimited"
                                            class="max_ticket_buy_type {{ $item['max_ticket_buy_type'] == 'limited' ? 'd-none' : '' }}"
                                            id="buy_unlimited_{{ $loop->iteration }}"
                                            data-id="{{ $loop->iteration }}">
                                          <label for="buy_unlimited_{{ $loop->iteration }}"
                                            class="buy_unlimited_{{ $loop->iteration }} {{ $item['max_ticket_buy_type'] == 'limited' ? 'd-none' : '' }}">{{ __('Ilimitado') }}</label>
                                        </div>

                                        <div
                                          class="form-group {{ $item['max_ticket_buy_type'] == 'unlimited' ? 'd-none' : '' }}"
                                          id="input2_{{ $loop->iteration }}">
                                          <label for="">{{ __('Máximo por cliente') . '*' }} </label>
                                          <input type="text" name="v_max_ticket_buy[]" class="form-control"
                                            value="{{ $item['v_max_ticket_buy'] }}">
                                        </div>
                                      </td>
                                    @else
                                      <input type="hidden" name="v_max_ticket_buy_type[]" value="unlimited">
                                      <input type="hidden" name="v_max_ticket_buy[]" class="form-control">
                                    @endif
                                    <td>
                                      <a href="javascript:void(0)" class="btn btn-danger btn-sm deleteRow"> <i
                                          class="fas fa-minus"></i></a>
                                    </td>
                                  </tr>
                                @endforeach
                              @else
                                <tr>
                                  <td>
                                    @foreach ($languages as $language)
                                      <div class="form-group">
                                        <label for="">{{ __('Nombre de la variación') . '*' }}
                                          ({{ $language->name }})
                                        </label>
                                        <input type="text" name="{{ $language->code }}_variation_name[]"
                                          class="form-control">
                                      </div>
                                    @endforeach
                                  </td>
                                  <td>
                                    <div class="form-group">
                                      <label for="">{{ __('Precio') . '*' }}
                                        ({{ $getCurrencyInfo->base_currency_text }}) </label>
                                      <input type="text" name="variation_price[]" class="form-control">
                                    </div>
                                  </td>
                                  <td>
                                    <div class="from-group mt-1">
                                      <input type="checkbox" checked name="v_ticket_available_type[]" value="limited"
                                        class="ticket_available_type" id="limited_1" data-id="1">
                                      <label for="limited_1" class="limited_1 ">{{ __('Limitado') }}</label>

                                      <input type="checkbox" name="v_ticket_available_type[]" value="unlimited"
                                        class="ticket_available_type d-none" id="unlimited_1" data-id="1">
                                      <label for="unlimited_1" class="unlimited_1 d-none">{{ __('Ilimitado') }}</label>
                                    </div>

                                    <div class="form-group" id="input_1">
                                      <label for="">{{ __('Entradas disponibles') }} * </label>
                                      <input type="text" name="v_ticket_available[]" value=""
                                        class="form-control">
                                    </div>
                                  </td>
                                  @if ($websiteInfo->event_guest_checkout_status != 1)
                                    <td>
                                      <div class="from-group mt-1">
                                        <input type="checkbox" checked name="v_max_ticket_buy_type[]" value="limited"
                                          class="max_ticket_buy_type" id="buy_limited_1" data-id="1">
                                        <label for="buy_limited_1" class="buy_limited_1 ">{{ __('Limitado') }}</label>

                                        <input type="checkbox" name="v_max_ticket_buy_type[]" value="unlimited"
                                          class="max_ticket_buy_type d-none" id="buy_unlimited_1" data-id="1">
                                        <label for="buy_unlimited_1"
                                          class="buy_unlimited_1 d-none">{{ __('Ilimitado') }}</label>
                                      </div>

                                      <div class="form-group" id="input2_1">
                                        <label for="">{{ __('Máximo por cliente') . '*' }} </label>
                                        <input type="text" name="v_max_ticket_buy[]" class="form-control">
                                      </div>
                                    </td>
                                  @else
                                    <input type="hidden" name="v_max_ticket_buy_type[]" value="unlimited">
                                    <input type="hidden" name="v_max_ticket_buy[]" class="form-control">
                                  @endif
                                  <td>
                                    <a href="javascript:void(0)" class="btn btn-danger btn-sm deleteRow">
                                      <i class="fas fa-minus"></i></a>
                                  </td>
                                </tr>
                              @endif

                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-6 {{ $ticket->pricing_type == 'normal' ? '' : 'd-none' }}" id="normal_pricing">
                      <div class="form-group">
                        <label for="">{{ __('Precio') }}
                          ({{ $getCurrencyInfo->base_currency_text }}) *</label>
                        <input type="number" name="price" value="{{ $ticket->price }}" class="form-control"
                          placeholder="{{ __('Ingresá el precio') }}">
                      </div>
                    </div>

                    <div class="col-lg-12  {{ $ticket->pricing_type == 'free' ? 'd-none' : '' }}"
                      id="early_bird_discount_free">
                      <div class="ticket-form-section-heading ticket-form-section-heading--compact">
                        <span class="ticket-form-section-heading__number">2</span>
                        <div>
                          <h4>{{ __('Descuento anticipado') }}</h4>
                          <p>{{ __('Usalo solo cuando quieras mostrar un precio promocional por tiempo limitado.') }}</p>
                        </div>
                      </div>
                      <div class="form-group mt-1">
                        <label for="">{{ __('Descuento anticipado') . '*' }}</label>
                        <div class="selectgroup w-100">
                          <label class="selectgroup-item">
                            <input type="radio" name="early_bird_discount_type"
                              {{ $ticket->early_bird_discount == 'disable' ? 'checked' : '' }} value="disable"
                              class="selectgroup-input" checked>
                            <span class="selectgroup-button">{{ __('Desactivado') }}</span>
                          </label>

                          <label class="selectgroup-item">
                            <input type="radio" name="early_bird_discount_type"
                              {{ $ticket->early_bird_discount == 'enable' ? 'checked' : '' }} value="enable"
                              class="selectgroup-input">
                            <span class="selectgroup-button">{{ __('Activado') }}</span>
                          </label>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-12 {{ $ticket->early_bird_discount == 'enable' ? '' : 'd-none' }}"
                      id="early_bird_dicount">
                      <div class="row">
                        <div class="col-lg-3">
                          <div class="form-group">
                            <label for="">{{ __('Tipo de descuento') }}</label>
                            <select name="discount_type" class="form-control">
                              <option disabled>{{ __('Seleccioná el tipo de descuento') }}</option>
                              <option {{ $ticket->early_bird_discount_type == 'fixed' ? 'selected' : '' }}
                                value="fixed">{{ __('Fijo') }}</option>
                              <option {{ $ticket->early_bird_discount_type == 'percentage' ? 'selected' : '' }}
                                value="percentage">{{ __('Porcentaje') }}</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-lg-3">
                          <div class="form-group">
                            <label for="">{{ __('Importe') }}</label>
                            <input type="number" name="early_bird_discount_amount"
                              value="{{ $ticket->early_bird_discount_amount }}" class="form-control">
                          </div>
                        </div>
                        <div class="col-lg-3">
                          <div class="form-group">
                            <label for="">{{ __('Fecha límite del descuento') }}</label>
                            <input type="date" name="early_bird_discount_date"
                              value="{{ $ticket->early_bird_discount_date }}" class="form-control">
                          </div>
                        </div>
                        <div class="col-lg-3">
                          <div class="form-group">
                            <label for="">{{ __('Hora límite del descuento') }}</label>
                            <input type="time" name="early_bird_discount_time"
                              value="{{ $ticket->early_bird_discount_time }}"class="form-control">
                          </div>
                        </div>

                      </div>
                    </div>
                    <!--=====--variationwise ticket & early bird discount--====== --->

                    <!---=======Ticekt limtit & ticket for each customer start--=====---->
                    <div
                      class="hideInvariatinwiseTicket col-lg-12 {{ $ticket->pricing_type == 'variation' ? 'd-none' : '' }}">
                      <div class="ticket-form-section-heading ticket-form-section-heading--compact">
                        <span class="ticket-form-section-heading__number">3</span>
                        <div>
                          <h4>{{ __('Cupo y límite de compra') }}</h4>
                          <p>{{ __('Controlá cuántas entradas quedan disponibles y cuántas puede reservar cada cliente.') }}</p>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-lg-6">
                          <div class="form-group mt-1">
                            <label for="">{{ __('Total de entradas disponibles') . '*' }}</label>
                            <div class="selectgroup w-100">
                              <label class="selectgroup-item">
                                <input type="radio" name="ticket_available_type"
                                  {{ $ticket->ticket_available_type == 'unlimited' ? 'checked' : '' }}
                                  value="unlimited" class="selectgroup-input">
                                <span class="selectgroup-button">{{ __('Ilimitado') }}</span>
                              </label>

                              <label class="selectgroup-item">
                                <input type="radio" name="ticket_available_type"
                                  {{ $ticket->ticket_available_type == 'limited' ? 'checked' : '' }} value="limited"
                                  class="selectgroup-input">
                                <span class="selectgroup-button">{{ __('Limitado') }}</span>
                              </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-lg-6 {{ $ticket->ticket_available_type == 'limited' ? '' : 'd-none' }}"
                          id="ticket_available">
                          <div class="form-group">
                            <label>{{ __('Ingresá el total de entradas disponibles') . '*' }}</label>
                            <input type="number" name="ticket_available" value="{{ $ticket->ticket_available }}"
                              placeholder="{{ __('Ingresá el total de entradas disponibles') }}" class="form-control">
                          </div>
                        </div>

                        @if ($websiteInfo->event_guest_checkout_status != 1)
                          <div class="col-lg-6">
                            <div class="form-group mt-1">
                              <label
                                for="">{{ __('Máximo de entradas por cliente') . '*' }}</label>
                              <div class="selectgroup w-100">
                                <label class="selectgroup-item">
                                  <input type="radio" name="max_ticket_buy_type" value="unlimited"
                                    class="selectgroup-input"
                                    {{ $ticket->max_ticket_buy_type == 'unlimited' ? 'checked' : '' }}>
                                  <span class="selectgroup-button">{{ __('Ilimitado') }}</span>
                                </label>

                                <label class="selectgroup-item">
                                  <input type="radio" name="max_ticket_buy_type" value="limited"
                                    class="selectgroup-input"
                                    {{ $ticket->max_ticket_buy_type == 'limited' ? 'checked' : '' }}>
                                  <span class="selectgroup-button">{{ __('Limitado') }}</span>
                                </label>
                              </div>
                            </div>
                          </div>
                          <div class="col-lg-6 {{ $ticket->max_ticket_buy_type == 'unlimited' ? 'd-none' : '' }}"
                            id="max_buy_ticket">
                            <div class="form-group">
                              <label>{{ __('Ingresá el máximo de entradas por cliente') . '*' }}</label>
                              <input type="number" name="max_buy_ticket" value="{{ $ticket->max_buy_ticket }}"
                                placeholder="{{ __('Ingresá el máximo de entradas por cliente') }}" class="form-control">
                            </div>
                          </div>
                        @else
                          <input type="hidden" name="max_ticket_buy_type" value="unlimited">
                        @endif
                      </div>
                    </div>
                    <!---======-Ticekt limtit & ticket for each customer end--======= --->
                  </div>
                @endif

                <div class="ticket-form-content-intro mt-3">
                  <h4 class="ticket-form-content-intro__title">{{ __('Nombre y descripción de la entrada') }}</h4>
                  <p class="ticket-form-content-intro__text">{{ __('Asegurate de que el nombre siga siendo claro para quien compra y para quien administra la venta.') }}</p>
                </div>
                <div id="accordion" class="mt-3 ticket-form-language">
                  @foreach ($languages as $language)
                    <div class="version">
                      <div class="version-header" id="heading{{ $language->id }}">
                        <h5 class="mb-0">
                          <button type="button" class="btn btn-link" data-toggle="collapse"
                            data-target="#collapse{{ $language->id }}"
                            aria-expanded="{{ $language->is_default == 1 ? 'true' : 'false' }}"
                            aria-controls="collapse{{ $language->id }}">
                            {{ $language->name }}
                            {{ $language->is_default == 1 ? '(' . __('Predeterminado') . ')' : '' }}
                          </button>
                        </h5>
                      </div>
                      @php
                        $ticket_content = App\Models\Event\TicketContent::where([['ticket_id', $ticket->id], ['language_id', $language->id]])->first();
                      @endphp

                      <div id="collapse{{ $language->id }}"
                        class="collapse {{ $language->is_default == 1 ? 'show' : '' }}"
                        aria-labelledby="heading{{ $language->id }}" data-parent="#accordion">
                        <div class="version-body">
                          <div class="row">
                            <div class="col-lg-12">
                              <div class="form-group">
                                <label>{{ __('Nombre de la entrada') . '*' }}</label>
                                <input type="text" name="{{ $language->code }}_title"
                                  placeholder="{{ __('Ingresá el nombre de la entrada') }}" value="{{ @$ticket_content->title }}"
                                  class="form-control">
                              </div>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col">
                              <div class="form-group">
                                <label>{{ __('Descripción') }}</label>
                                <textarea class="form-control" name="{{ $language->code }}_description" placeholder="{{ __('Ingresá una descripción') }}">{{ @$ticket_content->description }}</textarea>
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
                                    <span class="form-check-sign">{{ __('Clonar para') }} <strong
                                        class="text-capitalize text-secondary">{{ $language->name }}</strong>
                                    </span>
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
              </form>
            </div>
          </div>
        </div>

        <div class="card-footer">
          <div class="ticket-form-footer">
            <p class="ticket-form-footer__text">{{ __('Guardá cuando termines. Si algún dato está incompleto, el sistema te lo va a marcar antes de actualizar la entrada.') }}</p>
            <button type="submit" id="EventSubmit" class="btn btn-success ticket-form-footer__btn">
              {{ __('Guardar cambios') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('script')
  @php
    $languages = App\Models\Language::get();
    $names = '';
    foreach ($languages as $language) {
        $varitaion_name = $language->code . '_variation_name[]';
        $names .= "<div class='form-group'><label for=''>" . __('Nombre de la variación') . " *($language->name)</label><input type='text' name='$varitaion_name' class='form-control'></div>";
    }
  @endphp
  <script>
    let names = "{!! $names !!}";
    let BaseCTxt = "{{ $getCurrencyInfo->base_currency_text }}";
    var guest_checkout_status = "{{ $websiteInfo->event_guest_checkout_status }}";
  </script>
  <script type="text/javascript" src="{{ asset('assets/admin/js/admin-partial.js') }}"></script>
@endsection

@section('variables')
  <script>
    "use strict";
    var removeUrl = "{{ route('admin.event.imagermv') }}";
  </script>
@endsection

@section('style')
  <style>
    .ticket-form-card {
      border: 0;
      border-radius: 8px;
      box-shadow: 0 10px 24px rgba(15, 23, 42, .05);
      overflow: hidden;
    }

    .ticket-form-card .card-header {
      border-bottom: 1px solid #e5e7eb;
      background: #fff;
      padding: 24px;
    }

    .ticket-form-card .card-body {
      background: #fff;
      padding: 22px 24px;
    }

    .ticket-form-card .card-footer {
      border-top: 1px solid #eef2f7;
      background: #fbfcfe;
      padding: 18px 24px 24px;
    }

    .ticket-form-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 20px;
      flex-wrap: wrap;
    }

    .ticket-form-header__eyebrow,
    .ticket-form-intro__eyebrow {
      display: inline-flex;
      align-items: center;
      min-height: 24px;
      padding: 4px 10px;
      border-radius: 999px;
      background: #f1f5f9;
      color: #475569;
      font-size: 11px;
      font-weight: 600;
      margin-bottom: 10px;
    }

    .ticket-form-header__title,
    .ticket-form-content-intro__title {
      margin-bottom: 6px;
      color: #0f172a;
      font-size: 24px;
      font-weight: 700;
      line-height: 1.25;
    }

    .ticket-form-content-intro__title {
      font-size: 18px;
    }

    .ticket-form-header__text,
    .ticket-form-intro__text,
    .ticket-form-content-intro__text,
    .ticket-form-footer__text {
      margin-bottom: 0;
      color: #64748b;
      font-size: 13px;
      line-height: 1.6;
    }

    .ticket-form-header__event {
      display: inline-flex;
      margin-top: 10px;
      color: #334155;
      font-size: 12px;
      font-weight: 600;
    }

    .ticket-form-header__actions {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      justify-content: flex-end;
    }

    .ticket-form-header__btn {
      min-height: 40px;
      border-radius: 10px;
      padding-inline: 16px;
      font-weight: 600;
    }

    .ticket-form-intro,
    .ticket-form-content-intro {
      margin-bottom: 18px;
      padding: 16px 18px;
      border: 1px solid #e5e7eb;
      border-radius: 14px;
      background: #f8fafc;
    }

    .ticket-form-section {
      padding: 20px;
      border: 1px solid #e5e7eb;
      border-radius: 16px;
      background: #fff;
      box-shadow: 0 8px 18px rgba(15, 23, 42, .035);
    }

    .ticket-form-section-heading {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      margin-bottom: 16px;
    }

    .ticket-form-section-heading--compact {
      margin-top: 8px;
    }

    .ticket-form-section-heading__number {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 28px;
      height: 28px;
      flex: 0 0 28px;
      border-radius: 999px;
      background: #eff6ff;
      color: #1d4ed8;
      font-size: 12px;
      font-weight: 700;
    }

    .ticket-form-section-heading h4 {
      margin: 0 0 3px;
      color: #0f172a;
      font-size: 16px;
      font-weight: 700;
    }

    .ticket-form-section-heading p {
      margin: 0;
      color: #64748b;
      font-size: 12px;
      line-height: 1.45;
    }

    #eventForm .form-group {
      margin-bottom: 16px;
    }

    #eventForm .form-control {
      min-height: 44px;
      border-color: #dbe3ef;
      border-radius: 10px;
      color: #0f172a;
      font-size: 13px;
    }

    #eventForm .form-control:focus {
      border-color: #60a5fa;
      box-shadow: 0 0 0 3px rgba(37, 99, 235, .1);
    }

    #eventForm label {
      color: #334155;
      font-size: 12px;
      font-weight: 600;
      margin-bottom: 7px;
    }

    #eventForm .selectgroup {
      gap: 8px;
    }

    #eventForm .selectgroup-item {
      margin-right: 8px;
    }

    #eventForm .selectgroup-button {
      min-height: 44px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-color: #dbe3ef;
      border-radius: 10px !important;
      color: #334155;
      font-size: 13px;
      font-weight: 600;
    }

    #eventForm .selectgroup-input:checked + .selectgroup-button {
      border-color: #2563eb;
      background: #2563eb;
      color: #fff;
    }

    #eventForm .table-responsive {
      border: 1px solid #e5e7eb;
      border-radius: 12px;
      background: #fff;
    }

    #eventForm .table-bordered {
      min-width: 860px;
      margin-bottom: 0;
      border: 0;
    }

    #eventForm .table-bordered th {
      border-top: 0;
      border-color: #e5e7eb;
      background: #f8fafc;
      color: #475569;
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 0;
      text-transform: none;
      vertical-align: middle;
    }

    #eventForm .table-bordered td {
      border-color: #eef2f7;
      vertical-align: top;
    }

    .ticket-form-language .version {
      border: 1px solid #e5e7eb;
      border-radius: 12px;
      overflow: hidden;
      margin-bottom: 12px;
      background: #fff;
    }

    .ticket-form-language .version-header {
      background: #f8fafc;
    }

    .ticket-form-language .btn-link {
      width: 100%;
      padding: 14px 16px;
      text-align: left;
      color: #0f172a;
      font-weight: 600;
      text-decoration: none;
    }

    .ticket-form-language .version-body {
      padding: 18px;
    }

    .ticket-form-footer {
      max-width: 640px;
      margin: 0 auto;
      text-align: center;
    }

    .ticket-form-footer__btn {
      min-width: 220px;
      min-height: 44px;
      border-radius: 12px;
      padding: 11px 22px;
      font-weight: 700;
      margin-top: 14px;
    }

    @media (max-width: 767px) {
      .ticket-form-card .card-header,
      .ticket-form-card .card-body,
      .ticket-form-card .card-footer {
        padding: 16px;
      }

      .ticket-form-header__actions,
      .ticket-form-header__actions .btn {
        width: 100%;
      }

      .ticket-form-header__title {
        font-size: 21px;
      }

      .ticket-form-section,
      .ticket-form-intro,
      .ticket-form-content-intro {
        padding: 14px;
      }

      #eventForm .selectgroup {
        display: grid;
        grid-template-columns: 1fr;
      }

      #eventForm .selectgroup-item {
        margin-right: 0;
      }

      .ticket-form-footer__btn {
        width: 100%;
      }
    }
  </style>
@endsection
