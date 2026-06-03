{{--
  Partial: Modal de Add-ons (Bootstrap 4 in-page)
  Esperado: $event (objeto con ->id = event_id).

  Renderiza un modal Bootstrap 4 que se muestra al hacer click en
  "Reservar mi lugar". Permite seleccionar cantidades de add-ons opcionales
  antes de continuar al checkout. La sincronización con el backend se hace
  vía AJAX (POST a event.addon.update-ajax) desde el handler JS de
  event-details.blade.php.

  Accesibilidad WCAG 2.2 AA:
  - role="dialog" + aria-modal="true" + aria-labelledby.
  - aria-live="polite" en el recap para anunciar cambios.
  - Botones +/- con aria-label específico por addon.
  - Inputs readonly; los valores se controlan con los botones +/−.

  Resiliencia:
  - Si no hay secciones activas, el partial no renderiza el modal
    (return early) para evitar overlays vacíos.
  - Si JS está deshabilitado, el partial NO se muestra; el sidebar
    fallback en event-details.blade.php sigue funcionando con sus
    submits HTML nativos.
--}}

@php
  use App\Models\EventAddonSection;

  $eventId = isset($event) && is_object($event) && isset($event->id) ? (int) $event->id : 0;
  $cartAddons = Session::get('cart_addons', []);
  $selectedAddons = $cartAddons[$eventId] ?? [];
  $sections = $eventId > 0
    ? EventAddonSection::where('event_id', $eventId)
        ->where('is_active', true)
        ->with(['addons' => function ($q) {
          $q->where('is_active', true)
            ->where(function ($qq) {
              $qq->whereNull('stock')->orWhere('stock', '>', 0);
            })
            ->orderBy('sort_order');
        }])
        ->orderBy('sort_order')
        ->get()
    : collect();
  $basicInfo = \App\Models\BasicSettings\Basic::select('base_currency_symbol', 'base_currency_symbol_position')->first();
  $currencySymbol = $basicInfo->base_currency_symbol ?? '$';
  $currencyPosition = $basicInfo->base_currency_symbol_position ?? 'left';
@endphp

@if ($sections->isNotEmpty() && $eventId > 0)
  <div
    class="modal fade ed-addons-modal"
    id="edAddonsModal"
    tabindex="-1"
    role="dialog"
    aria-modal="true"
    aria-labelledby="edAddonsModalTitle"
    aria-hidden="true"
    data-update-url="{{ route('event.addon.update-ajax', ['event' => $eventId]) }}"
  >
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h2 class="modal-title h5" id="edAddonsModalTitle">
            {{ __('¿Querés sumar algo más para este evento?') }}
          </h2>
          <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Cerrar') }}">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <p class="text-muted ed-addons-modal__intro">
            {{ __('Sumá productos a tu reserva. Se canjean el día del evento. (Opcional)') }}
          </p>

          @foreach ($sections as $section)
            @if ($section->addons->isNotEmpty())
              <h3 class="ed-addons-modal__section-title">{{ $section->title }}</h3>
              @if (!empty($section->description))
                <p class="text-muted small">{{ $section->description }}</p>
              @endif

              <div class="ed-addons-modal__grid">
                @foreach ($section->addons as $addon)
                  @php
                    $maxAttr = $addon->max_per_order !== null
                                ? (int) $addon->max_per_order
                                : ($addon->stock !== null ? (int) $addon->stock : 9999);
                    $priceNum = (float) $addon->price;
                    $selectedQty = isset($selectedAddons[$addon->id]) ? (int) $selectedAddons[$addon->id] : 0;
                  @endphp
                  <article class="addon-modal-card{{ $selectedQty > 0 ? ' is-selected' : '' }}" data-addon-id="{{ $addon->id }}">
                    <div class="addon-modal-card__media">
                      @if (!empty($addon->image))
                        <img src="{{ asset('assets/admin/img/event-addons/' . $addon->image) }}" alt="{{ $addon->title }}" loading="lazy">
                      @else
                        <div class="addon-modal-card__placeholder" aria-hidden="true"></div>
                      @endif
                    </div>

                    <div class="addon-modal-card__body">
                      <h4 class="addon-modal-card__title">{{ $addon->title }}</h4>

                      @if (!empty($addon->description))
                        <p class="addon-modal-card__desc">{{ $addon->description }}</p>
                      @endif

                      <span class="addon-modal-card__price" dir="ltr">
                        @if ($currencyPosition === 'left')
                          {{ $currencySymbol }}<span class="addon-modal-card__price-amount" data-price="{{ $priceNum }}">{{ number_format($priceNum, 0, ',', '.') }}</span>
                        @else
                          <span class="addon-modal-card__price-amount" data-price="{{ $priceNum }}">{{ number_format($priceNum, 0, ',', '.') }}</span>{{ $currencySymbol }}
                        @endif
                      </span>

                      @if ($addon->requires_age_verification)
                        <p class="addon-modal-card__notice addon-modal-card__notice--warning" role="note">
                          <span aria-hidden="true">&#9888;</span>
                          {{ __('Requiere mayoría de edad (18+). Se solicitará DNI al momento del canje.') }}
                        </p>
                      @endif

                      @if ($addon->redeemable_only_at_event)
                        <p class="addon-modal-card__notice" role="note">
                          <span aria-hidden="true">&#127903;</span>
                          {{ __('Válido únicamente para canjear durante el evento.') }}
                        </p>
                      @endif

                      @if ($addon->non_refundable)
                        <p class="addon-modal-card__notice addon-modal-card__notice--danger" role="note">
                          <span aria-hidden="true">&#10006;</span>
                          {{ __('No reembolsable.') }}
                        </p>
                      @endif
                    </div>

                    <div class="addon-modal-card__qty">
                      <label for="addon-modal-qty-{{ $addon->id }}" class="sr-only">
                        {{ __('Cantidad de :title', ['title' => $addon->title]) }}
                      </label>
                      <div class="quantity-input quantity-input--modal">
                        <button
                          type="button"
                          class="addon-modal-quantity-down"
                          aria-label="{{ __('Disminuir cantidad de :title', ['title' => $addon->title]) }}"
                        >&minus;</button>
                        <input
                          type="number"
                          id="addon-modal-qty-{{ $addon->id }}"
                          name="addons[{{ $addon->id }}]"
                          value="{{ $selectedQty }}"
                          data-price="{{ $priceNum }}"
                          data-addon-id="{{ $addon->id }}"
                          min="0"
                          max="{{ $maxAttr }}"
                          readonly
                          class="quantity addon-modal-card__qty-input"
                          aria-label="{{ __('Cantidad de :title', ['title' => $addon->title]) }}"
                        >
                        <button
                          type="button"
                          class="addon-modal-quantity-up"
                          aria-label="{{ __('Aumentar cantidad de :title', ['title' => $addon->title]) }}"
                        >+</button>
                      </div>
                    </div>
                  </article>
                @endforeach
              </div>
            @endif
          @endforeach
        </div>

        <div class="modal-footer ed-addons-modal__footer">
          <p class="ed-addons-modal__recap" aria-live="polite" aria-atomic="true">
            <span id="edAddonsCount">0</span>
            {{ __('adicionales sumados') }} ·
            <span dir="ltr">+<span id="edAddonsTotal">{{ $currencySymbol }}0</span></span>
            {{ __('al total') }}
          </p>
          <button type="button" class="btn btn-link" id="edAddonsSkip">
            {{ __('Seguir sin sumar') }}
          </button>
          <button type="button" class="btn btn-primary" id="edAddonsConfirm">
            {{ __('Sumar y continuar') }}
          </button>
        </div>
      </div>
    </div>
  </div>
@endif
