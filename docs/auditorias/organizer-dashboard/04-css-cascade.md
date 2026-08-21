# 04-css-cascade.md — CSS Cascade Forensic Analysis

**Date:** 2026-08-21
**Page:** `/organizer/dashboard`

---

## Load Order (confirmed from `styles.blade.php`)

```
1. bootstrap.min.css          (152 KB) — Grid, utilities, base components
2. atlantis.css               (329 KB) — Theme: layout, sidebar, cards, navbar
3. admin-skin.css              (64 KB) — Tuki overrides: tokens, typography, components
4. admin-main.css              (18 KB) — Specific overrides (summernote, dark tables)
5. theme-dark.css              (41 KB) — Dark mode (html[data-theme="dark"])
6. responsive.css              (59 KB) — Responsive breakpoints
7. fonts.min.css               (66 KB) — FA5, Flaticon, Simple Line Icons
8. animate.min.css             (55 KB) — Animations
9-16. Plugin CSS (select2, datatables, dropzone, etc.)
```

**Cascade winner:** Later file wins at equal specificity. `admin-skin.css` beats `atlantis.css`. `theme-dark.css` beats all when `html[data-theme="dark"]`.

---

## Focus Cascade (Critical)

### Rule chain for `outline` on focus

| Selector | File:Line | Value | Specificity | Notes |
|----------|-----------|-------|-------------|-------|
| `:focus` | `atlantis.css:79` | `outline: 0 !important` | 0,1,0 + !important | **SUPPRESSES ALL FOCUS** |
| `:focus-visible` | `atlantis.css:87` | `outline: 2px solid #f97316 !important` | 0,1,0 + !important | Keyboard-only visible |
| `.ev-accordion-btn:focus` | `admin-skin.css:179` | `color: #C2410C !important` | 0,2,0 + !important | Color change only, no outline |
| `.ev-accordion-btn` | `admin-skin.css:175` | `outline: none !important` | 0,1,0 + !important | Redundant with atlantis |
| Summernote `:focus` | `admin-main.css:545` | `outline: none` | 0,7,0 (no !important) | Lower specificity, scoped |

**Net effect:**
- `:focus` → `outline: 0 !important` (global suppression)
- `:focus-visible` → `outline: 2px solid #f97316 !important` (keyboard only)
- Mouse click focus → NO visible indicator (by design in Atlantis)
- Keyboard Tab → Visible orange ring (via focus-visible)

**WCAG impact:** 2.4.7 Focus Visible — PASS for keyboard, CONCERN for mouse. 2.4.13 Focus Appearance (AAA) — FAIL (focus indicator not always visible).

---

## Design Token Cascade

### `--adm-*` tokens (admin shell)

| Token | Light (admin-skin.css) | Dark (theme-dark.css) | Component using it |
|-------|----------------------|----------------------|-------------------|
| `--adm-bg` | (none — default body) | `#1c2433` | Page background |
| `--adm-bg-soft` | (none) | `#2a303e` | Secondary backgrounds |
| `--adm-card` | (none) | `#2a3040` | Card backgrounds |
| `--adm-ink` | (none — browser default) | `#e5e5e5` | Primary text |
| `--adm-ink-strong` | (none) | `#ffffff` | Strong/bold text |
| `--adm-muted` | (none) | `#b0b0b0` | Secondary text |
| `--adm-border` | (none) | `#3d4354` | Borders |
| `--adm-primary` | `#C2410C` (admin-skin:230) | `#e05d38` | Primary CTA |
| `--adm-primary-dark` | (none) | `#f4845f` | Primary hover |
| `--adm-primary-strong` | (none) | `#f78a63` | Primary strong |
| `--adm-primary-soft` | (none) | `#3a2c26` | Primary background |
| `--adm-success` | (none) | `#4ade80` | Success badges |
| `--adm-info` | (none) | `#60a5fa` | Info badges |
| `--adm-warning` | (none) | `#fbbf24` | Warning badges |
| `--adm-danger` | (none) | `#f87171` | Danger badges |
| `--adm-sidebar` | (none) | `#171e2b` | Sidebar bg |
| `--adm-sidebar-soft` | (none) | `#1f2838` | Sidebar secondary |

**Problem:** Light mode tokens are NOT defined on `:root` in `admin-skin.css`. They only exist as hardcoded values in component rules. Dark mode tokens ARE properly defined on `html[data-theme="dark"]`. This means light mode relies on inline colors while dark mode uses CSS variables.

### `--od-*` tokens (dashboard profile score)

| Token | Light (index.blade.php) | Dark (theme-dark.css) |
|-------|------------------------|----------------------|
| `--od-primary` | `#e05d38` | (inherited) |
| `--od-primary-strong` | `#bf4424` | (inherited) |
| `--od-text` | `#1e2532` | `#e5e5e5` |
| `--od-muted` | `#4b5563` | `#b0b0b0` |
| `--od-surface` | `#ffffff` | `#2a3040` |
| `--od-border` | `#dcdfe2` | `#3d4354` |
| `--od-soft` | `#f3f4f6` | `#3a3c44` |

**This is properly themed.** Light tokens defined in `index.blade.php`, dark overrides in `theme-dark.css`.

---

## Component-Level Cascade (Dashboard)

### KPI Cards
| Property | Source | Specificity | Winner |
|----------|--------|-------------|--------|
| Background | `atlantis.css` `.card` | 0,1,0 | White (light) / `--adm-card` (dark) |
| Border | `admin-skin.css` `.card` | 0,1,0 | `1px solid var(--adm-border)` |
| Shadow | `atlantis.css` `.card` | 0,1,0 | `0 1px 15px 1px rgba(...)` |
| Text color | `admin-skin.css` via `--adm-ink` | 0,1,0 | Dark: `#e5e5e5` |

### Sidebar
| Property | Source | Specificity | Winner |
|----------|--------|-------------|--------|
| Background | `atlantis.css` `.sidebar` | 0,1,0 | `#1a1f36` |
| Dark override | `theme-dark.css` line 782+ | 0,2,0 + !important | `var(--adm-sidebar)` = `#171e2b` |
| Active item | `admin-skin.css` `.active` | 0,2,0 | `box-shadow: inset 3px 0 0 var(--adm-primary)` |

### Profile Score Module
| Property | Source | Specificity | Winner |
|----------|--------|-------------|--------|
| Background | `index.blade.php` `.od-profile-score` | 0,1,0 | `var(--od-surface)` |
| Dark override | `theme-dark.css:900` | 0,3,0 | `--od-surface: #2a3040` |
| Text | `index.blade.php` | 0,1,0 | `var(--od-text)` |
| Dark text | `theme-dark.css:901` | 0,3,0 | `--od-text: #e5e5e5` |

### Charts
| Property | Source | Specificity | Winner |
|----------|--------|-------------|--------|
| Grid color | `chart-init.js` via `tukiChartPalette()` | N/A (JS) | Dark: `rgba(255,255,255,.10)` |
| Tick color | `chart-init.js` | N/A (JS) | Dark: `#c8cdd6` |
| Border colors | `chart-init.js` | N/A (JS) | Hardcoded: `#f97316`, `#6366f1`, etc. |

**Chart border colors are NOT theme-aware.** They stay orange/indigo/emerald/blue regardless of theme. This is acceptable because these are data series colors (not text).

---

## `!important` Audit

| Count | File | Notes |
|-------|------|-------|
| ~200+ | `atlantis.css` | Theme uses !important extensively |
| ~150+ | `admin-skin.css` | Overrides require !important to beat Atlantis |
| ~80+ | `theme-dark.css` | Dark overrides require !important |
| ~20 | `admin-main.css` | Scoped overrides |

**This is by design.** The Tuki override strategy relies on `!important` to beat Atlantis specificity. Removing !important would require restructuring all three files — out of scope.

---

## `!important` vs. no-`!important` Conflicts

No conflicting rules found where one uses !important and the other doesn't at the same specificity. The cascade is clean: each layer uses !important to beat the previous layer.

---

## Summary

| Area | Status | Notes |
|------|--------|-------|
| Focus | ⚠️ Partial | `:focus-visible` works, `:focus` still suppressed globally |
| Light tokens | ⚠️ Incomplete | `--adm-*` not defined on `:root`, relies on hardcoded values |
| Dark tokens | ✅ Complete | All `--adm-*` and `--od-*` properly themed |
| Charts | ✅ Themed | Grid/tick colors adapt, data colors stay consistent |
| !important cascade | ✅ Clean | Consistent strategy across all layers |
| Profile score | ✅ Themed | Both light and dark tokens properly defined |
