@extends('frontend.layout')

@section('meta-robots', 'noindex,follow')
@section('pageHeading', __('Página no encontrada'))
@section('meta-description', __('La página que buscás no existe o fue movida. Volvé al inicio para seguir explorando eventos en Tukipass.'))
@section('canonical', url()->current())
@section('og-url', url()->current())
@section('og-type', 'website')

@section('content')
  <section class="error-area">
    <div class="container text-center padding-90">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <img src="{{ asset('assets/admin/img/404.png') }}" alt="{{ __('Página no encontrada') }}">
        </div>
        <div class="col-md-12">
          <div class="error-content">
            <h1 class="h4 mb-4">
              {{ __('404') }} — {{ __('Página no encontrada') }}
            </h1>
            <p class="mb-4">{{ __('La página que buscás no existe o fue movida.') }}</p>
            <ul class="list-unstyled">
              <li><a href="{{ route('index') }}" class="theme-btn">{{ __('Volver al inicio') }}</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection
