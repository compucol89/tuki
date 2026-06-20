<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Models\OrganizerTelegramAccount;
use App\Models\OrganizerTelegramLinkToken;
use App\Services\Telegram\OrganizerTelegramReportService;
use App\Services\Telegram\TelegramBotService;
use App\Services\Telegram\TelegramCommandParser;
use Illuminate\Http\Request;

class TelegramWebhookController extends Controller
{
  public function __construct(
    private TelegramBotService $bot,
    private OrganizerTelegramReportService $reports,
    private TelegramCommandParser $parser
  ) {
  }

  public function handle(Request $request)
  {
    $secret = config('telegram.webhook_secret');

    if (!empty($secret) && $request->header('X-Telegram-Bot-Api-Secret-Token') !== $secret) {
      return response()->json(['ok' => false], 403);
    }

    if (!config('telegram.enabled')) {
      return response()->json(['ok' => true]);
    }

    $message = $request->input('message') ?: $request->input('edited_message');

    if (!is_array($message)) {
      return response()->json(['ok' => true]);
    }

    $chatId = data_get($message, 'chat.id');
    $from = data_get($message, 'from', []);
    $telegramUserId = data_get($from, 'id');
    $text = data_get($message, 'text');

    if (empty($chatId) || empty($telegramUserId)) {
      return response()->json(['ok' => true]);
    }

    [$command, $argument] = $this->parser->parse($text);

    if ($command === 'start' && !empty($argument)) {
      $this->linkAccount($argument, $chatId, $from);

      return response()->json(['ok' => true]);
    }

    $account = OrganizerTelegramAccount::with('organizer')
      ->where('telegram_user_id', (string) $telegramUserId)
      ->where('is_active', true)
      ->first();

    if (!$account || !$account->organizer) {
      $this->bot->sendMessage($chatId, 'Tu Telegram no está vinculado a un organizador de TukiPass. Entrá al panel de organizador y generá un enlace de conexión.');

      return response()->json(['ok' => true]);
    }

    $this->bot->sendMessage($chatId, $this->responseFor($command, $argument, $account->organizer));

    return response()->json(['ok' => true]);
  }

  private function linkAccount(string $token, $chatId, array $from): void
  {
    $linkToken = OrganizerTelegramLinkToken::where('token_hash', hash('sha256', $token))
      ->whereNull('used_at')
      ->where('expires_at', '>=', now())
      ->first();

    if (!$linkToken) {
      $this->bot->sendMessage($chatId, 'El enlace de conexión venció o no es válido. Generá uno nuevo desde el panel de organizador.');

      return;
    }

    OrganizerTelegramAccount::updateOrCreate(
      ['telegram_user_id' => (string) data_get($from, 'id')],
      [
        'organizer_id' => $linkToken->organizer_id,
        'telegram_chat_id' => (string) $chatId,
        'username' => data_get($from, 'username'),
        'first_name' => data_get($from, 'first_name'),
        'last_name' => data_get($from, 'last_name'),
        'is_active' => true,
        'linked_at' => now(),
      ]
    );

    $linkToken->update(['used_at' => now()]);
    $this->bot->sendMessage($chatId, 'Listo, tu Telegram quedó vinculado a TukiPass. Usá /resumen, /eventos o /evento ID.');
  }

  private function responseFor(string $command, ?string $argument, $organizer): string
  {
    if ($command === 'resumen') {
      return $this->reports->summary($organizer);
    }

    if ($command === 'eventos') {
      return $this->reports->events($organizer);
    }

    if ($command === 'evento' && ctype_digit((string) $argument)) {
      return $this->reports->eventDetails($organizer, (int) $argument);
    }

    return $this->reports->help();
  }
}
