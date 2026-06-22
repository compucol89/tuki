<?php

namespace App\Services;

use Carbon\Carbon;

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

  public function summarizeByEvent($bookings, array $eventInfos = [], $events = null): array
  {
    $ticketRows = $this->summarize($bookings, $eventInfos);
    $eventModels = collect($events ?: [])->keyBy('id');
    $bookingGroups = collect($bookings)->groupBy('event_id');
    $groups = [];

    foreach ($ticketRows as $row) {
      $eventId = $row['event_id'];

      if (!isset($groups[$eventId])) {
        $event = $eventModels->get($eventId);
        $meta = $this->eventDateMeta($event);
        $eventBookings = $bookingGroups->get($eventId, collect());

        $groups[$eventId] = [
          'event_id' => $eventId,
          'event_title' => $row['event_title'],
          'date_label' => $meta['label'],
          'date_sort' => $meta['sort'],
          'date_status' => $meta['status'],
          'bookings_count' => $eventBookings->count(),
          'completed_bookings' => $eventBookings->filter(fn ($booking) => in_array((string) $booking->paymentStatus, ['completed', 'paid', '1'], true))->count(),
          'free_bookings' => $eventBookings->where('paymentStatus', 'free')->count(),
          'pending_bookings' => $eventBookings->where('paymentStatus', 'pending')->count(),
          'rejected_bookings' => $eventBookings->where('paymentStatus', 'rejected')->count(),
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
          'tickets' => [],
        ];
      }

      $groups[$eventId]['sold'] += $row['sold'];
      $groups[$eventId]['pending'] += $row['pending'];
      $groups[$eventId]['rejected'] += $row['rejected'];
      $groups[$eventId]['scanned'] += $row['scanned'];
      $groups[$eventId]['total'] += $row['total'];
      $groups[$eventId]['revenue'] += $row['revenue'];
      $groups[$eventId]['charged_amount'] += $row['charged_amount'];
      $groups[$eventId]['organizer_amount'] += $row['organizer_amount'];
      $groups[$eventId]['system_amount'] += $row['system_amount'];
      $groups[$eventId]['tickets'][] = $row;
    }

    foreach ($groups as $key => $group) {
      $groups[$key]['scan_percent'] = $group['total'] > 0
        ? min(100, (int) round(($group['scanned'] * 100) / $group['total']))
        : 0;
    }

    usort($groups, function ($left, $right) {
      return [$left['date_sort'], $left['event_title']] <=> [$right['date_sort'], $right['event_title']];
    });

    return $groups;
  }

  private function eventDateMeta($event): array
  {
    if (!$event) {
      return [
        'label' => 'Fecha sin cargar',
        'sort' => '9-9999-12-31 23:59:59',
        'status' => 'Sin fecha',
      ];
    }

    $now = now();
    $dates = collect();

    if ($event->date_type === 'single' && !empty($event->start_date)) {
      $dates->push(Carbon::parse(trim($event->start_date . ' ' . ($event->start_time ?: '00:00'))));
    }

    if ($event->date_type === 'multiple' && method_exists($event, 'dates')) {
      $eventDates = $event->relationLoaded('dates') ? $event->dates : $event->dates()->get();

      foreach ($eventDates as $eventDate) {
        if (!empty($eventDate->start_date_time)) {
          $dates->push(Carbon::parse($eventDate->start_date_time));
        } elseif (!empty($eventDate->start_date)) {
          $dates->push(Carbon::parse(trim($eventDate->start_date . ' ' . ($eventDate->start_time ?: '00:00'))));
        }
      }
    }

    $dates = $dates->filter()->sort()->values();
    $upcoming = $dates->first(fn ($date) => $date->greaterThanOrEqualTo($now));
    $selected = $upcoming ?: $dates->last();

    if (!$selected) {
      return [
        'label' => 'Fecha sin cargar',
        'sort' => '9-9999-12-31 23:59:59',
        'status' => 'Sin fecha',
      ];
    }

    $isPast = $selected->lessThan($now);

    return [
      'label' => $selected->locale('es')->translatedFormat('D d M Y · H:i') . ' hs',
      'sort' => ($isPast ? '1-' : '0-') . $selected->format('Y-m-d H:i:s'),
      'status' => $isPast ? 'Evento pasado' : ($selected->isToday() ? 'Hoy' : 'Próximo'),
    ];
  }
}
