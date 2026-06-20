<?php

namespace App\Http\Controllers\BackEnd\Organizer;

use App\Http\Controllers\Controller;
use App\Models\OrganizerTelegramAccount;
use App\Models\OrganizerTelegramLinkToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TelegramBotController extends Controller
{
  public function index()
  {
    $organizer = Auth::guard('organizer')->user();
    $account = OrganizerTelegramAccount::where('organizer_id', $organizer->id)
      ->where('is_active', true)
      ->latest()
      ->first();
    $botUsername = config('telegram.bot_username');
    $botEnabled = (bool) config('telegram.enabled');

    return view('organizer.telegram-bot.index', compact('account', 'botUsername', 'botEnabled'));
  }

  public function generate(Request $request)
  {
    $organizer = Auth::guard('organizer')->user();

    OrganizerTelegramLinkToken::where('organizer_id', $organizer->id)
      ->whereNull('used_at')
      ->update(['used_at' => now()]);

    $token = Str::random(40);
    $expiresAt = now()->addMinutes(30);

    OrganizerTelegramLinkToken::create([
      'organizer_id' => $organizer->id,
      'token_hash' => hash('sha256', $token),
      'expires_at' => $expiresAt,
    ]);

    return redirect()
      ->route('organizer.telegram_bot.index')
      ->with('telegram_link_token', $token)
      ->with('telegram_link_expires_at', $expiresAt->format('d/m/Y H:i'));
  }
}
