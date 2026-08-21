# 15 — Gaps conocidos y trabajo pendiente

Estado verificado al 2026-08-21 (post fases A-D).

| # | Gap | Impacto | Estado |
|---|-----|---------|--------|
| 1 | `.stylelintrc` inexistente (reglas `color-no-invalid-hex`, `declaration-block-no-redundant-longhands`) | Prevención de hex nuevos en CSS propio | Pendiente |
| 2 | `theme.spec.js` no verifica **contraste WCAG** por token (solo islas blancas, azul, overflow, texto oscuro) | a11y parcial en panel | Pendiente |
| 3 | `theme.spec.js` requiere credenciales `E2E_ORGANIZER_USERNAME/PASSWORD` — no corre local sin ellas | Gate visual solo en CI | Pendiente (documentado) |
| 4 | Workflow CI `organizer-theme.yml` existe; no ejecuta **PHPUnit** ni el corpus frontend de Playwright | Cobertura CI parcial | Pendiente |
| 5 | `--adm-primary*` (paleta de marca) sin token unificado (`--brand-*`); 8 refs en booking/index | Nomenclatura; riesgo bajo (misma capa admin-skin) | Aceptado — documentado en 06 |
| 6 | Contenido WYSIWYG de DB (about) con headings h4 — fuera del theming del panel | a11y contenido | Gestionado en auditoría frontend |
| 7 | Verificación browser local limitada por `throttle:5,1` del login organizer | Iteración local | Workaround: CI + render server-side |

## Recomendaciones priorizadas

1. `.stylelintrc` + gate en `organizer-theme.yml` (bajo esfuerzo, alto valor preventivo).
2. Extender `theme.spec.js` con cálculo de ratio de contraste por token en light/dark.
3. Ejecutar PHPUnit en el workflow CI.
