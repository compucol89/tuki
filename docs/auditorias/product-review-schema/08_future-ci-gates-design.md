# 08 · CI Gates Design (futuro — NO implementado)

Estado actual: workflows `hardcode-audit.yml`, `organizer-theme.yml`; `seo.spec.js` sin tests de rating. Sin gates de reviews.

## G1 · Anti-accidental-emission (diseño para HOY)

Mientras la feature NO esté aprobada, el CI debe **FALLAR** si aparece accidentalmente en el HTML público de páginas reales:

```text
FAIL si <script ld+json> contiene "@type":"AggregateRating"
FAIL si contiene "@type":"Review"
FAIL si contiene "ratingValue" / "reviewCount" / "ratingCount" (fuera de fixtures autorizados)
```

Scope: páginas de producto reales (no fixtures/tests). Esto protege contra alguien agregando markup sin auditoría.

## G2 · Dataset gates (diseño para la implementación futura)

```text
FAIL si aggregateRating sin dataset (0 reviews elegibles)
FAIL si ratingValue sin count
FAIL si rating fuera de escala 1-5
FAIL si locale decimal incorrecto (4,7 en vez de 4.7)
FAIL si ratingCount != count(dataset elegible)
FAIL si reviewCount != count(reviews con body visible)
FAIL si el aggregate incluye reviews rechazadas/eliminadas/test
FAIL si schema presente con 0 reviews
FAIL si rating visible (UI) != rating schema
FAIL si count visible (UI) != count schema
FAIL si Review individual no visible en página (content parity)
FAIL si author inexistente
FAIL si JSON-LD inválido
```

## G3 · Tests de dataset (diseño conceptual)

| Test | Fixture | Expected rows | Expected avg | Expected count |
|---|---|---|---|---|
| approved scope | 3 aprobadas + 1 rechazada | 3 | (a+b+c)/3 | 3 |
| deleted scope | 2 activas + 1 soft-deleted | 2 | (a+b)/2 | 2 |
| zero reviews | producto sin reviews | 0 | null → NO EMITIR | 0 |
| one review | 1 review 5★ | 1 | 5.0 | 1 |
| decimal avg | 4+5+5 | 3 | 4.67 (política de redondeo única) | 3 |
| invalid rating | 9 insertado directo | excluir/marcar | — | — |
| duplicate user+product | 2 filas mismo par | 1 (upsert) | — | 1 |

## Source of truth único (arquitectura futura)

UI rating + UI count + JSON-LD rating + JSON-LD count deben derivar del **mismo servicio/query/dataset lógico**. NO refactorizar ahora; solo contrato de diseño.

## Rollback / Regression contract

- Cambio futuro **aditivo**: `+ aggregateRating` (y opcional `+ review[]`), prohibido alterar Product.name/description/image/url/offers/BreadcrumbList/Organization/WebSite/Event/canonical/meta/robots/sitemap.
- Feature flag recomendado: `PRODUCT_REVIEW_SCHEMA_ENABLED=false` (despliegue controlado).
- Piloto futuro (G4): pocos productos representativos, snapshot baseline, flag, tests, Rich Results Test con fixtures locales, DOM parity, Search Console monitoring, rollback documentado.
