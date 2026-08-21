# 11 · Structured Data Audit — TukiPass

## Inventario de JSON-LD por página (crawler, producción)

| Página | Tipos |
|---|---|
| Home `/` | Organization + WebSite (SearchAction → /eventos?search-input=) · **sameAs vacíos** (`facebook.com/`, `linkedin.com/`) |
| /eventos, /organizadores, legales, auth | Organization + WebSite |
| Evento `/…/{id}` | Organization + WebSite + **Event** + BreadcrumbList |
| /preguntas-frecuentes | + BreadcrumbList + FAQPage |
| /sobre-nosotros | + BreadcrumbList |
| /blog | + **Blog** (type "Blog"; posts ocultos → sin BlogPosting) |
| Organizador | Organization + WebSite (sin ProfilePage) |
| 404/410 | Organization + WebSite |

## Event schema — hallazgos (código `event-details.blade.php:874-935`)

| ID | Sev | Hallazgo | Evidencia |
|---|---|---|---|
| GS-P1-05 | 🔴 P1 | **`eventStatus` hardcodeado** `https://schema.org/EventScheduled` (línea 880) — también en eventos pasados/cancelados; sin lógica de estado | línea 880 |
| GS-P1-05b | 🔴 P1 | **`offers` omitido** cuando `$over` (evento pasado) → 6/6 eventos sin offers; sin `validFrom`; `availability` sin relación con stock real de tickets | líneas 902-917 |
| GS-P1-05c | 🔴 P1 | **`performer` nunca emitido** | ausente en código |
| GS-P1-06 | 🔴 P1 | **startDate del schema ≠ DB** (evento 123: schema 2026-07-25 vs DB 2026-09-15) | crawler vs DB |
| GS-P1-07 | 🔴 P1 | **organizer.url con mayúsculas** (`str_replace(' ','-', username)` línea 909-913) → variante UPPERCASE indexada en GSC | línea 909-913 + GSC |
| GS-P2-15 | 🟡 P2 | **sameAs vacíos** en home (`facebook.com/`, `linkedin.com/`) — URLs rotas/sin entidad | home JSON-LD |
| GS-P2-16 | 🟡 P2 | **Organizer sin ProfilePage**; Organization sin `legalName`/`address` (TAYRONA GROUP SAS) | organizador + Organization |
| GS-P2-17 | 🟡 P2 | **FAQPage en /preguntas-frecuentes** — feature de rich results FAQ **eliminada por Google (mayo 2026)**; conservar contenido, no optimizar | — |

## Reglas Google aplicables (corpus)

- Structured data debe reflejar **contenido visible** (no ocultar, no exagerar).
- Event online puro sin componente físico: **no elegible** para rich result Event; no falsear `Place`/`PostalAddress` (hoy `OfflineEventAttendanceMode` + Place solo para venue — correcto).
- `offers` requiere `price` + `priceCurrency` (+ `validFrom` recomendado); múltiples tickets → `Offer[]`.
- Reseñas: prohibido self-serving; sin `AggregateRating`/`Review` sin dataset real (política + `content-integrity-policy.md`).

## Fix objetivo

1. `eventStatus` = `EventScheduled` solo para eventos NO pasados; **eventos pasados → NO emitir Event JSON-LD** (Google no admite "EventEnded" entre los valores de `16-evento.md` y no aplica rich results a contenido obsoleto). CANCELLED/POSTPONED solo si existen flags reales.
2. `offers`: emitir siempre con datos reales de tickets (precio mínimo + currency + availability desde stock real + validFrom); múltiples precios → `Offer[]`.
3. `performer` desde organizador/artistas si existen datos reales (si no, omitir — no inventar).
4. `organizer.url` → `Str::slug()` lowercase (canónico).
5. startDate/endDate: UNA fuente de verdad (DB) para visible+schema (corregir data del evento 123).
6. Home: quitar sameAs vacíos. Organization: legalName + address reales (TAYRONA GROUP SAS, Av. Pueyrredón 1357 Local 63).
7. Organizador público: ProfilePage + Organization con datos visibles.
