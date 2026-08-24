# TukiPass Event Detail 122 - Design-System Forensic Audit

Target: `/colombia-vs-suiza-por-gol-caracol/122`
Runtime: `http://127.0.0.1:8801` with production image fallback for missing local media
Date: 2026-08-23
Branch: `master`

## A. Repo And Runtime Anchor

- Workspace: `/Users/compucolargentina/Documents/www/tuki`
- Edited source: `public/assets/front/css/event-detail.css`
- Generated source: `public/assets/front/css/event-detail.min.css`
- Focused tests added: `tests/playwright/event-detail-design-system.spec.js`
- Evidence collector: `collect-design-system-audit.cjs`
- Local CSS served parity after remediation: `866722df016cdf2d98c6ddfa17b4999b` local = served.

## B. Request Boundary

This audit treated the pasted master prompt as the executable request and the design-system analysis as supporting evidence. The Google Search docs provided by the user were treated as SEO references only and were not staged or modified.

No checkout controller, payment gateway, stock calculation, hidden checkout fields, ticket quantities, `#total`, `#total_price`, or `recalcTotal()` were changed.

## C. Source Inventory

- Route: `/{slug}/{id}` -> `FrontEnd\EventController@details`
- View: `resources/views/frontend/event/event-details.blade.php`
- Page CSS: `public/assets/front/css/event-detail.css`
- Minified CSS: `public/assets/front/css/event-detail.min.css`
- Layout schema mount: `resources/views/frontend/layout.blade.php`
- Inline page behavior remains in Blade; this change avoided adding a stronger inline CSS layer.

## D. Event And Data State

The event is finished. Public state surfaces continue to show `Finalizado`, `Evento finalizado`, and `Venta finalizada`; active-sale copy is absent from commerce surfaces.

The local DB description now renders `Martes 7 de julio`; `Lunes 7 de julio` is absent. One editorial WYSIWYG phrase, `Reserva tu entrada ahora`, still exists inside the organizer-authored description. That is a content correction, not a CSS/system-token issue.

## E. Token Graph

Global scale comes from `style.css`: `--brand-primary -> --primary`, `--radius-*`, `--shadow-*`, `--tuki-space-*`, and font tokens.

Event detail bridge now adds semantic event roles:

- Radius: `--ed-radius-lg: var(--radius-lg)`, `--ed-radius-panel: 22px`, `--ed-radius-media: 20px`
- Lines: `--ed-line-control`, `--ed-line-hairline`
- Status: `--ed-success-bright`, `--ed-status-over-on-dark`, `--ed-state-closed-*`
- Disabled CTA: `--ed-disabled-*`
- Shadows: `--ed-shadow-panel`, `--ed-shadow-panel-strong`, `--ed-shadow-cover`, `--ed-shadow-media-hero`

The measured contradiction is resolved by naming the real roles: `--ed-radius-lg` is 16px, while the commerce panel semantic token is 22px.

## F. Color Semantics

- `#e05d38`: global brand primary. It remains a global accessibility risk only when white 14px text sits on it; not introduced by this change.
- `#0f172a`: legacy dark emphasis in older event CSS blocks; not the active sampled text color on the audited page.
- `#667085`: legacy muted copy in older CSS blocks.
- `#16a34a`: active success/trust signal, now expressed as `--ed-success-bright` in the winning event-detail layer.
- `#991b1b`, `#fff1f2`, `#fecaca`: finished/sold-out state roles, now expressed through `--ed-state-closed-*`.

## G. Radius And Shadow Authority

Before: desktop panel computed `22px` because a later literal won the cascade, while `--ed-radius-lg` computed `16px`.

After: desktop panel still computes `22px`, but now through `--ed-radius-panel`. Mobile below 992px intentionally flattens the commerce card to `0px`.

Computed after:

- `1440x900`: card radius `22px`, shadow `rgba(30, 37, 50, 0.12) 0px 18px 44px`
- `991x768` and below: card radius `0px`

## H. Typography

Runtime sampled components use Inter:

- Body, hero, title, commerce card, ticket rows, CTA, and description all compute Inter.
- `--tuki-font-data` / IBM Plex Mono exists globally for data/admin contexts, but is not used by the sampled public event-detail UI.
- Micro text remains in the 10.72px to 15px range where badges and compact controls require it; no negative letter spacing was introduced.

## I. Density And Rhythm

No visible layout redesign was performed. The change consolidates literals into tokens while preserving the current cinematic dark hero plus light commerce rail.

Spacing systems found:

- Global `--tuki-space-*`: 4, 8, 12, 16, 20, 24, 32, 40, 48
- Event legacy `--ed-space-*`: 4, 8, 12, 16, 20, 24, 32, 40, 48, 64

## J. Responsive And Sticky Behavior

Breakpoints observed in the active CSS include `1199.98`, `991.98`, `992`, `767.98`, `575.98`, `576`, `360`, plus older legacy `991` and `575` blocks.

After matrix:

- `1440x900`, `1280x800`, `1024x768`, `992x768`: no overflow, panel radius `22px`
- `991x768`, `768x1024`, `576x900`, `575x900`, `390x844`, `375x812`, `320x568`: no overflow, mobile panel radius `0px`
- Reflow at `640 CSS px`: no overflow
- Long-content stress at `320x568`: no overflow

## K. CSS Coverage And Cascade

Event CSS coverage:

- Before: `128527 / 227354` bytes, `56.53%`
- After: `129685 / 228782` bytes, `56.68%`

Legacy literals remain before the final event-detail bridge. They are documented debt, not newly introduced active behavior. This remediation only changed the winning layer needed for this public page.

## L. Lifecycle And Conversion

Finished lifecycle remains the highest-priority public state:

- Active-sale copy absent from hero, commerce card, quick facts, and mobile bar.
- CTA is disabled and exposes `aria-disabled="true"`.
- `FREE PASS` remains price meaning; `Agotadas` remains availability/status.
- Synthetic urgency/social-proof language remains suppressed for this finished event.

## M. SEO And Structured Data

Meta description, OG description, and Twitter description all describe the event as finished and the sale as closed.

The page intentionally does not emit `Event` JSON-LD for past events. Runtime structured data after remediation contains:

- `Organization`
- `WebSite`
- `BreadcrumbList`

No `Event` schema with active `offers` is emitted for this finished event.

## N. Accessibility

Focused event-detail Axe result: 0 violations across the audited viewport matrix.

Keyboard/CTA contract is covered by Playwright: disabled mobile CTA keeps `aria-disabled="true"`, and the finished purchase area is non-conversional.

Global a11y subset still fails outside this page on 5 routes due white text over `#e05d38` at 3.62:1:

- `/eventos`
- `/blog`
- `/contacto`
- `/sobre-nosotros`
- `/organizadores`

That is a pre-existing global contrast issue and remains outside this scoped event-detail remediation.

## O. Active Event Regression

The active event regression `/reggaeton-old-school/123` still preserves visible buy button, enabled quantity control when stock exists, and subtotal recalculation.

## P. Evidence Files

- Before JSON: `before/design-system-audit.json`
- Before screenshots: `before/screenshots/*.png`
- After JSON: `after/design-system-audit.json`
- After screenshots: `after/screenshots/*.png`
- Focused spec: `tests/playwright/event-detail-design-system.spec.js`

## Q. Verification And Certification

Verification:

- `node scripts/build-front-assets.js`: passed
- `npx playwright test tests/playwright/event-detail-forensic.spec.js tests/playwright/event-detail-design-system.spec.js`: 9 passed
- `npm run test:seo`: 4 passed
- `npm run test:theme`: 5 passed, 28 skipped
- `node .agents/skills/impeccable/scripts/detect.mjs --json --scope layout ...`: `[]`
- `git diff --check`: passed
- `npm run test:a11y`: 11 passed, 10 skipped, 5 failed on global `#e05d38` contrast outside this ficha

| Area | Verdict |
| --- | --- |
| Geometry | PASS |
| Density | PASS |
| Rhythm | PASS |
| Public parity | PASS |
| Conversion integrity | PASS |
| Mobile | PASS |
| Accessibility, event detail | PASS |
| Accessibility, global suite | NOT CERTIFIED |
| Data integrity | PASS local; production WYSIWYG copy still needs admin review |
| Global readiness | NOT CERTIFIED until global contrast debt is fixed |

Final status: event-detail page certified for scoped remediation; global site certification withheld because a P1 contrast issue remains outside this page.
