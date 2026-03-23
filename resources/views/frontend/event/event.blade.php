@extends('frontend.layout')
@section('pageHeading', 'Eventos en Argentina · Entradas y Tickets Online')

@php
  $metaKeywords    = !empty($seo->meta_keyword_event)    ? $seo->meta_keyword_event    : 'eventos, entradas, tickets, conciertos, shows, teatro, Argentina';
  $metaDescription = !empty($seo->meta_description_event) ? $seo->meta_description_event : 'Encontrá los mejores eventos en Argentina. Comprá entradas y tickets online de forma fácil, rápida y segura en Tukipass.';
  $ogImage = asset('assets/admin/img/' . $basicInfo->breadcrumb);
@endphp

@section('meta-keywords',    "{{ $metaKeywords }}")
@section('meta-description', "$metaDescription")
@section('og-title',       'Eventos en Argentina · Entradas y Tickets Online | Tukipass')
@section('og-description', $metaDescription)
@section('og-image',       $ogImage)
@section('og-type',        'website')

{{-- ─── HERO ─── --}}
@section('hero-section')
<section class="hero-section hero-collage-section" id="heroSection">

  <div class="hero-slideshow" id="heroCollageBg">
    @foreach($heroGalleryImages->values() as $thumb)
      <div class="hero-slide" style="background-image:url('{{ asset('assets/admin/img/event-gallery/' . $thumb) }}');"></div>
    @endforeach
  </div>

  <div class="hero-overlay"></div>
  <div class="hero-noise"></div>

  <div class="container hero-content-wrapper">
    <div class="hero-content">
      <h1>Todos los eventos,<br>en un solo lugar</h1>
      <p>Conciertos, shows, teatro, deportes y más. Comprá tus entradas fácil, rápido y seguro.</p>
    </div>
  </div>

</section>
@endsection

{{-- ─── CONTENT ─── --}}
@section('content')

{{-- Barra de filtros --}}
<div class="evf-bar-wrap">
  <div class="container">
    <form action="{{ route('events') }}" method="GET" class="evf-bar" id="evfForm">

      {{-- Búsqueda --}}
      <div class="evf-field evf-field--grow">
        <svg class="evf-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" name="search-input" class="evf-input"
               value="{{ request('search-input') }}"
               placeholder="Buscá por nombre de evento...">
      </div>

      {{-- Categoría --}}
      <div class="evf-field evf-field--select">
        <svg class="evf-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
        <select name="category" class="evf-select">
          <option value="">Todas las categorías</option>
          @foreach ($categories as $cat)
            <option value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
          @endforeach
        </select>
      </div>

      {{-- Ubicación --}}
      <div class="evf-field evf-field--loc">
        <svg class="evf-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
        <input type="text" name="location" class="evf-input"
               value="{{ request('location') }}"
               placeholder="Ciudad o país...">
      </div>

      {{-- Fecha --}}
      <div class="evf-field evf-field--date">
        <svg class="evf-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <input type="text" name="dates" id="evf-dates" class="evf-input"
               value="{{ request('dates') }}"
               placeholder="Fecha..." autocomplete="off" readonly>
      </div>

      {{-- Botón buscar --}}
      <button type="submit" class="evf-btn">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        Buscar
      </button>

      @if(request()->hasAny(['search-input','category','location','dates','event','pricing','min','max']))
        <a href="{{ route('events') }}" class="evf-clear" title="Limpiar filtros">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </a>
      @endif

    </form>

    {{-- Pills secundarias --}}
    <div class="evf-pills-row">
      @php
        $base = request()->except(['event','pricing']);
      @endphp

      {{-- Tipo --}}
      <div class="evf-pills-group">
        <span class="evf-pills-label">Tipo:</span>
        <a href="{{ route('events', array_merge($base, ['event'=>''])) }}"
           class="evf-pill {{ !request('event') ? 'active' : '' }}">Todos</a>
        <a href="{{ route('events', array_merge($base, ['event'=>'online'])) }}"
           class="evf-pill {{ request('event')=='online' ? 'active' : '' }}">Online</a>
        <a href="{{ route('events', array_merge($base, ['event'=>'venue'])) }}"
           class="evf-pill {{ request('event')=='venue' ? 'active' : '' }}">Presencial</a>
      </div>

      {{-- Precio --}}
      <div class="evf-pills-group">
        <span class="evf-pills-label">Precio:</span>
        <a href="{{ route('events', array_merge(request()->except('pricing'), ['pricing'=>''])) }}"
           class="evf-pill {{ !request('pricing') ? 'active' : '' }}">Todos</a>
        <a href="{{ route('events', array_merge(request()->except('pricing'), ['pricing'=>'free'])) }}"
           class="evf-pill evf-pill--free {{ request('pricing')=='free' ? 'active' : '' }}">Gratis</a>
        <a href="{{ route('events', array_merge(request()->except('pricing'), ['pricing'=>'paid'])) }}"
           class="evf-pill {{ request('pricing')=='paid' ? 'active' : '' }}">De pago</a>
      </div>

      {{-- Contador --}}
      <span class="evf-count">
        {{ $information['events']->total() }}
        {{ $information['events']->total() == 1 ? 'evento' : 'eventos' }}
      </span>
    </div>

  </div>
</div>

{{-- Grid de eventos --}}
<section class="ev-listing py-60">
  <div class="container">

    @if (count($information['events']) > 0)
      <div class="row">
        @foreach ($information['events'] as $event)
          <div class="col-md-6 col-lg-4 ev-card-col">
            @include('frontend.partials.event-card')
          </div>
        @endforeach
      </div>
    @else
      <div class="ev-empty">
        <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <p>No se encontraron eventos con esos filtros</p>
        <a href="{{ route('events') }}" class="evf-btn">Limpiar filtros</a>
      </div>
    @endif

    {{-- Paginación --}}
    <div class="ev-pagination mt-5">
      {{ $information['events']->links() }}
    </div>

    @if (!empty(showAd(3)))
      <div class="text-center mt-4">{!! showAd(3) !!}</div>
    @endif

  </div>
</section>

{{-- Form oculto para filtros JS (precio) --}}
<form id="filtersForm" class="d-none" action="{{ route('events') }}" method="GET">
  <input type="hidden" id="category-id"  name="category"    value="{{ request('category','') }}">
  <input type="hidden" id="event"         name="event"       value="{{ request('event','') }}">
  <input type="hidden" id="min-id"        name="min"         value="{{ request('min','') }}">
  <input type="hidden" id="max-id"        name="max"         value="{{ request('max','') }}">
  <input type="hidden"                    name="search-input" value="{{ request('search-input','') }}">
  <input type="hidden"                    name="location"    value="{{ request('location','') }}">
  <input type="hidden" id="dates-id"      name="dates"       value="{{ request('dates','') }}">
  <input type="hidden"                    name="pricing"     value="{{ request('pricing','') }}">
  <button type="submit" id="submitBtn"></button>
</form>

@endsection

@section('custom-script')
<script src="{{ asset('assets/front/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/front/js/daterangepicker.min.js') }}"></script>
<script>
  // Slideshow hero
  (function() {
    var slides = Array.from(document.querySelectorAll('#heroCollageBg .hero-slide'));
    var n = slides.length;
    if (n === 0) return;
    slides[0].style.transition = 'none';
    slides[0].style.opacity    = '1';
    slides[0].style.zIndex     = '0';
    if (n === 1) return;
    var cur = 0;
    setInterval(function() {
      var nxt = (cur + 1) % n;
      slides[cur].style.zIndex = '0';
      slides[nxt].style.zIndex = '1';
      slides[nxt].style.transition = 'opacity 1.2s ease-in-out';
      slides[nxt].style.opacity    = '1';
      var prev = cur;
      setTimeout(function() {
        slides[prev].style.transition = 'none';
        slides[prev].style.opacity    = '0';
      }, 1200);
      cur = nxt;
    }, 5000);
    var hero = document.getElementById('heroSection');
    var bg   = document.getElementById('heroCollageBg');
    if (!hero || !bg) return;
    var tx = 0, ty = 0, cx = 0, cy = 0;
    hero.addEventListener('mousemove', function(e) {
      var r = hero.getBoundingClientRect();
      tx = -(e.clientX - r.left) / r.width  * 14 + 7;
      ty = -(e.clientY - r.top)  / r.height * 14 + 7;
    });
    hero.addEventListener('mouseleave', function() { tx = 0; ty = 0; });
    (function loop() {
      cx += (tx - cx) * 0.04;
      cy += (ty - cy) * 0.04;
      bg.style.transform = 'translate(' + cx.toFixed(2) + 'px,' + cy.toFixed(2) + 'px)';
      requestAnimationFrame(loop);
    })();
  })();

  // Daterangepicker
  $('#evf-dates').daterangepicker({
    autoUpdateInput: false,
    locale: { cancelLabel: 'Limpiar', applyLabel: 'Aplicar', format: 'YYYY-MM-DD', separator: ' a ' }
  });
  $('#evf-dates').on('apply.daterangepicker', function(ev, picker) {
    $(this).val(picker.startDate.format('YYYY-MM-DD') + ' a ' + picker.endDate.format('YYYY-MM-DD'));
  });
  $('#evf-dates').on('cancel.daterangepicker', function() { $(this).val(''); });
</script>
@endsection
