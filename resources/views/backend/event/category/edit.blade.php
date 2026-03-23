<div class="modal fade" id="editEventCategoryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">{{ __('Edit Event Category') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form id="ajaxEditForm" class="modal-form" action="{{ route('admin.event_management.update_category') }}" method="post">

          @method('PUT')
          @csrf
          <input type="hidden" id="in_id" name="id">

          <div class="category-modal-intro">
            <span class="category-modal-intro__eyebrow">{{ __('Revision') }}</span>
            <p class="category-modal-intro__text">{{ __('Ajusta el nombre, el estado o la imagen para que la categoria siga siendo clara y consistente con el resto del catalogo.') }}</p>
          </div>

          <div class="form-group category-form-group">
            <div class="category-image-box">
              <div class="category-image-box__header">
                <span class="category-image-box__title">{{ __('Imagen de la categoria') . '*' }}</span>
                <span class="category-image-box__text">{{ __('Puedes mantener la actual o reemplazarla por una imagen mas clara.') }}</span>
              </div>

              <div class="category-image-box__body">
                <div class="thumb-preview">
                  <img src="{{ asset('assets/admin/img/noimage.jpg') }}" alt="..." class="uploaded-img in_image">
                </div>

                <label class="category-image-upload">
                  <span class="category-image-upload__icon"><i class="fas fa-sync-alt"></i></span>
                  <span class="category-image-upload__copy">
                    <strong>{{ __('Reemplazar imagen') }}</strong>
                    <span>{{ __('Haz clic para actualizar la imagen de esta categoria') }}</span>
                  </span>
                  <input type="file" class="img-input" name="image">
                </label>
              </div>
            </div>
          </div>

          <div class="form-group category-form-group">
            <label for="">{{ __('Nombre') . '*' }}</label>
            <input type="text" id="in_name" class="form-control" name="name" placeholder="{{ __('Ej: Musica en vivo') }}">
            <p id="editErr_name" class="mt-1 mb-0 text-danger em"></p>
          </div>

          <div class="form-group category-form-group">
            <label for="">{{ __('Estado') . '*' }}</label>
            <select name="status" id="in_status" class="form-control">
              <option disabled>{{ __('Selecciona un estado') }}</option>
              <option value="1">{{ __('Activa') }}</option>
              <option value="0">{{ __('Inactiva') }}</option>
            </select>
            <p id="editErr_status" class="mt-1 mb-0 text-danger em"></p>
          </div>

          <div class="form-group category-form-group">
            <label for="">{{ __('Destacada') . '*' }}</label>
            <select name="is_featured" id="in_is_featured" class="form-control">
              <option disabled>{{ __('Selecciona si quieres destacarla') }}</option>
              <option value="yes">{{ __('Si') }}</option>
              <option value="no">{{ __('No') }}</option>
            </select>
            <p id="editErr__is_featured" class="mt-1 mb-0 text-danger em"></p>
          </div>

          <div class="form-group category-form-group">
            <label for="">{{ __('Orden de aparicion') . '*' }}</label>
            <input type="number" id="in_serial_number" class="form-control ltr" name="serial_number" placeholder="{{ __('Ej: 10') }}">
            <p id="editErr_serial_number" class="mt-1 mb-0 text-danger em"></p>
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
        <button id="updateBtn" type="button" class="btn btn-primary btn-sm">
          {{ __('Guardar cambios') }}
        </button>
      </div>
    </div>
  </div>
</div>
