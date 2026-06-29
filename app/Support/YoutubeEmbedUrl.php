<?php

namespace App\Support;

final class YoutubeEmbedUrl
{
  public static function from(?string $url): ?string
  {
    $id = self::videoId($url);

    return $id ? 'https://www.youtube.com/embed/' . $id : null;
  }

  private static function videoId(?string $url): ?string
  {
    $url = trim((string) $url);
    if ($url === '') {
      return null;
    }

    $parts = parse_url($url);
    if (!is_array($parts)) {
      return null;
    }

    $host = strtolower($parts['host'] ?? '');
    $path = trim($parts['path'] ?? '', '/');

    if ($host === 'youtu.be') {
      return self::validId(explode('/', $path)[0] ?? null);
    }

    if (!str_ends_with($host, 'youtube.com') && !str_ends_with($host, 'youtube-nocookie.com')) {
      return null;
    }

    parse_str($parts['query'] ?? '', $query);
    if (!empty($query['v'])) {
      return self::validId($query['v']);
    }

    $segments = $path === '' ? [] : explode('/', $path);
    if (in_array($segments[0] ?? '', ['embed', 'shorts', 'live'], true)) {
      return self::validId($segments[1] ?? null);
    }

    return null;
  }

  private static function validId(?string $id): ?string
  {
    $id = trim((string) $id);

    return preg_match('/^[A-Za-z0-9_-]{11}$/', $id) === 1 ? $id : null;
  }
}
