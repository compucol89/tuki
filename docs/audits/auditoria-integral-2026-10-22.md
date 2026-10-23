# Auditoría integral TukiPass — 2026-10-22

**Rama:** `master` (fusionada desde `fix/audit-p0-p1-20261022`)

**Base original:** `dcaf8717`; **código HEAD:** `2320b03e`

**Modo:** cambios locales commiteados y pusheados, sin deploy ni producción.

## 1. Resumen

Se corrigieron los riesgos P0/P1 de integridad transaccional, aislamiento de datos, QR, CI y exposición del repositorio. La rama `master` acumula los commits de la auditoría. La suite PHPUnit pasó de 225 a **256 tests** y `composer audit` quedó en **0 advisories**.

## 2. Cambios implementados

### Pagos

- `BookingController@storeData` ahora ejecuta stock + reserva + add-ons en **una sola transacción**. Si falla un add-on o el segundo ticket, se revierte todo.
- `reserveSingleTicketStock` rechaza cantidades mayores al stock; se eliminó el `max(0, ...)` que permitía vender de más.
- `PendingBooking` TTL por defecto: **48 h** (`MERCADOPAGO_PENDING_TTL_HOURS`).
- `ReconcileOnlinePendingBookings` marca `expired` en vez de borrar; retiene 7 días para pagos tardíos.
- `claimPendingBooking` acepta `pending`, `expired` y `processing` estancado (>30 min).
- Webhook MP:
  - firma **fail-closed** en producción si falta `MERCADOPAGO_WEBHOOK_SECRET`;
  - 503 ante error transitorio de API;
  - 500 ante excepción inesperada;
  - valida monto y moneda;
  - registra `PaymentIncident` si no encuentra la reserva pendiente.
- `PaymentCompensationService` reembolsa por API de MP con idempotency key y registra el resultado en `payment_incidents`.
- `BookingStockService` libera stock una sola vez (`bookings.stock_released_at`) y restaura tickets, variaciones y add-ons.
- Organizer/Admin al rechazar una reserva usan `BookingStockService` en vez de duplicar la lógica.

### QR

- Nueva tabla `ticket_scans` con `unique(booking_id, unique_id)`.
- `TicketScanService` centraliza validación, autorización, auditoría y actualización de `scanned_tickets`.
- Admin y organizador usan el mismo servicio. El organizador solo escanea sus reservas.
- Rechaza `pending`, `rejected`, `refunded`, `cancelled` y `unique_id` inexistente.

### Aislamiento

- `SupportTicketController@message` y `@reply` verifican `user_id` y `user_type` del cliente.
- `store` usa `$request->only([...])`; ya no acepta `status` ni `last_message` desde el cliente.
- Se agregó test A/B de IDOR.

### Datos demo y SEO

- `DemoEventExclusion` es la fuente única: IDs históricos + slugs + sufijos `-demo` y `-prueba`.
- `DemoEventMode` delega en `DemoEventExclusion`.
- Home, eventos, sitemap, image sitemap, AI index, organizadores y detalle aplican la misma exclusión.
- `EventPublicWindow` aplica la exclusión completa.
- Los eventos `-demo` y `-prueba` dejan de aparecer en listados y sitemap; el detalle responde 410.

### CI y repositorio

- `organizer-theme.yml` ahora escucha `master` y declara `permissions: contents: read`.
- Nuevo `tests.yml`: PHPUnit, skills lock, exposición de repo, Composer audit high/critical, npm audit runtime, CodeQL.
- Nuevo `dependabot.yml` para Composer, npm y GitHub Actions.
- `public/installer/database.sql` y los ZIP de adjuntos dejaron de trackearse; `.gitignore` los bloquea y `scripts/audit-repo-exposure.sh` falla si vuelven.
- `scripts/audit-skills.sh` verifica los 37 hashes del lock y los symlinks.

### Rendimiento

- OPcache habilitado en la imagen (`opcache.enable=1`, `enable_cli=1`, 128 MB, 20k archivos).
- `PHP_CLI_SERVER_WORKERS=4` por defecto en `docker-start.sh`.
- Consulta de últimos blogs cacheada 5 minutos por idioma.

## 3. Evidencia

```text
php artisan test --no-coverage
Tests: 256 passed (186529 assertions)

composer audit --locked
No security vulnerability advisories found.

npm audit --omit=dev
0 vulnerabilities

scripts/audit-skills.sh
Skills OK: 37

scripts/audit-repo-exposure.sh
Repositorio: sin dumps de instalador ni adjuntos ZIP trackeados.
```

## 4. Pendiente de autorización / entorno

- **No se desplegó** ni se ejecutaron migraciones en producción.
- **Historial Git:** `public/installer/database.sql` sigue en commits anteriores. Para eliminarlo hace falta reescribir historial o privatizar el repo; no se hizo.
- **Variables de producción:** verificar `MERCADOPAGO_WEBHOOK_SECRET`, `ORGANIZER_LOGIN_THROTTLE_PER_MINUTE=5`, `APP_DEBUG=false`.
- **Playwright CI:** los jobs siguen condicionados a `tukipass.sql`, que no está versionado. Falta un seed reproducible.
- **Performance restante:** deduplicar consultas del detalle, assets condicionales del organizador, thumbnails WebP, cache de `basic_settings`/`languages`.
- **MCP:** EasyPanel sigue por HTTP; MercadoPago y n8n siguen con permisos altos. Requiere cambios en `~/.config/opencode/opencode.json` y credenciales.
- **Skills duplicadas:** los 10 directorios reales de `.claude/skills` que duplican `.agents` no se borraron para no destruir personalizaciones. El lock ahora refleja `.agents`.

## 5. Rollback

- Código: `git revert` de los commits de la rama.
- Migraciones: `php artisan migrate:rollback --step=3` (stock_released_at, ticket_scans, payment_incidents).
- Dependencias: restaurar `composer.lock` anterior y `composer install`.
- Docker: volver a la imagen anterior en EasyPanel.
