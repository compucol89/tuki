---
name: tukipass-ticket-stock
description: Garantiza la consistencia de cupos de TukiPass (disponible, vendido y liberado) en reservas, pagos, cancelaciones y reembolsos. Úsala antes de cambiar BookingController@storeData, stockCheck, TicketStockCheck, Ticket, EventAddonCartService, BookingStockService, ReconcileOnlinePendingBookings o cualquier consulta que descuente stock. Prohíbe optimizaciones que muevan el descuento fuera de la transacción.
---

# TukiPass — Integridad de stock

## Cuándo usarla

- `app/Http/Controllers/FrontEnd/Event/BookingController.php` (`storeData`, `reserveVariationStock`, `reserveSingleTicketStock`).
- `app/Http/Helpers/Helper.php` (`stockCheck:488`, `TicketStockCheck:508`).
- `app/Models/Event/Ticket.php` (`ticket_available`, `ticket_available_type`, `variations`).
- `app/Services/EventAddonCartService.php`.
- `app/Services/BookingStockService.php`.
- `app/Console/Commands/ReconcileOnlinePendingBookings.php`.
- Cambios de checkout que alteren cantidades, variaciones o add-ons.

## Estado verificado (2026-10-22)

| Control | Código HEAD `2320b03e` | Producción `bbc843b1` |
|---|---|---|
| Transacción única por orden | `BookingController.php:201` | versión previa |
| Descuento con lock y chequeo | `reserveVariationStock:320`, `reserveSingleTicketStock:403` | versión previa |
| Liberación idempotente | `BookingStockService.php:15` + `stock_released_at` | no |
| No libera entradas escaneadas | `BookingStockService::release` + `scannedTicketsCount()` | no |
| Tests | 256 pasan; stock 15 + release 4 | — |

**No asumir que producción tiene esto.** El deploy `bbc843b1` es anterior a `33e5e996` (stock transaccional) y a `2320b03e` (no liberar escaneadas). Antes de tocar stock, verificar el commit desplegado.

## Modelo real (importante)

- **No existe una columna `reserved`.** El sistema persiste `ticket_available` (disponible). La igualdad conceptual es `capacidad = ticket_available + vendido`; “reservado” no es un estado persistido.
- `PendingBooking` **no reserva stock**. El stock se descuenta recién cuando `storeData()` crea la `Booking`, después de que el pago fue aprobado.
- Consecuencia: dos compradores pueden pagar la última unidad. `storeData()` serializa con `lockForUpdate`; el segundo recibe “sin stock” y `PaymentCompensationService` reembolsa o deja `manual_review`. **No hay sold-out previo al cobro** mientras no exista una reserva de stock explícita.
- Si el negocio quiere bloquear el stock durante el redirect de pago, es una **feature nueva** (contador de reservado + expiración), no un “fix” de performance.

## Invariantes

1. **Conservación.** Para tickets limitados: `disponible = capacidad - vendido`; no reducir por debajo de 0.
2. **Nunca negativo.** `ticket_available` no puede quedar por debajo de 0. `reserveSingleTicketStock` rechaza cantidades mayores al stock; se eliminó el `max(0, ...)`.
3. **Una transacción por orden.** El descuento de todos los tickets y add-ons ocurre en la misma transacción que crea la reserva (`DB::transaction` en `storeData`, línea 201).
4. **Sin descuentos parciales.** Si falla el segundo ticket o un add-on, el primero se revierte.
5. **Reserva ligada al pago.** El stock se descuenta cuando se crea la reserva, no en un paso separado que pueda quedar huérfano.
6. **Liberación idempotente.** `BookingStockService::release()` restaura una sola vez; `bookings.stock_released_at` es la marca.
7. **Liberación correcta.** Se libera en expiración, cancelación y reembolso; no se libera dos veces ni se devuelve una entrada ya escaneada.
8. **Variaciones JSON.** El match usa `Booking::ticketNameMatches`; no comparar strings a mano.
9. **Add-ons.** El stock de `event_addons` se descuenta y se libera con la misma reserva (`EventAddonCartService`).
10. **El lock no se elimina.** `lockForUpdate()` es parte de la garantía, no un detalle de performance.

## Mapa real

```text
CheckOutController@checkout2
  → stockCheck() / TicketStockCheck()   [solo mira, no reserva]
  → MercadoPagoController@bookingProcess
      → PendingBooking (sin reservar stock)
  → webhook/notify aprobado
      → BookingController@storeData
          → DB::transaction                       [:201]
              → reserveVariationStock()           [:320]
              → reserveSingleTicketStock()        [:403]
              → Booking::create()
              → EventAddonCartService::attachToBooking()
          → si falla: rollback + PaymentCompensationService
```

## Procedimiento

1. **Escribir el test de fallo antes del fix.** Ver `tests/Feature/EventBookingTicketSelectionValidationTest.php` (15 tests) y `tests/Feature/BookingStockReleaseTest.php` (4 tests).
2. **Probar multi-ticket con el segundo agotado.** El primero debe quedar intacto.
3. **Probar fallo de add-on después de crear la reserva.** La reserva y el stock deben revertirse.
4. **Probar liberación doble.** `release()` debe devolver `false` la segunda vez.
5. **Probar variaciones.** Dos unidades de `General` deben restaurar 2, no 1.
6. **Probar entrada escaneada.** `release()` no debe devolver stock de una entrada ya usada.
7. **Medir consultas sin cambiar la semántica.** Si se agrega un índice o eager loading, el test de stock debe seguir verde.
8. **Ejecutar `php artisan test --no-coverage`.** Para el filtro: `php artisan test --filter='BookingStockReleaseTest|EventBookingTicketSelectionValidationTest'`.

## Tests mínimos

| Escenario | Resultado |
|---|---|
| Dos compradores por la última unidad | Una reserva; el otro recibe sin stock y se reembolsa/manual_review |
| Multi-ticket, segundo sin stock | Sin reserva, primer ticket intacto |
| Fallo de add-on | Sin reserva, ticket y add-on intactos |
| Expiración de reserva | Stock restaurado una vez |
| Cancelación/reembolso | Stock restaurado una vez |
| Doble liberación | No se duplica |
| Variación limitada | Se restaura la variación correcta |
| Ticket ilimitado | No se descuenta ni se libera |
| Entrada escaneada | No se libera |

**Gap actual:** los tests son single-thread. No hay un test de concurrencia real con MySQL; SQLite in-memory no ejercita `lockForUpdate`. Agregar uno antes de cambiar el orden de locks.

## Condiciones de parada

- **Detenerse** si el cambio propone mover el descuento fuera de `DB::transaction`.
- **Detenerse** si se elimina `lockForUpdate()` o se cambia el orden de locks sin un test de deadlock.
- **Pedir autorización** para cambiar el esquema de `tickets.variations` o el contrato de `selTickets`.
- **No** “optimizar” reemplazando la transacción por incrementos atómicos separados.
- **No** desplegar HEAD sin las migraciones `stock_released_at` y `ticket_scans`: el código nuevo las usa.

## Mantenimiento

- Dueño: backend/checkout.
- Revisión: ante cada cambio de checkout, ticket, add-on o reconciliación.
- Fuente de verdad: código + tests. Si este archivo difiere, gana el código y se actualiza en el mismo PR.
