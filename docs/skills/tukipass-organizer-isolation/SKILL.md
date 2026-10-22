---
name: tukipass-organizer-isolation
description: Previene acceso cruzado entre organizadores y entre clientes en TukiPass. Úsala al crear o modificar endpoints de organizador, reservas, tickets, exportaciones, retiros, soporte, QR o archivos. Exige scoping por organizer_id/customer_id y tests A/B de IDOR.
---

# TukiPass — Aislamiento de organizadores y clientes

## Cuándo usarla

- Nuevos endpoints en `routes/organizer*.php` o `routes/frontend_customer.php`.
- Cambios en `app/Http/Controllers/BackEnd/Organizer/*`.
- Cambios en `app/Http/Controllers/BackEnd/Event/*` con impacto en organizadores.
- Exportaciones, reportes, retiros, soporte, imágenes, QR.
- Cualquier `find($id)`, `firstOrFail()` o `where('id', $id)` sobre un recurso de tenant.

## Estado verificado (2026-10-22)

| Control | HEAD `2320b03e` | Producción `bbc843b1` |
|---|---|---|
| `SupportTicketController@message` | scoped por `user_id` + `user_type` (`:61-63`) | **sin scope** (`where('id',$id)->firstOrFail()`) |
| `SupportTicketController@reply` | scoped (`:71-73`) | **sin scope** |
| `EventBookingController` | `organizer_id` (`:31`, `:171`) | igual |
| Helpers de ownership | `EventController:50`, `TicketController:38,45` | igual |
| QR | `TicketScanService` valida `organizerId` (`:39-41`) | servicio no existía |
| Test A/B | `tests/Feature/SupportTicketIsolationTest.php` (2 tests) | — |

**Hallazgo de producción:** el deploy `bbc843b1` permite que un cliente autenticado lea y responda tickets de soporte de otro cliente (`SupportTicketController@message`/`@reply`). El fix está en HEAD, no desplegado. Es un IDOR real, no teórico. Priorizar el deploy del fix o un parche aislado.

## Invariantes

1. **Toda consulta de organizador incluye `organizer_id`.** No alcanza con `auth:organizer`; hay que verificar propiedad del recurso.
2. **Ningún `find($id)` sin scope.** Usar `getOwnedEventOrFail`, `getOwnedTicketOrFail` o un helper equivalente.
3. **Bulk operations por ID.** Cada ID se valida individualmente; no confiar en el primer ID.
4. **Exportaciones scoped.** El query que alimenta el Excel/CSV debe incluir el `organizer_id`.
5. **QR scoped.** `TicketScanService` recibe `organizerId` y rechaza reservas ajenas.
6. **Soporte scoped.** Un organizador solo ve sus tickets; un cliente solo los suyos (`user_id` + `user_type`).
7. **Retiros scoped.** Un organizador no puede operar retiros de otro.
8. **Archivos scoped.** No servir archivos por nombre sin verificar ownership; el fix de nombres no adivinables está en HEAD (`765d7380`).
9. **Admin bypass explícito.** Solo `auth:admin` puede saltar el scope; debe ser una decisión visible, no un accidente.
10. **Test A/B obligatorio.** Para cada endpoint, un test con el dueño y otro con un intruso.

## Mapa real

```text
Organizer\EventController
  → getOwnedEventOrFail($id)         [events.organizer_id, :50]
Organizer\TicketController
  → getOwnedEventOrFail / getOwnedTicketOrFail   [:38, :45]
Organizer\EventBookingController
  → Booking::where('organizer_id', auth('organizer')->id())   [:31]
  → show/updatePaymentStatus con verificación explícita       [:171]
Organizer\OrganizerController@check_qrcode
  → TicketScanService::scan(..., organizerId)
AdminController@check_qrcode
  → TicketScanService::scan(..., organizerId = null)  [admin bypass]
FrontEnd\SupportTicketController@message/@reply
  → where('user_id', auth('customer')->id())->where('user_type','customer')  [:61-73]
```

## Procedimiento

1. **Identificar el recurso raíz.** ¿Event, Ticket, Booking, Withdraw, SupportTicket?
2. **Buscar el scope existente.** Si no existe, crearlo en un helper privado del controller o en un query scope del modelo.
3. **Escribir el test A/B antes del fix.** Crear dos organizadores/clientes y dos recursos. Base: `tests/Feature/SupportTicketIsolationTest.php`.
4. **Probar lectura, escritura y borrado.** No alcanza con probar el listado.
5. **Probar bulk.** Enviar IDs del intruso mezclados con los propios.
6. **Probar export.** Verificar que el CSV no incluye filas ajenas.
7. **Ejecutar `php artisan test --no-coverage`.** Filtro: `php artisan test --filter='SupportTicketIsolationTest'`.

## Tests mínimos

| Recurso | Dueño | Intruso |
|---|---|---|
| Evento | 200 | 404/403 |
| Ticket | 200 | 404/403 |
| Booking | 200 | 404/403 |
| Export | Solo filas propias | Sin filas ajenas |
| QR | `verified` | `no_permission` |
| Soporte | 200 | 404 |
| Retiro | 200 | 404/403 |
| Imagen | 200 | 404/403 |
| Bulk | Solo IDs propios | 404/403 en el ajeno |

## Condiciones de parada

- **Detenerse** si un endpoint nuevo no tiene test de scoping.
- **Detenerse** si se usa `find($id)` sin verificar `organizer_id`/`customer_id`.
- **Pedir autorización** para cambiar el esquema de ownership o agregar un rol con bypass.
- **No** confundir `auth:organizer` con autorización por recurso.
- **No** desplegar HEAD sin el fix de `SupportTicketController` y su test.

## Mantenimiento

- Dueño: backend/seguridad.
- Revisión: ante cada endpoint nuevo de organizador o cliente.
- Fuente de verdad: código + tests. Si este archivo difiere, gana el código y se actualiza en el mismo PR.
