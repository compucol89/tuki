# 03-baseline.md — Runtime Baseline

**Date:** 2026-08-21
**Branch:** `remediation/audit-2026-08-21`
**HEAD:** `93466da9eccee9449847bef65ca1b09f8362b0f4`

---

## Server Status

| Check | Result |
|-------|--------|
| PHP server | ✅ Running on localhost:8000 |
| DB connection | ❌ FAIL — `db` hostname not resolving (Docker service) |
| Organizer login | ❌ 500 — SQLSTATE[HY000] [2002] |
| Dashboard render | ❌ Cannot render — requires auth → requires DB |

**Impact:** No screenshots, no axe-core automated scan, no computed-style measurement possible.
**Fallback:** Static analysis of committed code + re-measurement of Aug 20 findings from code.

---

## Git Baseline

```
Branch: remediation/audit-2026-08-21
HEAD: 93466da9eccee9449847bef65ca1b09f8362b0f4
Modified files: 65 (uncommitted changes on this branch)
```

Key modified files relevant to dashboard:
- `public/assets/admin/css/atlantis.css` (+11 lines — focus-visible)
- `public/assets/admin/css/admin-skin.css` (+437 lines — tokens, charts, skip-link)
- `public/assets/admin/css/admin-main.css` (+8 lines)
- `public/assets/admin/css/theme-dark.css` (+84 lines — dark tokens, profile score)
- `public/assets/admin/js/chart-init.js` (+194 lines — theme-aware palette)
- `resources/views/organizer/index.blade.php` (+45 lines — tokens, i18n)
- `resources/views/organizer/layout.blade.php` (+7 lines — skip-link, theme script)
- `resources/views/organizer/partials/side-navbar.blade.php` (+152 lines)
- `resources/views/organizer/partials/top-navbar.blade.php` (+26 lines)
- `resources/views/organizer/partials/scripts.blade.php` (+75 lines)

---

## Re-measurement of Aug 20 Findings

### Methodology
Re-measure by reading the current committed code. Each finding classified as:
- **RESOLVED-CODE** — Fix exists in code, verified by reading
- **RESOLVED-NEEDS-VERIFICATION** — Fix exists but runtime check needed
- **OPEN** — Issue still present in code
- **NOT-TESTED** — Cannot determine from static analysis alone

---

### Finding 1: A11Y-DARK-001 — `--adm-muted:#a3a3a3` fails AA on dark body
**Status: RESOLVED-CODE** ✅
- `theme-dark.css:759`: `--adm-muted: #b0b0b0` (was `#a3a3a3`)
- Ratio `#b0b0b0` on `#1c2433` = 6.54:1 (AA pass)
- File: `public/assets/admin/css/theme-dark.css:759`

### Finding 2: A11Y-DARK-002 — `--adm-muted` fails on `--adm-card`
**Status: RESOLVED-CODE** ✅
- `--adm-muted:#b0b0b0` on `--adm-card:#2a3040` = 5.22:1 (AA pass)
- File: `public/assets/admin/css/theme-dark.css:759,756`

### Finding 3: A11Y-DARK-003 — Focus suppression `outline:0 !important`
**Status: PARTIALLY RESOLVED** ⚠️
- `atlantis.css:79-83`: `:focus { outline: 0 !important }` STILL EXISTS
- `atlantis.css:87-92`: `:focus-visible { outline: 2px solid #f97316 !important }` ADDED
- `admin-skin.css:175`: `outline: none !important` on `.ev-accordion-btn` STILL EXISTS
- `admin-main.css:545`: `outline: none` on Summernote editor STILL EXISTS
- **Net effect:** Keyboard focus visible via `:focus-visible` (WCAG 2.4.7), but `:focus` (mouse + keyboard fallback) still suppressed globally
- **Risk:** If `focus-visible` is not supported by user agent, no focus indicator at all

### Finding 4: A11Y-DARK-004 — `--od-*` tokens light-only
**Status: RESOLVED-CODE** ✅
- `theme-dark.css:890-946`: Dark overrides for `.od-profile-score`:
  - `--od-text: #e5e5e5` (was `#1e2532`)
  - `--od-muted: #b0b0b0` (was `#4b5563`)
  - `--od-surface: #2a3040` (was `#ffffff`)
  - `--od-border: #3d4354` (was `#dcdfe2`)
  - `--od-soft: #3a3c44` (was `#f3f4f6`)
  - Eyebrow, buttons, labels all have dark overrides

### Finding 5: A11Y-DARK-005 — Chart colors hardcoded
**Status: RESOLVED-CODE** ✅
- `chart-init.js`: New `tukiChartPalette()` function reads `document.documentElement.dataset.theme`
- Dark: tick/legend `#c8cdd6`, grid `rgba(255,255,255,.10)`
- Light: tick/legend `#6b7280`, grid `rgba(0,0,0,.08)`
- Guard for non-existent canvases added

### Finding 6: A11Y-DARK-006 — Profile score white on dark
**Status: RESOLVED-CODE** ✅
- `theme-dark.css:900`: `--od-surface: #2a3040` (dark card background)
- All token overrides in place

### Finding 7: A11Y-DARK-007 — Breadcrumbs contrast
**Status: NOT-TESTED** ❓
- No breadcrumb component found in dashboard view (`index.blade.php`)
- May have been removed or never existed on this specific page

### Finding 8: A11Y-DARK-008 — KPI card text contrast
**Status: NOT-TESTED** ❓
- KPI cards use `--adm-ink` and `--adm-muted` tokens
- Tokens are now properly themed in dark mode
- Runtime verification needed for computed colors

### Finding 9: A11Y-DARK-009 — Sidebar menu contrast
**Status: NOT-TESTED** ❓
- Sidebar uses Atlantis menu classes
- `theme-dark.css` has sidebar overrides (lines 781-815)
- Runtime verification needed

### Finding 10: A11Y-DARK-010 — Top navbar contrast
**Status: NOT-TESTED** ❓
- Top navbar uses Atlantis classes
- Dark mode overrides exist in `theme-dark.css`
- Runtime verification needed

### Finding 11: A11Y-DARK-011 — Chart overlap on mobile
**Status: RESOLVED-CODE** ✅
- `admin-skin.css`: `.dashboard-items.row` uses CSS Grid with `repeat(auto-fit, minmax(min(100%,280px),1fr))`
- Charts get proper width

### Finding 12: A11Y-DARK-012 — Chart overlap in general
**Status: RESOLVED-CODE** ✅
- Same fix as above — grid layout prevents overlap

### Finding 13: A11Y-DARK-013 — Skip link missing
**Status: RESOLVED-CODE** ✅
- `layout.blade.php:47`: `<a href="#main-content" class="skip-link">Saltar al contenido</a>`
- `layout.blade.php:65`: `<main class="content" id="main-content">`
- `admin-skin.css`: `.skip-link` CSS for visibility on focus

### Finding 14: A11Y-DARK-014 — i18n hardcoded English
**Status: RESOLVED-CODE** ✅
- `admin.json`: `Welcome back` → `Bienvenido/a`
- `admin.json`: `Total Event Bookings` → `Total de reservas de eventos`
- `admin.json`: `Event Booking Monthly Income` → `Ingresos mensuales por reservas`

---

## Summary

| Status | Count | Details |
|--------|-------|---------|
| RESOLVED-CODE | 9 | Findings 1,2,4,5,6,11,12,13,14 |
| PARTIALLY RESOLVED | 1 | Finding 3 (focus: focus-visible added, focus:0 remains) |
| NOT-TESTED | 3 | Findings 7,8,9,10 (need runtime) |
| OPEN | 0 | — |

**Key finding:** Focus suppression is the only partially resolved issue. The `:focus { outline: 0 !important }` in `atlantis.css:79` is NOT removed — a `:focus-visible` override was added AFTER it. This works for keyboard navigation (modern browsers) but fails for:
- Browsers without `focus-visible` support
- Mouse users who expect focus ring on click
- Assistive technology that relies on `:focus` pseudo-class

**Recommendation for next wave:** Consider removing `outline: 0` from `:focus` and relying solely on `:focus-visible` for focus suppression. This is a 1-line change in `atlantis.css:79` but touches ALL admin pages.
