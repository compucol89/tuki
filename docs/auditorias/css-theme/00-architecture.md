# TukiPass — CSS Theme Architecture (Organizer)

**Fecha:** 2026-08-21 · **Alcance:** Panel de Organizador · **Método:** inventario estático + cascade runtime (Playwright)

---

## 01 — CSS Load Order (orden real de carga)

```
public/assets/admin/css/atlantis.css      (vendor, 14.692 líneas, 794 !important)
public/assets/admin/css/admin-main.css    (Tuki, 73 !important)
public/assets/admin/css/admin-skin.css    (Tuki, tokens + overrides, ~590 !important)
public/assets/admin/css/theme-dark.css    (Tuki dark, 226 !important → 1336 líneas)
Blade <style> inline (@yield('style'))    (Tuki page-scoped — gana por orden)
style=""                                  (inline attr — gana a todo)
```

Cargados desde `resources/views/organizer/partials/styles.blade.php`.

**Implicación:** un `<style>` inline de Blade vence a cualquier hoja externa por orden de carga; los overrides dark necesitan `html[data-theme]` (mayor especificidad) o `!important`.

## 02 — Atlantics Inventory (resumen)

| Familia | Uso en Organizer | Notas |
|---------|------------------|-------|
| Layout/sidebar/navbar | Sí | base estructural |
| Cards | Sí | superficies claras: corregidas vía tokens |
| Forms/tables/buttons | Sí | estados claros: corregidos vía theme-dark |
| Plugins (select2, datatables, etc.) | Parcial | en uso |

## 03 — Important Audit (Tuki-owned)

### Clasificación (administrada en admin-skin.css)

| Categoría | Cantidad aprox. | Ejemplo |
|-----------|-----------------|---------|
| Vendor Override (vencer atlantis) | ~15 | `sidebar icon active` (spec idéntica + orden) |
| Neutralización de estados | ~10 | `html[data-theme="dark"] .sidebar ...` |
| Tokens/aliases | ~20 | `color: var(--sidebar-text-*) !important` |
| Layout defensivo (wcag touch) | 3 | `.topbar-toggler.more` |

**Regla:** ningún `!important` nuevo sin justificación en el reporte. Se usa solo para vencer `!important` ajeno de atlantis (misma especificidad + orden) o estados heredados.

## 04 — Selector Leakage (documentado)

| Selector | Problema | Fix |
|----------|----------|-----|
| `.nav.nav-primary > .nav-item.active a i` (atlantis:866) | descendente → alcanza todo el submenu | override con igual spec + orden (Vendor Override en admin-skin) |
| `.nav.nav-primary > .nav-item a:hover i` (atlantis:853) | idem hover | override hover/focus |

## 05 — Inline Style Inventory (12 blades reales)

| Blade | Líneas <style> | Prefijos | Dark antes | Dark después |
|-------|---------------|----------|-----------|--------------|
| event/index | 317 | .oe-* | 14/34 | tokens ✅ |
| booking/index | 734 | .ob-* | 10/60 | tokens ✅ |
| booking/details | 374 | .bod-* | 20/38 | tokens ✅ |
| event/create | 590 | .create-*, .ai-* | parcial | tokens ✅ |
| event/edit | 371 | .ai-*, .event-* | parcial | tokens ✅ |
| ticket/create | 22 | .ticket-form-* | — | tokens ✅ |
| ticket/edit | 22 | .ticket-form-* | — | tokens ✅ |
| ticket/index | 92 | .ticket-* | — | tokens ✅ |
| index (dashboard) | 195 | .od-* | ya tenía | tokens ✅ |
| telegram-bot | 55 | .tb-* | — | tokens ✅ |
| ai-generate-button | 165 | .ai-* | — | tokens ✅ |
| edit-profile | 708 | .ep-* | ya tenía | sin cambios |

**Total aprox:** 3.645 líneas inline → migradas a tokens semánticos (10/12; dashboard y edit-profile ya tokenizados).

## 06 — Color Inventory (resumen)

- **Antes:** ~230 colores hardcoded en inline styles.
- **Ahora:** superficies/textos/bordes → `var(--surface-*)`, `var(--text-*)`, `var(--border-*)`, `var(--status-*)`.
- **Quedan (intencional):** colores de marca (`--sidebar-accent`, gradients de botones primarios), colores de estado semántico, valores de plugins.

## 07 — Token Map

### Light (`:root` en admin-skin.css)

```css
--surface-page: #f5f6f8;   --surface-card: #ffffff;   --surface-toolbar: #fbfcfe;
--surface-card-soft: #f8fafc;  --surface-input: #ffffff;  --surface-elevated: #ffffff;
--text-primary: #1e2532;   --text-secondary: #5f6b7d;  --text-muted: #6b7686;
--border-subtle: #eef1f5;  --border-default: #e7eaf0;  --border-strong: #d5dae3;
--status-success-bg/fg, --status-warning-bg/fg, --status-danger-bg/fg, --status-info-bg/fg;
--focus-ring: rgba(249,115,22,.35);
```

### Dark (`html[data-theme="dark"]`)

```css
--surface-page: #171e2b;   --surface-card: #232c3b;    --surface-toolbar: #1f2637;
--surface-card-soft: #283242;  --surface-input: #232c3b;  --surface-elevated: #2a3444;
--text-primary: #f2f4f7;   --text-secondary: #b8c0cc;  --text-muted: #8e98a8;
--border-subtle: rgba(255,255,255,.08);  --border-default: rgba(255,255,255,.12);  --border-strong: rgba(255,255,255,.18);
--status-* → rgba con fg claros (86efac, fdba74, fca5a5, 93c5fd)
```

### Aliases existentes mapeados
`--adm-*` (light) ≡ escala semántica · `--sidebar-*` (sidebar) · `--od-*` (dashboard score).

## 08 — Theme Coverage

**Verificado en runtime (dark, Chromium @ 1440):** dashboard, eventos (list/venue/online), booking list, booking details, ticket index, add-ticket, edit-event, telegram, withdraw — **0 superficies blancas, 0 texto oscuro sobre oscuro, 0 iconos #1572E8**.

---

## Regla NO NEW DEBT

Un componente nuevo del Organizer NO puede introducir silenciosamente:
1. `<style>` inline con colores hardcoded de superficie/texto/borde → debe usar tokens
2. `!important` sin justificación documentada
3. Selectores descendentes que dependan de estados de ancestros (`active a i`)

Verificación automatizada: ver `D2 — static audit` (scripts/audit-organizer-theme.sh + baseline JSON).
