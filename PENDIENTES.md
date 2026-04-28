# Pendientes — Tuki

> Este archivo se actualiza con cada commit. Claude lo lee al inicio de cada sesión.

---

## 🔴 En progreso / Alta prioridad

- [ ] **Social Login — Organizadores**: El login/signup de organizadores no tiene botones de Google/Facebook (solo los clientes los tienen).
- [x] **Social Login — catch vacío**: `authenticationViaProvider()` en `CustomerController` — ahora redirige a login con mensaje de error en español.

---

## 🟠 Traducción al español — Frontend completo

> Todo texto visible para el cliente debe estar en español. Revisar vista por vista.

- [ ] `frontend/home/index-v1.blade.php` — home principal
- [ ] `frontend/event/event.blade.php` — listado de eventos
- [ ] `frontend/event/event-details.blade.php` — detalle de evento
- [ ] `frontend/event/invoice.blade.php` — factura/entrada del cliente
- [ ] `frontend/check-out.blade.php` — checkout principal
- [ ] `frontend/shop/checkout.blade.php` — checkout de shop
- [ ] `frontend/shop/cart.blade.php` — carrito
- [ ] `frontend/shop/details.blade.php` — detalle producto shop
- [ ] `frontend/shop/index.blade.php` — listado shop
- [ ] `frontend/shop/invoice.blade.php` — factura shop
- [ ] `frontend/payment/success.blade.php` — pantalla de pago exitoso
- [ ] `frontend/payment/order_success.blade.php`
- [ ] `frontend/customer/login.blade.php` / `signup.blade.php` / `forget-password.blade.php` / `reset-password.blade.php`
- [ ] `frontend/customer/dashboard/` — todas las vistas del dashboard cliente (index, bookings, orders, wishlist, profile, password, support tickets)
- [ ] `frontend/organizer/login.blade.php` / `signup.blade.php` / `forget-password.blade.php` / `reset-password.blade.php`
- [ ] `frontend/organizer/details.blade.php` / `index.blade.php`
- [ ] `frontend/about.blade.php` / `contact.blade.php` / `faqs.blade.php` / `custom-page.blade.php`
- [ ] `frontend/journal/` — blogs y detalle
- [ ] `frontend/partials/` — header, footer, modals, breadcrumb, event-card, popups

---

## 🔵 Modern SaaS UI — Frontend público únicamente

> Panel de organizador y admin se quedan con Atlantis (decisión tomada). Solo frontend visible para clientes. Referencia: Stripe · Linear · Vercel · Lemon Squeezy · Resend · Clerk.

**Ya aplicado ✅**
- [x] Home — hero slideshow + marquee + ev-card v2 (`e261d8b`, `9559d88`, `12efc2e`)
- [x] `/eventos` — listado SaaS UI (`2d5726f`)
- [x] Event details — hero + sidebar + countdown + badges (`e261d8b`, `555ecc0`, `d6adbbe`)
- [x] Checkout — layout v2 Argentina + MercadoPago (`8ee6fb3`, `d6adbbe`)
- [x] Customer login / signup — auth split-screen (`25ac1f9`, `b28dcaa`)
- [x] Organizer login / signup — auth split-screen (`89eb584`)
- [x] Customer dashboard index (`1560fc8`)
- [x] Mis entradas / orders (`a4c7e99`)
- [x] Lista de deseos / wishlist (`26b705f`)
- [x] Contacto (`d6adbbe`)
- [x] `frontend/about.blade.php`
- [x] `frontend/faqs.blade.php`
- [x] `frontend/journal/blogs.blade.php` / `blog-details.blade.php`
- [x] `frontend/event/invoice.blade.php`

**Falta aplicar ❌**
- [ ] `frontend/shop/` — index, detalle, carrito, checkout, invoice
- [ ] `frontend/payment/success.blade.php` / `order_success.blade.php`
- [ ] `frontend/customer/forget-password.blade.php` / `reset-password.blade.php`
- [ ] `frontend/customer/dashboard/` — bookings details, orders details, support tickets, edit-profile, change-password
- [ ] `frontend/organizer/forget-password.blade.php` / `reset-password.blade.php` / `details.blade.php` / `index.blade.php`
- [ ] `frontend/partials/modals.blade.php` / `popups.blade.php`

---

## 🟢 SEO técnico — CERRADO

> Bloque SEO técnico completado y validado. Commits limpios, 0 archivos sensibles tocados.

**Quick wins completados:**
1. Fix meta tags CMS (`custom-page.blade.php`) — `@section('meta-keywords')` corregido.
2. Canonical + OG básico en 8 vistas principales (`about`, `contact`, `event`, `faqs`, `blog`, `blog-details`, `shop`, `shop-details`).
3. WebSite + Organization schema JSON-LD en home (`index-v1.blade.php`).
4. Product schema JSON-LD en `shop/details.blade.php`.
5. BreadcrumbList JSON-LD en 6 vistas con breadcrumb HTML (`blog-details`, `shop/index`, `shop/details`, `faqs`, `about`, `organizer/details`).
6. Sitemap XML expandido — ahora incluye blogs, productos, páginas CMS y organizadores (`SitemapController`).
7. Titles de eventos truncados a 55 chars para evitar > 70 en `<title>`; H1, OG title y schema Event intactos.
8. Alt descriptivos en logos aliados del home (reemplazados `alt=""` vacíos).

**Commits:**
- `19b5547` SEO: canonical, OG, schemas JSON-LD y BreadcrumbList en frontend
- `64e0ee5` SEO: truncar title largos en eventos y alt descriptivos en logos aliados

**Nota:** No se requieren más cambios de código por ahora. El resto es post-deploy/manual.

---

## 🟡 Search Console — PENDIENTE EXTERNO (post-deploy/manual)

> No mezclar Search Console con tareas de código. Es validación post-deploy/manual.

**Checklist post-deploy:**
- [ ] Verificar dominio en Google Search Console (si no está hecho).
- [ ] Reenviar `https://www.tukipass.com/sitemap.xml`.
- [ ] Inspeccionar y solicitar indexación de:
  - [ ] Home
  - [ ] `/eventos`
  - [ ] 2-3 eventos importantes activos
  - [ ] 1 blog publicado
  - [ ] 1 producto si shop está activo
  - [ ] 1 página CMS importante
- [ ] Validar Rich Results Test:
  - [ ] Home → WebSite + Organization
  - [ ] Evento → Event + BreadcrumbList + Offer
  - [ ] Producto → Product + BreadcrumbList + Offer
  - [ ] Blog detail → BlogPosting + BreadcrumbList
- [ ] PageSpeed Insights mobile:
  - [ ] Home
  - [ ] Event detail
- [ ] Revisar cobertura/indexación a las 48-72h.
- [ ] Verificar que no aparezcan URLs privadas indexadas: `/admin/`, `/customer/`, `/checkout/`, `/cart/`.

**Bloqueado por:** Deploy a producción + acceso a Google Search Console.

---

## 🔵 Features SEO futuras (con confirmación)

- [ ] `/mapa-del-sitio` HTML — página visible para usuarios, enlazada desde footer. Requiere confirmación explícita porque toca `routes/` y footer.
- [ ] Datos estructurados `FAQPage` en `/faq` si el contenido crece.
- [ ] Datos estructurados `LocalBusiness` si hay venue físico principal.
- [ ] Sitemap indexado (dividir en múltiples archivos si > 50k URLs).

---

## 🔴 Deuda técnica — separada

- [x] **Resuelto** — Removido `kreativdev/installer` y `rachidlaasri/laravel-installer` de Composer. `php artisan route:list` vuelve a funcionar (514 rutas). Validado `composer install`, `view:clear`, `config:clear`, sitemap 200 OK.
- [ ] Alt descriptivos adicionales — revisar listados de eventos, blogs, productos si se detectan más `alt=""` en imágenes informativas.

---

## 🟡 Pendiente confirmación

- [ ] Verificar si hay otras tareas pendientes que el usuario recuerde de sesiones anteriores.

---

## ✅ Completado recientemente

- [x] Fix: login/signup organizador en inglés — `adminLang` pisaba locale `es` (`048231a`)
- [x] Fix: traducciones faltantes signup organizador + "Repetir contraseña" (`89a5f77`)
- [x] Decisión: social login NO se agrega al panel organizador (intencional por diseño)
- [x] Fix: badge "Gratis" incorrecto en home para eventos con tickets `variation` (`50608cd`)
- [x] Fix: precio "Gratis" en sidebar de event-details para tickets `variation` (`50608cd`)
- [x] `payment/success.blade.php` — botón primario naranja + status free en verde (`50608cd`)

- [x] Rediseño formulario edición de eventos (`edit.blade.php`) — cards por sección, Inter font, `admin-skin.css` (`de5e8e7`)
- [x] Rediseño login/signup organizadores — auth-split layout (`89eb584`)
- [x] Rediseño split-screen login clientes (`25ac1f9`)
- [x] Fix: logo y favicon con nombres fijos para persistencia en Docker (`d085d1f`)
- [x] Fix: incluir compras de invitados en reporte de organizador (`e885bac`)
- [x] Rediseño checkout v2 Argentina + MercadoPago (`8ee6fb3`)
- [x] Rediseño event details — hero con imagen, layout SaaS (`e261d8b`)
- [x] Cambio de paleta — naranja `#F97316` + gris oscuro `#1e2532` (`3894f35`)

---

_Última actualización: 2026-03-18 — rediseño invoice/ticket evento: dark header, Inter, status badge, billing moderno_
