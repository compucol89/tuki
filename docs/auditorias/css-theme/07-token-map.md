# 07 — Token Map

Tokens semánticos definidos en `public/assets/admin/css/admin-skin.css`
(`:root` para light, `html[data-theme="dark"]` para dark).

## Light

```css
--surface-page: #f5f6f8;
--surface-card: #ffffff;
--surface-card-soft: #f8fafc;
--surface-toolbar: #fbfcfe;
--surface-input: #ffffff;
--surface-elevated: #ffffff;
--surface-hover: rgba(30, 37, 50, 0.05);
--surface-active: rgba(249, 115, 22, 0.12);

--text-primary: #1e2532;
--text-secondary: #5f6b7d;
--text-muted: #6b7686;
--text-on-accent: #ffffff;

--border-subtle: #eef1f5;
--border-default: #e7eaf0;
--border-strong: #d5dae3;

--status-success-bg: #f0fdf4;  --status-success-fg: #15803d;
--status-warning-bg: #fff7ed;  --status-warning-fg: #9a3412;
--status-danger-bg: #fff7f7;   --status-danger-fg: #b91c1c;
--status-info-bg: #e8f1ff;     --status-info-fg: #1d4ed8;

--focus-ring: rgba(249, 115, 22, 0.35);
```

## Dark

```css
--surface-page: #171e2b;
--surface-card: #232c3b;
--surface-card-soft: #283242;
--surface-toolbar: #1f2637;
--surface-input: #232c3b;
--surface-elevated: #2a3444;
--surface-hover: rgba(255, 255, 255, 0.06);
--surface-active: rgba(249, 115, 22, 0.16);

--text-primary: #f2f4f7;
--text-secondary: #b8c0cc;
--text-muted: #8e98a8;
--text-on-accent: #ffffff;

--border-subtle: rgba(255, 255, 255, 0.08);
--border-default: rgba(255, 255, 255, 0.12);
--border-strong: rgba(255, 255, 255, 0.18);

--status-success-bg: rgba(22, 163, 74, 0.14);  --status-success-fg: #86efac;
--status-warning-bg: rgba(249, 115, 22, 0.12); --status-warning-fg: #fdba74;
--status-danger-bg: rgba(220, 38, 38, 0.14);   --status-danger-fg: #fca5a5;
--status-info-bg: rgba(37, 99, 235, 0.16);     --status-info-fg: #93c5fd;
```

## Sidebar (dominio propio)

```css
--sidebar-bg / --sidebar-surface / --sidebar-surface-hover / --sidebar-surface-active
--sidebar-text-primary / --sidebar-text-secondary / --sidebar-text-muted
--sidebar-icon / --sidebar-active-text / --sidebar-active-icon
--sidebar-border / --sidebar-accent (#F97316)
```

## Aliases legacy mapeados

| Sistema | Equivalente | Estado |
|---------|-------------|--------|
| `--adm-*` (admin-skin :root) | escala base light | en uso |
| `--od-*` (dashboard blade) | override dark propio | en uso |
| `--tuki-font-ui` / `--tuki-font-data` | Inter / IBM Plex Mono | en uso |

## Contrato

- Un componente nuevo usa `--surface-*`/`--text-*`/`--border-*`/`--status-*`.
- No crear tokens duplicados semánticamente (`--dark-gray-2`, `--panel-dark`, …).
- Colores de marca (`--sidebar-accent`, gradients primarios) y estados semánticos
  son las únicas excepciones permitidas a hardcoded.
