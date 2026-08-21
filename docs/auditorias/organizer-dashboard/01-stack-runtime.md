# 01-stack-runtime.md — Stack & Runtime Analysis

**Date:** 2026-08-21
**Page:** `/organizer/dashboard`

---

## Server Environment

| Item | Value |
|------|-------|
| PHP | 8.2+ |
| Laravel | 12.x |
| DB Host | `db` (Docker service) |
| DB Driver | MySQL |
| DB Name | `eventos` |
| Server Status | ⚠️ Running but DB unreachable (`db` hostname not resolving) |

---

## CSS Transfer Analysis (what Chromium downloads for dashboard)

### Ordered by load (from `styles.blade.php`)

| # | File | Size (disk) | Gzipped est. | Blocking | Used on dashboard |
|---|------|-------------|-------------|----------|-------------------|
| 1 | `bootstrap.min.css` | 152 KB | ~22 KB | Yes | ✅ Grid, utilities |
| 2 | `atlantis.css` | 329 KB | ~55 KB | Yes | ✅ Theme base |
| 3 | `admin-skin.css` | 64 KB | ~12 KB | Yes | ✅ Tokens, overrides |
| 4 | `admin-main.css` | 18 KB | ~4 KB | Yes | ✅ Specific overrides |
| 5 | `theme-dark.css` | 41 KB | ~8 KB | Yes | ✅ Dark mode |
| 6 | `responsive.css` | 59 KB | ~11 KB | Yes | ✅ Responsive |
| 7 | `fonts.min.css` | 66 KB | ~12 KB | Yes | ✅ FA5 + Flaticon |
| 8 | `animate.min.css` | 55 KB | ~10 KB | Yes | ⚠️ Loaded but minimal use |
| 9 | `select2.min.css` | 16 KB | ~3 KB | Yes | ❌ No select2 on dashboard |
| 10 | `datatables.bootstrap4.min.css` | 5 KB | ~1 KB | Yes | ❌ No DataTable on dashboard |
| 11 | `datatables-1.10.23.min.css` | 14 KB | ~3 KB | Yes | ❌ No DataTable on dashboard |
| 12 | `dropzone.min.css` | 10 KB | ~2 KB | Yes | ❌ No upload on dashboard |
| 13 | `fontawesome-iconpicker.min.css` | 6 KB | ~1 KB | Yes | ❌ No icon picker |
| 14 | `jquery-ui.min.css` | 31 KB | ~6 KB | Yes | ⚠️ Loaded, not visibly used |
| 15 | `summernote.min.css` | 8 KB | ~2 KB | Yes | ❌ No editor on dashboard |
| 16 | `toastr.min.css` | 7 KB | ~1 KB | Yes | ⚠️ Notifications, not visible |

**Total CSS transferred:** ~881 KB (~153 KB gzipped)
**CSS actually used on dashboard:** ~659 KB (~124 KB gzipped)
**Wasted CSS:** ~222 KB (~29 KB gzipped) — 25% of total

### Critical CSS Notes
- `event-form-modern.css` (37 KB) is NOT loaded on dashboard (good)
- `mega-menu.css` (5 KB) is NOT loaded on dashboard (good)
- `admin-icons-compat.css` (1 KB) is NOT loaded on dashboard (good)

---

## JS Transfer Analysis

### Ordered by load (from `scripts.blade.php`)

| # | File | Size (disk) | Gzipped est. | Used on dashboard |
|---|------|-------------|-------------|-------------------|
| 1 | `jquery.min.js` | 118 KB | ~37 KB | ✅ |
| 2 | `popper.min.js` | 21 KB | ~7 KB | ✅ (Bootstrap dep) |
| 3 | `bootstrap.min.js` | 57 KB | ~16 KB | ✅ |
| 4 | `jquery-ui.min.js` | 284 KB | ~84 KB | ⚠️ Loaded, not visibly used |
| 5 | `jquery.scrollbar.min.js` | 12 KB | ~4 KB | ✅ Sidebar scrollbar |
| 6 | `chart.min.js` | 156 KB | ~52 KB | ✅ 4 charts |
| 7 | `chart-init.js` | 3 KB | ~1 KB | ✅ Custom init |
| 8 | `admin-main.js` | 33 KB | ~10 KB | ✅ |
| 9 | `admin-partial.js` | 19 KB | ~6 KB | ✅ |
| 10 | `atlantis.js` | 9 KB | ~3 KB | ✅ |
| 11 | `main.js` | 11 KB | ~4 KB | ✅ |
| 12 | `select2.min.js` | 74 KB | ~22 KB | ❌ No select2 on dashboard |
| 13 | `datatables-1.10.23.min.js` | 85 KB | ~29 KB | ❌ No DataTable |
| 14 | `datatables.bootstrap4.min.js` | 4 KB | ~1 KB | ❌ |
| 15 | `sweetalert.min.js` | 40 KB | ~13 KB | ❌ |
| 16 | `dropzone.min.js` | 41 KB | ~13 KB | ❌ |
| 17 | `admin_dropzone.js` | 6 KB | ~2 KB | ❌ |
| 18 | `vue-js.min.js` | 114 KB | ~39 KB | ❌ No Vue on dashboard |
| 19 | `fontawesome-iconpicker.min.js` | 108 KB | ~35 KB | ❌ |
| 20 | `jquery.nice-select.min.js` | 3 KB | ~1 KB | ❌ |
| 21 | `bootstrap-tagsinput.min.js` | 9 KB | ~3 KB | ❌ |
| 22 | `jquery.counterup.min.js` | 1 KB | ~0.5 KB | ⚠️ Counter animation |
| 23 | `wow.min.js` | 8 KB | ~3 KB | ❌ No WOW on dashboard |
| 24 | `waypoints.min.js` | 8 KB | ~3 KB | ❌ |
| 25 | `vanilla-lazyload.min.js` | 8 KB | ~3 KB | ❌ No lazy images |
| 26 | `bootstrap-notify.min.js` | 9 KB | ~3 KB | ⚠️ Depends on toastr |
| 27 | `toastr.min.js` | 5 KB | ~2 KB | ⚠️ Notifications |

**Total JS transferred:** ~1,141 KB (~393 KB gzipped)
**JS actually used on dashboard:** ~514 KB (~176 KB gzipped)
**Wasted JS:** ~627 KB (~217 KB gzipped) — 55% of total

---

## Font Transfer

| Font | Format | Size | Used |
|------|--------|------|------|
| Inter (400-800) | woff2 | ~100 KB | ✅ Primary UI font |
| IBM Plex Mono (400-700) | woff2 | ~30 KB | ✅ Code/charts |
| Font Awesome 5 | woff2 | ~120 KB | ✅ Icons |
| Font Awesome 6 | woff2 | ~300 KB | ⚠️ Also loaded (redundant?) |
| Flaticon | woff2 | ~90 KB | ⚠️ Minimal use |
| Simple Line Icons | woff2 | ~80 KB | ⚠️ Minimal use |

**Total fonts:** ~720 KB

---

## Summary

| Metric | Value |
|--------|-------|
| Total CSS | 881 KB (153 KB gzip) |
| Total JS | 1,141 KB (393 KB gzip) |
| Total Fonts | ~720 KB |
| **Total Transfer** | **~2,742 KB (~793 KB gzip)** |
| Wasted CSS | 222 KB (25%) |
| Wasted JS | 627 KB (55%) |
| Plugins loaded but unused | select2, DataTables, SweetAlert, Dropzone, Vue, tinymce, nice-select, tagsinput, wow, waypoints, lazyload |

**Note:** No se eliminan plugins en esta ola. Solo se documenta el coste.
