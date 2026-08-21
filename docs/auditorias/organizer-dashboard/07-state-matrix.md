# 07 — State Matrix

**Superficie:** `/organizer/dashboard` · **Fecha:** 2026-08-21 · **Método:** Playwright (light/dark × viewports × interacción) + simulación de data states

## Temas × Viewports × Zoom

| Theme | 320 | 375 | 768 | 1024 | 1440 | Zoom 200% |
|-------|-----|-----|-----|------|------|-----------|
| Light | ✅ | ✅ | ✅ | ✅ | ✅ | ⚠️ ver F13 |
| Dark  | ✅ | ✅ | ✅ | ✅ | ✅ | ⚠️ ver F13 |

Verificado hasta la fecha: sin overflow horizontal en 375/768/1440 (test @theme). 320 y zoom 200% pendientes de medición en FASE 5 (13-responsive-reflow).

## Estados de interacción

| Estado | Sidebar | Cards | Score | Botones | Charts |
|--------|---------|-------|-------|---------|--------|
| default | ✅ tokens | ✅ tokens | ✅ tokens | ✅ | ✅ |
| hover | ✅ (11.73:1 dark) | — | — | — | — |
| focus-visible | ✅ outline 2px #f97316 | — | — | — | — |
| active | ✅ 16.71:1 dark | — | — | — | — |
| expanded/collapsed | ✅ | n/a | n/a | n/a | n/a |

Pendiente de medición detallada (FASE 5): hover de cards, focus de inputs/selects, disabled, error/success de forms (el dashboard no tiene forms de edición — solo acciones).

## Data states

Estado actual de la DB (organizador de test "Rumba Colombiana"): eventos publicados con ingresos en Jun/Jul (incomeArr = [0,0,0,0,0,2372000,765000,0,...]). Estados no verificados aún:

| State | Riesgo | Plan |
|-------|--------|------|
| 0 eventos / 0 bookings | score "0/7 listo", charts planos, empty states | simular con segundo organizador sin datos (FASE 5) |
| Large data (100+ bookings) | render de charts + queries | medición query time (F7) |
| Large monetary values | formato de números | verificar `number_format`/mono font en métricas |
| Missing optional data | perfil sin bio/ubicación/pixel | verificar fallbacks en profileDashboard |
| Empty charts | canvas sin datos | verificar que no rompa Chart.js v2 |

## Contraste — estados del score (od-profile-score, dark)

Medido previamente (theme-dark.css): eyebrow `#f4845f`, action-label `#f78a63`, value strong `--od-text` (#e5e5e5) — valores tokenizados en theme-dark.css (líneas ~901-925).

## Conclusión

El dashboard es estable en el happy path (datos actuales). Los gaps reales de states están en: datos vacíos (no probado), zoom 200% y 320px (no probado), y re-init de charts (defecto confirmado). El resto se cubre con los fixes de theme/tokens/queries de esta auditoría.
