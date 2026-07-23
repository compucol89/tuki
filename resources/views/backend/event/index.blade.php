@extends('backend.layout')

@section('content')
  @php
    $settlementCurrencySettings = $settings ?? null;
    $formatSettlementMoney = function ($amount) use ($settlementCurrencySettings) {
        $symbol = optional($settlementCurrencySettings)->base_currency_symbol ?: '$';
        $position = optional($settlementCurrencySettings)->base_currency_symbol_position ?: 'left';
        $amount = number_format((float) $amount, 0, ',', '.');
        return ($position == 'left' ? $symbol : '') . $amount . ($position == 'right' ? $symbol : '');
    };
    $settlementStatusLabels = [
        'pending' => ['label' => __('Pendiente'), 'class' => 'warning text-dark'],
        'partial' => ['label' => __('Parcial'), 'class' => 'info'],
        'settled' => ['label' => __('Liquidado'), 'class' => 'success'],
        'no_balance' => ['label' => __('Sin saldo'), 'class' => 'secondary'],
    ];
  @endphp

  <div class="admin-event-index">
  <div class="page-header">
    <h4 class="page-title">{{ __('Eventos') }}</h4>
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
      @if (!request()->filled('event_type'))
        <li class="separator">
          <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
          <a
            href="{{ route('admin.event_management.event', ['language' => $defaultLang->code]) }}">{{ __('Todos los eventos') }}</a>
        </li>
      @endif
      @if (request()->filled('event_type') && request()->input('event_type') == 'venue')
        <li class="separator">
          <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
          <a href="#">{{ __('Eventos presenciales') }}</a>
        </li>
      @endif
      @if (request()->filled('event_type') && request()->input('event_type') == 'online')
        <li class="separator">
          <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
          <a href="#">{{ __('Eventos en línea') }}</a>
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
              <span class="event-index-header__eyebrow">{{ __('Gestión') }}</span>
              <h3 class="event-index-header__title">{{ __('Eventos') }}</h3>
              <p class="event-index-header__text">{{ __('Administrá tus eventos, revisá su estado y entrá rápido a edición, entradas o acciones clave.') }}</p>
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
                    <a href="{{ route('add.event.event', ['type' => 'online']) }}" class="dropdown-item">
                      {{ __('Evento en línea') }}
                    </a>

                    <a href="{{ route('add.event.event', ['type' => 'venue']) }}" class="dropdown-item">
                      {{ __('Evento presencial') }}
                    </a>
                  </div>
                </div>

                <button class="btn btn-danger d-none bulk-delete event-index-bulk-delete"
                  data-href="{{ route('admin.event_management.bulk_delete_event') }}">
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
                        {{ __('En línea') }}
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
                  <p class="mb-0">{{ __('Prueba con otro idioma, otro filtro o crea un evento nuevo.') }}</p>
                </div>
              @else
                <div class="table-responsive event-index-table-wrap">
                  <table class="table event-index-table mt-3">
                    <thead>
                      <tr>
                        <th scope="col" class="event-index-col-check">
                          <input type="checkbox" class="bulk-check" data-val="all" aria-label="{{ __('Seleccionar todos') }}">
                        </th>
                        <th scope="col" class="event-index-col-event">{{ __('Evento') }}</th>
                        <th scope="col" class="event-index-col-entry">{{ __('Entrada') }}</th>
                        <th scope="col" class="event-index-col-status">{{ __('Estado') }}</th>
                        <th scope="col" class="event-index-col-featured">{{ __('Destacada') }}</th>
                        <th scope="col" class="event-index-col-settlement">{{ __('Liquidación') }}</th>
                        <th scope="col" class="event-index-col-amounts">{{ __('Montos') }}</th>
                        <th scope="col" class="event-index-col-actions">{{ __('Acciones') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($events as $event)
                        @php
                          $settlementSummary = $settlementSummaries[$event->id] ?? null;
                          $settlementStatus = $settlementSummary
                              ? ($settlementStatusLabels[$settlementSummary['status']] ?? $settlementStatusLabels['pending'])
                              : $settlementStatusLabels['no_balance'];
                        @endphp
                        <tr>
                          <td class="event-index-col-check">
                            <input type="checkbox" class="bulk-check" data-val="{{ $event->id }}"
                              aria-label="{{ __('Seleccionar evento') }} {{ $event->title }}">
                          </td>
                          <td class="event-index-col-event">
                            <a target="_blank" rel="noopener"
                              href="{{ route('event.details', ['slug' => $event->slug, 'id' => $event->id]) }}"
                              class="event-index-title-link">{{ $event->title }}</a>
                            <span class="event-index-subline event-index-event-meta">
                              {{ __('Organizador') }}:
                              @if ($event->organizer)
                                <a target="_blank" rel="noopener"
                                  href="{{ route('admin.organizer_management.organizer_details', ['id' => $event->organizer_id, 'language' => $defaultLang->code]) }}">
                                  {{ $event->organizer->username }}
                                </a>
                              @else
                                <strong>{{ __('Admin') }}</strong>
                              @endif
                              <span class="event-index-type-pill">
                                @if ($event->event_type === 'venue')
                                  {{ __('Presencial') }}
                                @elseif ($event->event_type === 'online')
                                  {{ __('En línea') }}
                                @else
                                  {{ ucfirst($event->event_type) }}
                                @endif
                              </span>
                              <span class="event-index-category-chip">{{ __('Categoría') }}: {{ $event->category ?: '-' }}</span>
                            </span>
                          </td>
                          <td class="event-index-col-entry">
                            @if ($event->event_type == 'venue')
                              <a href="{{ route('admin.event.ticket', ['language' => request()->input('language'), 'event_id' => $event->id, 'event_type' => $event->event_type]) }}"
                                class="btn btn-success btn-sm event-index-ticket-btn">{{ __('Gestionar') }}</a>
                            @endif
                          </td>
                          <td class="event-index-col-status">
                            <form id="statusForm-{{ $event->id }}" class="event-index-choice-form"
                              action="{{ route('admin.event_management.event.event_status', ['id' => $event->id, 'language' => request()->input('language')]) }}"
                              method="post">

                              @csrf
                              <div class="event-index-choice {{ $event->status == 0 ? 'event-index-choice--warning' : 'event-index-choice--success' }}">
                                <button type="button" class="event-index-choice__button" aria-expanded="false">
                                  <span>{{ $event->status == 1 ? __('Activo') : __('Inactivo') }}</span>
                                  <i class="fas fa-chevron-down" aria-hidden="true"></i>
                                </button>
                                <div class="event-index-choice__menu">
                                  <button type="submit" name="status" value="1"
                                    class="event-index-choice__option {{ $event->status == 1 ? 'is-selected' : '' }}">
                                    {{ __('Activo') }}
                                  </button>
                                  <button type="submit" name="status" value="0"
                                    class="event-index-choice__option {{ $event->status == 0 ? 'is-selected' : '' }}">
                                    {{ __('Inactivo') }}
                                  </button>
                                </div>
                              </div>
                            </form>
                          </td>
                          <td class="event-index-col-featured">

                            <form id="featuredForm-{{ $event->id }}" class="event-index-choice-form"
                              action="{{ route('admin.event_management.event.update_featured', ['id' => $event->id]) }}"
                              method="post">

                              @csrf
                              <div class="event-index-choice {{ $event->is_featured == 'yes' ? 'event-index-choice--success' : 'event-index-choice--muted' }}">
                                <button type="button" class="event-index-choice__button" aria-expanded="false">
                                  <span>{{ $event->is_featured == 'yes' ? __('Sí') : __('No') }}</span>
                                  <i class="fas fa-chevron-down" aria-hidden="true"></i>
                                </button>
                                <div class="event-index-choice__menu">
                                  <button type="submit" name="is_featured" value="yes"
                                    class="event-index-choice__option {{ $event->is_featured == 'yes' ? 'is-selected' : '' }}">
                                    {{ __('Sí') }}
                                  </button>
                                  <button type="submit" name="is_featured" value="no"
                                    class="event-index-choice__option {{ $event->is_featured == 'no' ? 'is-selected' : '' }}">
                                    {{ __('No') }}
                                  </button>
                                </div>
                              </div>
                            </form>
                          </td>
                          <td class="event-index-col-settlement">
                            @if ($event->organizer && $settlementSummary && $settlementSummary['pending_organizer_amount'] > 0)
                              <button type="button"
                                class="btn btn-sm event-index-settlement-btn event-index-settlement-state--{{ $settlementSummary['status'] ?? 'pending' }}"
                                data-toggle="modal" data-target="#eventSettlementModal-{{ $event->id }}">
                                {{ $settlementStatus['label'] }}
                              </button>
                            @elseif ($event->organizer && $settlementSummary)
                              <span class="event-index-settlement-state event-index-settlement-state--{{ $settlementSummary['status'] ?? 'no_balance' }}">
                                {{ $settlementStatus['label'] }}
                              </span>
                            @else
                              <span class="event-index-settlement-state event-index-settlement-state--muted">{{ __('No aplica') }}</span>
                            @endif
                          </td>
                          <td class="event-index-col-amounts">
                            @if ($event->organizer && $settlementSummary)
                              <div class="event-index-money-stack">
                                <div class="event-index-money-row event-index-money-row--pending">
                                  <span>{{ __('Pendiente') }}</span>
                                  <strong>{{ $formatSettlementMoney($settlementSummary['pending_organizer_amount']) }}</strong>
                                </div>
                                <div class="event-index-money-row event-index-money-row--settled">
                                  <span>{{ __('Liquidado') }}</span>
                                  <strong>{{ $formatSettlementMoney($settlementSummary['covered_organizer_amount']) }}</strong>
                                </div>
                              </div>
                            @else
                              <span class="event-index-subline">{{ __('Sin montos') }}</span>
                            @endif
                          </td>
                          <td class="event-index-col-actions">
                            <div class="dropdown">
                              <button class="btn btn-secondary dropdown-toggle btn-sm event-index-actions-btn" type="button"
                                id="dropdownMenuButton-{{ $event->id }}" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                {{ __('Acciones') }}
                              </button>

                              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton-{{ $event->id }}">
                                <a href="{{ route('admin.event_management.edit_event', ['id' => $event->id]) }}"
                                  class="dropdown-item">
                                  {{ __('Editar') }}
                                </a>

                                <a href="{{ route('admin.event_management.ticket_setting', ['id' => $event->id]) }}"
                                  class="dropdown-item">
                                  {{ __('Diseño de entrada') }}
                                </a>

                                <form class="deleteForm d-block"
                                  action="{{ route('admin.event_management.delete_event', ['id' => $event->id]) }}"
                                  method="post">

                                  @csrf
                                  <button type="submit" class="btn btn-sm deleteBtn">
                                    {{ __('Eliminar') }}
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

                <div class="event-index-mobile-list">
                  @foreach ($events as $event)
                    @php
                      $settlementSummary = $settlementSummaries[$event->id] ?? null;
                      $settlementStatus = $settlementSummary
                          ? ($settlementStatusLabels[$settlementSummary['status']] ?? $settlementStatusLabels['pending'])
                          : $settlementStatusLabels['no_balance'];
                    @endphp
                    <article class="event-index-mobile-card">
                      <div class="event-index-mobile-head">
                        <label class="event-index-mobile-check">
                          <input type="checkbox" class="bulk-check" data-val="{{ $event->id }}"
                            aria-label="{{ __('Seleccionar evento') }} {{ $event->title }}">
                        </label>
                        <div class="event-index-mobile-title">
                          <a target="_blank" rel="noopener"
                            href="{{ route('event.details', ['slug' => $event->slug, 'id' => $event->id]) }}"
                            class="event-index-title-link">{{ $event->title }}</a>
                          <span class="event-index-subline">
                            {{ __('Organizador') }}:
                            @if ($event->organizer)
                              <a target="_blank" rel="noopener"
                                href="{{ route('admin.organizer_management.organizer_details', ['id' => $event->organizer_id, 'language' => $defaultLang->code]) }}">
                                {{ $event->organizer->username }}
                              </a>
                            @else
                              <strong>{{ __('Admin') }}</strong>
                            @endif
                          </span>
                        </div>
                      </div>

                      <div class="event-index-mobile-grid">
                        <div>
                          <span class="event-index-mobile-label">{{ __('Tipo') }}</span>
                          <span class="event-index-type-pill">
                            @if ($event->event_type === 'venue')
                              {{ __('Presencial') }}
                            @elseif ($event->event_type === 'online')
                              {{ __('En línea') }}
                            @else
                              {{ ucfirst($event->event_type) }}
                            @endif
                          </span>
                          <span class="event-index-subline">{{ __('Categoría') }}: {{ $event->category ?: '-' }}</span>
                        </div>
                        <div>
                          <span class="event-index-mobile-label">{{ __('Liquidación') }}</span>
                          @if ($event->organizer && $settlementSummary)
                            <span class="badge badge-{{ $settlementStatus['class'] }}">{{ $settlementStatus['label'] }}</span>
                            <span class="event-index-subline">{{ __('Pendiente') }}:
                              {{ $formatSettlementMoney($settlementSummary['pending_organizer_amount']) }}</span>
                            <span class="event-index-subline">{{ __('Liquidado') }}:
                              {{ $formatSettlementMoney($settlementSummary['covered_organizer_amount']) }}</span>
                          @else
                            <span class="badge badge-secondary">{{ __('No aplica') }}</span>
                          @endif
                        </div>
                      </div>

                      <div class="event-index-mobile-controls">
                        @if ($event->event_type == 'venue')
                          <a href="{{ route('admin.event.ticket', ['language' => request()->input('language'), 'event_id' => $event->id, 'event_type' => $event->event_type]) }}"
                            class="btn btn-outline-success btn-sm">
                            <i class="fas fa-ticket-alt mr-1" aria-hidden="true"></i>{{ __('Entradas') }}
                          </a>
                        @endif
                        <form id="statusFormMobile-{{ $event->id }}" class="event-index-mobile-form event-index-choice-form"
                          action="{{ route('admin.event_management.event.event_status', ['id' => $event->id, 'language' => request()->input('language')]) }}"
                          method="post">
                          @csrf
                          <div class="event-index-choice {{ $event->status == 0 ? 'event-index-choice--warning' : 'event-index-choice--success' }}">
                            <button type="button" class="event-index-choice__button" aria-expanded="false">
                              <span>{{ $event->status == 1 ? __('Activo') : __('Inactivo') }}</span>
                              <i class="fas fa-chevron-down" aria-hidden="true"></i>
                            </button>
                            <div class="event-index-choice__menu">
                              <button type="submit" name="status" value="1"
                                class="event-index-choice__option {{ $event->status == 1 ? 'is-selected' : '' }}">
                                {{ __('Activo') }}
                              </button>
                              <button type="submit" name="status" value="0"
                                class="event-index-choice__option {{ $event->status == 0 ? 'is-selected' : '' }}">
                                {{ __('Inactivo') }}
                              </button>
                            </div>
                          </div>
                        </form>
                        <form id="featuredFormMobile-{{ $event->id }}" class="event-index-mobile-form event-index-choice-form"
                          action="{{ route('admin.event_management.event.update_featured', ['id' => $event->id]) }}"
                          method="post">
                          @csrf
                          <div class="event-index-choice {{ $event->is_featured == 'yes' ? 'event-index-choice--success' : 'event-index-choice--muted' }}">
                            <button type="button" class="event-index-choice__button" aria-expanded="false">
                              <span>{{ $event->is_featured == 'yes' ? __('Destacado') : __('No destacado') }}</span>
                              <i class="fas fa-chevron-down" aria-hidden="true"></i>
                            </button>
                            <div class="event-index-choice__menu">
                              <button type="submit" name="is_featured" value="yes"
                                class="event-index-choice__option {{ $event->is_featured == 'yes' ? 'is-selected' : '' }}">
                                {{ __('Destacado') }}
                              </button>
                              <button type="submit" name="is_featured" value="no"
                                class="event-index-choice__option {{ $event->is_featured == 'no' ? 'is-selected' : '' }}">
                                {{ __('No destacado') }}
                              </button>
                            </div>
                          </div>
                        </form>
                        <div class="dropdown">
                          <button class="btn btn-secondary dropdown-toggle btn-sm event-index-actions-btn" type="button"
                            id="dropdownMobileMenuButton-{{ $event->id }}" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                            {{ __('Acciones') }}
                          </button>
                          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMobileMenuButton-{{ $event->id }}">
                            <a href="{{ route('admin.event_management.edit_event', ['id' => $event->id]) }}"
                              class="dropdown-item">{{ __('Editar') }}</a>
                            <a href="{{ route('admin.event_management.ticket_setting', ['id' => $event->id]) }}"
                              class="dropdown-item">{{ __('Diseño de entrada') }}</a>
                            @if ($event->organizer && $settlementSummary && $settlementSummary['pending_organizer_amount'] > 0)
                              <button type="button" class="dropdown-item" data-toggle="modal"
                                data-target="#eventSettlementModal-{{ $event->id }}">
                                {{ __('Marcar como liquidado') }}
                              </button>
                            @endif
                            <form class="deleteForm d-block"
                              action="{{ route('admin.event_management.delete_event', ['id' => $event->id]) }}" method="post">
                              @csrf
                              <button type="submit" class="btn btn-sm deleteBtn">{{ __('Eliminar') }}</button>
                            </form>
                          </div>
                        </div>
                      </div>
                    </article>
                  @endforeach
                </div>

                @foreach ($events as $event)
                  @php
                    $settlementSummary = $settlementSummaries[$event->id] ?? null;
                  @endphp
                  @if ($event->organizer && $settlementSummary && $settlementSummary['pending_organizer_amount'] > 0)
                    <div class="modal fade" id="eventSettlementModal-{{ $event->id }}" tabindex="-1" role="dialog"
                      aria-labelledby="eventSettlementModalLabel-{{ $event->id }}" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <form action="{{ route('admin.event_management.event.settlement.store', ['id' => $event->id]) }}" method="post">
                            @csrf
                            <div class="modal-header">
                              <h5 class="modal-title" id="eventSettlementModalLabel-{{ $event->id }}">
                                {{ __('Liquidar evento') }}
                              </h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Cerrar') }}">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <div class="modal-body">
                              <p class="mb-3">
                                <strong>{{ $event->title }}</strong><br>
                                <span class="text-muted">{{ __('Organizador') }}: {{ optional($event->organizer)->username }}</span>
                              </p>

                              <div class="row">
                                <div class="col-md-4">
                                  <div class="border rounded p-2 mb-3">
                                    <span class="small text-muted d-block">{{ __('Total cobrado') }}</span>
                                    <strong>{{ $formatSettlementMoney($settlementSummary['charged_amount']) }}</strong>
                                  </div>
                                </div>
                                <div class="col-md-4">
                                  <div class="border rounded p-2 mb-3">
                                    <span class="small text-muted d-block">{{ __('Neto organizador') }}</span>
                                    <strong>{{ $formatSettlementMoney($settlementSummary['organizer_net_amount']) }}</strong>
                                  </div>
                                </div>
                                <div class="col-md-4">
                                  <div class="border rounded p-2 mb-3">
                                    <span class="small text-muted d-block">{{ __('Pendiente') }}</span>
                                    <strong>{{ $formatSettlementMoney($settlementSummary['pending_organizer_amount']) }}</strong>
                                  </div>
                                </div>
                              </div>

                              <div class="form-group">
                                <label>{{ __('Monto a registrar') }}</label>
                                <select class="form-control" name="amount_option" required>
                                  <option value="organizer_net">
                                    {{ __('Monto completo') }} - {{ __('neto organizador') }} ({{ $formatSettlementMoney($settlementSummary['pending_organizer_amount']) }})
                                  </option>
                                  <option value="charged_total">
                                    {{ __('Total cobrado') }} ({{ $formatSettlementMoney($settlementSummary['charged_amount']) }})
                                  </option>
                                  <option value="custom">{{ __('Monto personalizado') }}</option>
                                </select>
                              </div>

                              <div class="form-group">
                                <label>{{ __('Monto personalizado') }}</label>
                                <input type="number" name="custom_amount" class="form-control" min="0.01" step="0.01"
                                  placeholder="{{ __('Usar solo si elegís monto personalizado') }}">
                              </div>

                              <div class="form-group">
                                <label>{{ __('Fecha de pago') }}</label>
                                <input type="date" name="paid_at" class="form-control" value="{{ now()->format('Y-m-d') }}">
                              </div>

                              <div class="form-group">
                                <label>{{ __('Referencia / comprobante') }}</label>
                                <input type="text" name="reference" class="form-control" maxlength="160"
                                  placeholder="{{ __('Transferencia, comprobante o referencia interna') }}">
                              </div>

                              <div class="form-group mb-0">
                                <label>{{ __('Nota interna') }}</label>
                                <textarea name="note" class="form-control" rows="3" maxlength="1000"></textarea>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('Cancelar') }}</button>
                              <button type="submit" class="btn btn-primary">{{ __('Guardar liquidación') }}</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  @endif
                @endforeach
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
      overflow-x: visible;
      overflow-y: visible;
      background: #fff;
    }

    .event-index-table {
      margin-top: 0 !important;
      margin-bottom: 0;
      table-layout: fixed;
      width: 100%;
    }

    .event-index-table thead th {
      border-top: 0;
      border-bottom: 1px solid #e5e7eb;
      background: #f8fafc;
      color: #475569;
      font-size: 12px;
      font-weight: 700;
      letter-spacing: .04em;
      padding: 14px 8px;
      text-transform: uppercase;
      vertical-align: middle;
    }

    .event-index-table tbody td {
      vertical-align: middle;
      border-top: 1px solid #eef2f7;
      padding: 12px 8px;
      overflow-wrap: normal;
    }

    .event-index-table .event-index-col-check,
    .event-index-col-check {
      width: 34px;
      max-width: 34px;
      padding-left: 6px !important;
      padding-right: 6px !important;
      text-align: center;
      overflow-wrap: normal;
    }

    .event-index-col-event {
      width: 30%;
    }

    .event-index-col-entry {
      width: 10%;
    }

    .event-index-col-status {
      width: 10%;
    }

    .event-index-col-featured {
      width: 9%;
    }

    .event-index-col-settlement {
      width: 12%;
    }

    .event-index-col-amounts {
      width: 15%;
    }

    .event-index-col-actions {
      width: 11%;
      min-width: 112px;
      text-align: right;
    }

    .event-index-title-link {
      display: block;
      color: #0f172a;
      font-size: 13px;
      font-weight: 700;
      line-height: 1.35;
      text-decoration: none;
      overflow-wrap: anywhere;
    }

    .event-index-title-link:hover {
      color: #2563eb;
      text-decoration: none;
    }

    .event-index-subline {
      display: block;
      margin-top: 4px;
      color: #64748b;
      font-size: 11px;
      line-height: 1.35;
    }

    .event-index-subline a {
      color: #2563eb;
      font-weight: 600;
    }

    .event-index-event-meta {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 6px;
    }

    .event-index-type-pill {
      display: inline-flex;
      align-items: center;
      min-height: 24px;
      padding: 3px 8px;
      border-radius: 999px;
      background: #fff7ed;
      color: #9a3412;
      font-size: 11px;
      font-weight: 800;
      white-space: nowrap;
    }

    .event-index-category-chip {
      display: inline-flex;
      align-items: center;
      min-height: 24px;
      padding: 3px 8px;
      border-radius: 999px;
      background: #f1f5f9;
      color: #475569;
      font-size: 11px;
      font-weight: 700;
      white-space: nowrap;
    }

    .event-index-pill-select {
      width: 100%;
      min-width: 88px;
      max-width: 112px;
      border-radius: 10px;
      border: 0;
      color: #fff;
      font-weight: 600;
    }

    .event-index-choice-form {
      position: relative;
      display: inline-block;
      width: 100%;
      min-width: 108px;
      max-width: 136px;
      margin: 0;
    }

    .event-index-choice {
      --choice-bg: #fff;
      --choice-border: #d0d5dd;
      --choice-color: #1e2532;
      --choice-hover: #f8fafc;
      position: relative;
      width: 100%;
    }

    .event-index-choice--success {
      --choice-bg: #ecfdf5;
      --choice-border: #bbf7d0;
      --choice-color: #047857;
      --choice-hover: #dcfce7;
    }

    .event-index-choice--warning {
      --choice-bg: #fffbeb;
      --choice-border: #fde68a;
      --choice-color: #92400e;
      --choice-hover: #fef3c7;
    }

    .event-index-choice--muted {
      --choice-bg: #f8fafc;
      --choice-border: #d0d5dd;
      --choice-color: #475467;
      --choice-hover: #eef2f7;
    }

    .event-index-choice__button {
      display: inline-flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      width: 100%;
      min-height: 40px;
      padding: 0 13px;
      border: 1px solid var(--choice-border);
      border-radius: 10px;
      background: var(--choice-bg);
      color: var(--choice-color);
      font-size: 12px;
      font-weight: 750;
      line-height: 1;
      box-shadow: none;
      cursor: pointer;
    }

    .event-index-choice__button:focus,
    .event-index-choice__button:hover {
      border-color: var(--choice-border);
      background: var(--choice-hover);
      color: var(--choice-color);
      outline: 0;
      box-shadow: 0 0 0 3px rgba(249, 115, 22, .10);
    }

    .event-index-choice__button .fas {
      color: currentColor;
      font-size: 10px;
      transition: transform .16s ease;
    }

    .event-index-choice.is-open .event-index-choice__button .fas {
      transform: rotate(180deg);
    }

    .event-index-choice__menu {
      position: absolute;
      z-index: 1055;
      top: calc(100% + 6px);
      left: 0;
      display: none;
      width: max-content;
      min-width: 100%;
      padding: 6px;
      border: 1px solid #e4e7ec;
      border-radius: 12px;
      background: #fff;
      box-shadow: 0 18px 36px rgba(16, 24, 40, .16);
    }

    .event-index-choice.is-open .event-index-choice__menu {
      display: grid;
      gap: 3px;
    }

    .event-index-choice__option {
      width: 100%;
      min-width: 116px;
      padding: 9px 11px;
      border: 0;
      border-radius: 8px;
      background: transparent;
      color: #344054;
      font-size: 12px;
      font-weight: 650;
      line-height: 1.1;
      text-align: left;
      white-space: nowrap;
      cursor: pointer;
    }

    .event-index-choice__option:hover,
    .event-index-choice__option:focus,
    .event-index-choice__option.is-selected {
      background: var(--choice-hover);
      color: var(--choice-color);
      outline: 0;
    }

    .event-index-actions-btn {
      width: 100%;
      min-width: 96px;
      max-width: 116px;
      border-radius: 10px;
      padding-inline: 10px;
      white-space: nowrap;
    }

    .event-index-ticket-btn {
      width: 100%;
      min-width: 86px;
      max-width: 112px;
      border-radius: 10px;
      font-weight: 700;
      overflow-wrap: normal;
      white-space: nowrap;
    }

    .event-index-settlement-btn,
    .event-index-settlement-state {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      min-width: 88px;
      max-width: 118px;
      min-height: 30px;
      padding: 5px 10px;
      border: 0;
      border-radius: 999px;
      background: #ecfdf5;
      color: #047857;
      font-size: 11px;
      font-weight: 800;
    }

    .event-index-settlement-btn:hover,
    .event-index-settlement-btn:focus {
      color: inherit;
      filter: brightness(.97);
      box-shadow: 0 0 0 2px rgba(37, 99, 235, .12);
    }

    .event-index-settlement-state--pending {
      background: #fff7ed;
      color: #9a3412;
    }

    .event-index-settlement-state--partial {
      background: #eff6ff;
      color: #1d4ed8;
    }

    .event-index-settlement-state--settled {
      background: #ecfdf5;
      color: #047857;
    }

    .event-index-settlement-state--no_balance,
    .event-index-settlement-state--muted {
      background: #f1f5f9;
      color: #64748b;
    }

    .event-index-money-stack {
      display: grid;
      gap: 4px;
      min-width: 128px;
    }

    .event-index-money-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
      padding: 0;
      color: #64748b;
      font-size: 11px;
      line-height: 1.2;
    }

    .event-index-money-row--pending strong {
      color: #9a3412;
    }

    .event-index-money-row--settled strong {
      color: #047857;
    }

    .event-index-money-row strong {
      font-size: 12px;
      font-weight: 800;
      white-space: nowrap;
    }

    .event-index-mobile-list {
      display: grid;
      gap: 12px;
    }

    .event-index-mobile-card {
      border: 1px solid #e5e7eb;
      border-radius: 14px;
      background: #fff;
      padding: 14px;
      box-shadow: 0 10px 24px rgba(15, 23, 42, .06);
    }

    .event-index-mobile-head {
      display: grid;
      grid-template-columns: 34px minmax(0, 1fr);
      gap: 10px;
      align-items: start;
    }

    .event-index-mobile-check {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 32px;
      min-height: 44px;
      margin: 0;
    }

    .event-index-mobile-title {
      min-width: 0;
    }

    .event-index-mobile-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 12px;
      margin-top: 14px;
      padding-top: 12px;
      border-top: 1px solid #eef2f7;
    }

    .event-index-mobile-label {
      display: block;
      margin-bottom: 6px;
      color: #475569;
      font-size: 11px;
      font-weight: 800;
      letter-spacing: .04em;
      text-transform: uppercase;
    }

    .event-index-mobile-controls {
      display: grid;
      grid-template-columns: minmax(160px, 1.35fr) minmax(112px, .85fr) minmax(126px, .95fr);
      align-items: start;
      gap: 8px;
      margin-top: 14px;
    }

    .event-index-mobile-controls > .btn,
    .event-index-mobile-controls > .dropdown,
    .event-index-mobile-form {
      width: 100%;
      min-width: 0;
    }

    .event-index-mobile-controls .btn,
    .event-index-mobile-controls .dropdown-toggle,
    .event-index-mobile-form .form-control,
    .event-index-mobile-form .event-index-choice-form,
    .event-index-mobile-form .event-index-choice__button {
      width: 100%;
      min-height: 40px;
      max-width: none;
    }

    .admin-event-index {
      --event-ink: #1e2532;
      --event-ink-strong: #111827;
      --event-muted: #667085;
      --event-border: #e4e7ec;
      --event-soft: #f8fafc;
      --event-orange: #f97316;
      --event-orange-dark: #c2410c;
      --event-orange-soft: #fff7ed;
      --event-green: #16a34a;
      --event-green-soft: #ecfdf5;
      --event-red: #dc2626;
      --event-red-soft: #fef2f2;
      --event-radius: 8px;
      color: var(--event-ink);
      font-size: 13px;
      line-height: 1.45;
    }

    .admin-event-index .page-header {
      margin-bottom: 22px;
    }

    .admin-event-index .page-title {
      color: var(--event-ink-strong) !important;
      font-size: 24px !important;
      font-weight: 750 !important;
      line-height: 1.2;
    }

    .admin-event-index .breadcrumbs,
    .admin-event-index .breadcrumbs a {
      color: #667085 !important;
      font-size: 12.5px;
      font-weight: 500;
    }

    .admin-event-index > .row > [class*="col-"] > .card {
      border-color: var(--event-border) !important;
      border-radius: var(--event-radius) !important;
      background: #fff !important;
      box-shadow: 0 1px 2px rgba(16, 24, 40, .04) !important;
      overflow: visible;
    }

    .admin-event-index .card-header,
    .admin-event-index .card-footer {
      border-color: var(--event-border) !important;
      background: #fbfcfd !important;
    }

    .admin-event-index .card-header {
      padding: 22px 24px;
    }

    .admin-event-index .card-body {
      padding: 22px 24px;
    }

    .admin-event-index .card-footer {
      padding: 14px 24px;
    }

    .admin-event-index .event-index-header {
      align-items: center;
      gap: 16px;
    }

    .admin-event-index .event-index-header__eyebrow {
      margin-bottom: 8px;
      padding: 5px 9px;
      background: var(--event-orange-soft);
      color: var(--event-orange-dark);
      font-size: 10.5px;
      font-weight: 700;
      letter-spacing: .05em;
    }

    .admin-event-index .event-index-header__title {
      margin-bottom: 5px;
      color: var(--event-ink-strong);
      font-size: 26px;
      font-weight: 750;
      line-height: 1.18;
    }

    .admin-event-index .event-index-header__text {
      max-width: 660px;
      color: var(--event-muted);
      font-size: 13.5px;
      line-height: 1.55;
    }

    .admin-event-index .event-index-toolbar__group {
      gap: 10px;
    }

    .admin-event-index .event-index-select,
    .admin-event-index .event-index-search__field .form-control {
      min-height: 40px;
      border-color: #d0d5dd;
      border-radius: var(--event-radius);
      color: var(--event-ink);
      font-size: 13px;
      font-weight: 500;
      box-shadow: none;
    }

    .admin-event-index .event-index-select:focus,
    .admin-event-index .event-index-search__field .form-control:focus {
      border-color: var(--event-orange);
      box-shadow: 0 0 0 3px rgba(249, 115, 22, .12);
    }

    .admin-event-index .event-index-add-btn,
    .admin-event-index .event-index-search__btn,
    .admin-event-index .event-index-bulk-delete {
      min-height: 40px;
      border-radius: var(--event-radius);
      font-size: 13px;
      font-weight: 650;
      box-shadow: none !important;
    }

    .admin-event-index .event-index-add-btn,
    .admin-event-index .event-index-search__btn {
      border-color: var(--event-orange-dark) !important;
      background: var(--event-orange-dark) !important;
    }

    .admin-event-index .event-index-filters {
      margin-bottom: 20px;
      padding: 16px;
      border-color: var(--event-border);
      border-radius: var(--event-radius);
      background: #fff;
    }

    .admin-event-index .event-index-stats {
      gap: 10px;
    }

    .admin-event-index .event-index-stat {
      min-width: 128px;
      padding: 11px 13px;
      border-color: #dce5f0;
      border-radius: var(--event-radius);
      background: #fff;
    }

    .admin-event-index .event-index-stat__label {
      color: #667085;
      font-size: 11.5px;
      font-weight: 500;
    }

    .admin-event-index .event-index-stat__value {
      color: var(--event-ink-strong);
      font-size: 16px;
      font-weight: 750;
      line-height: 1.2;
    }

    .admin-event-index .event-index-table-wrap {
      border-color: var(--event-border);
      border-radius: var(--event-radius);
      display: none;
      box-shadow: none;
    }

    .admin-event-index .event-index-mobile-list {
      display: grid;
    }

    .admin-event-index .event-index-table {
      table-layout: fixed;
    }

    .admin-event-index .event-index-table thead th {
      background: #edf4f9;
      color: #344054;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: .045em;
      line-height: 1.25;
      padding: 13px 10px;
      overflow-wrap: normal;
      white-space: nowrap;
    }

    .admin-event-index .event-index-table tbody td {
      color: var(--event-ink);
      font-size: 13px;
      font-weight: 400;
      line-height: 1.45;
      padding: 14px 10px;
      overflow: visible;
      overflow-wrap: normal;
    }

    .admin-event-index .event-index-table .event-index-col-event {
      width: 31%;
    }

    .admin-event-index .event-index-table .event-index-col-entry {
      width: 9.5%;
    }

    .admin-event-index .event-index-table .event-index-col-status {
      width: 9.5%;
    }

    .admin-event-index .event-index-table .event-index-col-featured {
      width: 8.5%;
    }

    .admin-event-index .event-index-table .event-index-col-settlement {
      width: 10.5%;
    }

    .admin-event-index .event-index-table .event-index-col-amounts {
      width: 15%;
    }

    .admin-event-index .event-index-table .event-index-col-actions {
      width: 10%;
    }

    .admin-event-index .event-index-table .event-index-col-status .event-index-choice-form,
    .admin-event-index .event-index-table .event-index-col-featured .event-index-choice-form {
      min-width: 0;
      max-width: none;
    }

    .admin-event-index .event-index-table .event-index-choice__button,
    .admin-event-index .event-index-table .event-index-ticket-btn,
    .admin-event-index .event-index-table .event-index-actions-btn,
    .admin-event-index .event-index-table .event-index-settlement-btn,
    .admin-event-index .event-index-table .event-index-settlement-state {
      overflow-wrap: normal;
      white-space: nowrap;
    }

    .admin-event-index .event-index-table .event-index-choice__button {
      gap: 6px;
      min-height: 36px;
      padding-inline: 10px;
    }

    .admin-event-index .event-index-table .event-index-choice__button span {
      min-width: 0;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .admin-event-index .event-index-table .event-index-actions-btn {
      min-width: 86px;
    }

    .admin-event-index .event-index-table .event-index-col-entry,
    .admin-event-index .event-index-table .event-index-col-status,
    .admin-event-index .event-index-table .event-index-col-featured,
    .admin-event-index .event-index-table .event-index-col-settlement,
    .admin-event-index .event-index-table .event-index-col-amounts,
    .admin-event-index .event-index-table .event-index-col-actions {
      padding-left: 5px;
      padding-right: 5px;
    }

    .admin-event-index .event-index-table .event-index-ticket-btn {
      min-width: 0;
      max-width: none;
      padding-inline: 6px;
    }

    .admin-event-index .event-index-table .event-index-settlement-btn,
    .admin-event-index .event-index-table .event-index-settlement-state,
    .admin-event-index .event-index-table .event-index-actions-btn,
    .admin-event-index .event-index-table .event-index-money-stack {
      min-width: 0;
      max-width: none;
      width: 100%;
    }

    .admin-event-index .event-index-table .event-index-settlement-btn,
    .admin-event-index .event-index-table .event-index-settlement-state,
    .admin-event-index .event-index-table .event-index-actions-btn {
      padding-left: 6px;
      padding-right: 6px;
    }

    .admin-event-index .event-index-title-link {
      color: var(--event-ink-strong);
      font-size: 13px;
      font-weight: 650;
      line-height: 1.35;
    }

    .admin-event-index .event-index-title-link:hover,
    .admin-event-index .event-index-subline a:hover {
      color: var(--event-orange-dark);
    }

    .admin-event-index .event-index-subline,
    .admin-event-index .event-index-subline a {
      color: #667085;
      font-size: 11.5px;
      font-weight: 500;
    }

    .admin-event-index .event-index-type-pill,
    .admin-event-index .event-index-category-chip {
      min-height: 22px;
      padding: 3px 8px;
      border: 1px solid transparent;
      font-size: 10.5px;
      font-weight: 700;
      letter-spacing: 0;
    }

    .admin-event-index .event-index-type-pill {
      border-color: #fed7aa;
      background: var(--event-orange-soft);
      color: var(--event-orange-dark);
    }

    .admin-event-index .event-index-category-chip {
      border-color: #e4e7ec;
      background: #f8fafc;
      color: #475467;
    }

    .admin-event-index .event-index-ticket-btn {
      min-height: 34px;
      border: 1px solid #bbf7d0 !important;
      border-radius: var(--event-radius);
      background: var(--event-green-soft) !important;
      color: #047857 !important;
      font-size: 12px;
      font-weight: 700;
      box-shadow: none !important;
    }

    .admin-event-index .event-index-pill-select {
      min-height: 34px;
      border: 1px solid #d0d5dd !important;
      border-radius: var(--event-radius);
      background-color: #fff !important;
      color: var(--event-ink) !important;
      font-size: 12px;
      font-weight: 650;
      line-height: 1.2;
      box-shadow: none !important;
    }

    .admin-event-index .event-index-pill-select.bg-success {
      border-color: #bbf7d0 !important;
      background-color: var(--event-green-soft) !important;
      color: #047857 !important;
    }

    .admin-event-index .event-index-pill-select.bg-warning {
      border-color: #fde68a !important;
      background-color: #fffbeb !important;
      color: #92400e !important;
    }

    .admin-event-index .event-index-pill-select.bg-danger {
      border-color: #fecdd3 !important;
      background-color: var(--event-red-soft) !important;
      color: #be123c !important;
    }

    .admin-event-index .event-index-choice-form {
      min-width: 108px;
      max-width: 136px;
    }

    .admin-event-index .event-index-mobile-form.event-index-choice-form {
      max-width: none;
    }

    .admin-event-index .event-index-settlement-btn,
    .admin-event-index .event-index-settlement-state {
      min-height: 30px;
      border: 1px solid transparent;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 700;
    }

    .admin-event-index .event-index-settlement-state--pending {
      border-color: #fed7aa;
    }

    .admin-event-index .event-index-settlement-state--settled {
      border-color: #bbf7d0;
    }

    .admin-event-index .event-index-actions-btn {
      min-height: 34px;
      border: 1px solid #d0d5dd !important;
      border-radius: var(--event-radius);
      background: #fff !important;
      color: var(--event-ink) !important;
      font-size: 12px;
      font-weight: 650;
      box-shadow: none !important;
    }

    .admin-event-index .dropdown-menu {
      border-color: var(--event-border) !important;
      border-radius: var(--event-radius) !important;
      box-shadow: 0 16px 32px rgba(16, 24, 40, .12) !important;
      overflow: hidden;
    }

    .admin-event-index .dropdown-item,
    .admin-event-index .deleteBtn {
      width: 100%;
      padding: 9px 14px;
      border: 0;
      background: #fff;
      color: var(--event-ink);
      font-size: 13px;
      font-weight: 500;
      text-align: left;
    }

    .admin-event-index .dropdown-item:hover,
    .admin-event-index .deleteBtn:hover {
      background: var(--event-orange-soft);
      color: var(--event-orange-dark);
    }

    .admin-event-index .event-index-money-row {
      color: #667085;
      font-size: 11.5px;
      font-weight: 500;
    }

    .admin-event-index .event-index-money-row strong {
      font-size: 12px;
      font-weight: 750;
    }

    .admin-event-index .event-index-mobile-card {
      border-color: var(--event-border);
      border-radius: var(--event-radius);
      box-shadow: 0 1px 2px rgba(16, 24, 40, .04);
    }

    .admin-event-index .event-index-mobile-label {
      color: #667085;
      font-size: 10.5px;
      font-weight: 700;
    }

    @media (min-width: 1400px) {
      .admin-event-index .event-index-table-wrap {
        display: block;
      }

      .admin-event-index .event-index-mobile-list {
        display: none;
      }
    }

    @media (min-width: 768px) and (max-width: 1399.98px) {
      .admin-event-index .event-index-mobile-list {
        gap: 14px;
      }

      .admin-event-index .event-index-mobile-card {
        padding: 16px;
      }

      .admin-event-index .event-index-mobile-grid {
        grid-template-columns: minmax(0, 1fr) minmax(240px, .72fr);
        gap: 16px;
      }

      .admin-event-index .event-index-mobile-controls {
        grid-template-columns: minmax(160px, 1fr) minmax(132px, .7fr) minmax(132px, .7fr) minmax(132px, .72fr);
        gap: 10px;
      }

      .admin-event-index .event-index-mobile-controls .btn,
      .admin-event-index .event-index-mobile-controls .dropdown-toggle,
      .admin-event-index .event-index-mobile-form .event-index-choice__button {
        min-height: 42px;
      }
    }

    @media (min-width: 992px) and (max-width: 1399.98px) {
      .admin-event-index .event-index-mobile-card {
        padding: 18px 20px;
      }

      .admin-event-index .event-index-mobile-head {
        grid-template-columns: 34px minmax(0, 1fr);
      }
    }

    @media (max-width: 991px) {
      .event-index-search {
        width: 100%;
      }

      .event-index-search__field {
        min-width: 100%;
      }
    }

    @media (max-width: 575px) {
      .event-index-mobile-grid {
        grid-template-columns: 1fr;
      }

      .event-index-mobile-card {
        padding: 12px;
      }

      .event-index-mobile-controls {
        grid-template-columns: 1fr;
      }
    }
  </style>
@endsection

@section('script')
  <script>
    (function ($) {
      'use strict';

      function closeEventChoices() {
        $('.event-index-choice.is-open')
          .removeClass('is-open')
          .find('.event-index-choice__button')
          .attr('aria-expanded', 'false');
      }

      $(document).on('click', '.event-index-choice__button', function (event) {
        event.preventDefault();
        event.stopPropagation();

        var $choice = $(this).closest('.event-index-choice');
        var wasOpen = $choice.hasClass('is-open');

        closeEventChoices();

        if (!wasOpen) {
          $choice.addClass('is-open');
          $(this).attr('aria-expanded', 'true');
        }
      });

      $(document).on('click', '.event-index-choice__menu', function (event) {
        event.stopPropagation();
      });

      $(document).on('click', closeEventChoices);

      $(document).on('keydown', function (event) {
        if (event.key === 'Escape') {
          closeEventChoices();
        }
      });
    })(jQuery);
  </script>
@endsection
