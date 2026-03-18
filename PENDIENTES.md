# Pendientes — Tuki

> Este archivo se actualiza con cada commit. Claude lo lee al inicio de cada sesión.

---

## 🔴 En progreso / Alta prioridad

- [ ] **Social Login — Organizadores**: El login/signup de organizadores no tiene botones de Google/Facebook (solo los clientes los tienen).
- [ ] **Social Login — catch vacío**: `authenticationViaProvider()` en `CustomerController` tiene el bloque `catch` vacío — los errores se tragan silenciosamente. Agregar manejo de error con redirección y mensaje.

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

**Falta aplicar ❌**
- [ ] `frontend/about.blade.php`
- [ ] `frontend/faqs.blade.php`
- [ ] `frontend/journal/blogs.blade.php` / `blog-details.blade.php`
- [ ] `frontend/shop/` — index, detalle, carrito, checkout, invoice
- [ ] `frontend/payment/success.blade.php` / `order_success.blade.php`
- [ ] `frontend/event/invoice.blade.php` — entrada/ticket del cliente
- [ ] `frontend/customer/forget-password.blade.php` / `reset-password.blade.php`
- [ ] `frontend/customer/dashboard/` — bookings details, orders details, support tickets, edit-profile, change-password
- [ ] `frontend/organizer/forget-password.blade.php` / `reset-password.blade.php` / `details.blade.php` / `index.blade.php`
- [ ] `frontend/partials/modals.blade.php` / `popups.blade.php`

---

## 🟡 Pendiente confirmación

- [ ] Verificar si hay otras tareas pendientes que el usuario recuerde de sesiones anteriores.

---

## ✅ Completado recientemente

- [x] Rediseño formulario edición de eventos (`edit.blade.php`) — cards por sección, Inter font, `admin-skin.css` (`de5e8e7`)
- [x] Rediseño login/signup organizadores — auth-split layout (`89eb584`)
- [x] Rediseño split-screen login clientes (`25ac1f9`)
- [x] Fix: logo y favicon con nombres fijos para persistencia en Docker (`d085d1f`)
- [x] Fix: incluir compras de invitados en reporte de organizador (`e885bac`)
- [x] Rediseño checkout v2 Argentina + MercadoPago (`8ee6fb3`)
- [x] Rediseño event details — hero con imagen, layout SaaS (`e261d8b`)
- [x] Cambio de paleta — naranja `#F97316` + gris oscuro `#1e2532` (`3894f35`)

---

_Última actualización: 2026-03-18 — decisión: panel organizador y admin se quedan con Atlantis, no se tocan_
