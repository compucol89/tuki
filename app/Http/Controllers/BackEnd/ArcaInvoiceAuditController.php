<?php

declare(strict_types=1);

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Jobs\ArcaInvoiceIssuingJob;
use App\Models\Arca\ArcaInvoice;
use App\Services\Billing\ArcaInvoicePdfGenerator;
use Illuminate\Http\Request;

class ArcaInvoiceAuditController extends Controller
{
    public function index(Request $request)
    {
        $query = ArcaInvoice::with(['booking', 'organizer', 'customer', 'items'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('cae', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%")
                  ->orWhereHas('booking', function ($bq) use ($search) {
                      $bq->where('fname', 'like', "%{$search}%")
                         ->orWhere('lname', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $invoices = $query->paginate(20)->withQueryString();

        return view('backend.arca-invoices.index', compact('invoices'));
    }

    public function show($id)
    {
        $invoice = ArcaInvoice::with(['booking', 'organizer', 'customer', 'items'])->findOrFail($id);

        return view('backend.arca-invoices.show', compact('invoice'));
    }

    public function retry($id)
    {
        $invoice = ArcaInvoice::findOrFail($id);

        if (!in_array($invoice->status, ['error', 'blocked'], true)) {
            return redirect()->back()->with('warning', __('Solo se pueden reintentar facturas con estado error o bloqueado.'));
        }

        if (!$invoice->booking_id) {
            return redirect()->back()->with('error', __('La factura no tiene una reserva asociada.'));
        }

        ArcaInvoiceIssuingJob::dispatch($invoice->booking_id);

        return redirect()->back()->with('success', __('Emisión reintentada. El job se procesará en segundo plano.'));
    }

    public function downloadPdf(Request $request, $id)
    {
        $invoice = ArcaInvoice::with('items')->findOrFail($id);

        if (!$invoice->booking) {
            return redirect()->back()->with('error', __('La factura no tiene una reserva asociada.'));
        }

        $pdfPath = app(ArcaInvoicePdfGenerator::class)->generate($invoice, $invoice->booking);

        $invoiceNumber = str_pad((string) ($invoice->cbte_tipo ?? 0), 3, '0', STR_PAD_LEFT) . '-'
            . str_pad((string) ($invoice->point_of_sale ?? 0), 5, '0', STR_PAD_LEFT) . '-'
            . str_pad((string) ($invoice->cbte_nro ?? 0), 8, '0', STR_PAD_LEFT);

        $fileName = 'Factura_' . $invoiceNumber . '.pdf';

        if ($request->boolean('inline')) {
            return response()->file($pdfPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            ]);
        }

        return response()->download($pdfPath, $fileName);
    }
}
