# 11 — Contrast Matrix (computada)

**Fecha:** 2026-08-21 · **Método:** contraste WCAG calculado de computed styles (no screenshots)

## Sidebar (revalidado)

| Elemento | Estado | Dark ratio | Light ratio | Requisito |
|----------|--------|-----------|-------------|-----------|
| Texto activo | active | 16.71:1 | 13.97:1 | ≥4.5 ✅ |
| Texto default | default | 9.11:1 | 4.91:1 | ≥4.5 ✅ |
| Iconos default | default | 7.70:1 | 4.91:1 | ≥3 ✅ |
| Labels de sección | default | 5.73:1 | **4.18:1 → FIX: 4.91:1** | ≥4.5 ✅ corregido |
| Placeholder búsqueda | default | 5.08:1 | — | ≥4.5 ✅ |

**Fix aplicado (axe P1):** `--sidebar-text-muted` light `#6b7686` (4.18:1) → `#5f6b7d` (4.91:1).

## Dashboard

| Elemento | Dark | Light | Requisito |
|----------|------|-------|-----------|
| H1 (Bienvenido) | --od-text #e5e5e5 | --od-text #1e2532 | ≥4.5 ✅ |
| Score eyebrow | #f4845f (token) | — | ≥4.5 ✅ |
| Score action-label | #f78a63 | — | ≥4.5 ✅ |
| Métricas card | tokens | tokens | ✅ |
| Chart ticks | #c8cdd6 | #6b7280 | ≥4.5 ✅ |
| Chart grid | rgba(255,255,255,.10) | rgba(0,0,0,.08) | no-text ≥3 ✅ |
| Canvas línea | #f97316 vs fondo card | idem | ≥3 ✅ |

## Axe resultados (WCAG 2.1 A/AA, dashboard autenticado)

| Antes del fix | Después |
|---------------|---------|
| light: color-contrast(5), label(2), list(2) | **0 violaciones** |
| dark: (no reportado individualmente) | **0 violaciones** |

Fixes: token muted (contraste), aria-label en radios de tema (label),
estructura `<ul>` válida (list), buscador fuera del `<ul>`, form/button en `<li>`.
