# Pendientes — TukiPass

> Actualizado: 2026-05-28 — ARCA ✅, failed job limpiado ✅, pendiente: Social Login + GSC meta tag + auditorías restantes

---

## 🔴 Alta prioridad

- [x] ~~Job fallido en producción~~: era `EventConfirmationMail` (base64_encode + HtmlString, 19-mayo, anterior a todos los fixes). Limpiado con `queue:forget 24`. Health check → 0 failed jobs ✅
- [ ] **Social Login — Organizadores**: Clientes ya tienen Google/Facebook (`CustomerController` + vistas auth). Organizador **no** tiene botones OAuth en login/signup (`frontend/organizer/login`, `signup`). **Decisión de negocio pendiente** (ver historial: marzo 2026 se dijo "no panel" — si cambió, implementar).
- [ ] **Google Search Console — meta tag de verificación**: Agregar en `<head>` de `resources/views/frontend/layout.blade.php`:
  ```html
  <meta name="google-site-verification" content="CODIGO_QUE_PASA_EL_USUARIO" />
  ```
  **Bloqueado hasta recibir el código** desde [Search Console](https://search.google.com/search-console). Sin esto no hay verificación de propiedad ni informes.

---

## 🟡 SEO / Google — post-deploy (manual + comandos)

> Código ya en `master` (commits recientes abajo). Falta ejecutar en **producción** y validar en GSC.

### Comandos en servidor (después del deploy)

```bash
php artisan legal:remove-demo-disclaimer
php artisan events:sync-canonical-refund-policies
php artisan events:clean-demo-refund-policies --dry-run   # revisar
php artisan events:clean-demo-refund-policies
php artisan events:unpublish-demo --dry-run               # revisar
php artisan events:unpublish-demo
php artisan view:clear
php artisan cache:clear
```

### Checklist Search Console (cuando el dominio esté verificado)

- [ ] Verificar propiedad `https://www.tukipass.com` (meta tag o DNS — ver ítem alta prioridad).
- [ ] Confirmar `APP_URL` / canonical alineados con `www` y HTTPS.
- [ ] Enviar sitemap: `https://www.tukipass.com/sitemap.xml`.
- [ ] Solicitar eliminación o recrawleo de URLs demo (ej. `/the-conference-planners/104`) — deben responder **410** tras deploy `a56a426`.
- [ ] Inspeccionar e indexar: home, `/eventos`, `/organizadores`, `/politica-de-reembolsos`, 2–3 eventos reales activos.
- [ ] [Rich Results Test](https://search.google.com/test/rich-results): evento real → `Event` + `Offer` con `availability` dinámico + `organizer.url` (`023fec1`).
- [ ] Revisar cobertura a 48–72 h: sin `/admin/`, `/customer/dashboard`, checkout, carrito.
- [ ] PageSpeed mobile: home + detalle de evento.

### SEO técnico — mejoras futuras (código, con confirmación)

- [ ] **Schema Event — múltiples imágenes** (Google recomienda hasta 3 ratios); usar galería si hay URLs válidas.
- [ ] **`offers.validFrom`** — requiere campo `ticket_sale_start` (o similar) en DB; no implementar sin migración.
- [ ] **`performer` en Schema** — requiere modelo/tabla de artistas; postergar.
- [ ] **`/mapa-del-sitio` HTML** — toca `routes/` + footer; confirmar antes.
- [ ] Sitemap index si supera 50k URLs.

---

## 🟢 SEO técnico — hecho en código (mayo 2026)

| Área | Estado | Commits / notas |
|------|--------|-----------------|
| Meta keywords/description sin doble `{{ }}` | ✅ | `1c5dad5` |
| JSON-LD Organization + WebSite en layout | ✅ | `1c5dad5` |
| BreadcrumbList, FAQPage, OG en páginas clave | ✅ | `1c5dad5`, `5658462` |
| Sitemap: URL canónica `www`, sin demo/blogs placeholder | ✅ | `d900487`, `5658462` |
| Redirect apex → `www` HTTPS | ✅ | `d077d9f`, `5658462` |
| Exclusión demo: home, `/eventos`, sitemap | ✅ | `5658462`, `DemoEventExclusion` |
| Detalle demo → **410 Gone** | ✅ | `a56a426` |
| Páginas legales sin pie “asesoría legal” demo | ✅ | `dc27a89` + comando `legal:remove-demo-disclaimer` |
| Política reembolso evento: texto fijo, no editable | ✅ | `1dd32b5`, `EventRefundPolicy` |
| Schema `offers.availability` dinámico (InStock/SoldOut) | ✅ | `023fec1` |
| Schema `organizer.url` | ✅ | `023fec1` |
| 404 frontend español + noindex | ✅ | `1c5dad5` |

**Congelar sin auditoría:** rutas checkout, `recalcTotal()`, gateways, `config/auth.php`.

---

## 🟠 Traducción al español — frontend público

> Texto visible al cliente en español (Argentina). Revisar vista por vista lo que siga en inglés.

- [ ] `frontend/home/index-v1.blade.php`
- [ ] `frontend/event/event.blade.php`
- [ ] `frontend/event/event-details.blade.php` — revisar microcopy residual
- [ ] `frontend/event/invoice.blade.php`
- [x] `frontend/check-out.blade.php` — pageHeading español (`Finalizar compra de entradas`)
- [ ] `frontend/shop/*` (index, details, cart, checkout, invoice)
- [ ] `frontend/payment/success.blade.php` / `order_success.blade.php`
- [ ] `frontend/customer/*` (auth, dashboard, tickets, wishlist, soporte)
- [ ] `frontend/organizer/*` (auth, details, index)
- [ ] `frontend/about`, `contact`, `faqs`, `custom-page`, `journal/*`
- [ ] `frontend/partials/*` (header, footer, modals, event-card)

---

## 🔵 Modern SaaS UI — solo frontend público

**Hecho ✅:** home, eventos, event-details, checkout, auth cliente/organizador, dashboard cliente (parcial), about, faqs, blog, contacto, invoice evento.

**Falta ❌:** shop completo, payment success, recupero contraseña cliente/organizador, detalle bookings/orders en dashboard, organizer public pages, modals/popups.

---

## 🟡 Deuda técnica / contenido

- [ ] **Eventos demo en DB**: ejecutar `events:unpublish-demo` en prod si aún tienen `status=1`.
- [x] **Alt descriptivos** — `about` (aliados), `offline`, `customer/dashboard/order/details` (auditoría integral).
- [ ] Revisar alt en listados (eventos, blog, shop) si quedan más `alt=""`.
- [ ] **Organizer details** — revisar thin content para SEO (auditoría sugerida).
- [ ] Confirmar que no queden enlaces internos a slugs demo (`the-conference-planners`, etc.).

---

## 🟡 Pendiente confirmación (humano)

- [ ] ¿Social login en panel **organizador** sigue en roadmap o se mantiene solo email/contraseña?
- [ ] Código exacto de **Google Search Console** para meta tag (pasarlo al agente cuando esté).
- [ ] Otras tareas de sesiones anteriores que falten en esta lista.

---

## ✅ Completado recientemente (mayo 2026)

- [x] **ARCA/AFIP**: integración completa WSFEv1 — WSAA, emisión CAE, preview, `ArcaInvoiceIssuingJob`, certificados, `condicion_iva_receptor_id`, producción con `ARCA_ENABLE_ISSUING=true`. Health check ✅ 19/2 warnings ✅
- [x] MercadoPago: Checkout Pro en producción (token APP_USR, webhooks, notificaciones)
- [x] Postmark: emails transaccionales en producción (Server Tukipass, info@tukipass.com)
- [x] Bloqueo HTTP 410 en detalle de eventos demo / no publicados (`a56a426`)
- [x] Schema Event: `availability` según stock + `organizer.url` (`023fec1`)
- [x] Política de reembolsos canónica fija en admin/organizador y detalle público (`1dd32b5`, `dc27a89`)
- [x] Comandos: `legal:remove-demo-disclaimer`, `events:sync-canonical-refund-policies`, `events:clean-demo-refund-policies`
- [x] Sitemap + redirect `www` + exclusión demo en listados (`d900487`, `d077d9f`, `5658462`)
- [x] Social Login cliente — catch vacío con mensaje en español (`CustomerController`)
- [x] Alt vacíos → descriptivos + pageHeading checkout español (`b340b5c`)

## ✅ Completado (histórico — marzo 2026 y antes)

- [x] Rediseño Modern SaaS: home, eventos, checkout, auth, about, faqs, blog, invoice (`e261d8b` … `d6adbbe`)
- [x] URLs español + aliases auth (`a40da31`, `8ed5289`)
- [x] Removido installer packages — `route:list` OK
- [x] Fix badge/precio Gratis tickets `variation` (`50608cd`)
- [x] Decisión documentada (marzo): social login organizador “no panel” — **revisar** si el producto cambió (ver alta prioridad)

---

_Última actualización: 2026-05-28 — ARCA ✅, failed job ✅ (EventConfirmationMail, queue:forget 24), alt + checkout español ✅ (b340b5c). Pendiente: GSC meta tag + Social Login Org + comandos post-deploy_
