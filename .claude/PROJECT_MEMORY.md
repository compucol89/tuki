# Memoria del proyecto Tuki (lectura para Claude / agentes)

Este archivo sustituye o complementa búsquedas en sesiones anteriores. Actualizar al cerrar un bloque de trabajo relevante.

## Git y despliegue

- **Remoto:** `origin` → `git@github.com:compucol89/tuki.git`
- **Rama principal:** `master` (tracking `origin/master`)
- **Último push documentado (sesión):** 2026-04-17 — commit amplio de frontend Laravel (Sobre nosotros, tokens de diseño, i18n, assets, rutas, checkout, etc.). Revisar `git log -1` para el hash exacto.

## Memoria cross-session instalada (usuario)

- **Nombre del producto:** **claude-mem** (plugin *Claude Code* / *thedotmack*): base de observaciones persistente entre chats.
- **Ubicación típica del plugin (Cursor/Claude):** `~/.claude/plugins/cache/thedotmack/claude-mem/` con skills `mem-search`, `make-plan`, `do`, etc.
- **Uso:** invocar el skill **mem-search** cuando haga falta “¿qué hicimos antes?” o recuperar patrones del repo. Este `PROJECT_MEMORY.md` es la capa **en repo**; claude-mem es la capa **fuera del repo** en el plugin.

## Sobre nosotros (`/sobre-nosotros`)

- **Vista:** `resources/views/frontend/about.blade.php` — estilos scoped en `@push('styles')` bajo `body.about-page`.
- **Design doc (awesome-design-md + fusión Apple/Airbnb aplicada):** `.references/awesome-design-md/DESIGN.about-tuki-page.md`
- **Tokens CSS:** variables `--about-ds-*` y `--about-surface-*` definidas en `body.about-page` en el mismo Blade; hero `page-banner--about-premium`, bloque organizadores `#para-organizadores`, métricas/historia, características.
- **i18n ejemplo:** `about_banner_nav_aria` en `resources/lang/es.json`, `lang/es.json`, `resources/lang/en.json`.

## Convenciones útiles

- **Body class** página acerca de: `about-page` (`@section('body-class')`).
- **Prerender** layout: regla Speculation Rules solo para URLs que contengan `sobre-nosotros` en `resources/views/frontend/layout.blade.php`.
