# 04 · Accesibilidad (Axe), ARIA, Visual, E2E, Theme, SEO/Legal — Detalle por suite

## @a11y (Axe)

- Config: `AxeBuilder` + `.exclude('.phpdebugbar')` + `.withTags(['wcag2a','wcag2aa'])` sobre 15 páginas públicas + dashboard organizer (light/dark).
- **Tags**: solo wcag2a/wcag2aa. La versión actual de axe soporta wcag21a/wcag21aa → cobertura WCAG 2.1 no ejercitada (P2).
- **Axe PASS ≠ WCAG PASS**: documentado en el propio spec ("GATE 7 manual"). **Estado del GATE 7**: solo existe como comentario en el spec + referencias en docs/auditorias; **no hay procedimiento versionado, evidencia ni responsable** → no es un gate implementado (P2).
- **Current-state problem**: el scan corre sobre el DOM inicial (300ms tras goto); estados interactivos (menús, modales, validación, mobile nav) **no se escanean** (P2).
- Resultado local: 14/14 + 2 skipped (dashboard requiere creds). Con creds desde la raíz: los 2 dashboard ejecutan (verificado en una corrida donde el resto falló por FF-002, no por axe).

## @aria

- 18 tests: "único h1" (15 páginas) + "jerarquía sin saltos" (4 páginas: home, contacto, organizadores, faqs).
- **FP-003**: matching parcial → nodos extra no fallan (segundo H1 visible pasa). El h1 count real vive en @e2e.
- Sin governance de actualización de snapshots (`--update-snapshots` sin política documentada; P3).

## @visual

- 4 screenshots: contacto/home/sobre-nosotros desktop fullPage + home mobile.
- **Baselines `-chromium-darwin`**; CI es Ubuntu → si @visual se agregara a CI, fallaría por entorno/fuentes (P2). En local, home y sobre-nosotros están **rojos por FF-001** (altura cambia por hero bloqueado por CSP).
- **maxDiffPixels 2000**: excepción global por un elemento dinámico del hero → cualquier diff ≤2000px en cualquier página pasa sin aviso (P2). Alternativas documentadas: mask, per-test tolerance, freeze time — no implementadas.
- Determinismo: sin fechas/carouseles visibles en las 4 páginas capturadas; el hero es la fuente conocida de variación.

## @e2e

- 15 smoke (status 200 + 1 h1 + consola vacía) + flujo home→eventos + mobile search + 2 tests de sobre-nosotros (ritmo vertical, reseñas/estado vacío).
- Gate de consola: array único de `console.error` + `pageerror` (no distingue recursos 4xx/5xx ni `requestfailed`; P2).
- Resultado local: 18/19 (home rojo por FF-001).

## @theme

- 16 tests: 1 detalle evento dark + 2 público dark + 12 organizer ×light/dark + 1 sidebar token.
- Mecánica: computed styles (getComputedStyle) sobre islas blancas/texto oscuro/colores — fuerte y sensible (M6 rojo h1 lo detecta).
- **FP-004 (P0)**: el test del sidebar es vacuo (elemento inexistente en la ruta).

## @seo / @legal

- 4 + 8 tests; deterministas y sensibles (M3 FAQPage detectado). @legal: TOC, H1/H2, axe, MerchantReturnPolicy con datos reales (refund vs no-refund desde la lógica real — verificado que distingue ambas ramas).
- Pertenecen a riesgo distinto (UI vs datos legales vs schema) pero viven en un archivo — aceptable; documentado.
