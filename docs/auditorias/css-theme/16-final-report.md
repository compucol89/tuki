# Reporte Final — CSS Theme Forense (Organizer)

**Fecha:** 2026-08-21

## Respuestas a las 15 preguntas del prompt

1. **¿Qué estilos siguen dependiendo de Atlantis?** Estructura base (layout/sidebar/navbar/cards/forms/tables/buttons) + 794 `!important` vendor.
2. **¿Qué reglas Atlantis generan conflictos reales?** `.nav.nav-primary > .nav-item.active a i` (#1572E8, descendente) y `.sidebar.sidebar-style-2 ... a[aria-expanded=true] i` (blanco en light) — ambas neutralizadas por Vendor Override (spec idéntica + orden).
3. **¿Qué !important gana cada conflicto?** El de admin-skin (misma spec, carga después).
4. **¿Qué selectores tienen alcance excesivo?** `active a i` / `active a p` descendentes (documentado en 04-selector-leakage).
5. **¿Cuántos Blade tienen CSS inline?** 12 (listados en 05).
6. **¿Cuáles no soportaban dark?** 10 de 12 → migrados a tokens (dashboard y edit-profile ya lo tenían).
7. **¿Cuántos colores hardcoded en CSS Tuki-owned?** 16 superficies claras (intencionales, con dark override — baseline).
8. **¿Cuáles deberían convertirse en tokens?** Los 16 restantes son botones/controles light legítimos; no requieren token (tienen override dark).
9. **¿Qué componentes fallaban dark?** .oe-*, .ob-*, .bod-*, .tb-*, .ai-*, .ticket-form-*, cover/async panels.
10. **¿Cuáles fueron corregidos?** Todos los anteriores (verificado en runtime + tests).
11. **¿Qué tests reproducen cada bug?** `tests/playwright/theme.spec.js` (13 tests): iconos azules, islas blancas, texto oscuro, overflow.
12. **¿Qué mecanismo impide otra pantalla light-only?** `npm run audit:organizer-theme` (NO NEW DEBT) + `npm run test:theme`.
13. **¿Qué mecanismo impide repetir el bug del icono?** Test de contrato computed + regla Vendor Override documentada.
14. **¿Qué deuda legacy queda?** 16 superficies claras intencionales; 12 blades con `<style>` inline (migrados a tokens pero sin extraer a archivos — opcional futuro).
15. **¿Qué se dejó intacto intencionalmente?** atlantis.css (vendor, read-only), Lato (neutralizado por Inter), 19 light rules en theme-dark (documentadas), edit-profile/dashboard (ya tokenizados).

## Gates

| Gate | Estado |
|------|--------|
| Icono azul (#1572E8) | ✅ 0 en 6 rutas × 2 temas |
| Islas blancas en dark | ✅ 0 en 6 rutas |
| Texto oscuro sobre dark | ✅ 0 |
| Overflow horizontal | ✅ 0 |
| Light reproduce valores originales | ✅ verificado |
| Scroll del menú entre navegaciones | ✅ preservado (sessionStorage) |
| Estado de secciones entre navegaciones | ✅ sesión del servidor |
| NO NEW DEBT (static audit) | ✅ PASS |
| Tests Playwright @theme | ✅ 13/13 |
| Focus visible | ✅ :focus-visible global + sidebar |
| Contraste iconos ≥3:1 | ✅ (9.11:1 default / 16.71:1 activo dark) |

## Archivos modificados

- `resources/views/organizer/partials/side-navbar.blade.php` — estado en sesión, aria-controls
- `resources/views/organizer/partials/scripts.blade.php` — persistencia sesión + scroll preservation + ESC
- `resources/views/organizer/partials/top-navbar.blade.php` — i18n cuenta
- `routes/organizer_dashboard.php` + `OrganizerController` — ruta sidebar-state
- 10 blades organizer — migración a tokens semánticos
- `public/assets/admin/css/admin-skin.css` — tokens unificados + Vendor Override
- `public/assets/admin/css/theme-dark.css` — limpieza de overrides redundantes (−179 líneas)
- `tests/playwright/theme.spec.js` — 13 tests de contrato
- `scripts/audit-organizer-theme.sh` + `scripts/baseline-theme.json` — gate NO NEW DEBT
- `docs/auditorias/css-theme/*` — arquitectura documentada
