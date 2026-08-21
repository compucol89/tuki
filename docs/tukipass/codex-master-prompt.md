# MASTER PROMPT — Remediación forense frontend público TukiPass

Versión: 2.1 · Corte: 21 de agosto de 2026
Modo: obligatorio. Copiar este bloque completo. No improvisar stack ni hipótesis.

> v2.1 incorpora las verificaciones del corpus `/docs/reference/` (2026-08-21):
> niveles WCAG corregidos, 5 excepciones de 2.5.8, patrón `.h1`–`.h6` de Bootstrap,
> aserción Axe `violations == []`, `maxDiffPixels`, cache sin tags, `whereHas` citable.

## 1. MISIÓN

Sos auditor-remediador forense del frontend público de TukiPass (Laravel SaaS de
eventos/entradas, español Argentina).

Objetivo: que toda afirmación, dato, estado, interacción y decisión visual
publicada pueda responder:

  ¿DE DÓNDE SALIÓ?
  ¿POR QUÉ ESTÁ ASÍ?
  ¿ES CORRECTO?
  ¿CÓMO LO DEMOSTRAMOS?
  ¿QUÉ TEST EVITA QUE VUELVA A ROMPERSE?

NO es "que se vea mejor". NO asignar scores numéricos sin fórmula reproducible.

Quality gate ejecutivo actual:

  CONTENT INTEGRITY     FAIL
  DATA CONSISTENCY      FAIL
  PUBLIC TRUST          FAIL
  SEO/SEMANTICS         PARTIAL / NEEDS FULL AUDIT
  ACCESSIBILITY         NOT YET FULLY VERIFIED
  RESPONSIVE            NOT YET FULLY VERIFIED
  VISUAL REGRESSION     NOT IMPLEMENTED
  E2E REGRESSION        NOT IMPLEMENTED
  BACKEND TESTING       EXISTS (PHPUnit 11)
  PERFORMANCE           NOT MEASURED IN THIS AUDIT
  PAYMENT UX            NOT INTRUSIVELY TESTED
  OVERALL               NOT READY FOR FORENSIC CLOSURE

## 2. STACK CONGELADO

  BACKEND        Laravel 12 (^12.0) · PHP ^8.2
  BUILD          Laravel Mix 6 + Webpack · .version() activo · NO Vite
  FRONT PÚBLICO  Bootstrap 4.5.3 vendorizado (public/assets/front/)
                 jQuery 3.6.0 + duplicado jquery.min.js · popper.min.js
  ADMIN/ORG      Bootstrap 4.3.1 (public/assets/admin/)
  PHPUnit        11
  Playwright/Axe NO instalado (autorizado GATE 6 como devDependency)

PROHIBIDO como referencia de implementación: Bootstrap 5.x · BS5 Color Modes ·
Vite · APIs BS modernas. Dark mode = CSS real del runtime (custom properties,
specificity, computed styles light/dark), NUNCA color-modes de BS5.
No inferir Bootstrap desde package.json. No actualizar deps productivas.
Migración de framework = otro proyecto.

## 3. ORDEN DE LECTURA

1. docs/reference/README.md
2. docs/reference/google-search/README.md
3. docs/tukipass/standards.md → accessibility-policy → seo-policy →
   content-integrity-policy → production-data-policy → frontend-quality-gates
4. Este prompt (hechos F-001…F-013) — prevalece sobre v1.0/v2.0
5. Referencia puntual según hallazgo (sección 7)

NUNCA editar docs/reference/. Aplicación solo en docs/tukipass/ y código.
Cadena: documentación oficial → política TukiPass → implementación → test.

## 4. MODELO DE EVIDENCIA

Estado: CONFIRMED | STRONG EVIDENCE | HYPOTHESIS | NOT TESTED | RESOLVED | ACCEPTED RISK
Confianza: HIGH | MEDIUM | LOW
Severidad: P0 Critical | P1 High | P2 Medium | P3 Low

PROHIBIDO promover hipótesis a hecho. Declarar sin prueba está prohibido para:
"dark mode cumple", "responsive funciona", "SEO está bien", "WCAG AA",
"performance excelente", "0 eventos es bug", "esa cuenta es test",
"los testimonios son falsos".

Niveles WCAG 2.2 verificados contra el corpus (how-to-meet-wcag-2.2.md,
WCAG-2.2.md y understanding/):
- 2.5.8 Target Size (Minimum) = **AA 24×24 CSS px** · 2.5.5 (Enhanced) = **AAA 44px**
- 2.4.7 Focus Visible = **nivel A** (promovido en 2019; no citar como AA)
- 2.4.11 Focus Not Obscured (Min) = **AA** · 2.4.13 Focus Appearance = **AAA**
- 1.4.3 = AA 4.5:1 (grande 3:1) · 1.4.6 = AAA 7:1 · 1.4.11 = AA 3:1
- 1.4.10 Reflow = AA 320 CSS px vertical / 256 horizontal
- 2.5.8 excepciones: exactamente **5** (Spacing, Equivalent, Inline,
  User Agent Control, Essential). No existe excepción "conformance".
- 3.3.8 Accessible Authentication (Min) = **AA**: no exigir test cognitivo sin
  alternativa; copy/paste es un *ejemplo* de mechanism, no el requisito.
- 1.3.5 = AA · 3.3.7 = A · 1.4.12 = AA (line-height 1.5, párrafos 2×, letter
  0.12em, word 0.16em) · 4.1.3 = AA · 1.4.1 = A

Cada corrección termina solo si:
  CAUSA RAÍZ + CODE FIX + TEST + REPRO PASS + NO REGRESSION + DOC

## 5. ZONAS PROHIBIDAS

NO MUTATE PAYMENT STATE. No tocar:
  FrontEnd\CheckOutController@checkout2 · FrontEnd\Event\BookingController
  FrontEnd\PaymentGateway\* · config/auth.php
  name="event_id" pricing_type quantity quantity[] date_type event_date
  data-price data-stock data-ticket_id data-purchase data-p_qty
  #total_price #total recalcTotal() · .quantity-up .quantity-down .quantity-down_variation
Checkout: auditoría pasiva permitida. Prohibido pagos reales/webhooks/mutaciones.
Copy visible: español Argentina. Nunca AltokeTicket, example.com, lorem, inglés de UI.
Fiscal: TAYRONA GROUP SAS — CUIT 30-71885087-4.
Guards: admin / organizer / customer (no User.php).

## 6. DECISIONES DE NEGOCIO (cerradas — no reabrir)

F-004 Blog: limpiar los 6 posts demo de BD (backup + comando reversible) Y
  quitar el filtro $demoBlogSlugs de la vista (blogs + details).
F-002 Métricas: PublicBusinessMetricsService con queries reales + cache 3600.
  Prohibido reemplazar 486.000+ por otro hardcode.
F-005 Organizadores: relajar listable() — QUITAR requisito de evento pasado
  (Organizer.php:87-89). Mantener email verificado + foto + portada + redes +
  info completa. Documentar definición en production-data-policy.md.
GATE 6: autorizado @playwright/test + @axe-core/playwright (devDeps).
  Baselines visuales SOLO después de GATE 1–5.
TDD: RED antes de cada fix. Un commit por problema.
PHPUnit = reglas backend. Playwright = browser. No sustituir.

## 7. HALLAZGOS — HECHOS DE CÓDIGO (archivo:línea)

--- F-001 P0 CONFIRMED HIGH ---
  resources/lang/es.json:17 about_metrics_note → about.blade.php:192
  es.json:9 about_metrics_chart_title → :117 · :21 → :138 · :22 → :165
  DUPLICADO: lang/es.json (mismas keys, ~:485+). Commit: 53c20bf9
  HomeController@about pasa config('about_metrics') (~:305)
Acción: eliminar patrones de copy final. Catálogo de test: referencia,
  reemplazá, placeholder, dummy, orientativ, ilustrativ, TODO, FIXME, lorem
  (no banear "demo" en URLs técnicas). Deduplicar es.json vs lang/es.json.
Test: tests/Feature/PublicContentIntegrityTest.php
Cierre: GET /sobre-nosotros y crawl público sin patrones; PHPUnit PASS.
Refs: docs/tukipass/content-integrity-policy.md

--- F-002 P0 CONFIRMED (publicación) / UNKNOWN (veracidad) ---
  config/about_metrics.php:7 "Valores placeholder…" · :28 3.200+ · :32 486.000+
  :36 1.050+ · :40 78 → about.blade.php:112,177 · "escala real" es.json:11 → :104
Acción: PublicBusinessMetricsService
  METRIC events_published_live: events.status=1, end_date_time >= now(),
    NOT IN DemoEventExclusion::EVENT_IDS, language de EventContent,
    TZ America/Argentina/Buenos_Aires
  METRIC tickets_sold_last_12_months: definición explícita (tablas/status;
    EXCLUDE cancelled, refunded, test, sandbox, duplicate)
  METRIC organizers_active: MISMA definición que listable() relajado (F-005)
  METRIC weekend_events_avg: definición explícita + test
  Cache: Cache::remember(key, 3600, closure). NO usar Cache::tags (driver
    default = database, no soporta tags — cache.md:40,411-412). Invalidación
    con Cache::forget en boot de modelos (Event/Organizer/Booking).
  SVG hero: derivar de datos o ELIMINAR (nunca "orientativo").
  Quitar about_metrics_note y disclaimers internos de AMBOS json.
  Si inventario real = 0 → mostrar 0 y cambiar "escala real". No inflar.
Test: tests/Feature/PublicMetricsTest.php (cifra visible == query; usar
  expectsDatabaseQueryCount para probar el cache — database-testing.md:286-295)
Refs: laravel/12.x/eloquent.md (scopes #[Scope] :1521-1567), queries.md
  (selectRaw+groupBy :1317), cache.md (:224-232), database-testing.md

--- F-003 P0/P1 STRONG ---
  CORRECCIÓN: "+500 opiniones verificadas" NO está en el repo (solo en la
  política interna). Runtime: TestimonialSection.review_text (DB) →
  about.blade.php:335, home-trust-sections.blade.php:68.
  Tabla testimonials: modelo app/Models/HomePage/Testimonial.php (sin
  published/verified). Query HomeController.php:307-310. NO hay migración
  de la tabla en el repo (dump externo).
Acción:
  Migración que documente el schema real + columnas published, verified,
    verified_at, verified_by (y source/consent/original_text si aplica).
  Default false. NO auto-publicar filas existentes.
  Query: solo published && verified.
  NUNCA JSON-LD Review / AggregateRating sin dataset:
    - Google prohíbe reseñas self-serving (28-resena.md:602-605): si la
      entidad controla sus reseñas → inelegible para estrellas. TukiPass
      alojando reseñas sobre sus propios organizadores queda fuera.
    - AggregateRating requiere ratingCount|reviewCount + ratingValue con
      punto decimal + ítems específicos (no categorías) (:597-600).
    Refs: schema-org/Review.md, schema-org/AggregateRating.md,
      03-datos-estructurados/28-resena.md
Cierre: cifra demostrada o eliminada; cada card con provenance o retirada.

--- F-004 P1 CONFIRMED + CAUSA RAÍZ ---
  Causa: journal/blogs.blade.php:23-32 ($demoBlogSlugs + Str::startsWith →
  $visibleBlogs; empty :119-124) + duplicado blog-details.blade.php:23-27.
  Contadores sin filtro: BlogController.php:48 ($allBlogs), :105-107
  (getCategories: sin language_id ni status). Commit 57fefb38.
  Schema: blogs solo image, serial_number. NO published/published_at/
  softDeletes. NO inventar tests contra columnas inexistentes.
Acción: quitar filtro Blade (blogs + details); comando reversible que borre
  los 6 posts demo (backup JSON); getCategories() misma política que listado
  (language_id + join a blogs activos).
Tests: BlogPublishingTest + BlogCategoryCountsTest.
Contrato: visible_total == results_count bajo la misma política. Nunca
  "Todos N>0" + empty state simultáneos sin filtro visible.
Refs: laravel/12.x/eloquent.md, queries.md, pagination.md, database-testing.md

--- F-005 P1 CONFIRMED + CAUSA RAÍZ ---
  Organizer::scopeListable Organizer.php:63-90; uso OrganizerController.php:70.
  Exigía :87-89 whereHas events status=1 AND end_date < now() (evento pasado).
  Commit a2a1e533. Bug: OrganizerController.php:54-58 orWhere ubicación sin
  agrupar (AND > OR) → envolver en where(function(){ orWhere… }).
Acción: quitar :87-89; mantener email verificado + foto + portada + redes +
  organizer_info (name != username, details >= 80, ubicación). Definición en
  production-data-policy.md. ProductionDataIntegrityTest debe excluir
  usernames aleatorios / test@ / name==username.
Tests: PublicOrganizerScopeTest + ProductionDataIntegrityTest.
Refs: laravel/12.x/eloquent.md (scopes), eloquent-relationships.md (whereHas)

--- F-006 P1/P2 CONFIRMED COMO DATO ---
  EventController.php:156-158 (status=1, notIn DemoEventExclusion, end>=now).
  Eventos vencidos (último 2026-08-01); evento 123 fechas corruptas
  (start futuro/end pasado).
Acción: comando reversible corrija fechas 123; empty-state CRO en
  event.blade.php:340 (publicar evento, novedades, contacto, redes) SIN
  fabricar inventario. Test: EventPublishingTest (ventana de venta).
F-012: home/index-v1.blade.php:72-76 CTA → mismo empty. No fake events.

--- F-007 P2/P1 a11y CONFIRMED ---
  organizer/index.blade.php SIN h1: :29 h2 → h1; :89 h6 contador → p (+clase
  si conserva tamaño); :121 h5 → re-nivelar; :140 h4 → p.
  home-trust-sections.blade.php:68 h6 review_text → p; :84 h5 → p/clase.
  about.blade.php:335 h6 → p (mismo patrón).
Regla: heading = jerarquía (H42/H101 son técnicas suficientes de 1.3.1);
  apariencia = clases de Bootstrap `.h1`–`.h6` (patrón oficial:
  typography.md:205 "match the font styling of a heading but cannot use the
  associated HTML element", ejemplo `<p class="h6">`) y `.display-*`.
  En el panel (BS 4.3) la preferencia de label visible es política propia
  (la doc 4.3 no la expresa; sí 4.5/input-group.md:614).
Refs: wcag/understanding/1-3-1, 2-4-6, bootstrap/4.5/typography.md
ARIA snapshots en GATE 6.

--- F-008 P2 CONFIRMED CON MATICES ---
  "Productores"/"Tuki" aislado: 0 en repo (prod histórico puede ser CMS viejo).
  Mezcla Tukipass (~65) vs TukiPass: layout.blade.php:20,128; home/index-v1
  .blade.php:22,71; footer.blade.php:60; event-details.blade.php:1198.
Taxonomía oficial: MARCA TukiPass · ROL Organizador · ÁREA Panel del
  organizador · CTA Publicar evento · LANDING Para organizadores.
Unificar vistas públicas. CI grep de legacy.

--- F-009 P2 REDIRIGIDO ---
  1139451837 NO está en código; es basic_settings.contact_numbers (DB).
  contact.blade.php:729 (explode), :783-784 (tel: crudo).
  footer.blade.php:6 $phones calculado y NUNCA usado (código muerto).
  contact-info.blade.php:11 huérfano (no incluido por ninguna vista).
Acción: helper display "+54 11 3945-1837" · tel:+541139451837 · wa.me E.164.
  Footer: renderizar $phones O borrar la línea.

--- F-010 P1/P2 CONFIRMED ---
  es.json:38 "comisión más baja del mercado + pagos más veloces" → :233
  es.json:26 "entre las más bajas" → :244 · :29 "plazos más cortos" → :249
  :11 "escala real" → :104 · Disclaimer :32 → :262. "plataforma líder": NO existe.
Sin benchmark: NO publicar superlativo. Bajar :38 a fact-only o tono :26 +
  disclaimer. Refs: content-integrity-policy.md

--- F-011 P1 STRONG ---
  Claims estáticos vs 0 eventos/0 listables. Se resuelve con F-002 (números
  reales o bloque off) + copy sin "escala real" si el inventario corriente
  es 0. historic total ≠ current inventory: definir en el servicio.

--- F-012 P1 CRO CONFIRMED ---
  home/index-v1.blade.php:72-76 CTA route('events') → cartelera vacía.
  Resolver con empty-state de F-006. Stats NO están en home.

--- F-013 NOT TESTED (prod) ---
  Código local OK: contact.blade.php:829-854 (4 labels for/id; hidden solo
  @csrf; recaptcha condicional). El input extra de prod NO está en este Blade.
  Antes de clasificar: snapshot DOM prod. NO "arreglar" sin evidencia de prod.

## 8. PROTOCOLO TDD

Por cada ítem de GATE 1–5:
  1. Test que describe el contrato. 2. Correr → FALLAR por el bug.
  3. Fix mínimo en los archivos citados. 4. PASS + suite sin regresiones.
  5. Commit: fix(<área>): <hallazgo> — cierra RED <TestClass>
No tests de columnas inexistentes. No Playwright para scopes. No PHPUnit
para heading order/teclado (GATE 6).

## 9. GATES (orden fijo)

GATE 0 — PRESERVAR (hecho 2026-08-21: tag, rama, corpus ampliado a 72
  archivos, master prompt v2.1, políticas, informe forense, baseline tests).
GATE 1 — P0: PublicContentIntegrityTest RED → PublicBusinessMetricsService +
  PublicMetricsTest → HomeController@about real → limpiar/dedup es.json +
  lang/es.json → SVG datos o fuera → migración testimonials + filtro
  published&&verified → superlativos F-010 → "escala real" F-011.
GATE 2 — DATOS: quitar filtro demo (blogs+details) → comando purge demo
  (backup JSON) → getCategories() misma política → BlogPublishingTest +
  BlogCategoryCountsTest → listable() sin :87-89 → fix orWhere → 
  PublicOrganizerScopeTest + ProductionDataIntegrityTest → comando evento 123
  → EventPublishingTest → empty-state event.blade.php:340.
GATE 3 — SEMÁNTICA: headings organizer/index + home-trust-sections + about:335
  con `.h*` para apariencia. 2.5.8 = 24px AA (no 44px).
GATE 4 — UX: unificar TukiPass (vistas públicas) → teléfono helper + footer
  $phones → F-013 solo si snapshot prod lo confirma.
GATE 5 — SEO: PublicSeoMetadataTest (title, canonical autocanónico, h1 único)
  → Organization existente layout.blade.php:144 → nunca Review/AggregateRating
  → blog JSON-LD coincide con posts visibles.
GATE 6 — PLAYWRIGHT: npm i -D @playwright/test @axe-core/playwright →
  npx playwright install chromium → playwright.config.js → scripts
  test:e2e/a11y/aria/visual/frontend → Axe light+dark páginas clave con
  aserción `violations == []` (excepciones SOLO vía disableRules documentado
  como waiver; NO hay filtro por impacto en @axe-core/playwright) →
  ARIA snapshots (toMatchAriaSnapshot) → visual con toHaveScreenshot +
  maxDiffPixels, baselines mismos entorno (OS/hardware/headless) y
  `-u/--update-snapshots` para actualizar → documentar AXE PASS ≠ WCAG PASS.
GATE 7 — PERF + CIERRE: lab LCP/CLS/INP/TTFB/FCP (home, eventos,
  sobre-nosotros) sin declarar CWV por Lighthouse local → cadena Mix
  (webpack.mix.js → mix-manifest.json → mix() en Blade) →
  docs/auditorias/frontend-publico-forensic-final.md (tablas ID/severidad/
  causa/fix/test/estado + baseline/post-fix) → sin score numérico.

## 10. SUPERFICIE PÚBLICA MÍNIMA

  / /eventos /blog /contacto /sobre-nosotros /organizadores
  /preguntas-frecuentes + Términos/Privacidad/Cookies/Reembolsos/Eliminación/
  Defensa al consumidor + login/registro cliente y organizador + lo que
  aparezca en route:list/sitemap/robots. No afirmar 100% cobertura.

## 11. MATRIZ WCAG 2.2 (objetivo; no declarar PASS sin evidencia)

Niveles verificados: 1.1.1 A · 1.3.1 A · 1.3.2 A · 1.3.5 AA · 1.4.1 A ·
1.4.3 AA · 1.4.4 AA · 1.4.10 AA · 1.4.11 AA · 1.4.12 AA · 1.4.13 AA ·
2.1.1 A · 2.1.2 A · 2.4.1 A · 2.4.2 A · 2.4.3 A · 2.4.4 A · 2.4.6 AA ·
2.4.7 A · 2.4.11 AA · 2.5.3 A · 2.5.8 AA · 3.1.1 A · 3.2.x A/AA ·
3.3.1 A · 3.3.2 A · 3.3.3 AA · 3.3.7 A · 3.3.8 AA · 4.1.2 A · 4.1.3 AA

Dark: tokens computados en light Y dark. Responsive: viewports 320→1920 +
  zoom 200% (documentar en GATE 6–7).

## 12. DEFINITION OF DONE

  0 P0 abiertos · 0 P1 sin owner/plan/waiver · PHPUnit PASS (suite completa)
  Playwright E2E PASS · Axe violations==[] con waivers documentados
  ARIA snapshots PASS · Visual PASS (baselines intencionales)
  Console 0 errores inesperados · Network 0 CSS/JS fail/mixed/404
  Content integrity PASS · Production-data check PASS · SEO sin defectos
  críticos · WCAG manual documentado · Responsive matriz documentada
  Informe final escrito.

VEREDICTO ACTUAL: REMEDIATION REQUIRED · FORENSIC CLOSURE NO
MAIN BLOCKERS: 1 Content integrity (F-001) · 2 Claim provenance (F-002,
F-010, F-011) · 3 Testimonial provenance (F-003) · 4 Blog count≠list
(F-004) · 5 Organizer listable (F-005) · 6 Missing browser suite (GATE 6)
