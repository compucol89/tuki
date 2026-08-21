# 18 — Token / Hardcode Inventory

**Fecha:** 2026-08-21 · **Método:** inventario estático de `<style>` inline de blades + gate automático

## Blades migrados — estado final

| Blade | Hex antes | Hex después | Tokens usados |
|-------|-----------|-------------|---------------|
| event/index | 4 | **0** | --status-info-fg, --status-warning-fg, --border-default, --text-on-accent |
| event/edit | 33+7 | **0** | --status-*, --border-*, --surface-*, --sidebar-accent, --text-on-accent |
| event/ticket/create | 4 | **0** | --surface-card, --surface-card-soft, --border-* |
| event/ticket/edit | 4 | **0** | idem |
| booking/index | 0 | 0 | (ya migrado) |
| booking/details | 0 | 0 | (ya migrado) |
| event/create | 0 | 0 | (ya migrado) |
| ticket/index | 0 | 0 | (ya migrado) |
| telegram-bot | 0 | 0 | (ya migrado) |
| ai-generate-button | 0 | 0 | (ya migrado) |

**Total migrado en esta auditoría: 48 valores** (4+40+4) con intención semántica
verificada por contexto de selector (no search/replace ciego).

## Mapeo semántico aplicado (ejemplos representativos)

| Valor | Selector | Intención | Token |
|-------|----------|-----------|-------|
| #2563eb | .oe-title:hover | azul info (link hover) | --status-info-fg |
| #9a3412 | .oe-pill | naranja warning (texto sobre bg warning) | --status-warning-fg |
| #e7eaf0 | .oe-progress | track neutro | --border-default |
| #fff | .oe-status-select | texto sobre accent | --text-on-accent |
| #e8f1ff | .event-cover-box | fondo info suave | --status-info-bg |
| #f8fbff | gradient forms | superficie card suave | --surface-card-soft |
| #bbf7d0 | .is-success border | verde suave | rgba(22,163,74,.35) |
| #16a34a | border-left success | verde semántico | --status-success-fg |
| #fecaca | .is-danger border | rojo suave | rgba(220,38,38,.35) |

## Valores no migrados (intencionales)

| Valor | Dónde | Por qué |
|-------|-------|---------|
| #1DB954 | Spotify icon | color de marca externa |
| #FF0000 | YouTube icon | color de marca externa |
| rgba(15,23,42,.04) | box-shadows | sombras (no superficies) |

## Gate automático

`scripts/audit-organizer-theme.sh` → check `blade_raw_colors`: 0 nuevos raw
colors en blades migrados (baseline actualizado). Detector: hex + rgb/rgba/hsl
+ white/black, excluyendo white-space/shadows. **PASS**.
