@extends('organizer.layout')

@section('content')
  <div class="page-header">
    <h1 class="page-title">{{ __('Cambiar contraseña') }}</h1>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{route('organizer.dashboard')}}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Cambiar contraseña') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-lg-12">
              <div class="card-title">{{ __('Cambiar contraseña') }}</div>
            </div>
          </div>
        </div>

        <div class="card-body">
          <div class="row">
            <div class="col-lg-6 offset-lg-3">
              <form id="ajaxEditForm" action="{{ route('organizer.update_password') }}" method="post">
                @csrf
                <div class="form-group">
                  <label for="current_password">{{ __('Contraseña actual') . '*' }}</label>
                  <input type="password" class="form-control" name="current_password" id="current_password">
                  <p id="editErr_current_password" class="mt-1 mb-0 text-danger em"></p>
                </div>

                <div class="form-group">
                  <label for="new_password">{{ __('Nueva contraseña') . '*' }}</label>
                  <input type="password" class="form-control" name="new_password" id="new_password">
                  <p id="editErr_new_password" class="mt-1 mb-0 text-danger em"></p>
                </div>

                <div class="form-group">
                  <label for="new_password_confirmation">{{ __('Confirmar nueva contraseña') . '*' }}</label>
                  <input type="password" class="form-control" name="new_password_confirmation" id="new_password_confirmation">
                  <p id="editErr_new_password_confirmation" class="mt-1 mb-0 text-danger em"></p>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="card-footer">
          <div class="row">
            <div class="col-12 text-center">
              <button type="submit" id="updateBtn" class="btn btn-success">
                {{ __('Actualizar') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
