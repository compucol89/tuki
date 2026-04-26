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

## Auditoría tipográfica (evento detalle — completada 2026-04-25)

### Fuentes cargadas
- **Inter** (`--base-font`): cuerpo de texto, base del sistema
- **Plus Jakarta Sans** (`--heading-font`): headings, títulos de cards
- **Sora**: componentes específicos (badges, countdowns, status pills)
- **Font Awesome 5 Free**: iconos
- Monospace: booking IDs, código

### Escala de headings (ya usa clamp, bien)
| Elemento | Tamaño | Line-height |
|----------|--------|-------------|
| h1 | `clamp(28px, 5vw + 8px, 48px)` | 1.15 |
| h2 | `clamp(24px, 4vw + 8px, 42px)` | 1.2 |
| h3 | `clamp(20px, 2.5vw + 6px, 30px)` | 1.4 |
| h4 | `clamp(18px, 1.5vw + 6px, 22px)` | 1.46 |
| h5 | `clamp(16px, 1vw + 6px, 20px)` | 1.5 |
| h6 | `clamp(14px, 0.5vw + 6px, 16px)` | — |

### Pesos en uso
- **300**: light (raro, algunos decorativos)
- **400**: regular (body, precios tachados)
- **500**: medium (links, labels, botones secundarios)
- **600**: semibold (meta items, badges)
- **700**: bold (headings, card titles)
- **800**: extra-bold (hero title, precios principales)
- **900**: black (raro, contadores)
- **⚠ No estándar**: 450, 650, 750, 35px (bug en línea 4203) — deben normalizarse

### Problemas críticos
1. **488 font-size hardcodeados en px** en `style.css` — cero uso de `rem` o tokens tipográficos
2. **Line-heights mezclados**: unitless (1, 1.04, 1.05…1.75) Y fijos en px (20px–120px) — los fijos rompen accesibilidad
3. **Fuentes duplicadas**: `var(--heading-font)` coexiste con `'Plus Jakarta Sans', sans-serif` hardcodeado en ~60+ reglas
4. **Sin tokens de escala**: no existen `--tuki-text-sm`, `--tuki-text-base`, `--tuki-text-lg`, etc.
5. **Hero title**: `clamp(26px, 3.4vw + 8px, 52px)` + `font-weight: 800` + `letter-spacing: -0.055em` — bien diseñado pero aislado del sistema

### Tokens CSS existentes (`:root`)
- `--base-font`, `--heading-font`: familias
- `--base-color`, `--heading-color`, `--primary-color`, `--primary-text-color`: colores
- `--flow-space`, `--flow-space-tight`, `--flow-space-heading`: espaciado de flujo
- **Faltan**: `--tuki-text-*` (escala), `--tuki-weight-*` (pesos), `--tuki-leading-*` (line-heights)

### Recomendación de sistema tipográfico
```css
:root {
  /* Escala de texto (mobile → desktop via clamp) */
  --tuki-text-xs:   clamp(11px, 0.8vw + 9px, 12px);
  --tuki-text-sm:   clamp(12px, 0.8vw + 10px, 14px);
  --tuki-text-base: clamp(14px, 0.8vw + 12px, 16px);
  --tuki-text-lg:   clamp(16px, 1vw + 14px, 18px);
  --tuki-text-xl:   clamp(18px, 1.2vw + 15px, 22px);
  --tuki-text-2xl:  clamp(22px, 1.5vw + 18px, 28px);
  --tuki-text-3xl:  clamp(26px, 2vw + 22px, 36px);
  --tuki-text-4xl:  clamp(32px, 2.5vw + 26px, 48px);

  /* Pesos semánticos */
  --tuki-weight-regular: 400;
  --tuki-weight-medium:  500;
  --tuki-weight-semibold: 600;
  --tuki-weight-bold:    700;
  --tuki-weight-extrabold: 800;

  /* Line-heights semánticos */
  --tuki-leading-tight: 1.15;
  --tuki-leading-snug:  1.3;
  --tuki-leading-normal: 1.5;
  --tuki-leading-relaxed: 1.75;
}
```
