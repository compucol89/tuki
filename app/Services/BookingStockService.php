<?php

namespace App\Services;

use App\Models\Event\Booking;
use App\Models\Event\Ticket;
use Illuminate\Support\Facades\DB;

/**
 * Única fuente de verdad para liberar stock de una reserva.
 *
 * La liberación es idempotente: bookings.stock_released_at se escribe una sola
 * vez dentro de la misma transacción que restaura tickets y add-ons.
 */
class BookingStockService
{
  public function release(Booking $booking): bool
  {
    return DB::transaction(function () use ($booking) {
      $locked = Booking::whereKey($booking->id)->lockForUpdate()->first();

      if (!$locked || $locked->stock_released_at !== null) {
        return false;
      }

      // No devolver al stock una entrada ya utilizada.
      if ($locked->scannedTicketsCount() > 0) {
        return false;
      }

      $this->restoreTicketStock($locked);
      $this->restoreAddonStock($locked);

      $locked->forceFill(['stock_released_at' => now()])->save();

      return true;
    });
  }

  private function restoreTicketStock(Booking $booking): void
  {
    $variations = json_decode((string) $booking->variation, true);

    if (!is_array($variations) || empty($variations)) {
      $ticket = Ticket::where('event_id', $booking->event_id)->lockForUpdate()->first();
      if ($ticket && $ticket->ticket_available_type === 'limited') {
        $ticket->ticket_available = (int) $ticket->ticket_available + (int) $booking->quantity;
        $ticket->save();
      }

      return;
    }

    $counts = [];
    foreach ($variations as $variation) {
      $key = (int) ($variation['ticket_id'] ?? 0) . '|' . ($variation['name'] ?? '');
      $counts[$key] = ($counts[$key] ?? 0) + (int) ($variation['qty'] ?? 1);
    }

    foreach ($counts as $key => $quantity) {
      [$ticketId, $name] = explode('|', $key, 2);
      $this->restoreTicket((int) $ticketId, $quantity, $name !== '' ? $name : null);
    }
  }

  private function restoreTicket(int $ticketId, int $quantity, ?string $name): void
  {
    if ($quantity <= 0) {
      return;
    }

    $ticket = Ticket::whereKey($ticketId)->lockForUpdate()->first();
    if (!$ticket) {
      return;
    }

    if ($ticket->pricing_type === 'variation' && $name !== null) {
      $ticketVariations = json_decode((string) $ticket->variations, true);
      if (!is_array($ticketVariations)) {
        return;
      }

      foreach ($ticketVariations as &$ticketVariation) {
        if (!Booking::ticketNameMatches($ticket->id, $ticketVariation['name'] ?? null, $name)) {
          continue;
        }

        if (($ticketVariation['ticket_available_type'] ?? '') === 'limited') {
          $ticketVariation['ticket_available'] = (int) ($ticketVariation['ticket_available'] ?? 0) + $quantity;
        }
        break;
      }
      unset($ticketVariation);

      $ticket->variations = json_encode($ticketVariations, true);
      $ticket->save();

      return;
    }

    if ($ticket->ticket_available_type === 'limited') {
      $ticket->ticket_available = (int) $ticket->ticket_available + $quantity;
      $ticket->save();
    }
  }

  private function restoreAddonStock(Booking $booking): void
  {
    foreach ($booking->addons()->with('addon')->get() as $bookingAddon) {
      if ($bookingAddon->redeemed || !$bookingAddon->addon || $bookingAddon->addon->stock === null) {
        continue;
      }

      $addon = $bookingAddon->addon;
      $addon->stock = (int) $addon->stock + (int) $bookingAddon->quantity;
      $addon->save();
    }
  }
}
