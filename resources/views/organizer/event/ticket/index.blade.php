@extends('organizer.layout')

@php
  $eventModel = $information['eventModel'] ?? null;
  $freeLimitEnabled = (bool) optional($eventModel)->limit_free_tickets_per_person;
  $freeLimitValue = max((int) (optional($eventModel)->free_tickets_per_person_limit ?: 2), 1);
@endphp

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Tickets') }}</h4>
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
      <li class="nav-item">
        <a href="#">
          {{ strlen($information['event']['title']) > 35 ? mb_substr($information['event']['title'], 0, 35, 'UTF-8') . '...' : $information['event']['title'] }}
        </a>
      </li>

      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a
          href="{{ route('organizer.event.ticket', ['language' => $defaultLang->code, 'event_id' => request()->input('event_id'), 'event_type' => request()->input('event_type')]) }}">{{ __('Tickets') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-lg-4">
              <div class="card-title d-inline-block">
                {{ __('Tickets') }}
              </div>
            </div>

            <div class="col-lg-3">
              <form action="" method="get" id="LangFrom">
                <input type="hidden" name="event_id" value="{{ request()->input('event_id') }}">
                <input type="hidden" name="event_type" value="{{ request()->input('event_type') }}">
                <select name="language" class="form-control" onchange="document.getElementById('LangFrom').submit()">
                  <option selected disabled>{{ __('Select a Language') }}</option>
                  @foreach ($languages as $lang)
                    <option value="{{ $lang->code }}"
                      {{ $lang->code == request()->input('language') ? 'selected' : '' }}>
                      {{ $lang->name }}
                    </option>
                  @endforeach
                </select>
              </form>
            </div>

            <div class="col-lg-4 offset-lg-1 mt-2 mt-lg-0">
              <button class="btn btn-danger btn-sm float-right ml-2 d-none bulk-delete"
                data-href="{{ route('organizer.event_management.bulk_delete_event_ticket') }}">
                <i class="flaticon-interface-5"></i> {{ __('Delete') }}
              </button>

              <a href="{{ route('organizer.event_management.event', ['language' => $defaultLang->code, 'event_type' => request()->input('event_type')]) }}"
                class="btn btn-info btn-sm float-right"><i class="fas fa-backward"></i>
                {{ __('Back') }}</a>

              <a href="{{ route('organizer.event.add.ticket', ['language' => $defaultLang->code, 'event_id' => request()->input('event_id'), 'event_type' => request()->input('event_type')]) }}"
                class="btn btn-secondary btn-sm float-right mr-2"><i class="fas fa-plus-circle"></i>
                {{ __('Add Ticket') }}</a>

              <a class="mr-2 btn btn-success btn-sm float-right d-inline-block"
                href="{{ route('event.details', ['slug' => eventSlug($defaultLang->id, request()->input('event_id')), 'id' => request()->input('event_id')]) }}"
                target="_blank">
                <span class="btn-label">
                  <i class="fas fa-eye"></i>
                </span>
                {{ __('Preview') }}
              </a>

            </div>
          </div>
        </div>

        <div class="card-body">
          <div class="row">
            <div class="col-lg-12">

              @if (session()->has('course_status_warning'))
                <div class="alert alert-warning">
                  <p class="text-dark mb-0">{{ session()->get('course_status_warning') }}</p>
                </div>
              @endif

              <form action="{{ route('organizer.event.ticket.free_limit') }}" method="POST" class="ticket-free-limit mb-4">
                @csrf
                <input type="hidden" name="event_id" value="{{ request()->input('event_id') }}">

                <div class="ticket-free-limit__copy">
                  <span>{{ __('Control de entradas gratis') }}</span>
                  <strong>{{ __('Limitar entradas sin costo por persona') }}</strong>
                  <p>{{ __('Cuando está activo, una persona puede reservar como máximo la cantidad indicada de entradas gratis o con precio $0 en este evento. El control cruza email, teléfono y DNI. No afecta entradas pagas.') }}</p>
                </div>

                <div class="ticket-free-limit__controls">
                  <label class="ticket-free-limit__toggle">
                    <input type="checkbox" name="limit_free_tickets_per_person" value="1" @checked($freeLimitEnabled)>
                    <span>{{ $freeLimitEnabled ? __('Activado') : __('Desactivado') }}</span>
                  </label>

                  <label class="ticket-free-limit__number">
                    <span>{{ __('Máximo gratis por persona') }}</span>
                    <input type="number" name="free_tickets_per_person_limit" min="1" max="10"
                      value="{{ $freeLimitValue }}" class="form-control">
                  </label>

                  <button type="submit" class="btn btn-primary btn-sm">
                    {{ __('Guardar límite') }}
                  </button>
                </div>
              </form>

              @if (count($information['tickets']) == 0)
                <h3 class="text-center mt-2">{{ __('NO TICKET FOUND') . '!' }}</h3>
              @else
                <div class="table-responsive">
                  <table class="table table-striped mt-3" id="basic-datatables">
                    <thead>
                      <tr>
                        <th scope="col">
                          <input type="checkbox" class="bulk-check" data-val="all">
                        </th>
                        <th scope="col">{{ __('Title') }}</th>
                        <th scope="col">{{ __('Ticket Available') }}</th>
                        <th scope="col">{{ __('Price') }}</th>
                        <th scope="col">{{ __('Actions') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($information['tickets'] as $ticket)
                        <tr>
                          <td width="5%">
                            <input type="checkbox" class="bulk-check" data-val="{{ $ticket->id }}">
                          </td>
                          <td width="20%">
                            @php
                              $ticket_content = App\Models\Event\TicketContent::where([['language_id', $information['language']['id']], ['ticket_id', $ticket->id]])->first();
                              if (empty($ticket_content)) {
                                  $ticket_content = App\Models\Event\TicketContent::where('ticket_id', $ticket->id)->first();
                              }
                            @endphp
                            {{ @$ticket_content->title }}
                          </td>
                          <td width="20%">
                            @if ($ticket->pricing_type == 'variation')
                              @php
                                $variation = json_decode($ticket->variations, true);
                              @endphp
                              @foreach ($variation as $v)
                                @if ($v['ticket_available_type'] == 'unlimited')
                                  {{ __('Unlimited') }}
                                @else
                                  {{ $v['ticket_available'] }}
                                @endif
                                @if (!$loop->last)
                                  ,
                                @endif
                              @endforeach
                            @else
                              @if ($ticket->ticket_available_type == 'unlimited')
                                <span class="badge badge-info">{{ $ticket->ticket_available_type }}</span>
                              @else
                                {{ $ticket->ticket_available }}
                              @endif
                            @endif

                          </td>
                          <td>
                            @if ($ticket->pricing_type == 'normal')
                              @if ($ticket->early_bird_discount == 'enable')
                                @php
                                  $discount_date = Carbon\Carbon::parse($ticket->early_bird_discount_date . $ticket->early_bird_discount_time);
                                @endphp

                                @if ($ticket->early_bird_discount_type == 'fixed' && !$discount_date->isPast())
                                  @php
                                    $calculate_price = $ticket->price - $ticket->early_bird_discount_amount;
                                  @endphp
                                  {{ symbolPrice($calculate_price) }}
                                  <del>
                                    {{ symbolPrice($ticket->price) }}
                                  </del>
                                @elseif ($ticket->early_bird_discount_type == 'percentage' && !$discount_date->isPast())
                                  @php
                                    $c_price = ($ticket->price * $ticket->early_bird_discount_amount) / 100;
                                    $calculate_price = $ticket->price - $c_price;
                                  @endphp
                                  {{ symbolPrice($calculate_price) }}
                                  <del>
                                    {{ symbolPrice($ticket->price) }}
                                  </del>
                                @else
                                  @php
                                    $calculate_price = $ticket->price;
                                  @endphp
                                  {{ symbolPrice($calculate_price) }}
                                @endif
                              @else
                                {{ symbolPrice($ticket->price) }}
                              @endif
                            @elseif ($ticket->pricing_type == 'variation')
                              @php
                                $variation = json_decode($ticket->variations, true);
                              @endphp
                              @foreach ($variation as $v)
                                @if ($ticket->early_bird_discount == 'enable')
                                  @php
                                    $discount_date = Carbon\Carbon::parse($ticket->early_bird_discount_date . $ticket->early_bird_discount_time);
                                  @endphp

                                  @if ($ticket->early_bird_discount_type == 'fixed' && !$discount_date->isPast())
                                    @php
                                      $calculate_price = $v['price'] - $ticket->early_bird_discount_amount;
                                    @endphp
                                    {{ symbolPrice($calculate_price) }}
                                    <del>

                                      {{ symbolPrice($v['price']) }}
                                    </del>
                                  @elseif ($ticket->early_bird_discount_type == 'percentage' && !$discount_date->isPast())
                                    @php
                                      $c_price = ($v['price'] * $ticket->early_bird_discount_amount) / 100;
                                      $calculate_price = $v['price'] - $c_price;
                                    @endphp
                                    {{ symbolPrice($calculate_price) }}

                                    <del>
                                      {{ symbolPrice($v['price']) }}
                                    </del>
                                  @else
                                    @php
                                      $calculate_price = $v['price'];
                                    @endphp
                                    {{ symbolPrice($calculate_price) }}
                                  @endif
                                  @if (!$loop->last)
                                    ,
                                  @endif
                                @else
                                  {{ symbolPrice($v['price']) }}
                                  @if (!$loop->last)
                                    ,
                                  @endif
                                @endif
                              @endforeach
                            @elseif ($ticket->pricing_type == 'free')
                              <span class="badge badge-info">{{ __('Free') }}</span>
                            @endif

                          </td>
                          <td>
                            <div class="dropdown">
                              <button class="btn btn-secondary dropdown-toggle btn-sm" type="button"
                                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ __('Select') }}
                              </button>

                              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a href="{{ route('organizer.event.edit.ticket', ['language' => $defaultLang->code, 'event_id' => request()->input('event_id'), 'event_type' => request()->input('event_type'), 'id' => $ticket->id]) }}"
                                  class="dropdown-item">
                                  {{ __('Edit') }}
                                </a>

                                <form class="deleteForm d-block"
                                  action="{{ route('organizer.ticket_management.delete_ticket', ['id' => $ticket->id]) }}"
                                  method="post">

                                  @csrf
                                  <button type="submit" class="btn btn-sm deleteBtn">
                                    {{ __('Delete') }}
                                  </button>
                                </form>
                              </div>
                            </div>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @endif
            </div>
          </div>
        </div>

        <div class="card-footer"></div>
      </div>
    </div>
  </div>
@endsection

@section('style')
  <style>
    .ticket-free-limit {
      display: grid;
      grid-template-columns: minmax(0, 1fr) minmax(280px, auto);
      gap: 16px;
      align-items: center;
      padding: 16px;
      border: 1px solid #dbeafe;
      border-radius: 12px;
      background: #f8fbff;
    }

    .ticket-free-limit__copy span,
    .ticket-free-limit__number span {
      display: block;
      margin-bottom: 6px;
      color: #2563eb;
      font-size: 11px;
      font-weight: 700;
    }

    .ticket-free-limit__copy strong {
      display: block;
      color: #0f172a;
      font-size: 15px;
      line-height: 1.3;
    }

    .ticket-free-limit__copy p {
      max-width: 760px;
      margin: 6px 0 0;
      color: #64748b;
      font-size: 12px;
      line-height: 1.5;
    }

    .ticket-free-limit__controls {
      display: flex;
      flex-wrap: wrap;
      justify-content: flex-end;
      gap: 10px;
      align-items: end;
    }

    .ticket-free-limit__toggle {
      display: inline-flex;
      align-items: center;
      min-height: 40px;
      margin: 0;
      padding: 8px 12px;
      border: 1px solid #cbd5e1;
      border-radius: 10px;
      background: #fff;
      color: #0f172a;
      font-size: 12px;
      font-weight: 700;
      cursor: pointer;
    }

    .ticket-free-limit__toggle input {
      margin-right: 8px;
      accent-color: #f97316;
    }

    .ticket-free-limit__number {
      min-width: 170px;
      margin: 0;
    }

    .ticket-free-limit__number .form-control {
      height: 40px;
      border-radius: 10px;
    }

    @media (max-width: 1199px) {
      .ticket-free-limit {
        grid-template-columns: 1fr;
      }

      .ticket-free-limit__controls {
        justify-content: flex-start;
      }
    }

    @media (max-width: 575px) {
      .ticket-free-limit__controls .btn,
      .ticket-free-limit__number,
      .ticket-free-limit__toggle {
        width: 100%;
      }
    }
  </style>
@endsection
