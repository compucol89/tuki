<?php

namespace App\Support;

final class EventSocialImage
{
  private const MAX_SOCIAL_IMAGE_BYTES = 300000;
  private const SOCIAL_IMAGE_WIDTHS = [1200, 900, 720, 600, 480];
  private const SOCIAL_IMAGE_QUALITIES = [82, 72, 62, 52, 44];

  public static function from(object $event, iterable $images): ?array
  {
    $candidates = [];
    $eventId = (int) ($event->id ?? 0);

    if (!empty($event->og_image) && $eventId > 0) {
      $candidates[] = ['assets/admin/img/event-ai/' . $eventId . '/', $event->og_image];
    }

    if (!empty($event->thumbnail)) {
      $candidates[] = ['assets/admin/img/event/thumbnail/', $event->thumbnail];
    }

    foreach ($images as $image) {
      if (!empty($image->image)) {
        $candidates[] = ['assets/admin/img/event-gallery/', $image->image];
      }
    }

    foreach ($candidates as [$directory, $filename]) {
      $path = public_path($directory . $filename);
      if (is_file($path)) {
        return self::metadata($directory, $filename, $path, $eventId);
      }
    }

    return null;
  }

  private static function metadata(string $directory, string $filename, string $path, int $eventId): array
  {
    $optimized = self::optimizedSocialImage($directory, $filename, $path, $eventId);

    if ($optimized !== null) {
      return $optimized;
    }

    return self::rawMetadata($directory, $filename, $path);
  }

  private static function rawMetadata(string $directory, string $filename, string $path): array
  {
    $size = @getimagesize($path);
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    return [
      'url' => asset($directory . $filename) . '?v=' . filemtime($path),
      'width' => (int) ($size[0] ?? 1200),
      'height' => (int) ($size[1] ?? 630),
      'type' => self::mimeType($size['mime'] ?? null, $extension),
    ];
  }

  private static function optimizedSocialImage(string $directory, string $filename, string $path, int $eventId): ?array
  {
    if ($eventId <= 0 || filesize($path) <= self::MAX_SOCIAL_IMAGE_BYTES || !function_exists('imagejpeg')) {
      return null;
    }

    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $source = EventGalleryImageValidator::loadImageResource($path, $extension);

    if ($source === false) {
      return null;
    }

    $sourceWidth = imagesx($source);
    $sourceHeight = imagesy($source);

    if ($sourceWidth <= 0 || $sourceHeight <= 0) {
      imagedestroy($source);
      return null;
    }

    $socialDir = 'assets/admin/img/event-social/' . $eventId . '/';
    $hash = substr(sha1($directory . '|' . $filename . '|' . filemtime($path) . '|' . filesize($path)), 0, 12);
    $baseName = preg_replace('/[^A-Za-z0-9_-]+/', '-', pathinfo($filename, PATHINFO_FILENAME)) ?: 'image';
    $socialFilename = $baseName . '-' . $hash . '.jpg';
    $socialPath = public_path($socialDir . $socialFilename);

    if (is_file($socialPath)) {
      imagedestroy($source);
      return self::rawMetadata($socialDir, $socialFilename, $socialPath);
    }

    @mkdir(dirname($socialPath), 0775, true);

    $targetWidths = array_values(array_unique(array_filter(array_map(
      fn($width) => min($width, $sourceWidth),
      self::SOCIAL_IMAGE_WIDTHS
    ), fn($width) => $width > 0)));

    foreach ($targetWidths as $targetWidth) {
      $targetHeight = (int) max(1, round($sourceHeight * ($targetWidth / $sourceWidth)));
      $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
      $white = imagecolorallocate($canvas, 255, 255, 255);
      imagefill($canvas, 0, 0, $white);
      imagecopyresampled($canvas, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);

      foreach (self::SOCIAL_IMAGE_QUALITIES as $quality) {
        imagejpeg($canvas, $socialPath, $quality);
        clearstatcache(true, $socialPath);

        if (is_file($socialPath) && filesize($socialPath) <= self::MAX_SOCIAL_IMAGE_BYTES) {
          imagedestroy($canvas);
          imagedestroy($source);
          return self::rawMetadata($socialDir, $socialFilename, $socialPath);
        }
      }

      imagedestroy($canvas);
    }

    imagedestroy($source);

    return is_file($socialPath) ? self::rawMetadata($socialDir, $socialFilename, $socialPath) : null;
  }

  private static function mimeType(?string $detected, string $extension): string
  {
    if (is_string($detected) && str_starts_with($detected, 'image/')) {
      return $detected;
    }

    return match ($extension) {
      'jpg', 'jpeg' => 'image/jpeg',
      'png' => 'image/png',
      'webp' => 'image/webp',
      'gif' => 'image/gif',
      default => 'image/jpeg',
    };
  }
}
