# === SYSTEM: TOKEN-OPTIMIZED EXECUTION ===

You are a precision execution agent for **TukiPass** — a Laravel SaaS for event management and ticket sales.

Goal: Solve tasks with maximum accuracy and minimum token usage.

---

# === STACK ===

Laravel 12 · PHP 8.2+ · MySQL · Eloquent · Blade · Bootstrap 4 · jQuery · Laravel Mix 6  
Auth: Socialite (Google, Facebook) + Custom Guards (admin/organizer/customer)  
Payments: Stripe · MercadoPago · PayPal + 16 gateways  
Frontend: Inter font · CSS custom (no Tailwind) · Modern SaaS design

---

# === PROJECT STRUCTURE ===

```
app/Http/Controllers/FrontEnd/     ← Public frontend (customers)
app/Http/Controllers/BackEnd/      ← Admin + Organizer panels
app/Http/Helpers/Helper.php        ⚠️ LARGE FILE — search before reading
app/Models/                        ← No User.php — use Admin/Organizer/Customer
app/Models/Event/                  ← Event · EventContent · Ticket · Booking · Coupon
app/Models/ShopManagement/         ← Product · ProductOrder
app/Models/PaymentGateway/         ← Payment logic

resources/views/frontend/          ← Public views (Modern SaaS UI)
resources/views/organizer/         ← Organizer panel (Atlantis theme)
resources/views/backend/           ← Admin panel (Atlantis theme)

routes/web.php                     ← Entry point, includes sub-files
routes/frontend_*.php              ← auth · events · customer · payments · shop · pages
routes/admin.php                   ← Admin routes
routes/organizer*.php              ← Organizer routes
```

---

# === AUTH — THREE GUARDS (NO User.php) ===

| Guard | Model | Prefix | Middleware |
|-------|-------|--------|------------|
| `admin` | `App\Models\Admin` | `/admin/*` | `auth:admin` |
| `organizer` | `App\Models\Organizer` | `/organizer/*` | `auth:organizer` |
| `customer` | `App\Models\Customer` | `/customer/*` | `auth:customer` |

RBAC admin: `permission:Event Management`  
Socialite: `auth.google` · `auth.facebook`

---

# === ASSETS & OPTIMIZATION ===

**CSS/JS Loading Strategy (Tanda 3B completed):**

Global assets (`resources/views/frontend/partials/`):
- `styles.blade.php` — Global CSS (no page-specific CSS here)
- `scripts.blade.php` — Global JS (slick, magnific-popup, script.js)

Conditional assets (use `@push('styles')` / `@push('scripts')`):
| Asset | Pages requiring it |
|-------|-------------------|
| `slick.css` | `shop/details.blade.php` |
| `magnific-popup.min.css` | `shop/details.blade.php` |
| `daterangepicker.css` | `shop/index.blade.php`, `event/event.blade.php` |
| `organizer.css` | `organizer/details.blade.php`, `organizer/signup.blade.php`, `about.blade.php` |
| `cart.js` | `shop/details.blade.php`, `shop/index.blade.php` |

**Critical:** Plugins (`slick.min.js`, `jquery.magnific-popup.min.js`) must load BEFORE `script.js` in global scripts.

**CSS Cascade:** `style.css` → `styles.blade.php` → `@section('custom-style')` in blade  
Use `body.page-name .class` for page-scoped styles.

**Typography:** Single font — Inter. Use `.summernote-content` for WYSIWYG. Variables: `--flow-space: 1lh`

---

# === DESIGN SYSTEM ===

Colors:
- Primary orange: `#F97316`
- Dark gray: `#1e2532`
- Font: Inter (via `@fontsource/inter`)

Modern SaaS UI applied to:
- ✅ Home, Events, Event detail, Checkout
- ✅ Customer login/signup/dashboard
- ✅ Organizer login/signup
- ✅ About, FAQs, Contact, Blog

Still on Atlantis theme: Admin panel, Organizer panel

---

# === FORBIDDEN ZONES ⚠️ ===

**NEVER modify without explicit analysis:**
```
FrontEnd\CheckOutController@checkout2      POST /check-out2
FrontEnd\Event\BookingController
FrontEnd\PaymentGateway\*Controller
config/auth.php (guards)
```

**HTML fields NEVER touch:**
```
name="event_id"   name="pricing_type"   name="quantity"   name="quantity[]"
name="date_type"  name="event_date"     data-price        data-stock
data-ticket_id    #total_price          #total            recalcTotal()
```

**Protected paths:** vendor/ node_modules/ storage/ logs/ cache/ build/ dist/ .git/ .env

---

# === FILE READING RULES ===

- Search FIRST (`rg` / `grep`)
- Read ONLY required lines or functions
- Max 1–2 files per task
- Max 300 lines per read
- Helper.php: ALWAYS search function before reading

---

# === EXECUTION MODE ===

Default:
- Code only
- No explanations
- No comments
- No refactors
- No assumptions

If explanation requested: Max 3 lines

Workflow:
1. Identify exact target
2. Search minimal context
3. Apply smallest fix
4. Stop immediately

---

# === EDITING RULES ===

- Modify ONLY what is requested
- Do NOT touch unrelated code
- Do NOT reformat
- Do NOT rename
- Do NOT add abstractions
- Prefer minimal diff

---

# === COMMANDS ===

```bash
# Search (always first)
rg "function_name" app/                      # Find before reading
grep -rn "class_name" resources/views/       # Search in views

# Assets
npm run dev                                    # Compile assets
npm run watch                                  # Watch mode
npm run production                             # Build for prod

# Laravel
php artisan view:clear                         # Clear view cache
php artisan route:list                         # List routes (needs DB)
php artisan migrate                            # Run migrations
php artisan db:seed                            # Run seeders

# Git
git status --short                             # Quick status
```

---

# === SKILLS AVAILABLE ===

Skills are in `.claude/skills/` — read SKILL.md before using:

| Skill | Use when... |
|-------|-------------|
| `laravel-patterns` | Architecture, Eloquent, services, queues |
| `laravel-specialist` | Models, auth, Livewire, testing |
| `php-pro` | Modern PHP 8.3+, strict typing, patterns |
| `frontend-design` | UI components, pages, HTML/CSS |
| `accessibility` | WCAG compliance, a11y audits |
| `seo` | Meta tags, structured data, sitemaps |
| `copywriting` | Marketing copy, headlines, CTAs |
| `test-driven-development` | Writing tests first |
| `systematic-debugging` | Debugging failures |
| `verification-before-completion` | Before claiming done |

---

# === PROJECT REFERENCES ===

| File | Purpose |
|------|---------|
| `AGENTS.md` | This file — execution rules |
| `CLAUDE.md` | Technical deep-dive (guards, checkout zones, CSS classes) |
| `PENDIENTES.md` | Active tasks and backlog |
| `.claude/PROJECT_MEMORY.md` | Cross-session memory |
| `INFORME.md` | Complete technical map (if exists) |

---

# === I18N — SPANISH (ARGENTINA) ===

All customer-facing text must be in **español rioplatense**:
- "Comprar" → "Reservar"
- "Ticket" → "Entrada"
- "Sign up" → "Crear cuenta"
- "Log in" → "Iniciar sesión"

Translation files: `resources/lang/es.json`

---

# === SESSION MANAGEMENT ===

- Run `/cost` when session grows long to monitor cache ratio
- Start new session when switching to unrelated task
- Check `PENDIENTES.md` at start of session for context

---

# === OBJECTIVE ===

Maximum precision · Minimum tokens · Zero unnecessary exploration

<!-- autoskills:start -->

## Accessibility (a11y)

Audit and improve web accessibility following WCAG 2.2 guidelines. Use when asked to "improve accessibility", "a11y audit", "WCAG compliance", "screen reader support", "keyboard navigation", or "make accessible".

- `.claude/skills/accessibility/SKILL.md`
- `.claude/skills/accessibility/references/A11Y-PATTERNS.md`: Practical patterns for common accessibility requirements.
- `.claude/skills/accessibility/references/WCAG.md`

## Copywriting

When the user wants to write, rewrite, or improve marketing copy for any page. Use whenever someone is working on website text that needs to persuade or convert.

- `.claude/skills/copywriting/SKILL.md`
- `.claude/skills/copywriting/references/copy-frameworks.md`: Headline formulas and page templates.
- `.claude/skills/copywriting/references/natural-transitions.md`: Transitional phrases for readability.

## Find Skills

Helps users discover and install agent skills when they ask questions like "how do I do X", "find a skill for X", "is there a skill that can..."

- `.claude/skills/find-skills/SKILL.md`

## Frontend Design

Create distinctive, production-grade frontend interfaces with high design quality. Use when building web components, pages, landing pages, dashboards.

- `.claude/skills/frontend-design/SKILL.md`

## Laravel Patterns

Laravel architecture patterns, routing/controllers, Eloquent ORM, service layers, queues, events, caching, and API resources for production apps.

- `.claude/skills/laravel-patterns/SKILL.md`

## Laravel Specialist

Build and configure Laravel 10+ applications, including Eloquent models, Sanctum auth, Horizon queues, API resources, Livewire components.

- `.claude/skills/laravel-specialist/SKILL.md`
- `.claude/skills/laravel-specialist/references/eloquent.md`
- `.claude/skills/laravel-specialist/references/routing.md`
- `.claude/skills/laravel-specialist/references/testing.md`

## Marketing Ideas

When the user needs marketing ideas, inspiration, or strategies for their SaaS or software product.

- `.claude/skills/marketing-ideas/SKILL.md`
- `.claude/skills/marketing-ideas/references/ideas-by-category.md`: Proven marketing approaches by category.

## PHP Pro

Use when building PHP applications with modern PHP 8.3+ features, Laravel, or Symfony. Strict typing, PHPStan level 9, PSR standards.

- `.claude/skills/php-pro/SKILL.md`
- `.claude/skills/php-pro/references/modern-php-features.md`
- `.claude/skills/php-pro/references/laravel-patterns.md`
- `.claude/skills/php-pro/references/testing-quality.md`

## Sales Enablement

When the user wants to create sales collateral, pitch decks, one-pagers, objection handling docs, or demo scripts.

- `.claude/skills/sales-enablement/SKILL.md`
- `.claude/skills/sales-enablement/references/deck-frameworks.md`
- `.claude/skills/sales-enablement/references/one-pager-templates.md`
- `.claude/skills/sales-enablement/references/objection-library.md`

## SEO

Optimize for search engine visibility and ranking. Use when asked to "improve SEO", "optimize for search", "fix meta tags", "add structured data", "sitemap optimization".

- `.claude/skills/seo/SKILL.md`

## Systematic Debugging

Use when encountering any bug, test failure, or unexpected behavior, before proposing fixes.

- `.claude/skills/systematic-debugging/SKILL.md`

## Test-Driven Development

Use when implementing any feature or bugfix, before writing implementation code.

- `.claude/skills/test-driven-development/SKILL.md`

## UI Design System

React UI component systems with TailwindCSS + Radix + shadcn/ui. (Note: This project uses custom CSS, not Tailwind)

- `.claude/skills/ui-design-system/SKILL.md`

## UI/UX Pro Max

UI/UX design intelligence for web and mobile. 50+ styles, 161 color palettes, 57 font pairings, 99 UX guidelines.

- `.claude/skills/ui-ux-pro-max/SKILL.md`

## Verification Before Completion

Use when about to claim work is complete, fixed, or passing, before committing or creating PRs.

- `.claude/skills/verification-before-completion/SKILL.md`

<!-- autoskills:end -->
