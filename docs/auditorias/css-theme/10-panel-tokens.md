# 10 — Tokens `--panel-*` (semántica de paneles de contenido)

**Problema:** los componentes de contenido (`.oe-*` event panel, `.ob-*` booking list,
`.bod-*` booking details, `.ticket-*` ticket forms, `.ai-*` AI generator) usaban tokens del
**sidebar** (`--sidebar-surface/border/text-primary`, 8 referencias en `theme-dark.css:1320-1541`).
Cambiar los tokens del sidebar rompería componentes de contenido.

## Fix

1. Nuevos tokens `--panel-*` en `admin-skin.css` (mismos valores que el sidebar):

   ```css
   :root                     { --panel-surface: #f8fafc; --panel-border: rgba(30,37,50,.08); --panel-text-primary: #1e2532; }
   html[data-theme="dark"]   { --panel-surface: #232c3b; --panel-border: rgba(255,255,255,.08); --panel-text-primary: #f2f4f7; }
   ```

2. Las 8 referencias de `.oe-*/.ob-*/.bod-*/.ticket-*/.ai-*` migradas a `var(--panel-*)`.

## Resultado

- `theme-dark.css`: 0 referencias `--sidebar-*` en componentes de contenido (solo el sidebar real usa tokens del sidebar).
- Sin renombramiento: nada existente se rompió.

## Verificación

`grep -c "var(--panel-" theme-dark.css` → 8 · `grep -c "var(--sidebar-" theme-dark.css` → 0 (en contenido).
