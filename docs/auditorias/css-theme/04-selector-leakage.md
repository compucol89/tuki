# 04 — Selector Leakage (lección del bug del icono azul)

## El bug (2026-08-21, corregido)

`atlantis.css:866`:

```css
.sidebar .nav.nav-primary > .nav-item.active a i {
  color: #1572E8 !important;   /* azul */
}
```

**Problema:** el selector usa `a i` (descendente, no `> a > i`). Alcanza a
**TODO** el submenu del item activo, no solo al icono del item padre. El
sub-item activo y sus hermanos dentro de un `.nav-item.active` recibían azul.

Además, atlantis declara reglas dark posteriores (blanco) **sin `!important`**
mientras la azul sí lo tiene → la cascada ganaba azul.

## Cómo se demostró

Cascade real con `i.matches(rule.selectorText)`:

| Regla | Spec | !important | Resultado |
|-------|------|-----------|-----------|
| `.sidebar .nav.nav-primary > .nav-item.active a i` (atlantis:866) | (0,6,2)* | sí | **ganaba** |
| `html[data-theme="dark"] .sidebar .nav > .nav-item a i` (theme-dark) | (0,4,3) | sí | perdía |

*El grupo del vendor incluye un selector con `[data-background-color="white"]`, elevando la spec del grupo a (0,6,2).

## El fix (Vendor Override en admin-skin.css)

Reglas con **misma especificidad + `!important` + orden posterior**:

```css
.sidebar[data-background-color] .nav.nav-primary > .nav-item.active a i,
.sidebar[data-background-color] .nav.nav-primary > .nav-item.selected a i {
  color: var(--sidebar-icon) !important;          /* neutralizador (mismo alcance que el vendor) */
}
.sidebar[data-background-color] .nav.nav-primary > .nav-item.active > a i { ... }
.sidebar[data-background-color] .nav.nav-primary > .nav-item.active .nav-collapse li.active > a i { ... }
.sidebar[data-background-color] .nav.nav-primary > .nav-item > a[data-toggle][aria-expanded="true"] i { ... }
.sidebar[data-background-color] .nav.nav-primary > .nav-item a:hover i { ... }
```

## Regla preventiva (NO NEW DEBT)

Un selector de estado con descendiente **sin `>`** debe revisarse:
- `active a i` → alcanza nietos → ¿es intencional?
- Si no es intencional: `active > a > i`.

Verificación automatizada: `npm run test:theme` (assert: 0 iconos `#1572E8`).
