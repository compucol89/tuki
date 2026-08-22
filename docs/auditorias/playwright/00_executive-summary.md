# 00 · Executive Summary — Auditoría Forense Playwright (TukiPass)

**Commit auditado:** `e8bfcc65` (rama `remediation/audit-2026-08-21`) · **Fecha:** 2026-08-21
**Entorno:** localhost:8801 (app local con DB dev) · Playwright 1.62.1 · @axe-core/playwright ^4.13.0 · Chromium Desktop Chrome · viewport 1440×900 · fullyParallel · retries 0 · maxDiffPixels 2000

## VEREDICTO

```
PLAYWRIGHT TRUST VERDICT: B — STRONG WITH LIMITED GAPS
```

**Respondiendo la pregunta central:** *¿Qué podría estar roto en producción y aun así dejar Playwright verde?*
Con evidencia: (a) el color del icono activo del sidebar podría volver a `#1572E8` y CI seguiría verde (test vacuo, FP-004); (b) un segundo H1 visible pasaría el gate @aria (matching parcial, FP-003), aunque @e2e lo atraparía **si estuviera verde** (hoy está rojo por entorno); (c) cambios visuales ≤2000px (~0.06% de la página) escapan al gate visual; (d) cualquier regresión en e2e/a11y/aria/visual/seo/legal **no tiene gate de CI** (solo @theme corre en CI).

## Top 5 strengths

1. **Sensibilidad real verificada por mutación**: console.error, FAQPage, segundo H1 (via e2e), color visible y token de tema **sí** rompen sus gates (M2, M3, M6, e2e-H1).
2. **@theme cubre 12 rutas del panel × light/dark** con assertions computadas (no solo screenshot): 16/16 verde.
3. **@seo y @legal**: gates aditivos, deterministas y sensibles (FAQPage deprecated, MerchantReturnPolicy con datos reales).
4. **Diagnósticos de fallo**: trace + screenshot + artifact en CI; mensajes con expected/actual claros.
5. **Disciplina**: retries=0, fullyParallel, tags segmentados, config documentada (tolerancia visual justificada).

## Top 10 riesgos

| # | Riesgo | Sev |
|---|---|---|
| 1 | **6 de 7 suites NO tienen gate CI** (solo @theme): e2e, a11y, aria, visual, seo, legal no protegen merges | P0 |
| 2 | ~~FP-003~~ **invalidado**: @aria usa `toHaveCount(1)` (detecta H1 duplicados) — la mutación fue un no-op | — |
| 3 | ~~FP-004~~ **invalidado como vacuo**, pero la aserción era débil (anti-valor sobre 1 elemento): se fortaleció con presencia + icono raíz (corregido en la remediación) | — |
| 4 | **FF-001**: CSP host-mismatch (assets en 127.0.0.1 vs localhost) → @e2e home y @visual home/sobre-nosotros **rojos por entorno** (false-fails) | P1 |
| 5 | **FF-002**: correr `npx playwright test` desde `tests/playwright/` no carga la config → **todos los tests fallan** (inversión frágil) | P1 |
| 6 | **maxDiffPixels 2000**: diffs ≤2000px pasan sin aviso (~0.06% de la página fullPage) | P2 |
| 7 | Baselines visuales **solo darwin**; CI (Ubuntu) no podría ejecutar @visual aunque se agregara | P2 |
| 8 | @a11y dashboard se **salta sin credenciales**; solo corre si se setean E2E_ORGANIZER_* | P2 |
| 9 | Axe acepta naming por `placeholder` → quitar un `<label>` no viola (semántica WCAG < axe) | P2 |
| 10 | `fullyParallel` + DB compartida del stack: sin colisiones demostradas, pero sin aislamiento garantizado entre corridas | P3 |

## Estado de suites (baseline local, evidencia)

| Suite | Tests | Resultado local | En CI |
|---|---|---|---|
| @seo | 4 | ✅ 4/4 | ❌ no |
| @legal | 8 | ✅ 8/8 | ❌ no |
| @aria | 18 | ✅ 18/18 (pero "único h1" parcial) | ❌ no |
| @e2e | 19 | ❌ 18/19 (home: CSP env) | ❌ no |
| @a11y | 16 | ✅ 14/14 + 2 skip (dashboard sin creds) | ❌ no |
| @visual | 4 | ❌ 2/4 (home/sobre-nosotros: CSP env) | ❌ no |
| @theme | 16 | ✅ 16/16 (con creds) — 1 sub-test vacuo | ✅ **única** |

## Mutaciones controladas (todas revertidas)

| Mutación | Gate esperado | Resultado | Verdict |
|---|---|---|---|
| Segundo H1 visible | @aria "único h1" | ✅ PASS (no detecta) | FP-003 |
| Segundo H1 visible | @e2e home (count) | ❌ FAIL (detecta) | defensa real |
| console.error | @e2e home | ❌ FAIL | sensible |
| FAQPage JSON-LD | @seo | ❌ FAIL | sensible |
| Sin label (con placeholder) | @a11y login | ✅ PASS (axe semántica) | FP-005 (P2) |
| Color visible (rojo h1) | @visual home | ❌ FAIL | sensible |
| #1572E8 en icono activo | @theme sidebar | ✅ PASS (elemento inexistente) | **FP-004 P0** |

## Conclusión

Playwright es **fuerte pero con gaps limitados**: las suites son sensibles a las regresiones que declaran (mutaciones lo demuestran), pero **no son un release gate completo** porque (1) solo @theme entra en CI, (2) dos tests nominales no prueban lo que su nombre dice (FP-003/FP-004), (3) el entorno (CSP/host) mantiene e2e/visual en rojo sin relación con el producto, y (4) la invocación desde `tests/playwright/` rompe todo (FF-002).

**Antes de llamarlo release gate:** correr las 6 suites en CI, arreglar el host/CSP del entorno de test, hacer el test del sidebar real (o eliminarlo), endurecer @aria (matching exacto del h1), y documentar la política de actualización de snapshots.
