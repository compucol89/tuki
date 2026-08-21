# Incidente Worker — 2026-08-11 (Easypanel)

**Estado:** RESUELTO 2026-08-21 · **Severidad:** alta (colas AI sin procesar desde 08-11)

## Síntoma

- Servicio `worker` de EasyPanel en **botón amarillo** (unhealthy) desde 2026-08-11.
- Logs: `SQLSTATE[HY000] [2002] php_network_getaddresses: getaddrinfo for tukipass_tukibd failed` → el contenedor del worker no resolvía el hostname de la DB (`tukipass_tukibd`) → imposible hacer `pop` de la cola database.
- Evento previo (08-07): `SQLSTATE[22001] Data too long for column 'audit_status'` en `GenerateEventContentDraftJob` (columna VARCHAR(30), status AI de 34 chars) → job FAIL + run estancado.

## Causas raíz

1. **08-07 — `audit_status` VARCHAR(30)**: la IA devolvió `listo_con_revision_de_promocion`. Corregido con migración `2026_08_07_000001` (→ VARCHAR(191)) + `EventAiContentDraft::normalizeAuditStatus()` (trunca a 190, fallback `needs_human_review`).
2. **08-11 — DNS del worker**: el contenedor perdió la resolución del hostname de la DB (red de EasyPanel tras recreate/restart). El worker quedó caído; el contenedor del `app` (que también corría workers en `docker-start.sh`) siguió procesando colas.

## Correcciones aplicadas (2026-08-21)

| Fix | Detalle |
|---|---|
| Redeploy worker → master | `deployAppService(tukipass/worker)` → contenedor recreado en `38f5c3d5` (resuelve DNS, código actual) |
| Migración en producción | `docker-start.sh` corre `php artisan migrate --force` en cada arranque → `audit_status` varchar(191) aplicado |
| Consolidación de workers | `docker-start.sh` ya **no** lanza `queue:work` internos (commit `398356cd`): el contenedor web = web + scheduler + migraciones. El servicio `worker` es el único procesador oficial, con comando por cola: `default` (512M/300s/tries3), `ai-content` (1024M/300s), `ai-images` (1024M/600s) |
| Runs estancados | Tinker: `EventAiAssistantRun::where('status','running')->where('updated_at','<',now()->subHours(24))->update(['status'=>'failed','error_message'=>'interrumpido por mantenimiento…'])` — ejecutado en producción |
| AutoDeploy worker | ⚠️ **NO habilitado**: token de GitHub inválido en la fuente del worker (`Github token is not valid`). Pendiente: refrescar el token en EasyPanel o cambiar la fuente del worker a `git` (SSH, como el app) |

## Runbook (si vuelve a pasar)

1. **DNS/amarillo**: `deployAppService(tukipass, worker)` (recrea contenedor) → si no: `restartAppService(tukipass, worker)`.
2. **Cola acumulada**: el worker la drena sola (`--tries`); revisar `failed_jobs` con `php artisan queue:retry all` (terminal del app).
3. **Runs AI estancados** (bloquean `MAX_RUNS_PER_EVENT=1`): correr el tinker de arriba.
4. **Migraciones**: se aplican solas en el arranque del `app` (`docker-start.sh`). Verificar `php artisan migrate:status` en el terminal del app.

## Arquitectura de colas (post-fix)

- **Contenedor `app`**: web (php -S :8080) + scheduler (crond) + `migrate --force` + seed de imágenes.
- **Servicio `worker`**: 3 procesos `queue:work` (default, ai-content, ai-images) con flags por cola.
- **DB `tukibd`**: MySQL 9 (hostname interno `tukipass_tukibd`).
