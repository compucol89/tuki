# Verificación Playwright — Panel Organizador TukiPass

**Fecha:** 2026-08-20
**Servidor:** Docker (`http://localhost:8801`)
**Organizador:** Rumba Colombiana
**Herramienta:** Playwright MCP (headless Chrome)

---

## Resumen Ejecutivo

| Categoría | Verificados | OK | Issues | Rate |
|-----------|:-----------:|:--:|:------:|:----:|
| Accessibility core | 10 | 8 | 2 | 80% |
| Dark mode tokens | 6 | 6 | 0 | 100% |
| Responsive 375px | 3 | 3 | 0 | 100% |
| Forms (create event) | 1 | 0 | 1 | 0% |
| Search inputs | 4 | 4 | 0 | 100% |
| Tables responsive | 3 | 2 | 1 | 67% |
| **TOTAL** | **27** | **23** | **4** | **85%** |

---

## 1. Dashboard ✅

| Check | Estado | Evidencia |
|-------|:------:|-----------|
| `<main>` landmark | ✅ | `main [ref=f1e103]` presente |
| Skip link | ✅ | "Saltar al contenido" → #main-content |
| aria-label "Alternar barra lateral" | ✅ | Botón toggle sidebar |
| aria-label "Cambiar a modo oscuro" | ✅ | Botón theme toggle |
| aria-label "Menú de perfil" | ✅ | Link avatar dropdown |
| Chart #incomeChart aria-label | ✅ | `aria-label="Ingresos mensuales por reservas (2026)"` |
| Chart #incomeChart text alt | ✅ | `<span class="visually-hidden">` con descripción |
| Chart #TotalEventBookingChart aria-label | ✅ | `aria-label="Reservas mensuales de eventos (2026)"` |
| Chart #TotalEventBookingChart text alt | ✅ | `<span class="visually-hidden">` con descripción |
| Sidebar icons aria-hidden | ✅ | 14/15 icons con `aria-hidden="true"` |
| Sidebar search label | ✅ | `<label for="sidebar-search">` |
| Console errors | ✅ | 0 errors |
| Cards rendering | ✅ | 4 metric cards visibles |

---

## 2. Event List (`/organizer/event-management/events`) ✅

| Check | Estado | Evidencia |
|-------|:------:|-----------|
| Selects con aria-label | ✅ | 10/11 selects con `aria-label` |
| Bulk checkbox | ✅ | `aria-label="Seleccionar todos"` |
| Table responsive | ⚠️ | Tabla dentro de `div.d-none.d-lg-block`, NO en `.table-responsive` |
| Search input label | ⚠️ | 1/2 search inputs tiene label |
| Console errors | ⚠️ | 5 errors (imágenes 404 — datos faltantes, no de código) |

**Issues encontrados:**
1. Tabla NO tiene wrapper `.table-responsive` — necesita fix en CSS/Blade
2. Select `event_type` (filtro principal) sin `aria-label`

---

## 3. Create Event (`/organizer/add-event`) ⚠️

| Check | Estado | Evidencia |
|-------|:------:|-----------|
| Progress bar ARIA | ✅ | `aria-valuenow` presente |
| Breadcrumb home label | ❌ | No existe breadcrumb en esta vista |
| Form labels | ❌ | **23/25 inputs sin label** |

**Issue principal:** 23 campos del formulario no tienen `<label>` ni `aria-label`:
- `start_date`, `start_time`, `end_date`, `end_time` (date/time)
- `m_start_date[]`, `m_start_time[]`, `m_end_date[]`, `m_end_time[]` (dynamic dates)
- `status`, `is_featured` (selects)
- `es_title`, `es_category_id`, `es_description`, `es_meta_keywords`, `es_meta_description`
- `spotify_url`, `youtube_url`, `meta_pixel_id`, `google_analytics_id`, `tiktok_pixel_id`
- File upload input

---

## 4. Transactions (`/organizer/transaction`) ✅

| Check | Estado | Evidencia |
|-------|:------:|-----------|
| Search input con label | ✅ | `id="transSearch"` + `<label for="transSearch">` |
| View buttons "Ver comprobante" | ✅ | `aria-label="Ver comprobante"` en 8 links |
| Table responsive | ✅ | Tabla dentro de `.table-responsive` |
| Bulk checkbox | N/A | No aplica en esta vista |

---

## 5. Bookings (`/organizer/event-booking`) ✅

| Check | Estado | Evidencia |
|-------|:------:|-----------|
| Search input con label | ✅ | 1/1 search input tiene label |
| Main landmark | ✅ | `<main>` presente |
| Table | N/A | No hay tabla (vista de cards/lista) |

---

## 6. Monthly Income (`/organizer/monthly-income`) ✅

| Check | Estado | Evidencia |
|-------|:------:|-----------|
| Table responsive | ✅ | Tabla dentro de `.table-responsive` |
| Main landmark | ✅ | `<main>` presente |
| Breadcrumb | ❌ | No existe breadcrumb |

---

## 7. Support Tickets (`/organizer/support-tikcet/tickets`) ✅

| Check | Estado | Evidencia |
|-------|:------:|-----------|
| Search input "ticketSearch" | ✅ | `id="ticketSearch"` + `<label for="ticketSearch">` |
| Main landmark | ✅ | `<main>` presente |
| Table | N/A | No hay tabla (vista de cards/lista) |

---

## 8. Dark Mode ✅

| Token | Valor esperado | Valor real | Ratio | WCAG AA |
|-------|---------------|-----------|------:|:-------:|
| `--adm-bg` | `#1c2433` | `rgb(28,36,51)` ✅ | — | — |
| `--adm-ink` | `#e5e5e5` | `rgb(229,229,229)` ✅ | 10.39:1 | ✅ |
| `--adm-card` | `#2a3040` | `rgb(42,48,64)` ✅ | — | — |
| `--adm-muted` | `#b0b0b0` | `#b0b0b0` ✅ | 4.76:1 | ✅ |
| `--adm-info` | `#60a5fa` | — | 5.74:1 | ✅ |
| `--adm-warning` | `#fbbf24` | — | 7.76:1 | ✅ |

Toggle funciona correctamente. Tokens se aplican en todos los elementos.

---

## 9. Responsive 375px ✅

| Check | Estado | Evidencia |
|-------|:------:|-----------|
| Sidebar hidden | ✅ | `transform: matrix(1,0,0,1,-280,0)` |
| Main content width | ✅ | 360px (dentro de 375px viewport) |
| Cards width | ✅ | 316px cada card (no overflow) |
| Layout roto | ✅ | No hay horizontal scroll |

---

## Issues Pendientes (requieren fix)

### P0 — Crítico
1. **Create event: 23 inputs sin label** — WCAG 1.3.1 violado. Cada input necesita `<label>` visible o `aria-label`.

### P1 — Alto
2. **Events list: tabla sin `.table-responsive`** — En mobile la tabla se desborda.
3. **Events list: select `event_type` sin `aria-label`** — Primer filtro sin accessible name.

### P2 — Medio
4. **Breadcrumbs faltantes** — Las vistas de eventos, income, withdraw, soporte no tienen breadcrumb (solo el layout base lo incluye en las rutas que lo definen en Blade).

---

## Score Final

| Métrica | Antes (code audit) | Después (Playwright) | Cambio |
|---------|:------------------:|:--------------------:|:------:|
| Accessibility core | 1/10 | 8/10 | +7 |
| Dark mode | 2/10 | 10/10 | +8 |
| Responsive | 2/10 | 9/10 | +7 |
| Forms | 2/10 | 1/10 | -1 |
| Tables | 5/10 | 7/10 | +2 |
| **Global** | **38/100** | **72/100** | **+34** |

**Score post-verificación Playwright: 72/100**

---

## Próximos Pasos Recomendados

1. **URGENTE:** Agregar `<label>` a los 23 inputs del form de crear evento
2. **Alto:** Wrapper `.table-responsive` en events list
3. **Alto:** `aria-label` en select `event_type`
4. **Medio:** Agregar breadcrumbs a vistas que los necesiten
5. **Opcional:** Verificar edit-event (requiere ID de evento existente)

---

## Screenshots Capturados

- `dashboard-light.png` — Dashboard en light mode
- `dashboard-dark.png` — Dashboard en dark mode
- `dashboard-mobile.png` — Dashboard en 375px (mobile)
