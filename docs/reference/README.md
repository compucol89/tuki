# /docs/reference — Corpus de documentación oficial (SOURCE OF TRUTH)

Corpus de referencia para auditorías y remediación de TukiPass. Todos los archivos son
documentación **oficial sin modificar** (o espejos oficiales), en Markdown estable y legible
para agentes de IA. **No editar estos archivos.** La política de aplicación propia de TukiPass
vive en `/docs/tukipass/`.

- Capturado: **2026-08-21**
- Matriz de stack congelada (verificado en repo el 2026-08-21):

| Capa | Versión real | Evidencia |
|------|--------------|-----------|
| Laravel | 12.x | `composer.json` (`laravel/framework: ^12.0`) |
| PHP | ^8.2 | `composer.json` |
| Laravel Mix | 6.x + `.version()` | `webpack.mix.js`, `public/mix-manifest.json` |
| Bootstrap frontend | 4.5.3 | `public/assets/front/css/bootstrap.4.5.3.min.css` + `bootstrap.4.5.3.min.js` |
| Bootstrap admin/organizer | 4.3.1 | `public/assets/admin/css/bootstrap.min.css` + `bootstrap.min.js` |
| jQuery frontend | 3.6.0 (y duplicado `jquery.min.js`) | `public/assets/front/js/` |
| Popper | presente (`popper.min.js`) | `public/assets/front/js/` |
| Build | Laravel Mix (sin Vite) | `package.json` |
| Testing PHP | PHPUnit 11 | `composer.json`, `phpunit.xml` |
| Playwright / Axe | ausentes | — (Fase 4 planificada) |

## Índice

| Carpeta | Contenido | Problemas TukiPass que cubre |
|---------|-----------|------------------------------|
| `wcag/` | WCAG 2.2 normativa (tag oficial WCAG22-20231005), How to Meet (quickref), 11 páginas Understanding | Headings, contraste, dark mode, teclado, foco, targets, labels, reflow |
| `google-search/` | Mapeo a la captura completa de Google Search Central (155 docs, `docs/docs-google-search/`) | SEO, canonical, sitemap, robots, políticas de reseñas |
| `schema-org/` | Review, AggregateRating | Structured data solo con dataset real |
| `bootstrap/4.3/` | 11 páginas oficiales de Bootstrap 4.3 (admin/organizer) | Markup real del panel |
| `bootstrap/4.5/` | 11 páginas oficiales de Bootstrap 4.5 (frontend) | Markup real del frontend |
| `bootstrap/4.6/` | `REFERENCE-ONLY.md` (nota de diffs) | Referencia secundaria, nunca source of truth |
| `laravel/12.x/` | 10 docs oficiales (Eloquent, queries, validation, pagination, cache, database, database-testing, mail, blade, csrf) | Blog 6→0, organizadores 6→0, stats desde backend |
| `laravel-mix/6.x/` | 7 docs oficiales (what-is-mix, installation, api, javascript, css, sass, versioning) | Cache-busting de assets, CSS "que no se actualiza" |
| `playwright/` | 3 docs oficiales (accessibility-testing, test-snapshots, aria-snapshots) | Regresiones a11y/visuales/ARIA |

## Método de captura

- **HTML estático** (W3C spec/Understanding, Bootstrap, Schema.org): `curl` + `html2text`.
  W3C bloquea clientes no-navegador (403): se usó el **espejo oficial de GitHub** `w3c/wcag`
  (tag `WCAG22-20231005` para la spec; rama `main`, carpetas `understanding/{20,21,22}`).
- **Raw oficial en GitHub** (Laravel `laravel/docs` branch 12.x, Mix `laravel-mix/laravel-mix`, Playwright `microsoft/playwright`): descarga directa de `.md`.
- **JS-rendered** (quickref de W3C): webfetch.
- Descargas serializadas con pausas (1s), User-Agent de navegador, un solo hilo. Sin scraping agresivo.
- Cada archivo lleva header con URL fuente + fecha de captura. Cualquier archivo que no pase
  validación de contenido se marca `FAIL` en el log de captura y se reemplaza por fallback.

## Notas

- `laravel-mix/6.x/css.md`, `javascript.md`, `what-is-mix.md` son páginas oficialmente cortas (contenido íntegro).
- Mix no publica páginas `source-maps` ni `production` (404 en laravel-mix.com); el equivalente de producción es `docs/workflow.md` del repo laravel-mix (no capturado por innecesario).
- Ampliación 2026-08-21 (GATE 0): agregados `laravel/12.x/eloquent-relationships.md` (whereHas) y 11 páginas Understanding WCAG (1.4.1, 1.4.12, 1.3.5, 2.1.2, 2.5.5, 2.4.13, 3.3.7, 3.3.8, 4.1.2, 4.1.3 — estos últimos tres vía carpetas understanding/{20,21,22} del espejo w3c/wcag). Total: 72 archivos.
- Relaciones con otras carpetas de `/docs/`: `docs/laravel_12/` (capturas anteriores) y `docs/docs-google-search/` (captura completa de Google) son complementarias; este corpus es el índice canónico versionado.
