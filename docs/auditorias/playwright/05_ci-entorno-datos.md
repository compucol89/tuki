# 05 · Integridad de CI, Entorno, Datos y Determinismo

## CI Protection Matrix (evidencia)

| Suite | Existe | Corre local | **En CI** | Sensible | Determinista |
|---|---|---|---|---|---|
| @e2e | ✅ | ⚠️ 18/19 (FF-001) | ❌ | ✅ (H1, console) | ⚠️ |
| @a11y | ✅ | ✅ 14/14 (+2 skip sin creds) | ❌ | ✅ (axe) | ✅ |
| @aria | ✅ | ✅ 18/18 | ❌ | ⚠️ (parcial: FP-003) | ✅ |
| @visual | ✅ | ⚠️ 2/4 (FF-001) | ❌ | ✅ | ⚠️ (env) |
| @seo | ✅ | ✅ 4/4 | ❌ | ✅ | ✅ |
| @legal | ✅ | ✅ 8/8 | ❌ | ✅ | ✅ |
| @theme | ✅ | ✅ 16/16 (con creds) | ✅ **única** | ✅ (excepto FP-004) | ✅ |

**P0:** 6 de 7 suites (87 - 16 = 71 tests) **no tienen gate de CI**. Un merge puede romper e2e/a11y/aria/visual/seo/legal y CI queda verde.

## Pipeline CI (`organizer-theme.yml`)

- `playwright-theme`: ubuntu, 30 min, `needs: static-audit` · checkout → `.env` (DB_HOST=db/3306) → `docker compose up -d --build` (seed tukipass.sql) → healthcheck 60×5s en :8801 → `key:generate` → `npm ci` → `npx playwright install chromium --with-deps` → `npm run test:theme` → artifacts (fallo) → apagar stack.
- **Healthcheck**: solo HTTP 200 — no garantiza "migraciones aplicadas + seed listo + login organizer listo" (P3: definir TEST ENVIRONMENT READY explícito). Las migraciones se aplican en `docker-start.sh`, que corre dentro del contenedor al arrancar → el healthcheck 200 implica que docker-start.sh ya corrió (bloqueante) → migraciones+seed presentes ✓ (mitigado de facto).
- **Timeout budget**: job 30 min vs test 90s — sin inconsistencias críticas.

## Entorno (parity)

| Factor | Local | CI | Prod |
|---|---|---|---|
| OS | darwin | ubuntu | — |
| BaseURL | localhost:8801 | localhost:8801 (docker) | www.tukipass.com |
| APP_DEBUG/Debugbar | dev (excluido en axe) | dev (seed) | off |
| Credenciales organizer | coinciden con seed CI (verificado) | seed tukipass.sql | distintas |

**FF-001 (host)**: el home genera assets con `127.0.0.1:8801` (host del request del contenedor?) mientras los tests navegan `localhost:8801` → CSP bloquea. En CI (docker + localhost) es **probable que ocurra lo mismo** si @e2e/@visual se agregaran → clasificado: environment-sensitive.

## Datos / aislamiento

- `docker compose up` **reutiliza volúmenes** entre corridas si no se hace `down -v` → estado de DB puede persistir entre corridas CI (riesgo P3 de contaminación de datos; no verificado el compose en detalle).
- `fullyParallel: true` + DB compartida: sin colisiones observadas en @theme (16 tests en paralelo pasaron), pero sin garantía formal de aislamiento (P3).
- `retries: 0`: correcto (no oculta flakiness); observado: e2e estable excepto FF-001; theme estable en 1 corrida (16/16).

## Observabilidad de fallos

- Trace + screenshot + artifact (7 días) ✓ · mensajes con expected/actual ✓ (e2e, visual, aria) · **sin video**, sin reporte HTML, sin Axe JSON completo (solo violations), sin network logs (P3).
- **Artifact security**: traces/screenshots pueden contener credenciales del seed y datos de sesión — retención 7 días, acceso al repo; riesgo bajo pero no revisado en profundidad (documentado).

## Drift de listas de rutas entre specs

- e2e, a11y, aria comparten la misma lista de 15 páginas (duplicada en 3 archivos) → **route manifest drift riesgo**: cambiar una lista sin las otras es un bug silencioso (P2 recomendación: manifest único). Se verificó que hoy las 3 listas coinciden.
