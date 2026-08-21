# 19 — Prevention Gates

**Fecha:** 2026-08-21 · **Estado:** operativos + extendidos en esta auditoría

## Gates activos

| Gate | Comando | Detecta | Baseline |
|------|---------|---------|----------|
| Inline `<style>` | `npm run audit:organizer-theme` | blades nuevos con CSS inline | 12 blades |
| Hardcoded surfaces | idem | hex claros en background/border de CSS propio | 16 |
| `!important` | idem | nuevos usos en CSS propio | 882→868 |
| **Blade raw colors** (NUEVO) | idem | raw colors en `<style>` de blades migrados | 37→17 |
| **Outline suppression** (NUEVO) | idem | `outline: none/0` en admin-main/admin-skin | 3 |
| Theme contract | `npm run test:theme` | 14 tests: 6 rutas × 2 temas + iconos | — |
| A11y contract | `npm run test:a11y` | axe dashboard (2 temas) + 14 páginas públicas | — |
| CI | GitHub Actions | static-audit + playwright-theme en PRs | — |

## Cobertura de tests (dashboard)

- axe light ✅ / dark ✅ (agregado en esta auditoría)
- theme toggle → DB persistence (verificado manualmente; test automatizado
  opcional — el toggle ahora es server-side con fallback)
- charts render + re-theme (guard anti-reinit — test manual verificado)
- reflow 320px (sin overflow — verificado manualmente)

## Regla NO NEW DEBT

Todo raw color nuevo en blades migrados, `!important` sin justificación o
`outline:none` nuevo → CI falla. Los baselines se congelan y se reducen
gradualmente (ej. `!important` 882→868 en esta sesión).

## Siguientes gates recomendados (FOLLOW-UP)

- Test automatizado del toggle theme→DB (requiere intercepción de fetch en
  Playwright — pendiente de implementar como test dedicado)
- Visual regression del dashboard en CI (screenshots deterministas — requiere
  fixture de datos estable)
