<?php

declare(strict_types=1);

namespace App\Services\Billing;

use App\Models\BillingSetting;
use App\Models\Event\Booking;
use App\Models\CustomerFiscalProfile;

class BookingFiscalCalculator
{
    public function calculate(Booking $booking): array
    {
        $warnings = [];
        $blockedReasons = [];

        $settings = BillingSetting::current();
        $feePct = (float) ($settings->service_fee_percentage ?? 0);
        $vatPct = (float) ($settings->vat_percentage ?? 0);
        $taxMode = (string) ($settings->service_fee_tax_mode ?? 'no_vat_added');
        if (!in_array($taxMode, ['no_vat_added', 'vat_added', 'vat_included'], true)) {
            $taxMode = 'no_vat_added';
        }

        $ticketAmount = $this->money((float) ($booking->price ?? 0));
        $quantity = (float) ($booking->quantity ?? 1);
        $organizerGrossAmount = $this->money($ticketAmount * max($quantity, 1));

        [$commissionRate, $commissionAmount, $commissionWarning, $serviceFeePercentageUsed] = $this->resolveCommission(
            $booking,
            $organizerGrossAmount,
            $feePct
        );
        if ($commissionWarning !== null) {
            $warnings[] = $commissionWarning;
        }
        $warnings[] = 'Modelo fiscal pendiente de validación contable.';
        $warnings[] = 'Factura al cliente: confirmar si corresponde comisión o total cobrado.';
        $warnings[] = 'Tayrona Group SAS es Responsable Inscripto; revisar tipo de comprobante A/B.';

        if ($commissionAmount <= 0) {
            $blockedReasons[] = 'La comisión calculada debe ser mayor a cero';
        }

        [$netAmount, $vatAmount, $invoiceTotal] = $this->splitCommissionTax($commissionAmount, $taxMode, $vatPct);

        if (($taxMode === 'vat_added' || $taxMode === 'vat_included') && $vatPct <= 0) {
            $warnings[] = 'Modo IVA con porcentaje 0 en billing_settings; IVA resultante 0.';
        }

        $vatRateFraction = $vatPct > 0 ? $this->rate($vatPct / 100) : 0.0;
        $buyerTotalEstimated = $this->money($organizerGrossAmount + $invoiceTotal);

        if (!$this->isPaid((string) ($booking->paymentStatus ?? ''))) {
            $blockedReasons[] = 'La reserva no está pagada';
        }

        $recipient = $this->resolveRecipient($booking);
        foreach ($this->missingFiscalData($recipient) as $missingReason) {
            $blockedReasons[] = $missingReason;
        }

        return [
            'booking_id' => $booking->id,
            'event_id' => $booking->event_id,
            'organizer_id' => $booking->organizer_id,
            'customer_id' => $booking->customer_id,
            'payment_status' => $booking->paymentStatus,
            'ticket_amount' => $ticketAmount,
            'quantity' => $quantity,
            'organizer_gross_amount' => $organizerGrossAmount,
            'platform_commission_rate' => $commissionRate,
            'platform_commission_amount' => $commissionAmount,
            'buyer_total_estimated' => $buyerTotalEstimated,
            'taxable_amount_for_tukipass' => $netAmount,
            'vat_rate' => $vatRateFraction,
            'vat_amount' => $vatAmount,
            'invoice_total' => $invoiceTotal,
            'service_fee_percentage_used' => $this->rate($serviceFeePercentageUsed),
            'service_fee_tax_mode_used' => $taxMode,
            'vat_percentage_used' => $this->rate($vatPct),
            'warnings' => array_values(array_unique($warnings)),
            'blocked_reasons' => array_values(array_unique($blockedReasons)),
            'recipient' => $recipient,
        ];
    }

    /**
     * @return array{0: float, 1: float, 2: string|null, 3: float} rate, amount, warning, service_fee_percentage_used (0-100)
     */
    private function resolveCommission(Booking $booking, float $baseAmount, float $serviceFeePercentage): array
    {
        $persistedCommission = (float) ($booking->commission ?? 0);
        if ($persistedCommission > 0) {
            $percentage = (float) ($booking->commission_percentage ?? 0);
            $rate = $percentage > 0 ? $percentage / 100 : ($baseAmount > 0 ? $persistedCommission / $baseAmount : 0);
            $pctUsed = $percentage > 0 ? $percentage : ($baseAmount > 0 ? round(($persistedCommission / $baseAmount) * 100, 4) : 0.0);

            return [$this->rate($rate), $this->money($persistedCommission), null, (float) $pctUsed];
        }

        $defaultRate = $serviceFeePercentage / 100;
        $warning = 'Comisión no persistida; usando porcentaje de billing_settings';

        return [
            $this->rate($defaultRate),
            $this->money($baseAmount * $defaultRate),
            $warning,
            (float) $serviceFeePercentage,
        ];
    }

    /**
     * @return array{0: float, 1: float, 2: float} net, vat, total (ARS, 2 decimals)
     */
    private function splitCommissionTax(float $commissionAmount, string $mode, float $vatPct): array
    {
        $commissionAmount = $this->money($commissionAmount);

        if ($mode === 'no_vat_added') {
            return [$commissionAmount, 0.0, $commissionAmount];
        }

        if ($mode === 'vat_added') {
            $net = $commissionAmount;
            $vat = $this->money($net * ($vatPct / 100));
            $total = $this->money($net + $vat);

            return [$net, $vat, $total];
        }

        $total = $commissionAmount;
        if ($vatPct <= 0) {
            return [$total, 0.0, $total];
        }

        $net = $this->money($total / (1 + $vatPct / 100));
        $vat = $this->money($total - $net);

        return [$net, $vat, $total];
    }

    private function resolveRecipient(Booking $booking): array
    {
        $customer = $booking->relationLoaded('customerInfo') ? $booking->getRelation('customerInfo') : null;
        $customerProfile = $customer && $customer->relationLoaded('fiscalProfile') ? $customer->getRelation('fiscalProfile') : null;

        if ($customerProfile instanceof CustomerFiscalProfile) {
            return $this->recipientFromProfile($customerProfile);
        }

        $bookingProfile = $booking->relationLoaded('fiscalProfile') ? $booking->getRelation('fiscalProfile') : null;

        if ($bookingProfile instanceof CustomerFiscalProfile) {
            return $this->recipientFromProfile($bookingProfile);
        }

        return [
            'name' => trim((string) (($booking->fname ?? '') . ' ' . ($booking->lname ?? ''))) ?: null,
            'tax_condition' => null,
            'tax_id' => null,
            'document_type' => null,
            'document_number' => null,
            'address' => $booking->address ?: null,
            'email' => $booking->email ?: null,
        ];
    }

    private function recipientFromProfile(CustomerFiscalProfile $profile): array
    {
        return [
            'name' => $profile->full_name,
            'tax_condition' => $profile->iva_condition,
            'tax_id' => $profile->document_number,
            'document_type' => $profile->document_type,
            'document_number' => $profile->document_number,
            'address' => $profile->fiscal_address,
            'email' => $profile->fiscal_email,
        ];
    }

    private function missingFiscalData(array $recipient): array
    {
        $missing = [];
        if (empty($recipient['name'])) {
            $missing[] = 'Falta nombre o razón social del cliente';
        }
        if (empty($recipient['document_type'])) {
            $missing[] = 'Falta tipo de documento del cliente';
        }
        if (empty($recipient['document_number'])) {
            $missing[] = 'Falta número de documento del cliente';
        }
        if (empty($recipient['tax_condition'])) {
            $missing[] = 'Falta condición IVA del cliente';
        }

        return $missing;
    }

    private function isPaid(string $status): bool
    {
        return in_array(strtolower($status), ['paid', 'completed', 'success'], true);
    }

    private function money(float $value): float
    {
        return round($value, 2);
    }

    private function rate(float $value): float
    {
        return round($value, 4);
    }
}
