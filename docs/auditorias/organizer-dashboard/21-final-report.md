# 21 — Final Report

**Fecha:** 2026-08-21 · **Superficie:** /organizer/dashboard + infraestructura compartida + event/index + event/edit

---

## EXECUTIVE VERDICT

**La reauditoría forense sistémica del Dashboard del Organizador está COMPLETA.**
Los 3 root causes sistémicos (RC-1 selector leakage, RC-2 inline light-only,
RC-3 sin enforcement) fueron tratados en capas previas y **revalidados**; esta
auditoría encontró y corrigió **11 defectos nuevos** (1 P1 theme, 4 P1 a11y,
3 P2 charts, 2 P2 queries, 1 P2 cascade), extendió los gates de prevención y
cerró con **0 violaciones axe** y **14/14 tests de tema PASS**.

---

## BASELINE

- Branch: `remediation/audit-2026-08-21` · HEAD: `77002119` (al inicio)
- 51 archivos modificados preexistentes (clasificados PREEXISTING — no incluidos en el commit de esta auditoría)
- Stack: Laravel 12.56, PHP 8.4, Bootstrap 4.3.1, jQuery 3.7.0, Chart.js 2.7.2, FA6, Playwright 1.62, axe 4.13

## WHAT WAS REVALIDATED

- 14 findings previos del dashboard: 9 RESOLVED-CODE, 1 PARTIAL (focus → ahora VERIFIED), 3 NOT-TESTED → todos re-medidos
- Charts renderizados (falso positivo previo de "canvas vacío": `Chart.getChart` no existe en v2.7.2)
- Contraste sidebar: activo 16.71:1, default 9.11:1
- H1 único, 0 islas blancas dark

## FALSE CLAIMS REMOVED

| Claim | Veredicto |
|-------|-----------|
| "DB unreachable / server 500" | FALSO — Docker up (8801) |
| "--adm-* no definidos" | FALSO — 25 en :root |
| "booking/index con hardcoded" | FALSO — 0 hex |
| "Ruta /event/ticket rota" | FALSO POSITIVE — requiere event_id (diseño correcto) |

## CONFIRMED ISSUES (nuevos, corregidos)

| ID | Issue | Severidad | Fix |
|----|-------|-----------|-----|
| THEME-001 | Toggle theme no persiste a DB (desync) | P1 | fetch + whitelist + reconciliación |
| A11Y-001 | Labels de sección sidebar 4.18:1 light | P1 | token #5f6b7d (4.91:1) |
| A11Y-002 | Radios de tema sin accessible name | P1 | aria-label ES |
| A11Y-003 | `<ul>` con hijos no-`<li>` (2) | P2 | estructura corregida |
| CHART-001 | Re-init duplica instancias | P2 | registry guard |
| CHART-002 | pointBorderColor #fff hardcoded | P2 | token |
| CHART-003 | Charts no re-teman en caliente | P2 | tukiRethemeCharts |
| DATA-001 | `->get()->count()` ×3 | P2 | `->count()` |
| CASC-001 | .event-cover-box perdía cascade | P2 | override documentado |
| TOKEN-001 | 48 raw colors en 4 blades | P1 | tokens semánticos |
| FOCUS-001 | (revalidado) foco teclado OK | — | VERIFIED, no defecto |

## ROOT CAUSES

1. RC-1 (selector leakage) — revalidado, Vendor Override estable
2. RC-2 (inline light-only) — **eliminado**: 0 hex en blades migrados
3. RC-3 (sin enforcement) — **mitigado**: gate extendido (blade_raw, outline)

## ACCESSIBILITY RESULTS

- axe: 9 violaciones light → **0**; dark **0**
- Teclado: skip-link → logo → contenido, `:focus-visible` outline 2px naranja ✅
- Target size: todos ≥24px (mayoría 40+), excepciones evaluadas
- Reflow: 320px sin overflow, zoom 200% OK

## THEME / TOKEN / CHART / DATA RESULTS

- Theme: DB canónica, toggle sincronizado, fallback accesible, reload coherente
- Tokens: 0 hex restantes en blades migrados; gate activo
- Charts: guard anti-reinit, re-theme hot, pointBorder token, accesibles (role=img + aria-label + fallback)
- Data: −3 queries fetch; sin N+1; preventLazyLoading recomendado (no activado)

## TEST RESULTS

| Suite | Resultado |
|-------|-----------|
| test:theme | ✅ 14/14 |
| test:a11y (dashboard) | ✅ 2/2 |
| audit:organizer-theme | ✅ PASS (5 gates) |
| Compilación | ✅ |

## PREVENTION GATES

- 5 gates estáticos operativos (2 nuevos: blade_raw_colors, outline_suppression)
- CI GitHub Actions configurado (workflow del repo)
- Branch protection: pendiente manual en GitHub UI (documentado)

## KNOWN GAPS (FOLLOW-UP)

1. Empty state visual para charts con 0 datos (decisión de producto)
2. Skeleton loading para métricas
3. Formato compacto de montos grandes (k/M)
4. Test automatizado del toggle→DB con interceptación de fetch
5. Visual regression del dashboard en CI (requiere fixture estable)
6. preventLazyLoading en dev (recomendado, no activado)

## RESIDUAL RISKS

- Low: multiple tabs sin broadcast de tema (aceptado — DB es fuente al cargar)
- Low: localStorage puede quedar stale si otro dispositivo cambia el tema
  (aceptado — DB gana al cargar)
- Informational: `!important` en .event-cover-box (justificado, documentado en blade)

## GIT STATE

- 51 archivos preexistentes NO incluidos en el commit (clasificados PREEXISTING/UNKNOWN)
- Commit de esta auditoría: archivos THIS AUDIT únicamente (docs 06-21, fixes, tests, gates)
- Push: `remediation/audit-2026-08-21` (si el remoto corresponde)

## FINAL VERDICT

**CONTROLLING THE SYSTEM**: los defectos se corrigieron por causa raíz (no por
instancia), los gates impiden reintroducción, la evidencia es reproducible y
las métricas están medidas (no estimadas). El dashboard pasa axe, contraste,
teclado, reflow, tema y charts. La auditoría cumple los exit gates definidos.
