<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\ArcaInvoiceMail;
use App\Models\Arca\ArcaInvoice;
use App\Models\Arca\ArcaInvoiceItem;
use App\Models\Event\Booking;
use App\Services\Arca\ArcaInvoiceIssuer;
use App\Services\Arca\WsfeClient;
use App\Services\Billing\BookingFiscalCalculator;
use App\Models\BillingSetting;
use App\Services\Billing\CommissionInvoiceBuilder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
        ArcaInvoiceIssuer $issuer,
        WsfeClient $wsfe,
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
                    'booking_id'     => $booking->id,
                    'organizer_id'   => $booking->organizer_id,
                    'customer_id'    => $booking->customer_id,
                    'environment'    => config('arca.environment', 'homologation'),
                    'status'         => 'blocked',
                    'invoice_model'  => config('arca.invoice_model', 'customer_service_fee_invoice'),
                    'currency'       => 'ARS',
                    'error_message'  => 'La reserva no está pagada',
                ]);
                $invoice->save();
            });

            Log::info('ARCA invoice issuing skipped: booking is not paid.', [
                'booking_id'     => $booking->id,
                'payment_status' => $booking->paymentStatus,
            ]);

            return;
        }

        try {
            $calculation = $calculator->calculate($booking);
            $preview     = $builder->buildPreview($calculation);
            $payload     = $this->payloadFromInvoice($preview);

            // FASE A — transacción corta: persiste estado previo a llamar ARCA.
            // Reserva cbte_nro antes de la emisión para poder recuperarlo en reintentos.
            // getLastComprobante() se calcula ANTES de la transacción para no mantener
            // el row lock activo durante una llamada SOAP (puede tardar 2-10 segundos).
            $nextCbteNro = config('arca.enable_issuing')
                ? ($wsfe->getLastComprobante((int) config('arca.tipo_comprobante')) + 1)
                : null;

            $invoice     = null;
            $shouldIssue = false;

            DB::transaction(function () use ($preview, $payload, $nextCbteNro, &$invoice, &$shouldIssue): void {
                $inv = $this->findInvoiceForUpdate((int) $preview->booking_id);

                if ($inv?->isApproved()) {
                    $invoice = $inv;

                    return;
                }

                // Preserva cbte_nro de un intento anterior antes del fill()
                $reservedCbteNro = $inv?->cbte_nro;

                $inv ??= new ArcaInvoice(['booking_id' => $preview->booking_id]);
                $inv->fill($preview->getAttributes());
                $inv->arca_request = $payload;

                if ($preview->isBlocked()) {
                    $inv->save();
                    $this->syncItems($inv, $preview);
                    $invoice = $inv;

                    return;
                }

                if (!config('arca.enable_issuing')) {
                    $inv->status = 'ready';
                    $inv->save();
                    $this->syncItems($inv, $preview);
                    $invoice = $inv;

                    return;
                }

                // Restaura cbte_nro de un intento anterior, o usa el pre-calculado
                $inv->cbte_nro = $reservedCbteNro ?: $nextCbteNro;
                $inv->status   = 'issuing';
                $inv->save();

                $invoice     = $inv;
                $shouldIssue = true;
            });

            if (!$invoice || !$shouldIssue || $invoice->isApproved()) {
                return;
            }

            // FASE B — llamada a ARCA fuera de transacción (evita lock durante I/O de red).
            // En reintento: verifica si ARCA ya autorizó el número reservado antes de re-emitir.
            $tipoCbte = (int) config('arca.tipo_comprobante');
            $cbteNro  = (int) $invoice->cbte_nro;

            $response = $wsfe->recuperarSiYaEmitido($tipoCbte, $cbteNro);

            if ($response === null) {
                $payload['cbte_desde'] = $cbteNro;
                $payload['cbte_hasta'] = $cbteNro;
                $response = $issuer->issue($payload);
            } elseif (empty($response['cae']) && ($response['authorized_without_cae'] ?? false)) {
                // ARCA autorizó pero no tenemos CAE → buscar en histórico
                $historicalCae = $invoice->arca_response['cae'] ?? null;

                if ($historicalCae) {
                    $response['cae'] = $historicalCae;
                    Log::info('ARCA CAE recovered from historical response.', [
                        'booking_id' => $invoice->booking_id,
                        'cbte_nro'   => $cbteNro,
                        'cae'        => $historicalCae,
                    ]);
                } else {
                    Log::error('ARCA comprobante autorizado sin CAE disponible.', [
                        'booking_id' => $invoice->booking_id,
                        'cbte_nro'   => $cbteNro,
                    ]);
                }
            } else {
                Log::info('ARCA invoice recovered from previous emission.', [
                    'booking_id' => $invoice->booking_id,
                    'cbte_nro'   => $cbteNro,
                    'cae'        => $response['cae'],
                ]);
            }

            // FASE C — transacción corta: persiste CAE y encola email.
            DB::transaction(function () use ($invoice, $response, $preview, $booking): void {
                $inv = $this->findInvoiceForUpdate((int) $invoice->booking_id);

                if ($inv?->isApproved()) {
                    return;
                }

                $inv ??= $invoice;

                // Si no hay CAE, no marcar como approved → marcar como error para revisión manual
                if (empty($response['cae'])) {
                    $inv->fill([
                        'status'        => 'error',
                        'arca_response' => $response,
                        'error_code'    => 'CAE_MISSING',
                        'error_message' => 'ARCA autorizó el comprobante pero el CAE no está disponible. Requiere revisión manual.',
                    ]);
                    $inv->save();
                    $this->syncItems($inv, $preview);

                    return;
                }

                $inv->fill([
                    'status'        => 'approved',
                    'arca_response' => $response,
                    'cae'           => $response['cae'],
                    'cae_due_date'  => $this->normalizeArcaDate($response['cae_vencimiento'] ?? null),
                    'cbte_tipo'     => $response['cbte_tipo'] ?? $inv->cbte_tipo,
                    'cbte_nro'      => $response['cbte_nro'] ?? $inv->cbte_nro,
                    'point_of_sale' => $response['punto_venta'] ?? $inv->point_of_sale,
                    'issued_at'     => now(),
                    'error_code'    => null,
                    'error_message' => null,
                ]);
                $inv->save();
                $this->syncItems($inv, $preview);

                if (empty($booking->email)) {
                    Log::warning('ARCA invoice email skipped: booking has no email.', [
                        'booking_id' => $booking->id,
                    ]);
                } elseif (!BillingSetting::current()->send_arca_invoice_email) {
                    Log::info('ARCA invoice email skipped: disabled by settings.', [
                        'booking_id' => $booking->id,
                    ]);
                } else {
                    Mail::to($booking->email)->queue(new ArcaInvoiceMail($inv, $booking));
                    Log::info('ARCA invoice email queued.', [
                        'booking_id'      => $booking->id,
                        'email'           => $booking->email,
                        'arca_invoice_id' => $inv->id,
                    ]);
                }
            });

        } catch (Throwable $exception) {
            DB::transaction(function () use ($booking, $exception): void {
                $invoice = $this->findInvoiceForUpdate($booking->id);

                if ($invoice?->isApproved()) {
                    return;
                }

                $invoice ??= new ArcaInvoice(['booking_id' => $booking->id]);
                $invoice->fill([
                    'booking_id'    => $booking->id,
                    'organizer_id'  => $booking->organizer_id,
                    'customer_id'   => $booking->customer_id,
                    'environment'   => config('arca.environment', 'homologation'),
                    'status'        => 'error',
                    'invoice_model' => config('arca.invoice_model', 'customer_service_fee_invoice'),
                    'currency'      => 'ARS',
                    'error_code'    => (string) $exception->getCode(),
                    'error_message' => $exception->getMessage(),
                ]);
                $invoice->save();
            });

            Log::error('ARCA invoice issuing failed.', [
                'booking_id' => $booking->id,
                'error'      => $exception->getMessage(),
            ]);
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

        $today = now()->format('Ymd');
        $serviceFrom = $invoice->service_from?->format('Ymd') ?? $today;
        $serviceTo   = $invoice->service_to?->format('Ymd') ?? $today;
        $dueDate     = $invoice->due_date?->format('Ymd') ?? $today;

        $concepto = $invoice->concept ?? config('arca.concepto');

        $payload = [
            'concepto' => $concepto,
            'doc_tipo' => $invoice->doc_tipo ?? config('arca.tipo_documento'),
            'doc_nro' => $invoice->doc_nro ?? '0',
            'fecha' => $today,
            'imp_total' => (float) ($invoice->total_amount ?? 0),
            'imp_tot_conc' => (float) ($invoice->non_taxed_amount ?? 0),
            'imp_neto' => $netAmount,
            'imp_op_ex' => (float) ($invoice->exempt_amount ?? 0),
            'imp_iva' => $vatAmount,
            'imp_trib' => 0,
            'imp_autop' => 0,
            'moneda' => config('arca.moneda', 'PES'),
            'moneda_ctz' => config('arca.moneda_ctz', 1),
            'condicion_iva_receptor_id' => $this->condicionIvaReceptorId($invoice->recipient_tax_condition),
        ];

        if ((int) $concepto === 2 || (int) $concepto === 3) {
            $payload['fch_serv_desde'] = $serviceFrom;
            $payload['fch_serv_hasta'] = $serviceTo;
            $payload['fch_vto_pago']   = $dueDate;
        }

        if ($vatAmount > 0) {
            $payload['iva'] = [[
                'id' => config('arca.iva_id'),
                'base' => $netAmount,
                'importe' => $vatAmount,
            ]];
        } elseif ($netAmount > 0) {
            $payload['iva'] = [[
                'id' => 3,
                'base' => $netAmount,
                'importe' => 0,
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

    private function condicionIvaReceptorId(?string $condition): int
    {
        return match ($condition) {
            'responsable_inscripto' => 1,
            'exento' => 4,
            'monotributo' => 6,
            'no_responsable' => 15,
            default => 5,
        };
    }

    private function isPaid(string $status): bool
    {
        return in_array(strtolower($status), ['paid', 'completed', 'success'], true);
    }
}
