# 03 · Assertion Quality y Análisis de False-Pass / False-Fail

## Register de vectores

| ID | Tipo | Descripción | Evidencia | Severidad |
|---|---|---|---|---|
| ~~FP-003~~ | **INVALIDADO** | La mutación M1' fue un **no-op** (sed no encontró `<body>` exacto; el layout usa `<body class=...>`). El test @aria usa `toHaveCount(1)` con locator (aria.spec.js:42) → **sí detectaría un segundo H1 visible**. Garantía real confirmada | Re-verificación: `<body>` exacto no existe (0 matches) | — |
| ~~FP-004~~ | **INVALIDADO** (elemento existe) pero **riesgo de diseño débil → corregido en remediación** | Con sesión válida el icono SÍ existe (probe: 11 icons, `li.active a i` presente). La cascada dark fija el icono a blanco con MÚLTIPLES reglas `!important` (probe CSSOM: el CSS base ya trae `#1572E8 !important` para `.nav.nav-primary > .nav-item.active a i` — regla Atlantis — y theme-dark la pisa). Mutar 2 de las reglas blancas no cambió el computed → la aserción anti-valor sobre 1 solo elemento era débil, no vacua. **Remediación:** aserción de PRESENCIA (`toBeTruthy`) + muestreo del icono raíz activo | M5 serie completa (3 variantes de mutación): computed siempre `rgb(255,255,255)` | P2 (diseño de aserción, ya corregido) |
| **FP-005** | FALSE-PASS (semántica axe) | Quitar el `<label for="username">` de login **no viola axe** (el `placeholder` da nombre accesible). El gate a11y no puede detectar eliminación de labels persistentes mientras existan placeholders | Mutación M4 y M4' (label eliminado): @a11y PASS | P2 |
| **FF-001** | FALSE-FAIL (entorno) | CSP `img-src 'self'` bloquea imágenes hero porque el home sirve assets con `http://127.0.0.1:8801` mientras el test navega `http://localhost:8801` → **@e2e home y @visual home/sobre-nosotros están ROJOS por mismatch de host, no por bug de producto** (en producción el host coincide) | 2 console errors CSP (127.0.0.1 vs localhost); visual: 10150/38384 px + altura cambiada | **P1** |
| **FF-002** | FALSE-FAIL (inversión) | `npx playwright test` desde `tests/playwright/` **no carga la config raíz** → baseURL ausente → los 87 tests fallan con "Cannot navigate to invalid URL". Determinista (3/3). Solo `npm run test:x` (raíz) funciona | Reproducción 3× desde `cd tests/playwright` | P1 |

## Assertion quality por suite

- **e2e**: assertions fuertes (status, count, console array `toEqual([])`) — excepto que el gate de consola mezcla `console.error` y `pageerror` en un solo array (no distingue network failures/4xx assets).
- **a11y**: `violations == []` con tags wcag2a+wcag2aa (no wcag21a/21aa — la versión de axe los soporta; gap de cobertura WCAG 2.1 P2). `incomplete`/`passes` no se registran.
- **aria**: snapshots parciales (FP-003) + sin `--update-snapshots` governance.
- **visual**: `toHaveScreenshot` fullPage con tolerancia global 2000px (P2 blind spot: ~0.06% de 1440×2394 ≈ 3.4M px).
- **theme**: assertions por computed style (getComputedStyle) — fuertes y sensibles (M5 no aplica porque el elemento no existe: FP-004).
- **seo/legal**: assertions sobre JSON-LD parseado + presencia/ausencia de tipos — sensibles (M3 FAQPage detectado ✓).

## Nota de defensa en profundidad

- Segundo H1: lo caza @e2e (locator), no @aria (snapshot) → **la garantía "único h1" depende de un test actualmente rojo por entorno** (FF-001).
- Label roto: ni axe ni otro gate lo detectan con placeholder presente (P2, decisión semántica documentada).
