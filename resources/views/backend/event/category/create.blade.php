<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">{{ __('Add Event Category') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form id="modalForm" class="modal-form create" action="{{ route('admin.event_management.store_category') }}"
          method="post" enctype="multipart/form-data">
          @csrf
          <div class="category-modal-intro">
            <span class="category-modal-intro__eyebrow">{{ __('Paso 1') }}</span>
            <p class="category-modal-intro__text">{{ __('Define una categoria clara y facil de reconocer para que despues sea simple elegirla al crear cada evento.') }}</p>
          </div>

          @if (!empty($langs) && count($langs) > 1)
            <div class="form-group category-form-group">
              <label for="">{{ __('Idioma') . '*' }}</label>
              <select name="language_id" class="form-control">
                <option selected disabled>{{ __('Selecciona un idioma') }}</option>
                @foreach ($langs as $lang)
                  <option value="{{ $lang->id }}">{{ $lang->name }}</option>
                @endforeach
              </select>
              <p id="err_language_id" class="mt-1 mb-0 text-danger em"></p>
            </div>
          @else
            <input type="hidden" name="language_id" value="{{ $language->id }}">
          @endif

          <div class="form-group category-form-group">
            <div class="category-image-box">
              <div class="category-image-box__header">
                <span class="category-image-box__title">{{ __('Imagen de la categoria') . '*' }}</span>
                <span class="category-image-box__text">{{ __('Sube una imagen clara y facil de reconocer. Se vera en el listado y ayuda a identificar cada categoria mas rapido.') }}</span>
              </div>

              <div class="category-image-box__body">
                <div class="thumb-preview">
                  <img src="{{ asset('assets/admin/img/noimage.jpg') }}" alt="..." class="uploaded-img">
                </div>

                <label class="category-image-upload">
                  <span class="category-image-upload__icon"><i class="fas fa-image"></i></span>
                  <span class="category-image-upload__copy">
                    <strong>{{ __('Elegir imagen') }}</strong>
                    <span>{{ __('Haz clic para subir la portada de la categoria') }}</span>
                  </span>
                  <input type="file" class="img-input" name="image">
                </label>
              </div>

              <p id="err_image" class="mt-3 mb-0 text-danger em"></p>
            </div>
          </div>

          <div class="form-group category-form-group">
            <label for="">{{ __('Nombre') . '*' }}</label>
            <input type="text" class="form-control" name="name" placeholder="{{ __('Ej: Musica en vivo') }}">
            <p id="err_name" class="mt-1 mb-0 text-danger em"></p>
          </div>

          <div class="form-group category-form-group">
            <label for="">{{ __('Estado') . '*' }}</label>
            <select name="status" class="form-control">
              <option selected disabled>{{ __('Selecciona un estado') }}</option>
              <option value="1">{{ __('Activa') }}</option>
              <option value="0">{{ __('Inactiva') }}</option>
            </select>
            <p id="err_status" class="mt-1 mb-0 text-danger em"></p>
          </div>

          <div class="form-group category-form-group">
            <label for="">{{ __('Destacada') . '*' }}</label>
            <select name="is_featured" class="form-control">
              <option selected disabled>{{ __('Selecciona si quieres destacarla') }}</option>
              <option value="yes">{{ __('Si') }}</option>
              <option value="no">{{ __('No') }}</option>
            </select>
            <p id="err_is_featured" class="mt-1 mb-0 text-danger em"></p>
          </div>

          <div class="form-group category-form-group">
            <label for="">{{ __('Orden de aparicion') . '*' }}</label>
            <input type="number" class="form-control ltr" name="serial_number"
              placeholder="{{ __('Ej: 10') }}">
            <p id="err_serial_number" class="mt-1 mb-0 text-danger em"></p>
            <p class="text-warning mt-2 mb-0">
              <small>{{ __('Mientras mas alto sea el numero, mas abajo aparecera la categoria en el listado.') }}</small>
            </p>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
          {{ __('Cerrar') }}
        </button>
        <button id="modalSubmit" type="button" class="btn btn-primary btn-sm">
          {{ __('Guardar categoria') }}
        </button>
      </div>
    </div>
  </div>
</div>
