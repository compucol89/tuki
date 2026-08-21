# 14 — Chart.js Dark & Accessibility

**Fecha:** 2026-08-21 · **Versión real:** Chart.js **2.7.2** (verificado en header del bundle: `Version: 2.7.2`) · **API v2** (NO usar `Chart.getChart` — es v3+)

## Configuración resuelta (chart-init.js)

| Canvas | Dataset | Color línea | Fill | tick/legend | grid |
|--------|---------|-------------|------|-------------|------|
| incomeChart | Ingresos mensuales | `#f97316` | `rgba(249,115,22,.08)` | `#c8cdd6` dark / `#6b7280` light | `rgba(255,255,255,.10)` / `rgba(0,0,0,.08)` |
| TotalEventBookingChart | Reservas mensuales | `#6366f1` | `rgba(99,102,241,.08)` | idem | idem |

## Defectos encontrados y corregidos (P2)

1. **Sin guard anti-reinit**: llamar `tukiInitLineChart` 2× sobre el mismo canvas
   creaba 2 instancias (verificado: `Chart.instances` con id duplicado).
   → **Fix**: `window.__tukiCharts[id]` registry + documentado.
2. **`pointBorderColor: '#fff'` hardcoded**: en dark quedaba blanco sobre fondo
   oscuro (borde de punto apenas visible). → **Fix**: `p.pointBorder` token
   (`#171e2b` dark / `#ffffff` light).
3. **Sin reacción al theme switch**: el chart conservaba la palette con la que
   fue creado al togglear el tema. → **Fix**: `tukiRethemeCharts()` — actualiza
   ticks/grid/legend/pointBorder en caliente vía `chart.update()` sin recrear.

## Verificado en runtime

| Check | Antes | Después |
|-------|-------|---------|
| Instancias por canvas | 2 (duplicadas) | 1 |
| pointBorder en dark | `#ffffff` | `#171e2b` |
| toggle light→dark | palette vieja | ticks `#6b7280`→`#c8cdd6`, pointBorder→`#171e2b`, 2 instancias estables |

## Accesibilidad de charts (WCAG 1.1.1 / 1.4.11)

- Los 2 canvas tienen `role="img"` + `aria-label` descriptivo en español ✅
- Fallback `<span class="visually-hidden">` con equivalente textual ✅
- Contraste de la línea `#f97316` sobre fondo de card dark (`#232c3b`): ratio
  >3:1 (non-text UI ≥3:1 ✅)
- ticks `#c8cdd6` sobre fondo card: ≥4.5:1 ✅
- grid `rgba(255,255,255,.10)`: decorativo (no transmite info) — no requiere 3:1

## Nota de data

Los datos vienen de `$eventIncomes`/`$totalBookings` (agregación SQL por mes).
Con datos 0/empty el chart dibuja línea plana — sin error (verificado con
arrays de ceros). No hay empty state dedicado: documentado como gap UX menor
(no bloqueante — las métricas muestran los totales).
