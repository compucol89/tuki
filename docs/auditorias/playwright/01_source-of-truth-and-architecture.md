# 01 · Source of Truth, Arquitectura y Coherencia Docs↔Repo

## Corpus oficial (READ-ONLY, capturado 2026-08-21, microsoft/playwright main)

| Doc | Líneas | Capacidad documentada | Implementación TukiPass |
|---|---|---|---|
| `docs/reference/playwright/accessibility-testing.md` | 300 | Axe vía `@axe-core/playwright`; advierte automático ≠ manual | `a11y.spec.js` (AxeBuilder, wcag2a+wcag2aa, waivers) |
| `docs/reference/playwright/aria-snapshots.md` | 613 | `toMatchAriaSnapshot` del árbol de accesibilidad | `aria.spec.js` (h1 + jerarquía) |
| `docs/reference/playwright/test-snapshots.md` | 149 | `toHaveScreenshot` visual; env-dependence advertida | `visual.spec.js` (4 screenshots) |

**Coherencia docs↔repo:** las 3 capacidades están implementadas. La advertencia de la doc de visual ("correr en el mismo entorno donde se generó el baseline") **se viola en diseño**: baselines `-chromium-darwin` y CI es Ubuntu (ver 06).

## Versiones y pinning

- `@playwright/test ^1.62.1` → lockfile resuelve **1.62.1** (pin real vía package-lock + `npm ci`) ✓
- `@axe-core/playwright ^4.13.0` → resuelto por lockfile (no verificado vía runtime por exports del paquete).
- **DRIFT DOCS↔RUNTIME:** el corpus es `main` (futuro); runtime 1.62.1. Las APIs usadas (`toHaveScreenshot`, `toMatchAriaSnapshot`, `AxeBuilder`) existen en 1.62.1 → sin drift funcional demostrado; documentado como riesgo menor.

## Configuración (`playwright.config.js`)

- `testDir: ./tests/playwright` · `baseURL: http://localhost:8801` · viewport 1440×900 · `fullyParallel: true` · `retries: 0` · trace/screenshot on-failure · project chromium Desktop Chrome · `toHaveScreenshot.maxDiffPixels: 2000`.

## Invocación (FF-002 — determinista)

- `npm run test:<x>` (desde la raíz) → ✅ funciona (config cargada).
- `cd tests/playwright && npx playwright test` → ❌ **todos los tests fallan con "Cannot navigate to invalid URL"** (la config raíz no se aplica al correr desde el subdirectorio; baseURL ausente). Reproducido 3/3. **Ningún script npm usa este patrón; el riesgo es humano/CI mal configurado.**
- `npx playwright test` sin grep → ejecuta las 87.

## Tags

- 7 tags exclusivos por suite (@e2e, @a11y, @aria, @visual, @seo, @theme, @legal); sin colisiones ni multi-tag detectadas.

## Inventario de 87 tests

e2e 20 · a11y 16 · aria 18 · theme 16 · visual 4 · seo 4 · legal 8 (listado real `--list`).
