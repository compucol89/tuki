# 07 · Faceted Navigation / Parámetros — TukiPass

**Superficie:** `/eventos` (catálogo) + `/blog`.

## Parámetros descubiertos (crawler + código)

| Parámetro | Ejemplos probados | Status | Canonical | Decisión |
|---|---|---|---|---|
| `pricing` | `free`, `paid` | 200 | `/eventos` (limpio) | ✅ canonical (correcto) |
| `category` | `conciertos-y-musica`, `fiestas`, `teatro-y-shows`, `deportes`, `conferencias` (slugs) | 200 | `/eventos` | ✅ canonical |
| `category` | `1` (id inexistente) | **302 → /eventos** | — | ⚠️ validar: id inválido → 404/redirect controlado |
| `dates` | `a`, `to`, `%20a%20`, `YYYY-MM-DD a YYYY-MM-DD`, rangos futuros | 200 | `/eventos` | ✅ canonical (colapsado) |
| `search` | `colombia`, vacío | 200 | `/eventos` | ✅ canonical |
| `page` | `2` | 200 | `/eventos?page=2` (self) | ✅ paginación self-canonical (Google 08-ecommerce-paginacion); con filtros → canonical base |
| `sort` | `oldest` | 200 | `/eventos` | ✅ canonical |
| `event` | vacío | 200 | `/eventos` | 🟡 parámetro fantasma (¿de dónde sale?) |

## Riesgo de espacio de URLs

- `pricing × category × dates × search × sort × page` → combinatoria alta, pero **canonicaliza todo a `/eventos`** → riesgo contenido pero controlado.
- `dates` acepta fechas arbitrarias (probé 2026-01-01..2026-12-31, futuras, formatos `a`/`to`) → **espacio infinito potencial**; mitigado por canonical, pero Google puede descubrir variantes vía enlaces generados en UI (daterangepicker genera `to` y `a`).
- **Hallazgo P2-14**: existen al menos 3 formatos de `dates` (`a`, `to`, `%20a%20`) generados desde la UI → normalizar a UNO solo en enlaces internos (evita variantes).

## Recomendaciones (por tipo, no solución universal)

- Filtros con resultados → mantener canonical a `/eventos` (ya OK).
- **Nota corpus (`08-ecommerce-paginacion.md:49`):** para URLs de filtro de alta combinatoria Google prefiere `noindex` (o evitar rastreo) sobre canonical a base — canonical es aceptable pero más débil; evaluar noindex si el espacio de facetas crece.
- Combinaciones **sin resultados** → evaluar 404 (según guías de faceted nav) o canonical a `/eventos`.
- `category` con slug inválido/inexistente → 404 (no 302 genérico).
- Normalizar formato de `dates` y usar `?key=value` único; nunca repetir parámetros.
- `page=2` → self-canonical en la página 2 (verificar implementación actual: hoy canonicaliza a `/eventos`).
- `event=` vacío → eliminar del generador de enlaces.
