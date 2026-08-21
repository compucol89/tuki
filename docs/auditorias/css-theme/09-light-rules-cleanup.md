# 09 — Limpieza de theme-dark.css: reglas light movidas

**Problema:** `theme-dark.css` (archivo de tema OSCURO) contenía **19 reglas**
`html[data-theme="light"]` (líneas 735, 1139, 1153, 1158, 1195, 1202, 1208, 1222, 1228, 1247,
1251-1255, 1278-1279, 1295-1298 del estado previo). Confundía la responsabilidad del archivo.

## Fix

- **13 bloques** (45 líneas) movidos a `admin-skin.css` (sección "Light-theme overrides").
- **3 bloques mixtos** (selectores light + dark compartiendo cuerpo) **divididos**: regla light
  completa movida a admin-skin; regla dark conservada en theme-dark. La división duplicaba
  cuerpos `!important` (+5) → se **eliminaron los `!important` de las reglas light movidas**
  (ganan por especificidad `html[data-theme="light"]` + orden de carga posterior a atlantis).

## Resultado

- `theme-dark.css`: **0 reglas light** (solo `html[data-theme="dark"]`).
- `admin-skin.css`: 19 selectores light, sin `!important` redundantes.
- Total `!important` global: **868** (baseline 882 — mejora neta de 14).

## Verificación

`grep -c 'data-theme="light"' theme-dark.css` → 0 · `audit-organizer-theme.sh` → PASS.
