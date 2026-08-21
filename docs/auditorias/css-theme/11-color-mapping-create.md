# 11 — Mapa hex → token: `organizer/event/create.blade.php`

Migración de los **30 colores únicos** (59 ocurrencias) del bloque CSS inline
(`resources/views/organizer/event/create.blade.php:730-1345`) al token system unified.

## Mapa aplicado (por rol semántico)

| Rol | Hex original | Token |
|-----|--------------|-------|
| Texto principal | `#1e2532`, `#0f172a` | `--text-primary` |
| Texto secundario | `#334155`, `#475569` | `--text-secondary` |
| Texto sobre acento | `#fff`, `#ffffff` (en `color:`) | `--text-on-accent` |
| Superficie card | `#ffffff` | `--surface-card` |
| Superficie suave | `#f8fbff`, `#f8fafc`, `#eff6ff`, `#f1f5f9`, `#f3f7fd` | `--surface-card-soft` |
| Superficie toolbar | `#f8f9fc` | `--surface-toolbar` |
| Bordes | `#e5e7eb`, `#e2e8f0` → `--border-default`; `#eef2f7` → `--border-subtle`; `#d6d9e6` → `--border-strong`; `#dbe5f3` → `--border-default` | |
| Estado success | `#16a34a`, `#166534`, `#86efac` → `--status-success-fg`; `#f0fdf4`, `#f7fef9`, `#bbf7d0`, `#dcfce7` → `--status-success-bg` | |
| Estado warning | `#9a3412` → `--status-warning-fg`; `#fff7ed`, `#fed7aa` → `--status-warning-bg` | |
| Estado danger | `#dc2626` → `--status-danger-fg`; `#fff7f7`, `#fecaca` → `--status-danger-bg` | |
| Estado info | `#1d4ed8`, `#1e40af`, `#1e3a8a`, `#2563eb`, `#3b82f6` → `--status-info-fg`; `#e8f1ff`, `#dbeafe`, `#bfdbfe` → `--status-info-bg` | |

## Dark overrides añadidos

Badges con fondo verde sólido + texto blanco (`.create-cover-ai-requirement.is-ready strong`,
`.async-progress-panel.is-success`): en dark pasan a `--status-success-bg` + `--status-success-fg`.

## Verificación

- Render server-side: **0 hex, 109 referencias token, 7 bloques dark**.
- `scripts/audit-organizer-theme.sh`: PASS (sin deuda nueva).
