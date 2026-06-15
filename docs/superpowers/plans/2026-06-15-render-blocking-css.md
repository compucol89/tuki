# Render Blocking CSS Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Reduce the homepage render-blocking CSS warning without introducing CLS or breaking the header, hero, mobile drawer, or search area.

**Architecture:** Keep the current Blade/CSS architecture and make the smallest reversible change. Add critical font/home CSS inline for the homepage path, defer only CSS files that keep CLS at `0`, then verify with visual smoke tests and Lighthouse before committing.

**Tech Stack:** Laravel Blade, custom CSS, Bootstrap 4 markup, PHP built-in server in Docker, Lighthouse CLI, Playwright via system Chrome.

---

### Task 1: Confirm Critical CSS Coverage

**Files:**
- Read: `resources/views/frontend/partials/header/header-nav.blade.php`
- Read: `resources/views/frontend/partials/styles.blade.php`
- Read: `resources/views/frontend/home/index-v1.blade.php`
- Read: `public/assets/front/css/menu.css`
- Read: `public/assets/front/css/style.css`
- Read: `public/assets/front/css/responsive.css`

- [x] **Step 1: Identify root cause**

Run:
```bash
rg -n "main-header|header-upper|header-inner|main-menu|navigation|menu-right|mobile-menu|hero-collage|hs-search" public/assets/front/css/menu.css public/assets/front/css/style.css public/assets/front/css/responsive.css
```

Expected: header critical rules live mainly in `menu.css` and `style.css`; hero/search critical rules are already in `home/index-v1.blade.php`.

- [x] **Step 2: Define safe hypothesis**

Only defer global CSS if critical CSS includes header/nav/mobile drawer and font-face coverage. Reject the change if Lighthouse reports CLS above `0`.

### Task 2: Implement Reversible CSS Deferral

**Files:**
- Modify: `resources/views/frontend/partials/styles.blade.php`

- [x] **Step 1: Add homepage detection**

Add a local Blade variable based on `@section('body-class', 'home-page')`.

- [x] **Step 2: Inline critical Inter latin font faces**

Inline only the latin weights used above the fold: `400`, `500`, `600`, `700`, `800`, matching the hashes from `public/css/app.css`.

- [x] **Step 3: Add critical home layout/search CSS**

Inline the homepage layout/search selectors needed for stable above-the-fold rendering.

- [x] **Step 4: Defer homepage-only safe global CSS**

For home only, load `app.css`, `menu.css`, and `responsive.css` with `media="print" onload="this.media='all'"` plus `noscript` fallbacks. Leave other pages unchanged.

- [x] **Step 5: Reject unsafe `style.css` deferral**

Two Lighthouse runs with `style.css` deferred removed the render-blocking insight but introduced CLS (`0.201` and `0.245`). Keep `style.css` synchronous until a fuller stylesheet split can be done safely.

### Task 3: Verify Before Commit

**Files:**
- Verify: `resources/views/frontend/partials/styles.blade.php`

- [x] **Step 1: Syntax and Blade smoke**

Run:
```bash
docker compose exec -T app php -l resources/views/frontend/partials/styles.blade.php
docker compose exec -T app php artisan view:clear
curl -sI http://localhost:8801/
```

Expected: syntax OK, view clear OK, home returns `200`.

- [x] **Step 2: Visual smoke**

Use Playwright with system Chrome at desktop `1440x900`, mobile `390x844`, and `/eventos` mobile. Reject if console errors, horizontal overflow, broken header, missing hero, or broken menu layout appear.

- [x] **Step 3: Lighthouse local**

Run:
```bash
npx lighthouse http://localhost:8801/ --output=html --output=json --output-path="resultados search console/local-render-blocking-css/lighthouse.report" --chrome-flags="--headless=new --no-sandbox" --quiet
```

Result: `CLS` remains `0`; Performance local is `68`; legacy render-blocking list is empty; new render-blocking insight retains only `style.css`.

- [x] **Step 4: Commit only if accepted**

Run:
```bash
git add resources/views/frontend/partials/styles.blade.php
git commit -m "perf: defer homepage global css safely"
git push origin master
```
