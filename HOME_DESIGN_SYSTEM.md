# TukiPass Design System — Home Reference

## 1. Propósito

Documentar el sistema visual del home de TukiPass para replicarlo de forma consistente en páginas públicas futuras, sin introducir dependencias nuevas ni romper Bootstrap 4.

## 2. Alcance

- Frontend público (Blade + Bootstrap 4 + CSS existente).
- No aplica a panel **admin** ni dashboard **organizer** como producto a rediseñar; el **aspecto de CTAs** en login/registro cliente sigue el §7 si comparten `.theme-btn` / `.auth-guest-btn`.
- **Checkout, pagos, gateways y lógica de auth** no se modifican en tareas solo visuales; este documento no autoriza tocar controladores ni rutas.
- No introducir Tailwind, React, Vue ni librerías nuevas.

## 3. Nombre operativo del estilo

**`TukiPass Warm SaaS Orange`**

Descripción breve: identidad cálida y profesional tipo SaaS, con naranja como acción, tipografía Inter, jerarquía clara, superficies con aire, cards redondeadas y CTAs visibles.

## 4. Principios visuales

- Cálido pero profesional; naranja como color de acción principal en la marca.
- Gris oscuro (`#1e2532`) para títulos y estructura.
- Espaciado generoso entre bloques en el home (`body.home-page`).
- Cards con bordes suaves y sombras ligeras.
- **Inter** como familia tipográfica del stack público (variables `--tuki-font-sans` / `--base-font`).
- Componentes reutilizables (hero, marquee, headers de sección, cards de evento) en lugar de estilos one-off por página.
- Responsive apoyado en Bootstrap 4 y reglas en `responsive.css` / bloques `@media` en `style.css`.

## 5. Tokens visuales

Tabla operativa (alineada con auditoría; usar como referencia de diseño). Donde exista CSS, preferir **variables** en lugar de repetir hexadecimales.

| Token | Valor |
| --- | --- |
| Primary | `#F97316` |
| Primary hover (texto/enlaces) | `#C2410C` (`--primary-text-color` en `:root` de `style.css`) |
| Dark | `#1e2532` |
| Light | `#F7F7F7` (`--light-color`) |
| Text | `#454545` (`--base-color`) |
| Muted | `#6b7280` |
| Border | `#eaecf0` (en layout también aparecen `#e5e7eb` / `--tuki-border`) |
| Font | `'Inter', sans-serif` (vía `--tuki-font-sans`) |
| Radius SM | `6px` (guía auditoría) |
| Radius MD | `8px` |
| Radius LG | `12px` |
| Shadow SM | `0 2px 8px rgba(0,0,0,0.06)` |
| Shadow MD | `0 8px 24px rgba(0,0,0,0.10)` |
| Section padding desktop | `80px 0` (guía auditoría) |
| Section padding mobile | `60px 0` (guía auditoría) |

**Implementación real en el home (`body.home-page` en `style.css`):** el espaciado vertical de secciones usa `--home-section-space: 72px`, `--home-section-space-mobile: 48px` (y variantes en breakpoints). Conviene reutilizar esas variables al adaptar páginas que deban “sentirse” como el home.

**Variables útiles en código**

- `:root` en `public/assets/front/css/style.css`: `--heading-color`, `--primary-color`, `--primary-text-color`, `--light-color`, `--base-color`, escala `--tuki-text-*`, pesos `--tuki-weight-*`, `line-height` `--tuki-leading-*`.
- Bloque inline en `resources/views/frontend/layout.blade.php`: `--tuki-primary`, `--tuki-dark`, `--tuki-muted`, `--tuki-radius-*`, `--tuki-shadow-*`, `--tuki-space-*` (espaciado 4–48px).
- `resources/views/frontend/partials/styles.blade.php` redefine `--primary-color` con el color configurado en admin (`$basicInfo->primary_color`); el default del tema apunta al naranja de marca.

## 6. Tipografía

- **Inter** como fuente principal (`--tuki-font-sans` / `--base-font` / `--heading-font`).
- Pesos disponibles en tokens: **400, 500, 600, 700** (y **800** en algunos títulos de componentes).
- Títulos: color `--heading-color` (`#1e2532`).
- Metadatos / secundario: `#6b7280` o `var(--tuki-muted)` según contexto.
- Escala fluida: variables `--tuki-text-xs` … `--tuki-text-4xl` con `clamp()` en `style.css`.
- Line-height recomendado: cuerpo global `1.75` en `body`; para titulares usar `--tuki-leading-tight` / `--tuki-leading-snug` cuando se agreguen componentes nuevos acordes al sistema.

## 7. Botones

### CTA canónico (compra / confirmar / enviar)

**Referencia única en código:** **`.ed-buy-btn`** (sidebar detalle de evento) y, con el mismo patrón visual, **`.theme-btn`** en `public/assets/front/css/style.css`.

| Aspecto | Valor |
| --- | --- |
| Fondo | `#f97316` |
| Hover | `#ea580c` + `translateY(-1px)` |
| Texto | blanco |
| Tipografía | Inter, **14px**, peso **800**, `letter-spacing: -0.01em` |
| Altura mínima | **52px** (`min-height`; ancho fluido según contenedor) |
| Radio | **15px** |
| Sombra | **ninguna** (sin halo naranja/verde; regla global al final de `style.css` fuerza `box-shadow: none` en `.theme-btn`) |
| Focus | `outline: 3px solid rgba(249, 115, 22, 0.28)` donde aplica |

**Auth (login/registro cliente):** **`.auth-guest-btn--cta`** y **`.auth-guest-btn--green`** usan el mismo naranja y hover que arriba (el modificador `--green` ya no es Spotify-verde en superficie).

### Hero (home)

- **`.hero-btn`**: aparte del CTA canónico: pill claro/oscuro sobre collage (ver CSS hero).
- **`.hero-btn--primary`**: CTA claro sobre hero oscuro.
- **`.hero-btn--secondary`**: secundario sobre hero.

### Notas

- **`.btn-theme` / `.btn-outline-theme`**: no existen como clases en `style.css`; usar **`.theme-btn`** o **`.ed-buy-btn`** según contexto.
- **No** crear una clase nueva por página para el mismo tipo de acción.

## 8. Cards de evento (home y listados)

- El partial de Blade se incluye como `@include('frontend.partials.event-card')`, pero el **bloque visual en DOM** usa la familia **`.ev-card`** (BEM: `.ev-card__visual`, `.ev-card__body`, etc.), definida en `style.css` (“EVENT CARD — Tukipass”).
- Rasgos principales en código: `border-radius` vía `--ev-card-radius` (**20px** en la implementación actual), imagen con **aspect-ratio 4/3**, borde y sombras multicapa, acento naranja en hover y `focus-visible`.
- Cuando la documentación o Figma digan “`.event-card`”, interpretar como **esta implementación `.ev-card` + partial `event-card`**.

## 9. Section headers

- **`.hs-header`**: flex, título (`.hs-header__title`), subtítulo (`.hs-header__sub`), CTA opcional (`.hs-header__cta`).
- **`.section-title`**: patrón amplio del theme (títulos de sección con `h2`, márgenes tipo `mb-55` / `mb-30` en home).
- Mantener jerarquía: título claro, subtítulo breve, una acción opcional alineada a la derecha en desktop.

## 10. Layout

- Contenedor: **`.container`** (Bootstrap 4).
- Home: padding vertical de secciones vía `--home-section-space` / `--home-section-space-mobile` (ver §5).
- Grillas: 3 / 2 / 1 columnas según breakpoints Bootstrap; gaps del orden de **24px** (`--tuki-space-6`) donde aplique.

## 11. Navbar / Footer

- Navbar y footer en tono **oscuro** acorde a `#1e2532` / `--heading-color` en el CSS existente.
- Links claros; hover con acento **naranja** donde ya esté definido en `menu.css` / `style.css`.
- Footer en columnas según maquetación actual del layout.

## 12. Do / Don’t

### Do

- Reutilizar variables CSS existentes (`:root` y bloque `--tuki-*` del layout).
- Mantener **Inter** y textos en **español rioplatense / neutro argentino** para el cliente final.
- Usar Bootstrap 4 y componentes ya presentes: `.hs-header`, `.section-title`, `.events-marquee`, `.ev-card`, `.hero-btn` donde corresponda.
- Avanzar por **una página pública por tanda**, con diff acotado.

### Don’t

- No introducir Tailwind, React ni Vue.
- No hardcodear colores nuevos si ya existe un token o variable equivalente.
- No mezclar otras familias tipográficas en el frontend público.
- No sumar estilos inline masivos salvo casos puntuales ya establecidos.
- No tocar checkout, pagos, gateways, webhooks, auth, guards, rutas sensibles, migraciones, seeds, base de datos ni `.env` en tareas de solo UI.
- No intentar “limpiar todo el CSS legacy” en la misma tarea que adaptar una página.

## 13. Checklist para adaptar una página pública

- [ ] Usa Inter (`--tuki-font-sans` / fuentes cargadas como hoy).
- [ ] Primary de acción coherente con `#F97316` / `var(--primary-color)`.
- [ ] Títulos con `#1e2532` / `var(--heading-color)`.
- [ ] Cards: preferir **`.ev-card`** si la página lista eventos; radio consistente con el sistema (20px en card actual o tokens de layout para otros bloques).
- [ ] Botones alineados al patrón existente (hero vs resto del sitio).
- [ ] Responsive probado (mobile + desktop).
- [ ] No toca zonas sensibles (checkout, pagos, auth).
- [ ] No agrega dependencias nuevas.
- [ ] Textos en español.
- [ ] Diff pequeño y revisable.

## 14. Archivos fuente

| Archivo | Rol |
| --- | --- |
| `resources/views/frontend/home/index-v1.blade.php` | Vista del home (hero, marquee, secciones). |
| `resources/views/frontend/layout.blade.php` | Layout base, meta, tokens `--tuki-*` inline. |
| `resources/views/frontend/partials/styles.blade.php` | Encadenamiento CSS + override dinámico de `--primary-color`. |
| `resources/views/frontend/partials/event-card.blade.php` | Markup de card de evento (clases `.ev-card`). |
| `public/assets/front/css/style.css` | CSS principal custom (tokens globales, home, componentes). |
| `public/assets/front/css/responsive.css` | Ajustes responsive transversales. |
| `public/assets/front/css/menu.css` | Navegación / menú. |

Referencia de build: `webpack.mix.js` (compilación de assets vía Laravel Mix 6).

## 15. Estrategia de adopción

- Adaptar **una** página pública por tanda; validar visualmente desktop y mobile.
- No rediseñar todo el sitio en un solo PR o sesión.
- No mezclar refactor global de CSS legacy con migración de una vista.
- Evitar checkout, pagos y auth en el mismo alcance que cambios cosméticos.
- Próximo paso práctico: elegir una URL concreta (por ejemplo `/about`, `/contact`, FAQs, listado de eventos o detalle de evento) y aplicar este documento como checklist.

---

## Notas de consistencia (auditoría vs código)

- Los **hero CTAs** (`.hero-btn--primary`) son **blancos sobre hero oscuro**; el naranja protagonista aparece en marca, enlaces, acentos y otros bloques — no forzar el hero a un botón naranja sólido sin revisar contraste y diseño.
- **`.btn-theme` / `.btn-outline-theme`**: convención deseable; **pendiente de existir en CSS** — ver §7.
- **Card de evento**: usar **`.ev-card`**, no la clase literal `.event-card`.
- **Espaciado de secciones del home**: valores reales **72px / 48px** vía variables, no exactamente 80/60 salvo que se unifique en una tarea dedicada.

---

*Documento generado solo como referencia interna. No sustituye revisión visual ni pruebas en dispositivos reales.*
