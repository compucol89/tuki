# 19 · Post-Remediation Regression — TukiPass Google Search

**Fecha:** 2026-08-21 · **Método:** re-ejecución del crawler forense (local `127.0.0.1:8010`, mismos seeds) + verificación de cada fix contra corpus Google.

## Validación vs corpus (resumen)

- 20/26 cambios conformes a reglas explícitas de Google Search Central.
- 6 desviaciones detectadas por auditor independiente → **corregidas** (abajo).
- Documentos auditados: `11` (EVENT_ENDED → omitir pasados), `07` (nota noindex facetas), `00` (auth status), `18` (FIX-04 actualizado).

## Correcciones aplicadas (post-validación)

| # | Desviación | Fix aplicado | Evidencia local |
|---|---|---|---|
| 1 | Canonical `page=2` perdía filtros | `event.blade.php` — con filtros → canonical base; solo page → self | `?category=X&page=2` → canon `/eventos`; `?page=2` → canon self |
| 2 | `offers` InStock en pasados + `validFrom=now` | offers solo si `!$over`, sin validFrom | evento pasado sin offers |
| 3 | `EventEnded` fuera de valores admitidos | eventos pasados → **sin Event JSON-LD** (página sigue indexable) | 122 → solo Org/WebSite/BreadcrumbList |
| 4 | Soft-404 blog por idioma | `abort(404)` si `details` null tras join | `/blog/morbi-…` → 404 |
| 5 | organizer.url con slug username | `EventController` pasa `organizerProfileSlug` (nombre público) | schema url = slug canónico |
| 6 | Sitemap organizers sin gate completo | `filter(isComplete)` en `SitemapController` | sitemap → solo perfiles completos |

## Before / After (evidencia runtime)

| Métrica | Before | After |
|---|---|---|
| Perfil fantasma `?admin=true` | 200 + index | **404** |
| Eventos en sitemap | 0 | **6** |
| Organizadores en sitemap | 3 (sin gate) | **1** (solo completo) |
| Meta descriptions placeholder | 4 | **0** |
| Event JSON-LD en eventos pasados | Sí (EventScheduled falso) | **No** (omitido) |
| offers en eventos pasados | InStock falso | **No** |
| `?category=` inválida | 302 | **404** |
| `?event=` vacío | 200 | **301** |
| Blog demo | 200 soft-404 | **404** |
| Case-duplicate organizador | 200 duplicado | **301** canónico |
| Auth forget/reset | index | **noindex** |
| Wishlist | GET indexable | **POST+CSRF** |
| sameAs | dominios vacíos | **null** |
| Canonical filtros+page | contenido distinto | **base /eventos** |

## Gates de salida (master §57)

- ✅ Sin placeholder público · ✅ sin demo crawlable/indexable · ✅ sin perfil admin crawlable · ✅ sin acción endpoint GET · ✅ sitemap solo URLs 200+indexable+canónica · ✅ sin canonical conflict · ✅ sin 5xx (regresión 10/10 críticas 200) · ✅ event lifecycle coherente (pasados sin rich result) · ✅ schema = contenido visible.

## Pendiente (documentado, no oculto)

- Deploy a producción + `migrate` + `cache:clear` (seos + home_seo_*).
- GSC post-deploy: re-enviar sitemap, inspección `?admin=true`/demo, retirada si persisten impresiones (FIX-16).
- Blog contenido ES (sub-fase editorial, FIX-15).
- `Offer[]` multi-ticket y `performer` (solo con datos reales) — refinamiento schema.
- Validación final con Rich Results Test tras deploy.
