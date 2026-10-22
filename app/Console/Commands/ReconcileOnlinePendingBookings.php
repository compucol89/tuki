<?php

namespace App\Console\Commands;

use App\Models\Event\Booking;
use App\Models\PendingBooking;
use App\Services\BookingStockService;
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
    // No se borran: un pago aprobado puede llegar después del TTL.
    $expired = PendingBooking::where('status', 'pending')
      ->where('expires_at', '<', now())
      ->update(['status' => 'expired']);

    $staleProcessing = PendingBooking::where('status', 'processing')
      ->where('updated_at', '<', now()->subHours(24))
      ->update(['status' => 'failed']);

    // Retención de 7 días para permitir reconciliación de pagos tardíos.
    $deleted = PendingBooking::whereIn('status', ['failed', 'expired'])
      ->where('updated_at', '<', now()->subDays(7))
      ->delete();

    Log::info('ReconcileOnlinePendingBookings: limpieza PendingBooking', [
      'pending_expirados' => $expired,
      'processing_estancados' => $staleProcessing,
      'eliminados_retencion' => $deleted,
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

        app(BookingStockService::class)->release($locked);
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
}
