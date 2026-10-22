---
name: tukipass-ticket-stock
description: Garantiza la consistencia de cupos de TukiPass (disponible, reservado, vendido) en reservas, pagos, cancelaciones y reembolsos. Úsala antes de cambiar BookingController@storeData, TicketStockCheck, stockCheck, Ticket, EventAddonCartService, ReconcileOnlinePendingBookings o cualquier consulta que descuente stock. Prohíbe optimizaciones que muevan el descuento fuera de la transacción.
---

# TukiPass — Integridad de stock

## Cuándo usarla

- `app/Http/Controllers/FrontEnd/Event/BookingController.php` (`storeData`, `reserveVariationStock`, `reserveSingleTicketStock`).
- `app/Http/Helpers/Helper.php` (`stockCheck`, `TicketStockCheck`).
- `app/Models/Event/Ticket.php` (`ticket_available`, `ticket_available_type`, `variations`).
- `app/Services/EventAddonCartService.php`.
- `app/Services/BookingStockService.php`.
- `app/Console/Commands/ReconcileOnlinePendingBookings.php`.
- Cambios de checkout que alteren cantidades, variaciones o add-ons.

## Invariantes

1. **Conservación.** `disponible + reservado + vendido = capacidad` para tickets limitados.
2. **Nunca negativo.** `ticket_available` no puede quedar por debajo de 0.
3. **Una transacción por orden.** El descuento de todos los tickets y add-ons ocurre en la misma transacción que crea la reserva.
4. **Sin descuentos parciales.** Si falla el segundo ticket, el primero se revierte.
5. **Reserva ligada al pago.** El stock se descuenta cuando se crea la reserva, no en un paso separado que pueda quedar huérfano.
6. **Liberación idempotente.** `BookingStockService::release()` restaura una sola vez; `bookings.stock_released_at` es la marca.
7. **Liberación correcta.** Se libera en expiración, cancelación y reembolso; no se libera dos veces.
8. **Variaciones JSON.** El match usa `Booking::ticketNameMatches`; no comparar strings a mano.
9. **Add-ons.** El stock de `event_addons` se descuenta y se libera con la misma reserva.
10. **El lock no se elimina.** `lockForUpdate()` es parte de la garantía, no un detalle de performance.

## Mapa real

```text
CheckOutController@checkout2
  → stockCheck() / TicketStockCheck()   [solo mira, no reserva]
  → MercadoPagoController@bookingProcess
      → PendingBooking (sin reservar stock)
  → webhook/notify aprobado
      → BookingController@storeData
          → DB::transaction
              → reserveVariationStock() o reserveSingleTicketStock()
              → Booking::create()
              → EventAddonCartService::attachToBooking()
```

## Procedimiento

1. **Escribir el test de fallo antes del fix.** Ver `tests/Feature/EventBookingTicketSelectionValidationTest.php` y `tests/Feature/BookingStockReleaseTest.php`.
2. **Probar multi-ticket con el segundo agotado.** El primero debe quedar intacto.
3. **Probar fallo de add-on después de crear la reserva.** La reserva y el stock deben revertirse.
4. **Probar liberación doble.** `release()` debe devolver `false` la segunda vez.
5. **Probar variaciones.** Dos unidades de `General` deben restaurar 2, no 1.
6. **Medir consultas sin cambiar la semántica.** Si se agrega un índice o eager loading, el test de stock debe seguir verde.
7. **Ejecutar `php artisan test --no-coverage`.**

## Tests mínimos

| Escenario | Resultado |
|---|---|
| Dos compradores por la última unidad | Una reserva; el otro recibe sold-out antes del cobro o reembolso |
| Multi-ticket, segundo sin stock | Sin reserva, primer ticket intacto |
| Fallo de add-on | Sin reserva, ticket y add-on intactos |
| Expiración de reserva | Stock restaurado una vez |
| Cancelación/reembolso | Stock restaurado una vez |
| Doble liberación | No se duplica |
| Variación limitada | Se restaura la variación correcta |
| Ticket ilimitado | No se descuenta ni se libera |

## Condiciones de parada

- **Detenerse** si el cambio propone mover el descuento fuera de `DB::transaction`.
- **Detenerse** si se elimina `lockForUpdate()` o se cambia el orden de locks sin un test de deadlock.
- **Pedir autorización** para cambiar el esquema de `tickets.variations` o el contrato de `selTickets`.
- **No** “optimizar” reemplazando la transacción por incrementos atómicos separados.

## Ejemplos

**Correcto:** el test fuerza un fallo en `EventAddonCartService` y verifica que `ticket_available` volvió a 10 y no quedó `Booking`.

**Incorrecto:** descontar cada ticket en su propia transacción y crear la reserva después; un fallo intermedio deja stock vendido sin reserva.

## Mantenimiento

- Dueño: backend/checkout.
- Revisión: ante cada cambio de checkout, ticket, add-on o reconciliación.
- Si cambia el esquema de variaciones, actualizar el mapa y los tests en el mismo PR.
