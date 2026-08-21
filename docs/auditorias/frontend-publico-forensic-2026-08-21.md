# Auditoría forense frontend público — TukiPass

> Fecha de corte: 2026-08-21 · Rama: `remediation/audit-2026-08-21` · Tag: `audit-2026-08-21`
> Documento GATE 0 — evidencia preservada antes de remediar.

## 0. Baseline preservado

- `git tag audit-2026-08-21` creado sobre `master` (commit previo a la remediación).
- Rama de trabajo: `remediation/audit-2026-08-21`.
- **`php artisan test` (baseline, Docker): 4 failed / 155 passed (466 assertions)**.
  Fallos preexistentes (no causados por esta remediación):
  1. `tests/Unit/Rules/ImageMimeTypeRuleTest > accepts real images` — LogicException (entorno Docker/GD).
  2. `tests/Feature/AiIndexFilesTest > llms txt exposes machine readable site map` — asserts sobre archivos generados.
  3. `tests/Feature/AiIndexFilesTest > llms full txt exposes public url inventory` — ídem.
  4. `tests/Feature/AiIndexFilesTest > image sitemap is exposed for google search console` — `robots.txt` no es respuesta streamed.
  → Se evalúa su corrección en GATE 7; si quedan, se registran como WAIVER con evidencia.
- Entorno: Docker (`tuki-app-1`, `tuki-db-1`), APP_URL local: `http://localhost:8801`.

## 1. Stack congelado (verificado)

| Capa | Versión | Evidencia |
|------|---------|-----------|
| Laravel | 12.x | composer.json |
| PHP | ^8.2 | composer.json |
| Laravel Mix | 6.x + `.version()` | webpack.mix.js, mix-manifest.json |
| Bootstrap frontend | 4.5.3 | public/assets/front/ |
| Bootstrap admin/organizer | 4.3.1 | public/assets/admin/ |
| jQuery | 3.6.0 + duplicado jquery.min.js | public/assets/front/js/ |
| PHPUnit | 11 (155+4 tests) | composer.json |
| Playwright/Axe | ausentes (GATE 6) | — |

## 2. Hallazgos — evidencia de código (tabla A)

| ID | Hallazgo | Sev | Estado | Evidencia (archivo:línea) | Causa raíz |
|----|----------|-----|--------|---------------------------|------------|
| F-001 | Copy interno publicado (`/sobre-nosotros`) | P0 | CONFIRMED | `resources/lang/es.json:9,17,21,22` (duplicado `lang/es.json:485-492`) → `about.blade.php:117,138,165,192`; commit `53c20bf9` | Placeholders renderizados como copy final |
| F-002 | Claims sin provenance (3.200+, 486.000+, 1.050+, 78) | P0 | CONFIRMED | `config/about_metrics.php:7,28,32,36,40` → `about.blade.php:112,177` | Valores estáticos declarados placeholder |
| F-003 | Testimonios sin cadena verified/published | P0/P1 | STRONG | `app/Models/HomePage/Testimonial.php:13-20` (sin published/verified); query `HomeController.php:307-310`; **sin migración de la tabla en repo** | Tabla creada por dump externo |
| F-004 | Blog contadores 6 ≠ 0 resultados | P1 | CONFIRMED + causa | `journal/blogs.blade.php:23-32` (filtro `$demoBlogSlugs`) + duplicado `blog-details.blade.php:23-27`; contadores sin filtro `BlogController.php:48,105-107`; commit `57fefb38` | Filtro anti-demo hardcodeado oculta los 6 posts seed |
| F-005 | Organizadores 6→0 | P1 | CONFIRMED + causa | `Organizer.php:63-90` (`listable()`, exige :87-89 evento publicado ya realizado); `OrganizerController.php:70`; commit `a2a1e533`; bug `orWhere` sin agrupar `OrganizerController.php:54-58` | Gate de completitud desplegado en agosto; ningún perfil real califica |
| F-006 | `/eventos` 0 eventos | P1/P2 | CONFIRMED como dato | `EventController.php:156-158` (`status=1`, `notIn DemoEventExclusion`, `end_date_time >= now()`); eventos vencidos (último 2026-08-01); evento 123 fechas corruptas | Inventario vigente = 0 (no bug de filtros) |
| F-007 | Headings: sin h1, saltos h2→h6→h5→h4 | P2/P1 | CONFIRMED | `organizer/index.blade.php:29,89,121,140`; `home-trust-sections.blade.php:68,84`; `about.blade.php:335` | Headings usados como estilo visual |
| F-008 | Marca `Tukipass` (65) vs `TukiPass` | P2 | CONFIRMED | `layout.blade.php:20,128`, `home/index-v1.blade.php:22,71`, `footer.blade.php:60`, etc. | Inconsistencia histórica de naming |
| F-009 | Teléfono sin normalización | P2 | REDIRIGIDO | Teléfono NO hardcodeado: `contact.blade.php:729,783-784` (de DB); `footer.blade.php:6` `$phones` calculado y no renderizado (código muerto) | Valor de DB renderizado crudo |
| F-010 | Superlativos sin benchmark | P1/P2 | CONFIRMED | `es.json:38,26,29,11` → `about.blade.php:233,244,249,104` | Copy comparativo sin provenance |
| F-011 | "Escala real" vs inventario vacío | P1 | STRONG | `es.json:11` → `about.blade.php:104` | Claims estáticos vs realidad de datos |
| F-012 | Home CTA → cartelera vacía | P1 CRO | CONFIRMED | `home/index-v1.blade.php:72-76` → `route('events')` | Inventario 0 + empty state sin salida |
| F-013 | Input extra en form contacto | TBD | NOT TESTED | `contact.blade.php:829-854` (4 labels OK, hidden solo @csrf) | No confirmado en código local; verificar prod |

## 3. Decisiones de negocio (cerradas)

1. Blog: limpiar los 6 posts demo de BD (backup + comando) **y** quitar el filtro `$demoBlogSlugs`.
2. Métricas: `PublicBusinessMetricsService` con queries reales + cache 3600; prohibido otro hardcode.
3. Organizadores: relajar `listable()` quitando el requisito de evento pasado; mantener email verificado + perfil completo.
4. GATE 6 autorizado: `@playwright/test` + `@axe-core/playwright` como devDependencies.

## 4. Corpus de documentación (actualizado en GATE 0)

- Agregado: `laravel/12.x/eloquent-relationships.md` (whereHas) y 11 Understanding WCAG (1.4.1, 1.4.12, 1.3.5, 2.1.2, 2.5.5, 2.4.13, 3.3.7, 3.3.8, 4.1.2, 4.1.3, + 4.1.3 vía understanding/21). Total: 72 archivos.
- Verificaciones de la doc que ajustan la remediación: `.h1`–`.h6` (patrón oficial BS4 para estilo sin semántica, typography.md:205), 2.4.7 = nivel A, 2.5.8 excepciones = 5, cache tags no soportado por driver default `database`, aserción Axe = `violations == []`, `maxDiffPixels`.

## 5. Próximos gates

GATE 1 (P0 contenido) → GATE 2 (datos) → GATE 3 (semántica) → GATE 4 (UX) → GATE 5 (SEO) → GATE 6 (Playwright) → GATE 7 (perf + informe final).
