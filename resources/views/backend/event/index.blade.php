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

  <div class="page-header">
    <h4 class="page-title">{{ __('Events') }}</h4>
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
      @if (!request()->filled('event_type'))
        <li class="separator">
          <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
          <a
            href="{{ route('admin.event_management.event', ['language' => $defaultLang->code]) }}">{{ __('All Events') }}</a>
        </li>
      @endif
      @if (request()->filled('event_type') && request()->input('event_type') == 'venue')
        <li class="separator">
          <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
          <a href="#">{{ __('Venue Events') }}</a>
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
                    <a href="{{ route('add.event.event', ['type' => 'online']) }}" class="dropdown-item">
                      {{ __('Evento online') }}
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
                  <p class="mb-0">{{ __('Prueba con otro idioma, otro filtro o crea un evento nuevo.') }}</p>
                </div>
              @else
                <div class="table-responsive event-index-table-wrap d-none d-lg-block">
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
                          <td>
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
                                  {{ __('Online') }}
                                @else
                                  {{ ucfirst($event->event_type) }}
                                @endif
                              </span>
                              <span class="event-index-category-chip">{{ __('Categoría') }}: {{ $event->category ?: '-' }}</span>
                            </span>
                          </td>
                          <td>
                            @if ($event->event_type == 'venue')
                              <a href="{{ route('admin.event.ticket', ['language' => request()->input('language'), 'event_id' => $event->id, 'event_type' => $event->event_type]) }}"
                                class="btn btn-success btn-sm event-index-ticket-btn">{{ __('Gestionar') }}</a>
                            @endif
                          </td>
                          <td>
                            <form id="statusForm-{{ $event->id }}" class="d-inline-block"
                              action="{{ route('admin.event_management.event.event_status', ['id' => $event->id, 'language' => request()->input('language')]) }}"
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
                              action="{{ route('admin.event_management.event.update_featured', ['id' => $event->id]) }}"
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
                          <td>
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
                          <td>
                            <div class="dropdown">
                              <button class="btn btn-secondary dropdown-toggle btn-sm event-index-actions-btn" type="button"
                                id="dropdownMenuButton-{{ $event->id }}" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                {{ __('Acciones') }}
                              </button>

                              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton-{{ $event->id }}">
                                <a href="{{ route('admin.event_management.edit_event', ['id' => $event->id]) }}"
                                  class="dropdown-item">
                                  {{ __('Edit') }}
                                </a>

                                <a href="{{ route('admin.event_management.ticket_setting', ['id' => $event->id]) }}"
                                  class="dropdown-item">
                                  {{ __('Ticket Settings') }}
                                </a>

                                <form class="deleteForm d-block"
                                  action="{{ route('admin.event_management.delete_event', ['id' => $event->id]) }}"
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

                <div class="event-index-mobile-list d-lg-none">
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
                              {{ __('Online') }}
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
                        <form id="statusFormMobile-{{ $event->id }}" class="event-index-mobile-form"
                          action="{{ route('admin.event_management.event.event_status', ['id' => $event->id, 'language' => request()->input('language')]) }}"
                          method="post">
                          @csrf
                          <select
                            class="form-control form-control-sm event-index-pill-select {{ $event->status == 0 ? 'bg-warning text-dark' : 'bg-primary' }}"
                            name="status" onchange="document.getElementById('statusFormMobile-{{ $event->id }}').submit()">
                            <option value="1" {{ $event->status == 1 ? 'selected' : '' }}>{{ __('Activo') }}</option>
                            <option value="0" {{ $event->status == 0 ? 'selected' : '' }}>{{ __('Inactivo') }}</option>
                          </select>
                        </form>
                        <form id="featuredFormMobile-{{ $event->id }}" class="event-index-mobile-form"
                          action="{{ route('admin.event_management.event.update_featured', ['id' => $event->id]) }}"
                          method="post">
                          @csrf
                          <select
                            class="form-control form-control-sm event-index-pill-select {{ $event->is_featured == 'yes' ? 'bg-success' : 'bg-danger' }}"
                            name="is_featured" onchange="document.getElementById('featuredFormMobile-{{ $event->id }}').submit()">
                            <option value="yes" {{ $event->is_featured == 'yes' ? 'selected' : '' }}>{{ __('Destacado') }}</option>
                            <option value="no" {{ $event->is_featured == 'no' ? 'selected' : '' }}>{{ __('No destacado') }}</option>
                          </select>
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
      overflow-wrap: anywhere;
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
      width: 34%;
    }

    .event-index-col-entry {
      width: 9%;
    }

    .event-index-col-status,
    .event-index-col-featured {
      width: 9%;
    }

    .event-index-col-settlement {
      width: 10%;
    }

    .event-index-col-amounts {
      width: 14%;
    }

    .event-index-col-actions {
      width: 10%;
    }

    .event-index-title-link {
      display: block;
      color: #0f172a;
      font-size: 13px;
      font-weight: 700;
      line-height: 1.35;
      text-decoration: none;
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

    .event-index-ticket-btn {
      min-width: 86px;
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
      min-width: 88px;
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
      min-width: 118px;
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
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-top: 14px;
    }

    .event-index-mobile-controls > .btn,
    .event-index-mobile-controls > .dropdown,
    .event-index-mobile-form {
      flex: 1 1 132px;
      min-width: 0;
    }

    .event-index-mobile-controls .btn,
    .event-index-mobile-controls .dropdown-toggle,
    .event-index-mobile-form .form-control {
      width: 100%;
      min-height: 40px;
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

      .event-index-mobile-controls > .btn,
      .event-index-mobile-controls > .dropdown,
      .event-index-mobile-form {
        flex-basis: 100%;
      }
    }
  </style>
@endsection
