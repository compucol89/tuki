@extends('organizer.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Events') }}</h4>
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
        <a href="#">{{ __('Events Management') }}</a>
      </li>
      @if (!request()->filled('event_type'))
        <li class="separator">
          <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
          <a
            href="{{ route('organizer.event_management.event', ['language' => $defaultLang->code]) }}">{{ __('All Events') }}</a>
        </li>
      @endif
      @if (request()->filled('event_type') && request()->input('event_type') == 'venue')
        <li class="separator">
          <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
          <a
            href="{{ route('organizer.event_management.event', ['language' => $defaultLang->code, 'event_type' => 'venue']) }}">{{ __('Venue Events') }}</a>
        </li>
      @endif
      @if (request()->filled('event_type') && request()->input('event_type') == 'online')
        <li class="separator">
          <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
          <a href="#">{{ __('Online Events') }}</a>
        </li>
      @endif
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="event-index-header">
            <div class="event-index-header__intro">
              <span class="event-index-header__eyebrow">{{ __('Gestion') }}</span>
              <h3 class="event-index-header__title">{{ __('Eventos') }}</h3>
              <p class="event-index-header__text">{{ __('Administra tus eventos, revisa su estado y entra rapido a edicion, tickets o acciones clave.') }}</p>
            </div>

            <div class="event-index-toolbar">
              <div class="event-index-toolbar__group">
                @if (!empty($langs) && count($langs) > 1)
                  <select name="language" class="form-control event-index-select"
                    onchange="window.location='{{ url()->current() . '?language=' }}' + this.value+'&event_type='+'{{ request()->input('event_type') }}'">
                    <option selected disabled>{{ __('Idioma del listado') }}</option>
                    @foreach ($langs as $lang)
                      <option value="{{ $lang->code }}"
                        {{ $lang->code == request()->input('language') ? 'selected' : '' }}>
                        {{ $lang->name }}
                      </option>
                    @endforeach
                  </select>
                @endif

                <div class="dropdown">
                  <button class="btn btn-primary dropdown-toggle event-index-add-btn" type="button"
                    id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ __('Crear evento') }}
                  </button>

                  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                    <a href="{{ route('organizer.add.event.event', ['type' => 'online']) }}" class="dropdown-item">
                      {{ __('Evento online') }}
                    </a>

                    <a href="{{ route('organizer.add.event.event', ['type' => 'venue']) }}" class="dropdown-item">
                      {{ __('Evento presencial') }}
                    </a>
                  </div>
                </div>

                <button class="btn btn-danger d-none bulk-delete event-index-bulk-delete"
                  data-href="{{ route('organizer.event_management.bulk_delete_event') }}">
                  <i class="flaticon-interface-5"></i> {{ __('Eliminar seleccionados') }}
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="card-body">
          <div class="row">
            <div class="col-lg-12">
              <div class="event-index-filters">
                <div class="event-index-stats">
                  <div class="event-index-stat">
                    <span class="event-index-stat__label">{{ __('Idioma activo') }}</span>
                    <strong class="event-index-stat__value">{{ $language->name }}</strong>
                  </div>
                  <div class="event-index-stat">
                    <span class="event-index-stat__label">{{ __('Resultados') }}</span>
                    <strong class="event-index-stat__value">{{ $events->total() }}</strong>
                  </div>
                  <div class="event-index-stat">
                    <span class="event-index-stat__label">{{ __('Tipo') }}</span>
                    <strong class="event-index-stat__value">
                      @if (!request()->filled('event_type'))
                        {{ __('Todos') }}
                      @elseif (request()->input('event_type') == 'venue')
                        {{ __('Presencial') }}
                      @else
                        {{ __('Online') }}
                      @endif
                    </strong>
                  </div>
                </div>

                <form action="" method="get" class="event-index-search">
                  <input type="hidden" name="language" value="{{ request()->input('language') }}">
                  @if (request()->filled('event_type'))
                    <input type="hidden" name="event_type" value="{{ request()->input('event_type') }}">
                  @endif
                  <div class="event-index-search__field">
                    <i class="fas fa-search"></i>
                    <input type="text" name="title" value="{{ request()->input('title') }}"
                      placeholder="{{ __('Busca por nombre del evento') }}" class="form-control">
                  </div>
                  <button type="submit" class="btn btn-primary event-index-search__btn">{{ __('Buscar') }}</button>
                </form>
              </div>

              @if (count($events) == 0)
                <div class="event-index-empty text-center">
                  <h3>{{ __('No encontramos eventos para este idioma') }}</h3>
                  <p class="mb-0">{{ __('Prueba con otro filtro o crea un evento nuevo.') }}</p>
                </div>
              @else
                <div class="table-responsive event-index-table-wrap">
                  <table class="table event-index-table mt-3">
                    <thead>
                      <tr>
                        <th scope="col">
                          <input type="checkbox" class="bulk-check" data-val="all">
                        </th>
                        <th scope="col" width="30%">{{ __('Title') }}</th>
                        <th scope="col">{{ __('Type') }}</th>
                        <th scope="col">{{ __('Category') }}</th>
                        <th scope="col">{{ __('Ticket') }}</th>
                        <th scope="col">{{ __('Status') }}</th>
                        <th scope="col">{{ __('Featured') }}</th>
                        <th scope="col">{{ __('Actions') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($events as $event)
                        <tr>
                          <td>
                            <input type="checkbox" class="bulk-check" data-val="{{ $event->id }}">
                          </td>
                          <td width="20%">
                            <a target="_blank"
                              href="{{ route('event.details', ['slug' => $event->slug, 'id' => $event->id]) }}" class="event-index-title-link">{{ strlen($event->title) > 30 ? mb_substr($event->title, 0, 30, 'UTF-8') . '....' : $event->title }}</a>
                          </td>
                          <td>
                            {{ ucfirst($event->event_type) }}
                          </td>
                          <td>
                            {{ $event->category }}
                          </td>
                          <td>
                            @if ($event->event_type == 'venue')
                              <a href="{{ route('organizer.event.ticket', ['language' => request()->input('language'), 'event_id' => $event->id, 'event_type' => $event->event_type]) }}"
                                class="btn btn-success btn-sm">{{ __('Manage') }}</a>
                            @endif
                          </td>
                          <td>
                            <form id="statusForm-{{ $event->id }}" class="d-inline-block"
                              action="{{ route('organizer.event_management.event.event_status', ['id' => $event->id, 'language' => request()->input('language')]) }}"
                              method="post">

                              @csrf
                              <select
                                class="form-control form-control-sm event-index-pill-select {{ $event->status == 0 ? 'bg-warning text-dark' : 'bg-primary' }}"
                                name="status"
                                onchange="document.getElementById('statusForm-{{ $event->id }}').submit()">
                                <option value="1" {{ $event->status == 1 ? 'selected' : '' }}>
                                  {{ __('Active') }}
                                </option>
                                <option value="0" {{ $event->status == 0 ? 'selected' : '' }}>
                                  {{ __('Deactive') }}
                                </option>
                              </select>
                            </form>
                          </td>
                          <td>

                            <form id="featuredForm-{{ $event->id }}" class="d-inline-block"
                              action="{{ route('organizer.event_management.event.update_featured', ['id' => $event->id]) }}"
                              method="post">

                              @csrf
                              <select
                                class="form-control form-control-sm event-index-pill-select {{ $event->is_featured == 'yes' ? 'bg-success' : 'bg-danger' }}"
                                name="is_featured"
                                onchange="document.getElementById('featuredForm-{{ $event->id }}').submit()">
                                <option value="yes" {{ $event->is_featured == 'yes' ? 'selected' : '' }}>
                                  {{ __('Yes') }}
                                </option>
                                <option value="no" {{ $event->is_featured == 'no' ? 'selected' : '' }}>
                                  {{ __('No') }}
                                </option>
                              </select>
                            </form>
                          </td>
                          <td>
                            <div class="dropdown">
                              <button class="btn btn-secondary dropdown-toggle btn-sm event-index-actions-btn" type="button"
                                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                {{ __('Acciones') }}
                              </button>

                              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a href="{{ route('organizer.event_management.edit_event', ['id' => $event->id]) }}"
                                  class="dropdown-item">
                                  {{ __('Edit') }}
                                </a>

                                <a href="{{ route('organizer.event_management.ticket_setting', ['id' => $event->id]) }}"
                                  class="dropdown-item">
                                  {{ __('Ticket Settings') }}
                                </a>

                                <form class="deleteForm d-block"
                                  action="{{ route('organizer.event_management.delete_event', ['id' => $event->id]) }}"
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

        <div class="card-footer text-center">
          <div class="d-inline-block mt-3">
            {{ $events->appends([
                    'language' => request()->input('language'),
                    'title' => request()->input('title'),
                ])->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('style')
  <style>
    .event-index-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 20px;
      flex-wrap: wrap;
    }

    .event-index-header__eyebrow {
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

    .event-index-header__title {
      margin-bottom: 6px;
      color: #0f172a;
      font-size: 28px;
      font-weight: 700;
    }

    .event-index-header__text {
      margin-bottom: 0;
      max-width: 620px;
      color: #64748b;
      line-height: 1.7;
    }

    .event-index-toolbar__group {
      display: flex;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
      justify-content: flex-end;
    }

    .event-index-select {
      min-width: 210px;
      border-radius: 12px;
    }

    .event-index-add-btn,
    .event-index-bulk-delete {
      border-radius: 12px;
      padding-inline: 16px;
    }

    .event-index-filters {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 18px;
      flex-wrap: wrap;
      margin-bottom: 18px;
      padding: 18px;
      border: 1px solid #e5e7eb;
      border-radius: 18px;
      background: linear-gradient(180deg, #fcfdff 0%, #f8fbff 100%);
    }

    .event-index-stats {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
    }

    .event-index-stat {
      min-width: 140px;
      padding: 12px 14px;
      border: 1px solid #dbe5f3;
      border-radius: 14px;
      background: #fff;
    }

    .event-index-stat__label {
      display: block;
      margin-bottom: 4px;
      color: #64748b;
      font-size: 12px;
    }

    .event-index-stat__value {
      color: #0f172a;
      font-size: 16px;
      font-weight: 700;
    }

    .event-index-search {
      display: flex;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
      margin-left: auto;
    }

    .event-index-search__field {
      position: relative;
      min-width: 320px;
    }

    .event-index-search__field i {
      position: absolute;
      top: 50%;
      left: 14px;
      transform: translateY(-50%);
      color: #94a3b8;
    }

    .event-index-search__field .form-control {
      padding-left: 40px;
      border-radius: 12px;
    }

    .event-index-search__btn {
      border-radius: 12px;
      padding-inline: 18px;
    }

    .event-index-empty {
      padding: 40px 20px;
      border: 1px dashed #d6d9e6;
      border-radius: 18px;
      background: #f8fafc;
      color: #64748b;
    }

    .event-index-empty h3 {
      margin-bottom: 10px;
      color: #0f172a;
      font-size: 24px;
      font-weight: 700;
    }

    .event-index-table-wrap {
      border: 1px solid #e5e7eb;
      border-radius: 18px;
      overflow: hidden;
      background: #fff;
    }

    .event-index-table {
      margin-top: 0 !important;
      margin-bottom: 0;
    }

    .event-index-table thead th {
      border-top: 0;
      border-bottom: 1px solid #e5e7eb;
      background: #f8fafc;
      color: #475569;
      font-size: 12px;
      font-weight: 700;
      letter-spacing: .04em;
      text-transform: uppercase;
      vertical-align: middle;
    }

    .event-index-table tbody td {
      vertical-align: middle;
      border-top: 1px solid #eef2f7;
    }

    .event-index-title-link {
      color: #0f172a;
      font-weight: 600;
      text-decoration: none;
    }

    .event-index-title-link:hover {
      color: #2563eb;
      text-decoration: none;
    }

    .event-index-pill-select {
      min-width: 104px;
      border-radius: 10px;
      border: 0;
      color: #fff;
      font-weight: 600;
    }

    .event-index-actions-btn {
      border-radius: 10px;
      padding-inline: 14px;
    }
  </style>
@endsection
