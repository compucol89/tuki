# 02 · Current Structured Data Baseline (READ-ONLY snapshot)

**SEO FREEZE: este documento describe el estado, no lo modifica.**

## Product JSON-LD actual (`shop/details.blade.php:296-330`)

```json
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "...",
  "description": "...",
  "image": "...",
  "url": "...",
  "offers": {
    "@type": "Offer",
    "priceCurrency": "ARS",
    "price": "...",
    "availability": "https://schema.org/InStock|OutOfStock",
    "url": "..."
  }
}
```

- `offers` solo si `current_price` es numérico (sin precio → sin Offer).
- **`aggregateRating`: AUSENTE (CONFIRMED).**
- **`review`: AUSENTE (CONFIRMED).**

## Otras páginas (baseline)

| Página | JSON-LD |
|---|---|
| Home | Organization + WebSite |
| Evento | Event + BreadcrumbList (solo no-pasados; auditoría SEO) |
| Organizador | Organization + WebSite |
| FAQ | BreadcrumbList (FAQPage retirado) |
| Producto | Product + Offer + BreadcrumbList |

## Paridad UI (cuando la tienda esté activa)

- Estrellas: `width: {{ $avarage_rating * 20 }}%` (escala 1-5 asumida) — `round(avg, 2)`.
- Conteo visible: tab "Review (n)" → `count($reviews)`.
- Lista: autor (fname+lname+foto), fecha (d-m-Y), comentario escapado.

**Golden contract futuro:** cualquier implementación de rating debe ser **aditiva** y limitada a `+ aggregateRating` (y, si se aprueba por separado, `+ review[]`), sin tocar name/description/image/url/offers/BreadcrumbList/canonical/meta/robots/sitemap.
