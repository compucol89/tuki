# 16 · Análisis Google Search Console — TukiPass (exports 2026-08-21)

Fuente: `docs/auditorias/google-search/gsc/*.zip` (Performance, Coverage, Events, Breadcrumbs, Https).

## Performance (todos los períodos del export)

| Página | Clics | Impresiones | CTR | Posición |
|---|---:|---:|---:|---:|
| `/` | 76 | 176 | 43% | 4.5 |
| `/sobre-nosotros` | 8 | 127 | 6% | 2.5 |
| `/eventos` | 4 | 101 | 4% | 5.1 |
| `/preguntas-frecuentes` | 3 | 118 | 2.5% | 1.9 |
| `/blog` | 0 | 60 | 0% | 1.7 |
| `/recuperar-contrasena` | 0 | 18 | 0% | 2.2 |
| `/minitk-dosmilera-en-la-troja-rumba-2000-en-bu…` (evento demo legacy) | 0 | 12 | 0% | 6.8 |
| **`/organizer/details/1/admin?admin=true` (perfil fantasma)** | 0 | **6** | 0% | 6.8 |
| `/blog/morbi-in-sem-quis-dui-placerat-ornare…` (post demo Lorem) | 0 | 2 | 0% | 3.5 |
| `/organizer/details/29/Rumba-Colombiana` (variante UPPERCASE) | 0 | 1 | 0% | 7 |

**Consultas:** 46 clics / 53 impresiones para "tukipass" (marca); el resto 1-4 impresiones sin clics
(colombia vs suiza, fan fest palermo, como comprar entradas, etc.). **Sitio muy joven en Google.**

## Coverage (Page Indexing)

- **0 problemas críticos · 0 no críticos** (reporte vacío — sin errores de indexación reportados).
- Implica: lo indexado hoy no genera errores, pero el universo indexado es mínimo.

## Events (rich results — structured data)

| Incidencia | Elementos |
|---|---|
| Falta el campo "offers" | 1 |
| Falta el campo "performer" | 1 |
| Falta el campo "validFrom" (en offers) | 0 |

→ Coincide con el crawler: 6 eventos emiten Event schema **sin offers/performer**.

## Breadcrumbs / Https

- Breadcrumbs: sin problemas. · Https: sin problemas (0 filas).

## Conclusiones GSC

1. **El perfil fantasma `?admin=true` recibe impresiones de Google** → P0 confirmado con evidencia GSC.
2. **Deuda demo confirmada**: URL legacy de evento demo + post blog Lorem siguen con impresiones.
3. **Variante UPPERCASE del organizador 29 indexada** (por el schema organizer.url).
4. Cobertura: el sitio tiene poca presencia — **momento ideal para limpiar antes de crecer**.
5. `/recuperar-contrasena` (auth) con 18 impresiones → candidata a noindex.
