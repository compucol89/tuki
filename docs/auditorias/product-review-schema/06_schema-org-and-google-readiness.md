# 06 · Schema.org & Google Readiness

## Matriz de elegibilidad

| Property | Fuente real | Confiable | Visible | Estable | Elegible hoy | Riesgo |
|---|---|---|---|---|---|---|
| ratingValue | `AVG(review)` (float, sin filtro) | ❌ | ✅ estrellas | ✅ | ❌ | ALTO (escala no enforced) |
| ratingCount | `COUNT(*)` producto | ❌ (incluye inválidos) | ⚠️ tab "Review (n)" | ✅ | ❌ | ALTO |
| reviewCount | idem | ❌ | ⚠️ | ✅ | ❌ | ALTO |
| bestRating/worstRating | escala 1-5 inferida (`*20%`) | ⚠️ | — | ✅ | ⚠️ | MEDIO |
| reviewBody | `comment` escapado | ❌ (sin moderación) | ✅ | ✅ | ❌ | MEDIO |
| author | `fname lname` público | ⚠️ | ✅ | ✅ | ❌ | PRIVACIDAD |
| datePublished | `created_at` | ✅ | ✅ (d-m-Y) | ✅ | ⚠️ | BAJO |

## Google eligibility (Capas B/C separadas)

- **Schema.org**: `Product.aggregateRating` y `Product.review` son válidos (AggregateRating.md / Review.md). ✅ estructural.
- **Google (28-resena.md)**: `Product` es tipo soportado (L24) con ejemplo de aggregateRating (L195). La restricción **self-serving se documenta para Organization/LocalBusiness (L22,39) y NO se extrapola automáticamente a Product** — cumplido: no mezclé capas. Pero Google exige que los ratings reflejen **reseñas reales y visibles** (directrices generales).
- **TukiPass policy**: `content-integrity-policy.md` exige provenance + dataset verificable → más estricta que Google → **bloquea hoy**.
- **Realidad**: dataset vacío/dormante + sin controles → **no elegible hoy**.

## Google eligibility matrix (condensada)

| Requisito | Schema.org | Google | TukiPass | Estado real |
|---|---|---|---|---|
| ratingValue | ok | ok | ok* | ❌ (sin validación) |
| ratingCount/reviewCount | ok | ok | ok* | ❌ |
| visible rating | — | exigible | ok | ⚠️ (solo con tienda activa) |
| genuine reviews | ok | exigible | exigible | ❌ (no demostrable) |
| author | ok | ok | ok* | ❌ (privacidad) |
| reviewBody | ok | ok | ok* | ❌ (sin moderación) |

\* ok si el dataset existiera y fuera defendible.

## Readiness score

| Rubro | Pts | Justificación |
|---|---|---|
| Authenticity/Provenance (20) | 2 | Sin compra verificable, dataset vacío |
| Data integrity (15) | 4 | Sin validación/constraints/escala |
| Product binding (10) | 7 | product_id ok; sin existencia check |
| Moderation/Abuse (10) | 1 | Cero |
| Visible parity (10) | 8 | UI muestra avg+count; escapado |
| Schema semantics (10) | 6 | Adición aditiva trivial |
| Google policy (10) | 6 | Product soportado; genuinidad no |
| Security/Privacy (5) | 2 | XSS ok; nombre público; guest 500 |
| Cache/Consistency/Perf (5) | 2 | Sin caché, N+1 |
| Testability/Governance (5) | 1 | Sin tests ni gates |
| **TOTAL** | **39/100** | |
