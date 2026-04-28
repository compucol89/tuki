# ARCA - Facturacion al cliente

El modelo fiscal vigente para preview considera a Tayrona Group SAS / TAYRONA - GROUP como emisor y al cliente/comprador como receptor fiscal.

La rama previa de datos fiscales del organizador queda descartada para ARCA. El organizador se conserva solo como referencia operativa del evento y para conciliacion/liquidacion.

## Estado

- `ARCA_ENABLE_ISSUING=false` debe permanecer deshabilitado.
- No se emite CAE.
- No se llama `FECAESolicitar`.
- El preview usa `Booking.commission` como importe primario del servicio.
- `ARCA_DEFAULT_COMMISSION_RATE` se usa solo como fallback tecnico de preview.

## Datos fiscales del cliente

La estructura nueva es `customer_fiscal_profiles`, compatible con clientes registrados y guest bookings.

Campos principales:

- `customer_id` nullable para clientes registrados.
- `booking_id` nullable para compras invitadas.
- `full_name`.
- `document_type`.
- `document_number`.
- `iva_condition`.
- `fiscal_address` opcional hasta definicion contable.
- `fiscal_email` opcional.

## Decisiones contables pendientes

- Si Tayrona factura solo la comision o el total cobrado.
- Si la comision es IVA incluido o mas IVA.
- Tipo de comprobante ARCA para responsable inscripto: A/B segun receptor. Revisar si Factura C queda descartada.
- Tratamiento de cuenta y orden, liquidacion al organizador, descuentos, cancelaciones y notas de credito.
