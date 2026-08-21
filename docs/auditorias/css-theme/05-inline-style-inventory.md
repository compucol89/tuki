# 05 — Inline Style Inventory (post-migración)

**12 blades** con `<style>` en el Organizer (inventario real, `grep -rl "<style>"`):

| Blade | Líneas <style> | Prefijos | Estado | Hardcoded restantes |
|-------|---------------|----------|--------|---------------------|
| event/index | 317 | .oe-* | ✅ tokens | 0 |
| booking/index | 734 | .ob-* | ✅ tokens | 0 |
| booking/details | 374 | .bod-* | ✅ tokens | 0 |
| event/create | 590 | .create-*, .ai-* | ✅ tokens | 2 (marca: #1DB954 Spotify, #FF0000 YouTube) |
| event/edit | 371 | .ai-*, .event-* | ✅ tokens | 0 |
| ticket/create | 22 | .ticket-form-* | ✅ tokens | 0 |
| ticket/edit | 22 | .ticket-form-* | ✅ tokens | 0 |
| ticket/index | 92 | .ticket-* | ✅ tokens | 0 |
| index (dashboard) | 195 | .od-* | ✅ tokens (ya tenía) | 0 |
| telegram-bot | 55 | .tb-* | ✅ tokens | 0 |
| ai-generate-button | 165 | .ai-* | ✅ tokens | 0 |
| edit-profile | 708 | .ep-* | ✅ tokens (ya tenía) | 0 |

**Tokens usados** (verificado por familia): `--surface-*`, `--text-*`, `--border-*`, `--status-*`, `--sidebar-accent`.

**Nota metodológica:** la migración se hizo reemplazando superficies/textos/bordes
por `var(--...)` dentro del `<style>` de cada blade (mismo lugar, mínimo diff).
La extracción física a hojas externas es deuda opcional futura — no requerida
por el contrato de theming.

**Guard:** `npm run audit:organizer-theme` falla si aparece un `<style>` nuevo
sin estar en `scripts/baseline-theme.json`.
