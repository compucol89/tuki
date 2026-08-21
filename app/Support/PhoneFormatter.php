<?php

namespace App\Support;

/**
 * F-009 — normalización de presentación de teléfonos (valores de
 * basic_settings.contact_numbers, formato local argentino).
 *
 *  display('1139451837')  → '+54 11 3945-1837'
 *  tel('1139451837')      → 'tel:+541139451837'
 *  wa('1139451837')       → 'https://wa.me/541139451837'
 */
final class PhoneFormatter
{
  public static function display(string $phone): string
  {
    $e164 = self::toE164($phone);

    if (preg_match('/^54(11)(\d{4})(\d{4})$/', $e164, $m)) {
      return sprintf('+54 %s %s-%s', $m[1], $m[2], $m[3]);
    }

    if (preg_match('/^54(\d{2})(\d{4})(\d{4})$/', $e164, $m)) {
      return sprintf('+54 %s %s-%s', $m[1], $m[2], $m[3]);
    }

    return trim((string) $phone);
  }

  public static function tel(string $phone): string
  {
    return 'tel:+' . self::toE164($phone);
  }

  public static function wa(string $phone): string
  {
    return 'https://wa.me/' . self::toE164($phone);
  }

  public static function toE164(string $phone): string
  {
    $digits = preg_replace('/\D+/', '', (string) $phone) ?? '';

    if (str_starts_with($digits, '54')) {
      return $digits;
    }

    return '54' . $digits;
  }
}
