# 07 · UI Parity, Performance, Cache

## Paridad UI ↔ hipotético schema

- **Avg visible**: estrellas `width = round(avg,2)*20%` + valor? (el detalle muestra estrellas; el valor numérico explícito está en el tab/count). Cuando `is_shop_rating=1`.
- **Count visible**: `count($reviews)` en la pestaña.
- **Fuente común**: ambos derivan de la misma query `ProductReview::where(product_id)->...` → un futuro JSON-LD con la MISMA query tendría paridad. Hoy no hay JSON-LD → sin divergencia.
- **Regla futura (content parity)**: si UI muestra `4.7 · 25`, el schema no puede decir `4.9 · 112` — mismo dataset o matemáticamente equivalente.

## Performance

- Catálogo (`shop/index`): **N+1×2** por card (`get()` + `avg()`).
- Detalle (`shop/details`): **N+1** por review (query de Customer por cada review).
- Sin índices adicionales (solo PK); sin caché del average (fresco pero costoso).

## Cache / Staleness

- **Sin caché** → sin riesgo de `UI 4.6 vs DB 4.5 vs JSON-LD 4.7` por cachés distintos (no existen).
- Inversa: cualquier futuro agregado cacheado deberá invalidarse en create/update/delete/approve/reject.

## Concurrencia

- Upsert (una fila por user+product): dos POST simultáneos del mismo usuario → segundo hace UPDATE (no duplica) — riesgo bajo.
- Sin contadores denormalizados → sin race conditions de cache.

## Zero/One review (gate futuro)

- **0 reviews**: hoy la UI no muestra estrellas (avg null). Un futuro schema **no debe emitir** `ratingValue:0 / ratingCount:0` — si no hay aggregate real → no emitir.
- **1 review**: representable; no inventar mínimos arbitrarios (Google no lo exige); documentar robustez por separado.
