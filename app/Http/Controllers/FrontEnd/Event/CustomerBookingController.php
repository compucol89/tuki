<?php

namespace App\Http\Controllers\FrontEnd\Event;

use App\Http\Controllers\Controller;
use App\Models\Event\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerBookingController extends Controller
{
  public function my_booking()
  {
    $bookings = Booking::where('customer_id', Auth::guard('customer')->user()->id)->orderBy('created_at', 'desc')->paginate(20);
    return view('frontend.customer.dashboard.booking.my_booking', compact('bookings'));
  }
  //details
  public function details($id)
  {
    $booking = Booking::with(['evnt', 'organizer', 'fiscalProfile'])->where('id', $id)->firstOrFail();
    $user = Auth::guard('customer')->user();
    if (!$user || $user->id != $booking->customer_id) {
      return back();
    }
    $isGuest = false;
    return view('frontend.customer.dashboard.booking.details', compact('booking', 'isGuest'));
  }

  public function guestDetails($id, Request $request)
  {
    $token = $request->query('token');
    if (!$token) {
      abort(403);
    }
    $booking = Booking::with(['evnt', 'organizer', 'fiscalProfile'])->where('id', $id)->where('access_token', $token)->firstOrFail();
    if ($booking->token_legacy_expires_at && now()->gt($booking->token_legacy_expires_at)) {
      abort(403, 'El link de acceso ha expirado.');
    }
    $isGuest = true;
    return view('frontend.customer.dashboard.booking.details', compact('booking', 'isGuest'));
  }
}
