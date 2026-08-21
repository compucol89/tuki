# Auditoría Forense de Accesibilidad Visual — Dashboard del Organizador

**Fecha:** 2026-08-20
**Alcance:** `/organizer/dashboard` (solo esta página)
**Estándar:** WCAG 2.2 AA (mínimo) + AAA (objetivo visual)
**Acción:** Documental — sin correcciones en esta pasada

---

## 0. Correcciones aplicadas (post-auditoría)

> Esta sección refleja los fixes implementados en el repo (pendientes de deploy a producción)
> y **corrige 3 imprecisiones** detectadas al re-medir los colores computados en vivo.

### Correcciones a los hallazgos originales

1. **A11Y-DARK-006 (P0) — incorrecto.** La card `.od-profile-score` **NO** queda blanca en dark:
   `theme-dark.css` ya la forzaba a `#2a3040`. El problema real era que los **tokens internos**
   (`--od-text:#1e2532`, `--od-muted:#6b7280`) seguían siendo light sobre el fondo oscuro →
   `0%` a **1.17:1** (casi invisible) y `0/7 listo`/copy a **2.72:1**. (Fijado)
2. **A11Y-DARK-001/002 — matiz.** `--adm-muted:#a3a3a3` pasa AA (5.22:1) sobre la card `#2a3040`;
   solo falla sobre el body `#1c2433` (3.70:1). Se subió a `#b0b0b0` de todos modos (objetivo AAA).
3. **A11Y-DARK-012 — causa real del solapamiento de charts.** No era `text-overflow`; era
   `admin-skin.css` `.dashboard-items.row { display:grid; grid-template-columns: repeat(auto-fit,
   minmax(min(100%,280px),1fr)) }` que colapsaba los `.col-lg-6` de los charts a ~252px (canvas
   206px), solapando ejes/leyenda en vertical. (Fijado)

### Hallazgos nuevos (no estaban en el doc)

4. **Nuevo P0 — botones del score corruptos en dark.** `theme-dark.css` `html[data-theme="dark"]
   .page-inner a:not(...)` (especificidad alta) pisaba el `color:#fff` inline de los CTAs →
   "Completar perfil" naranja sobre naranja **1.23:1**, "Ver perfil público" **2.95:1**. (Fijado)
5. **Nuevo P1 — error de consola.** `chart-init.js:89` lanzaba TypeError en el panel organizer
   (`ProductOrderChart` no existe ahí). (Fijado con guard)
6. **Nuevo P2 — fondo de página inconsistente.** `admin-skin.css:597` forzaba `#1e2532 !important`
   mientras `theme-dark.css` usa `#1c2433` → sidebar y contenido con fondos distintos. (Fijado:
   ahora usa `var(--adm-bg)`).

### Fixes aplicados

| # | Archivo | Cambio | Resuelve |
|---|---------|--------|----------|
| 1 | `resources/views/organizer/index.blade.php` | Tokens light del score: `--od-muted` → `#4b5563` (AA), eyebrow → `#c2410c` (AA), botón primario `#c2410c`/`#9a3412` (blanco 5.18:1) | AA en light |
| 2 | `public/assets/admin/css/theme-dark.css` | Overrides dark del score (tokens + eyebrow `#f4845f` + labels `#f78a63` + botones + `--od-muted:#b0b0b0` + placeholder + logo invert) | P0/P1 dark |
| 3 | `public/assets/admin/js/chart-init.js` | Paleta por tema (ticks/leyenda `#c8cdd6` dark, `#6b7280` light; grid visible), guard de canvas inexistentes | P1 charts + TypeError |
| 4 | `public/assets/admin/css/atlantis.css` | `*:focus{outline:0}` → `:focus-visible` con anillo naranja visible | P0 teclado |
| 5 | `public/assets/admin/css/admin-skin.css` | `.col-lg-6:not(.col-xl-3)` charts a ancho completo (fix solapamiento), `.ev-label-section` → `var(--adm-muted)`, fondo `body[data-background-color="dark"]` → `var(--adm-bg)`, CSS skip-link | P1 layout + P2 |
| 6 | `resources/views/organizer/layout.blade.php` | Skip link "Saltar al contenido" + `id="main-content"` | 2.4.1 |
| 7 | `resources/lang/admin.json` | `Welcome back`→Bienvenido/a, `Total Event Bookings`→Total de reservas de eventos, `Event Booking Monthly Income`→Ingresos mensuales por reservas | i18n |

### Ratios verificados en vivo (con CSS inyectado)

| Elemento | Antes (dark) | Después (dark) | WCAG |
|---|--:|--:|:--:|
| `0%` | 1.17 🔴 | **10.45** | ✅ |
| `0/7 listo` | 2.72 🔴 | **6.07** | ✅ |
| copy | 2.72 🔴 | **6.07** | ✅ |
| eyebrow | 3.63 🔴 | **5.21** | ✅ |
| action-label | 4.12 🔴 | **5.08** | ✅ |
| action-hint | 2.51 🔴 | **5.60** | ✅ |
| btn Completar | 1.23 🔴 | **5.18** | ✅ |
| btn Ver perfil | 2.95 🔴 | **10.48** | ✅ |
| placeholder buscador | ~3.2 🔴 | **6.82** | ✅ |
| charts | 206px / solapado | **1553px** | ✅ |

**Pendiente de deploy a producción** (los fixes están en el repo local, no publicados).
**Quedan fuera de alcance de esta página:** alt del avatar del navbar, sync de radios de tema con
localStorage, y demás páginas del panel (el mismo patrón se aplica, pero requieren su propia pasada).

---

## 1. Resumen Ejecutivo

| Severidad | Cantidad | Descripción |
|-----------|----------|-------------|
| 🔴 P0 Critical | 3 | Foco suprimido globalmente, tokens locales sin dark mode, gráficos sin adaptación |
| 🔴 P1 High | 5 | Contraste insuficiente en dark mode para labels, texto de gráficos, breadcrumbs |
| 🟠 P2 Medium | 4 | Sistema de temas dual, skip link ausente, colores hardcodeados en CSS |
| 🟡 P3 Low | 2 | Bootstrap 4.3.1, tipografía del sidebar |

**Hallazgos totales:** 14
**Causas raíz principales:** 4 (tokens dark, focus suppression, chart hardcoding, dual theme)

---

## 2. Contexto Técnico

### Stack

| Componente | Versión | Archivo |
|------------|---------|---------|
| Bootstrap | 4.3.1 | `assets/admin/css/bootstrap.min.css` |
| Chart.js | 2.7.2 | `assets/admin/js/chart.min.js` |
| Theme CSS | Atlantis | `assets/admin/css/atlantis.css` (14,683 líneas) |
| Token CSS | Custom | `assets/admin/css/admin-skin.css` (1,931 líneas) |
| Dark CSS | Custom | `assets/admin/css/theme-dark.css` (1,249 líneas) |

### Arquitectura de Tokens

**Light mode** (`:root` en `admin-skin.css:230-256`):

```css
--adm-bg: #f5f6f8;
--adm-bg-soft: #f8fafc;
--adm-card: #ffffff;
--adm-ink: #1e2532;
--adm-ink-strong: #111827;
--adm-muted: #5f6b7d;
--adm-primary: #F97316;
--adm-primary-dark: #C2410C;
--adm-success: #16A34A;
--adm-info: #2563EB;
--adm-warning: #D97706;
--adm-danger: #DC2626;
```

**Dark mode** (`html[data-theme="dark"]` en `theme-dark.css:752-778`):

```css
--adm-bg: #1c2433;
--adm-bg-soft: #2a303e;
--adm-card: #2a3040;
--adm-ink: #e5e5e5;
--adm-ink-strong: #ffffff;
--adm-muted: #a3a3a3;
--adm-primary: #e05d38;
--adm-primary-dark: #f4845f;
--adm-success: #4ade80;
--adm-info: #60a5fa;
--adm-warning: #fbbf24;
--adm-danger: #f87171;
```

**Tokens locales del dashboard** (`index.blade.php:6-12`):

```css
.od-profile-score {
  --od-primary: #e05d38;
  --od-primary-strong: #bf4424;
  --od-text: #1e2532;
  --od-muted: #6b7280;
  --od-surface: #ffffff;
  --od-border: #dcdfe2;
  --od-soft: #f3f4f6;
}
```

### Sistema de Temas Dual

| Mecanismo | Ubicación | Persistencia | CSS afectado |
|-----------|-----------|--------------|--------------|
| `data-theme` en `<html>` | `layout.blade.php:8` | localStorage (`tuki-theme`) | `theme-dark.css` |
| `data-background-color` en `<body>` | `layout.blade.php:45` | Base de datos (`organizers.theme_version`) | `admin-skin.css` líneas 593-1076 |

**Problema:** Los dos mecanismos son independientes y no están sincronizados. El toggle de radio botones envía un form POST al servidor (DB), mientras que el botón toggle guarda en localStorage. Pueden quedar en estados mixtos.

### Configuración de Gráficos

**Library:** Chart.js 2.7.2
**Archivos:** `chart-init.js` (165 líneas)
**Gráficos en dashboard:**

| Canvas ID | Tipo | Propósito | Color línea |
|-----------|------|-----------|-------------|
| `incomeChart` | Line | Ingresos mensuales eventos | `#f97316` (naranja) |
| `TotalEventBookingChart` | Line | Reservas mensuales eventos | `#6366f1` (índigo) |

**Colores hardcodeados (sin adaptación a theme):**

| Elemento | Color | Archivo:línea |
|----------|-------|---------------|
| `borderColor` chart 1 | `#f97316` | `chart-init.js:19` |
| `pointBackgroundColor` chart 1 | `#f97316` | `chart-init.js:21` |
| `backgroundColor` chart 1 | `rgba(249,115,22,.08)` | `chart-init.js:26` |
| `borderColor` chart 2 | `#6366f1` | `chart-init.js:58` |
| `pointBackgroundColor` chart 2 | `#6366f1` | `chart-init.js:60` |
| `backgroundColor` chart 2 | `rgba(99,102,241,.08)` | `chart-init.js:65` |
| `fontColor` leyenda (todos) | `#6b7280` | `chart-init.js:36,75,114,153` |
| `fontColor` ticks X/Y (todos) | `#9ca3af` | `chart-init.js:44-45,83-84,122-123,161-162` |
| `gridLines` color (todos) | `rgba(0,0,0,.04)` | `chart-init.js:44-45,83-84,122-123,161-162` |

---

## 3. Hallazgos por Categoría

### 3.1 Contraste de Texto (WCAG 1.4.3)

#### Hallazgo A11Y-DARK-001: Token `--adm-muted` con contraste insuficiente en dark mode

| Campo | Valor |
|-------|-------|
| **ID** | A11Y-DARK-001 |
| **Ruta** | `/organizer/dashboard` |
| **Componente** | Tokens globales |
| **Selector** | `--adm-muted` |
| **Tema** | dark |
| **Estado** | default |
| **Foreground computado** | `#a3a3a3` |
| **Background computado** | `#1c2433` (body) |
| **Ratio** | 3.70:1 |
| **Requerido** | 4.5:1 AA |
| **WCAG** | 1.4.3 |
| **Severidad** | 🔴 P1 High |
| **Síntoma visible** | Texto muted aparece tenue, difícil de leer |
| **Causa raíz** | `--adm-muted: #a3a3a3` diseñado para light mode, heredado en dark sin contraste suficiente |
| **Archivo:línea** | `theme-dark.css:758` |
| **Fix centralizado** | Cambiar `--adm-muted` en dark a al menos `#b0b0b0` (4.5:1) |
| **Riesgo de regresión** | Todos los componentes que usan `--adm-muted` (breadcrumbs, labels, card-category, dataTables_info) |

#### Hallazgo A11Y-DARK-002: `.card-category` en stat cards con contraste insuficiente

| Campo | Valor |
|-------|-------|
| **ID** | A11Y-DARK-002 |
| **Ruta** | `/organizer/dashboard` |
| **Componente** | Stat cards (Pendiente por liquidar, Events, etc.) |
| **Selector** | `.dashboard-items .card-stats .numbers .card-category` |
| **Tema** | dark |
| **Estado** | default |
| **Foreground computado** | `#a3a3a3` |
| **Background computado** | `#2a3040` (card background dark) |
| **Ratio** | 2.88:1 |
| **Requerido** | 4.5:1 AA |
| **WCAG** | 1.4.3 |
| **Severidad** | 🔴 P1 High |
| **Síntoma visible** | Labels "Pendiente por liquidar", "Events", "Total Event Bookings", "Total Transcation" casi ilegibles |
| **Causa raíz** | `theme-dark.css:164-166` fuerza `color: #a3a3a3` para `.card-stats .numbers .card-category` |
| **Archivo:línea** | `theme-dark.css:164-166` |
| **Fix centralizado** | Cambiar color de `.card-stats .numbers .card-category` en dark a al menos `#b8b8b8` |
| **Riesgo de regresión** | Todas las stat cards del organizador |

#### Hallazgo A11Y-DARK-003: `.ev-label-section` con contraste insuficiente

| Campo | Valor |
|-------|-------|
| **ID** | A11Y-DARK-003 |
| **Ruta** | `/organizer/dashboard` |
| **Componente** | Etiquetas de subsección |
| **Selector** | `.ev-label-section` |
| **Tema** | light |
| **Estado** | default |
| **Foreground computado** | `#9ca3af` |
| **Background computado** | `#ffffff` |
| **Ratio** | 3.05:1 |
| **Requerido** | 4.5:1 AA |
| **WCAG** | 1.4.3 |
| **Severidad** | 🟠 P2 Medium |
| **Síntoma visible** | Etiquetas uppercase muy tenues |
| **Causa raíz** | `admin-skin.css:66` hardcodea `color: #9ca3af !important` |
| **Archivo:línea** | `admin-skin.css:66` |
| **Fix centralizado** | Cambiar a `#6b7280` o usar `var(--adm-muted)` |

#### Hallazgo A11Y-DARK-004: Labels de formularios con contraste bajo

| Campo | Valor |
|-------|-------|
| **ID** | A11Y-DARK-004 |
| **Ruta** | `/organizer/dashboard` |
| **Componente** | Labels de formulario |
| **Selector** | `.form-group > label:first-child` |
| **Tema** | light |
| **Estado** | default |
| **Foreground computado** | `#6b7280` |
| **Background computado** | `#ffffff` |
| **Ratio** | 5.03:1 |
| **Requerido** | 4.5:1 AA |
| **WCAG** | 1.4.3 |
| **Severidad** | ✅ PASS AA |
| **Nota** | Pasa AA pero no AAA (7:1 requerido) |

### 3.2 Contraste de Componentes (WCAG 1.4.11)

#### Hallazgo A11Y-DARK-005: Iconos de stat cards en dark mode

| Campo | Valor |
|-------|-------|
| **ID** | A11Y-DARK-005 |
| **Ruta** | `/organizer/dashboard` |
| **Componente** | Iconos de stat cards |
| **Selector** | `.card-stats .icon-big i` |
| **Tema** | dark |
| **Estado** | default |
| **Foreground computado** | `rgba(229, 229, 229, 0.7)` ≈ `#c3c3c3` sobre `#2a3040` |
| **Ratio efectivo** | ~3.5:1 (estimado, considerando alpha) |
| **Requerido** | 3:1 AA |
| **WCAG** | 1.4.11 |
| **Severidad** | ✅ PASS AA (borderline) |
| **Nota** | Pasa AA pero el uso de alpha reduce el contraste efectivo |

### 3.3 Dark Mode / Theming

#### Hallazgo A11Y-DARK-006: Tokens locales `od-*` sin overrides para dark mode

| Campo | Valor |
|-------|-------|
| **ID** | A11Y-DARK-006 |
| **Ruta** | `/organizer/dashboard` |
| **Componente** | Sección de perfil |
| **Selector** | `.od-profile-score` |
| **Tema** | dark |
| **Estado** | default |
| **Foreground computado** | `#1e2532` (var(--od-text)) |
| **Background computado** | `#ffffff` (var(--od-surface)) |
| **Ratio** | 14.29:1 |
| **Requerido** | 4.5:1 AA |
| **WCAG** | 1.4.3 |
| **Severidad** | 🔴 P0 Critical |
| **Síntoma visible** | En dark mode, el fondo de la card de perfil queda blanco (#ffffff) porque `--od-surface` no tiene override. El texto negro sobre fondo blanco es legible pero destruye la experiencia dark mode. |
| **Causa raíz** | Los tokens `od-*` están definidos en `index.blade.php:6-12` inline en `<style>`, sin ningún override para `html[data-theme="dark"]` |
| **Archivo:línea** | `index.blade.php:6-12` |
| **Fix centralizado** | Agregar bloque `html[data-theme="dark"] .od-profile-score { --od-text: #e5e5e5; --od-muted: #a3a3a3; --od-surface: #2a3040; --od-border: #3d4354; --od-soft: #3a3c44; }` |
| **Riesgo de regresión** | Solo afecta la sección de perfil del dashboard |

#### Hallazgo A11Y-DARK-007: Sistema de temas dual no sincronizado

| Campo | Valor |
|-------|-------|
| **ID** | A11Y-DARK-007 |
| **Ruta** | `/organizer/dashboard` |
| **Componente** | Toggle de tema |
| **Selector** | `data-theme` + `data-background-color` |
| **Tema** | ambos |
| **Severidad** | 🟠 P2 Medium |
| **Síntoma visible** | Al usar el botón toggle (localStorage), el `body[data-background-color]` no se actualiza. Al usar los radio buttons (DB), el `html[data-theme]` no se actualiza hasta recargar. Pueden quedar en estados mixtos. |
| **Causa raíz** | Dos mecanismos independientes: `layout.blade.php:11-22` (inline script lee localStorage) y `layout.blade.php:45` (PHP lee DB). El script toggle (`layout.blade.php:78-104`) sincroniza `data-theme` + `data-background-color` pero solo al hacer click en el botón toggle, no al usar radio buttons. |
| **Archivo:línea** | `layout.blade.php:11-22, 45, 78-104` |

### 3.4 Gráficos

#### Hallazgo A11Y-DARK-008: Texto de ejes de gráficos con contraste insuficiente en dark

| Campo | Valor |
|-------|-------|
| **ID** | A11Y-DARK-008 |
| **Ruta** | `/organizer/dashboard` |
| **Componente** | Ejes X e Y de ambos gráficos |
| **Selector** | `xAxes[].ticks.fontColor` / `yAxes[].ticks.fontColor` |
| **Tema** | dark |
| **Estado** | default |
| **Foreground computado** | `#9ca3af` (hardcoded) |
| **Background computado** | `#2a3040` (card background dark) |
| **Ratio** | 3.20:1 |
| **Requerido** | 4.5:1 AA |
| **WCAG** | 1.4.3 |
| **Severidad** | 🔴 P1 High |
| **Síntoma visible** | Meses (Jan, Mar, May...) y valores del eje Y prácticamente ilegibles en dark mode |
| **Causa raíz** | `chart-init.js:44-45,83-84,122-123,161-162` hardcodea `fontColor: '#9ca3af'` sin adaptación a theme |
| **Archivo:línea** | `chart-init.js:44-45` (y equivalentes en los 4 charts) |
| **Fix centralizado** | Detectar theme actual y usar `fontColor` dinámico, o usar colores que pasen en ambos temas |

#### Hallazgo A11Y-DARK-009: Texto de leyenda de gráficos con contraste insuficiente

| Campo | Valor |
|-------|-------|
| **ID** | A11Y-DARK-009 |
| **Ruta** | `/organizer/dashboard` |
| **Componente** | Leyenda de gráficos |
| **Selector** | `legend.labels.fontColor` |
| **Tema** | dark |
| **Estado** | default |
| **Foreground computado** | `#6b7280` (hardcoded) |
| **Background computado** | `#2a3040` (card background dark) |
| **Ratio** | 2.36:1 |
| **Requerido** | 4.5:1 AA |
| **WCAG** | 1.4.3 |
| **Severidad** | 🔴 P1 High |
| **Síntoma visible** | "Ingresos mensuales" y "Reservas mensuales" casi invisibles en dark mode |
| **Causa raíz** | `chart-init.js:36,75,114,153` hardcodea `fontColor: '#6b7280'` |
| **Archivo:línea** | `chart-init.js:36` |
| **Fix centralizado** | Usar color dinámico según theme |

#### Hallazgo A11Y-DARK-010: Grid lines de gráficos casi invisibles en dark

| Campo | Valor |
|-------|-------|
| **ID** | A11Y-DARK-010 |
| **Ruta** | `/organizer/dashboard` |
| **Componente** | Grid lines de ambos gráficos |
| **Selector** | `xAxes[].gridLines.color` / `yAxes[].gridLines.color` |
| **Tema** | dark |
| **Estado** | default |
| **Foreground computado** | `rgba(0,0,0,.04)` (casi transparente) |
| **Background computado** | `#2a3040` |
| **Ratio** | ~1.02:1 (casi invisible) |
| **Requerido** | Variable (informativo, no esencial) |
| **WCAG** | 1.4.11 (si se usa para leer datos) |
| **Severidad** | 🟡 P3 Low |
| **Síntoma visible** | Líneas de cuadrícula prácticamente invisibles en dark mode |
| **Causa raíz** | `chart-init.js` usa `rgba(0,0,0,.04)` que es negro casi transparente — invisible sobre fondo oscuro |

### 3.5 Navegación por Teclado (WCAG 2.1.1)

#### Hallazgo A11Y-DARK-011: Foco global suprimido

| Campo | Valor |
|-------|-------|
| **ID** | A11Y-DARK-011 |
| **Ruta** | `/organizer/dashboard` |
| **Componente** | Todos los elementos enfocables |
| **Selector** | `*:focus` |
| **Tema** | todos |
| **Estado** | focus |
| **Severidad** | 🔴 P0 Critical |
| **Síntoma visible** | Ningún elemento muestra indicador de foco al navegar con teclado. El usuario no puede saber dónde está el foco. |
| **Causa raíz** | `atlantis.css:79-83` establece `*:focus { outline: 0 !important; box-shadow: none !important; }` — suprime todo indicador de foco globalmente. |
| **Archivo:línea** | `atlantis.css:79-83` |
| **WCAG** | 2.1.1 (Keyboard), 2.4.7 (Focus Visible), 2.4.11 (Focus Not Obscured), 2.4.13 (Focus Appearance) |
| **Fix centralizado** | Reemplazar `*:focus` con estilos `:focus-visible` que mantengan visibilidad del foco |
| **Riesgo de regresión** | Afecta a todos los componentes del panel |

**Nota:** `admin-skin.css:146-149` define foco para `.form-control:focus` y `admin-skin.css:460,475` para botones, pero estos son insuficientes porque no cubren todos los elementos navegables (links, nav items, tabs, etc.).

### 3.6 Foco (WCAG 2.4.7, 2.4.11, 2.4.13)

Ver Hallazgo A11Y-DARK-011 arriba.

**Elementos adicionales sin foco visible:**

| Elemento | Selector | Foco visible |
|----------|----------|--------------|
| Links del sidebar | `.sidebar .nav > .nav-item a` | ❌ No (suprimido) |
| Botones toggle | `.navbar-toggler`, `.topbar-toggler` | ❌ No |
| Dropdown profile | `.dropdown-toggle.profile-pic` | ❌ No |
| Links de stat cards | `.dashboard-items > [class*="col-"] > a` | ❌ No |
| Tabs de navegación | `.nav-link` | ❌ No |
| Radio buttons tema | `.selectgroup-input` | ⚠️ Parcial (custom CSS) |

### 3.7 Tipografía y Reflow (WCAG 1.4.4, 1.4.10, 1.4.12)

#### Hallazgo A11Y-DARK-012: Stats cards con `text-overflow: ellipsis`

| Campo | Valor |
|-------|-------|
| **ID** | A11Y-DARK-012 |
| **Ruta** | `/organizer/dashboard` |
| **Componente** | Números de stat cards |
| **Selector** | `.dashboard-items .card-stats .numbers .card-title` |
| **Tema** | todos |
| **Severidad** | 🟡 P3 Low |
| **Síntoma visible** | Números grandes pueden truncarse con `text-overflow: ellipsis` en viewports estrechos |
| **Causa raíz** | `admin-skin.css:1196-1203` aplica `overflow: hidden; text-overflow: ellipsis; white-space: nowrap` |
| **Archivo:línea** | `admin-skin.css:1199-1202` |
| **WCAG** | 1.4.10 Reflow |

### 3.8 Uso del Color (WCAG 1.4.1)

#### Hallazgo A11Y-DARK-013: Stat cards dependen solo del color del borde superior

| Campo | Valor |
|-------|-------|
| **ID** | A11Y-DARK-013 |
| **Ruta** | `/organizer/dashboard` |
| **Componente** | Stat cards (4 cards) |
| **Selector** | `.card-stats.card-info`, `.card-success`, `.card-danger`, `.card-secondary` |
| **Tema** | todos |
| **Severidad** | 🟠 P2 Medium |
| **Síntoma visible** | Las 4 cards se diferencian solo por el color del borde superior y el icono. Sin el color, serían difíciles de distinguir. |
| **Causa raíz** | `admin-main.css:716-719` define `border-top-color` por tipo. Los iconos tienen colores diferentes pero son decorativos (`aria-hidden="true"`). |
| **Archivo:línea** | `admin-main.css:716-719` |
| **WCAG** | 1.4.1 Use of Color |
| **Fix sugerido** | Agregar labels de texto o iconos semánticos (no `aria-hidden`) |

### 3.9 Skip Link

#### Hallazgo A11Y-DARK-014: Skip link ausente

| Campo | Valor |
|-------|-------|
| **ID** | A11Y-DARK-014 |
| **Ruta** | `/organizer/dashboard` |
| **Componente** | Navegación global |
| **Selector** | N/A |
| **Tema** | todos |
| **Severidad** | 🟠 P2 Medium |
| **Síntoma visible** | Usuarios de teclado/screen reader deben navegar por todo el sidebar antes de llegar al contenido principal |
| **Causa raíz** | No existe skip link en `layout.blade.php` ni en ningún partial |
| **Archivo:línea** | `layout.blade.php` (ausente) |
| **WCAG** | 2.4.1 Bypass Blocks |
| **Fix sugerido** | Agregar `<a href="#content" class="sr-only sr-only-focusable">Saltar al contenido</a>` al inicio del `<body>` |

---

## 4. Matriz de Contraste Completa

### 4.1 Tokens Globales — Light Mode

| Token | Foreground | Background | Ratio | AA (4.5:1) | AAA (7:1) |
|-------|------------|------------|-------|------------|-----------|
| `--adm-ink` | `#1e2532` | `#ffffff` | 14.29:1 | ✅ | ✅ |
| `--adm-ink-strong` | `#111827` | `#ffffff` | 17.37:1 | ✅ | ✅ |
| `--adm-muted` | `#5f6b7d` | `#ffffff` | 5.03:1 | ✅ | ❌ |
| `--adm-primary` | `#F97316` | `#ffffff` | 3.15:1 | ❌ (large only) | ❌ |
| `--adm-primary-dark` | `#C2410C` | `#ffffff` | 5.74:1 | ✅ | ❌ |
| `--adm-success` | `#16A34A` | `#ffffff` | 3.85:1 | ❌ (large only) | ❌ |
| `--adm-info` | `#2563EB` | `#ffffff` | 5.52:1 | ✅ | ❌ |
| `--adm-warning` | `#D97706` | `#ffffff` | 3.85:1 | ❌ (large only) | ❌ |
| `--adm-danger` | `#DC2626` | `#ffffff` | 4.64:1 | ✅ | ❌ |

### 4.2 Tokens Globales — Dark Mode

| Token | Foreground | Background | Ratio | AA (4.5:1) | AAA (7:1) |
|-------|------------|------------|-------|------------|-----------|
| `--adm-ink` | `#e5e5e5` | `#1c2433` | 12.85:1 | ✅ | ✅ |
| `--adm-ink-strong` | `#ffffff` | `#1c2433` | 15.36:1 | ✅ | ✅ |
| `--adm-muted` | `#a3a3a3` | `#1c2433` | 3.70:1 | ❌ | ❌ |
| `--adm-primary` | `#e05d38` | `#1c2433` | 3.53:1 | ❌ (large only) | ❌ |
| `--adm-primary-dark` | `#f4845f` | `#1c2433` | 5.48:1 | ✅ | ❌ |
| `--adm-success` | `#4ade80` | `#1c2433` | 8.52:1 | ✅ | ✅ |
| `--adm-info` | `#60a5fa` | `#1c2433` | 5.27:1 | ✅ | ❌ |
| `--adm-warning` | `#fbbf24` | `#1c2433` | 10.22:1 | ✅ | ✅ |
| `--adm-danger` | `#f87171` | `#1c2433` | 4.35:1 | ❌ | ❌ |

### 4.3 Tokens Locales del Dashboard (sin dark override)

| Token | Foreground | Background (light) | Ratio Light | Background (dark) | Ratio Dark |
|-------|------------|---------------------|-------------|---------------------|------------|
| `--od-text` | `#1e2532` | `#ffffff` | 14.29:1 ✅ | `#ffffff` (sin override) | 14.29:1 ⚠️ |
| `--od-muted` | `#6b7280` | `#ffffff` | 5.03:1 ✅ | `#ffffff` (sin override) | 5.03:1 ⚠️ |
| `--od-surface` | — | `#ffffff` | — | `#ffffff` (sin override) | ❌ Dark roto |

**Nota:** En dark mode, `--od-surface` queda en `#ffffff` porque no hay override. La card de perfil se ve con fondo blanco en dark mode.

### 4.4 Colores de Gráficos

| Elemento | Foreground | Background (light) | Ratio Light | Background (dark) | Ratio Dark |
|----------|------------|---------------------|-------------|---------------------|------------|
| Ticks ejes | `#9ca3af` | `#ffffff` | 3.05:1 ❌ | `#2a3040` | 3.20:1 ❌ |
| Leyenda | `#6b7280` | `#ffffff` | 5.03:1 ✅ | `#2a3040` | 2.36:1 ❌ |
| Grid lines | `rgba(0,0,0,.04)` | `#ffffff` | ~1.02:1 | `#2a3040` | ~1.02:1 |
| Línea chart 1 | `#f97316` | `#ffffff` | 3.15:1 | `#2a3040` | 3.02:1 |
| Línea chart 2 | `#6366f1` | `#ffffff` | 3.86:1 | `#2a3040` | 2.69:1 ❌ |

---

## 5. Análisis de Causa Raíz

### Agrupación por problema arquitectónico

| # | Causa raíz | Hallazgos afectados | Fix centralizado |
|---|------------|---------------------|------------------|
| 1 | `--adm-muted` con contraste bajo en dark | A11Y-DARK-001, A11Y-DARK-002 | Cambiar `--adm-muted: #a3a3a3` → `#b0b0b0` en `theme-dark.css:758` |
| 2 | `*:focus { outline: 0 }` suprime foco | A11Y-DARK-011 | Reemplazar con `:focus-visible` styles en `atlantis.css:79-83` |
| 3 | Chart.js colores hardcodeados | A11Y-DARK-008, A11Y-DARK-009, A11Y-DARK-010 | Hacer colores dinámicos en `chart-init.js` |
| 4 | Tokens `od-*` sin dark override | A11Y-DARK-006 | Agregar bloque dark en `index.blade.php` o `theme-dark.css` |

### Impacto de cada fix

| Fix | Hallazgos resueltos | Esfuerzo | Riesgo |
|-----|---------------------|----------|--------|
| Cambiar `--adm-muted` dark | 2 | Bajo | Medio (afecta todos los componentes que usan muted) |
| Reemplazar focus suppression | 1 | Medio | Alto (afecta todos los elementos del panel) |
| Chart.js dinámico | 3 | Medio | Bajo (solo afecta gráficos) |
| Tokens `od-*` dark | 1 | Bajo | Bajo (solo afecta dashboard) |
| Agregar skip link | 1 | Bajo | Bajo |

---

## 6. Hallazgos Adicionales (no de contraste)

### 6.1 Autenticación de avatar sin alt text

| Campo | Valor |
|-------|-------|
| **Archivo** | `top-navbar.blade.php:67` |
| **HTML** | `<img src="...blank_user.jpg" alt="" class="avatar-img rounded-circle">` |
| **Problema** | `alt=""` vacío en imagen que muestra el perfil del usuario |
| **WCAG** | 1.1.1 Non-text Content |
| **Severidad** | 🟡 P3 Low |

### 6.2 Botón toggle sin aria-label consistente

| Campo | Valor |
|-------|-------|
| **Archivo** | `top-navbar.blade.php:17` |
| **HTML** | `<button class="topbar-toggler more"><i class="fas fa-ellipsis-v"></i></button>` |
| **Problema** | Sin `aria-label`, icono font-awesome sin aria-hidden |
| **WCAG** | 4.1.2 Name, Role, Value |
| **Severidad** | 🟡 P3 Low |

---

## 7. Priorización de Fixes

### Orden recomendado

| Prioridad | Fix | Esfuerzo | Impacto |
|-----------|-----|----------|---------|
| 1 | Token `--adm-muted` dark → `#b0b0b0` | 5 min | Resuelve 2 hallazgos P1 |
| 2 | Tokens `od-*` dark overrides | 15 min | Resuelve 1 hallazgo P0 |
| 3 | Chart.js colores dinámicos | 30 min | Resuelve 3 hallazgos P1 |
| 4 | Focus visible (`:focus-visible`) | 1 hora | Resuelve 1 hallazgo P0 |
| 5 | Skip link | 15 min | Resuelve 1 hallazgo P2 |
| 6 | Sync temas dual | 30 min | Resuelve 1 hallazgo P2 |
| 7 | Labels stat cards con icono/texto | 30 min | Resuelve 1 hallazgo P2 |

---

## 8. Archivos Afectados

| Archivo | Cambios necesarios |
|---------|-------------------|
| `public/assets/admin/css/theme-dark.css:758` | Cambiar `--adm-muted: #a3a3a3` a valor con ≥4.5:1 |
| `resources/views/organizer/index.blade.php:6-12` | Agregar bloque `html[data-theme="dark"]` con overrides para `od-*` |
| `public/assets/admin/js/chart-init.js` | Hacer `fontColor` dinámico según theme |
| `public/assets/admin/css/atlantis.css:79-83` | Reemplazar `*:focus` con `:focus-visible` |
| `resources/views/organizer/layout.blade.php` | Agregar skip link al inicio del `<body>` |

---

*Documento generado como parte de la Auditoría Forense de Accesibilidad Visual, Contraste, Tipografía, Estados Interactivos y Dark/Light Theme — WCAG 2.2 AA + Visual AAA Target.*
