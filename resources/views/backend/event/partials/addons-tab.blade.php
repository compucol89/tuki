{{--
  Tab: Add-ons por evento (panel admin/organizer)
  Esperado: $event (App\Models\Event) y opcionalmente $sections (collection precargada).
  Si $sections no viene, se carga acá.

  Fix #1 (P0 #2): todas las rutas usan addonsRoute($logicalName, ...) que resuelve
                   según el guard activo (admin vs organizer) definido en Helper.php.
  Fix #2 (P1 #1): form completo de creación de add-ons con 11 campos, edición
                   inline (details/summary), eliminar con confirmación JS,
                   badge "Agotado" cuando stock === 0, preview de imagen.
--}}

@php
  use App\Models\EventAddonSection;

  $eventId = isset($event) && is_object($event) ? (int) $event->id : 0;
  $sections = isset($sections) ? $sections : (
    $eventId > 0
      ? EventAddonSection::where('event_id', $eventId)
          ->with(['addons' => function ($q) {
            $q->orderBy('sort_order');
          }])
          ->orderBy('sort_order')
          ->get()
      : collect()
  );
@endphp

<div class="card ev-section-card mt-3" id="ev-addons-card">
  <div class="card-header ev-section-header">
    <h4 class="mb-0">
      <i class="fas fa-cubes mr-2"></i>{{ __('Add-ons del evento') }}
    </h4>
  </div>
  <div class="card-body">
    <p class="text-muted small">
      {{ __('Secciones y productos adicionales que el cliente puede sumar a su reserva. Stock, precio y validaciones se controlan acá.') }}
    </p>

    {{-- Lista de secciones existentes + add-ons por sección --}}
    @if ($sections->isNotEmpty())
      @foreach ($sections as $section)
        <div class="border rounded p-3 mb-3">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h5 class="mb-1">{{ $section->title }}</h5>
              @if (!empty($section->description))
                <small class="text-muted d-block">{{ $section->description }}</small>
              @endif
              <small class="text-muted">
                {{ __('Orden: :n · :count add-ons', ['n' => (int) $section->sort_order, 'count' => $section->addons->count()]) }}
              </small>
            </div>
            <div>
              <form
                method="POST"
                action="{{ addonsRoute('section.update', $eventId, $section->id) }}"
                class="d-inline"
              >
                @csrf
                @method('PUT')
                <input type="hidden" name="title" value="{{ $section->title }}">
                <input type="hidden" name="description" value="{{ $section->description }}">
                <input type="hidden" name="sort_order" value="{{ $section->sort_order }}">
                <input type="hidden" name="is_active" value="{{ $section->is_active ? 1 : 0 }}">
                <button type="submit" class="btn btn-sm btn-outline-secondary" title="{{ __('Guardar (sin cambios) sección') }}">
                  <i class="fas fa-save"></i>
                </button>
              </form>
              <form
                method="POST"
                action="{{ addonsRoute('section.destroy', $eventId, $section->id) }}"
                class="d-inline"
                onsubmit="return confirm('{{ __('¿Eliminar sección y todos sus add-ons?') }}')"
              >
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Eliminar sección') }}">
                  <i class="fas fa-trash"></i>
                </button>
              </form>
            </div>
          </div>

          {{-- Lista de add-ons de esta sección --}}
          @if ($section->addons->isNotEmpty())
            <table class="table table-sm mt-3 mb-0">
              <thead>
                <tr>
                  <th>{{ __('Add-on') }}</th>
                  <th>{{ __('Precio') }}</th>
                  <th>{{ __('Stock') }}</th>
                  <th>{{ __('Estado') }}</th>
                  <th class="text-right">{{ __('Acciones') }}</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($section->addons as $addon)
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        @if (!empty($addon->image))
                          <img
                            src="{{ asset('assets/admin/img/event-addons/' . $addon->image) }}"
                            alt="{{ $addon->title }}"
                            class="mr-2"
                            style="width: 36px; height: 36px; object-fit: cover; border-radius: 4px;"
                          >
                        @else
                          <div
                            class="mr-2"
                            style="width: 36px; height: 36px; background: #f1f5f9; border-radius: 4px;"
                            aria-hidden="true"
                          ></div>
                        @endif
                        <div>
                          <strong>{{ $addon->title }}</strong>
                          @if (!empty($addon->description))
                            <small class="d-block text-muted">{{ \Illuminate\Support\Str::limit($addon->description, 60) }}</small>
                          @endif
                        </div>
                      </div>
                    </td>
                    <td dir="ltr">${{ number_format((float) $addon->price, 2, ',', '.') }}</td>
                    <td>
                      @if ($addon->stock === null)
                        <span class="badge badge-secondary">{{ __('Ilimitado') }}</span>
                      @elseif ((int) $addon->stock <= 0)
                        <span class="badge badge-danger">{{ __('Agotado') }}</span>
                      @else
                        {{ (int) $addon->stock }}
                      @endif
                    </td>
                    <td>
                      @if ($addon->is_active)
                        <span class="badge badge-success">{{ __('Activo') }}</span>
                      @else
                        <span class="badge badge-warning">{{ __('Inactivo') }}</span>
                      @endif
                      @if ($addon->requires_age_verification)
                        <span class="badge badge-warning" title="{{ __('Requiere mayoría de edad') }}">18+</span>
                      @endif
                      @if ($addon->non_refundable)
                        <span class="badge badge-danger" title="{{ __('No reembolsable') }}">{{ __('No reemb.') }}</span>
                      @endif
                    </td>
                    <td class="text-right">
                      <details class="d-inline-block">
                        <summary class="btn btn-sm btn-outline-secondary d-inline-block" style="list-style: none; cursor: pointer;" title="{{ __('Editar add-on') }}">
                          <i class="fas fa-edit"></i>
                        </summary>
                        <div class="p-2 border rounded mt-1" style="min-width: 320px; text-align: left;">
                          <form
                            method="POST"
                            action="{{ addonsRoute('addon.update', $eventId, $addon->id) }}"
                            enctype="multipart/form-data"
                          >
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="event_addon_section_id" value="{{ $section->id }}">
                            <div class="form-group mb-2">
                              <label class="small mb-0">{{ __('Nombre') }}</label>
                              <input type="text" name="title" class="form-control form-control-sm" value="{{ $addon->title }}" required maxlength="191">
                            </div>
                            <div class="form-group mb-2">
                              <label class="small mb-0">{{ __('Descripción') }}</label>
                              <textarea name="description" class="form-control form-control-sm" rows="2" maxlength="500">{{ $addon->description }}</textarea>
                            </div>
                            <div class="row">
                              <div class="col-6 form-group mb-2">
                                <label class="small mb-0">{{ __('Precio') }}</label>
                                <input type="number" name="price" step="0.01" min="0" class="form-control form-control-sm" value="{{ $addon->price }}" required>
                              </div>
                              <div class="col-6 form-group mb-2">
                                <label class="small mb-0">{{ __('Precio anterior') }}</label>
                                <input type="number" name="previous_price" step="0.01" min="0" class="form-control form-control-sm" value="{{ $addon->previous_price }}">
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-6 form-group mb-2">
                                <label class="small mb-0">{{ __('Stock (vacío = ilimitado)') }}</label>
                                <input type="number" name="stock" min="0" class="form-control form-control-sm" value="{{ $addon->stock }}">
                              </div>
                              <div class="col-6 form-group mb-2">
                                <label class="small mb-0">{{ __('Máx. por compra') }}</label>
                                <input type="number" name="max_per_order" min="1" class="form-control form-control-sm" value="{{ $addon->max_per_order }}">
                              </div>
                            </div>
                            <div class="form-group mb-2">
                              <label class="small mb-0">{{ __('Imagen (reemplazar)') }}</label>
                              <input type="file" name="image" accept="image/*" class="form-control form-control-sm">
                            </div>
                            <div class="form-check mb-1">
                              <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $addon->is_active ? 'checked' : '' }}>
                              <label class="form-check-label small">{{ __('Activo') }}</label>
                            </div>
                            <div class="form-check mb-1">
                              <input class="form-check-input" type="checkbox" name="requires_age_verification" value="1" {{ $addon->requires_age_verification ? 'checked' : '' }}>
                              <label class="form-check-label small">{{ __('Requiere mayoría de edad (18+)') }}</label>
                            </div>
                            <div class="form-check mb-1">
                              <input class="form-check-input" type="checkbox" name="redeemable_only_at_event" value="1" {{ $addon->redeemable_only_at_event ? 'checked' : '' }}>
                              <label class="form-check-label small">{{ __('Canjeable solo en el evento') }}</label>
                            </div>
                            <div class="form-check mb-1">
                              <input class="form-check-input" type="checkbox" name="non_refundable" value="1" {{ $addon->non_refundable ? 'checked' : '' }}>
                              <label class="form-check-label small">{{ __('No reembolsable') }}</label>
                            </div>
                            <div class="form-group mb-2">
                              <label class="small mb-0">{{ __('Orden') }}</label>
                              <input type="number" name="sort_order" min="0" class="form-control form-control-sm" value="{{ $addon->sort_order }}">
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">
                              <i class="fas fa-save mr-1"></i>{{ __('Guardar cambios') }}
                            </button>
                          </form>
                        </div>
                      </details>
                      <form
                        method="POST"
                        action="{{ addonsRoute('addon.destroy', $eventId, $addon->id) }}"
                        class="d-inline"
                        onsubmit="return confirm('{{ __('¿Eliminar este add-on?') }}')"
                      >
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Eliminar add-on') }}">
                          <i class="fas fa-trash"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @endif
        </div>
      @endforeach
    @else
      <div class="alert alert-info">
        {{ __('Todavía no hay secciones de add-ons. Creá la primera abajo.') }}
      </div>
    @endif

    {{-- Form: nueva sección --}}
    <div class="border-top pt-3 mt-3">
      <h5>{{ __('Nueva sección de add-ons') }}</h5>
      <form
        method="POST"
        action="{{ addonsRoute('section.store', $eventId) }}"
        class="form-inline flex-wrap"
      >
        @csrf
        <div class="form-group mr-2 mb-2 flex-grow-1">
          <label for="addon-section-title" class="sr-only">{{ __('Título') }}</label>
          <input
            type="text"
            id="addon-section-title"
            name="title"
            class="form-control w-100"
            placeholder="{{ __('Título (ej: Trago en rumba, Combos)') }}"
            required
            maxlength="191"
          >
        </div>
        <div class="form-group mr-2 mb-2">
          <label for="addon-section-order" class="sr-only">{{ __('Orden') }}</label>
          <input
            type="number"
            id="addon-section-order"
            name="sort_order"
            class="form-control"
            placeholder="{{ __('Orden') }}"
            value="0"
            min="0"
          >
        </div>
        <div class="form-group mr-2 mb-2">
          <div class="form-check">
            <input
              type="checkbox"
              id="addon-section-active"
              name="is_active"
              value="1"
              class="form-check-input"
              checked
            >
            <label for="addon-section-active" class="form-check-label">{{ __('Activa') }}</label>
          </div>
        </div>
        <button type="submit" class="btn btn-primary mb-2">
          <i class="fas fa-plus mr-1"></i>{{ __('Crear sección') }}
        </button>
      </form>
      <small class="text-muted d-block mt-1">
        {{ __('Después de crear la sección, agregá los add-ons (productos) que la componen.') }}
      </small>
    </div>

    {{-- Form: crear add-on (Fix #2) --}}
    @if ($sections->isNotEmpty())
      <div class="border-top pt-3 mt-4">
        <h5>{{ __('Crear add-on') }}</h5>
        <form
          method="POST"
          action="{{ addonsRoute('addon.store', $eventId) }}"
          enctype="multipart/form-data"
        >
          @csrf
          <input type="hidden" name="event_addon_section_id" id="new-addon-section-id" value="{{ $sections->first()->id ?? '' }}">

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="new-addon-section" class="small">{{ __('Sección') }} <span class="text-danger">*</span></label>
                <select id="new-addon-section" name="event_addon_section_id_select" class="form-control" required onchange="document.getElementById('new-addon-section-id').value=this.value">
                  @foreach ($sections as $section)
                    <option value="{{ $section->id }}">{{ $section->title }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="new-addon-title" class="small">{{ __('Nombre') }} <span class="text-danger">*</span></label>
                <input type="text" id="new-addon-title" name="title" class="form-control" required maxlength="191" placeholder="{{ __('Ej: Botella, Combo, Balde') }}">
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="new-addon-description" class="small">{{ __('Descripción') }}</label>
            <textarea id="new-addon-description" name="description" class="form-control" rows="2" maxlength="500" placeholder="{{ __('Descripción breve del add-on') }}"></textarea>
          </div>

          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="new-addon-price" class="small">{{ __('Precio') }} <span class="text-danger">*</span></label>
                <input type="number" id="new-addon-price" name="price" step="0.01" min="0" class="form-control" required placeholder="0.00">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label for="new-addon-prev-price" class="small">{{ __('Precio anterior (opcional)') }}</label>
                <input type="number" id="new-addon-prev-price" name="previous_price" step="0.01" min="0" class="form-control" placeholder="0.00">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label for="new-addon-stock" class="small">{{ __('Stock (vacío = ilimitado)') }}</label>
                <input type="number" id="new-addon-stock" name="stock" min="0" class="form-control" placeholder="{{ __('Ilimitado') }}">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label for="new-addon-max" class="small">{{ __('Máx. por compra') }}</label>
                <input type="number" id="new-addon-max" name="max_per_order" min="1" class="form-control" placeholder="{{ __('Sin tope') }}">
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="new-addon-image" class="small">{{ __('Imagen') }}</label>
            <input type="file" id="new-addon-image" name="image" accept="image/jpeg,image/png,image/webp" class="form-control-file">
            <small class="form-text text-muted">{{ __('JPG, PNG o WebP. Máx. 2 MB.') }}</small>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="new-addon-active" name="is_active" value="1" checked>
                <label class="form-check-label small" for="new-addon-active">{{ __('Activo') }}</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="new-addon-age" name="requires_age_verification" value="1">
                <label class="form-check-label small" for="new-addon-age">{{ __('Requiere mayoría de edad (18+)') }}</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="new-addon-redeem" name="redeemable_only_at_event" value="1" checked>
                <label class="form-check-label small" for="new-addon-redeem">{{ __('Canjeable solo en el evento') }}</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="new-addon-norefund" name="non_refundable" value="1">
                <label class="form-check-label small" for="new-addon-norefund">{{ __('No reembolsable') }}</label>
              </div>
            </div>
          </div>

          <div class="form-group mt-2">
            <label for="new-addon-order" class="small">{{ __('Orden de visualización') }}</label>
            <input type="number" id="new-addon-order" name="sort_order" min="0" class="form-control col-md-3" value="0">
          </div>

          <button type="submit" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i>{{ __('Crear add-on') }}
          </button>
        </form>
      </div>
    @endif
  </div>
</div>
