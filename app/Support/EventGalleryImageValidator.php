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

    if (!is_string($path) || $path === '' || !is_readable($path) || filesize($path) === 0) {
      $fail('No pudimos leer la imagen que intentaste subir. Verifica que el archivo no este vacio o corrupto.');

      return false;
    }

    $width = null;
    $height = null;
    $imageSize = @getimagesize($path);

    if ($imageSize !== false) {
      $type = $imageSize[2] ?? null;

      if (!in_array($type, [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true)) {
        $fail('Solo se permiten imagenes JPG, PNG o WebP.');

        return false;
      }

      $width = $imageSize[0];
      $height = $imageSize[1];
    } else {
      $image = self::loadImageResource($path, $ext);

      if ($image !== false) {
        $width = imagesx($image);
        $height = imagesy($image);
        imagedestroy($image);
      }
    }

    if ($width === null || $height === null) {
      Log::warning('Gallery upload: could not read image', [
        'extension' => $ext,
        'size' => $img->getSize(),
        'original_name' => $img->getClientOriginalName(),
        'detected_type' => self::detectContentExtension($path),
      ]);

      if (self::isJpegContentWithExtension($path, $ext, 'png')) {
        $fail('El archivo parece JPEG pero tiene extension .png (comun en imagenes de IA). Guardalo como .jpg o exportalo de nuevo en JPG/PNG real.');
      } else {
        $fail('No pudimos leer la imagen que intentaste subir.');
      }

      return false;
    }

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

  public static function isJpegContentWithExtension(string $path, string $ext, string $expectedExt): bool
  {
    if ($ext !== $expectedExt) {
      return false;
    }

    return self::detectContentExtension($path) === 'jpg';
  }

  /**
   * Extension real segun contenido (no el nombre del archivo).
   */
  public static function detectContentExtension(string $path): ?string
  {
    $imageSize = @getimagesize($path);

    if ($imageSize !== false) {
      return match ($imageSize[2] ?? null) {
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG => 'png',
        IMAGETYPE_WEBP => 'webp',
        default => null,
      };
    }

    $header = @file_get_contents($path, false, null, 0, 12);

    if (!is_string($header)) {
      return null;
    }

    if (str_starts_with($header, "\xFF\xD8\xFF")) {
      return 'jpg';
    }

    if (str_starts_with($header, "\x89PNG\r\n\x1a\n")) {
      return 'png';
    }

    if (
      strlen($header) >= 12
      && substr($header, 0, 4) === 'RIFF'
      && substr($header, 8, 4) === 'WEBP'
    ) {
      return 'webp';
    }

    return null;
  }

  public static function resolveStorageExtension(UploadedFile $file): string
  {
    $path = $file->getRealPath();
    $clientExt = strtolower($file->getClientOriginalExtension());

    if (is_string($path) && $path !== '') {
      $detected = self::detectContentExtension($path);

      if ($detected !== null) {
        return $detected;
      }
    }

    return in_array($clientExt, self::ALLOWED_EXTENSIONS, true) ? $clientExt : 'jpg';
  }
}
