<?php

namespace App\Http\Controllers\BackEnd\Event;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventAddon;
use App\Models\EventAddonSection;
use App\Models\Organizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EventAddonManagementController extends Controller
{
  /**
   * Determinar si el usuario actual puede gestionar este evento.
   * Admin: permiso total. Organizer: solo si es dueño del evento.
   */
  private function authorizeEvent(Event $event): void
  {
    if (Auth::guard('admin')->check()) {
      return;
    }
    if (Auth::guard('organizer')->check()) {
      $organizer = Auth::guard('organizer')->user();
      if ((int) $event->organizer_id === (int) $organizer->id) {
        return;
      }
    }
    abort(403, 'No tiene permiso para gestionar add-ons de este evento.');
  }

  public function index(Event $event)
  {
    $this->authorizeEvent($event);

    $sections = EventAddonSection::where('event_id', $event->id)
      ->with(['addons' => function ($q) {
        $q->orderBy('sort_order');
      }])
      ->orderBy('sort_order')
      ->get();

    return view('backend.event.partials.addons-tab', [
      'event'    => $event,
      'sections' => $sections,
    ]);
  }

  public function storeSection(Event $event, Request $request)
  {
    $this->authorizeEvent($event);

    $data = $request->validate([
      'title'       => 'required|string|max:191',
      'description' => 'nullable|string',
      'sort_order'  => 'nullable|integer',
      'is_active'   => 'nullable|boolean',
    ]);

    $data['event_id']     = $event->id;
    $data['organizer_id'] = $event->organizer_id;
    $data['slug']         = Str::slug($data['title']);
    $data['is_active']    = (bool) ($data['is_active'] ?? true);
    $data['sort_order']   = (int) ($data['sort_order'] ?? 0);

    EventAddonSection::create($data);

    return redirect()->back()->with('success', 'Sección de add-ons creada.');
  }

  public function updateSection(Event $event, EventAddonSection $section, Request $request)
  {
    $this->authorizeEvent($event);
    if ((int) $section->event_id !== (int) $event->id) {
      abort(404);
    }

    $data = $request->validate([
      'title'       => 'required|string|max:191',
      'description' => 'nullable|string',
      'sort_order'  => 'nullable|integer',
      'is_active'   => 'nullable|boolean',
    ]);

    $data['slug']       = Str::slug($data['title']);
    $data['is_active']  = (bool) ($data['is_active'] ?? false);
    $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

    $section->update($data);

    return redirect()->back()->with('success', 'Sección actualizada.');
  }

  public function destroySection(Event $event, EventAddonSection $section)
  {
    $this->authorizeEvent($event);
    if ((int) $section->event_id !== (int) $event->id) {
      abort(404);
    }
    $section->delete();
    return redirect()->back()->with('success', 'Sección eliminada.');
  }

  public function storeAddon(Event $event, Request $request)
  {
    $this->authorizeEvent($event);

    $data = $request->validate([
      'event_addon_section_id'      => 'required|integer|exists:event_addon_sections,id',
      'title'                       => 'required|string|max:191',
      'description'                 => 'nullable|string',
      'price'                       => 'required|numeric|min:0',
      'previous_price'              => 'nullable|numeric|min:0',
      'image'                       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
      'stock'                       => 'nullable|integer|min:0',
      'max_per_order'               => 'nullable|integer|min:1',
      'sort_order'                  => 'nullable|integer',
      'is_active'                   => 'nullable|boolean',
      'requires_age_verification'   => 'nullable|boolean',
      'redeemable_only_at_event'    => 'nullable|boolean',
      'non_refundable'              => 'nullable|boolean',
    ]);

    $section = EventAddonSection::findOrFail($data['event_addon_section_id']);
    if ((int) $section->event_id !== (int) $event->id) {
      abort(404);
    }

    if ($request->hasFile('image')) {
      $data['image'] = $this->storeImage($request);
    } else {
      unset($data['image']);
    }

    $data['event_id']                    = $event->id;
    $data['is_active']                   = (bool) ($data['is_active'] ?? true);
    $data['requires_age_verification']   = (bool) ($data['requires_age_verification'] ?? false);
    $data['redeemable_only_at_event']    = (bool) ($data['redeemable_only_at_event'] ?? true);
    $data['non_refundable']              = (bool) ($data['non_refundable'] ?? false);
    $data['sort_order']                  = (int) ($data['sort_order'] ?? 0);

    EventAddon::create($data);

    return redirect()->back()->with('success', 'Add-on creado.');
  }

  public function updateAddon(Event $event, EventAddon $addon, Request $request)
  {
    $this->authorizeEvent($event);
    if ((int) $addon->event_id !== (int) $event->id) {
      abort(404);
    }

    $data = $request->validate([
      'title'                       => 'required|string|max:191',
      'description'                 => 'nullable|string',
      'price'                       => 'required|numeric|min:0',
      'previous_price'              => 'nullable|numeric|min:0',
      'image'                       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
      'stock'                       => 'nullable|integer|min:0',
      'max_per_order'               => 'nullable|integer|min:1',
      'sort_order'                  => 'nullable|integer',
      'is_active'                   => 'nullable|boolean',
      'requires_age_verification'   => 'nullable|boolean',
      'redeemable_only_at_event'    => 'nullable|boolean',
      'non_refundable'              => 'nullable|boolean',
    ]);

    if ($request->hasFile('image')) {
      $data['image'] = $this->storeImage($request);
    } else {
      unset($data['image']);
    }

    $data['is_active']                   = (bool) ($data['is_active'] ?? false);
    $data['requires_age_verification']   = (bool) ($data['requires_age_verification'] ?? false);
    $data['redeemable_only_at_event']    = (bool) ($data['redeemable_only_at_event'] ?? true);
    $data['non_refundable']              = (bool) ($data['non_refundable'] ?? false);
    $data['sort_order']                  = (int) ($data['sort_order'] ?? 0);

    $addon->update($data);

    return redirect()->back()->with('success', 'Add-on actualizado.');
  }

  public function destroyAddon(Event $event, EventAddon $addon)
  {
    $this->authorizeEvent($event);
    if ((int) $addon->event_id !== (int) $event->id) {
      abort(404);
    }
    $addon->delete();
    return redirect()->back()->with('success', 'Add-on eliminado.');
  }

  public function uploadImage(Request $request)
  {
    $request->validate([
      'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
    ]);

    $filename = $this->storeImage($request);

    return response()->json(['image' => $filename]);
  }

  private function storeImage(Request $request): string
  {
    $file     = $request->file('image');
    $filename = uniqid('addon_') . '.' . $file->getClientOriginalExtension();
    $dest     = public_path('assets/admin/img/event-addons');
    if (!is_dir($dest)) {
      @mkdir($dest, 0775, true);
    }
    $file->move($dest, $filename);

    return $filename;
  }
}
