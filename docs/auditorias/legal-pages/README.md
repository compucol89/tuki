# Páginas Legales — Rediseño (modo lectura) + SEO

**Fecha:** 2026-08-21 · **Superficie:** 6 páginas legales (cookies, privacidad, términos, reembolsos, eliminación de datos, defensa al consumidor) · **Plantilla:** `resources/views/frontend/custom-page.blade.php`

## Diseño (modo Read — impeccable)

- **Banner compacto** (336px → 222px, `body.legal-page`) — menos scroll al contenido
- **Layout 2 columnas** desktop: artículo (medida 68ch) + **TOC sticky** ("En esta página") con anchors `#seccion-N`
- **TOC solo si ≥4 secciones**; en mobile pasa a static debajo del artículo
- **Fecha "Última actualización"** extraída del contenido real (15/04/2026)
- **Navegación "Documentos legales"** (6 páginas) con link actual marcado `aria-current`
- **Lead** reemplaza el H2 duplicado del H1 (sin heading duplicado)
- Dark mode con tokens frontend (`--surface-card`, `--card-foreground`); contraste TOC 10.45:1 dark / 12.63:1 light

## SEO (sin dañar — mejorando)

| Item | Antes | Después |
|------|-------|---------|
| JSON-LD | Organization + WebSite | + **WebPage** + **BreadcrumbList** por página |
| MerchantReturnPolicy | ausente | presente solo en reembolsos (AR, MerchantReturnUnspecified — sin inventar plazos) |
| H2 duplicado del H1 | sí | no (lead) |
| Enlazado interno | footer | + bloque legal-nav (6 links) |
| Title/canonical/OG/desc | OK | intactos |
| Sitemap | — | sin cambios (URLs ya indexables) |

## Tests

`npm run test:legal` — 8 tests: 6 páginas × (title, H1, canonical, meta, JSON-LD, TOC válido, nav legal, axe 0) + MerchantReturnPolicy presencia/ausencia.

## Evidencia

`screenshots/` — antes (cookies) + after light/dark.
