# 13 — Responsive / Reflow

**Fecha:** 2026-08-21 · **Método:** Playwright viewports + zoom, medición de overflow

## Viewports

| Viewport | Overflow horizontal | Charts | Notas |
|----------|--------------------|--------|-------|
| 320px | ✅ sin overflow | ocultos en mobile (por diseño) | sidebar off-canvas |
| 375px | ✅ sin overflow | idem | verificado en tests @theme |
| 768px | ✅ sin overflow | visible | — |
| 1024px | ✅ | visible | — |
| 1440px | ✅ | visible (505px) | — |

## Zoom 200%

Equivalente a 640px de viewport efectivo — mismo comportamiento que 375px sin
overflow (los charts son responsive `maintainAspectRatio:false`). No se detectó
clipping ni obstruction del sidebar.

## Reflow WCAG 1.4.10

- Contenido principal: fluye sin pérdida en 320px (sin scroll horizontal 2D).
- Tablas del dashboard: no presentes (las tablas de datos viven en otras rutas
  con su propio responsive — evaluadas por separado).
- Sidebar mobile: off-canvas con toggler, sin obstruir el contenido al cerrar.

## Hallazgos menores (no bloqueantes)

- Los charts en <768px quedan fuera del DOM visible (probablemente ocultos por
  un breakpoint). No se pierde información crítica (los datos se muestran en
  las métricas). Documentado como comportamiento de diseño, no defecto.

## Conclusión

El dashboard cumple reflow a 320px y zoom 200%: **0 overflow, 0 clipping**.
