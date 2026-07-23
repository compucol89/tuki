@extends('backend.layout')

@php
  $tickets = collect($information['tickets'] ?? []);
  $ticketIds = $tickets->pluck('id');
  $ticketContentsByTicket = App\Models\Event\TicketContent::whereIn('ticket_id', $ticketIds)->get()->groupBy('ticket_id');
  $languageId = $information['language']['id'] ?? null;
  $eventTitle = $information['event']['title'] ?? __('Evento');
  $eventModel = $information['eventModel'] ?? null;
  $freeLimitEnabled = (bool) optional($eventModel)->limit_free_tickets_per_person;
  $freeLimitValue = max((int) (optional($eventModel)->free_tickets_per_person_limit ?: 2), 1);
  $activeLanguageName = $information['language']['name'] ?? strtoupper(request()->input('language'));

  $formatCount = function ($value) {
      return number_format((int) $value, 0, ',', '.');
  };

  $decodeVariations = function ($ticket) {
      $decoded = json_decode($ticket->variations ?? '[]', true);
      return is_array($decoded) ? collect($decoded) : collect();
  };

  $discountIsActive = function ($ticket) {
      if ($ticket->early_bird_discount !== 'enable') {
          return false;
      }

      try {
          $date = trim(($ticket->early_bird_discount_date ?? '') . ' ' . ($ticket->early_bird_discount_time ?? ''));
          return $date !== '' && !\Carbon\Carbon::parse($date)->isPast();
      } catch (\Throwable $exception) {
          return false;
      }
  };

  $applyDiscount = function ($price, $ticket) use ($discountIsActive) {
      $price = (float) $price;

      if (!$discountIsActive($ticket)) {
          return $price;
      }

      if ($ticket->early_bird_discount_type === 'fixed') {
          return max($price - (float) $ticket->early_bird_discount_amount, 0);
      }

      if ($ticket->early_bird_discount_type === 'percentage') {
          return max($price - (($price * (float) $ticket->early_bird_discount_amount) / 100), 0);
      }

      return $price;
  };

  $ticketRows = $tickets->map(function ($ticket) use (
      $languageId,
      $ticketContentsByTicket,
      $decodeVariations,
      $discountIsActive,
      $applyDiscount,
      $formatCount
  ) {
      $ticketContents = $ticketContentsByTicket->get($ticket->id, collect());
      $ticketContent = $ticketContents->firstWhere('language_id', $languageId) ?: $ticketContents->first();

      $variations = $decodeVariations($ticket);
      $discountActive = $discountIsActive($ticket);
      $isNoCost = $ticket->pricing_type === 'free'
          || ($ticket->pricing_type === 'normal' && (float) ($ticket->price ?? 0) <= 0)
          || ($ticket->pricing_type === 'variation' && $variations->contains(fn($variation) => (float) ($variation['price'] ?? 0) <= 0));

      if ($ticket->pricing_type === 'free') {
          $priceLabel = __('Gratis');
          $originalPriceLabel = null;
          $pricingLabel = __('Gratis');
      } elseif ($ticket->pricing_type === 'variation') {
          $prices = $variations->pluck('price')->map(fn($price) => $applyDiscount($price, $ticket))->filter(fn($price) => $price !== null);
          $originalPrices = $variations->pluck('price')->map(fn($price) => (float) $price)->filter(fn($price) => $price !== null);
          $pricingLabel = __('Variaciones');

          if ($prices->isEmpty()) {
              $priceLabel = '-';
          } elseif ($prices->min() == $prices->max()) {
              $priceLabel = symbolPrice($prices->min());
          } else {
              $priceLabel = symbolPrice($prices->min()) . ' - ' . symbolPrice($prices->max());
          }

          $originalPriceLabel = null;
          if ($discountActive && $originalPrices->isNotEmpty()) {
              $originalPriceLabel = $originalPrices->min() == $originalPrices->max()
                  ? symbolPrice($originalPrices->min())
                  : symbolPrice($originalPrices->min()) . ' - ' . symbolPrice($originalPrices->max());
          }
      } else {
          $priceLabel = symbolPrice($applyDiscount($ticket->price, $ticket));
          $originalPriceLabel = $discountActive ? symbolPrice($ticket->price) : null;
          $pricingLabel = __('Precio fijo');
      }

      if ($ticket->pricing_type === 'variation') {
          $hasUnlimited = $variations->contains(fn($variation) => ($variation['ticket_available_type'] ?? null) === 'unlimited');
          $stockTotal = $variations
              ->filter(fn($variation) => ($variation['ticket_available_type'] ?? null) !== 'unlimited')
              ->sum(fn($variation) => (int) ($variation['ticket_available'] ?? 0));
          $stockLabel = $hasUnlimited ? __('Ilimitado') : $formatCount($stockTotal);
          $stockMeta = $formatCount($variations->count()) . ' ' . __('tipos');
          $maxBuyLabel = __('Por variación');
      } else {
          $stockLabel = $ticket->ticket_available_type === 'unlimited'
              ? __('Ilimitado')
              : $formatCount($ticket->ticket_available);
          $stockMeta = __('Disponibles');
          $maxBuyLabel = $ticket->max_ticket_buy_type === 'limited'
              ? $formatCount($ticket->max_buy_ticket) . ' ' . __('por compra')
              : __('Sin límite');
      }

      return [
          'model' => $ticket,
          'title' => $ticketContent->title ?? __('Entrada sin título'),
          'pricing_type' => $ticket->pricing_type,
          'is_no_cost' => $isNoCost,
          'pricing_label' => $pricingLabel,
          'price_label' => $priceLabel,
          'original_price_label' => $originalPriceLabel,
          'stock_label' => $stockLabel,
          'stock_meta' => $stockMeta,
          'max_buy_label' => $maxBuyLabel,
          'discount_active' => $discountActive,
          'variation_count' => $variations->count(),
      ];
  });

  $freeTickets = $ticketRows->where('is_no_cost', true)->count();
  $variationTickets = $ticketRows->where('pricing_type', 'variation')->count();
@endphp

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Entradas') }}</h4>
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
        <a href="{{ route('admin.event_management.event', ['language' => $defaultLang->code]) }}">{{ __('Eventos') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">
          {{ strlen($eventTitle) > 35 ? mb_substr($eventTitle, 0, 35, 'UTF-8') . '...' : $eventTitle }}
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="{{ route('admin.event.ticket', ['language' => $defaultLang->code, 'event_id' => request()->input('event_id'), 'event_type' => request()->input('event_type')]) }}">{{ __('Entradas') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card ticket-index-card">
        <div class="card-header">
          <div class="ticket-index-header">
            <div class="ticket-index-header__intro">
              <span class="ticket-index-header__eyebrow">{{ __('Configuración de venta') }}</span>
              <h3 class="ticket-index-header__title">{{ $eventTitle }}</h3>
              <p class="ticket-index-header__text">{{ __('Gestioná tipos de entrada, precios, disponibilidad y límites de compra del evento.') }}</p>
            </div>

            <div class="ticket-index-actions">
              <button class="btn btn-danger btn-sm d-none bulk-delete"
                data-href="{{ route('admin.event_management.bulk_delete_event_ticket') }}">
                <i class="flaticon-interface-5"></i> {{ __('Eliminar') }}
              </button>
              <a href="{{ route('admin.event_management.event', ['language' => $defaultLang->code, 'event_type' => request()->input('event_type')]) }}"
                class="btn btn-light btn-sm ticket-index-action-btn">
                <i class="fas fa-arrow-left mr-1"></i>{{ __('Volver') }}
              </a>
              <a class="btn btn-outline-primary btn-sm ticket-index-action-btn"
                href="{{ route('event.details', ['slug' => eventSlug($defaultLang->id, request()->input('event_id')), 'id' => request()->input('event_id')]) }}"
                target="_blank" rel="noopener">
                <i class="fas fa-eye mr-1"></i>{{ __('Ver evento') }}
              </a>
              <a href="{{ route('admin.event.add.ticket', ['language' => $defaultLang->code, 'event_id' => request()->input('event_id'), 'event_type' => request()->input('event_type')]) }}"
                class="btn btn-primary btn-sm ticket-index-action-btn">
                <i class="fas fa-plus-circle mr-1"></i>{{ __('Agregar entrada') }}
              </a>
            </div>
          </div>
        </div>

        <div class="card-body">
          @if (session()->has('course_status_warning'))
            <div class="alert alert-warning">
              <p class="text-dark mb-0">{{ session()->get('course_status_warning') }}</p>
            </div>
          @endif

          <div class="ticket-index-summary" aria-label="{{ __('Resumen de entradas') }}">
            <div class="ticket-index-summary__item">
              <span>{{ __('Idioma') }}</span>
              <strong>{{ $activeLanguageName }}</strong>
            </div>
            <div class="ticket-index-summary__item">
              <span>{{ __('Entradas') }}</span>
              <strong>{{ $formatCount($ticketRows->count()) }}</strong>
            </div>
            <div class="ticket-index-summary__item">
              <span>{{ __('Gratis') }}</span>
              <strong>{{ $formatCount($freeTickets) }}</strong>
            </div>
            <div class="ticket-index-summary__item">
              <span>{{ __('Con variaciones') }}</span>
              <strong>{{ $formatCount($variationTickets) }}</strong>
            </div>

            @if (!empty($languages) && count($languages) > 1)
              <form action="" method="get" id="LangFrom" class="ticket-index-language">
                <input type="hidden" name="event_id" value="{{ request()->input('event_id') }}">
                <input type="hidden" name="event_type" value="{{ request()->input('event_type') }}">
                <label for="ticketIndexLanguage">{{ __('Idioma del listado') }}</label>
                <select id="ticketIndexLanguage" name="language" class="form-control"
                  onchange="document.getElementById('LangFrom').submit()">
                  <option selected disabled>{{ __('Seleccioná idioma') }}</option>
                  @foreach ($languages as $lang)
                    <option value="{{ $lang->code }}" {{ $lang->code == request()->input('language') ? 'selected' : '' }}>
                      {{ $lang->name }}
                    </option>
                  @endforeach
                </select>
              </form>
            @endif
          </div>

          <form action="{{ route('admin.event.ticket.free_limit') }}" method="POST" class="ticket-free-limit">
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

          @if ($ticketRows->isEmpty())
            <div class="ticket-index-empty text-center">
              <h3>{{ __('No hay entradas configuradas') }}</h3>
              <p>{{ __('Agregá la primera entrada para habilitar la venta del evento.') }}</p>
              <a href="{{ route('admin.event.add.ticket', ['language' => $defaultLang->code, 'event_id' => request()->input('event_id'), 'event_type' => request()->input('event_type')]) }}"
                class="btn btn-primary btn-sm">
                <i class="fas fa-plus-circle mr-1"></i>{{ __('Agregar entrada') }}
              </a>
            </div>
          @else
            <div class="table-responsive ticket-index-table-wrap d-none d-lg-block">
              <table class="table ticket-index-table mt-3">
                <thead>
                  <tr>
                    <th scope="col" class="ticket-index-col-check">
                      <input type="checkbox" class="bulk-check" data-val="all" aria-label="{{ __('Seleccionar todas') }}">
                    </th>
                    <th scope="col" class="ticket-index-col-title">{{ __('Entrada') }}</th>
                    <th scope="col" class="ticket-index-col-stock">{{ __('Disponibilidad') }}</th>
                    <th scope="col" class="ticket-index-col-price">{{ __('Precio') }}</th>
                    <th scope="col" class="ticket-index-col-max">{{ __('Compra máxima') }}</th>
                    <th scope="col" class="ticket-index-col-actions">{{ __('Acciones') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($ticketRows as $row)
                    @php($ticket = $row['model'])
                    <tr>
                      <td class="ticket-index-col-check">
                        <input type="checkbox" class="bulk-check" data-val="{{ $ticket->id }}"
                          aria-label="{{ __('Seleccionar entrada') }} {{ $row['title'] }}">
                      </td>
                      <td>
                        <span class="ticket-index-title">{{ $row['title'] }}</span>
                        <span class="ticket-index-meta">
                          <span class="ticket-index-pill">{{ $row['pricing_label'] }}</span>
                          @if ($row['discount_active'])
                            <span class="ticket-index-pill ticket-index-pill--discount">{{ __('Early bird activo') }}</span>
                          @endif
                        </span>
                      </td>
                      <td>
                        <span class="ticket-index-value">{{ $row['stock_label'] }}</span>
                        <span class="ticket-index-muted">{{ $row['stock_meta'] }}</span>
                      </td>
                      <td>
                        <span class="ticket-index-price">{{ $row['price_label'] }}</span>
                        @if ($row['original_price_label'])
                          <del class="ticket-index-muted">{{ $row['original_price_label'] }}</del>
                        @endif
                      </td>
                      <td>
                        <span class="ticket-index-value">{{ $row['max_buy_label'] }}</span>
                      </td>
                      <td>
                        <div class="dropdown">
                          <button class="btn btn-secondary dropdown-toggle btn-sm ticket-index-actions-btn" type="button"
                            id="ticketActions-{{ $ticket->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ __('Acciones') }}
                          </button>
                          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="ticketActions-{{ $ticket->id }}">
                            <a href="{{ route('admin.event.edit.ticket', ['language' => $defaultLang->code, 'event_id' => request()->input('event_id'), 'event_type' => request()->input('event_type'), 'id' => $ticket->id]) }}"
                              class="dropdown-item">
                              {{ __('Editar') }}
                            </a>
                            <form class="deleteForm d-block"
                              action="{{ route('admin.ticket_management.delete_ticket', ['id' => $ticket->id]) }}"
                              method="post">
                              @csrf
                              <button type="submit" class="btn btn-sm deleteBtn">{{ __('Eliminar') }}</button>
                            </form>
                          </div>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <div class="ticket-index-mobile-list d-lg-none">
              @foreach ($ticketRows as $row)
                @php($ticket = $row['model'])
                <article class="ticket-index-mobile-card">
                  <div class="ticket-index-mobile-head">
                    <label class="ticket-index-mobile-check">
                      <input type="checkbox" class="bulk-check" data-val="{{ $ticket->id }}"
                        aria-label="{{ __('Seleccionar entrada') }} {{ $row['title'] }}">
                    </label>
                    <div>
                      <span class="ticket-index-title">{{ $row['title'] }}</span>
                      <span class="ticket-index-meta">
                        <span class="ticket-index-pill">{{ $row['pricing_label'] }}</span>
                        @if ($row['discount_active'])
                          <span class="ticket-index-pill ticket-index-pill--discount">{{ __('Early bird') }}</span>
                        @endif
                      </span>
                    </div>
                  </div>

                  <div class="ticket-index-mobile-grid">
                    <div>
                      <span class="ticket-index-mobile-label">{{ __('Disponibilidad') }}</span>
                      <span class="ticket-index-value">{{ $row['stock_label'] }}</span>
                      <span class="ticket-index-muted">{{ $row['stock_meta'] }}</span>
                    </div>
                    <div>
                      <span class="ticket-index-mobile-label">{{ __('Precio') }}</span>
                      <span class="ticket-index-price">{{ $row['price_label'] }}</span>
                      @if ($row['original_price_label'])
                        <del class="ticket-index-muted">{{ $row['original_price_label'] }}</del>
                      @endif
                    </div>
                    <div>
                      <span class="ticket-index-mobile-label">{{ __('Compra máxima') }}</span>
                      <span class="ticket-index-value">{{ $row['max_buy_label'] }}</span>
                    </div>
                  </div>

                  <div class="ticket-index-mobile-controls">
                    <a href="{{ route('admin.event.edit.ticket', ['language' => $defaultLang->code, 'event_id' => request()->input('event_id'), 'event_type' => request()->input('event_type'), 'id' => $ticket->id]) }}"
                      class="btn btn-outline-primary btn-sm">
                      <i class="fas fa-edit mr-1"></i>{{ __('Editar') }}
                    </a>
                    <form class="deleteForm d-block"
                      action="{{ route('admin.ticket_management.delete_ticket', ['id' => $ticket->id]) }}" method="post">
                      @csrf
                      <button type="submit" class="btn btn-outline-danger btn-sm deleteBtn">
                        <i class="fas fa-trash mr-1"></i>{{ __('Eliminar') }}
                      </button>
                    </form>
                  </div>
                </article>
              @endforeach
            </div>
          @endif
        </div>

        <div class="card-footer"></div>
      </div>
    </div>
  </div>
@endsection

@section('style')
  <style>
    .ticket-index-card {
      border: 0;
      border-radius: 8px;
      box-shadow: 0 8px 20px rgba(15, 23, 42, .045);
      overflow: hidden;
    }

    .ticket-index-card .card-header {
      border-bottom: 1px solid #e5e7eb;
      background: #fff;
      padding: 22px 24px;
    }

    .ticket-index-card .card-body {
      padding: 18px 24px 22px;
      background: #fff;
    }

    .ticket-index-header {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 18px;
    }

    .ticket-index-header__eyebrow {
      display: inline-flex;
      align-items: center;
      min-height: 24px;
      padding: 4px 10px;
      border-radius: 999px;
      background: #f1f5f9;
      color: #475569;
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 0;
      text-transform: none;
    }

    .ticket-index-header__title {
      margin: 10px 0 6px;
      color: #0f172a;
      font-size: 22px;
      font-weight: 700;
      line-height: 1.25;
    }

    .ticket-index-header__text {
      margin: 0;
      color: #64748b;
      font-size: 13px;
      line-height: 1.5;
    }

    .ticket-index-actions {
      display: flex;
      flex-wrap: wrap;
      justify-content: flex-end;
      gap: 8px;
      min-width: 320px;
    }

    .ticket-index-action-btn,
    .ticket-index-actions-btn {
      min-height: 40px;
      border-radius: 10px;
      font-weight: 600;
      white-space: nowrap;
    }

    .ticket-index-summary {
      display: grid;
      grid-template-columns: repeat(4, minmax(120px, 1fr)) minmax(220px, 1.2fr);
      gap: 12px;
      align-items: stretch;
      margin-bottom: 18px;
      padding: 12px;
      border: 1px solid #e5e7eb;
      border-radius: 12px;
      background: #fff;
    }

    .ticket-index-summary__item,
    .ticket-index-language {
      border: 0;
      border-radius: 0;
      background: transparent;
      padding: 6px 10px;
    }

    .ticket-index-summary__item span,
    .ticket-index-language label {
      display: block;
      margin-bottom: 6px;
      color: #64748b;
      font-size: 11px;
      font-weight: 500;
    }

    .ticket-index-summary__item strong {
      color: #0f172a;
      font-size: 16px;
      font-weight: 600;
    }

    .ticket-index-language {
      margin: 0;
      padding: 2px 10px;
    }

    .ticket-index-language .form-control {
      height: 34px;
      padding: 4px 0;
      border: 0;
      color: #0f172a;
      font-weight: 600;
      background: transparent;
      box-shadow: none;
    }

    .ticket-free-limit {
      display: grid;
      grid-template-columns: minmax(0, 1fr) minmax(280px, auto);
      gap: 16px;
      align-items: center;
      margin-bottom: 18px;
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

    .ticket-index-empty {
      padding: 54px 16px;
      border: 1px dashed #cbd5e1;
      border-radius: 16px;
      background: #f8fafc;
    }

    .ticket-index-empty h3 {
      margin-bottom: 8px;
      color: #0f172a;
      font-size: 22px;
      font-weight: 700;
    }

    .ticket-index-empty p {
      color: #64748b;
    }

    .ticket-index-table-wrap {
      border: 1px solid #e5e7eb;
      border-radius: 12px;
      overflow-x: visible;
      background: #fff;
    }

    .ticket-index-table {
      width: 100%;
      margin: 0 !important;
      table-layout: fixed;
    }

    .ticket-index-table thead th {
      border-top: 0;
      border-bottom: 1px solid #e5e7eb;
      background: #f8fafc;
      color: #475569;
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 0;
      padding: 12px 8px;
      text-transform: none;
      vertical-align: middle;
    }

    .ticket-index-table tbody td {
      border-top: 1px solid #eef2f7;
      padding: 13px 8px;
      vertical-align: middle;
      overflow-wrap: anywhere;
    }

    .ticket-index-col-check {
      width: 34px;
      max-width: 34px;
      padding-left: 6px !important;
      padding-right: 6px !important;
      text-align: center;
      overflow-wrap: normal;
    }

    .ticket-index-col-title {
      width: 34%;
    }

    .ticket-index-col-stock,
    .ticket-index-col-price,
    .ticket-index-col-max {
      width: 17%;
    }

    .ticket-index-col-actions {
      width: 12%;
    }

    .ticket-index-title,
    .ticket-index-value,
    .ticket-index-price {
      display: block;
      color: #0f172a;
      font-size: 13px;
      font-weight: 600;
      line-height: 1.35;
    }

    .ticket-index-price {
      color: #047857;
      font-weight: 700;
    }

    .ticket-index-muted {
      display: block;
      margin-top: 4px;
      color: #64748b;
      font-size: 11px;
      line-height: 1.35;
    }

    .ticket-index-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      margin-top: 8px;
    }

    .ticket-index-pill {
      display: inline-flex;
      align-items: center;
      min-height: 22px;
      padding: 3px 8px;
      border-radius: 999px;
      background: #f8fafc;
      color: #475569;
      font-size: 11px;
      font-weight: 600;
      white-space: nowrap;
    }

    .ticket-index-pill--discount {
      background: #fff7ed;
      color: #9a3412;
    }

    .ticket-index-mobile-list {
      display: grid;
      gap: 12px;
    }

    .ticket-index-mobile-card {
      border: 1px solid #e5e7eb;
      border-radius: 12px;
      background: #fff;
      padding: 14px;
      box-shadow: 0 8px 18px rgba(15, 23, 42, .045);
    }

    .ticket-index-mobile-head {
      display: grid;
      grid-template-columns: 34px minmax(0, 1fr);
      gap: 10px;
      align-items: start;
    }

    .ticket-index-mobile-check {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 32px;
      min-height: 44px;
      margin: 0;
    }

    .ticket-index-mobile-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 12px;
      margin-top: 14px;
      padding-top: 12px;
      border-top: 1px solid #eef2f7;
    }

    .ticket-index-mobile-label {
      display: block;
      margin-bottom: 6px;
      color: #475569;
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 0;
      text-transform: none;
    }

    .ticket-index-mobile-controls {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-top: 14px;
    }

    .ticket-index-mobile-controls .btn,
    .ticket-index-mobile-controls form {
      flex: 1 1 132px;
      min-width: 0;
    }

    .ticket-index-mobile-controls .btn {
      width: 100%;
      min-height: 40px;
      border-radius: 10px;
      font-weight: 600;
    }

    @media (max-width: 1199px) {
      .ticket-index-summary {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      .ticket-index-language {
        grid-column: span 2;
      }

      .ticket-free-limit {
        grid-template-columns: 1fr;
      }

      .ticket-free-limit__controls {
        justify-content: flex-start;
      }
    }

    @media (max-width: 991px) {
      .ticket-index-header {
        flex-direction: column;
      }

      .ticket-index-actions {
        justify-content: flex-start;
        min-width: 0;
        width: 100%;
      }
    }

    @media (max-width: 575px) {
      .ticket-index-card .card-header,
      .ticket-index-card .card-body {
        padding: 16px;
      }

      .ticket-index-summary,
      .ticket-index-mobile-grid {
        grid-template-columns: 1fr;
      }

      .ticket-index-language {
        grid-column: auto;
      }

      .ticket-index-actions > .btn,
      .ticket-index-actions > a,
      .ticket-free-limit__controls .btn,
      .ticket-index-mobile-controls .btn,
      .ticket-index-mobile-controls form {
        flex-basis: 100%;
      }

      .ticket-free-limit__number,
      .ticket-free-limit__toggle {
        width: 100%;
      }
    }
  </style>
@endsection
