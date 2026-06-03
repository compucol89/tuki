<?php

namespace App\Services;

use App\Models\Event\Booking;
use App\Models\EventAddon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class EventAddonCartService
{
  public function summaryForEvent(int $eventId, ?array $cartAddons = null): array
  {
    $selected = $this->itemsForEvent($eventId, $cartAddons);
    if (empty($selected)) {
      return ['items' => [], 'total' => 0.0];
    }

    $addons = EventAddon::where('event_id', $eventId)
      ->where('is_active', true)
      ->whereIn('id', array_keys($selected))
      ->get()
      ->keyBy('id');

    if ($addons->count() !== count($selected)) {
      throw new \Exception('Uno de los add-ons seleccionados ya no está disponible.');
    }

    $items = [];
    $total = 0.0;

    foreach ($selected as $addonId => $qty) {
      $addon = $addons->get($addonId);
      if (!$addon) {
        continue;
      }

      $qty = (int) $qty;
      if ($qty <= 0) {
        continue;
      }

      if ($addon->max_per_order !== null && $qty > (int) $addon->max_per_order) {
        throw new \Exception("La cantidad máxima por compra para {$addon->title} es {$addon->max_per_order}.");
      }

      if ($addon->stock !== null && $qty > (int) $addon->stock) {
        throw new \Exception("Stock insuficiente para {$addon->title}.");
      }

      $unitPrice = (float) $addon->price;
      $subtotal = round($unitPrice * $qty, 2);
      $items[] = [
        'event_addon_id' => (int) $addon->id,
        'title' => $addon->title,
        'description' => $addon->description,
        'unit_price' => $unitPrice,
        'quantity' => $qty,
        'subtotal' => $subtotal,
        'requires_age_verification' => (bool) $addon->requires_age_verification,
        'redeemable_only_at_event' => (bool) $addon->redeemable_only_at_event,
        'non_refundable' => (bool) $addon->non_refundable,
      ];
      $total += $subtotal;
    }

    return ['items' => $items, 'total' => round($total, 2)];
  }

  public function attachToBooking(Booking $booking, ?array $cartAddons = null): void
  {
    $selected = $this->itemsForEvent((int) $booking->event_id, $cartAddons);
    if (empty($selected)) {
      return;
    }

    DB::transaction(function () use ($booking, $selected) {
      foreach ($selected as $addonId => $qty) {
        $qty = (int) $qty;
        if ($qty <= 0) {
          continue;
        }

        $addon = EventAddon::where('id', (int) $addonId)
          ->where('event_id', (int) $booking->event_id)
          ->where('is_active', true)
          ->lockForUpdate()
          ->first();

        if (!$addon) {
          throw new \Exception("Add-on {$addonId} no disponible.");
        }

        if ($addon->max_per_order !== null && $qty > (int) $addon->max_per_order) {
          throw new \Exception("Máximo {$addon->max_per_order} unidades de {$addon->title}.");
        }

        if ($addon->stock !== null) {
          if ($qty > (int) $addon->stock) {
            throw new \Exception("Stock insuficiente para {$addon->title}.");
          }
          $addon->stock = (int) $addon->stock - $qty;
          $addon->save();
        }

        $unitPrice = (float) $addon->price;
        $booking->addons()->create([
          'event_id' => (int) $booking->event_id,
          'event_addon_id' => (int) $addon->id,
          'title' => $addon->title,
          'description' => $addon->description,
          'unit_price' => $unitPrice,
          'quantity' => $qty,
          'subtotal' => round($unitPrice * $qty, 2),
          'requires_age_verification' => (bool) $addon->requires_age_verification,
          'redeemable_only_at_event' => (bool) $addon->redeemable_only_at_event,
          'non_refundable' => (bool) $addon->non_refundable,
        ]);
      }
    });
  }

  public function putSummaryInSession(int $eventId): array
  {
    $summary = $this->summaryForEvent($eventId);
    Session::put('event_addons_summary', $summary['items']);
    Session::put('event_addons_total', $summary['total']);

    return $summary;
  }

  public function forgetEvent(int $eventId): void
  {
    $cartAddons = Session::get('cart_addons', []);
    if (isset($cartAddons[$eventId])) {
      unset($cartAddons[$eventId]);
      Session::put('cart_addons', $cartAddons);
    }

    Session::forget('event_addons_summary');
    Session::forget('event_addons_total');
    Session::forget('event_addons_decided.' . $eventId);
  }

  public function itemsForEvent(int $eventId, ?array $cartAddons = null): array
  {
    $cartAddons = $cartAddons ?? Session::get('cart_addons', []);
    $selected = $cartAddons[$eventId] ?? $cartAddons[(string) $eventId] ?? [];

    return is_array($selected) ? array_filter($selected, fn ($qty) => (int) $qty > 0) : [];
  }
}
