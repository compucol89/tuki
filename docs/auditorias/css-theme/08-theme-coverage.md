# 08 — Theme Coverage

Matriz verificada en runtime (Playwright, Chromium @ 1440px, `npm run test:theme` — 13 tests).

| Ruta | Light | Dark | Iconos #1572E8 | Islas blancas | Texto oscuro/dark | Overflow |
|------|-------|------|----------------|---------------|-------------------|----------|
| /organizer/dashboard | ✅ | ✅ | 0 | 0 | 0 | no |
| /organizer/event-management/events | ✅ | ✅ | 0 | 0 | 0 | no |
| /organizer/choose-event-type | ✅ | ✅ | 0 | 0 | 0 | no |
| /organizer/event-booking | ✅ | ✅ | 0 | 0 | 0 | no |
| /organizer/telegram-bot | ✅ | ✅ | 0 | 0 | 0 | no |
| /organizer/withdraw | ✅ | ✅ | 0 | 0 | 0 | no |
| event-booking/details/5 (manual) | — | ✅ | 0 | 0 | 0 | no |
| event/ticket?event_id=122 (manual) | — | ✅ | 0 | 0 | 0 | no |
| event/add-ticket (manual) | — | ✅ | 0 | 0 | 0 | no |
| edit-event/122 (manual) | — | ✅ | 0 | 0 | 0 | no |

**Contrastes clave del sidebar (computed, dark/light):**

| Elemento | Dark | Light |
|----------|------|-------|
| Texto activo | 16.71:1 | 13.97:1 |
| Texto default | 9.11:1 | 4.91:1 |
| Iconos default | 7.70:1 | 4.91:1 |
| Labels de sección | 5.73:1 | 4.91:1 |
| Placeholder búsqueda | 5.08:1 | — |

**Mobile 375px:** sin overflow horizontal, sin wraps (verificado en dashboard + events).

**Fuentes FA:** una sola request de fa-solid-900.woff2 (preload + @font-face misma URL),
`font-display: block`, caché immutable 1 año.
