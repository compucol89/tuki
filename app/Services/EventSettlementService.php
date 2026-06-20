<?php

namespace App\Services;

use App\Models\BasicSettings\Basic;
use App\Models\Event;
use App\Models\Event\Booking;
use App\Models\EventSettlement;
use App\Models\Organizer;
use App\Models\Transaction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class EventSettlementService
{
  public const AMOUNT_ORGANIZER_NET = 'organizer_net';
  public const AMOUNT_CHARGED_TOTAL = 'charged_total';
  public const AMOUNT_CUSTOM = 'custom';

  public function summariesForEvents($eventIds): array
  {
    $eventIds = collect($eventIds)->filter()->unique()->values();

    if ($eventIds->isEmpty()) {
      return [];
    }

    $bookings = Booking::whereIn('event_id', $eventIds)->get()->groupBy('event_id');
    $settlements = EventSettlement::whereIn('event_id', $eventIds)->get()->groupBy('event_id');
    $summaries = [];

    foreach ($eventIds as $eventId) {
      $summaries[$eventId] = $this->summarize(
        $bookings->get($eventId, collect()),
        $settlements->get($eventId, collect())
      );
    }

    return $summaries;
  }

  public function dashboardSummaryForOrganizer(int $organizerId): array
  {
    $eventIds = Event::where('organizer_id', $organizerId)->pluck('id');
    $summaries = $this->summariesForEvents($eventIds);

    return [
      'pending_organizer_amount' => array_sum(array_column($summaries, 'pending_organizer_amount')),
      'organizer_net_amount' => array_sum(array_column($summaries, 'organizer_net_amount')),
      'charged_amount' => array_sum(array_column($summaries, 'charged_amount')),
      'settled_amount' => array_sum(array_column($summaries, 'settled_amount')),
      'covered_organizer_amount' => array_sum(array_column($summaries, 'covered_organizer_amount')),
      'pending_events_count' => collect($summaries)->whereIn('status', ['pending', 'partial'])->count(),
      'settled_events_count' => collect($summaries)->where('status', 'settled')->count(),
    ];
  }

  public function summarize($bookings, $settlements = null): array
  {
    $bookings = collect($bookings);
    $settlements = collect($settlements);
    $paidBookings = $bookings->where('paymentStatus', 'completed');
    $issuedTickets = $bookings
      ->filter(fn ($booking) => in_array($booking->paymentStatus, ['completed', 'free'], true))
      ->sum(fn ($booking) => (int) ($booking->quantity ?? 0));

    $chargedAmount = $paidBookings->sum(fn ($booking) => (float) ($booking->price ?? 0) + (float) ($booking->tax ?? 0));
    $organizerNetAmount = $paidBookings->sum(fn ($booking) => max((float) ($booking->price ?? 0) - (float) ($booking->commission ?? 0), 0));
    $platformAmount = $paidBookings->sum(fn ($booking) => (float) ($booking->tax ?? 0) + (float) ($booking->commission ?? 0));
    $settledAmount = $settlements->sum(fn ($settlement) => (float) ($settlement->paid_amount ?? 0));
    $coveredOrganizerAmount = $settlements->sum(fn ($settlement) => (float) ($settlement->covered_organizer_amount ?? 0));
    $balanceDebitedAmount = $settlements->sum(fn ($settlement) => (float) ($settlement->balance_debited_amount ?? 0));
    $pendingOrganizerAmount = max($organizerNetAmount - $coveredOrganizerAmount, 0);

    return [
      'charged_amount' => round($chargedAmount, 2),
      'organizer_net_amount' => round($organizerNetAmount, 2),
      'platform_amount' => round($platformAmount, 2),
      'settled_amount' => round($settledAmount, 2),
      'covered_organizer_amount' => round($coveredOrganizerAmount, 2),
      'balance_debited_amount' => round($balanceDebitedAmount, 2),
      'pending_organizer_amount' => round($pendingOrganizerAmount, 2),
      'paid_bookings_count' => $paidBookings->count(),
      'issued_tickets_count' => $issuedTickets,
      'status' => $this->resolveStatus($organizerNetAmount, $coveredOrganizerAmount),
    ];
  }

  public function resolvePaidAmount(string $option, array $summary, ?float $customAmount = null): float
  {
    if ($option === self::AMOUNT_ORGANIZER_NET) {
      return round((float) ($summary['pending_organizer_amount'] ?? 0), 2);
    }

    if ($option === self::AMOUNT_CHARGED_TOTAL) {
      return round(max((float) ($summary['charged_amount'] ?? 0) - (float) ($summary['settled_amount'] ?? 0), 0), 2);
    }

    if ($option === self::AMOUNT_CUSTOM) {
      return round(max((float) $customAmount, 0), 2);
    }

    throw new RuntimeException(__('Opcion de liquidacion invalida.'));
  }

  public function settleEvent(Event $event, array $data, ?int $adminId = null): EventSettlement
  {
    return DB::transaction(function () use ($event, $data, $adminId) {
      $lockedEvent = Event::where('id', $event->id)->lockForUpdate()->firstOrFail();

      if (empty($lockedEvent->organizer_id)) {
        throw new RuntimeException(__('Solo se pueden liquidar eventos con organizador.'));
      }

      $bookings = Booking::where('event_id', $lockedEvent->id)->get();
      $settlements = EventSettlement::where('event_id', $lockedEvent->id)->get();
      $summary = $this->summarize($bookings, $settlements);

      if ((float) $summary['pending_organizer_amount'] <= 0) {
        throw new RuntimeException(__('El evento no tiene saldo pendiente para liquidar.'));
      }

      $paidAmount = $this->resolvePaidAmount(
        $data['amount_option'],
        $summary,
        isset($data['custom_amount']) ? (float) $data['custom_amount'] : null
      );

      if ($paidAmount <= 0) {
        throw new RuntimeException(__('El monto a liquidar debe ser mayor a cero.'));
      }

      $coveredOrganizerAmount = min($paidAmount, (float) $summary['pending_organizer_amount']);
      $organizer = Organizer::where('id', $lockedEvent->organizer_id)->lockForUpdate()->firstOrFail();
      $balanceDebitAmount = min($coveredOrganizerAmount, max((float) $organizer->amount, 0));
      $preBalance = (float) $organizer->amount;
      $organizer->amount = $preBalance - $balanceDebitAmount;
      $organizer->save();

      $settlement = EventSettlement::create([
        'event_id' => $lockedEvent->id,
        'organizer_id' => $lockedEvent->organizer_id,
        'admin_id' => $adminId,
        'amount_option' => $data['amount_option'],
        'paid_amount' => $paidAmount,
        'covered_organizer_amount' => $coveredOrganizerAmount,
        'balance_debited_amount' => $balanceDebitAmount,
        'charged_amount_snapshot' => $summary['charged_amount'],
        'organizer_net_amount_snapshot' => $summary['organizer_net_amount'],
        'platform_amount_snapshot' => $summary['platform_amount'],
        'paid_bookings_count' => $summary['paid_bookings_count'],
        'paid_at' => $data['paid_at'] ?? now()->toDateString(),
        'reference' => $data['reference'] ?? null,
        'note' => $data['note'] ?? null,
      ]);

      if ($balanceDebitAmount > 0) {
        $currencyInfo = Basic::select('base_currency_symbol', 'base_currency_symbol_position')->first();
        Transaction::create([
          'transcation_id' => time(),
          'booking_id' => $settlement->id,
          'transcation_type' => 5,
          'customer_id' => null,
          'organizer_id' => $lockedEvent->organizer_id,
          'payment_status' => 1,
          'payment_method' => 'event_settlement',
          'grand_total' => $balanceDebitAmount,
          'tax' => 0,
          'commission' => 0,
          'pre_balance' => $preBalance,
          'after_balance' => $organizer->amount,
          'gateway_type' => null,
          'currency_symbol' => optional($currencyInfo)->base_currency_symbol ?: '$',
          'currency_symbol_position' => optional($currencyInfo)->base_currency_symbol_position ?: 'left',
        ]);
      }

      return $settlement;
    });
  }

  private function resolveStatus(float $organizerNetAmount, float $coveredOrganizerAmount): string
  {
    if ($organizerNetAmount <= 0) {
      return 'no_balance';
    }

    if ($coveredOrganizerAmount <= 0) {
      return 'pending';
    }

    return $coveredOrganizerAmount >= $organizerNetAmount ? 'settled' : 'partial';
  }
}
