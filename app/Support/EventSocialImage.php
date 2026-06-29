<?php

namespace App\Support;

final class EventSocialImage
{
  public static function from(object $event, iterable $images): ?array
  {
    $candidates = [];

    if (!empty($event->og_image) && !empty($event->id)) {
      $candidates[] = ['assets/admin/img/event-ai/' . $event->id . '/', $event->og_image];
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
        return self::metadata($directory, $filename, $path);
      }
    }

    return null;
  }

  private static function metadata(string $directory, string $filename, string $path): array
  {
    $size = @getimagesize($path);
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    return [
      'url' => asset($directory . $filename),
      'width' => (int) ($size[0] ?? 1200),
      'height' => (int) ($size[1] ?? 630),
      'type' => self::mimeType($size['mime'] ?? null, $extension),
    ];
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
