<?php

declare(strict_types=1);

namespace App\Http\Controllers\FrontEnd\Event;

use App\Http\Controllers\Controller;
use App\Models\Arca\ArcaInvoice;
use App\Models\Event\Booking;
use App\Models\Event\EventContent;
use Illuminate\Http\Request;

class BookingInvoiceStatusController extends Controller
{
    public function show(int $id, Request $request)
    {
        $token = $request->query('token');

        if (!$token) {
            abort(403);
        }

        $booking = Booking::where('id', $id)->where('access_token', $token)->firstOrFail();

        if ($booking->token_legacy_expires_at && now()->gt($booking->token_legacy_expires_at)) {
            abort(403);
        }

        $invoice = ArcaInvoice::where('booking_id', $booking->id)->latest()->first();

        $eventContent = EventContent::where('event_id', $booking->event_id)->first();
        $eventTitle = $eventContent?->title ?? '';

        return view('frontend.invoice.status', compact('booking', 'invoice', 'eventTitle'));
    }
}
