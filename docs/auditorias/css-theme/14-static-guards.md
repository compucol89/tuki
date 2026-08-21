# 14 — Static Guards (NO NEW DEBT)

## Comandos

```bash
# Audit estático de theming (gate CI)
npm run audit:organizer-theme        # = bash scripts/audit-organizer-theme.sh

# Test de contrato de theming (Playwright, light × dark × rutas)
npm run test:theme                   # = playwright test --grep @theme
```

## Qué detecta el audit estático

| Check | Detecta | Falla si |
|-------|---------|----------|
| inline `<style>` | nuevos blades con CSS inline | aparece un blade nuevo sin estar en `scripts/baseline-theme.json` |
| hardcoded surfaces | hex claros en `background`/`border` de CSS propio | el conteo supera el baseline (hoy 16, todos intencionales con dark override) |
| `!important` | nuevos usos en CSS propio | el conteo supera el baseline (hoy 882) |

## Baseline

`scripts/baseline-theme.json` — generado al estado actual (2026-08-21).
Regla: **NO NEW DEBT** — la deuda existente se congela; los nuevos cambios
deben justificarse (agregar al allowlist explícitamente).

## CI (GitHub Actions)

`.github/workflows/organizer-theme.yml`:

1. **Job static-audit** (ubuntu, ~1 min): `npm ci` → `npm run dev` → `npm run audit:organizer-theme`
2. **Job playwright-theme** (necesita `static-audit`):
   - `cp .env.example .env` + setear `DB_*` para docker (db/3306/eventos/root/tukipass)
   - `docker compose up -d --build` (la DB se siembra con `tukipass.sql`, incluye las credenciales de test)
   - esperar healthcheck de `http://localhost:8801/`
   - `php artisan key:generate` si `APP_KEY` vacía
   - `npx playwright install chromium --with-deps` → `npm run test:theme`
   - artefactos de fallo (`test-results/`) + `docker compose down -v`

## Branch protection (paso manual en GitHub)

1. Repo → Settings → Branches → Add rule (main)
2. Require status checks to pass: `static-audit` + `playwright-theme`
3. Require pull request reviews before merging (recomendado)

## Documentado

- Al actualizar `@fortawesome/fontawesome-free`, las URLs de @font-face son
  planas (`/webfonts/fa-solid-900.woff2`) con `Cache-Control: immutable` →
  **hacer bump manual** del query si el contenido cambia (ej. `?v=2`).
