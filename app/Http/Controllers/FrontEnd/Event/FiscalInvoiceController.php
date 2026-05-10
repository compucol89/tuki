<?php

declare(strict_types=1);

namespace App\Http\Controllers\FrontEnd\Event;

use App\Http\Controllers\Controller;
use App\Models\Arca\ArcaInvoice;
use App\Models\Event\Booking;
use App\Models\Event\EventContent;

class FiscalInvoiceController extends Controller
{
    public function showByToken(string $token)
    {
        $booking = Booking::where('fiscal_invoice_token', $token)->firstOrFail();

        $invoice = ArcaInvoice::where('booking_id', $booking->id)->latest()->first();

        $eventContent = EventContent::where('event_id', $booking->event_id)->first();
        $eventTitle = $eventContent?->title ?? '';

        return view('frontend.invoice.status', compact('booking', 'invoice', 'eventTitle'));
    }
}
