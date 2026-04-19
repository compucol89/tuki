# DESIGN — Página «Sobre nosotros» (Tuki)

Fusión deliberada de referencias **`design-md/apple`** + **`design-md/airbnb`** de [awesome-design-md](https://github.com/VoltAgent/awesome-design-md), adaptada al stack Blade/CSS del proyecto (sin Tailwind aquí).

## Fuentes de referencia (colección)

| Referencia | Rol en esta página |
|------------|-------------------|
| **Apple** | Mucho aire, jerarquía tipográfica clara, sombras muy suaves, superficies neutras casi monocromáticas, hero “cinemático” con imagen protagonista. |
| **Airbnb** | Esquinas redondeadas generosas, acento cálido en llamadas y detalles (en Tuki: `var(--primary-color)` en lugar del coral Airbnb), componentes tipo “card” acogedores. |

## Tokens (CSS) — `body.about-page`

Definidos en `resources/views/frontend/about.blade.php` (`@push('styles')`). Todos los componentes de esta vista deben consumirlos en lugar de hex/rgba sueltos cuando sea posible.

### Color y superficie

- `--about-surface-a` / `--about-surface-b`: franja alternada (fondo editorial).
- `--about-ds-ink`, `--about-ds-text`, `--about-ds-text-secondary`, `--about-ds-muted`, `--about-ds-muted-2`: texto con contraste WCAG sobre blanco/gris claro.
- `--about-ds-border-hair`, `--about-ds-border`, `--about-ds-border-soft`, `--about-ds-border-faint`: bordes / trazos neutros tipo “Apple sheet”.
- `--about-ds-accent-wash`: velo cálido muy suave (rol Airbnb → marca Tuki).
- `--about-ds-hero-scrim-1` … `--about-ds-hero-scrim-3`: gradiente del hero sobre foto (legibilidad tipo Apple).

### Radios (Airbnb “rounded UI”)

- `--about-ds-radius-xs` (14px), `--about-ds-radius-icon`, `--about-ds-radius-sm`, `--about-ds-radius-md`, `--about-ds-radius-lg`, `--about-ds-radius-xl`, `--about-ds-radius-pill`.

### Elevación (Apple “soft layered”)

- `--about-ds-shadow-card`, `--about-ds-shadow-card-soft`, `--about-ds-shadow-organizer`, `--about-ds-shadow-organizer-hover`, `--about-ds-shadow-chip`, `--about-ds-inset-highlight`.

### Ritmo vertical

- `--about-band-space` / `--about-band-space-mobile`: secciones ( `rem` = escala coherente con el sistema ).
- `--about-section-head-space`: título de sección → contenido.
- Donde aplique texto corrido: `lh` en márgenes (`margin-block`, `gap`) según bloque ya definido.

## Componentes

1. **Hero**: foto full-bleed + *scrim* (`--about-ds-hero-scrim`) + grano sutil; título display; migas en chip con *glass* (Airbnb chip + Apple minimalismo).
2. **Métricas + historia**: dos “sheets” con el mismo radio sombra y borde tokenizado.
3. **Organizadores**: grillas de cards con radio/card shadow alineados a tokens.
4. **Características**: cards con borde/sombra/accent line de `--about-ds-accent`.

## No hacer

- Mezclar nuevos hex fuera de tokens en esta página salvo degradados que referencien `--primary-color`.
- Romper contraste de enlaces y focus visible ya definidos.
