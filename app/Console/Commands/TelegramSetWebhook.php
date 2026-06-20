<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TelegramSetWebhook extends Command
{
  protected $signature = 'telegram:set-webhook {url? : Webhook URL}';

  protected $description = 'Register the Telegram bot webhook URL.';

  public function handle(): int
  {
    $token = config('telegram.bot_token');

    if (empty($token)) {
      $this->error('TELEGRAM_BOT_TOKEN is not configured.');

      return self::FAILURE;
    }

    $url = $this->argument('url') ?: route('telegram.webhook', [], true);
    $payload = [
      'url' => $url,
    ];

    if (config('telegram.webhook_secret')) {
      $payload['secret_token'] = config('telegram.webhook_secret');
    }

    $response = Http::timeout(10)->asJson()->post('https://api.telegram.org/bot' . $token . '/setWebhook', $payload);

    if (!$response->successful() || $response->json('ok') !== true) {
      $this->error('Telegram rejected the webhook registration.');
      $this->line($response->body());

      return self::FAILURE;
    }

    $this->info('Telegram webhook registered: ' . $url);

    return self::SUCCESS;
  }
}
