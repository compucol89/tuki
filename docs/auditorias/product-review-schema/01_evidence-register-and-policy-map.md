# 01 · Evidence Register + Source/Policy Map

## Fuentes de verdad consultadas

| Fuente | Archivo | Uso |
|---|---|---|
| Schema.org AggregateRating | `docs/reference/schema-org/AggregateRating.md` | Semántica: ratingValue, ratingCount, reviewCount, bestRating, worstRating, itemReviewed; decimal point |
| Schema.org Review | `docs/reference/schema-org/Review.md` | Semántica: reviewBody, author, datePublished, itemReviewed |
| Google Review snippets | `docs/docs-google-search/03-datos-estructurados/28-resena.md` | Product soportado (L24), self-serving para Organization/LocalBusiness (L22,39), ejemplo Product con aggregateRating (L195) |
| Política interna | `docs/tukipass/content-integrity-policy.md` | Provenance obligatoria, reviews sin pipeline → published=false, AggregateRating solo con dataset real |
| Claims | `docs/auditorias/google-search/claim-register.csv` | "+500 opiniones verificadas" → UNVERIFIED |
| Schema helpers | `shop/details.blade.php:296-330` | Product JSON-LD actual |

## Evidencia

| ID | Claim | Evidence | Confidence |
|---|---|---|---|
| EV-001 | Product JSON-LD NO emite aggregateRating/review | `shop/details.blade.php:296-330` (name/description/image/url + offers condicional) + baseline runtime | CONFIRMED |
| EV-002 | No se emite AggregateRating/Review en ninguna página | grep producción JSON-LD (home/evento/organizador/faq/blog/tienda) — ausente | CONFIRMED |
| EV-003 | ProductReview model sin scopes/relations/casts | `app/Models/ShopManagement/ProductReview.php` (solo fillable) | CONFIRMED |
| EV-004 | Tabla sin status/moderación/order_id/soft-delete | `SHOW CREATE TABLE product_reviews` (7 columnas) | CONFIRMED |
| EV-005 | Controller sin validación; escala solo frontend | `ShopController@review:658-686` (`$request->all()`, sin validate) + form estrellas 1-5 | CONFIRMED |
| EV-006 | Upsert por usuario+producto | controller: `exists()` → update | CONFIRMED |
| EV-007 | Ruta sin middleware auth | `routes/frontend_shop.php:12` (grupo shop sin auth) → guest 500 | CONFIRMED (inferencia: deref de null) |
| EV-008 | avg por request, N+1 | `shop/index.blade.php:162-163`, `shop/details.blade.php:43-44` | CONFIRMED |
| EV-009 | Comentario escapado | `{{ convertUtf8($review->comment) }}` (blade) | CONFIRMED |
| EV-010 | Nombre completo público | `shop/details.blade.php:181-183` (`fname . lname`) | CONFIRMED |
| EV-011 | DB local 0 filas; AUTO_INCREMENT=9 (histórico) | query mysql | CONFIRMED |
| EV-012 | Tienda inactiva en producción | `/tienda` → 302; sitemap sin /tienda | CONFIRMED |
| EV-013 | Sin migración de product_reviews en repo | grep migraciones | CONFIRMED (legacy/instalador) |
| EV-014 | Sin tests de ProductReview ni gates CI | tests/ vacío de reviews; workflows hardcode-audit + organizer-theme | CONFIRMED |
| EV-015 | is_shop_rating gate de UI | `basic_settings.is_shop_rating` (default 0) + `shop/index.blade.php:166` | CONFIRMED |
| EV-016 | Producción: cantidad de reviews | Sin acceso SQL a prod → NOT VERIFIED | NOT VERIFIED |

## Capas normativas (sin mezclar)

- **Capa A (Schema.org):** Product.aggregateRating/review válidos semánticamente.
- **Capa B (Google):** Product soportado para review snippets; self-serving NO se extrapola a Product (pero genuinidad exigible).
- **Capa C (TukiPass):** más estricta → provenance + pipeline reales requeridos.
- **Capa D (realidad):** dataset vacío/dormante; sistema sin controles → domina la decisión.
