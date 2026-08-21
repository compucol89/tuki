# 00-scope.md — Organizer Dashboard Forensic Audit Scope

**Date:** 2026-08-21
**Branch:** `remediation/audit-2026-08-21`
**HEAD:** `93466da9eccee9449847bef65ca1b09f8362b0f4`

---

## Superficie de esta ola (Fases 0–2)

### Página target
`/organizer/dashboard` — `OrganizerController@index` → `organizer.index`

### Partials compartidos (cargados vía layout)
| Partial | Archivo | Responsabilidad |
|---------|---------|-----------------|
| Layout master | `organizer/layout.blade.php` | HTML shell, theme init, skip-link, wrapper |
| Styles | `organizer/partials/styles.blade.php` | 16 CSS files (order documentado) |
| Scripts | `organizer/partials/scripts.blade.php` | 25+ JS files (order documentado) |
| Side navbar | `organizer/partials/side-navbar.blade.php` | Menú colapsable, búsqueda, localStorage |
| Top navbar | `organizer/partials/top-navbar.blade.php` | Logo, hamburger, theme toggle, perfil |
| Footer | `organizer/partials/footer.blade.php` | Footer del panel |
| Async progress | `organizer/partials/async-progress.blade.php` | Panel de progreso asíncrono |

### CSS archivos involucrados (solo los que carga dashboard)
| Archivo | Tamaño | Rol |
|---------|--------|-----|
| `bootstrap.min.css` | 152 KB | Base grid/utilities |
| `atlantis.css` | 329 KB | Theme principal |
| `admin-skin.css` | 64 KB | Tokens Tuki + overrides |
| `admin-main.css` | 18 KB | Overrides específicos |
| `theme-dark.css` | 41 KB | Dark mode |
| `responsive.css` | 59 KB | Responsive overrides |
| `fonts.min.css` | 66 KB | Icon fonts |
| `animate.min.css` | 55 KB | Animations |
| `event-form-modern.css` | 37 KB | Event form (loaded but not used on dashboard) |

### JS archivos involucrados
| Archivo | Tamaño | Usado en dashboard |
|---------|--------|-------------------|
| `jquery.min.js` | 118 KB | ✅ |
| `bootstrap.min.js` | 57 KB | ✅ |
| `chart.min.js` | 156 KB | ✅ (4 charts) |
| `chart-init.js` | 3 KB | ✅ (custom) |
| `admin-main.js` | 33 KB | ✅ |
| `admin-partial.js` | 19 KB | ✅ |
| `atlantis.js` | 9 KB | ✅ |
| `select2.min.js` | 74 KB | ❌ (no select2 on dashboard) |
| `datatables-1.10.23.min.js` | 85 KB | ❌ (no table on dashboard) |
| `sweetalert.min.js` | 40 KB | ❌ (no sweetalert on dashboard) |
| `dropzone.min.js` | 41 KB | ❌ (no upload on dashboard) |
| `vue-js.min.js` | 114 KB | ❌ (no vue on dashboard) |
| `tinymce/*` | ~1 MB | ❌ (no editor on dashboard) |

---

## 68 rutas organizer — Clasificación

### DIRECT — Cargan dashboard o sus partials
| # | Método | URI | Nombre | Controller |
|---|--------|-----|--------|------------|
| 1 | GET | `/organizer/dashboard` | `organizer.dashboard` | `OrganizerController@index` |

### SHARED — Usan el mismo layout pero no son el dashboard
| # | Método | URI | Nombre | Controller |
|---|--------|-----|--------|------------|
| 2 | GET | `/organizer/profile` | — | `OrganizerController@editProfile` |
| 3 | POST | `/organizer/profile` | — | `OrganizerController@updateProfile` |
| 4 | GET | `/organizer/password` | — | `OrganizerController@changePassword` |
| 5 | POST | `/organizer/password` | — | `OrganizerController@updatePassword` |
| 6 | GET | `/organizer/events` | `organizer.events.index` | `Organizer\EventController@index` |
| 7 | GET | `/organizer/events/create` | — | `Organizer\EventController@create` |
| 8 | POST | `/organizer/events` | — | `Organizer\EventController@store` |
| 9 | GET | `/organizer/events/{id}/edit` | — | `Organizer\EventController@edit` |
| 10 | PUT | `/organizer/events/{id}` | — | `Organizer\EventController@update` |
| 11 | GET | `/organizer/events/{id}/tickets` | — | `Organizer\TicketController@index` |
| 12 | POST | `/organizer/events/{id}/tickets` | — | `Organizer\TicketController@store` |
| 13 | GET | `/organizer/events/{id}/tickets/{tid}/edit` | — | `Organizer\TicketController@edit` |
| 14 | PUT | `/organizer/events/{id}/tickets/{tid}` | — | `Organizer\TicketController@update` |
| 15 | GET | `/organizer/events/{id}/bookings` | — | `Organizer\BookingController@index` |
| 16 | GET | `/organizer/events/{id}/bookings/{bid}` | — | `Organizer\BookingController@show` |
| 17 | GET | `/organizer/events/{id}/bookings/report` | — | `Organizer\BookingController@report` |
| 18 | GET | `/organizer/transactions` | — | `OrganizerController@transaction` |
| 19 | GET | `/organizer/income` | — | `OrganizerController@income` |
| 20 | GET | `/organizer/withdraw` | — | `OrganizerController@withdrawIndex` |
| 21 | GET | `/organizer/withdraw/create` | — | `OrganizerController@withdrawCreate` |
| 22 | POST | `/organizer/withdraw` | — | `OrganizerController@withdrawStore` |
| 23 | GET | `/organizer/support` | — | `OrganizerController@supportIndex` |
| 24 | GET | `/organizer/support/create` | — | `OrganizerController@supportCreate` |
| 25 | POST | `/organizer/support` | — | `OrganizerController@supportStore` |
| 26 | GET | `/organizer/support/{id}` | — | `OrganizerController@supportShow` |
| 27 | POST | `/organizer/support/{id}/reply` | — | `OrganizerController@supportReply` |
| 28 | GET | `/organizer/verify` | — | `OrganizerController@verify` |
| 29 | POST | `/organizer/verify/resend` | — | `OrganizerController@resendVerification` |
| 30 | GET | `/organizer/telegram` | — | `OrganizerController@telegramIndex` |
| 31 | POST | `/organizer/telegram/link` | — | `OrganizerController@telegramLink` |

### AUTH — No usan dashboard layout
| # | Método | URI | Nombre | Controller |
|---|--------|-----|--------|------------|
| 32 | GET | `/organizer/login` | — | `Organizer\AuthController@showLogin` |
| 33 | POST | `/organizer/login` | — | `Organizer\AuthController@login` |
| 34 | GET | `/organizer/signup` | — | `Organizer\AuthController@showSignup` |
| 35 | POST | `/organizer/signup` | — | `Organizer\AuthController@signup` |
| 36 | POST | `/organizer/logout` | — | `Organizer\AuthController@logout` |
| 37 | GET | `/organizer/forgot-password` | — | `Organizer\AuthController@showForgotPassword` |
| 38 | POST | `/organizer/forgot-password` | — | `Organizer\AuthController@sendResetLink` |
| 39 | GET | `/organizer/reset-password/{token}` | — | `Organizer\AuthController@showResetPassword` |
| 40 | POST | `/organizer/reset-password` | — | `Organizer\AuthController@resetPassword` |

### API/AJAX — No renderizan vista
| # | Método | URI | Nombre | Controller |
|---|--------|-----|--------|------------|
| 41+ | POST/GET | `/organizer/events/{id}/tickets/*` | — | Various (API) |
| 42+ | POST | `/organizer/events/{id}/bookings/*` | — | Various (API) |

---

## Criterio de cierre

El **master prompt de 143 secciones** es la rúbrica completa. Esta ola (Fases 0–2) cubre:
- Secciones 0–6 del master prompt (scope, stack, routes, baseline, CSS cascade, theme)
- NO cierra WCAG AA
- NO crea informes 06–20
- NO declara el dashboard "auditoría completa"

## Entregables de esta ola
- `00-scope.md` ← este archivo
- `01-stack-runtime.md`
- `02-route-surface.md`
- `03-baseline.md`
- `04-css-cascade.md`
- `05-theme-dark-light.md`
- `issues.csv`
- `worklog.md`
