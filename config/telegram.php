<?php

return [
  'bot_token' => env('TELEGRAM_BOT_TOKEN'),
  'bot_username' => env('TELEGRAM_BOT_USERNAME'),
  'webhook_secret' => env('TELEGRAM_WEBHOOK_SECRET'),
  'enabled' => env('TELEGRAM_BOT_ENABLED', false),
];
