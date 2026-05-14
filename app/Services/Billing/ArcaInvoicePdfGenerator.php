<?php

declare(strict_types=1);

namespace App\Services\Billing;

use App\Models\Arca\ArcaInvoice;
use App\Models\BillingSetting;
use App\Models\Event\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ArcaInvoicePdfGenerator
{
    public function generate(ArcaInvoice $invoice, Booking $booking): string
    {
        $billing = BillingSetting::current();
        $pdf = Pdf::loadView('pdf.arca_invoice', [
            'invoice' => $invoice,
            'booking' => $booking,
            'billing' => $billing,
            'invoiceNumber' => $this->formatInvoiceNumber($invoice),
        ]);

        $filename = 'arca_invoices/invoice_' . $invoice->id . '_' . time() . '.pdf';
        Storage::disk('public')->makeDirectory('arca_invoices');
        Storage::disk('public')->put($filename, $pdf->output());

        return Storage::disk('public')->path($filename);
    }

    private function formatInvoiceNumber(ArcaInvoice $invoice): string
    {
        $tipo = str_pad((string) ($invoice->cbte_tipo ?? 0), 3, '0', STR_PAD_LEFT);
        $pv = str_pad((string) ($invoice->point_of_sale ?? 0), 5, '0', STR_PAD_LEFT);
        $nro = str_pad((string) ($invoice->cbte_nro ?? 0), 8, '0', STR_PAD_LEFT);

        return $tipo . '-' . $pv . '-' . $nro;
    }
}
