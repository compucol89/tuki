# 09 — Theme Desync Remediation

**Fecha:** 2026-08-21 · **Issue:** THEME-001 (P1) · **Estado:** FIXED → VERIFIED

## Defecto

El toggle del panel (`[data-theme-toggle-panel]`) actualizaba DOM + localStorage
pero NO persistía a la DB. El formulario de radios persistía a DB (POST con
reload) pero no actualizaba localStorage. Resultado: al recargar, la DB y el
localStorage podían divergir → el tema saltaba al valor de DB en el server
render y luego al de localStorage tras el JS bootstrap.

## Root cause

Dos mecanismos independientes de persistencia sin reconciliación
(localStorage por el toggle, DB por el form), sin fuente canónica clara.

## Fix

**Arquitectura objetivo:** DB canónica (cross-device) + localStorage cache
bootstrap + DOM estado visual. Un solo mecanismo de cambio (el toggle JS)
que sincroniza TODO:

1. `OrganizerController@changeTheme`: whitelist `['light','dark']`, respuesta
   JSON para requests AJAX (`expectsJson`/`ajax`), `update()` (sin fetch previo).
2. `layout.blade.php` JS:
   - `applyTheme(theme, persist)`: DOM + radios sincronizados + localStorage +
     `persistServerTheme(theme)` (fetch POST con CSRF)
   - fallo de fetch → revertir a los radios (valor DB) — sin desync silencioso
3. El form de radios sigue funcionando (POST normal con reload) como fallback
   accesible sin JS; al cargar, `applyTheme(currentTheme(), false)` sincroniza.

## Verificado (runtime)

| Paso | Resultado |
|------|-----------|
| click toggle dark→light | DOM light + radios light-checked + localStorage light + DB light |
| reload | HTML nace con light (server) — sin flash |
| click light→dark | idem, DB dark |
| suite @theme 14/14 | PASS |

## Archivos

- `app/Http/Controllers/BackEnd/Organizer/OrganizerController.php` (changeTheme)
- `resources/views/organizer/layout.blade.php` (JS applyTheme/persistServerTheme)
