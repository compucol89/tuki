# 10 · Auditoría Tipográfica Forense — Baseline (Panel de Organizador)

**Fecha:** 2026-08-20/21
**Entorno:** local `http://127.0.0.1:8010` (MySQL 127.0.0.1:3311, fixture `orgaudit@test.local` id 39, evento 123)
**Método:** Playwright CDP + `getComputedStyle` + `document.fonts` + screenshots. Nivel de detalle: evidencia medida, no inferida.

---

## 1. Resumen ejecutivo

El panel declara una fuente principal (Inter, self-hosted) pero **NO la aplica de forma consistente**.
Dos familias fantasma — **Lato** (declarada por el tema Atlantis) y **Plus Jakarta Sans** (regla muerta en `admin-main.css`) — **no están cargadas** y producen fallback involuntario a la fuente del sistema (Helvetica/Arial) en una gran parte de la interfaz. **Score baseline: 45/100.**

## 2. Fuentes del proyecto

| Familia | Estado | Carga | Pesos | Ámbito |
|---|---|---|---|---|
| Inter | ✅ self-hosted | `@fontsource/inter` → `public/css/app.css` | 400,500,600,700,800 (woff2, `font-display: swap`) | panel + frontend |
| Lato | ⚠️ declarada, **NO cargada** en el panel | `atlantis.css:95-119` | — | solo backend auth (Google) |
| Plus Jakarta Sans | ⚠️ declarada, **NO cargada** | `admin-main.css:751` (`!important`) | — | números de stats |
| FontAwesome 6 Free/Brands | ✅ self-hosted | `@fortawesome/fontawesome-free` | 400/900 | iconos |
| Font Awesome 5 Free (residual) | ⚠️ referencia muerta | `admin-main.css:341` (`content:'\f101'`) | — | glyph version-body |

## 3. Evidencia `document.fonts` (dashboard, dark)

```
Inter 400/500/600/700/800 (cargados parcialmente) · FontAwesome6Free 900/400 · FontAwesome6Brands 400
```
**Ausentes:** Lato, Plus Jakarta Sans, IBM Plex Mono (aún no existe). → todo lo que las declara cae a fallback.

## 4. Familias COMPUTADAS por rol (dashboard dark, viewport 1600)

| Rol | Selector | Computado real | Estado |
|---|---|---|---|
| Body | `body` | Inter 14/400 | ✅ |
| **KPI (número)** | `.card-stats .card-title` | **Plus Jakarta Sans 22/700** | 🔴 fallback |
| **Label KPI** | `.card-stats .card-category` | **Lato 12/500** | 🔴 fallback |
| **Sidebar nav** | `.sidebar .nav > .nav-item a p` | **Lato 13/500** | 🔴 fallback |
| **Nombre/rol usuario** | `.sidebar .user .info a` / `.user-level` | **Lato 14/500 · 12/400** | 🔴 fallback |
| Score `0%` | `.od-profile-score__value strong` | Inter 28/**800** | ✅ (peso alto) |
| Score `0/7` | `.od-profile-score__value span` | Inter 12/**800** | ✅ (peso alto) |
| **Score copy** | `.od-profile-score__copy` | **Lato 13/400** | 🔴 fallback |
| **Score eyebrow** | `.od-profile-score__eyebrow` | **Lato 11/800** | 🔴 fallback + 800 |
| **Score botones** | `.od-profile-score__buttons a` | **Lato 12/800** | 🔴 fallback + 800 |
| Botones | `.btn` | Inter 13/600 | ✅ |
| Inputs | `.form-control` | Inter 13/400 | ✅ |
| Labels | `label` | Inter 14/400 | ✅ |
| Título charts | `.card-header .card-title` | Inter 15/600 | ✅ |

## 5. Firmas tipográficas (dashboard dark)

**22 firmas únicas** (familia|px|peso). Fragmentación confirmada:

```
Plus Jakarta Sans: 1 · Inter: 6 · Lato: 13 · ui-monospace: 1 · ui-sans-serif: 2
```

Lato aparece en 11/12/13/14px con pesos 400/500/600/700/800. El `ui-monospace|12px` y `ui-sans-serif|11px/13px` provienen de componentes que usan stacks del sistema (charts/código).

## 6. Carga en red

- 0 font 404 · 0 Google Fonts en el panel (solo `backend/reset-password` y `forget-password` cargan Lato vía WebFont) · woff2 self-hosted con hash · `font-display: swap`.
- Se descargan **8 caras Inter** (400-800 × subsets que hagan falta); el peso 800 se usa pero la dirección es reducirlo.

## 7. Cascade / Specificity (causa raíz de las fugas)

```
atlantis.css:95-119  body,h1-h6,.h1-.h6,p,.navbar,.brand,.btn-simple,.alert,a,.td-name,td,button.close → font-family:'Lato',sans-serif
admin-skin.css:6-17   body,.card,.form-control,label,.btn,.nav-item,.sidebar-wrapper,.main-header,.page-title,h1-h6 → Inter !important  (lista INCOMPLETA)
admin-main.css:751   .dashboard-items .card-stats .card-title → 'Plus Jakarta Sans' !important  (gana por !important+especificidad)
```
- `p`, `a`, `td`, `.alert`, etc. **no están cubiertos** por la lista Inter de admin-skin → quedan en Lato→fallback.
- Los números de stats pierden contra `!important` de Plus Jakarta.

## 8. Jerarquía actual (problemas)

- **Demasiados pesos altos**: 800 en eyebrow/0%/0/7/botones/títulos → "todo compite".
- Body 14px correcto para admin; títulos con `clamp(22-30px)` en 2 reglas duplicadas (`.mt-2.mb-4 h2` y `.page-title`).
- `line-height:1` en KPI (correcto) pero `letter-spacing` poco sistemático.
- **No existe mono/datos**: números, montos, % e IDs usan la misma fuente que el lenguaje.

## 9. Dark/Light, contraste, responsive

- Contraste dark ya corregido en pasada previa (score del perfil ≥5:1, KPIs 6:1).
- Sin overflow en dashboard/transaction a 1600px (baseline).
- Pendiente probar la mono (puede ensanchar) + zoom 200% + text-spacing.

## 10. Score baseline

| Rubro (peso) | Puntos | Nota |
|---|---|---|
| Font loading + ausencia de fallbacks (20) | 6 | Lato + Plus Jakarta + ui-* leaks |
| Jerarquía tipográfica (25) | 10 | 800 excesivo, duplicados, pesos compitiendo |
| Sistema numérico / mono (15) | 3 | inexistente |
| Legibilidad + ritmo (10) | 6 | ok parcial |
| Dark/light + contraste (10) | 7 | dark corregido, light ok |
| Responsive + zoom (10) | 7 | sin overflow, falta probar mono/zoom |
| Performance fonts (5) | 4 | self-hosted+swap, 8 caras |
| Arquitectura / mantenibilidad (5) | 2 | fugas por lista incompleta + !important |
| **TOTAL** | **45** | |

## 11. P0/P1/P2/P3 (baseline)

- **P1** — Fuga Lato en `p/a/td/.alert/.card-category/sidebar/profile-score` → Helvetica (P1 jerarquía + fallback).
- **P1** — Fuga Plus Jakarta en números de KPI → Helvetica.
- **P2** — FA5 residual (`admin-main.css:341`).
- **P2** — Lato remoto (Google) en `backend/reset-password` + `forget-password`.
- **P2** — Pesos 800 excesivos en score/títulos.
- **P3** — `ui-monospace`/`ui-sans-serif` sueltos en charts/código.
- **P3** — Duplicación de `.page-title`/`.card-title` en 3 zonas.

## 12. Screenshots baseline

- `docs/auditorias/data/dashboard-dark-before.png`
- `docs/auditorias/data/dashboard-light-before.png`
- `docs/auditorias/data/baseline-transaction-dark.json`

---

*Documento generado con Playwright/CDP. Próximo: `11_plan_remediacion_tipografica_2026-08-20.md` e implementación.*
