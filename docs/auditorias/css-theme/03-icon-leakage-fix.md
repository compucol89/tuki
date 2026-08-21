# 03 — Fix del icono azul #1572E8 (evidencia)

**Síntoma:** iconos del menú del panel aparecían azules (#1572E8) en vez del color del tema.

## Causa raíz

Regla de `atlantis.css` (vendor) con especificidad `(0,6,2)` ganaba sobre la capa Tuki en TODOS
los estados (light/dark, item activo/sub-item).

## Fix

`admin-skin.css:2439-2468` — Vendor Override con la **misma especificidad** `(0,6,2)` +
`!important` + declarado después de atlantis en el orden de carga. Análisis de cascada confirma
la victoria en todos los escenarios.

## Evidencia

- `#1572E8` en capa Tuki: **0 referencias vivas** (1 en comentario).
- `theme-dark.css`: 0.
- Quedan 90 en `atlantis.css` (vendor, intocado — es su paleta original).

## Regresión

`tests/playwright/theme.spec.js` verifica en runtime: 0 elementos con color computado azul
`#1572E8` en las 6 rutas × light/dark.
