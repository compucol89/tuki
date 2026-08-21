# Informe final — Auditoría forense y remediación frontend público TukiPass

> Fecha: 2026-08-21 · Rama: `remediation/audit-2026-08-21` · Tag baseline: `audit-2026-08-21`
> Referencias: `/docs/reference/` (72 archivos oficiales) · `/docs/tukipass/` (6 políticas + master prompt v2.1)

## 0. Resumen ejecutivo

Se ejecutaron los GATES 0→7 del plan forense. Todos los hallazgos P0/P1 del audit fueron
corregidos con causa raíz identificada, test de regresión y verificación reproducible.

| Área | Baseline | Post-fix | Evidencia |
|------|----------|----------|-----------|
| Content integrity | FAIL | **PASS** | `PublicContentIntegrityTest` + verificación curl (0 patrones internos) |
| Data consistency | FAIL | **PASS** | Blog 6→0 consistente; eventos 1 vigente; organizadores honestos |
| Public trust | FAIL | **PASS** | Claims con provenance real (`PublicBusinessMetricsService`) |
| SEO/Semantics | PARTIAL | **PASS** | h1 único ×7 páginas, canonical autocanónico, marca unificada |
| Accessibility | NOT VERIFIED | **PASS (axe 0 violaciones)** | 6 páginas, WCAG 2.1 A/AA + fixes manuales de contraste/landmarks |
| Responsive | NOT VERIFIED | **PASS parcial** | Visual desktop+mobile estable; matriz completa en GATE 7 nota |
| Visual regression | NOT IMPLEMENTED | **IMPLEMENTADO** | Playwright `toHaveScreenshot` ×4 baselines estables (3 corridas) |
| E2E regression | NOT IMPLEMENTED | **IMPLEMENTADO** | 8 tests E2E (7 páginas + flujo) + 6 Axe + 4 ARIA |
| Backend testing | EXISTS (155) | **187/187 PASS** | +25 tests nuevos; 7 tests preexistentes corregidos |
| Performance | NOT MEASURED | **MEDIDO (lab)** | TTFB 96-101ms · LCP 212-256ms · CLS ≤0.003 |
| Payment UX | NOT TESTED | **PASS (pasiva)** | Sin mutación; semántica/labels verificados en checkout |

**Veredicto: REMEDIATION REQUIRED → COMPLETED (con waivers documentados).** No se asigna
score numérico (no existe fórmula reproducible).

## 1. Tabla de hallazgos (ID | Sev | Estado inicial | Causa raíz | Fix | Test | Estado final)

| ID | Sev | Hallazgo | Causa raíz (evidencia) | Fix | Test de regresión | Estado |
|----|-----|----------|------------------------|-----|-------------------|--------|
| F-001 | P0 | Copy interno en /sobre-nosotros ("reemplazá por datos auditados") | `resources/lang/es.json:17` (+duplicado `lang/es.json`), render `about.blade.php`; commit `53c20bf9` | Eliminadas 8 keys internas; sección de métricas reescrita sin SVG decorativos ni notas | `PublicContentIntegrityTest` (patrones + vistas) | **RESOLVED** |
| F-002 | P0 | Claims 3.200+/486.000+/1.050+/78 sin provenance | `config/about_metrics.php` (placeholder declarado `:7`) | `PublicBusinessMetricsService` con queries documentadas (events live, tickets 12m, organizers listable, weekend avg) + cache 3600 + invalidación en boot de Event/Organizer/Booking; `config/about_metrics.php` eliminado | `PublicBusinessMetricsServiceTest` (7 tests, sqlite) | **RESOLVED** |
| F-003 | P0/P1 | Testimonios sin published/verified; tabla sin migración | `Testimonial.php` sin columnas; tabla creada por dump externo | Migración con moderación (published/verified/verified_at/verified_by/source/consent/original_text); query solo `published && verified` | Migración + política en content-integrity-policy; fallback "Aún no hay reseñas" | **RESOLVED** (datos de prod a revisar por humano) |
| F-004 | P1 | Blog contadores 6 ≠ 0 resultados | Filtro anti-demo hardcodeado `blogs.blade.php:23-32` (+`blog-details:23-27`), commit `57fefb38`; posts demo aún en BD | Filtro eliminado; comando `blog:purge-demo` (backup JSON + restore) purgó los 6 posts; `getCategories()` ahora filtra por idioma | `BlogDemoPurgeTest` (3) + `BlogCategoryCountsTest` + `ProductionDataIntegrityTest` | **RESOLVED** |
| F-005 | P1 | Organizadores 6→0 | `listable()` exigía evento publicado ya realizado (`Organizer.php:87-89`, commit `a2a1e533`); bug `orWhere` sin agrupar `OrganizerController:54-58` | Requisito de evento pasado eliminado; exclusión de emails de prueba en el scope; `orWhere` agrupado | `PublicOrganizerScopeTest` (5) + `ProductionDataIntegrityTest` | **RESOLVED** |
| F-006 | P1/P2 | /eventos 0 eventos | Dato: eventos vencidos (último 2026-08-01); evento 123 fechas corruptas | Comando `events:fix-corrupted-dates` (corrigió el 123 → cartelera 1); política de ventana unificada en `EventPublicWindow` | `EventPublishingTest` (4) | **RESOLVED** (inventario = dato de negocio) |
| F-007 | P2/P1 | Headings: sin h1, saltos h2→h6→h5→h4 | Headings usados como estilo (`organizer/index:29,89,121,140`, `home-trust-sections:68,84`, `about:335`) | h1 en /organizadores y /contacto; patrones semánticos + clases `.h*` (patrón oficial BS4, typography.md:205); `main` anidados → `div` | `PublicSeoMetadataTest` + ARIA tests (Playwright) + Axe heading-order | **RESOLVED** |
| F-008 | P2 | Tukipass vs TukiPass | Inconsistencia histórica (48 ocurrencias en 14 archivos + DB) | Unificado a TukiPass en vistas + lang + DB (website_title, footer, event_contents.refund_policy) | `PublicSeoMetadataTest` + curl | **RESOLVED** |
| F-009 | P2 | Teléfono sin formato | Valor de DB renderizado crudo; footer calculaba `$phones` sin imprimir | `PhoneFormatter` (display E.164 + tel: + wa.me); footer ahora renderiza WhatsApp con wa.me | Verificación curl (`+54 11 3945-1837`, `wa.me/541139451837`) | **RESOLVED** |
| F-010 | P1/P2 | Superlativos sin benchmark | `es.json:38,26,29,11` | "más baja del mercado" → "comisión competitiva"; "escala real" → copy neutro; disclaimer mantenido | `PublicContentIntegrityTest` + inspección | **RESOLVED** |
| F-011 | P1 | "Escala real" vs estados vacíos | Claims estáticos vs inventario real | Métricas reales (0 eventos / 1.009 entradas / 1 organizador / 0 weekend) con copy neutral | `PublicMetricsServiceTest` + curl | **RESOLVED** |
| F-012 | P1 CRO | CTA home → cartelera vacía | Inventario 0 sin salida | Empty-state con CTAs (Publicar evento, Recibir novedades) | `EventPublishingTest` + visual | **RESOLVED** |
| F-013 | TBD | Input extra en /contacto | **No defecto**: es el formulario del popup de newsletter (`popup-email-20`, verificado en producción) | Sin cambio; form de contacto 4/4 labels OK | Snapshot DOM de producción | **RESOLVED (no defecto)** |

## 2. Correcciones adicionales descubiertas durante la remediación

1. **`empty("0")` filtraba métricas reales en la vista** (stats con valor 0 desaparecían) — corregido en `about.blade.php`.
2. **`$isDemo`/`$demoBlogSlugs`** eliminados de `blog-details` (variable indefinida tras el fix).
3. **ReferenciaError `$ is not defined` en /blog**: `blog.js` (admin) se cargaba sin `defer` antes de jQuery (defer) — corregido con `defer`.
4. **`role="link"` inválido en `article`** (event-card) — eliminado.
5. **`main > main` anidados** en 5 vistas — reemplazados por `div`.
6. **Contraste real (medido, 4.5:1)**: botón primario `#e05d38` (3.63:1) → brand `#c24b2b` (4.84:1) vía DB; loc-row de cards `#737c88`→`#5a6472`; labels contacto `--ctp-subtle`→`#5a6472`; chip activo blog → `#8f3718` (6.9:1) + borde 1.4.11; botón buscar eventos `--foreground`→`#fff`. Synchronized min. CSS.
7. **`rootUrl()` sin esquema** en fallback de llms.txt → `https://` garantizado.
8. **Tests rotos preexistentes** (robots streamedContent, GD sin webp, llms): corregidos (suite 155→187).
9. **Título de página "Productores"** (DB page_headings) → "Organizadores".
10. **`MÉTODO`/`TODOS` falsos positivos** del escáner de patrones → regex con límites de palabra + `/u`.

## 3. Evidencia de pruebas

- **PHPUnit (Docker):** `187 passed (181733 assertions)` — suite completa verde.
- **Playwright (host, Chromium 1.62):** `35 passed` — 8 E2E (7 páginas + flujo home→eventos), 6 Axe (WCAG 2.1 A/AA, `violations == []` con waiver debugbar), 4 ARIA (h1 único + jerarquía sin saltos), 4 visual (baselines estables ×3 corridas con `maxDiffPixels: 2000` documentado), 4 theme (preexistentes) + resto.
- **Axe por página:** home/eventos/blog/contacto/sobre-nosotros/organizadores = **0 violaciones reales** (solo Laravel Debugbar excluido, no existe en producción).
- **Web Vitals (lab, local Docker, 1440×900):**

| Página | TTFB | FCP | LCP | CLS |
|--------|------|-----|-----|-----|
| / | 98ms | 204ms | 212ms | 0.0014 |
| /eventos | 101ms | 224ms | 224ms | 0.0030 |
| /sobre-nosotros | 96ms | 180ms | 256ms | 0.0001 |

- **Cadena Mix:** `webpack.mix.js` (`.version()`) → `mix-manifest.json` (hashes) → `mix('css/app.css')` → navegador recibe `/css/app.css?id=50f410…` (hash coincide). Cache-busting operativo.
- **Producción (auditoría pasiva, 1 GET):** form de contacto = mismo markup que código (4/4 labels); el "input extra" es el popup de newsletter.

## 4. Waivers y pendientes (documentados)

| Ítem | Motivo | Dueño |
|------|--------|-------|
| Datos de producción (testimonials: publicar solo filas verificadas; page_headings "Organizadores"; primary_color c24b2b; marca TukiPass en DB; purga de posts demo; evento 123) | Cambios de datos realizados en entorno dev; **requieren despliegue a producción + revisión humana** | Business/Ops |
| WYSIWYG `about_us_sections.text` con `<h4>` (contenido DB) | Se normalizó en dev (`<h4>`→`<h3>`); revisar contenido restante en prod | Content |
| `exclude('.phpdebugbar')` en Axe | Herramienta de desarrollo (require-dev), no existe en producción | Dev |
| `maxDiffPixels: 2000` en visual | Banda y≈800-900 del home (elemento dinámico del hero, ~1.2k px verificados) | Dev |
| GD sin `imagewebp` en imagen Docker | Test webp condicional; regla de negocio intacta | Infra |
| Dark mode | Runtime no implementa `prefers-color-scheme` (0 ocurrencias en CSS front) → no aplica; si se implementa, auditar contraste con colores computados | Design |
| Field data (CrUX) | Solo lab medido; CrUX requiere tráfico real (Search Console) | Ops |
| `theme.spec.js` preexistente | No modificado; pasa | — |
| `/tienda` y rutas autenticadas | Fuera de la superficie mínima; cubrir en iteración siguiente | Dev |

## 5. Commits (rama `remediation/audit-2026-08-21`)

1. `docs: gate 0 — corpus (72 archivos), master prompt v2.1, políticas, informe forense`
2. `fix(content): gate 1 — métricas reales, integridad, testimonios, superlativos (F-001/002/003/010/011)`
3. `fix(data): gate 2 — blog, organizadores, ventana de venta, evento 123 (F-004/005/006/012)`
4. `fix(a11y): gate 3 — jerarquía de headings (F-007)`
5. `fix(ux): gate 4 — marca TukiPass, teléfono E.164, F-013 (F-008/009)`
6. `fix(seo): gate 5 — h1/canonical + PublicSeoMetadataTest`
7. `feat(testing): gate 6 — Playwright + Axe + ARIA + visual; fixes a11y reales`
8. `fix(seo): gate 7 — rootUrl https, tests corregidos, suite 187/187`

## 6. Cierre

- **0 P0 abiertos · 0 P1 sin owner/plan/waiver.**
- PHPUnit PASS (187) · Playwright E2E PASS · Axe `violations==[]` con waivers documentados ·
  ARIA PASS · Visual PASS · Console 0 errores inesperados · Content integrity PASS ·
  Production-data check PASS · SEO sin defectos críticos · WCAG manual documentado
  (matriz en accessibility-policy) · Responsive: matriz cubierta por visual desktop/mobile
  (320-1920 + zoom 200% pendiente de corrida completa en CI).
- Próximo paso recomendado: desplegar a producción + re-correr la suite contra el entorno
  real (Playwright con baseURL de prod) + revisión humana de testimonios y contenido WYSIWYG.
