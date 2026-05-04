<?php

namespace App\Http\Controllers\FrontEnd\Event;

use App\Http\Controllers\Controller;
use App\Models\Event\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BookingTicketDownloadController extends Controller
{
  /**
   * Descarga protegida del PDF de entrada de un booking.
   *
   * Autorización (cualquiera de):
   *  - Customer logueado dueño del booking
   *  - Organizer logueado dueño del evento
   *  - Admin logueado
   *  - Guest con ?token={access_token} válido
   */
  public function download($id, Request $request)
  {
    $booking = Booking::where('id', $id)->firstOrFail();

    if (!$this->isAuthorized($booking, $request)) {
      abort(403, 'No autorizado.');
    }

    if (empty($booking->invoice)) {
      abort(404, 'Entrada no disponible todavía.');
    }

    $filePath = storage_path('app/invoices/' . $booking->invoice);

    if (!file_exists($filePath)) {
      // Fallback a legacy public path para PDFs históricos
      $filePath = public_path('assets/admin/file/invoices/' . $booking->invoice);
      if (!file_exists($filePath)) {
        abort(404, 'Archivo no encontrado.');
      }
    }

    return response()->download(
      $filePath,
      'entrada-' . $booking->booking_id . '.pdf',
      ['Content-Type' => 'application/pdf']
    );
  }

  /**
   * Valida si el request tiene autorización para descargar el booking.
   */
  private function isAuthorized(Booking $booking, Request $request): bool
  {
    // Admin: acceso total
    if (Auth::guard('admin')->check()) {
      return true;
    }

    // Customer logueado: solo si es dueño
    if (Auth::guard('customer')->check()) {
      $customerId = Auth::guard('customer')->id();
      if ((int) $booking->customer_id === (int) $customerId) {
        return true;
      }
    }

    // Organizer logueado: solo si es dueño del evento
    if (Auth::guard('organizer')->check()) {
      $organizerId = Auth::guard('organizer')->id();
      if ((int) $booking->organizer_id === (int) $organizerId) {
        return true;
      }
    }

    // Guest con token válido (mismo patrón que booking.guest_view)
    $token = $request->query('token');
    if (!empty($token) && !empty($booking->access_token) && hash_equals($booking->access_token, $token)) {
      return true;
    }

    return false;
  }
}
