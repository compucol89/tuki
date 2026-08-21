# 13 — Política anti "islas blancas" en dark

**Regla:** ninguna superficie de panel puede quedar blanca (`#fff`/`#ffffff`) cuando
`html[data-theme="dark"]` está activo.

## Cómo se garantiza

1. **Tokens semánticos obligatorios** para superficies/texto/bordes en CSS de blades del
   Organizer (`--surface-*`, `--text-*`, `--border-*`, `--status-*`): el valor dark cambia
   automáticamente. Hex directos prohibidos (gate: `audit-organizer-theme.sh`).
2. **Bloques dark explícitos** solo donde un componente necesita comportamiento distinto
   (ej. badges sólidos): `html[data-theme="dark"] .selector { … }` dentro del mismo `<style>`.
3. **theme-dark.css contiene SOLO reglas dark** (las 19 light rules fueron movidas a
   admin-skin.css — ver `09-light-rules-cleanup.md`).
4. **Runtime check en CI**: `theme.spec.js` recorre superficies clave (`.card`, `.oe-panel`,
   `.oe-toolbar`, `.ob-event-row`, `.ticket-free-limit`, `.event-cover-box`, …) y falla si el
   color computado es blanco en dark.

## Patrón para componentes nuevos

```css
/* Light: sin bloque — los tokens ya resuelven */
.component { background: var(--surface-card); color: var(--text-primary); }

/* Solo si el componente necesita ajuste específico en dark */
html[data-theme="dark"] .component.is-solid { background: var(--status-success-bg); }
```

## Pendiente (deuda conocida)

- Contraste WCAG por token (light/dark) no está automatizado aún en `theme.spec.js`
  (ver `15-known-gaps.md`).
