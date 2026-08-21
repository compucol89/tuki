# 08 — Theme State Machine

**Fecha:** 2026-08-21 · **Estado:** post-remediación

## Antes (desync confirmado)

```
SERVER RENDER (DB: theme_version)
      ↓
HTML: html[data-theme] = DB value
      ↓
JS bootstrap: applyTheme(currentTheme(), false)
      ↓
TOGGLE click → DOM + localStorage  ← ¡NO DB!
      ↓
RELOAD → server renderiza DB (viejo) → flash a localStorage → inconsistencia
```

## Después (unificado)

```
DATABASE = fuente canónica (cross-device)
      ↓
SERVER RENDER → html[data-theme] = DB  (primer paint correcto, sin flash)
      ↓
JS bootstrap → DOM = data-theme; radios sincronizados
      ↓
USER TOGGLE:
  1. DOM update inmediato (data-theme + body)
  2. radios sincronizados (checked = theme)
  3. localStorage = theme (cache bootstrap)
  4. fetch POST → change_theme (CSRF, whitelist light|dark)
  5. fallo → revertir DOM a los radios (valor DB) + localStorage
      ↓
RELOAD → server renderiza DB actualizada → coherente
```

## Casos de borde manejados

| Caso | Comportamiento |
|------|----------------|
| Double click / rapid toggling | cada click dispara fetch; el último valor gana (idempotente) |
| Fallo de red en fetch | `.catch` → revertir DOM al valor de los radios (DB) |
| Multiple tabs | cada tab lee DB al cargar; sin broadcast (aceptado) |
| Logout/login | server renderiza DB del usuario → coherente |
| Valores inválidos en request | whitelist `['light','dark']` en controlador → fallback 'light' |
