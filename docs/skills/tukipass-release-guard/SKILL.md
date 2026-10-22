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

## Checklist obligatorio

### 1. Rama y diff

- [ ] Rama separada, sin cambios accidentales.
- [ ] `git status` revisado.
- [ ] `git diff` revisado por un humano.
- [ ] Sin secretos, `.env`, dumps de producción ni datos personales en el diff.
- [ ] Sin `force push` ni reescritura de historial.

### 2. Backend

- [ ] `php -l` sobre los archivos tocados.
- [ ] `php artisan test --no-coverage` verde.
- [ ] Tests nuevos para el comportamiento nuevo.
- [ ] Sin tests eliminados para “hacer pasar” la suite.
- [ ] Migraciones con `down()` reversible.
- [ ] `php artisan migrate --pretend` revisado en staging.

### 3. Frontend

- [ ] `npm run production` sin errores.
- [ ] Playwright `@e2e`, `@a11y`, `@aria`, `@seo`, `@legal` verde en staging.
- [ ] Sin regresiones visuales no explicadas.

### 4. Seguridad

- [ ] `composer audit --locked` sin advisories high/critical.
- [ ] `npm audit --omit=dev --audit-level=high` sin hallazgos.
- [ ] `MERCADOPAGO_WEBHOOK_SECRET` configurado en producción.
- [ ] `APP_DEBUG=false`, `APP_ENV=production`.
- [ ] `ORGANIZER_LOGIN_THROTTLE_PER_MINUTE` en 5 (no 200).
- [ ] Sin secretos en logs ni en respuestas.

### 5. Infraestructura

- [ ] Backup de DB verificado y restaurable.
- [ ] Backup de archivos críticos.
- [ ] Rollback documentado: imagen anterior, `git revert`, restore de DB.
- [ ] Health check `php artisan tukipass:health-check` verde.
- [ ] Smoke test: home, eventos, detalle, login, checkout sandbox, panel organizador, QR.

### 6. Post-deploy

- [ ] 5xx y latencia monitoreados 30 minutos.
- [ ] Webhook de MercadoPago recibido y procesado.
- [ ] QR escaneado en staging con una entrada de prueba.
- [ ] Sitemap sin URLs demo.
- [ ] `storage/logs/laravel.log` sin errores nuevos.

## Bloqueantes de release

Un release **no se publica** si:

- Falla PHPUnit o Playwright.
- Hay un advisory critical/high sin mitigación documentada.
- La migración no tiene `down()` o requiere downtime no planificado.
- No hay backup verificado.
- No hay rollback probado.
- El health check falla.
- Se tocó checkout/pagos/stock/QR sin tests específicos.
- Producción tiene `APP_DEBUG=true` o `MERCADOPAGO_WEBHOOK_SECRET` vacío.

## Procedimiento de rollback

1. Revertir la imagen al tag anterior en EasyPanel.
2. Si hay migración nueva, ejecutar `php artisan migrate:rollback --step=1` solo si el `down()` es seguro.
3. Si el rollback de DB no es posible, restaurar el backup verificado.
4. Revisar logs y health check.
5. Documentar el incidente y la causa.

## Condiciones de parada

- **Detenerse** si no hay autorización humana explícita para producción.
- **Detenerse** si el backup no se probó restaurando en un entorno aislado.
- **Detenerse** si el cambio toca `CheckOutController@checkout2`, `BookingController`, `config/auth.php` o los guards sin revisión dedicada.
- **No** ejecutar `migrate --force` en producción sin backup.

## Referencias reales

- `.github/workflows/tests.yml`
- `.github/workflows/organizer-theme.yml`
- `Dockerfile`, `docker-start.sh`, `docker-compose.yml`
- `phpunit.xml`, `playwright.config.js`
- `app/Console/Commands/SystemHealthCheck.php`
- `docs/audits/performance-audit-2026-10-22.md`

## Mantenimiento

- Dueño: DevOps/backend lead.
- Revisión: cada release y cada cambio de infraestructura.
