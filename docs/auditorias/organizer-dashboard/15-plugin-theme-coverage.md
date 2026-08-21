# 15 — Plugin Theme Coverage (dashboard)

**Fecha:** 2026-08-21 · **Método:** inventario de plugins reales cargados en /organizer/dashboard + cobertura dark

## Plugins reales en el dashboard

| Plugin | Versión | Se usa en dashboard? | Dark coverage |
|--------|---------|----------------------|---------------|
| Chart.js | 2.7.2 | ✅ 2 charts | ✅ corregido (F14) |
| Select2 | 1.10.23 bundle | solo si hay `<select>` (dashboard: no) | n/a en dashboard |
| DataTables | 1.10.23 | no (sin tablas) | n/a |
| TinyMCE | — | no | n/a |
| Dropzone | — | no (solo create/edit) | auditado en F3 (edit) |
| SweetAlert | — | no crítico | n/a |
| jQuery UI / datepicker / timepicker | — | no | n/a |
| Bootstrap 4.3.1 | 4.3.1 | sí (navbar, dropdown perfil) | ✅ theme-dark.css |

## Bootstrap 4.3.1 en dark (dashboard)

- `.navbar`: overrides en theme-dark.css (fondo/borde) ✅
- `.dropdown` (menú de perfil): cubierto por theme-dark ✅
- `.btn-secondary` (theme toggle): cubierto ✅

## Pattern scan — plugins con riesgo en otras rutas

Los plugins con dark gaps conocidos viven en rutas de formularios (create/edit:
dropzone, select2, datepicker, tagsinput). Esas rutas fueron tokenizadas en F3;
los adapters de plugin (select2/dropzone/datepicker dark) están en theme-dark.css
y se validan con los tests @theme de esas rutas (add-ticket, edit-event).

## Conclusión

El dashboard no carga plugins con gaps de tema: Chart.js corregido (F14),
Bootstrap/dropdown cubiertos. Sin hallazgos pendientes en esta superficie.
