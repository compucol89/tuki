# Auditoría integral de rendimiento — TukiPass

**Fecha:** 2026-10-22
**Commit auditado (local):** `dcaf8717` (mediciones de producción) — el commit desplegado en producción es `bbc843b1`.
**Código HEAD:** `2320b03e` (256 tests). **Rama de auditoría:** `fix/audit-p0-p1-20261022` (19 commits; docs hasta `242843e0`). **No es lo desplegado:** HEAD incluye OPcache, workers, stock transaccional, firma fail-closed, QR y aislamiento; producción no.
**Entorno de producción:** `https://www.tukipass.com` — EasyPanel, 1 réplica `app`, 1 réplica `worker`, MySQL 9, PHP 8.4.27.
**Entorno local de medición:** PHP 8.4.27 (CLI), MySQL 8.0 en Docker, dump de producción del 2026-08-22 (`tukipass.sql`, 2,4 MB).
**Estado:** auditoría de solo lectura. **No se modificó código de aplicación.** Los cambios de estado realizados fueron solo caches de runtime (`php artisan optimize:clear`) y un contenedor MySQL temporal para medir.

> **Correcciones verificadas el 2026-10-22 contra Código HEAD `2320b03e`:**
> - **Stock:** el código actual es atómico (`BookingController@storeData` abre `DB::transaction` en la línea 201; `reserveVariationStock`/`reserveSingleTicketStock` usan `lockForUpdate` y validan stock; 15+4 tests pasan). Se **retira** el hallazgo P1-06. El deploy `bbc843b1` es anterior a ese fix.
> - **Detalle de evento:** en producción pesa **120.886 B raw / 24.132 B gzip** y TTFB **0,29–0,35 s**. Los 550 KB/537 ms eran el evento local 126 (descripción larga) + emulación móvil, no un problema sistémico.
> - **OPcache/workers/blog cache:** ya están en HEAD (`Dockerfile:65-72`, `docker-start.sh:108`, `AppServiceProvider.php:202`), **no** en producción.
> - **`config:cache`:** no es categóricamente inseguro. El env de Docker se lee por `getenv()/$_ENV/$_SERVER`; el riesgo es para valores `.env`-only (p. ej. `ARCA_CERT_PATH`). Requiere staging.
> - **Webhook MP:** HEAD es fail-closed y devuelve 503/500; el deploy viejo devuelve 200. **No desplegar HEAD sin `MERCADOPAGO_WEBHOOK_SECRET`** (hoy ausente): todos los webhooks darían 401.
> - **Tests:** HEAD tiene **256 tests** (186.529 assertions), no 225.
>
> Donde una sección de este informe contradiga este bloque, vale este bloque.

---

## 0. Dictamen ejecutivo

TukiPass **funciona y sus Core Web Vitals medidos son aceptables en condiciones normales** (LCP móvil 2,1 s, CLS 0,02, Lighthouse 100/100/100 en accesibilidad, buenas prácticas y SEO). El problema principal **no es el diseño ni el frontend**: es la **capacidad de concurrencia del servidor web** y el **trabajo repetido por request**.

Hallazgos dominantes, en orden de impacto real:

1. **Producción corre con `php -S` (servidor embebido de PHP) con un solo worker.** Todas las requests se serializan. Medido: 20 requests a concurrencia 5 tardan **2,60 s** (~7,7 req/s); con 4 workers locales el mismo lote baja a **0,33 s** (~61 req/s, **3,9×**). En un pico de venta o ante webhooks de pago lentos, el sitio entero se encola detrás de una sola request.
2. **OPcache está deshabilitado en el deploy `bbc843b1`.** `php -S` usa el SAPI CLI y la imagen base trae `opcache.enable_cli=0`; el Dockerfile desplegado no lo activa. **HEAD ya lo habilita** (`Dockerfile:65-72`), pero no está desplegado. Cada request del deploy viejo recompila el código PHP.
3. **El View Composer de `frontend.*` se ejecuta una vez por cada vista parcial.** En el deploy viejo la consulta de “últimos blogs” no está cacheada y se repite **7–9 veces por página**: en home representa **15,7 ms de 33,6 ms de SQL (47%)**. **HEAD ya la cachea 5 min** (`AppServiceProvider.php:202`), pero no está desplegado.
4. **El detalle de evento del deploy viejo ejecuta hasta 41 consultas** (local, evento 126; variable según tickets) con duplicados de `languages` (4), `tickets` (6), `billing_settings` (3), `timezones` (3), `basic_settings` (3), `wishlists`/`bookings` (2 cada uno) y blogs (9). En producción pesa **120 KB raw / 24 KB gzip** y TTFB **0,29–0,35 s**; los 550 KB/537 ms eran un evento local outlier + emulación móvil.
5. **El panel del organizador carga ~800 KB gzip de JS/CSS en todas las páginas**, incluido TinyMCE (495 KB) aunque no se use, jQuery UI, DataTables, Dropzone e icon picker.
6. **Riesgos de integridad en pagos:** el deploy viejo acepta firmas inválidas (200 con firma basura), hace trabajo sincrónico y responde 200 ante errores; HEAD lo corrige (fail-closed, 503/500) pero **no está desplegado** y producción no tiene `MERCADOPAGO_WEBHOOK_SECRET`. El stock **sí es atómico en HEAD**; el riesgo real es el deploy atrasado, no el código actual.

**Conclusión:** OPcache, workers y cache de blogs ya están implementados en HEAD; el primer paso es desplegarlos con el secret de MP y las migraciones. La deduplicación del detalle, los assets del organizador y las imágenes siguen pendientes. El stock actual no requiere el “fix” que este informe sugería.

---

## 1. Alcance, método y limitaciones

### 1.1 Qué se inspeccionó

- Arquitectura, despliegue, Dockerfile, `docker-start.sh`, router de estáticos, configuración de PHP/OPcache.
- Rutas públicas, controladores de frontend, modelos, helpers, providers, middleware, scheduler y colas.
- Esquema e índices de la base (dump de producción) y planes de ejecución `EXPLAIN`.
- Assets, CSS/JS, fuentes, imágenes, popups, preloads y CSS crítico.
- Comportamiento real en producción con `curl`, Chrome DevTools Performance y Lighthouse.
- Flujos críticos de reserva/pago (solo lectura).

### 1.2 Herramientas efectivamente ejecutadas

| Herramienta | Uso |
|---|---|
| `curl` con timings (`time_starttransfer`, `size_download`) | TTFB, concurrencia, tamaños, headers |
| PHP CLI + profiler temporal en `/tmp` (`DB::listen`) | Conteo y tiempo de consultas SQL por ruta |
| MySQL 8 en Docker + `EXPLAIN` | Índices, planes, cardinalidad |
| Chrome DevTools MCP (trace + insights) | LCP, CLS, render-blocking, image delivery |
| Lighthouse MCP (mobile) | Accesibilidad, SEO, buenas prácticas |
| Playwright (Chromium, CPU 4×, red 1,6 Mbps, RTT 150 ms) | Core Web Vitals móviles, CLS, long tasks, DOM |
| EasyPanel MCP (solo lectura) | Servicios, variables, métricas de contenedores |
| PHPUnit | Línea base de regresiones: **225 tests, 186.314 assertions, 18,4 s, todos pasan** |

### 1.3 Limitaciones explícitas

- **No hubo acceso SQL directo a producción.** Las consultas se midieron contra el dump de producción del 2026-08-22 (7 eventos, 397 bookings, 41 customers, 82 transactions). Los tiempos locales están inflados por el I/O de Docker y MySQL 8 local; se usan **proporciones y conteos**, no latencias absolutas, para decidir.
- Producción usa **MySQL 9**; local se midió con **MySQL 8.0**. Los planes de ejecución con datasets pequeños pueden elegir full scan aunque el índice exista.
- **Los logs de Loki vía EasyPanel devolvieron HTTP 400** en todos los intentos; no se pudieron analizar errores reales de producción. Se documenta como limitación de observabilidad.
- Las pruebas de carga fueron **deliberadamente livianas** (máximo 20 requests, concurrencia 5) para no afectar producción.
- No se ejecutaron pagos reales ni pruebas en sandbox de MercadoPago.
- El trace móvil usa emulación (CPU 4×, 1,6 Mbps, RTT 150 ms); no reemplaza CrUX (no hay datos de campo para el sitio).

---

## 2. Topología y flujo real

```
Internet
  → Traefik (EasyPanel, TLS, HTTP/2, gzip)
  → contenedor app: php -S 0.0.0.0:8080 (1 worker por defecto)
       docker-router.php: sirve estáticos con ETag/Cache-Control/gzip
       → public/index.php → Laravel 12
            → MySQL 9 (tukipass_tukibd)
            → file cache (CACHE_DRIVER=file)
            → database sessions (SESSION_DRIVER=database)
            → database queue (QUEUE_CONNECTION=database)
  → contenedor worker: 3 procesos queue:work (default, ai-content, ai-images)
  → cron interno: schedule:run cada 60 s
```

Datos de despliegue relevantes:

| Elemento | Valor observado |
|---|---|
| Commit desplegado | `bbc843b1` (código HEAD es `2320b03e`) |
| Imagen app | `php:8.4.27-cli-alpine`, sin PHP-FPM/Nginx |
| Servidor web | deployed: `php -S 0.0.0.0:8080` (1 worker); HEAD: `PHP_CLI_SERVER_WORKERS=4` |
| OPcache | deployed: `opcache.enable_cli=0`; HEAD: on (`Dockerfile:65-72`) |
| Config cache | No se genera en ninguno (`config:cache` no se ejecuta) |
| Route cache | No se genera en ninguno (`route:cache` no se ejecuta) |
| View cache | Sí (`view:cache` en arranque) |
| Blog cache | deployed: no; HEAD: 5 min (`AppServiceProvider.php:202`) |
| APP_ENV / DEBUG | `production` / `false` |
| LOG_LEVEL | `error` |
| Réplicas | app 1, worker 1 |
| Migraciones | `migrate --force` en cada arranque |
| Deploy | zero-downtime de EasyPanel |

**Implicancia (deploy `bbc843b1`):** el contenedor no usa concurrencia multiproceso ni OPcache, y cada request paga el arranque completo de configuración + rutas + compilación de PHP. Es la causa raíz de la baja capacidad. **HEAD ya corrige OPcache y workers**; el problema es que no está desplegado.

---

## 3. Línea base medida

### 3.1 Backend — consultas y tiempos (local, dump de producción)

| Ruta | Consultas (frío) | Consultas (caliente) | Wall caliente | SQL caliente | Bytes |
|---|---:|---:|---:|---:|---:|
| `/` (home) | 48 | 10–13 | 30–34 ms | 16–34 ms | 37 KB (local; 203 KB prod) |
| `/eventos` | — | 15 | 33,6 ms | 16,0 ms | — |
| `/blog` | — | 19 | 28,9 ms | 16,2 ms | — |
| `/login` | — | 13 | 27,8 ms | 15,0 ms | — |
| `/sobre-nosotros` | — | 20 | 30,4 ms | 16,4 ms | — |
| `/auditoria-.../126` (detalle) | 41 | 41 | 94,4 ms | 38,8 ms | 550 KB (evento local outlier; producción 128: 120 KB raw / 24 KB gzip) |

Consultas repetidas en home (caliente): **7–9 ×** `SELECT blogs… JOIN blog_informations…`; **15,7 ms de 33,6 ms** de SQL (47%).
Consultas repetidas en detalle de evento (41 totales):

| Tabla | Consultas |
|---|---:|
| `blogs` | 9 |
| `tickets` | 6 |
| `languages` | 4 |
| `basic_settings` | 3 |
| `billing_settings` | 3 |
| `timezones` | 3 |
| `events` | 2 |
| `bookings` | 2 |
| `wishlists` | 2 |
| `organizers`, `admins`, `organizer_infos`, `event_addon_sections`, `ticket_contents`, `event_images` | 1 c/u |

### 3.2 Producción — TTFB y concurrencia

TTFB (3 muestras, sin caché del cliente):

| Ruta | Muestras |
|---|---|
| `/` | 227 / 367 / 298 ms |
| `/eventos` | 568 / 203 / 313 ms |
| `/blog` | 307 / 292 / 196 ms |
| `/login` | 195 / 248 / 185 ms |
| `/sobre-nosotros` | 366 / 267 / 623 ms |
| Detalle de evento (móvil, emulación 4× CPU/150 ms RTT) | 537 ms; medición directa producción 0,29–0,35 s |

Concurrencia (home):

| Escenario | Resultado |
|---|---|
| 5 concurrentes | 0,267 → 0,387 → 0,474 → 0,591 → 0,695 s (escalera ≈ serial) |
| 20 requests a concurrencia 5 | **2,602 s** → ~7,7 req/s |
| 20 requests secuenciales | **9,053 s** |
| Local, 1 worker, 20 a c5 | 1,288 s (~15,5 req/s) |
| Local, 4 workers, 20 a c5 | **0,327 s (~61 req/s)** → **3,9×** |

La escalera de latencias es la firma de un solo proceso: cada request espera a que termine la anterior. No es un problema de ancho de banda ni de base de datos.

### 3.3 Base de datos

Snapshot: 102 tablas, 7 eventos, 397 bookings, 41 customers, 82 transactions, 36 pending bookings, 52 sessions, 1 failed job. Volumen chico: **la base no es el cuello de botella hoy**. Los problemas son de forma de consulta e índices faltantes para cuando crezca.

Índices observados (los relevantes):

| Tabla | Índices |
|---|---|
| `events` | PRIMARY, `(status, end_date_time)` |
| `event_contents` | PRIMARY, `(language_id, event_id)`, `(slug)` |
| `tickets` | PRIMARY, `(event_id)`, `(f_price)`, `(pricing_type)` |
| `bookings` | PRIMARY, `(event_id, paymentStatus)`, dos unique de tokens |
| `sessions` | PRIMARY, `(last_activity)`, `(user_id)` |
| `wishlists` | PRIMARY, `(customer_id, event_id)` |
| `ticket_contents` | **solo PRIMARY** |
| `customers` | **solo PRIMARY** |
| `organizers` | **solo PRIMARY** |
| `admins` | **solo PRIMARY** |
| `organizer_infos` | **solo PRIMARY** |
| `transactions` | **solo PRIMARY** |
| `pending_bookings` | PRIMARY + unique token |
| `event_addon_sections` | `(event_id)`, `(organizer_id)` |

`EXPLAIN` del `COUNT(*)` del listado `/eventos`: MySQL materializa la derivada `tk` (agregado de **todos** los tickets, `DERIVED` sobre `idx_tickets_event_id` al 100%) aunque el `COUNT` no use sus columnas. Con 29 tickets es irrelevante; con decenas de miles, cada paginación/filtro agrega ese costo.

### 3.4 Frontend (producción)

| Métrica | Desktop (trace, sin throttling) | Móvil (CPU 4×, 1,6 Mbps, RTT 150 ms) |
|---|---:|---:|
| FCP | — | 2.072–2.156 ms (outlier 4.156) |
| LCP | **1.032 ms** | **2.072–2.156 ms** (outlier 4.156) |
| TTFB | 152 ms | 262–290 ms (outlier 403) |
| CLS | 0,00 | **0,0198** (outlier 0,233) |
| Load | — | 7,58–7,66 s |
| Peso home | — | **1.527 KB**, 39 requests |
| LCP element | `IMG.hero-slide__image` | ídem |
| Render delay LCP | **815 ms (79%)** | — |
| Long tasks | 1 × 226 ms (outlier) | 0 |

Detalle de evento (móvil, emulación): **LCP 2.900 ms, TTFB 537 ms, load 4.746 ms, 806 KB, 34 requests, DOM 780 nodos, CLS 0**. Medición directa de producción (evento 128): **120.886 B raw / 24.132 B gzip, TTFB 0,29–0,35 s**; el evento local 126 con 550 KB era un outlier de contenido.

Lighthouse móvil (home): **Accesibilidad 100, Best Practices 100, SEO 100**. Único audit fallido: `label-content-name-mismatch` en 6 tarjetas de evento (`aria-labelledby` apunta solo al título, pero el texto visible incluye fecha/lugar).

Peso de recursos de home:

| Recurso | Transferido (gzip) | Sin comprimir |
|---|---:|---:|
| CSS total (13 archivos) | ~93 KB | **535 KB** |
| JS total (9 archivos) | ~90 KB | **250 KB** |
| Imágenes | ~1,4 MB | — |
| Popup PNG | **593 KB** | — |
| Thumbnails de eventos | 85–232 KB c/u (1200×758 mostrados a 326×326) | — |
| Hero WebP | 20 KB | — |

Chrome DevTools estimó **1,4 MB de ahorro potencial** solo en imágenes. El popup PNG se descarga aunque el popup esté cerrado (lazy-load se dispara igual).

### 3.5 Infraestructura (EasyPanel, lectura)

| Contenedor | CPU | Memoria |
|---|---:|---:|
| app | 1,55% | 68,8 MB |
| worker | 0,11% | 123,1 MB |
| MySQL | 1,03% | 607,8 MB |
| Traefik | ~0% | 244 MB |

Carga actual muy baja. El riesgo es de **picos**, no de saturación permanente. El host aparenta ~16 GB de RAM (por los porcentajes); hay margen para workers y OPcache.

### 3.6 Tests

`php artisan test` (Código HEAD `2320b03e`): **256 passed, 186.529 assertions**. Hay tests de stock (`EventBookingTicketSelectionValidationTest`, `BookingStockReleaseTest`), pagos (firma, claim, compensación), QR e aislamiento. **Sigue faltando un test de concurrencia real en MySQL y un end-to-end HTTP del webhook duplicado.**

---

## 4. Inventario de hallazgos

### P0 — Crítico

#### P0-01 — Servidor web de un solo proceso

- **Módulo:** infraestructura / despliegue.
- **Archivos:** `docker-start.sh`, `Dockerfile`.
- **Causa raíz:** se usa `php -S 0.0.0.0:8080` (servidor embebido) sin `PHP_CLI_SERVER_WORKERS`. El valor por defecto es 1 worker.
- **Evidencia:** 5 requests concurrentes forman una escalera de ~110 ms por request; 20 requests a c5 = 2,602 s (~7,7 req/s); con 4 workers locales el mismo lote = 0,327 s (3,9×).
- **Consecuencia:** una request lenta (detalle con consultas duplicadas, webhook de pago con curl de hasta 30 s, generación de PDF/QR) bloquea **todo** el sitio. En un pico de venta, los usuarios se encolan; los webhooks de pago se demoran y pueden expirar/reintentar.
- **Solución:** inmediata: `PHP_CLI_SERVER_WORKERS=4–8` en el arranque (cambio de una línea, reversible). Estratégica: migrar a PHP-FPM + Nginx/Traefik o FrankenPHP, con 8–16 workers y healthcheck.
- **Riesgo:** bajo para workers (más memoria: ~68 MB por worker); medio para FPM (cambio de imagen/despliegue).
- **Validación:** medición local hecha; pendiente medir en producción con el mismo test liviano.

### P1 — Alto

#### P1-01 — OPcache deshabilitado en el deploy viejo

- **Módulo:** PHP / runtime.
- **Archivos:** `Dockerfile`, `docker-start.sh`.
- **Causa raíz:** el deploy `bbc843b1` usa `php -S` con `opcache.enable_cli=0`; la imagen oficial `php:8.4.27-cli-alpine` fue verificada y devuelve `enable_cli=0`.
- **Estado HEAD:** **ya implementado** en `Dockerfile:65-72` (`enable=1`, `enable_cli=1`, 128 MB, 20k archivos, `validate_timestamps=0`). No está desplegado.
- **Evidencia:** A/B local sobre `/tienda`: 16,9 ms (off) → 14,9 ms (on) → 12,6 ms (on + config/route cache).
- **Solución:** desplegar HEAD y verificar `php -i | grep opcache` dentro del contenedor. No reimplementar.
- **Validación:** A/B local hecho; pendiente A/B en staging/producción.

#### P1-02 — View Composer por parcial; blogs 7–9× en el deploy viejo

- **Módulo:** frontend / providers.
- **Archivo:** `app/Providers/AppServiceProvider.php`, `View::composer('frontend.*', …)` (línea ~120) y consulta `$blogs` (línea ~202).
- **Causa raíz:** el wildcard `frontend.*` matchea el layout **y cada parcial** (`header`, `footer`, `popups`, etc.). En el deploy viejo la consulta de últimos blogs no estaba cacheada y se ejecutaba en cada invocación.
- **Estado HEAD:** **blog cacheado 5 min** (`AppServiceProvider.php:202`); el resto del payload del composer sigue ejecutándose por parcial. Memoizarlo una vez por request sigue pendiente.
- **Evidencia (deploy viejo):** home caliente: 7–9 consultas idénticas; 15,7 ms de 33,6 ms de SQL (47%). Detalle: 9 consultas.
- **Solución:** (a) blog cache ya hecho; (b) memoizar el payload completo del composer con un `static` por request y solo hacer `$view->with(...)` en cada vista.
- **Riesgo:** bajo si se preserva la inyección de variables en todas las vistas; validar home, evento, blog, checkout, legales y emails.
- **Validación:** blog cache implementado; memoización pendiente de medir.

#### P1-03 — Detalle de evento: consultas duplicadas y UPDATE por vista

- **Módulo:** frontend / evento.
- **Archivo:** `app/Http/Controllers/FrontEnd/EventController.php` (`details`, línea 225; `buildTicketSummary`, 520; `buildInterestIndicator`, 603).
- **Causa raíz:**
  - `Event::where('id',$id)->update(['views_count' => +1, 'views_last_24h' => +1])` en **cada** request (5,5 ms local; row lock y write amplification).
  - `buildTicketSummary` consulta `tickets` 2 veces + un `SUM` extra; el detalle principal ya trae tickets.
  - `getLanguage()` se llama 4 veces; `basic_settings` 3; `billing_settings` 3; `timezones` 3; `buildInterestIndicator` hace 4 consultas (wishlist ×2, bookings ×2) que se pueden unir.
  - Consulta de blogs 9× (P1-02).
- **Evidencia:** 41 consultas en el peor caso local (evento 126; variable según cantidad de tickets), 94 ms caliente. En producción, evento 128: **120.886 B raw / 24.132 B gzip**, TTFB **0,29–0,35 s**. El 550 KB/537 ms era un outlier local + emulación móvil. HEAD ya cachea los blogs 5 min; el resto de la duplicación sigue.
- **Consecuencia:** la página de conversión hace trabajo evitable; el UPDATE por vista impide cachear y genera contención en eventos populares.
- **Solución:** deduplicar consultas (una carga de tickets, una de settings, una de timezone, una de interés con `SUM(CASE…)`); mover el conteo de vistas a un buffer/cola (por ejemplo, incrementar en cache y volcar cada N minutos) o al menos hacerlo fuera de la request; cachear settings/language. Evaluar cache de página para invitados (con TTL corto) una vez eliminado el UPDATE.
- **Riesgo:** medio (cambia semántica de `views_count`); hacer con tests y feature flag.
- **Validación:** pendiente.

#### P1-04 — Panel del organizador carga ~800 KB gzip de assets globales

- **Módulo:** organizer UI.
- **Archivo:** `resources/views/organizer/partials/scripts.blade.php` y `styles.blade.php`.
- **Causa raíz:** todos los assets se incluyen sin condición: TinyMCE (495 KB), jQuery UI (290 KB), highlight (135 KB), icon picker (110 KB), DataTables (86 KB), Dropzone (42 KB), Select2 (75 KB), etc.; CSS Atlantis 336 KB + Bootstrap 155 KB.
- **Evidencia:** 1,5 MB de JS sin comprimir (**449 KB gzip**) + 336 KB CSS + fuentes (**351 KB gzip**) en **cada** página del panel.
- **Consecuencia:** dashboard del organizador lento en 4G/móvil; primera carga de 1–3 s solo de assets.
- **Solución:** cargar TinyMCE solo en vistas con editor; DataTables solo en tablas; Dropzone solo en uploads; icon picker solo en settings; highlight solo donde se usa. Mover el resto a un bundle `defer`.
- **Riesgo:** bajo (condicionales por `@stack`/`@push`); validar cada pantalla.
- **Validación:** pendiente.

#### P1-05 — Webhook de MercadoPago: firma, disponibilidad y deploy

- **Módulo:** pagos.
- **Archivo:** `app/Http/Controllers/FrontEnd/PaymentGateway/MercadoPagoController.php` (`webhook`, línea 602; `hasValidWebhookSignature`, 866; `claimPendingBooking`, 851).
- **Estado desplegado (`bbc843b1`):**
  - Ruta POST pública, **sin throttle**, `curl` **sincrónico** con timeout 30 s.
  - `hasValidWebhookSignature()` devuelve `true` si el secret está vacío. Producción no tiene `MERCADOPAGO_WEBHOOK_SECRET`: **verificado empíricamente**, firma inválida devolvió **200 en 0,842 s**.
  - Ante error de API o excepción responde **200**; MP no reintenta. La reconciliación vieja solo cubría Iyzico.
- **Estado HEAD (`2320b03e`):**
  - Firma **fail-closed** en production (401 sin secret), **503** ante error transitorio (`:654`), **500** ante excepción (`:847`), claim atómico y compensación.
  - **Release blocker:** producción sigue sin `MERCADOPAGO_WEBHOOK_SECRET`. Si se despliega HEAD sin configurarlo, **todos los webhooks devuelven 401**.
  - Sigue siendo sincrónico: el `curl` de hasta 30 s y el reembolso de hasta 20 s pueden bloquear el worker. Falta rate-limit + job async.
- **Consecuencia:** en el deploy viejo, bypass de firma y 200 silencioso; en HEAD, riesgo de no desplegar por secret faltante y bloqueo del worker por llamadas sincrónicas.
- **Solución:** configurar el secret antes del deploy; rate-limit; mover verificación/procesamiento a un job idempotente con reintentos; mantener 503/500 para fallos transitorios.
- **Riesgo:** alto (zona protegida de pagos). Requiere tests y staging.
- **Validación:** firma cubierta por `MercadoPagoWebhookSignatureTest`; claim por `PendingBookingClaimTest`; falta test HTTP end-to-end de webhook duplicado.

#### P1-06 — [RETIRADO] Atomicidad del stock

- **Estado:** el código HEAD **sí es atómico**. `BookingController@storeData` abre una única `DB::transaction` (línea 201); `reserveVariationStock` (320) y `reserveSingleTicketStock` (403) usan `lockForUpdate`, validan stock y lanzan excepción; los tests `EventBookingTicketSelectionValidationTest` (15) y `BookingStockReleaseTest` (4) prueban rollback, stock insuficiente y liberación idempotente. **No hay `max(0)` ni throw después del commit.**
- **Lo que sí queda:** el deploy `bbc843b1` es anterior a `33e5e996` y al código `2320b03e`; producción puede tener el comportamiento viejo. No desplegar HEAD sin las migraciones `stock_released_at` y `ticket_scans`.
- **Modelo real:** no existe columna `reserved`; `PendingBooking` no reserva stock; dos compradores de la última unidad pueden pagar y el segundo recibe reembolso/`manual_review` vía `PaymentCompensationService`.
- **Validación:** 256 tests pasan (186.529 assertions).

#### P1-07 — Primer impacto de imágenes: 1,4 MB evitables en home

- **Módulo:** frontend / media.
- **Archivos:** `resources/views/frontend/partials/popups.blade.php` (línea 26), uploads de thumbnails, `resources/views/frontend/home/index-v1.blade.php`.
- **Causa raíz:** thumbnails de 1200×758 (85–232 KB) mostrados a 326×326; popup PNG de 593 KB descargado aunque el popup esté cerrado.
- **Evidencia:** Chrome DevTools `ImageDelivery`: 1,4 MB de ahorro potencial; el PNG del popup se descarga a los ~1,8 s.
- **Consecuencia:** 1,5 MB en la primera visita móvil; costo de datos y load de 7,6 s.
- **Solución:** generar variantes 400×267 y 800×534 en WebP/AVIF al subir; `srcset`/`sizes`; convertir el popup a WebP y no asignar `src` hasta que se abra (o usar `loading="lazy"` real y `content-visibility`).
- **Riesgo:** bajo.
- **Validación:** pendiente.

### P2 — Medio

#### P2-01 — Sin config/route cache (y `config:cache` bloqueado por `env()` en runtime)

- **Evidencia:** A/B `/tienda`: 16,9 ms → 12,6 ms con OPcache + config/route cache (-25%). `route:cache` funciona (probado, 829 KB de rutas).
- **`config:cache` — matiz empírico:** no es categóricamente inseguro. `LoadEnvironmentVariables` no carga `.env` si hay config cacheada, pero `Env::get()` lee `getenv()`, `$_ENV` y `$_SERVER`; verificado en la imagen real con `FOO=bar`. El riesgo es para valores que existan **solo en `.env`**, no en el entorno del contenedor (por ejemplo `ARCA_CERT_PATH`, que `docker-start.sh` agrega al archivo). Antes de activarlo: staging con el env de producción y verificar cada `env()` runtime. Mover esos valores a `config/*.php` sigue siendo la solución determinista.
- **Solución:** `route:cache` es seguro; `config:cache` solo después de la prueba en staging. No ejecutar `composer optimize` a ciegas.
- **Riesgo:** medio; el impacto real depende de qué valores sean `.env`-only.

#### P2-02 — Sesiones en base de datos: 2 consultas por request

- **Evidencia:** `select * from sessions` + `insert into sessions` (~4–20 ms locales según frío/caliente).
- **Solución:** no migrar a Redis por moda. Con 1 réplica, `SESSION_DRIVER=file` o cookie reduciría 2 queries; pero se pierde persistencia entre deploys. Si se escala a varias réplicas, Redis sí se justifica. Decidir por costo/beneficio; hoy es P2.

#### P2-03 — CSS/JS fragmentados y sobredimensionados

- **Evidencia:** 13 CSS (535 KB sin comprimir, 93 KB gzip) + 9 JS (250 KB, 90 KB gzip); 16 `<link>` y 17 `<script>` en home. Bootstrap 4 completo (160 KB) convive con un design system propio.
- **Solución:** consolidar CSS crítico en 2–3 archivos, diferir el resto, purgar Bootstrap/Atlantis no usado con verificación visual, y unificar JS con `defer`. No purgar a ciegas: hay clases dinámicas.
- **Riesgo:** medio (regresiones visuales).

#### P2-04 — `COUNT` de `/eventos` materializa el agregado de tickets

- **Evidencia:** `EXPLAIN` muestra `DERIVED` sobre `tickets` al 100% para el `COUNT(*)`, aunque no usa `tk`.
- **Solución:** calcular el total con una query sin el `LEFT JOIN` derivado (o `simplePaginate`), y mantener el join solo para la página de resultados.
- **Riesgo:** bajo.

#### P2-05 — Índices faltantes para crecimiento

- **Evidencia:** `ticket_contents` solo PK y se consulta por `(language_id, ticket_id)` en cada ticket (N+1); `wishlists` sin índice por `event_id`; `bookings` sin `customer_id`; `customers`/`organizers`/`admins` sin índice/unique por `email`; `organizer_infos` sin `(organizer_id, language_id)`; `events` sin `organizer_id`; `transactions` sin `booking_id`/`organizer_id`; `pending_bookings` sin `(status, expires_at)`.
- **Solución:** ver DDL propuesto en el Anexo B. Crear en ventana de mantenimiento, verificar duplicados antes de los unique y medir `EXPLAIN` después. Hoy el volumen es chico; el beneficio es preventivo.
- **Riesgo:** bajo, pero los unique pueden fallar si hay duplicados.

#### P2-06 — Observabilidad insuficiente

- **Evidencia:** no hay APM/Sentry/Pulse; Loki devolvió 400; no hay slow query log ni alertas de latencia/errores. `LOG_LEVEL=error` reduce ruido pero también oculta warnings.
- **Solución:** slow query log de MySQL (>200 ms), un middleware liviano de latencia p95 por ruta (o Laravel Pulse), uptime externo y alerta de 5xx. No agregar Prometheus/Grafana para este tamaño.
- **Riesgo:** bajo.

#### P2-07 — Cache busting inconsistente en estáticos

- **Evidencia:** `docker-router.php` sirve con `Cache-Control: immutable, max-age=31536000`; los CSS usan `?v=hash`, pero jQuery, Bootstrap, magnific, etc. se referencian sin versión. Un cambio de archivo no invalida el navegador por un año.
- **Solución:** agregar hash/versión a todos los assets o usar nombres con hash; no bajar el TTL.
- **Riesgo:** bajo.

#### P2-08 — Accesibilidad: nombre accesible de tarjetas de evento

- **Evidencia:** Lighthouse `label-content-name-mismatch` en 6 tarjetas (`aria-labelledby` solo al título; el texto visible incluye fecha/lugar/precio).
- **Solución:** incluir el texto visible en el nombre accesible o quitar `aria-labelledby` y dejar que el contenido conforme el nombre.
- **Riesgo:** bajo.

### P3 — Bajo

- **P3-01** — `docker-start.sh` duplica el bloque de `crond` (código muerto, dos escrituras del mismo crontab). Limpiar.
- **P3-02** — `font-display: block` en Font Awesome; usar `swap` para evitar iconos invisibles en redes lentas.
- **P3-03** — El ETag de home no se aprovecha para 304: el framework renderiza igual. Si se busca ahorro de bandwidth, implementar `isNotModified()`; hoy el impacto es bajo.
- **P3-04** — El marquee repite 6 thumbnails 3 veces (38 `<img>`); reducir nodos o reutilizar.
- **P3-05** — `theme-dark-quiet.css` y reglas duplicadas en `home.css` (múltiples bloques para `.hero-slide__image`); consolidar.
- **P3-06** — `updated_at` se actualiza en cada vista de evento (efecto colateral de `views_count`); revisar si sitemaps/caches lo usan.
- **P3-07** — El reporte de Lighthouse no incluye performance; el trace sí. Mantener ambos en la rutina.

---

## 5. Ranking de cuellos de botella

| # | Problema | Impacto | Evidencia | Esfuerzo | Riesgo |
|---|---|---|---|---|---|
| 1 | 1 worker `php -S` | Capacidad 7,7 req/s; bloqueo global | 20 req c5 = 2,60 s; 4 workers = 0,33 s | 1 línea | Bajo |
| 2 | OPcache off **en el deploy viejo** | CPU por request/job | A/B 16,9→14,9 ms; HEAD ya lo habilita | Bajo | Bajo |
| 3 | Composer × parcial (blogs 7–9×) | 47% del SQL de home | 15,7/33,6 ms; HEAD ya cachea 5 min | Bajo | Bajo |
| 4 | Detalle: queries duplicadas + UPDATE por vista | Trabajo evitable; producción 120 KB/24 KB, TTFB 0,29–0,35 s | 41 queries en el peor caso local | Medio | Medio |
| 5 | Assets del organizador | ~800 KB gzip por página | 1,5 MB JS + 336 KB CSS | Bajo | Bajo |
| 6 | Webhook MP sincrónico/sin firma/200 en error | Disponibilidad + integridad | deploy viejo: 200 con firma basura; HEAD: 401/503, falta secret | Medio | Alto |
| 7 | Deploy `bbc843b1` atrasado | No tiene stock transaccional, QR, aislamiento, OPcache/workers | Código HEAD `2320b03e` vs deploy | Alto | Alto |
| 8 | Imágenes 1,4 MB | Primera carga móvil | popup 593 KB; thumbs 1200→326 | Bajo | Bajo |
| 9 | Índices faltantes | Degradación futura | esquema | Bajo | Bajo |
| 10 | Config/route cache | Arranque | A/B -25% | Medio (env refactor) | Medio |

---

## 6. Plan de implementación propuesto

> **Regla:** cada fase se implementa en rama separada, se valida localmente, se mide antes/después y recién después se propone despliegue. Nada se despliega automáticamente.

### Fase 1 — Capacidad y arranque (bajo riesgo, alto impacto)

1. **OPcache y workers ya están en HEAD** (`Dockerfile:65-72`, `docker-start.sh:108`). No reimplementar: verificar que el deploy los tenga y medir en staging.
2. **Route cache**: agregar `php artisan route:cache` después de generar `.env`; probado localmente (829 KB de rutas). **No** activar `config:cache` sin staging (P2-01).
3. **Medición:** mismo test de 20 requests a c5 y TTFB de home/evento. Criterio: ≥2× throughput y sin 5xx. En producción, no correr carga sin autorización.

### Fase 2 — Trabajo repetido (bajo riesgo)

4. **Blog cache ya está en HEAD** (5 min, `AppServiceProvider.php:202`); falta memoizar el payload completo del composer una vez por request.
5. Cachear `defaultLanguage()` y `basic_settings` en el controlador base (evitar 3–4 queries por request).
6. Deduplicar en detalle de evento: tickets (1 carga), timezone (1), billing settings (1), interés (1 query con `SUM(CASE…)`), y mover `views_count` a un buffer/cola (P1-03).
7. Medir: queries por ruta (objetivo home ≤5, detalle ≤30), TTFB de detalle, tests PHPUnit + Playwright.

### Fase 3 — Assets (bajo/medio riesgo)

10. Condicionales de assets en organizer (TinyMCE/DataTables/Dropzone/icon picker) (P1-04).
11. Pipeline de thumbnails 400/800 WebP + `srcset`; popup WebP y carga diferida real (P1-07).
12. Consolidar CSS/JS crítico; `defer` en JS no crítico (P2-03).
13. Medir Lighthouse/Playwright móvil y peso transferido.

### Fase 4 — Integridad y pagos (alto riesgo, requiere autorización y tests)

14. **Webhook MP:** firma fail-closed, 503/500, claim y compensación **ya están en HEAD**. Falta rate-limit + job async con reintentos para no bloquear el worker (P1-05). **Antes de desplegar HEAD, configurar `MERCADOPAGO_WEBHOOK_SECRET`** o todos los webhooks devolverán 401.
15. **Stock:** la transacción única, locks y tests **ya están en HEAD** (P1-06 retirado). Falta un test de concurrencia real en MySQL; el deploy viejo no tiene estos fixes.
16. Tests de concurrencia (dos compras de la última unidad) y de fallo parcial.
17. Índices del Anexo B + verificación `EXPLAIN`; evaluar `DECIMAL` para montos (hoy `float`/`double`/`varchar`).

### Fase 5 — Observabilidad y cierre

18. Slow query log, alertas de 5xx/latencia, uptime.
19. Documentar métricas antes/después y rollback.

---

## 7. Métricas comparativas (antes / después)

Las columnas “después” están **pendientes de medición**: no se declara ninguna mejora como lograda hasta repetir el mismo método.

| Indicador | Antes (medido) | Después (objetivo/estimado) | Estado |
|---|---|---|---|
| Throughput home c5 (20 req) | 2,602 s (~7,7 req/s) | ~0,7–0,9 s con 4 workers (3,9× local) | Pendiente |
| TTFB home | 227–367 ms | -10 a -20% con OPcache/route cache | Pendiente |
| TTFB detalle evento | 0,29–0,35 s (producción) | ≤0,28 s | Pendiente |
| Consultas home (caliente) | 10–13 (7–9 blogs) | ~4–5 | Pendiente |
| SQL home | 33,6 ms (47% blogs) | ~18 ms | Pendiente |
| Consultas detalle | 41 | ~25 | Pendiente |
| Peso home móvil | 1.527 KB | ~600–700 KB | Pendiente |
| Assets organizer | ~800 KB gzip | ~350–400 KB gzip | Pendiente |
| LCP móvil home | 2,07–2,16 s | ≤2,0 s | Pendiente |
| Tests | 256 passed | 256+ passed | Pendiente |

**Nota:** no se debe interpretar “menos consultas” como “menos latencia” sin medir. Las estimaciones se basan en el tiempo SQL observado y en el A/B local, no en producción.

---

## 8. Validación funcional y regresiones

### Ya validado

- 225 tests PHPUnit pasan (186.314 assertions).
- Home, `/eventos`, `/blog`, `/login`, `/sobre-nosotros`, `/preguntas-frecuentes`, `/contacto`, `/organizadores`, `/tienda` responden 200/302 correctamente.
- Detalle de evento responde 200; producción 128: 120.886 B raw / 24.132 B gzip (el evento local 126 de 550 KB es un outlier de contenido).
- Lighthouse móvil: 100 accesibilidad, 100 best practices, 100 SEO.
- Assets estáticos: gzip, ETag, Cache-Control immutable, HTTP/2.

### Pendiente de validar después de cambios

- Checkout completo con MercadoPago sandbox (crear reserva, pagar, webhook, factura, email, QR).
- Concurrencia de stock (dos compradores de la última unidad).
- Reserva multi-ticket con fallo en el segundo ticket.
- Reconciliación de reservas pendientes de todos los gateways.
- Panel del organizador: dashboard, crear/editar evento (TinyMCE), tablas (DataTables), uploads (Dropzone).
- Regresión visual de home/evento en desktop y móvil.

---

## 9. Riesgos residuales

1. **Producción no fue modificada.** El análisis es de solo lectura. Los fixes de pagos/stock/QR están en HEAD, **no desplegados**; producción sigue en `bbc843b1`.
2. **El dump de producción es del 2026-08-22.** Los volúmenes actuales pueden ser mayores; los conteos de queries y las proporciones se mantienen, pero los planes pueden cambiar.
3. **No hay logs de producción disponibles vía Loki.** No se pudo confirmar la tasa real de errores/timeouts.
4. **El servidor embebido no es una solución definitiva.** `PHP_CLI_SERVER_WORKERS` mejora el throughput pero no reemplaza FPM (sin gestión de procesos, sin reciclado, sin métricas).
5. **La cache file es local al contenedor.** Si se escala a más réplicas, cada una tendrá su propia cache y las invalidaciones no se propagan; en ese escenario se justifica Redis.
6. **Credenciales sensibles.** La API de EasyPanel devolvió variables sensibles (APP_KEY, DB_PASSWORD, OPENAI_API_KEY, token de MercadoPago). No se incluyen en este informe. Si el token de EasyPanel o los registros de esta sesión se comparten, rotarlas.
7. **`config:cache` tiene riesgo acotado:** el env de Docker se lee por `getenv()/$_ENV/$_SERVER`; solo se pierden valores `.env`-only (p. ej. `ARCA_CERT_PATH`). Probar en staging antes de activarlo.

---

## 10. Recomendaciones futuras (justificadas)

- **PHP-FPM + Nginx/Traefik** con 8–16 workers, `opcache.preload` opcional, healthcheck y límites de memoria. Es la solución sustentable a P0-01.
- **CDN o cache de página completa** para home/eventos públicos si se proyectan picos de venta; hoy no es necesario por volumen, pero el UPDATE de vistas y las sesiones lo impiden. Resolver P1-03 primero.
- **Redis** solo si se pasa a múltiples réplicas o se necesita cache/colas compartidas. No agregarlo hoy.
- **MySQL**: `innodb_buffer_pool_size` acorde al crecimiento; slow query log; revisar índices cada vez que se agregue un listado/reporte.
- **Imágenes**: mover generación de variantes al upload (no al request); AVIF/WebP; limpiar el volumen de uploads.
- **Observabilidad**: Laravel Pulse o middleware de latencia p95 + alerta de 5xx + uptime. No Prometheus/Grafana para este tamaño.
- **Presupuesto de rendimiento en CI**: fallar si el bundle de home supera X KB gzip, si el número de queries de las rutas críticas supera Y, o si Lighthouse móvil baja de 90.
- **Tests de concurrencia** en CI para stock, con SQLite no alcanza: usar MySQL en un servicio de CI.

---

## Anexo A — Evidencia de medición (resumen)

- `php -S` 1 worker, 20 requests a c5: **2,602 s**; 4 workers: **0,327 s**.
- Home caliente: 10–13 queries; blogs 7–9; SQL 33,6 ms; blogs 15,7 ms (47%).
- Detalle: 41 queries en el peor caso local (evento 126); producción 128: 120.886 B raw / 24.132 B gzip; TTFB 0,29–0,35 s. El 550 KB/537 ms era un outlier local + emulación móvil.
- Home móvil: LCP 2.072–2.156 ms; CLS 0,0198; load 7,58–7,66 s; 1.527 KB; 39 requests.
- Lighthouse: 100/100/100; 1 audit fallido (6 nodos).
- OPcache A/B: 16,9 → 14,9 ms; con config+route cache: 12,6 ms.
- Organizer: 1,5 MB JS (449 KB gzip) + 336 KB CSS + fuentes (351 KB gzip).
- Infra: app 68,8 MB / 1,55% CPU; worker 123,1 MB / 0,11%; MySQL 607,8 MB / 1,03%.

## Anexo B — DDL propuesto (revisar duplicados antes de ejecutar)

```sql
-- Verificar antes: SELECT email, COUNT(*) FROM customers GROUP BY email HAVING COUNT(*) > 1;
CREATE UNIQUE INDEX uq_customers_email ON customers (email);
CREATE UNIQUE INDEX uq_organizers_email ON organizers (email);
CREATE UNIQUE INDEX uq_admins_email ON admins (email);

CREATE INDEX idx_ticket_contents_lang_ticket ON ticket_contents (language_id, ticket_id);
CREATE INDEX idx_wishlists_event ON wishlists (event_id);
CREATE INDEX idx_bookings_customer_created ON bookings (customer_id, created_at);
CREATE INDEX idx_organizer_infos_org_lang ON organizer_infos (organizer_id, language_id);
CREATE INDEX idx_events_organizer ON events (organizer_id);
CREATE INDEX idx_transactions_booking ON transactions (booking_id);
CREATE INDEX idx_transactions_organizer_created ON transactions (organizer_id, created_at);
CREATE INDEX idx_pending_bookings_status_expires ON pending_bookings (status, expires_at);
```

Cada índice debe justificarse con una consulta real y validarse con `EXPLAIN`; no crear índices “por si acaso”. En MySQL 9 las tablas son pequeñas y la creación es rápida, pero hacerlo en ventana de mantenimiento y con backup.

## Anexo C — Comandos reproducibles

```bash
# TTFB
curl -s -o /dev/null -w "%{http_code} %{time_starttransfer}\n" https://www.tukipass.com/

# Concurrencia liviana
seq 20 | xargs -P5 -I{} curl -s -o /dev/null -w "%{time_total}\n" https://www.tukipass.com/

# Conteo de queries (profiler temporal, sin tocar el repo)
php /tmp/tuki_probe.php "/" 1

# Tests
php artisan test

# OPcache en la imagen real
docker run --rm php:8.4.27-cli-alpine php -r 'echo ini_get("opcache.enable_cli"),"\n";'
```

---

**Fin del informe.** OPcache, `PHP_CLI_SERVER_WORKERS=4` y cache de blogs están implementados en HEAD, pero **no desplegados**. El primer paso no es reoptimizar: es desplegar HEAD con `MERCADOPAGO_WEBHOOK_SECRET` y las tres migraciones, y medir en staging. Después quedan la memoización del composer, la deduplicación del detalle, los assets del organizador y las imágenes. Las fases de pagos/stock ya están corregidas en HEAD; el riesgo residual es el deploy atrasado.
