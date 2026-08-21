# 04 · Dataset Integrity Audit

## DB local (`eventos.product_reviews`)

| Métrica | Valor |
|---|---|
| Total | **0** (AUTO_INCREMENT=9 → histórico de 8 filas previas, hoy vacía) |
| Ratings por fuera de escala | N/D (sin filas) |
| Duplicados | N/D |
| NULLs | N/D |
| Producción | **NOT VERIFIED** (sin acceso SQL; tienda inactiva) |

## Cálculo del average

```php
// shop/details.blade.php:43-44  y  shop/index.blade.php:162-163
$avarage_rating = ProductReview::where('product_id', $product->id)->avg('review');
$avarage_rating = round($avarage_rating, 2);
```

- **Incluye**: todas las filas del producto (no existe status/scope).
- **Excluye**: nada explícito (MySQL AVG ignora NULL; una review con rating inválido 0/9 SÍ cuenta).
- **Sin caché** → siempre fresco, pero N+1×2 por card en el catálogo.
- `round(..., 2)` → UI con 2 decimales; DB float (posible 4.666666…); un futuro JSON-LD debería usar la misma política reproducible.

## Escala de rating

- **UI**: 1–5 (estrellas `review * 20%`, picker review-1..5).
- **Backend**: sin validación → acepta cualquier float (0, 6, -5, 99).
- **DB**: FLOAT, sin CHECK.
- **Conclusión**: la escala 1–5 es una **asumción de frontend**, no una invariante del sistema. Un futuro `bestRating:5/worstRating:1` no estaría respaldado por una constraint real.

## Integridad temporal / binding

- Sin order_id → imposible verificar compra o "review anterior a la orden".
- Sin deleted_at → sin soft-delete; eliminar una review = DELETE (no hay endpoint → solo DB).
- Cliente eliminado → `$customer->photo` sobre null → **error de vista** (P2).
- Producto eliminado → filas huérfanas posibles (sin FK en la tabla).

## Veredicto de integridad

**NO DEFENDIBLE hoy**: el average es matemáticamente correcto pero agrega sobre un conjunto sin validación, sin moderación y sin verificación transaccional. No existe base para `ratingCount`/`reviewCount` semánticamente sólidos.
