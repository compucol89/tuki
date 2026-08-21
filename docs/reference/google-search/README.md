# Google Search Central — mapeo de referencias

Google Search Central renderiza por JavaScript. En lugar de re-descargar páginas individuales,
este corpus reutiliza la **captura completa ya existente** en `docs/docs-google-search/`
(155 documentos, español, capturados con fecha y fuente por página).

> SOURCE OF TRUTH - TukiPass. No editar. Capturado: 2026-08-21.
> Prefijo de los archivos: `docs/docs-google-search/`

## Mapeo por problema TukiPass

| Problema | Documento de referencia (Google oficial) | Archivo |
|----------|------------------------------------------|---------|
| SEO general (titles, contenido, navegación, URLs, indexación) | [SEO Starter Guide](https://developers.google.com/search/docs/fundamentals/seo-starter-guide?hl=es) | `00-fundamentos-seo/04-guia-seo-principiantes.md` |
| SEO para desarrolladores (sitemap, enlaces rastreables, JS) | [Search guide for developers](https://developers.google.com/search/docs/fundamentals/get-started-developers?hl=es) | `00-fundamentos-seo/09-guia-desarrolladores.md` |
| Canonicalización (duplicados, parámetros, redirects, canonical) | [Consolidate duplicate URLs](https://developers.google.com/search/docs/crawling-indexing/consolidate-duplicate-urls?hl=es) | `01-rastrear-indexar/20-consolidate-duplicate-urls.md` |
| Canonicalización — conceptos | [Canonicalization](https://developers.google.com/search/docs/crawling-indexing/canonicalization?hl=es) | `01-rastrear-indexar/19-canonicalizacion.md` |
| Canonicalización — troubleshooting | [Troubleshooting](https://developers.google.com/search/docs/crawling-indexing/canonicalization-troubleshooting?hl=es) | `01-rastrear-indexar/21-canonicalization-troubleshooting.md` |
| Sitemap | [Build and submit a sitemap](https://developers.google.com/search/docs/crawling-indexing/sitemaps/build-sitemap?hl=es) | `01-rastrear-indexar/05-sitemaps-crear.md` + `06-sitemaps-indice.md` |
| Robots.txt | [Manage your robots.txt](https://developers.google.com/search/docs/crawling-indexing/robots/intro?hl=es) | `01-rastrear-indexar/18-robots-txt-especificacion.md` + `34-robots-meta-tag.md` |
| Reseñas / testimonios (política: prohibido self-serving) | [Review snippets policy](https://developers.google.com/search/docs/appearance/structured-data/reviews-snippet?hl=es) | `03-datos-estructurados/28-resena.md` |
| Structured data — guía general | [Structured data guidelines](https://developers.google.com/search/docs/appearance/structured-data/sd-policies?hl=es) | `03-datos-estructurados/00-indice.md` + `01-directrices-generales.md` |

## Regla

Todo trabajo de SEO/canonical/structured-data debe leer primero el archivo correspondiente de
esta tabla antes de proponer cambios. Las políticas de aplicación de TukiPass están en
`/docs/tukipass/seo-policy.md` y `/docs/tukipass/content-integrity-policy.md`.
