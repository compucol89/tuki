---
name: tukipass-payment-integrity
description: Protege el ciclo completo de pago de TukiPass (MercadoPago, reservas, webhooks, emisión de entradas y compensaciones). Úsala antes de tocar CheckOutController, BookingController@storeData, PendingBooking, MercadoPagoController, TransactionService, Billing/ARCA o cualquier ruta de webhook. Exige invariantes, idempotencia y tests de fallo antes de cambiar código.
---

# TukiPass — Integridad de pagos

## Cuándo usarla

- Cambios en `app/Http/Controllers/FrontEnd/CheckOutController.php`.
- Cambios en `app/Http/Controllers/FrontEnd/Event/BookingController.php` (`storeData`, `index`).
- Cambios en `app/Http/Controllers/FrontEnd/PaymentGateway/*Controller.php`.
- Cambios en `app/Models/PendingBooking.php`, `app/Models/Event/Booking.php`, `app/Services/TransactionService.php`.
- Cambios en `app/Console/Commands/ReconcileOnlinePendingBookings.php`.
- Nuevas rutas de webhook, callback o retorno de pago.
- Cambios en `config/services.php` o variables `MERCADOPAGO_*`.

## Invariantes que no se negocian

1. **Pago aprobado ⇒ una reserva y sus entradas.** No puede existir un pago aprobado sin `Booking` con `paymentStatus` `completed` o `free`.
2. **Ninguna entrada sin pago.** Una reserva `completed|free` debe tener pago verificado o ser gratuita.
3. **Webhook idempotente.** Notificaciones duplicadas no crean dos reservas, dos transacciones ni dos emails.
4. **Transición atómica.** `PendingBooking` pasa de `pending|expired` a `processing` y luego a `completed|failed` con un `UPDATE` condicional. Nunca dos procesos crean la misma reserva.
5. **Pago tardío reconciliado.** Un pago aprobado después del TTL se procesa o se reembolsa; nunca se descarta en silencio.
6. **Fallo posterior al cobro compensado.** Si `storeData()` falla después de la aprobación, se registra un `PaymentIncident` y se intenta el reembolso total.
7. **Firma obligatoria en producción.** Si falta `MERCADOPAGO_WEBHOOK_SECRET`, el webhook se rechaza en producción (fail-closed).
8. **Monto y moneda verificados.** Se comparan contra `PendingBooking.amount` y `data.currencyText`, nunca contra datos del cliente.
9. **Errores transitorios se reintentan.** El webhook responde 5xx ante fallos de API/DB transitorios; 200 solo cuando el evento ya está procesado o es inválido de forma definitiva.
10. **Toda mutación de dinero es transaccional.** `Earning`, `Transaction` y `OrganizerBalance` se actualizan una sola vez por reserva.

## Mapa real del flujo

```text
CheckOutController@checkout2
  → MercadoPagoController@bookingProcess
      → PendingBooking::create(token, data, amount, expires_at)
      → preferencia MP (external_reference = token)
  → MercadoPagoController@notify (redirect del navegador)
  → MercadoPagoController@webhook (server-to-server)
      → claimPendingBooking(token)  [pending|expired|processing estancado]
      → GET /v1/payments/{id}
      → valida status/amount/currency/external_reference
      → BookingController@storeData  [transacción única de stock + reserva]
      → emisión de entradas / factura / mail
      → PaymentCompensationService si storeData falla
```

## Procedimiento

1. **Reproducir el escenario con un test antes de tocar código.** Usar `tests/Unit/Payment`, `tests/Feature/EventBookingTicketSelectionValidationTest` o un test nuevo.
2. **No mockear la base para la lógica de idempotencia.** Usar SQLite in-memory o MySQL de test; el claim atómico se prueba contra la DB.
3. **Para la API de MercadoPago, usar `Http::fake()`.** El servicio `PaymentCompensationService` ya usa el facade `Http`.
4. **Verificar el orden de las transiciones.** Un webhook duplicado debe devolver 200 sin crear una segunda reserva.
5. **Verificar el fallo de `storeData`.** Forzar un fallo (por ejemplo, stock agotado) y comprobar que se crea `PaymentIncident` y se intenta el reembolso.
6. **Verificar el pago tardío.** Crear un `PendingBooking` `expired`, invocar el claim y comprobar que pasa a `processing`.
7. **Verificar la firma.** `tests/Unit/Payment/MercadoPagoWebhookSignatureTest`.
8. **Ejecutar `php artisan test --no-coverage`.** No avanzar con tests rojos.

## Tests mínimos

| Escenario | Resultado esperado |
|---|---|
| Webhook duplicado | Una reserva, una transacción, un mail; 200 |
| Webhook fuera de orden | No retrocede el estado; 200 |
| Pago tardío (`expired`) | Reserva creada o reembolso; incidente si falla |
| Monto menor al esperado | `failed`, reembolso, `PaymentIncident` `amount_mismatch` |
| Moneda distinta | `failed`, reembolso, `PaymentIncident` `currency_mismatch` |
| `storeData` falla por stock | `PaymentIncident` + reembolso intentado |
| Firma ausente en producción | 401, sin procesar |
| API MP caída | 503 para que MP reintente |
| `PendingBooking` no encontrado | `PaymentIncident` `pending_booking_not_found` |

## Condiciones de parada

- **Detenerse** si el cambio requiere tocar producción, ejecutar un cobro real o usar credenciales productivas.
- **Detenerse** si no hay test que reproduzca el fallo.
- **Pedir autorización** para cambiar `CheckOutController@checkout2`, `BookingController@storeData`, `config/auth.php` o los guards.
- **No** cambiar el contrato de `external_reference`, `notification_url` o el payload de preferencia sin versión compatible.

## Ejemplos

**Correcto:** el webhook duplicado devuelve 200 y no crea una segunda reserva; el log muestra `ya siendo procesado`.

**Incorrecto:** `catch (\RuntimeException)` marca `failed` y redirige a cancelación sin reembolsar ni registrar incidente.

## Mantenimiento

- Dueño: backend/pagos.
- Revisión: trimestral y ante cada cambio de gateway o de contrato de webhook.
- Fuente de verdad: este archivo + tests. Si el código cambia, actualizar el mapa y los tests en el mismo PR.
