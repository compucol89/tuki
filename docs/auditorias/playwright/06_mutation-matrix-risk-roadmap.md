# 06 · Matriz de Mutaciones, Risk Register y Remediation Roadmap

## Matriz de mutaciones (evidencia runtime, todas revertidas; re-ejecutadas desde la raíz)

| # | Regresión inyectada | Detector esperado | Resultado | Clasificación |
|---|---|---|---|---|
| M1 | Segundo H1 **oculto** (display:none) | @aria único h1 | PASS | No aplica (fuera del árbol a11y) — mutación mal diseñada |
| M1' | Segundo H1 **visible** | @aria único h1 | **PASS (no detecta)** | FP-003 (P1) |
| M1'' | Segundo H1 visible | @e2e home (count) | FAIL (detecta) | defensa real, pero gate rojo por FF-001 |
| M2 | `console.error` inyectado | @e2e home | FAIL (detecta) | sensible ✓ |
| M3 | FAQPage JSON-LD | @seo | FAIL (detecta) | sensible ✓ |
| M4/M4' | Label de login eliminado | @a11y | PASS (axe acepta placeholder) | FP-005 (P2, semántica axe) |
| M5 | `#1572E8` en icono activo (con !important) | @theme sidebar | **PASS (no detecta)** | **FP-004 (P0, vacuo: elemento inexistente)** |
| M6 | Color visible (h1 rojo) | @visual home | FAIL (detecta) | sensible ✓ |

**Conclusión empírica:** los gates son sensibles a su dominio (console, schema, color visible), pero **2 tests nominales no prueban lo que su nombre afirma** (FP-003, FP-004) y el entorno produce 2 false-fails (FF-001, FF-002).

## Risk Register

| ID | Riesgo | Sev | Confidence |
|---|---|---|---|
| ~~FP-004~~ invalidado como vacuo; aserción débil (cascada de blancos !important) → fortalecida con presencia + icono raíz | P2 corregido | |
| CI-6suites | 6/7 suites sin gate CI | P0 | CONFIRMED (workflows inspeccionados) |
| ~~FP-003~~ invalidado: @aria usa toHaveCount(1); mutación no-op | — | |
| FF-001 | CSP host-mismatch: e2e home + visual 2/4 rojos por entorno | P1 | CONFIRMED (console errors + diffs) |
| FF-002 | Inversión desde tests/playwright rompe los 87 tests | P1 | CONFIRMED (3/3 reproducción) |
| VISUAL-2000 | maxDiffPixels 2000 oculta diffs ≤2000px | P2 | ALTA (analítico) |
| VISUAL-ENV | Baselines darwin; CI ubuntu → @visual no portable | P2 | ALTA |
| A11Y-TAGS | Solo wcag2a/2aa; sin wcag21a/21aa | P2 | ALTA |
| A11Y-STATE | Axe solo DOM inicial; estados interactivos sin escanear | P2 | ALTA |
| GATE7 | "GATE 7 manual" solo en comentarios; sin procedimiento | P2 | ALTA |
| A11Y-SKIP | Dashboard a11y se salta sin creds | P2 | ALTA |
| MANIFEST | Listas de rutas duplicadas (drift potencial) | P2 | MEDIA |
| LABEL | Axe acepta placeholder como nombre (labels removibles) | P2 | ALTA (semántica) |
| PARALLEL/DB | fullyParallel + volúmenes Docker compartidos | P3 | MEDIA |
| HEALTHCHECK | 200 ≠ "entorno de test listo" formal | P3 | MEDIA |
| ARTIFACTS | Sin video/HTML report/network logs en fallo | P3 | MEDIA |

## Remediation Roadmap (no implementado — propuesta)

**P0 (inmediato)**
1. **FP-004**: o bien corregir la ruta/elemento del test del sidebar (asegurar que el icono activo exista: navegar a una ruta con submenú colapsable real, p. ej. `/organizer/event-management/events` con estado expandido) o **eliminar el test vacuo**; añadir assertion `expect(colors.active).toBeTruthy()`.
2. **CI**: ejecutar las 6 suites restantes en CI (workflow con `npm run test:seo` + `test:legal` + `test:aria` + `test:a11y` + `test:e2e` + `test:visual` — con baselines generados en CI o `--update-snapshots` controlado en una primera pasada Ubuntu).

**P1 (antes de confiar en el gate)**
3. **FF-001**: alinear host del entorno de test (usar `127.0.0.1:8801` en config o normalizar assets) para que e2e/visual vuelvan a verde por el motivo correcto.
4. **FP-003**: @aria — assertion explícita `toHaveCount(1)` de `h1` (locator) además del snapshot, o snapshot con `children: true` en el nodo de headings.
5. **FF-002**: documentar/forzar la invocación desde la raíz (script `test:all` que use `npx` desde root) o mover config a `tests/playwright/`.

**P2 (hardening)**
6. maxDiffPixels por-test/mask del hero · tags wcag21a/21aa · escaneo axe de estados interactivos clave · gate de actualización de snapshots con review · manifest único de rutas · formalizar GATE 7.

**P3 (madurez)**
7. Test data isolation (down -v en CI), healthcheck formal, video/HTML reporter, artefactos redactados.

## Diseño de gate futuro (derivado de evidencia)

```
STATIC (hardcode-audit)
 → SMOKE (e2e load) 
 → FUNCTIONAL-A11Y (axe + aria exacta)
 → THEME (computed contract, con sidebar real)
 → SEO/LEGAL (deterministas, ya sólidos)
 → VISUAL (baselines por-OS + mask + tolerancia por-test)
 → MANUAL WCAG/UX (GATE 7 con procedimiento versionado)
```
Cada capa con su propio job CI y artefactos; orden derivado de sensibilidad medida (mutation matrix) y coste.
