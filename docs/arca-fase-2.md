# ARCA Fase 2 - Preview fiscal

Esta fase agrega una capa interna para calcular y previsualizar comprobantes fiscales sin emisión real.

No emite CAE, no llama `FECAESolicitar`, no se conecta al checkout y no modifica reservas, pagos, stock ni gateways.

## Alcance

- Tablas nuevas: `arca_invoices` y `arca_invoice_items`.
- Modelos nuevos en `App\Models\Arca`.
- Servicios nuevos en `App\Services\Billing`.
- Issuer futuro en `App\Services\Arca\ArcaInvoiceIssuer`, bloqueado por `ARCA_ENABLE_ISSUING=false`.
- Comando de preview: `php artisan arca:preview-booking-invoice {booking_id}`.

## Modelo fiscal vigente

El modelo vigente para preview es facturación de Tayrona Group SAS al cliente/comprador. El organizador queda solo como referencia operativa del evento.

Ver `docs/arca-facturacion-cliente.md` para el detalle. Comisión vs total cobrado, IVA y tipo de comprobante requieren validación contable antes de emisión real.

## Reglas de preview

- Si la reserva no está pagada, el preview queda `blocked`.
- Si faltan datos fiscales mínimos del cliente, el preview queda `blocked`.
- Si existe comisión persistida en la reserva, se usa esa comisión.
- Si no existe comisión persistida, se usa `arca.default_commission_rate` solo para preview y se agrega warning.
- Si no existe IVA configurado, el preview usa IVA 0 y agrega warning.

## Pendientes para fases siguientes

- Confirmar con contador si se factura comisión o total cobrado.
- Confirmar con contador tipo de comprobante y tratamiento IVA.
- Validar datos fiscales mínimos por tipo de cliente.
- Diseñar notas de crédito para cancelaciones y reembolsos.
- Habilitar emisión real solo con `ARCA_ENABLE_ISSUING=true`.
