# 03 · ProductReview Architecture & Data Lineage

## Mapa del sistema

```
ROUTE    POST /shop/review-submit (product.review.submit)      routes/frontend_shop.php:12
  → CONTROLLER  ShopController@review                          ShopController.php:658
  → MODEL       ProductReview (fillable: user_id, product_id, review, comment)
  → TABLE       product_reviews (id, user_id int NULL, product_id int NULL,
                 review FLOAT NULL, comment LONGTEXT, created_at, updated_at)
                 — sin status, sin order_id, sin deleted_at, sin title, sin IP
  → RELATIONS   ninguna (customer/product consultados manualmente)
  → VIEWS       shop/details.blade.php (lista + form + estrellas)
                 shop/index.blade.php (avg por card)
  → JSON-LD     Product + Offer (sin rating)
  → CACHE       ninguna
  → TESTS       ninguno
```

## Data lineage de una review

```
CLIENTE (guard customer)
  → POST review-submit (CSRF)
  → ShopController@review
      → ¿review o comment? si no → flash error
      → ¿existe review user+product? → UPDATE (upsert) | sino INSERT
      → user_id FORZADO al cliente autenticado
      → product_id del REQUEST (sin validar existencia)
      → review/comment SIN validación server
      → AVG(review) recalculado (por request, sin filtro)
  → UI: estrellas + avg + count + lista (fname/lname/foto/fecha/comentario escapado)
```

## Quién puede crear / modificar

| Actor | Crear | Editar | Eliminar | Aprobar |
|---|---|---|---|---|
| Invitado | ❌ (500, no 401 limpio) | ❌ | ❌ | ❌ |
| Cliente autenticado | ✅ cualquier product_id | ✅ solo la propia (upsert) | ❌ (no hay UI/endpoint) | ❌ |
| Admin | ⚠️ no encontrada UI/endpoint (NOT VERIFIED si hay otro canal) | ⚠️ ídem | ⚠️ ídem | ❌ (no existe concepto) |

**Resumen:** el control es mínimo y centrado en el cliente; no existe moderación ni administración de reviews en el código encontrado.

## Provenance (escala de evidencia)

- Nivel **B** (cliente autenticado con interacción legítima posible) — sin vínculo transaccional.
- Nivel **E** para cualquier fila legacy/importada (NOT VERIFIED en prod).
- **No existe Nivel A** (compra verificada): no hay order_id ni relación transaccional.
