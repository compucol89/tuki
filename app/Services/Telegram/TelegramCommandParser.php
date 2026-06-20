<?php

namespace App\Services\Telegram;

class TelegramCommandParser
{
  public function parse(?string $text): array
  {
    $text = trim((string) $text);

    if ($text === '' || $text[0] !== '/') {
      return ['ayuda', null];
    }

    $text = preg_replace('/@\w+/', '', $text);
    $parts = preg_split('/\s+/', trim($text), 2);
    $command = ltrim($parts[0] ?? '', '/');
    $argument = $parts[1] ?? null;

    if (str_starts_with($command, 'evento_')) {
      $argument = substr($command, 7);
      $command = 'evento';
    }

    return [$command ?: 'ayuda', $argument !== '' ? $argument : null];
  }
}
