# 16 — UX Dashboard

**Fecha:** 2026-08-21 · **Método:** revisión heurística (sin rediseño — solo defectos con tarea de usuario clara)

## Lo que funciona bien (revalidado)

- **H1 único** "Bienvenido de vuelta, {username}" — contexto claro de la página
- **Score de perfil** (od-profile-score): checklist de 7 pasos con progreso —
  guía accionable para completar el evento (cada item con href)
- **Métricas clave** (eventos, reservas, transacciones, settlement): números con
  fuente mono (IBM Plex Mono) para datos financieros
- **2 charts** por mes con labels accesibles
- Formato rioplatense y monolingüe (es-AR) sin mezcla ES/EN (validado en F33 previo)

## Defectos UX encontrados

| # | Defecto | Tarea afectada | Severidad | Estado |
|---|---------|----------------|-----------|--------|
| UX-1 | Sin empty state dedicado en charts cuando hay 0 datos (línea plana sin mensaje) | "¿Mi negocio está creciendo?" | P3 | DOCUMENTADO (gap) |
| UX-2 | El score ocupa el ancho completo en desktop pero el layout de cards métricas es compacto — jerarquía visual correcta, sin cambio | — | n/a | OK |
| UX-3 | Los labels de sección del sidebar eran ilegibles en light (4.18:1) | navegación | P1 | ✅ FIXED (F5) |
| UX-4 | El toggle de tema no persistía (DB) — tras recargar volvía al tema viejo | consistencia | P1 | ✅ FIXED (F2) |
| UX-5 | Los charts no reaccionaban al cambio de tema | lectura de datos en dark | P2 | ✅ FIXED (F14) |

## Mejoras registradas como FOLLOW-UP (fuera de scope)

- Empty state visual para charts (mensaje + ícono cuando sum(monthly)=0)
- Formato compacto de métricas financieras (k/M para montos grandes)
- Skeleton loading para las métricas (hoy render síncrono del server)

Estas mejoras requieren decisión de producto — registradas, no implementadas
(política: no rediseñar sin necesidad, auditoría forense).
