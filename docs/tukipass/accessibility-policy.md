# Accessibility Policy — TukiPass

> Versión: 1.0 · 2026-08-21 · Estado: vigente
> Referencias: `docs/reference/wcag/` (WCAG 2.2, Understanding, How to Meet) · `docs/reference/bootstrap/{4.3,4.5}/`

## Objetivo

Cumplimiento **WCAG 2.2 nivel AA** (el estándar vigente; W3C recomienda adoptarlo para
desarrollo nuevo y actualizaciones) en el frontend público, checkout, y paneles
admin/organizer en la medida del rol de cada superficie.

## Principios

1. **Axe passing ≠ WCAG passing.** Las pruebas automatizadas no sustituyen la evaluación
   manual. Cero violaciones automáticas es condición necesaria, no suficiente.
2. **Contraste medido, no asumido.** Bootstrap advierte que su propia paleta puede producir
   combinaciones por debajo de WCAG. Todo par de colores se mide con los valores **computados
   reales** (`getComputedStyle`) contra 4.5:1 (AA, texto normal) y 3:1 (texto grande/UI).
   Aplicar a light **y** dark mode.
3. **HTML = significado, CSS = apariencia.** No cambiar `<h1>`→`<h6>` por estética:
   corregir la semántica y ajustar el CSS. Desacoplar estructura de presentación.
4. **Teclado y foco obligatorios.** Toda interacción operable por ratón debe ser operable
   por teclado (2.1.1), con foco visible (2.4.7) y no oscurecido (2.4.11).

## Criterios prioritarios para esta base de código

| Criterio | Tema | Referencia |
|----------|------|------------|
| 1.3.1 | Info y relaciones (headings, landmarks, labels) | `wcag/understanding/1-3-1-info-and-relationships.md` |
| 1.4.3 / 1.4.6 | Contraste mínimo/mejorado (light + dark) | `wcag/understanding/1-4-3-contrast-minimum.md`, `1-4-6-contrast-enhanced.md` |
| 1.4.10 | Reflow (320px sin scroll bidireccional) | `wcag/understanding/1-4-10-reflow.md` |
| 1.4.11 | Contraste de no-texto (bordes, estados) | `wcag/understanding/1-4-11-non-text-contrast.md` |
| 2.1.1 | Teclado | `wcag/understanding/2-1-1-keyboard.md` |
| 2.4.6 | Headings y labels descriptivos | `wcag/understanding/2-4-6-headings-and-labels.md` |
| 2.4.7 | Foco visible | `wcag/understanding/2-4-7-focus-visible.md` |
| 2.4.11 | Foco no oscurecido | `wcag/understanding/2-4-11-focus-not-obscured.md` |
| 2.5.8 | Tamaño de objetivo ≥ 24×24 CSS px | `wcag/understanding/2-5-8-target-size-minimum.md` |
| 3.3.2 | Labels o instrucciones | `wcag/understanding/3-3-2-labels-or-instructions.md` |

## Reglas por componente (Bootstrap real del runtime)

- Frontend: seguir `bootstrap/4.5/` (markup, `.sr-only`, roles, aria).
- Panel admin/organizer: seguir `bootstrap/4.3/`.
- Input groups: label visible preferible; `aria-label`/`aria-labelledby`/`aria-describedby`
  como mecanismos documentados (ver `4.5/input-group.md`).
- No usar color como único medio para estados (agregar icono/texto) — 1.4.1.
- No acoplar la política a qué haga Axe: definir también checks manuales de foco,
  navegación de teclado y lectura de heading order (ver `frontend-quality-gates.md`).

## Pruebas

- Automatizadas: Axe (`@axe-core/playwright`) + ARIA snapshots + checks de contraste
  con colores computados (Fase 4).
- Manuales obligatorias en el quality gate: tabulación completa, foco visible, zoom 200%,
  viewport 320px, lector de pantalla en flujo crítico (home → evento → checkout).
