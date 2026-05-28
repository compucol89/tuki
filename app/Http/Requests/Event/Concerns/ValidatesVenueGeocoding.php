<?php

namespace App\Http\Requests\Event\Concerns;

use App\Support\VenueGeocoder;
use Illuminate\Contracts\Validation\Validator;

trait ValidatesVenueGeocoding
{
  protected function validateVenueGeocoding(Validator $validator): void
  {
    if ($this->input('event_type') !== 'venue') {
      return;
    }

    if ($this->filled('latitude') && $this->filled('longitude')) {
      return;
    }

    $query = VenueGeocoder::buildAddressQueryFromRequest($this);

    if ($query === '') {
      $validator->errors()->add('latitude', 'Completá la dirección o buscá la ubicación en el mapa.');
      return;
    }

    $coords = VenueGeocoder::geocode($query);

    if ($coords === null) {
      $validator->errors()->add('latitude', 'No pudimos ubicar esa dirección. Ajustala o marcá el lugar en el mapa.');
      return;
    }

    $this->merge([
      'latitude' => $coords['lat'],
      'longitude' => $coords['lon'],
    ]);
  }
}
