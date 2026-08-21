# 09 — Theme Desync Remediation

**Fecha:** 2026-08-21 · **Issue:** THEME-001 (P1) · **Estado:** FIXED → VERIFIED

## Defecto

El toggle del panel (`[data-theme-toggle-panel]`) actualizaba DOM + localStorage
pero NO persistía a la DB. El formulario de radios persistía a DB (POST con
reload) pero no actualizaba localStorage. Resultado: al recargar, la DB y el
localStorage podían divergir → el tema saltaba al valor de DB en el server
render y luego al de localStorage tras el JS bootstrap.

**Además**: el `.sidebar` (y logo/navbar-header) llevan su propio
`data-background-color` fijado por el servidor; `applyTheme` no lo actualizaba
→ el menú quedaba anclado al tema de la DB al renderizar y NO cambiaba en vivo
(especialmente visible en la dirección dark2→light).

## Root cause

Dos mecanismos independientes de persistencia sin reconciliación
(localStorage por el toggle, DB por el form), sin fuente canónica clara +
contenedores con `data-background-color` propio no sincronizados por el JS.

## Fix

**Arquitectura objetivo:** DB canónica (cross-device) + localStorage cache
bootstrap + DOM estado visual. Un solo mecanismo de cambio (el botón toggle)
que sincroniza TODO:

1. **Radios eliminados** (eran redundantes con el toggle y requerían reload).
   El botón toggle es el único control de tema en organizer y backend.
2. `OrganizerController@changeTheme` (y `AdminController@changeTheme`):
   whitelist `['light','dark']`, respuesta JSON para requests AJAX,
   `update()`/`updateOrInsert` sin fetch previo.
3. `layout.blade.php` (organizer + backend) JS:
   - `applyTheme(theme, persist)`: DOM + **`data-background-color` de
     `.sidebar`, `.logo-header` (dark2/white) y `.navbar-header` (dark/white)**
     + localStorage + `persistServerTheme(theme)` (fetch POST con CSRF)
   - `serverTheme` guarda el tema de DB al cargar; si el fetch falla →
     revertir a `serverTheme` (sin desync silencioso)
4. Sin JS: el server renderiza el tema de la DB (fallback natural).

## Verificado (runtime)

| Paso | Resultado |
|------|-----------|
| click toggle dark→light | DOM light + sidebar white + logo white + navbar white + localStorage light + DB light |
| reload | HTML nace con light (server) — sin flash |
| click light→dark | idem, DB dark + sidebar dark2 |
| fallback de red | revertir a serverTheme (DB) |
| suite @theme 14/14 | PASS |

## Archivos

- `app/Http/Controllers/BackEnd/Organizer/OrganizerController.php` (changeTheme)
- `app/Http/Controllers/BackEnd/AdminController.php` (changeTheme)
- `resources/views/organizer/layout.blade.php` (JS applyTheme/persistServerTheme + sync contenedores)
- `resources/views/backend/layout.blade.php` (idem)
- `resources/views/organizer/partials/top-navbar.blade.php` (radios eliminados)
- `resources/views/backend/partials/top-navbar.blade.php` (radios eliminados)
