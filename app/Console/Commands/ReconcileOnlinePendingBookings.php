<?php

namespace App\Console\Commands;

use App\Models\Event\Booking;
use App\Models\Event\Ticket;
use App\Models\PendingBooking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReconcileOnlinePendingBookings extends Command
{
    protected $signature = 'bookings:reconcile-online-pending {--hours=4 : Antigüedad mínima en horas de un booking online pendiente para cancelarlo y restaurar stock}';

    protected $description = 'Expira PendingBookings vencidos y cancela bookings online sin pago confirmado restaurando el stock (fix auditoría H4)';

    public function handle(): int
    {
        $this->expirePendingBookings();

        $cutoff = now()->subHours((int) $this->option('hours'));

        $bookings = Booking::where('paymentMethod', 'Iyzico')
            ->where('paymentStatus', 'pending')
            ->where('created_at', '<', $cutoff)
            ->get();

        $cancelled = 0;
        foreach ($bookings as $booking) {
            if ($this->cancelPendingBooking($booking)) {
                $cancelled++;
            }
        }

        $this->info("Bookings online pendientes cancelados: {$cancelled}");

        return self::SUCCESS;
    }

    private function expirePendingBookings(): void
    {
        $expired = PendingBooking::where('status', 'pending')
            ->where('expires_at', '<', now())
            ->delete();

        $staleProcessing = PendingBooking::where('status', 'processing')
            ->where('expires_at', '<', now()->subHours(24))
            ->delete();

        Log::info('ReconcileOnlinePendingBookings: limpieza PendingBooking', [
            'pending_expirados' => $expired,
            'processing_estancados' => $staleProcessing,
        ]);
    }

    private function cancelPendingBooking(Booking $booking): bool
    {
        try {
            DB::transaction(function () use ($booking) {
                $locked = Booking::where('id', $booking->id)->lockForUpdate()->first();
                if (!$locked || (string) $locked->paymentStatus !== 'pending') {
                    return;
                }
                $this->restoreStock($locked);
                $locked->update(['paymentStatus' => 'rejected']);
            });

            Log::warning('ReconcileOnlinePendingBookings: booking cancelado por falta de pago', [
                'booking_id' => $booking->id,
                'event_id' => $booking->event_id,
            ]);

            return true;
        } catch (\Throwable $th) {
            Log::error('ReconcileOnlinePendingBookings: error cancelando booking', [
                'booking_id' => $booking->id,
                'error' => $th->getMessage(),
            ]);

            return false;
        }
    }

    private function restoreStock(Booking $booking): void
    {
        $variations = json_decode((string) $booking->variation, true);

        if (!is_array($variations) || empty($variations)) {
            $ticket = Ticket::where('event_id', $booking->event_id)->lockForUpdate()->first();
            if ($ticket && ($ticket->ticket_available_type ?? '') === 'limited') {
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

        foreach ($counts as $key => $qty) {
            [$ticketId, $name] = explode('|', $key, 2);
            $this->restoreTicketStock((int) $ticketId, $qty, $name !== '' ? $name : null);
        }
    }

    private function restoreTicketStock(int $ticketId, int $qty, ?string $name): void
    {
        $ticket = Ticket::where('id', $ticketId)->lockForUpdate()->first();
        if (!$ticket) {
            return;
        }

        if ($ticket->pricing_type === 'variation' && $name !== null) {
            $ticketVariations = json_decode((string) $ticket->variations, true);
            if (!is_array($ticketVariations)) {
                return;
            }

            $updateVariation = [];
            foreach ($ticketVariations as $tv) {
                if (Booking::ticketNameMatches($ticket->id, $tv['name'] ?? null, $name)) {
                    if (($tv['ticket_available_type'] ?? '') === 'limited') {
                        $tv['ticket_available'] = (int) ($tv['ticket_available'] ?? 0) + $qty;
                    }
                }
                $updateVariation[] = $tv;
            }
            $ticket->variations = json_encode($updateVariation, true);
            $ticket->save();

            return;
        }

        if (($ticket->ticket_available_type ?? '') === 'limited') {
            $ticket->ticket_available = (int) $ticket->ticket_available + $qty;
            $ticket->save();
        }
    }
}
