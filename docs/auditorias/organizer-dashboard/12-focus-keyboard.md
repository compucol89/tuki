# 12 — Focus & Keyboard

**Fecha:** 2026-08-21 · **Método:** recorrido Tab real con Playwright (teclado nativo)

## Estado del sistema de foco

- `atlantis.css:79` — `:focus { outline: 0 !important }` (suppression global, vendor)
- `atlantis.css:87` — `:focus-visible { outline: 2px solid #f97316; box-shadow: 0 0 0 3px rgba(249,115,22,.35) }` (recuperación)
- **Conclusión WCAG**: el foco por **teclado** es visible (`:focus-visible` con outline 2px naranja + ring 3px). WCAG 2.4.7 requiere foco visible para teclado — **cumple**. El foco por mouse sin outline es una mejora UX opcional, NO un fallo WCAG (documentado como UX enhancement en el reporte).

## Verificado en runtime (dashboard, dark)

| Paso Tab | Elemento | :focus-visible | Outline |
|----------|----------|----------------|---------|
| 1 | skip-link "Saltar al contenido principal" | ✅ | solid 3px |
| 2 | logo "Eventos" (link) | ✅ | solid 2px rgb(194,75,43) |
| 3+ | (recorrido continúa por sidebar y contenido) | ✅ | — |

- Skip-link presente y enfocable primero (primer elemento Tab).
- Sin traps de teclado detectados (recorrido fluido).
- `Enter`/`Space` en toggles de submenú funcionan (Bootstrap collapse, verificado en auditoría previa).
- `ESC` cierra submenú y devuelve foco (handler propio en scripts.blade.php).

## Target size (WCAG 2.5.8 AA = 24×24 mínimo)

| Control | Tamaño real | Excepción aplicable | Resultado |
|---------|-------------|---------------------|-----------|
| Sidebar nav items | 40-42px alto, ancho completo | — | ✅ |
| Sub-items | 38-39px | — | ✅ |
| Radios de tema (topbar) | ~34px (label envolvente) | label es el target | ✅ |
| Botón theme-toggle-panel | ≥40×24 (fix previo) | — | ✅ |
| Topbar toggler.more | 40×24 (fix previo) | — | ✅ |

**Nota:** el objetivo interno TukiPass es 40-44px para controles frecuentes
(target más cómodo que el mínimo WCAG). Documentado como UX target, no como
requisito normativo.
