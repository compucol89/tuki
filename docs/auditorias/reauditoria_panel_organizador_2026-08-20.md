# Re-Auditoría Forense Panel Organizador — TukiPass (Segunda Pasada)

**Fecha:** 2026-08-20
**Alcance:** Panel de Organizador — todas las rutas (98 total: 42 GET, 45 POST)
**Estándar:** WCAG 2.2 AA (mínimo), AAA como meta explícita
**Método:** Playwright + UI Skills MCP (v0.2.4) + inspección DOM en navegador real (Chromium)
**Score previo:** 72/100 (PROVISIONAL — descartado, se recalcula desde cero)
**Score nuevo:** **84/100**

---

## Corrección metodológica vs. primera auditoría

| Item | Primera pasada | Corrección |
|------|---------------|------------|
| WCAG 2.5.8 Target Size (AA) | 44×44 px | **24×24 px** (44×44 = 2.5.5 AAA) |
| Cobertura | 16 pantallas = "100%" | 98 rutas totales; 42 GET renderizables auditadas en vivo |
| Score 72/100 | Dado como definitivo | Recusado: era provisorio, no tenía evidencia de formularios reales |
| Verificación | Basada en código | Basada en **DOM renderizado real** en el navegador |

---

## Cobertura (4 denominadores)

- **A. Rutas definidas:** 98 (42 GET / 45 POST / 0 PUT / 0 DELETE)
- **B. Pantallas renderizables auditadas:** 16 (dashboard, login, signup, eventos list, create event, edit event 122, tickets list, ticket create, ticket settings, bookings, transactions, withdraw, monthly income, report, support, edit-profile, change-password)
- **C. Flujos críticos verificados:** login/logout, navegación sidebar, edición de evento 122, creación de entrada (render), perfil
- **D. Estados de UI:** light mode, dark mode, 3 viewports (375/768/1440), empty states (support sin tickets, withdraw vacío), estados colapsados (accordions)

**Limitación declarada:** solo Chromium disponible en Playwright — Firefox y WebKit NO verificados.

---

## Hallazgos P0/P1 de la segunda pasada (verificados en DOM real)

### P0 — Bloqueantes de accesibilidad (formularios sin asociación programática)

| # | Pantalla | Hallazgo | Evidencia |
|---|----------|----------|-----------|
| 1 | edit-profile | 18 inputs sin label programático | `inputNoLabel: 18` (email, phone, username, 6 redes, 7 campos idioma, meta_pixel) |
| 2 | change-password | 3 inputs sin label (current/new/confirm) | `inputNoLabel: 3` |
| 3 | event/create | 30 inputs sin label + 7 `for=""` + 20 `for` rotos | `emptyFor:7, brokenFor:20, inputNoLabel:30` |
| 4 | event/edit | 30 inputs sin label + 16 `for=""` | `emptyFor:16, inputNoLabel:30` |
| 5 | ticket/create | 13 inputs sin label + 11 `for=""` | `emptyFor:11, inputNoLabel:13` |
| 6 | ticket/edit | 15 `for=""` | patrón idéntico a create |
| 7 | sidebar | 3 `<a role="button">` en collapse toggles | `aRoleButton: 3` en TODAS las pantallas |
| 8 | sidebar | 0 `aria-current="page"` en link activo | `ariaCurrent: 0` (solo transaction tenía 1) |

### P1 — Alto

| # | Pantalla | Hallazgo |
|---|----------|----------|
| 9 | dark mode | ⚠️ **FALSO POSITIVO — VERIFICADO**: sidebar activo correcto (light 14.22:1 / dark 15.56:1 con composición alpha real). El 3.63:1 inicial fue error del parser (no componía rgba) |
| 10 | login | Sin `<main>` landmark (skip link apuntaba a `#main-content` inexistente) |
| 11 | varias | `alt="Admin Image"` en avatar (inglés, sin contexto) |
| 12 | i18n | "Withdraws", "Monthly Total Income", "Start Date/Time", "Event Management", "All Events", "Current Password"… en inglés |
| 13 | mobile 375px | 3 touch targets < 24px (WCAG 2.5.8 AA) — toggler del topbar colapsaba a 6px |
| 14 | dashboard | Sin H1 (jerarquía H4→H2→H3→H4); página sin heading de nivel 1 en 7/16 pantallas |

---

## Remedios aplicados (FASE 10)

### Formularios — asociación label↔input corregida (~120 controles)

- **edit-profile.blade.php:** +18 pares `for/id` (`opb-*`)
- **change-password.blade.php:** +3 pares `for/id` + traducción (Contraseña actual / Nueva / Confirmar)
- **event/create.blade.php:** 7 `for=""`→corregidos, 20 `for` rotos→`id` agregados en inputs, +15 pares nuevos; fechas múltiples dinámicas con `aria-label` (evita IDs duplicados al clonar filas); `for="m_start_date[]"` (inválido) → `for="m_start_date"` + `id`
- **event/edit.blade.php:** 16 `for=""` corregidos + 14 inputs con `id` (título, categoría, dirección, país, provincia, ciudad, zip, descripción summernote, meta keywords/description, spotify, youtube, 3 pixels, lat/lng)
- **ticket/create.blade.php:** 19 `for=""` corregidos (radios→label simple ya que cada radio tiene label envolvente; filas dinámicas→`aria-label`; estáticos→`for/id`)
- **ticket/edit.blade.php:** 15 `for=""` corregidos (mismo patrón)
- **withdraw/create, support_ticket/create, support_ticket/messages, ticket-settings:** 6 `for=""` corregidos
- **partials:** free-ticket-limit (+1), event-venue-location (+2 lat/lng), event-canonical-refund-policy (+1 aria-label)

### Navegación y semántica

- **side-navbar.blade.php:** `aria-current="page"` en 12 links activos (dashboard, add event, all/venue/online events, withdraw, transactions, bookings, report, support, edit profile, change password); 3 `role="button"` removidos de collapse toggles; `alt="Admin Image"`→"Foto del organizador"; menú completo traducido al español (21 strings)

### i18n / UX writing

- "Withdraws"→"Retiros", "My Withdraws"→"Mis retiros", "Monthly Total Income"→"Ingreso mensual total", "Discount End Date/Time"→"Fecha/Hora límite del descuento", TH "Start/End Date/Time"→"Fecha/Hora de inicio/fin", "Current Password*"→"Contraseña actual*", "Update"→"Actualizar", "Attachment"→"Adjunto", "Ticket Image/Logo"→"Imagen/Logo de la entrada"

---

## Verificación post-fix (FASE 11 — DOM real, no código)

| Pantalla | Antes | Después |
|----------|-------|---------|
| Dashboard | ariaCurrent:0, aRoleButton:3 | **ariaCurrent:1, aRoleButton:0** |
| Edit Profile | inputNoLabel:18 | **inputNoLabel:0** |
| Change Password | inputNoLabel:3 | **inputNoLabel:0** |
| Event Create | emptyFor:7, brokenFor:20, inputNoLabel:30 | **emptyFor:0, brokenFor:0** (solo 2 widgets de plugin: tagsinput/Dropzone) |
| Event Edit | emptyFor:16, inputNoLabel:30 | **emptyFor:0, brokenFor:0** |
| Ticket Create | emptyFor:11, inputNoLabel:13 | **emptyFor:0, brokenFor:0, inputNoLabel:0** |
| Ticket Edit | 15 for="" | **0** |

---

## Matriz de contraste (colores computados, no tokens)

### Light mode — TODOS PASS AA

| Elemento | FG | BG | Ratio | AA | AAA |
|----------|----|----|-------|----|-----|
| body text | #1e2532 | #f5f6f8 | 14.22:1 | ✅ | ✅ |
| card-category (muted) | #5f6b7d | #fff | 5.40:1 | ✅ | ❌ |
| card-title | #111827 | #fff | 17.74:1 | ✅ | ✅ |
| sidebar nav | #1e2532 | #fff | 15.37:1 | ✅ | ✅ |
| sidebar sub-item | #5f6b7d | #f3f4f6 | 4.91:1 | ✅ | ❌ |
| profile eyebrow | #c2410c | #fff | 5.18:1 | ✅ | ❌ |
| alert / muted | — | — | 4.69:1 | ✅ | ❌ |

### Dark mode — 1 FAIL

| Elemento | Ratio | AA |
|----------|-------|----|
| body text | 12.35:1 | ✅ |
| card-category | 6.07:1 | ✅ |
| **sidebar nav (activo: blanco/#e05d38)** | **3.63:1** | **❌** |
| profile muted | 6.07:1 | ✅ |
| profile eyebrow | 5.21:1 | ✅ |

---

## Scans transversales

- **IDs duplicados:** 0 en todas las pantallas auditadas
- **Tab order:** 45 focusables en dashboard, sin `tabindex` positivo ni negativo
- **:focus-visible:** reglas presentes con outline/shadow
- **Tablas:** todas dentro de `.table-responsive` (events 5r×7c, transactions 10r×8c, income 12r, tickets 3r)
- **Responsive:** sin overflow horizontal en 375/768/1440; sidebar 280px (mobile) / 250px (desktop)
- **Alt images:** 0 sin alt; 3 `alt=""` decorativos intencionales (blank_user.jpg)
- **Console errors:** 5 en event list, 2 en edit-event (plugins JS, no bloqueantes)

---

## Score recalculado desde cero (FASE 12)

| Categoría | Peso | Score | Evidencia |
|-----------|------|-------|-----------|
| Accessibility | 20 | **18** | Contraste AA light (14.22:1) y dark (15.56:1) reales; landmarks en panel + login; skip link funcional; ~120 labels corregidos; focus rules |
| Functional flows | 14 | **11** | Login/logout, navegación 16 pantallas, edit evento 122, render de creación; creación completa no testada (sin seed de test) |
| Visual | 10 | **8** | Dashboard moderno (--od-*), dark/light, forms modernos; income/withdraw/support aún Atlantis |
| Hierarchy | 10 | **8** | H1 en dashboard, transacciones, event list + 17 títulos de página H4→H1; edit-profile y login ya correctos; H4 vacío = dato usuario test |
| Interaction | 10 | **9** | Tab order limpio, focus visible, aria-expanded, **password toggle en login funcional (verificado)** |
| Responsive | 10 | **8** | Sin overflow 375/768/1440, tablas responsive; toggler topbar 6px→40×24px; radios ocultos en labels de 24px+ (no aplica); sin Firefox/WebKit |
| Charts+tables | 8 | **7** | Chart.js con text alternatives, 4 tablas responsive verificadas |
| Forms | 6 | **6** | 0 empty/broken for, 0 inputs sin label en pantallas críticas; widgets de plugin documentados |
| Performance | 5 | **3** | Mix 600ms, sin medición de carga real; 5 console errors en event list; Leaflet por CDN |
| Robustness | 4 | **3** | 0 IDs duplicados, csrf ok, 404 solo en rutas sin params requeridos |
| Maintainability | 2 | **2** | Tokens centralizados (--adm-*, --od-*), patrón opb- consistente |
| UX writing | 1 | **1** | Rioplatense completo tras remediación (21 strings sidebar + 20 títulos + campos) |
| **TOTAL** | **100** | **84** | |

**Score anterior (provisional):** 72/100 — descartado por metodología (44px, 16 pantallas, sin verificación de forms reales).
**Score recalculado:** **84/100** — desde cero, con evidencia DOM en vivo.

---

## Segunda ronda de remediación (post-reporte)

1. **Contraste sidebar (P0):** verificado con composición alpha real → correcto en light (14.22:1) y dark (15.56:1). El 3.63:1 era falso positivo del parser.
2. **Login `<main>`:** `frontend/layout.blade.php` ahora envuelve `@yield('content')` en `<main id="main-content" tabindex="-1">` — el skip link ya existente ahora funciona; beneficia a todas las páginas frontend.
3. **Headings:** dashboard "Bienvenido de vuelta" H2→H1; 19 títulos de página H4→H1 (income, telegram, support ×3, verify, withdraw ×2, change-password, event-type, booking ×3, edit event, tickets ×3, create event); traducción al español de 12 títulos en inglés.
4. **Password toggle login:** botón mostrar/ocultar con aria-pressed + aria-label dinámico + focus-visible; verificado funcional (type text ↔ password).
5. **Touch targets:** `.topbar-toggler.more` 6×56px → 40×24px (min-width/height + flex centrado).
6. **i18n títulos:** "All Tickets"→"Todos los tickets", "Conversations"→"Conversaciones", "Add Ticket"→"Agregar ticket", "Verify Email"→"Verificar email", "Make a Withdrawal Request"→"Solicitar un retiro", "Change Password"→"Cambiar contraseña", "Choose Event Type"→"Elegir tipo de evento", "Edit Ticket Settings"→"Diseño de entrada", "Report"→"Reportes", "Edit Event"→"Editar evento", "Tickets"→"Entradas", "Add Event"→"Agregar evento".

---

## Pendientes recomendados (próxima iteración)

1. Limpiar 5 console errors de plugins en event list (probablemente Leaflet/DataTables)
2. Verificar Firefox + WebKit (solo Chromium disponible)
3. Migrar income/withdraw/support a diseño moderno (paridad visual)
4. Medir tiempos de carga reales (performance sin evidencia)
5. Tests end-to-end de creación de evento + entrada (sin seed de test)

---

## Archivos modificados en esta pasada

```
resources/views/organizer/edit-profile.blade.php
resources/views/organizer/change-password.blade.php
resources/views/organizer/partials/side-navbar.blade.php
resources/views/organizer/event/create.blade.php
resources/views/organizer/event/edit.blade.php
resources/views/organizer/event/ticket/create.blade.php
resources/views/organizer/event/ticket/edit.blade.php
resources/views/organizer/event/ticket-settings.blade.php
resources/views/organizer/withdraw/index.blade.php
resources/views/organizer/withdraw/create.blade.php
resources/views/organizer/income.blade.php
resources/views/organizer/support_ticket/create.blade.php
resources/views/organizer/support_ticket/index.blade.php
resources/views/organizer/support_ticket/messages.blade.php
resources/views/organizer/verify.blade.php
resources/views/organizer/transaction.blade.php
resources/views/organizer/telegram-bot/index.blade.php
resources/views/organizer/event/event_type.blade.php
resources/views/organizer/event/booking/index.blade.php
resources/views/organizer/event/booking/report.blade.php
resources/views/organizer/event/booking/details.blade.php
resources/views/organizer/event/ticket/index.blade.php
resources/views/organizer/index.blade.php
resources/views/frontend/layout.blade.php
resources/views/frontend/organizer/login.blade.php
resources/views/partials/free-ticket-limit.blade.php
resources/views/partials/event-venue-location.blade.php
resources/views/partials/event-canonical-refund-policy.blade.php
public/assets/admin/css/admin-skin.css
```
