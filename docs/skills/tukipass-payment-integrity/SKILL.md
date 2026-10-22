---
name: tukipass-payment-integrity
description: Protege el ciclo completo de pago de TukiPass (MercadoPago, reservas, webhooks, emisión de entradas y compensaciones). Úsala antes de tocar CheckOutController, BookingController@storeData, PendingBooking, MercadoPagoController, TransactionService, Billing/ARCA o cualquier ruta de webhook. Exige invariantes, idempotencia y tests de fallo antes de cambiar código.
---

# TukiPass — Integridad de pagos

## Cuándo usarla

- `app/Http/Controllers/FrontEnd/CheckOutController.php`.
- `app/Http/Controllers/FrontEnd/Event/BookingController.php` (`storeData`, `reserveVariationStock`, `reserveSingleTicketStock`).
- `app/Http/Controllers/FrontEnd/PaymentGateway/*Controller.php`.
- `app/Models/PendingBooking.php`, `app/Models/Event/Booking.php`, `app/Services/TransactionService.php`, `app/Services/PaymentCompensationService.php`.
- `app/Console/Commands/ReconcileOnlinePendingBookings.php`, `app/Console/Commands/ReviewPaymentIncidents.php`.
- Rutas de webhook/callback/retorno, `config/services.php`, variables `MERCADOPAGO_*`.

## Estado verificado (2026-10-22)

La fuente de verdad es el código y los tests, no este archivo. Verificar con `git rev-parse HEAD` y `php artisan test`.

| Control | HEAD `2320b03e` | Producción `bbc843b1` |
|---|---|---|
| Firma sin secret | **fail-closed** en production (`MercadoPagoController.php:866-873`) | fail-open (`:744-746`) |
| Error transitorio de API | **503** (`:654`) | 200 |
| Excepción inesperada | **500** (`:847`) | 200 |
| Stock + reserva | transacción única (`BookingController.php:201`) | versión previa |
| Pago tardío | `claimPendingBooking` acepta `expired` y `processing` estancado (`:851-864`) | no |
| Compensación | `PaymentCompensationService` + `payment_incidents` | no |
| Tests | 256 pasan; 3 de firma, 3 de claim, 3 de compensación | — |

**Release blocker:** producción **no tiene** `MERCADOPAGO_WEBHOOK_SECRET` (verificado contra el env de EasyPanel y contra el endpoint: firma inválida devolvió 200 en el deploy viejo). Al desplegar HEAD, `hasValidWebhookSignature()` falla cerrado y **todos los webhooks devolverán 401**. Configurar el secret **antes** del deploy.

## Invariantes que no se negocian

1. **Pago aprobado ⇒ una reserva y sus entradas.** No puede existir un pago aprobado sin `Booking` con `paymentStatus` `completed` o `free`, salvo que exista un `PaymentIncident` con reembolso o revisión manual.
2. **Ninguna entrada sin pago.** Una reserva `completed|free` debe tener pago verificado o ser gratuita.
3. **Webhook idempotente.** Notificaciones duplicadas no crean dos reservas, dos transacciones ni dos emails. El claim es un `UPDATE` condicional, no un `if` en PHP.
4. **Transición atómica.** `PendingBooking` pasa de `pending|expired` (o `processing` estancado >30 min) a `processing`, y luego a `completed|failed`. Nunca dos procesos crean la misma reserva.
5. **Pago tardío reconciliado.** Un pago aprobado después del TTL se procesa o se reembolsa; no se descarta en silencio. `ReconcileOnlinePendingBookings` marca `expired` y retiene 7 días.
6. **Fallo posterior al cobro compensado.** Si `storeData()` falla después de la aprobación, se registra un `PaymentIncident` y se intenta el reembolso total con idempotency key.
7. **Firma obligatoria en producción.** Si falta `MERCADOPAGO_WEBHOOK_SECRET`, el webhook se rechaza en producción (fail-closed). En local se permite para desarrollo.
8. **Monto y moneda verificados.** Se comparan contra `PendingBooking.amount` y `data.currencyText`, nunca contra datos del cliente.
9. **Errores transitorios se reintentan.** HEAD responde 503 ante fallos de API/DB transitorios y 500 ante excepción; 200 solo cuando el evento ya está procesado, es inválido de forma definitiva o no aplica. No volver a 200 silencioso en errores.
10. **Toda mutación de dinero es transaccional o atómica.** `Earning`, `Transaction` y `OrganizerBalance` se actualizan una sola vez por reserva; el claim evita duplicados.

## Mapa real

```text
CheckOutController@checkout2
  → valida stock y arma sesión; NO descuenta stock
  → MercadoPagoController@bookingProcess
      → PendingBooking::create(token, data, amount, expires_at)   [:250]
      → preferencia MP (external_reference = token, notification_url = webhook)
  → MercadoPagoController@notify (redirect del navegador)          [:278]
  → MercadoPagoController@webhook (server-to-server)              [:602]
      → hasValidWebhookSignature()                                 [:866]
      → claimPendingBooking(token)                                 [:851]
      → GET /v1/payments/{id}
      → valida status/amount/currency/external_reference
      → BookingController@storeData  [transacción única stock+reserva+addons, :201]
      → emisión de entradas / factura / mail
      → PaymentCompensationService si storeData falla
```

## Procedimiento

1. **Reproducir el escenario con un test antes de tocar código.** Base: `tests/Unit/Payment`, `tests/Feature/EventBookingTicketSelectionValidationTest.php`.
2. **No mockear la base para idempotencia.** El claim atómico se prueba contra DB. Para concurrencia real usar MySQL, no SQLite in-memory.
3. **Para la API de MercadoPago, usar `Http::fake()`.** `PaymentCompensationService` ya usa el facade `Http`.
4. **Verificar transiciones.** Un webhook duplicado debe devolver 200 sin crear una segunda reserva. El claim atómico está cubierto por `PendingBookingClaimTest`; falta un test HTTP end-to-end del webhook duplicado.
5. **Verificar fallo de `storeData`.** Forzar stock agotado y comprobar `PaymentIncident` + reembolso intentado (`PaymentCompensationServiceTest`).
6. **Verificar pago tardío.** `PendingBooking` `expired` → claim → `processing` (`PendingBookingClaimTest`).
7. **Verificar firma.** `tests/Unit/Payment/MercadoPagoWebhookSignatureTest.php`: sin secret en producción → false; con secret y sin headers → false.
8. **Ejecutar `php artisan test --no-coverage`.** No avanzar con tests rojos.

## Tests mínimos

| Escenario | Resultado esperado | Test actual |
|---|---|---|
| Webhook duplicado | Una reserva, una transacción, un mail; 200 | Claim atómico cubierto; falta HTTP end-to-end |
| Webhook fuera de orden | No retrocede el estado; 200 | Pendiente |
| Pago tardío (`expired`) | Reserva creada o reembolso; incidente si falla | `PendingBookingClaimTest` |
| Monto menor al esperado | `failed`, reembolso, `PaymentIncident` `amount_mismatch` | `PaymentCompensationServiceTest` + código |
| Moneda distinta | `failed`, reembolso, `PaymentIncident` `currency_mismatch` | `PaymentCompensationServiceTest` + código |
| `storeData` falla por stock | `PaymentIncident` + reembolso intentado | `PaymentCompensationServiceTest` |
| Firma ausente en producción | 401, sin procesar | `MercadoPagoWebhookSignatureTest` |
| API MP caída | 503 para que MP reintente | Código HEAD (`:654`); falta test HTTP |
| `PendingBooking` no encontrado | `PaymentIncident` `pending_booking_not_found` | Código HEAD |

## Condiciones de parada

- **Detenerse** si el cambio requiere tocar producción, ejecutar un cobro real o usar credenciales productivas.
- **Detenerse** si no hay test que reproduzca el fallo.
- **Detenerse** si se intenta desplegar HEAD sin `MERCADOPAGO_WEBHOOK_SECRET`: el webhook falla cerrado y se pierden notificaciones.
- **Pedir autorización** para cambiar `CheckOutController@checkout2`, `BookingController@storeData`, `config/auth.php` o los guards.
- **No** cambiar el contrato de `external_reference`, `notification_url` o el payload de preferencia sin versión compatible.

## Mantenimiento

- Dueño: backend/pagos.
- Revisión: trimestral y ante cada cambio de gateway o de contrato de webhook.
- Si el código cambia, actualizar este mapa y los tests en el mismo PR. Si el deploy queda atrás, anotar la diferencia en `tukipass-release-guard`.
