@extends('frontend.layout')
@section('pageHeading', 'Editar perfil')
@section('hero-section')
  <section class="page-banner overlay pt-120 pb-125 rpt-90 rpb-95 lazy"
    data-bg="{{ asset('assets/admin/img/' . $basicInfo->breadcrumb) }}">
    <div class="container">
      <div class="banner-inner">
        <h2 class="page-title">Editar perfil</h2>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active">Editar perfil</li>
          </ol>
        </nav>
      </div>
    </div>
  </section>
@endsection

@section('content')
@php $user = Auth::guard('customer')->user(); @endphp

<section class="cd-page py-60">
  <div class="container">
    <div class="row g-4">

      @includeIf('frontend.customer.partials.sidebar')

      <div class="col-lg-9">

        @if (Session::has('success'))
          <div class="ep-alert ep-alert--success mb-4">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ Session::get('success') }}
          </div>
        @endif

        <form action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data">
          @csrf

          {{-- Avatar --}}
          <div class="cd-card mb-4">
            <div class="cd-card__head">
              <h3 class="cd-card__title">Foto de perfil</h3>
            </div>
            <div class="ep-card-body">
            <div class="ep-avatar-wrap">
              <div class="ep-avatar">
                @if ($user->photo)
                  <img id="avatarPreview" src="{{ asset('assets/admin/img/customer-profile/' . $user->photo) }}" alt="Avatar">
                @else
                  <img id="avatarPreview" src="{{ asset('assets/front/images/profile.jpg') }}" alt="Avatar">
                @endif
                <label for="imageUpload" class="ep-avatar__edit" title="Cambiar foto">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </label>
                <input type="file" name="photo" id="imageUpload" accept="image/*" class="d-none">
              </div>
              <div class="ep-avatar__info">
                <p class="ep-avatar__name">{{ $user->fname }} {{ $user->lname }}</p>
                <p class="ep-avatar__hint">JPG, PNG o GIF · Máx. 2 MB</p>
                @error('photo')<p class="ep-field__error">{{ $message }}</p>@enderror
              </div>
            </div>
            </div>{{-- /ep-card-body --}}
          </div>

          {{-- Personal data --}}
          <div class="cd-card mb-4">
            <div class="cd-card__head">
              <h3 class="cd-card__title">Datos personales</h3>
            </div>
            <div class="ep-card-body">
            <div class="ep-form-grid">

              <div class="ep-field">
                <label class="ep-field__label">Nombre <span class="ep-field__req">*</span></label>
                <input type="text" name="fname" required
                       class="ep-field__input @error('fname') is-invalid @enderror"
                       value="{{ old('fname', $user->fname) }}"
                       placeholder="Tu nombre">
                @error('fname')<p class="ep-field__error">{{ $message }}</p>@enderror
              </div>

              <div class="ep-field">
                <label class="ep-field__label">Apellido <span class="ep-field__req">*</span></label>
                <input type="text" name="lname" required
                       class="ep-field__input @error('lname') is-invalid @enderror"
                       value="{{ old('lname', $user->lname) }}"
                       placeholder="Tu apellido">
                @error('lname')<p class="ep-field__error">{{ $message }}</p>@enderror
              </div>

              <div class="ep-field">
                <label class="ep-field__label">Email <span class="ep-field__req">*</span></label>
                <input type="email" name="email"
                       class="ep-field__input @error('email') is-invalid @enderror"
                       value="{{ old('email', $user->email) }}"
                       placeholder="tu@email.com">
                @error('email')<p class="ep-field__error">{{ $message }}</p>@enderror
              </div>

              <div class="ep-field">
                <label class="ep-field__label">Usuario <span class="ep-field__req">*</span></label>
                <input type="text" name="username"
                       class="ep-field__input @error('username') is-invalid @enderror"
                       value="{{ old('username', $user->username) }}"
                       placeholder="tu_usuario">
                @error('username')<p class="ep-field__error">{{ $message }}</p>@enderror
              </div>

              <div class="ep-field">
                <label class="ep-field__label">Teléfono</label>
                <input type="text" name="phone"
                       class="ep-field__input"
                       value="{{ old('phone', $user->phone) }}"
                       placeholder="+54 11 XXXX-XXXX">
              </div>

              <div class="ep-field">
                <label class="ep-field__label">País</label>
                <input type="text" name="country"
                       class="ep-field__input"
                       value="{{ old('country', $user->country) }}"
                       placeholder="Argentina">
              </div>

              <div class="ep-field">
                <label class="ep-field__label">Provincia</label>
                <input type="text" name="state"
                       class="ep-field__input"
                       value="{{ old('state', $user->state) }}"
                       placeholder="Buenos Aires">
              </div>

              <div class="ep-field">
                <label class="ep-field__label">Ciudad</label>
                <input type="text" name="city"
                       class="ep-field__input"
                       value="{{ old('city', $user->city) }}"
                       placeholder="Ciudad">
              </div>

              <div class="ep-field">
                <label class="ep-field__label">Código postal</label>
                <input type="text" name="zip_code"
                       class="ep-field__input"
                       value="{{ old('zip_code', $user->zip_code) }}"
                       placeholder="1234">
              </div>

              <div class="ep-field ep-field--full">
                <label class="ep-field__label">Dirección</label>
                <textarea name="address" rows="3"
                          class="ep-field__input ep-field__textarea"
                          placeholder="Calle, número, piso...">{{ old('address', $user->address) }}</textarea>
              </div>

            </div>
            </div>{{-- /ep-card-body --}}
          </div>

          {{-- Submit --}}
          <div class="ep-actions">
            <button type="submit" class="ep-btn-save">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
              Guardar cambios
            </button>
          </div>

        </form>
      </div>
    </div>
  </div>
</section>

@push('scripts')
<script>
  document.getElementById('imageUpload').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(ev) {
      document.getElementById('avatarPreview').src = ev.target.result;
    };
    reader.readAsDataURL(file);
  });
</script>
@endpush
@endsection
