# 14 · Organizer Indexation — TukiPass

## Estado actual (crawler producción + DB dev)

| Organizador | DB status | HTTP | robots | Nota |
|---|---|---|---|---|
| 27 tayrona | 1 | 200 | index,follow | ✅ público |
| 29 rumba-colombiana | 1 | 200 | index,follow | ✅ público (+ variante UPPERCASE también 200) |
| 37 olivia | 1 | 200 | index,follow | ✅ público |
| 32 Emigo | **0 (suspended)** | **200** | **index,follow** | 🔴 indexado estando inactivo |
| 36 minacionalnet | **0 (suspended)** | **200** | **index,follow** | 🔴 indexado estando inactivo |
| 38 dknfglsxzy | 0 | 404 | noindex | ✅ |
| 39/40 orgaudit, 41 audit-theme | 1 (fixtures) | 404 | noindex | ✅ (no públicos) |
| **1 "admin" + ?admin=true** | **no existe** | **200** | **index,follow** | 🔴 P0 fantasma |

## Hallazgos

| ID | Sev | Hallazgo | Evidencia |
|---|---|---|---|
| GS-P0-01 | 🔴 P0 | Perfil fantasma `/organizer/details/1/admin?admin=true` → 200 + index + meta desc dinámica ("...publicados por admin..."); organizador id=1 no existe; GSC 6 impresiones | crawler + DB + GSC |
| GS-P0-02 | 🔴 P0 | robots.txt permite `/organizer/details/` (rastreo de toda la superficie) | robots.txt |
| GS-P1-08 | 🔴 P1 | **No existe campo public/private** en `organizers`; la lógica "perfil no público" no está formalizada → suspended (32,36) indexables | DB SHOW COLUMNS |
| GS-P1-09 | 🔴 P1 | `?admin=true` queda en el **canonical** (OrganizerController:183-186) | código |
| GS-P1-07 | 🔴 P1 | Variante UPPERCASE `/29/Rumba-Colombiana` (canonical real lowercase `rumba-colombiana`); enlazada desde Event schema | crawler + schema |

## Estados formales propuestos

| Estado | HTTP | Index | Sitemap | Schema |
|---|---|---|---|---|
| PUBLIC (completo, activo, verificado) | 200 | index,follow | ✅ | ProfilePage |
| INCOMPLETE (falta nombre/bio/enlaces) | 200 | noindex (o index si valor) | ❌ | — |
| SUSPENDED (status=0) | 404 o 200+noindex | noindex | ❌ | — |
| DELETED | 404/410 | noindex | ❌ | — |
| TEST (fixtures) | 404 | noindex | ❌ | — |
| No existe (id inventado, ?admin=true) | **404/410 + noindex** | noindex | ❌ | — |

## Fix P0 (a implementar en Batch 1)

1. Ruta `/organizer/details/{id}/{slug}`: si `organizer` no existe → **404 + noindex** (hoy da 200 con template de perfil vacío).
2. Eliminar el parámetro `?admin=true` de: canonical, enlaces, controlador (o auth-only con noindex).
3. Slug canónico: `Str::slug()` (lowercase) en rutas + schema organizer.url.
4. Gate de visibilidad: suspended/private → 404 o 200+noindex (decisión: 404 para no-existentes; noindex para suspended reales).
5. GSC: tras deploy, pedir re-crawl vía inspección de URLs / removals para el perfil admin.
