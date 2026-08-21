# 02 — Estado del sidebar: sesión + scroll (evidencia)

**Componente:** `resources/views/organizer/partials/side-navbar.blade.php` + `app/Http/Controllers/BackEnd/Organizer/OrganizerController.php`.

## Cómo funciona

- El estado de secciones abiertas/cerradas se persiste **server-side** en `session('sidebar_state')`
  (`side-navbar.blade.php:2` lee; `OrganizerController.php:958` guarda vía AJAX en
  `shown.bs.collapse` / `hidden.bs.collapse`).
- El **scroll** se preserva en `sessionStorage` y se restaura al navegar (cero saltos entre páginas).

## Por qué

El menú del Atlantis re-renderiza al navegar; sin estado persistido, cada navegación colapsaba
las secciones y perdía la posición de scroll (regresión reportada en la auditoría).

## Verificación

- `grep sidebar_state` → sesión + restore (2 sitios).
- Manual: abrir sección → navegar → vuelve abierta; scroll → navegar → vuelve la posición.
