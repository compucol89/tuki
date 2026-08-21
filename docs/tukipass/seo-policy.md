# SEO Policy — TukiPass

> Versión: 1.0 · 2026-08-21 · Estado: vigente
> Referencias: `docs/reference/google-search/README.md` (mapeo a la captura oficial completa)

## Principios

1. **Enlaces `<a>` rastreables.** Cero enlaces de acción con `onclick`/`data-*` sin href
   real; los enlaces de contenido deben ser HTML.
2. **Una URL por contenido.** Cada página singular tiene una única URL. Nada de duplicados
   por parámetros de sesión, paginación sin canonical, o `?fbclid`-tipo.
3. **Canonical coherente.** `<link rel="canonical">` autocanónico en cada página indexable;
   una sola técnica por página (no mezclar canonical con sitemap que apunte a otra URL).
4. **Sitemap y robots.txt correctos** y actualizados; robots.txt no debe bloquear assets
   ni páginas indexables.
5. **Structured data solo con datos reales.** Jamás generar `AggregateRating`, `Review` ni
   testimonios para "rellenar". Ver `content-integrity-policy.md`.

## Checklist por página (implementar en tests)

| Ítem | Fuente Google |
|------|---------------|
| Title único y descriptivo | `00-fundamentos-seo/04-guia-seo-principiantes.md` |
| Heading único y jerárquico | ídem |
| URL canónica autocanónica | `01-rastrear-indexar/20-consolidate-duplicate-urls.md` |
| Meta description | `04-guia-seo-principiantes.md` |
| Enlaces internos con texto descriptivo | `00-fundamentos-seo/04-guia-seo-principiantes.md` |
| Datos estructurados (si aplica) con contenido visible equivalente | `03-datos-estructurados/01-directrices-generales.md` |

## Prohibiciones

- No indexar páginas de panel, checkout en estados internos, ni URLs con parámetros de
  sesión (robots meta / `X-Robots-Tag` según caso).
- No emitir reseñas/estrellas sin dataset verificable (ver content-integrity).
- No cambiar `robots.txt`, sitemap ni canonical sin test que lo verifique.

## Verificación

SEO checks como tests de regresión (no solo auditorías puntuales): cada página clave debe
tener test que falle si pierde title/canonical/heading único.
