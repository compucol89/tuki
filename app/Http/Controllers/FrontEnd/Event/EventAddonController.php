<?php

namespace App\Http\Controllers\FrontEnd\Event;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventAddon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class EventAddonController extends Controller
{
  /**
   * Agregar un add-on al carrito de add-ons del evento (en sesión).
   * NO descuenta stock aquí: se descuenta al confirmar el pago.
   */
  public function addToCart(Event $event, Request $request)
  {
    $request->validate([
      'addon_id' => 'required|integer',
      'qty'      => 'required|integer|min:0',
    ]);

    $addonId = (int) $request->input('addon_id');
    $qty     = (int) $request->input('qty');

    try {
      DB::transaction(function () use ($addonId, $event, $qty) {
        $addon = EventAddon::where('id', $addonId)
          ->where('event_id', $event->id)
          ->where('is_active', true)
          ->lockForUpdate()
          ->first();

        if (!$addon) {
          throw new \Exception('Add-on no disponible para este evento.');
        }

        if ($addon->max_per_order !== null && $qty > (int) $addon->max_per_order) {
          throw new \Exception("La cantidad máxima por compra para {$addon->title} es {$addon->max_per_order}.");
        }

        if ($addon->stock !== null && $qty > 0) {
          if ($qty > (int) $addon->stock) {
            throw new \Exception("Stock insuficiente para {$addon->title}.");
          }
        }
      });
    } catch (\Exception $e) {
      Session::flash('error', $e->getMessage());
      return redirect()->back();
    }

    $cartAddons = Session::get('cart_addons', []);
    if (!isset($cartAddons[$event->id])) {
      $cartAddons[$event->id] = [];
    }
    if ($qty <= 0) {
      unset($cartAddons[$event->id][$addonId]);
    } else {
      $cartAddons[$event->id][$addonId] = $qty;
    }
    if (empty($cartAddons[$event->id])) {
      unset($cartAddons[$event->id]);
    }
    Session::put('cart_addons', $cartAddons);

    Session::flash('success', 'Add-on actualizado.');
    return redirect()->back();
  }

  public function removeFromCart(Event $event, Request $request)
  {
    $request->validate([
      'addon_id' => 'required|integer',
    ]);

    $addonId = (int) $request->input('addon_id');
    $cartAddons = Session::get('cart_addons', []);
    if (isset($cartAddons[$event->id][$addonId])) {
      unset($cartAddons[$event->id][$addonId]);
      if (empty($cartAddons[$event->id])) {
        unset($cartAddons[$event->id]);
      }
      Session::put('cart_addons', $cartAddons);
    }

    Session::flash('success', 'Add-on eliminado del carrito.');
    return redirect()->back();
  }

  public function updateCart(Event $event, Request $request)
  {
    $request->validate([
      'addons'   => 'required|array',
      'addons.*' => 'integer|min:0',
    ]);

    $addons = $request->input('addons', []);

    try {
      DB::transaction(function () use ($addons, $event) {
        $validated = [];
        foreach ($addons as $addonId => $qty) {
          $qty = (int) $qty;
          if ($qty <= 0) {
            continue;
          }
          $addon = EventAddon::where('id', $addonId)
            ->where('event_id', $event->id)
            ->where('is_active', true)
            ->lockForUpdate()
            ->first();
          if (!$addon) {
            throw new \Exception("Add-on {$addonId} no disponible.");
          }
          if ($addon->max_per_order !== null && $qty > (int) $addon->max_per_order) {
            throw new \Exception("Máximo {$addon->max_per_order} unidades de {$addon->title}.");
          }
          if ($addon->stock !== null && $qty > (int) $addon->stock) {
            throw new \Exception("Stock insuficiente para {$addon->title}.");
          }
          $validated[(int) $addonId] = $qty;
        }
        Session::put('cart_addons.' . $event->id, $validated);
      });
    } catch (\Exception $e) {
      Session::flash('error', $e->getMessage());
      return redirect()->back();
    }

    Session::flash('success', 'Carrito de add-ons actualizado.');
    return redirect()->back();
  }

  /**
   * Variante AJAX de updateCart: sincroniza el carrito de add-ons en sesión
   * y devuelve JSON, sin redirigir. Usado por el modal Bootstrap 4 in-page
   * que se abre desde event-details.blade.php al hacer click en "Reservar mi lugar".
   * La validación de stock se hace dentro de DB::transaction con lockForUpdate.
   */
  public function updateCartAjax(Event $event, Request $request)
  {
    $request->validate([
      'addons'   => 'nullable|array',
      'addons.*' => 'integer|min:0',
    ]);

    $addons = $request->input('addons', []) ?: [];

    if (empty($addons)) {
      Session::put('cart_addons.' . $event->id, []);
      Session::put('event_addons_decided.' . $event->id, true);
      return response()->json(['status' => 'success', 'items' => 0, 'total' => 0.0], 200);
    }

    try {
      $validated = [];
      DB::transaction(function () use ($addons, $event, &$validated) {
        foreach ($addons as $addonId => $qty) {
          $qty = (int) $qty;
          if ($qty <= 0) {
            continue;
          }
          $addon = EventAddon::where('id', (int) $addonId)
            ->where('event_id', $event->id)
            ->where('is_active', true)
            ->lockForUpdate()
            ->first();
          if (!$addon) {
            throw new \Exception("Add-on {$addonId} no disponible.");
          }
          if ($addon->max_per_order !== null && $qty > (int) $addon->max_per_order) {
            throw new \Exception("Máximo {$addon->max_per_order} unidades de {$addon->title}.");
          }
          if ($addon->stock !== null && $qty > (int) $addon->stock) {
            throw new \Exception("Stock insuficiente para {$addon->title}.");
          }
          $validated[(int) $addonId] = $qty;
        }
        Session::put('cart_addons.' . $event->id, $validated);
        Session::put('event_addons_decided.' . $event->id, true);
      });

      return response()->json([
        'status' => 'success',
        'items'  => count($validated),
        'total'  => array_sum(array_map(function ($id) use ($validated) {
          $addon = EventAddon::find($id);
          return $addon ? (float) $addon->price * $validated[$id] : 0;
        }, array_keys($validated))),
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'status'  => 'error',
        'message' => $e->getMessage(),
      ], 422);
    }
  }

  /**
   * Marca el modal de add-ons como ya visto/descartado en este evento.
   * Permite al handler JS del modal submitear el form de tickets sin
   * reabrir el modal en submits subsecuentes de la misma sesión.
   * El flag se limpia en EventAddonCartService::forgetEvent (post-checkout).
   */
  public function markDecided(Event $event, Request $request)
  {
    Session::put('event_addons_decided.' . $event->id, true);
    return response()->json(['status' => 'success'], 200);
  }
}
