# 12 — Evidencia de verificación (2026-08-21, fases A-D)

Resultados reales capturados tras la migración de blades y limpieza de theme-dark:

| Gate | Comando | Resultado |
|------|---------|-----------|
| Deuda de theming | `bash scripts/audit-organizer-theme.sh` | **PASS** — `!important` 868 (baseline 882), superficies claras 16 (baseline 16), sin `<style>` nuevos |
| Render create | Render server-side `view('organizer.event.create')` | **0 hex · 109 refs token · 7 dark blocks · 0 refs `--adm-*`** |
| Render booking | Render server-side `view('organizer.event.booking.index')` | **0 hex · 80 refs token · 8 refs `--adm-primary*` (marca)** |
| CSS theme-dark | `grep 'data-theme="light"'` | **0** (19 reglas movidas a admin-skin) |
| Tokens panel | `grep 'var(--panel-' theme-dark.css` | 8 (sidebar 0 en contenido) |
| Regresión PHP | `php artisan test` | **187 passed** (181743 assertions) |
| Theme contract (browser) | `npm run test:theme` (Playwright) | Requiere `E2E_ORGANIZER_USERNAME/PASSWORD` (CI); 14 tests definidos |

## Nota sobre verificación browser local

El login del panel tiene `throttle:5,1` (rutas POST /organizer/store) → los intentos locales
frecuentes se bloquean con 429. El contrato visual real (0 islas blancas, 0 azul, 0 overflow)
corre en CI con credenciales E2E reales (`tests/playwright/theme.spec.js`, 14 tests).
