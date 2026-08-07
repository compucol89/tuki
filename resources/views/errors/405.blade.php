@extends('frontend.layout')

@section('meta-robots', 'noindex,follow')
@section('pageHeading', __('Método no permitido'))
@section('meta-description', __('La solicitud no se pudo procesar. Volvé al inicio para seguir explorando eventos en Tukipass.'))
@section('canonical', url()->current())
@section('og-url', url()->current())
@section('og-type', 'website')

@section('content')
  <section class="error-area">
    <div class="container text-center padding-90">
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="error-content">
            <h1 class="h4 mb-4">{{ __('Tu reserva no se pudo procesar') }}</h1>
            <p class="mb-4">{{ __('Hubo un problema temporal con tu solicitud. Volvé al evento, actualizá la página e intentá reservar nuevamente.') }}</p>
            <ul class="list-unstyled">
              <li><a href="{{ url()->previous() ?: route('events') }}" class="theme-btn">{{ __('Volver a intentar') }}</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection
