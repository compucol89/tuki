# 12 · Verificación Tipográfica Post-Fix — Panel de Organizador

**Fecha:** 2026-08-21 · **Método:** Playwright + `getComputedStyle` + `document.fonts` + `getComputedStyle('::placeholder')` + overflow (evidencia medida).

## Before / After

| Métrica | Antes | Después |
|---|---|---|
| Familias UI involuntarias (Lato + Plus Jakarta) | 2 | **0** |
| Elementos con fallback Lato | 13 firmas | **0** |
| Elementos con fallback Plus Jakarta | 1 (KPIs) | **0** |
| Font requests fallidos / remotos | Lato remoto en 2 auth pages | **0** (eliminado) |
| IBM Plex Mono cargado | — | **Sí** (400/600 verificado en `document.fonts`) |
| KPIs en mono | No | **Sí** (IBM Plex Mono 22/700, 4/4) |
| Score `0%` / `0/7` en mono | No | **Sí** (28/600 y 12/600) |
| Tabla transacciones mono/Inter | Mixto | 16 mono (right) + 16 Inter (left), **0 otras** |
| Pesos tipográficos del score | 800 (5 usos) | 600 (hints 500) |
| Overflow (dashboard, mono) | — | **0** |
| P0 tipográfico | 0 | 0 |
| P1 tipográfico | 2 (fugas Lato/Plus Jakarta) | **0** |

## Evidencia final por rol (dashboard dark, computed)

| Elemento | Family computada | Size | Weight | Estado |
|---|---|---|---|---|
| Page title | Inter | 25.6px | 600 | ✅ |
| KPI `$0` | IBM Plex Mono | 22px | 700 | ✅ |
| Label KPI | Inter | 12px | 500 | ✅ |
| Sidebar nav | Inter | 13px | 500 | ✅ |
| Score `0%` | IBM Plex Mono | 28px | 600 | ✅ |
| Score `0/7` | IBM Plex Mono | 12px | 600 | ✅ |
| Score copy / eyebrow / buttons | Inter | 13/11/12px | 400/600/600 | ✅ |
| Botones / inputs / labels | Inter | 13/13/14px | 600/400/400 | ✅ |
| Título charts | Inter | 15px | 600 | ✅ |

## Score final

| Rubro (peso) | Puntos | Nota |
|---|---|---|
| Font loading + fallbacks (20) | 19 | 0 fugas; quedan 2 stacks del sistema en code/pre (P3) |
| Jerarquía tipográfica (25) | 21 | pesos normalizados 400-700, niveles claros |
| Sistema numérico / mono (15) | 14 | KPIs/score/tablas/charts |
| Legibilidad + ritmo (10) | 9 | Inter unificado |
| Dark/light + contraste (10) | 9 | ambos verificados |
| Responsive + zoom (10) | 8 | sin overflow; zoom 200% pendiente de automatizar |
| Performance fonts (5) | 4 | self-hosted + swap; Inter 800 aún se descarga |
| Arquitectura (5) | 4 | tokens + neutralización raíz + clases semánticas |
| **TOTAL** | **88** | baseline 45 → **+43** |

## Deuda restante (no oculta)

1. Vistas sin marcado de datos: booking details/report, support, withdraw/create, edit-event forms, sidebar badges.
2. Quitar Inter 800 de la carga (ya sin uso).
3. Automatizar zoom 200% / text-spacing / reflow (WCAG 1.4.4/1.4.10/1.4.12).
4. Test de regresión automatizado (fail si reaparece Lato/Plus Jakarta/Helvetica/Arial) — script listo en el patrón, no persistido como CI.
5. Build de producción (`npm run production` + `front-assets:min`) y deploy pendiente.

## Screenshots

- Before: `dashboard-dark-before.png`, `dashboard-light-before.png`
- After: `dashboard-dark-after.png`, `dashboard-light-after.png`
