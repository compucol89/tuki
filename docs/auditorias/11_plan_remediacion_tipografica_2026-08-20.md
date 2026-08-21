# 11 · Plan de Remediación Tipográfica — Panel de Organizador

**Fecha:** 2026-08-20/21 · **Baseline:** 45/100 (doc 10) · **Objetivo:** ≥ 85+ con deuda documentada.

| ID | Problema | Causa raíz | Archivo | Estrategia | Estado |
|---|---|---|---|---|---|
| FIX-1 | Fuga Lato (`p/a/td/th/.alert/.card-category/.h1-.h6/button.close` → Helvetica) | `atlantis.css:95-119` declara Lato; `admin-skin.css` solo cubría body/h1-h6 | `public/assets/admin/css/admin-skin.css` | Neutralización a nivel raíz con la MISMA especificidad que atlantis, cargada después (gana por orden). No toca FA. | ✅ |
| FIX-2 | Fuga Plus Jakarta en números de KPI | `admin-main.css:751` `font-family:'Plus Jakarta Sans' !important` | `public/assets/admin/css/admin-main.css` | Reemplazada por `var(--tuki-font-data)` (IBM Plex Mono) + peso 600. | ✅ |
| FIX-3 | FA5 residual (`\f101` con 'Font Awesome 5 Free') | `admin-main.css:341` | `public/assets/admin/css/admin-main.css` | → `'Font Awesome 6 Free'` + `font-weight:900` (glyph `\f101` = circle-chevron-right existe en FA6). | ✅ |
| FIX-4 | Lato remoto (Google Fonts) en auth backend | `backend/reset-password.blade.php:95`, `forget-password.blade.php:73` | ambos blades | Eliminado el bloque `google:` del WebFont loader. | ✅ |
| FIX-5 | IBM Plex Mono no existía | sin dependencia | `package.json` + `resources/css/app.css` + build | `npm install @fontsource/ibm-plex-mono` (400/500/600/700), import en app.css. | ✅ |
| FIX-6 | Tokens de familia ausentes | — | `admin-skin.css :root` + `style.css :root` | `--tuki-font-ui` / `--tuki-font-data`. | ✅ |
| FIX-7 | Jerarquía: pesos 800 en score/títulos | inline blade + reglas 800 | `resources/views/organizer/index.blade.php` | 800→600 (eyebrow/h3/0%/0/7/labels/botones), hints 500, ls eyebrow .10em. | ✅ |
| FIX-8 | Data typography en dashboard | KPIs en fuente de UI | `admin-main.css` + `index.blade.php` | KPIs→mono (CSS global); `0%`/`0/7`→`.tuki-data`. | ✅ |
| FIX-9 | Data typography en tablas | columnas numéricas sin tratamiento | transaction, income, withdraw/index, event/index, event/ticket/index, event/booking/index | `.tuki-data*` en th+td numéricos (mono + right-align + tabular-nums). | ✅ |
| FIX-10 | Charts no distinguían dato de texto | `chart-init.js` hardcodeaba solo color | `public/assets/admin/js/chart-init.js` | fontFamily por rol: ejes X/leyenda Inter, eje Y/tooltip value Mono. | ✅ |
| FIX-11 | `font-synthesis` / pesos sintéticos | riesgo | — | Evaluado: Inter 400-700 + Mono 400-700 cubren el uso real; no se aplica `font-synthesis:none` para no arriesgar. | ⏳ deuda |

## Verificación (evidencia en vivo)

- Dashboard dark: **0 Lato, 0 Plus Jakarta** (antes 13 Lato + 1 Plus Jakarta). Sólo Inter + IBM Plex Mono (+ stacks del sistema en code/pre, P3).
- KPIs (`$0`, `1`, `0`, `0`) y score (`0%`, `0/7`) → **IBM Plex Mono**.
- Transacciones: 32 celdas → 16 mono (right) + 16 Inter (left), **0 otras**.
- Overflow: 0 en dashboard con la mono.
- `document.fonts`: IBM Plex Mono 400/600 cargado; Inter 400-700 cargado.

## Deuda documentada

- Vistas restantes sin marcado de datos: booking details/report, support tickets, withdraw/create, forms de edit-event, sidebar badges.
- Se descarga Inter 800 aunque ya no se usa (optimizar: quitar 800 de la carga).
- Stacks `ui-monospace`/`ui-sans-serif` en code/pre (P3, legítimos).
- Zoom 200% / text-spacing / reflow no automatizados en esta pasada.
