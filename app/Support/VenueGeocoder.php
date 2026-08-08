<?php

namespace App\Support;

use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

final class VenueGeocoder
{
  private const USER_AGENT_PREFIX = 'TukipassEventGeocoder/1.0';

  public static function userAgent(): string
  {
    $email = trim((string) \App\Models\BasicSettings\Basic::query()->value('email_address'));

    return self::USER_AGENT_PREFIX . ' (' . ($email !== '' ? $email : (string) config('tukipass.fiscal.support_email')) . ')';
  }

  public static function buildAddressQueryFromRequest(Request $request): string
  {
    $languages = Language::all();
    $default = $languages->firstWhere('is_default', 1) ?? $languages->first();

    if (!$default) {
      return '';
    }

    $code = $default->code;

    $parts = array_filter([
      trim((string) $request->input($code . '_address')),
      trim((string) $request->input($code . '_city')),
      trim((string) $request->input($code . '_state')),
      trim((string) $request->input($code . '_country')),
    ]);

    return implode(', ', $parts);
  }

  public static function geocode(string $query): ?array
  {
    $query = trim($query);

    if ($query === '') {
      return null;
    }

    try {
      $response = Http::withHeaders([
        'User-Agent' => self::userAgent(),
        'Accept-Language' => 'es',
      ])->timeout(10)->get('https://nominatim.openstreetmap.org/search', [
        'q' => $query,
        'format' => 'json',
        'limit' => 1,
      ]);
    } catch (\Throwable $e) {
      return null;
    }

    if (!$response->successful()) {
      return null;
    }

    $results = $response->json();

    if (!is_array($results) || empty($results[0])) {
      return null;
    }

    $first = $results[0];

    if (!isset($first['lat'], $first['lon'])) {
      return null;
    }

    return [
      'lat' => (string) $first['lat'],
      'lon' => (string) $first['lon'],
      'display_name' => (string) ($first['display_name'] ?? ''),
    ];
  }
}
