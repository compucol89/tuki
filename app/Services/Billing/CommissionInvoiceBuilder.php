<?php

declare(strict_types=1);

namespace App\Services\Billing;

use App\Models\Arca\ArcaInvoice;
use App\Models\Arca\ArcaInvoiceItem;
use App\Models\BillingSetting;
use Illuminate\Database\Eloquent\Collection;

class CommissionInvoiceBuilder
{
    public function buildPreview(array $calculation): ArcaInvoice
    {
        $blockedReasons = $calculation['blocked_reasons'] ?? [];
        $billing = BillingSetting::current();

        $invoice = new ArcaInvoice([
            'booking_id' => $calculation['booking_id'] ?? null,
            'organizer_id' => $calculation['organizer_id'] ?? null,
            'customer_id' => $calculation['customer_id'] ?? null,
            'environment' => config('arca.environment', 'homologation'),
            'status' => empty($blockedReasons) ? 'ready' : 'blocked',
            'invoice_model' => config('arca.invoice_model', 'customer_service_fee_invoice'),
            'currency' => 'ARS',
            'point_of_sale' => config('arca.punto_venta'),
            'cbte_tipo' => config('arca.tipo_comprobante'),
            'concept' => config('arca.concepto'),
            'doc_tipo' => $this->documentTypeCode($calculation['recipient']['document_type'] ?? null),
            'doc_nro' => $calculation['recipient']['tax_id'] ?? null,
            'recipient_name' => $calculation['recipient']['name'] ?? null,
            'recipient_tax_condition' => $calculation['recipient']['tax_condition'] ?? null,
            'recipient_tax_id' => $calculation['recipient']['tax_id'] ?? null,
            'recipient_address' => $calculation['recipient']['address'] ?? null,
            'net_amount' => $calculation['taxable_amount_for_tukipass'] ?? 0,
            'vat_amount' => $calculation['vat_amount'] ?? 0,
            'exempt_amount' => 0,
            'non_taxed_amount' => 0,
            'total_amount' => $calculation['invoice_total'] ?? 0,
            'commission_rate' => $calculation['platform_commission_rate'] ?? null,
            'commission_base_amount' => $calculation['organizer_gross_amount'] ?? null,
            'commission_amount' => $calculation['platform_commission_amount'] ?? null,
            'service_fee_percentage_used' => $calculation['service_fee_percentage_used'] ?? null,
            'service_fee_tax_mode_used' => $calculation['service_fee_tax_mode_used'] ?? null,
            'vat_percentage_used' => $calculation['vat_percentage_used'] ?? null,
            'issuer_cuit_used' => $billing->issuer_cuit,
            'invoice_type_used' => $billing->default_invoice_type,
            'point_of_sale_used' => $billing->point_of_sale,
            'error_message' => empty($blockedReasons) ? null : implode(' | ', $blockedReasons),
        ]);

        $item = new ArcaInvoiceItem([
            'description' => 'Comisión TukiPass por venta de entradas',
            'quantity' => 1,
            'unit_price' => $calculation['taxable_amount_for_tukipass'] ?? 0,
            'net_amount' => $calculation['taxable_amount_for_tukipass'] ?? 0,
            'vat_rate' => $calculation['vat_rate'] ?? 0,
            'vat_amount' => $calculation['vat_amount'] ?? 0,
            'total_amount' => $calculation['invoice_total'] ?? 0,
            'metadata' => [
                'booking_id' => $calculation['booking_id'] ?? null,
                'event_id' => $calculation['event_id'] ?? null,
                'warnings' => $calculation['warnings'] ?? [],
                'blocked_reasons' => $blockedReasons,
            ],
        ]);

        $invoice->setRelation('items', new Collection([$item]));

        return $invoice;
    }

    private function documentTypeCode(?string $documentType): int
    {
        return match ($documentType) {
            'CUIT', 'CUIL' => 80,
            'PASAPORTE' => 94,
            default => 96,
        };
    }
}
