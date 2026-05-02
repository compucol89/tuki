<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Arca\ArcaInvoice;
use App\Models\Arca\ArcaInvoiceItem;
use App\Models\Event\Booking;
use App\Services\Arca\ArcaInvoiceIssuer;
use App\Services\Billing\BookingFiscalCalculator;
use App\Services\Billing\CommissionInvoiceBuilder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ArcaInvoiceIssuingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 600;

    public function __construct(private readonly int $bookingId)
    {
    }

    public function handle(
        BookingFiscalCalculator $calculator,
        CommissionInvoiceBuilder $builder,
        ArcaInvoiceIssuer $issuer
    ): void {
        $booking = Booking::with(['evnt.information', 'organizer.organizer_info', 'customerInfo.fiscalProfile', 'fiscalProfile'])
            ->find($this->bookingId);

        if (!$booking) {
            Log::warning('ARCA invoice issuing skipped: booking not found.', [
                'booking_id' => $this->bookingId,
            ]);

            return;
        }

        if (!$this->isPaid((string) ($booking->paymentStatus ?? ''))) {
            DB::transaction(function () use ($booking): void {
                $invoice = $this->findInvoiceForUpdate($booking->id);

                if ($invoice?->isApproved()) {
                    return;
                }

                $invoice ??= new ArcaInvoice(['booking_id' => $booking->id]);
                $invoice->fill([
                    'booking_id' => $booking->id,
                    'organizer_id' => $booking->organizer_id,
                    'customer_id' => $booking->customer_id,
                    'environment' => config('arca.environment', 'homologation'),
                    'status' => 'blocked',
                    'invoice_model' => config('arca.invoice_model', 'customer_service_fee_invoice'),
                    'currency' => 'ARS',
                    'error_message' => 'La reserva no está pagada',
                ]);
                $invoice->save();
            });

            Log::info('ARCA invoice issuing skipped: booking is not paid.', [
                'booking_id' => $booking->id,
                'payment_status' => $booking->paymentStatus,
            ]);

            return;
        }

        try {
            $calculation = $calculator->calculate($booking);
            $preview = $builder->buildPreview($calculation);
            $payload = $this->payloadFromInvoice($preview);

            DB::transaction(function () use ($preview, $payload, $issuer): void {
                $invoice = $this->findInvoiceForUpdate((int) $preview->booking_id);

                if ($invoice?->isApproved()) {
                    return;
                }

                $invoice ??= new ArcaInvoice(['booking_id' => $preview->booking_id]);
                $invoice->fill($preview->getAttributes());
                $invoice->arca_request = $payload;

                if ($preview->isBlocked()) {
                    $invoice->save();
                    $this->syncItems($invoice, $preview);

                    return;
                }

                if (!config('arca.enable_issuing')) {
                    $invoice->status = 'ready';
                    $invoice->save();
                    $this->syncItems($invoice, $preview);

                    return;
                }

                $response = $issuer->issue($payload);

                $invoice->fill([
                    'status' => 'approved',
                    'arca_response' => $response,
                    'cae' => $response['cae'] ?? null,
                    'cae_due_date' => $this->normalizeArcaDate($response['cae_vencimiento'] ?? null),
                    'cbte_tipo' => $response['cbte_tipo'] ?? $invoice->cbte_tipo,
                    'cbte_nro' => $response['cbte_nro'] ?? $invoice->cbte_nro,
                    'point_of_sale' => $response['punto_venta'] ?? $invoice->point_of_sale,
                    'issued_at' => now(),
                    'error_code' => null,
                    'error_message' => null,
                ]);
                $invoice->save();
                $this->syncItems($invoice, $preview);
            });
        } catch (Throwable $exception) {
            DB::transaction(function () use ($booking, $exception): void {
                $invoice = $this->findInvoiceForUpdate($booking->id);

                if ($invoice?->isApproved()) {
                    return;
                }

                $invoice ??= new ArcaInvoice(['booking_id' => $booking->id]);
                $invoice->fill([
                    'booking_id' => $booking->id,
                    'organizer_id' => $booking->organizer_id,
                    'customer_id' => $booking->customer_id,
                    'environment' => config('arca.environment', 'homologation'),
                    'status' => 'error',
                    'invoice_model' => config('arca.invoice_model', 'customer_service_fee_invoice'),
                    'currency' => 'ARS',
                    'error_code' => (string) $exception->getCode(),
                    'error_message' => $exception->getMessage(),
                ]);
                $invoice->save();
            });

            Log::error('ARCA invoice issuing failed.', [
                'booking_id' => $booking->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    private function findInvoiceForUpdate(int $bookingId): ?ArcaInvoice
    {
        return ArcaInvoice::where('booking_id', $bookingId)->lockForUpdate()->first();
    }

    private function syncItems(ArcaInvoice $invoice, ArcaInvoice $preview): void
    {
        $items = $preview->relationLoaded('items') ? $preview->getRelation('items') : collect();

        $invoice->items()->delete();

        foreach ($items as $item) {
            if (!$item instanceof ArcaInvoiceItem) {
                continue;
            }

            $invoice->items()->create($item->getAttributes());
        }
    }

    private function payloadFromInvoice(ArcaInvoice $invoice): array
    {
        $vatAmount = (float) ($invoice->vat_amount ?? 0);
        $netAmount = (float) ($invoice->net_amount ?? 0);

        $payload = [
            'concepto' => $invoice->concept ?? config('arca.concepto'),
            'doc_tipo' => $invoice->doc_tipo ?? config('arca.tipo_documento'),
            'doc_nro' => $invoice->doc_nro ?? '0',
            'fecha' => now()->format('Ymd'),
            'imp_total' => (float) ($invoice->total_amount ?? 0),
            'imp_tot_conc' => (float) ($invoice->non_taxed_amount ?? 0),
            'imp_neto' => $netAmount,
            'imp_op_ex' => (float) ($invoice->exempt_amount ?? 0),
            'imp_iva' => $vatAmount,
            'imp_trib' => 0,
            'imp_autop' => 0,
            'moneda' => config('arca.moneda', 'PES'),
            'moneda_ctz' => config('arca.moneda_ctz', 1),
        ];

        if ($vatAmount > 0) {
            $payload['iva'] = [[
                'id' => config('arca.iva_id'),
                'base' => $netAmount,
                'importe' => $vatAmount,
            ]];
        }

        return $payload;
    }

    private function normalizeArcaDate(?string $date): ?string
    {
        if (!$date) {
            return null;
        }

        $normalized = preg_replace('/\D/', '', $date);

        if (!is_string($normalized) || strlen($normalized) !== 8) {
            return null;
        }

        return substr($normalized, 0, 4) . '-' . substr($normalized, 4, 2) . '-' . substr($normalized, 6, 2);
    }

    private function isPaid(string $status): bool
    {
        return in_array(strtolower($status), ['paid', 'completed', 'success'], true);
    }
}
