# 18 · Plan de Remediación Google Search — TukiPass

> Basado en evidencia runtime (crawler 74 URLs, GSC, DB, código). Cada fix: ID · causa raíz · archivo · estrategia · riesgo · test.
> **Implementación por batches aprobados** (P0 → P1 → P2).

---

## BATCH 1 — P0 (higiene del índice + core content) — ✅ IMPLEMENTADO Y VERIFICADO (2026-08-21)

### FIX-01 · Perfil fantasma "admin" → 404 + noindex (GS-P0-01) ✅
- **Causa raíz:** `OrganizerController::details` cargaba `Admin::first()` con `?admin=true` sin validar sesión; canonical conservaba el query (`OrganizerController.php:183-186`).
- **Implementado:** preview `?admin=true` → **solo con sesión de admin autenticada**; sin sesión → 404. `previewMode` → `noindex,follow` en la vista. Canonical **siempre sin query** (`publicOrganizerUrl` sin `admin=true`).
- **Archivos:** `app/Http/Controllers/FrontEnd/OrganizerController.php` · `resources/views/frontend/organizer/details.blade.php`.
- **Verificado local:** `/organizer/details/1/admin?admin=true` → **404** · `/29/rumba-colombiana?admin=true` (sin sesión) → **404** · `/29/rumba-colombiana` → 200 + canonical self + robots index.

### FIX-02 · Meta descriptions placeholder → dinámicas (GS-P0-04) ✅
- **Causa raíz:** tabla `seos` (language_id=8) con placeholders ("Home Description", etc.); home cachea `home_seo_{id}` 24h.
- **Implementado:** migración `2026_08_21_000001_update_seos_meta_descriptions.php` — 14 descripciones reales es-AR (sin claims inventados) + invalidación de `home_seo_*`.
- **Verificado local:** home, /eventos, /blog → descripciones reales. (Requiere `php artisan migrate` + `cache:clear` en deploy.)

### FIX-03 · Sitemap de eventos + gate de fechas (GS-P0-03) ✅
- **Causa raíz:** `SitemapController.php:69` exigía `end_date_time >= now` (excluía pasados) + evento 123 con fechas inconsistentes.
- **Implementado:** gate → `status=1 ∧ end_date_time >= start_date ∧ no demo ∧ language default`; `lastmod` = `updated_at` (ya usaba `formatLastmod`).
- **Verificado local:** sitemap **25 URLs** incluyendo los 6 eventos (118-123). Evento 123 incluido (end_date_time 2026-09-15 23:59:59 ≥ start) — consistente con la decisión de mantenerlo visible en home.

### FIX-04 · Event schema: eventStatus + offers + organizer.url lowercase (GS-P1-05/06/07) ✅ (con correcciones de validación vs corpus)
- **Causa raíz:** `event-details.blade.php:880` hardcodeaba `EventScheduled`; `offers` condicional a `!$over` y sin `validFrom`; `organizer.url` con `str_replace(' ','-',username)` (mayúsculas).
- **Implementado:**
  - **Eventos pasados → NO se emite Event JSON-LD** (Google no admite `EventEnded` en `16-evento.md`; no aplica rich results a contenido obsoleto). Los eventos futuros emiten `EventScheduled`.
  - `offers` **solo si `!$over`** (sin venta → sin Offer; evita `InStock` falso) + **sin `validFrom`** (no determinístico; solo aplica con fecha real de apertura de venta).
  - `organizer.url` → `\Illuminate\Support\Str::slug()` (lowercase).
- **Verificado local:** evento 122 (pasado) → **sin Event JSON-LD**; evento 123 (futuro) → Event con offers + EventScheduled + organizer.url lowercase.
- **Pendiente Batch 2+:** `Offer[]` multi-ticket, `performer` (solo con datos reales), fechas del evento 123 (excepción intencional, documentada).

### FIX-05 · Demo URLs → 404 real (GS-P1-10) ✅
- **Causa raíz:** `BlogController::details` hacía `->first()->blog_id` sobre null → soft-404 200; catch devolvía vista 404 con 200.
- **Implementado:** blog no encontrado → `abort(404)` (404 real + noindex en la vista de error).
- **Verificado local:** `/blog/morbi-in-sem-...` → **404**.

---

## BATCH 2 — P1 (organizadores, canonical, auth, blog) — ✅ IMPLEMENTADO Y VERIFICADO (2026-08-21)

### FIX-06 · Gate de visibilidad de organizadores (GS-P1-08) ✅
- **Implementado:** `details.blade.php` → `noindex,follow` cuando `organizer->status != 1` (suspended/inactivo). Perfiles públicos siguen `index`.
- **Verificado local:** 32 (Emilia Gomez) → noindex · 36 (minacionalnet) → noindex.

### FIX-07 · Case-duplicates organizadores (GS-P1-07) ✅
- **Implementado:** en `OrganizerController::details`, si el slug solicitado ≠ slug canónico (`Str::slug(publicOrganizerName)`) → **301** a la URL canónica (consolida uppercase/old slugs).
- **Verificado local:** `/29/Rumba-Colombiana` → 301 → `/29/rumba-colombiana` · `/32/emigo` → 301 → `/32/emilia-gomez` (slug canónico por nombre público).

### FIX-08 · Wishlist como acción (GS-P2-10) ✅
- **Implementado:** rutas `addto/wishlist/{id}` y `remove/wishlist/{id}` → **POST**; vistas (event-details + wishlist dashboard) → formularios POST con `@csrf`.
- **Verificado local:** GET `/addto/wishlist/123` → 404 (la ruta ya no es GET).

### FIX-09 · Auth pages noindex (GS-P2-11) ✅
- **Implementado:** `noindex,follow` en forget-password + reset-password (customer + organizer). *(login/signup ya lo tenían vía traducciones — verificado en producción.)*
- **Verificado local:** `/recuperar-contrasena`, `/reset-password`, `/organizador/olvide-contrasena`, `/organizador/restablecer-contrasena` → noindex.

### FIX-10 · Blog: gate de categorías + exclusión demo (GS-P2-18/19) ✅
- **Implementado:** `BlogController::blogs` — categoría inexistente → 404 (antes `->first()->id` sobre null = 500); listado excluye slugs demo (`DEMO_BLOG_SLUG_PREFIXES` hecho público en SitemapController).
- **Verificado local:** `/blog?category=no-existe` → 404 · `/blog?category=business` → 200.
- **Nota:** el desfase contadores/listado no existe en producción (los 6 posts EN están en otro idioma; el listado ES está vacío por diseño). La creación de contenido ES es la sub-fase editorial.

### FIX-11 · Facetas (GS-P2-14) ✅
- **Implementado:**
  - `EventController::index` — categoría inexistente → **404** (antes 302 genérico).
  - Query string sin filtros significativos (`?event=`, `?category=` vacíos, etc.) → **301** a `/eventos`.
  - `event.blade.php` — `page>1` → **canonical self** con `?page=N` (filtros siguen colapsando a `/eventos`).
  - Home chips "Hoy"/"Este finde" → formato de fechas unificado `' a '` (antes `' to '`).
- **Verificado local:** `?category=1` → 404 · `?category=no-existe` → 404 · `?event=` → 301 · `?page=2` → canonical self.

---

## BATCH 3 — P2 (contenido, integridad, entity, GSC) — ✅ PARCIALMENTE IMPLEMENTADO (2026-08-21)

### FIX-12 · sameAs vacíos + Organization real (GS-P2-15/16) ✅
- **Implementado:** `layout.blade.php` — filtro de `sameAs` descarta dominios desnudos (path vacío); Organization con `legalName` (TAYRONA GROUP SAS) + `address` real (config fiscal).
- **Verificado local:** home JSON-LD → legalName + address presentes, sameAs null (sin URLs rotas).

### FIX-13 · Claim register (content-integrity) ✅
- **Creado:** `docs/auditorias/google-search/claim-register.csv` — 6 claims (3.200+, 486.000+, 1.050+, 78/fin de semana, +500 opiniones, comisión más baja) → UNVERIFIED hasta provenance; REMOVE si no se demuestra.

### FIX-14 · FAQPage (feature retirada) ✅ documentado — mantener contenido, no optimizar rich result.

### FIX-15 · Blog nuevo en español (sub-fase editorial) ⏳ SEPARADA
- Plan en `13_blog-audit.md` (pilares, fuentes, gates). Requiere decisión editorial del usuario.

### FIX-16 · GSC post-deploy ⏳ PENDIENTE DE DEPLOY
- Tras deploy: re-enviar sitemap, inspección de URLs (admin phantom, demo, organizers), retirada de URLs si persisten impresiones.

---

## Puertas server-side (gates de publicación)

```text
EVENT CAN INDEX  si published ∧ organizer_public ∧ fechas válidas ∧ no demo ∧ título/desc válidos
BLOG CAN INDEX   si published ∧ contenido real ∧ autor válido ∧ sin placeholders ∧ claims con provenance
ORGANIZER CAN INDEX si public ∧ activo ∧ completo ∧ no test
```

## CI Google-Search gate (tests que hagan fallar CI ante P0)

- Sin los 4 placeholders de meta description en HTML público.
- Sitemap: todas las URLs 200 + indexable + canonical self + no noindex/redirect/privado/demo/acción.
- `?admin=true` no rastreable; perfil fantasma → 404.
- Event schema: eventStatus coherente con fecha; offers presente para eventos con tickets; organizer.url lowercase; fechas = DB.
- Sin Lorem/example.com/kreativdev en contenido público.
- Blog: sin posts demo/sin listado fantasma.

## Regresión

- Re-ejecutar el mismo crawler (antes/después/delta) + GSC tras 2-4 semanas.

---

## Archivos principales involucrados

`app/Http/Controllers/FrontEnd/OrganizerController.php` · `app/Http/Controllers/FrontEnd/SitemapController.php` · `resources/views/frontend/event/event-details.blade.php` · `resources/views/frontend/layout.blade.php` · `resources/views/frontend/organizer/details.blade.php` · `resources/views/frontend/journal/*` (blog) · `app/Support/DemoEventExclusion.php` · rutas frontend · settings DB (meta descriptions) · DB (evento 123, organizer states).
