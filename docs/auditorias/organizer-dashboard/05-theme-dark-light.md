# 05-theme-dark-light.md — Dual Theme System Forensic Analysis

**Date:** 2026-08-21
**Page:** `/organizer/dashboard`

---

## Architecture

TukiPass has TWO independent theme mechanisms that must stay synchronized:

### Mechanism 1: `html[data-theme]` (localStorage)
- **Set by:** Inline `<script>` in `layout.blade.php:11-22` (runs BEFORE paint)
- **Persisted in:** `localStorage.getItem('tuki-theme')`
- **Values:** `"light"` | `"dark"`
- **Fallback:** `prefers-color-scheme` media query → defaults to `"light"`
- **Read by:** `theme-dark.css` (`html[data-theme="dark"]` selector), `chart-init.js` (`document.documentElement.dataset.theme`)

### Mechanism 2: `body[data-background-color]` (DB)
- **Set by:** `layout.blade.php:45` via Blade
- **Source:** `Auth::guard('organizer')->user()->theme_version`
- **Persisted in:** `organizers.theme_version` column (DB)
- **Values:** `"white"` | `"dark"`
- **Read by:** `atlantis.css` (`body[data-background-color="dark"]`), `admin-skin.css`, `admin-main.css`

---

## Synchronization Points

### On page load (layout.blade.php)
```html
<!-- Line 8: Default to light -->
<html lang="es" dir="ltr" data-theme="light">

<!-- Line 11-22: Override from localStorage BEFORE paint -->
<script>
  var saved = localStorage.getItem('tuki-theme');
  var theme = saved === 'dark' || saved === 'light' ? saved : ...;
  document.documentElement.dataset.theme = theme;
</script>

<!-- Line 45: Set body attribute from DB -->
<body data-background-color="{{ Auth::guard('organizer')->user()->theme_version == 'light' ? 'white' : 'dark' }}">
```

### On toggle (layout.blade.php:81-115)
```javascript
function applyTheme(theme, persist) {
  document.documentElement.dataset.theme = theme;  // Mechanism 1
  document.body.setAttribute('data-background-color', theme === 'dark' ? 'dark' : 'white');  // Mechanism 2
  if (persist) {
    localStorage.setItem('tuki-theme', theme);  // Persist Mechanism 1
  }
  // Note: Does NOT update DB (Mechanism 2 source)
}
```

### On theme change API call
```javascript
// POST /organizer/change-theme
// Updates organizers.theme_version in DB
// But does NOT update html[data-theme] or localStorage
```

---

## Desync Scenarios

### Scenario A: Toggle in browser → DB out of sync
1. User clicks theme toggle → `applyTheme('dark', true)`
2. `html[data-theme="dark"]` ✅
3. `body[data-background-color="dark"]` ✅
4. `localStorage.tuki-theme = 'dark'` ✅
5. `organizers.theme_version` = `'light'` ❌ (NOT updated by toggle)
6. **Result:** Next page load from DIFFERENT browser → DB says light, localStorage says dark → desync

### Scenario B: POST theme change → localStorage out of sync
1. Admin changes theme via POST `/organizer/change-theme`
2. `organizers.theme_version` = `'dark'` ✅
3. `localStorage.tuki-theme` = `'light'` ❌ (NOT updated by POST)
4. `html[data-theme]` = `'light'` ❌ (NOT updated by POST)
5. **Result:** Page loads with light, body attribute says dark → visual inconsistency

### Scenario C: New tab / incognito
1. localStorage is shared in same browser profile
2. New incognito window → no localStorage → falls back to `prefers-color-scheme`
3. DB `theme_version` is not read by the inline script
4. **Result:** Theme depends on OS preference, not user's saved choice

### Scenario D: FOUC (Flash of Unstyled Content)
1. `html` default is `data-theme="light"` (line 8)
2. Inline script runs (lines 11-22) → sets to dark if saved
3. Between line 8 and line 22: ~10ms where light theme flashes
4. **Mitigation:** Script is inline and runs before first paint, but not before CSS loads
5. **Result:** Possible FOUC if CSS loads before script executes

---

## Mapping Table

| Source | Attribute | Selector | Values |
|--------|-----------|----------|--------|
| localStorage | `html[data-theme]` | `html[data-theme="dark"]` | `"light"` / `"dark"` |
| DB | `body[data-background-color]` | `body[data-background-color="dark"]` | `"white"` / `"dark"` |

### CSS Responsibilities

| Selector | File | Controls |
|----------|------|----------|
| `html[data-theme="dark"]` | `theme-dark.css` | Token redefinition, component overrides |
| `body[data-background-color="dark"]` | `atlantis.css`, `admin-skin.css`, `admin-main.css` | Atlantis native dark, table colors, form colors |

---

## Test Matrix

| Scenario | `html[data-theme]` | `body[data-bg-color]` | `localStorage` | `DB` | Sync? |
|----------|-------------------|----------------------|----------------|------|-------|
| Fresh install, OS light | `light` | `white` | (empty) | `light` | ✅ |
| Fresh install, OS dark | `dark` | (depends on DB) | (empty) | `light` | ⚠️ Partial |
| Toggle dark, same browser | `dark` | `dark` | `dark` | `light` | ❌ DB desync |
| Toggle light, same browser | `light` | `white` | `light` | `dark` | ❌ DB desync |
| New browser, DB light | `light` | `white` | (empty) | `light` | ✅ |
| New browser, DB dark | `light` | `dark` | (empty) | `dark` | ❌ html desync |
| POST change-theme dark | `light` | `dark` | `light` | `dark` | ❌ html+localStorage desync |

---

## Confirmed Desync Cases

### CONFIRMED-1: Toggle does not persist to DB
- **Evidence:** `layout.blade.php:86-98` — `applyTheme()` updates `html[data-theme]`, `body[data-background-color]`, and `localStorage`, but does NOT call the API endpoint to update `organizers.theme_version`
- **Impact:** User toggles theme → looks correct → logs in from different device → theme reverts to DB value
- **Severity:** P2 (UX inconsistency, not a blocker)

### CONFIRMED-2: POST change-theme does not update DOM
- **Evidence:** The `change-theme` route calls `OrganizerController@changeTheme` which updates DB but doesn't update `html[data-theme]` or `localStorage`
- **Impact:** Admin changes theme via API → page still shows old theme until reload
- **Severity:** P3 (rare path)

### CONFIRMED-3: FOUC potential
- **Evidence:** `layout.blade.php:8` sets `data-theme="light"`, script at line 11 overrides it
- **Impact:** ~10ms flash of light theme before dark theme applies
- **Severity:** P3 (cosmetic, barely noticeable)

---

## Recommendations (for next wave, not this one)

1. **Unify the toggle:** Make `applyTheme()` also call `POST /organizer/change-theme` to persist to DB
2. **Or:** Remove DB dependency for theme, rely solely on localStorage + `prefers-color-scheme`
3. **FOUC mitigation:** Add `[data-theme="dark"] { background: #1c2433; }` as a blocking inline style in `<head>`

---

## Testing (when server is available)

| Test | Method | Expected |
|------|--------|----------|
| Toggle desync | Toggle → check DB | DB should update |
| API desync | POST change-theme → check DOM | DOM should update |
| FOUC | Screenshot at 100ms | No flash |
| Cross-browser | Toggle in Chrome → open Firefox | Should respect DB |
| Incognito | Open incognito → check theme | Should respect `prefers-color-scheme` |
