# 17 — Data / Query Performance

**Fecha:** 2026-08-21 · **Método:** inspección de código + instrumentación disponible

## Controller: OrganizerController@index (dashboard)

| Query | Antes | Después | Impacto |
|-------|-------|---------|---------|
| total_events | `->get()->count()` | `->count()` | −fetch de todas las filas |
| total_event_bookings | `->get()->count()` | `->count()` | idem |
| transcation_count | `->get()->count()` | `->count()` | idem |
| profileEventStats | 3 counts sobre clone | sin cambio (ya óptimo: 3 SQL counts) | — |
| Language default | `first() ?: first()` | `firstOr(fn)` | sin cambio real (2 queries solo en fallback) |
| Settlement summary | pluck + 2 whereIn batch | sin cambio (ya batched, sin N+1) | — |
| 2 agregaciones mensuales | SQL groupBy | sin cambio | — |
| basic_settings | 1 query | sin cambio | — |

## N+1 audit

- **Sin N+1 por fila** en el dashboard: settlement usa `whereIn` + `groupBy` en
  colección (2 queries totales, no 1 por evento) ✅
- `profileDashboard`: 2 queries fijas (Language + OrganizerInfo) — sin bucle ✅
- `summarize()`: todo in-memory sobre colecciones ya cargadas ✅

## Conteo de queries por render

**Estimación: ~11-12 queries** (3 counts + 3 counts de perfil + 2 agregaciones
+ 2 settlement + 1 settings + 1 language + 1 organizer_info).

Mejora aplicada: −3 queries fetch (los `->get()->count()`).

## preventLazyLoading

No está activo. Recomendación (documentada, no activada en prod):
```php
// AppServiceProvider::boot() — solo en entornos de desarrollo/test
Model::preventLazyLoading(!app()->isProduction());
```
Impacto: detecta N+1 futuros en desarrollo sin afectar producción.

## Conclusión

Sin problemas de rendimiento críticos: el dashboard es liviano (~12 queries,
todas indexables por organizer_id). Las mejoras aplicadas reducen transferencia
de datos. Sin caché necesaria a este volumen; si crece, candidatos: counts
agregados (eventos/reservas/transacciones) con cache TTL corto.
