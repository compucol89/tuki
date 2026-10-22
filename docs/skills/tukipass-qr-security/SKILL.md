---
name: tukipass-qr-security
description: Asegura que las entradas QR de TukiPass sean únicas, válidas, auditables y no reutilizables. Úsala antes de tocar la generación de unique_id, AdminController@check_qrcode, OrganizerController@check_qrcode, Booking::scannedTicketIds, TicketScanService, las vistas PWA o el flujo de reembolso/cancelación.
---

# TukiPass — Seguridad de QR y acceso

## Cuándo usarla

- `app/Http/Controllers/BackEnd/AdminController.php` (`check_qrcode`).
- `app/Http/Controllers/BackEnd/Organizer/OrganizerController.php` (`check_qrcode`).
- `app/Services/TicketScanService.php`.
- `app/Models/Event/TicketScan.php`.
- `app/Models/Event/Booking.php` (`hasIssuedTicketUniqueId`, `scannedTicketIds`, `issuedTicketUniqueIds`).
- Generación de `unique_id` en `BookingController@storeData` y `reserveVariationStock`.
- `resources/views/backend/pwa/`, `resources/views/organizer/pwa/`.
- `app/Mail/EventConfirmationMail.php`.

## Estado verificado (2026-10-22)

| Control | HEAD `2320b03e` | Producción `bbc843b1` |
|---|---|---|
| Tabla `ticket_scans` | migración `2026_10_22_000002` con `unique(booking_id, unique_id)` | **no existe** |
| Servicio central | `TicketScanService` (`scan:26`, lock `:36`, auditoría `:64`) | lógica duplicada |
| Rechazo de doble escaneo | unique + `QueryException` → `already_scanned` | solo JSON `in_array` (race) |
| Legado JSON | `scannedTicketIds()` antes de crear fila (`45f59e5f`) | — |
| Tests | `TicketScanServiceTest` (5), `EventBookingTicketSelectionValidationTest` (2 de escaneo) | — |

**Gap de deploy:** `ticket_scans` es nueva. Al desplegar HEAD, la migración debe correr antes de que el servicio reciba tráfico. El código viejo no tiene la garantía de unicidad en base; dos escaneos concurrentes pueden pasar el `in_array`.

## Invariantes

1. **Un `unique_id` por entrada.** UUID v4 para variaciones; IDs numéricos solo en el path legacy.
2. **Un escaneo exitoso por entrada.** La unicidad se garantiza con `unique(booking_id, unique_id)` en `ticket_scans`, no solo con un `in_array` en PHP.
3. **Atomicidad.** El escaneo ocurre dentro de `DB::transaction` con `lockForUpdate()` sobre la reserva.
4. **Autorización.** Admin puede escanear cualquier reserva; organizador solo las de su `organizer_id` (`TicketScanService:39-41`).
5. **Estado de pago.** Solo `completed` o `free` son escaneables. `pending` → `payment_incomplete`; `rejected|refunded|cancelled` → `payment_rejected`.
6. **Pertenencia.** El `unique_id` debe estar en `Booking::issuedTicketUniqueIds()`.
7. **Auditoría.** Cada escaneo exitoso registra `booking_id`, `event_id`, `organizer_id`, `scanner_type`, `scanner_id`, `ip`, `user_agent`, `scanned_at`.
8. **Revocación.** Una entrada reembolsada o cancelada no se puede validar; `BookingStockService` no devuelve stock de una entrada ya escaneada.
9. **Compatibilidad.** `scanned_tickets` se sigue actualizando para reportes y pantallas existentes.
10. **Sin PII en el QR.** El payload es `booking_id__unique_id`; no incluye email, DNI ni datos personales.

## Mapa real

```text
BookingController@storeData
  → unique_id = Str::uuid() por unidad
  → Booking.variation = [{ticket_id, name, qty, price, scan_status, unique_id}]

EventConfirmationMail
  → QrCode::generate(booking_id__unique_id)

AdminController@check_qrcode / OrganizerController@check_qrcode
  → TicketScanService::scan()
      → DB::transaction + lockForUpdate                    [:36]
      → valida organizer_id                                [:39-41]
      → valida paymentStatus                               [:43-50]
      → valida issuedTicketUniqueIds                       [:52-54]
      → valida scanned_tickets legacy                      [:57-59]
      → TicketScan::create()  [unique]                     [:64]
      → actualiza scanned_tickets JSON
```

## Procedimiento

1. **Escribir el test de doble escaneo antes del cambio.** `tests/Feature/TicketScanServiceTest.php` y el test de controller en `EventBookingTicketSelectionValidationTest`.
2. **Probar cross-organizer.** Un organizador no puede escanear una reserva ajena.
3. **Probar reembolsado/cancelado.** El escaneo debe devolver `payment_rejected`.
4. **Probar `unique_id` inexistente.** Debe devolver `unverified`.
5. **Probar concurrencia.** En MySQL, dos procesos con el mismo `unique_id`: uno `verified`, uno `already_scanned`. Hoy los tests son single-thread; falta este test.
6. **Verificar la migración.** `ticket_scans` debe tener el unique y los índices antes del deploy.
7. **Ejecutar `php artisan test --no-coverage`.** Filtro: `php artisan test --filter='TicketScanServiceTest'`.

## Tests mínimos

| Escenario | Resultado |
|---|---|
| Primer escaneo | `verified`, una fila en `ticket_scans` |
| Segundo escaneo | `already_scanned`, sin nueva fila |
| Dos escaneos concurrentes | Un solo `verified` |
| Otro organizador | `no_permission` |
| Reserva pending | `payment_incomplete` |
| Reserva refunded/cancelled | `payment_rejected` |
| `unique_id` inexistente | `unverified` |
| Payload sin `__` | `unverified` |
| Reserva sin variación (legacy) | Acepta IDs 1..quantity |
| Entrada ya escaneada + reembolso | No se libera stock |

## Condiciones de parada

- **Detenerse** si se propone cambiar el payload del QR sin compatibilidad con entradas ya emitidas.
- **Detenerse** si se elimina el unique de `ticket_scans` o el lock de la reserva.
- **Detenerse** si se despliega HEAD sin correr la migración `2026_10_22_000002`.
- **Pedir autorización** para agregar modo offline; requiere diseño de sincronización y resolución de conflictos.
- **No** confiar en que la posesión del QR demuestra la validez de la entrada.

## Mantenimiento

- Dueño: backend/acceso.
- Revisión: ante cada cambio de QR, PWA, reembolso o cancelación.
- Fuente de verdad: código + tests. Si este archivo difiere, gana el código y se actualiza en el mismo PR.
