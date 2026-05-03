<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Arca\ArcaInvoice;
use App\Models\Event\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ArcaInvoiceMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public ArcaInvoice $arcaInvoice,
        public Booking $booking
    ) {
    }

    public function build(): self
    {
        $invoiceNumber = $this->formatInvoiceNumber($this->arcaInvoice);
        $subject = 'Factura electrónica ' . $invoiceNumber . ' — TukiPass';

        return $this
            ->subject($subject)
            ->view('emails.arca_invoice')
            ->with([
                'invoice' => $this->arcaInvoice,
                'booking' => $this->booking,
                'invoiceNumber' => $invoiceNumber,
            ]);
    }

    private function formatInvoiceNumber(ArcaInvoice $invoice): string
    {
        $tipo = str_pad((string) ($invoice->cbte_tipo ?? 0), 3, '0', STR_PAD_LEFT);
        $pv = str_pad((string) ($invoice->point_of_sale ?? 0), 5, '0', STR_PAD_LEFT);
        $nro = str_pad((string) ($invoice->cbte_nro ?? 0), 8, '0', STR_PAD_LEFT);

        return $tipo . '-' . $pv . '-' . $nro;
    }
}
