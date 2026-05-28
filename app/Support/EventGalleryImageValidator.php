<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

final class EventGalleryImageValidator
{
  public const ALLOWED_EXTENSIONS = ['jpg', 'png', 'jpeg', 'webp'];

  public static function validateUploadedFile(UploadedFile $img, callable $fail): bool
  {
    $ext = strtolower($img->getClientOriginalExtension());

    if (!in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
      $fail('Solo se permiten imagenes JPG, PNG o WebP.');

      return false;
    }

    $path = $img->getRealPath();
    $image = self::loadImageResource($path, $ext);

    if ($image === false) {
      Log::warning('Gallery upload: could not read image', [
        'extension' => $ext,
        'size' => $img->getSize(),
        'original_name' => $img->getClientOriginalName(),
      ]);
      $fail('No pudimos leer la imagen que intentaste subir.');

      return false;
    }

    $width = imagesx($image);
    $height = imagesy($image);
    imagedestroy($image);

    $longestSide = max($width, $height);
    $shortestSide = min($width, $height);

    if ($longestSide < 600 || $shortestSide < 450) {
      $fail('La imagen debe tener al menos 600 px en su lado mas largo y 450 px en su lado mas corto. Puede ser horizontal, cuadrada o vertical.');

      return false;
    }

    return true;
  }

  /**
   * @return \GdImage|false
   */
  public static function loadImageResource(string $path, string $ext)
  {
    $header = @file_get_contents($path, false, null, 0, 12);

    if (is_string($header)) {
      if (str_starts_with($header, "\xFF\xD8\xFF")) {
        $jpeg = @imagecreatefromjpeg($path);
        if ($jpeg !== false) {
          return $jpeg;
        }
      }

      if (str_starts_with($header, "\x89PNG\r\n\x1a\n")) {
        $png = @imagecreatefrompng($path);
        if ($png !== false) {
          return $png;
        }
      }

      if (
        strlen($header) >= 12
        && substr($header, 0, 4) === 'RIFF'
        && substr($header, 8, 4) === 'WEBP'
        && function_exists('imagecreatefromwebp')
      ) {
        $webp = @imagecreatefromwebp($path);
        if ($webp !== false) {
          return $webp;
        }
      }
    }

    $imageSize = @getimagesize($path);

    if ($imageSize !== false) {
      $loaded = match ($imageSize[2] ?? null) {
        IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
        IMAGETYPE_PNG => @imagecreatefrompng($path),
        IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
        default => false,
      };

      if ($loaded !== false) {
        return $loaded;
      }
    }

    return match ($ext) {
      'jpg', 'jpeg' => @imagecreatefromjpeg($path) ?: false,
      'png' => @imagecreatefrompng($path) ?: false,
      'webp' => function_exists('imagecreatefromwebp') ? (@imagecreatefromwebp($path) ?: false) : false,
      default => false,
    };
  }
}
