@php
  $defaultLang = ($languages ?? collect())->firstWhere('is_default', 1) ?? ($languages ?? collect())->first();
  $defaultLangCode = $defaultLang->code ?? 'es';
  $mapElementId = $mapId ?? 'eventVenueMap';
@endphp
<div class="ev-venue-location mb-3"
  data-venue-map
  data-default-lang="{{ $defaultLangCode }}"
  data-geocode-url="{{ $geocodeUrl }}"
  data-map-id="{{ $mapElementId }}">
  <div class="form-group mb-2">
    <label class="font-weight-bold mb-1">{{ __('Ubicación en el mapa') }}</label>
    <p class="text-muted small mb-2">{{ __('Completá la dirección en el idioma principal y hacé clic en «Buscar en mapa». Podés arrastrar el marcador para afinar.') }}</p>
    <div class="d-flex flex-wrap align-items-center mb-2" style="gap: 8px;">
      <button type="button" class="btn btn-sm btn-primary js-venue-geocode-btn">{{ __('Buscar en mapa') }}</button>
      <span class="small text-muted js-venue-geocode-status" aria-live="polite"></span>
    </div>
    <div id="{{ $mapElementId }}" class="ev-venue-map" style="height: 280px; border-radius: 8px; background: #e9ecef;"></div>
  </div>
  <div class="row">
    <div class="col-md-6">
      <div class="form-group mb-md-0">
        <label>{{ __('Latitud') }}</label>
        <input type="text" name="latitude" class="form-control js-venue-latitude" placeholder="-34.6037"
          value="{{ old('latitude', $latitude ?? '') }}" readonly>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group mb-0">
        <label>{{ __('Longitud') }}</label>
        <input type="text" name="longitude" class="form-control js-venue-longitude" placeholder="-58.3816"
          value="{{ old('longitude', $longitude ?? '') }}" readonly>
      </div>
    </div>
  </div>
</div>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script src="{{ asset('assets/admin/js/event-venue-map.js') }}"></script>
