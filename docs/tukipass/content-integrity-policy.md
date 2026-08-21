# Content Integrity Policy — TukiPass

> Versión: 1.0 · 2026-08-21 · Estado: vigente
> Referencias: `docs/reference/google-search/` (reviews-snippet → `03-datos-estructurados/28-resena.md`),
> `docs/reference/schema-org/Review.md`, `docs/reference/schema-org/AggregateRating.md`

## Regla central

> **NINGÚN CLAIM CUANTITATIVO O COMPARATIVO PUEDE PUBLICARSE SIN PROVENANCE.**

Un claim es cualquier número, comparación, superlativo o reseña visible al cliente
(`3.200+ eventos`, `486.000 entradas`, `+500 opiniones verificadas`, `"la comisión más
baja del mercado"`, etc.).

## Provenance obligatoria por claim

Ejemplo de bloque que debe existir (en el código o en una tabla de contenido aprobado):

```yaml
claim: "486.000+ entradas vendidas"
source: orders/tickets database
definition: tickets with status = paid/issued
period: rolling 12 months
updated: 2026-08-21
owner: Business / Product
calculation: query documentada
frontend: dynamic | approved snapshot
expiry: null
```

Para claims comparativos (`"la más baja del mercado"`):

```yaml
benchmark_source: <competidores comparados + links>
date: 2026-08-21
methodology: <criterio de comparación>
scope: <mercado/segmento>
approval: <responsable>
expiry: 2026-12-31
```

Si el claim no tiene provenance:

```text
BLOCK PRODUCTION
```

## Origen de las estadísticas

- **Regla general:** las estadísticas del sitio proceden de consultas/backend con definición
  documentada (owner + query), no hardcodeadas en Blade.
- **Excepción única:** snapshot comercial previamente aprobado y fechado, registrado con su
  `frontend: approved snapshot` + fecha de aprobación + expiración. Sin fecha, no.

## Testimonios

Pipeline obligatorio — sin él, `published = false`:

```text
TESTIMONIAL
    ↓ identidad verificable (nombre/avatar/rol real)
    ↓ consentimiento registrado
    ↓ texto y origen almacenados
    ↓ fecha
    ↓ moderación humana
    ↓ published = true
```

- Prohibido generar testimonios ficticios para "rellenar" secciones.
- `AggregateRating` / `Review` (Schema.org) solo se emiten cuando existe el dataset real de
  reseñas verificadas con la estructura exigida por Google (`28-resena.md`). El contenido
  visible debe coincidir con el marcado (no estrellas sin reseñas reales).
- Google prohíbe reseñas "self-serving" (la propia plataforma como fuente de sus reseñas
  cuando no reflejan valoración independiente): verificar caso por caso antes de emitir
  structured data de valoración.

## Verificación

Tests de regresión (PHPUnit + Playwright) que detecten claims sin provenance:
- Scan de Blade/resources por números con sufijos (`+`, `%`, miles) en contexto de marketing.
- Test por claim: la cifra visible == resultado de la query documentada (o snapshot aprobado).
