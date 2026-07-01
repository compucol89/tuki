@extends('frontend.layout')

@section('meta-robots', 'noindex,follow')
@section('pageHeading', __('Tu reserva expiró'))
@section('meta-description', __('La sesión de reserva expiró por seguridad. Actualizá la página del evento e intentá nuevamente.'))
@section('canonical', url()->current())
@section('og-url', url()->current())
@section('og-type', 'website')

@section('content')
  <section class="error-area">
    <div class="container text-center padding-90">
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="error-content">
            <h1 class="h4 mb-4">{{ __('Tu reserva expiró') }}</h1>
            <p class="mb-4">{{ __('Por seguridad, actualizá la página del evento y volvé a intentar la reserva.') }}</p>
            <ul class="list-unstyled">
              <li><a href="{{ url()->previous() ?: route('events') }}" class="theme-btn">{{ __('Volver a intentar') }}</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection
