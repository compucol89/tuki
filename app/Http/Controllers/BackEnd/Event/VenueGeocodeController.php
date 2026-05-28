<?php

namespace App\Http\Controllers\BackEnd\Event;

use App\Http\Controllers\Controller;
use App\Support\VenueGeocoder;
use Illuminate\Http\Request;

class VenueGeocodeController extends Controller
{
  public function __invoke(Request $request)
  {
    $request->validate([
      'q' => 'required|string|max:500',
    ]);

    $result = VenueGeocoder::geocode($request->input('q'));

    if ($result === null) {
      return response()->json([
        'ok' => false,
        'message' => 'No encontramos esa dirección. Probá con calle, ciudad y país.',
      ], 422);
    }

    return response()->json([
      'ok' => true,
      'lat' => $result['lat'],
      'lon' => $result['lon'],
      'display_name' => $result['display_name'],
    ]);
  }
}
