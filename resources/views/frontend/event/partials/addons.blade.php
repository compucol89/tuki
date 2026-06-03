{{--
  Partial: Add-ons por evento
  Esperado: $event (objeto con ->id = event_id, App\Models\Event o EventContent+Event join)

  Por cada sección activa del evento, muestra los add-ons disponibles
  con selector de cantidad, precio, imagen y advertencias legales
  (mayoría de edad, no reembolsable, canjeable solo en el evento).
--}}

@php
  use App\Models\EventAddonSection;

  $eventId = isset($event) && is_object($event) && isset($event->id) ? (int) $event->id : 0;
  $cartAddons = Session::get('cart_addons', []);
  $selectedAddons = $cartAddons[$eventId] ?? $cartAddons[(string) $eventId] ?? [];
  $isSidebar = ($variant ?? null) === 'sidebar';
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
@endphp

@if ($sections->isNotEmpty())
  <section class="event-addons{{ $isSidebar ? ' event-addons--sidebar' : ' ed-section' }}" id="event-addons" aria-labelledby="event-addons-title">
    <div class="{{ $isSidebar ? 'event-addons__inner' : 'container' }}">
      <h2 class="{{ $isSidebar ? 'event-addons__title' : 'ed-section-title' }}" id="event-addons-title">
        {{ __('Adicionales del evento') }}
      </h2>
      <p class="{{ $isSidebar ? 'event-addons__subtitle' : 'text-muted ed-section-subtitle' }}">
        {{ __('Sumá productos a tu reserva. Se canjean el día del evento.') }}
      </p>

      @foreach ($sections as $section)
        @if ($section->addons->isNotEmpty())
          <div class="addon-section{{ $isSidebar ? '' : ' mb-5' }}">
            <h3 class="addon-section-title">{{ $section->title }}</h3>
            @if (!empty($section->description))
              <p class="text-muted">{{ $section->description }}</p>
            @endif

            <div class="{{ $isSidebar ? 'addon-sidebar-list' : 'row' }}">
              @foreach ($section->addons as $addon)
                @php
                  $hasStock = $addon->stock === null ? true : ((int) $addon->stock > 0);
                  $maxAttr  = $addon->max_per_order !== null
                                ? (int) $addon->max_per_order
                                : ($addon->stock !== null ? (int) $addon->stock : 9999);
                  $priceNum = (float) $addon->price;
                  $selectedQty = isset($selectedAddons[$addon->id]) ? (int) $selectedAddons[$addon->id] : 0;
                @endphp
                <div class="{{ $isSidebar ? 'addon-sidebar-item' : 'col-md-4 col-sm-6 mb-4' }}">
                  <article class="addon-card" data-addon-id="{{ $addon->id }}">
                    @if (!empty($addon->image))
                      <img
                        src="{{ asset('assets/admin/img/event-addons/' . $addon->image) }}"
                        alt="{{ $addon->title }}"
                        class="addon-card__image"
                        loading="lazy"
                      >
                    @else
                      <div class="addon-card__image addon-card__image--placeholder" aria-hidden="true"></div>
                    @endif

                    <h4 class="addon-card__title">{{ $addon->title }}</h4>

                    @if (!empty($addon->description))
                      <p class="addon-card__description">{{ $addon->description }}</p>
                    @endif

                    <div class="addon-card__price" dir="ltr">
                      <span class="addon-card__price-amount">${{ number_format($priceNum, 0, ',', '.') }}</span>
                    </div>

                    @if ($addon->requires_age_verification)
                      <p class="addon-card__notice addon-card__notice--warning" role="note">
                        <span aria-hidden="true">&#9888;</span>
                        {{ __('Requiere mayoría de edad (18+). Se solicitará DNI al momento del canje.') }}
                      </p>
                    @endif

                    @if ($addon->redeemable_only_at_event)
                      <p class="addon-card__notice" role="note">
                        <span aria-hidden="true">&#127903;</span>
                        {{ __('Válido únicamente para canjear durante el evento.') }}
                      </p>
                    @endif

                    @if ($addon->non_refundable)
                      <p class="addon-card__notice addon-card__notice--danger" role="note">
                        <span aria-hidden="true">&#10006;</span>
                        {{ __('No reembolsable.') }}
                      </p>
                    @endif

                    @if (!$hasStock)
                      <span class="badge badge-danger">{{ __('Agotado') }}</span>
                    @else
                      @if ($addon->stock !== null)
                        <small class="text-muted d-block mb-2">
                          {{ __('Stock disponible:') }} {{ (int) $addon->stock }}
                        </small>
                      @endif

                      <form
                        method="POST"
                        action="{{ route('event.addon.add', ['event' => $eventId]) }}"
                        class="addon-card__form"
                      >
                        @csrf
                        <input type="hidden" name="addon_id" value="{{ $addon->id }}">
                        <div class="addon-card__qty">
                          <label for="addon-qty-{{ $addon->id }}" class="addon-card__qty-label">
                            {{ __('Cantidad') }}
                          </label>
                          <div class="quantity-input addon-card__quantity-input">
                            <button class="addon-quantity-down" type="button" aria-label="{{ __('Disminuir cantidad') }}">
                              -
                            </button>
                            <input
                              type="number"
                              id="addon-qty-{{ $addon->id }}"
                              name="qty"
                              min="0"
                              max="{{ $maxAttr }}"
                              value="{{ $selectedQty }}"
                              data-price="{{ $priceNum }}"
                              data-addon-id="{{ $addon->id }}"
                              class="quantity addon-card__qty-input"
                              aria-label="{{ __('Cantidad de :title', ['title' => $addon->title]) }}"
                              readonly
                            >
                            <button class="addon-quantity-up" type="button" aria-label="{{ __('Aumentar cantidad') }}">
                              +
                            </button>
                          </div>
                          <button
                            type="submit"
                            class="btn btn-primary btn-sm addon-card__add-btn"
                          >
                            {{ __('Guardar') }}
                          </button>
                        </div>
                      </form>
                    @endif
                  </article>
                </div>
              @endforeach
            </div>
          </div>
        @endif
      @endforeach
    </div>
  </section>
@endif
