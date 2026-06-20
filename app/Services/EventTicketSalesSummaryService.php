<?php

namespace App\Services;

class EventTicketSalesSummaryService
{
  public function summarize($bookings, array $eventInfos = []): array
  {
    $rows = [];

    foreach ($bookings as $booking) {
      $tickets = $booking->ticketBreakdown();
      $bookingTicketSubtotal = array_sum(array_map(function ($ticket) {
        return max((float) ($ticket['subtotal'] ?? 0), 0);
      }, $tickets));
      $bookingTax = (float) ($booking->tax ?? 0);
      $bookingCommission = (float) ($booking->commission ?? 0);
      $hasOrganizer = !empty($booking->organizer_id);
      $isSold = in_array((string) $booking->paymentStatus, ['completed', 'free', 'paid', '1'], true);

      foreach ($tickets as $ticket) {
        $key = $booking->event_id . '|' . $ticket['name'];

        if (!isset($rows[$key])) {
          $eventInfo = $eventInfos[$booking->event_id] ?? null;

          $rows[$key] = [
            'event_id' => $booking->event_id,
            'event_title' => $eventInfo ? $eventInfo->title : 'Evento #' . $booking->event_id,
            'ticket_name' => $ticket['name'],
            'sold' => 0,
            'pending' => 0,
            'rejected' => 0,
            'scanned' => 0,
            'total' => 0,
            'revenue' => 0.0,
            'charged_amount' => 0.0,
            'organizer_amount' => 0.0,
            'system_amount' => 0.0,
            'scan_percent' => 0,
          ];
        }

        $quantity = (int) $ticket['quantity'];
        $rows[$key]['total'] += $quantity;
        $rows[$key]['scanned'] += (int) $ticket['scanned'];

        if ($isSold) {
          $subtotal = (float) $ticket['subtotal'];
          $ratio = $bookingTicketSubtotal > 0 ? max($subtotal, 0) / $bookingTicketSubtotal : 0;
          $taxAmount = $bookingTax * $ratio;
          $commissionAmount = $bookingCommission * $ratio;
          $chargedAmount = $subtotal + $taxAmount;
          $organizerAmount = $hasOrganizer ? $subtotal - $commissionAmount : 0;
          $systemAmount = $hasOrganizer ? $taxAmount + $commissionAmount : $chargedAmount;

          $rows[$key]['sold'] += $quantity;
          $rows[$key]['revenue'] += $subtotal;
          $rows[$key]['charged_amount'] += $chargedAmount;
          $rows[$key]['system_amount'] += $systemAmount;
          $rows[$key]['organizer_amount'] += $organizerAmount;
        } elseif ($booking->paymentStatus === 'pending') {
          $rows[$key]['pending'] += $quantity;
        } elseif ($booking->paymentStatus === 'rejected') {
          $rows[$key]['rejected'] += $quantity;
        }
      }
    }

    foreach ($rows as $key => $row) {
      $rows[$key]['scan_percent'] = $row['total'] > 0
        ? min(100, (int) round(($row['scanned'] * 100) / $row['total']))
        : 0;
    }

    usort($rows, function ($left, $right) {
      return [$left['event_title'], $left['ticket_name']] <=> [$right['event_title'], $right['ticket_name']];
    });

    return $rows;
  }
}
