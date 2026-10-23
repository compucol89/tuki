---
name: tukipass-release-guard
description: Procedimiento de publicación segura de TukiPass. Úsala antes de desplegar a staging o producción, al cerrar una rama, al cambiar migraciones, Docker, CI o variables de entorno. Define qué pruebas bloquean un release, cómo verificar el backup y cómo revertir.
---

# TukiPass — Release guard

## Cuándo usarla

- Antes de un deploy a staging o producción.
- Al cerrar una rama con cambios de código, migraciones o configuración.
- Al modificar `Dockerfile`, `docker-start.sh`, `docker-compose.yml`, `deploy.sh` o `.github/workflows/`.
- Al agregar dependencias, migraciones o variables de entorno.
- Antes de pedir una revisión final.

## Estado real del deploy (2026-10-22)

| Dato | Valor |
|---|---|
| Código HEAD | `2320b03e` |
| Rama | `master` (código `2320b03e`; producción `bbc843b1`) |
| Producción EasyPanel | `bbc843b1` |
| Diferencia | HEAD incluye stock transaccional, firma fail-closed, compensación, QR con `ticket_scans`, aislamiento de soporte, OPcache, workers y cache de blogs; **nada de eso está desplegado** |
| `MERCADOPAGO_WEBHOOK_SECRET` | **ausente en producción** |
| `APP_ENV` / `APP_DEBUG` | `production` / `false` |
| Tests HEAD | 256 pasan (186.529 assertions) |

**Regla:** `git rev-parse HEAD` no es el deploy. Comparar contra el commit que muestra EasyPanel (`inspectAppService` o la UI) antes de afirmar que un fix está en producción.

**Riesgo de producción:** `bbc843b1` tiene un IDOR real en `SupportTicketController@message`/`@reply` (un cliente puede leer/responder tickets de otro) y QR sin la garantía `ticket_scans`. HEAD corrige ambos. No postergar el deploy por tareas de performance.

**Bloqueante crítico:** HEAD hace `hasValidWebhookSignature()` fail-closed en production. Si se despliega HEAD sin `MERCADOPAGO_WEBHOOK_SECRET`, **todos los webhooks de MercadoPago devolverán 401** y no se crearán reservas. Configurar el secret en EasyPanel **antes** del deploy y reiniciar el servicio.

## Checklist obligatorio

### 0. Deploy vs HEAD

- [ ] `git rev-parse HEAD` comparado con el commit de EasyPanel.
- [ ] Si difieren, tratar el deploy como **big-bang**: incluye migraciones y variables nuevas.
- [ ] Confirmar que el rollback vuelve a la imagen/commit anterior, no solo a `master`.

### 1. Rama y diff

- [ ] Rama separada, sin cambios accidentales.
- [ ] `git status` revisado.
- [ ] `git diff` revisado por un humano.
- [ ] Sin secretos, `.env`, dumps de producción ni datos personales en el diff.
- [ ] Sin `force push` ni reescritura de historial.

### 2. Backend

- [ ] `php -l` sobre los archivos tocados.
- [ ] `php artisan test --no-coverage` verde (256 tests al 2026-10-22).
- [ ] Tests específicos verdes:
      `MercadoPagoWebhookSignatureTest`, `PendingBookingClaimTest`, `PaymentCompensationServiceTest`, `BookingStockReleaseTest`, `EventBookingTicketSelectionValidationTest`, `SupportTicketIsolationTest`, `TicketScanServiceTest`, `ReconcileOnlinePendingBookingsTest`.
- [ ] Sin tests eliminados para “hacer pasar” la suite.
- [ ] Migraciones `2026_10_22_000001_add_stock_released_at_to_bookings_table`, `2026_10_22_000002_create_ticket_scans_table`, `2026_10_22_000003_create_payment_incidents_table` con `down()` reversible.
- [ ] `php artisan migrate --pretend` revisado en staging.
- [ ] `php artisan tukipass:health-check` verde.

### 3. Frontend

- [ ] `npm run production` sin errores (Node 22; ver `.nvmrc`).
- [ ] `npm run test:full` (e2e, a11y, aria, seo, legal) verde en staging.
- [ ] `npm run test:visual` sin regresiones no explicadas.
- [ ] El seed de Playwright (`tukipass.sql`) existe en el entorno; si falta, el job se saltea y no cuenta como verde.

### 4. Seguridad

- [ ] `composer audit --locked` sin advisories high/critical.
- [ ] `npm audit --omit=dev --audit-level=high` sin hallazgos.
- [ ] `MERCADOPAGO_WEBHOOK_SECRET` configurado **antes** de desplegar HEAD.
- [ ] `APP_DEBUG=false`, `APP_ENV=production`.
- [ ] `ORGANIZER_LOGIN_THROTTLE_PER_MINUTE` en 5 en producción (no 200; 200 es solo para tests locales).
- [ ] `scripts/audit-repo-exposure.sh` verde.
- [ ] `scripts/audit-skills.sh` verde (o `STRICT=1` en la máquina del operador).
- [ ] Sin secretos en logs ni en respuestas.

### 5. Infraestructura y performance

- [ ] Backup de DB verificado y restaurable.
- [ ] Backup de archivos críticos (volumen `img`, logs).
- [ ] Rollback documentado: imagen anterior, `git revert`, restore de DB.
- [ ] Si el release incluye OPcache/workers/cache de blogs: medir antes/después con `tukipass-performance` (20 requests a concurrencia 5, TTFB, RSS por worker).
- [ ] Smoke test: home, eventos, detalle, login, checkout sandbox, panel organizador, QR.

### 6. Post-deploy

- [ ] 5xx y latencia monitoreados 30 minutos.
- [ ] Webhook de MercadoPago recibido y procesado (con secret configurado).
- [ ] QR escaneado en staging con una entrada de prueba.
- [ ] Sitemap sin URLs demo.
- [ ] `storage/logs/laravel.log` sin errores nuevos.
- [ ] `payment_incidents` sin incidentes nuevos sin revisar (`php artisan payments:review-incidents` si aplica).

## Bloqueantes de release

Un release **no se publica** si:

- Falla PHPUnit o Playwright.
- Hay un advisory critical/high sin mitigación documentada.
- La migración no tiene `down()` o requiere downtime no planificado.
- No hay backup verificado.
- No hay rollback probado.
- El health check falla.
- Se tocó checkout/pagos/stock/QR sin tests específicos.
- Producción tiene `APP_DEBUG=true` o `MERCADOPAGO_WEBHOOK_SECRET` vacío **y el código a desplegar es HEAD/fail-closed**.
- El commit desplegado no coincide con el HEAD revisado y no se documentó la diferencia.

## Procedimiento de rollback

1. Revertir la imagen al tag/commit anterior en EasyPanel.
2. Si hay migración nueva, ejecutar `php artisan migrate:rollback --step=3` solo si los `down()` son seguros (stock_released_at, ticket_scans, payment_incidents).
3. Si el rollback de DB no es posible, restaurar el backup verificado.
4. Revisar logs y health check.
5. Documentar el incidente y la causa.

## Condiciones de parada

- **Detenerse** si no hay autorización humana explícita para producción.
- **Detenerse** si el backup no se probó restaurando en un entorno aislado.
- **Detenerse** si el cambio toca `CheckOutController@checkout2`, `BookingController`, `config/auth.php` o los guards sin revisión dedicada.
- **Detenerse** si se intenta desplegar HEAD sin `MERCADOPAGO_WEBHOOK_SECRET`.
- **No** ejecutar `migrate --force` en producción sin backup.

## Referencias reales

- `.github/workflows/tests.yml`, `.github/workflows/organizer-theme.yml`
- `Dockerfile`, `docker-start.sh`, `docker-compose.yml`, `deploy.sh`
- `phpunit.xml`, `playwright.config.js`
- `app/Console/Commands/SystemHealthCheck.php` (`tukipass:health-check`)
- `docs/audits/auditoria-integral-2026-10-22.md` (cambios de código)
- `docs/audits/performance-audit-2026-10-22.md` (mediciones; leer la cabecera de correcciones)

## Mantenimiento

- Dueño: DevOps/backend lead.
- Revisión: cada release y cada cambio de infraestructura.
- Fuente de verdad: código + tests + commit desplegado. Si este archivo difiere, gana el sistema y se actualiza en el mismo PR.
