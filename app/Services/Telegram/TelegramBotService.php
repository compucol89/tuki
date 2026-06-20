<?php

namespace App\Services\Telegram;

use Illuminate\Support\Facades\Http;

class TelegramBotService
{
  public function sendMessage($chatId, string $text): bool
  {
    $token = config('telegram.bot_token');

    if (empty($token) || empty($chatId)) {
      return false;
    }

    $response = Http::timeout(8)->asJson()->post('https://api.telegram.org/bot' . $token . '/sendMessage', [
      'chat_id' => $chatId,
      'text' => $text,
      'disable_web_page_preview' => true,
    ]);

    return $response->successful();
  }
}
