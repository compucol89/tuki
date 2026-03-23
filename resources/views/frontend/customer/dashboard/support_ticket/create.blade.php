@extends('frontend.layout')
@section('pageHeading', 'Nuevo ticket de soporte')
@section('hero-section')
  <section class="page-banner overlay pt-120 pb-125 rpt-90 rpb-95 lazy"
    data-bg="{{ asset('assets/admin/img/' . $basicInfo->breadcrumb) }}">
    <div class="container">
      <div class="banner-inner">
        <h2 class="page-title">Nuevo ticket de soporte</h2>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customer.support_tickert') }}">Soporte</a></li>
            <li class="breadcrumb-item active">Nuevo ticket</li>
          </ol>
        </nav>
      </div>
    </div>
  </section>
@endsection

@section('content')
<section class="cd-page py-60">
  <div class="container">
    <div class="row g-4">

      @includeIf('frontend.customer.partials.sidebar')

      <div class="col-lg-9">

        @if (Session::has('success'))
          <div class="ep-alert ep-alert--success mb-4">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ Session::get('success') }}
          </div>
        @endif

        <div class="cd-card">
          <div class="cd-card__head">
            <div style="display:flex;align-items:center;gap:10px;">
              <div class="cp-icon-wrap">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
              </div>
              <h3 class="cd-card__title">Abrir nuevo ticket</h3>
            </div>
            <a href="{{ route('customer.support_tickert') }}" class="cd-bk-back-btn">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
              Mis tickets
            </a>
          </div>

          <div class="ep-card-body">
            <form action="{{ route('customer.support_ticket.store') }}" method="POST" enctype="multipart/form-data">
              @csrf

              <div class="ep-form-grid mb-4">

                <div class="ep-field">
                  <label class="ep-field__label">Email <span class="ep-field__req">*</span></label>
                  <div class="cp-input-wrap">
                    <svg class="cp-input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    <input type="email" name="email" required
                           class="ep-field__input ep-field__input--icon"
                           value="{{ Auth::guard('customer')->user()->email }}"
                           placeholder="tu@email.com">
                  </div>
                </div>

                <div class="ep-field">
                  <label class="ep-field__label">Asunto <span class="ep-field__req">*</span></label>
                  <div class="cp-input-wrap">
                    <svg class="cp-input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="21" y1="6" x2="3" y2="6"/><line x1="15" y1="12" x2="3" y2="12"/><line x1="17" y1="18" x2="3" y2="18"/></svg>
                    <input type="text" name="subject" required
                           class="ep-field__input ep-field__input--icon @error('subject') is-invalid @enderror"
                           placeholder="¿En qué podemos ayudarte?"
                           value="{{ old('subject') }}">
                  </div>
                  @error('subject')<p class="ep-field__error">{{ $message }}</p>@enderror
                </div>

                <div class="ep-field ep-field--full">
                  <label class="ep-field__label">Descripción</label>
                  <textarea name="description" rows="5"
                            class="ep-field__input ep-field__textarea @error('description') is-invalid @enderror"
                            placeholder="Describí tu consulta o problema con el mayor detalle posible...">{{ old('description') }}</textarea>
                  @error('description')<p class="ep-field__error">{{ $message }}</p>@enderror
                </div>

                <div class="ep-field ep-field--full">
                  <label class="ep-field__label">Adjunto <span class="ep-field__opt">(opcional)</span></label>
                  <div class="st-file-wrap">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                    <span class="st-file-label">Seleccionar archivo</span>
                    <input type="file" name="attachment" class="st-file-input">
                  </div>
                  @error('attachment')<p class="ep-field__error mt-1">{{ $message }}</p>@enderror
                </div>

              </div>

              <div class="ep-actions">
                <button type="submit" class="ep-btn-save">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                  Enviar ticket
                </button>
              </div>

            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>
@endsection
