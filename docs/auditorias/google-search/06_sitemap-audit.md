# 06 · Sitemap Audit — TukiPass

**Fuentes:** `sitemap.xml` (16 URLs) + `sitemap-images.xml` (200, con imágenes).
**Código:** `SitemapController.php:36-227` · `ImageSitemapController.php` · rutas `web.php:14-15`.

## sitemap.xml — inventario (16 URLs)

Home, /eventos, /blog, /organizadores, 8 legales/estáticas, 3 organizadores
(27 tayrona, 29 rumba-colombiana, 37 olivia) · **CERO eventos** · **CERO posts de blog**.

## Hallazgos

| ID | Sev | Hallazgo | Evidencia |
|---|---|---|---|
| GS-P0-03 | 🔴 P0 | **0 eventos en sitemap**. Gate `SitemapController.php:67-71`: `status=1 AND end_date_time >= hoy AND slug∉demo AND id∉EVENT_IDS AND language=default`. Con 5 eventos pasados y 1 futuro con `end_date < start_date`, **ninguno pasa** → el contenido core no está en sitemap | crawler + DB |
| GS-P0-03b | 🔴 P0 | **Bug de datos**: evento 123 `end_date 2026-08-01 < start_date 2026-09-15` → se autoexcluye del sitemap y contradice todo schema | DB |
| GS-P1-08 | 🔴 P1 | **Organizadores en sitemap sin gate público**: 27, 29, 37 indexados; no existe campo public/private; los suspended 32/36 no están (por status?) pero 27/29/37 tampoco tienen validación de "perfil público" | `SitemapController:190-212` + DB |
| GS-P2-12 | 🟡 P2 | **`lastmod` idéntico** (2026-08-21T06:37:54) en todas las URLs → regeneración masiva, no refleja cambios reales (usar `updated_at` por entidad) | sitemap.xml |
| GS-P2-13 | 🟡 P2 | `changefreq`/`priority` presentes (obsoletos para Google, inofensivos) | sitemap.xml |

## Sitemap objetivo (por Google docs: IMPORTANT + CANONICAL + 200 + INDEXABLE)

- Eventos: `status=1 ∧ fechas válidas (end>=start) ∧ organizador público ∧ no demo ∧ slug válido` → **única URL canónica** `/{slug}/{id}`.
- Organizadores: solo PUBLIC (con gate formal).
- Blog: solo posts publicados con contenido real.
- **Nunca**: redirects, 404, noindex, privados, demo, acción, auth.

## Nota

- **No hay sitemap de eventos** → eventos dependen solo de enlaces internos + descubrimiento → con el fix del gate reaparece el sitemap de eventos.
- `sitemap-images.xml` correcto (200, imágenes por evento/blog/producto/organizador).
