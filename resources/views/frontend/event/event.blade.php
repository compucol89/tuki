@extends('frontend.layout')
@section('pageHeading')
  @if (!empty($pageHeading))
    {{ $pageHeading->event_page_title ?? __('Eventos') }}
  @else
    {{ __('Eventos') }}
  @endif
@endsection

@php
  $metaKeywords = !empty($seo->meta_keyword_event) ? $seo->meta_keyword_event : '';
  $metaDescription = !empty($seo->meta_description_event) ? $seo->meta_description_event : '';
@endphp
@section('meta-keywords', "{{ $metaKeywords }}")
@section('meta-description', "$metaDescription")

@section('hero-section')
  <section class="page-banner overlay pt-120 pb-125 rpt-90 rpb-95 lazy"
    data-bg="{{ asset('assets/admin/img/' . $basicInfo->breadcrumb) }}">
    <div class="container">
      <div class="banner-inner">
        <h2 class="page-title">
          @if (!empty($pageHeading))
            {{ $pageHeading->event_page_title ?? __('Eventos') }}
          @else
            {{ __('Eventos') }}
          @endif
        </h2>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">{{ __('Home') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Eventos') }}</li>
          </ol>
        </nav>
      </div>
    </div>
  </section>
@endsection

@section('content')
<section class="ev-listing py-70">
  <div class="container">
    <div class="row g-4">

      {{-- ── SIDEBAR FILTROS ── --}}
      <div class="col-lg-3">
        <div class="ev-filters">

          {{-- Búsqueda --}}
          <div class="ev-filter-card">
            <p class="ev-filter-label">Buscar</p>
            <form action="{{ route('events') }}" class="ev-search-form">
              @foreach(['category','event','min','max','location','dates','pricing'] as $param)
                @if(request()->filled($param))
                  <input type="hidden" name="{{ $param }}" value="{{ request()->input($param) }}">
                @endif
              @endforeach
              <div class="ev-search-wrap">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="search-input"
                  value="{{ request()->input('search-input','') }}"
                  placeholder="Nombre, lugar...">
              </div>
              <button type="submit" class="ev-filter-apply-btn">Buscar</button>
            </form>
          </div>

          {{-- Ubicación --}}
          <div class="ev-filter-card">
            <p class="ev-filter-label">Ubicación</p>
            <form action="{{ route('events') }}" class="ev-location-form">
              @foreach(['search-input','category','event','min','max','dates','pricing'] as $param)
                @if(request()->filled($param))
                  <input type="hidden" name="{{ $param }}" value="{{ request()->input($param) }}">
                @endif
              @endforeach
              <div class="ev-search-wrap">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <input type="text" name="location"
                  value="{{ request()->input('location','') }}"
                  placeholder="Ciudad o país...">
              </div>
              <button type="submit" class="ev-filter-apply-btn">Aplicar</button>
            </form>
          </div>

          {{-- Fecha --}}
          <div class="ev-filter-card">
            <p class="ev-filter-label">Rango de fechas</p>
            <div class="ev-search-wrap">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              <input type="text" name="daterange"
                @if(request()->input('dates')) value="{{ request()->input('dates') }}" @endif
                placeholder="Desde — Hasta">
            </div>
          </div>

          {{-- Categoría --}}
          <div class="ev-filter-card">
            <p class="ev-filter-label">Categoría</p>
            <form action="{{ route('events') }}" id="catForm">
              @if(request()->filled('search-input'))
                <input type="hidden" name="search-input" value="{{ request()->input('search-input') }}">
              @endif
              @foreach(['location','event','min','max','dates','pricing'] as $param)
                @if(request()->filled($param))
                  <input type="hidden" name="{{ $param }}" value="{{ request()->input($param) }}">
                @endif
              @endforeach
              <select id="category" name="category" class="ev-select">
                <option value="">Todas las categorías</option>
                @foreach ($information['categories'] as $item)
                  <option {{ request()->input('category') == $item->slug ? 'selected' : '' }}
                    value="{{ $item->slug }}">{{ $item->name }}</option>
                @endforeach
              </select>
            </form>
          </div>

          {{-- Tipo de evento --}}
          <div class="ev-filter-card">
            <p class="ev-filter-label">Tipo</p>
            <div class="ev-type-pills">
              @php $baseParams = request()->except('event'); @endphp
              <a href="{{ route('events', array_merge($baseParams, ['event' => ''])) }}"
                 class="ev-type-pill {{ !request()->filled('event') ? 'active' : '' }}">Todos</a>
              <a href="{{ route('events', array_merge($baseParams, ['event' => 'online'])) }}"
                 class="ev-type-pill {{ request()->input('event') == 'online' ? 'active' : '' }}">Online</a>
              <a href="{{ route('events', array_merge($baseParams, ['event' => 'venue'])) }}"
                 class="ev-type-pill {{ request()->input('event') == 'venue' ? 'active' : '' }}">Presencial</a>
            </div>
          </div>

          {{-- Precio --}}
          <div class="ev-filter-card">
            <p class="ev-filter-label">Precio</p>
            <div class="price-slider-range" id="range-slider"></div>
            <div class="ev-price-row">
              <input type="text" dir="ltr" id="price" value="{{ request()->input('min') }}" readonly class="ev-price-input">
              <button class="ev-filter-apply-btn" id="slider_submit">Filtrar</button>
            </div>
          </div>

          @if (!empty(showAd(2)))
            <div class="text-center mt-3">{!! showAd(2) !!}</div>
          @endif

        </div>{{-- /.ev-filters --}}
      </div>

      {{-- ── CONTENIDO ── --}}
      <div class="col-lg-9">

        {{-- Barra superior: pricing pills + contador --}}
        <div class="ev-topbar mb-4">
          @php
            $currentPricing = request()->input('pricing', '');
            $baseParams = request()->except('pricing');
            $totalEvents = $information['events']->total();
          @endphp
          <div class="ev-pricing-pills">
            <a href="{{ route('events', array_merge($baseParams, ['pricing' => ''])) }}"
               class="ev-pricing-pill {{ $currentPricing === '' ? 'active' : '' }}">Todos</a>
            <a href="{{ route('events', array_merge($baseParams, ['pricing' => 'free'])) }}"
               class="ev-pricing-pill {{ $currentPricing === 'free' ? 'active' : '' }}">Gratis</a>
            <a href="{{ route('events', array_merge($baseParams, ['pricing' => 'paid'])) }}"
               class="ev-pricing-pill {{ $currentPricing === 'paid' ? 'active' : '' }}">De pago</a>
          </div>
          <span class="ev-count">{{ $totalEvents }} {{ $totalEvents == 1 ? 'evento' : 'eventos' }}</span>
        </div>

        {{-- Grid --}}
        <div class="row g-4">
          @if (count($information['events']) > 0)
            @foreach ($information['events'] as $event)
              <div class="col-sm-6 col-xl-4">
                @include('frontend.partials.event-card')
              </div>
            @endforeach
          @else
            <div class="col-12">
              <div class="ev-empty">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <p>No se encontraron eventos</p>
                <a href="{{ route('events') }}" class="ev-empty-link">Limpiar filtros</a>
              </div>
            </div>
          @endif
        </div>

        {{-- Paginación --}}
        <div class="ev-pagination mt-4">
          {{ $information['events']->links() }}
        </div>

        @if (!empty(showAd(3)))
          <div class="text-center mt-4">{!! showAd(3) !!}</div>
        @endif

      </div>
    </div>
  </div>
</section>

{{-- Formulario hidden para filtros JS (precio, fecha) --}}
<form id="filtersForm" class="d-none" action="{{ route('events') }}" method="GET">
  <input type="hidden" id="category-id" name="category" value="{{ request()->input('category','') }}">
  <input type="hidden" id="event" name="event" value="{{ request()->input('event','') }}">
  <input type="hidden" id="min-id" name="min" value="{{ request()->input('min','') }}">
  <input type="hidden" id="max-id" name="max" value="{{ request()->input('max','') }}">
  <input type="hidden" name="search-input" value="{{ request()->input('search-input','') }}">
  <input type="hidden" name="location" value="{{ request()->input('location','') }}">
  <input type="hidden" id="dates-id" name="dates" value="{{ request()->input('dates','') }}">
  <input type="hidden" name="pricing" value="{{ request()->input('pricing','') }}">
  <button type="submit" id="submitBtn"></button>
</form>
@endsection

@section('custom-script')
  <script type="text/javascript" src="{{ asset('assets/front/js/moment.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/front/js/daterangepicker.min.js') }}"></script>

  <script>
    let min_price = {!! htmlspecialchars($information['min']) !!};
    let max_price = {!! htmlspecialchars($information['max']) !!};
    let symbol = "{!! htmlspecialchars($basicInfo->base_currency_symbol) !!}";
    let position = "{!! htmlspecialchars($basicInfo->base_currency_symbol_position) !!}";
    let curr_min = {!! !empty(request()->input('min')) ? htmlspecialchars(request()->input('min')) : 5 !!};
    let curr_max = {!! !empty(request()->input('max')) ? htmlspecialchars(request()->input('max')) : 800 !!};
  </script>

  <script src="{{ asset('assets/front/js/custom_script.js') }}"></script>
@endsection
