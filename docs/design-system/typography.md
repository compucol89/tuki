# Typography — TukiPass Design System

## Sistema tipográfico

| Rol | Familia | Pesos | Tokens |
|---|---|---|---|
| **UI / lenguaje** | Inter (self-hosted WOFF2, `font-display: swap`) | 400, 500, 600, 700 | `--tuki-font-ui` |
| **Datos numéricos** | IBM Plex Mono (self-hosted WOFF2) | 400, 500, 600, 700 | `--tuki-font-data` |
| Iconos | Font Awesome 6 Free / Brands | 400 / 900 | `fontawesome.css` |

```css
--tuki-font-ui:   'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
--tuki-font-data: 'IBM Plex Mono', ui-monospace, 'SFMono-Regular', Consolas, 'Liberation Mono', monospace;
```

**Regla oficial:** *Inter = lenguaje · Mono = datos numéricos y legibles por máquina.*
Una frase con un número ("Vendiste 14 entradas") se mantiene en Inter; un valor separado
como dato ("14") usa mono.

## Escala del panel (admin)

| Nivel | Tamaño | Peso | Uso |
|---|---|---|---|
| Page title | `clamp(22px, 1.6vw, 26px)` | 600 | Título de página |
| Section title | 18px | 600 | Secciones |
| Card title | 15–16px | 600 | Encabezados de card |
| Body | 14px | 400 | Texto base |
| Navigation | 14px | 500 (activo 600) | Sidebar |
| Form label | 13px | 500 | Labels |
| Inputs | 14px | 400 | Valores de formulario |
| Metadata | 12–13px | 400–500 | Hints, fechas, muted |
| Eyebrow | 11px | 600 | uppercase, `ls .10em` |
| KPI (dato) | 26–30px | 600 (mono) | Números destacados |

## Data typography (clases semánticas)

Aplicar a **datos**, no a lenguaje:

- `.tuki-data` — base mono + `font-variant-numeric: tabular-nums lining-nums`
- `.tuki-data-money` — montos, saldos, totales (mono, `white-space: nowrap`, right-align en tablas)
- `.tuki-data-percent` — porcentajes
- `.tuki-data-id` — IDs, números de operación
- `.tuki-data-count` — contadores, cantidades, stock
- `.tuki-stat-value` — valores de KPI

En tablas: el `<th>` de la columna numérica lleva la clase y el `<td>` también
(más alineación derecha automática).

## Do / Don't

- **Do:** separar datos de lenguaje; mono con `tabular-nums`; KPI destacado por tamaño, no por bold extra.
- **Don't:** `* { font-family: Inter !important }` (rompe Font Awesome); regex sobre dígitos; pesos 800/900 sin motivo; `opacity` para "muted".

## Charts (Chart.js 2.7)

- Leyenda / eje categoría (X) / título → **Inter**
- Eje numérico (Y) / tooltip value → **IBM Plex Mono**
- Config en `public/assets/admin/js/chart-init.js` (`tukiChartPalette` + `tukiInitLineChart`).

## Dark / Light

La tipografía no cambia entre temas; solo colores. El texto muted se define con tokens
(`--adm-muted`), nunca con `opacity` sobre contenedores.

## Fallbacks permitidos (whitelist)

Lato y Plus Jakarta Sans están **prohibidos** en el panel (no cargadas → causaban Helvetica).
El test de regresión falla si vuelven a computar en `.main-panel` / `.sidebar` / `.main-header`.

## Nota de excepción

La regla anterior del proyecto era "Single font — Inter". Este documento **la reemplaza
intencionalmente** por Inter + IBM Plex Mono (datos), como decisión de diseño documentada.
