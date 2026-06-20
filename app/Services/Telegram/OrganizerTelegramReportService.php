<?php

namespace App\Services\Telegram;

use App\Models\BasicSettings\Basic;
use App\Models\Event;
use App\Models\Event\Booking;
use App\Models\Event\EventContent;
use App\Models\Language;
use App\Models\Organizer;
use Carbon\Carbon;

class OrganizerTelegramReportService
{
  public function help(): string
  {
    return implode("\n", [
      'TukiPass Bot - consultas de organizador',
      '',
      '/resumen - resumen general',
      '/eventos - listado de eventos',
      '/evento ID - detalle de un evento',
      '',
      'Este bot es solo lectura. No escanea ni modifica entradas.',
    ]);
  }

  public function summary(Organizer $organizer): string
  {
    $from = Carbon::now()->subDays(30);
    $query = Booking::where('organizer_id', $organizer->id)->where('created_at', '>=', $from);
    $completed = (clone $query)->whereIn('paymentStatus', ['completed', 'free'])->get();
    $pending = (clone $query)->where('paymentStatus', 'pending')->count();
    $rejected = (clone $query)->where('paymentStatus', 'rejected')->count();
    $revenue = $completed->sum(function ($booking) {
      return (float) ($booking->price ?? 0) + (float) ($booking->tax ?? 0);
    });
    $entries = $completed->sum('quantity');
    $scanned = $completed->sum(function ($booking) {
      return $booking->scannedTicketsCount();
    });

    return implode("\n", [
      'Resumen de los últimos 30 días',
      '',
      'Reservas pagas/gratis: ' . $completed->count(),
      'Entradas reservadas: ' . $entries,
      'Ingresos brutos: ' . $this->formatMoney($revenue),
      'Escaneadas: ' . $scanned . '/' . $entries,
      'Pendientes de pago: ' . $pending,
      'Rechazadas: ' . $rejected,
    ]);
  }

  public function events(Organizer $organizer): string
  {
    $events = Event::where('organizer_id', $organizer->id)
      ->orderByDesc('id')
      ->limit(8)
      ->get();

    if ($events->isEmpty()) {
      return 'No encontré eventos para tu cuenta.';
    }

    $titles = $this->eventTitles($events->pluck('id')->all());
    $lines = ['Tus eventos', ''];

    foreach ($events as $event) {
      $bookings = Booking::where('event_id', $event->id)->where('organizer_id', $organizer->id)->get();
      $completed = $bookings->whereIn('paymentStatus', ['completed', 'free']);
      $entries = $completed->sum('quantity');
      $scanned = $completed->sum(function ($booking) {
        return $booking->scannedTicketsCount();
      });
      $revenue = $completed->sum(function ($booking) {
        return (float) ($booking->price ?? 0) + (float) ($booking->tax ?? 0);
      });

      $lines[] = '#' . $event->id . ' - ' . ($titles[$event->id] ?? 'Evento');
      $lines[] = 'Reservas: ' . $bookings->count() . ' | Entradas: ' . $entries . ' | Escaneo: ' . $scanned . '/' . $entries . ' | ' . $this->formatMoney($revenue);
      $lines[] = '';
    }

    return trim(implode("\n", $lines));
  }

  public function eventDetails(Organizer $organizer, int $eventId): string
  {
    $event = Event::where('id', $eventId)->where('organizer_id', $organizer->id)->first();

    if (!$event) {
      return 'No encontré ese evento en tu cuenta.';
    }

    $title = $this->eventTitles([$event->id])[$event->id] ?? 'Evento #' . $event->id;
    $bookings = Booking::with('addons')
      ->where('event_id', $event->id)
      ->where('organizer_id', $organizer->id)
      ->get();
    $completed = $bookings->whereIn('paymentStatus', ['completed', 'free']);
    $pending = $bookings->where('paymentStatus', 'pending')->count();
    $entries = $completed->sum('quantity');
    $scanned = $completed->sum(function ($booking) {
      return $booking->scannedTicketsCount();
    });
    $revenue = $completed->sum(function ($booking) {
      return (float) ($booking->price ?? 0) + (float) ($booking->tax ?? 0);
    });
    $ticketTypes = [];

    foreach ($completed as $booking) {
      foreach ($booking->ticketBreakdown() as $item) {
        $name = $item['name'];

        if (!isset($ticketTypes[$name])) {
          $ticketTypes[$name] = ['quantity' => 0, 'scanned' => 0, 'subtotal' => 0.0];
        }

        $ticketTypes[$name]['quantity'] += $item['quantity'];
        $ticketTypes[$name]['scanned'] += $item['scanned'];
        $ticketTypes[$name]['subtotal'] += $item['subtotal'];
      }
    }

    $lines = [
      $title,
      '',
      'Reservas totales: ' . $bookings->count(),
      'Entradas pagas/gratis: ' . $entries,
      'Ingresos brutos: ' . $this->formatMoney($revenue),
      'Escaneadas: ' . $scanned . '/' . $entries,
      'Pendientes de pago: ' . $pending,
    ];

    if (!empty($ticketTypes)) {
      $lines[] = '';
      $lines[] = 'Tipos de entrada';

      foreach ($ticketTypes as $name => $item) {
        $lines[] = '- ' . $name . ': ' . $item['quantity'] . ' entradas, escaneo ' . $item['scanned'] . '/' . $item['quantity'] . ', ' . $this->formatMoney($item['subtotal']);
      }
    }

    $addons = $bookings->flatMap(function ($booking) {
      return $booking->addonBreakdown();
    });
    $addonTotal = $addons->sum('subtotal');
    $addonQuantity = $addons->sum('quantity');

    if ($addonQuantity > 0) {
      $lines[] = '';
      $lines[] = 'Add-ons: ' . $addonQuantity . ' | ' . $this->formatMoney($addonTotal);
    }

    return implode("\n", $lines);
  }

  private function eventTitles(array $eventIds): array
  {
    $language = Language::where('code', 'es')->first();

    return EventContent::whereIn('event_id', $eventIds)
      ->get()
      ->groupBy('event_id')
      ->map(function ($items) use ($language) {
        $item = $language ? ($items->firstWhere('language_id', $language->id) ?: $items->first()) : $items->first();

        return $item ? $item->title : null;
      })
      ->all();
  }

  private function formatMoney($amount): string
  {
    $currency = Basic::select('base_currency_symbol', 'base_currency_symbol_position')->first();
    $symbol = $currency->base_currency_symbol ?? '$';
    $position = $currency->base_currency_symbol_position ?? 'left';
    $amount = number_format((float) $amount, 2, ',', '.');

    return ($position == 'left' ? $symbol . ' ' : '') . $amount . ($position == 'right' ? ' ' . $symbol : '');
  }
}
