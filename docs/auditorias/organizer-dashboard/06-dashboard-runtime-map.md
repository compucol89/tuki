# 06 — Dashboard Runtime Map

**Superficie:** `/organizer/dashboard` · **Fecha:** 2026-08-21 · **Método:** reconstrucción de código + runtime Playwright

## Cadena completa

```
GET /organizer/dashboard
  → routes/organizer_dashboard.php:6
  → middleware: auth:organizer, admin.locale, Deactive:organizer, EmailStatus:organizer
  → BackEnd\Organizer\OrganizerController@index (línea 41)
  → EventSettlementService::dashboardSummaryForOrganizer
  → view('organizer.index')  (resources/views/organizer/index.blade.php, ~415 líneas)
  → layout: organizer.layout → partials: styles (16 CSS), top-navbar, side-navbar, scripts (25+ JS)
  → DOM: H1 "Bienvenido de vuelta", score de perfil (.od-profile-score), métricas,
    2 charts canvas (incomeChart, TotalEventBookingChart)
  → JS: chart.min.js (v2.7.2) + chart-init.js (tukiChartPalette / tukiInitLineChart)
```

## Controller — queries reales (index(), líneas 41-130)

| # | Query | Método actual | Problema |
|---|-------|---------------|----------|
| Q1 | `Event::where(organizer_id)->get()->count()` | fetch completo + count en PHP | inneficiente → `->count()` |
| Q2 | `Booking::where(organizer_id)->get()->count()` | idem | idem |
| Q3 | `Transaction::where(organizer_id)->get()->count()` | idem | idem |
| Q4 | `Event::where(organizer_id, status=1)` ×3 clones (total/upcoming/past) | 3 counts sobre clon | 1 count + 2 where counts OK |
| Q5 | `DB::table('bookings')` income por mes (year actual, paymentStatus=completed) | agregación SQL ✓ | OK |
| Q6 | `DB::table('bookings')` count por mes | agregación SQL ✓ | OK |
| Q7 | `EventSettlementService::dashboardSummaryForOrganizer` → `Event::pluck('id')` + 2 `whereIn` + sumas in-memory | 3 queries + PHP | aceptable (2 queries batch) |
| Q8 | `DB::table('basic_settings')->where('uniqid', 12345)->first()` | 1 query | OK |
| Q9 | `profileDashboard()` → `Language::where(is_default)->first() ?: first()` + `OrganizerInfo::where(...)->first()` | 2 queries | OK, podría eager |

**Total: ~13 queries por render** (medición con DB::listen pendiente en F7). Sin N+1 por fila; el principal desperdicio son los `->get()->count()` (Q1-Q3) y el doble `Language` fallback.

## Runtime verificado (Playwright, dark, 1440px)

- H1 único: ✅
- 2 charts renderizados: ✅ (pixel de la línea naranja `[255,115,26,20]` en canvas incomeChart) — Chart.js **v2.7.2** (verificado en header del bundle: `Version: 2.7.2`). NOTA: `Chart.getChart()` no existe en v2 — es API v3+; cualquier test debe usar `Chart.instances`.
- 0 superficies blancas en dark: ✅ (cards, od-profile-score, dashboard-items)
- Console: 0 errores JS del dashboard (los 404 de thumbnails provienen de rutas de eventos con imágenes faltantes en el seed — no del dashboard)
- Duplicación de charts en re-init: ⚠️ CONFIRMADO — `tukiInitLineChart` no tiene guard; llamarlo 2× crea 2 instancias sobre el mismo canvas (relevante para theme switch)

## Charts — configuración resuelta (chart-init.js)

| Canvas | Dataset | Color | Fill |
|--------|---------|-------|------|
| incomeChart | Ingresos mensuales | `#f97316` | `rgba(249,115,22,.08)` |
| TotalEventBookingChart | Reservas mensuales | `#6366f1` | `rgba(99,102,241,.08)` |

Palette tema-aware (`tukiChartPalette`): tick/legend `#c8cdd6` (dark) / `#6b7280` (light), grid `rgba(255,255,255,.10)` (dark) / `rgba(0,0,0,.08)` (light).
