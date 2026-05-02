# ARCA Billing Fase 1 - Plan de integracion

> Estado: Fase 1A + 1C validadas (tecnica y visual). **Fase 1B bloqueada** hasta confirmacion contable por escrito. No activar emision ni integrar pagos hasta entonces.

## Estado Fase 1A + 1C — Validada

Cierre documental (sin cambios de runtime en este paso):

| Verificacion | Resultado |
|--------------|-----------|
| Commit base (admin + snapshot fiscal) | `1d05670` |
| Commit fix colision `$settings` / `$billingSettings` | `6080cb5` |
| DB Docker | Migraciones aplicadas; tabla `billing_settings`; columnas snapshot en `arca_invoices` |
| Panel admin `/admin/basic-settings/billing-settings` | Carga sin Ignition; sin `Undefined property: stdClass::$enabled` |
| Bug variable | Colision con `$settings` global del layout eliminada (vista + controller usan `$billingSettings`) |
| `billing_settings.enabled` | `false` |
| `billing_settings.environment` | `testing` |
| `ArcaInvoice::count()` | `0` |
| Cola `jobs` | `0` |
| Pagos / checkout / gateways | No modificados en esta fase |
| Calculators / WSFE / jobs emision | No modificados para activar emision |

Pendientes de verificacion menor (no bloquean el cierre 1A+1C):

- Submit del formulario admin no probado en todos los entornos.
- Campo "Emision automatica" y alerta amarilla pueden no haberse capturado en viewport; defaults en DB son seguros.

## Bloqueantes para Fase 1B

No implementar Fase 1B hasta que el asesor contable responda **por escrito** al menos:

1. **Base imponible:** TukiPass factura solo la **comision de servicio** o el **total de la entrada** cobrada al comprador?
2. **IVA:** La comision se trata como **sin IVA agregado** (`no_vat_added`), **IVA agregado** (`vat_added`) o **IVA incluido** (`vat_included`), segun modelo validado?
3. **Codigos ARCA:** Confirmar tipos de comprobante (referencia comun A=2, B=6, C=11) segun emisor, receptor y operacion; no asumir sin validacion.
4. **Datos fiscales:** Se exigen **antes** del pago, **despues** del pago, o solo al momento de emitir?
5. **Integracion con pagos:** **Event/Listener** global (ej. tras pago confirmado) o **dispatch manual** por gateway?

Hasta tener esas respuestas: no modificar `BookingFiscalCalculator`, `CommissionInvoiceBuilder`, `WsfeClient`, `ArcaInvoiceIssuingJob` para produccion; no integrar checkout ni gateways; no poner `enabled=true` ni `environment=production` en `billing_settings` por decision operativa sin acta contable.

## Stop conditions documentales (Fase 1B)

Hasta confirmacion contable por escrito:

- No modificar calculator fiscal para nuevas reglas de negocio.
- No modificar WSFE para emision real mas alla de pruebas ya aisladas.
- No modificar jobs de emision para disparo desde pago.
- No integrar pagos ni listeners globales de pago para ARCA.
- No tocar checkout ni vistas de pago para este fin.
- No emitir factura ARCA en produccion.
- No activar `billing_settings.enabled = true` como politica de producto.

## Objetivo

Preparar la integracion real de facturacion ARCA para ventas confirmadas de TukiPass sin romper pagos, checkout, balances ni recibos existentes.

La Fase 1 debe convertir el preview/issuer existente en un flujo administrable, auditable e idempotente. No debe activar emision real en produccion sin flag explicito.

## Estado real verificado

Componentes existentes:

- `app/Services/Arca/WsaaClient.php`
- `app/Services/Arca/WsfeClient.php`
- `app/Services/Arca/ArcaInvoiceIssuer.php`
- `app/Services/Billing/BookingFiscalCalculator.php`
- `app/Services/Billing/CommissionInvoiceBuilder.php`
- `app/Jobs/ArcaInvoiceIssuingJob.php`
- `app/Models/Arca/ArcaInvoice.php`
- `app/Models/Arca/ArcaInvoiceItem.php`
- `app/Models/CustomerFiscalProfile.php`
- `config/arca.php`
- `app/Console/Commands/ArcaPreviewBookingInvoice.php`
- `app/Console/Commands/ArcaTestConnection.php`

Estado actual:

- `ArcaInvoiceIssuingJob` existe, pero no se dispara desde el flujo de pago.
- `BookingInvoiceJob` sigue siendo el flujo de recibo/PDF interno.
- `config/arca.php` conserva valores hardcodeados para preview: `tipo_comprobante`, `default_commission_rate`, `default_vat_rate`.
- `BookingFiscalCalculator` calcula sobre comision persistida o fallback de config.
- `CustomerFiscalProfile` existe, pero el checkout no fuerza captura completa de datos fiscales.
- `docs/arca-facturacion-cliente.md` define el modelo vigente como Tayrona Group SAS facturando al cliente/comprador, sujeto a validacion contable.
- `docs/arca-fase-2.md` documenta el preview fiscal sin emision CAE.

## Decisiones humanas obligatorias

1. `billing_settings` separada vs `basic_settings`
   - Recomendacion tecnica: crear `billing_settings`.
   - Motivo: aislar configuracion fiscal, evitar contaminar settings generales y dejar auditoria clara de cambios fiscales.

2. Codigos ARCA y tipo de comprobante
   - Validar con contador antes de implementar.
   - Pendiente confirmar: A=2, B=6, C=11.
   - No asumir que monotributo recibe C si Tayrona es Responsable Inscripto; validar receptor, emisor y tratamiento.

3. Punto de disparo del job fiscal
   - Opcion A: dispatch manual en cada gateway luego de `storeData`.
   - Opcion B: evento `BookingPaymentCompleted` + listener ARCA.
   - Recomendacion tecnica: Event/Listener si se quiere cubrir multiples gateways con menos duplicacion; dispatch manual si se prioriza cambio minimo inicial.

4. Tratamiento de IVA
   - Confirmar si la comision se cobra con IVA agregado, IVA incluido o sin IVA agregado.
   - Confirmar tratamiento de Factura B emitida por Responsable Inscripto.

5. Datos fiscales en checkout
   - Definir si son obligatorios antes de pagar o si se permite completar luego.
   - Definir datos minimos por tipo de receptor: consumidor final, monotributo, responsable inscripto, extranjero.

## Alcance propuesto para Fase 1

No incluir en Fase 1:

- No emitir automaticamente en produccion.
- No tocar stock ni calculo de totales de checkout.
- No cambiar balances ni transactions.
- No cambiar recibos/PDF internos.
- No cambiar facturas ya emitidas.

Incluir en Fase 1:

- Configuracion fiscal dinamica desde Admin.
- Snapshot fiscal en `arca_invoices`.
- Soporte para `service_fee_tax_mode`.
- Ajuste de payload ARCA con tipo de comprobante y punto de venta dinamicos.
- Integracion controlada del job fiscal despues de pago aprobado.
- Listado y detalle admin de facturas ARCA.
- Tests unitarios del calculo fiscal y pruebas de idempotencia.

## Migraciones propuestas

### `create_billing_settings_table`

Columnas sugeridas:

- `id`
- `enabled` boolean default false.
- `issuer_cuit` string nullable.
- `issuer_iva_condition` string nullable.
- `point_of_sale` integer nullable.
- `service_fee_percentage` decimal nullable.
- `service_fee_tax_mode` string default `vat_added`.
- `vat_percentage` decimal default 21.
- `default_invoice_type` string nullable.
- `environment` string default `testing`.
- `created_at`
- `updated_at`

Valores permitidos:

- `service_fee_tax_mode`: `no_vat_added`, `vat_added`, `vat_included`.
- `environment`: `testing`, `production`.

### `add_fiscal_snapshot_to_arca_invoices`

Columnas sugeridas:

- `service_fee_percentage_used`
- `service_fee_tax_mode_used`
- `vat_percentage_used`
- `issuer_cuit_used`
- `invoice_type_used`
- `point_of_sale_used`

Motivo: una factura aprobada debe conservar los valores fiscales usados al momento de emision, aunque luego cambie la configuracion admin.

## Archivos probables de Fase 1

Crear:

- `database/migrations/*_create_billing_settings_table.php`
- `database/migrations/*_add_fiscal_snapshot_to_arca_invoices.php`
- `app/Models/BillingSetting.php`
- `app/Http/Controllers/BackEnd/BillingSettingController.php`
- `resources/views/backend/billing-settings/index.blade.php`
- `resources/views/backend/billing-settings/invoices.blade.php`
- `resources/views/backend/billing-settings/show.blade.php`

Modificar:

- `routes/admin.php`
- `resources/views/backend/partials/side-navbar.blade.php`
- `app/Services/Billing/BookingFiscalCalculator.php`
- `app/Services/Billing/CommissionInvoiceBuilder.php`
- `app/Jobs/ArcaInvoiceIssuingJob.php`
- `app/Services/Arca/WsfeClient.php`
- `app/Console/Commands/ArcaPreviewBookingInvoice.php`

Modificar solo con aprobacion explicita:

- `app/Http/Controllers/FrontEnd/PaymentGateway/*Controller.php`
- `app/Http/Controllers/FrontEnd/Event/BookingController.php`
- `app/Http/Controllers/FrontEnd/CheckOutController.php`
- `resources/views/frontend/check-out.blade.php`

## Diseno tecnico recomendado

### Configuracion fiscal

Crear `BillingSetting` como singleton.

Responsabilidades:

- Exponer configuracion fiscal vigente.
- Validar valores permitidos.
- Mantener `enabled=false` por defecto.
- No reemplazar `config/arca.php` por completo en Fase 1; usar settings DB como fuente dinamica y config como fallback tecnico.

### Calculo fiscal

Actualizar `BookingFiscalCalculator` para leer:

- `service_fee_percentage`.
- `service_fee_tax_mode`.
- `vat_percentage`.

Modos:

- `no_vat_added`: total factura = comision.
- `vat_added`: neto = comision; IVA = comision * IVA; total = neto + IVA.
- `vat_included`: total = comision; neto = total / (1 + IVA); IVA = total - neto.

Reglas:

- Si `Booking.commission` existe, usarlo como base primaria.
- Si no existe, usar porcentaje configurado sobre base definida.
- Si faltan datos fiscales, bloquear con status `blocked`.
- No emitir si `enabled=false`.

### Tipo de comprobante

No hardcodear en Fase 1 sin confirmacion contable.

Diseño tentativo:

- Resolver tipo segun condicion fiscal del receptor y reglas validadas.
- Guardar tipo resuelto en snapshot `invoice_type_used`.
- Pasar `cbte_tipo` dinamico al payload ARCA y a `WsfeClient`.

### Integracion con pagos

Opcion A: dispatch manual por gateway.

Pros:

- Cambio mas pequeno.
- Menor abstraccion inicial.
- Facil de revisar por gateway.

Contras:

- Repeticion en muchos gateways.
- Riesgo de olvidar uno.
- Mas dificil de mantener.

Opcion B: `BookingPaymentCompleted` event + listener.

Pros:

- Punto unico de integracion fiscal.
- Mejor para multiples gateways.
- Mas testeable e idempotente.

Contras:

- Requiere introducir evento/listener.
- Mayor cambio arquitectonico.

Recomendacion: usar Event/Listener si se aprueba tocar el flujo comun de confirmacion de pago. Si la prioridad es minimo riesgo, comenzar con MercadoPago manual y luego extraer evento.

## Pruebas propuestas

Unitarias:

- `BookingFiscalCalculator` con `no_vat_added`.
- `BookingFiscalCalculator` con `vat_added`.
- `BookingFiscalCalculator` con `vat_included`.
- Booking con comision persistida vs porcentaje de settings.
- Booking sin datos fiscales queda bloqueado.

Feature/integracion:

- Pago aprobado dispara job fiscal cuando `enabled=true`.
- Pago aprobado no emite cuando `enabled=false`.
- Webhook duplicado no duplica `arca_invoices`.
- Job reintentado no duplica factura aprobada.
- Snapshot fiscal queda persistido.

Manual:

- Admin settings guarda y muestra configuracion vigente.
- Preview CLI usa settings dinamicos.
- Factura ready no llama a ARCA si emision esta apagada.
- Factura approved conserva CAE y snapshot.
- Listado admin muestra `ready`, `blocked`, `error`, `approved`.

## Riesgos y mitigaciones

- Riesgo: facturar base incorrecta.
  - Mitigacion: no implementar hasta validar comision vs total cobrado.

- Riesgo: tipo de comprobante incorrecto.
  - Mitigacion: decision contable obligatoria antes de Fase 1.

- Riesgo: datos fiscales incompletos.
  - Mitigacion: bloquear emision y mostrar estado `blocked`.

- Riesgo: duplicacion por webhooks.
  - Mitigacion: unique `booking_id`, `lockForUpdate`, no modificar approved.

- Riesgo: multiples gateways con caminos distintos.
  - Mitigacion: preferir Event/Listener o checklist por gateway.

- Riesgo: activar produccion accidentalmente.
  - Mitigacion: `enabled=false` por defecto, environment testing, no emitir si falta confirmacion.

## Stop conditions de Fase 1

- No implementar si no hay confirmacion de codigos ARCA.
- No implementar si no hay decision de `service_fee_tax_mode`.
- No implementar si no hay decision de datos fiscales en checkout.
- No activar emision automatica en produccion sin flag admin y `.env` compatible.
- No emitir si faltan datos fiscales minimos.
- No duplicar facturas.
- No cambiar facturas aprobadas.
- No tocar checkout/gateways sin prompt especifico y confirmacion humana.

## Prompt recomendado para Fase 1

Usar cuando esten respondidas las decisiones contables:

```txt
Implementa Fase 1 de ARCA en TukiPass con cambios minimos y verificables.

Decisiones confirmadas:
- Configuracion fiscal en tabla separada `billing_settings`.
- Tipo de comprobante validado: [COMPLETAR].
- `service_fee_tax_mode`: [no_vat_added|vat_added|vat_included].
- Datos fiscales requeridos antes de emitir: [COMPLETAR].
- Estrategia de integracion: [dispatch manual|Event/Listener].

Reglas:
- No tocar checkout/gateways salvo los archivos explicitamente listados.
- No activar emision real en produccion.
- Mantener `enabled=false` por defecto.
- Usar tests primero para `BookingFiscalCalculator`.
- Preservar idempotencia por `booking_id`.
- Guardar snapshot fiscal en `arca_invoices`.
- No modificar facturas `approved`.

Implementar:
1. Migracion y modelo `BillingSetting`.
2. Migracion de snapshot en `arca_invoices`.
3. Admin UI de settings.
4. Ajuste de `BookingFiscalCalculator` para `service_fee_tax_mode`.
5. Ajuste de `CommissionInvoiceBuilder` y `ArcaInvoiceIssuingJob` para snapshot.
6. Ajuste de `WsfeClient` para `cbte_tipo` dinamico.
7. Integracion de pago segun estrategia confirmada.
8. Tests unitarios y verificacion manual.
```
