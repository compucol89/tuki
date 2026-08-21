# 00 · Executive Summary — Auditoría Forense Google Search · TukiPass

**Fecha:** 2026-08-21 · **Dominio:** https://www.tukipass.com
**Fuentes:** corpus Google Search Central (154 docs), `docs/tukipass/seo-policy.md`, `content-integrity-policy.md`, robots.txt, sitemap.xml, crawler forense (74 URLs, dual UA Mozilla+Googlebot), GSC (exports 2026-08-21), DB local (dev).

## Verdicto por criterio

| Criterio | Estado |
|---|---|
| Crawl control (robots.txt) | **FAIL** — `/organizer/details/` permitido; perfil fantasma "admin" indexable |
| Sitemap (importante/canónico) | **FAIL** — 16 URLs, **0 eventos** (contenido core), lastmod fijo |
| Canonicalización | **WARNING** — filtros OK; `?admin=true` en canonical; variantes case duplicadas |
| Structured data | **FAIL** — Event sin `offers`/`performer`, `eventStatus` hardcodeado, fecha ≠ DB, organizer.url uppercase |
| Event lifecycle | **FAIL** — 3 fechas distintas en evento 123; pasados con `EventScheduled` |
| Content quality | **FAIL** — meta descriptions placeholder en 4 páginas core; post demo soft-404 |
| Organizer indexation | **FAIL** — sin gate public/private; suspended (status=0) indexables |
| Blog | **FAIL** — 6 posts EN ocultos del listado; categorías con conteos fantasma |
| GSC correlation | **WARNING** — sitio joven (76 clics marca); deuda demo confirmada con impresiones |
| Integridad (claims) | **WARNING** — claims sin provenance visibles (ver claim-register) |

## Hallazgos P0/P1 (evidencia runtime)

| ID | Sev | Hallazgo | Evidencia |
|---|---|---|---|
| GS-P0-01 | 🔴 P0 | **Perfil fantasma "admin" indexable**: `/organizer/details/1/admin?admin=true` → 200 + index,follow + meta desc dinámica; organizador id=1 no existe; **GSC: 6 impresiones** | crawler + DB + GSC Páginas |
| GS-P0-02 | 🔴 P0 | **robots.txt permite `/organizer/details/`** a todos los bots → habilita GS-P0-01 | robots.txt público |
| GS-P0-03 | 🔴 P0 | **Sitemap con 0 eventos** (16 URLs: estáticas + 3 organizers); gate `end_date_time >= now` autoexcluye todo; evento único futuro tiene `end_date < start_date` | `SitemapController.php:67-71` + DB |
| GS-P0-04 | 🔴 P0 | **Meta descriptions placeholder**: "Home Description", "Event  Description", "Organizer Description", "Blog Description" | crawler (home, /eventos, /organizadores, /blog) |
| GS-P1-05 | 🔴 P1 | **Event schema incompleto**: falta `offers` (omisión por `!$over`) y `performer`; `eventStatus` **hardcodeado** `EventScheduled` (también en eventos pasados); sin `validFrom` | `event-details.blade.php:880,902-917` + GSC Events report |
| GS-P1-06 | 🔴 P1 | **Fecha inconsistente evento 123**: JSON-LD startDate 2026-07-25 ≠ DB start 2026-09-15 ≠ end 2026-08-01 (end<start) → meta desc también dice julio | crawler JSON-LD + DB |
| GS-P1-07 | 🔴 P1 | **organizer.url en schema con mayúsculas**: `/organizer/details/29/Rumba-Colombiana` (uppercase) — duplicado case del canonico lowercase; ambos 200; enlazado desde schema | `event-details.blade.php:909-913` + curl |
| GS-P1-08 | 🔴 P1 | **Organizadores suspended indexables**: ids 32 (Emigo), 36 (minacionalnet) status=0 en DB → 200 + index,follow | DB + crawler |
| GS-P1-09 | 🟠 P1 | **`?admin=true` en canonical** del organizador (canonical self con query) | `OrganizerController.php:183-186` |
| GS-P1-10 | 🟠 P1 | **Demo blog soft-404**: `/blog/morbi-in-sem-...` → 200 "Página no encontrada" + noindex (deuda indexación Google: 2 impresiones) | crawler + GSC |

## Hallazgos P2

- **Acción indexable**: `/addto/wishlist/{id}` → 200 + canonical `/login` (debería ser POST/button). 
- **Auth pages**: `/login`, `/registro` ya tenían noindex (verificado producción); `/recuperar-contrasena` y las 4 forget/reset (customer+organizer) **corregidas a noindex** (GSC: 18 impresiones).
- **Case-duplicates** de organizadores (uppercase/lowercase).
- **`/example.com` → 404**: enlace placeholder vivo en alguna plantilla.
- **`sameAs` vacíos** en home JSON-LD (`facebook.com/`, `linkedin.com/`).
- **lastmod idéntico** en todo el sitemap (regeneración masiva).
- **`?category=1` → 302** (id inválido; los filtros reales usan slugs).
- **`?event=` y `?dates=` (varios formatos: `a`, `to`, `%20a%20`)** → 200 + canonical /eventos (colapsado OK, pero espacio de variantes).
- **Blog**: listado sin posts (6 EN ocultos); categorías con conteos fantasma; FAQPage en /preguntas-frecuentes (feature retirada — sin riesgo, pero no optimizar).
- **Eventos online**: sin Event schema cuando no hay location (correcto per Google, virtual no elegible).

## Correcto (mantener)

- Redirects de dominio 308 single-hop a https://www · 404 reales con noindex · 410 Gone · legacy `/event/{slug}/{id}` → 301 · filtros canonicalizan a `/eventos` · llms.txt/llms-full.txt · sitemap-images.xml 200 · /sobre-nosotros + /preguntas-frecuentes con BreadcrumbList/FAQPage.

## NOT VERIFIED

- Server logs de Googlebot (no disponibles) · URL Inspection estratificada (solo via GSC UI) · evidencia de indexación fuera de GSC.
