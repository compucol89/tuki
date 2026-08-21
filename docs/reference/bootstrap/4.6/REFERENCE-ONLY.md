# Bootstrap 4.6 — REFERENCE-ONLY (no usar como source of truth)

> SOURCE OF TRUTH - TukiPass. No editar. Capturado: 2026-08-21.

**TukiPass no usa Bootstrap 4.6.** Usa 4.5.3 (frontend) y 4.3.1 (admin/organizer).

| Versión | Estado en TukiPass | Docs oficiales usados |
|---------|--------------------|-----------------------|
| **5.3** | ❌ NO USAR — no aplica al stack actual | — |
| **4.6** | ⚠️ Referencia secundaria (esta nota) | getbootstrap.com/docs/4.6/ |
| **4.5.3** | ✅ Verdad del frontend | `../4.5/` |
| **4.3.1** | ✅ Verdad del admin/organizer | `../4.3/` |

## Regla para agentes de IA

1. **Nunca inferir la versión de Bootstrap desde `package.json`** (no está declarado ahí).
   Determinar por layout/página qué CSS y JS termina descargando realmente Chromium,
   incluyendo assets vendorizados (`public/assets/{front,admin}/`).
2. Para corregir markup/a11y: usar SOLO `../4.5/` (frontend) o `../4.3/` (panel).
3. Esta carpeta solo sirve para entender diffs posteriores, no para implementar.

## Diferencias 4.3 → 4.5 relevantes (resumen de las docs oficiales)

- **Input group:** 4.5 amplía las instrucciones de etiquetado accesible (`label` visible
  preferible sobre `aria-label`/`.sr-only`; ver `4.5/input-group.md`). 4.3 ya documentaba
  `.sr-only`, `aria-label`, `aria-labelledby`, `aria-describedby` para input groups.
- **Custom forms/checks:** 4.5 estandariza el markup de checkboxes/radios/selects custom.
- **Accesibilidad general:** la página de a11y evoluciona entre versiones; Bootstrap advierte
  que **incluso su propia paleta puede producir combinaciones bajo WCAG** — medir los colores
  computados reales (getComputedStyle) contra los ratios de `wcag/understanding/1-4-3-contrast-minimum.md`.

## Diferencias 4.5 → 4.6 (para no asumir)

4.6 fue la última de la rama v4: bugfixes y soporte jQuery 3, sin cambios de API. Markup de
componentes esencialmente idéntico a 4.5.3.

## Dependencias JS reales (para inventario de carga)

Frontend 4.5.3 requiere jQuery + Popper (`bootstrap.4.5.3.min.js`). El panel 4.3.1 requiere
jQuery + Popper (`bootstrap.min.js`). **Verificar por página el orden y la versión real de
cada script descargado** (existe duplicado `jquery-3.6.0.min.js` vs `jquery.min.js` en
`public/assets/front/js/` — determinar cuál carga cada layout).
