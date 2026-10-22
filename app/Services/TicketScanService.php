<?php

namespace App\Services;

use App\Models\Event\Booking;
use App\Models\Event\TicketScan;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * Validación y registro atómico de escaneos QR.
 *
 * La tabla ticket_scans tiene un unique(booking_id, unique_id): el escaneo
 * concurrente se resuelve en la base, no en PHP. El JSON scanned_tickets se
 * mantiene por compatibilidad con reportes y pantallas existentes.
 */
class TicketScanService
{
  public const VERIFIED = 'verified';
  public const ALREADY_SCANNED = 'already_scanned';
  public const UNVERIFIED = 'unverified';
  public const PAYMENT_INCOMPLETE = 'payment_incomplete';
  public const PAYMENT_REJECTED = 'payment_rejected';
  public const NO_PERMISSION = 'no_permission';

  public function scan(
    Booking $booking,
    string $uniqueId,
    string $scannerType,
    ?int $scannerId = null,
    ?int $organizerId = null,
    ?string $ip = null,
    ?string $userAgent = null
  ): string {
    return DB::transaction(function () use ($booking, $uniqueId, $scannerType, $scannerId, $organizerId, $ip, $userAgent) {
      $locked = Booking::whereKey($booking->id)->lockForUpdate()->first();

      if (!$locked) {
        return self::UNVERIFIED;
      }

      if ($organizerId !== null && (int) $locked->organizer_id !== $organizerId) {
        return self::NO_PERMISSION;
      }

      $status = (string) $locked->paymentStatus;
      if ($status === 'pending') {
        return self::PAYMENT_INCOMPLETE;
      }
      if (!in_array($status, ['completed', 'free'], true)) {
        return self::PAYMENT_REJECTED;
      }

      if (!$locked->hasIssuedTicketUniqueId($uniqueId)) {
        return self::UNVERIFIED;
      }

      try {
        TicketScan::create([
          'booking_id' => $locked->id,
          'event_id' => $locked->event_id,
          'organizer_id' => $locked->organizer_id,
          'unique_id' => $uniqueId,
          'scanner_type' => $scannerType,
          'scanner_id' => $scannerId,
          'ip' => $ip,
          'user_agent' => $userAgent !== null ? mb_substr($userAgent, 0, 255) : null,
          'scanned_at' => now(),
        ]);
      } catch (QueryException $e) {
        if ($this->isDuplicate($e)) {
          return self::ALREADY_SCANNED;
        }
        throw $e;
      }

      $scanned = $locked->scannedTicketIds();
      if (!in_array($uniqueId, $scanned, true)) {
        $scanned[] = $uniqueId;
        $locked->scanned_tickets = json_encode($scanned);
        $locked->save();
      }

      return self::VERIFIED;
    });
  }

  private function isDuplicate(QueryException $e): bool
  {
    $sqlState = (string) ($e->errorInfo[0] ?? '');
    $driverCode = (int) ($e->errorInfo[1] ?? 0);

    return $sqlState === '23000'
      || $driverCode === 1062
      || str_contains(strtolower($e->getMessage()), 'unique');
  }
}
