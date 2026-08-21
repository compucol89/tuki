# Frontend Quality Gates — TukiPass

> Versión: 1.0 · 2026-08-21 · Estado: vigente
> Referencias: `docs/reference/playwright/` (accessibility-testing, test-snapshots, aria-snapshots)

## Definición de "done"

Un cambio de frontend/calidad está terminado solo cuando pasan **todas** las capas:

```text
PHPUnit
│  backend, consultas, scopes, publicación, datos, reglas de negocio
Playwright E2E
│  navegación, interacción, formularios, responsive
Axe (@axe-core/playwright)
│  violaciones automáticas WCAG 2.2
ARIA snapshots (toMatchAriaSnapshot)
│  landmarks, headings, labels, roles, accessible names, jerarquía
Visual snapshots (toHaveScreenshot)
│  light / dark · desktop / mobile · regresión CSS
```

## Scripts objetivo (Fase 4 — aún no instalado)

```json
{
  "test:e2e":     "playwright test --grep @e2e",
  "test:a11y":    "playwright test --grep @a11y",
  "test:aria":    "playwright test --grep @aria",
  "test:visual":  "playwright test --grep @visual",
  "test:frontend":"npm run test:e2e && npm run test:a11y && npm run test:aria"
}
```

## Reglas de cada capa

1. **PHPUnit** — Feature tests del backend: publicación (draft/futuro/soft-deleted),
   scopes de organizadores, counts consistentes, Production Data Integrity.
2. **Playwright E2E** — flujos críticos: home → evento → checkout (solo render, ver
   `standards.md` zona de pagos), login, dashboard. Sin mutar estados de pago.
3. **Axe** — corre contra cada página clave en light y dark. Aserción canónica:
   `expect(violations).toEqual([])` — `@axe-core/playwright` NO documenta filtros por
   impacto (verificado en `docs/reference/playwright/accessibility-testing.md`); las
   excepciones se registran SOLO vía `disableRules()` con waiver documentado (regla,
   motivo, dueño, fecha). *Axe passing ≠ WCAG passing* (ver accessibility-policy).
4. **ARIA snapshots** — congelan el árbol de accesibilidad: si un heading, rol, label o
   landmark cambia sin intención, el test falla. Es la red que atrapa regresiones
   semánticas que el píxel no ve.
5. **Visual** — baselines generados **en el mismo entorno** que las comparaciones
   (Playwright lo recomienda expresamente: mismo SO, versión, settings, hardware,
   energía, headless — `docs/reference/playwright/test-snapshots.md`). Tolerancia
   vía `maxDiffPixels` (no ratio). Actualización intencional con `-u/--update-snapshots`.
   Incluir light y dark, desktop y mobile.

## Quality gate de remediación (Fases 3–8 del plan)

```text
FASE 3  BASELINE   → capturar estado actual (PHPUnit, DOM, computed CSS, screenshots,
                     console, network, DB states) ANTES de tocar código
FASE 4  RED        → escribir tests que reproducen cada bug del audit; verlos fallar
FASE 5  REMEDIACIÓN→ cambios mínimos y trazables (sin actualizar dependencias)
FASE 6  GREEN      → todos los tests pasan (todas las capas)
FASE 7  FORENSE    → re-auditoría post-fix con las mismas herramientas del baseline
FASE 8  GATE       → PASS / FAIL / WAIVER documentado (cada excepción con motivo y dueño)
```

## Bloqueantes de producción

- Test de regresión nuevo que falle (ningún error del audit puede reincidir).
- Claim sin provenance (ver content-integrity-policy).
- Violación a11y automática crítica/seria en flujo principal.
- Regresión visual no intencional en vistas públicas.
