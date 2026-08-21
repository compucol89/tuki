# 06 — Estado de migración de blades a tokens (post-fix)

**Contrato:** ningún color hardcodeado en CSS inline de blades del Organizer; superficies y
texto vía tokens `--surface-*/--text-*/--border-*/--status-*` (light + dark automático).

## Estado por blade crítico

| Blade | Antes | Después | Evidencia |
|-------|-------|---------|-----------|
| `organizer/event/create.blade.php` | **59 hex, 0 dark blocks** | **0 hex, 109 refs token, 7 dark blocks** | Render server-side verificado (2026-08-21) |
| `organizer/event/booking/index.blade.php` | 7 hex + mezcla `--adm-*`/`--surface-*` | **0 hex, 80 refs token**; solo 8 refs `--adm-primary*` (paleta de marca, sin equivalente unified) | Render server-side verificado |

## Mapeo aplicado en create.blade.php (30 colores únicos)

Documentado en `11-color-mapping-create.md`.

## Decisiones

- `--adm-card/ink/muted/border` (UI neutra) → `--surface-card/--text-primary/--text-secondary/--border-default`.
- `--adm-primary*` se mantiene: es la paleta de marca (naranja) definida en `admin-skin.css`
  junto a los tokens unified; no tiene equivalente en los 16 tokens semánticos.
- Badges con fondo verde sólido + texto blanco reciben override dark (fondo `--status-success-bg`).

## Gate

`scripts/audit-organizer-theme.sh` (NO NEW DEBT): hex nuevos = 0; `!important` 868 vs baseline 882.
