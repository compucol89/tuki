---
name: tukipass-performance
description: Mide y optimiza el rendimiento real de TukiPass (PHP/OPcache, workers, consultas, caché, imágenes y assets del organizador) sin romper stock, pagos ni aislamiento. Úsala antes de tocar Dockerfile, docker-start, OPcache, workers, queries de home/evento/organizer, imágenes o cache. Exige medición antes/después y distingue HEAD de producción.
---

# TukiPass — Rendimiento y capacidad

## Cuándo usarla

- Cambios en `Dockerfile`, `docker-start.sh`, OPcache, `PHP_CLI_SERVER_WORKERS`.
- Cambios en `AppServiceProvider` (View Composer), `HomeController`, `EventController`, `BookingController`, `BasicController`.
- Cambios de caché (`CACHE_DRIVER`, TTLs, invalidación).
- Cambios de imágenes, thumbnails, CSS/JS del frontend o del panel organizador.
- Antes/después de un deploy que incluya performance.
- Antes de agregar Redis, CDN, queue workers o “optimizaciones” de infraestructura.

## Estado medido (2026-10-22)

La fuente de verdad son las mediciones y el código; este archivo es un mapa. **Medir el commit desplegado y HEAD por separado.**

| Medición | Producción `bbc843b1` | Código HEAD `2320b03e` |
|---|---|---|
| Servidor web | `php -S`, 1 worker | `PHP_CLI_SERVER_WORKERS=4` (`docker-start.sh:108`) |
| OPcache | off | on (`Dockerfile:65-72`, 128 MB, 20k archivos) |
| Blog query | 7–9 por página | cacheada 5 min (`AppServiceProvider.php:202`) |
| 20 requests a c5 | **2,602 s (~7,7 req/s)** | no medido en producción |
| 4 workers (local) vs 1 | — | **0,327 s vs 1,288 s (3,9×)** |
| TTFB home | 0,354–0,375 s (servidor ~0,17–0,23 s) | no medido |
| TTFB `/eventos` | 0,316–0,350 s (servidor ~0,17 s) | no medido |
| TTFB detalle | 0,399–0,528 s (servidor ~0,20–0,25 s) | no medido |
| Home queries (local) | 10–13; 7–9 blogs; SQL 33,6 ms; blogs 15,7 ms (47%) | blog cacheado |
| Detalle producción 128 | 120.886 B raw / 24.132 B gzip | no medido |
| Detalle evento (local, 124) | 45 queries / 224,7 ms | 19 queries / 50–56 ms; mismo contenido (2026-10-23) |
| Home móvil (4× CPU/1,6 Mbps) | 1.527 KB, 39 req; LCP 2,07–2,16 s; CLS 0,0198 | no medido |
| Organizer | 1,5 MB JS raw / 449 KB gzip + 351 KB gzip CSS/fuentes | dashboard ~437 KB JS raw / ~125 KB gzip; pesados condicionales (2026-10-23) |
| Tests | — | 263 pasan (190.963 assertions) |

**Conclusión empírica:** el problema principal del deploy viejo es **capacidad** (1 worker, OPcache off) y **payload de imágenes/assets**, no el byte size del HTML. El detalle de evento de producción pesa ~120 KB raw / ~24 KB gzip; los 550 KB/537 ms reportados antes eran un evento local outlier + emulación móvil.

## Implementado en HEAD, no desplegado

- OPcache en la imagen (`Dockerfile:65-72`).
- `PHP_CLI_SERVER_WORKERS=4` (`docker-start.sh:108`).
- Cache de últimos blogs 5 min (`AppServiceProvider.php:202`).
- Las mejoras de stock/pagos/QR del release guard.

Antes de “optimizar” de nuevo, verificar que el deploy tenga estos cambios. Si no, el primer paso es desplegar HEAD **con** `MERCADOPAGO_WEBHOOK_SECRET` y las migraciones.

## Estado medido (2026-10-23) — requests y caché

Medición local con el stack Docker (`php -S` + `PHP_CLI_SERVER_WORKERS=4`, OPcache on), Chromium 390×844, CPU 4×, red 1,6 Mbps / RTT 150 ms. Antes/después con el mismo método.

| Página | Requests | KB | LCP | Load | CLS |
|---|---|---|---|---|---|
| Home | 35 → 35 | 696 → 454 | 1692 → 1480 ms | 3959 → 2748 ms | 0,0198 |
| `/eventos` | 41 → 36 | 1262 → 421 | 4644 → 1636 ms | 6702 → 2690 ms | 0,01 |
| Detalle | 26 → 26 | 475 → 325 | 2200 → 1724 ms | 4128 → 3636 ms | 0 |

TTFB local (50 muestras, `route:cache` + caches calientes): home p50 37→27 ms / p95 60→41 ms; `/eventos` 34→25 / 45→39; detalle 40→27 / 48→34.

Consultas por request (mismo stack): home 6→3 (SQL 12,7→4 ms), `/eventos` 9→7 (13,8→7 ms), detalle 19→13 (21→7-11 ms).

Implementado y verificado:

- `route:cache` en `docker-start.sh` (no `config:cache`: el panel escribe credenciales de pasarelas en `.env` y quedarían congeladas hasta reiniciar).
- `getLanguage()`, `getCurrencyInfo()`, `Language::all()` (hreflang), `Admin::first()` público, `BillingSetting::current()` en detalle y `timeZoneOffset()` cacheados con invalidación donde aplica.
- `views_count`/`views_last_24h`: buffer append-only (`EventViewTracker`) + `events:flush-views` cada minuto; `ResetDailyViews` vuelca antes de resetear. Elimina el UPDATE con lock por visita.
- Font Awesome del frontend: subset de 53 iconos (`fa-solid-front.woff2`, 158 KB → 4,4 KB) generado con `scripts/build-fa-subset.py`; admin/organizer conservan la fuente completa.
- Hero de `/eventos` y detalle: slides secundarios con `data-bg` e hidratación al entrar en escena; sólo baja la primera imagen. Dedupe de `.jpg`/`.webp` en `HeroSlideUrlsService`.
- Anuncios y `breadcrumb_bg` con variante WebP (`imagePath`/`responsiveImageUrl` la prefieren).
- `/eventos` y detalle: `Cache-Control: private, max-age=0` + ETag (304 verificado). Home mantiene `public, max-age=300` + ETag.
- Tests: 263 pasan (190.963 assertions).

Pendiente: `COUNT` de `/eventos` sin la derivada (0,65 ms hoy), consolidación CSS/JS (requiere regresión visual) y observabilidad p95/5xx.

## Trabajo pendiente (medir antes de tocar)

1. **Detalle de evento:** ✅ deduplicado (2026-10-23). Una carga de tickets, traducciones y variaciones; `SUM(CASE...)` para wishlists/bookings; 45 → 19 queries (224,7 → 50–56 ms local).
2. **`getLanguage()`/`basic_settings`:** ✅ cacheado en el controlador base y View Composer (2026-10-23); 19 → 13 queries en detalle.
3. **`views_count`:** ✅ bufferizado (2026-10-23); sin UPDATE por visita, volcado cada minuto.
4. **Organizer:** ✅ TinyMCE, Dropzone, jQuery UI y DataTables se cargan solo en las vistas que los usan (2026-10-23). Dashboard ~437 KB JS raw / ~125 KB gzip; primera pantalla sin pesados.
5. **Imágenes:** ✅ popup sirve WebP (702 KB → 22 KB) y carga al abrir (2026-10-23); thumbnails 400 WebP + hero lazy. Ad/breadcrumb WebP (2026-10-23).
6. **`/eventos`:** el `COUNT` materializa la derivada de tickets; calcular el total sin el `LEFT JOIN` derivado.
7. **CSS/JS:** 535 KB CSS + 250 KB JS sin comprimir; consolidar sin purgar a ciegas (hay clases dinámicas).
8. **Observabilidad:** slow query log, alerta 5xx, latencia p95. Loki devolvió HTTP 400; no hay APM ni Sentry instalado.

## Cómo medir

### TTFB y tiempo de servidor

```bash
for i in 1 2 3; do
  curl -s -o /dev/null -w "dns=%{time_namelookup} connect=%{time_connect} tls=%{time_appconnect} ttfb=%{time_starttransfer}\n" \
    https://www.tukipass.com/
done
```

`tiempo de servidor ≈ ttfb - tls`. No confundir con el RTT de red.

### Concurrencia (liviana, no agresiva)

```bash
seq 20 | xargs -P5 -I{} curl -s -o /dev/null -w "%{time_total}\n" https://www.tukipass.com/
```

Comparar 1 worker vs `PHP_CLI_SERVER_WORKERS=4` en staging/local. En producción, no correr pruebas de carga sin autorización.

### Consultas por request (local/staging)

Usar `DB::listen()` en un script temporal fuera del repo, o Laravel Debugbar en local. Medir:
- total de queries,
- tiempo SQL acumulado,
- top 10 por tiempo,
- queries repetidas por tabla.

No medir con OPcache/caché fríos y comparar con calientes: fijar las condiciones.

### Frontend

- Chrome DevTools Performance (LCP, CLS, long tasks).
- Playwright con viewport 390×844, CPU 4×, red 1,6 Mbps, RTT 150 ms.
- CrUX no tiene datos del sitio; no inventar campo.

### Tests

```bash
php artisan test --no-coverage
```

## Reglas de seguridad

- **No mover el descuento de stock fuera de `DB::transaction`.** Ver `tukipass-ticket-stock`.
- **No cambiar pagos/webhooks para “optimizar”.** Ver `tukipass-payment-integrity`.
- **No activar `config:cache` sin staging.** El env de Docker se lee por `getenv()/$_ENV`, pero los valores que existen solo en `.env` (p. ej. `ARCA_CERT_PATH`) se pierden. Probar primero.
- **No purgar CSS/JS a ciegas.** Bootstrap 4 + CSS propio + clases dinámicas; usar regresión visual.
- **No agregar Redis** con 1 réplica y cache file; solo si se escala a varias réplicas o se necesita cache/colas compartidas.
- **No medir solo el promedio.** Reportar p50/p95 y el peor caso; una sola request lenta en un worker bloquea a todos.
- **No declarar mejora sin antes/después** con el mismo método y condiciones.

## Criterios de aceptación

| Fase | Criterio |
|---|---|
| Capacidad | 20 req a c5 ≤ 1,0 s; 0 5xx; RSS por worker medido; rollback probado |
| Consultas | Home ≤5 queries y SQL ≤20 ms; detalle ≤30; tests verdes |
| Imágenes | Home móvil ≤700 KB; LCP ≤2,5 s; CLS ≤0,1 |
| Organizer | JS ≤250 KB gzip; primeras pantallas sin TinyMCE/DataTables si no se usan |
| Producción | TTFB p95 y 5xx monitoreados 30 min post-deploy |

## Mantenimiento

- Dueño: backend/infra.
- Revisión: ante cada cambio de Dockerfile, OPcache, workers, View Composer, queries de home/evento/organizer o imágenes.
- Fuente de verdad: código + mediciones. Si este archivo difiere, gana el sistema y se actualiza en el mismo PR.
