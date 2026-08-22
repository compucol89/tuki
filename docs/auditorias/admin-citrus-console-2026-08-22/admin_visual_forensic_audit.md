# Auditoría Forense UI Admin — TukiPass Admin Citrus Console

- **Fecha:** 2026-08-22
- **Base:** `master` @ `8aa6bfb1` · Rama de trabajo: `audit/admin-citrus-console-2026-08-22`
- **Superficie:** Organizer (8 rutas) + Admin (3 rutas) · light/dark · 1440×900 → 360×800 + alturas bajas
- **Metodología:** DISCOVER → MEASURE → AUDIT → ROOT CAUSE → REMEDIATE (por bloques con commit) → VERIFY → CERTIFY
- **Identidad a preservar:** Citrus Console (Operate mode) — naranja `#F97316` como acción, superficies neutras, compacto, calmo, operativo.

---

## 1. Executive summary

**Estado inicial: FAIL (remediación programada en la misma rama, por bloques).**

| Dimensión | Score | Evidencia clave |
|---|---|---|
| Visual System | 55 | 5 sistemas de tokens coexistiendo; doble naranja canónico |
| Geometry | 60 | Wizard 980×640 estable; 3 componentes con overflow real medido |
| Spacing | 45 | Sopa de valores (13px×4, 22px×9, 28px×4); fraccionarios 11.5/12.5/13.5px |
| Typography | 58 | Inter correcta; jerarquía h5→h3 invertida; fraccionarios solo en wizard |
| Components | 55 | `.modal-content` definido en 3 archivos; admin-skin pisa al wizard |
| Responsive | 52 | Matriz mayormente limpia; fallan stepper, header-btn y leaflet |
| Mobile | 50 | Overflow real en 430/390/375/360 (stepper 395-477px, botón 405px) |
| Dark Mode | 60 | Funcional y 100% scoped a `html[data-theme=dark]`; fragmentado en 3 mecanismos |
| Accessibility | 42 | ~25 labels sin `for`; `aria-current="false"` inválido; sin focus trap; headings invertidos |
| CSS Maintainability | 35 | ~996 `!important` custom; ~630 hex; 14 radios; 17 variantes de breakpoint |
| Data Integrity | 82 | Placeholders legítimos; `$` hardcodeado, copy de progreso inconsistente, rickroll |
| Regression Safety | 70 | Playwright (e2e/a11y/aria/theme/visual) + gate de deuda `audit-organizer-theme.sh` |

**Top hallazgos P0/P1:**
1. **[P1] El wizard pierde su diseño contra admin-skin**: `.modal-content` radius 16px→14px, footer padding 8/16px→18/22px (`admin-skin.css` L1049-1056 con `!important`).
2. **[P1] Overflow horizontal mobile en stepper del wizard** (min-width 72px × 6 = 395-477px en viewports 360-430).
3. **[P1] Overflow horizontal en `a.header-ingresar-btn`** del layout (right=405px en 390/375) — shared organizer+admin.
4. **[P1] Leaflet tiles desbordan `edit-event` en todos los viewports** (mapa ~47-82px más ancho que el viewport en mobile).
5. **[P1] A11y**: labels sin asociación, `aria-current="false"`, sin focus trap ni retorno de foco, botones `<a href="javascript:void(0)">`.
6. **[P2] Deuda CSS sistémica**: 5 sistemas de tokens, doble naranja (`#F97316` vs `#e05d38`), dark repartido en 3 archivos, `--od-*` huérfano.

## 2. Inventario de hallazgos (P0–P3)

| ID | Sev | Ruta | Viewport | Componente | Issue | Root cause | Status |
|---|---|---|---|---|---|---|---|
| A-01 | P1 | add-event | todos | `.modal-content` | radius 14px (diseño: 16px) | admin-skin L1049 `radius 14px !important` gana al wizard | abierto |
| A-02 | P1 | add-event | todos | `.modal-footer` | padding 18/22 (diseño: 8/16), alto 77-108px | admin-skin L1053-1056 `!important` | abierto |
| A-03 | P1 | add-event | 430/390/375/360 | stepper | overflow 395-477px | `__item min-width:72px` × 6 sin wrap/scroll | abierto |
| A-04 | P1 | shared layout | 390/375 | `a.header-ingresar-btn` | right=405px > viewport | ancho fijo/posición en header mobile | abierto |
| A-05 | P1 | edit-event | todos | `#eventVenueMapCreateOrganizer` | leaflet tiles right 442-1619px | contenedor Leaflet sin overflow controlado | abierto |
| A-06 | P1 | wizard | — | labels | ~25 `<label>` sin `for` | Blade legacy | abierto |
| A-07 | P1 | wizard | — | stepper nav | `aria-current="false"` (valor inválido) | JS setea string 'false' | abierto |
| A-08 | P1 | wizard | — | modal | sin focus trap ni retorno de foco; `backdrop:static`+`confirm()` | Bootstrap default sin custom | abierto |
| A-09 | P1 | wizard | — | headings | h5 (modal) → h3 (pasos) → h4 (shell) invertidos | marcado legacy | abierto |
| B-01 | P2 | admin-skin | — | tokens | 5 sistemas; `--event-primary #e05d38` ≠ `--adm-primary #F97316` | 3 generaciones de skin sin consolidar | abierto |
| B-02 | P2 | theme-dark | — | tokens | `--od-*` sin par light (muerto) | refactor a medias | abierto |
| B-03 | P2 | admin-skin+theme-dark | — | dark mode | override de tokens repartido en 2 archivos + legacy `body[data-background-color]` | evolución sin consolidación | abierto |
| B-04 | P2 | todos los CSS | — | radios/sombras/espaciado | 14 radios, 15+ sombras, 13/22/28px, fraccionarios | sin escala de diseño | abierto |
| B-05 | P2 | todos los CSS | — | breakpoints | 17 variantes (`991` vs `991.98`, etc.) | copy-paste por archivo | abierto |
| B-06 | P2 | wizard | — | tipografía | `font-size` 11.5/12.5/13.5px | parches puntuales sin escala | abierto |
| B-07 | P2 | event-wizard.js | — | moneda | `$` hardcodeado (L337/347) en vez de `currency_text` | shortcut JS | abierto |
| B-08 | P2 | admin_dropzone.js | — | galería create | `loadImgs=0` → `0.length` → bloque muerto (L118) | bug silencioso | abierto |
| B-09 | P2 | event-wizard.js | — | dropzone | `dispatchEvent(resize)` + destroy/recreate (L276-284) | doble init | abierto |
| B-10 | P2 | admin-main.js | — | scroll | anima scroll de página hacia `#eventErrors` dentro del modal (L1109) | JS asume page, no modal | abierto |
| B-11 | P2 | event-wizard.js | — | listeners | `input`/`change` document-wide sin debounce (L818-825) | event delegation cruda | abierto |
| C-01 | P3 | wizard | — | copy | progreso "30 segundos" (create) vs "20 segundos" (async-progress) | copy divergente | abierto |
| C-02 | P3 | wizard | — | placeholder | YouTube rickroll como placeholder | ejemplo meme | abierto |
| C-03 | P3 | wizard | — | clase muerta | `gap-2` no existe en Bootstrap 4 | refactor a medias | abierto |
| C-04 | P3 | partials | — | huérfanos | `ai-generate-button`, `ai-images-status` sin @include | código no usado | abierto |
| C-05 | P3 | admin-main | — | color | `#1a2035` ×8 y `#1572E8` ×1 hardcodeados | fuga Atlantis | abierto |

## 3. Evidencia de medición (baseline)

- **111 screenshots** + `measurements.json` en `baseline/` (excluidos de git, 17MB).
- Wizard 1440×900: content **980×640**, radius **14px** (debería 16), footer **978×77** (debería ~56), node 30×30 radius 11px, label 12px, next-btn radius 9px.
- Wizard mobile 390×844: fullscreen correcto (0,0,390,844); stepper overflow 395px; footer 108px.
- Low height 1366×650: content 594px ≤ max-height 602px ✓ footer visible ✓.
- Dark pass (1440 + 390): funcional; overflow solo en stepper + leaflet.
- Sin overflow global en dashboard/events-list/bookings/transactions/profile/support (0 en 9 viewports).
- Admin: 0 overflow en 9 viewports salvo `header-ingresar-btn` (390/375) — el layout es compartido.

## 4. Hardcode data audit

| Literal | Clasif. | Decisión |
|---|---|---|
| `Ej: 500`, `Ej: 4`, `Ej: 12000`, dimensiones de imagen | B. copy/placeholder legítimo | mantener |
| `$` en JS de precios | F. dato de negocio hardcodeado | reemplazar por `currency_text` |
| "30 segundos y 3 minutos" vs "20 segundos y 2 minutos" | B. copy inconsistente | unificar |
| Spotify artist ID real + YouTube rickroll | D. demo/placeholder | neutralizar URLs |
| `-34.6037/-58.3816` (Buenos Aires) | C. config/geo default legítimo | mantener |
| Sin estadísticas ni montos de negocio falsos en blades | — | ✅ limpio |

## 5. Impeccable report

- **Versión:** local `.agents/skills/impeccable` · **Modo:** Operate · **Ruta:** refinement sobre implementación incumbente (no hay PRODUCT.md/DESIGN.md → la implementación es la autoridad visual).
- **Principios aplicados:** "Refinement preserves" (no reemplazar identidad), craft-floor se carga antes de editar UI, verificación en pasadas acotadas (baseline → fix → detect → re-test), detector mecánico `detect.mjs` al final.
- **Hallazgos adicionales del flujo:** la skill exige congelar evidencia antes de editar (hecho: baseline BEFORE) y recomienda no perseguir métricas absurdas (0 `!important` no es el objetivo; reducir los injustificados sí).

## 6. Matriz responsive (estado actual, overflow > 0 = FAIL)

| Ruta | 1440 | 1366 | 1280 | 1024 | 768 | 430 | 390 | 375 | 360 |
|---|---|---|---|---|---|---|---|---|---|
| org dashboard | PASS | PASS | PASS | PASS | PASS | PASS | PASS | PASS | PASS |
| org events-list | PASS | PASS | PASS | PASS | PASS | PASS | FAIL | FAIL | PASS |
| org wizard | PASS | PASS | PASS | PASS | PASS | FAIL | FAIL | FAIL | FAIL |
| org edit-event | FAIL | PASS | FAIL | FAIL | FAIL | FAIL | FAIL | FAIL | FAIL |
| org bookings/transactions/profile/support | PASS ×4 en 9 viewports |
| adm dashboard | PASS ×9 | — | — | — | — | — | — | — | — |
| adm events-list | PASS ×9 salvo 390/375 (FAIL) |
| adm add-event | PASS ×9 |

## 7. Residual debt (al inicio)

| Item | Sev | Por qué no se arregla ya | Acción recomendada |
|---|---|---|---|
| Atlantica vendor (920 reglas sidebar, 794 !important) | P2 | Vendor freeze (§43) | overrides first-party únicamente |
| Reescritura total de blades legacy | P3 | Riesgo funcional > beneficio | primitivas compartidas |
| Snapshots visuales no commiteados | P3 | 17MB | evidencia local + informe |

## 8. Estado

- [x] repo inspeccionado · [x] UI inventory · [x] baseline capturado · [x] auditorías (CSS/blade/JS/medición) · [x] informe
- [ ] remediación por bloques (3.1→3.6) · [ ] verificación adversarial · [ ] certificación final

Los bloques de remediación se ejecutan en commits separados sobre esta rama; este informe se actualiza al cierre con scores AFTER.
