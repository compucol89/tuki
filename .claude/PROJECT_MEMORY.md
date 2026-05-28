# Memoria del proyecto Tuki (lectura para Claude / agentes)

Este archivo sustituye o complementa búsquedas en sesiones anteriores. Actualizar al cerrar un bloque de trabajo relevante.

## Git y despliegue

- **Remoto:** `origin` → `git@github.com:compucol89/tuki.git`
- **Rama principal:** `master` (tracking `origin/master`)
- **Último push documentado (sesión):** 2026-04-17 — `master` en `origin`: trabajo principal `53c20bf` (frontend + Sobre nosotros + tokens `--about-ds-*`); punta actual tras doc `8b1a895` (`git log -1`).

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

## Tipografía (verificado 2026-05-02)

**Una sola fuente: Inter.**

```css
--tuki-font-sans: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
--base-font:    var(--tuki-font-sans);
--heading-font: var(--tuki-font-sans);
```

Plus Jakarta Sans y Sora NO están en uso — eran datos de una auditoría anterior incorrecta. No introducir esas fuentes.

---

## Datos Fiscales y Corporativos (actualizado 2026-05-20)

**Entidad operadora:** TAYRONA GROUP SAS
**CUIT:** 30-71885087-4
**Rol:** Operador comercial de la plataforma Tukipass.

### Disclaimer legal
> Importante: Tukipass no organiza ni produce los eventos publicados, salvo indicación expresa. Tukipass presta un servicio tecnológico de publicación, gestión y venta online de entradas. La realización, calidad, accesos, horarios, cambios, cancelaciones, reembolsos y condiciones particulares del evento son responsabilidad exclusiva del organizador.
>
> Al utilizar este sitio o comprar una entrada, el usuario acepta los Términos y Condiciones de Tukipass y las políticas aplicables de cada evento.

### Footer estándar
```
TAYRONA GROUP SAS — CUIT 30-71885087-4
Operador comercial de la plataforma Tukipass.
Copyright © 2026 Tukipass. Todos los derechos reservados.
```

### Uso obligatorio en
- PDFs de entradas/tickets (visible, contraste correcto)
- Emails de confirmación
- Página de confirmación post-compra
- Footer del sitio
- Facturas electrónicas ARCA/AFIP

**NUNCA usar:** "AltokeTicket", "example.com", lorem ipsum, texto en inglés visible al cliente.

### Archivos de referencia actualizados
- `.opencode/agents/ayuda.md` — sección "Datos Fiscales y Corporativos de TukiPass"
- `AGENTS.md` — sección "DATOS FISCALES Y CORPORATIVOS"
- `CLAUDE.md` — sección "DATOS FISCALES Y CORPORATIVOS"
