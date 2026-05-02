<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Event\Booking;
use App\Services\Billing\BookingFiscalCalculator;
use App\Services\Billing\CommissionInvoiceBuilder;
use Illuminate\Console\Command;

class ArcaPreviewBookingInvoice extends Command
{
    protected $signature = 'arca:preview-booking-invoice {booking_id}';

    protected $description = 'Previsualiza la factura de comisión ARCA para una reserva sin emitir CAE.';

    public function handle(BookingFiscalCalculator $calculator, CommissionInvoiceBuilder $builder): int
    {
        $booking = Booking::with(['evnt.information', 'organizer.organizer_info', 'customerInfo.fiscalProfile', 'fiscalProfile'])
            ->find($this->argument('booking_id'));

        if (!$booking) {
            $this->error('Booking no encontrado.');

            return self::FAILURE;
        }

        $calculation = $calculator->calculate($booking);
        $invoice = $builder->buildPreview($calculation);

        $this->info('ARCA preview de factura al cliente');
        $this->line('Emisor: ' . (config('arca.issuer_name') ?: 'No configurado'));
        $this->line('Receptor: cliente/comprador');
        $this->line("Booking ID: {$calculation['booking_id']}");
        $this->line("Event ID: {$calculation['event_id']}");
        $this->line("Organizer ID: {$calculation['organizer_id']}");
        $this->line("Customer ID: {$calculation['customer_id']}");
        $this->line("Payment status: {$calculation['payment_status']}");
        $this->line('Evento: ' . ($booking->evnt?->information?->title ?? 'no disponible'));
        $this->line('Organizador: ' . ($booking->organizer?->organizer_info?->name ?? $booking->organizer?->email ?? 'no disponible') . ' (referencia operativa)');
        $buyerName = trim((string) (($booking->customerInfo?->fname ?? '') . ' ' . ($booking->customerInfo?->lname ?? '')));
        $this->line('Comprador: ' . ($buyerName !== '' ? $buyerName : ($booking->customerInfo?->email ?? 'no disponible')));
        $this->newLine();

        $this->line('Precio base entrada: ARS ' . number_format($calculation['ticket_amount'], 2, ',', '.'));
        $this->line('Cantidad: ' . number_format($calculation['quantity'], 2, ',', '.'));
        $this->line('Monto organizador: ARS ' . number_format($calculation['organizer_gross_amount'], 2, ',', '.'));
        $this->line('Comisión: ' . number_format($calculation['platform_commission_rate'] * 100, 2, ',', '.') . '%');
        $this->line('Comisión ARS (base servicio): ' . number_format($calculation['platform_commission_amount'], 2, ',', '.'));
        $this->newLine();
        $this->info('Billing settings (dinámico)');
        $this->line('Porcentaje comisión de servicio usado (%): ' . number_format((float) ($calculation['service_fee_percentage_used'] ?? 0), 4, ',', '.'));
        $this->line('Modo IVA comisión: ' . ($calculation['service_fee_tax_mode_used'] ?? '—'));
        $this->line('IVA (%): ' . number_format((float) ($calculation['vat_percentage_used'] ?? 0), 4, ',', '.'));
        $this->newLine();
        $this->line('Neto factura TukiPass: ARS ' . number_format($calculation['taxable_amount_for_tukipass'], 2, ',', '.'));
        $this->line('IVA factura TukiPass: ARS ' . number_format($calculation['vat_amount'], 2, ',', '.'));
        $this->line('Total factura TukiPass: ARS ' . number_format($calculation['invoice_total'], 2, ',', '.'));
        $this->line('Total booking estimado (organizador + factura TukiPass): ARS ' . number_format($calculation['buyer_total_estimated'], 2, ',', '.'));
        $this->line("Estado: {$invoice->status}");

        $this->printList('Warnings', $calculation['warnings']);
        $this->printList('Blocked reasons', $calculation['blocked_reasons']);

        return self::SUCCESS;
    }

    private function printList(string $title, array $items): void
    {
        if (empty($items)) {
            return;
        }

        $this->newLine();
        $this->warn($title . ':');
        foreach ($items as $item) {
            $this->line("- {$item}");
        }
    }
}
