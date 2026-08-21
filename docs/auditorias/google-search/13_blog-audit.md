# 13 · Blog Audit — TukiPass

## Estado real (producción, vía crawler + GSC)

| Dato | Valor |
|---|---|
| Posts en listado `/blog` | **0 visibles** ("No encontramos artículos para mostrar" + sin enlaces a posts en HTML) |
| Categorías (producción, vía UI) | Todos 6 · Business 2 · Conference 2 · Wedding 1 · Others 1 |
| Posts en DB local (dev) | 0 (`blogs` = 0, `blog_informations` = 0) |
| Posts indexados (GSC) | `/blog/morbi-in-sem-quis-dui-placerat-ornare…` (2 impresiones) — post **Lorem ipsum** |
| Estado del post GSC | **200 soft-404 + noindex** ("Página no encontrada") — el post ya no existe en producción |

## Hallazgos

| ID | Sev | Hallazgo |
|---|---|---|
| GS-P1-10 | 🟠 P1 | Post demo Lorem con impresiones en Google, ahora soft-404 (200 + noindex): deuda de indexación; debería ser 404/410 |
| GS-P2-18 | 🟡 P2 | **Listado vacío vs contadores de categorías** (6/2/2/1/1): desfase UI vs DB — los 6 posts (EN, genéricos) existen en producción pero no se listan (probable: language_id del contenido ≠ idioma activo, o status/fecha) |
| GS-P2-19 | 🟡 P2 | Contenido EN genérico ("Morbi in sem quis dui placerat ornare" = Lorem): decisión del usuario = **reemplazar por contenido nuevo en español** |

## Decisión del usuario (registrada)

- Los 6 posts existentes son EN y genéricos → **no se restauran**; se creará contenido nuevo en
  español, bien hecho, con fuentes (sub-fase editorial).
- Acciones sobre los 6: despublicar/noindex + fuera de sitemap + las URLs legacy → 410/404
  (para limpiar el índice de Google).

## Plan editorial (Fase 2B — pendiente de aprobación del plan)

1. Inventario de los 6 posts en producción (vía panel admin o API de blogs) → despublicar.
2. Pilares de contenido ES: guía del productor, eventos en Argentina, entradas/ticketing, preguntas del público.
3. Investigación con fuentes reales (sector/estadísticas con provenance — política de integridad).
4. 1 post piloto como plantilla (título único, contenido útil, BlogPosting JSON-LD, claims con provenance).
5. Gates: sin placeholder, sin Lorem, sin example.com, sin instrucciones editoriales internas.

## Gates de salida para blog

- BLOG CAN INDEX si: publicado ∧ contenido real (≥ umbral de utilidad, sin template) ∧ autor válido ∧ sin placeholders ∧ sin claims sin provenance.
- Test CI: `/blog` no debe listar posts vacíos/EN genéricos; sitemap sin posts demo.
