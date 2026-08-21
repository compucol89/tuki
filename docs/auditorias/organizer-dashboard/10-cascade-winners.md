# 10 — Cascade Winners (evidencia computada)

**Fecha:** 2026-08-21 · **Método:** `getComputedStyle` + inspección de `cssRules` en runtime (Playwright)

## Dashboard — componentes clave

| Componente | Propiedad | Valor computado | Regla ganadora | Archivo:línea | Spec | !important |
|------------|-----------|-----------------|----------------|---------------|------|-----------|
| `.card` (ev-section-card) | background | `rgb(42,48,64)` dark / `#fff` light | tokens `--surface-card` | admin-skin.css:2163/2197 | — | — |
| `.od-profile-score` | --od-* vars | `#e5e5e5`/`#b0b0b0` dark | override html[data-theme=dark] | theme-dark.css:~901 | (0,2,1)+ | — |
| H1 "Bienvenido" | color | `--od-text` (dark #e5e5e5) | token | theme-dark | — | — |
| Canvas incomeChart | line | `#f97316` | JS `tukiInitLineChart` | chart-init.js:74 | — | — |
| `.card-stats` (dashboard-items) | background | `var(--adm-card)` dark | html[data-theme=dark] | theme-dark.css:1270 | — | — |

## Conflicto real documentado (event/edit)

| Elemento | Propiedad | Antes | Después |
|----------|-----------|-------|---------|
| `.event-cover-box` | background | `transparent` (perdía cascade) | gradient tokens (gana con `!important` documentado) |

**Cascade del conflicto (reconstruida):**
```
.event-form-modern .event-cover-box  (event-form-modern.css:626, spec 0,2,0)
    → background: transparent; border:0; padding:0
    (CSS externo cargado DESPUÉS del <style> inline del blade → gana por orden)

.event-cover-box  (blade inline, spec 0,1,0)
    → background: gradient tokens  (PERDÍA por menor spec + orden anterior)
```

**Fix:** `.event-form-modern .event-cover-box` en el blade con `!important`
(especificidad 0,2,0 + importante → gana). Documentado en el blade con
comentario de por qué existe el `!important` (política de overrides).

## Sidebar (de la auditoría previa, revalidado)

| Estado | Icono | Texto | Ganador |
|--------|-------|-------|---------|
| default | `--sidebar-icon` | `--sidebar-text-secondary` | Vendor Override admin-skin:2428+ |
| active | `--sidebar-active-icon` | `--sidebar-active-text` | idem (spec 0,6,2 + orden) |
| hover | `--sidebar-text-primary` | idem | idem |
| expanded | `--sidebar-text-primary` | idem | idem |

## Conclusión

Los ganadores de cascade del dashboard son todos tokens de admin-skin/theme-dark
(sin hardcoded). El único conflicto real encontrado fue `.event-cover-box`,
corregido con override documentado. No hay escalamiento de especificidad
innecesario: los `!important` de la capa Tuki son ≤ (0,8,2) y todos justificados.
