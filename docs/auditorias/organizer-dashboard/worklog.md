# worklog.md — Organizer Dashboard Forensic Audit

## Session 2026-08-21 (Fases 0–2)

### What was done

| Time | Action | Result |
|------|--------|--------|
| — | Created directory structure | `docs/auditorias/organizer-dashboard/` + `baseline/` subdirs |
| — | Installed `@playwright/test` + `@axe-core/playwright` | devDependencies added |
| — | Created `playwright.config.js` | Config with light/dark projects |
| — | Installed Chromium for Playwright | Ready for runtime tests |
| — | Captured git baseline | Branch, HEAD, diff stat saved |
| — | Re-measured Aug 20 findings | 9 RESOLVED-CODE, 1 PARTIAL, 3 NOT-TESTED, 0 OPEN |
| — | Created `00-scope.md` | Scope, partials, CSS/JS inventory |
| — | Created `01-stack-runtime.md` | Transfer analysis: 2.7 MB total, 55% JS waste, 25% CSS waste |
| — | Created `02-route-surface.md` | 98 routes classified: 1 DIRECT, 25 SHARED, 12 AUTH, 37 API/AJAX |
| — | Created `03-baseline.md` | Git baseline + re-measurement of 14 Aug 20 findings |
| — | Created `04-css-cascade.md` | Cascade analysis, token mapping, focus chain, !important audit |
| — | Created `05-theme-dark-light.md` | Dual theme architecture, 3 confirmed desync scenarios |
| — | Created `issues.csv` | 13 issues (1 P2, 3 P3, 4 P4, 5 informational) |

### What could NOT be done

| Blocker | Reason | Impact |
|---------|--------|--------|
| Playwright screenshots | DB unreachable (`db` hostname) | No visual baseline |
| axe-core automated scan | Requires rendered page | No automated a11y data |
| Computed style measurement | Requires DOM | No runtime contrast verification |
| Keyboard navigation test | Requires rendered page | No tab order documentation |
| Responsive testing | Requires rendered page | No viewport measurements |

### Server status
- PHP server running on localhost:8000
- DB connection FAIL: `SQLSTATE[HY000] [2002] php_network_getaddresses: getaddrinfo for db failed`
- `.env` points to `DB_HOST=db` (Docker service not running)

### Key discoveries

1. **Focus suppression is MORE nuanced than Aug 20 doc suggests.** `:focus { outline: 0 }` is still there, but `:focus-visible` was added. Mouse focus = invisible, keyboard focus = visible. This is acceptable for WCAG 2.4.7 but fails 2.4.13 (AAA).

2. **Light mode has NO token system.** `--adm-*` tokens only exist in dark mode (`theme-dark.css`). Light mode relies on hardcoded values in `admin-skin.css` component rules. This means adding new components in light mode requires editing individual rules, not just setting tokens.

3. **Theme desync is CONFIRMED.** Toggle updates DOM+localStorage but NOT DB. API updates DB but NOT DOM+localStorage. Three distinct desync scenarios documented.

4. **55% of JS is wasted on dashboard.** Select2, DataTables, Vue, TinyMCE, Dropzone are loaded but never used. No conditional loading.

5. **Route typos are persistent.** `support-tikcet`, `transcation`, `witdraw` — documented but not fixed (would break existing links/APIs).

### Deliverables

| File | Status |
|------|--------|
| `00-scope.md` | ✅ Complete |
| `01-stack-runtime.md` | ✅ Complete |
| `02-route-surface.md` | ✅ Complete |
| `03-baseline.md` | ✅ Complete |
| `04-css-cascade.md` | ✅ Complete |
| `05-theme-dark-light.md` | ✅ Complete |
| `issues.csv` | ✅ Complete (13 issues) |
| `worklog.md` | ✅ This file |

### Next steps (Fase 3–4 / next wave)

1. **Start Docker DB** → render dashboard → Playwright screenshots light/dark
2. **Run axe-core** → automated a11y violations
3. **Keyboard tab order** → document full tab path
4. **Component-by-component audit** → sidebar, navbar, KPIs, charts, footer
5. **Fix theme desync** → unify toggle to also persist to DB
6. **Fix focus suppression** → remove `outline:0` from `:focus`, keep only `:focus-visible`
7. **Conditional CSS/JS loading** → reduce dashboard payload by ~800 KB
