# 09 · Decision Record

## Decisión

```
FINAL GATE:        G1 — NOT ELIGIBLE / DO NOT EMIT
RECOMMENDATION:    A — NO TOCAR NADA
READINESS SCORE:   39/100
SEO MODIFIED:      NO
```

## Racional

1. **El estado actual es el correcto**: cero `AggregateRating`/`Review` emitidos en todo el sitio. No hay bug que corregir, no hay markup que añadir.
2. **El sistema ProductReview no es defendible hoy**: sin moderación, sin validación server-side, sin vínculo de compra, sin audit trail, sin constraints; dataset vacío (local) y dormante (producción, tienda inactiva).
3. **Google**: `Product` admite ratings (28-resena), pero los ratings deben reflejar reseñas reales y visibles → no elegible con el pipeline actual.
4. **Política interna** (`content-integrity-policy.md`): más estricta → bloquea.

## Path a G3/G4 (documentado, no ejecutado)

Si en el futuro se activa la tienda:

1. **Remediar pipeline** (dataset): validación server de escala (1-5) + constraints DB (CHECK/FK/ENUM), moderación con status/approved_at + audit trail (moderator_id, changed_by, historial), rate limiting, manejo limpio de guest (redirect/401), cliente eliminado (null-safe), seudónimo/consentimiento para autor, índice (product_id, status), eliminar N+1.
2. **Definir política única de redondeo/decimales** (4.666 → 4.7 UI y JSON-LD iguales).
3. **Gates CI** (doc 08) + source of truth único.
4. **Piloto controlado** (flag + pocos productos + Rich Results Test con fixtures locales + monitoring GSC + rollback).

## Hallazgos de referencia (para cuando se actúe)

- P1: sin moderación/estado; sin validación server; sin compra verificable.
- P2: privacidad (nombre completo), guest 500, cliente borrado rompe vista, N+1, sin audit, sin migración en repo (legacy utf8mb3).
- NOT VERIFIED: dataset de producción (tienda inactiva, sin acceso SQL).

## Cierre

> Cada número que eventualmente vea un buscador debe proceder del mismo conjunto de opiniones auténticas y publicables que ve el usuario, estar asociado al producto correcto, ser reproducible desde la DB, resistir manipulación razonable, cumplir la semántica aplicable e incorporarse sin alterar ningún otro componente SEO. **Hoy esa afirmación NO puede demostrarse → NO EMITIR.**
