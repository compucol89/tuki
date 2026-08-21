# 20 — Regression Evidence

**Fecha:** 2026-08-21 · **Método:** mediciones antes/después con runtime real

## Métricas

| Métrica | Antes | Después |
|---------|-------|---------|
| Raw colors en blades migrados | 48 (4+40+4) | **0** |
| `!important` en CSS propio | 882 | **868** (−14, limpieza) |
| Axe violaciones dashboard light | 9 (5 contraste + 2 label + 2 list) | **0** |
| Axe violaciones dashboard dark | — (no medido individual) | **0** |
| Theme persistence (toggle→DB) | ❌ no persistía | ✅ DB + DOM + localStorage sincronizados |
| Chart instancias duplicadas | ✅ (re-init duplicaba) | ❌ guard anti-reinit |
| Chart pointBorder dark | #ffffff (invisible) | #171e2b (visible) |
| Chart re-theme en caliente | ❌ palette congelada | ✅ ticks/grid/point actualizados |
| Queries dashboard (counts) | 3 × `->get()->count()` | 3 × `->count()` (−fetch filas) |
| Cascade .event-cover-box | transparente (perdía) | gradient tokens (gana) |
| Sidebar label contraste light | 4.18:1 | **4.91:1** |
| Overflow 320px | no medido | ✅ 0 |

## Suites

| Suite | Resultado |
|-------|-----------|
| `npm run test:theme` | ✅ 14/14 PASS |
| `npm run test:a11y` (dashboard ×2) | ✅ 2/2 PASS |
| `npm run audit:organizer-theme` | ✅ PASS (5 checks) |
| Compilación assets | ✅ webpack OK |

## Evidence directory

- `git-baseline.txt` — estado previo al trabajo
- Artefactos vinculados en issues.csv (computed styles y reproducciones
  documentadas en 06/10/11/14)
